<?php

/**
 * Управление БД
 * Предоставляет небольшую абстракцию от БД
 */
class db {

  /**
   * Позволяет множественное подключение к БД
   * редко используется, но иногда бывает полезно
   */
  private $connections = array();

  /**
   * Сообщает об активном соединение
   * setActiveConnection($id) позволяет изменить активное соединение
   */
  private $activeConnection = 0;

  /**
   * Запросы которые выполнились и сохранены на будущее
   */
  private $queryCache = array();

  /**
   * Данные которые были извлечены и сохранены на будущее
   */
  private $dataCache = array();

  /**
   * Запись последненго запроса
   */
  private $last;


  /**
   * Конструктор
   */
  public function __construct()
  {

  }

  /**
   * Создание нового соединения
   * @param String database hostname
   * @param String database username
   * @param String database password
   * @param String database we are using
   * @return int the id of the new connection
   */
  public function newConnection( $host, $user, $password, $database )
  {
    $this->connections[] = new mysqli( $host, $user, $password, $database );
    $connection_id = count( $this->connections )-1;
    if( mysqli_connect_errno() )
    {
      trigger_error('Error connecting to host. '.$this->connections[$connection_id]->error, E_USER_ERROR);
    }

    return $connection_id;
  }

  /**
   * Закрываем активное соединение
   * @return void
   */
  public function closeConnection()
  {
    $this->connections[$this->activeConnection]->close();
  }

  /**
   * Изменяем активное соединение
   * @param int the new connection id
   * @return void
   */
  public function setActiveConnection( int $new )
  {
    $this->activeConnection = $new;
  }

  /**
   * Сохранияем запрос в кэш
   * @param String the query string
   * @return the pointed to the query in the cache
   */
  public function cacheQuery( $queryStr )
  {
    if( !$result = $this->connections[$this->activeConnection]->query( $queryStr ) )
    {
      trigger_error('Error executing and caching query: '.$this->connections[$this->activeConnection]->error, E_USER_ERROR);
      return -1;
    }
    else
    {
      $this->queryCache[] = $result;
      return count($this->queryCache)-1;
    }
  }

  /**
   * Получение количества строк в кэше
   * @param int the query cache pointer
   * @return int the number of rows
   */
  public function numRowsFromCache( $cache_id )
  {
    return $this->queryCache[$cache_id]->num_rows;
  }

  /**
   * Получение строк из кэша
   * @param int the query cache pointer
   * @return array the row
   */
  public function resultsFromCache( $cache_id )
  {
    return $this->queryCache[$cache_id]->fetch_array(MYSQLI_ASSOC);
  }

  /**
   * Сохраняем кэш
   * @param array the data
   * @return int the pointed to the array in the data cache
   */
  public function cacheData( $data )
  {
    $this->dataCache[] = $data;
    return count( $this->dataCache )-1;
  }

  /**
   * Получаем данные из кэша
   * @param int data cache pointed
   * @return array the data
   */
  public function dataFromCache( $cache_id )
  {
    return $this->dataCache[$cache_id];
  }

  /**
   * Удаляем запись из таблицы
   * @param String the table to remove rows from
   * @param String the condition for which rows are to be removed
   * @param int the number of rows to be removed
   * @return void
   */
  public function deleteRecords( $table, $condition, $limit )
  {
    $limit = ( $limit == '' ) ? '' : ' LIMIT ' . $limit;
    $delete = "DELETE FROM {$table} WHERE {$condition} {$limit}";
    $this->executeQuery( $delete );
  }

  /**
   * Обновляем запись в таблице
   * @param String the table
   * @param array of changes field => value
   * @param String the condition
   * @return bool
   */
  public function updateRecords( $table, $changes, $condition )
  {
    $update = "UPDATE " . $table . " SET ";
    foreach( $changes as $field => $value )
    {
      $update .= "`" . $field . "`='{$value}',";
    }

    // remove our trailing ,
    $update = substr($update, 0, -1);
    if( $condition != '' )
    {
      $update .= "WHERE " . $condition;
    }

    $this->executeQuery( $update );

    return true;

  }

  /**
   * Вставляем запись в таблицу
   * @param String the database table
   * @param array data to insert field => value
   * @return bool
   */
  public function insertRecords( $table, $data )
  {
    // setup some variables for fields and values
    $fields  = "";
    $values = "";

    // populate them
    foreach ($data as $f => $v)
    {

      $fields  .= "`$f`,";
      $values .= ( is_numeric( $v ) && ( intval( $v ) == $v ) ) ? $v."," : "'$v',";

    }

    // remove our trailing ,
    $fields = substr($fields, 0, -1);
    // remove our trailing ,
    $values = substr($values, 0, -1);

    $insert = "INSERT INTO $table ({$fields}) VALUES({$values})";
    $this->executeQuery( $insert );
    return true;
  }

  /**
   * Выполнение запроса к бд
   * @param String the query
   * @return void
   */
  public function executeQuery( $queryStr )
  {
    if( !$result = $this->connections[$this->activeConnection]->query( $queryStr ) )
    {
      trigger_error('Error executing query: '.$this->connections[$this->activeConnection]->error, E_USER_ERROR);
    }
    else
    {
      $this->last = $result;
    }

  }

  /**
   * Получить строки последнего запроса, исключая запросы из кэша
   * @return array
   */
  public function getRows()
  {
    return $this->last->fetch_array(MYSQLI_ASSOC);
  }

  /**
   * Получить количество строк последнего запроса
   * @return int the number of affected rows
   */
  public function affectedRows()
  {
    return $this->$this->connections[$this->activeConnection]->affected_rows;
  }

  /**
   * Проверка безопасности данных
   * @param String the data to be sanitized
   * @return String the sanitized data
   */
  public function sanitizeData( $data )
  {
    return $this->connections[$this->activeConnection]->real_escape_string( $data );
  }

  /**
   * Декструктор, закрывает соединение
   * close all of the database connections
   */
  public function __deconstruct()
  {
    foreach( $this->connections as $connection )
    {
      $connection->close();
    }
  }
}
?>
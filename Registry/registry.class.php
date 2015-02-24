<?php
/**
 * Объект реестра
 * Реализует паттерн Реестра и Единичный класс
 *
 */
class Registry {
     
    /**
     * Массив наших объектов
     * @access private
     */
    private static $objects = array();
     
    /**
     * Массив наших настроек
     * @access private
     */
    private static $settings = array();
     
    /**
     * Человекочитаемое название нашего фреймворка
     * @access private
     */
    private static $frameworkName = 'Framework version 0.1';
     
    /**
     * Экземпляр нашего реестра
     * @access private
     */
    private static $instance;
     
    /**
     * Конструктор для нашего реестра
     * @access private
     */
    private function __construct()
    {
     
    }
         
    /**
     * метод единичного класса для доступа к объекту
     * @access public
     * @return
     */
    public static function singleton()
    {
        if( !isset( self::$instance ) )
        {
            $obj = __CLASS__;
            self::$instance = new $obj;
        }
         
        return self::$instance;
    }
     
    /**
     * предотвращение копирования нашего объекта: проблем с E_USER_ERROR если это произошло
     */
    public function __clone()
    {
        trigger_error( 'Cloning the registry is not permitted', E_USER_ERROR );
    }
     
    /**
     * хранит объект в нашем реестре
     * @param String $object the name of the object
     * @param String $key the key for the array
     * @return void
     */
    public static function storeObject( $object, $key )
    {
        require_once('Registry/objects/' . $object . '.class.php');
        self::$objects[ $key ] = new $object( self::$instance );
    }

    public function storeCoreObjects()
    {
      $this->storeObject('db', 'db' );
      $this->storeObject('template', 'template' );
    }

    /**
     * получение объекта из нашего реестра
     * @param String $key the array key
     * @return object
     */
    public static function getObject( $key )
    {
        if( is_object ( self::$objects[ $key ] ) )
        {
            return self::$objects[ $key ];
        }
    }
     
    /**
     * Хранит настройки нашего реестра
     * @param String $data
     * @param String $key the key for the array
     * @return void
     */
    public function storeSetting( $data, $key )
    {
        self::$settings[ $key ] = $data;
 
 
    }
     
    /**
     * Получение настроек нашего реестра
     * @param String $key the key in the array
     * @return void
     */
    public function getSetting( $key )
    {
        return self::$settings[ $key ];
    }
     
    /**
     * Получение имени фреймворка
     * @return String
     */
    public function getFrameworkName()
    {
        return self::$frameworkName;
    }
     
     
}
 
?>
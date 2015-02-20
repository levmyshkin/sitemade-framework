<?php

// Константа определенная в index.php, чтобы избежать вызова класса из другого места
if ( ! defined( 'FW' ) )
{
  echo 'Этот файл может быть вызван только из index.php и не напрямую';
  exit();
}

/**
 * Класс работы с шаблонами
 */
class template {

  private $page;

  /**
   * Конструктор
   */
  public function __construct()
  {
    // Далее мы добавим этот класс страницы
    include( APP_PATH . '/Registry/objects/page.class.php');
    $this->page = new Page();

  }

  /**
   * Добавляет тег в страницу
   * @param String $tag тег где мы вставляем шаблон, например {hello}
   * @param String $bit путь к шаблону
   * @return void
   */
  public function addTemplateBit( $tag, $bit )
  {
    if( strpos( $bit, 'Views/' ) === false )
    {
      $bit = 'Views/Templates/' . $bit;
    }
    $this->page->addTemplateBit( $tag, $bit );
  }

  /**
   * Включаем шаблоны в страницу
   * Обновляем контент страницы
   * @return void
   */
  private function replaceBits()
  {
    $bits = $this->page->getBits();
    foreach( $bits as $tag => $template )
    {
      $templateContent = file_get_contents( $template );
      $newContent = str_replace( '{' . $tag . '}', $templateContent, $this->page->getContent() );
      $this->page->setContent( $newContent );
    }
  }

  /**
   * Заменяем теги на новый контент
   * @return void
   */
  private function replaceTags()
  {
    // получаем теги
    $tags = $this->page->getTags();
    // перебераем теги
    foreach( $tags as $tag => $data )
    {
      if( is_array( $data ) )
      {

        if( $data[0] == 'SQL' )
        {
          // Заменяем теги из кешированного запроса
          $this->replaceDBTags( $tag, $data[1] );
        }
        elseif( $data[0] == 'DATA' )
        {
          // Заменяем теги из кешированных данных
          $this->replaceDataTags( $tag, $data[1] );
        }
      }
      else
      {
        // заменяем теги на контент
        $newContent = str_replace( '{' . $tag . '}', $data, $this->page->getContent() );
        // обновляем содержимое страницы
        $this->page->setContent( $newContent );
      }
    }
  }

  /**
   * Заменяем теги данными из БД
   * @param String $tag тег (токен)
   * @param int $cacheId ID запросов
   * @return void
   */
  private function replaceDBTags( $tag, $cacheId )
  {
    $block = '';
    $blockOld = $this->page->getBlock( $tag );

    // Проверяем кэш для каждого из запросов...
    while ($tags = Registry::getObject('db')->resultsFromCache( $cacheId ) )
    {
      $blockNew = $blockOld;
      // создаем новый блок и вставляем его вместо тега
      foreach ($tags as $ntag => $data)
      {
        $blockNew = str_replace("{" . $ntag . "}", $data, $blockNew);
      }
      $block .= $blockNew;
    }
    $pageContent = $this->page->getContent();
    // удаляем разделители из шаблона, чистим HTML
    $newContent = str_replace( '<!-- START ' . $tag . ' -->' . $blockOld . '<!-- END ' . $tag . ' -->', $block, $pageContent );
    // обновляем контент страницы
    $this->page->setContent( $newContent );
  }

  /**
   * Заменяем контент страницы вместо тегов
   * @param String $tag тег
   * @param int $cacheId ID данных из кэша
   * @return void
   */
  private function replaceDataTags( $tag, $cacheId )
  {
    $block = $this->page->getBlock( $tag );
    $blockOld = $block;
    while ($tags = Registry::getObject('db')->dataFromCache( $cacheId ) )
    {
      foreach ($tags as $tag => $data)
      {
        $blockNew = $blockOld;
        $blockNew = str_replace("{" . $tag . "}", $data, $blockNew);
      }
      $block .= $blockNew;
    }
    $pageContent = $this->page->getContent();
    $newContent = str_replace( $blockOld, $block, $pageContent );
    $this->page->setContent( $newContent );
  }

  /**
   * Получаем страницу
   * @return Object
   */
  public function getPage()
  {
    return $this->page;
  }

  /**
   * Устанавливаем контент страницы в зависимости от количества шаблонов
   * передаем пути к шаблонам
   * @return void
   */
  public function buildFromTemplates()
  {
    $bits = func_get_args();
    $content = "";
    foreach( $bits as $bit )
    {

      if( strpos( $bit, 'skins/' ) === false )
      {
        $bit = 'Views/Templates/' . $bit;
      }
      if( file_exists( $bit ) == true )
      {
        $content .= file_get_contents( $bit );
      }

    }
    $this->page->setContent( $content );
  }

  /**
   * Convert an array of data (i.e. a db row?) to some tags
   * @param array the data
   * @param string a prefix which is added to field name to create the tag name
   * @return void
   */
  public function dataToTags( $data, $prefix )
  {
    foreach( $data as $key => $content )
    {
      $this->page->addTag( $key.$prefix, $content);
    }
  }

  public function parseTitle()
  {
    $newContent = str_replace('<title>', '<title>'. $this->$page->getTitle(), $this->page->getContent() );
    $this->page->setContent( $newContent );
  }

  /**
   * Подставляем теги и токены, заголовки
   * @return void
   */
  public function parseOutput()
  {
    $this->replaceBits();
    $this->replaceTags();
    $this->parseTitle();
  }

}
?>
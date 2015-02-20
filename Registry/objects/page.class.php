<?php

/**
 * Наш класс для страницы
 * Этот класс позволяет добавить несколько нужных нам вещей
 * Например: подпароленные страницы, добавление js/css файлов, и т.д.
 */
class page {

  private $css = array();
  private $js = array();
  private $bodyTag = '';
  private $bodyTagInsert = '';

  // будущий функционал
  private $authorised = true;
  private $password = '';

  // элементы страницы
  private $title = '';
  private $tags = array();
  private $postParseTags = array();
  private $bits = array();
  private $content = "";

  /**
   * Constructor...
   */
  function __construct() { }

  public function getTitle()
  {
    return $this->title;
  }

  public function setPassword( $password )
  {
    $this->password = $password;
  }

  public function setTitle( $title )
  {
    $this->title = $title;
  }

  public function setContent( $content )
  {
    $this->content = $content;
  }

  public function addTag( $key, $data )
  {
    $this->tags[$key] = $data;
  }

  public function getTags()
  {
    return $this->tags;
  }

  public function addPPTag( $key, $data )
  {
    $this->postParseTags[$key] = $data;
  }

  /**
   * Парсим теги
   * @return array
   */
  public function getPPTags()
  {
    return $this->postParseTags;
  }

  /**
   * Добавляем тег
   * @param String the tag where the template is added
   * @param String the template file name
   * @return void
   */
  public function addTemplateBit( $tag, $bit )
  {
    $this->bits[ $tag ] = $bit;
  }

  /**
   * Получаем все теги
   * @return array the array of template tags and template file names
   */
  public function getBits()
  {
    return $this->bits;
  }

  /**
   * Ищем все блоки на странице
   * @param String the tag wrapping the block ( <!-- START tag --> block <!-- END tag --> )
   * @return String the block of content
   */
  public function getBlock( $tag )
  {
    preg_match ('#<!-- START '. $tag . ' -->(.+?)<!-- END '. $tag . ' -->#si', $this->content, $tor);

    $tor = str_replace ('<!-- START '. $tag . ' -->', "", $tor[0]);
    $tor = str_replace ('<!-- END '  . $tag . ' -->', "", $tor);

    return $tor;
  }

  public function getContent()
  {
    return $this->content;
  }

}
?>
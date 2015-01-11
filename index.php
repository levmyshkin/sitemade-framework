<?php
/**
 * Framework
 * Framework loader - acts as a single point of access to the Framework
 *
 */
  
// стартуем сессию
session_start();
 
// задаем некоторые константы
// Задаем корень фреймворка, чтобы легко получать его в любом скрипте
define( "APP_PATH", dirname( __FILE__ ) ."/" );
// Мы будем использовать это, чтобы избежать вызов скриптов не из нашего фреймворка
define( "FW", true );
 
/**
 * Магическая функция автозагрузки
 * позволяет вызвать необходимый -controller- когда он нужен
 * @param String the name of the class
 */
function __autoload( $class_name )
{
    require_once('Controllers/' . $class_name . '/' . $class_name . '.php' );
}
 
// подключаем наш реестр
require_once('Registry/registry.class.php');
$registry = Registry::singleton();
 
// выводим имя фреймворка, чтобы проверить, что все работает
print $registry->getFrameworkName();
 
exit();
 
?>
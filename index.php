<?php
/**
 * Framework
 * Framework loader - точка входа в наш фреймворк
 *
 */
  
// стартуем сессию
session_start();

error_reporting(E_ALL);
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


// мы храним список всех объектов в классе регистра
$registry->storeCoreObjects();

// здесь должны быть ваши доступы к бд
$registry->getObject('db')->newConnection('localhost', 'iwan', 'qpzm1235', 'framework');

// Подключаем шаблон главной страницы
$registry->getObject('template')->buildFromTemplates('main.tpl.php');

// Делаем запрос к таблице пользователей
$cache = $registry->getObject('db')->cacheQuery('SELECT * FROM users');

// Добавяем тег users, чтобы вызвать его в шаблоне,
// в этом теге будут доступны поля таблицы через токены {name}, {email}
$registry->getObject('template')->getPage()->addTag('users', array('SQL', $cache) );

// Устанавливаем заголовок страницы
$registry->getObject('template')->getPage()->setTitle('Our users');

// Парсим страницу в поисках тегов и токенов и выводим страницу
$registry->getObject('template')->parseOutput();
print $registry->getObject('template')->getPage()->getContent();

// выводим имя фреймворка, чтобы проверить, что все работает
print $registry->getFrameworkName();
 
exit();
 
?>
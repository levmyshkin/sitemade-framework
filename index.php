<?php
/**
 * Framework
 * Framework loader - acts as a single point of access to the Framework
 *
 */
  
// �������� ������
session_start();
 
// ������ ��������� ���������
// ������ ������ ����������, ����� ����� �������� ��� � ����� �������
define( "APP_PATH", dirname( __FILE__ ) ."/" );
// �� ����� ������������ ���, ����� �������� ����� �������� �� �� ������ ����������
define( "FW", true );
 
/**
 * ���������� ������� ������������
 * ��������� ������� ����������� -controller- ����� �� �����
 * @param String the name of the class
 */
function __autoload( $class_name )
{
    require_once('Controllers/' . $class_name . '/' . $class_name . '.php' );
}
 
// ���������� ��� ������
require_once('Registry/registry.class.php');
$registry = Registry::singleton();
 
// ������� ��� ����������, ����� ���������, ��� ��� ��������
print $registry->getFrameworkName();
 
exit();
 
?>
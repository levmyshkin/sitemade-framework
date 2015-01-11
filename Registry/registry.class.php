<?php
/**
 * ������ �������
 * ��������� ������� ������� � ��������� �����
 *
 */
class Registry {
     
    /**
     * ������ ����� ��������
     * @access private
     */
    private static $objects = array();
     
    /**
     * ������ ����� ��������
     * @access private
     */
    private static $settings = array();
     
    /**
     * ���������������� �������� ������ ����������
     * @access private
     */
    private static $frameworkName = 'Framework version 0.1';
     
    /**
     * ��������� ������ �������
     * @access private
     */
    private static $instance;
     
    /**
     * ����������� ��� ������ �������
     * @access private
     */
    private function __construct()
    {
     
    }
         
    /**
     * ����� ���������� ������ ��� ������� � �������
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
     * �������������� ����������� ������ �������: ������� � E_USER_ERROR ���� ��� ���������
     */
    public function __clone()
    {
        trigger_error( 'Cloning the registry is not permitted', E_USER_ERROR );
    }
     
    /**
     * ������ ������ � ����� �������
     * @param String $object the name of the object
     * @param String $key the key for the array
     * @return void
     */
    public function storeObject( $object, $key )
    {
        require_once('objects/' . $object . '.class.php');
        self::$objects[ $key ] = new $object( self::$instance );
    }
     
    /**
     * ��������� ������� �� ������ �������
     * @param String $key the array key
     * @return object
     */
    public function getObject( $key )
    {
        if( is_object ( self::$objects[ $key ] ) )
        {
            return self::$objects[ $key ];
        }
    }
     
    /**
     * ������ ��������� ������ �������
     * @param String $data
     * @param String $key the key for the array
     * @return void
     */
    public function storeSetting( $data, $key )
    {
        self::$settings[ $key ] = $data;
 
 
    }
     
    /**
     * ��������� �������� ������ �������
     * @param String $key the key in the array
     * @return void
     */
    public function getSetting( $key )
    {
        return self::$settings[ $key ];
    }
     
    /**
     * ��������� ����� ����������
     * @return String
     */
    public function getFrameworkName()
    {
        return self::$frameworkName;
    }
     
     
}
 
?>
<?php
/**
 * Created by PhpStorm.
 * User: wanghui03
 * Date: 2017/7/24
 * Time: 11:27
 */
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set('PRC');

class Base
{
    private static $_instance;

    public function __clone() {}
    public function __construct()
    {
        spl_autoload_register(array('Base', 'autoload'));
    }

    public function autoload($class)
    {
        $className = str_replace('\\', '/', $class);
        require_once BASE . '/' . $className . '.php';
    }

    public static function init()
    {
        if (! self::$_instance instanceof self) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
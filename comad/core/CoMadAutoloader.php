<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core;

/**
 * Class Autoloader
 * @package comad\core
 */
class ComadAutoloader
{
    public function __construct()
    {
        spl_autoload_register(array($this, 'load_class'));
    }

    public static function register()
    {
        new ComadAutoloader();
    }

    public function load_class($class_name)
    {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/' . strtolower(str_replace('\\', '/', $class_name)) . '.php';
        if (file_exists($file)) {
            require_once($file);
        }
    }
}
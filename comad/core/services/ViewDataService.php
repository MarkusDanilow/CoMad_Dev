<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\services;


/**
 * Class ViewDataService
 * @package comad\core\services
 */
class ViewDataService
{

    const TITLE = 'title', VIEW_MODEL = 'viewModel';

    /**
     * @var array
     */
    private static $_VIEW_DATA = [];

    /**
     * @param $key
     * @param $value
     */
    public static function _set($key, $value)
    {
        self::$_VIEW_DATA[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public static function _get($key)
    {
        return isset($key) && isset(self::$_VIEW_DATA[$key]) ? self::$_VIEW_DATA[$key] : null;
    }

}
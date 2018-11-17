<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\services\annotations;


use comad\core\controllers\Controller;

/**
 * Class HttpAnnotation
 * @package comad\core\services\annotations
 */
class HttpAnnotation extends BaseAnnotation
{

    public static $key = 'Http';

    /**
     * @param $checkKey
     * @param $checkValue
     * @return mixed
     */
    public function handle($checkKey, $checkValue)
    {
        if (strtolower($this->value) !== strtolower($checkValue)) {
            Controller::_initErrorCase();
        }
        return;
    }
}
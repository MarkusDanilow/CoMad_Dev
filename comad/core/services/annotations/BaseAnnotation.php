<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\services\annotations;

abstract class BaseAnnotation
{

    /**
     * @var
     */
    public static $key;
    /**
     * @var
     */
    protected $value;

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }


    /**
     * @param $checkKey
     * @param $checkValue
     * @return mixed
     */
    public abstract function handle($checkKey, $checkValue);

}
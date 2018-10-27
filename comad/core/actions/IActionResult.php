<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\actions;


/**
 * Interface IAction
 * @package comad\core\actions
 */
interface IActionResult
{

    /**
     * @return mixed
     */
    public function execute();

}
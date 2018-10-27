<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\controllers;

use comad\core\actions\ViewActionResult;
use comad\core\Application;
use comad\core\controllers\Controller;

/**
 * Class ErrorController
 * @package comad\controllers
 */
class ErrorController extends Controller
{

    /**
     *
     */
    public function index()
    {
        return new ViewActionResult();
    }

    public function er()
    {
        return new ViewActionResult();
    }

}
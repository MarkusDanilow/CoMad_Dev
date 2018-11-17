<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\controllers;


use comad\core\actions\ViewActionResult;
use comad\core\controllers\Controller;

/**
 * Class LoginController
 * @package comad\controllers
 */
class LoginController extends Controller
{

    /**
     * @return ViewActionResult
     * @[Http=get]
     */
    public function index()
    {
        return new ViewActionResult();
    }

    /**
     * @return ViewActionResult
     * @[Http=post]
     */
    public function login()
    {
        return new ViewActionResult();
    }

}
<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\controllers;

use comad\core\actions\RedirectActionResult;
use comad\core\actions\ViewActionResult;
use comad\core\controllers\Controller;

/**
 * Class IndexController
 * @package comad\controllers
 */
class IndexController extends Controller
{

    /**
     * @[Alias=Home]
     */
    public function index()
    {
        return new RedirectActionResult('hello/world');
        // return new ViewActionResult();
    }

}
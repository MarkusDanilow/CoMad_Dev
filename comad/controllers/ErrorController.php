<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\controllers;

use comad\core\actions\ViewActionResult;
use comad\core\controllers\Controller;
use comad\core\services\ViewDataService;

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
        ViewDataService::_set('title', 'Page Not Found');
        return new ViewActionResult();
    }


}
<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\controllers;

use comad\core\actions\ViewActionResult;
use comad\core\controllers\Controller;
use comad\core\services\ViewDataService;
use comad\models\DemoModel;

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
        $model = new DemoModel('Gustav', '512');
        ViewDataService::_set(ViewDataService::TITLE, 'Home');
        ViewDataService::_set(ViewDataService::VIEW_MODEL, $model);
        return new ViewActionResult();
    }

}
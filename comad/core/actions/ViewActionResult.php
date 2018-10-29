<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\actions;

use comad\core\Application;

/**
 * Class View
 * @package comad\core\actions
 */
class ViewActionResult implements IActionResult
{

    /**
     * @var null
     */
    private $viewPath = null;

    /**
     * @return mixed
     */
    public function execute()
    {
        $this->viewPath = Application::_getInstance()->getView(true, true);

    }

    /**
     *
     */
    public function renderView()
    {
        include_once $this->viewPath;
    }

}
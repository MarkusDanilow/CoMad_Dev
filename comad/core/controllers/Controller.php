<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\controllers;

use comad\core\actions\IActionResult;
use comad\core\Application;
use comad\core\services\AnnotationService;

/**
 * Class Controller
 * @package comad\core\controllers
 */
class Controller
{

    /**
     * @param Controller $controller
     * @param string $view
     * @param array $parameters
     * @return IActionResult
     */
    public static function _executeAction(Controller $controller = null, $view = null, $parameters = [])
    {
        if (!(isset($controller) && isset($view))) {
            self::_initErrorCase($controller, $view);
        }
        AnnotationService::_handleAnnotations($controller, $view);
        return $controller->{$view}($parameters);
    }

    /**
     * @param Controller|null $controller
     * @param null $view
     */
    public static function _initErrorCase(Controller &$controller = null, &$view = null)
    {
        Application::_initErrorCase();
        $controller = Application::_getInstance()->getController();
        $view = Application::_getInstance()->getView();
        Application::_getInstance()->setController($controller);
    }

}
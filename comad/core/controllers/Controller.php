<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\controllers;

use comad\core\actions\IActionResult;
use comad\core\Application;

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
    public static function _executeAction($controller = null, $view = null, $parameters = [])
    {
        if (!(isset($controller) && isset($view))) {
            Application::_initErrorCase();
            $controller = Application::_getInstance()->getController();
            $view = Application::_getInstance()->getView();
        }

        /* analyze annotations */
        $reflector = new \ReflectionClass(get_class($controller));
        $comment = $reflector->getMethod($view)->getDocComment();

        return $controller->{$view}($parameters);
    }

}
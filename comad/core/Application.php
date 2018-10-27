<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core;

use comad\core\actions\IActionResult;
use comad\core\actions\ViewActionResult;
use comad\core\controllers\Controller;
use comad\core\services\RenderingService;
use comad\core\services\RoutingService;

/**
 * Class Application
 * @package comad\core
 */
class Application
{

    /**
     * @var Application
     */
    private static $applicationInstance;

    /**
     * @var RoutingService
     */
    private $routingService;

    /**
     * @var Controller
     */
    private $controller;

    /**
     * @var RenderingService
     */
    private $renderingService;

    /**
     * @var IActionResult
     */
    private $tempActionResult;

    /**
     * Application constructor.
     */
    public function __construct()
    {
        self::$applicationInstance = $this;
        $this->routingService = new RoutingService();
        $this->createController();
        $this->tempActionResult = Controller::_executeAction($this->controller, $this->getView(), array());
        if (isset($this->tempActionResult)) {
            $this->tempActionResult->execute();
        }
        $this->renderingService = new RenderingService();
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param bool $useExtension
     * @param bool $useFullPath
     * @return string
     */
    public function getView($useExtension = false, $useFullPath = false)
    {
        return RoutingService::_getView($useExtension, $useFullPath);
    }

    /**
     *
     */
    protected function createController()
    {
        $controllerName = RoutingService::_getController(true, true);
        $this->controller = new $controllerName();
    }

    /**
     *
     */
    public static function _initErrorCase()
    {
        RoutingService::_initErrorCase();
        self::$applicationInstance->createController();
    }

    /**
     *
     */
    public static function _renderView()
    {
        if (self::_getInstance()->tempActionResult instanceof ViewActionResult) {
            self::_getInstance()->tempActionResult->renderView();
        }
    }

    /**
     * @return Application
     */
    public static function _getInstance()
    {
        return self::$applicationInstance;
    }

}
<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core;

use comad\core\services\RoutingService;

/**
 * Class Application
 * @package comad\core
 */
class Application
{

    /**
     * @var RoutingService
     */
    private $routingService;

    private $controller;

    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->routingService = new RoutingService();
        $controllerName = RoutingService::_getController(true, true);
        $this->controller = new $controllerName();
    }

}
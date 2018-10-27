<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\services;


/**
 * Class RenderingService
 * @package comad\core\services
 */
class RenderingService
{

    /**
     * RenderingService constructor.
     */
    public function __construct()
    {
        $this->baseRendering();
    }

    /**
     *
     */
    private function baseRendering()
    {
        require_once RoutingService::$_VIEW_DIRECTORY . '_shared/_layout.php';
    }

}
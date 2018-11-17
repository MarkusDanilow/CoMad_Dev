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
        require_once RoutingService::$_SHARED_VIEW_DIRECTORY . '_layout.php';
    }

    /**
     * @param $sectionName
     */
    public static function renderSection($sectionName)
    {
        include_once RoutingService::$_SHARED_VIEW_DIRECTORY . $sectionName . '.php';
    }

}
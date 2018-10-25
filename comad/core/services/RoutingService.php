<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\services;

/**
 * Class RoutingService
 * @package myria\core\services
 */
class RoutingService
{

    /**
     * @var RoutingService $routingInstance
     */
    private static $routingInstance = null;

    /**
     * @var null
     */
    public static $_HOST = null;

    /**
     * @var null
     */
    public static $_PROTOCOL = null;

    /**
     * @var null
     */
    public static $_REQUEST_URI = null;

    /**
     * @var null
     */
    public static $_DOCUMENT_ROOT = null;

    /**
     * @var null
     */
    public static $_VIEW_DIRECTORY = null;

    /**
     * @var null
     */
    private $controller = null;

    /**
     * @var null
     */
    private $view = null;

    /**
     * @var null
     */
    private $identifier = null;

    /**
     * @var array
     */
    private $internalRoute = [];

    /**
     * RoutingService constructor.
     */
    public function __construct()
    {
        self::initStaticVariables($this);
        $this->resetLocalVariables();
        $this->determineRoute();
        $this->validateRoute();
    }

    /**
     *
     */
    private function resetLocalVariables()
    {
        $this->controller = null;
        $this->view = null;
        $this->identifier = null;
        $this->internalRoute = [];
    }

    /**
     *
     */
    private function determineRoute()
    {
        $uriPartIndex = 0;
        $uriParts = explode('/', self::$_REQUEST_URI);
        foreach ($uriParts as $uriPart) {
            $uriPart = trim($uriPart);
            if (strlen($uriPart) <= 0) continue;
            $this->internalRoute[$uriPartIndex++] = $uriPart;
        }
    }

    /**
     * @return bool
     */
    protected function isLanguageSet()
    {
        return sizeof($this->internalRoute) > 0 && in_array($this->internalRoute[0], LanguageService::$_KNOWN_LANGUAGES);
    }

    /**
     * @return mixed|string
     */
    protected function getLanguage()
    {
        return $this->isLanguageSet() ? $this->internalRoute[0] : LanguageService::$_DEFAULT_LANGUAGE;
    }

    /**
     * @return bool
     */
    protected function isControllerSet()
    {
        $routeSize = sizeof($this->internalRoute);
        return $this->isLanguageSet() ? $routeSize >= 2 : $routeSize >= 1;
    }

    /**
     * @param bool $getFullName
     * @param bool $includeNamespace
     * @return string
     */
    protected function getController($getFullName = false, $includeNamespace = false)
    {
        $controllerName = ($this->isControllerSet() ? $this->internalRoute[$this->isLanguageSet() ? 1 : 0] : 'Index') . ($getFullName ? 'Controller' : '');
        return ($includeNamespace ? 'comad\\controllers\\' : '') . $controllerName;
    }

    /**
     * @return bool
     */
    protected function isValidController()
    {
        return class_exists($this->getController(true, true));
    }

    /**
     * @return bool
     */
    protected function isViewSet()
    {
        $routeSize = sizeof($this->internalRoute);
        return ($this->isLanguageSet() ? $routeSize >= 3 : $routeSize >= 2) && $this->isControllerSet();
    }

    /**
     * @param bool $useExtension
     * @param bool $useFullPath
     * @return string
     */
    protected function getView($useExtension = false, $useFullPath = false)
    {
        $viewFile = ($this->isViewSet() ? $this->internalRoute[$this->isLanguageSet() ? 2 : 1] : 'index') . ($useExtension ? '.php' : '');
        return ($useFullPath ? self::$_VIEW_DIRECTORY : '') . $viewFile;
    }

    /**
     * @return bool
     */
    protected function isValidView()
    {
        $controllerName = strtolower($this->getController(false, false));
        $viewDirectory = self::$_VIEW_DIRECTORY . $controllerName . '/';
        return file_exists($viewDirectory) && file_exists($viewDirectory . $this->getView(true));
    }

    /**
     * @return bool
     */
    protected function isIdentifierSet()
    {
        $routeSize = sizeof($this->internalRoute);
        return ($this->isLanguageSet() ? $routeSize >= 4 : $routeSize >= 3) && $this->isViewSet();
    }

    /**
     * @return mixed|null
     */
    protected function getIdentifier()
    {
        return $this->isIdentifierSet() ? $this->internalRoute[$this->isLanguageSet() ? 3 : 2] : null;
    }

    /**
     * @return bool
     */
    protected function isValidRoute()
    {
        $routeSize = sizeof($this->internalRoute);
        $req1 = $this->isLanguageSet() ? $routeSize <= 4 : $routeSize <= 3;
        $req2 = $this->isValidController();
        $req3 = $this->isValidView();
        return $req1 && $req2 && $req3;
    }

    /**
     *
     */
    protected function validateRoute()
    {
        if ($this->isValidRoute()) return;
        $this->internalRoute[$this->isLanguageSet() ? 1 : 0] = 'Error';
        $this->internalRoute[$this->isLanguageSet() ? 2 : 1] = 'Index';
    }

    /* -------------------------------------------------------------------------------------------------- */

    /**
     * @return mixed|string
     */
    public static function _getLanguage()
    {
        return self::$routingInstance->getLanguage();
    }

    /**
     * @param bool $includeNamespace
     * @param bool $useFullName
     * @return string
     */
    public static function _getController($includeNamespace = false, $useFullName = false)
    {
        return self::$routingInstance->getController($includeNamespace, $useFullName);
    }

    /**
     * @param bool $useExtension
     * @param bool $useFullPath
     * @return string
     */
    public static function _getView($useExtension = false, $useFullPath = false)
    {
        return self::$routingInstance->getView($useExtension, $useFullPath);
    }


    /**
     * @return mixed|null
     */
    public static function _getIdentifier()
    {
        return self::$routingInstance->getIdentifier();
    }

    /**
     * @return bool
     */
    public static function _isValidRoute()
    {
        return self::$routingInstance->isValidRoute();
    }

    /**
     * @param $routingInstance
     */
    private static function initStaticVariables($routingInstance)
    {
        self::$routingInstance = $routingInstance;
        self::$_HOST = $_SERVER['SERVER_NAME'];
        self::$_PROTOCOL = $_SERVER['SERVER_PROTOCOL'];
        self::$_REQUEST_URI = $_SERVER['REQUEST_URI'];
        self::$_DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
        self::$_VIEW_DIRECTORY = self::$_DOCUMENT_ROOT . '/comad/views/';
    }

}
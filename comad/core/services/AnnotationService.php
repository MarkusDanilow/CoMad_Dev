<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\services;


use comad\core\controllers\Controller;
use comad\core\services\annotations\AliasAnnotation;
use comad\core\services\annotations\BaseAnnotation;
use comad\core\services\annotations\HttpAnnotation;

/**
 * Class AnnotationService
 * @package comad\core\services
 */
class AnnotationService
{

    /**
     * @var BaseAnnotation array
     */
    protected static $_ANNOTATION_MAP = [];

    public static function _init()
    {
        self::$_ANNOTATION_MAP = [
            HttpAnnotation::$key => new HttpAnnotation(),
            AliasAnnotation::$key => new AliasAnnotation()
        ];
    }

    /**
     * @param Controller $controllerInstance
     * @param $view
     */
    public static function _handleAnnotations(Controller &$controllerInstance, &$view)
    {
        /* analyze annotations */
        $reflector = new \ReflectionClass(get_class($controllerInstance));
        $comment = $reflector->getMethod($view)->getDocComment();
        $annotations = RegexService::extractAnnotationsFromMethod($comment);
        foreach ($annotations as $key => $value) {
            $annotationHandler = self::$_ANNOTATION_MAP[$key];
            $annotationHandler->setValue($value);
            $annotationHandler->handle($key, RoutingService::$_HTTP_METHOD);
        }
    }


}
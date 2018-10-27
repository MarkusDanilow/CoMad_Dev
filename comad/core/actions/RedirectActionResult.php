<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\actions;


/**
 * Class RedirectAction
 * @package comad\core\actions
 */
class RedirectActionResult implements IActionResult
{


    /**
     * @var string
     */
    protected $redirectUrl;

    /**
     * @var int
     */
    protected $statusCode = 303;

    /**
     * RedirectAction constructor.
     * @param $url
     * @param int $statusCode
     */
    public function __construct($url, $statusCode = 303)
    {
        $this->redirectUrl = $url;
        $this->statusCode = $statusCode;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        header("Location: " . $this->redirectUrl);
        exit();
    }
}
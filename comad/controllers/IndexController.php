<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\controllers;

use comad\core\actions\ViewActionResult;
use comad\core\controllers\Controller;
use comad\core\data\repo\UserRepository;
use comad\core\services\ViewDataService;
use comad\models\UserModel;

/**
 * Class IndexController
 * @package comad\controllers
 */
class IndexController extends Controller
{

    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * IndexController constructor.
     */
    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    /**
     * @[Alias=Home]
     */
    public function index()
    {

        $user = $this->userRepo->findByName('madinow');
        ViewDataService::_set(ViewDataService::VIEW_MODEL, $user);

        ViewDataService::_set(ViewDataService::TITLE, 'Home');
        return new ViewActionResult();
    }

}
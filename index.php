<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/comad/core/CoMadAutoloader.php';

use comad\core\Application;
use comad\core\ComadAutoloader;

ComadAutoloader::register();

new Application();

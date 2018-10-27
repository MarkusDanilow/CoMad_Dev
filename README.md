# CoMad_Dev

## 1. Initialization
The *.htaccess* file will redirect all requests to the *index.php* file in the root directory. Here the initialization of the entire application happens.

´´´php
require_once 'comad/core/CoMadAutoloader.php';

use comad\core\Application;
use comad\core\ComadAutoloader;

ComadAutoloader::register();

new Application();
´´´


# CoMad_Dev

## 1. Initialization
The *.htaccess* file will redirect all requests to the *index.php* file in the root directory. Here the initialization of the entire application happens. 

```php
require_once 'comad/core/CoMadAutoloader.php';

use comad\core\Application;
use comad\core\ComadAutoloader;

ComadAutoloader::register();

new Application();
```
By registering the *CoMadAutoloader* all classes will be included automatically, independent of their namespace. After that we create a new application instance.
The application  will handle the entire routing itself.

## 2. Routing
A URL in the CoMad framework must always match the following pattern: 

***http(s)://hostname/[language]/{controller}/{view}/{identifier}***




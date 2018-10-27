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

***http(s)://hostname/[language]/{controllerName}/{actionName}/{identifier}***

The language is optional. If no language is specified, the default language defined in the *LanguageService* will be selected.
If no controller is specified, the *IndexController* will be used. The action *index* of the corresponding controller is the default action.
An identifier will only be necessary for requesting data. By default the object's ID will be used as a unique identifier.

If the route is invalid, the *ErrorController* with is *index* action will be use instead and a 404 age is displayed.

You can always get all parts of the URL by using the following static methods of the application: 

```php
// returns the current language
Application::_getInstance()->getLanguage();

// returns an instance of the current controller
Application::_getInstance()->getController();

// returns the current view as string. 
// $useExtension specifies, whether the file extension shall be appended.
// $useFullPath specifies, whether the entire path or only the file name of the view shall be returned.
Application::_getInstance()->getView($useExtension = false, $useFullPath = false);

// returns the identifier
Application::_getInstance()->getIdentifier();
```

## 3. Controller
A controller always has to be placed inside the directory *comad/controllers/*. Also its name has to match the pattern *{controllerName}Controller.php*. Taking the *IndexController*, a controller class looks like this:

```php
namespace comad\controllers;

use comad\core\actions\ViewActionResult;
use comad\core\controllers\Controller;

/**
 * Class IndexController
 * @package comad\controllers
 */
class IndexController extends Controller
{

    /**
     * The index action
     */
    public function index()
    {
        return new ViewActionResult();
    }

}
```

The names of the public methods must match with the actions that can be sepecified in the URL. In this case the *IndexController* only has a single action named *index*. This action does nothing but return the view. This is done by using a new *ViewActionResult*. It will detect the *{actionName}.php* file in the corresponding view directory *views/{controllerName}/*.

## 4. Views
A view always needs to be a PHP file. 

## 5. Layout

# CoMad_Dev

## Initialization
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

## Routing
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
## Repositories and Database Models
There is a basic repository interface called *IComadRepository* which offers the following methods to retreive and maniulate data stored in the database: 

```phpinterface IComadRepository
{

    public function find($id);

    public function save(DatabaseModel $model);

    public function remove(DatabaseModel $model);

}

```
A concrete respository implementation then could look like this: 

```php
class UserRepository implements IComadRepository
{

    public function find($id)
    {
        return UserModel::getById($id);
    }

    public function findByName($name)
    {
        $result = DbContext::execute("SELECT * FROM " . UserModel::$tableName . " WHERE name = ?", array($name), false,             UserModel::$tableName, false);
        return UserModel::process($result);
    }

    public function save(DatabaseModel $model)
    {
        $model->save();
    }
    
    public function remove(DatabaseModel $model)
    {
        $model->delete();
    }
}
```
This is a repository for users. It extends the original functionality given by the interface by a method to get a single user by its name. The data is returned as database models. In this case a user is a *UserModel* object. 
We have an abstract class called *DatabaseModel*. All further models need to be inherited from this class. So here is the *UserModel* class from our example.

```php
class UserModel extends DatabaseModel
{

    public static $tableName = 'cm_users';

}
```

All the functionality like saving or deleting data is already implemented in the abstract *DatabaseModel* class and can be used from all other models without having to overwrite the methods. When creating a new model class, all you have to do is to set the static field *tableName* to the name of the corresponding database table. All columns will be automatically accessible in the model, which means you do not have to set define the database columns as model attributes by yourself. 


## Controllers
A controller always has to be placed inside the directory *comad/controllers/*. Also its name has to match the pattern *{controllerName}Controller.php*. Taking the *IndexController*, a controller class looks like this:

```php
namespace comad\controllers;

use comad\core\actions\ViewActionResult;
use comad\core\controllers\Controller;
use comad\core\services\ViewDataService;

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
        ViewDataService::_set(ViewDataService::TITLE, 'Home');
        return new ViewActionResult();
    }

}
```

The names of the public methods must match with the actions that can be sepecified in the URL. In this case the *IndexController* only has a single action named *index*. This action does nothing but return the view. This is done by using a new *ViewActionResult*. It will detect the *{actionName}.php* file in the corresponding view directory *views/{controllerName}/*. 
All data that needs to be passed to the view can be set by using the *ViewDataService*. Calling the *_set* method you can store data as key-value-pair and access it in the view afterwards. This way you can also pass database models - which will be discussed later on - to the view and display the model's data there.

## Views
A view always needs to be a PHP file. It can contain HTML as well as PHP code. The view file *views/index/indx.php* for our example looks like this.

```php
<h1>Homepage</h1>
```

## Layout
The *_layout* view is the view that will always be rendered as skeletton of the page. It can contain static elements like the header, footer or the navigation bar. It can look something like this: 

```php
<?php
use comad\core\Application;
use comad\core\services\ViewDataService;

?>
<!DOCTYPE html>
<html>
<head>
    <title>CoMad - <?php print ViewDataService::_get(ViewDataService::TITLE); ?></title>
</head>
<body>

<?php
Application::_renderView();
?>

</body>
</html>
```

As you can see the view data attribute *title*, which was set in the controller's action, is accessed and printed as the page title by using the *ViewDataService*.
Inside the body we request the current view from the application. In our example the *index.php* from section 4 will be included in here.

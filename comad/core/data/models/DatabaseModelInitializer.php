<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\data\models;

/**
 * This class initializes all database models.
 *
 * Class DatabaseModelInitializer
 */
class DatabaseModelInitializer
{

    /**
     * Will be invoked at the start of the application.
     * Initializes all non-abstract database models automatically via autoloading and reflection.
     *
     * @param null $dir
     */
    public static function initializeModels($dir = null)
    {

        /*
        $files = [
            'BookModel', 'ConfigurationModel', 'EmployeesModel',
            'GraphModel', 'UserModel', 'VersioningModel'
        ];

        foreach ($files as $modelClass) {
            $modelClass::initialize();
        }
        */

    }

}


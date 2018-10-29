<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\data\repo;

use comad\core\data\DbContext;
use comad\core\data\models\DatabaseModel;
use comad\models\UserModel;

/**
 * Class UserRepository
 * @package comad\core\data\repo
 */
class UserRepository implements IComadRepository
{

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return UserModel::getById($id);
    }

    /**
     * @param $name
     * @return array|DatabaseModel|null
     */
    public function findByName($name)
    {
        $result = DbContext::execute("SELECT * FROM " . UserModel::$tableName . " WHERE name = ?", array($name), false, UserModel::$tableName, false);
        return UserModel::process($result);
    }

    /**
     * @param DatabaseModel $model
     * @return mixed
     */
    public function save(DatabaseModel $model)
    {
        $model->save();
    }

    /**
     * @param DatabaseModel $model
     * @return mixed
     */
    public function remove(DatabaseModel $model)
    {
        $model->delete();
    }
}
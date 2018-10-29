<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\data\repo;

use comad\core\data\models\DatabaseModel;

/**
 * Interface IComadRepository
 * @package comad\core\data\repo
 */
interface IComadRepository
{

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param DatabaseModel $model
     * @return mixed
     */
    public function save(DatabaseModel $model);

    /**
     * @param DatabaseModel $model
     * @return mixed
     */
    public function remove(DatabaseModel $model);

}
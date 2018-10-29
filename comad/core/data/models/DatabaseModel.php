<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

/**
 *
 * This class maps the data contained in a single database table and provides a list of specific database operation that can be performed for this very model.
 * All queries to the database should be performed by calling these methods instead of working directly with the database adapter!
 *
 * All database models must inherit this class!
 *
 * Class DatabaseModel
 *
 */
class DatabaseModel implements JsonSerializable
{

    /**
     * Name of the database table needs to adjusted with every model class
     * @var
     */
    public static $tableName;

    /**
     * Array for different levels of detail.
     * The smaller the index, the less data will be requested from the database.
     * The larger the index, the more data will be requested from the database.
     * @var array
     */
    protected static $_levelsOfDetail;

    /**
     * List of fields: is automatically mapped with the database
     * @var array
     */
    protected $_data;

    /**
     * List of fields that have been modified. Will be reset every time the model is saved
     * @var array
     */
    protected $_modified = array();

    /**
     * XSS mode for loading or escaping HTML code that comes from the database
     * @var int
     */
    protected static $explicitXSSMode = 0;

    /**
     * Constructor receives a list of fields / properties right from the database
     * DatabaseModel constructor.
     * @param array $properties
     */
    public function __construct(Array $properties = array())
    {
        $this->_data = $properties;
    }

    /**
     * Magic method for setting a single property
     * @param $property
     * @param $value
     * @return mixed
     */
    public function __set($property, $value)
    {
        $field = $this->_data[$property] = $value;
        $this->_modified[$property] = true;
        return $field;
    }

    /**
     * Magic method for accessing and returning a single property
     * @param $property
     * @return mixed|null
     */
    public function __get($property)
    {
        return array_key_exists($property, $this->_data) ? $this->_data[$property] : null;
    }

    /**
     * Truncates an entire table
     */
    public static function truncateTable()
    {
        DbContext::execute("TRUNCATE TABLE " . static::$tableName);
    }

    /**
     * Returns all fields as associative array.
     * @return array
     */
    public function getAllData()
    {
        return $this->_data;
    }

    /**
     * Applies an entire array of data to the model
     * @param $data
     */
    public function setAllData($data)
    {
        if (!isset($data))
            return;
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * saves a single model with modified attributes to the database.
     */
    public function save()
    {
        $modifiedPropertyKeys = array_keys($this->_modified);
        $modifiedProperties = array();
        foreach ($modifiedPropertyKeys as $propertyKey) {
            array_push($modifiedProperties, $this->_data[$propertyKey]);
        }
        array_push($modifiedProperties, $this->id);
        DbContext::execute('UPDATE ' . self::getTableName() . ' SET ' .
            self::generatePropertyKeyList($modifiedPropertyKeys, false, true) . ' WHERE id = ?', $modifiedProperties);
        $this->_modified = array();
    }

    /**
     * Removes an entry from the database
     */
    public function delete()
    {
        DbContext::execute('DELETE FROM ' . self::getTableName() . ' WHERE id = ?', array($this->id));
    }

    /**
     * Converts the database model to a json object.
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->_data;
    }

    /**
     * Simplifies a model, so only the necessary data is left.
     * Can be used to keep data secret to the frontend, e.g. user password, credentials, etc.
     * This will be performed after the data has been loaded from the database.
     * For simplifying a model during the database request in order to increase the database performance,
     * levels of details must bei used.
     * @return mixed
     */
    public function simplify()
    {
        $model = self::instantiateCalledClazz();
        $model->id = $this->id;
        return $model;
    }

    /**
     * Static initializer for each database model.
     * Can be used the define the individual levels of detail.
     * Must be implemented in each database model class.
     */
    public static function initialize()
    {
        // not implemented in the abstract database model
    }

    /**
     * Creates a new entry in the database and returns this entry
     * @param array $data
     * @return mixed
     */
    public static function createNewModel($data = array())
    {
        $keys = array_keys($data);
        $values = array_values($data);
        $keyList = ' ' . self::generatePropertyKeyList($keys, true, false);
        $placeholders = self::generateValuePlaceholders(sizeof($values));
        $databaseResult = DbContext::execute('INSERT INTO ' . self::getTableName() . $keyList . ' VALUES ' .
            $placeholders, $values, true, self::getTableName());
        $result = array_pop($databaseResult);
        return self::convertToModel($result);
    }

    /**
     * Recreates a model. Deletes the old model and saves the data as a new entry.
     * The data can either be passed as a new array. Otherwise the data will be taken from the old model.
     * @param DatabaseModel $model
     * @param array $data
     */
    public static function recreate($model, $data = array())
    {
        if (isset($model)) {
            if (!isset($data))
                $data = $model->getAllData();
            $model->setAllData($data);
            $model->save();
        } else if (isset($data)) {
            self::createNewModel($data);
        }
    }

    /**
     * Returns the name of the table for a specific model
     * @return string
     */
    protected static function getTableName()
    {
        return static::$tableName;
    }

    /**
     * @return string
     */
    public static function getCalledClazz()
    {
        return get_called_class();
    }

    /**
     * @param array $params
     * @return mixed
     */
    public static function instantiateCalledClazz($params = array())
    {
        $clazz = self::getCalledClazz();
        return new $clazz($params);
    }


    /**
     * Converts an array of plain database entries to an array of corresponding database models
     * @param array $dbArray
     * @return array
     */
    public static function convertArrayToModels($dbArray = array())
    {
        $models = array();
        foreach ($dbArray as $dbEntry) {
            array_push($models, self::convertToModel($dbEntry));
        }
        return $models;
    }

    /**
     * Converts a single plain database entry to the corresponding database model
     * @param array $dbEntry
     * @return DatabaseModel|null
     */
    public static function convertToModel($dbEntry = array())
    {
        if (!isset($dbEntry)) return null;
        return self::instantiateCalledClazz($dbEntry);
    }

    /**
     * Processes the entire database result.
     * Resuls will either be converted to a list of models or a single model.
     * @param $databaseResult
     * @param bool $asArray
     * @return array|DatabaseModel|null
     */
    public static function process($databaseResult, $asArray = false)
    {
        $databaseResult = $asArray ? $databaseResult : array_pop($databaseResult);
        $result = $asArray ? self::convertArrayToModels($databaseResult) : self::convertToModel($databaseResult);
        return $result;
    }


    /**
     * Defines the different levels of detail for a database model.
     * @param array $lods
     */
    protected static function defineLevelsOfDetail($lods = array())
    {
        if (!isset($lods))
            $lods = array();
        static::$_levelsOfDetail = $lods;
    }

    /**
     * Will convert a specific level of detail set to an SQL query
     * @param int $level
     * @return string
     */
    protected static function convertLevelOfDetailToSQL($level = -1)
    {
        if (!is_numeric($level)) {
            return 'id, ' . self::generatePropertyKeyList($level, false, false);
        } else if (isset(static::$_levelsOfDetail) && $level < sizeof(static::$_levelsOfDetail) && $level > -1) {
            if (isset(static::$_levelsOfDetail[$level])) {
                return 'id, ' . self::generatePropertyKeyList(static::$_levelsOfDetail[$level], false, false);
            }
        }
        return ' * ';
    }

    /**
     * Checks whether the table has any entries or not
     * @return bool
     */
    public static function isEmpty()
    {
        return self::getNumRows() <= 0;
    }

    /**
     * Returns an object containing the number of entries in a table.
     * @return array|DatabaseModel|null
     */
    public static function getNumRows()
    {
        return self::process(
            DbContext::execute("SELECT COUNT(*) AS count FROM " . self::getTableName()), false
        )->count;
    }

    /**
     * Returns all entries from the database table
     * @param int $limit
     * @param int $offset
     * @param int $lod
     * @return array|DatabaseModel|null
     */
    public static function getAll($limit = 100, $offset = 0, $lod = -1)
    {
        return self::getAllAsc($limit, $offset, $lod);
    }

    /**
     * Returns all entries from the database table in descending order
     * @param int $limit
     * @param int $offset
     * @param int $lod
     * @return array|DatabaseModel|null
     */
    public static function getAllDesc($limit = 100, $offset = 0, $lod = -1)
    {
        return self::process(
            DbContext::executeWithBindings(
                "SELECT " . self::convertLevelOfDetailToSQL($lod) . " FROM " . static::$tableName . " ORDER BY id DESC LIMIT ?,?",
                array($offset, $limit),
                array(PDO::PARAM_INT, PDO::PARAM_INT)
            ),
            true
        );
    }

    /**
     * Returns all entries from the database table in ascending order
     * @param int $limit
     * @param int $offset
     * @param int $lod
     * @return array|DatabaseModel|null
     */
    public static function getAllAsc($limit = 100, $offset = 0, $lod = -1)
    {
        return self::process(
            DbContext::executeWithBindings(
                "SELECT " . self::convertLevelOfDetailToSQL($lod) . " FROM " . static::$tableName . " ORDER BY id ASC LIMIT ?,?",
                array($offset, $limit),
                array(PDO::PARAM_INT, PDO::PARAM_INT)
            ),
            true
        );
    }

    /**
     * Returns a single database entry based on the entries ID.
     * @param $id
     * @param int $limit
     * @param int $offset
     * @param int $lod
     * @return array|DatabaseModel|null
     */
    public static function getById($id, $limit = 100, $offset = 0, $lod = -1)
    {
        return self::process(
            DbContext::executeWithBindings(
                "SELECT " . static::convertLevelOfDetailToSQL($lod) . " FROM " . static::$tableName . " WHERE id = ? LIMIT ?,?",
                [$id, $offset, $limit], [PDO::PARAM_INT, PDO::PARAM_INT, PDO::PARAM_INT]
            ),
            false
        );
    }

    /**
     * Returns multiple database entries by their individual IDs.
     * @param array $ids
     * @param int $limit
     * @param int $offset
     * @param int $lod
     * @return array|DatabaseModel|null
     */
    public static function getMultipleModelsById($ids = array(), $limit = 100, $offset = 0, $lod = -1)
    {
        $in = '(' . join(',', $ids) . ')';
        return self::process(
            DbContext::executeWithBindings(
                "SELECT " . self::convertLevelOfDetailToSQL($lod) . " FROM " . static::$tableName . " WHERE id IN " . $in . " LIMIT ?, ?",
                array($offset, $limit),
                array(PDO::PARAM_INT, PDO::PARAM_INT)
            ),
            true
        );
    }

    /**
     * ONLY for GET!!!
     * Inserts, Updates and Deletes must be implemented in custom specific methods to avoid abuse and SQL injections
     *
     * @param array $parameters
     * @return null
     */
    public static function genericDatabaseRequest($parameters = array())
    {

        try {
            $excludedFromWhereClause = array('lod', 'limit', 'offset', 'orderBy', 'groupBy');

            $requestParams = array();
            $requestTypes = array();

            $whereClauseArray = array();

            foreach ($parameters as $key => $parameter) {
                if (isset($parameter) && !in_array($key, $excludedFromWhereClause)) {
                    $innerWhereClauseArray = array();
                    if (is_array($parameter)) {
                        for ($i = 0; $i < sizeof($parameter['singleValues']); $i++) {
                            $value = $parameter['singleValues'][$i];
                            array_push($innerWhereClauseArray, " " . $key . " = ? ");
                            array_push($requestParams, $value);
                            array_push($requestTypes, is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
                        }
                        for ($i = 0; $i < sizeof($parameter['lowerBounds']); $i++) {
                            $lower = $parameter['lowerBounds'][$i];
                            $upper = $parameter['upperBounds'][$i];
                            array_push($innerWhereClauseArray, " " . $key . " BETWEEN ? AND ? ");
                            array_push($requestParams, $lower);
                            array_push($requestParams, $upper);
                            array_push($requestTypes, is_numeric($lower) ? PDO::PARAM_INT : PDO::PARAM_STR);
                            array_push($requestTypes, is_numeric($upper) ? PDO::PARAM_INT : PDO::PARAM_STR);
                        }
                    } else {
                        array_push($innerWhereClauseArray, " " . $key . " = ? ");
                        array_push($requestParams, $parameter);
                        array_push($requestTypes, is_numeric($parameter) ? PDO::PARAM_INT : PDO::PARAM_STR);
                    }
                    array_push($whereClauseArray, "( " . join(" OR ", $innerWhereClauseArray) . " )");
                }
            }

            $whereClause = sizeof($whereClauseArray) > 0 ? " WHERE " . join(" AND ", $whereClauseArray) : "";

            $orderByClause = '';
            if (isset($parameters['orderBy'])) {
                $orderValue = $parameters['orderBy'];
                $orderFactor = ' ASC';
                $orderFactorFound = substr($orderValue, 0, 1);
                if ($orderFactorFound === '+' || $orderFactorFound === '-') {
                    $orderFactor = $orderFactorFound === '+' ? ' ASC' : ' DESC';
                    $orderValue = str_replace($orderFactorFound, '', $orderValue);
                }
                $orderByClause = " ORDER BY " . $orderValue . $orderFactor;
            }

            $groupByClause = '';
            if (isset($parameters['groupBy'])) {
                $groupValue = $parameters['groupBy'];
                $groupByClause = " GROUP BY " . $groupValue;
            }

            $lod = isset($parameters['lod']) ? $parameters['lod'] : -1;
            $limit = isset($parameters['limit']) ? $parameters['limit'] : 500;
            $offset = isset($parameters['offset']) ? $parameters['offset'] : 0;

            array_push($requestParams, $offset);
            array_push($requestTypes, PDO::PARAM_INT);
            array_push($requestParams, $limit);
            array_push($requestTypes, PDO::PARAM_INT);

            $sql = "SELECT " . self::convertLevelOfDetailToSQL($lod) . " FROM " . static::$tableName .
                $whereClause . $groupByClause . $orderByClause . " LIMIT ?, ?";

            XSS_Service::useExplicitMode(static::$explicitXSSMode);
            $data = self::process(DbContext::executeWithBindings($sql, $requestParams, $requestTypes), true);
            XSS_Service::disableExplicitMode();

            return $data;

        } catch (Exception $e) {

        }

        return array();

    }

    /**
     * Generates a number of placeholders for generic database queries based on a variable list of parameters.
     * @param $num
     * @return string
     */
    protected static function generateValuePlaceholders($num)
    {
        if ($num < 0) $num = 0;
        return '(' . join(', ', array_fill(0, $num, '?')) . ')';
    }

    /**
     * Generates a list of property names, separated by commas, for database only.
     * Can be used for inserting or requesting data
     *
     * @param array $propertyNames
     * @param bool $useBrackets
     * @param bool $useForUpdate
     * @return string
     */
    protected static function generatePropertyKeyList($propertyNames = array(), $useBrackets = false, $useForUpdate = false)
    {
        $openingBracket = ($useBrackets ? '(' : '');
        $closingBracket = ($useBrackets ? ')' : '');
        if ($useForUpdate) {
            array_walk($propertyNames, function (&$item) {
                $item .= ' = ?';
            });
        }
        return $openingBracket . join(', ', $propertyNames) . $closingBracket;
    }


}
<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\data;

use comad\core\Core;
use comad\core\data\models\DatabaseModel;
use comad\core\data\models\VersioningModel;
use comad\core\services\RegexService;

/**
 * This class automatically creates a new version of anything that is modified in the database.
 * The versions of all entries are stored in the version table inside the database.
 *
 * Class VersionControl
 */
class VersionControl
{

    /*
     * MySQL queries to handle:
     * (x) Insert Into (=> models will be received from DatabaseAdapter as last inserted elements )
     * (x) Update (=> analyze WHERE clause )
     * (x) Delete (=> analyze WHERE clause )
     * - Drop Database
     * (x) Drop Table
     * (x) Truncate Table
     */

    /**
     * List of queries that need to be handled  before the database changes are made.
     * @var array
     */
    protected static $preChangeQueries = [
        'delete from', 'drop table', 'drop database', 'truncate table'
    ];

    /**
     * List of queries that can or must be handled after the changes have been made to the database.
     * @var array
     */
    protected static $postChangeQueries = [
        'insert into', 'update'
    ];

    /**
     * @var array
     */
    protected static $queryMethodMap = [
        'delete from' => 'handleDeleteFrom',
        'drop table' => 'handleTableChanges',
        'truncate table' => 'handleTableChanges',
        'drop database' => 'handleDropDatabase',
        'insert into' => 'handleInsertInto',
        'update' => 'handleUpdate'
    ];

    /**
     * Handle queries before changes are made to the database.
     * @param string $query
     * @param array $params
     */
    public static function preChangeManagement($query = '', $params = array())
    {
        $versionStatus = $GLOBALS['system']['versioning']['enabled'];
        $GLOBALS['system']['versioning']['enabled'] = false;
        self::handleChanges(self::$preChangeQueries, $query, $params, null);
        $GLOBALS['system']['versioning']['enabled'] = $versionStatus;
    }

    /**
     * Handle queries after changes have been made to the database.
     * @param string $query
     * @param array $params
     * @param array $affectedItems
     */
    public static function postChangeManagement($query = '', $params = array(), $affectedItems = array())
    {
        $versionStatus = $GLOBALS['system']['versioning']['enabled'];
        $GLOBALS['system']['versioning']['enabled'] = false;
        self::handleChanges(self::$postChangeQueries, $query, $params, $affectedItems);
        $GLOBALS['system']['versioning']['enabled'] = $versionStatus;
    }

    /**
     * General and generic way of handling pre-changes as well as post-changes
     * @param array $queryArray
     * @param string $query
     * @param array $params
     * @param null $affectedItems
     */
    private static function handleChanges($queryArray = array(), $query = '', $params = array(), $affectedItems = null)
    {
        if (isset($query) && isset($queryArray)) {
            $queryLowerCase = strtolower($query);
            $queryFound = self::extractChangeQuery($queryArray, $queryLowerCase);
            if (isset($queryFound)) {
                $tableOrDatabaseName = Core::getNextWordInString($queryLowerCase, $queryFound);
                $methodName = self::$queryMethodMap[$queryFound];
                if (method_exists(get_called_class(), $methodName)) {
                    call_user_func_array(get_called_class() . '::' . $methodName, array($tableOrDatabaseName, $query, $params, $affectedItems));
                }
            }
        }
    }

    /**
     * @param array $queryArray
     * @return null|string
     */
    protected static function convertChangeQueriesToRegex_Or($queryArray = array())
    {
        if (!isset($queryArray))
            return null;
        return '#(' . implode('|', $queryArray) . ')#';
    }

    /**
     * @param array $queryArray
     * @param string $query
     * @return string
     */
    protected static function extractChangeQuery($queryArray = array(), $query = '')
    {
        if (strpos(strtolower($query), 'select') < 0)
            return null;
        $pattern = self::convertChangeQueriesToRegex_Or($queryArray);
        if (isset($pattern)) {
            RegexService::matchesAll($pattern, $query, $matches);
            return $matches[1][0];
        }
        return null;
    }

    /**
     * @param $query
     * @return array
     */
    private static function getWhereClauseFromQuery($query, $params = array())
    {
        if (!isset($query) || !isset($params))
            return [null, null];
        RegexService::extractWhereClauseFromQuery($query, $whereClauseMatch);
        $whereClause = $whereClauseMatch[0];
        $indexOfWhereClause = strpos(strtolower($query), strtolower($whereClause));
        $preWhereClause = substr($query, 0, $indexOfWhereClause);
        $occurrencesTotal = substr_count($preWhereClause, '?');
        $occurrencesWhere = substr_count($whereClause, '?');
        $offset = $occurrencesTotal - $occurrencesWhere - 1;
        $whereParams = array_slice($params, $occurrencesTotal);
        return array($whereClause, $whereParams);
    }

    /* --------------------------------------------------------------------------------------------------
     *      HERE COME THE DIFFERENT METHODS FOR HANDLING THE DATABASE QUERIES
     * --------------------------------------------------------------------------------------------------  */

    /**
     * @param $databaseName
     */
    private static function handleDropDatabase($databaseName)
    {
        echo 'now dropping database...' . $databaseName;
    }

    /**
     * @param $tableName
     */
    private static function handleTableChanges($tableName)
    {
        $tableName = RegexService::replaceAllWhitespaces($tableName);
        if ($GLOBALS['system']['versioning']['backups']['enabled']) {
            $timestamp = time();
            $backupTableName = 'bak_' . $tableName . '_' . $timestamp;
            DbContext::execute('CREATE TABLE ' . $backupTableName . ' LIKE ' . $tableName, null, false, null, true);
            DbContext::execute('INSERT INTO ' . $backupTableName . ' SELECT * FROM ' . $tableName, null, false, null, true);
        }
        $serialized = Core::binarySerialize(DbContext::execute("SELECT * FROM " . $tableName));
        VersioningModel::createNewVersionEntry('table_' . $tableName, $serialized);
    }

    /**
     * @param $tableName
     * @param string $query
     * @param array $params
     *
     */
    private static function handleDeleteFrom($tableName, $query = '', $params = array())
    {
        $whereClauseInfo = self::getWhereClauseFromQuery($query, $params);
        $entries = DatabaseModel::process(
            DbContext::execute("SELECT * FROM " . $tableName . " " . $whereClauseInfo[0], $whereClauseInfo[1]), true
        );
        if ($entries != null && sizeof($entries) > 0) {
            foreach ($entries as $entry) {
                VersioningModel::createNewVersionEntry($tableName . '_' . $entry->id, serialize($entry->getAllData()));
            }
        }
    }

    /**
     * @param $tableName
     * @param $query
     * @param $params
     * @param $affectedRows
     */
    private static function handleInsertInto($tableName, $query, $params, $affectedRows)
    {
        $models = DatabaseModel::convertArrayToModels($affectedRows);
        if (isset($models) && sizeof($models) > 0) {
            foreach ($models as $model) {
                VersioningModel::createNewVersionEntry($tableName . '_' . $model->id, serialize($model->getAllData()));
            }
        }
    }

    /**
     * @param $tableName
     * @param $query
     * @param $params
     */
    private static function handleUpdate($tableName, $query, $params)
    {
        $whereClauseInfo = self::getWhereClauseFromQuery($query, $params);
        $entries = DatabaseModel::process(
            DbContext::execute("SELECT * FROM " . $tableName . " " . $whereClauseInfo[0], $whereClauseInfo[1]), true
        );
        if ($entries != null && sizeof($entries) > 0) {
            foreach ($entries as $entry) {
                VersioningModel::createNewVersionEntry($tableName . '_' . $entry->id, serialize($entry->getAllData()));
            }
        }
    }

}
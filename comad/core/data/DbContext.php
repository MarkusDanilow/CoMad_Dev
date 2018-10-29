<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

use comad\core\Config;
use comad\core\services\Core;

/**
 * The essential class for all database requests!
 *
 * Class DatabaseAdapter
 */
class DbContext
{

    /**
     * Database connection instance
     * @var PDO
     */
    private static $connection = null;

    /**
     * connects to the database
     */
    private static function connect()
    {
        self::$connection = new PDO("mysql:dbname=" . Config::DB_NAME .
            ";host=" . Config::DB_HOST . ";charset=utf8",
            Config::DB_USER, Config::DB_PASSWORD);
    }

    /**
     * disconnects from database
     */
    private static function disconnect()
    {
        self::$connection = null;
    }

    /**
     * Checks whether the database adapter is connected to the database or not.
     * @return bool
     */
    public static function isConnected()
    {
        return isset(self::$connection);
    }

    /**
     * performs a query on the database and returns the corresponding result
     *
     * @param $query
     * @param array $params
     * @param bool $isInsert
     * @param null $dbTable
     * @param bool $disableVersioning
     * @return array
     */
    public static function execute($query, $params = array(), $isInsert = false, $dbTable = null, $disableVersioning = false)
    {

        $results = array();

        if (Config::DB_VERSIONING && !$disableVersioning) {
            VersionControl::preChangeManagement($query, $params);
        }

        self::connect();
        $statement = self::$connection->prepare($query);

        self::$connection->beginTransaction();

        try {

            if (!$statement->execute($params))
                throw new DatabaseExecutionException();

            $results = self::getLastInsertedElement($statement, $isInsert, $dbTable)->fetchAll(PDO::FETCH_ASSOC);

            self::$connection->commit();

        } catch (Exception $e) {
            self::$connection->rollBack();
        }

        if (Config::DB_VERSIONING && !$disableVersioning) {
            VersionControl::postChangeManagement($query, $params, $results);
        }

        self::disconnect();

        Core::protectFromXSS($results);

        return $results;
    }


    /**
     * performs a query on the database and returns the corresponding result.
     * This function does not use anonymous parameters, but binds the values to the PDO statement.
     *
     * @param $query
     * @param array $params
     * @param array $types
     * @param bool $isInsert
     * @param null $dbTable
     * @param bool $disableVersioning
     * @return array
     */
    public static function executeWithBindings($query, $params = array(), $types = array(), $isInsert = false, $dbTable = null, $disableVersioning = false)
    {

        $results = array();

        if (sizeof($params) == sizeof($types)) {

            if (Config::DB_VERSIONING && !$disableVersioning) {
                VersionControl::preChangeManagement($query, $params);
            }

            self::connect();

            try {

                self::$connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $statement = self::$connection->prepare($query);

                $index = 0;
                foreach ($params as $value) {
                    $statement->bindValue($index + 1, $value, $types[$index]);
                    $index++;
                }

                if (!$statement->execute())
                    throw new DatabaseExecutionException();

                $results = self::getLastInsertedElement($statement, $isInsert, $dbTable)->fetchAll(PDO::FETCH_ASSOC);

            } catch (Exception $e) {
                self::$connection->rollBack();
            }

            self::disconnect();

            if (Config::DB_VERSIONING && !$disableVersioning) {
                VersionControl::postChangeManagement($query, $params, $results);
            }

        }

        Core::protectFromXSS($results);
        return $results;
    }

    /**
     * Returns the last element that has been inserted into the database
     *
     * @param $statement
     * @param $isInsert
     * @param $dbTable
     * @return PDOStatement
     */
    private static function getLastInsertedElement($statement, $isInsert, $dbTable)
    {
        if (self::isConnected() && $isInsert && isset($dbTable)) {
            $insertedId = self::$connection->lastInsertId();
            $statement = self::$connection->prepare('SELECT * FROM ' . $dbTable . ' WHERE id = ?');
            $statement->execute(array($insertedId));
            return $statement;
        }
        return $statement;
    }


}

<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\data\models;


/**
 * Class VersioningModel
 */
class VersioningModel extends DatabaseModel
{

    public static $tableName = 'tm_versioning';

    /**
     * Creates a new
     *
     * @param $versionKey
     * @param $serialized
     */
    public static function createNewVersionEntry($versionKey, $serialized)
    {
        $previousVersion = self::getLatestVersion($versionKey);
        $previousVersionExists = isset($previousVersion);
        if (!$previousVersionExists || ($previousVersionExists && strcmp($previousVersion->serializedVersion, $serialized) !== 0)) {
            $version = $previousVersionExists ? ($previousVersion->version + 1) : 0;
            $author = UserSessionService::getLoggedInUserMail();
            if (!isset($author) || is_array($author)) {
                $author = '-';
            }
            self::createNewModel([
                'versionKey' => $versionKey,
                'version' => $version,
                'author' => $author,
                'serializedVersion' => $serialized
            ]);
        }
    }

    /**
     * @param $versionKey
     * @return array|DatabaseModel|null
     */
    public static function getLatestVersion($versionKey)
    {
        return self::process(
            DatabaseAdapter::execute("SELECT * FROM " . self::$tableName . " WHERE versionKey = ? ORDER BY version DESC LIMIT 0,1", array($versionKey)),
            false
        );
    }

}
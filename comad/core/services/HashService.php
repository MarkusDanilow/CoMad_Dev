<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\services;

/**
 * Encryption tool
 *
 * Class PassHash
 */
class HashService
{

    /**
     * @var string
     */
    private static $algo = '$2a';

    /**
     * @var string
     */
    private static $cost = '$10';

    /**
     * creates a unique string using the sha1 algorithm
     * @return string
     */
    public static function unique_salt()
    {
        return substr(sha1(mt_rand()), 0, 22);
    }

    /**
     * Encrypts a password
     * @param $password
     * @return string
     */
    public static function hash($password)
    {
        return crypt($password,
            self::$algo .
            self::$cost .
            '$' . self::unique_salt());
    }

    /**
     * Checks whether an encrypted password matches the unencrypted one.
     * @param $hash
     * @param $password
     * @return bool
     */
    public static function checkPassword($hash, $password)
    {
        $full_salt = substr($hash, 0, 29);
        $new_hash = crypt($password, $full_salt);
        return ($hash == $new_hash);
    }

}
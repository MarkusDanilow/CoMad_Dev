<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\services;


/**
 * Class Core
 * @package comad\core\services
 */
class Core
{

    /**
     * @param $array
     * @return string
     */
    public static function convertToJSON($array)
    {
        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $object
     * @return string
     */
    public static function binarySerialize($object)
    {
        return isset($object) ? serialize($object) : '';
    }

    /**
     * @param $serialString
     * @return string
     */
    public static function binaryUnSerialize($serialString)
    {
        return isset($serialString) ? unserialize($serialString) : null;
    }

    /**
     * @param $html
     * @return string
     */
    public static function serializeHtml($html)
    {
        return $html;
        // return base64_encode($html);
    }

    /**
     * @param $serializedHtml
     * @return string
     */
    public static function unSerializeHtml($serializedHtml)
    {
        return $serializedHtml;
        // return base64_decode($serializedHtml);
    }

    /**
     * @param $data
     * @param int $mode
     * @param bool $useCustomElements
     * @param null $allowedElements
     * @return mixed
     */
    public static function protectFromXSS(&$data, $mode = 0, $useCustomElements = false, $allowedElements = null)
    {
        if (isset($data)) {
            array_walk_recursive($data, function (&$value) use ($mode, $useCustomElements, $allowedElements) {
                $value = XSS_Service::protectFromXSS($value, $mode, $useCustomElements, $allowedElements);
            });
        }
        return $data;
    }

    /**
     * @param $statusCode
     * @param string $message
     * @param null $data
     */
    public static function sendHTTPResponse($statusCode, $message = "", $data = null)
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' ' . $statusCode . ' ' . $message, true, $statusCode);
        echo self::convertToJSON(array('statusCode' => $statusCode, 'message' => $message, 'data' => $data));
        exit();
    }

    /**
     * @param $array
     * @param $keys
     * @param $value
     */
    public static function setMultiDimensionalArrayValue(&$array, $keys, $value)
    {
        $reference = &$array;
        foreach ($keys as $key) {
            if (!array_key_exists($key, $reference)) {
                $reference[$key] = [];
            }
            $reference = &$reference[$key];
        }
        $reference = $value;
        unset($reference);
    }

    /**
     * @param $array
     * @param $keys
     */
    public static function unsetMultiDimensionalArrayValue(&$array, $keys)
    {
        $reference = &$array;
        foreach ($keys as $key) {
            if (!array_key_exists($key, $reference)) {
                $reference[$key] = [];
            }
            $reference = &$reference[$key];
        }
        $reference = null;
        unset($reference);
    }

    /**
     * @param $array
     * @param $keys
     * @return mixed
     */
    public static function getMultiDimensionalArrayValue($array, $keys)
    {
        if (!isset($array) || !isset($keys) || sizeof($keys) <= 0)
            return null;
        $reference = &$array;
        foreach ($keys as $key) {
            if (array_key_exists($key, $reference)) {
                $reference = &$reference[$key];
            }
        }
        return $reference;
    }

    /**
     * @param $input
     * @param string $keysCSV
     * @param array $csvData
     */
    public static function convertMultidimensionalArrayToCSVStructure($input, $keysCSV = '', &$csvData = array())
    {
        if (is_array($input)) {
            foreach ($input as $key => $item) {
                self::convertMultidimensionalArrayToCSVStructure($item, $keysCSV . ',' . $key, $csvData);
            }
        } else {
            if (is_bool($input))
                $input = $input ? ' true' : 'false';
            array_push($csvData, $keysCSV . ':' . $input);
        }
    }

    /**
     * Returns all values from a multidimensional array
     * @param $array
     * @param array $values
     */
    public static function getAllValuesFromArray($array, &$values = array())
    {
        if (!isset($values))
            $values = array();
        foreach ($array as $item) {
            if (is_array($item)) {
                self::getAllValuesFromArray($item, $values);
            } else {
                array_push($values, $item);
            }
        }
    }

    /**
     * Returns the key hierarchy for a value in a multidimensional array
     * @param $array
     * @param $search
     * @param array $keys
     * @param int $offset
     * @return array
     */
    public static function findKeysInArrayByValue($array, $search, $keys = array(), $offset = 0)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $sub = self::findKeysInArrayByValue($value, $search, array_merge($keys, array($key)), $offset);
                if (count($sub)) {
                    if ($offset <= 0)
                        return $sub;
                }
            } else if ($value === $search) {
                if ($offset <= 0)
                    return array_merge($keys, array($key));
            }
        }
        return null;
    }

    /**
     * @param $array
     * @param array $keys
     * @param array $values
     */
    public static function setArrayValues(&$array, $keys = array(), $values = array())
    {
        if (!isset($array) || sizeof($keys) != sizeof($values))
            return;
        for ($i = 0; $i < sizeof($keys); $i++) {
            $array[$keys[$i]] = $values[$i];
        }
    }

    /**
     * @param $array
     * @param array $keys
     */
    public static function unsetArrayValues(&$array, $keys = array())
    {
        if (!isset($array))
            return;
        for ($i = 0; $i < sizeof($keys); $i++) {
            unset($array[$keys[$i]]);
        }
    }

    /**
     * @param $array
     */
    public static function unsetEntireArray(&$array)
    {
        foreach (array_keys($array) as $key)
            unset($array[$key]);
    }

    /**
     * @param $array
     * @param $search
     * @param $replace
     */
    public static function replaceInAllArrayKeys(&$array, $search, $replace)
    {
        foreach (array_keys($array) as $key) {
            $newKey = str_replace($search, $replace, $key);
            $array[$newKey] = $array[$key];
            unset($array[$key]);
        }
    }

    /**
     * @param $array
     * @param $add
     * @param bool $appendFlag
     */
    public static function addToAllArrayKeys(&$array, $add, $appendFlag = true)
    {
        foreach (array_keys($array) as $key) {
            $newKey = $appendFlag ? $key . $add : $add . $key;
            $array[$newKey] = $array[$key];
            unset($array[$key]);
        }
    }

    /**
     * @param string $string
     * @param bool $justGiveMePartBeforeParameters
     * @return mixed
     */
    public static function getKeyValuePairs($string = '', $justGiveMePartBeforeParameters = false)
    {
        $parts = explode('?', $string);
        if ($justGiveMePartBeforeParameters)
            return $parts[0];
        $parameters = array();
        if (sizeof($parts) > 1) {
            $parts = explode('&', $parts[1]);
            foreach ($parts as $param) {
                $tmp = explode('=', $param);
                $parameters[$tmp[0]] = $tmp[1];
            }
        }
        return $parameters;
    }

    /**
     * Creates a unique ID for any object
     * @return string
     */
    public static function guid()
    {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = chr(123)// "{"
                . substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12)
                . chr(125);// "}"
            return $uuid;
        }
    }

    /**
     * Creates a hash value from an array of data
     * @param array $dataToHash
     * @return string
     */
    public static function createHash($dataToHash = array())
    {
        $serialString = '';
        foreach ($dataToHash as $item) {
            $serialString .= self::binarySerialize($item);
        }
        return sha1($serialString);
    }


    /**
     * Returns the first n words of a database query
     * @param string $query
     * @param int $n
     * @return string
     */
    public static function getFirstNWordsOfQuery($query = '', $n = 1)
    {
        if (isset($query) && isset($n) && $n > 0) {
            RegexService::getFirstNWords($query, $n, $matches);
            return $matches[0];
        }
        return '';
    }

    /**
     * Returns the next word after a previous string in
     * @param string $string
     * @param string $previousString
     * @return mixed
     */
    public static function getNextWordInString($string = '', $previousString = '')
    {
        RegexService::getNextWord($previousString, $string, $matches);
        return $matches[2][0];
    }

    /**
     * Checks whether a string starts with a certain substring.
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * Checks whether a string ends with a certain substring.
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return $length === 0 ||
            (substr($haystack, -$length) === $needle);
    }

    /**
     * replaces all special characters and whitespaces in a string and converts it to lower case.
     * @param $string
     * @param bool $ignoreDots
     * @return mixed
     */
    public static function normalizeString($string, $ignoreDots = false)
    {
        return RegexService::normalizeString($string, $ignoreDots);
    }

    /**
     * Removes a directory and all files in it from the file system
     * @param $dir
     */
    public static function deleteDirectory($dir)
    {
        FileService::deleteDirectory($dir);
    }

}
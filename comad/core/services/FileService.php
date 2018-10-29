<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\services;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class FileService
 * @package comad\core\services
 */
class FileService
{

    /**
     * @param $filename
     * @return string
     */
    public static function buildTemplatePath($filename)
    {
        return $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['paths']['templates']['base'] . $filename . '.html';
    }

    /**
     * @param $file
     * @return bool|string
     */
    public static function getFileContent($file)
    {
        return isset($file) && file_exists($file) ? file_get_contents($file) : '';
    }

    /**
     * @param $file
     * @param $content
     * @param bool $append
     */
    public static function putFileContent($file, $content, $append = false)
    {
        if (isset($file) && isset($content))
            file_put_contents($file, $content, $append ? FILE_APPEND : 0);
    }

    /**
     * @param $file
     */
    public static function emptyFile($file)
    {
        self::putFileContent($file, '');
    }

    /**
     * @param $path
     */
    public static function createDirectory($path)
    {
        if (!file_exists($path))
            mkdir($path);
    }

    /**
     * @param $dir
     */
    public static function deleteDirectory($dir)
    {
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
            RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }

}
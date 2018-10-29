<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\services;

/**
 * Class RegexService
 * @package comad\core\services
 */
class RegexService
{

    /**
     * @param $url
     * @return int
     */
    public static function extractIdFromUrl($url)
    {
        return self::matches('/\(fromUrl\)/', $url, $match);
    }

    /**
     * @param $languages
     * @param $url
     * @return int
     */
    public static function isLanguageInUrl($languages, $url)
    {
        return self::matches('/.*\/?(' . $languages . ')\/?.*/', $url, $match);
    }

    /**
     * @param $languages
     * @param $url
     * @param $match
     * @return int
     */
    public static function extractLanguageFromUrl($languages, $url, &$match)
    {
        return self::matches('/.*\/(' . $languages . ')\/?.*/', $url, $match);
    }

    /**
     * @param $host
     * @param $url
     * @return bool
     */
    public static function hasHostInUrl($host, $url)
    {
        return self::matches('#' . $host . '.*#', $url, $match) || self::matches('#.*:\/\/.*#', $url, $match);
    }

    /**
     * @param $url
     * @return int
     */
    public static function isInterfaceUrl($url)
    {
        return self::matches('#\/?server\/core\/interfaces\/?#', $url, $match);
    }

    /**
     * @param $host
     * @param $url
     * @param $match
     * @return int
     */
    public static function extractRequestPathFromUrl($host, $url, &$match)
    {
        return self::matches('#' . $host . '(\/?.*?)#', $url, $match);
    }

    /**
     * @param $expressionName
     * @param $htmlContent
     * @param $foundPlaceholders
     */
    public static function getAllPlaceholdersForExpression($expressionName, $htmlContent, &$foundPlaceholders)
    {
        self::matchesAll('#\{\{' . $expressionName . ':(.*)(\?=.*)*\}\}#', $htmlContent, $foundPlaceholders);
    }

    /**
     * @param $expressionName
     * @param $match
     * @param $placeholderValue
     * @param $htmlContent
     * @return mixed
     */
    public static function fillInExpressionPlaceholders($expressionName, $match, $placeholderValue, $htmlContent)
    {
        return self::replace('#\{\{' . $expressionName . '\:' . $match . '\}\}#', $placeholderValue, $htmlContent);
    }

    /**
     * @param $key
     * @param $value
     * @param $htmlContent
     * @return mixed
     */
    public static function fillInDataFieldPlaceholders($key, $value, $htmlContent)
    {
        return self::replace('#\{\{field\:' . $key . '\}\}#', $value, $htmlContent);
    }

    /**
     * @param $query
     * @param $whereClauseMatch
     * @return int
     */
    public static function extractWhereClauseFromQuery($query, &$whereClauseMatch)
    {
        return self::matches('#(where .*)#', strtolower($query), $whereClauseMatch);
    }

    /**
     * @param $htmlContent
     * @param $token
     * @return mixed
     */
    public static function addCSRFTokenToForm($htmlContent, $token)
    {
        return self::replace('/<\/form>/', $token . '</form>', $htmlContent);
    }

    /**
     * @param $subject
     * @param $n
     * @param $matches
     * @return int
     */
    public static function getFirstNWords($subject, $n, &$matches)
    {
        return self::matches("/(?:\w+(?:\W+|$)){0,$n}/", $subject, $matches);
    }

    /**
     * @param $previousString
     * @param $subject
     * @param $matches
     * @return int
     */
    public static function getNextWord($previousString, $subject, &$matches)
    {
        return self::matchesAll('/(?<=(' . $previousString . '))(\s\w*)/', $subject, $matches);
    }

    /**
     * @param $string
     * @param bool $ignoreDots
     * @return mixed
     */
    public static function normalizeString($string, $ignoreDots = false)
    {
        $string = strtolower($string);
        $string = self::replace('/\s+/', '', $string);
        $dotsRegex = $ignoreDots ? '\.' : '';
        $string = self::replace('/[^a-zA-Z\-\d' . $dotsRegex . ']/', '', $string);
        return str_replace('#', '', $string);
    }

    /**
     * @param $value
     * @return string
     */
    public static function quote($value)
    {
        return preg_quote($value);
    }

    /**
     * @param $search
     * @param $replace
     * @param $subject
     * @return mixed
     */
    public static function replace($search, $replace, $subject)
    {
        return preg_replace($search, $replace, $subject);
    }

    /**
     * @param $pattern
     * @param $subject
     * @param $match
     * @return int
     */
    public static function matches($pattern, $subject, &$match)
    {
        return preg_match($pattern, $subject, $match);
    }

    /**
     * @param $pattern
     * @param $subject
     * @param $match
     * @return int
     */
    public static function matchesAll($pattern, $subject, &$match)
    {
        return preg_match_all($pattern, $subject, $match);
    }

    /**
     * @param $subject
     * @return mixed
     */
    public static function replaceAllWhitespaces($subject)
    {
        return self::replace('/\s+/', '', $subject);
    }

    /**
     * @param $subject
     * @return mixed
     */
    public static function replaceWhitespacesAndLineBreaks($subject)
    {
        return self::replace('/[ \t]+/', ' ', self::replace('/[\r\n]+/', "\n", $subject));
    }

    /**
     * @param $subject
     * @return mixed
     */
    public static function removeAllScriptTags($subject)
    {
        return self::replace('#<script(.*?)>(.*?)</script>#is', '', $subject);
    }

    /**
     * @param $subject
     * @return mixed
     */
    public static function removeAllStyleTags($subject)
    {
        return self::replace('#<style(.*?)>(.*?)</style>#is', '', $subject);
    }

    /**
     * @param $subject
     * @param $attribute
     * @return mixed
     */
    public static function removeAttributeFromHtml($subject, $attribute)
    {
        return preg_replace('#(<[^>]+) (' . $attribute . ')=".*?"#i', '$1', $subject);
    }

}
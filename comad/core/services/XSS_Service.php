<?php
/**
 * Copyright (c) 2018. Markus Danilow
 */

namespace comad\core\services;

/**
 * Class XSS_Service
 * @package comad\core\services
 */
class XSS_Service
{

    /**
     * @var array
     */
    private static $defaultAllowedElements = array('<a>',
        '<abbr>',
        '<acronym>',
        '<address>',
        '<applet>',
        '<area>',
        '<article>',
        '<aside>',
        '<audio>',
        '<b>',
        '<base>',
        '<basefont>',
        '<bdi>',
        '<bdo>',
        '<big>',
        '<blockquote>',
        '<body>',
        '<br>',
        '<button>',
        '<canvas>',
        '<caption>',
        '<center>',
        '<cite>',
        '<code>',
        '<col>',
        '<colgroup>',
        '<datalist>',
        '<dd>',
        '<del>',
        '<details>',
        '<dfn>',
        '<dialog>',
        '<dir>',
        '<div>',
        '<dl>',
        '<dt>',
        '<em>',
        '<embed>',
        '<fieldset>',
        '<figcaption>',
        '<figure>',
        '<font>',
        '<footer>',
        '<form>',
        '<frame>',
        '<frameset>',
        '<h1>',
        '<h2>',
        '<h3>',
        '<h4>',
        '<h5>',
        '<h6>',
        '<head>',
        '<header>',
        '<hr>',
        '<html>',
        '<i>',
        '<iframe>',
        '<img>',
        '<input>',
        '<ins>',
        '<kbd>',
        '<label>',
        '<legend>',
        '<li>',
        '<link>',
        '<main>',
        '<map>',
        '<mark>',
        '<menu>',
        '<menuitem>',
        '<meta>',
        '<meter>',
        '<nav>',
        '<noscript>',
        '<object>',
        '<ol>',
        '<optgroup>',
        '<option>',
        '<output>',
        '<p>',
        '<param>',
        '<picture>',
        '<pre>',
        '<progress>',
        '<q>',
        '<rp>',
        '<rt>',
        '<ruby>',
        '<s>',
        '<samp>',
        '<section>',
        '<select>',
        '<small>',
        '<source>',
        '<span>',
        '<strike>',
        '<strong>',
        '<sub>',
        '<summary>',
        '<sup>',
        '<table>',
        '<tbody>',
        '<td>',
        '<template>',
        '<textarea>',
        '<tfoot>',
        '<th>',
        '<thead>',
        '<time>',
        '<title>',
        '<tr>',
        '<track>',
        '<tt>',
        '<u>',
        '<ul>',
        '<var>',
        '<video>',
        '<wbr>'
    );

    /**
     * @var array
     */
    private static $maliciousAttributes = array('onclick',
        'oncontextmenu',
        'ondblclick',
        'onmousedown',
        'onmouseenter',
        'onmouseleave',
        'onmousemove',
        'onmouseover',
        'onmouseout',
        'onmouseup',
        'onkeydown',
        'onkeypress',
        'onkeyup',
        'onabort',
        'onbeforeunload',
        'onerror',
        'onhashchange',
        'onload',
        'onpageshow',
        'onpagehide',
        'onresize',
        'onscroll',
        'onunload',
        'onblur',
        'onchange',
        'onfocus',
        'onfocusin',
        'onfocusout',
        'oninput',
        'oninvalid',
        'onreset',
        'onsearch',
        'onselect',
        'onsubmit',
        'ondrag',
        'ondragend',
        'ondragenter',
        'ondragleave',
        'ondragover',
        'ondragstart',
        'ondrop',
        'oncopy',
        'oncut',
        'onpaste',
        'onafterprint',
        'onbeforeprint',
        'oncanplay',
        'oncanplaythrough',
        'ondurationchange',
        'onemptied',
        'onended',
        'onloadeddata',
        'onloadedmetadata',
        'onloadstart',
        'onpause',
        'onplay',
        'onplaying',
        'onprogress',
        'onratechange',
        'onseeked',
        'onseeking',
        'onstalled',
        'onsuspend',
        'ontimeupdate',
        'onvolumechange',
        'onwaiting',
        'animationend',
        'animationiteration',
        'animationstart',
        'transitionend',
        'onmessage',
        'onopen',
        'onmousewheel',
        'ononline',
        'onoffline',
        'onpopstate',
        'onshow',
        'onstorage',
        'ontoggle',
        'onwheel',
        'ontouchcancel',
        'ontouchend',
        'ontouchmove',
        'ontouchstart',
        'CAPTURING_PHASE',
        'AT_TARGET',
        'BUBBLING_PHASE',
        'bubbles',
        'cancelable',
        'currentTarget',
        'defaultPrevented',
        'eventPhase',
        'isTrusted',
        'target',
        'timeStamp',
        'view',
        'altKey',
        'button',
        'buttons',
        'clientX',
        'clientY',
        'ctrlKey',
        'detail',
        'metaKey',
        'pageX',
        'pageY',
        'relatedTarget',
        'screenX',
        'screenY',
        'shiftKey',
        'which',
        'charCode',
        'key',
        'keyCode',
        'location',
        'newURL',
        'oldURL',
        'persisted',
        'animationName',
        'elapsedTime',
        'propertyName',
        'deltaX',
        'deltaY',
        'deltaZ',
        'deltaMode',
        'style'
    );

    /**
     * @var int
     */
    private static $explicitMode = -1;

    /**
     * @param $mode
     */
    public static function useExplicitMode($mode)
    {
        self::$explicitMode = $mode;
    }

    /**
     *
     */
    public static function disableExplicitMode()
    {
        self::$explicitMode = -1;
    }

    /**
     * @return bool
     */
    public static function isExplicitModeEnabled()
    {
        return self::$explicitMode > -1;
    }

    /**
     * @param $string
     * @param int $mode
     * @param bool $useCustomElements
     * @param array $allowedElements
     * @return mixed|string
     */
    public static function protectFromXSS($string, $mode = 0, $useCustomElements = false, $allowedElements = array())
    {
        if (isset($string) && isset($mode)) {
            $string = self::removeMaliciousTags($string);
            $string = self::removeMaliciousAttributes($string);
            if ($mode == 1 || (self::isExplicitModeEnabled() && self::$explicitMode == 1)) {
                if (!isset($allowedElements) || !$useCustomElements) {
                    $allowedElements = self::$defaultAllowedElements;
                }
                $allowedElementsList = implode('', $allowedElements);
                $string = self::smarty_modifier_strip_tags($string, $allowedElementsList);
            } else {
                $string = strip_tags($string);
            }
        }
        return $string;
    }

    /**
     * @param $string
     * @return mixed
     */
    private static function removeMaliciousTags($string)
    {
        if (isset($string)) {
            $string = RegexService::removeAllScriptTags($string);
            $string = RegexService::removeAllStyleTags($string);
        }
        return $string;
    }

    /**
     * @param $string
     * @return mixed
     */
    private static function removeMaliciousAttributes($string)
    {
        if (isset($string)) {
            /*
             * Todo: OR concatenated attributes in a single regex pattern does not work properly yet.
             * Instead just loop through each of the malicious attributes for the moment.
             *
            $maliciousAttributeRegex = implode('|', self::$maliciousAttributes);
            $string = RegexUtil::removeAttributeFromHtml($string, $maliciousAttributeRegex);
            */
            foreach (self::$maliciousAttributes as $maliciousAttribute) {
                $string = RegexService::removeAttributeFromHtml($string, $maliciousAttribute);
            }
        }
        return $string;
    }

    /**
     * Smarty strip_tags modifier plugin
     *
     * Source:
     * https://www.smarty.net/forums/viewtopic.php?t=22878&sid=72a165c455cb2fe3167f2462a54efed5
     *
     * Type:     modifier<br>
     * Name:     strip_tags<br>
     * Purpose:  strip html tags from text
     * @link http://smarty.php.net/manual/en/language.modifier.strip.tags.php
     *          strip_tags (Smarty online manual)
     * @author   Monte Ohrt <monte at ohrt dot com>
     * @author  Jordon Mears <jordoncm at gmail dot com>
     * @author  Tekin Birdüzen <t.birduezen at web-coding dot eu>
     *
     * @version 3.0
     *
     * @param string
     * @param boolean optional
     * @param string optional
     * @return string
     */
    private static function smarty_modifier_strip_tags($string)
    {
        // HTML5 selfclosing tags
        $selfclosingTags = array('area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr');

        /**
         * Find out how many arguments we have and
         * initialize the needed variables
         */
        switch (func_num_args()) {
            case 1:
                $replace_with_space = true;
                break;
            case 2:
                $arg = func_get_arg(1);
                if ($arg === 1 || $arg === true || $arg === '1' || $arg === 'true') {
                    // for full legacy support || $arg === 'false' should be included
                    $replace_with_space = ' ';
                    $allowed_tags = '';
                } elseif ($arg === 0 || $arg === false || $arg === '0' || $arg === 'false') {
                    // for full legacy support || $arg === 'false' should be removed
                    $replace_with_space = '';
                    $allowed_tags = '';
                } else {
                    $replace_with_space = ' ';
                    $allowed_tags = $arg;
                }
                break;
            case 3:
                $replace_with_space = func_get_arg(1) ? ' ' : '';
                $allowed_tags = func_get_arg(2);
                break;
        }
        if (strlen($allowed_tags)) {
            // Allowed tags are set
            $allowed_tags = str_replace(array(' />', '/>', '>'), '', $allowed_tags);

            // This is to delete the allowed selfclosing tags from the list
            $tagArray = explode('<', substr($allowed_tags, 1));
            $selfClosing = array_intersect($tagArray, $selfclosingTags);
            $selfclosingTags = array_diff($selfclosingTags, $tagArray);
            $allowed_tags = implode('|', array_diff($tagArray, $selfClosing));

            unset ($tagArray, $selfClosing);
        }

        // Let's get rid of the selfclosing tags first
        if (count($selfclosingTags)) {
            $string = preg_replace('/<(' . implode('|', $selfclosingTags) . ')\s?[^>]*?\/?>/is', $replace_with_space, $string);
        }

        // And now the other tags
        if (strlen($allowed_tags)) {
            while (preg_match("/<(?!({$allowed_tags}))([a-z][a-z1-5]+)\s?[^>]*?>(.*?)<\/\\2>/is", $string))
                $string = preg_replace("/<(?!({$allowed_tags}))([a-z][a-z1-5]+)\s?[^>]*?>(.*?)<\/\\2>/is", '$3' . $replace_with_space, $string);
        } else {
            // Absolutely no tags allowed
            while (preg_match("/<([a-z][a-z1-5]+)\s?[^>]*?>(.*?)<\/\\1>/is", $string))
                $string = preg_replace("/<([a-z][a-z1-5]+)\s?[^>]*?>(.*?)<\/\\1>/is", '$2' . $replace_with_space, $string);
        }

        return $string;
    }

}
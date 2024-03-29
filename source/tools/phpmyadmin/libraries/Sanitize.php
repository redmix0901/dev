<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * This class includes various sanitization methods that can be called statically
 *
 * @package PhpMyAdmin
 */
namespace PMA\libraries;

use PMA\libraries\Util;

/**
 * This class includes various sanitization methods that can be called statically
 *
 * @package PhpMyAdmin
 */
class Sanitize
{
    /**
     * Checks whether given link is valid
     *
     * @param string  $url   URL to check
     * @param boolean $http  Whether to allow http links
     * @param boolean $other Whether to allow ftp and mailto links
     *
     * @return boolean True if string can be used as link
     */
    public static function checkLink($url, $http=false, $other=false)
    {
        $url = strtolower($url);
        $valid_starts = array(
            'https://',
            './url.php?url=https%3a%2f%2f',
            './doc/html/',
            # possible return values from Util::getScriptNameForOption
            './index.php?',
            './server_databases.php?',
            './server_status.php?',
            './server_variables.php?',
            './server_privileges.php?',
            './db_structure.php?',
            './db_sql.php?',
            './db_search.php?',
            './db_operations.php?',
            './tbl_structure.php?',
            './tbl_sql.php?',
            './tbl_select.php?',
            './tbl_change.php?',
            './sql.php?',
            # Hardcoded options in libraries/special_schema_links.lib.php
            './db_events.php?',
            './db_routines.php?',
            './server_privileges.php?',
            './tbl_structure.php?',
        );
        // Adjust path to setup script location
        if (defined('PMA_SETUP')) {
            foreach ($valid_starts as $key => $value) {
                if (substr($value, 0, 2) === './') {
                    $valid_starts[$key] = '.' . $value;
                }
            }
        }
        if ($other) {
            $valid_starts[] = 'mailto:';
            $valid_starts[] = 'ftp://';
        }
        if ($http) {
            $valid_starts[] = 'http://';
        }
        if (defined('PMA_SETUP')) {
            $valid_starts[] = '?page=form&';
            $valid_starts[] = '?page=servers&';
        }
        foreach ($valid_starts as $val) {
            if (substr($url, 0, strlen($val)) == $val) {
                return true;
            }
        }
        return false;
    }

    /**
     * Callback function for replacing [a@link@target] links in bb code.
     *
     * @param array $found Array of preg matches
     *
     * @return string Replaced string
     */
    public static function replaceBBLink($found)
    {
        /* Check for valid link */
        if (! Sanitize::checkLink($found[1])) {
            return $found[0];
        }
        /* a-z and _ allowed in target */
        if (! empty($found[3]) && preg_match('/[^a-z_]+/i', $found[3])) {
            return $found[0];
        }

        /* Construct target */
        $target = '';
        if (! empty($found[3])) {
            $target = ' target="' . $found[3] . '"';
            if ($found[3] == '_blank') {
                $target .= ' rel="noopener noreferrer"';
            }
        }

        /* Construct url */
        if (substr($found[1], 0, 4) == 'http') {
            $url = PMA_linkURL($found[1]);
        } else {
            $url = $found[1];
        }

        return '<a href="' . $url . '"' . $target . '>';
    }

    /**
     * Callback function for replacing [doc@anchor] links in bb code.
     *
     * @param array $found Array of preg matches
     *
     * @return string Replaced string
     */
    public static function replaceDocLink($found)
    {
        if (count($found) >= 4) {
            $page = $found[1];
            $anchor = $found[3];
        } else {
            $anchor = $found[1];
            if (strncmp('faq', $anchor, 3) == 0) {
                $page = 'faq';
            } else if (strncmp('cfg', $anchor, 3) == 0) {
                $page = 'config';
            } else {
                /* Guess */
                $page = 'setup';
            }
        }
        $link = Util::getDocuLink($page, $anchor);
        return '<a href="' . $link . '" target="documentation">';
    }

    /**
     * Sanitizes $message, taking into account our special codes
     * for formatting.
     *
     * If you want to include result in element attribute, you should escape it.
     *
     * Examples:
     *
     * <p><?php echo Sanitize::sanitize($foo); ?></p>
     *
     * <a title="<?php echo Sanitize::sanitize($foo, true); ?>">bar</a>
     *
     * @param string  $message the message
     * @param boolean $escape  whether to escape html in result
     * @param boolean $safe    whether string is safe (can keep < and > chars)
     *
     * @return string   the sanitized message
     */
    public static function sanitize($message, $escape = false, $safe = false)
    {
        if (!$safe) {
            $message = strtr($message, array('<' => '&lt;', '>' => '&gt;'));
        }

        /* Interpret bb code */
        $replace_pairs = array(
            '[em]'      => '<em>',
            '[/em]'     => '</em>',
            '[strong]'  => '<strong>',
            '[/strong]' => '</strong>',
            '[code]'    => '<code>',
            '[/code]'   => '</code>',
            '[kbd]'     => '<kbd>',
            '[/kbd]'    => '</kbd>',
            '[br]'      => '<br />',
            '[/a]'      => '</a>',
            '[/doc]'      => '</a>',
            '[sup]'     => '<sup>',
            '[/sup]'    => '</sup>',
            // used in common.inc.php:
            '[conferr]' => '<iframe src="show_config_errors.php"><a href="show_config_errors.php">show_config_errors.php</a></iframe>',
            // used in libraries/Util.php
            '[dochelpicon]' => Util::getImage('b_help.png', __('Documentation')),
        );

        $message = strtr($message, $replace_pairs);

        /* Match links in bb code ([a@url@target], where @target is options) */
        $pattern = '/\[a@([^]"@]*)(@([^]"]*))?\]/';

        /* Find and replace all links */
        $message = preg_replace_callback($pattern, function($match){
            return Sanitize::replaceBBLink($match);
        }, $message);

        /* Replace documentation links */
        $message = preg_replace_callback(
            '/\[doc@([a-zA-Z0-9_-]+)(@([a-zA-Z0-9_-]*))?\]/',
            function($match){
                return Sanitize::replaceDocLink($match);
            },
                $message
            );

        /* Possibly escape result */
        if ($escape) {
            $message = htmlspecialchars($message);
        }

        return $message;
    }


    /**
     * Sanitize a filename by removing anything besides legit characters
     *
     * Intended usecase:
     *    When using a filename in a Content-Disposition header
     *    the value should not contain ; or "
     *
     *    When exporting, avoiding generation of an unexpected double-extension file
     *
     * @param string  $filename    The filename
     * @param boolean $replaceDots Whether to also replace dots
     *
     * @return string  the sanitized filename
     *
     */
    public static function sanitizeFilename($filename, $replaceDots = false)
    {
        $pattern = '/[^A-Za-z0-9_';
        // if we don't have to replace dots
        if (! $replaceDots) {
            // then add the dot to the list of legit characters
            $pattern .= '.';
        }
        $pattern .= '-]/';
        $filename = preg_replace($pattern, '_', $filename);
        return $filename;
    }

    /**
     * Format a string so it can be a string inside JavaScript code inside an
     * eventhandler (onclick, onchange, on..., ).
     * This function is used to displays a javascript confirmation box for
     * "DROP/DELETE/ALTER" queries.
     *
     * @param string  $a_string       the string to format
     * @param boolean $add_backquotes whether to add backquotes to the string or not
     *
     * @return string   the formatted string
     *
     * @access  public
     */
    public static function jsFormat($a_string = '', $add_backquotes = true)
    {
        $a_string = htmlspecialchars($a_string);
        $a_string = Sanitize::escapeJsString($a_string);
        // Needed for inline javascript to prevent some browsers
        // treating it as a anchor
        $a_string = str_replace('#', '\\#', $a_string);

        return $add_backquotes
            ? Util::backquote($a_string)
            : $a_string;
    } // end of the 'Sanitize::jsFormat()' function

    /**
     * escapes a string to be inserted as string a JavaScript block
     * enclosed by <![CDATA[ ... ]]>
     * this requires only to escape ' with \' and end of script block
     *
     * We also remove NUL byte as some browsers (namely MSIE) ignore it and
     * inserting it anywhere inside </script would allow to bypass this check.
     *
     * @param string $string the string to be escaped
     *
     * @return string  the escaped string
     */
    public static function escapeJsString($string)
    {
        return preg_replace(
            '@</script@i', '</\' + \'script',
            strtr(
                $string,
                array(
                    "\000" => '',
                    '\\' => '\\\\',
                    '\'' => '\\\'',
                    '"' => '\"',
                    "\n" => '\n',
                    "\r" => '\r'
                )
            )
        );
    }

    /**
     * Formats a value for javascript code.
     *
     * @param string $value String to be formatted.
     *
     * @return string formatted value.
     */
    public static function formatJsVal($value)
    {
        if (is_bool($value)) {
            if ($value) {
                return 'true';
            }

            return 'false';
        }

        if (is_int($value)) {
            return (int)$value;
        }

        return '"' . Sanitize::escapeJsString($value) . '"';
    }

    /**
     * Formats an javascript assignment with proper escaping of a value
     * and support for assigning array of strings.
     *
     * @param string $key    Name of value to set
     * @param mixed  $value  Value to set, can be either string or array of strings
     * @param bool   $escape Whether to escape value or keep it as it is
     *                       (for inclusion of js code)
     *
     * @return string Javascript code.
     */
    public static function getJsValue($key, $value, $escape = true)
    {
        $result = $key . ' = ';
        if (!$escape) {
            $result .= $value;
        } elseif (is_array($value)) {
            $result .= '[';
            foreach ($value as $val) {
                $result .= Sanitize::formatJsVal($val) . ",";
            }
            $result .= "];\n";
        } else {
            $result .= Sanitize::formatJsVal($value) . ";\n";
        }
        return $result;
    }

    /**
     * Prints an javascript assignment with proper escaping of a value
     * and support for assigning array of strings.
     *
     * @param string $key   Name of value to set
     * @param mixed  $value Value to set, can be either string or array of strings
     *
     * @return void
     */
    public static function printJsValue($key, $value)
    {
        echo Sanitize::getJsValue($key, $value);
    }

    /**
     * Formats javascript assignment for form validation api
     * with proper escaping of a value.
     *
     * @param string  $key   Name of value to set
     * @param string  $value Value to set
     * @param boolean $addOn Check if $.validator.format is required or not
     * @param boolean $comma Check if comma is required
     *
     * @return string Javascript code.
     */
    public static function getJsValueForFormValidation($key, $value, $addOn, $comma)
    {
        $result = $key . ': ';
        if ($addOn) {
            $result .= '$.validator.format(';
        }
        $result .= Sanitize::formatJsVal($value);
        if ($addOn) {
            $result .= ')';
        }
        if ($comma) {
            $result .= ', ';
        }
        return $result;
    }

    /**
     * Prints javascript assignment for form validation api
     * with proper escaping of a value.
     *
     * @param string  $key   Name of value to set
     * @param string  $value Value to set
     * @param boolean $addOn Check if $.validator.format is required or not
     * @param boolean $comma Check if comma is required
     *
     * @return void
     */
    public static function printJsValueForFormValidation($key, $value, $addOn=false, $comma=true)
    {
        echo Sanitize::getJsValueForFormValidation($key, $value, $addOn, $comma);
    }

    /**
     * Removes all variables from request except whitelisted ones.
     *
     * @param string &$whitelist list of variables to allow
     *
     * @return void
     * @access public
     */
    public static function removeRequestVars(&$whitelist)
    {
        // do not check only $_REQUEST because it could have been overwritten
        // and use type casting because the variables could have become
        // strings
        if (! isset($_REQUEST)) {
            $_REQUEST = array();
        }
        if (! isset($_GET)) {
            $_GET = array();
        }
        if (! isset($_POST)) {
            $_POST = array();
        }
        if (! isset($_COOKIE)) {
            $_COOKIE = array();
        }
        $keys = array_keys(
            array_merge((array)$_REQUEST, (array)$_GET, (array)$_POST, (array)$_COOKIE)
        );

        foreach ($keys as $key) {
            if (! in_array($key, $whitelist)) {
                unset($_REQUEST[$key], $_GET[$key], $_POST[$key]);
                continue;
            }

            // allowed stuff could be compromised so escape it
            // we require it to be a string
            if (isset($_REQUEST[$key]) && ! is_string($_REQUEST[$key])) {
                unset($_REQUEST[$key]);
            }
            if (isset($_POST[$key]) && ! is_string($_POST[$key])) {
                unset($_POST[$key]);
            }
            if (isset($_COOKIE[$key]) && ! is_string($_COOKIE[$key])) {
                unset($_COOKIE[$key]);
            }
            if (isset($_GET[$key]) && ! is_string($_GET[$key])) {
                unset($_GET[$key]);
            }
        }
    }
}

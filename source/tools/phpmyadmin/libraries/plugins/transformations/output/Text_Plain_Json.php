<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Text Plain JSON Transformations plugin for phpMyAdmin
 *
 * @package    PhpMyAdmin-Transformations
 * @subpackage SQL
 */
namespace PMA\libraries\plugins\transformations\output;

use PMA;
use PMA\libraries\plugins\TransformationsPlugin;
use PMA\libraries\Response;

/**
 * Handles the json transformation for text plain
 *
 * @package    PhpMyAdmin-Transformations
 * @subpackage JSON
 */
class Text_Plain_Json extends TransformationsPlugin
{
    /**
     * No-arg constructor
     */
    public function __construct()
    {
        if (!empty($GLOBALS['cfg']['CodemirrorEnable'])) {
            $response = Response::getInstance();
            $scripts = $response->getHeader()
                ->getScripts();
            $scripts->addFile('codemirror/lib/codemirror.js');
            $scripts->addFile('codemirror/mode/javascript/javascript.js');
            $scripts->addFile('codemirror/addon/runmode/runmode.js');
            $scripts->addFile('transformations/json.js');
        }
    }

    /**
     * Gets the transformation description of the specific plugin
     *
     * @return string
     */
    public static function getInfo()
    {
        return __(
            'Formats text as JSON with syntax highlighting.'
        );
    }

    /**
     * Does the actual work of each specific transformations plugin.
     *
     * @param string $buffer  text to be transformed
     * @param array  $options transformation options
     * @param string $meta    meta information
     *
     * @return string
     */
    public function applyTransformation($buffer, $options = array(), $meta = '')
    {
        return '<code class="json"><pre>' . "\n"
        . htmlspecialchars($buffer) . "\n"
        . '</pre></code>';
    }

    /* ~~~~~~~~~~~~~~~~~~~~ Getters and Setters ~~~~~~~~~~~~~~~~~~~~ */

    /**
     * Gets the plugin`s MIME type
     *
     * @return string
     */
    public static function getMIMEType()
    {
        return "Text";
    }

    /**
     * Gets the plugin`s MIME subtype
     *
     * @return string
     */
    public static function getMIMESubtype()
    {
        return "Plain";
    }

    /**
     * Gets the transformation name of the specific plugin
     *
     * @return string
     */
    public static function getName()
    {
        return "JSON";
    }
}

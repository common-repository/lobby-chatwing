<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * @package Chatwing_Api
 */

define('LOBBY_CHATWING_SDK_PATH', dirname(__FILE__));

/**
 * Autoloader function for PSR-0 coding style
 * @param  string $class 
 * @return boolean        
 */
function lobbyChatwingSDKAutoload($class)
{
    $originalClass = $class;
    if (strpos($class, '\\') === 0) {
        $class = substr($class, 1);
    }

    if (strpos($class, 'LobbyChatwing') === 0) {
        $class = substr($class, 13);
        $path = LOBBY_CHATWING_SDK_PATH . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        if (file_exists($path)) {
            include($path);

            if (!class_exists($originalClass)) {
                return false;
            } else {
                return true;
            }
        }
    }
}

spl_autoload_register('lobbyChatwingSDKAutoload');

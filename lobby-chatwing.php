<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * @package LobbyChatwing\IntegrationPlugins\WordPress
 */

/*
Plugin Name: chatWING Lobby - Group Chat Rooms + 1 on 1 Live Chat
Plugin URI: http://chatwing.com/
Description: Chatwing offers an unlimited live website or blog chat experience. This chat widget specializes in delivering real-time communication at any given time. Engage in a free chat with visitors and friends!
Version: 1.0.9
Author: chatwing
Author URI: http://chatwing.com/
License: GPLv2 or later
Text Domain: lobby-chatwing
*/

define('LOBBY_CHATWING_VERSION', '1.0.9');
define('LOBBY_CHATWING_TEXTDOMAIN', 'lobby-chatwing');
define('LOBBY_CHATWING_PATH', dirname(__FILE__));
define('LOBBY_CHATWING_CLASS_PATH', LOBBY_CHATWING_PATH . '/classes');
define('LOBBY_CHATWING_TPL_PATH', LOBBY_CHATWING_PATH . '/templates');
define('LOBBY_CHATWING_PLG_MAIN_FILE', __FILE__);
define('LOBBY_CHATWING_PLG_URL', plugin_dir_url(__FILE__));

define('LOBBY_CHATWING_DEBUG', true);
define('LOBBY_CHATWING_USE_STAGING', false);

define('LOBBY_CHATWING_CLIENT_ID', 'wordpress');

require_once LOBBY_CHATWING_PATH . '/chatwing-sdk/src/LobbyChatwing/autoloader.php';
require_once LOBBY_CHATWING_PATH . '/chatwing-sdk/src/LobbyChatwing/start.php';  
use LobbyChatwing\IntegrationPlugins\WordPress\Asset;

$keyPath = LOBBY_CHATWING_PATH . '/key.php';
if (file_exists($keyPath)) {
    require $keyPath;
}

/**
** function: add script and css file
**/
function load_custom_wp_admin_style() {
        wp_register_style( 'forms_min_wp_admin_css', Asset::link('forms-min.css'), false, '1.0.0' );
        wp_enqueue_style( 'forms_min_wp_admin_css' );

        wp_register_style( 'buttons_min_wp_admin_css', Asset::link('buttons-min.css'), false, '1.0.0' );
        wp_enqueue_style( 'buttons_min_wp_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );

/**
 * Plugin class autoloader
 * @param  $className
 * @return bool
 * @throws Exception
 */
function lobbychatwingAutoloader($className)
{
    $prefix = 'LobbyChatwing\\IntegrationPlugins\\WordPress\\';

    if ($pos = strpos($className, $prefix) !== 0) {
        return false;
    }

    $filePath = LOBBY_CHATWING_CLASS_PATH . '/' . str_replace('\\', '/', substr($className, strlen($prefix))) . '.php';

    if (file_exists($filePath)) {
        require_once($filePath);

        if (!class_exists($className)) {
            throw new Exception(__("Class {$className} doesn't exist ", LOBBY_CHATWING_TEXTDOMAIN));
        }

        return true;
    } else {
        throw new Exception(__("Cannot find file at {$filePath} ", LOBBY_CHATWING_TEXTDOMAIN));
    }
}

spl_autoload_register('lobbychatwingAutoloader');

use LobbyChatwing\LobbyApplication as Chatwing;
use LobbyChatwing\IntegrationPlugins\WordPress\Application;
use LobbyChatwing\IntegrationPlugins\WordPress\DataModel;

Chatwing::getInstance()->bind('client_id', LOBBY_CHATWING_CLIENT_ID);
$app = new Application(DataModel::getInstance());
$app->run();
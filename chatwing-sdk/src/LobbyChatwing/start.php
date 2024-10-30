<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * @author chatwing
 * @package Chatwing_SDK
 */

if (!defined('LOBBY_CHATWING_DEBUG')) {
    define('LOBBY_CHATWING_DEBUG', false);
}

define('LOBBY_CHATWING_SDK_VESION', '1.0');
define('LOBBY_CHATWING_ENV_DEVELOPMENT', 'development');
define('LOBBY_CHATWING_ENV_PRODUCTION', 'production');

use LobbyChatwing\LobbyApplication as App;

$app = App::getInstance();
$app->bind(
    'api',
    function (\LobbyChatwing\Container $container) {
        $app = new LobbyChatwing\Api($container->get('client_id'));
        
        $app->setEnv(
            defined('LOBBY_CHATWING_USE_STAGING') && LOBBY_CHATWING_USE_STAGING ? LOBBY_CHATWING_ENV_DEVELOPMENT : LOBBY_CHATWING_ENV_PRODUCTION
        );

        if ($container->has('access_token')) {
            $app->setAccessToken($container->get('access_token'));
        }

        return $app;
    }
);

$app->factory(
    'lobby',
    function (\LobbyChatwing\Container $container) {
        return new \LobbyChatwing\Lobby($container->get('api'));
    }
);
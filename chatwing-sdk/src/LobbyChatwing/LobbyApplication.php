<?php
/**
 * @author chatwing <dev@chatwing.com>
 * @package LobbyChatwing\SDK
 */
namespace LobbyChatwing;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class LobbyApplication extends Container
{
    /**
     * @var Container
     */
    protected static $container = null;

    public static function getInstance()
    {
        if (is_null(static::$container)) {
            static::$container = new static();
        }

        return static::$container;
    }
}

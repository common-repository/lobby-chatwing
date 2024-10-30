<?php
namespace LobbyChatwing\IntegrationPlugins\WordPress;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Asset
{
    /**
     * @param $file
     * @return string
     */
    public static function link($file)
    {
        return LOBBY_CHATWING_PLG_URL . 'assets/' . $file;
    }
}
<?php 
namespace LobbyChatwing\IntegrationPlugins\WordPress;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @package LobbyChatwing\IntegrationPlugins\Wordpress
 * @author chatwing
 */
use LobbyChatwing\LobbyApplication as App;

class ShortCode {
     /**
     * @param array $params
     * @return string
     */
    public static function render($params = array())
    {
        $model = DataModel::getInstance();
        $width_lobby = $model->getOption('width_lobby') ? $model->getOption('width_lobby') : "600";
        $height_lobby = $model->getOption('height_lobby') ? $model->getOption('height_lobby') : "800";

        $defaultAttributes = array(
            'width_lobby' => $width_lobby,
            'height_lobby' => $height_lobby
        );

        $params = array_merge($defaultAttributes, $params);
        if (empty($params['id'])) {
            return '';
        }
        /**
         * @var \Chatwing\Lobby $box
         */
        $box = App::getInstance()->get('lobby');
        $box->setId($params['id']);

        $height_lobby = $params['height_lobby'] ? $params['height_lobby'] : "400";
        $box->setData('width_lobby', $params['width_lobby']);
        $box->setData('height_lobby', $height_lobby);

        return $box->getIframe();
    }

	/**
	* get information shortcode (id, height, width, ...) of lobby
	* @param: id of lobby
	* @return shortcode string
	**/
	 public static function generateShortCode($params = array())
    {
        if (empty($params) || (empty($params['id']))) {
            return '';
        }

        $model = DataModel::getInstance();

        $defaultAttributes = array(
            'id' => '',
            'width' => $model->getOption('width_lobby'),
            'height' => $model->getOption('height_lobby')
        );
        $params = shortcode_atts($defaultAttributes, $params);

        if (!empty($params['key'])) {
            unset($params['alias']);
        } else {
            unset($params['key']);
        }

        $shortCode = '';
        foreach ($params as $key => $value) {
            $shortCode .= "{$key}=\"{$value}\" ";
        }
        $shortCode = "[lobby-chatwing {$shortCode} ][/lobby-chatwing]";
        return $shortCode;
    }
}
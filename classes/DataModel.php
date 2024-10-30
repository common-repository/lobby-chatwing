<?php 
namespace LobbyChatwing\IntegrationPlugins\WordPress;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use LobbyChatwing\Object;
use LobbyChatwing\Encryption\DataEncryptionHelper;
use LobbyChatwing\LobbyApplication as ChatwingContainer;

class DataModel extends Object
{

    protected static $isntance = null;

    function __construct()
    {

    }

    /**
     * @return DataModel|null
     */
    public static function getInstance()
    {
        if (is_null(self::$isntance)) {
            self::$isntance = new self;
        }

        return self::$isntance;
    }

    public function saveAccessToken($token) {
        if ($token) {
            $token = DataEncryptionHelper::encrypt($token);
        }

        $this->token = $token;

        update_option('lobby_chatwing_access_token', $token);
    }

     public function getAccessToken()
    {
        if (is_null($this->token)) {
            try {
                $this->token = DataEncryptionHelper::decrypt(get_option('lobby_chatwing_access_token'));
            } catch (\Exception $e) {
                die($e->getMessage());
            }
        }
        return $this->token;
    }
    
     public function hasAccessToken()
    {
        return (bool) $this->getAccessToken();
    }

    public function getOption($key, $default = null)
    {
        return get_option( 'chatwing_default_' . $key, $default );
    }

    public function getBoxList()
    {
        $boxes = array(); 

        try {
            $api = ChatwingContainer::getInstance()->get('api');
            $response = $api->call('app/float_ui/list',array('app_id' => get_option('chatwing_default_app_id_lobby')));
            if ($response->isSuccess()) {
                $boxes = $response->get('data');
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }

        return $boxes;
    }
}
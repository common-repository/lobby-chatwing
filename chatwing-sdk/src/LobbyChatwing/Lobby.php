<?php
/**
 * @author  chatwing
 * @package LobbyChatwing\SDK
 */

namespace LobbyChatwing;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use LobbyChatwing\Exception\ChatwingException;

class Lobby extends Object
{
    /**
     * @var Api
     */
    protected $api;
    protected $id = null;
    protected $key = null;
    protected $alias = null;
    protected $params = array();
    protected $secret = null;

    protected $baseUrl = null;
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function getBaseUrl() {
        $api = new Api('');
        $domain = $api->getDomain();
        $this->baseUrl = $domain[$api->getEnv()]. "/service/embed/". $this->getId();

        return $this->baseUrl;
    }

    /**
     * Return lobby's url
     *
     * @throws ChatwingException If no alias or lobby key is set
     * @return string
     */
    public function getLobbyUrl() {
        if (!$this->getId()) {
          throw new ChatwingException(__("Lobby ID is not set!", LOBBY_CHATWING_TEXTDOMAIN));
        }

        $lobbyUrl = $this->getBaseUrl();

        if (!empty($this->params)) {
            if ($this->getSecret()) {
              $this->getEncryptedSession(); // call this method to create encrypted session
            }
            $lobbyUrl .= '?' . http_build_query($this->params);
        }

        return $lobbyUrl;
    }

    /**
     * Return lobby iframe code
     * @throws ChatwingException If no alias or lobby key is set
     * @return string
     */
    public function getIframe() {
        $url = $this->getLobbyUrl();

        return '<iframe src="'. $url .'" height="'. $this->getData('height_lobby') .'" width="'. $this->getData('width_lobby') .'" frameborder="0"></iframe>';
    }

    /**
     * Set lobby ID
     * @param string $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set lobby key
     *
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * get the current lobby's key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set lobby alias
     *
     * @param string $alias
     *
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * Get current lobby's alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set lobby's parameter
     *
     * @param string|array $key 
     * @param string $value
     *
     * @return $this
     */
    public function setParam($key, $value = '')
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->setParam($k, $v);
            }
        } else {
            $this->params[$key] = $value;
        }
        return $this;
    }

    /**
     * Get parameter
     * @param  string $key     
     * @param  null|mixed $default 
     * @return mixed|null
     */
    public function getParam($key = '', $default = null)
    {
        if (empty($key)) {
            return $this->params;
        }
        return isset($this->params[$key]) ? $this->params[$key] : $default;
    }

    /**
     * Get all parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set lobby secret key
     * @param $s
     *
     * @return $this
     */
    public function setSecret($s)
    {
        $this->secret = $s;
        return $this;
    }

    /**
     * Get secret
     * @return string|null
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Get encrypted session
     * @return string
     */
    public function getEncryptedSession()
    {
        if (isset($this->params['custom_session'])) {
            $customSession = $this->params['custom_session'];
            if (is_string($customSession)) {
                return $customSession;
            }

            if (is_array($customSession) && !empty($customSession) && $this->getSecret()) {
                $session = new CustomSession();
                $session->setSecret($this->getSecret());
                $session->setData($customSession);
                $this->setParam('custom_session', $session->toEncryptedSession());

                return $this->getParam('custom_session');
            }

            unset($this->params['custom_session']);
        }

        return false;
    }
} 
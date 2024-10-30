<?php 
namespace LobbyChatwing\IntegrationPlugins\WordPress;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @package LobbyChatwing\IntegrationPlugins\Wordpress
 * @author chatwing
 */
use LobbyChatwing\Encryption\DataEncryptionHelper;
use LobbyChatwing\LobbyApplication as Chatwing;

class Application extends PluginBase
{
	protected function init()
	{
		// if not exists define for LOBBY_CHATWING_ENCRYPTION_KEY, we will define LOBBY_CHATWING_ENCRYPTION_KEY
		// get key in file and set this value for LOBBY_CHATWING_ENCRYPTION_KEY
		if (!defined('LOBBY_CHATWING_ENCRYPTION_KEY')) {
			$this->onPluginActivation();
			$this->getModel()->saveAccessToken('');
			return;
		}

		// if existsed define LOBBY_CHATWING_ENCRYPTION_KEY
		DataEncryptionHelper::setEncryptionKey(LOBBY_CHATWING_ENCRYPTION_KEY);
		Chatwing::getInstance()->bind('access_token', $this->getModel()->getAccessToken());
		add_shortcode('lobby-chatwing', array('LobbyChatwing\\IntegrationPlugins\\WordPress\\ShortCode', 'render'));

	}

	protected function registerHooks()
	{
		register_activation_hook(LOBBY_CHATWING_PLG_MAIN_FILE, array($this, 'onPluginActivation'));
		if ($this->getModel()->hasAccessToken()) {
			add_action('widgets_init', function(){
				register_widget('LobbyChatwing\\IntegrationPlugins\\WordPress\\Widget');
			});
		}

	}

	protected function registerFilters()
	{
         add_filter('loggedin_redirect', array($this, 'handleUserLogin'), 10, 3);
	}

	public function run()
	{
		parent::run();

		if (is_admin()) {
			$admin = new Admin($this->getModel());
			$admin->run();
		}
	}

	public function onPluginActivation()
	{
		// check if we have encryption key
		$filePath = LOBBY_CHATWING_PATH . '/key.php';
		if (!file_exists($filePath)) {
			$encryptionKey = DataEncryptionHelper::generateKey();
			$n = file_put_contents($filePath, "<?php define('LOBBY_CHATWING_ENCRYPTION_KEY', '{$encryptionKey}');?>");
			if ($n) {
				require $filePath;
			} else {
				die("Cannot create encryption key.");
			}
		}
	}

	 /**
     * @param $redirectUrl
     * @param string $requestedRedirectUrl
     * @param \WP_Error|\WP_User $user
     * @return string
     */
    public function handleUserLogin($redirectUrl, $requestedRedirectUrl = '', $user = null)
    {
        $targetURL = $redirectUrl;

        if ($user instanceof \WP_User && $user->ID) {
            // login successfully
            if (!empty($requestedRedirectUrl)) {
                $targetURL = $requestedRedirectUrl;
            }

            $targetURL = urldecode($targetURL);
            $parsedData = parse_url($targetURL);
            if (!empty($parsedData['host'])
                && in_array($parsedData['host'], array('chatwing.com', 'staging.chatwing.com'))
            ) {

                // try to get the lobby alias
                // then determine if we have custom redirection URL
                $parts = isset($parsedData['path']) ? array_filter(explode('/', $parsedData['path'])) : array();
                if (count($parts) > 1) {
                    $lobbyKey = $parts[2];
                    $boxId = null;
                    $boxList = $this->getModel()->getBoxList();
                    foreach ($boxList as $box) {
                        if ($box['key'] == $lobbyKey) {
                            $boxId = $box['id'];
                            break;
                        }
                    }

                    if ($boxId) {
                        $response = Chatwing::getInstance()->get('api')->call('chatbox/read', array('id' => $boxId));
                        if ($response->isSuccess()){
                            $lobbyData = $response->get('data');
                            $secret = $lobbyData['custom_login']['secret'];
                            $customSession = Helper::prepareUserInformationForCustomLogin($user);

                            $box = Chatwing::getInstance()->get('lobby');
                            $box->setId($boxId);
                            $box->setParam('custom_session', $customSession);
                            $box->setSecret($secret);

                            $targetURL = $box->getLobbyUrl();

                            ?>
                            <script>
                                window.opener.location = '<?php echo $targetURL;?>';
                                self.close();
                            </script>
                            <?php
                            die;
                        }
                    }

                }
            }
        } else {
            switch (true) {
                case !empty($_GET['redirect_url']):
                    $targetURL = $_GET['redirect_url'];
                    break;

                case !empty($requestedRedirectUrl):
                    $targetURL = $requestedRedirectUrl;
                    break;

                default:
                    break;
            }

        }

        return urldecode($targetURL);
    }

    protected function redirectUser($url, WP_User $user)
    {

    }

}
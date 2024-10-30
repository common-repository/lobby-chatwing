<?php 
namespace LobbyChatwing\IntegrationPlugins\WordPress;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @package LobbyChatwing\IntegrationPlugins\Wordpress
 * @author chatwing
 */

use InvalidArgumentException;
use LobbyChatwing\Application as App;

class Admin extends PluginBase
{
    protected function init()
    {
        parent::init();
    }

    protected function registerHooks()
    {
        add_action('admin_menu', array($this, 'registerAdminMenu'));
        add_action('admin_action_lobby_chatwing_save_token', array($this, 'handleTokenSaving'));
        add_action('admin_action_lobby_chatwing_save_settings', array($this, 'handleSettingsSave'));
    }

    protected function registerFilters()
    {

    }

    public function registerAdminMenu()
    {
        add_menu_page(__('Lobby Chatwing plugin settings', LOBBY_CHATWING_TEXTDOMAIN), 'Lobby Chatwing', 'manage_options', 'lobby-chatwing', array($this, 'showSettingsPage'));
    }

     /**
     * Hanle token update/remove
     */
    public function handleTokenSaving($skipNonce = false)
    {
        if (!$skipNonce) {
            $nonce = !empty($_POST['nonce']) ? $_POST['nonce'] : '';
            if (!wp_verify_nonce($nonce, 'token_save')) {
                die('Oops .... Authentication failed!');
            }
        }

        if (!empty($_POST['token_lobby'])) {
            $token = $_POST['token_lobby'];
            $this->getModel()->saveAccessToken($token);
        }

        wp_redirect('admin.php?page=lobby-chatwing');
        die;
    }

    public function handleSettingsSave()
    {
        $nonce = !empty($_POST['nonce']) ? $_POST['nonce'] : '';

        if (!wp_verify_nonce($nonce, 'settings_save')) {
            die('Oops .... Authentication failed!');
        }

        $fieldsToUpdate = array('width_lobby', 'height_lobby');

        foreach($fieldsToUpdate as $field) {
            if (!empty($_POST[$field]) && is_numeric($_POST[$field])) {
                update_option('chatwing_default_' . $field, $_POST[$field]);
            }
        }

        // app id lobby is string type
        $app_id_lobby = sanitize_text_field($_POST["app_id_lobby"]);
        if (!empty($app_id_lobby)) {
             update_option('chatwing_default_app_id_lobby', $app_id_lobby);
        }

        if (!empty($_POST['token_lobby']) || !empty($_POST['remove_token'])) {
            $this->handleTokenSaving(true);
        } else {
            wp_redirect('admin.php?page=lobby-chatwing');
            die;
        }
    }

    /**
     * Show chatwing settings page
     */
    public function showSettingsPage()
    {
        try {
            if ($this->getModel()->hasAccessToken()) {
                $boxes = $this->getModel()->getBoxList();
                $this->loadTemplate('settings', array('boxes' => $boxes));
            }
        } catch(\Exception $e) {

        }
    }

    /**
     * Load admin template
     * @param  string $templateName
     * @param  array $data
     * @throws InvalidArgumentException
     */
    public function loadTemplate($templateName, $data = array())
    {
        if (strpos($templateName, '.php') === false) {
            $templateName .= '.php';
        }

        $file = LOBBY_CHATWING_TPL_PATH . '/' . $templateName;

        if (file_exists($file)) {

            ob_start();
            if (!empty($data)) {

                extract($data);
            }
            require $file;

            $content = ob_get_clean();

            echo $content;
        } else {
            throw new InvalidArgumentException("Template {$templateName} doesn't exist");
        }
    }

}

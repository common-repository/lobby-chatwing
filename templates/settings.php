<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use LobbyChatwing\IntegrationPlugins\WordPress\DataModel;
use LobbyChatwing\IntegrationPlugins\WordPress\ShortCode;
?>

<h2><?php _e('Lobby Chatboxes', LOBBY_CHATWING_TEXTDOMAIN); ?></h2>
<div class="wrap">
    <table class="widefat">
        <thead>
        <tr>
            <th><?php _e('Alias', LOBBY_CHATWING_TEXTDOMAIN); ?></th>
            <th><?php _e('ID', LOBBY_CHATWING_TEXTDOMAIN); ?></th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php $count = 1; foreach ($boxes as $box) {  
            if ($box['floating_type'] == 'lobby') { ?>
          <tr>
              <td> <?php echo $box['alias']; ?> </td>
              <td> <?php echo $box['id']; ?> </td>
              <td>
                <input type="button" class="pure-button pure-button-primary" value="<?php _e('Get shortcode', LOBBY_CHATWING_TEXTDOMAIN) ?>" 
                onclick="prompt('Shortcode for lobby <?php echo $box['alias'] ?>', '<?php echo esc_attr(ShortCode::generateShortCode(array('id' => $box['id']))) ?>')" />
              </td>
          </tr>
        <?php
            }
        }
        ?>
        </tbody>
    </table>

    <h2><?php _e("Settings", LOBBY_CHATWING_TEXTDOMAIN) ?></h2>

    <div id="poststuff" style="max-width: 800px;">
        <form class="pure-form pure-form-aligned pure-g" method="post"
              action="<?php echo admin_url('admin.php') ?>">
            <fieldset>
                <div class="pure-control-group">
                    <label
                        for="token"><?php _e('Access token', LOBBY_CHATWING_TEXTDOMAIN) ?></label>
                    <input id="token_lobby" type="text" name="token_lobby">
                </div>
                <div class="pure-control-group">
                    <label
                        for="appID"><?php _e('App ID', LOBBY_CHATWING_TEXTDOMAIN) ?></label>
                    <input id="app_id_lobby" type="text" name="app_id_lobby" 
                            value="<?php echo get_option('chatwing_default_app_id_lobby'); ?>">
                </div>
                <div class="pure-control-group">
                    <label
                        for="width"><?php _e('Default lobby width', LOBBY_CHATWING_TEXTDOMAIN) ?></label>
                    <input type="text" name="width_lobby" id="width_lobby" value="<?php echo get_option('chatwing_default_width_lobby'); ?>">
                    <?php _e('pixel') ?>
                </div>

                <div class="pure-control-group">
                    <label
                        for="height"><?php _e('Default lobby height', LOBBY_CHATWING_TEXTDOMAIN) ?></label>
                    <input type="text" name="height_lobby" id="height_lobby" value="<?php echo get_option('chatwing_default_height_lobby') ?>">
                    <?php _e('pixel') ?>
                </div>

                <div class="pure-controls">
                    <input type="submit" class="pure-button pure-button-primary"
                           value="<?php _e('Save', LOBBY_CHATWING_TEXTDOMAIN) ?>">
                </div>
            </fieldset>
            <div style="display: none">
                <input type="hidden" name="action" value="lobby_chatwing_save_settings">
                <?php wp_nonce_field('settings_save', 'nonce' ); ?>
            </div>
        </form>
    </div>
</div>
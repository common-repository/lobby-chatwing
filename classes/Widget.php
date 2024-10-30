<?php 
namespace LobbyChatwing\IntegrationPlugins\WordPress;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Widget
 * @package LobbyChatwing\IntegrationPlugins\WordPress
 * @author chatwing
 */

class Widget extends \WP_Widget
{
    function __construct()
    {
        parent::__construct('lobby_chatwing_cb', __('Lobby Chatwing chatbox', LOBBY_CHATWING_TEXTDOMAIN));
    }

    public function widget($args, $instance)
    {
        $defaultAttributes = array(
            'title_lobby' => ''
        );

        $instance = array_merge($defaultAttributes, $instance);
        echo $args['before_widget'];
        echo $args['before_title'] . $instance['title'] . $args['after_title'];
        echo ShortCode::render(array(
            'id' => $instance['lobby'],
            'width_lobby' => !empty($instance['width_lobby']) ? $instance['width_lobby'] : '',
            'height_lobby' => !empty($instance['height_lobby']) ? $instance['height_lobby'] : ''
        ));
        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $boxes = DataModel::getInstance()->getBoxList();
        $currentID = !empty($instance['lobby']) ? $instance['lobby'] : null;
        ?>
        <p>
            <label
                for="<?php echo $this->get_field_id('title_lobby'); ?>"><?php _e("Title", LOBBY_CHATWING_TEXTDOMAIN); ?></label>
            <input type="text" class="widefat"
                   id="<?php echo $this->get_field_id('title_lobby'); ?>"
                   name="<?php echo $this->get_field_name('title_lobby'); ?>"
                   value="<?php echo !empty($instance['title_lobby']) ? $instance['title_lobby'] : '' ?>"/>
        </p>
        <p>
            <label
                for="<?php echo $this->get_field_id('lobby'); ?>"><?php _e('Lobby', LOBBY_CHATWING_TEXTDOMAIN); ?></label>
            <select name="<?php echo $this->get_field_name('lobby'); ?>"
                    id="<?php echo $this->get_field_id('lobby'); ?>">
                <?php if (!empty($boxes)): foreach ($boxes as $box):  if($box['floating_type'] == 'lobby') {?>
                    <option
                        value="<?php echo $box['id'] ?>" <?php if ($box['id'] == $currentID) echo 'selected="selected"'; ?>><?php echo $box['alias']; ?></option>
                <?php } endforeach;endif; ?>
            </select>
        </p>
        <p>
            <label
                for="<?php echo $this->get_field_id('width_lobby'); ?>"><?php _e('Width', LOBBY_CHATWING_TEXTDOMAIN); ?></label>
            <input type="text"
                   name="<?php echo $this->get_field_name('width_lobby'); ?>"
                   id="<?php echo $this->get_field_id('width_lobby') ?>"
                   class="widefat"
                   value="<?php echo !empty($instance['width_lobby']) ? $instance['width_lobby'] : '' ?>"/>
        </p>
        <p>
            <label
                for="<?php echo $this->get_field_id('height_lobby'); ?>"><?php _e('Height', LOBBY_CHATWING_TEXTDOMAIN); ?></label>
            <input type="text"
                   name="<?php echo $this->get_field_name('height_lobby'); ?>"
                   id="<?php echo $this->get_field_id('height_lobby'); ?>"
                   class="widefat"
                   value="<?php echo !empty($instance['height_lobby']) ? $instance['height_lobby'] : '' ?>"/>
        </p>
    <?php
    }

    public function update($new, $old)
    {
        return $new;
    }
}
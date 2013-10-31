<?php
/**
 * Widget template. This template can be overriden by copying this file to your theme folder: 'social-icons/widget-admin.php'
 */

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

    $enabled_icons = json_decode($instance['enabled_icons'],true);
    $icons = json_decode($instance['icons'],true);

?>

<div id="<?php echo $this->get_field_id('fields'); ?>" class="social_icon_holder">
	<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'social_icons'); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr(strip_tags($instance['title'])); ?>" /></p>

	<p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description', 'social_icons'); ?>:</label>
	<textarea rows="8" class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo format_to_edit($instance['description']); ?></textarea></p>

    <p>
        <label for="<?php echo $this->get_field_id('icon_size'); ?>"><?php _e('Icon Size', 'social_icons'); ?>:</label>
        <select class="widefat" name="<?php echo $this->get_field_name('icon_size'); ?>" id="<?php echo $this->get_field_id('icon_size'); ?>">
            <option value="small"<?php echo $instance['icon_size'] == 'small' ? ' selected' : '';?>><?php _e('Small','social_icons');?></option>
            <option value="medium"<?php echo $instance['icon_size'] == 'medium' ? ' selected' : '';?>><?php _e('Medium','social_icons');?></option>
            <option value="large"<?php echo $instance['icon_size'] == 'large' ? ' selected' : '';?>><?php _e('Large','social_icons');?></option>
        </select>
    </p>
    <div class="no-svg enabled_icons"<?php echo !is_array($enabled_icons) || !count($enabled_icons) ? ' style="display:none;"' : '';?>>
        <input type="hidden" name="key" value="<?php echo $this->get_field_name('enabled_icons'); ?>" class="social_icon_prefix">
        <label for="<?php echo $this->get_field_id('enabled_icons'); ?>"><?php _e('Enabled Icons &amp; Links (click to disable)', 'social_icons'); ?>:</label>
        <br/>
        <div class="enabled_icons_holder">
            <?php
            if(is_array($enabled_icons)){
                foreach($enabled_icons as $icon_name => $url){ ?>
                    <div>
                    <a href="#" class="webicon <?php echo $icon_name;?> small" onclick="return false;"><?php echo $icon_name;?></a>
                    <input type="text" name="<?php echo $this->get_field_name('enabled_icons'); ?>[<?php echo $icon_name;?>]" value="<?php echo esc_attr(strip_tags($url));?>">
                    </div>
                <?php }
            } ?>
        </div>
    </div>
    <p class="no-svg disabled_icons">
        <label for="<?php echo $this->get_field_id('icons'); ?>"><?php _e('Available Icons (click to enable)', 'social_icons'); ?>:</label>
        <br/>
        <?php
        $icons = json_decode($instance['icons'],true);
        if(is_array($icons)){
            foreach($icons as $icon_name => $url){ ?>
                <a href="#" class="webicon <?php echo $icon_name;?> small" data-icon-name="<?php echo $icon_name;?>" title="<?php echo $icon_name;?>" onclick="return false;"><?php echo $icon_name;?></a>
            <?php }
        } ?>
    </p>

</div>
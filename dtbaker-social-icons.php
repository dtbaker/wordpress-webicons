<?php
/*
Plugin Name: Social Icons by Fairhead Creative
Plugin URI: https://github.com/dtbaker/wordpress-webicons/
Description: Display a series of Social Icons (Facebook, YouTube, etc..) in a widget on your website.
Author: Fairhead Creative (icons) and dtbaker (plugin)
Version: 1.0.4
Author URI: http://dtbaker.net
Icons are CC-Attrib to http://fairheadcreative.com
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


/**
 * Tribe_Image_Widget class
 **/
class dtbaker_Social_Icons extends WP_Widget {

	/**
	 * Social Icons constructor
	 */
	function __construct() {
		load_plugin_textdomain( 'social_icons', false, trailingslashit(basename(dirname(__FILE__))) . 'lang/');
		$widget_ops = array( 'classname' => 'widget_social_icons', 'description' => __( 'Display a series of Social Icons (Facebook, YouTube, etc..)', 'social_icons' ) );
		$control_ops = array( 'id_base' => 'widget_social_icons' );
		parent::__construct('widget_social_icons', __('Social Icons', 'social_icons'), $widget_ops, $control_ops);

        add_action( 'sidebar_admin_setup', array( $this, 'admin_setup' ) );
        add_action( 'wp_enqueue_scripts', array($this, 'frontend_setup' ) );
	}


	/**
	 * Enqueue all the styles for displaying icons in admin area.
	 */
	function admin_setup() {
		//wp_enqueue_media();
		wp_enqueue_style( 'dtbaker-social-icons', plugins_url('webicons.css', __FILE__) );
		wp_enqueue_script( 'dtbaker-social-icons-admin', plugins_url('webicons-admin.js', __FILE__) );
	}

    function frontend_setup(){
        //if ( is_active_widget(false, false, $this->id_base, true) ) {
            wp_enqueue_style( 'dtbaker-social-icons', plugins_url('webicons.css', __FILE__) );
            wp_enqueue_script( 'dtbaker-social-icons-svg-check', plugins_url('webicons.js', __FILE__) );
        //}
    }

	/**
	 * Widget frontend output
	 *
	 * @param array $args
	 * @param array $instance
	 */
	function widget( $args, $instance ) {
		extract( $args );
		$instance = wp_parse_args( (array) $instance, self::get_defaults() );

        $instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'] );
        $instance['description'] = apply_filters( 'widget_text', $instance['description'], $args, $instance );
        $instance['icons'] = empty( $instance['icons'] ) ? array() : json_decode($instance['icons'], true);

        extract( $instance );

        include( $this->getTemplateHierarchy( 'widget' ) );
	}

	/**
	 * Update widget options
	 *
	 * @param object $new_instance Widget Instance
	 * @param object $old_instance Widget Instance
	 * @return object
	 * @author Modern Tribe, Inc.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, self::get_defaults() );
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['icon_size'] = strip_tags($new_instance['icon_size']);
		$instance['align'] = strip_tags($new_instance['align']);
		if ( current_user_can('unfiltered_html') ) {
			$instance['description'] = $new_instance['description'];
		} else {
			$instance['description'] = wp_filter_post_kses($new_instance['description']);
		}

        $instance['enabled_icons'] = array();
        if(is_array($new_instance['enabled_icons'])){
            $instance['enabled_icons'] = $new_instance['enabled_icons'];
        }
        $instance['enabled_icons'] = json_encode($instance['enabled_icons']);

		return $instance;
	}


	/**
	 * Render an array of default values.
	 *
	 * @return array default values
	 */
	public static function get_defaults() {

		$defaults = array(
			'title' => 'Contact Us',
			'description' => "Phone:||1800 123 123\nFax:||1800 321 321\nAddress:||Sydney, Australia",
			'icon_size' => 'large',
			'align' => '',
			'icons' => array(),
			'enabled_icons' => array(),
		);

        $svg_files = glob(trailingslashit(dirname(__FILE__)).'webicons/*.svg');
        if(is_array($svg_files)){
            foreach($svg_files as $icon){
                // format [icon name] => [urm]
                $defaults['icons'][str_replace('webicon-','',str_replace('.svg','',basename($icon)))] = '';
                $defaults['description'] .= '[webicon icon="'.str_replace('webicon-','',str_replace('.svg','',basename($icon))).'"] ';
            }
            // sort some popular icons above others.
            foreach(array('tumblr','flickr','foursquare','linkedin','mail','pinterest','youtube','twitter','facebook') as $priority){
                if(isset($defaults['icons'][$priority])){
                    unset($defaults['icons'][$priority]);
                    $defaults['icons'] = array($priority => '') + $defaults['icons'];
                }
            }
        }

        $defaults['icons'] = json_encode($defaults['icons']);
        $defaults['enabled_icons'] = json_encode($defaults['enabled_icons']);

		return $defaults;
	}


	/**
	 * Form UI
	 *
	 * @param object $instance Widget Instance
	 * @author Modern Tribe, Inc.
	 */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, self::get_defaults() );
        include( $this->getTemplateHierarchy( 'widget-admin' ) );
	}



	/**
	 * Loads theme files in appropriate hierarchy: 1) child theme,
	 * 2) parent template, 3) plugin resources. will look in the social-icons/
	 * directory in a theme and the views/ directory in the plugin
	 *
	 * @param string $template template file to search for
	 * @return template path
	 * @author Modern Tribe, Inc. (Matt Wiebe) - from image-widget plugin
	 **/

	function getTemplateHierarchy($template) {
		// whether or not .php was added
		$template_slug = rtrim($template, '.php');
		$template = $template_slug . '.php';

		if ( $theme_file = locate_template(array('social-icons/'.$template)) ) {
			$file = $theme_file;
		} else {
			$file = 'views/' . $template;
		}
		return apply_filters( 'template_social-icons_'.$template, $file);
	}


}



add_action( 'vc_before_init', 'dtbaker_vc_Social_Icons' );

function vc_dtbaker_shortcode_icon_form_field( $settings, $value ) {

    $defaults = dtbaker_Social_Icons::get_defaults();
    $icons = isset($defaults['icons']) ? json_decode($defaults['icons'],true) : array();;
    $enabled_icons = isset($defaults['enabled_icons']) ? json_decode($defaults['enabled_icons'],true) : array();;


    ob_start();
    ?>
    <div class="social_icon_holder">
        <div class="no-svg enabled_icons single"<?php echo !is_array($enabled_icons) || !count($enabled_icons) ? ' style="display:none;"' : '';?>>
            <input type="hidden" name="key" value="enabled_icons" class="social_icon_prefix">
            <label for=""><?php _e('Enabled Icons &amp; Links (click to disable):', 'social_icons'); ?>:</label>
            <br/>
            <div class="enabled_icons_holder">
                <?php
                if(is_array($enabled_icons)){
                    foreach($enabled_icons as $icon_name => $url){ ?>
                        <div>
                            <a href="#" class="webicon <?php echo $icon_name;?> small" onclick="return false;"><?php echo $icon_name;?></a>
                            <input type="text" name="enabled_icons[<?php echo $icon_name;?>]" value="<?php echo esc_attr(strip_tags($url));?>">
                        </div>
                    <?php }
                } ?>
            </div>
        </div>
        <div class="no-svg disabled_icons">
            <label for=""><?php _e('Available Icons: (click to enable):', 'social_icons'); ?>:</label>
            <br/>
            <?php
            if(is_array($icons)){
                foreach($icons as $icon_name => $url){ ?>
                    <a href="#" class="webicon <?php echo $icon_name;?> small" data-icon-name="<?php echo $icon_name;?>" title="<?php echo $icon_name;?>" onclick="return false;"><?php echo $icon_name;?></a>
                <?php }
            } ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function dtbaker_vc_Social_Icons() {

    //$param = 'dtbaker_shortcode_icon';
    //vc_add_shortcode_param( $param, 'vc_' . $param . '_form_field' );

    $defaults = dtbaker_Social_Icons::get_defaults();
    $icons = isset($defaults['icons']) ? json_decode($defaults['icons'],true) : array();;
    $enabled_icons = isset($defaults['enabled_icons']) ? json_decode($defaults['enabled_icons'],true) : array();;

    foreach($icons as $icon => $name){
        $icons[$icon] = $icon;
    }
    $params = array();
    $params[] = array(
        'type' => 'dropdown',
        'param_name' => 'icon',
        'holder' => 'div',
        "class" => "",
        "heading" => 'Choose Icon',
        "value" => $icons,
        "std" => '',
        "description" => ''
    );
    $params[] = array(
        'type' => 'textfield',
        'param_name' => 'link',
        'holder' => 'div',
        "class" => "",
        "heading" => 'Enter Link',
        "value" => 'http://',
        "std" => 'http://',
        "description" => ''
    );
    $params[] = array(
        'type' => 'dropdown',
        'param_name' => 'icon_size',
        'holder' => 'div',
        "class" => "",
        "heading" => 'Choose Size',
        "value" => array(
            'Small' => 'small',
            'Medium' => 'medium',
            'Large' => 'large',
        ),
        "std" => '',
        "description" => ''
    );
    $params[] = array(
        'type' => 'dropdown',
        'param_name' => 'align',
        'holder' => 'div',
        "class" => "",
        "heading" => 'Alignment',
        "value" => array(
            'Left' => 'left',
            'Center' => 'center',
            'Right' => 'right',
        ),
        "std" => '',
        "description" => ''
    );
    /*$params[] = array(
        'type' => 'dtbaker_shortcode_icon',
        'param_name' => 'enabled_icons',
        'holder' => 'div',
        "class" => "",
        "heading" => 'Choose Icons',
        "value" => '',
        "std" => '',
        "description" => ''
    );*/
    vc_map( array(
        "name" => __( "Social Icon", "dtbaker" ),
        "base" => "webicon",
        "class" => "",
        "category" => __( "Content", "dtbaker"),
        "params" => $params
    ) );
}



class dtbaker_Shortcode_Social_Icons
{
    private static $instance = null;

    public static function get_instance()
    {
        if (!self::$instance)
            self::$instance = new self;
        return self::$instance;
    }

    public function init()
    {
        // comment this 'add_action' out to disable shortcode backend mce view feature
        add_action('admin_init', array($this, 'init_plugin'), 20);
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_setup' ) );
        add_shortcode('webicon', array($this, 'dtbaker_shortcode_webicon'));
        add_action('widgets_init', create_function('', 'return register_widget("dtbaker_Social_Icons");'));
    }

    function admin_setup() {
        //wp_enqueue_media();
        wp_enqueue_style( 'dtbaker-social-icons', plugins_url('webicons.css', __FILE__) );
        wp_enqueue_script( 'dtbaker-social-icons-admin', plugins_url('webicons-admin.js', __FILE__) );
    }


    public function init_plugin()
    {
        // todo - copy google maps shortcode to add MCE button
    }

    function dtbaker_shortcode_webicon($atts, $innercontent = '', $code = '')
    {
        extract(shortcode_atts(array(
            'icon' => '',
            'title' => '',
            'icon_size' => 'large',
            'link' => '',
            'align' => '',
        ), $atts));
        return '<a href="' . esc_attr($link) . '" class="webicon ' . esc_attr($icon) . ' ' . ($icon_size != 'medium' ? $icon_size : '') . (!empty($align) ? " align$align" : '') . '" target="_blank" title="' . esc_attr($title) . '">' . esc_attr($title) . '</a>';
    }
}



dtbaker_Shortcode_Social_Icons::get_instance()->init();
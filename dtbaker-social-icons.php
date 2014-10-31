<?php
/*
Plugin Name: Social Icons by Fairhead Creative
Plugin URI: http://dtbaker.net
Description: Display a series of Social Icons (Facebook, YouTube, etc..) in a widget on your website.
Author: Fairhead Creative (icons) and dtbaker (plugin)
Version: 1.0.2
Author URI: https://github.com/adamfairhead/webicons and http://dtbaker.net
Icons are CC-Attrib to http://fairheadcreative.com
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

// Load the widget on widgets_init
function dtbaker_load_social_plugins() {
	register_widget('dtbaker_Social_Icons');
}
add_action('widgets_init', 'dtbaker_load_social_plugins');

/**
 * Tribe_Image_Widget class
 **/
class dtbaker_Social_Icons extends WP_Widget {

	/**
	 * Social Icons constructor
	 */
	function dtbaker_Social_Icons() {
		load_plugin_textdomain( 'social_icons', false, trailingslashit(basename(dirname(__FILE__))) . 'lang/');
		$widget_ops = array( 'classname' => 'widget_social_icons', 'description' => __( 'Display a series of Social Icons (Facebook, YouTube, etc..)', 'social_icons' ) );
		$control_ops = array( 'id_base' => 'widget_social_icons' );
		$this->WP_Widget('widget_social_icons', __('Social Icons', 'social_icons'), $widget_ops, $control_ops);

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
	private static function get_defaults() {

		$defaults = array(
			'title' => 'Contact Us',
			'description' => "Phone:||1800 123 123\nFax:||1800 321 321\nAddress:||Sydney, Australia",
			'icon_size' => 'large',
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



function dtbaker_shortcode_webicon($atts, $innercontent='', $code='') {
    extract(shortcode_atts(array(
        'icon' => '',
        'title' => '',
        'size' => 'large',
        'link' => '',
    ), $atts));
    return '<a href="'.esc_attr($link).'" class="webicon '.esc_attr($icon).' '.($size != 'medium' ? $size : '').'" target="_blank" title="'.esc_attr($title).'">'.esc_attr($title).'</a>';
}
add_shortcode('webicon', 'dtbaker_shortcode_webicon');


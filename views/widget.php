<?php
/**
 * Widget template. This template can be overriden by copying this file to your theme folder: 'social-icons/widget.php'
 */

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


echo $before_widget;

if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }

if ( !empty( $description ) ) {
	echo '<div class="'.$this->widget_options['classname'].'-description" >';
    // split it up by pipes
    $new_description = '';
    foreach(explode("\n",$description) as $line){
        // check if any pipes in this line, if there's pipes, wrap in label.
        if(strpos($line,'||')){
            $bits = explode('||',trim($line));
            $new_description .= '<strong>'.$bits[0].'</strong> <span>'.$bits[1].'</span> <br/>';
        }else{
            $new_description .= $line;
        }
    }
    echo wpautop( $new_description );
	echo "</div>";
}

echo '<!-- start webicons by http://fairheadcreative.com -->';
echo '<p class="widget_social_icons">';
$enabled_icons = json_decode($enabled_icons,true);
if(is_array($enabled_icons)){
    foreach($enabled_icons as $icon_name => $url){ ?>
        <a href="<?php echo esc_attr(strip_tags($url));?>" class="webicon <?php echo esc_attr($icon_name);?> <?php echo $icon_size!='medium' ? esc_attr($icon_size) : '';?>" title="<?php printf(__('Find us on %s','image_widget'),esc_attr($icon_name));?>" target="_blank"><?php echo esc_attr($icon_name);?></a>
    <?php }
}
echo '</p>';
echo '<!-- end webicons by http://fairheadcreative.com -->';

echo $after_widget;
?>
<?php
/*
Plugin Name: JMA Tablet Grid Display
Description: Updated for Genesis REQUIRES tgd
Version: 1.0
Author: John Antonacci
Author URI: http://cleansupersites.com
License: GPL2
*/

function jma_tgd_plugin_activate()
{
    /* activation code here */
}
register_activation_hook(__FILE__, 'jma_tgd_plugin_activate');

function jma_tgd_admin_notice()
{
    echo '<div class="notice notice-error is-dismissible">
             <p>The Genesis tgd Components plugin REQUIRES Genesis Bootstrap plugin</p>
         </div>';
}
function jma_tgd_check_for_plugin()
{
    if (!is_plugin_active('jma-bootstrap-genesis/jma-bootstrap-genesis.php')) {
        add_action('admin_notices', 'jma_tgd_admin_notice');
        return null;
    }
}
add_action('admin_init', 'jma_tgd_check_for_plugin');

function jma_tgd_detect_accordion_shortcode()
{
    global $post;
    $return = false;
    $pattern = get_shortcode_regex();

    if (preg_match_all('/'. $pattern .'/s', $post->post_content, $matches)
        && array_key_exists(2, $matches)
        && in_array('tgd_component', $matches[2])) {
        $return = true;
    }
    return $return;
}

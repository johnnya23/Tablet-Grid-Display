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
             <p>The JMA Tablet Grid Display plugin REQUIRES Genesis Bootstrap plugin</p>
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

add_image_size('jma-tgd-grid-lg', 1000, 800, true);
add_image_size('jma-tgd-grid', 500, 400, true);

function jma_tgd_detect_shortcode($post = false)
{
    if(!$post){
        global $post;
    }
    $return = false;
    $pattern = get_shortcode_regex();

    if (preg_match_all('/'. $pattern .'/s', $post->post_content, $matches)
        && array_key_exists(2, $matches)
        && in_array('tgd_component', $matches[2])) {
        $return = true;
    }
    return $return;
}

function jma_tgd_enqueue()
{
    $min = WP_DEBUG? '': '.min';
    // wp_enqueue_style( $handle, $src, $deps, $ver, $media );
    wp_enqueue_style('jma_tgd_css', trailingslashit(plugin_dir_url(__FILE__)) . 'tgd-style' . $min . '.css', array());

    $output = get_transient('jma_tgd_general_css');
    if (false == $output) {
        // It wasn't there, so regenerate the data and save the transient
        //$mods = jma_tgd_get_theme_mods('jma_tgd_');
        $css = '';

        //$output = jma_tgd_process_css_array($css);
    set_transient('jma_tgd_general_css', $output);
    }
    //wp_add_inline_style('JMA_tgd_combined_css', $output);
}
add_action('wp_enqueue_scripts', 'jma_tgd_enqueue');


function jma_tgd_grid_display(){
    global $wp_query;
    $id = $wp_query->get_query_object_id();
    if(!get_field('tablet_groups', $id))return;

    $tablets_array = get_field('tablet_groups', $id);
    $open = $inner = $close = '';
    /*foreach($tablets_array as $tablets)*/
    if(isset($tablets_array[0]['tablet'])){
    $tablets = $tablets_array[0]['tablet'];
    foreach($tablets as $i => $tablet){
        $img_size = !$i? 'add_image_size-lg':'add_image_size';
        $inner .= '<div class="tgd-item">';
          $inner .= '<div class="tgd-bg">';
          if(isset($tablet['image']) && $tablet['image'])
          $inner .= wp_get_attachment_image($tablet['image'], $img_size);
            $inner .= '<div class="tgd-overlay">';
            if(isset($tablet['title']) && $tablet['title'])
            $inner .= '<h3>' . $tablet['title'] . '</h3>';
            if(isset($tablet['excerpt']) && $tablet['excerpt'])
            $inner .= '<div>' . $tablet['excerpt'] . '</div>';
          $inner .= '</div>';
        $inner .= '</div>';
        $inner .= '</div>';
    }
    $open .= '<div class="tgd-filler" style="background-image: url(\'' . $tablets_array[0]['background_image'] . '\');background-repeat: no-repeat; background-size: cover">';
    $open .= '<div class="tgd-wrapper">';
    $open .= '<div class="tgd-outer">';
    $open .= '<div class="tgd-inner">';



    $close .= '</div>';
    $close .= '</div>';
    $close .= '</div>';
    $close .= '</div>';
}
/*echo '<pre>';
    print_r($tablets);
    echo '</pre>';*/
    return $open . $inner . $close;
}
add_shortcode('tgd_grid_display', 'jma_tgd_grid_display');

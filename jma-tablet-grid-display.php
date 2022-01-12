<?php
/*
Plugin Name: JMA Tablet Grid Display
Description: Updated for Genesis REQUIRES
Version: 1.0
Author: John Antonacci
Author URI: http://cleansupersites.com
License: GPL2
*/

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

    /*$output = get_transient('jma_tgd_general_css');
    if (false == $output) {
        // It wasn't there, so regenerate the data and save the transient
        //$mods = jma_tgd_get_theme_mods('jma_tgd_');
        $css = '';

        //$output = jma_tgd_process_css_array($css);
    set_transient('jma_tgd_general_css', $output);
}*/
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
        if($i > 4)break;
        //echo '<pre>';print_r($tablet);echo '</pre>';
        $link_open = $link_close = $click_for_more = '';
        if(isset($tablet['link']) && is_array($tablet['link'])){
            $target = isset($tablet['link']['target']) && $tablet['link']['target']?$tablet['link']['target']:'_self';
            $link_open = '<a href="' . esc_url($tablet['link']['url']) . '" target ="' . $target . '" title="' . $tablet['link']['title'] . '">';
            $link_close = '</a>';
            $click_for_more = '<span class="tgd-click">click to see more</span>';
        }
        $img_size = !$i? 'add_image_size-lg':'add_image_size';


        $inner .= '<div class="tgd-item">';
        $inner .= '<div class="tgd-bg">';

        if(isset($tablet['image']) && $tablet['image'])
            $inner .= wp_get_attachment_image($tablet['image'], $img_size);

        if(isset($tablet['title']) && $tablet['title'])
            $inner .= '<h3>' . wp_kses_decode_entities($tablet['title']) . '</h3>';

        $inner .= $link_open;
        $inner .= '<span class="tgd-overlay">';
        $inner .= $click_for_more;
            if(isset($tablet['excerpt']) && $tablet['excerpt'])
            $inner .= '<span style="display:block">' . esc_html($tablet['excerpt']) . '</span>';
        $inner .= '</span>';/*tgd-overlay*/
        $inner .= $link_close;

        $inner .= '</div>';/*tgd-item*/
        $inner .= '</div>';/*tgd-bg*/
    }
    $bg = '';
    if(isset($tablets_array[0]['background_image']) && $tablets_array[0]['background_image']){
        $bg =' style="background-image: url(\'' . esc_url($tablets_array[0]['background_image']) . '\');background-repeat: no-repeat; background-size: cover"';
    }elseif(get_the_post_thumbnail_url($id,'full')){
        $bg =' style="background-image: url(\'' . get_the_post_thumbnail_url($id,'full') . '\');background-repeat: no-repeat; background-size: cover"';
    }
    $open .= '<div class="tgd-filler"' . $bg . '>';
    $open .= '<div class="tgd-wrapper">';
    $open .= '<div class="tgd-outer">';
    if(isset($tablets_array[0]['tablets_title']) && $tablets_array[0]['tablets_title'])
    $open .= '<h3 style="text-align:center">' . wp_kses_decode_entities($tablets_array[0]['tablets_title']) . '</h3>';
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

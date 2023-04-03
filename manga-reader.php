<?php
/**
 * Plugin Name: Manga Reader
 * Description: A super basic manga reader plugin that works with almost any wordpress theme
 * Version: BETA
 * Author: Ha Sky
 * Author URI: https://hasky.rf.gd/read
 **/

function manga_reader_shortcode($atts) {
    // Extract shortcode attributes
    extract(shortcode_atts(array(
        'images' => ''
    ), $atts));

    // Convert image links to array
    $images = explode(',', $images);

    // Output HTML markup
    $output = '<div class="manga-reader">';
    $output .= '<div class="manga-images">';
    foreach ($images as $image) {
        $output .= '<img src="' . $image . '" />';
    }
    $output .= '</div>';
    $output .= '<div class="manga-pagination"></div>';
    $output .= '<div class="manga-reader-view">';
    $output .= '<button class="paged-view active">Paged View</button>';
    $output .= '<button class="list-view">List View</button>';
    $output .= '</div>';
    $output .= '</div>';

    // Load scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('manga-reader-script', plugins_url('manga-reader.js', __FILE__), array('jquery'), '1.0', true);

    return $output;
}
add_shortcode('manga_reader', 'manga_reader_shortcode');

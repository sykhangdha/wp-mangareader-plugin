<?php
/**
 * Plugin Name: Manga Reader
 * Description: A manga reader plugin.
 * Version: 1.1
 * Author: Ha Sky
 * Author URI: https://hasky.rf.gd
 **/

function manga_reader_shortcode($atts) {
    // Extract shortcode attributes
    extract(shortcode_atts(array(
        'images' => ''
    ), $atts));

    global $post;

    // Check if images were provided as shortcode attribute, if not, get image links from custom field
    if (empty($images)) {
        $images = get_post_meta(get_the_ID(), 'image_links', true);
        // Convert image links to array
        $images = preg_split('/\r\n|[\r\n]/', $images);
        $images = array_map('trim', $images);
        $images = array_filter($images);
    } else {
        // Convert image links to array
        $images = preg_split('/\s*(?:,|$)\s*/', $images);
    }

    // Output HTML markup
    $output = '<div class="manga-reader">';
    $output .= '<div class="manga-reader-view">';
    $output .= '<button class="paged-view active">Paged View</button>';
    $output .= '<button class="list-view">List View</button>';
    $output .= '</div>';
    $output .= '<div class="manga-images">';
    foreach ($images as $image) {
        $output .= '<img class="img-loading" src="' . $image . '" title="'.basename($image).'" alt="'.basename($image).'" />';
    }
    $output .= '</div>';
    $output .= '<div class="manga-pagination"></div>';
    $output .= '</div>';

    return $output;
}

// Load scripts
function manga_reader_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('manga-reader-script', plugins_url('manga-reader.js', __FILE__), array('jquery'), '1.2', true);

    // Pass loading image URL to JavaScript file
    wp_localize_script( 'manga-reader-script', 'manga_data', array(
        'loading_image' => plugins_url( '/images/loading.gif', __FILE__ )
    ));
}
add_action('wp_enqueue_scripts', 'manga_reader_scripts');

// Register shortcode
function manga_reader_register_shortcode() {
    add_shortcode('manga_reader', 'manga_reader_shortcode');
}
add_action('init', 'manga_reader_register_shortcode');

// Add custom rewrite rule
function manga_reader_rewrite_rules($rules) {
    $new_rules = array(
        'manga/([^/]+)/?$' => 'index.php?manga=$matches[1]'
    );
    return $new_rules + $rules;
}
add_filter('rewrite_rules_array', 'manga_reader_rewrite_rules');

// Handle custom query variables
function manga_reader_query_vars($vars) {
    $vars[] = 'manga';
    return $vars;
}
add_filter('query_vars', 'manga_reader_query_vars');

// Handle custom template
function manga_reader_template($template) {
    global $wp_query;
    if (isset($wp_query->query_vars['manga'])) {
        return plugin_dir_path(__FILE__) . 'manga-reader.php';
    }
    return $template;
}
add_filter('template_include', 'manga_reader_template');

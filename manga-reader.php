<?php
/**
 * Plugin Name: Manga Reader
 * Description: A manga reader plugin.
 * Version: 3.0
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
        $image_links = get_post_meta(get_the_ID(), 'image_links', true);

        // Check if there is any text in the custom field 'image_links'
        if (!empty($image_links)) {
            // Convert image links to array
            $images = preg_split('/\r\n|[\r\n]/', $image_links);
            $images = array_map('trim', $images);
            $images = array_filter($images);
        }
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
    foreach ($images as $key => $image) {
        $output .= '<img class="img-loading" src="' . $image . '" title="'.basename($image).'" alt="'.basename($image).'" data-post-id="' . get_the_ID() . '" data-post-category="' . get_the_category()[0]->slug . '" data-image-index="' . $key . '" />';
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
        // Check if the user is in paged view or list view
        $paged_view = isset($_COOKIE['manga_reader_view']) && $_COOKIE['manga_reader_view'] === 'paged';
        $list_view = isset($_COOKIE['manga_reader_view']) && $_COOKIE['manga_reader_view'] === 'list';

        // Check if last image is clicked
        $last_image_clicked = isset($_GET['last_image_clicked']) && $_GET['last_image_clicked'] === 'true';

        // Get the current post ID
        $post_id = $wp_query->queried_object_id;

        // Get the current category ID
        $category = get_the_category($post_id);
        $category_id = !empty($category) ? $category[0]->cat_ID : '';

        // Get the next post ID from the same category
        $next_post_id = get_next_post_id($category_id, $post_id);

        // Load different templates based on view and last image clicked
        if ($paged_view || $last_image_clicked) {
            return plugin_dir_path(__FILE__) . 'manga-reader-paged.php';
        } elseif ($list_view) {
            return plugin_dir_path(__FILE__) . 'manga-reader-list.php';
        }

        return $template;
    }
    return $template;
}
add_filter('template_include', 'manga_reader_template');

// Add shortcode on post publish or update
function manga_reader_add_shortcode_on_publish($post_id) {
    $post = get_post($post_id);

    if ($post->post_type === 'post' && ($post->post_status === 'publish' || $post->post_status === 'draft')) {
        $image_links = get_post_meta($post_id, 'image_links', true);

        // Check if there is any text in the custom field 'image_links'
        if (!empty($image_links)) {
            $content = $post->post_content;
            if (strpos($content, '[manga_reader]') === false) {
                $updated_content = $content . '[manga_reader]';
                wp_update_post(array('ID' => $post_id, 'post_content' => $updated_content));
            }
        }
    }
}
add_action('save_post', 'manga_reader_add_shortcode_on_publish', 10, 2);

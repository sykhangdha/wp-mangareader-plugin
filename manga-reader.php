<?php
/**
 * Plugin Name: Manga Reader
 * Description: A manga reader plugin.
 * Version: MR4 - hotfix
 * Author: Ha Sky
 * Author URI: https://hasky.rf.gd
 */

// Start a session
session_start();

// Add admin menu
function manga_reader_admin_menu() {
    add_menu_page(
        'WP-MangaReader Settings',
        'WP-MangaReader',
        'manage_options',
        'wp-manga-reader-settings',
        'manga_reader_settings_page'
    );
}
add_action('admin_menu', 'manga_reader_admin_menu');

// Display settings page
function manga_reader_settings_page() {
    ?>
    <div class="wrap">
        <h1>WP-MangaReader Settings</h1>
        <h2>How to add the - <a href="https://skyha.rf.gd/reader/" target="_blank" title="Reader Template DEMO">Reader Template PHP code</a></h2>
        <p>To use the reader page template, download this plugin <button id="recommended-plugin-link" class="button" onclick="redirectRecommendedPlugin()">DOWNLOAD NOW</button></p>
        <p>Follow these steps:</p>
        <ol>
            <li>Download the plugin and upload the ZIP file to WordPress using the Plugins settings.</li>
            <li>After activating the plugin, go to the left side menu and select "XYZ PHP Code" -> PHPCode Snippet.</li>
            <li>Create a new snippet and copy + paste this code here: <a href="https://raw.githubusercontent.com/sykhangdha/wp-mangareader-plugin/main/reader-example.php" target="_blank">https://raw.githubusercontent.com/sykhangdha/wp-mangareader-plugin/main/reader-example.php</a></li>
            <li>A shortcode will be given; use that shortcode and paste it on any page or post.</li>
        </ol>

        <h2>Extensions - Quick Chapter Adder</h2>
        <?php
        // Output the form directly on the settings page
        echo do_shortcode('[custom_category_post_generator]');
        ?>
        
        <script>
            function redirectRecommendedPlugin() {
                window.location.href = 'https://wordpress.org/plugins/insert-php-code-snippet/';
            }
        </script>
    </div>
    <?php
}

// Define the shortcode for Quick Chapter Adder
function custom_category_post_generator_shortcode($atts) {
    // Extract shortcode attributes
    extract(shortcode_atts(array(
        'category' => '',
    ), $atts));

    // Initialize the number or retrieve it from the session
    if (!isset($_SESSION['post_number'])) {
        $_SESSION['post_number'] = 1;
    }

    // Initialize the category or retrieve it from the session
    if (!isset($_SESSION['selected_category'])) {
        $_SESSION['selected_category'] = '';
    }

    // Output the form
    $output = '';

    // Handle form submission
    if (isset($_POST['generate_post'])) {
        $selected_category_slug = sanitize_text_field($_POST['category']);
        $user_entered_number = intval($_POST['number']);
        $image_links = sanitize_textarea_field($_POST['image_links']);

        if (!empty($selected_category_slug) && $user_entered_number > 0) {
            // Get the selected category object by slug
            $selected_category = get_term_by('slug', $selected_category_slug, 'category');

            if ($selected_category && !is_wp_error($selected_category)) {
                // Use the user-entered number for the current post
                $post_number = $user_entered_number;

                // Create a new post
                $post_title = $selected_category->name . ' - ' . $post_number;
                $post_content = '[manga_reader]'; // Add [manga_reader] shortcode to post content

                // Create post data
                $post_data = array(
                    'post_title'    => $post_title,
                    'post_content'  => $post_content,
                    'post_status'   => 'publish',
                    'post_category' => array($selected_category->term_id),
                );

                // Insert the post
                $post_id = wp_insert_post($post_data);

                // Save image links as a single block of text in the custom field
                update_post_meta($post_id, 'image_links', $image_links);

                $output .= '<p>Post created successfully: <a href="' . get_permalink($post_id) . '">' . $post_title . '</a></p>';

                // Store the selected category in the session
                $_SESSION['selected_category'] = $selected_category_slug;

                // Increment the number for the next post
                $_SESSION['post_number'] = $user_entered_number + 1;

                // Refresh the page to start a new post
                echo '<meta http-equiv="refresh" content="0">';
            } else {
                $output .= '<p>Invalid manga name selected.</p>';
            }
        } else {
            $output .= '<p>Please select a category and enter a valid starting number.</p>';
        }
    } else {
        // Preselect the category from the session
        $selected_category_slug = $_SESSION['selected_category'];

        // Output the form when the page initially loads
        $output .= '
            <form method="post">
                <label for="category">Select Manga:</label>
                <select name="category">
                    <option value="">Select Manga</option>';

        // Retrieve all categories with no posts
        $categories = get_categories(array(
            'hide_empty' => false,
        ));
        foreach ($categories as $cat) {
            $selected = ($cat->slug === $selected_category_slug) ? 'selected' : '';
            $output .= '<option value="' . $cat->slug . '" ' . $selected . '>' . $cat->name . '</option>';
        }

        $output .= '
                </select>
                <br>
                <label for="number">Enter chapter number:</label>
                <input type="text" name="number" value="' . $_SESSION['post_number'] . '"><br>
                <label for="image_links">Image Links (one block of text):</label>
                <textarea name="image_links" rows="5"></textarea><br>
                <input type="submit" name="generate_post" value="Create Post">
            </form>';
    }

    return $output;
}

// Register the shortcode
add_shortcode('custom_category_post_generator', 'custom_category_post_generator_shortcode');

// Define the shortcode for manga_reader
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

    // Output HTML markup with added CSS styles and Magnific Popup attributes
    $output = '<style>
        /* Other styles for manga reader view */
        .hentry .manga-reader .manga-images {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }

        /* Change width percentage to change image size! */
        .manga-reader .manga-images a {
            width: 90%;
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
        }

        .hentry .manga-reader .manga-images img {
            transform: none;
        }

        /* Byline */
        .hentry .entry-meta .byline {
            display: none;
        }

        /* Left part */
        .full-site .left-part {

        }

        /* Mobile responsiveness */
        @media only screen and (max-width: 600px) {
            .manga-reader .manga-images a {
                width: 100%;
            }
        }
    </style>';

    $output .= '<div class="manga-reader">';
    $output .= '<div class="manga-images">';
    foreach ($images as $key => $image) {
        $output .= '<a class="img-popup" href="' . $image . '" title="'.basename($image).'" data-post-id="' . get_the_ID() . '" data-post-category="' . get_the_category()[0]->slug . '" data-image-index="' . $key . '">';
        $output .= '<img class="img-loading" src="' . $image . '" alt="'.basename($image).'" />';
        $output .= '</a>';
    }
    $output .= '</div>';
    $output .= '</div>';

    return $output;
}

// Load scripts
function manga_reader_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('magnific-popup', 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js', array('jquery'), '1.1.0', true);
    wp_enqueue_script('manga-reader-script', plugins_url('manga-reader.js', __FILE__), array('jquery', 'magnific-popup'), '5.0', true);

    // Preload images before initializing Magnific Popup
    $image_links = get_post_meta(get_the_ID(), 'image_links', true);

    if (!empty($image_links)) {
        $images = preg_split('/\r\n|[\r\n]/', $image_links);
        $images = array_map('trim', $images);
        $images = array_filter($images);

        echo '<script>';
        foreach ($images as $image) {
            echo 'var preloadImage = new Image(); preloadImage.src = "' . $image . '";';
        }
        echo '</script>';
    }

    // Pass loading image URL to JavaScript file
    wp_localize_script('manga-reader-script', 'manga_data', array(
        'loading_image' => plugins_url('/images/loading.gif', __FILE__)
    ));

    // Add Magnific Popup styles
    wp_enqueue_style('magnific-popup', 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css');
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
        // Get the current post ID
        $post_id = $wp_query->queried_object_id;

        // Get the current category ID
        $category = get_the_category($post_id);
        $category_id = !empty($category) ? $category[0]->cat_ID : '';

        // Load different templates based on view and last image clicked
        return plugin_dir_path(__FILE__) . 'manga-reader.php';
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

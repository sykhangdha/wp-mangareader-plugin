<?php
/**
 * Plugin Name: Manga Reader
 * Description: A manga reader plugin.
 * Version: MR5 - Features Update
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
            $selected_category = get_term_by('slug', $selected_category_slug, 'manga');

            if ($selected_category && !is_wp_error($selected_category)) {
                // Use the user-entered number for the current post
                $post_number = $user_entered_number;

               // Create a new post
				$post_title = 'Chapter ' . $post_number;
				$post_content = '[manga_reader]'; // Add [manga_reader] shortcode to post content


                // Create post data
                $post_data = array(
                    'post_title'    => $post_title,
                    'post_content'  => $post_content,
                    'post_status'   => 'publish',
                    'post_type'     => 'chapter', // Changed to 'chapter' post type
                    'tax_input'     => array(
                        'manga' => array($selected_category->term_id),
                    ),
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

        // Retrieve all manga taxonomies
        $mangas = get_terms(array(
            'taxonomy' => 'manga',
            'hide_empty' => false,
        ));
        foreach ($mangas as $manga) {
            $selected = ($manga->slug === $selected_category_slug) ? 'selected' : '';
            $output .= '<option value="' . $manga->slug . '" ' . $selected . '>' . $manga->name . '</option>';
        }

        $output .= '
                </select>
                <br>
                <label for="number">Enter chapter number:</label>
                <input type="text" name="number" value="' . $_SESSION['post_number'] . '"><br>
                <label for="image_links">Image Links:</label>
                <textarea name="image_links" rows="5" cols="40"></textarea><br>
                <input type="submit" name="generate_post" value="Generate Chapter">
            </form>';
    }

    return $output;
}
add_shortcode('custom_category_post_generator', 'custom_category_post_generator_shortcode');

// Shortcode to display manga images
function manga_reader_shortcode($atts) {
    $atts = shortcode_atts(array(), $atts, 'manga_reader');
    $post_id = get_the_ID();
    $image_links = get_post_meta($post_id, 'image_links', true);

    if (empty($image_links)) {
        return '<p>No images found for this chapter.</p>';
    }

    $images = explode("\n", $image_links);

    $output = '<div class="manga-reader">';
$output .= '<div class="manga-images">';
foreach ($images as $image) {
    $image = esc_url(trim($image));
    $output .= '<a href="' . $image . '" class="manga-image">';
    $output .= '<img class="lazyload" src="' . $image . '" alt="Manga Image">';
    $output .= '</a>';
}
$output .= '</div>';
$output .= '</div>';

return $output;
}
add_shortcode('manga_reader', 'manga_reader_shortcode');

// Load required scripts and styles
function manga_reader_scripts() {
    wp_enqueue_script('magnific-popup', 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js', array('jquery'), '1.1.0', true);
    wp_enqueue_style('magnific-popup', 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css', array(), '1.1.0');
    wp_enqueue_style('manga-reader', plugin_dir_url(__FILE__) . 'css/manga-reader.css', array(), '1.0');
    wp_enqueue_script('manga-reader', plugin_dir_url(__FILE__) . 'js/manga-reader.js', array('jquery', 'magnific-popup'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'manga_reader_scripts');

// Register custom post type for chapters
function create_chapters_post_type() {
    $labels = array(
        'name' => _x('Chapters', 'post type general name'),
        'singular_name' => _x('Chapter', 'post type singular name'),
        'menu_name' => _x('Chapters', 'admin menu'),
        'name_admin_bar' => _x('Chapter', 'add new on admin bar'),
        'add_new' => _x('Add New', 'chapter'),
        'add_new_item' => __('Add New Chapter'),
        'new_item' => __('New Chapter'),
        'edit_item' => __('Edit Chapter'),
        'view_item' => __('View Chapter'),
        'all_items' => __('All Chapters'),
        'search_items' => __('Search Chapters'),
        'parent_item_colon' => __('Parent Chapters:'),
        'not_found' => __('No chapters found.'),
        'not_found_in_trash' => __('No chapters found in Trash.'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'chapter', 'with_front' => false),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
    );

    register_post_type('chapter', $args);
}
add_action('init', 'create_chapters_post_type');

// Register custom taxonomy for manga
function create_manga_taxonomy() {
    $labels = array(
        'name' => _x('Manga', 'taxonomy general name'),
        'singular_name' => _x('Manga', 'taxonomy singular name'),
        'search_items' => __('Search Manga'),
        'all_items' => __('All Manga'),
        'parent_item' => __('Parent Manga'),
        'parent_item_colon' => __('Parent Manga:'),
        'edit_item' => __('Edit Manga'),
        'update_item' => __('Update Manga'),
        'add_new_item' => __('Add New Manga'),
        'new_item_name' => __('New Manga Name'),
        'menu_name' => __('Manga'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'manga'),
    );

    register_taxonomy('manga', array('chapter'), $args);
}
add_action('init', 'create_manga_taxonomy', 0);

// Add custom meta box for manga selection
function add_manga_meta_box() {
    add_meta_box(
        'manga_meta_box',
        __('Manga', 'text_domain'),
        'render_manga_meta_box',
        'chapter',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'add_manga_meta_box');

function render_manga_meta_box($post) {
    // Retrieve current manga selection
    $manga_id = get_post_meta($post->ID, 'manga_id', true);
    $mangas = get_terms(array(
        'taxonomy' => 'manga',
        'hide_empty' => false,
    ));
    ?>
    <select name="manga_id" id="manga_id">
        <option value=""><?php _e('Select Manga', 'text_domain'); ?></option>
        <?php foreach ($mangas as $manga) : ?>
            <option value="<?php echo esc_attr($manga->term_id); ?>" <?php selected($manga_id, $manga->term_id); ?>>
                <?php echo esc_html($manga->name); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

function save_manga_meta_box($post_id) {
    if (isset($_POST['manga_id'])) {
        update_post_meta($post_id, 'manga_id', sanitize_text_field($_POST['manga_id']));
    }
}
add_action('save_post', 'save_manga_meta_box');

// Add custom rewrite rules
function custom_rewrite_rules() {
    add_rewrite_rule(
        '^([^/]+)/([^/]+)/?',
        'index.php?chapter=$matches[2]&manga=$matches[1]',
        'top'
    );
}
add_action('init', 'custom_rewrite_rules');


// Modify the title display of Chapters in admin
function custom_chapters_admin_title($title, $post_id) {
    // Check if it's a chapter post type
    if (get_post_type($post_id) === 'chapter') {
        // Get the manga term associated with this chapter
        $manga_terms = get_the_terms($post_id, 'manga');
        if (!empty($manga_terms)) {
            // Get the first manga name
            $manga_name = $manga_terms[0]->name;
            // Get the chapter number
            $chapter_number = str_replace('Chapter ', '', $title);
            // Replace the title with [manga name] chapter number
            $title = $manga_name . ' ' . $chapter_number;
        }
    }
    return $title;
}
add_filter('the_title', 'custom_chapters_admin_title', 10, 2);



// Modify post type permalink structure
function custom_chapter_post_link($post_link, $post) {
    if (is_object($post) && $post->post_type == 'chapter') {
        $terms = wp_get_object_terms($post->ID, 'manga');
        if ($terms) {
            return str_replace('chapter/', $terms[0]->slug . '/', $post_link);
        }
    }
    return $post_link;
}
add_filter('post_type_link', 'custom_chapter_post_link', 10, 2);
?>

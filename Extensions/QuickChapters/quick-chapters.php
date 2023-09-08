<?php
/*
Plugin Name: Quick Chapters
Description: A simple extension for WP-manga to quickly add chapters
Version: BETA
Author: Sky Ha
*/

// Start a session
session_start();

// Define the plugin's shortcode
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
                $output .= '<p>Invalid category selected.</p>';
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
                <label for="category">Select a category (with no posts):</label>
                <select name="category">
                    <option value="">Select Category</option>';

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
                <label for="number">Enter a starting number:</label>
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

// Add a submenu page under "Posts" in the WordPress admin menu
function add_custom_category_post_generator_submenu() {
    add_submenu_page(
        'edit.php', // Parent menu (Posts)
        'Category Post Generator', // Page title
        'Category Post Generator', // Menu title
        'manage_options', // Capability required to access the page
        'custom-category-post-generator', // Page slug
        'custom_category_post_generator_page' // Callback function to display the page
    );
}

add_action('admin_menu', 'add_custom_category_post_generator_submenu');

// Callback function to display the custom submenu page
function custom_category_post_generator_page() {
    echo '<div class="wrap">';
    echo '<h2>Category Post Generator</h2>';
    echo do_shortcode('[custom_category_post_generator]');
    echo '</div>';
}

<?php
/**
 * Plugin Name: Manga Reader
 * Description: A manga reader plugin.
 * Version: 5
 * Author: Ha Sky
 * Author URI: https://hasky.rf.gd
 **/


// Add JavaScript to handle view toggling
$output .= '<script>
    document.addEventListener("DOMContentLoaded", function() {
        var pagedViewButton = document.querySelector(".paged-view");
        var listViewButton = document.querySelector(".list-view");
        var mangaReader = document.querySelector(".manga-reader");

        function updateView() {
            if (mangaReader.classList.contains("list-view")) {
                document.querySelector(".pagination-nav").style.display = "none";
            } else {
                document.querySelector(".pagination-nav").style.display = "block";
            }
        }

        pagedViewButton.addEventListener("click", function() {
            mangaReader.classList.remove("list-view");
            mangaReader.classList.add("paged-view");
            updateView();
        });

        listViewButton.addEventListener("click", function() {
            mangaReader.classList.remove("paged-view");
            mangaReader.classList.add("list-view");
            updateView();
        });

        // Initial update on page load
        updateView();
    });
</script>';

// Add inline CSS to handle default button styling
$output .= '<style>
    .btn {
        border-radius: 0.25rem;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        display: inline-block;
        transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .btn-primary {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-secondary {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }
    .btn-outline-primary {
        color: #007bff;
        border-color: #007bff;
    }
    .btn-outline-primary:hover {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-outline-primary:focus, .btn-outline-primary.focus {
        box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.5);
    }
    /* Pagination nav */
    .pagination-nav {
        display: block; /* Default state */
    }
    .manga-reader.list-view .pagination-nav {
        display: none; /* Hide in list view */
    }
</style>';

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
    $atts = shortcode_atts(array(
        'category' => '',
    ), $atts);

    // Initialize the number or retrieve it from the session
    if (!isset($_SESSION['post_number'])) {
        $_SESSION['post_number'] = 1;
    }

    // Initialize the category or retrieve it from the session
    if (!isset($_SESSION['selected_category'])) {
        $_SESSION['selected_category'] = '';
    }

    // Handle form submission
    if (isset($_POST['generate_post'])) {
        $selected_category_slug = sanitize_text_field($_POST['category']);
        $user_entered_number = intval($_POST['number']);
        $image_links = sanitize_textarea_field($_POST['image_links']);

        if (!empty($selected_category_slug) && $user_entered_number > 0) {
            $selected_category = get_term_by('slug', $selected_category_slug, 'category');

            if ($selected_category && !is_wp_error($selected_category)) {
                $post_title = $selected_category->name . ' - ' . $user_entered_number;
                $post_content = '[manga_reader]'; // Add [manga_reader] shortcode to post content

                $post_data = array(
                    'post_title'    => $post_title,
                    'post_content'  => $post_content,
                    'post_status'   => 'publish',
                    'post_category' => array($selected_category->term_id),
                );

                $post_id = wp_insert_post($post_data);
                update_post_meta($post_id, 'image_links', $image_links);

                $_SESSION['selected_category'] = $selected_category_slug;
                $_SESSION['post_number'] = $user_entered_number + 1;

                echo '<meta http-equiv="refresh" content="0">';
            } else {
                echo '<p>Invalid manga name selected.</p>';
            }
        } else {
            echo '<p>Please select a category and enter a valid starting number.</p>';
        }
    }

    $selected_category_slug = $_SESSION['selected_category'];
    $output = '
        <form method="post">
            <label for="category">Select Manga:</label>
            <select name="category">
                <option value="">Select Manga</option>';

    $categories = get_categories(array('hide_empty' => false));
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

    return $output;
}

// Register the shortcode for Quick Chapter Adder
add_shortcode('custom_category_post_generator', 'custom_category_post_generator_shortcode');


function manga_reader_shortcode($atts) {
    // Extract shortcode attributes
    $atts = shortcode_atts(array(
        'images' => ''
    ), $atts);

    global $post;

    // Get recent posts from the same category
$category = get_the_category($post->ID);
$recent_posts = '';
if (!empty($category)) {
    $category_id = $category[0]->term_id;
    $recent_posts_query = new WP_Query(array(
        'cat' => $category_id,
        'post__not_in' => array($post->ID),
        'posts_per_page' => -1, // Ensure all posts from the category are retrieved
    ));

    if ($recent_posts_query->have_posts()) {
        $recent_posts .= '<div class="recent-chapters">';
        $recent_posts .= '<label for="chapterSelect">Select Chapter:</label>';
        $recent_posts .= '<div class="chapter-select-wrapper">'; // Wrapper for styling
        $recent_posts .= '<select id="chapterSelect" class="form-control">';
        $recent_posts .= '<option value="" disabled selected>Select chapter</option>'; // Placeholder option
        while ($recent_posts_query->have_posts()) {
            $recent_posts_query->the_post();
            
            // Extract chapter number from title
            $title = get_the_title();
            // Assuming title is in the format "Chapter - [number]"
            $option_text = preg_replace('/^.*Chapter - /', 'Chapter - ', $title);
            
            $recent_posts .= '<option value="' . esc_url(get_permalink()) . '">' . esc_html($option_text) . '</option>';
        }
        $recent_posts .= '</select>';
        $recent_posts .= '</div>'; // End wrapper
        $recent_posts .= '</div>';
        wp_reset_postdata();
    }
}

    // Get images from post meta or shortcode attribute
    if (empty($atts['images'])) {
        $images = get_post_meta(get_the_ID(), 'image_links', true);
        $images = preg_split('/\r\n|[\r\n]/', $images);
        $images = array_map('trim', $images);
        $images = array_filter($images);
    } else {
        $images = preg_split('/\s*(?:,|$)\s*/', $atts['images']);
    }

    // Output HTML markup
    $output = '<div class="manga-reader">';
    $output .= $recent_posts; // Add recent chapters dropdown above the reader

    $output .= '<div class="manga-reader-view btn-group" role="group">';
    $output .= '<button class="btn btn-primary paged-view active">Paged View</button>';
    $output .= '<button class="btn btn-secondary list-view">List View</button>';
    $output .= '</div>';

    $output .= '<div class="manga-images">';
    foreach ($images as $key => $image) {
        $output .= '<img class="img-fluid" src="' . esc_url($image) . '" title="' . esc_attr(basename($image)) . '" alt="' . esc_attr(basename($image)) . '" />';
    }
    $output .= '</div>';

    $output .= '<div class="manga-pagination text-center mt-3"></div>';

    // Page navigation buttons
    $output .= '<div class="pagination-nav text-center mt-3">';
    $output .= '<button class="btn btn-outline-primary prev-page">Previous page</button>';
    $output .= '<button class="btn btn-outline-primary next-page">Next page</button>';
    $output .= '</div>';

    $output .= '</div>'; // End manga-reader

    // Inline styles for button styling
    $output .= '<style>
        .btn {
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            display: inline-block;
            transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-secondary {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-outline-primary {
            color: #007bff;
            border-color: #007bff;
        }
        .btn-outline-primary:hover {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-outline-primary:focus, .btn-outline-primary.focus {
            box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.5);
        }
    </style>';

    return $output;
}

// Load scripts and styles
function manga_reader_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('manga-reader-script', plugins_url('manga-reader.js', __FILE__), array('jquery'), '1.3', true);
    
    // Pass loading image URL to JavaScript file
    wp_localize_script('manga-reader-script', 'manga_data', array(
        'loading_image' => plugins_url('/images/loading.gif', __FILE__)
    ));
}
add_action('wp_enqueue_scripts', 'manga_reader_scripts');

// Register shortcode
function manga_reader_register_shortcode() {
    add_shortcode('manga_reader', 'manga_reader_shortcode');
}
add_action('init', 'manga_reader_register_shortcode');


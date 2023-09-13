<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 */
get_header(); ?>

<div class="wrap">
    <div id="primary" class="content-area uk-grid">
	
		<?php
// Get all manga posts
$manga_posts = get_posts(array(
    'post_type' => 'mangas',
    'posts_per_page' => -1,
));

// Initialize an array to store manga posts with chapters and their latest chapter dates
$mangas_with_chapters = array();

// Loop through manga posts to check for chapters and get their latest chapter dates
foreach ($manga_posts as $manga_post) {
    $manga_id = $manga_post->ID;

    // Query chapters for the current manga, sorted by date to get the latest chapter
    $chapters_query = new WP_Query(array(
        'post_type' => 'chapters',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => 'manga', // Custom field key for chapters relationship
                'value' => '"' . $manga_id . '"', // Matches exactly the manga ID
                'compare' => 'LIKE'
            )
        ),
        'orderby' => 'date',
        'order' => 'DESC',
    ));

    // If the manga has at least one chapter, add it to the list with its latest chapter date
    if ($chapters_query->have_posts()) {
        $latest_chapter = $chapters_query->posts[0];
        $latest_chapter_date = get_post_field('post_date', $latest_chapter); // Get the post date of the latest chapter
        $mangas_with_chapters[] = array(
            'manga_post' => $manga_post,
            'latest_chapter_date' => $latest_chapter_date,
        );
    }
}

// Sort the array of manga posts with chapters by their latest chapter date in descending order
usort($mangas_with_chapters, function ($a, $b) {
    return strtotime($b['latest_chapter_date']) - strtotime($a['latest_chapter_date']);
});

// Check if there are mangas with chapters to display
if (!empty($mangas_with_chapters)) :
    echo '<style>';
    echo '.manga-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(225px, 1fr)); gap: 20px; }'; // Style for the manga container as a responsive grid with fixed width
    echo '.manga-item { background-color: #f7f7f7; padding: 10px; border-radius: 5px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.1); text-align: center; }'; // Style for each manga item
    echo '.manga-thumbnail img { max-width: 225px; max-height: 320px; }'; // Style for the manga thumbnail image, fixed width and height
    echo '.manga-item a { text-decoration: none; color: #333; font-weight: bold; border-bottom: 1px solid #ddd; }'; // Style for the clickable manga link with border
    echo '.manga-item a:hover { background-color: #ddd; }'; // Hover effect for manga link
    echo '.chapter-list { list-style: none; padding: 0; margin: 0; }'; // Style for the chapter list
    echo '.chapter-button { background-color: #f7f7f7; color: #333; text-decoration: none; padding: 5px 10px; border-radius: 5px; margin-top: 10px; display: inline-block; }'; // Style for chapter buttons
    echo '.chapter-button:hover { background-color: #ddd; }'; // Hover effect for chapter buttons
    echo '.more-button { background-color: #ddd; color: #333; text-decoration: none; padding: 5px 10px; border-radius: 5px; margin-top: 10px; display: block; }'; // Style for the "More" button (now displayed as a block)
    echo '.more-button:hover { background-color: #999; }'; // Hover effect for the "More" button
    echo '</style>';

    echo '<div class="manga-container">';

    // Loop through sorted mangas with chapters
    foreach ($mangas_with_chapters as $manga_with_chapters) {
        $manga_post = $manga_with_chapters['manga_post'];
        $manga_id = $manga_post->ID;
        $manga_title = get_the_title($manga_id);
        $manga_thumbnail = get_the_post_thumbnail($manga_id, array(225, 320), array('class' => 'manga-thumbnail'));

        // Display Manga as a clickable container with title, featured image, and border
        echo '<div class="manga-item">';
        echo '<a href="' . get_permalink($manga_id) . '">' . $manga_thumbnail . '<br>' . esc_html($manga_title) . '</a>';
        echo '<div class="chapter-list">';

        // Query chapters for the current manga, sorted by date to get the latest chapter
        $chapters_query = new WP_Query(array(
            'post_type' => 'chapters',
            'order' => 'DESC',
            'numberposts' => 3, // Display up to 3 chapters per Manga
            'meta_query' => array(
                array(
                    'key' => 'manga', // Custom field key for chapters relationship
                    'value' => '"' . $manga_id . '"', // Matches exactly the manga ID
                    'compare' => 'LIKE'
                )
            )
        ));

        $displayed_chapters = 0;

        while ($chapters_query->have_posts() && $displayed_chapters < 3) :
            $chapters_query->the_post();
            $chapter_number = preg_replace('/\D/', '', get_the_title()); // Extract numeric part from title
            $chapter_title = 'Chapter ' . $chapter_number;
            $chapter_date = get_the_date('F j, Y');

            // Display chapter as a button with title and date
            echo '<a href="' . get_permalink() . '" class="chapter-button">' . esc_html($chapter_title) . ' - ' . esc_html($chapter_date) . '</a>';

            $displayed_chapters++;
        endwhile;

        if ($chapters_query->found_posts > 3) {
            // If there are more than 3 chapters, show the "More" button
            echo '<a href="' . get_permalink($manga_id) . '" class="more-button">More</a>';
        }

        echo '</div>'; // Close the chapter-list container
        echo '</div>'; // Close the manga-item container
    }

    echo '</div>'; // Close the manga container
    wp_reset_postdata();
else :
    echo 'No Manga with chapters found.';
endif;
?>


        <?php get_sidebar(); ?>
    </div><!-- end primary -->
</div><!-- end wrap -->
<?php get_footer(); ?>

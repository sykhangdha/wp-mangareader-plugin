<?php
/*
Template Name: Reader Page Example
*/

get_header();
?>

<style>
    <!-- Your CSS styles here -->
</style>

<script>
    // JavaScript to toggle recent posts visibility
    function toggleRecentPosts(categoryId) {
        var postsContainer = document.getElementById('category-posts-' + categoryId);
        if (postsContainer.style.display === 'none') {
            postsContainer.style.display = 'block';
        } else {
            postsContainer.style.display = 'none';
        }
    }
</script>

<div id="az-list">
    <?php
    $categories = get_terms('category'); // Get all categories

    // Initialize an array to store the categories by the first letter
    $category_by_letter = array();

    foreach ($categories as $category) {
        if (in_array($category->term_id, [1, 14])) {
            continue; // Skip Category ID, you can either remove this part of the code OR change the ID numbers
        }

        $first_letter = strtoupper(substr($category->name, 0, 1));
        $category_by_letter[$first_letter][] = $category; // Group categories by the first letter
    }

    // Sort the category by letter array
    ksort($category_by_letter);

    // Display the A-Z letters
    foreach (range('A', 'Z') as $letter) {
        if (isset($category_by_letter[$letter])) {
            echo '<a href="#' . $letter . '">' . $letter . '</a>';
        } else {
            echo '<span>' . $letter . '</span>';
        }
    }
    ?>
</div>

<div class="info-box">
    Click on manga name to show chapters!
</div>

<div class="all-categories-posts">
    <?php
    foreach (range('A', 'Z') as $letter) {
        if (isset($category_by_letter[$letter])) {
            echo '<h2 id="' . $letter . '">' . $letter . '</h2>';
            foreach ($category_by_letter[$letter] as $category) {
                $args = array(
                    'post_type' => 'post',
                    'cat' => $category->term_id,
                    'posts_per_page' => 3,
                );

                $query = new WP_Query($args);

                if ($query->have_posts()) :
                    echo '<div class="category-posts">';
                    echo '<div class="category-header">';
                    echo '<span class="post-title" onclick="toggleRecentPosts(' . $category->term_id . ')">' . $category->name . '</span>';

                    // Get the date of the most recently updated post from this category
                    $recent_post_date = get_the_modified_date('', $query->posts[0]);

                    // Display the recent post date below the category title
                    echo '<div class="category-post-date">Last Updated: ' . $recent_post_date . '</div>';

                    echo '</div>';
                    echo '<div id="category-posts-' . $category->term_id . '" style="display:none;" class="grid-container">';
                    $post_count = 0; // Initialize a post count
                    foreach ($query->posts as $post) :
                        $post_count++;
                        if ($post_count <= 3) { // Display only the first 3 posts
                        ?>
                            <div class="grid-item">
                                <a href="<?php echo get_permalink($post->ID); ?>" class="post-title"><?php echo $post->post_title; ?></a>
                                <div class="post-date"><?php echo get_the_date('', $post); ?></div>
                            </div>
                        <?php
                        }
                    endforeach;

                    // Add a "More Chapters" button that links to the category archive
                    echo '<div class="more-chapters">';
                    echo '<a href="' . get_category_link($category->term_id) . '">More Chapters</a>';
                    echo '</div>';

                    echo '</div>'; // Close the grid-container
                    echo '</div>'; // Close the category-posts
                endif;
                wp_reset_postdata();
            }
        }
    }
    ?>
</div>

<?php get_footer(); ?>

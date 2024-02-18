<?php
/*
Template Name: Reader Page Example
*/
?>

<style>
    /* Style for the A-Z listing */
    #az-list {
        margin-top: 20px;
        text-align: center;
    }

    #az-list a {
        text-decoration: none;
        margin: 0 5px;
        color: blue;
    }

    /* Style for the grid container */
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); /* Responsive 3-column layout */
        gap: 20px;
        justify-content: center;
        padding: 20px;
        max-width: 1200px; /* Set a maximum width for the grid */
        margin: 0 auto; /* Center the grid horizontally */
    }

    /* Style for each grid item */
    .grid-item {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: center;
    }

    /* Style for the post title */
    .post-title {
        font-size: 18px;
        font-weight: bold;
        cursor: pointer; /* Add cursor pointer for clickable titles */
    }

    /* Style for category headers */
    .category-header {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    /* Style for the post date */
    .category-post-date {
        font-size: 14px;
        font-weight: normal;
        margin-top: 5px;
    }

    /* Style for the "More Chapters" button */
    .more-chapters {
        text-align: center;
        margin-top: 10px;
    }

    /* Style for the rounded light gray box */
    .info-box {
        background-color: #f0f0f0;
        border-radius: 10px;
        padding: 10px;
        text-align: center;
        margin-bottom: 20px;
    }

    /* Style for the search container */
    #search-container {
        text-align: center;
        margin-bottom: 20px;
    }

    /* Style for the search input */
    #category-search {
        padding: 5px;
        width: 100%;
        max-width: 300px;
    }

    /* Style for the clickable text container */
    .sort-links-container {
        text-align: center;
        margin-top: 10px;
    }

    /* Style for the clickable text */
    .sort-links {
        cursor: pointer;
        text-decoration: underline;
        margin: 0 10px; /* Adjust the margin as needed */
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var defaultCategoryOrder;

    function toggleRecentPosts(categoryId) {
        var postsContainer = document.getElementById('category-posts-' + categoryId);
        postsContainer.style.display = (postsContainer.style.display === 'none') ? 'block' : 'none';
    }

    function filterCategories() {
        var searchInput = document.getElementById('category-search').value.trim().toUpperCase();
        var categoryHeaders = document.querySelectorAll('.category-header');

        categoryHeaders.forEach(function (categoryHeader) {
            var categoryTitles = categoryHeader.querySelectorAll('.post-title');
            var categoryContainer = categoryHeader.parentElement;

            // Check if any category name within this category matches the search term
            var showCategory = Array.from(categoryTitles).some(function (categoryTitle) {
                var categoryName = categoryTitle.textContent.trim().toUpperCase();
                return categoryName.includes(searchInput);
            });

            // Show or hide the entire category based on the search result
            categoryContainer.style.display = showCategory ? 'block' : 'none';
        });
    }

    function sortCategoriesAZ() {
        var categoryContainers = document.querySelectorAll('.category-posts');
        var sortedCategoryArray = Array.from(categoryContainers).sort(function (a, b) {
            var titleA = a.querySelector('.post-title').textContent.toUpperCase();
            var titleB = b.querySelector('.post-title').textContent.toUpperCase();
            return titleA.localeCompare(titleB);
        });

        var allCategoriesPosts = document.querySelector('.all-categories-posts');
        allCategoriesPosts.innerHTML = '';
        sortedCategoryArray.forEach(function (category) {
            allCategoriesPosts.appendChild(category);
        });
    }

    function sortCategoriesByLatest() {
        var categoryContainers = document.querySelectorAll('.category-posts');
        var sortedCategoryArray = Array.from(categoryContainers).sort(function (a, b) {
            var dateA = new Date(a.getAttribute('data-latest-post-date'));
            var dateB = new Date(b.getAttribute('data-latest-post-date'));
            return dateB - dateA;
        });

        var allCategoriesPosts = document.querySelector('.all-categories-posts');
        allCategoriesPosts.innerHTML = '';
        sortedCategoryArray.forEach(function (category) {
            allCategoriesPosts.appendChild(category);
        });
    }

    document.querySelector('.sort-links-az').addEventListener('click', function () {
        sortCategoriesAZ();
        filterCategories();
    });

    document.querySelector('.sort-links-latest').addEventListener('click', function () {
        sortCategoriesByLatest();
        filterCategories();
    });

    document.querySelectorAll('.post-title').forEach(function (title) {
        title.addEventListener('click', function () {
            var categoryId = title.getAttribute('data-category-id');
            toggleRecentPosts(categoryId);
        });
    });

    sortCategoriesByLatest();

    document.getElementById('category-search').addEventListener('input', function () {
        filterCategories();
    });
});



</script>

<div id="search-container">
    <input type="text" id="category-search" placeholder="Type to search for manga..." onkeyup="filterCategories()">
</div>

<div class="sort-links-container">
    <span class="sort-links sort-links-az">Sort A-Z</span>
    <span class="sort-links sort-links-latest">Sort by latest update</span>
</div>

<div id="az-list">
    <?php
    $categories = get_terms('category');

    $category_by_letter = array();

    foreach ($categories as $category) {
        if (in_array($category->term_id, array(1))) {
            continue;
        }

        $first_letter = strtoupper(substr($category->name, 0, 1));
        $category_by_letter[$first_letter][] = $category;
    }

    ksort($category_by_letter);

    foreach (range('A', 'Z') as $letter) {
        echo isset($category_by_letter[$letter]) ? '<a href="#' . $letter . '">' . $letter . '</a>' : '<span>' . $letter . '</span>';
    }
    ?>
</div>

<div class="info-box">
    Click on manga name to show chapters! Refresh the reader if the manga list is not sorting properly!
</div>

<div class="all-categories-posts">
    <?php
    $lastDisplayedLetter = '';

    foreach (range('A', 'Z') as $letter) {
        if (isset($category_by_letter[$letter])) {
            foreach ($category_by_letter[$letter] as $category) {
                $args = array(
                    'post_type' => 'post',
                    'cat' => $category->term_id,
                    'posts_per_page' => 3,
                );

                $query = new WP_Query($args);

                if ($query->have_posts()) :
                    if ($lastDisplayedLetter !== $letter) {
                        echo '<div class="category-posts" data-latest-post-date="' . get_the_modified_date('', $query->posts[0]) . '">';
                        echo '<div class="category-header">';
                        echo '<h2 id="' . $letter . '">' . $letter . '</h2>';
                        echo '</div>';
                        $lastDisplayedLetter = $letter;
                    }

                    echo '<div class="category-header">';
                    echo '<span class="post-title" data-category-id="' . $category->term_id . '">' . $category->name . '</span>';

                    $recent_post_date = get_the_modified_date('', $query->posts[0]);

                    echo '<div class="category-post-date">Last Updated: ' . $recent_post_date . '</div>';
                    echo '</div>';

                    echo '<div id="category-posts-' . $category->term_id . '" style="display:none;" class="grid-container">';
                    $post_count = 0;

                    foreach ($query->posts as $post) :
                        $post_count++;
                        if ($post_count <= 3) {
                            ?>
                            <div class="grid-item">
                                <a href="<?php echo get_permalink($post->ID); ?>" class="post-title"><?php echo $post->post_title; ?></a>
                                <div class="post-date"><?php echo get_the_date('', $post); ?></div>
                            </div>
                        <?php
                        }
                    endforeach;

                    echo '<div class="more-chapters">';
                    echo '<a href="' . get_category_link($category->term_id) . '">More Chapters</a>';
                    echo '</div>';

                    echo '</div>';
                    echo '</div>';
                endif;
                wp_reset_postdata();
            }
        }
    }
    ?>
</div>

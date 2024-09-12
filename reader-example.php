<?php
/*
Template Name: Reader Page Example - Bootstrap 4 addition
*/
?>

<style>
    /* A-Z Listing */
    #az-list {
        margin-top: 20px;
        text-align: center;
    }

    #az-list a {
        text-decoration: none;
        margin: 0 5px;
        color: blue;
    }

    /* Style for the grid container using Bootstrap */
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); /* Responsive grid */
        gap: 20px;
        padding: 20px;
    }

    .grid-item {
        border: 1px solid #dee2e6; /* Bootstrap border color */
        padding: 10px;
        text-align: center;
        border-radius: 0.25rem;
        background-color: #f8f9fa;
    }

    .post-title {
        font-size: 1.125rem; /* Bootstrap font size */
        font-weight: 700;
        cursor: pointer;
        color: #007bff;
        text-decoration: none;
    }

    .post-title:hover {
        text-decoration: underline;
    }

    .category-header {
        font-size: 1.25rem;
        font-weight: bold;
        margin-bottom: 1rem;
        color: #343a40;
    }

    .category-post-date {
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    .info-box {
        background-color: #e9ecef;
        padding: 1rem;
        border-radius: 0.375rem;
        margin-bottom: 2rem;
        text-align: center;
    }

    #search-container {
        margin-bottom: 20px;
    }

    #category-search {
        padding: 0.5rem;
        width: 100%;
        max-width: 300px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .sort-links {
        cursor: pointer;
        margin: 0 10px;
        color: #007bff;
        text-decoration: underline;
    }

    .sort-links:hover {
        text-decoration: none;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
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

            var showCategory = Array.from(categoryTitles).some(function (categoryTitle) {
                var categoryName = categoryTitle.textContent.trim().toUpperCase();
                return categoryName.includes(searchInput);
            });

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

<div id="search-container" class="text-center">
    <input type="text" id="category-search" placeholder="Type to search for manga..." onkeyup="filterCategories()">
</div>

<div class="sort-links-container text-center">
    <span class="sort-links sort-links-az">Sort A-Z</span>
    <span class="sort-links sort-links-latest">Sort by latest update</span>
</div>

<div id="az-list">
    <?php
    $categories = get_terms('category');
    $category_by_letter = array();

    foreach ($categories as $category) {
        if (in_array($category->term_id, array(1, 7))) {
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

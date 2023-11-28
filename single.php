//Example code the main thing you want to add to your next_post_link is the 'true' logic for the same category
 <?php
        while (have_posts()) :
            the_post();

            get_template_part('template-parts/content', get_post_type());

            // Post navigation from the same category
            previous_post_link('%link', __('Previous Chapter', 'text-domain'), true, '', 'category');
            next_post_link('%link', __('Next Chapter', 'text-domain'), true, '', 'category');

            // If comments are open or we have at least one comment, load up the comment template.
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;

        endwhile; // End of the loop.
        ?>

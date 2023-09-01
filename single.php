//Example code the main thing you want to add to your next_post_link is the 'true' logic for the same category

<nav class="next"><?php next_post_link('%link', '<strong>'.esc_html__('').'</strong> <span>%title</span>' , true); ?></nav>
<nav class="previous"><?php previous_post_link('%link', '<strong>'.esc_html__('').'</strong> <span>%title</span>' , true); ?></nav>

<?php
/**
 * Comments.
 *
 * @package Websource\Theme
 */

defined('ABSPATH') || exit;

if (post_password_required()) {
    return;
}
?>
<section id="comments" class="wsx-comments">
  <?php if (have_comments()) : ?>
    <h2 class="wsx-comments__title">
        <?php
        echo esc_html(sprintf(/* translators: %d: number of comments */ _n('%d comment', '%d comments', (int) get_comments_number(), 'websource'), (int) get_comments_number()));
        ?>
    </h2>
    <ol class="wsx-comments__list">
        <?php wp_list_comments(['style' => 'ol', 'short_ping' => true, 'avatar_size' => 44]); ?>
    </ol>
        <?php the_comments_pagination(); ?>
  <?php endif; ?>
  <?php comment_form(['class_submit' => 'wsx-btn wsx-btn--primary', 'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title wsx-comments__title">', 'title_reply_after' => '</h2>']); ?>
</section>

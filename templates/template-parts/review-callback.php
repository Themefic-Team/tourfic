<<?php echo esc_attr($tag) . ' '; ?><?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?> id="comment-<?php comment_ID(); ?>">
    <div class="comment-body">
        <?php if ($args['avatar_size'] != 0) {
            echo get_avatar($comment, $args['avatar_size']);
        } ?>
        <div class="tf-author-infobox">
            <div class="comment-meta commentmetadata">
                <div class="comment-author vcard">
                    <?php printf(wp_kses_post('<cite class="fn">%s</cite>', TF_NAME), get_comment_author_link()); ?>
                </div>
                <?php if ('0' === $comment->comment_approved) { ?>
                    <em class="comment-awaiting-moderation"><?php esc_html_e('Your review is awaiting moderation.', TF_NAME); ?></em>
                    <br />
                <?php } ?>
                <a href="<?php echo esc_url(htmlspecialchars(get_comment_link($comment->comment_ID))); ?>" class="comment-date">
                    <?php echo '<time datetime="' . esc_attr(get_comment_date('c')) . '">' . esc_html(get_comment_date()) . '</time>'; ?>
                </a>
                <p><?php echo $tf_overall_rate ?>/<?php echo $base_rate ?? '5' ?></p>
            </div>
        </div>
        <?php if ('div' !== $args['style']) { ?>
            <div id="div-comment-<?php comment_ID(); ?>" class="comment-content">
            <?php } ?>
            <div class="comment-text">
                <?php comment_text(); ?>
            </div>
            <div class="reply">
                <?php
                comment_reply_link(
                    array_merge(
                        $args,
                        [
                            'add_below' => $add_below,
                            'depth'     => $depth,
                            'max_depth' => $args['max_depth'],
                        ]
                    )
                );
                ?>
                <?php edit_comment_link(__('Edit', TF_NAME), '  ', ''); ?>
            </div>
            </div>
            <?php if ('div' !== $args['style']) { ?>
    </div>
<?php } ?>

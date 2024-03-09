<div class="tf-wishlists">
    <table class="table" data-type='<?php echo esc_attr($type) ?>'>
        <thead>
        <tr>
            <th><?php esc_html_e( 'Your Wishlist', 'tourfic' ) ?></th>
        </tr>
        </thead>
        <tbody>
		<?php
		foreach ( $ids as $key => $id ) :
			$post = get_post( $id );
			if ( $post ):
				?>
                <tr>
                    <td>
                        <a href="<?php the_permalink( $post->ID ) ?>" target="_blank"><?php echo esc_html($post->post_title) ?></a>
                        <i title="<?php esc_attr_e( 'Remove from Wishlist', 'tourfic' ); ?>" class="fas fa-trash remove-wishlist" data-id="<?php echo esc_attr($post->ID) ?>"
                           data-nonce="<?php echo esc_attr(wp_create_nonce( "wishlist-nonce" )) ?>"></i>
                    </td>
                </tr>
			<?php endif;
		endforeach; ?>
        </tbody>
    </table>
</div>

<div class="tf-wishlists">
    <table class="table" data-type='<?php echo $type ?>'>
        <thead>
        <tr>
            <th><?php _e( 'Your Wishlist', 'tourfic' ) ?></th>
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
                        <a href="<?php echo get_post_permalink( $post->ID ) ?>" target="_blank"><?php echo $post->post_title ?></a>
                        <i title="<?php esc_attr_e( 'Remove from Wishlist', 'tourfic' ); ?>" class="fas fa-trash remove-wishlist" data-id="<?php echo $post->ID ?>"
                           data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>"></i>
                    </td>
                </tr>
			<?php endif;
		endforeach; ?>
        </tbody>
    </table>
</div>

<div class="tf-wishlists">
    <table class="table" data-type='<?php echo $type  ?>'>
        <thead>
            <tr>
                <th>Your Wishlist</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($ids as $key => $id) {
                $post = get_post($id);
            ?>
                <tr>
                    <td><a href="<?php echo get_post_permalink($post->ID)  ?>" target="_blank"><?php echo $post->post_title ?></a><i title="Remove from Wishlist" class="fas fa-trash remove-wishlist" data-id="<?php echo $post->ID ?>" data-nonce="<?php echo wp_create_nonce("wishlist-nonce") ?>"></i></td>
                </tr>
            <?php };  ?>
        </tbody>
    </table>
</div>

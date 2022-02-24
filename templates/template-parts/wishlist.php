<div class="tf-wishlists">
    <table class="table" data-type='<?php echo $type  ?>'>
        <thead>
            <tr>
                <th></th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($ids as $key => $id) {
                $post = get_post($id);
            ?>
                <tr>
                    <td scope="row" class="tf-text-center"><i class="fas fa-trash remove-wishlist" data-id="<?php echo $post->ID ?>" data-nonce="<?php echo wp_create_nonce("wishlist-nonce") ?>"></i></td>
                    <td><a href="<?php echo get_post_permalink($post->ID)  ?>" target="_blank"><?php echo $post->post_title ?></a></td>
                </tr>
            <?php };  ?>
        </tbody>
    </table>
</div>

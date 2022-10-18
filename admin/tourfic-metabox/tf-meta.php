<?php


// Tourfic Classes Functions
if ( file_exists( TF_ADMIN_PATH . 'tourfic-metabox/classes/field.php' ) ) {
    require_once TF_ADMIN_PATH . 'tourfic-metabox/classes/field.php';
} else {
    tf_file_missing( TF_ADMIN_PATH . 'tourfic-metabox/classes/field.php' );
}


abstract class WPOrg_Meta_Box {


    /**
     * Set up and add the meta box.
     */
    public static function add() {
        $screens = [ 'page' ];
        foreach ( $screens as $screen ) {
            add_meta_box(
                'wporg_box_id',          // Unique ID
                'Custom Meta Box Title', // Box title
                [ self::class, 'html' ],   // Content callback, must be of type callable
                $screen                  // Post type
            );
        }
    }


    /**
     * Save the meta box selections.
     *
     * @param int $post_id  The post ID.
    */
    public static function save( int $post_id ) {
        // echo "<pre>";
        // $json_data = json_encode($_POST['tf_page']);
        // var_dump($json_data);
        // exit();
        if ( array_key_exists( 'wporg_field', $_POST ) ) {
            update_post_meta(
                $post_id,
                '_wporg_meta_key',
                $_POST['wporg_field']
            );
        }
    }


    /**
     * Display the meta box HTML to the user.
     *
     * @param \WP_Post $post   Post object.
     */
    public static function html( $post ) {
        ?>
        <?php 
        $test_prefix = 'tf_page';

        Field::rander( $post->ID, $test_prefix ,array(
            'title'  => 'Test Meta Detail',
            'fields' => array(
                array(
                    'id' => 'name',
                    'type' => 'text',
                    'placeholder' => 'Enter Your Name',
                    'title' => 'Name'
                ),
                array(
                    'id' => 'size',
                    'type' => 'number',
                    'placeholder' => 'Enter Your Size',
                    'title' => 'Size'
                ),
                array(
                    'id' => 'email',
                    'type' => 'email',
                    'placeholder' => 'Enter Your Email',
                    'title' => 'Email'
                )
            )
        )
        
        );

        ?>
        <?php
    }
}

add_action( 'add_meta_boxes', [ 'WPOrg_Meta_Box', 'add' ] );
add_action( 'save_post', [ 'WPOrg_Meta_Box', 'save' ] );
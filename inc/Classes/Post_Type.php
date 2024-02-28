<?php
namespace Tourfic\Classes;

class Post_Type
{
    use \Tourfic\Traits\Singleton;
    protected $post_args;
    protected $tax_args;

    public function __construct($post_args = array(), $tax_args = array())
    {
        $this->post_args = $post_args;
        $this->tax_args = $tax_args;

        // Add form.
        add_action('init', array($this, 'tf_post_type'));

    }

    public function tf_post_type()
    {
        $post_args = $this->post_args;
        $labels = array(
            'name' => __($post_args['name'], 'tourfic'),
            'singular_name' => __($post_args['singular_name'], 'tourfic'),
            'add_new' => __('Add New ' . $post_args['singular_name'], 'tourfic'),
            'add_new_item' => __('Add New', 'tourfic'),
            'edit_item' => __('Edit ' . $post_args['singular_name'], 'tourfic'),
            'new_item' => __('New ' . $post_args['singular_name'], 'tourfic'),
            'view_item' => __('View ' . $post_args['singular_name'], 'tourfic'),
            'search_items' => __('Search ' . $post_args['singular_name'], 'tourfic'),
            'not_found' => __('No ' . $post_args['singular_name'] . ' found', 'tourfic'),
            'not_found_in_trash' => __('No ' . $post_args['singular_name'] . ' found in Trash', 'tourfic'),
            'parent_item_colon' => '',
        );

        $labels = apply_filters('tf_post_type_labels_' . $post_args['slug'], $labels);

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'has_archive' => true,
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'hierarchical' => true,
            'menu_icon' => $post_args['menu_icon'],
            'menu_position' => $post_args['menu_position'],
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'show_in_rest' => true,
            'supports' => $post_args['supports'],
        );

        $args = apply_filters('tf_post_type_args_' . $post_args['slug'], $args);

        register_post_type($post_args['slug'], $args);
    }

    public function tf_cpt_taxonomy()
    {

        foreach ($this->tax_args as $tax_args) {
            $tf_cpt_tax_labels = array(
                'name' => __($tax_args['name'], 'tourfic'),
                'add_new_item' => __('Add New ' . $tax_args['singular_name'], 'tourfic'),
                'new_item_name' => __('New ' . $tax_args['singular_name'], 'tourfic'),
            );
            $tf_cpt_tax_labels = apply_filters('tf_cpt_tax_labels_' . $tax_args['taxonomy'], $tf_cpt_tax_labels);

            $post_type = apply_filters('tf_cpt_tax_post_type_filter_' . $tax_args['taxonomy'], array($this->post_args['slug']));

            $args = array(
                'labels' => $tf_cpt_tax_labels,
                'hierarchical' => true,
                'query_var' => true,
                'show_in_rest' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => true,
                'rest_base' => $tax_args['taxonomy'],
                'rest_controller_class' => 'WP_REST_Terms_Controller',
            );
            $args = apply_filters('tf_cpt_tax_args_filter_' . $tax_args['taxonomy'], $args);

            register_taxonomy($tax_args['taxonomy'], $post_type, $args); // Register Custom Taxonomy
        }
    }
}

<?php

namespace Tourfic\App\Templates;

// don't load directly
defined( 'ABSPATH' ) || exit;

class Template_Builder {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('init', array($this, 'tf_template_builder_post_type'));
		add_filter('post_row_actions', array($this, 'tf_template_post_row_actions'), 20, 2);
		add_filter('manage_tf_template_builder_posts_columns', array($this, 'tf_template_set_columns'));
		add_action('manage_tf_template_builder_posts_custom_column', array($this, 'tf_template_render_column'), 10, 2);
        add_action('admin_footer', array($this, 'tf_template_builder_add_popup_html'), 9);
        add_action('wp_ajax_tf_get_template_data', array($this, 'tf_get_template_data_callback'));
        add_action('wp_ajax_tf_save_template_builder', array($this, 'tf_save_template_builder_callback'));
	}

	public function admin_menu() {
		add_submenu_page(
            'tf_settings',
			esc_html__('Template Builder', 'tourfic'),
			esc_html__('Template Builder', 'tourfic'),
			'manage_options',
			'edit.php?post_type=tf_template_builder',
		);
	}

	public function tf_template_builder_post_type() {
		$labels = [
			'name'                  => esc_html_x('Template Builder', 'Post Type General Name', 'tourfic'),
			'singular_name'         => esc_html_x('Template Builder', 'Post Type Singular Name', 'tourfic'),
			'menu_name'             => esc_html__('Template Builder', 'tourfic'),
			'name_admin_bar'        => esc_html__('Template Builder', 'tourfic'),
			'archives'              => esc_html__('Template Archives', 'tourfic'),
			'attributes'            => esc_html__('Template Attributes', 'tourfic'),
			'parent_item_colon'     => esc_html__('Parent Item:', 'tourfic'),
			'all_items'             => esc_html__('Templates', 'tourfic'),
			'add_new_item'          => esc_html__('Add New Template', 'tourfic'),
			'add_new'               => esc_html__('Add New', 'tourfic'),
			'new_item'              => esc_html__('New Template', 'tourfic'),
			'edit_item'             => esc_html__('Edit Template', 'tourfic'),
			'update_item'           => esc_html__('Update Template', 'tourfic'),
			'view_item'             => esc_html__('View Template', 'tourfic'),
			'view_items'            => esc_html__('View Templates', 'tourfic'),
			'search_items'          => esc_html__('Search Templates', 'tourfic'),
			'not_found'             => esc_html__('Not found', 'tourfic'),
			'not_found_in_trash'    => esc_html__('Not found in Trash', 'tourfic'),
			'featured_image'        => esc_html__('Featured Image', 'tourfic'),
			'set_featured_image'    => esc_html__('Set featured image', 'tourfic'),
			'remove_featured_image' => esc_html__('Remove featured image', 'tourfic'),
			'use_featured_image'    => esc_html__('Use as featured image', 'tourfic'),
			'insert_into_item'      => esc_html__('Insert into Template', 'tourfic'),
			'uploaded_to_this_item' => esc_html__('Uploaded to this Template', 'tourfic'),
			'items_list'            => esc_html__('Templates list', 'tourfic'),
			'items_list_navigation' => esc_html__('Templates list navigation', 'tourfic'),
			'filter_items_list'     => esc_html__('Filter from list', 'tourfic'),
		];

		$rewrite = [
			'slug'       => 'tf_template_builder',
			'with_front' => true,
			'pages'      => false,
			'feeds'      => false,
		];

		$args = [
			'label'               => esc_html__('Template Builder', 'tourfic'),
			'description'         => esc_html__('Tourfic Template Builder', 'tourfic'),
			'labels'              => $labels,
			'supports'            => ['title', 'editor', 'elementor', 'permalink'],
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'rewrite'             => $rewrite,
			'query_var'           => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'show_in_rest'        => true,
			'rest_base'           => 'tf_template_builder',
		];

		register_post_type('tf_template_builder', $args);

        // Add this to ensure Elementor support
        add_post_type_support('tf_template_builder', 'elementor');
	}

	public function tf_template_post_row_actions($actions, $post) {

        if ($post->post_type === 'tf_template_builder') {
            unset($actions['inline hide-if-no-js']); // Removes "Quick Edit"
            unset($actions['view']); // Removes "View"
        }

		if($post->post_type === 'tf_template_builder' && isset($actions['edit'])){
			$actions['edit'] = sprintf(
				'<a title="%1$s" aria-label="%1$s" data-url="%2$s" href="#">%1$s</a>',
				esc_html__('Edit', 'tourfic'),
				$this->get_edit_url($post->ID)
			);
		}

		return $actions;
	}

	public function tf_template_set_columns($columns) {
		$date_column   = $columns['date'];
		$author_column = $columns['author'];

		unset($columns['date']);
		unset($columns['author']);

		$columns['service']    = esc_html__('Service', 'tourfic');
		$columns['type']    = esc_html__('Type', 'tourfic');
		$columns['status']  = esc_html__('Status', 'tourfic');
		$columns['author']  = esc_html__('Author', 'tourfic');
		$columns['date']    = esc_html($date_column);

		return $columns;
	}

	public function tf_template_render_column($column, $post_id) {
        $service = get_post_meta($post_id, 'tf_template_service', true);
        $template_type = get_post_meta($post_id, 'tf_template_type', true);
        $status = get_post_meta($post_id, 'tf_template_active', true);
        $service_label = [
            'tf_hotel' => esc_html__('Hotel', 'tourfic'),
            'tf_tours' => esc_html__('Tour', 'tourfic'),
            'tf_apartment' => esc_html__('Apartment', 'tourfic'),
            'tf_carrental' => esc_html__('Car Rental', 'tourfic'),
        ];

		switch($column) {
			case 'service':
				echo esc_html(empty($service) ?  '' : $service_label[$service]);
				break;

			case 'type':
				echo esc_html(empty($template_type) ?  '' : ucfirst($template_type));
				break;

			case 'status':
                $status_class = $status ? 'active' : 'inactive';
				echo wp_kses('<span class="tf-template-status ' . $status_class . '">' . ($status ? esc_html__('Active', 'tourfic') : esc_html__('Inactive', 'tourfic')) . '</span>', ['span' => ['class' => []]]);
				break;
		}
	}

	public function get_edit_url($post_id) {

		$url = add_query_arg(
			[
				'post'   => $post_id,
				'action' => 'edit',
			],
			admin_url('post.php')
		);

		$url = apply_filters('tf_template_builder_url_edit', $url, $post_id, $this);

		return $url;
	}

    function tf_template_builder_add_popup_html() {
        global $pagenow, $post_type;
        
        if (($pagenow == 'edit.php' && $post_type == 'tf_template_builder') || 
            ($pagenow == 'post.php' && $post_type == 'tf_template_builder')) {
            ?>
            <div class="tf-modal tf-modal-small" id="tf-template-builder-popup">
                <div class="tf-modal-dialog">
                    <div class="tf-modal-content">
                        <div class="tf-modal-header">
                            <h2><?php echo esc_html__('Build Your Template', 'tourfic'); ?></h2>
                            <a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
                        </div>
                        <div class="tf-modal-body">
                            <form id="tf-template-builder-form">
                                <input type="hidden" name="post_id" id="tf-post-id" value="0">
                                <input type="hidden" name="action" value="tf_save_template_builder">
                                
                                <div class="tf-field tf-field-text">
                                    <label for="tf-template-name" class="tf-field-label"><?php echo esc_html__('Name', 'tourfic'); ?></label>
                                    <div class="tf-fieldset">
                                        <input type="text" name="template_name" id="tf-template-name" placeholder="<?php echo esc_attr__('Enter template name', 'tourfic') ?>" required>
                                    </div>
                                </div>

                                <div class="tf-field tf-field-select">
                                    <label for="tf-template-service" class="tf-field-label"><?php echo esc_html__('Service', 'tourfic'); ?></label>
                                    <div class="tf-fieldset">
                                        <select name="tf_template_service" id="tf-template-service" class="tf-select">
                                            <option value="tf_hotel"><?php echo esc_html__('Hotel', 'tourfic'); ?></option>
                                            <option value="tf_tours"><?php echo esc_html__('Tour', 'tourfic'); ?></option>
                                            <option value="tf_apartment"><?php echo esc_html__('Apartment', 'tourfic'); ?></option>
                                            <option value="tf_carrental"><?php echo esc_html__('Car Rental', 'tourfic'); ?></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="tf-field tf-field-select">
                                    <label for="tf-template-type" class="tf-field-label"><?php echo esc_html__('Type', 'tourfic'); ?></label>
                                    <div class="tf-fieldset">
                                        <select name="tf_template_type" id="tf-template-type" class="tf-select">
                                            <option value="archive"><?php echo esc_html__('Archive', 'tourfic'); ?></option>
                                            <option value="single"><?php echo esc_html__('Single', 'tourfic'); ?></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="tf-field tf-field-switch">
			                        <label for="tf-template-active" class="tf-field-label"><?php echo esc_html__('Active', 'tourfic'); ?></label>
                                    <div class="tf-fieldset">
                                        <label for="tf-template-active" class="tf-switch-label">
                                            <input type="checkbox" id="tf-template-active" name="tf_template_active" value="" class="tf-switch">
                                            <span class="tf-switch-slider">
                                                <span class="tf-switch-on"><?php echo esc_html__('Yes', 'tourfic'); ?></span>
                                                <span class="tf-switch-off"><?php echo esc_html__('No', 'tourfic'); ?></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="tf-field tf-field-imageselect">
                                    <label class="tf-field-label"><?php echo esc_html__('Select Archive & Search Result Template', 'tourfic'); ?></label>
                                    <div class="tf-fieldset">
                                        <ul class="tf-image-radio-group">
                                            <li class="">
                                                <label class="tf-image-checkbox">
                                                    <input type="radio" name="tf_archive_template" value="blank" checked>
                                                    <div class="tf-template-blank"></div>
                                                    <span class="tf-circle-check"></span>
                                                </label>
                                                <span class="tf-image-checkbox-footer">
                                                    <span class="tf-template-title"><?php echo esc_html__('Blank', 'tourfic'); ?></span>
                                                </span>
                                            </li>
                                            <li class="">
                                                <label class="tf-image-checkbox">
                                                    <input type="radio" name="tf_archive_template" value="design-1">
                                                    <img src="<?php echo esc_url(TF_ASSETS_ADMIN_URL . "images/template/hotel-archive-design1.jpg"); ?>" alt="Design 1">
                                                    <span class="tf-circle-check"></span>
                                                </label>
                                                <span class="tf-image-checkbox-footer">
                                                    <span class="tf-template-title"><?php echo esc_html__('Design 1', 'tourfic'); ?></span>
                                                </span>
                                            </li>
                                            <li class="">
                                                <label class="tf-image-checkbox">
                                                    <input type="radio" name="tf_archive_template" value="design-2">
                                                    <img src="<?php echo esc_url(TF_ASSETS_ADMIN_URL . "images/template/hotel-archive-design2.jpg"); ?>" alt="Design 2">
                                                    <span class="tf-circle-check"></span>
                                                </label>
                                                <span class="tf-image-checkbox-footer">
                                                    <span class="tf-template-title"><?php echo esc_html__('Design 2', 'tourfic'); ?></span>
                                                </span>
                                            </li>
                                            <li class="">
                                                <label class="tf-image-checkbox">
                                                    <input type="radio" name="tf_archive_template" value="design-3">
                                                    <img src="<?php echo esc_url(TF_ASSETS_ADMIN_URL . "images/template/hotel-archive-design3.jpg"); ?>" alt="Design 3">
                                                    <span class="tf-circle-check"></span>
                                                </label>
                                                <span class="tf-image-checkbox-footer">
                                                    <span class="tf-template-title"><?php echo esc_html__('Design 3', 'tourfic'); ?></span>
                                                </span>
                                            </li>
                                            <li class="">
                                                <label class="tf-image-checkbox">
                                                    <input type="radio" name="tf_archive_template" value="default">
                                                    <img src="<?php echo esc_url(TF_ASSETS_ADMIN_URL . "images/template/hotel-archive-default.jpg"); ?>" alt="Legacy">
                                                    <span class="tf-circle-check"></span>
                                                </label>
                                                <span class="tf-image-checkbox-footer">
                                                    <span class="tf-template-title"><?php echo esc_html__('Legacy', 'tourfic'); ?></span>
                                                </span>
                                            </li>
                                        </ul>            
                                    </div>
                                </div>
                                
                                <div class="tf-form-actions">
                                    <button type="submit" id="tf-save-template" class="tf-admin-btn tf-btn-secondary">
                                        <?php echo esc_html__('Save Changes', 'tourfic'); ?>
                                    </button>
                                    <button type="button" id="tf-edit-with-elementor" class="tf-admin-btn">
                                        <?php echo esc_html__('Edit With Elementor', 'tourfic'); ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Loader Image -->
            <div class="tf-template-builder-loader">
                <div class="tf-template-builder-loader-img">
                    <img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
                </div>
            </div>
            <?php
        }
    }

    // Get template data for editing
    function tf_get_template_data_callback() {
        check_ajax_referer('updates', 'nonce');
        
        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);
        
        if (!$post || $post->post_type != 'tf_template_builder') {
            wp_send_json_error();
        }
        
        $response = array(
            'ID' => $post->ID,
            'post_title' => $post->post_title,
            'tf_template_service' => get_post_meta($post->ID, 'tf_template_service', true),
            'tf_template_type' => get_post_meta($post->ID, 'tf_template_type', true),
            'tf_template_active' => get_post_meta($post->ID, 'tf_template_active', true),
            'tf_archive_template' => get_post_meta($post->ID, 'tf_archive_template', true),
        );
        
        wp_send_json_success($response);
    }

    // Save template data
    function tf_save_template_builder_callback() {
        check_ajax_referer('updates', 'nonce');
        
        $edit_with_elementor = isset($_POST['edit_with_elementor']) ? $_POST['edit_with_elementor'] : false;
        $post_id = intval($_POST['post_id']);
        $post_data = array(
            'post_title' => sanitize_text_field($_POST['template_name']),
            'post_type' => 'tf_template_builder',
            'post_status' => 'publish',
        );
        
        if ($post_id > 0) {
            $post_data['ID'] = $post_id;
            $post_id = wp_update_post($post_data);
        } else {
            $post_id = wp_insert_post($post_data);
        }
        
        if (!is_wp_error($post_id)) {
            $tf_template_type = !empty($_POST['tf_template_type']) ? sanitize_text_field($_POST['tf_template_type']) : '';
            update_post_meta($post_id, 'tf_template_service', sanitize_text_field($_POST['tf_template_service']));
            update_post_meta($post_id, 'tf_template_type', sanitize_text_field($_POST['tf_template_type']));
            update_post_meta($post_id, 'tf_template_active', isset($_POST['tf_template_active']) ? '1' : '0');
            if($tf_template_type == 'archive'){
                update_post_meta($post_id, 'tf_archive_template', sanitize_text_field($_POST['tf_archive_template']));
            } else{
                update_post_meta($post_id, 'tf_archive_template', '');
            }

            if($tf_template_type == 'single'){
                update_post_meta($post_id, 'tf_single_template', sanitize_text_field($_POST['tf_single_template']));
            } else{
                update_post_meta($post_id, 'tf_single_template', '');
            }
            
            $response = array(
                'post_id' => $post_id,
                'message' => esc_html__('Template saved successfully.', 'tourfic'),
            );
            if($edit_with_elementor){
                $response['edit_url'] = add_query_arg(array('post' => $post_id, 'action' => 'elementor'), admin_url('post.php'));
            }
            wp_send_json_success($response);
        } else {
            wp_send_json_error();
        }
    }
}
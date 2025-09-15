<?php

namespace Tourfic\App\Templates;

// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

class Template_Builder {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		// add_action('admin_menu', array($this, 'admin_menu'));
        if ( did_action( 'elementor/loaded' ) ) {
            add_action('init', array($this, 'tf_template_builder_post_type'));
            add_filter('post_row_actions', array($this, 'tf_template_post_row_actions'), 20, 2);
            add_filter('manage_tf_template_builder_posts_columns', array($this, 'tf_template_set_columns'));
            add_action('manage_tf_template_builder_posts_custom_column', array($this, 'tf_template_render_column'), 10, 2);
            add_action('admin_footer', array($this, 'tf_template_builder_add_popup_html'), 9);
            add_action('wp_ajax_tf_load_template_markup', array($this, 'tf_load_template_markup_callback'));
            add_action('wp_ajax_tf_get_template_options', array($this, 'tf_get_template_options_callback'));
            add_action('wp_ajax_tf_update_term_options', array($this, 'tf_update_term_options_callback'));
            add_action('wp_ajax_tf_save_template_builder', array($this, 'tf_save_template_builder_callback'));
            add_filter('template_include', array($this, 'tf_template_builder_custom_template'));
            add_action('save_post_tf_template_builder', [$this, 'enforce_elementor_template_on_save'], 20, 3);

            add_filter('elementor/document/urls/edit', [$this, 'modify_elementor_edit_url'], 10, 2);
            add_action('elementor/editor/init', [$this, 'setup_editor_post_data']);
        }
	}

	static function tf_template_builder_elementor_check() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Template Builder', 'tourfic'); ?></h1>
            <div class="notice notice-error" style="margin-top: 20px;">
                <p><?php esc_html_e('Please install and activate Elementor to use the Template Builder.', 'tourfic'); ?></p>

                <?php
                $plugin_slug = 'elementor/elementor.php';

                // Elementor not installed
                if ( ! file_exists( WP_PLUGIN_DIR . '/elementor/elementor.php' ) ) {
                    $install_url = wp_nonce_url(
                        self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ),
                        'install-plugin_elementor'
                    );
                    echo '<p><a href="' . esc_url( $install_url ) . '" class="button button-primary">';
                    esc_html_e( 'Install Elementor', 'tourfic' );
                    echo '</a></p>';
                }
                // Elementor installed but inactive
                elseif ( current_user_can( 'activate_plugins' ) && ! is_plugin_active( $plugin_slug ) ) {
                    $activate_url = wp_nonce_url(
                        self_admin_url( 'plugins.php?action=activate&plugin=' . $plugin_slug ),
                        'activate-plugin_' . $plugin_slug
                    );
                    echo '<p><a href="' . esc_url( $activate_url ) . '" class="button button-primary">';
                    esc_html_e( 'Activate Elementor', 'tourfic' );
                    echo '</a></p>';
                }
                ?>
            </div>
        </div>
        <?php
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
	}

	public function tf_template_post_row_actions($actions, $post) {

        if ($post->post_type === 'tf_template_builder') {
            unset($actions['inline hide-if-no-js']); // Removes "Quick Edit"
            unset($actions['view']); // Removes "View"
        }

		if($post->post_type === 'tf_template_builder' && isset($actions['edit'])){
			$actions['edit'] = sprintf(
				'<a title="%1$s" aria-label="%1$s" href="#">%1$s</a>',
				esc_html__('Edit', 'tourfic')
			);
		}

		return $actions;
	}

	public function tf_template_set_columns($columns) {
		$date_column   = $columns['date'];

		unset($columns['date']);
		unset($columns['author']);

		$columns['service']    = esc_html__('Service', 'tourfic');
		$columns['type']    = esc_html__('Type', 'tourfic');
		$columns['taxonomy_type']    = esc_html__('Taxonomy Type', 'tourfic');
		$columns['term']    = esc_html__('Term', 'tourfic');
		$columns['status']  = esc_html__('Status', 'tourfic');
		$columns['author']  = esc_html__('Author', 'tourfic');
		$columns['date']    = esc_html($date_column);

		return $columns;
	}

	public function tf_template_render_column($column, $post_id) {
        $service = get_post_meta($post_id, 'tf_template_service', true);
        $template_type = get_post_meta($post_id, 'tf_template_type', true);
        $taxonomy_type = get_post_meta($post_id, 'tf_taxonomy_type', true);
        $taxonomy_term = get_post_meta($post_id, 'tf_taxonomy_term', true);
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

            case 'taxonomy_type':
                if (empty($taxonomy_type)) {
                    echo '—';
                } else {
                    if ($taxonomy_type === 'all') {
                        // translators: %s is the template type (e.g., "hotel", "tour").
                        echo esc_html(sprintf( esc_html__('All %s', 'tourfic'), 
                            ucfirst($template_type)
                        ));
                    } else {
                        // Get taxonomy label from taxonomy object
                        $taxonomy_object = get_taxonomy($taxonomy_type);
                        $taxonomy_label = $taxonomy_object ? $taxonomy_object->labels->name : $taxonomy_type;
                        echo esc_html($taxonomy_label);
                    }
                }
                break;
    
            case 'term':
                if (empty($taxonomy_term)) {
                    echo '—';
                } else {
                    if ($taxonomy_term === 'all') {
                        if ($taxonomy_type === 'all') {
                            echo esc_html__('All Items', 'tourfic');
                        } else {
                            // For specific taxonomy type, show "All [Taxonomy]"
                            $taxonomy_object = get_taxonomy($taxonomy_type);
                            $taxonomy_label = $taxonomy_object ? $taxonomy_object->labels->name : $taxonomy_type;
                            // translators: %s will be the taxonomy name.
                            echo esc_html(sprintf(esc_html__('All %s', 'tourfic'), $taxonomy_label));
                        }
                    } else {
                        // Try to get term name
                        $term_name = $taxonomy_term;
                        if ($taxonomy_type && $taxonomy_type !== 'all') {
                            $term = get_term_by('slug', $taxonomy_term, $taxonomy_type);
                            if ($term && !is_wp_error($term)) {
                                $term_name = $term->name;
                            }
                        }
                        echo esc_html($term_name);
                    }
                }
                break;

			case 'status':
                $status_class = $status ? 'active' : 'inactive';
				echo wp_kses('<span class="tf-template-status ' . $status_class . '">' . ($status ? esc_html__('Active', 'tourfic') : esc_html__('Inactive', 'tourfic')) . '</span>', ['span' => ['class' => []]]);
				break;
		}
	}

    function tf_template_builder_add_popup_html() {
        global $pagenow, $post_type;
        
        if (($pagenow == 'edit.php' && $post_type == 'tf_template_builder') || 
            ($pagenow == 'post.php' && $post_type == 'tf_template_builder')) {
            ?>
            <div class="tf-modal tf-modal-small" id="tf-template-builder-popup">
                <div class="tf-modal-dialog">
                    <div class="tf-modal-content">
                        <div class="tf-modal-body">
                            <form id="tf-template-builder-form">
                                <input type="hidden" name="post_id" id="tf-post-id" value="0">
                                <input type="hidden" name="action" value="tf_save_template_builder">
                                
                                <div class="tf-fields">
                                    <div class="tf-modal-header">
                                        <h2>
                                            <?php echo esc_html__('Build Your Template', 'tourfic'); ?>
                                            <div class="tf-field tf-field-switch">
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
                                        </h2>
                                        <a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
                                    </div>
                                    <div class="tf-field tf-field-text">
                                        <label for="tf-template-name" class="tf-field-label"><?php echo esc_html__('Name', 'tourfic'); ?></label>
                                        <div class="tf-fieldset">
                                            <input type="text" name="template_name" id="tf-template-name" placeholder="<?php echo esc_attr__('Enter template name', 'tourfic') ?>" required>
                                        </div>
                                    </div>

                                    <div class="tf-field-wrapper">
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
                                                    <!-- <option value="single"><?php //echo esc_html__('Single', 'tourfic'); ?></option> -->
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="tf-field-wrapper">
                                        <div class="tf-field tf-field-select tf-field-taxonomy">
                                            <label for="tf-taxonomy-type" class="tf-field-label"><?php echo esc_html__('Archive Type', 'tourfic'); ?></label>
                                            <div class="tf-fieldset">
                                                <select name="tf_taxonomy_type" id="tf-taxonomy-type" class="tf-select">
                                                    <option value="all"><?php echo esc_html__('All Archive', 'tourfic'); ?></option>
                                                    <?php foreach (Helper::get_all_taxonomies() as $taxonomy => $taxonomy_data) : ?>
                                                        <option value="<?php echo esc_attr($taxonomy); ?>"><?php echo esc_html($taxonomy_data->label); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="tf-field tf-field-select tf-field-term" style="display: none;">
                                            <label for="tf-taxonomy-term" class="tf-field-label"><?php echo esc_html__('Taxonomy Term', 'tourfic'); ?></label>
                                            <div class="tf-fieldset">
                                                <select name="tf_taxonomy_term" id="tf-taxonomy-term" class="tf-select"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tf-field tf-template-preview">
                                    <div class="tf-field-imageselect">
                                        <label class="tf-field-label"><?php echo esc_html__('Select Archive & Search Result Template', 'tourfic'); ?></label>
                                        <div class="tf-fieldset">
                                            <ul class="tf-image-radio-group">
                                                <li class="">
                                                    <label class="tf-image-checkbox">
                                                        <input type="radio" name="tf_template_design" value="blank" checked>
                                                        <img src="<?php echo esc_url(TF_ASSETS_ADMIN_URL . "images/template/tb-design-blank.png"); ?>" alt="Blank">
                                                        <span class="tf-circle-check"></span>
                                                    </label>
                                                    <span class="tf-image-checkbox-footer">
                                                        <span class="tf-template-title"><?php echo esc_html__('Blank', 'tourfic'); ?></span>
                                                    </span>
                                                </li>
                                                <li class="">
                                                    <label class="tf-image-checkbox">
                                                        <input type="radio" name="tf_template_design" value="design-1">
                                                        <img src="<?php echo esc_url(TF_ASSETS_ADMIN_URL . "images/template/tb-hotel-design-1.png"); ?>" alt="Design 1">
                                                        <span class="tf-circle-check"></span>
                                                    </label>
                                                    <span class="tf-image-checkbox-footer">
                                                        <span class="tf-template-title"><?php echo esc_html__('Design 1', 'tourfic'); ?></span>
                                                    </span>
                                                </li>
                                                <li class="">
                                                    <label class="tf-image-checkbox">
                                                        <input type="radio" name="tf_template_design" value="design-2">
                                                        <img src="<?php echo esc_url(TF_ASSETS_ADMIN_URL . "images/template/tb-hotel-design-2.png"); ?>" alt="Design 2">
                                                        <span class="tf-circle-check"></span>
                                                    </label>
                                                    <span class="tf-image-checkbox-footer">
                                                        <span class="tf-template-title"><?php echo esc_html__('Design 2', 'tourfic'); ?></span>
                                                    </span>
                                                </li>
                                                <li class="">
                                                    <label class="tf-image-checkbox">
                                                        <input type="radio" name="tf_template_design" value="design-3">
                                                        <img src="<?php echo esc_url(TF_ASSETS_ADMIN_URL . "images/template/tb-hotel-design-3.png"); ?>" alt="Design 3">
                                                        <span class="tf-circle-check"></span>
                                                    </label>
                                                    <span class="tf-image-checkbox-footer">
                                                        <span class="tf-template-title"><?php echo esc_html__('Design 3', 'tourfic'); ?></span>
                                                    </span>
                                                </li>
                                                <li class="">
                                                    <label class="tf-image-checkbox">
                                                        <input type="radio" name="tf_template_design" value="default">
                                                        <img src="<?php echo esc_url(TF_ASSETS_ADMIN_URL . "images/template/tb-hotel-design-legacy.png"); ?>" alt="Legacy">
                                                        <span class="tf-circle-check"></span>
                                                    </label>
                                                    <span class="tf-image-checkbox-footer">
                                                        <span class="tf-template-title"><?php echo esc_html__('Legacy', 'tourfic'); ?></span>
                                                    </span>
                                                </li>
                                            </ul>            
                                        </div>
                                    </div>
                                    <!-- Loader Image -->
                                    <div class="tf-template-preview-loader">
                                        <div class="tf-template-preview-loader-img">
                                            <img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="tf-form-actions">
                                    <button type="button" id="tf-edit-with-elementor" class="tf-admin-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <mask id="mask0_747_79" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="29" height="29">
                                            <path d="M28.5 0.5V28.5H0.5V0.5H28.5Z" fill="white" stroke="white"/>
                                        </mask>
                                        <g mask="url(#mask0_747_79)">
                                            <path d="M12 0C5.37193 0 0 5.37193 0 12C0 18.6259 5.37193 24 12 24C18.6281 24 24 18.6281 24 12C23.9978 5.37193 18.6259 0 12 0ZM9.00054 16.9984H7.00164V6.99948H9.00054V16.9984ZM16.9984 16.9984H10.9994V14.9995H16.9984V16.9984ZM16.9984 12.9983H10.9994V10.9994H16.9984V12.9983ZM16.9984 8.99838H10.9994V6.99948H16.9984V8.99838Z" fill="#003C79"/>
                                        </g>
                                        </svg>
                                        <?php echo esc_html__('Edit With Elementor', 'tourfic'); ?>
                                    </button>
                                    <button type="submit" id="tf-save-template" class="tf-admin-btn tf-btn-secondary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M17 21V14C17 13.7348 16.8946 13.4804 16.7071 13.2929C16.5196 13.1054 16.2652 13 16 13H8C7.73478 13 7.48043 13.1054 7.29289 13.2929C7.10536 13.4804 7 13.7348 7 14V21M7 3V7C7 7.26522 7.10536 7.51957 7.29289 7.70711C7.48043 7.89464 7.73478 8 8 8H15M15.2 3C15.7275 3.00751 16.2307 3.22317 16.6 3.6L20.4 7.4C20.7768 7.76926 20.9925 8.27246 21 8.8V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H15.2Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <?php echo esc_html__('Save Changes', 'tourfic'); ?>
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

    /* 
     * Template markup change on service & type change
     * Taxonomy markup change on service & type change
     */
    public function tf_get_template_options_callback() {
        check_ajax_referer('updates', 'nonce');
        
        $service = !empty($_POST['service']) ? sanitize_text_field($_POST['service']) : 'tf_hotel';
        $type = !empty($_POST['type']) ? sanitize_text_field($_POST['type']) : 'archive';
        
        // Define all template options
        $all_templates = [
            'tf_hotel' => [
                'archive' => [
                    'blank' => [
                        'title' => esc_html__('Blank', 'tourfic'),
                        'url' => TF_ASSETS_ADMIN_URL . 'images/template/tb-design-blank.png',
                        'is_blank' => true
                    ],
                    'design-1' => [
                        'title' => esc_html__('Design 1', 'tourfic'),
                        'url' => TF_ASSETS_ADMIN_URL . "images/template/tb-hotel-design-1.png"
                    ],
                    'design-2' => [
                        'title' => esc_html__('Design 2', 'tourfic'),
                        'url' => TF_ASSETS_ADMIN_URL . "images/template/tb-hotel-design-2.png"
                    ],
                    'design-3' => [
                        'title' => esc_html__('Design 3', 'tourfic'),
                        'url' => TF_ASSETS_ADMIN_URL . "images/template/tb-hotel-design-3.png",
                    ],
                    'default' => [
                        'title' => esc_html__('Legacy', 'tourfic'),
                        'url' => TF_ASSETS_ADMIN_URL . "images/template/tb-hotel-design-legacy.png"
                    ]
                ],
                // 'single' => [
                //     'blank' => [
                //         'title' => esc_html__('Blank', 'tourfic'),
                //         'url' => TF_ASSETS_ADMIN_URL . 'images/template/tb-design-blank.png',
                //         'is_blank' => true
                //     ],
                //     'design-1' => [
                //         'title' => esc_html__('Design 1', 'tourfic'),
                //         'url' => TF_ASSETS_ADMIN_URL . "images/template/preview-single-design-1.png",
                //         'preview_link' => 'https://tourfic.com/preview/hotels/tuvo-suites-hotel/'
                //     ],
                //     'design-2' => [
                //         'title' => esc_html__('Design 2', 'tourfic'),
                //         'url' => TF_ASSETS_ADMIN_URL . "images/template/preview-single-design-2.png",
                //         'preview_link' => 'https://tourfic.com/preview/hotels/melbourne-mastlereagh/'
                //     ],
                //     'default' => [
                //         'title' => esc_html__('Legacy', 'tourfic'),
                //         'url' => TF_ASSETS_ADMIN_URL . "images/template/preview-single-default.png",
                //         'preview_link' => 'https://tourfic.com/preview/hotels/rio-ontho-palace/'
                //     ]
                // ]
            ],
            'tf_tours' => [
                'archive' => [
                    'blank' => [
                        'title' => esc_html__('Blank', 'tourfic'),
                        'url' => TF_ASSETS_ADMIN_URL . 'images/template/tb-design-blank.png',
                        'is_blank' => true
                    ],
                    'design-1' => [
                        'title' => esc_html__('Design 1', 'tourfic'),
                        'url' => TF_ASSETS_ADMIN_URL . "images/template/tb-tour-design-1.png"
                    ],
                    'design-2' => [
                        'title' => esc_html__('Design 2', 'tourfic'),
                        'url' => TF_ASSETS_ADMIN_URL . "images/template/tb-tour-design-2.png"
                    ],
                    'design-3' => [
                        'title' => esc_html__('Design 3', 'tourfic'),
                        'url' => TF_ASSETS_ADMIN_URL . "images/template/tb-tour-design-3.png",
                    ],
                    'default' => [
                        'title' => esc_html__('Legacy', 'tourfic'),
                        'url' => TF_ASSETS_ADMIN_URL . "images/template/tb-tour-design-legacy.png"
                    ]
                ],
                // 'single' => [
                //     'blank' => [
                //         'title' => esc_html__('Blank', 'tourfic'),
                //         'url' => TF_ASSETS_ADMIN_URL . 'images/template/tb-design-blank.png',
                //         'is_blank' => true
                //     ],
                //     'design-1' => [
                //         'title' => esc_html__('Design 1', 'tourfic'),
                //         'url' => TF_ASSETS_ADMIN_URL . "images/template/preview-single-design-1.png",
                //         'preview_link' => 'https://tourfic.com/preview/tours/amplified-nz-tour/'
                //     ],
                //     'design-2' => [
                //         'title' => esc_html__('Design 2', 'tourfic'),
                //         'url' => TF_ASSETS_ADMIN_URL . "images/template/preview-single-design-2.png",
                //         'preview_link' => 'https://tourfic.com/preview/tours/ancient-trails-of-japan/'
                //     ],
                //     'default' => [
                //         'title' => esc_html__('Legacy', 'tourfic'),
                //         'url' => TF_ASSETS_ADMIN_URL . "images/template/preview-single-default.png",
                //         'preview_link' => 'https://tourfic.com/preview/tours/magical-russia/'
                //     ]
                // ]
            ],
            'tf_apartment' => [
                'archive' => [
                    'blank' => [
                        'title' => esc_html__('Blank', 'tourfic'),
                        'url' => TF_ASSETS_ADMIN_URL . 'images/template/tb-design-blank.png',
                        'is_blank' => true
                    ],
                    'design-1' => [
                        'title' => esc_html__('Design 1', 'tourfic'),
                        'url' => TF_ASSETS_ADMIN_URL . "images/template/tb-apartment-design-1.png"
                    ],
                    'design-2' => [
                        'title' => esc_html__('Design 2', 'tourfic'),
                        'url' => TF_ASSETS_ADMIN_URL . "images/template/tb-apartment-design-2.png"
                    ],
                    'default' => [
                        'title' => esc_html__('Legacy', 'tourfic'),
                        'url' => TF_ASSETS_ADMIN_URL . "images/template/tb-apartment-design-legacy.png"
                    ]
                ],
                // 'single' => [
                //     'blank' => [
                //         'title' => esc_html__('Blank', 'tourfic'),
                //         'url' => TF_ASSETS_ADMIN_URL . 'images/template/tb-design-blank.png',
                //         'is_blank' => true
                //     ],
                //     'design-1' => [
                //         'title' => esc_html__('Design 1', 'tourfic'),
                //         'url' => TF_ASSETS_ADMIN_URL . "images/template/preview-single-apt-design-1.png",
                //         'preview_link' => 'https://tourfic.com/preview/apartments/2-bedroom-apartment-in-gamle-oslo/'
                //     ],
                //     'default' => [
                //         'title' => esc_html__('Legacy', 'tourfic'),
                //         'url' => TF_ASSETS_ADMIN_URL . "images/template/preview-single-apt-default.png",
                //         'preview_link' => 'https://tourfic.com/preview/apartments/barcelo-residences-dubai-marina/'
                //     ]
                // ]
            ],
            'tf_carrental' => [
                'archive' => [
                    'blank' => [
                        'title' => esc_html__('Blank', 'tourfic'),
                        'url' => TF_ASSETS_ADMIN_URL . 'images/template/tb-design-blank.png',
                        'is_blank' => true
                    ],
                    'design-1' => [
                        'title' => esc_html__('Design 1', 'tourfic'),
                        'url' => TF_ASSETS_ADMIN_URL . "images/template/tb-car-design-1.png"
                    ]
                ],
                // 'single' => [
                //     'blank' => [
                //         'title' => esc_html__('Blank', 'tourfic'),
                //         'url' => TF_ASSETS_ADMIN_URL . 'images/template/tb-design-blank.png',
                //         'is_blank' => true
                //     ],
                //     'design-1' => [
                //         'title' => esc_html__('Design 1', 'tourfic'),
                //         'url' => TF_ASSETS_ADMIN_URL . "images/template/preview-single-car-design-1.png",
                //         'preview_link' => 'https://tourfic.com/preview/cars/honda-city/'
                //     ]
                // ]
            ]
        ];
        
        // Default to hotel options if service not found
        if (!isset($all_templates[$service])) {
            $service = 'tf_hotel';
        }
        
        // Get the specific templates for this service and type
        $templates = isset($all_templates[$service][$type]) ? $all_templates[$service][$type] : $all_templates['tf_hotel']['archive'];
        
        // Generate the markup
        $markup = '';
        if($type == 'archive'){
            $markup .= '<label class="tf-field-label">'. esc_html__('Select Archive Template', 'tourfic') .'</label>';
        } else {
            $markup .= '<label class="tf-field-label">'. esc_html__('Select Single Template', 'tourfic') .'</label>';
        }
        $markup .= '<div class="tf-fieldset"><ul class="tf-image-radio-group">';
        
        foreach ($templates as $value => $option) {
            $checked = ($value == 'blank') ? 'checked' : '';
            $markup .= '<li>';
            $markup .= '<label class="tf-image-checkbox">';
            $markup .= '<input type="radio" name="tf_template_design" value="' . esc_attr($value) . '" ' . $checked . '>';
            $markup .= '<img src="' . esc_url($option['url']) . '" alt="' . esc_attr($option['title']) . '">';
            $markup .= '<span class="tf-circle-check"></span>';
            $markup .= '</label>';
            
            if (!empty($option['preview_link'])) {
                $markup .= '<a class="tf-image-checkbox-footer" href="' . esc_url($option['preview_link']) . '" target="_blank" title="preview">';
                $markup .= '<span class="tf-template-title">' . esc_html($option['title']) . '</span>';
                $markup .= '<i class="dashicons dashicons-external"></i>';
                $markup .= '</a>';
            } else {
                $markup .= '<span class="tf-image-checkbox-footer">';
                $markup .= '<span class="tf-template-title">' . esc_html($option['title']) . '</span>';
                $markup .= '</span>';
            }
            
            $markup .= '</li>';
        }
        
        $markup .= '</ul></div>';

        //taxonomy types
        $taxonomy_markup = '';
        if($type == 'archive'){
            $taxonomy_markup .= '<label for="tf-taxonomy-type" class="tf-field-label">'. esc_html__('Archive Type', 'tourfic') .'</label>';
        } elseif($type == 'single'){
            $taxonomy_markup .= '<label for="tf-taxonomy-type" class="tf-field-label">'. esc_html__('Single Type', 'tourfic') .'</label>';
        }
        $taxonomy_markup .= '<div class="tf-fieldset">';
        $taxonomy_markup .= '<select name="tf_taxonomy_type" id="tf-taxonomy-type" class="tf-select">';

        if($type == 'archive'){
            $taxonomy_markup .= '<option value="all">'. esc_html__('All Archives', 'tourfic') .'</option>';
        } elseif($type == 'single'){
            $taxonomy_markup .= '<option value="all">'. esc_html__('All Single', 'tourfic') .'</option>';
        }

        foreach (Helper::get_all_taxonomies($service) as $taxonomy => $taxonomy_data) {
            $taxonomy_markup .= '<option value="'. esc_attr($taxonomy) .'">'. esc_html($taxonomy_data->label). '</option>';
        }
        $taxonomy_markup .= '</select>';
        $taxonomy_markup .= '</div>';
        
        wp_send_json_success([
            'markup' => $markup,
            'taxonomy_markup' => $taxonomy_markup
        ]);
    }

    /*
     * Term markup change on taxonomy change
     */
    public function tf_update_term_options_callback() {
        check_ajax_referer('updates', 'nonce');
        
        $taxonomy = !empty($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : '';
        $post_id = !empty($_POST['postId']) ? sanitize_text_field($_POST['postId']) : '';
        
        if (empty($taxonomy)) {
            wp_send_json_error(['message' => esc_html__('Taxonomy not provided', 'tourfic')]);
        }
        
        if ($taxonomy === 'all') {
            wp_send_json_success([
                'term_markup' => ''
            ]);
        }

        // Get taxonomy object to access its label
        $taxonomy_object = get_taxonomy($taxonomy);
        $taxonomy_label = $taxonomy_object ? $taxonomy_object->labels->name : esc_html__('Terms', 'tourfic');
        
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ]);
        
        if (is_wp_error($terms)) {
            wp_send_json_error(['message' => $terms->get_error_message()]);
        }
        if(!empty($post_id)){
            $selected_term = get_post_meta($post_id, 'tf_taxonomy_term', true);
        }
        
        $term_markup = '';
        // translators: %s will be the taxonomy name.
        $term_markup .= '<option value="all"' . selected('all', $selected_term, false) . '>' . sprintf(esc_html__('All %s', 'tourfic'), $taxonomy_label) . '</option>';
        if (!empty($terms)) {
            foreach ($terms as $term) {
                $selected = selected($term->slug, $selected_term, false);
                $term_markup .= '<option value="' . esc_attr($term->slug) . '"' . $selected . '>' . esc_html($term->name) . '</option>';
            }
        }
        
        wp_send_json_success([
            'term_markup' => $term_markup
        ]);
    }

    // Get template data for editing
    function tf_load_template_markup_callback() {
        check_ajax_referer('updates', 'nonce');
        
        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);
        
        if (!$post || $post->post_type != 'tf_template_builder') {
            wp_send_json_error();
        }

        $post_title = $post->post_title;
        $tf_template_service = get_post_meta($post_id, 'tf_template_service', true);
        $tf_template_type = get_post_meta($post_id, 'tf_template_type', true);
        $tf_taxonomy_type = get_post_meta($post_id, 'tf_taxonomy_type', true);
        $tf_taxonomy_term = get_post_meta($post_id, 'tf_taxonomy_term', true);
        $tf_template_active = get_post_meta($post_id, 'tf_template_active', true);

        ob_start();
        ?>
        <div class="tf-modal-header">
            <h2>
                <?php echo esc_html__('Build Your Template', 'tourfic'); ?>
                <div class="tf-field tf-field-switch">
                    <div class="tf-fieldset">
                        <label for="tf-template-active" class="tf-switch-label">
                            <input type="checkbox" id="tf-template-active" name="tf_template_active" value="" class="tf-switch" <?php checked($tf_template_active, '1'); ?>>
                            <span class="tf-switch-slider">
                                <span class="tf-switch-on"><?php echo esc_html__('Yes', 'tourfic'); ?></span>
                                <span class="tf-switch-off"><?php echo esc_html__('No', 'tourfic'); ?></span>
                            </span>
                        </label>
                    </div>
                </div>
            </h2>
            <a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
        </div>
        <div class="tf-field tf-field-text">
            <label for="tf-template-name" class="tf-field-label"><?php echo esc_html__('Name', 'tourfic'); ?></label>
            <div class="tf-fieldset">
                <input type="text" name="template_name" id="tf-template-name" placeholder="<?php echo esc_attr__('Enter template name', 'tourfic') ?>" required value="<?php echo esc_attr($post_title) ?>">
            </div>
        </div>

        <div class="tf-field-wrapper">
            <div class="tf-field tf-field-select">
                <label for="tf-template-service" class="tf-field-label"><?php echo esc_html__('Service', 'tourfic'); ?></label>
                <div class="tf-fieldset">
                    <select name="tf_template_service" id="tf-template-service" class="tf-select">
                        <option value="tf_hotel" <?php selected($tf_template_service, 'tf_hotel'); ?>><?php echo esc_html__('Hotel', 'tourfic'); ?></option>
                        <option value="tf_tours" <?php selected($tf_template_service, 'tf_tours'); ?>><?php echo esc_html__('Tour', 'tourfic'); ?></option>
                        <option value="tf_apartment" <?php selected($tf_template_service, 'tf_apartment'); ?>><?php echo esc_html__('Apartment', 'tourfic'); ?></option>
                        <option value="tf_carrental" <?php selected($tf_template_service, 'tf_carrental'); ?>><?php echo esc_html__('Car Rental', 'tourfic'); ?></option>
                    </select>
                </div>
            </div>

            <div class="tf-field tf-field-select">
                <label for="tf-template-type" class="tf-field-label"><?php echo esc_html__('Type', 'tourfic'); ?></label>
                <div class="tf-fieldset">
                    <select name="tf_template_type" id="tf-template-type" class="tf-select">
                        <option value="archive" <?php selected($tf_template_type, 'archive'); ?>><?php echo esc_html__('Archive', 'tourfic'); ?></option>
                        <option value="single" <?php selected($tf_template_type, 'single'); ?>><?php echo esc_html__('Single', 'tourfic'); ?></option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="tf-field-wrapper">
            <div class="tf-field tf-field-select tf-field-taxonomy">
                <label for="tf-taxonomy-type" class="tf-field-label"><?php echo esc_html__('Archive Type', 'tourfic'); ?></label>
                <div class="tf-fieldset">
                    <select name="tf_taxonomy_type" id="tf-taxonomy-type" class="tf-select">
                        <option value="all" <?php selected($tf_taxonomy_type, 'all'); ?>>
                            <?php echo ($tf_template_type == 'archive') ? esc_html__('All Archives', 'tourfic') : esc_html__('All Single', 'tourfic'); ?>
                        </option>
                        <?php foreach (Helper::get_all_taxonomies($tf_template_service) as $taxonomy => $taxonomy_data) : ?>
                            <option value="<?php echo esc_attr($taxonomy); ?>" <?php selected($tf_taxonomy_type, $taxonomy); ?>>
                                <?php echo esc_html($taxonomy_data->label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="tf-field tf-field-select tf-field-term" <?php echo ($tf_taxonomy_type == 'all') ? 'style="display: none;"' : ''; ?>>
                <label for="tf-taxonomy-term" class="tf-field-label"><?php echo esc_html__('Taxonomy Term', 'tourfic'); ?></label>
                <div class="tf-fieldset">
                    <select name="tf_taxonomy_term" id="tf-taxonomy-term" class="tf-select">
                        <?php if ($tf_taxonomy_type && $tf_taxonomy_type != 'all') : ?>
                            <?php 
                            $taxonomy_object = get_taxonomy($tf_taxonomy_type);
                            $taxonomy_label = $taxonomy_object ? $taxonomy_object->labels->name : esc_html__('Terms', 'tourfic');
                            ?>
                            <option value="all" <?php selected($tf_taxonomy_term, 'all'); ?>>
                                <?php 
                                // translators: %s will be the taxonomy name.
                                echo sprintf(esc_html__('All %s', 'tourfic'), esc_html($taxonomy_label)); ?>
                            </option>
                            <?php 
                            $terms = get_terms([
                                'taxonomy' => $tf_taxonomy_type,
                                'hide_empty' => false,
                            ]);
                            
                            if (!is_wp_error($terms)) {
                                foreach ($terms as $term) {
                                    echo '<option value="' . esc_attr($term->slug) . '" ' . selected($tf_taxonomy_term, $term->slug, false) . '>' . esc_html($term->name) . '</option>';
                                }
                            }
                            ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>
        <?php
        $fields_markup = ob_get_clean();
        
        wp_send_json_success([
            'ID' => $post->ID,
            'fields_markup' => $fields_markup,
        ]);
    }

    // Save template data
    function tf_save_template_builder_callback() {
        check_ajax_referer('updates', 'nonce');
        
        $edit_with_elementor = isset($_POST['edit_with_elementor']) ? sanitize_text_field( wp_unslash( $_POST['edit_with_elementor'] ) ) : false;
        $post_id = intval(wp_unslash($_POST['post_id']));
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
            $tf_template_service = !empty($_POST['tf_template_service']) ? sanitize_text_field($_POST['tf_template_service']) : '';
            $tf_template_type = !empty($_POST['tf_template_type']) ? sanitize_text_field($_POST['tf_template_type']) : '';
            $tf_taxonomy_type = !empty($_POST['tf_taxonomy_type']) ? sanitize_text_field($_POST['tf_taxonomy_type']) : '';
            $tf_taxonomy_term = !empty($_POST['tf_taxonomy_term']) ? sanitize_text_field($_POST['tf_taxonomy_term']) : '';
            $tf_template_active = isset($_POST['tf_template_active']) ? '1' : '0';
            $tf_template_design = !empty($_POST['tf_template_design']) ? sanitize_text_field($_POST['tf_template_design']) : '';
            
            // If this template is being activated, deactivate all others for the same service and type
            if ($tf_template_active === '1') {
                $this->deactivate_other_templates($post_id, $tf_template_service, $tf_template_type, $tf_taxonomy_type, $tf_taxonomy_term);
            }
            update_post_meta($post_id, 'tf_template_service', $tf_template_service);
            update_post_meta($post_id, 'tf_template_type', $tf_template_type);
            update_post_meta($post_id, 'tf_taxonomy_type', $tf_taxonomy_type);
            update_post_meta($post_id, 'tf_taxonomy_term', $tf_taxonomy_term);
            update_post_meta($post_id, 'tf_template_active', $tf_template_active);
            update_post_meta($post_id, 'tf_template_design', $tf_template_design);

            update_post_meta( $post_id, '_wp_page_template', 'elementor_header_footer' );
            update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );

            // Template import
            if ( !empty( $tf_template_design ) && $tf_template_design !== 'blank' ) {
                $service = array(
                    'tf_hotel' => 'hotel',
                    'tf_tours' => 'tour',
                    'tf_apartment' => 'apartment',
                    'tf_carrental' => 'carrental'
                );
                
                $template_path = TF_ASSETS_PATH . "demo/{$service[$tf_template_service]}/{$tf_template_type}/{$tf_template_design}.json";
    
                if ( is_file( $template_path ) ) {
                    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
                    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
    
                    $wp_filesystem = new \WP_Filesystem_Direct(null);
                    $fileContent = $wp_filesystem->get_contents($template_path);
    
                    if ( !is_null( $fileContent ) ) {
    
                        add_filter('elementor/files/allow_unfiltered_upload', '__return_true');
    
                        $result = \Elementor\Plugin::$instance->templates_manager->import_template( [
                            'fileData' => base64_encode( $fileContent ),
                            'fileName' => 'tourfic-content.json'
                        ] );
    
                        $imported_template_id = $result[0]['template_id'];
                        $template_data        = get_post_meta( $imported_template_id, '_elementor_data', true );
                        update_post_meta( $post_id, '_elementor_data', $template_data );
                        wp_delete_post( $imported_template_id );
                    }
                }
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

    /**
     * Assign Archive/Single Templates based on active Elementor templates
     */
    function tf_template_builder_custom_template($template) {
        global $post;
        
        // Only proceed if Elementor is loaded
        if (!did_action('elementor/loaded')) {
            return $template;
        }
        
        // Check if we're viewing a template builder post
        if (is_singular('tf_template_builder')) {
            $document = \Elementor\Plugin::$instance->documents->get_doc_for_frontend($post->ID);
            
            if ($document && $document::get_property('support_wp_page_templates')) {
                $page_template = $document->get_meta('_wp_page_template');
                $page_template = in_array($page_template, ['elementor_header_footer', 'elementor_canvas']) 
                    ? $page_template 
                    : 'elementor_header_footer';

                $template_module = \Elementor\Plugin::$instance->modules_manager->get_modules('page-templates');
                $template_path = $template_module->get_template_path($page_template);

                // Fallback to kit default template if needed
                if ('elementor_theme' !== $page_template && $document->is_built_with_elementor()) {
                    $kit_default_template = \Elementor\Plugin::$instance->kits_manager->get_current_settings('default_page_template');
                    $template_path = $template_module->get_template_path($kit_default_template);
                }

                if ($template_path) {
                    // Set up the content rendering callback
                    $template_module->set_print_callback(function() use ($post) {
                        echo \Elementor\Plugin::$instance->frontend->get_builder_content($post->ID, true); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    });
                    
                    return $template_path;
                }
            }
        }
        
        // For service templates (archive/single)
        $service_post_types = ['tf_hotel', 'tf_tours', 'tf_apartment', 'tf_carrental'];
        
        // Check archive pages
        if (is_post_type_archive($service_post_types)) {
            $post_type = get_post_type();
            $active_template = $this->get_active_template($post_type, 'archive', 'all');
            
            if ($active_template) {
                return $this->load_elementor_template($active_template);
            }
        } 
        // Check taxonomy archive pages
        elseif (is_tax()) {
            $current_taxonomy = get_queried_object()->taxonomy;
            $current_term = get_queried_object()->slug;
            
            // First try to find a template specific to this term
            $active_template = $this->get_active_template_by_taxonomy($current_taxonomy, $current_term);
            
            if (!$active_template) {
                // If no term-specific template, try for a taxonomy-wide template
                $active_template = $this->get_active_template_by_taxonomy($current_taxonomy, 'all');
            }
            
            if ($active_template) {
                return $this->load_elementor_template($active_template);
            }
        }
        // Check single posts
        elseif (is_singular($service_post_types)) {
            $post_type = get_post_type();
            $post_id = get_the_ID();
            
            // First try to find a template specific to this post's terms
            $taxonomies = get_object_taxonomies($post_type);
            $active_template = false;
            
            foreach ($taxonomies as $taxonomy) {
                $terms = wp_get_post_terms($post_id, $taxonomy, ['fields' => 'slugs']);
                
                foreach ($terms as $term) {
                    $active_template = $this->get_active_template($post_type, 'single', $taxonomy, $term);
                    if ($active_template) break;
                }
                
                if ($active_template) break;
                
                // If no term-specific template, try for a taxonomy-wide template
                $active_template = $this->get_active_template($post_type, 'single', $taxonomy, 'all');
                if ($active_template) break;
            }
            
            // If no taxonomy/term specific template, try for a general single template
            if (!$active_template) {
                $active_template = $this->get_active_template($post_type, 'single', 'all', 'all');
            }
            
            if ($active_template) {
                return $this->load_elementor_template($active_template);
            }
        }

        return $template;
    }

    /**
     * Load Elementor template using Elementor's built-in template system
     */
    private function load_elementor_template($template_post) {
        $document = \Elementor\Plugin::$instance->documents->get_doc_for_frontend($template_post->ID);
        
        if ($document && $document::get_property('support_wp_page_templates')) {
            $page_template = $document->get_meta('_wp_page_template');
            $page_template = in_array($page_template, ['elementor_header_footer', 'elementor_canvas']) 
                ? $page_template 
                : 'elementor_header_footer';

            $template_module = \Elementor\Plugin::$instance->modules_manager->get_modules('page-templates');
            $template_path = $template_module->get_template_path($page_template);

            // Fallback to kit default template if needed
            if ('elementor_theme' !== $page_template && !$template_path && $document->is_built_with_elementor()) {
                $kit_default_template = \Elementor\Plugin::$instance->kits_manager->get_current_settings('default_page_template');
                $template_path = $template_module->get_template_path($kit_default_template);
            }

            if ($template_path) {
                // Set up the content rendering callback
                $template_module->set_print_callback(function() use ($template_post) {
                    echo \Elementor\Plugin::$instance->frontend->get_builder_content($template_post->ID, true); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                });
                
                return $template_path;
            }
        }
        
        return false;
    }

    /**
     * Get active template for a service, type, taxonomy and term
     */
    private function get_active_template($service, $type, $taxonomy = 'all') {
        $args = [
            'post_type' => 'tf_template_builder',
            'posts_per_page' => 1,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'tf_template_service',
                    'value' => $service,
                ],
                [
                    'key' => 'tf_template_type',
                    'value' => $type,
                ],
                [
                    'key' => 'tf_taxonomy_type',
                    'value' => $taxonomy,
                ],
                [
                    'key' => 'tf_template_active',
                    'value' => '1',
                ]
            ]
        ];
        
        $templates = get_posts($args);
        
        return !empty($templates) ? $templates[0] : false;
    }

    /**
     * Get active template for a specific taxonomy and term
     */
    private function get_active_template_by_taxonomy($taxonomy, $term) {
        $args = [
            'post_type' => 'tf_template_builder',
            'posts_per_page' => 1,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'tf_template_type',
                    'value' => 'archive',
                ],
                [
                    'key' => 'tf_taxonomy_type',
                    'value' => $taxonomy,
                ],
                [
                    'key' => 'tf_taxonomy_term',
                    'value' => $term,
                ],
                [
                    'key' => 'tf_template_active',
                    'value' => '1',
                ]
            ]
        ];
        
        $templates = get_posts($args);
        
        return !empty($templates) ? $templates[0] : false;
    }

    /**
     * Deactivate other templates for the same service and type
     */
    private function deactivate_other_templates($current_post_id, $service, $type, $tf_taxonomy_type, $tf_taxonomy_term) {
        $args = array(
            'post_type' => 'tf_template_builder',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'post__not_in' => array($current_post_id),
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'tf_template_service',
                    'value' => $service,
                ),
                array(
                    'key' => 'tf_template_type',
                    'value' => $type,
                ),
                array(
                    'key' => 'tf_template_active',
                    'value' => '1',
                ),
                array(
                    'key' => 'tf_taxonomy_type',
                    'value' => $tf_taxonomy_type,
                ),
                array(
                    'key' => 'tf_taxonomy_term',
                    'value' => $tf_taxonomy_term,
                ),
            )
        );
        
        $templates = get_posts($args);
        
        foreach ($templates as $template) {
            update_post_meta($template->ID, 'tf_template_active', '0');

            // Add admin notice for deactivated templates
            // translators: %s will be the template id
            $deactivated_notice = sprintf( esc_html__('Template "%s" was deactivated because a new active template was created with the same criteria.', 'tourfic'),
                get_the_title($template->ID)
            );
            
            set_transient('tf_template_deactivated_' . $template->ID, $deactivated_notice, 60);
        }
    }

    public function enforce_elementor_template_on_save($post_id, $post, $update) {
        // Only for Elementor-built templates
        if (did_action('elementor/loaded')) {
            $page_template = get_post_meta($post_id, '_wp_page_template', true);
    
            // Fallback or enforce our required template
            if (!$page_template || $page_template === 'default') {
                update_post_meta($post_id, '_wp_page_template', 'elementor_header_footer');
            }
    
            // Optional: force edit mode
            if (!get_post_meta($post_id, '_elementor_edit_mode', true)) {
                update_post_meta($post_id, '_elementor_edit_mode', 'builder');
            }
        }
    }
    
    public function modify_elementor_edit_url($url, $document) {
        $post = $document->get_post();

        if ($post->post_type === 'tf_template_builder') {
            $template_type = get_post_meta($post->ID, 'tf_template_type', true);
            $service = get_post_meta($post->ID, 'tf_template_service', true);

            if ($template_type === 'archive' && $service) {
                // Append service param to the URL
                return add_query_arg('tf_archive_service', $service, $url);
            }
        }

        return $url;
    }

    public function setup_editor_post_data() {
        // ARCHIVE PREVIEW
        if (isset($_GET['tf_archive_service'])) {
            $post_type = sanitize_key($_GET['tf_archive_service']);

            global $wp_query;

            // Mock an archive query
            $wp_query = new \WP_Query([
                'post_type' => $post_type,
                'posts_per_page' => 10,
                'orderby' => 'rand',
            ]);

            // Ensure Elementor knows it's an archive
            add_filter('elementor/utils/is_archive_template', '__return_true');
            add_filter('elementor_pro/utils/is_archive_template', '__return_true');
            add_filter('elementor_pro/utils/get_preview_query_vars', function($vars) use ($post_type) {
                $vars['post_type'] = $post_type;
                return $vars;
            });
        }
    }

}
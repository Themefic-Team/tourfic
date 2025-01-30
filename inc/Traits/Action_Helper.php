<?php
namespace Tourfic\Traits;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use Tourfic\Classes\Hotel\Hotel;
use Tourfic\Classes\Tour\Tour;
use \Tourfic\Classes\Apartment\Apartment;
use \Tourfic\Classes\Car_Rental\Availability;
use \Tourfic\Admin\Emails\TF_Handle_Emails;
use Tourfic\Classes\Apartment\Pricing as Apt_Pricing;
use Tourfic\Classes\Tour\Pricing as Tour_Pricing;
use Tourfic\Classes\Hotel\Pricing as Hotel_Pricing;

trait Action_Helper {
	
	/**
	 * Assign Archive Template
	 *
	 * @since 1.0
	 */
	function tourfic_archive_page_template( $template ) {
		if ( is_post_type_archive( 'tf_hotel' ) ) {
			$theme_files     = array( 'tourfic/hotel/archive-hotels.php' );
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . 'hotel/archive-hotels.php';
			}
		}


		if ( is_post_type_archive( 'tf_apartment' ) ) {
			$theme_files     = array( 'tourfic/apartment/archive-apartments.php' );
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . 'apartment/archive-apartments.php';
			}
		}

		if ( is_post_type_archive( 'tf_tours' ) ) {
			$theme_files     = array( 'tourfic/tour/archive-tours.php' );
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . 'tour/archive-tours.php';
			}
		}

		if ( is_post_type_archive( 'tf_carrental' ) ) {
			$theme_files     = array( 'tourfic/car/archive-cars.php' );
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . 'car/archive-cars.php';
			}
		}

		return $template;
	}


	function tourfic_admin_menu_seperator() {

		global $menu;

		$menu[] = array( '', 'read', 'separator-tourfic', '', 'wp-menu-separator tourfic' );
		$menu[] = array( '', 'read', 'separator-tourfic2', '', 'wp-menu-separator tourfic' );
	}

	function tourfic_admin_menu_order_change( $menu_order ) {

		if ( ! empty( $menu_order ) && $menu_order != null ) {
			$tourfic_menu_order = array();

			$tourfic_separator  = array_search( 'separator-tourfic', $menu_order, true );
			$tourfic_separator2 = array_search( 'separator-tourfic2', $menu_order, true );
			$tourfic_tours      = array_search( 'edit.php?post_type=tf_tours', $menu_order, true );
			$tourfic_hotel      = array_search( 'edit.php?post_type=tf_hotel', $menu_order, true );
			$tourfic_hotel_room = array_search( 'edit.php?post_type=tf_room', $menu_order, true );
			$tourfic_car        = array_search( 'edit.php?post_type=tf_carrental', $menu_order, true );
			$tourfic_apt        = array_search( 'edit.php?post_type=tf_apartment', $menu_order, true );
			$tourfic_emails     = array_search( 'edit.php?post_type=tf_email_templates', $menu_order, true );
			$tourfic_vendor     = array_search( 'tf-multi-vendor', $menu_order, true );

			// // remove previous orders
			unset( $menu_order[ $tourfic_separator ] );
			unset( $menu_order[ $tourfic_separator2 ] );

			if ( ! empty( $tourfic_apt ) ) {
				unset( $menu_order[ $tourfic_apt ] );
			}

			if ( ! empty( $tourfic_tours ) ) {
				unset( $menu_order[ $tourfic_tours ] );
			}

			if ( ! empty( $tourfic_hotel ) ) {
				unset( $menu_order[ $tourfic_hotel ] );
			}
			if ( ! empty( $tourfic_car ) ) {
				unset( $menu_order[ $tourfic_car ] );
			}

			if ( ! empty( $tourfic_hotel_room ) && !empty( $tourfic_hotel ) ) {
				unset( $menu_order[ $tourfic_hotel_room ] );
			}

			if ( ! empty( $tourfic_vendor ) ) {
				unset( $menu_order[ $tourfic_vendor ] );
			}

			if ( ! empty( $tourfic_emails ) ) {
				unset( $menu_order[ $tourfic_emails ] );
			}

			foreach ( $menu_order as $index => $item ) {

				if ( 'tf_settings' === $item ) {
					$tourfic_menu_order[] = 'separator-tourfic';
					$tourfic_menu_order[] = $item;
					$tourfic_menu_order[] = 'edit.php?post_type=tf_tours';
					$tourfic_menu_order[] = 'edit.php?post_type=tf_hotel';
					$tourfic_menu_order[] = 'edit.php?post_type=tf_room';
					$tourfic_menu_order[] = 'edit.php?post_type=tf_apartment';
					$tourfic_menu_order[] = 'edit.php?post_type=tf_carrental';
					$tourfic_menu_order[] = 'tf-multi-vendor';
					$tourfic_menu_order[] = 'edit.php?post_type=tf_email_templates';
					$tourfic_menu_order[] = 'separator-tourfic2';

				} elseif ( ! in_array( $item, array( 'separator-tourfic' ), true ) ) {
					$tourfic_menu_order[] = $item;
				}
			}

			return $tourfic_menu_order;

		} else {

			return;
		}
	}

	/*
     * Save user extra fields
     * @author Foysal
     */
	function tf_save_extra_user_profile_fields( $user_id ) {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-user_' . $user_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		update_user_meta( $user_id, 'language', $_POST['language'] );
	}

	/*
     * User extra fields
     * @author Foysal
     */
	function tf_extra_user_profile_fields( $user ) { ?>
        <h3><?php esc_html_e( 'Tourfic Extra profile information', 'tourfic' ); ?></h3>

        <table class="form-table">
            <tr>
                <th><label for="language"><?php esc_html_e( 'Language', 'tourfic' ); ?></label></th>
                <td>
                    <input type="text" name="language" id="language"
                           value="<?php echo esc_attr( get_the_author_meta( 'language', $user->ID ) ); ?>"
                           class="regular-text"/><br/>
                    <span class="description"><?php esc_html_e( "Please enter your languages. Example: Bangla, English, Hindi", 'tourfic' ); ?></span>
                </td>
            </tr>
        </table>
	<?php }

	/**
	 * Go to Documentaion Metabox
	 */
	function tf_hotel_tour_docs() {
		$tf_promo_option = get_option( 'tf_promo__schudle_option' );
		$tf_hotel_promo_sidebar_notice = get_option( 'tf_hotel_promo_sidebar_notice' );  
		$tf_apartment_promo_sidebar_notice = get_option( 'tf_apartment_promo_sidebar_notice' );  
		$tf_tour_promo_sidebar_notice = get_option( 'tf_tour_promo_sidebar_notice' );  
		$service_banner = isset($tf_promo_option['service_banner']) ? $tf_promo_option['service_banner'] : array();
        $promo_banner = isset($tf_promo_option['promo_banner']) ? $tf_promo_option['promo_banner'] : array();
		$tf_promo__schudle_start_from = !empty(get_option( 'tf_promo__schudle_start_from' )) ? get_option( 'tf_promo__schudle_start_from' ) : 0;

		if((!empty($service_banner) && $service_banner['enable_status']  == true) || ( !empty($promo_banner) && $promo_banner['enable_status'] == true ) ) {
			$side_banaer_status = true;
		}else{
			$side_banaer_status = false;
		}

		if ( ($tf_hotel_promo_sidebar_notice != 1  && time() <  $tf_hotel_promo_sidebar_notice) || $side_banaer_status == false || ($tf_promo__schudle_start_from  != 0 && $tf_promo__schudle_start_from > time()) ) {  // Check if the notice is not dismissed or promo is not exicuted
			add_meta_box( 'tfhotel_docs', esc_html__( 'Tourfic Documentation', 'tourfic' ), array( $this, 'tf_hotel_docs_callback' ), 'tf_hotel', 'side', 'high' );
		}

		if ( ($tf_apartment_promo_sidebar_notice != 1  && time() <  $tf_apartment_promo_sidebar_notice) || $side_banaer_status == false || ($tf_promo__schudle_start_from  != 0 && $tf_promo__schudle_start_from > time())) {  // Check if the notice is not dismissed or promo is not exicuted
			add_meta_box( 'tfapartment_docs', esc_html__( 'Tourfic Documantation', 'tourfic' ), array( $this, 'tf_apartment_docs_callback' ), 'tf_apartment', 'side', 'high' );
		}


		if ( ($tf_tour_promo_sidebar_notice != 1  && time() <  $tf_tour_promo_sidebar_notice) || $side_banaer_status == false || ($tf_promo__schudle_start_from  != 0 && $tf_promo__schudle_start_from > time())) { // Check if the notice is not dismissed or promo is not exicuted
			add_meta_box( 'tftour_docs', esc_html__( 'Tourfic Documentation', 'tourfic' ), array( $this, 'tf_tour_docs_callback' ), 'tf_tours', 'side', 'high' ); 
		}
		
		

		add_filter( 'get_user_option_meta-box-order_tf_tours', array( $this, 'tour_metabox_order' ) );
		add_filter( 'get_user_option_meta-box-order_tf_apartment', array( $this, 'apartment_metabox_order' ) );
		add_filter( 'get_user_option_meta-box-order_tf_hotel', array( $this, 'hotel_metabox_order' ) );
	}

	function tf_hotel_docs_callback() {
		$tfhoteldocumentation = sanitize_url( 'https://themefic.com/docs/tourfic/how-it-works/add-new-hotel/' );
		?>
        <div class="tf_docs_preview">
            <a href="<?php echo esc_url( $tfhoteldocumentation ); ?>" target="_blank">
                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL . 'images/banner-cta.png' ); ?>" alt="<?php echo esc_html__( 'Go to Documentation', 'tourfic' ); ?>">
            </a>
        </div>
		<?php
	}

	function tf_apartment_docs_callback() {
		global $wp_meta_boxes;
		$tf_apartment_documentation = sanitize_url( 'https://themefic.com/docs/tourfic/add-new-apartment/locations-types-and-featured-image/' );
		?>
        <div class="tf_docs_preview">
            <a href="<?php echo esc_url( $tf_apartment_documentation ); ?>" target="_blank">
                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL . 'images/banner-cta.png' ); ?>" alt="<?php echo esc_html__( 'Go to Documentation', 'tourfic' ); ?>">
            </a>
        </div>
		<?php
	}

	function tf_tour_docs_callback() {
		$tf_tour_documentation = sanitize_url( 'https://themefic.com/docs/tourfic/tours/tourfic-hotel-general-settings/' );
		?>
        <div class="tf_docs_preview">
            <a href="<?php echo esc_url( $tf_tour_documentation ); ?>" target="_blank">
                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL . 'images/banner-cta.png' ); ?>" alt="<?php echo esc_html__( 'Go to Documentation', 'tourfic' ); ?>">
            </a>
        </div>
		<?php
	}

	function apartment_metabox_order( $order ) {
		return array(
			'side' => join(
				",",
				array(
					'submitdiv',
					'tfapartment_docs',
					'tfapartment_black_friday_docs'
				)
			),
		);
	}

	function tour_metabox_order( $order ) {
		return array(
			'side' => join(
				",",
				array(
					'submitdiv',
					'tftour_docs',
					'tftour_black_friday_docs'
				)
			),
		);
	}

	function hotel_metabox_order( $order ) {
		return array(
			'side' => join(
				",",
				array(
					'submitdiv',
					'tfhotel_docs',
					'tfhotel_black_friday_docs'
				)
			),
		);
	}


		/**
	 * Go to Documentation Menu Item
	 */
	function tf_documentation_page_integration() {
		global $submenu;
		$doc_url = sanitize_url( 'https://themefic.com/docs/tourfic/' );

		$submenu['edit.php?post_type=tf_hotel'][]     = array(
			sprintf( '<span class="tf-go-docs" style=color:#ffba00;">%s</span>', esc_html__( 'Go to Documentation', 'tourfic' ) ),
			'edit_tf_hotels',
			$doc_url
		);
		$submenu['edit.php?post_type=tf_apartment'][] = array(
			sprintf( '<span class="tf-go-docs" style=color:#ffba00;">%s</span>', esc_html__( 'Go to Documentation', 'tourfic' ) ),
			'edit_tf_apartments',
			$doc_url
		);
		$submenu['edit.php?post_type=tf_tours'][]     = array(
			sprintf( '<span class="tf-go-docs" style=color:#ffba00;">%s</span>', esc_html__( 'Go to Documentation', 'tourfic' ) ),
			'edit_tf_tourss',
			$doc_url
		);

	}

	//  Plugin Page Action Links for Tourfic Pro
	function tf_pro_plugin_licence_action_links( $links ) {

		$active_licence_link = array(
			'<a href="'.admin_url().'admin.php?page=tf_license_info" style="color:#cc0000;font-weight: bold;text-shadow: 0px 1px 1px hsl(0deg 0% 0% / 28%);">' . esc_html__( 'Activate the Licence', 'tourfic' ) . '</a>',
		);

		return array_merge( $links, $active_licence_link );
	}

	//  Plugin Page Action Links for Tourfic
	function tf_plugin_action_links( $links ) {

		$settings_link = array(
			'<a href="admin.php?page=tf_dashboard">' . esc_html__( 'Settings', 'tourfic' ) . '</a>',
		);

		if ( !is_plugin_active( 'tourfic-pro/tourfic-pro.php' ) ) {
			$gopro_link = array(
				'<a href="https://tourfic.com/go/upgrade" target="_blank" style="color:#cc0000;font-weight: bold;text-shadow: 0px 1px 1px hsl(0deg 0% 0% / 28%);">' . esc_html__( 'GO PRO', 'tourfic' ) . '</a>',
			);

			return array_merge( $settings_link, $links, $gopro_link );
		} else {
			return array_merge( $settings_link, $links );
		}
	}

	function tf_image_sizes() {
		// Hotel gallery, hard crop
		add_image_size( 'tf_apartment_gallery_large', 819, 475, true );
		add_image_size( 'tf_apartment_gallery_small', 333, 231, true );
		add_image_size( 'tf_apartment_single_thumb', 1170, 500, true );
		add_image_size( 'tf_gallery_thumb', 900, 490, true );
		add_image_size( 'tf-thumb-480-320', 480, 320, true );
	}

	/**
	 * Assign Single Template
	 *
	 * @since 1.0
	 */
	function tf_single_page_template( $single_template ) {

		global $post;

		/**
		 * Hotel Single
		 *
		 * single-hotel.php
		 */
		if ( 'tf_hotel' === $post->post_type ) {

			$theme_files     = array( 'tourfic/hotel/single-hotel.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . "hotel/single-hotel.php";
			}
		}

		/**
		 * Apartment Single
		 *
		 * single-apartment.php
		 */
		if ( 'tf_apartment' === $post->post_type ) {

			$theme_files     = array( 'tourfic/apartment/single-apartment.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . "apartment/single-apartment.php";
			}
		}

		/**
		 * Tour Single
		 *
		 * single-tour.php
		 */
		if ( $post->post_type == 'tf_tours' ) {

			$theme_files     = array( 'tourfic/tour/single-tour.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . "tour/single-tour.php";
			}
		}

		/**
		 * Car Single
		 *
		 * single-car.php
		 */
		if ( $post->post_type == 'tf_carrental' ) {

			$theme_files     = array( 'tourfic/car/single-car.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . "car/single-car.php";
			}
		}

		return $single_template;
	}

	/**
	 * Assign Review Template Part
	 *
	 * @since 1.0
	 */
	function load_comment_template( $comment_template ) {
		global $post;

		if ( 'tf_hotel' === $post->post_type || 'tf_tours' === $post->post_type || 'tf_apartment' === $post->post_type ) {
			$theme_files     = array( 'tourfic/template-parts/review.php' );
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . 'template-parts/review.php';
			}
		}

	}

	/*
     * Asign Destination taxonomy template
     */
	function taxonomy_template( $template ) {

		if ( is_tax( 'hotel_location' ) ) {

			$theme_files     = array( 'tourfic/hotel/taxonomy-hotel_locations.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				$template = $exists_in_theme;
			} else {
				$template = TF_TEMPLATE_PATH . 'hotel/taxonomy-hotel_locations.php';
			}
		}

		if ( is_tax( 'apartment_location' ) ) {
			$theme_files     = array( 'tourfic/apartment/taxonomy-apartment_locations.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				$template = $exists_in_theme;
			} else {
				$template = TF_TEMPLATE_PATH . 'apartment/taxonomy-apartment_locations.php';
			}
		}

		if ( is_tax( 'tour_destination' ) ) {

			$theme_files     = array( 'tourfic/tour/taxonomy-tour_destinations.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				$template = $exists_in_theme;
			} else {
				$template = TF_TEMPLATE_PATH . 'tour/taxonomy-tour_destinations.php';
			}

		}

		if ( is_tax( 'carrental_location' ) ) {
			$theme_files     = array( 'tourfic/car/taxonomy-carrental_locations.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				$template = $exists_in_theme;
			} else {
				$template = TF_TEMPLATE_PATH . 'car/taxonomy-carrental_locations.php';
			}
		}

		if ( is_tax( 'carrental_brand' ) ) {
			$theme_files     = array( 'tourfic/car/taxonomy-carrental_brands.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				$template = $exists_in_theme;
			} else {
				$template = TF_TEMPLATE_PATH . 'car/taxonomy-carrental_brands.php';
			}
		}

		return $template;

	}

	/**
	 * Add tour, hotel & apartment capabilities to admin & editor
	 *
	 * tf_tours, tf_hotel, tf_apartment
	 */
	function tf_admin_role_caps() {

		if ( get_option( 'tf_admin_caps' ) < 7 ) {
			$admin_role  = get_role( 'administrator' );
			$editor_role = get_role( 'editor' );

			// Add a new capability.
			$caps = array(
				// Hotels
				'edit_tf_hotel',
				'read_tf_hotel',
				'delete_tf_hotel',
				'edit_tf_hotels',
				'edit_others_tf_hotels',
				'publish_tf_hotels',
				'read_private_tf_hotels',
				'delete_tf_hotels',
				'delete_private_tf_hotels',
				'delete_published_tf_hotels',
				'delete_others_tf_hotels',
				'edit_private_tf_hotels',
				'edit_published_tf_hotels',
				'create_tf_hotels',
				// Rooms
				'edit_tf_room',
				'read_tf_room',
				'delete_tf_room',
				'edit_tf_rooms',
				'edit_others_tf_rooms',
				'publish_tf_rooms',
				'read_private_tf_rooms',
				'delete_tf_rooms',
				'delete_private_tf_rooms',
				'delete_published_tf_rooms',
				'delete_others_tf_rooms',
				'edit_private_tf_rooms',
				'edit_published_tf_rooms',
				'create_tf_rooms',
				// Apartment
				'edit_tf_apartment',
				'read_tf_apartment',
				'delete_tf_apartment',
				'edit_tf_apartments',
				'edit_others_tf_apartments',
				'publish_tf_apartments',
				'read_private_tf_apartments',
				'delete_tf_apartments',
				'delete_private_tf_apartments',
				'delete_published_tf_apartments',
				'delete_others_tf_apartments',
				'edit_private_tf_apartments',
				'edit_published_tf_apartments',
				'create_tf_apartments',
				// Tours
				'edit_tf_tours',
				'read_tf_tours',
				'delete_tf_tours',
				'edit_tf_tourss',
				'edit_others_tf_tourss',
				'publish_tf_tourss',
				'read_private_tf_tourss',
				'delete_tf_tourss',
				'delete_private_tf_tourss',
				'delete_published_tf_tourss',
				'delete_others_tf_tourss',
				'edit_private_tf_tourss',
				'edit_published_tf_tourss',
				'create_tf_tourss',

				// Car
				'edit_tf_carrental',
				'read_tf_carrental',
				'delete_tf_carrental',
				'edit_tf_carrentals',
				'edit_others_tf_carrentals',
				'publish_tf_carrentals',
				'read_private_tf_carrentals',
				'delete_tf_carrentals',
				'delete_private_tf_carrentals',
				'delete_published_tf_carrentals',
				'delete_others_tf_carrentals',
				'edit_private_tf_carrentals',
				'edit_published_tf_carrentals',
				'create_tf_carrentals',
			);

			foreach ( $caps as $cap ) {
				$admin_role->add_cap( $cap );
				$editor_role->add_cap( $cap );
			}

			update_option( 'tf_admin_caps', 7 );
		}
	}

	function tf_customer_role_caps(){
		if ( get_option( 'tf_customer_caps' ) < 1 ) {
			$customer_role  = get_role( 'customer' );

			// Add a new capability.
			$caps = array(
				// for comment submit
				'unfiltered_html',
			);

			foreach ( $caps as $cap ) {
				$customer_role->add_cap( $cap );
			}

			update_option( 'tf_customer_caps', 1 );
		}
	}

	/**
	 * Search Result Sidebar check availability
	 *
	 * Hotel Filter by Feature
	 *
	 * Ajax function
	 */
	function tf_search_result_ajax_sidebar() {
		// Check nonce security
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ), 'tf_ajax_nonce' ) ) {
			return;
		}
		/**
		 * Get form data
		 */
		global $wpdb;
		$adults = ! empty( $_POST['adults'] ) ? sanitize_text_field( $_POST['adults'] ) : '';
		$child  = ! empty( $_POST['children'] ) ? sanitize_text_field( $_POST['children'] ) : '';
		$infant = ! empty( $_POST['infant'] ) && $_POST['infant'] != "undefined" ? sanitize_text_field( $_POST['infant'] ) : '';

		$room         = ! empty( $_POST['room'] ) ? sanitize_text_field( $_POST['room'] ) : '';
		$check_in_out = ! empty( $_POST['checked'] ) && 'undefined'!=$_POST['checked'] ? sanitize_text_field( $_POST['checked'] ) : '';

		$relation        = self::tfopt( 'search_relation', 'AND' );
		$filter_relation = self::tfopt( 'filter_relation', 'OR' );

		$search                = !empty( $_POST['dest'] ) ? sanitize_text_field( $_POST['dest'] ) : null;
		$filters               = !empty( $_POST['filters'] ) ? explode( ',', sanitize_text_field( $_POST['filters'] ) ) : null;
		$features              = !empty( $_POST['features'] ) ? explode( ',', sanitize_text_field( $_POST['features'] ) ) : null;
		$tf_hotel_types        = !empty( $_POST['tf_hotel_types'] ) ? explode( ',', sanitize_text_field( $_POST['tf_hotel_types'] ) ) : null;
		$tour_features         = !empty( $_POST['tour_features'] ) ? explode( ',', sanitize_text_field( $_POST['tour_features'] ) ) : null;
		$attractions           = !empty( $_POST['attractions'] ) ? explode( ',', sanitize_text_field( $_POST['attractions'] ) ) : null;
		$activities            = !empty( $_POST['activities'] ) ? explode( ',', sanitize_text_field( $_POST['activities'] ) ) : null;
		$tf_tour_types         = !empty( $_POST['tf_tour_types'] ) ? explode( ',', sanitize_text_field( $_POST['tf_tour_types'] ) ) : null;
		$tf_apartment_features = !empty( $_POST['tf_apartment_features'] ) ? explode( ',', sanitize_text_field( $_POST['tf_apartment_features'] ) ) : null;
		$tf_apartment_types    = !empty( $_POST['tf_apartment_types'] ) ? explode( ',', sanitize_text_field( $_POST['tf_apartment_types'] ) ) : null;
		$posttype              = !empty( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'tf_hotel';
		$ordering_type 		   = !empty( $_POST["tf_ordering"] ) ? $_POST["tf_ordering"] : 'default';
		# Separate taxonomy input for filter query
		$place_taxonomy  = $posttype == 'tf_tours' ? 'tour_destination' : ( $posttype == 'tf_apartment' ? 'apartment_location' : 'hotel_location' );
		$filter_taxonomy = $posttype == 'tf_tours' ? 'null' : 'hotel_feature';
		# Take dates for filter query
		$checkin    = isset( $_POST['checkin'] ) ? trim( $_POST['checkin'] ) : array();
		$startprice = ! empty( $_POST['startprice'] ) ? $_POST['startprice'] : '';
		$endprice   = ! empty( $_POST['endprice'] ) ? $_POST['endprice'] : '';

        //Map Template only
        $mapFilter = !empty($_POST['mapFilter']) ? sanitize_text_field($_POST['mapFilter']) : false;
        $mapCoordinates = !empty($_POST['mapCoordinates']) ? explode(',', sanitize_text_field($_POST['mapCoordinates'])) : [];
        if (!empty($mapCoordinates) && count($mapCoordinates) === 4) {
            list($minLat, $minLng, $maxLat, $maxLng) = $mapCoordinates;
        }

		// Cars Data Start
		$pickup   = isset( $_POST['pickup'] ) ? sanitize_text_field( $_POST['pickup'] ) : '';
		$dropoff = isset( $_POST['dropoff'] ) ? sanitize_text_field( $_POST['dropoff'] ) : '';
		$tf_pickup_date  = isset( $_POST['pickup_date'] ) ? sanitize_text_field( $_POST['pickup_date'] ) : '';
		$tf_dropoff_date  = isset( $_POST['dropoff_date'] ) ? sanitize_text_field( $_POST['dropoff_date'] ) : '';
		$tf_pickup_time  = isset( $_POST['pickup_time'] ) ? sanitize_text_field( $_POST['pickup_time'] ) : '';
		$tf_dropoff_time  = isset( $_POST['dropoff_time'] ) ? sanitize_text_field( $_POST['dropoff_time'] ) : '';

		$tf_dropoff_same_location  = isset( $_POST['same_location'] ) ? sanitize_text_field( $_POST['same_location'] ) : '';
		if(!empty($tf_dropoff_same_location)){
			$dropoff = $pickup;
		}
		$tf_driver_age  = isset( $_POST['driver_age'] ) ? sanitize_text_field( $_POST['driver_age'] ) : '';

		$category = ( $_POST['category'] ) ? explode( ',', sanitize_text_field( $_POST['category'] ) ) : null;
		$fuel_type = ( $_POST['fuel_type'] ) ? explode( ',', sanitize_text_field( $_POST['fuel_type'] ) ) : null;
		$engine_year = ( $_POST['engine_year'] ) ? explode( ',', sanitize_text_field( $_POST['engine_year'] ) ) : null;

		$tf_startprice  = isset( $_POST['startprice'] ) ? sanitize_text_field( $_POST['startprice'] ) : '';
		$tf_endprice  = isset( $_POST['endprice'] ) ? sanitize_text_field( $_POST['endprice'] ) : '';
		$tf_min_seat  = isset( $_POST['min_seat'] ) ? sanitize_text_field( $_POST['min_seat'] ) : '';
		$tf_max_seat  = isset( $_POST['max_seat'] ) ? sanitize_text_field( $_POST['max_seat'] ) : '';

		$car_driver_min_age = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['car_archive_driver_min_age'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['car_archive_driver_min_age'] : 18;
        $car_driver_max_age = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['car_archive_driver_max_age'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['car_archive_driver_max_age'] : 40;

		// Cars Data End

		// Author Id if any
		$tf_author_ids = ! empty( $_POST['tf_author'] ) ? $_POST['tf_author'] : '';

		if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
			if ( $posttype == "tf_tours" ) {
				$data = array( $adults, $child, $check_in_out, $startprice, $endprice );
			} elseif ( $posttype == "tf_hotel" ) {
				$data = array( $adults, $child, $room, $check_in_out, $startprice, $endprice );
			} else {
				$data = array( $adults, $child, $infant, $check_in_out, $startprice, $endprice );
			}
		} else {
			if ( $posttype == "tf_tours" ) {
				$data = array( $adults, $child, $check_in_out );
			} elseif ( $posttype == "tf_hotel" ) {
				$data = array( $adults, $child, $room, $check_in_out );
			} else {
				$data = array( $adults, $child, $infant, $check_in_out );
			}
		}

		if ( ! empty( $check_in_out ) ) {
			list( $tf_form_start, $tf_form_end ) = explode( ' - ', $check_in_out );
		}

		if ( ! empty( $check_in_out ) ) {
			$period = new \DatePeriod(
				new \DateTime( $tf_form_start ),
				new \DateInterval( 'P1D' ),
				new \DateTime( ! empty( $tf_form_end ) ? $tf_form_end : $tf_form_start . '23:59' )
			);
		} else {
			$period = '';
		}
		if ( $check_in_out ) {
			$form_check_in      = substr( $check_in_out, 0, 10 );
			$form_check_in_stt  = strtotime( $form_check_in );
			$form_check_out     = substr( $check_in_out, 13, 10 );
			$form_check_out_stt = strtotime( $form_check_out );
		}

		$post_per_page = self::tfopt( 'posts_per_page' ) ? self::tfopt( 'posts_per_page' ) : 10;
		// $paged = !empty($_POST['page']) ? absint( $_POST['page'] ) : 1;
		// Properties args
		if ( $posttype == "tf_tours" ) {
			$tf_expired_tour_showing = ! empty( self::tfopt( 't-show-expire-tour' ) ) ? self::tfopt( 't-show-expire-tour' ) : '';
			if ( ! empty( $tf_expired_tour_showing ) ) {
				$tf_tour_posts_status = array( 'publish', 'expired' );
			} else {
				$tf_tour_posts_status = array( 'publish' );
			}

			$args = array(
				'post_type'      => $posttype,
				'post_status'    => $tf_tour_posts_status,
				'posts_per_page' => - 1,
				'author'         => $tf_author_ids,
			);
		} else {
			$args = array(
				'post_type'      => $posttype,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'author'         => $tf_author_ids,
			);
		}

		if ( $search && 'undefined'!=$search ) {

			$args['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => $place_taxonomy,
					'field'    => 'slug',
					'terms'    => sanitize_title( $search, '' ),
				),
			);
		}

		if ( $filters ) {
			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => $filter_taxonomy,
					'terms'    => $filters,
				);
			} else {
				$args['tax_query']['tf_filters']['relation'] = 'AND';

				foreach ( $filters as $key => $term_id ) {
					$args['tax_query']['tf_filters'][] = array(
						'taxonomy' => $filter_taxonomy,
						'terms'    => array( $term_id ),
					);
				}
			}
		}

		//Query for the features filter of hotel
		if ( $features ) {
			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'tf_feature',
					'terms'    => $features,
				);
			} else {
				$args['tax_query']['tf_feature']['relation'] = 'AND';

				foreach ( $filters as $key => $term_id ) {
					$args['tax_query']['tf_feature'][] = array(
						'taxonomy' => 'tf_feature',
						'terms'    => array( $term_id ),
					);
				}
			}
		}

		//Query for the types filter of hotel
		if ( $tf_hotel_types ) {

			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'hotel_type',
					'terms'    => $tf_hotel_types,
				);
			} else {
				$args['tax_query']['hotel_type']['relation'] = 'AND';

				foreach ( $tf_hotel_types as $key => $term_id ) {
					$args['tax_query']['hotel_type'][] = array(
						'taxonomy' => 'hotel_type',
						'terms'    => array( $term_id ),
					);
				}
			}
		}

		//Query for the features filter of Tour
		if ( $tour_features ) {
			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'tour_features',
					'terms'    => $tour_features,
				);
			} else {
				$args['tax_query']['tour_features']['relation'] = 'AND';

				foreach ( $tour_features as $key => $term_id ) {
					$args['tax_query']['tour_features'][] = array(
						'taxonomy' => 'tour_features',
						'terms'    => array( $term_id ),
					);
				}

			}

		}

		//Query for the attractions filter of tours
		if ( $attractions ) {
			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'tour_attraction',
					'terms'    => $attractions,
				);
			} else {
				$args['tax_query']['tour_attraction']['relation'] = 'AND';

				foreach ( $attractions as $key => $term_id ) {
					$args['tax_query']['tour_attraction'][] = array(
						'taxonomy' => 'tour_attraction',
						'terms'    => array( $term_id ),
					);
				}

			}

		}

		//Query for the activities filter of tours
		if ( $activities ) {
			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'tour_activities',
					'terms'    => $activities,
				);
			} else {
				$args['tax_query']['tour_activities']['relation'] = 'AND';

				foreach ( $activities as $key => $term_id ) {
					$args['tax_query']['tour_activities'][] = array(
						'taxonomy' => 'tour_activities',
						'terms'    => array( $term_id ),
					);
				}

			}

		}

		//Query for the types filter of tours
		if ( $tf_tour_types ) {
			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'tour_type',
					'terms'    => $tf_tour_types,
				);
			} else {
				$args['tax_query']['tour_type']['relation'] = 'AND';

				foreach ( $tf_tour_types as $key => $term_id ) {
					$args['tax_query']['tour_type'][] = array(
						'taxonomy' => 'tour_type',
						'terms'    => array( $term_id ),
					);
				}
			}
		}

		//Query for the features filter of apartments
		if ( $tf_apartment_features ) {
			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'apartment_feature',
					'terms'    => $tf_apartment_features,
				);
			} else {
				$args['tax_query']['apartment_feature']['relation'] = 'AND';

				foreach ( $tf_apartment_features as $key => $term_id ) {
					$args['tax_query']['apartment_feature'][] = array(
						'taxonomy' => 'apartment_feature',
						'terms'    => array( $term_id ),
					);
				}
			}
		}

		//Query for the types filter of apartments
		if ( $tf_apartment_types ) {
			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'apartment_type',
					'terms'    => $tf_apartment_types,
				);
			} else {
				$args['tax_query']['apartment_type']['relation'] = 'AND';

				foreach ( $tf_apartment_types as $key => $term_id ) {
					$args['tax_query']['apartment_type'][] = array(
						'taxonomy' => 'apartment_type',
						'terms'    => array( $term_id ),
					);
				}
			}
		}

		// Car Data Filter Start
		if(!empty($pickup) && "undefined"!=$pickup){
			$args['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'carrental_location',
					'field'    => 'slug',
					'terms'    => sanitize_title( $pickup, '' ),
				),
			);
		}

		if(!empty($tf_min_seat) && !empty($tf_max_seat)){
			$args['meta_query'] = array(
				array(
					'key' => 'tf_search_passengers',
					'value'    => [$tf_min_seat, $tf_max_seat],
					'compare'    => 'BETWEEN',
					'type' => 'DECIMAL(10,3)'
				),
			);
		}

		if(!empty($tf_startprice) && !empty($tf_endprice) && $posttype == 'tf_carrental'){
			$args['meta_query'] = array(
				array(
					'key' => 'tf_search_car_rent',
					'value'    => [$tf_startprice, $tf_endprice],
					'compare'    => 'BETWEEN',
					'type' => 'DECIMAL(10,3)'
				),
			);
		}

		if(!empty($tf_driver_age) && 'on'==$tf_driver_age && $posttype == 'tf_carrental'){
			$args['meta_query'] = array(
				array(
					'key' => 'tf_search_driver_age',
					'value'    => [$car_driver_min_age, $car_driver_max_age],
					'compare'    => 'BETWEEN',
					'type' => 'DECIMAL(10,3)'
				),
			);
		}

		if (!empty($args['meta_query']) && count($args['meta_query']) > 1) {
			$args['meta_query']['relation'] = 'AND';
		}

		if ( $category ) {
			$args['tax_query']['relation'] = $relation;
			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'carrental_category',
					'terms'    => $category,
				);
			} else {
				$args['tax_query']['tf_category']['relation'] = 'AND';

				foreach ( $category as $key => $term_id ) {
					$args['tax_query']['tf_category'][] = array(
						'taxonomy' => 'carrental_category',
						'terms'    => array( $term_id ),
					);
				}
			}
		}

		if ( $fuel_type ) {
			$args['tax_query']['relation'] = $relation;
			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'carrental_fuel_type',
					'terms'    => $fuel_type,
				);
			} else {
				$args['tax_query']['tf_fuel_type']['relation'] = 'AND';

				foreach ( $fuel_type as $key => $term_id ) {
					$args['tax_query']['tf_fuel_type'][] = array(
						'taxonomy' => 'carrental_fuel_type',
						'terms'    => array( $term_id ),
					);
				}
			}
		}

		if ( $engine_year ) {
			$args['tax_query']['relation'] = $relation;
			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'carrental_engine_year',
					'terms'    => $engine_year,
				);
			} else {
				$args['tax_query']['tf_engine_year']['relation'] = 'AND';

				foreach ( $engine_year as $key => $term_id ) {
					$args['tax_query']['tf_engine_year'][] = array(
						'taxonomy' => 'carrental_engine_year',
						'terms'    => array( $term_id ),
					);
				}
			}
		}
		// Car Data Filter End

		$loop = new \WP_Query( $args );

		//get total posts count
		$total_posts = $loop->found_posts;
		if ( $loop->have_posts() ) {
			$not_found = [];
			while ( $loop->have_posts() ) {

				$loop->the_post();

				if ( $posttype == 'tf_hotel' ) {

					if ( empty( $check_in_out ) ) {
						Hotel::tf_filter_hotel_without_date( $period, $not_found, $data );
					} else {
						Hotel::tf_filter_hotel_by_date( $period, $not_found, $data );
					}

				} elseif ( $posttype == 'tf_tours' ) {
					if ( empty( $check_in_out ) ) {
						/**
						 * Check if minimum and maximum people limit matches with the search query
						 */
						$total_person = intval( $adults ) + intval( $child );
						$meta         = get_post_meta( get_the_ID(), 'tf_tours_opt', true );

						//skip the tour if the search form total people  exceeds the maximum number of people in tour
						if ( ! empty( $meta['cont_max_people'] ) && $meta['cont_max_people'] < $total_person && $meta['cont_max_people'] != 0 ) {
							$total_posts --;
							continue;
						}

						//skip the tour if the search form total people less than the maximum number of people in tour
						if ( ! empty( $meta['cont_min_people'] ) && $meta['cont_min_people'] > $total_person && $meta['cont_min_people'] != 0 ) {
							$total_posts --;
							continue;
						}
						Tour::tf_filter_tour_by_without_date( $period, $total_posts, $not_found, $data );
					} else {
						Tour::tf_filter_tour_by_date( $period, $total_posts, $not_found, $data );
					}
				} elseif ( $posttype == 'tf_apartment' ) {
					if ( empty( $check_in_out ) ) {
						Apartment::tf_filter_apartment_without_date( $period, $not_found, $data );
					} else {
						Apartment::tf_filter_apartment_by_date( $period, $not_found, $data );
					}
				} elseif ( $posttype == 'tf_carrental' ) {
					$car_meta = get_post_meta( get_the_ID() , 'tf_carrental_opt', true );

					$car_inventory = Availability::tf_car_inventory(get_the_ID(), $car_meta, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time);
					if($car_inventory){
						tf_car_availability_response($car_meta, $not_found, $pickup, $dropoff, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time, $tf_startprice, $tf_endprice);
					}
				}else{

				}
			}
			$tf_total_results = 0;
			$tf_total_filters = [];
			foreach ( $not_found as $not ) {
				if ( $not['found'] != 1 ) {
					$tf_total_results   = $tf_total_results + 1;
					$tf_total_filters[] = $not['post_id'];
				}
			}

			if ( empty( $tf_total_filters ) ) {
				if ( function_exists( 'is_tf_pro' ) && is_tf_pro() &&
                     (Hotel::template( 'archive' ) == 'design-3' ||
                      Tour::template( 'archive' ) == 'design-3' ||
                      Apartment::template( 'archive' ) == 'design-2' ) ) {
					?>
                    <div id="map-datas" style="display: none"><?php echo wp_json_encode([]); ?></div>
					<?php
				}
				echo '<div class="tf-nothing-found" data-post-count="0">' . esc_html__( 'Nothing Found!', 'tourfic' ) . '</div>';
			}
			$post_per_page = self::tfopt( 'posts_per_page' ) ? self::tfopt( 'posts_per_page' ) : 10;

			$total_filtered_results = count( $tf_total_filters );
			$current_page           = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
			$offset                 = ( $current_page - 1 ) * $post_per_page;
			$displayed_results      =  array_slice( $tf_total_filters, $offset, $post_per_page );
			$sorting_data = $this->tf_get_sorting_data( $ordering_type, $displayed_results, $posttype );

			$displayed_results = !empty( $sorting_data ) ? $sorting_data : $displayed_results;

			if ( ! empty( $displayed_results ) ) {
				$filter_args = array(
					'post_type'      => $posttype,
					'posts_per_page' => $post_per_page,
					'orderby' 		 => array( 'post__in' => 'ASC' ),
					'post__in'       => $displayed_results,
				);

				if ( $ordering_type == "default" ) {
					unset( $filter_args['orderby'] );
				} else if ( $ordering_type == 'latest') {
					$filter_args['orderby'] = 'ID';
					$filter_args['order'] = 'DESC';
				}else if ( $ordering_type == 'price-low') {
					$filter_args['orderby'] = array( 'post__in' => 'DESC' );
				}

				$result_query  = new \WP_Query( $filter_args );
				$result_query2 = $result_query;
				if ( $result_query->have_posts() ) {
					$count     = 0;
					$locations = [];

					while ( $result_query->have_posts() ) {
						$result_query->the_post();

						if ( $posttype == 'tf_hotel' ) {
							$hotel_meta = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );
							if ( ! $hotel_meta["featured"] ) {
                                continue;
							}

							if ( function_exists( 'is_tf_pro' ) && is_tf_pro()) {
								$count ++;
								$map                 = ! empty( $hotel_meta['map'] ) ? Helper::tf_data_types( $hotel_meta['map'] ) : '';
								$min_price_arr       = Hotel_Pricing::instance( get_the_ID() )->get_min_price();
								$min_sale_price      = ! empty( $min_price_arr['min_sale_price'] ) ? $min_price_arr['min_sale_price'] : 0;
								$min_regular_price   = ! empty( $min_price_arr['min_regular_price'] ) ? $min_price_arr['min_regular_price'] : 0;
								$min_discount_type   = ! empty( $min_price_arr['min_discount_type'] ) ? $min_price_arr['min_discount_type'] : 'none';
								$min_discount_amount = ! empty( $min_price_arr['min_discount_amount'] ) ? $min_price_arr['min_discount_amount'] : 0;

								if ( $min_regular_price != 0 ) {
									$price_html = wc_format_sale_price( $min_regular_price, $min_sale_price );
								} else {
									$price_html = wp_kses_post( wc_price( $min_sale_price ) ) . " ";
								}

								if ( ! empty( $map ) ) {
									$lat = $map['latitude'];
									$lng = $map['longitude'];

									// Filter based on the map coordinates provided in the POST request
									if (!empty($mapCoordinates) && ($lat < $minLat || $lat > $maxLat || $lng < $minLng || $lng > $maxLng)) {
                                        $count--;
                                        continue;
									}
									ob_start();
									?>
                                    <div class="tf-map-item">
                                        <div class="tf-map-item-thumb">
                                            <a href="<?php the_permalink(); ?>">
												<?php
												if ( ! empty( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) ) {
													the_post_thumbnail( 'full' );
												} else {
													echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
												}
												?>
                                            </a>

											<?php
											if ( ! empty( $min_discount_amount ) ) : ?>
                                                <div class="tf-map-item-discount">
													<?php echo $min_discount_type == "percent" ? wp_kses_post($min_discount_amount . '%') : wp_kses_post(wc_price( $min_discount_amount )) ?>
													<?php esc_html_e( " Off", "tourfic" ); ?>
                                                </div>
											<?php endif; ?>
                                        </div>
                                        <div class="tf-map-item-content">
                                            <h4>
												<a href="<?php the_permalink(); ?>">
													<?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
												</a>
											</h4>
                                            <div class="tf-map-item-price">
												<?php echo wp_kses_post(Hotel_Pricing::instance( get_the_ID() )->get_min_price_html()); ?>
                                            </div>
											<?php \Tourfic\App\TF_Review::tf_archive_single_rating(); ?>
                                        </div>
                                    </div>
									<?php
									$infoWindowtext = ob_get_clean();

									$locations[ $count ] = [
										'id'      => get_the_ID(),
										'url'	  => get_the_permalink(),
										'lat'     => (float) $lat,
										'lng'     => (float) $lng,
										'price'   => base64_encode( $price_html ),
										'content' => base64_encode( $infoWindowtext )
									];
								}
							}
							if ( ! empty( $data ) ) {
								if ( isset( $data[4] ) && isset( $data[5] ) ) {
									[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;

									if ( $hotel_meta["featured"] ) {
										Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice );
									}
								} else {
									[ $adults, $child, $room, $check_in_out ] = $data;
									if ( $hotel_meta["featured"] ) {
										Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
									}
								}
							} else {
								if ( $hotel_meta["featured"] ) {
									Hotel::tf_hotel_archive_single_item();
								}
							}
						} elseif ( $posttype == 'tf_tours' ) {
							$tour_meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
							if ( ! $tour_meta["tour_as_featured"] ) {
                                continue;
							}

							if ( function_exists( 'is_tf_pro' ) && is_tf_pro()) {
                                $count ++;
                                $map            = ! empty( $tour_meta['location'] ) ? Helper::tf_data_types( $tour_meta['location'] ) : '';
                                $discount_type  = ! empty( $tour_meta['discount_type'] ) ? $tour_meta['discount_type'] : '';
                                $discount_price = ! empty( $tour_meta['discount_price'] ) ? $tour_meta['discount_price'] : '';

                                $min_price_arr     = Tour_Pricing::instance( get_the_ID() )->get_min_price();
                                $min_sale_price    = ! empty( $min_price_arr['min_sale_price'] ) ? $min_price_arr['min_sale_price'] : 0;
                                $min_regular_price = ! empty( $min_price_arr['min_regular_price'] ) ? $min_price_arr['min_regular_price'] : 0;
                                $min_discount      = ! empty( $min_price_arr['min_discount'] ) ? $min_price_arr['min_discount'] : 0;

                                if ( ! empty( $min_discount ) ) {
                                    $price_html = wc_format_sale_price( $min_regular_price, $min_sale_price );
                                } else {
                                    $price_html = wp_kses_post( wc_price( $min_sale_price ) ) . " ";
                                }

                                if ( ! empty( $map ) ) {
                                    $lat = $map['latitude'];
                                    $lng = $map['longitude'];

	                                // Filter based on the map coordinates provided in the POST request
	                                if (!empty($mapCoordinates) && ($lat < $minLat || $lat > $maxLat || $lng < $minLng || $lng > $maxLng)) {
                                        $count--;
		                                continue;
	                                }
                                    ob_start();
                                    ?>
                                    <div class="tf-map-item">
                                        <div class="tf-map-item-thumb">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php
                                                if ( ! empty( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) ) {
                                                    the_post_thumbnail( 'full' );
                                                } else {
                                                    echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
                                                }
                                                ?>
                                            </a>

                                            <?php if ( $discount_type !== 'none' && ! empty( $discount_price ) ) : ?>
                                                <div class="tf-map-item-discount">
                                                    <?php echo $discount_type == "percent" ? wp_kses_post($discount_price . '%') : wp_kses_post(wc_price( $discount_price )) ?>
													<?php esc_html_e( " Off", "tourfic" ); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="tf-map-item-content">
                                            <h4>
												<a href="<?php the_permalink(); ?>">
													<?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
												</a>
											</h4>
                                            <div class="tf-map-item-price">
                                                <?php echo wp_kses_post(Tour_Pricing::instance( get_the_ID() )->get_min_price_html()); ?>
                                            </div>
                                            <?php \Tourfic\App\TF_Review::tf_archive_single_rating(); ?>
                                        </div>
                                    </div>
                                    <?php
                                    $infoWindowtext = ob_get_clean();

                                    $locations[ $count ] = [
                                        'id'      => get_the_ID(),
										'url'	  => get_the_permalink(),
                                        'lat'     => (float) $lat,
                                        'lng'     => (float) $lng,
                                        'price'   => base64_encode( $price_html ),
                                        'content' => base64_encode( $infoWindowtext )
                                    ];
                                }
							}

							if ( ! empty( $data ) ) {
								if ( isset( $data[3] ) && isset( $data[4] ) ) {
									[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
									if ( $tour_meta["tour_as_featured"] ) {
										Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice );
									}
								} else {
									[ $adults, $child, $check_in_out ] = $data;

									if ( $tour_meta["tour_as_featured"] ) {
										Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out );
									}
								}
							} else {
								if ( $tour_meta["tour_as_featured"] ) {
									Tour::tf_tour_archive_single_item();
								}
							}
						} elseif ( $posttype == 'tf_apartment' ) {
							$apartment_meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
							if ( ! $apartment_meta["apartment_as_featured"] ) {
								continue;
							}

							if ( function_exists( 'is_tf_pro' ) && is_tf_pro()) {
								$count ++;
								$map  = ! empty( $apartment_meta['map'] ) ? Helper::tf_data_types( $apartment_meta['map'] ) : '';
								$discount_type  = ! empty( $apartment_meta['discount_type'] ) ? $apartment_meta['discount_type'] : '';
								$discount_price = ! empty( $apartment_meta['discount'] ) ? $apartment_meta['discount'] : '';

								$min_price_arr = Apt_Pricing::instance(get_the_ID())->get_min_price();
								$min_sale_price = !empty($min_price_arr['min_sale_price']) ? $min_price_arr['min_sale_price'] : 0;
								$min_regular_price = !empty($min_price_arr['min_regular_price']) ? $min_price_arr['min_regular_price'] : 0;

								// if ( $min_regular_price != 0 ) {
								// 	$price_html = wc_format_sale_price( $min_regular_price, $min_sale_price );
								// } else {
								// 	$price_html = wp_kses_post( wc_price( $min_sale_price ) ) . " ";
								// }

								$price_html = wp_kses_post(Apt_Pricing::instance(get_the_ID())->get_min_price_html());

								if ( ! empty( $map ) ) {
									$lat = $map['latitude'];
									$lng = $map['longitude'];

									// Filter based on the map coordinates provided in the POST request
									if (!empty($mapCoordinates) && ($lat < $minLat || $lat > $maxLat || $lng < $minLng || $lng > $maxLng)) {
                                        $count--;
										continue;
									}
									ob_start();
									?>
                                    <div class="tf-map-item">
                                        <div class="tf-map-item-thumb">
                                            <a href="<?php the_permalink(); ?>">
												<?php
												if ( ! empty( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) ) {
													the_post_thumbnail( 'full' );
												} else {
													echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
												}
												?>
                                            </a>

											<?php
											if ( ! empty( $discount_price ) ) : ?>
                                                <div class="tf-map-item-discount">
													<?php echo $discount_type == "percent" ? wp_kses_post($discount_price . '%') : wp_kses_post(wc_price( $discount_price )) ?>
													<?php esc_html_e( " Off", "tourfic" ); ?>
                                                </div>
											<?php endif; ?>
                                        </div>
                                        <div class="tf-map-item-content">
                                            <h4>
												<a href="<?php the_permalink(); ?>">
													<?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
												</a>
											</h4>
                                            <div class="tf-map-item-price">
												<?php echo wp_kses_post(Apt_Pricing::instance(get_the_ID())->get_min_price_html()); ?>
                                            </div>
											<?php \Tourfic\App\TF_Review::tf_archive_single_rating(); ?>
                                        </div>
                                    </div>
									<?php
									$infoWindowtext = ob_get_clean();

									$locations[ $count ] = [
										'id'      => get_the_ID(),
										'url'	  => get_the_permalink(),
										'lat'     => (float) $lat,
										'lng'     => (float) $lng,
										'price'   => base64_encode( $price_html ),
										'content' => base64_encode( $infoWindowtext )
									];
								}
							}
							if ( ! empty( $data ) ) {
								if ( isset( $data[4] ) && isset( $data[5] ) ) {
									if ( $apartment_meta["apartment_as_featured"] ) {
										Apartment::tf_apartment_archive_single_item( $data );
									}
								} else {
									if ( $apartment_meta["apartment_as_featured"] ) {
										Apartment::tf_apartment_archive_single_item( $data );
									}
								}
							} else {
								if ( $apartment_meta["apartment_as_featured"] ) {
									Apartment::tf_apartment_archive_single_item();
								}
							}
						} elseif ( $posttype == 'tf_carrental' ) {
							$car_meta = get_post_meta( get_the_ID(), 'tf_carrental_opt', true );
							if ( $car_meta["car_as_featured"] ) {
								tf_car_archive_single_item($pickup, $dropoff, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time);
							}
						}else{

						}

					}

					while ( $result_query2->have_posts() ) {
						$result_query2->the_post();

						if ( $posttype == 'tf_hotel' ) {
							$hotel_meta = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );
							if ( $hotel_meta["featured"] ) {
								continue;
							}
							if (function_exists( 'is_tf_pro' ) && is_tf_pro()) {
								$count ++;
								$map                 = ! empty( $hotel_meta['map'] ) ? Helper::tf_data_types( $hotel_meta['map'] ) : '';
								$min_price_arr       = Hotel_Pricing::instance( get_the_ID() )->get_min_price();
								$min_sale_price      = ! empty( $min_price_arr['min_sale_price'] ) ? $min_price_arr['min_sale_price'] : 0;
								$min_regular_price   = ! empty( $min_price_arr['min_regular_price'] ) ? $min_price_arr['min_regular_price'] : 0;
								$min_discount_type   = ! empty( $min_price_arr['min_discount_type'] ) ? $min_price_arr['min_discount_type'] : 'none';
								$min_discount_amount = ! empty( $min_price_arr['min_discount_amount'] ) ? $min_price_arr['min_discount_amount'] : 0;

								if ( $min_regular_price != 0 ) {
									$price_html = wc_format_sale_price( $min_regular_price, $min_sale_price );
								} else {
									$price_html = wp_kses_post( wc_price( $min_sale_price ) ) . " ";
								}

								if ( ! empty( $map ) ) {
									$lat = $map['latitude'];
									$lng = $map['longitude'];

									// Filter based on the map coordinates provided in the POST request
									if (!empty($mapCoordinates) && ($lat < $minLat || $lat > $maxLat || $lng < $minLng || $lng > $maxLng)) {
                                        $count--;
                                        continue;
									}

									ob_start();
									?>
                                    <div class="tf-map-item">
                                        <div class="tf-map-item-thumb">
                                            <a href="<?php the_permalink(); ?>">
												<?php
												if ( ! empty( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) ) {
													the_post_thumbnail( 'full' );
												} else {
													echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
												}
												?>
                                            </a>

											<?php
											if ( ! empty( $min_discount_amount ) ) : ?>
                                                <div class="tf-map-item-discount">
													<?php echo $min_discount_type == "percent" ? wp_kses_post($min_discount_amount . '%') : wp_kses_post(wc_price( $min_discount_amount )) ?>
													<?php esc_html_e( " Off", "tourfic" ); ?>
                                                </div>
											<?php endif; ?>
                                        </div>
                                        <div class="tf-map-item-content">
                                            <h4>
												<a href="<?php the_permalink(); ?>">
													<?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
												</a>
											</h4>
                                            <div class="tf-map-item-price">
												<?php echo wp_kses_post(Hotel_Pricing::instance( get_the_ID() )->get_min_price_html()); ?>
                                            </div>
											<?php \Tourfic\App\TF_Review::tf_archive_single_rating(); ?>
                                        </div>
                                    </div>
									<?php
									$infoWindowtext = ob_get_clean();

									$locations[ $count ] = [
										'id'      => get_the_ID(),
										'url'	  => get_the_permalink(),
										'lat'     => (float) $lat,
										'lng'     => (float) $lng,
										'price'   => base64_encode( $price_html ),
										'content' => base64_encode( $infoWindowtext )
									];
								}
							}

							if ( ! empty( $data ) ) {
								if ( isset( $data[4] ) && isset( $data[5] ) ) {
									[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;

									if ( ! $hotel_meta["featured"] ) {
										Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice );
									}
								} else {
									[ $adults, $child, $room, $check_in_out ] = $data;

									if ( ! $hotel_meta["featured"] ) {
										Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
									}
								}
							} else {
								if ( ! $hotel_meta["featured"] ) {
									Hotel::tf_hotel_archive_single_item();
								}
							}
						} elseif ( $posttype == 'tf_tours' ) {
							$tour_meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
							if ( $tour_meta["tour_as_featured"] ) {
								continue;
							}

							if (function_exists( 'is_tf_pro' ) && is_tf_pro()) {
								$count ++;
								$map            = ! empty( $tour_meta['location'] ) ? Helper::tf_data_types( $tour_meta['location'] ) : '';
								$discount_type  = ! empty( $tour_meta['discount_type'] ) ? $tour_meta['discount_type'] : '';
								$discount_price = ! empty( $tour_meta['discount_price'] ) ? $tour_meta['discount_price'] : '';

								$min_price_arr     = Tour_Pricing::instance( get_the_ID() )->get_min_price();
								$min_sale_price    = ! empty( $min_price_arr['min_sale_price'] ) ? $min_price_arr['min_sale_price'] : 0;
								$min_regular_price = ! empty( $min_price_arr['min_regular_price'] ) ? $min_price_arr['min_regular_price'] : 0;
								$min_discount      = ! empty( $min_price_arr['min_discount'] ) ? $min_price_arr['min_discount'] : 0;

								if ( ! empty( $min_discount ) ) {
									$price_html = wc_format_sale_price( $min_regular_price, $min_sale_price );
								} else {
									$price_html = wp_kses_post( wc_price( $min_sale_price ) ) . " ";
								}

								if ( ! empty( $map ) ) {
									$lat = $map['latitude'];
									$lng = $map['longitude'];

									// Filter based on the map coordinates provided in the POST request
									if (!empty($mapCoordinates) && ($lat < $minLat || $lat > $maxLat || $lng < $minLng || $lng > $maxLng)) {
                                        $count--;
										continue;
									}
									ob_start();
									?>
                                    <div class="tf-map-item">
                                        <div class="tf-map-item-thumb">
                                            <a href="<?php the_permalink(); ?>">
												<?php
												if ( ! empty( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) ) {
													the_post_thumbnail( 'full' );
												} else {
													echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
												}
												?>
                                            </a>

											<?php if ( $discount_type !== 'none' && ! empty( $discount_price ) ) : ?>
                                                <div class="tf-map-item-discount">
													<?php echo $discount_type == "percent" ? wp_kses_post($discount_price . '%') : wp_kses_post(wc_price( $discount_price )) ?>
													<?php esc_html_e( " Off", "tourfic" ); ?>
                                                </div>
											<?php endif; ?>
                                        </div>
                                        <div class="tf-map-item-content">
                                            <h4>
												<a href="<?php the_permalink(); ?>">
													<?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
												</a>
											</h4>
                                            <div class="tf-map-item-price">
												<?php echo wp_kses_post(Tour_Pricing::instance( get_the_ID() )->get_min_price_html()); ?>
                                            </div>
											<?php \Tourfic\App\TF_Review::tf_archive_single_rating(); ?>
                                        </div>
                                    </div>
									<?php
									$infoWindowtext = ob_get_clean();

									$locations[ $count ] = [
										'id'      => get_the_ID(),
										'url'	  => get_the_permalink(),
										'lat'     => (float) $lat,
										'lng'     => (float) $lng,
										'price'   => base64_encode( $price_html ),
										'content' => base64_encode( $infoWindowtext )
									];
								}
							}
							if ( ! empty( $data ) ) {
								if ( isset( $data[3] ) && isset( $data[4] ) ) {
									[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
									if ( ! $tour_meta["tour_as_featured"] ) {
										Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice );
									}
								} else {
									[ $adults, $child, $check_in_out ] = $data;
									if ( ! $tour_meta["tour_as_featured"] ) {
										Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out );
									}
								}
							} else {
								if ( ! $tour_meta["tour_as_featured"] ) {
									Tour::tf_tour_archive_single_item();
								}
							}
						} elseif ( $posttype == 'tf_apartment' ) {
							$apartment_meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
							if ( $apartment_meta["apartment_as_featured"] ) {
								continue;
							}

							if ( function_exists( 'is_tf_pro' ) && is_tf_pro()) {
								$count ++;
								$map  = ! empty( $apartment_meta['map'] ) ? Helper::tf_data_types( $apartment_meta['map'] ) : '';
								$discount_type  = ! empty( $apartment_meta['discount_type'] ) ? $apartment_meta['discount_type'] : '';
								$discount_price = ! empty( $apartment_meta['discount'] ) ? $apartment_meta['discount'] : '';

								$min_price_arr = Apt_Pricing::instance(get_the_ID())->get_min_price();
								$min_sale_price = !empty($min_price_arr['min_sale_price']) ? $min_price_arr['min_sale_price'] : 0;
								$min_regular_price = !empty($min_price_arr['min_regular_price']) ? $min_price_arr['min_regular_price'] : 0;

								$price_html = wp_kses_post(Apt_Pricing::instance(get_the_ID())->get_min_price_html());

								if ( ! empty( $map ) ) {
									$lat = $map['latitude'];
									$lng = $map['longitude'];

									// Filter based on the map coordinates provided in the POST request
									if (!empty($mapCoordinates) && ($lat < $minLat || $lat > $maxLat || $lng < $minLng || $lng > $maxLng)) {
                                        $count--;
										continue;
									}
									ob_start();
									?>
                                    <div class="tf-map-item">
                                        <div class="tf-map-item-thumb">
                                            <a href="<?php the_permalink(); ?>">
												<?php
												if ( ! empty( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) ) {
													the_post_thumbnail( 'full' );
												} else {
													echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
												}
												?>
                                            </a>

											<?php
											if ( ! empty( $discount_price ) ) : ?>
                                                <div class="tf-map-item-discount">
													<?php echo $discount_type == "percent" ? wp_kses_post($discount_price . '%') : wp_kses_post(wc_price( $discount_price )) ?>
													<?php esc_html_e( " Off", "tourfic" ); ?>
                                                </div>
											<?php endif; ?>
                                        </div>
                                        <div class="tf-map-item-content">
                                            <h4>
												<a href="<?php the_permalink(); ?>">
													<?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
												</a>
											</h4>
                                            <div class="tf-map-item-price">
												<?php echo wp_kses_post(Apt_Pricing::instance(get_the_ID())->get_min_price_html()); ?>
                                            </div>
											<?php \Tourfic\App\TF_Review::tf_archive_single_rating(); ?>
                                        </div>
                                    </div>
									<?php
									$infoWindowtext = ob_get_clean();

									$locations[ $count ] = [
										'id'      => get_the_ID(),
										'url'	  => get_the_permalink(),
										'lat'     => (float) $lat,
										'lng'     => (float) $lng,
										'price'   => base64_encode( $price_html ),
										'content' => base64_encode( $infoWindowtext )
									];
								}
							}

							if ( ! empty( $data ) ) {
								if ( isset( $data[4] ) && isset( $data[5] ) ) {
									if ( ! $apartment_meta["apartment_as_featured"] ) {
										Apartment::tf_apartment_archive_single_item( $data );
									}
								} else {
									if ( ! $apartment_meta["apartment_as_featured"] ) {
										Apartment::tf_apartment_archive_single_item( $data );
									}
								}
							} else {
								if ( ! $apartment_meta["apartment_as_featured"] ) {
									Apartment::tf_apartment_archive_single_item();
								}
							}
						} elseif ( $posttype == 'tf_carrental' ) {
							$car_meta = get_post_meta( get_the_ID(), 'tf_carrental_opt', true );
							if ( ! $car_meta["car_as_featured"] ) {
								tf_car_archive_single_item($pickup, $dropoff, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time);
							}
						}else{

						}

					}

					if ( Hotel::template( 'archive' ) == 'design-3' || Tour::template( 'archive' ) == 'design-3' || Apartment::template( 'archive' ) == 'design-2' ) {
						?>
                        <div id="map-datas" style="display: none"><?php echo array_filter( $locations ) ? wp_json_encode( array_values( $locations ) ) : wp_json_encode([]); ?></div>
						<?php
					}
				} else {
					echo '<div class="tf-nothing-found" data-post-count="0">' . esc_html__( 'Nothing Found!', 'tourfic' ) . '</div>';
				}
                if($mapFilter == false) {
                    $total_pages = ceil($total_filtered_results / $post_per_page);
                } else {
                    $total_pages = ceil($count / $post_per_page);
					if($count == 0){
						?>
						<div class="tf-nothing-found tf-template-4-nothing-found" data-post-count="0">
							<svg xmlns="http://www.w3.org/2000/svg" width="57" height="56" viewBox="0 0 57 56" fill="none">
							<path d="M28.5 5.25C24.0005 5.25 19.602 6.58426 15.8608 9.08407C12.1196 11.5839 9.20364 15.1369 7.48175 19.294C5.75986 23.451 5.30933 28.0252 6.18715 32.4383C7.06496 36.8514 9.23169 40.905 12.4133 44.0867C15.595 47.2683 19.6486 49.435 24.0617 50.3129C28.4748 51.1907 33.049 50.7402 37.2061 49.0183C41.3631 47.2964 44.9161 44.3804 47.4159 40.6392C49.9157 36.898 51.25 32.4995 51.25 28C51.2436 21.9683 48.8447 16.1854 44.5797 11.9204C40.3146 7.65528 34.5317 5.25637 28.5 5.25ZM28.5 47.25C24.6927 47.25 20.9709 46.121 17.8053 44.0058C14.6396 41.8906 12.1723 38.8841 10.7153 35.3667C9.25834 31.8492 8.87713 27.9786 9.61989 24.2445C10.3627 20.5104 12.196 17.0804 14.8882 14.3882C17.5804 11.696 21.0104 9.86265 24.7445 9.11988C28.4787 8.37712 32.3492 8.75833 35.8667 10.2153C39.3841 11.6723 42.3906 14.1396 44.5058 17.3053C46.621 20.4709 47.75 24.1927 47.75 28C47.7442 33.1036 45.7142 37.9966 42.1054 41.6054C38.4966 45.2142 33.6036 47.2442 28.5 47.25ZM18 23.625C18 23.1058 18.154 22.5983 18.4424 22.1666C18.7308 21.7349 19.1408 21.3985 19.6205 21.1998C20.1001 21.0011 20.6279 20.9492 21.1371 21.0504C21.6463 21.1517 22.1141 21.4017 22.4812 21.7688C22.8483 22.136 23.0983 22.6037 23.1996 23.1129C23.3009 23.6221 23.2489 24.1499 23.0502 24.6295C22.8515 25.1092 22.5151 25.5192 22.0834 25.8076C21.6517 26.096 21.1442 26.25 20.625 26.25C19.9288 26.25 19.2611 25.9734 18.7689 25.4812C18.2766 24.9889 18 24.3212 18 23.625ZM39 23.625C39 24.1442 38.8461 24.6517 38.5576 25.0834C38.2692 25.515 37.8592 25.8515 37.3796 26.0502C36.8999 26.2489 36.3721 26.3008 35.8629 26.1996C35.3537 26.0983 34.886 25.8483 34.5189 25.4812C34.1517 25.114 33.9017 24.6463 33.8004 24.1371C33.6992 23.6279 33.7511 23.1001 33.9498 22.6205C34.1485 22.1408 34.485 21.7308 34.9166 21.4424C35.3483 21.154 35.8558 21 36.375 21C37.0712 21 37.7389 21.2766 38.2312 21.7688C38.7234 22.2611 39 22.9288 39 23.625ZM38.7638 37.625C38.8904 37.8242 38.9754 38.0469 39.0137 38.2798C39.052 38.5127 39.0428 38.7509 38.9867 38.9802C38.9305 39.2094 38.8286 39.4249 38.687 39.6138C38.5454 39.8026 38.367 39.9608 38.1627 40.0789C37.9583 40.197 37.7322 40.2726 37.4979 40.3011C37.2636 40.3295 37.026 40.3103 36.7993 40.2445C36.5726 40.1788 36.3616 40.0678 36.1789 39.9184C35.9962 39.769 35.8457 39.5841 35.7363 39.375C34.1022 36.5509 31.5341 35 28.5 35C25.4659 35 22.8978 36.5531 21.2638 39.375C21.1544 39.5841 21.0038 39.769 20.8211 39.9184C20.6384 40.0678 20.4274 40.1788 20.2007 40.2445C19.974 40.3103 19.7364 40.3295 19.5021 40.3011C19.2678 40.2726 19.0417 40.197 18.8373 40.0789C18.633 39.9608 18.4547 39.8026 18.3131 39.6138C18.1715 39.4249 18.0695 39.2094 18.0134 38.9802C17.9572 38.7509 17.948 38.5127 17.9863 38.2798C18.0246 38.0469 18.1096 37.8242 18.2363 37.625C20.4872 33.7334 24.2278 31.5 28.5 31.5C32.7722 31.5 36.5128 33.7312 38.7638 37.625Z" fill="#6E655E"/>
							</svg>
							<span><?php echo esc_html__( 'No results found!', 'tourfic' ); ?></span>
						</div>
						<?php
					}
                }

                if ($total_pages > 1) {
                    echo "<div class='tf_posts_navigation tf_posts_ajax_navigation tf_search_ajax_pagination'>";
                    echo wp_kses_post(
                        paginate_links(array(
                            'total' => $total_pages,
                            'current' => $current_page
                        ))
                    );
                    echo "</div>";
                }

			}
		} else {
			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() &&
				(Hotel::template( 'archive' ) == 'design-3' ||
				Tour::template( 'archive' ) == 'design-3' ||
				Apartment::template( 'archive' ) == 'design-2' ) ) {
				?>
				<div id="map-datas" style="display: none"><?php echo wp_json_encode([]); ?></div>
				<div class="tf-nothing-found tf-template-4-nothing-found" data-post-count="0">
					<svg xmlns="http://www.w3.org/2000/svg" width="57" height="56" viewBox="0 0 57 56" fill="none">
					<path d="M28.5 5.25C24.0005 5.25 19.602 6.58426 15.8608 9.08407C12.1196 11.5839 9.20364 15.1369 7.48175 19.294C5.75986 23.451 5.30933 28.0252 6.18715 32.4383C7.06496 36.8514 9.23169 40.905 12.4133 44.0867C15.595 47.2683 19.6486 49.435 24.0617 50.3129C28.4748 51.1907 33.049 50.7402 37.2061 49.0183C41.3631 47.2964 44.9161 44.3804 47.4159 40.6392C49.9157 36.898 51.25 32.4995 51.25 28C51.2436 21.9683 48.8447 16.1854 44.5797 11.9204C40.3146 7.65528 34.5317 5.25637 28.5 5.25ZM28.5 47.25C24.6927 47.25 20.9709 46.121 17.8053 44.0058C14.6396 41.8906 12.1723 38.8841 10.7153 35.3667C9.25834 31.8492 8.87713 27.9786 9.61989 24.2445C10.3627 20.5104 12.196 17.0804 14.8882 14.3882C17.5804 11.696 21.0104 9.86265 24.7445 9.11988C28.4787 8.37712 32.3492 8.75833 35.8667 10.2153C39.3841 11.6723 42.3906 14.1396 44.5058 17.3053C46.621 20.4709 47.75 24.1927 47.75 28C47.7442 33.1036 45.7142 37.9966 42.1054 41.6054C38.4966 45.2142 33.6036 47.2442 28.5 47.25ZM18 23.625C18 23.1058 18.154 22.5983 18.4424 22.1666C18.7308 21.7349 19.1408 21.3985 19.6205 21.1998C20.1001 21.0011 20.6279 20.9492 21.1371 21.0504C21.6463 21.1517 22.1141 21.4017 22.4812 21.7688C22.8483 22.136 23.0983 22.6037 23.1996 23.1129C23.3009 23.6221 23.2489 24.1499 23.0502 24.6295C22.8515 25.1092 22.5151 25.5192 22.0834 25.8076C21.6517 26.096 21.1442 26.25 20.625 26.25C19.9288 26.25 19.2611 25.9734 18.7689 25.4812C18.2766 24.9889 18 24.3212 18 23.625ZM39 23.625C39 24.1442 38.8461 24.6517 38.5576 25.0834C38.2692 25.515 37.8592 25.8515 37.3796 26.0502C36.8999 26.2489 36.3721 26.3008 35.8629 26.1996C35.3537 26.0983 34.886 25.8483 34.5189 25.4812C34.1517 25.114 33.9017 24.6463 33.8004 24.1371C33.6992 23.6279 33.7511 23.1001 33.9498 22.6205C34.1485 22.1408 34.485 21.7308 34.9166 21.4424C35.3483 21.154 35.8558 21 36.375 21C37.0712 21 37.7389 21.2766 38.2312 21.7688C38.7234 22.2611 39 22.9288 39 23.625ZM38.7638 37.625C38.8904 37.8242 38.9754 38.0469 39.0137 38.2798C39.052 38.5127 39.0428 38.7509 38.9867 38.9802C38.9305 39.2094 38.8286 39.4249 38.687 39.6138C38.5454 39.8026 38.367 39.9608 38.1627 40.0789C37.9583 40.197 37.7322 40.2726 37.4979 40.3011C37.2636 40.3295 37.026 40.3103 36.7993 40.2445C36.5726 40.1788 36.3616 40.0678 36.1789 39.9184C35.9962 39.769 35.8457 39.5841 35.7363 39.375C34.1022 36.5509 31.5341 35 28.5 35C25.4659 35 22.8978 36.5531 21.2638 39.375C21.1544 39.5841 21.0038 39.769 20.8211 39.9184C20.6384 40.0678 20.4274 40.1788 20.2007 40.2445C19.974 40.3103 19.7364 40.3295 19.5021 40.3011C19.2678 40.2726 19.0417 40.197 18.8373 40.0789C18.633 39.9608 18.4547 39.8026 18.3131 39.6138C18.1715 39.4249 18.0695 39.2094 18.0134 38.9802C17.9572 38.7509 17.948 38.5127 17.9863 38.2798C18.0246 38.0469 18.1096 37.8242 18.2363 37.625C20.4872 33.7334 24.2278 31.5 28.5 31.5C32.7722 31.5 36.5128 33.7312 38.7638 37.625Z" fill="#6E655E"/>
					</svg>
					<span><?php echo esc_html__( 'No results found!', 'tourfic' ); ?></span>
				</div>
				<?php
			} else {
				echo '<div class="tf-nothing-found" data-post-count="0">' . esc_html__( 'Nothing Found!', 'tourfic' ) . '</div>';
			}

		}

		echo "<span hidden=hidden class='tf-posts-count'>";
		echo ! empty( $tf_total_results ) ? esc_html( $tf_total_results ) : 0;
		echo "</span>";
		echo "<span hidden=hidden class='tf-map-posts-count'>";
		echo ! empty( $count ) ? esc_html( $count ) : 0;
		echo "</span>";
		wp_reset_postdata();

		die();
	}

	private function tf_get_sorting_data($ordering_type, $results, $post_type) {
        global $wpdb;
        $sort_results = [];
        foreach ( $results as $post_id ) {
			$comments = $ratings = '';
            if( $ordering_type == 'order') {
                $order_count = $wpdb->get_var($wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}tf_order_data WHERE post_id = %s AND ostatus != %s", $post_id, 'cancelled' ));
                $sort_results[$post_id] = $order_count;
            }else if( $ordering_type == 'enquiry') {
                $enquiry_count = $wpdb->get_var($wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}tf_enquiry_data WHERE post_id = %s ", $post_id ));
                $sort_results[$post_id] = $enquiry_count;
            } else if( $ordering_type == 'rating') {
                $comments        = get_comments( [ 'post_id' => $post_id, 'status' => 'approve' ] );
                $ratings = TF_Review::tf_total_avg_rating( $comments );
                $sort_results[$post_id] = $ratings;
            }else if ($ordering_type == 'price-high') {
                if($post_type == 'tf_apartment') {
					$min_max_price = Apt_Pricing::instance($post_id)->get_min_max_price();
					$sort_results[$post_id] = $min_max_price['max'];
				}

				if( $post_type == 'tf_tours' ) {
					$min_max_price = Tour_Pricing::instance($post_id)->get_min_max_price();
					$sort_results[$post_id] = $min_max_price['max'];
				}

				if( $post_type == 'tf_hotel' ) {
					$min_max_price = Hotel_Pricing::instance($post_id)->get_min_max_price();
					$sort_results[$post_id] = $min_max_price['max']["regular_price"];
				}

            }else if ($ordering_type == 'price-low') {

                if($post_type == 'tf_apartment') {
					$min_max_price = Apt_Pricing::instance($post_id)->get_min_max_price();
					$sort_results[$post_id] = $min_max_price['min'];
				}

				if( $post_type == 'tf_tours' ) {
					$min_max_price = Tour_Pricing::instance($post_id)->get_min_max_price();
					$sort_results[$post_id] = $min_max_price['min'];
				}

				if( $post_type == 'tf_hotel' ) {
					$min_max_price = Hotel_Pricing::instance($post_id)->get_min_max_price();
					$sort_results[$post_id] = $min_max_price['min']["regular_price"];
				}

            }
        }

        arsort($sort_results);

        return $ordering_type !== "default" ? array_keys($sort_results) : $results;
    }

	/**
	 * Monthwise Chart Ajax function
	 *
	 * @author Jahid
	 */
	function tf_month_chart_filter_callback() {
		//Verify Nonce
		check_ajax_referer( 'updates', '_nonce' );

		$search_month = sanitize_key( $_POST['month'] );
		$search_year  = sanitize_key( $_POST['year'] );
		$month_dates  = cal_days_in_month( CAL_GREGORIAN, $search_month, $search_year );

		//Order Data Retrive
		$tf_old_order_limit = new \WC_Order_Query( array(
			'limit'   => - 1,
			'orderby' => 'date',
			'order'   => 'ASC',
			'return'  => 'ids',
		) );
		$order              = $tf_old_order_limit->get_orders();
		$months_day_number  = [];
		for ( $i = 1; $i <= $month_dates; $i ++ ) {
			$months_day_number [] = $i;

			// Booking Month
			${"tf_co$i"} = 0;
			// Booking Cancel Month
			${"tf_cr$i"} = 0;
		}

		foreach ( $order as $item_id => $item ) {
			$itemmeta         = wc_get_order( $item );
			$tf_ordering_date = $itemmeta->get_date_created();
			for ( $i = 1; $i <= $month_dates; $i ++ ) {
				if ( $tf_ordering_date->date( 'n-j-y' ) == $search_month . '-' . $i . '-' . $search_year ) {
					if ( "completed" == $itemmeta->get_status() ) {
						${"tf_co$i"} += 1;
					}
					if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
						${"tf_cr$i"} += 1;
					}
				}
			}
		}
		$tf_complete_orders = [];
		$tf_cancel_orders   = [];
		for ( $i = 1; $i <= $month_dates; $i ++ ) {
			$tf_complete_orders [] = ${"tf_co$i"};
			$tf_cancel_orders []   = ${"tf_cr$i"};
		}

		$response['months_day_number']  = $months_day_number;
		$response['tf_complete_orders'] = $tf_complete_orders;
		$response['tf_cancel_orders']   = $tf_cancel_orders;
		$response['tf_search_month']    = gmdate( "F", strtotime( '2000-' . $search_month . '-01' ) );
		echo wp_json_encode( $response );

		die();
	}

	/**
	 * Remove icon add to order item
	 * @since 2.9.6
	 * @author Foysal
	 */
	function tf_remove_icon_add_to_order_item( $subtotal, $cart_item, $cart_item_key ) {
		if ( ! is_checkout() ) {
			return $subtotal;
		}
		$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
		?>
        <div class="tf-product-total">
			<?php echo wp_kses_post( $subtotal ); ?>
			<?php
			echo sprintf(
				'<a href="#" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
		//		esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
				esc_attr__( 'Remove this item', 'tourfic' ),
				esc_attr( $product_id ),
				esc_attr( $cart_item_key ),
				esc_attr( $_product->get_sku() )
			);
			?>
        </div>
		<?php
	}

	/**
	 * Remove cart item from checkout page
	 * @since 2.9.6
	 * @author Foysal
	 */
	function tf_checkout_cart_item_remove() {
		check_ajax_referer( 'tf_ajax_nonce', '_nonce' );

		if ( isset( $_POST['cart_item_key'] ) ) {
			$cart_item_key = sanitize_key( $_POST['cart_item_key'] );

			// Remove cart item
			WC()->cart->remove_cart_item( $cart_item_key );
		}

		die();
	}

	/**
	 * Update options of email templates[admin,vendor, customer]
	 *
	 * @return void
	 *
	 * @since 2.9.19
	 * @author Abu Hena
	 */
	function tf_update_email_template_default_content() {

		$tf_settings = ! empty( get_option( 'tf_settings' ) ) ? get_option( 'tf_settings' ) : array();
		if ( isset( $tf_settings['email-settings'] ) ) {
			$tf_settings = $tf_settings['email-settings'];

			if ( ! is_array( $tf_settings ) ) {
				return;
			}

			//update email template for admin
			if ( empty( $tf_settings['admin_booking_email_template'] ) ) {
				update_option( $tf_settings['admin_booking_email_template'], TF_Handle_Emails::get_email_template( 'order_confirmation', '', 'admin' ) );
			}
			//update email template for vendor
			if ( empty( $tf_settings['vendor_booking_email_template'] ) ) {
				if ( array_key_exists( 'vendor_booking_email_template', $tf_settings ) ) {
					update_option( $tf_settings['vendor_booking_email_template'], TF_Handle_Emails::get_email_template( 'order_confirmation', '', 'vendor' ) );
				}
			}
			//update email template for customer
			if ( empty( $tf_settings['customer_confirm_email_template'] ) ) {
				update_option( $tf_settings['customer_confirm_email_template'], TF_Handle_Emails::get_email_template( 'order_confirmation', '', 'customer' ) );
			}
		}
	}

	/*
     * Install and active Tourfic Affiliate
     */
	function tf_affiliate_install_callback() {
		$response = [
			'status'  => 'error',
			'message' => esc_html__( 'Something went wrong. Please try again.', 'tourfic' )
		];
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'tf_affiliate_install' ) ) {
			wp_send_json_error( $response );
		}
		if ( current_user_can( 'activate_plugins' ) ) {
			$plugin = 'tourfic-affiliate';
			$result = install_plugin_install_status( $plugin );
			if ( is_wp_error( $result ) ) {
				$response['message'] = $result->get_error_message();
			} else {
				$response['status']  = 'success';
				$response['message'] = esc_html__( 'Tourfic Affiliate installed successfully.', 'tourfic' );
			}
		}

		echo wp_json_encode( $response );
		die();
	}

	/*
     * Activate Tourfic Affiliate
     */
	function tf_affiliate_active_callback() {
		$response = [
			'status'  => 'error',
			'message' => esc_html__( 'Something went wrong. Please try again.', 'tourfic' )
		];
		//    $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
		//    if ( ! wp_verify_nonce( $nonce, 'tf_affiliate_active' ) ) {
		//        wp_send_json_error( $response );
		//    }
		if ( current_user_can( 'activate_plugins' ) ) {
			$plugin = 'tourfic-affiliate/tourfic-affiliate.php';
			$result = activate_plugin( $plugin );
			if ( is_wp_error( $result ) ) {
				$response['message'] = $result->get_error_message();
			} else {
				$response['status']  = 'success';
				$response['message'] = esc_html__( 'Tourfic Affiliate activated successfully.', 'tourfic' );
			}
		}

		echo wp_json_encode( $response );
		die();
	}

	function tf_shortcode_type_to_location_callback() {
		//Nonce Verification
		check_ajax_referer( 'updates', '_nonce' );

		$term_name = $_POST['termName'] ? sanitize_text_field( $_POST['termName'] ) : 'tf_hotel';

		$terms = get_terms( array(
			'taxonomy'   => $term_name,
			'hide_empty' => false,
		) );

		wp_reset_postdata();

		wp_send_json_success( array(
			'value'    => $terms,
			"termName" => $term_name
		) );
	}

	function tf_gutenberg_author_dropdown_roles( $args, $request = null ) {

		// get all the roles in a website
		global $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new \WP_Roles();
		}

		$tf_all_roles = is_array( $wp_roles->get_names() ) && ! empty( $wp_roles->get_names() ) ? array_keys( $wp_roles->get_names() ) : array( 'administrator', 'author', 'editor', 'tf_vendor', 'tf_manager' );

		// exclude the roles that are not needed
		$tf_all_roles = array_filter( $tf_all_roles, function ( $role ) {
			return $role !== 'contributor' && $role !== 'subscriber' && $role !== 'customer';
		}
		);

		if ( current_user_can( 'edit_posts' ) ) {
			if ( isset( $args['who'] ) && $args['who'] === 'authors' ) {
				unset( $args['who'] );
				$args['role__in'] = $tf_all_roles;
			}

			return $args;
		}

		return $args;
	}

	function tf_admin_footer() {

		$screen = get_current_screen();

		if ( is_admin() && ( $screen->id == 'tf_hotel' ) ) {
			global $post;
		?>
			<script>
				var post_id = '<?php echo esc_html( $post->ID ); ?>';
			</script>
			<?php
		}
	}

	function tf_remove_metabox_gutenburg( $response, $taxonomy, $request ) {

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';

		if ( $context === 'edit' && $taxonomy->meta_box_cb === false ) {

			$data_response = $response->get_data();

			$data_response['visibility']['show_ui'] = false;

			$response->set_data( $data_response );
		}

		return $response;
	}

	/**
	 * Template 3 Compatible to others Themes
	 *
	 * @since 2.10.8
	 */
	function tf_templates_body_class( $classes ) {

		$tf_tour_arc_selected_template      = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['tour-archive'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['tour-archive'] : 'design-1';
		$tf_hotel_arc_selected_template     = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['hotel-archive'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['hotel-archive'] : 'design-1';
		$tf_apartment_arc_selected_template = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['apartment-archive'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['apartment-archive'] : 'default';
		$tf_hotel_global_template           = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['single-hotel'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['single-hotel'] : 'design-1';
		$tf_tour_global_template            = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['single-tour'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['single-tour'] : 'design-1';
		$tf_apartment_global_template       = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['single-apartment'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['single-apartment'] : 'default';

		if ( is_post_type_archive( 'tf_tours' ) || is_tax( 'tour_destination' ) ) {
			if ( 'design-2' == $tf_tour_arc_selected_template ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
            if ( 'design-3' == $tf_tour_arc_selected_template ) {
				$classes[] = 'tf_template_4_tour_archive';
			}
		}

		if ( is_post_type_archive( 'tf_hotel' ) || is_tax( 'hotel_location' ) ) {
			if ( 'design-2' == $tf_hotel_arc_selected_template ) {
				$classes[] = 'tf_template_3_global_layouts';
				$classes[] = 'tf_template_3_hotel_archive';
			}
            if ( 'design-3' == $tf_hotel_arc_selected_template ) {
                $classes[] = 'tf_template_4_hotel_archive';
            }
		}

		if ( is_post_type_archive( 'tf_apartment' ) || is_tax( 'apartment_location' ) ) {
			if ( 'design-1' == $tf_apartment_arc_selected_template ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
            if ( 'design-2' == $tf_apartment_arc_selected_template ) {
				$classes[] = 'tf_template_4_apartment_archive';
			}
		}

		if ( is_singular( 'tf_hotel' ) ) {
			$meta                       = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );
			$tf_hotel_layout_conditions = ! empty( $meta['tf_single_hotel_layout_opt'] ) ? $meta['tf_single_hotel_layout_opt'] : 'global';
			if ( "single" == $tf_hotel_layout_conditions ) {
				$tf_hotel_single_template = ! empty( $meta['tf_single_hotel_template'] ) ? $meta['tf_single_hotel_template'] : 'design-1';
			}
			$tf_hotel_selected_check = ! empty( $tf_hotel_single_template ) ? $tf_hotel_single_template : $tf_hotel_global_template;
			if ( 'design-2' == $tf_hotel_selected_check ) {
				$classes[] = 'tf_template_3_global_layouts';
				$classes[] = 'tf_template_3_single_hotel';
			}
			if ( 'design-3' == $tf_hotel_selected_check ) {
				$classes[] = 'tf_template_4_single_hotel';
			}
		}

		if ( is_singular( 'tf_tours' ) ) {
			$meta                      = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
			$tf_tour_layout_conditions = ! empty( $meta['tf_single_tour_layout_opt'] ) ? $meta['tf_single_tour_layout_opt'] : 'global';
			if ( "single" == $tf_tour_layout_conditions ) {
				$tf_tour_single_template = ! empty( $meta['tf_single_tour_template'] ) ? $meta['tf_single_tour_template'] : 'design-1';
			}
			$tf_tour_selected_check = ! empty( $tf_tour_single_template ) ? $tf_tour_single_template : $tf_tour_global_template;
			if ( 'design-2' == $tf_tour_selected_check ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
            if ( 'design-3' == $tf_tour_selected_check ) {
				$classes[] = 'tf_template_4_single_tour';
			}
		}

		if ( is_singular( 'tf_apartment' ) ) {
			$meta                          = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
			$tf_aprtment_layout_conditions = ! empty( $meta['tf_single_apartment_layout_opt'] ) ? $meta['tf_single_apartment_layout_opt'] : 'global';
			if ( "single" == $tf_aprtment_layout_conditions ) {
				$tf_apartment_single_template = ! empty( $meta['tf_single_apartment_template'] ) ? $meta['tf_single_apartment_template'] : 'default';
			}
			$tf_apartment_selected_check = ! empty( $tf_apartment_single_template ) ? $tf_apartment_single_template : $tf_apartment_global_template;
			if ( 'design-1' == $tf_apartment_selected_check ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
			if ( 'design-3' == $tf_apartment_selected_check ) {
				$classes[] = 'tf_template_4_single_apartment';
			}
		}

		$tf_search_result_page_id = ! empty( self::tfopt( 'search-result-page' ) ) ? self::tfopt( 'search-result-page' ) : '';
		if ( ! empty( $tf_search_result_page_id ) ) {
			$tf_search_result_page_slug = get_post_field( 'post_name', $tf_search_result_page_id );
		}
		if ( ! empty( $tf_search_result_page_slug ) ) {
			$tf_current_page_id = get_post_field( 'post_name', get_the_ID() );
			if ( $tf_search_result_page_slug == $tf_current_page_id ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
		}

		return $classes;
	}

	public function tf_admin_bar_dashboard_link( $wp_admin_bar ) {

		if ( ! is_admin() || ! is_admin_bar_showing() ) {
            return;
        }

		if ( ! is_user_member_of_blog() && ! is_super_admin() ) {
            return;
        }

		if( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$tf_dashboard_page_link = !empty( get_option( 'tf_dashboard_page_id' ) ) ? get_permalink( get_option( 'tf_dashboard_page_id' ) )  : get_home_url();

			$wp_admin_bar->add_node(
				array(
					'parent' => 'site-name',
					'id'     => 'view-vendor-dashboard-link',
					'title'  => __( 'Visit Vendor Dashboard', 'tourfic' ),
					'href'   => $tf_dashboard_page_link,
				)
			);

		} else {

			return;
		}
	}

	function tourfic_booking_set_search_result( $url ) {

		$search_result_page = self::tfopt( 'search-result-page' );

		if ( isset( $search_result_page ) ) {
			$url = get_permalink( $search_result_page );
		}

		return $url;

	}

	function tourfic_wp_dropdown_cats_multiple( $output, $r ) {
		if ( isset( $r['multiple'] ) && $r['multiple'] ) {
			$output = preg_replace( '/^<select/i', '<select multiple', $output );
			$output = str_replace( "name='{$r['name']}'", "name='{$r['name']}[]'", $output );
			//if( is_array($r['selected']) ):
			foreach ( array_map( 'trim', explode( ",", $r['selected'] ) ) as $value ) {
				$output = str_replace( "value=\"{$value}\"", "value=\"{$value}\" selected", $output );
			}
			//endif;
		}

		return $output;
	}

	function tf_tours_excerpt_more( $more ) {

		if ( 'tf_tours' === get_post_type() ) {
			return '...';
		}

	}

	function tourfic_notice_wrapper() {
		?>
			<div class="tf-container">
				<div class="tf-notice-wrapper"></div>
			</div>
		<?php
	}

	function tourfic_check_instantio_active() {
		$quick_checkout = !empty(self::tfopt( 'tf-quick-checkout' )) ? self::tfopt( 'tf-quick-checkout' ) : 0;

		if ( $quick_checkout == 0 ) {
			return;
		}else {
			if( is_plugin_active( 'instantio/instantio.php' ) ) {
				return;
			}

			add_action( 'admin_notices', array( $this, 'tourfic_instantio_notice' ) );
		}
	}

	function tourfic_instantio_notice() {
		if( !is_plugin_active( 'instantio/instantio.php' ) && ! file_exists( WP_PLUGIN_DIR . '/instantio/instantio.php' ) ) {
			?>
			<div id="message" class="notice notice-error">
				<p><?php echo  wp_kses_post(sprintf(__( 'Instantio plugin is required for the %s"QUICK CHECKOUT"%s feature of Tourfic. Please install and activate Instantio to ensure this feature works seamlessly.', 'tourfic' ), '<strong>', '</strong>')); ?></p>
				<p><a class="install-now button inc-install" href=<?php echo esc_url( admin_url( '/plugin-install.php?s=slug:instantio&tab=search&type=term' ) ); ?> data-plugin-slug="tourfic"><?php esc_attr_e( 'Install Now', 'tourfic' ); ?></a></p>
			</div>
		<?php
		} else {
			$notice = sprintf( __( 'The %s Instantio%s plugin is inactive. Please activate it to enable the %s "QUICK CHECKOUT" %s for Tourfic.', 'tourfic' ), '<strong><a href="https://wordpress.org/plugins/instantio/" target="_blank">', '</a></strong>', '<b>', '</b>');
			?>
				<div id="message" class="notice notice-error">
					<p><?php echo wp_kses_post( $notice ); ?></p>
					<p><a href="<?php echo esc_html( get_admin_url() ); ?>plugins.php?_wpnonce=<?php echo esc_html( wp_create_nonce( 'activate-plugin_instantio/instantio.php' ) ); ?>&action=activate&plugin=instantio/instantio.php"
							class="button activate-now button-primary">
							<?php esc_attr_e( 'Activate', 'tourfic' ); ?>
						</a>
					</p>
				</div>
			<?php
		}
	}

	function tf_no_idex_search_page($robots) {
		global $post;
		$tf_search_page_id = get_option("tf_search_page_id");

		if( !empty($tf_search_page_id) && $tf_search_page_id == $post->ID ) {
			$robots['noindex'] = true;
			$robots['nofollow'] = true;
			$robots['max-image-preview'] = false;
			return $robots;
		}
	}
}
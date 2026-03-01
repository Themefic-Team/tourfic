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

	/*
	 * Ask question modal 
	 */
	function tf_ask_question_modal() {

		// Allowed post types
		$allowed_post_types = array( 'tf_hotel', 'tf_tours', 'tf_apartment', 'tf_carrental' );

		// Ensure we're on a singular page and correct post type
		if ( ! is_singular( $allowed_post_types ) && ! is_post_type_archive( $allowed_post_types ) ) {
			return;
		}
		?>
		<div class="tf-modal tf-modal-extra-small" id="tf-ask-modal">
			<div class="tf-modal-dialog">
				<div class="tf-modal-content">
					<div class="tf-modal-header">
						<a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
					</div>
					<div class="tf-modal-body">
						<div class="tf-ask-question-head-content">
							<div class="ask-question-svg">
								<svg xmlns="http://www.w3.org/2000/svg" width="82" height="65" viewBox="0 0 82 65" fill="none">
									<path d="M39.8433 2.87109C51.7608 2.87109 58.7471 8.37858 58.7471 17.8305C58.7471 29.748 46.4165 32.1321 46.4165 40.763H32.6912C32.6912 28.5165 41.7326 27.2009 41.7326 20.8724C41.7326 18.4883 40.5827 17.42 38.1171 17.42C35.0752 17.42 33.678 19.4751 33.678 23.9143H19.5396C19.5396 10.681 27.0206 2.87109 39.8433 2.87109ZM47.7348 53.5041C47.7348 58.5169 44.2824 61.9693 39.2697 61.9693C34.3384 61.9693 30.8861 58.5169 30.8861 53.5041C30.8861 48.5729 34.3384 45.1206 39.2697 45.1206C44.2824 45.1206 47.7348 48.5729 47.7348 53.5041Z"
										fill="#CFE6FC"/>
									<path d="M41.688 1C53.6056 1 60.5919 6.50748 60.5919 15.9594C60.5919 27.8769 48.2612 30.261 48.2612 38.8919H34.5359C34.5359 26.6455 43.5774 25.3298 43.5774 19.0013C43.5774 16.6173 42.4274 15.5489 39.9618 15.5489C36.92 15.5489 35.5227 17.604 35.5227 22.0432H21.3843C21.3869 8.80731 28.8653 1 41.688 1ZM49.5795 51.6331C49.5795 56.6458 46.1272 60.0982 41.1144 60.0982C36.1832 60.0982 32.7308 56.6458 32.7308 51.6331C32.7308 46.7018 36.1832 43.2495 41.1144 43.2495C46.1272 43.2468 49.5795 46.6992 49.5795 51.6331Z"
										stroke="#1B334B" stroke-width="1.5" stroke-miterlimit="10"/>
									<path d="M24.1691 51.6832C19.7378 56.1144 15.0908 56.6644 11.5753 53.1515C7.14403 48.7202 10.8411 43.2496 7.63346 40.0393L12.7383 34.9344C17.2933 39.4893 14.4198 43.3391 16.7723 45.6941C17.659 46.5809 18.4827 46.5493 19.401 45.6336C20.5325 44.5021 20.2878 43.218 18.6379 41.5681L23.8954 36.3106C28.8161 41.2313 28.9371 46.9151 24.1691 51.6832ZM2.40754 35.7896C0.541888 33.924 0.541888 31.3584 2.40754 29.4927C4.24161 27.6587 6.80984 27.6587 8.67286 29.5243C10.5069 31.3584 10.5069 33.9266 8.67286 35.7607C6.80984 37.6237 4.24161 37.6237 2.40754 35.7896Z"
										fill="#CFE6FC"/>
									<path d="M24.1795 53.064C19.7483 57.4953 15.1012 58.0452 11.5857 54.5323C7.15447 50.1011 10.8516 44.6304 7.64391 41.4202L12.7488 36.3153C17.3037 40.8702 14.4302 44.7199 16.7827 47.075C17.6695 47.9618 18.4931 47.9302 19.4114 47.0145C20.5429 45.883 20.2982 44.5989 18.6483 42.949L23.9058 37.6915C28.8239 42.6122 28.9476 48.296 24.1795 53.064ZM2.41535 37.1705C0.549701 35.3048 0.549701 32.7392 2.41535 30.8736C4.24942 29.0395 6.81765 29.0395 8.68067 30.9052C10.5147 32.7392 10.5147 35.3075 8.68067 37.1415C6.81765 39.0046 4.24942 39.0046 2.41535 37.1705Z"
										stroke="#1B334B" stroke-width="1.5" stroke-miterlimit="10"/>
									<path d="M75.1344 30.6488C79.9683 34.638 80.9577 39.2113 77.7921 43.0452C73.8029 47.8791 68.0034 44.7188 65.1141 48.2185L59.5461 43.6241C63.6458 38.6561 67.7534 41.1506 69.8717 38.585C70.669 37.6193 70.5611 36.8009 69.5612 35.9747C68.327 34.9563 67.0719 35.3221 65.5878 37.122L59.8566 32.3855C64.2879 27.0201 69.9348 26.357 75.1344 30.6488ZM61.3828 53.8234C59.704 55.8574 57.1489 56.1021 55.1149 54.4233C53.115 52.7734 52.8703 50.2157 54.5491 48.1843C56.199 46.1845 58.7567 45.9397 60.7566 47.5922C62.7906 49.2684 63.0353 51.8235 61.3828 53.8234Z"
										fill="#CFE6FC"/>
									<path d="M76.5077 30.506C81.3416 34.4951 82.331 39.0685 79.1654 42.9024C75.1762 47.7363 69.3767 44.576 66.4874 48.0757L60.9194 43.4813C65.0191 38.5133 69.1267 41.0078 71.245 38.4422C72.0423 37.4765 71.9344 36.6581 70.9345 35.8319C69.7003 34.8135 68.4452 35.1793 66.9611 36.9792L61.2273 32.2453C65.6612 26.8747 71.3081 26.2116 76.5077 30.506ZM62.7561 53.6806C61.0773 55.7146 58.5222 55.9593 56.4882 54.2805C54.4883 52.6306 54.2436 50.0729 55.9224 48.0415C57.5723 46.0416 60.13 45.7969 62.1299 47.4494C64.1639 49.1256 64.4086 51.6807 62.7561 53.6806Z"
										stroke="#1B334B" stroke-width="1.5" stroke-miterlimit="10"/>
									<path d="M14.8461 23.9229C13.7856 22.073 12.7252 20.2231 11.6647 18.3733C11.4121 17.9338 10.7306 18.3285 10.9832 18.7706C12.0437 20.6205 13.1041 22.4703 14.1645 24.3202C14.4172 24.7623 15.0987 24.3649 14.8461 23.9229Z"
										fill="#1B334B"/>
									<path d="M17.0346 22.8627C16.9057 21.2681 16.7794 19.6708 16.6505 18.0762C16.611 17.5736 15.8216 17.5683 15.8611 18.0762C15.99 19.6708 16.1163 21.2681 16.2452 22.8627C16.2873 23.3679 17.0768 23.3705 17.0346 22.8627Z"
										fill="#1B334B"/>
									<path d="M12.3674 25.4572C11.2912 24.7546 10.2176 24.0494 9.14137 23.3468C8.71508 23.0679 8.31775 23.7521 8.74403 24.0283C9.82027 24.7309 10.8939 25.4361 11.9701 26.1387C12.3938 26.4176 12.7911 25.7335 12.3674 25.4572Z"
										fill="#1B334B"/>
									<path d="M63.754 20.4706C64.3934 19.1075 65.0302 17.7419 65.6696 16.3788C65.8828 15.9209 65.2039 15.521 64.9881 15.9815C64.3487 17.3445 63.7119 18.7102 63.0724 20.0733C62.8567 20.5311 63.5382 20.9311 63.754 20.4706Z"
										fill="#1B334B"/>
									<path d="M65.024 22.4106C66.7818 21.2449 68.5369 20.0819 70.2947 18.9162C70.7157 18.6372 70.321 17.9531 69.8973 18.2346C68.1396 19.4003 66.3844 20.5634 64.6267 21.7291C64.203 22.008 64.5977 22.6922 65.024 22.4106Z"
										fill="#1B334B"/>
									<path d="M66.0904 24.4261C67.5955 24.2129 69.1007 23.9998 70.6084 23.7866C71.1084 23.7156 70.8953 22.9551 70.3979 23.0262C68.8928 23.2393 67.3876 23.4524 65.8798 23.6656C65.3799 23.734 65.593 24.4945 66.0904 24.4261Z"
										fill="#1B334B"/>
									<path d="M50.6624 59.706C51.9308 61.4164 53.2017 63.1268 54.47 64.8346C54.77 65.2372 55.4542 64.8451 55.1516 64.4372C53.8832 62.7268 52.6123 61.0164 51.344 59.3087C51.044 58.9034 50.3572 59.2981 50.6624 59.706Z"
										fill="#1B334B"/>
									<path d="M53.1403 58.175C54.2166 58.8776 55.2902 59.5828 56.3664 60.2854C56.7927 60.5643 57.19 59.8802 56.7637 59.6039C55.6875 58.9013 54.6139 58.1961 53.5377 57.4935C53.1114 57.2146 52.7167 57.8961 53.1403 58.175Z"
										fill="#1B334B"/>
									<path d="M26.5213 58.8527C25.3767 60.6236 24.232 62.3919 23.0874 64.1628C22.8111 64.5917 23.4926 64.9864 23.7689 64.5601C24.9135 62.7892 26.0582 61.0209 27.2029 59.25C27.4791 58.8237 26.7976 58.4264 26.5213 58.8527Z"
										fill="#1B334B"/>
									<path d="M24.8085 57.3504C23.5139 58.1477 22.2192 58.945 20.922 59.7397C20.4904 60.0054 20.8851 60.6896 21.3193 60.4212C22.6139 59.6239 23.9086 58.8266 25.2058 58.0319C25.64 57.7661 25.2427 57.082 24.8085 57.3504Z"
										fill="#1B334B"/>
								</svg>
							</div>
							<h3><?php esc_html_e( 'Your Question', 'tourfic' ); ?></h3>
						</div>

						<form id="ask-question" action="" method="post">
							<div class="tf-aq-field">
								<label for="your-name"><?php esc_html_e( 'Name', 'tourfic' ); ?></label>
								<input type="text" name="your-name" placeholder="<?php esc_attr_e( 'Type full name', 'tourfic' ); ?>" required/>
							</div>
							<div class="tf-aq-field">
								<label for="your-email"><?php esc_html_e( 'Email', 'tourfic' ); ?></label>
								<input type="email" name="your-email" placeholder="<?php esc_attr_e( 'Type email', 'tourfic' ); ?>" required/>
							</div>
							<div class="tf-aq-field">
								<label for="your-question"><?php esc_html_e( 'Question', 'tourfic' ); ?></label>
								<textarea placeholder="<?php esc_attr_e( 'Type here...', 'tourfic' ); ?>" name="your-question" required></textarea>
							</div>
							<div class="tf-aq-field">
								<button type="reset" class="screen-reader-text"><?php esc_html_e( 'Reset', 'tourfic' ); ?></button>
								<button type="submit" form="ask-question" class="tf_btn tf-ask-question-submit"><?php esc_html_e( 'Submit', 'tourfic' ); ?></button>
								<input type="hidden" name="post_id" value="<?php echo esc_attr( get_the_ID() ); ?>" data-post-type="<?php echo esc_attr( get_post_type() ); ?>">
								<?php wp_nonce_field( 'ask_question_nonce' ); ?>
								<div class="response"></div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php
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
		update_user_meta( $user_id, 'language', sanitize_text_field( wp_unslash($_POST['language']) ) );
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
	function taxonomy_template_old( $template ) {

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

	public function taxonomy_template( $template ) {

		if ( ! is_tax() ) {
			return $template;
		}

		$term     = get_queried_object();
		$taxonomy = $term->taxonomy;

		$map = $this->get_taxonomy_post_type_map();

		foreach ( $map as $post_type => $taxonomies ) {

			if ( in_array( $taxonomy, $taxonomies, true ) ) {

				// 1. Theme override path
				$theme_file = "tourfic/{$post_type}/taxonomy.php";
				$theme_tpl  = locate_template( $theme_file, false );

				if ( $theme_tpl ) {
					return $theme_tpl;
				}

				// 2. Plugin fallback
				$plugin_tpl = TF_TEMPLATE_PATH . "{$post_type}/taxonomy.php";

				if ( file_exists( $plugin_tpl ) ) {
					return $plugin_tpl;
				}
			}
		}

		return $template;
	}

	private function get_taxonomy_post_type_map() {
		return [
			'hotel' => [
				'hotel_location',
				'hotel_feature',
				'hotel_type',
			],
			'tour' => [
				'tour_destination',
				'tour_attraction',
				'tour_activities',
				'tour_features',
				'tour_type',
			],
			'apartment' => [
				'apartment_location',
				'apartment_feature',
				'apartment_type',
			],
			'car' => [
				'carrental_location',
				'carrental_brand',
				'carrental_category',
			],
		];
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

		if (is_user_logged_in()) {
			// Handle guest users
			wp_set_current_user(0);
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
		$posttype              = !empty( $_POST['type'] ) ? sanitize_text_field( wp_unslash($_POST['type']) ) : 'tf_hotel';
		$ordering_type 		   = !empty( $_POST["tf_ordering"] ) ? sanitize_text_field(wp_unslash($_POST["tf_ordering"])) : 'default';
		# Separate taxonomy input for filter query
		$place_taxonomy  = $posttype == 'tf_tours' ? 'tour_destination' : ( $posttype == 'tf_apartment' ? 'apartment_location' : 'hotel_location' );
		$filter_taxonomy = $posttype == 'tf_tours' ? 'null' : 'hotel_feature';
		# Take dates for filter query
		$checkin    = isset( $_POST['checkin'] ) ? sanitize_text_field( wp_unslash( $_POST['checkin'] ) ) : array();
		$startprice = ! empty( $_POST['startprice'] ) ? sanitize_text_field( wp_unslash( $_POST['startprice'] ) ) : '';
		$endprice   = ! empty( $_POST['endprice'] ) ? sanitize_text_field( wp_unslash( $_POST['endprice'] ) ) : '';

        //Map Template only
        $mapFilter = !empty($_POST['mapFilter']) ? sanitize_text_field($_POST['mapFilter']) : false;
        $mapCoordinates = !empty($_POST['mapCoordinates']) ? explode(',', sanitize_text_field($_POST['mapCoordinates'])) : [];
        if (!empty($mapCoordinates) && count($mapCoordinates) === 4) {
            list($minLat, $minLng, $maxLat, $maxLng) = $mapCoordinates;
        }

		// Cars Data Start
		$pickup   = isset( $_POST['pickup'] ) ? sanitize_text_field( $_POST['pickup'] ) : '';
		$dropoff = isset( $_POST['dropoff'] ) ? sanitize_text_field( $_POST['dropoff'] ) : '';
		$tf_pickup_date  = ! empty( $_POST['pickup_date'] ) ? tf_normalize_date( sanitize_text_field( $_POST['pickup_date'] ) ) : '';
		$tf_dropoff_date = ! empty( $_POST['dropoff_date'] ) ? tf_normalize_date( sanitize_text_field( $_POST['dropoff_date'] ) ) : '';
		$tf_pickup_time  = isset( $_POST['pickup_time'] ) ? sanitize_text_field( $_POST['pickup_time'] ) : '';
		$tf_dropoff_time  = isset( $_POST['dropoff_time'] ) ? sanitize_text_field( $_POST['dropoff_time'] ) : '';

		$tf_dropoff_same_location  = isset( $_POST['same_location'] ) ? sanitize_text_field( $_POST['same_location'] ) : '';
		if(!empty($tf_dropoff_same_location)){
			$dropoff = $pickup;
		}
		$tf_driver_age  = isset( $_POST['driver_age'] ) ? sanitize_text_field( $_POST['driver_age'] ) : '';

		$tf_category = !empty( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : null;
		$category = !empty( $tf_category ) ? explode( ',', $tf_category ) : null;
		$tf_fuel_type = !empty( $_POST['fuel_type'] ) ? sanitize_text_field( $_POST['fuel_type'] ) : null;
		$fuel_type = !empty( $tf_fuel_type ) ? explode( ',', $tf_fuel_type ) : null;
		$tf_engine_year = !empty( $_POST['engine_year'] ) ? sanitize_text_field( $_POST['engine_year'] ) : null;
		$engine_year = !empty( $tf_engine_year ) ? explode( ',', $tf_engine_year ) : null;

		$tf_startprice  = isset( $_POST['startprice'] ) ? sanitize_text_field( $_POST['startprice'] ) : '';
		$tf_endprice  = isset( $_POST['endprice'] ) ? sanitize_text_field( $_POST['endprice'] ) : '';
		$tf_min_seat  = isset( $_POST['min_seat'] ) ? sanitize_text_field( $_POST['min_seat'] ) : '';
		$tf_max_seat  = isset( $_POST['max_seat'] ) ? sanitize_text_field( $_POST['max_seat'] ) : '';

		$car_driver_min_age = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['car_archive_driver_min_age'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['car_archive_driver_min_age'] : 18;
        $car_driver_max_age = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['car_archive_driver_max_age'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['car_archive_driver_max_age'] : 40;
		// Cars Data End
		
		$elSettings = !empty($_POST['elSettings']) ? json_decode(stripslashes($_POST['elSettings']), true) : [];

		// Author ID if any (single value)
		$tf_author_ids = isset( $_POST['tf_author'] ) ? intval( $_POST['tf_author'] ) : '';

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
				'posts_per_page' => -1,
			);

			if(!empty($tf_author_ids)){
				$args['author'] = $tf_author_ids;
			}
		} else {
			$args = array(
				'post_type'      => $posttype,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			);

			if(!empty($tf_author_ids)){
				$args['author'] = $tf_author_ids;
			}
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
						if ( !empty($total_person) && ! empty( $meta['cont_max_people'] ) && $meta['cont_max_people'] < $total_person && $meta['cont_max_people'] != 0 ) {
							$total_posts --;
							continue;
						}

						//skip the tour if the search form total people less than the maximum number of people in tour
						if ( !empty($total_person) && ! empty( $meta['cont_min_people'] ) && $meta['cont_min_people'] > $total_person && $meta['cont_min_people'] != 0 ) {
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
			$post_per_page = self::tfopt( 'posts_per_page' ) ? absint( wp_unslash(self::tfopt( 'posts_per_page' ))) : 10;

			//elementor settigns
			$post_per_page = !empty($elSettings['posts_per_page']) ? absint( wp_unslash($elSettings['posts_per_page'])) : $post_per_page;
			$el_orderby = !empty($elSettings['orderby'] ) ? $elSettings['orderby'] : '';
			$el_order = !empty($elSettings['order']) ? $elSettings['order'] : '';

			$total_filtered_results = count( $tf_total_filters );
			$current_page           = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
			$offset                 = ( $current_page - 1 ) * $post_per_page;
			// $displayed_results      =  array_slice( $tf_total_filters, $offset, $post_per_page );
			$sorting_data = $this->tf_get_sorting_data( $ordering_type, $tf_total_filters, $posttype );

			$displayed_results = !empty( $sorting_data ) ? $sorting_data : $tf_total_filters;

			if ( ! empty( $displayed_results ) ) {
				$filter_args = array(
					'post_type'      => $posttype,
					'posts_per_page' => $post_per_page,
					'paged' 		 => $current_page,
					'orderby' 		 => array( 'post__in' => 'ASC' ),
					'post__in'       => $displayed_results,
				);

				if(isset($ordering_type)){
					if ( $ordering_type == "default" ) {
						unset( $filter_args['orderby'] );
					} else if ( $ordering_type == 'latest') {
						$filter_args['orderby'] = 'ID';
						$filter_args['order'] = 'DESC';
					}else if ( $ordering_type == 'price-low') {
						$filter_args['orderby'] = array( 'post__in' => 'DESC' );
					}
				} elseif(!empty($el_orderby) || !empty($el_order)){
					$filter_args['orderby'] = $el_orderby;
					$filter_args['order'] = $el_order;
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
										Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice, $elSettings );
									}
								} else {
									[ $adults, $child, $room, $check_in_out ] = $data;
									if ( $hotel_meta["featured"] ) {
										Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, '', '', $elSettings );
									}
								}
							} else {
								if ( $hotel_meta["featured"] ) {
									Hotel::tf_hotel_archive_single_item('', '', '', '', '', '', $elSettings );
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
								$allow_discount    = ! empty( $tour_meta['allow_discount'] ) ? $tour_meta['allow_discount'] : '';
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

                                            <?php if ( !empty($allow_discount) && $discount_type !== 'none' && ! empty( $discount_price ) ) : ?>
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
										Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice, $elSettings );
									}
								} else {
									[ $adults, $child, $check_in_out ] = $data;

									if ( $tour_meta["tour_as_featured"] ) {
										Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out, '', '', $elSettings );
									}
								}
							} else {
								if ( $tour_meta["tour_as_featured"] ) {
									Tour::tf_tour_archive_single_item('', '', '', '', '', $elSettings );
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
										Apartment::tf_apartment_archive_single_item( $data, $elSettings );
									}
								} else {
									if ( $apartment_meta["apartment_as_featured"] ) {
										Apartment::tf_apartment_archive_single_item( $data, $elSettings );
									}
								}
							} else {
								if ( $apartment_meta["apartment_as_featured"] ) {
									Apartment::tf_apartment_archive_single_item([ 1, 0, 0, '' ], $elSettings);
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
										Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice, $elSettings );
									}
								} else {
									[ $adults, $child, $room, $check_in_out ] = $data;

									if ( ! $hotel_meta["featured"] ) {
										Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, '', '', $elSettings );
									}
								}
							} else {
								if ( ! $hotel_meta["featured"] ) {
									Hotel::tf_hotel_archive_single_item('', '', '', '', '', '', $elSettings );
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
								$allow_discount    = ! empty( $tour_meta['allow_discount'] ) ? $tour_meta['allow_discount'] : '';
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

											<?php if ( !empty($allow_discount) && $discount_type !== 'none' && ! empty( $discount_price ) ) : ?>
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
										Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice, $elSettings );
									}
								} else {
									[ $adults, $child, $check_in_out ] = $data;
									if ( ! $tour_meta["tour_as_featured"] ) {
										Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out, '', '', $elSettings );
									}
								}
							} else {
								if ( ! $tour_meta["tour_as_featured"] ) {
									Tour::tf_tour_archive_single_item('', '', '', '', '', $elSettings );
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
										Apartment::tf_apartment_archive_single_item( $data, $elSettings );
									}
								} else {
									if ( ! $apartment_meta["apartment_as_featured"] ) {
										Apartment::tf_apartment_archive_single_item( $data, $elSettings );
									}
								}
							} else {
								if ( ! $apartment_meta["apartment_as_featured"] ) {
									Apartment::tf_apartment_archive_single_item([ 1, 0, 0, '' ], $elSettings);
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
				( ($posttype == 'tf_hotel' && Hotel::template( 'archive' ) == 'design-3') ||
				($posttype == 'tf_tours' && Tour::template( 'archive' ) == 'design-3') ||
				($posttype == 'tf_apartment' && Apartment::template( 'archive' ) == 'design-2') ) ) {
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

	/**
	 * Search form date time slot availability
	 *
	 * Author: Mofazzal Hossain
	 *
	 * Ajax function
	 */
	function tf_car_time_slots_callback() {
		$pickup_day = isset($_POST['pickup_day']) ? sanitize_text_field($_POST['pickup_day']) : '';
		$drop_day   = isset($_POST['drop_day']) ? sanitize_text_field($_POST['drop_day']) : '';

		$car_time_slots = !empty(Helper::tfopt('car_time_slots')) ? Helper::tfopt('car_time_slots') : '';
		$unserialize_car_time_slots = !empty($car_time_slots) ? unserialize($car_time_slots) : array();

		$pickup_time = $drop_time = '';

		if (!empty($unserialize_car_time_slots)) {
			foreach ($unserialize_car_time_slots as $slot) {
				if (isset($slot['day'])) {
					if (strtolower($slot['day']) == strtolower($pickup_day)) {
						$pickup_time = $slot['pickup_time'];
					}
					if (strtolower($slot['day']) == strtolower($drop_day)) {
						$drop_time = $slot['drop_time'];
					}
				}
			}
		}
		wp_send_json(array(
			'pickup_time' => $pickup_time,
			'drop_time'   => $drop_time,
		));
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

		$term_name = !empty($_POST['termName']) ? sanitize_text_field( $_POST['termName'] ) : 'tf_hotel';

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
					'title'  => esc_html__( 'Visit Vendor Dashboard', 'tourfic' ),
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
			<p>
			<?php
			// translators: %1$s opening strong tag, %2$s closing strong tag, highlighting the "QUICK CHECKOUT" feature.
			echo wp_kses_post( sprintf( esc_html__(
						'Instantio plugin is required for the %1$s"QUICK CHECKOUT"%2$s feature of Tourfic. Please install and activate Instantio to ensure this feature works seamlessly.',
						'tourfic'
					),
					'<strong>',
					'</strong>'
				)
			);
			?>

			</p>
			<p>
				<a
					class="install-now button inc-install"
					href="<?php echo esc_url( admin_url( '/plugin-install.php?s=slug:instantio&tab=search&type=term' ) ); ?>"
					data-plugin-slug="tourfic"
				>
					<?php esc_attr_e( 'Install Now', 'tourfic' ); ?>
				</a>
			</p>

			</div>
		<?php
		} else {
			// translators: 1: opening <strong><a> tag, 2: closing </a></strong> tag, 3: opening <b> tag, 4: closing </b> tag.
			$notice = sprintf( esc_html__( 'The %1$sInstantio%2$s plugin is inactive. Please activate it to enable the %3$s "QUICK CHECKOUT" %4$s for Tourfic.', 'tourfic' ),
				'<strong><a href="https://wordpress.org/plugins/instantio/" target="_blank">',
				'</a></strong>',
				'<b>',
				'</b>'
			);

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


	function tf_get_min_max_price_callback() {

		// Check nonce security
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ), 'tf_ajax_nonce' ) ) {
			wp_send_json_error();
			return;
		}

		$post_type = !empty( $_POST['post_type']) ? sanitize_text_field( $_POST['post_type'] ) : '';
		$response = array();

		if( $post_type == 'tf_hotel' ) {
			$response[$post_type] = Hotel_Pricing::get_min_max_price_from_all_hotel();
		} else if( $post_type == 'tf_tours' ) {
			$response[$post_type] = Tour_Pricing::get_min_max_price_from_all_tour();
		} else if( $post_type == 'tf_apartment' ) {
			$response[$post_type] = Apt_Pricing::get_min_max_price_from_all_apartment();
		} else if( $post_type == 'tf_carrental' ) {
			$response[$post_type] = get_cars_min_max_price();
		}

		wp_send_json_success( $response );
		wp_die();
	}
}
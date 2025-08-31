<?php 

namespace Tourfic\Classes\Apartment;

# don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;
use Tourfic\Classes\Apartment\Pricing as Apt_Pricing;
use Tourfic\App\TF_Review;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Icons_Manager;

class Apartment {

	use \Tourfic\Traits\Singleton;

	public function __construct() {

		if ( Helper::tf_is_woo_active() ) {
			if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-apartment.php' ) ) {
				require_once TF_INC_PATH . 'functions/woocommerce/wc-apartment.php';
			} else {
				tf_file_missing( TF_INC_PATH . 'functions/woocommerce/wc-apartment.php' );
			}
		}

		add_action( 'wp_ajax_tf_apt_room_details_qv', array( $this, 'tf_apartment_room_quick_view' ) );
		add_action( 'wp_ajax_nopriv_tf_apt_room_details_qv', array( $this, 'tf_apartment_room_quick_view' ) );
		add_action( 'wp_after_insert_post', array( $this, 'tf_apartment_feature_assign_taxonomies' ), 100, 3 );
		add_action( 'wp_ajax_tf_apartments_search', array( $this, 'tf_apartments_search_ajax_callback' ) );
		add_action( 'wp_ajax_nopriv_tf_apartments_search', array( $this, 'tf_apartments_search_ajax_callback' ) );

		// apartmet CPT
		Apartment_CPT::instance();
	}

	function tf_apartment_room_quick_view() {
		// Check nonce security
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['_nonce'])), 'tf_ajax_nonce' ) ) {
			return;
		}
		$meta = !empty($_POST['post_id']) ? get_post_meta( sanitize_text_field( wp_unslash($_POST['post_id']) ), 'tf_apartment_opt', true ) : [];
		// Single Template Style
		$tf_apartment_layout_conditions = ! empty( $meta['tf_single_apartment_layout_opt'] ) ? $meta['tf_single_apartment_layout_opt'] : 'global';
		if("single"==$tf_apartment_layout_conditions){
			$tf_apartment_single_template = ! empty( $meta['tf_single_apartment_template'] ) ? $meta['tf_single_apartment_template'] : 'default';
		}
		$tf_apartment_global_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-apartment'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-apartment'] : 'default';

		$tf_apartment_selected_check = !empty($tf_apartment_single_template) ? $tf_apartment_single_template : $tf_apartment_global_template;

		$tf_apartment_selected_template = $tf_apartment_selected_check;
		if('default'==$tf_apartment_selected_template){
		?>
        <div class="tf-hotel-quick-view" style="display: flex">
			<?php

			foreach ( Helper::tf_data_types( $meta['rooms'] ) as $key => $room ) :
				if ( $key == sanitize_text_field( wp_unslash($_POST['id'] )) ):  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
					$tf_room_gallery = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
					?>
                    <div class="tf-hotel-details-qc-gallelry" style="width: 545px;">
						<?php if ( ! empty( $tf_room_gallery ) ) :
							$tf_room_gallery_ids = explode( ',', $tf_room_gallery );
							?>

                            <div class="tf-details-qc-slider tf-details-qc-slider-single tf-slick-slider">
								<?php
								if ( ! empty( $tf_room_gallery_ids ) ) {
									foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
										?>
                                        <div class="tf-details-qcs">
											<?php
											$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
											echo '<img src="' . esc_url( $image_url ) . '" alt="">';
											?>
                                        </div>
									<?php }
								} ?>
                            </div>
                            <div class="tf-details-qc-slider tf-details-qc-slider-nav tf-slick-slider">
								<?php
								if ( ! empty( $tf_room_gallery_ids ) ) {
									foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
										?>
                                        <div class="tf-details-qcs">
											<?php
											$image_url = wp_get_attachment_url( $gallery_item_id, 'thumbnail' );
											echo '<img src="' . esc_url( $image_url ) . '" alt="">';
											?>
                                        </div>
									<?php }
								} ?>
                            </div>
						<?php else : ?>
                        <img src="<?php echo esc_url( $room['thumbnail'] ) ?>" alt="room-thumbnail">
						<?php endif; ?>
                    </div>
                    <div class="tf-hotel-details-info" style="width:440px; padding-left: 35px;max-height: 470px;padding-top: 25px; overflow-y: auto;">
						<?php
						$footage       = ! empty( $room['footage'] ) ? $room['footage'] : '';
						$bed           = ! empty( $room['bed'] ) ? $room['bed'] : '';
						$adult_number  = ! empty( $room['adult'] ) ? $room['adult'] : '0';
						$child_number  = ! empty( $room['child'] ) ? $room['child'] : '0';
						$infant_number = ! empty( $room['infant'] ) ? $room['infant'] : '0';
						?>
                        <h3><?php echo esc_html( $room['title'] ); ?></h3>
                        <p><?php echo esc_html( $room['description'] ); ?></p>
                        <div class="tf-room-title description">
							<?php if ( $footage ) { ?>
                                <div class="tf-tooltip tf-d-ib">
                                    <div class="room-detail-icon">
                                        <span class="room-icon-wrap"><i class="fas fa-ruler-combined"></i></span>
                                        <span class="icon-text tf-d-b"><?php echo esc_html( $footage ); ?><?php esc_html_e( 'sft', 'tourfic' ); ?></span>
                                    </div>
                                    <div class="tf-top">
										<?php esc_html_e( 'Room Footage', 'tourfic' ); ?>
                                        <i class="tool-i"></i>
                                    </div>
                                </div>
							<?php }
							if ( $bed ) { ?>
                                <div class="tf-tooltip tf-d-ib">
                                    <div class="room-detail-icon">
                                        <span class="room-icon-wrap"><i class="fas fa-bed"></i></span>
                                        <span class="icon-text tf-d-b">x<?php echo esc_html( $bed ); ?></span>
                                    </div>
                                    <div class="tf-top">
										<?php esc_html_e( 'Number of Beds', 'tourfic' ); ?>
                                        <i class="tool-i"></i>
                                    </div>
                                </div>
							<?php } ?>
							<?php if ( $adult_number ) { ?>
                                <div class="tf-tooltip tf-d-ib">
                                    <div class="room-detail-icon">
                                        <span class="room-icon-wrap"><i class="fas fa-male"></i><i class="fas fa-female"></i></span>
                                        <span class="icon-text tf-d-b">x<?php echo esc_html( $adult_number ); ?></span>
                                    </div>
                                    <div class="tf-top">
										<?php esc_html_e( 'Number of Adults', 'tourfic' ); ?>
                                        <i class="tool-i"></i>
                                    </div>
                                </div>
							<?php }
							if ( $child_number ) { ?>
                                <div class="tf-tooltip tf-d-ib">
                                    <div class="room-detail-icon">
                                        <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                                        <span class="icon-text tf-d-b">x<?php echo esc_html( $child_number ); ?></span>
                                    </div>
                                    <div class="tf-top">
										<?php esc_html_e( 'Number of Children', 'tourfic' ); ?>
                                        <i class="tool-i"></i>
                                    </div>
                                </div>
							<?php }
							if ( $infant_number ) { ?>
                                <div class="tf-tooltip tf-d-ib">
                                    <div class="room-detail-icon">
                                        <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                                        <span class="icon-text tf-d-b">x<?php echo esc_html( $infant_number); ?></span>
                                    </div>
                                    <div class="tf-top">
										<?php esc_html_e( 'Number of Infants', 'tourfic' ); ?>
                                        <i class="tool-i"></i>
                                    </div>
                                </div>
							<?php } ?>
                        </div>
                    </div>
				<?php
				endif;
			endforeach;
			?>
        </div>
		<?php } 
		if('design-1'==$tf_apartment_selected_template){ 
			foreach ( Helper::tf_data_types( $meta['rooms'] ) as $key => $room ) :
				if ( $key == sanitize_text_field( wp_unslash($_POST['id'] )) ): // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				$tf_room_gallery = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
				$tf_room_gallery_ids = !empty($tf_room_gallery) ? explode( ',', $tf_room_gallery ) : '';
				$footage       = ! empty( $room['footage'] ) ? $room['footage'] : '';
				$bed           = ! empty( $room['bed'] ) ? $room['bed'] : '';
				$adult_number  = ! empty( $room['adult'] ) ? $room['adult'] : '0';
				$child_number  = ! empty( $room['child'] ) ? $room['child'] : '0';
				$infant_number = ! empty( $room['infant'] ) ? $room['infant'] : '0';
			?>
			<div class="tf-popup-inner">
				<div class="tf-popup-body">
					<div class="tf-popup-left">
						<?php 
						$post_id = isset( $_POST['post_id'] ) ? intval( wp_unslash( $_POST['post_id'] ) ) : 0;

						if ( ! empty( $tf_room_gallery_ids ) ) {
						foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
						$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
						if(!empty($image_url)){
						?>
						<img src="<?php echo esc_url($image_url); ?>" alt="<?php esc_html_e("Room Image","tourfic"); ?>" class="tf-popup-image">
						<?php } } }else{ 
						$aprt_thumbnail_url = get_the_post_thumbnail_url( $post_id );
						if(!empty($aprt_thumbnail_url)){	
						?>
						<img src="<?php echo esc_url($aprt_thumbnail_url); ?>" alt="<?php esc_html_e("Room Image","tourfic"); ?>" class="tf-popup-image">
						<?php }} ?>
					</div>
					<div class="tf-popup-right">
						<span class="tf-popup-info-title"><?php esc_html_e("Room details", "tourfic"); ?></span>
						<ul>
							<?php if ( $footage ) { ?>
								<li><i class="ri-pencil-ruler-2-line"></i> <?php echo esc_html( $footage ); ?><?php esc_html_e( 'sft', 'tourfic' ); ?></li>
							<?php } ?>
							<?php if ( $bed ) { ?>
								<li><i class="ri-hotel-bed-line"></i> <?php echo esc_html( $bed ); ?><?php esc_html_e( ' Beds', 'tourfic' ); ?></li>
							<?php } ?>
							<?php if ( $adult_number ) { ?>
								<li><i class="ri-user-2-line"></i> <?php echo esc_html( $adult_number ); ?><?php esc_html_e( ' Adults', 'tourfic' ); ?></li>
							<?php } ?>
							<?php if ( $child_number ) { ?>
								<li><i class="ri-user-smile-line"></i><?php echo esc_html( $child_number ); ?><?php esc_html_e( ' Child', 'tourfic' ); ?></li>
							<?php } ?>
							<?php if ( $infant_number ) { ?>
								<li><i class="ri-user-smile-line"></i><?php echo esc_html( $infant_number ); ?><?php esc_html_e( ' Infant', 'tourfic' ); ?></li>
							<?php } ?>                     
						</ul> 
					</div>
				</div>                
				<div class="tf-popup-close">
					<i class="fa-solid fa-xmark"></i>
				</div>
			</div>
		<?php 
		endif;
		endforeach;	
		}
		wp_die();
	}

	function tf_apartment_feature_assign_taxonomies( $post_id, $post, $old_status ) {
		if ( 'tf_apartment' !== $post->post_type ) {
			return;
		}
		$meta = get_post_meta( $post_id, 'tf_apartment_opt', true );
		if ( isset( $meta['amenities'] ) && ! empty( Helper::tf_data_types( $meta['amenities'] ) ) ) {
			$apartment_features = array();
			foreach ( Helper::tf_data_types( $meta['amenities'] ) as $amenity ) {
				$apartment_features[] = intval( $amenity['feature'] );
			}
			wp_set_object_terms( $post_id, $apartment_features, 'apartment_feature' );
		}
	}

	function tf_apartments_search_ajax_callback() {
		// Check nonce security
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['_nonce'])), 'tf_ajax_nonce' ) ) {
			return;
		}
		
		$response = [
			'status'  => 'error',
			'message' => '',
		];

		// Validation
		if ( Helper::tfopt( 'date_apartment_search' ) && empty( $_POST['check-in-out-date'] ) ) {
			$response['message'] = esc_html__( 'Please select a date', 'tourfic' );
		} else {
			// Whitelist allowed fields
			$allowed_fields = [
				'place-name',
				'place',
				'adults',
				'children',
				'infant',
				'check-in-out-date',
				'type',
				'types',
				'features',
				'from',
				'to',
				'_nonce',
			];

			$fields = [];
			foreach ( $allowed_fields as $key ) {
				if ( isset( $_POST[ $key ] ) ) {
					if ( is_array( $_POST[ $key ] ) ) {
						$fields[ $key ] = array_map( 'sanitize_text_field', wp_unslash( $_POST[ $key ] ) );
					} else {
						$fields[ $key ] = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
					}
				}
			}

			$response['query_string'] = http_build_query( $fields );
			$response['status']       = 'success';
		}

		echo wp_json_encode( $response );
		wp_die();
	}

	public static function tf_apartment_search_form_horizontal( $classes, $title, $subtitle, $advanced, $design ) {
		
		// Check-in & out date
		$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( wp_unslash( $_GET['check-in-out-date'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		// date format for apartments
		$date_format_change_apartments = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";

		$disable_apartment_child_search  = ! empty( Helper::tfopt( 'disable_apartment_child_search' ) ) ? Helper::tfopt( 'disable_apartment_child_search' ) : '';
		$disable_apartment_infant_search  = ! empty( Helper::tfopt( 'disable_apartment_infant_search' ) ) ? Helper::tfopt( 'disable_apartment_infant_search' ) : '';
		if( !empty($design) && 2==$design ){
		?>
		<form class="tf_booking-widget-design-2 tf_hotel-shortcode-design-2" id="tf_apartment_booking" method="get" autocomplete="off" action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>">
			<div class="tf_hotel_searching">
				<div class="tf_form_innerbody">
					<div class="tf_form_fields">
						<div class="tf_destination_fields">
							<label class="tf_label_location">
								<span class="tf-label"><?php esc_html_e( 'Location', 'tourfic' ); ?></span>
								<div class="tf_form_inners tf_form-inner">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
									<path d="M8 13.9317L11.2998 10.6318C13.1223 8.80943 13.1223 5.85464 11.2998 4.0322C9.4774 2.20975 6.52261 2.20975 4.70017 4.0322C2.87772 5.85464 2.87772 8.80943 4.70017 10.6318L8 13.9317ZM8 15.8173L3.75736 11.5747C1.41421 9.2315 1.41421 5.43254 3.75736 3.08939C6.10051 0.746245 9.89947 0.746245 12.2427 3.08939C14.5858 5.43254 14.5858 9.2315 12.2427 11.5747L8 15.8173ZM8 8.66536C8.7364 8.66536 9.33333 8.06843 9.33333 7.33203C9.33333 6.59565 8.7364 5.9987 8 5.9987C7.2636 5.9987 6.66667 6.59565 6.66667 7.33203C6.66667 8.06843 7.2636 8.66536 8 8.66536ZM8 9.9987C6.52724 9.9987 5.33333 8.80476 5.33333 7.33203C5.33333 5.85927 6.52724 4.66536 8 4.66536C9.47273 4.66536 10.6667 5.85927 10.6667 7.33203C10.6667 8.80476 9.47273 9.9987 8 9.9987Z" fill="#FAEEDD"/>
									</svg>
									<input type="text" required="" name="place-name" id="tf-apartment-location" class="" placeholder="<?php esc_html_e( 'Enter Location', 'tourfic' ); ?>" value="">
                                    <input type="hidden" name="place" class="tf-place-input">
								</div>
							</label>
						</div>
						
						<div class="tf_checkin_date">
							<label class="tf_label_checkin tf_apartment_check_in_out_date">
								<span class="tf-label"><?php esc_html_e( 'Check in', 'tourfic' ); ?></span>
								<div class="tf_form_inners">
									<div class="tf_checkin_dates">
										<span class="date"><?php echo esc_html( gmdate('d') ); ?></span>
										<span class="month">
											<span><?php echo esc_html( gmdate('M') ); ?></span>
											<div class="tf_check_arrow">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
												<path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
												</svg>
											</div>
										</span>
									</div>
								</div>
							</label>

							<input type="text" name="check-in-out-date" class="tf-apartment-check-in-out-date" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Check-in - Check-out', 'tourfic' ); ?>" <?php echo Helper::tfopt( 'date_apartment_search' ) ? 'required' : ''; ?>>
						</div>
						
						<div class="tf_checkin_date tf_apartment_check_in_out_date">
							<label class="tf_label_checkin">
								<span class="tf-label"><?php esc_html_e( 'Check Out', 'tourfic' ); ?></span>
								<div class="tf_form_inners">
									<div class="tf_checkout_dates">
										<span class="date"><?php echo esc_html( gmdate('d') ); ?></span>
										<span class="month">
											<span><?php echo esc_html( gmdate('M') ); ?></span>
											<div class="tf_check_arrow">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
												<path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
												</svg>
											</div>
										</span>
									</div>
								</div>
							</label>
						</div>

						<div class="tf_guest_info tf_selectperson-wrap">
							<label class="tf_label_checkin tf_input-inner">
								<span class="tf-label"><?php esc_html_e( 'Guests', 'tourfic' ); ?></span>
								<div class="tf_form_inners">
									<div class="tf_guest_calculation">
										<div class="tf_guest_number">
											<span class="guest"><?php esc_html_e( '1', 'tourfic' ); ?></span>
											<span class="label"><?php esc_html_e( 'Guests', 'tourfic' ); ?></span>
										</div>
									</div>
									<div class="tf_check_arrow">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
										<path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
										</svg>
									</div>
								</div>
							</label>

							<div class="tf_acrselection-wrap">
								<div class="tf_acrselection-inner">
									<div class="tf_acrselection">
										<div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
										<div class="acr-select">
											<div class="acr-dec">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
												<g clip-path="url(#clip0_3229_13094)">
													<rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
												</g>
												<defs>
													<clipPath id="clip0_3229_13094">
													<rect width="20" height="20" fill="white"/>
													</clipPath>
												</defs>
												</svg>
											</div>
											<input type="tel" name="adults" class="adults-style2" id="adults" min="1" value="1" readonly />
											<div class="acr-inc">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
												<g clip-path="url(#clip0_3229_13100)">
													<path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
												</g>
												<defs>
													<clipPath id="clip0_3229_13100">
													<rect width="20" height="20" fill="white"/>
													</clipPath>
												</defs>
												</svg>
											</div>
										</div>
									</div>
									<?php if ( empty($disable_apartment_child_search) ): ?>
										<div class="tf_acrselection">
											<div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
											<div class="acr-select">
												<div class="acr-dec">
													<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
													<g clip-path="url(#clip0_3229_13094)">
														<rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
													</g>
													<defs>
														<clipPath id="clip0_3229_13094">
														<rect width="20" height="20" fill="white"/>
														</clipPath>
													</defs>
													</svg>
												</div>
												<input type="tel" name="children" class="childs-style2" id="children" min="0" value="0" readonly />
												<div class="acr-inc">
													<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
													<g clip-path="url(#clip0_3229_13100)">
														<path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
													</g>
													<defs>
														<clipPath id="clip0_3229_13100">
														<rect width="20" height="20" fill="white"/>
														</clipPath>
													</defs>
													</svg>
												</div>
											</div>
										</div>
									<?php endif; ?>
									<?php if ( empty($disable_apartment_infant_search) ): ?>
										<div class="tf_acrselection">
											<div class="acr-label"><?php esc_html_e( 'Infant', 'tourfic' ); ?></div>
											<div class="acr-select">
												<div class="acr-dec">
													<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
													<g clip-path="url(#clip0_3229_13094)">
														<rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
													</g>
													<defs>
														<clipPath id="clip0_3229_13094">
														<rect width="20" height="20" fill="white"/>
														</clipPath>
													</defs>
													</svg>
												</div>
												<input type="tel" name="infant" class="infant-style2" id="infant" min="0" value="0" readonly />
												<div class="acr-inc">
													<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
													<g clip-path="url(#clip0_3229_13100)">
														<path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
													</g>
													<defs>
														<clipPath id="clip0_3229_13100">
														<rect width="20" height="20" fill="white"/>
														</clipPath>
													</defs>
													</svg>
												</div>
											</div>
										</div>
									<?php endif; ?>
								</div>
							</div>

						</div>
					</div>
					<div class="tf_availability_checker_box">
						<input type="hidden" name="type" value="tf_apartment" class="tf-post-type"/>
						<button><?php echo esc_html_e("Check availability", "tourfic"); ?></button>
					</div>
				</div>
			</div>

		</form>
		<?php }elseif( !empty($design) && 3==$design ){ ?>
			<form class="tf-archive-search-box-wrapper <?php echo esc_attr( $classes ); ?>" id="tf_apartment_booking" method="get" autocomplete="off" action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>">
				<div class="tf-date-selection-form">
				<div class="tf-date-select-box tf-flex tf-flex-gap-8">
					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn tf-pick-drop-location full-width">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_257_3711)">
                                        <path d="M7.36246 11.6666H4.16663C3.99707 11.6759 3.83438 11.7367 3.70034 11.8409C3.56631 11.9452 3.46732 12.0879 3.41663 12.25L1.74996 17.25C1.66663 17.3333 1.66663 17.4166 1.66663 17.5C1.66663 18 1.99996 18.3333 2.49996 18.3333H17.5C18 18.3333 18.3333 18 18.3333 17.5C18.3333 17.4166 18.3333 17.3333 18.25 17.25L16.5833 12.25C16.5326 12.0879 16.4336 11.9452 16.2996 11.8409C16.1655 11.7367 16.0028 11.6759 15.8333 11.6666H12.6375M15 6.66663C15 10.4166 9.99996 14.1666 9.99996 14.1666C9.99996 14.1666 4.99996 10.4166 4.99996 6.66663C4.99996 5.34054 5.52674 4.06877 6.46442 3.13109C7.40211 2.19341 8.67388 1.66663 9.99996 1.66663C11.326 1.66663 12.5978 2.19341 13.5355 3.13109C14.4732 4.06877 15 5.34054 15 6.66663ZM11.6666 6.66663C11.6666 7.5871 10.9204 8.33329 9.99996 8.33329C9.07948 8.33329 8.33329 7.5871 8.33329 6.66663C8.33329 5.74615 9.07948 4.99996 9.99996 4.99996C10.9204 4.99996 11.6666 5.74615 11.6666 6.66663Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_257_3711">
                                        <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                    </defs>
                                    </svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Location", "tourfic"); ?></h5>
									<input type="text" required="" name="place-name" id="tf-apartment-location" class="" placeholder="<?php esc_html_e( 'Enter Location', 'tourfic' ); ?>" value="">
                                    <input type="hidden" name="place" class="tf-place-input">
								</div>
							</div>
						</div>

					</div>

					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn full-width">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Check-in & Check-out Date", "tourfic"); ?></h5>
									<input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                           placeholder="<?php esc_attr_e( 'Check-in - Check-out', 'tourfic' ); ?>" <?php echo Helper::tfopt( 'date_apartment_search' ) ? 'required' : ''; ?>>
								</div>
							</div>
						</div>

					</div>

					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn full-width">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M9.99992 10.8333C12.3011 10.8333 14.1666 8.96785 14.1666 6.66667C14.1666 4.36548 12.3011 2.5 9.99992 2.5C7.69873 2.5 5.83325 4.36548 5.83325 6.66667C5.83325 8.96785 7.69873 10.8333 9.99992 10.8333ZM9.99992 10.8333C11.768 10.8333 13.4637 11.5357 14.714 12.786C15.9642 14.0362 16.6666 15.7319 16.6666 17.5M9.99992 10.8333C8.23181 10.8333 6.53612 11.5357 5.28587 12.786C4.03563 14.0362 3.33325 15.7319 3.33325 17.5" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Guests", "tourfic"); ?></h5>
									<div class="tf_selectperson-wrap">
										<div class="tf_input-inner">
											<div class="adults-text"><?php esc_html_e( '1 Adults', 'tourfic' ); ?></div>
											<?php if ( empty( $disable_apartment_child_search ) ): ?>
												<div class="person-sep"></div>
												<div class="child-text"><?php esc_html_e( '0 Children', 'tourfic' ); ?></div>
											<?php endif; ?>
											<?php if ( empty( $disable_apartment_infant_search ) ): ?>
												<div class="person-sep"></div>
												<div class="infant-text"><?php esc_html_e( '0 Infant', 'tourfic' ); ?></div>
											<?php endif; ?>
										</div>
										<div class="tf_acrselection-wrap">
											<div class="tf_acrselection-inner">
												<div class="tf_acrselection">
													<div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
													<div class="acr-select">
														<div class="acr-dec">
															-
														</div>
														<input type="tel" name="adults" class="adults-style2" id="adults" min="1" value="1" readonly />
														<div class="acr-inc">
															+
														</div>
													</div>
												</div>
												<?php if ( empty($disable_apartment_child_search) ): ?>
													<div class="tf_acrselection">
														<div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
														<div class="acr-select">
															<div class="acr-dec">
																-
															</div>
															<input type="tel" name="children" class="childs-style2" id="children" min="0" value="0" readonly />
															<div class="acr-inc">
																+
															</div>
														</div>
													</div>
												<?php endif; ?>
												<?php if ( empty($disable_apartment_infant_search) ): ?>
													<div class="tf_acrselection">
														<div class="acr-label"><?php esc_html_e( 'Infant', 'tourfic' ); ?></div>
														<div class="acr-select">
															<div class="acr-dec">
																-
															</div>
															<input type="tel" name="infant" class="infant-style2" id="infant" min="0" value="0" readonly />
															<div class="acr-inc">
																+
															</div>
														</div>
													</div>
												<?php endif; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>

				<div class="tf-driver-location-box">
					<div class="tf-submit-button">
						<input type="hidden" name="type" value="tf_apartment" class="tf-post-type"/>
						<button type="submit" class="tf_btn tf-flex-align-center"><?php echo esc_html( apply_filters("tf_apartment_search_form_submit_button_text", 'Search' )); ?> <i class="ri-search-line"></i></button>
					</div>
				</div>
				</div>
            </form>
        <?php } elseif (!empty($design) && 4 == $design) { ?>
            <form class="tf-archive-search-box-wrapper tf-search__form tf-shortcode-design-4 <?php echo esc_attr($classes); ?>" id="tf_apartment_booking" method="get" autocomplete="off" action="<?php echo esc_url(Helper::tf_booking_search_action()); ?>">
                <fieldset class="tf-search__form__fieldset">
                    <!-- Location -->
                    <div class="tf-search__form__fieldset__left">
                        <label for="tf-search__form-location" class="tf-search__form__label">
                            <?php echo esc_html_e("Locations", "tourfic"); ?>
                        </label>
                        <div class="tf-search__form__field">
                            <input type="text" required="" name="place-name" id="tf-apartment-location" class="tf-search__form__input" placeholder="<?php esc_html_e('Where you wanna stay?', 'tourfic'); ?>" value="">
                            <input type="hidden" name="place" class="tf-place-input">
                            <span class="tf-search__form__field__icon icon--location">
								<svg width="12" height="17" viewBox="0 0 12 17" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M5.25 15.625C3.625 13.5938 0 8.75 0 6C0 2.6875 2.65625 0 6 0C9.3125 0 12 2.6875 12 6C12 8.75 8.34375 13.5938 6.71875 15.625C6.34375 16.0938 5.625 16.0938 5.25 15.625ZM6 8C7.09375 8 8 7.125 8 6C8 4.90625 7.09375 4 6 4C4.875 4 4 4.90625 4 6C4 7.125 4.875 8 6 8Z" fill="white" />
								</svg>
							</span>
                        </div>
                    </div>

                    <div class="tf-search__form__fieldset__middle">
                        <!-- Adult Person -->
                        <div class="tf-search__form__group tf_selectperson-wrap">
                            <label for="tf-search__form-adult" class="tf-search__form__label">
                                <?php echo esc_html_e('Adult Person', 'tourfic'); ?>
                            </label>
                            <div class="tf-search__form__field tf-mx-width">
                                <div class="tf-search__form__field__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="41" height="40" viewBox="0 0 41 40" fill="none">
                                        <path d="M20.2222 20C22.3439 20 24.3787 19.1571 25.879 17.6569C27.3793 16.1566 28.2222 14.1217 28.2222 12C28.2222 9.87827 27.3793 7.84344 25.879 6.34315C24.3787 4.84285 22.3439 4 20.2222 4C18.1004 4 16.0656 4.84285 14.5653 6.34315C13.065 7.84344 12.2222 9.87827 12.2222 12C12.2222 14.1217 13.065 16.1566 14.5653 17.6569C16.0656 19.1571 18.1004 20 20.2222 20ZM17.3659 23C11.2097 23 6.22217 27.9875 6.22217 34.1437C6.22217 35.1687 7.05342 36 8.07842 36H32.3659C33.3909 36 34.2222 35.1687 34.2222 34.1437C34.2222 27.9875 29.2347 23 23.0784 23H17.3659Z" fill="#3E64E0" />
                                    </svg>
                                </div>
                                <div class="tf-search__form__field__incdec">
                                    <input type="number" name="adults" id="adults" class="tf-search__form__field__input field--title" min="1" value="1">
                                    <span class="tf-search__form__field__incdre__inc form--span acr-inc">
										<svg xmlns="http://www.w3.org/2000/svg" width="33" height="25" viewBox="0 0 33 25" fill="none">
											<rect x="1.25" y="1" width="31" height="23" rx="5.5" stroke="#3E64E0" />
											<path d="M10.75 12.9998H22.4167M16.5833 7.1665V18.8332" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
									</span>
                                    <span class="tf-search__form__field__incdre__dec form--span acr-dec">
										<svg xmlns="http://www.w3.org/2000/svg" width="33" height="24" viewBox="0 0 33 24" fill="none">
											<rect x="0.722168" y="0.5" width="31" height="23" rx="5.5" stroke="#3E64E0" />
											<path d="M10.2222 12.5H21.8888" stroke="white" stroke-width="2" stroke-linecap="round" stroke-line join="round" />
										</svg>
									</span>
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="tf-search__form__divider"></div>

                        <!-- Children -->
                        <?php if (empty($disable_apartment_child_search)) : ?>
                            <div class="tf-search__form__group tf_selectperson-wrap">
                                <label for="tf-search__form-children" class="tf-search__form__label">
                                    <?php echo esc_html_e('Children', 'tourfic'); ?>
                                </label>
                                <div class="tf-search__form__field tf-mx-width">
                                    <div class="tf-search__form__field__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="40" viewBox="0 0 26 40" fill="none">
                                            <path d="M7.99873 5C7.99873 3.67392 8.52552 2.40215 9.4632 1.46447C10.4009 0.526784 11.6727 0 12.9987 0C14.3248 0 15.5966 0.526784 16.5343 1.46447C17.472 2.40215 17.9987 3.67392 17.9987 5C17.9987 6.32608 17.472 7.59785 16.5343 8.53553C15.5966 9.47322 14.3248 10 12.9987 10C11.6727 10 10.4009 9.47322 9.4632 8.53553C8.52552 7.59785 7.99873 6.32608 7.99873 5ZM11.7487 30V37.5C11.7487 38.8828 10.6315 40 9.24873 40C7.86592 40 6.74873 38.8828 6.74873 37.5V22.4844L5.11592 25.0781C4.38155 26.25 2.83467 26.5938 1.67061 25.8594C0.506547 25.125 0.147172 23.5859 0.881547 22.4219L3.99873 17.4766C5.94405 14.375 9.34248 12.5 12.9987 12.5C16.655 12.5 20.0534 14.375 21.9987 17.4688L25.1159 22.4219C25.8503 23.5938 25.4987 25.1328 24.3347 25.8672C23.1706 26.6016 21.6237 26.25 20.8894 25.0859L19.2487 22.4844V37.5C19.2487 38.8828 18.1315 40 16.7487 40C15.3659 40 14.2487 38.8828 14.2487 37.5V30H11.7487Z" fill="#3E64E0" />
                                        </svg>
                                    </div>
                                    <div class="tf-search__form__field__incdec">
                                        <input type="number" name="children" id="children" class="tf-search__form__field__input field--title" min="0" value="0">
                                        <span class="tf-search__form__field__incdre__inc form--span acr-inc">
											<svg xmlns="http://www.w3.org/2000/svg" width="33" height="25" viewBox="0 0 33 25" fill="none">
												<rect x="1.25" y="1" width="31" height="23" rx="5.5" stroke="#3E64E0" />
												<path d="M10.75 12.9998H22.4167M16.5833 7.1665V18.8332" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
											</svg>
										</span>
                                        <span class="tf-search__form__field__incdre__dec form--span acr-dec">
											<svg xmlns="http://www.w3.org/2000/svg" width="33" height="24" viewBox="0 0 33 24" fill="none">
												<rect x="0.722168" y="0.5" width="31" height="23" rx="5.5" stroke="#3E64E0" />
												<path d="M10.2222 12.5H21.8888" stroke="white" stroke-width="2" stroke-linecap="round" stroke-line join="round" />
											</svg>
										</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Divider -->
                            <div class="tf-search__form__divider"></div>
                        <?php endif; ?>
                        <!-- Infant -->
                        <?php if (empty($disable_apartment_infant_search)): ?>
                            <div class="tf-search__form__group tf_selectperson-wrap">
                                <label for="tf-search__form-infant" class="tf-search__form__label">
                                    <?php echo esc_html_e('Infant', 'tourfic'); ?>
                                </label>
                                <div class="tf-search__form__field tf-mx-width">
                                    <div class="tf-search__form__field__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="31" height="40" viewBox="0 0 31 40" fill="none">
                                            <g clip-path="url(#clip0_2862_2125)">
                                                <path d="M20.5 5C20.5 3.67392 19.9732 2.40215 19.0355 1.46447C18.0978 0.526784 16.8261 0 15.5 0C14.1739 0 12.9021 0.526784 11.9644 1.46447C11.0268 2.40215 10.5 3.67392 10.5 5C10.5 6.32608 11.0268 7.59785 11.9644 8.53553C12.9021 9.47322 14.1739 10 15.5 10C16.8261 10 18.0978 9.47322 19.0355 8.53553C19.9732 7.59785 20.5 6.32608 20.5 5ZM12.4453 13.2266C10.5937 12.5703 8.96872 11.3281 7.85153 9.64844L6.32809 7.35938C5.56247 6.21094 4.01559 5.90625 2.85934 6.67188C1.70309 7.4375 1.39841 8.99219 2.16403 10.1406L3.69528 12.4219C5.10934 14.5391 7.03903 16.2031 9.24997 17.2969V37.5C9.24997 38.8828 10.3672 40 11.75 40C13.1328 40 14.25 38.8828 14.25 37.5V30H16.75V37.5C16.75 38.8828 17.8672 40 19.25 40C20.6328 40 21.75 38.8828 21.75 37.5V17.3125C24.0234 16.2031 26 14.4844 27.4297 12.2969L28.8515 10.1172C29.6015 8.96094 29.2734 7.41406 28.1172 6.65625C26.9609 5.89844 25.414 6.22656 24.6562 7.39062L23.2343 9.5625C21.5312 12.1719 18.6328 13.75 15.5156 13.75C14.5312 13.75 13.5703 13.5938 12.6562 13.2969C12.5859 13.2734 12.5156 13.2422 12.4453 13.2266Z" fill="#3E64E0" />
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_2862_2125">
                                                    <rect width="30" height="40" fill="white" transform="translate(0.5)" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <div class="tf-search__form__field__incdec">
                                        <input type="number" name="infant" class="infant-style2 tf-search__form__field__input field--title" id="infant" min="0" value="0">
                                        <span class="tf-search__form__field__incdre__inc form--span acr-inc">
											<svg xmlns="http://www.w3.org/2000/svg" width="33" height="25" viewBox="0 0 33 25" fill="none">
												<rect x="1.25" y="1" width="31" height="23" rx="5.5" stroke="#3E64E0" />
												<path d="M10.75 12.9998H22.4167M16.5833 7.1665V18.8332" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
											</svg>
										</span>
                                        <span class="tf-search__form__field__incdre__dec form--span acr-dec">
											<svg xmlns="http://www.w3.org/2000/svg" width="33" height="24" viewBox="0 0 33 24" fill="none">
												<rect x="0.722168" y="0.5" width="31" height="23" rx="5.5" stroke="#3E64E0" />
												<path d="M10.2222 12.5H21.8888" stroke="white" stroke-width="2" stroke-linecap="round" stroke-line join="round" />
											</svg>
										</span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <!-- Divider -->
                        <div class="tf-search__form__divider"></div>
                        <!-- Check-in -->
                        <div class="tf-search__form__group tf-checkin-group">
                            <div class="tf_apt_check_in_out_date">
                                <label for="tf-search__form-checkin" class="tf-search__form__label">
                                    <?php echo esc_html_e('Check-In', 'tourfic'); ?>
                                </label>
                                <div class="tf-search__form__field">
                                    <div class="tf-search__form__field__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="40" viewBox="0 0 36 40" fill="none">
                                            <g clip-path="url(#clip0_2862_2140)">
                                                <path d="M10.7778 0C12.1606 0 13.2778 1.11719 13.2778 2.5V5H23.2778V2.5C23.2778 1.11719 24.395 0 25.7778 0C27.1606 0 28.2778 1.11719 28.2778 2.5V5H32.0278C34.0981 5 35.7778 6.67969 35.7778 8.75V12.5H0.777832V8.75C0.777832 6.67969 2.45752 5 4.52783 5H8.27783V2.5C8.27783 1.11719 9.39502 0 10.7778 0ZM0.777832 15H35.7778V36.25C35.7778 38.3203 34.0981 40 32.0278 40H4.52783C2.45752 40 0.777832 38.3203 0.777832 36.25V15ZM5.77783 21.25V23.75C5.77783 24.4375 6.34033 25 7.02783 25H9.52783C10.2153 25 10.7778 24.4375 10.7778 23.75V21.25C10.7778 20.5625 10.2153 20 9.52783 20H7.02783C6.34033 20 5.77783 20.5625 5.77783 21.25ZM15.7778 21.25V23.75C15.7778 24.4375 16.3403 25 17.0278 25H19.5278C20.2153 25 20.7778 24.4375 20.7778 23.75V21.25C20.7778 20.5625 20.2153 20 19.5278 20H17.0278C16.3403 20 15.7778 20.5625 15.7778 21.25ZM27.0278 20C26.3403 20 25.7778 20.5625 25.7778 21.25V23.75C25.7778 24.4375 26.3403 25 27.0278 25H29.5278C30.2153 25 30.7778 24.4375 30.7778 23.75V21.25C30.7778 20.5625 30.2153 20 29.5278 20H27.0278ZM5.77783 31.25V33.75C5.77783 34.4375 6.34033 35 7.02783 35H9.52783C10.2153 35 10.7778 34.4375 10.7778 33.75V31.25C10.7778 30.5625 10.2153 30 9.52783 30H7.02783C6.34033 30 5.77783 30.5625 5.77783 31.25ZM17.0278 30C16.3403 30 15.7778 30.5625 15.7778 31.25V33.75C15.7778 34.4375 16.3403 35 17.0278 35H19.5278C20.2153 35 20.7778 34.4375 20.7778 33.75V31.25C20.7778 30.5625 20.2153 30 19.5278 30H17.0278ZM25.7778 31.25V33.75C25.7778 34.4375 26.3403 35 27.0278 35H29.5278C30.2153 35 30.7778 34.4375 30.7778 33.75V31.25C30.7778 30.5625 30.2153 30 29.5278 30H27.0278C26.3403 30 25.7778 30.5625 25.7778 31.25Z" fill="#3E64E0" />
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_2862_2140">
                                                    <rect width="35" height="40" fill="white" transform="translate(0.777832)" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <div class="tf_checkin_dates tf-flex tf-flex-align-center">
                                        <span class="date field--title"><?php echo esc_html(gmdate('d')); ?></span>
                                        <div class="tf-search__form__field__mthyr">
                                            <span class="month form--span"><?php echo esc_html(gmdate('M')); ?></span>
                                            <span class="year form--span"><?php echo esc_html(gmdate('Y')); ?></span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <input type="hidden" name="check-in-out-date" class="tf-check-inout-hidden tf-apt-check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php esc_attr_e('Check-in - Check-out', 'tourfic'); ?>" <?php echo Helper::tfopt('date_apartment_search') ? 'required' : ''; ?>>
                        </div>
                        <!-- label to -->
                        <div class="tf_checkin_to_label">
                            <?php echo esc_html_e('To', 'tourfic'); ?>
                        </div>
                        <!-- Check-out -->
                        <div class="tf-search__form__group tf_apt_check_in_out_date tf-checkout-group">
                            <label for="tf-search__form-checkout" class="tf-search__form__label">
                                <?php echo esc_html_e('Check-Out', 'tourfic'); ?>
                            </label>
                            <div class="tf-search__form__field">
                                <div class="tf-search__form__field__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="40" viewBox="0 0 36 40" fill="none">
                                        <g clip-path="url(#clip0_2862_2140)">
                                            <path d="M10.7778 0C12.1606 0 13.2778 1.11719 13.2778 2.5V5H23.2778V2.5C23.2778 1.11719 24.395 0 25.7778 0C27.1606 0 28.2778 1.11719 28.2778 2.5V5H32.0278C34.0981 5 35.7778 6.67969 35.7778 8.75V12.5H0.777832V8.75C0.777832 6.67969 2.45752 5 4.52783 5H8.27783V2.5C8.27783 1.11719 9.39502 0 10.7778 0ZM0.777832 15H35.7778V36.25C35.7778 38.3203 34.0981 40 32.0278 40H4.52783C2.45752 40 0.777832 38.3203 0.777832 36.25V15ZM5.77783 21.25V23.75C5.77783 24.4375 6.34033 25 7.02783 25H9.52783C10.2153 25 10.7778 24.4375 10.7778 23.75V21.25C10.7778 20.5625 10.2153 20 9.52783 20H7.02783C6.34033 20 5.77783 20.5625 5.77783 21.25ZM15.7778 21.25V23.75C15.7778 24.4375 16.3403 25 17.0278 25H19.5278C20.2153 25 20.7778 24.4375 20.7778 23.75V21.25C20.7778 20.5625 20.2153 20 19.5278 20H17.0278C16.3403 20 15.7778 20.5625 15.7778 21.25ZM27.0278 20C26.3403 20 25.7778 20.5625 25.7778 21.25V23.75C25.7778 24.4375 26.3403 25 27.0278 25H29.5278C30.2153 25 30.7778 24.4375 30.7778 23.75V21.25C30.7778 20.5625 30.2153 20 29.5278 20H27.0278ZM5.77783 31.25V33.75C5.77783 34.4375 6.34033 35 7.02783 35H9.52783C10.2153 35 10.7778 34.4375 10.7778 33.75V31.25C10.7778 30.5625 10.2153 30 9.52783 30H7.02783C6.34033 30 5.77783 30.5625 5.77783 31.25ZM17.0278 30C16.3403 30 15.7778 30.5625 15.7778 31.25V33.75C15.7778 34.4375 16.3403 35 17.0278 35H19.5278C20.2153 35 20.7778 34.4375 20.7778 33.75V31.25C20.7778 30.5625 20.2153 30 19.5278 30H17.0278ZM25.7778 31.25V33.75C25.7778 34.4375 26.3403 35 27.0278 35H29.5278C30.2153 35 30.7778 34.4375 30.7778 33.75V31.25C30.7778 30.5625 30.2153 30 29.5278 30H27.0278C26.3403 30 25.7778 30.5625 25.7778 31.25Z" fill="#3E64E0" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_2862_2140">
                                                <rect width="35" height="40" fill="white" transform="translate(0.777832)" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="tf_checkout_dates tf-flex tf-flex-align-center">
                                    <span class="date field--title"><?php echo esc_html(gmdate('d')); ?></span>
                                    <div class="tf-search__form__field__mthyr">
                                        <span class="month form--span"><?php echo esc_html(gmdate('M')); ?></span>
                                        <span class="year form--span"><?php echo esc_html(gmdate('Y')); ?></span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="tf-search__form__fieldset__right">
                        <!-- Submit Button -->
                        <input type="hidden" name="type" value="tf_apartment" class="tf-post-type" />
                        <button type="submit" class="tf-search__form__submit tf_btn">
                            <?php echo esc_html(apply_filters("tf_apartment_search_form_submit_button_text", 'Search')); ?>
                            <svg class="tf-search__form__submit__icon" width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.75 14.7188L11.5625 10.5312C12.4688 9.4375 12.9688 8.03125 12.9688 6.5C12.9688 2.9375 10.0312 0 6.46875 0C2.875 0 0 2.9375 0 6.5C0 10.0938 2.90625 13 6.46875 13C7.96875 13 9.375 12.5 10.5 11.5938L14.6875 15.7812C14.8438 15.9375 15.0312 16 15.25 16C15.4375 16 15.625 15.9375 15.75 15.7812C16.0625 15.5 16.0625 15.0312 15.75 14.7188ZM1.5 6.5C1.5 3.75 3.71875 1.5 6.5 1.5C9.25 1.5 11.5 3.75 11.5 6.5C11.5 9.28125 9.25 11.5 6.5 11.5C3.71875 11.5 1.5 9.28125 1.5 6.5Z" fill="white" />
                            </svg>
                        </button>
                    </div>
                </fieldset>
            </form>
        <?php } else { ?>
        <form class="tf_booking-widget <?php echo esc_attr( $classes ); ?>" id="tf_apartment_booking" method="get" autocomplete="off" action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>">
            <div class="tf_homepage-booking">
                <div class="tf_destination-wrap">
                    <div class="tf_input-inner">
                        <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php esc_html_e( 'Location', 'tourfic' ); ?>:</span>
                                <div class="tf_form-inner">
                                    <div class="tf-search-form-field-icon">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <input type="text" required="" name="place-name" id="tf-apartment-location" class="" placeholder="<?php esc_html_e( 'Enter Location', 'tourfic' ); ?>" value="">
                                    <input type="hidden" name="place" class="tf-place-input">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="tf_selectperson-wrap">
                    <div class="tf_input-inner">
						<span class="tf_person-icon tf-search-form-field-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        <div class="adults-text"><?php esc_html_e( '1 Adults', 'tourfic' ); ?></div>
						<?php if ( empty( $disable_apartment_child_search ) ): ?>
                            <div class="person-sep"></div>
                            <div class="child-text"><?php esc_html_e( '0 Children', 'tourfic' ); ?></div>
						<?php endif; ?>
						<?php if ( empty( $disable_apartment_infant_search ) ): ?>
                            <div class="person-sep"></div>
                            <div class="infant-text"><?php esc_html_e( '0 Infant', 'tourfic' ); ?></div>
						<?php endif; ?>
                    </div>
                    <div class="tf_acrselection-wrap">
                        <div class="tf_acrselection-inner">
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                    <input type="number" name="adults" id="adults" min="1" value="1"/>
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
							<?php if ( empty( $disable_apartment_child_search ) ): ?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="children" id="children" min="0" value="0"/>
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
							<?php endif; ?>
							<?php if ( empty( $disable_apartment_infant_search ) ): ?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php esc_html_e( 'Infant', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="infant" id="infant" min="0" value="0"/>
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
							<?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="tf_selectdate-wrap">
                    <div class="tf_input-inner">
                        <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php esc_html_e( 'Check-in & Check-out date', 'tourfic' ); ?></span>
                                <div class="tf_form-inner">
                                    <div class="tf-search-form-field-icon">
                                        <i class="far fa-calendar-alt"></i>
                                    </div>
                                    <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                           placeholder="<?php esc_attr_e( 'Check-in - Check-out', 'tourfic' ); ?>" <?php echo Helper::tfopt( 'date_apartment_search' ) ? 'required' : ''; ?>>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

				<?php if ( $advanced ): ?>
                    <div class="tf_selectdate-wrap tf_more_info_selections">
                        <div class="tf_input-inner">
                            <label class="tf_label-row" style="width: 100%;">
                                <span class="tf-label"><?php esc_html_e( 'More', 'tourfic' ); ?></span>
                                <span style="text-decoration: none; display: block; cursor: pointer;"><?php esc_html_e( 'Filter', 'tourfic' ); ?>  <i class="fas fa-angle-down"></i></span>
                            </label>
                        </div>
                        <div class="tf-more-info">
                            <h3><?php esc_html_e( 'Filter Price (Per Night)', 'tourfic' ); ?></h3>
                            <div class="tf-filter-price-range">
                                <div class="tf-apartment-filter-range"></div>
                            </div>

                            <h3 style="margin-top: 20px"><?php esc_html_e( 'Apartment Features', 'tourfic' ); ?></h3>
							<?php
							$tf_apartment_feature = get_terms( array(
								'taxonomy'     => 'apartment_feature',
								'orderby'      => 'title',
								'order'        => 'ASC',
								'hide_empty'   => true,
								'hierarchical' => 0,
							) );
							if ( $tf_apartment_feature ) : ?>
                                <div class="tf-apartment-features" style="overflow: hidden">
									<?php foreach ( $tf_apartment_feature as $term ) : ?>
                                        <div class="form-group form-check">
                                            <input type="checkbox" name="features[]" class="form-check-input" value="<?php echo esc_html( $term->slug ); ?>" id="<?php echo esc_html( $term->slug ); ?>">
                                            <label class="form-check-label" for="<?php echo esc_html( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></label>
                                        </div>
									<?php endforeach; ?>
                                </div>
							<?php endif; ?>

                            <h3 style="margin-top: 20px"><?php esc_html_e( 'Apartment Types', 'tourfic' ); ?></h3>
							<?php
							$tf_apartment_type = get_terms( array(
								'taxonomy'     => 'apartment_type',
								'orderby'      => 'title',
								'order'        => 'ASC',
								'hide_empty'   => true,
								'hierarchical' => 0,
							) );
							if ( $tf_apartment_type ) : ?>
                                <div class="tf-apartment-types" style="overflow: hidden">
									<?php foreach ( $tf_apartment_type as $term ) : ?>
                                        <div class="form-group form-check">
                                            <input type="checkbox" name="types[]" class="form-check-input" value="<?php echo esc_html( $term->slug ); ?>" id="<?php echo esc_html( $term->slug ); ?>">
                                            <label class="form-check-label" for="<?php echo esc_html( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></label>
                                        </div>
									<?php endforeach; ?>
                                </div>
							<?php endif; ?>
                        </div>
                    </div>
				<?php endif; ?>

                <div class="tf_submit-wrap">
                    <input type="hidden" name="type" value="tf_apartment" class="tf-post-type"/>
                    <button class="tf_btn tf-submit" type="submit"><?php echo esc_html(apply_filters("tf_apartment_search_form_submit_button_text", esc_html__('Search', 'tourfic' ))); ?></button>
                </div>

            </div>

        </form>
		<?php
		}
	}

	public static function tf_apartment_single_booking_form( $comments, $disable_review_sec ) {

		$meta                = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
		
		$min_stay            = ! empty( $meta['min_stay'] ) ? $meta['min_stay'] : 1;
		$pricing_type        = ! empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : 'per_night';
		$price_per_night     = ! empty( $meta['price_per_night'] ) ? $meta['price_per_night'] : 0;
		$adult_price         = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
		$child_price         = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
		$infant_price        = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;
		$discount_type       = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : 'none';
		$discount            = ! empty( $meta['discount'] ) ? $meta['discount'] : 0;
		$enable_availability = ! empty( $meta['enable_availability'] ) ? $meta['enable_availability'] : '';
		$apt_availability    = ! empty( $meta['apt_availability'] ) ? $meta['apt_availability'] : '';
		$booked_dates        = self::tf_apartment_booked_days( get_the_ID() );
		$apt_reserve_button_text = !empty(Helper::tfopt('apartment_booking_form_button_text')) ? stripslashes(sanitize_text_field(Helper::tfopt('apartment_booking_form_button_text'))) : esc_html__("Reserve", 'tourfic');

		$tf_booking_type = '1';
		$tf_booking_url  = $tf_booking_query_url = $tf_booking_attribute = $tf_hide_booking_form = $tf_hide_price = $tf_ext_booking_type = $tf_booking_code = '';
		
			$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
			$tf_ext_booking_type = ! empty( $meta['external-booking-type'] ) ? $meta['external-booking-type'] : 1;
			$tf_booking_code = ! empty( $meta['booking-code'] ) ? $meta['booking-code'] : '';
			$tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
			$tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&room={room}';
			$tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
			$tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
			$tf_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
		

		// date format for apartment
		$date_format_change_appartments = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";

		
			$additional_fee_label = ! empty( $meta['additional_fee_label'] ) ? $meta['additional_fee_label'] : '';
			$additional_fee       = ! empty( $meta['additional_fee'] ) ? $meta['additional_fee'] : 0;
			$fee_type             = ! empty( $meta['fee_type'] ) ? $meta['fee_type'] : '';
		

		$adults       = ! empty( $_GET['adults'] ) ? sanitize_text_field( wp_unslash($_GET['adults']) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$child        = ! empty( $_GET['children'] ) ? sanitize_text_field( wp_unslash($_GET['children'] )) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$infant       = ! empty( $_GET['infant'] ) ? sanitize_text_field( wp_unslash($_GET['infant'] )) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( wp_unslash($_GET['check-in-out-date']) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $check_in_out_arr = explode(" - ", $check_in_out);
        $check_in = ! empty( $check_in_out_arr[0] ) ? $check_in_out_arr[0] : '';
        $check_out = ! empty( $check_in_out_arr[1] ) ? $check_in_out_arr[1] : '';

		$apt_disable_dates = [];
		$tf_apt_enable_dates = [];
		if ( $enable_availability === '1' && ! empty( $apt_availability ) ) {
			$apt_availability_arr = json_decode( $apt_availability, true );
			//iterate all the available disabled dates
			if ( ! empty( $apt_availability_arr ) && is_array( $apt_availability_arr ) ) {
				foreach ( $apt_availability_arr as $date ) {
					if ( $date['status'] === 'unavailable' ) {
						$apt_disable_dates[] = $date['check_in'];
					}
					if ( $date['status'] === 'available' ) {
						$tf_apt_enable_dates[] = $date['check_in'];
					}
				}
			}
		}

		$only_booked_dates = is_array($booked_dates) && !empty($booked_dates) ? array_merge( array_column($booked_dates, "check_in") , array_column($booked_dates, "check_out")) : array();

		if( !empty( $booked_dates) && is_array($booked_dates) ) {
			foreach ($booked_dates as $booked_date) {
				$booked_date_period[] = new \DatePeriod(
					new \DateTime( $booked_date["check_in"] . ' 00:00' ),
					new \DateInterval( 'P1D' ),
					new \DateTime( $booked_date["check_out"] . ' 23:59' )
				);
			}
			foreach ($booked_date_period as $b_date) {
				foreach ($b_date as $date) {
					$only_booked_dates[] = $date->format('Y/m/d');
				}
			}
		}

		$only_booked_dates = !empty( $only_booked_dates ) ? array_unique($only_booked_dates) : array();
		

		if( is_array( $tf_apt_enable_dates ) && !empty( $tf_apt_enable_dates ) ) {
			$checked_enable_dates = array_filter( $tf_apt_enable_dates, function($date) use($only_booked_dates) {
				return !in_array($date, $only_booked_dates);
			});
		}
		
		$apartment_min_price = Apt_Pricing::instance( get_the_ID() )->get_min_max_price();

		$tf_apartment_layout_conditions = ! empty( $meta['tf_single_apartment_layout_opt'] ) ? $meta['tf_single_apartment_layout_opt'] : 'global';
		if("single"==$tf_apartment_layout_conditions){
			$tf_apartment_single_template = ! empty( $meta['tf_single_apartment_template'] ) ? $meta['tf_single_apartment_template'] : 'default';
		}
		$tf_apartment_global_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-apartment'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-apartment'] : 'default';

		$tf_apartment_selected_check = !empty($tf_apartment_single_template) ? $tf_apartment_single_template : $tf_apartment_global_template;

		$tf_apartment_selected_template = $tf_apartment_selected_check;

		if($tf_apartment_selected_template=="design-1"){
		?>
		<form id="tf-apartment-booking" class="tf-apartment-side-booking" method="get" autocomplete="off">
            
            <div class="tf-apartment-form-header">
				<?php if ( $tf_booking_type == 2 && $tf_hide_price !== '1' && ( $tf_ext_booking_type == 2 ) || $tf_booking_type == 1 ): ?>
					<h3 class="tf-apartment-price-per-night">
						<span class="tf-apartment-base-price">
						<?php
							//get the lowest price from all available room price
							$apartment_min_main_price = $apartment_min_price["min"];
							$apartment_min_price = Apt_Pricing::instance( get_the_ID() )->calculate_discount( $apartment_min_price["min"] );
							$lowest_price = wc_price( $apartment_min_price );
							
							if ( $apartment_min_price != $apartment_min_main_price ) {
								echo "<b>" . esc_html__("From ", "tourfic") . "</b>" . "<del>" . esc_html( wp_strip_all_tags(wc_price( $apartment_min_main_price )) ) . "</del>" . " " . wp_kses_post( $lowest_price );
							} else {
								echo esc_html__("From ", "tourfic") . wp_kses_post(wc_price( $apartment_min_main_price ));
							}
							?>
						</span>
						<?php if ( $pricing_type == "per_night") : ?>
							<span class="per-pricing-type"><?php esc_html_e( '/per night', 'tourfic' ) ?></span>
						<?php else : ?>
							<span class="per-pricing-type"><?php esc_html_e( '/per person', 'tourfic' ) ?></span>
						<?php endif; ?>
					</h3>
				<?php endif; ?>
            </div>

			<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form == '1' || ( $tf_ext_booking_type == 2 && empty( $tf_booking_code ) ) ) || $tf_booking_type == 1 ): ?>
				
				<h3 class="tf-section-title"><?php esc_html_e("Available Date", "tourfic"); ?></h3>
				<div class="tf-apartment-form-fields">
					<div class="tf_booking-dates tf-check-in-out-date">
						<div class="tf-aprtment-check-in-out-date">
							<label class="tf_label_rows">
								<i class="fa-sharp fa-solid fa-calendar-days"></i>
								<input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Choose date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . esc_attr( $check_in_out ) . '"' : '' ?> required>
							</label>
						</div>
						<div class="tf_label-row"></div>
					</div>

					<div class="tf_form-row tf-apartment-guest-row">
						<label class="tf_label-row">
							<div class="tf_form-inner">
								<div class="tf_selectperson-wrap">
								<div class="tf-form-title">
									<h3 class="tf-person-info-title"><?php esc_html_e( 'Person Info', 'tourfic' ); ?></h3>
								</div>
									<div class="tf_acrselection">
										<div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
										<div class="acr-select">
											<div class="acr-dec">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
												<g clip-path="url(#clip0_3229_13094)">
													<rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
												</g>
												<defs>
													<clipPath id="clip0_3229_13094">
													<rect width="20" height="20" fill="white"/>
													</clipPath>
												</defs>
												</svg>
											</div>
											<input type="tel" name="adults" id="adults" min="1" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '1' ?>" readonly/>
											<div class="acr-inc">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
												<g clip-path="url(#clip0_3229_13100)">
													<path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
												</g>
												<defs>
													<clipPath id="clip0_3229_13100">
													<rect width="20" height="20" fill="white"/>
													</clipPath>
												</defs>
												</svg>
											</div>
										</div>
									</div>
									<div class="tf_acrselection">
										<div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
										<div class="acr-select">
											<div class="acr-dec">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
												<g clip-path="url(#clip0_3229_13094)">
													<rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
												</g>
												<defs>
													<clipPath id="clip0_3229_13094">
													<rect width="20" height="20" fill="white"/>
													</clipPath>
												</defs>
												</svg>
											</div>
											<input type="tel" name="children" id="children" min="0" value="<?php echo ! empty( $child ) ? esc_attr( $child ) : '0' ?>" readonly/>
											<div class="acr-inc">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
												<g clip-path="url(#clip0_3229_13100)">
													<path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
												</g>
												<defs>
													<clipPath id="clip0_3229_13100">
													<rect width="20" height="20" fill="white"/>
													</clipPath>
												</defs>
												</svg>
											</div>
										</div>
									</div>
									<div class="tf_acrselection">
										<div class="acr-label"><?php esc_html_e( 'Infant', 'tourfic' ); ?></div>
										<div class="acr-select">
											<div class="acr-dec">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
												<g clip-path="url(#clip0_3229_13094)">
													<rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
												</g>
												<defs>
													<clipPath id="clip0_3229_13094">
													<rect width="20" height="20" fill="white"/>
													</clipPath>
												</defs>
												</svg>
											</div>
											<input type="tel" name="infant" id="infant" min="0" value="<?php echo ! empty( $infant ) ? esc_attr( $infant ) : '0' ?>" readonly/>
											<div class="acr-inc">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
												<g clip-path="url(#clip0_3229_13100)">
													<path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
												</g>
												<defs>
													<clipPath id="clip0_3229_13100">
													<rect width="20" height="20" fill="white"/>
													</clipPath>
												</defs>
												</svg>
											</div>
										</div>
									</div>
								</div>
							</div>
						</label>
					</div>
				</div>
			<?php endif; ?>

            <div class="tf_form-row">
				<?php $ptype = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash($_GET['type']) ) : get_post_type(); // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
                <input type="hidden" name="type" value="<?php echo esc_attr( $ptype ); ?>" class="tf-post-type"/>
                <input type="hidden" name="post_id" value="<?php echo esc_attr( get_the_ID() ); ?>"/>

                <div class="tf-btn-booking">
					<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' && $tf_ext_booking_type == 1 ) || $tf_booking_type == 1 ) : ?>
							<?php if (!empty($apt_reserve_button_text)) : ?>
								<button class="tf_btn tf_btn_full tf_btn_large tf-submit" type="submit"><?php echo esc_html( $apt_reserve_button_text ); ?></button>
						<?php endif; ?>
					<?php elseif( $tf_booking_type == 2 && $tf_hide_booking_form == 1 ): ?>
						<?php if (!empty($apt_reserve_button_text)) : ?>
							<a href="<?php echo esc_url( $tf_booking_url ); ?>"
							class="tf_btn tf_btn_full tf_btn_large tf-submit" <?php echo ! empty( $tf_booking_attribute ) ? esc_attr( $tf_booking_attribute ) : ''; ?> target="_blank"><?php echo esc_html($apt_reserve_button_text ); ?></a>
						<?php endif; ?>
					<?php endif; ?>
                </div>

				<?php if(!empty( $tf_booking_code ) && $tf_booking_type == 2 && $tf_ext_booking_type == 2 ) : ?>
					<?php echo wp_kses( $tf_booking_code, Helper::tf_custom_wp_kses_allow_tags()); ?>
				<?php endif; ?>
            </div>

            <ul class="tf-apartment-price-list" style="display: none">
                <li class="total-days-price-wrap" style="display: none">
                    <span class="total-days tf-price-list-label"></span>
                    <span class="days-total-price tf-price-list-price"></span>
                </li>

				
				<?php if ( ! empty( $additional_fee_label ) && ! empty( $additional_fee ) ): ?>
                    <li class="additional-fee-wrap" style="display: none">
                        <span class="additional-fee-label tf-price-list-label"><?php echo esc_html( $additional_fee_label ); ?></span>
                        <span class="additional-fee tf-price-list-price"></span>
                    </li>
				<?php endif; ?>

				<?php if ( ! empty( $discount ) ): ?>
                    <li class="apartment-discount-wrap" style="display: none">
                        <span class="apartment-discount-label tf-price-list-label"><?php esc_html_e( 'Discount', 'tourfic' ); ?></span>
                        <span class="apartment-discount tf-price-list-price"></span>
                    </li>
				<?php endif; ?>

                <li class="total-price-wrap" style="display: none">
                    <span class="total-price-label tf-price-list-label"><?php esc_html_e( 'Total Price', 'tourfic' ); ?></span>
                    <span class="total-price"></span>
                </li>
            </ul>

			<?php wp_nonce_field( 'tf_apartment_booking', 'tf_apartment_nonce' ); ?>
        </form>
		<?php }else{ ?>

		<?php do_action("tf_apartment_before_single_booking_form"); ?>
        <!-- Start Booking widget -->
        <form id="tf-apartment-booking" class="tf-apartment-side-booking tf-apartment-design-one-form" method="get" autocomplete="off">
            <h5><?php echo ! empty( $meta['booking_form_title'] ) ? esc_html( $meta['booking_form_title'] ) : esc_html_e( 'Book your Apartment', 'tourfic' ); ?></h5>
            <div class="tf-apartment-form-header">
				<?php if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' && $tf_ext_booking_type == 1 ) || $tf_booking_type == 1 ) : ?>
                    <h3 class="tf-apartment-price-per-night">
                        <span class="tf-apartment-base-price">
						<?php
							//get the lowest price from all available room price
							$apartment_min_main_price = $apartment_min_price["min"];
							$apt_disocunt_price = Apt_Pricing::instance( get_the_ID() )->calculate_discount( $apartment_min_price["min"] );
							
							$lowest_price = wc_price( $apt_disocunt_price );
							
							if ( ! empty( $apt_disocunt_price ) && $apt_disocunt_price != $apartment_min_main_price ) {
								echo "<del>" . esc_html( wp_strip_all_tags(wc_price( $apartment_min_main_price )) ) . "</del>" . " " . wp_kses_post( $lowest_price );
							} else {
								echo wp_kses_post(wc_price( $apartment_min_main_price ));	;
							}
							?>
						</span>
						<?php if ( $pricing_type == "per_night") : ?>
                        	<span><?php esc_html_e( '/ per night', 'tourfic' ) ?></span>
						<?php else : ?>
							<span><?php esc_html_e( '/ per person', 'tourfic' ) ?></span>
						<?php endif; ?>

                    </h3>
				<?php endif; ?>
				<?php if ( $comments && ! $disable_review_sec == '1' ): ?>
                    <div class="tf-top-review">
                        <a href="#tf-review">
                            <div class="tf-single-rating">
                                <i class="fas fa-star"></i> <span><?php echo esc_html( TF_Review::tf_total_avg_rating( $comments ) ); ?></span>
                                (<?php TF_Review::tf_based_on_text( count( $comments ) ); ?>)
                            </div>
                        </a>
                    </div>
				<?php endif; ?>
            </div>

			<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' && $tf_ext_booking_type == 1 ) || $tf_booking_type == 1 ) : ?>
                <div class="tf-apartment-form-fields">
                    <div class="tf_booking-dates">
                        <div class="tf-check-in-date">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php esc_html_e( 'Check in', 'tourfic' ); ?></span>
                                <input type="text" name="check-in-date" id="check-in-date" onkeypress="return false;"
                                       placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in ) ? 'value="' . esc_attr( $check_in ) . '"' : '' ?>
                                       required readonly>
                            </label>
                        </div>
                        <div class="tf-check-out-date">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php esc_html_e( 'Check out', 'tourfic' ); ?></span>
                                <input type="text" name="check-out-date" id="check-out-date" onkeypress="return false;"
                                       placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_out ) ? 'value="' . esc_attr( $check_out ) . '"' : '' ?>
                                       required>
                                <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                       placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_out ) ? 'value="' . esc_attr($check_out) . '"' : '' ?> required>
                            </label>
                        </div>
                    </div>

                    <div class="tf_form-row tf-apartment-guest-row">
                        <label class="tf_label-row">
                            <span class="tf-label"><?php esc_html_e( 'Guest', 'tourfic' ); ?></span>
                            <div class="tf_form-inner">
                                <div class="tf_selectperson-wrap">
                                    <div class="tf_input-inner">
										<?php /* translators: %s Adult Number */ ?>
                                        <div class="adults-text"><?php echo sprintf( esc_html__( '%s Adults', 'tourfic' ), ! empty( $adults ) ? esc_html( $adults ) : 1 ); ?></div>
                                        <div class="person-sep"></div>
										<?php /* translators: %s Children Number */ ?>
                                        <div class="child-text"><?php echo sprintf( esc_html__( '%s Children', 'tourfic' ), ! empty( $child ) ? esc_html( $child ) : 0 ); ?></div>
                                        <div class="person-sep"></div>
										<?php /* translators: %s Infant Number */ ?>
                                        <div class="infant-text"><?php echo sprintf( esc_html__( '%s Infant', 'tourfic' ), ! empty( $infant ) ? esc_html( $infant ) : 0 ); ?></div>
                                    </div>
                                    <div class="tf_acrselection-wrap">
                                        <div class="tf_acrselection-inner">
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">-</div>
                                                    <input type="number" name="adults" id="adults" min="1" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '1' ?>"/>
                                                    <div class="acr-inc">+</div>
                                                </div>
                                            </div>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">-</div>
                                                    <input type="number" name="children" id="children" min="0" value="<?php echo ! empty( $child ) ? esc_attr( $child ) : '0' ?>"/>
                                                    <div class="acr-inc">+</div>
                                                </div>
                                            </div>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php esc_html_e( 'Infant', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">-</div>
                                                    <input type="number" name="infant" id="infant" min="0" value="<?php echo ! empty( $infant ) ? esc_attr( $infant ) : '0' ?>"/>
                                                    <div class="acr-inc">+</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
			<?php endif; ?>

            <div class="tf_form-row">
				<?php $ptype = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash($_GET['type']) ) : get_post_type();  // phpcs:ignore WordPress.Security.NonceVerification.Recommended?>
                <input type="hidden" name="type" value="<?php echo esc_attr( $ptype); ?>" class="tf-post-type"/>
                <input type="hidden" name="post_id" value="<?php echo esc_attr( get_the_ID() ); ?>"/>

                <div class="tf-btn-wrap">
					<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' && $tf_ext_booking_type == 1 ) || $tf_booking_type == 1 ) : ?>
                        <?php if (!empty($apt_reserve_button_text)) : ?>
							<button class="tf_btn tf_btn_full tf_btn_large tf-submit" type="submit"><?php echo esc_html( $apt_reserve_button_text ); ?></button>
						<?php endif; ?>
					<?php elseif( $tf_booking_type == 2 && $tf_hide_booking_form == 1 ): ?>
						<?php if (!empty($apt_reserve_button_text)) : ?>
							<a href="<?php echo esc_url( $tf_booking_url ); ?>"
							class="tf_btn tf_btn_full tf_btn_large tf-submit" <?php echo ! empty( $tf_booking_attribute ) ? esc_attr( $tf_booking_attribute ) : ''; ?> target="_blank"><?php echo esc_html( $apt_reserve_button_text ); ?></a>
						<?php endif; ?>
					<?php endif; ?>
                </div>

				<?php if(!empty( $tf_booking_code ) && $tf_booking_type == 2 && $tf_ext_booking_type == 2 ) : ?>
					<?php echo wp_kses( $tf_booking_code, Helper::tf_custom_wp_kses_allow_tags()); ?>
				<?php endif; ?>

            </div>

            <ul class="tf-apartment-price-list" style="display: none">
                <li class="total-days-price-wrap" style="display: none">
                    <span class="total-days tf-price-list-label"></span>
                    <span class="days-total-price tf-price-list-price"></span>
                </li>

				
				<?php if ( ! empty( $additional_fee_label ) && ! empty( $additional_fee ) ): ?>
                    <li class="additional-fee-wrap" style="display: none">
                        <span class="additional-fee-label tf-price-list-label"><?php echo esc_html( $additional_fee_label ); ?></span>
                        <span class="additional-fee tf-price-list-price"></span>
                    </li>
				<?php endif; ?>

				<?php if ( ! empty( $discount ) ): ?>
                    <li class="apartment-discount-wrap" style="display: none">
                        <span class="apartment-discount-label tf-price-list-label"><?php esc_html_e( 'Discount', 'tourfic' ); ?></span>
                        <span class="apartment-discount tf-price-list-price"></span>
                    </li>
				<?php endif; ?>

                <li class="total-price-wrap" style="display: none">
                    <span class="total-price-label tf-price-list-label"><?php esc_html_e( 'Total Price', 'tourfic' ); ?></span>
                    <span class="total-price"></span>
                </li>
            </ul>

			<?php wp_nonce_field( 'tf_apartment_booking', 'tf_apartment_nonce' ); ?>
        </form>

		<?php do_action("tf_apartment_after_single_booking_form"); ?>

		<?php } ?>

		<?php
	}

	public static function tf_apartment_archive_single_item( array $data = [ 1, 0, 0, '' ], $settings = [] ): void {

		$post_id  = get_the_ID();
		$features = ! empty( get_the_terms( $post_id, 'apartment_feature' ) ) ? get_the_terms( $post_id, 'apartment_feature' ) : '';

		// Form Data
		if ( isset( $data[4] ) && isset( $data[5] ) ) {
			[ $adults, $child, $infant, $check_in_out, $startprice, $endprice ] = $data;
		} else {
			[ $adults, $child, $infant, $check_in_out ] = $data;
		}

		// Get apartment meta options
		$meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
		if ( empty( $meta ) ) {
			return;
		}

		// Location
		$map = ! empty( $meta['map'] ) ? $meta['map'] : '';
		if ( ! empty( $map ) && gettype( $map ) == "string" ) {
			$tf_apartment_map_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $map );
			$map                    = unserialize( $tf_apartment_map_value );
			$address                = ! empty( $map['address'] ) ? $map['address'] : '';
		}else{
			$address                = ! empty( $map['address'] ) ? $map['address'] : '';
		}
		$featured        = ! empty( $meta['apartment_as_featured'] ) ? $meta['apartment_as_featured'] : '';
		$pricing_type    = ! empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : 'per_night';
		
		$apartment_tags_raw = isset($meta['tf-apartment-tags']) ? $meta['tf-apartment-tags'] : null;
		$apartment_multiple_tags = !empty($apartment_tags_raw) ? Helper::tf_data_types($apartment_tags_raw) : [];
		//Discout Info
		$apartment_discount_type = !empty($meta["discount_type"]) ? $meta["discount_type"] : "none";
		$apartment_discount_amount = !empty($meta["discount"]) ? $meta["discount"] : 0;

		$meta_disable_review 			  = !empty($meta["disable-apartment-review"]) ? $meta["disable-apartment-review"] : 0;
		$tfopt_disable_review 			  = !empty(Helper::tfopt("disable-apartment-review")) ? Helper::tfopt("disable-apartment-review") : 0;
		$disable_review 				  = $tfopt_disable_review == 1 || $meta_disable_review == 1 ? true : $tfopt_disable_review;

		// Gallery Image
		$gallery = ! empty( $meta['apartment_gallery'] ) ? $meta['apartment_gallery'] : '';
		if ( $gallery ) {
			$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
		}
		
		// Single link
		$url = get_the_permalink();
		$url = add_query_arg( array(
			'adults'            => $adults,
			'children'          => $child,
			'infant'            => $infant,
			'check-in-out-date' => $check_in_out,
		), $url );

		$design = !empty($settings['design_apartment']) ? $settings['design_apartment'] : '';
		$tf_apartment_arc_selected_template = !empty($design) ? $design : (! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] : 'default');
		
		//elementor settings
		$show_image = isset($settings['show_image']) ? $settings['show_image'] : 'yes';
		$featured_badge = isset($settings['featured_badge']) ? $settings['featured_badge'] : 'yes';
		$discount_tag = isset($settings['discount_tag']) ? $settings['discount_tag'] : 'yes';
		$promotional_tags = isset($settings['promotional_tags']) ? $settings['promotional_tags'] : 'yes';
		$gallery_switch = isset($settings['gallery']) ? $settings['gallery'] : 'yes';
		$show_title = isset($settings['show_title']) ? $settings['show_title'] : 'yes';
		$title_length = isset($settings['title_length']) ? absint($settings['title_length']) : 55;
		$show_excerpt = isset($settings['show_excerpt']) ? $settings['show_excerpt'] : 'yes';
		$excerpt_length = isset($settings['excerpt_length']) ? absint($settings['excerpt_length']) : 100;
		$show_location = isset($settings['show_location']) ? $settings['show_location'] : 'yes';
		$location_length = isset($settings['location_length']) ? absint($settings['location_length']) : 120;
		$show_features = isset($settings['show_features']) ? $settings['show_features'] : 'yes';
		$features_count = isset($settings['features_count']) ? absint($settings['features_count']) : 4;
		$show_review = isset($settings['show_review']) ? $settings['show_review'] : 'yes';
		$show_price = isset($settings['show_price']) ? $settings['show_price'] : 'yes';
		$show_view_details = isset($settings['show_view_details']) ? $settings['show_view_details'] : 'yes';
		$view_details_text = isset($settings['view_details_text']) ? sanitize_text_field($settings['view_details_text']) : esc_html__('View Details', 'tourfic');
		
		// Thumbnail
		$thumbnail_html = '';
		if ( !empty($settings) && $show_image == 'yes' ) {
			$settings[ 'image_size_customize' ] = [
				'id' => get_post_thumbnail_id(),
			];
			$settings['image_size_customize_size'] = $settings['image_size'];
			$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings,'image_size_customize' );
			
			if ( "" === $thumbnail_html && 'yes' === $settings['show_fallback_img'] && !empty( $settings['fallback_img']['url'] ) ) {
				$settings[ 'image_size_customize' ] = [
					'id' => $settings['fallback_img']['id'],
				];
				$settings['image_size_customize_size'] = $settings['image_size'];
				$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings,'image_size_customize' );
			} elseif("" === $thumbnail_html && 'yes' !== $settings['show_fallback_img']) {
				$thumbnail_html = '<img src="' . esc_url( TF_ASSETS_APP_URL ) . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
			}
		}

		//Location icon
		$location_icon_html = '<i class="fa-solid fa-location-dot"></i>';
		if(!empty($settings) && $show_location == 'yes'){
			$location_icon_migrated = isset($settings['__fa4_migrated']['location_icon']);
			$location_icon_is_new = empty($settings['location_icon_comp']);

			if ( $location_icon_is_new || $location_icon_migrated ) {
				ob_start();
				Icons_Manager::render_icon( $settings['location_icon'], [ 'aria-hidden' => 'true' ] );
				$location_icon_html = ob_get_clean();
			} else{
				$location_icon_html = '<i class="' . esc_attr( $settings['location_icon_comp'] ) . '"></i>';
			}
		}

		//Featured badge
		$featured_badge_text = !empty( $meta['featured_text'] ) ? esc_html( $meta['featured_text'] ) : esc_html( "HOT DEAL" );

		if ( $tf_apartment_arc_selected_template == "design-1" ) {
		$first_gallery_image = explode(',', $gallery);
		?>
		<div class="tf-available-room">
			<!-- Thumbnail -->
			<?php if($show_image == 'yes'): ?>
			<div class="tf-available-room-gallery">                       
				<div class="tf-room-gallery">
					<?php
					if ( ! empty( $thumbnail_html ) ) {
						echo wp_kses_post( $thumbnail_html );
					} elseif ( has_post_thumbnail($post_id) ) {
						echo get_the_post_thumbnail($post_id, 'full' );
					} else {
						echo '<img src="' . esc_url(TF_ASSETS_APP_URL) . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
					}
					?>
				</div>
				
				<?php if( $gallery_switch == 'yes' && !empty($gallery_ids) ){ ?>                                                                     
				<div data-id="<?php echo esc_attr( get_the_ID() ); ?>" data-type="tf_apartment" class="tf-room-gallery tf-popup-buttons tf-hotel-room-popup" style="<?php echo !empty($first_gallery_image[0]) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.70) 0%, rgba(48, 40, 28, 0.70) 100%), url('.esc_url(wp_get_attachment_image_url($first_gallery_image[0])).'), lightgray 50% / cover no-repeat; background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
					<svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
					<g id="content">
					<path id="Rectangle 2111" d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5" stroke="#FDF9F4" stroke-width="1.5"></path>
					<path id="Rectangle 2109" d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z" stroke="#FDF9F4" stroke-width="1.5"></path>
					<path id="Vector" d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17" stroke="#FDF9F4" stroke-width="1.5" stroke-linejoin="round"></path>
					<path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
					</g>
					</svg>
				</div>
				<?php } ?>
				
				<div class="tf-available-labels">
					<?php if ( $featured_badge == 'yes' && $featured ): ?>
					<span class="tf-available-labels-featured"><?php echo esc_html( $featured_badge_text ); ?></span>
					<?php endif; ?>
					<?php
					if($promotional_tags == 'yes' && sizeof($apartment_multiple_tags) > 0) {
						foreach($apartment_multiple_tags as $tag) {
							$apartment_tag_name = !empty($tag['apartment-tag-title']) ? esc_html( $tag['apartment-tag-title'] ) : '';
							$tag_background_color = !empty($tag["apartment-tag-color-settings"]["background"]) ? esc_attr($tag["apartment-tag-color-settings"]["background"]) : "#003162";
							$tag_font_color = !empty($tag["apartment-tag-color-settings"]["font"]) ? esc_attr($tag["apartment-tag-color-settings"]["font"]) : "#fff";

							if (!empty($apartment_tag_name)) {
								echo '<span class="tf-multiple-tag" style="color: ' . esc_attr( $tag_font_color ) . '; background-color: ' . esc_attr( $tag_background_color ) . '">' . esc_html( $apartment_tag_name ) . '</span>';
							}
						}
					}
					?>
				</div>  
				
                <!-- Review -->
				<?php if( $show_review == 'yes' && $disable_review != true ): ?>
					<div class="tf-available-ratings">
						<?php TF_Review::tf_archive_single_rating($post_id, $design); ?>
						<i class="fa-solid fa-star"></i>
					</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<div class="tf-available-room-content" style="<?php echo $show_image != 'yes' ? 'width: 100%;' : ''; ?>">
				<div class="tf-available-room-content-left">
					<div class="tf-card-heading-info">
						<!-- Title & Location -->
						<div class="tf-section-title-and-location">
							<!-- Title -->
							<?php if( $show_title == 'yes' ): ?>
							<a href="<?php echo esc_url( get_the_permalink($post_id) ); ?>"><h2 class="tf-section-title"><?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title($post_id), $title_length ) ); ?></h2></a>
							<?php endif; ?>

							<!-- Location -->
							<?php if ( $show_location == 'yes' && ! empty( $address ) ) :?>
							<div class="tf-title-location">
								<div class="location-icon"><?php echo wp_kses( $location_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?></div>
								<span><?php echo esc_html( Helper::tourfic_character_limit_callback( esc_html( $address ), $location_length ) ); ?></span>
							</div>
							<?php endif; ?>
						</div>

						<!-- Mobile Price -->
						<div class="tf-mobile tf-pricing-info">
							<?php if ( $discount_tag == 'yes' && ! empty( $apartment_discount_amount ) ){ ?>
								<div class="tf-available-room-off">
									<span>
										<?php echo $apartment_discount_type == "percent" ? wp_kses_post($apartment_discount_amount . '%') : wp_kses_post(wc_price( $apartment_discount_amount )) ?>
										<?php esc_html_e( " Off", "tourfic" ); ?>
									</span>
								</div>
							<?php } ?>
							<?php if($show_price == 'yes') : ?>
							<div class="tf-available-room-price">
								<span class="tf-price-from">
								<?php echo wp_kses_post(Pricing::instance(get_the_ID())->get_min_price_html()); ?>
								</span>
							</div>
							<?php endif; ?>
						</div>
					</div>

					<!-- Features -->
					<?php if ( $show_features == 'yes' && $features ) : ?>
						<ul class="features">
							<?php foreach ( $features as $tfkey => $feature ) {
							$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_apartment_feature', true );
							if ( ! empty( $feature_meta ) ) {
								$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
							}
							if ( ! empty( $f_icon_type ) && $f_icon_type == 'icon' ) {
								$feature_icon = ! empty( $feature_meta['apartment-feature-icon'] ) ? '<i class="' . esc_attr( $feature_meta['apartment-feature-icon'] ) . '"></i>' : '';
							} elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'custom' ) {
								$feature_icon = ! empty( $feature_meta['apartment-feature-icon-custom'] ) ? '<img src="' . esc_url( $feature_meta['apartment-feature-icon-custom'] ) . '" style="min-width: ' . esc_attr( $feature_meta['apartment-feature-icon-dimension'] ) . 'px; height: ' . esc_attr( $feature_meta['apartment-feature-icon-dimension'] ) . 'px;" />' : '';
							}
							if ( $tfkey < $features_count ) {
							?>
								<li>
								<?php
								if ( ! empty( $feature_icon ) ) {
									echo wp_kses_post( $feature_icon );
								} ?>
								<?php echo esc_html( $feature->name ); ?>
								</li>
							<?php } } ?>
							<?php if(count($features) > $features_count){ ?>
								<li><a href="<?php echo esc_url( $url ); ?>"><?php esc_html_e("View More", "tourfic"); ?></a></li>
							<?php } ?>
						</ul>
					<?php endif; ?>
				</div>
				<div class="tf-available-room-content-right">
					<div class="tf-card-pricing-heading">
						<?php if ( $discount_tag == 'yes' && ! empty( $apartment_discount_amount ) && $apartment_discount_type!="none" ){ ?>
							<div class="tf-available-room-off">
								<span>
									<?php echo $apartment_discount_type=="percent" ? wp_kses_post( $apartment_discount_amount ).'%' : wp_kses_post(wc_price($apartment_discount_amount)); ?> 
									<?php esc_html_e( " Off ", "tourfic" ); ?>
								</span>
							</div>
						<?php } ?>

						<!-- Price -->
						<?php if($show_price == 'yes') : ?>
						<div class="tf-available-room-price">
							<span class="tf-price-from">
								<?php echo wp_kses_post(Pricing::instance(get_the_ID())->get_min_price_html()); ?>
							</span>
						</div>
						<?php endif; ?>
					</div>

					<!-- View Details -->
					<?php if($show_view_details == 'yes') : ?>    
					<a href="<?php echo esc_url( $url ); ?>" class="tf_btn tf_btn_large tf_btn_sharp"><?php echo esc_html( $view_details_text ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
        <?php } elseif ( $tf_apartment_arc_selected_template == "design-2" ) { ?>
            <div class="tf-archive-hotel" data-id="<?php echo esc_attr(get_the_ID()); ?>">
            	<!-- Thumbnail -->
				<?php if($show_image == 'yes'): ?>    
				<div class="tf-archive-hotel-thumb">
                    <a href="<?php echo esc_url( $url ); ?>">
						<?php
						if ( ! empty( $thumbnail_html ) ) {
							echo wp_kses_post( $thumbnail_html );
						} elseif ( ! empty( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) ) {
							the_post_thumbnail( 'full' );
						} else {
							echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
						}
						?>
                    </a>

					<div class="tf-tag-items">
						<?php if ( $discount_tag == 'yes' && ! empty( $apartment_discount_amount ) ) : ?>
							<div class="tf-tag-item tf-tag-item-discount">
								<?php echo $apartment_discount_type == "percent" ? wp_kses_post($apartment_discount_amount . '%') : wp_kses_post(wc_price( $apartment_discount_amount )) ?>
								<?php esc_html_e( " Off", "tourfic" ); ?>
							</div>
						<?php endif; ?>
						<?php if ( $featured_badge == 'yes' && $featured ): ?>
							<div class="tf-tag-item tf-tag-item-featured"><?php echo esc_html( $featured_badge_text ); ?></div>
						<?php endif; ?>

						<?php
						if($promotional_tags == 'yes' && sizeof($apartment_multiple_tags) > 0) {
							foreach($apartment_multiple_tags as $tag) {
								$apartment_tag_name = !empty($tag['apartment-tag-title']) ? esc_html( $tag['apartment-tag-title'] ) : '';
								$tag_background_color = !empty($tag["apartment-tag-color-settings"]["background"]) ? esc_attr($tag["apartment-tag-color-settings"]["background"]) : "#003162";
								$tag_font_color = !empty($tag["apartment-tag-color-settings"]["font"]) ? esc_attr($tag["apartment-tag-color-settings"]["font"]) : "#fff";

								if (!empty($apartment_tag_name)) {
									echo '<span class="tf-tag-item tf-multiple-tag" style="color: ' . esc_attr( $tag_font_color ) . '; background-color: ' . esc_attr( $tag_background_color ) . '">' . esc_html( $apartment_tag_name ) . '</span>';
								}
							}
						}
						?>
					</div>
                </div>
				<?php endif; ?>

                <div class="tf-archive-hotel-content" style="<?php echo $show_image != 'yes' ? 'width: 100%;' : ''; ?>">
                    <div class="tf-archive-hotel-content-left">
						<!-- Location -->
						<?php if ( $show_location == 'yes' && ! empty( $address ) ) : ?>
                            <div class="tf-title-location">
                                <div class="location-icon">
									<?php echo wp_kses( $location_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
                                </div>
                                <span><?php echo wp_kses_post(Helper::tourfic_character_limit_callback( esc_html( $address ), $location_length )); ?></span>
                            </div>
						<?php endif; ?>
						
						<!-- Title -->
						<?php if( $show_title == 'yes' ): ?>
                        <h4 class="tf-section-title">
                            <a href="<?php echo esc_url( $url ); ?>">
								<?php echo wp_kses_post(Helper::tourfic_character_limit_callback( get_the_title(), $title_length )); ?>
                            </a>
                        </h4>
						<?php endif; ?>

						<!-- Features -->
						<?php if ( $show_features == 'yes' && $features ) { ?>
                            <ul class="features">
								<?php foreach ( array_slice( $features, 0, $features_count ) as $tfkey => $feature ) :
									$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_apartment_feature', true );
									if ( ! empty( $feature_meta ) ) {
										$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
									}
									if ( ! empty( $f_icon_type ) && $f_icon_type == 'icon' ) {
										$feature_icon = ! empty( $feature_meta['apartment-feature-icon'] ) ? '<i class="' . esc_attr( $feature_meta['apartment-feature-icon'] ) . '"></i>' : '';
									} elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'custom' ) {
										$feature_icon = ! empty( $feature_meta['apartment-feature-icon-custom'] ) ? '<img src="' . esc_url( $feature_meta['apartment-feature-icon-custom'] ) . '" style="min-width: ' . esc_attr( $feature_meta['apartment-feature-icon-dimension'] ) . 'px; height: ' . esc_attr( $feature_meta['apartment-feature-icon-dimension'] ) . 'px;" />' : '';
									}

									echo '<li>';
									if ( ! empty( $feature_icon ) ) {
										echo wp_kses_post( $feature_icon );
									}
									echo esc_html( $feature->name );
									//add comma after each feature except last one, if only 1/2 exists then don't add comma to last one
									if ( count( $features ) > 1 && $tfkey != count( array_slice( $features, 0, $features_count ) ) - 1  ) {
										echo ',';
									}
									echo '</li>';

								endforeach;
								?>
								<?php if ( count( $features ) > $features_count ) { ?>
                                    <li><a href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( "View More", "tourfic" ); ?></a></li>
								<?php } ?>
                            </ul>
						<?php } ?>

						<!-- Review -->
						<?php if( $show_review == 'yes' && $disable_review != true ): ?>
							<?php TF_Review::tf_archive_single_rating('', $design); ?>
						<?php endif; ?>
                    </div>
                    <div class="tf-archive-hotel-content-right">
						<!-- Price -->
						<?php if($show_price == 'yes') : ?>
                        <div class="tf-archive-hotel-price">
							<?php echo wp_kses_post(Pricing::instance( $post_id )->get_min_price_html()); ?>
                        </div>
						<?php endif; ?>

						<!-- View Details -->
						<?php if($show_view_details == 'yes') : ?>
                        <a href="<?php echo esc_url( $url ); ?>" class="tf_btn tf_btn_gray tf_btn_small"><?php echo esc_html( $view_details_text ); ?></a>
						<?php endif; ?>
					</div>
                </div>
            </div>
		<?php }else{ ?>
        <div class="single-tour-wrap <?php echo $featured ? esc_attr( 'tf-featured' ) : '' ?>">
            <div class="single-tour-inner">
				<?php if ( $show_image == 'yes' && $featured_badge == 'yes' && $featured ): ?>
                    <div class="tf-featured-badge"><span><?php echo esc_html( $featured_badge_text ); ?></span></div>
				<?php endif; ?>

				<!-- Thumbnail -->
				<?php if($show_image == 'yes'): ?>
                <div class="tourfic-single-left">
                	<div class="default-tags-container">
						<?php
						if($promotional_tags == 'yes' && sizeof($apartment_multiple_tags) > 0) {
							foreach($apartment_multiple_tags as $tag) {
								$tag_title = !empty($tag["apartment-tag-title"]) ? esc_html( $tag["apartment-tag-title"], 'tourfic') : '';
								$tag_background_color = !empty($tag["apartment-tag-color-settings"]["background"]) ? esc_attr( $tag["apartment-tag-color-settings"]["background"] ) : "#003162";
								$tag_font_color = !empty($tag["apartment-tag-color-settings"]["font"]) ? esc_attr( $tag["apartment-tag-color-settings"]["font"] ) : "#fff";

								if (!empty($tag_title)) {
										echo '<span class="default-single-tag" style="color: ' . esc_attr( $tag_font_color ) . '; background-color: ' . esc_attr( $tag_background_color ) . '">' . esc_html( $tag_title ) . '</span>';
								}
							}
						}
						?>
					</div>
                    <a href="<?php echo esc_url( $url ); ?>">
						<?php
						if ( ! empty( $thumbnail_html ) ) {
							echo wp_kses_post( $thumbnail_html );
						} elseif ( has_post_thumbnail($post_id) ) {
							echo get_the_post_thumbnail($post_id, 'full' );
						} else {
							echo '<img width="100%" height="100%" src="' . esc_url(TF_ASSETS_APP_URL) . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
						}
						?>
                    </a>
                </div>
				<?php endif; ?>

                <div class="tourfic-single-right">
                    <div class="tf_property_block_main_row">
                        <div class="tf_item_main_block">
							<!-- Title -->
							<?php if( $show_title == 'yes' ): ?>
                            <div class="tf-hotel__title-wrap">
                                <h3 class="tourfic_hotel-title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title($post_id), $title_length ) ); ?></a></h3>
                            </div>
							<?php endif; ?>

							<!-- Location -->
							<?php
							if ( $show_location == 'yes' && !empty($address) ) {
								echo '<div class="tf-map-link">';
								echo '<span class="tf-d-ib">' . wp_kses( $location_icon_html, Helper::tf_custom_wp_kses_allow_tags() ) . wp_kses_post(Helper::tourfic_character_limit_callback( esc_html( $address ), $location_length )) . '</span>';
								echo '</div>';
							}
							?>
                        </div>

						<!-- Reivew -->
						<?php if( $show_review == 'yes' && $disable_review != true ): ?>
							<?php TF_Review::tf_archive_single_rating($post_id, $design); ?>
						<?php endif; ?>
                    </div>

                    <div class="sr_rooms_table_block">
                        <div class="room_details">
                            <div class="featuredRooms">

								<!-- Excerpt -->
								<?php if ( $show_excerpt == 'yes' ) : ?>
                                <div class="prco-ltr-right-align-helper">
                                    <div class="tf-archive-shortdesc">
										<?php echo esc_html( substr( wp_strip_all_tags( get_post_field('post_content', $post_id) ), 0, $excerpt_length ) ) . '...'; ?>
                                    </div>
                                </div>
								<?php endif; ?>

                                <div class="tf_room_name_inner">
                                    <div class="room_link">
                                        <div class="roomrow_flex">

											<!-- Features -->
											<?php if ( $show_features == 'yes' && $features ) : ?>
                                                <div class="roomName_flex">
                                                    <ul class="tf-archive-desc">
														<?php foreach ( array_slice( $features, 0, $features_count ) as $feature ) {
															$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_apartment_feature', true );
															if ( ! empty( $feature_meta ) ) {
																$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
															}
															if ( ! empty( $f_icon_type ) && $f_icon_type == 'icon' ) {
																$feature_icon = ! empty( $feature_meta['apartment-feature-icon'] ) ? '<i class="' . $feature_meta['apartment-feature-icon'] . '"></i>' : '<i class="fas fa-bread-slice"></i>';
															} elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'custom' ) {
																$feature_icon = ! empty( $feature_meta['apartment-feature-icon-custom'] ) ? '<img src="' . $feature_meta['apartment-feature-icon-custom'] . '" style="min-width: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px; height: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px;" />' : '<i class="fas fa-bread-slice"></i>';
															} else {
																$feature_icon = '<i class="fas fa-bread-slice"></i>';
															}
															?>
                                                            <li class="tf-tooltip">
																<?php
																if ( ! empty( $feature_icon ) ) {
																	echo wp_kses_post( $feature_icon );
																} ?>
                                                                <div class="tf-top">
																	<?php echo esc_html( $feature->name ); ?>
                                                                    <i class="tool-i"></i>
                                                                </div>
                                                            </li>
														<?php } ?>
                                                    </ul>
                                                </div>
											<?php endif; ?>

                                            <div class="roomPrice roomPrice_flex sr_discount" style="<?php echo empty( $features ) ? 'text-align:left' : ''; ?>">
                                                <!-- View Details -->
												<?php if($show_view_details == 'yes') : ?>
												<div class="availability-btn-area">
                                                    <a href="<?php echo esc_url( $url ); ?>" class="tf_btn"><?php echo esc_html( $view_details_text ); ?></a>
                                                </div>
												<?php endif; ?>
												
												<!-- Price -->
												<?php if($show_price == 'yes') : ?>
                                                <div class="tf-room-price-area">
                                                    <div class="tf-room-price">
                                                        <?php echo wp_kses_post(Pricing::instance( $post_id )->get_min_price_html()); ?>
                                                    </div>
                                                </div>
												<?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php
		}
	}

	public static function tf_filter_apartment_by_date( $period, array &$not_found, array $data = [] ): void {

		// Form Data
		if ( isset( $data[4] ) && isset( $data[5] ) ) {
			[ $adults, $child, $infant, $check_in_out, $startprice, $endprice ] = $data;
		} else {
			[ $adults, $child, $infant, $check_in_out ] = $data;
		}

		// Get apartment meta options
		$meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );

		// Set initial status
		$has_apartment = false;

		if ( ! empty( $check_in_out ) ) {
			$booked_dates   = self::tf_apartment_booked_days( get_the_ID() );
			$checkInOutDate = explode( ' - ', $check_in_out );
			if ( $checkInOutDate[0] && $checkInOutDate[1] ) {
				$check_in_stt  = strtotime( $checkInOutDate[0] . ' +1 day' );
				$check_out_stt = strtotime( $checkInOutDate[1] );
				$days          = ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1;

				$tfperiod = new \DatePeriod(
					new \DateTime( $checkInOutDate[0] . ' 00:00' ),
					new \DateInterval( 'P1D' ),
					new \DateTime( $checkInOutDate[1] . ' 23:59' )
				);

				$avail_searching_date = [];
				foreach ( $tfperiod as $date ) {
					$avail_searching_date[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
				}

				//skip apartment if min stay is grater than selected days
				if ( ! empty( $meta['min_stay'] ) && intval( $meta['min_stay'] ) <= $days && $meta['min_stay'] != 0 ) {
					if ( ! empty( $meta['max_adults'] ) && $meta['max_adults'] >= $adults && $meta['max_adults'] != 0 ) {
						if ( ! empty( $child ) && ! empty( $meta['max_children'] ) ) {
							if ( ! empty( $meta['max_children'] ) && $meta['max_children'] >= $child && $meta['max_children'] != 0 ) {

								if ( ! empty( $infant ) && ! empty( $meta['max_infants'] ) ) {
									if ( ! empty( $meta['max_infants'] ) && $meta['max_infants'] >= $infant && $meta['max_infants'] != 0 ) {
										if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
											$tf_aprt_booked_dates = [];
											if ( ! empty( $booked_dates ) ) {
												foreach ( $booked_dates as $booked_date ) {
													$booked_from = $booked_date['check_in'];
													$booked_to   = $booked_date['check_out'];

													$tfbookedperiod = new \DatePeriod(
														new \DateTime( $booked_from . ' 00:00' ),
														new \DateInterval( 'P1D' ),
														new \DateTime( $booked_to . ' 23:59' )
													);

													foreach ( $tfbookedperiod as $date ) {
														$tf_aprt_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
													}
												}
											}
											$avil_by_date = !empty($meta['enable_availability']) ? $meta['enable_availability'] : '';
											$apt_availability_dates = !empty($meta['apt_availability']) ? $meta['apt_availability'] : '';
											if(!empty($avil_by_date) && !empty($apt_availability_dates)){
												$tf_check_in_date = 0;
												$searching_period = [];
												// Check if any date range match with search form date range and set them on array
												if ( ! empty( $period ) ) {
													foreach ( $period as $datekey => $date ) {
														if(0==$datekey){
															$tf_check_in_date = $date->format( 'Y/m/d' );
														}
														$searching_period[$date->format( 'Y/m/d' )] = $date->format( 'Y/m/d' );
													}
												}

												$availability_dates = [];
												$tf_check_in_date_price = [];
												// Run loop through custom date range repeater and filter out only the dates
											
												if ( ! empty( $apt_availability_dates ) && gettype( $apt_availability_dates ) == "string" ) {
													$apt_availability_dates = json_decode( $apt_availability_dates, true );
													foreach($apt_availability_dates as $sdate){
														if($tf_check_in_date==$sdate['check_in']){
															$tf_check_in_date_price['price'] = !empty($sdate['price']) ? $sdate['price'] : '';
														}
														if(!array_key_exists($sdate['check_in'], $tf_aprt_booked_dates)){
															$availability_dates[$sdate['check_in']] =  $sdate['check_in'];
														}
													}
												}
												
												$tf_common_dates = array_intersect($availability_dates, $searching_period);
												if (count($tf_common_dates) === count($searching_period)) {
													if ( ! empty( $tf_check_in_date_price['price'] ) ) {
														if ( $startprice <= $tf_check_in_date_price['price'] && $tf_check_in_date_price['price'] <= $endprice ) {
															$has_apartment = true;
														}
													}
												}
											}else{
												if ( ! empty( $meta['price_per_night'] ) && $startprice <= $meta['price_per_night'] && $meta['price_per_night'] <= $endprice ) {
													$tf_booked_dates = [];
													if ( ! empty( $booked_dates ) ) {
														foreach ( $booked_dates as $booked_date ) {
															$booked_from = $booked_date['check_in'];
															$booked_to   = $booked_date['check_out'];

															$tfbookedperiod = new \DatePeriod(
																new \DateTime( $booked_from . ' 00:00' ),
																new \DateInterval( 'P1D' ),
																new \DateTime( $booked_to . ' 23:59' )
															);

															foreach ( $tfbookedperiod as $date ) {
																$tf_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
															}
														}
														foreach ( $avail_searching_date as $searching ) {
															if ( array_key_exists( $searching, $tf_booked_dates ) ) {
																$has_apartment = false;
																break;
															} else {
																$has_apartment = true;
															}
														}
													} else {
														$has_apartment = true;
													}
												}
											}
										} else {

											$tf_aprt_booked_dates = [];
											if ( ! empty( $booked_dates ) ) {
												foreach ( $booked_dates as $booked_date ) {
													$booked_from = $booked_date['check_in'];
													$booked_to   = $booked_date['check_out'];

													$tfbookedperiod = new \DatePeriod(
														new \DateTime( $booked_from . ' 00:00' ),
														new \DateInterval( 'P1D' ),
														new \DateTime( $booked_to . ' 23:59' )
													);

													foreach ( $tfbookedperiod as $date ) {
														$tf_aprt_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
													}
												}
											}
											$avil_by_date = !empty($meta['enable_availability']) ? $meta['enable_availability'] : '';
											$apt_availability_dates = !empty($meta['apt_availability']) ? $meta['apt_availability'] : '';
											if(!empty($avil_by_date) && !empty($apt_availability_dates)){
												$tf_check_in_date = 0;
												$searching_period = [];
												// Check if any date range match with search form date range and set them on array
												if ( ! empty( $period ) ) {
													foreach ( $period as $datekey => $date ) {
														if(0==$datekey){
															$tf_check_in_date = $date->format( 'Y/m/d' );
														}
														$searching_period[$date->format( 'Y/m/d' )] = $date->format( 'Y/m/d' );
													}
												}

												$availability_dates = [];
												$tf_check_in_date_price = [];
												// Run loop through custom date range repeater and filter out only the dates
											
												if ( ! empty( $apt_availability_dates ) && gettype( $apt_availability_dates ) == "string" ) {
													$apt_availability_dates = json_decode( $apt_availability_dates, true );
													foreach($apt_availability_dates as $sdate){
														if($tf_check_in_date==$sdate['check_in']){
															$tf_check_in_date_price['price'] = !empty($sdate['price']) ? $sdate['price'] : '';
														}
														if(!array_key_exists($sdate['check_in'], $tf_aprt_booked_dates)){
															$availability_dates[$sdate['check_in']] =  $sdate['check_in'];
														}
													}
												}
												
												$tf_common_dates = array_intersect($availability_dates, $searching_period);
												if (count($tf_common_dates) === count($searching_period)) {
													$has_apartment = true;
												}
											}else{
												$tf_booked_dates = [];
												if ( ! empty( $booked_dates ) ) {
													foreach ( $booked_dates as $booked_date ) {
														$booked_from = $booked_date['check_in'];
														$booked_to   = $booked_date['check_out'];

														$tfbookedperiod = new \DatePeriod(
															new \DateTime( $booked_from . ' 00:00' ),
															new \DateInterval( 'P1D' ),
															new \DateTime( $booked_to . ' 23:59' )
														);

														foreach ( $tfbookedperiod as $date ) {
															$tf_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
														}
													}
													foreach ( $avail_searching_date as $searching ) {
														if ( array_key_exists( $searching, $tf_booked_dates ) ) {
															$has_apartment = false;
															break;
														} else {
															$has_apartment = true;
														}
													}
												} else {
													$has_apartment = true;
												}
											}
										}
									}
								} else {
									if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
										$tf_aprt_booked_dates = [];
										if ( ! empty( $booked_dates ) ) {
											foreach ( $booked_dates as $booked_date ) {
												$booked_from = $booked_date['check_in'];
												$booked_to   = $booked_date['check_out'];

												$tfbookedperiod = new \DatePeriod(
													new \DateTime( $booked_from . ' 00:00' ),
													new \DateInterval( 'P1D' ),
													new \DateTime( $booked_to . ' 23:59' )
												);

												foreach ( $tfbookedperiod as $date ) {
													$tf_aprt_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
												}
											}
										}
										$avil_by_date = !empty($meta['enable_availability']) ? $meta['enable_availability'] : '';
										$apt_availability_dates = !empty($meta['apt_availability']) ? $meta['apt_availability'] : '';
										if(!empty($avil_by_date) && !empty($apt_availability_dates)){
											$tf_check_in_date = 0;
											$searching_period = [];
											// Check if any date range match with search form date range and set them on array
											if ( ! empty( $period ) ) {
												foreach ( $period as $datekey => $date ) {
													if(0==$datekey){
														$tf_check_in_date = $date->format( 'Y/m/d' );
													}
													$searching_period[$date->format( 'Y/m/d' )] = $date->format( 'Y/m/d' );
												}
											}

											$availability_dates = [];
											$tf_check_in_date_price = [];
											// Run loop through custom date range repeater and filter out only the dates
										
											if ( ! empty( $apt_availability_dates ) && gettype( $apt_availability_dates ) == "string" ) {
												$apt_availability_dates = json_decode( $apt_availability_dates, true );
												foreach($apt_availability_dates as $sdate){
													if($tf_check_in_date==$sdate['check_in']){
														$tf_check_in_date_price['price'] = !empty($sdate['price']) ? $sdate['price'] : '';
													}
													if(!array_key_exists($sdate['check_in'], $tf_aprt_booked_dates)){
														$availability_dates[$sdate['check_in']] =  $sdate['check_in'];
													}
												}
											}
											
											$tf_common_dates = array_intersect($availability_dates, $searching_period);
											if (count($tf_common_dates) === count($searching_period)) {
												if ( ! empty( $tf_check_in_date_price['price'] ) ) {
													if ( $startprice <= $tf_check_in_date_price['price'] && $tf_check_in_date_price['price'] <= $endprice ) {
														$has_apartment = true;
													}
												}
											}
										}else{
											if ( ! empty( $meta['price_per_night'] ) && $startprice <= $meta['price_per_night'] && $meta['price_per_night'] <= $endprice ) {
												$tf_booked_dates = [];
												if ( ! empty( $booked_dates ) ) {
													foreach ( $booked_dates as $booked_date ) {
														$booked_from = $booked_date['check_in'];
														$booked_to   = $booked_date['check_out'];

														$tfbookedperiod = new \DatePeriod(
															new \DateTime( $booked_from . ' 00:00' ),
															new \DateInterval( 'P1D' ),
															new \DateTime( $booked_to . ' 23:59' )
														);

														foreach ( $tfbookedperiod as $date ) {
															$tf_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
														}
													}
													foreach ( $avail_searching_date as $searching ) {
														if ( array_key_exists( $searching, $tf_booked_dates ) ) {
															$has_apartment = false;
															break;
														} else {
															$has_apartment = true;
														}
													}
												} else {
													$has_apartment = true;
												}
											}
										}
									} else {

										$tf_aprt_booked_dates = [];
										if ( ! empty( $booked_dates ) ) {
											foreach ( $booked_dates as $booked_date ) {
												$booked_from = $booked_date['check_in'];
												$booked_to   = $booked_date['check_out'];

												$tfbookedperiod = new \DatePeriod(
													new \DateTime( $booked_from . ' 00:00' ),
													new \DateInterval( 'P1D' ),
													new \DateTime( $booked_to . ' 23:59' )
												);

												foreach ( $tfbookedperiod as $date ) {
													$tf_aprt_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
												}
											}
										}
										$avil_by_date = !empty($meta['enable_availability']) ? $meta['enable_availability'] : '';
										$apt_availability_dates = !empty($meta['apt_availability']) ? $meta['apt_availability'] : '';
										if(!empty($avil_by_date) && !empty($apt_availability_dates)){
											$tf_check_in_date = 0;
											$searching_period = [];
											// Check if any date range match with search form date range and set them on array
											if ( ! empty( $period ) ) {
												foreach ( $period as $datekey => $date ) {
													if(0==$datekey){
														$tf_check_in_date = $date->format( 'Y/m/d' );
													}
													$searching_period[$date->format( 'Y/m/d' )] = $date->format( 'Y/m/d' );
												}
											}

											$availability_dates = [];
											$tf_check_in_date_price = [];
											// Run loop through custom date range repeater and filter out only the dates
										
											if ( ! empty( $apt_availability_dates ) && gettype( $apt_availability_dates ) == "string" ) {
												$apt_availability_dates = json_decode( $apt_availability_dates, true );
												foreach($apt_availability_dates as $sdate){
													if($tf_check_in_date==$sdate['check_in']){
														$tf_check_in_date_price['price'] = !empty($sdate['price']) ? $sdate['price'] : '';
													}
													if(!array_key_exists($sdate['check_in'], $tf_aprt_booked_dates)){
														$availability_dates[$sdate['check_in']] =  $sdate['check_in'];
													}
												}
											}
											
											$tf_common_dates = array_intersect($availability_dates, $searching_period);
											if (count($tf_common_dates) === count($searching_period)) {
												$has_apartment = true;
											}
										}else{
											$tf_booked_dates = [];
											if ( ! empty( $booked_dates ) ) {
												foreach ( $booked_dates as $booked_date ) {
													$booked_from = $booked_date['check_in'];
													$booked_to   = $booked_date['check_out'];

													$tfbookedperiod = new \DatePeriod(
														new \DateTime( $booked_from . ' 00:00' ),
														new \DateInterval( 'P1D' ),
														new \DateTime( $booked_to . ' 23:59' )
													);

													foreach ( $tfbookedperiod as $date ) {
														$tf_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
													}
												}
												foreach ( $avail_searching_date as $searching ) {
													if ( array_key_exists( $searching, $tf_booked_dates ) ) {
														$has_apartment = false;
														break;
													} else {
														$has_apartment = true;
													}
												}
											} else {
												$has_apartment = true;
											}
										}
									}
								}
							}
						} else {
							if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
								$tf_aprt_booked_dates = [];
								if ( ! empty( $booked_dates ) ) {
									foreach ( $booked_dates as $booked_date ) {
										$booked_from = $booked_date['check_in'];
										$booked_to   = $booked_date['check_out'];

										$tfbookedperiod = new \DatePeriod(
											new \DateTime( $booked_from . ' 00:00' ),
											new \DateInterval( 'P1D' ),
											new \DateTime( $booked_to . ' 23:59' )
										);

										foreach ( $tfbookedperiod as $date ) {
											$tf_aprt_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
										}
									}
								}
								$avil_by_date = !empty($meta['enable_availability']) ? $meta['enable_availability'] : '';
								$apt_availability_dates = !empty($meta['apt_availability']) ? $meta['apt_availability'] : '';
								if(!empty($avil_by_date) && !empty($apt_availability_dates)){
									$tf_check_in_date = 0;
									$searching_period = [];
									// Check if any date range match with search form date range and set them on array
									if ( ! empty( $period ) ) {
										foreach ( $period as $datekey => $date ) {
											if(0==$datekey){
												$tf_check_in_date = $date->format( 'Y/m/d' );
											}
											$searching_period[$date->format( 'Y/m/d' )] = $date->format( 'Y/m/d' );
										}
									}

									$availability_dates = [];
									$tf_check_in_date_price = [];
									// Run loop through custom date range repeater and filter out only the dates
								
									if ( ! empty( $apt_availability_dates ) && gettype( $apt_availability_dates ) == "string" ) {
										$apt_availability_dates = json_decode( $apt_availability_dates, true );
										foreach($apt_availability_dates as $sdate){
											if($tf_check_in_date==$sdate['check_in']){
												$tf_check_in_date_price['price'] = !empty($sdate['price']) ? $sdate['price'] : '';
											}
											if(!array_key_exists($sdate['check_in'], $tf_aprt_booked_dates)){
												$availability_dates[$sdate['check_in']] =  $sdate['check_in'];
											}
										}
									}
									
									$tf_common_dates = array_intersect($availability_dates, $searching_period);
									if (count($tf_common_dates) === count($searching_period)) {
										if ( ! empty( $tf_check_in_date_price['price'] ) ) {
											if ( $startprice <= $tf_check_in_date_price['price'] && $tf_check_in_date_price['price'] <= $endprice ) {
												$has_apartment = true;
											}
										}
									}
								}else{
									if ( ! empty( $meta['price_per_night'] ) && $startprice <= $meta['price_per_night'] && $meta['price_per_night'] <= $endprice ) {
										$tf_booked_dates = [];
										if ( ! empty( $booked_dates ) ) {
											foreach ( $booked_dates as $booked_date ) {
												$booked_from = $booked_date['check_in'];
												$booked_to   = $booked_date['check_out'];

												$tfbookedperiod = new \DatePeriod(
													new \DateTime( $booked_from . ' 00:00' ),
													new \DateInterval( 'P1D' ),
													new \DateTime( $booked_to . ' 23:59' )
												);

												foreach ( $tfbookedperiod as $date ) {
													$tf_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
												}
											}
											foreach ( $avail_searching_date as $searching ) {
												if ( array_key_exists( $searching, $tf_booked_dates ) ) {
													$has_apartment = false;
													break;
												} else {
													$has_apartment = true;
												}
											}
										} else {
											$has_apartment = true;
										}
									}
								}
							} else {
								$tf_aprt_booked_dates = [];
								if ( ! empty( $booked_dates ) ) {
									foreach ( $booked_dates as $booked_date ) {
										$booked_from = $booked_date['check_in'];
										$booked_to   = $booked_date['check_out'];

										$tfbookedperiod = new \DatePeriod(
											new \DateTime( $booked_from . ' 00:00' ),
											new \DateInterval( 'P1D' ),
											new \DateTime( $booked_to . ' 23:59' )
										);

										foreach ( $tfbookedperiod as $date ) {
											$tf_aprt_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
										}
									}
								}
								$avil_by_date = !empty($meta['enable_availability']) ? $meta['enable_availability'] : '';
								$apt_availability_dates = !empty($meta['apt_availability']) ? $meta['apt_availability'] : '';
								if(!empty($avil_by_date) && !empty($apt_availability_dates)){
									$tf_check_in_date = 0;
									$searching_period = [];
									// Check if any date range match with search form date range and set them on array
									if ( ! empty( $period ) ) {
										foreach ( $period as $datekey => $date ) {
											if(0==$datekey){
												$tf_check_in_date = $date->format( 'Y/m/d' );
											}
											$searching_period[$date->format( 'Y/m/d' )] = $date->format( 'Y/m/d' );
										}
									}

									$availability_dates = [];
									$tf_check_in_date_price = [];
									// Run loop through custom date range repeater and filter out only the dates
								
									if ( ! empty( $apt_availability_dates ) && gettype( $apt_availability_dates ) == "string" ) {
										$apt_availability_dates = json_decode( $apt_availability_dates, true );
										foreach($apt_availability_dates as $sdate){
											if($tf_check_in_date==$sdate['check_in']){
												$tf_check_in_date_price['price'] = !empty($sdate['price']) ? $sdate['price'] : '';
											}
											if(!array_key_exists($sdate['check_in'], $tf_aprt_booked_dates)){
												$availability_dates[$sdate['check_in']] =  $sdate['check_in'];
											}
										}
									}
									
									$tf_common_dates = array_intersect($availability_dates, $searching_period);
									if (count($tf_common_dates) === count($searching_period)) {
										$has_apartment = true;
									}
								}else{
									$tf_booked_dates = [];
									if ( ! empty( $booked_dates ) ) {
										foreach ( $booked_dates as $booked_date ) {
											$booked_from = $booked_date['check_in'];
											$booked_to   = $booked_date['check_out'];

											$tfbookedperiod = new \DatePeriod(
												new \DateTime( $booked_from . ' 00:00' ),
												new \DateInterval( 'P1D' ),
												new \DateTime( $booked_to . ' 23:59' )
											);

											foreach ( $tfbookedperiod as $date ) {
												$tf_booked_dates[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
											}
										}
										foreach ( $avail_searching_date as $searching ) {
											if ( array_key_exists( $searching, $tf_booked_dates ) ) {
												$has_apartment = false;
												break;
											} else {
												$has_apartment = true;
											}
										}
									} else {
										$has_apartment = true;
									}
								}
							}
						}
					}
				}

			}
		}

		// Conditional apartment showing
		if ( $has_apartment ) {
			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 0,
			);
		} else {
			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 1,
			);
		}

	}

	public static function tf_filter_apartment_without_date( $period, array &$not_found, array $data = [] ): void {

		// Form Data
		if ( isset( $data[4] ) && isset( $data[5] ) ) {
			[ $adults, $child, $infant, $check_in_out, $startprice, $endprice ] = $data;
		} else {
			[ $adults, $child, $infant, $check_in_out ] = $data;
		}

		// Get apartment meta options
		$meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );

		// Set initial status
		$has_apartment = false;

		if ( ! empty( $meta['max_adults'] ) && $meta['max_adults'] >= $adults && $meta['max_adults'] != 0 ) {
			if ( ! empty( $child ) && ! empty( $meta['max_children'] ) ) {
				if ( ! empty( $meta['max_children'] ) && $meta['max_children'] >= $child && $meta['max_children'] != 0 ) {

					if ( ! empty( $infant ) && ! empty( $meta['max_infants'] ) ) {
						if ( ! empty( $meta['max_infants'] ) && $meta['max_infants'] >= $infant && $meta['max_infants'] != 0 ) {
							if ( ! empty( $meta['price_per_night'] ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
								if ( $startprice <= $meta['price_per_night'] && $meta['price_per_night'] <= $endprice ) {
									$has_apartment = true;
								}
							} else {
								$has_apartment = true;
							}
						}
					} else {
						if ( ! empty( $meta['price_per_night'] ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
							if ( $startprice <= $meta['price_per_night'] && $meta['price_per_night'] <= $endprice ) {
								$has_apartment = true;
							}
						} else {
							$has_apartment = true;
						}
					}
				}
			} else {
				if ( ! empty( $meta['price_per_night'] ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
					if ( $startprice <= $meta['price_per_night'] && $meta['price_per_night'] <= $endprice ) {
						$has_apartment = true;
					}
				} else {
					$has_apartment = true;
				}
			}
		}


		// Conditional apartment showing
		if ( $has_apartment ) {
			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 0,
			);
		} else {
			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 1,
			);
		}

	}

	public static function tf_apartment_booked_days( $post_id ) {
		$wc_orders = wc_get_orders( array(
			'post_status' => array( 'wc-completed' ),
			'limit'       => - 1,
		) );

		$booked_days = array();
		foreach ( $wc_orders as $wc_order ) {
			$order_items = $wc_order->get_items();

			foreach ( $order_items as $item_id => $item ) {
				$item_post_id = wc_get_order_item_meta( $item_id, '_post_id', true );
				if ( $item_post_id == $post_id ) {
					$check_in_out_date = wc_get_order_item_meta( $item_id, 'check_in_out_date', true );

					if ( ! empty( $check_in_out_date ) ) {
						$check_in_out_date = explode( ' - ', $check_in_out_date );
						$booked_days[]     = array(
							'check_in'  => $check_in_out_date[0],
							'check_out' => $check_in_out_date[1],
						);
					}
				}
			}
		}

		return $booked_days;
	}

	public static function get_apartment_locations() {

		$locations = array();

		$location_terms = get_terms( array(
			'taxonomy'   => 'apartment_location',
			'hide_empty' => true,
		) );

		foreach ( $location_terms as $location_term ) {
			if ( ! empty( $location_term->slug ) ) {
				$locations[ $location_term->slug ] = $location_term->name;
			}
		}

		return $locations;
	}

	public static function tf_apartment_host_rating( $author_id ) {
		$author_posts = get_posts( array(
			'author'         => $author_id,
			'post_type'      => 'tf_apartment',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		) );

		//get post comments
		$comments_array = array();
		foreach ( $author_posts as $author_post ) {
			$comments_array[] = get_comments( array(
				'post_id' => $author_post->ID,
				'status'  => 'approve',
			) );
		}

		$total_comment_rating = [];
		$comment_count        = 0;
		foreach ( $comments_array as $comments ) {
			if ( ! empty( $comments ) ) {
				$total_comment_rating[] = TF_Review::tf_total_avg_rating( $comments );
			}
			$comment_count += count( $comments );
		}

		if ( $comments ) {
			ob_start();
			?>
            <div class="tf-host-rating-wrapper">
                <i class="fas fa-star"></i>
                <div class="tf-host-rating">
					<?php echo esc_html( TF_Review::tf_average_ratings( array_values( $total_comment_rating ?? [] ) ) ); ?>
                </div>
                <h6>(<?php TF_Review::tf_based_on_text( $comment_count ); ?>)</h6>
            </div>

			<?php
			echo wp_kses_post( ob_get_clean() );
		}
	}

	static function template( $type = 'archive', $post_id = '' ) {
		$apartment_template = '';
		$post_id        = ! empty( $post_id ) ? $post_id : '';

		if ( $type == 'archive' ) {
			$apartment_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['apartment-archive'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['apartment-archive'] : 'design-1';
		} elseif ( $type == 'single' && $post_id ) {
			$meta = get_post_meta( $post_id, 'tf_tours_opt', true );

			$layout_conditions = ! empty( $meta['tf_single_apartment_layout_opt'] ) ? $meta['tf_single_apartment_layout_opt'] : 'global';
			if ( "single" == $layout_conditions ) {
				$single_template = ! empty( $meta['tf_single_apartment_template'] ) ? $meta['tf_single_apartment_template'] : 'design-1';
			}
			$global_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-apartment'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-apartment'] : 'design-1';
			$apartment_template  = ! empty( $single_template ) ? $single_template : $global_template;
		} elseif ( $type == 'single' ) {
			$apartment_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-apartment'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-apartment'] : 'design-1';
		}

		return $apartment_template;
	}

}
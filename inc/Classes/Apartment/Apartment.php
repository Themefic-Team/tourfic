<?php 

namespace Tourfic\Classes\Apartment;

// do not load directly
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

use Tourfic\Classes\Helper;
use Tourfic\Classes\Apartment\Pricing as Apt_Pricing;
use Tourfic\App\TF_Review;

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
		$meta = get_post_meta( sanitize_text_field( $_POST['post_id'] ), 'tf_apartment_opt', true );
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
				if ( $key == sanitize_text_field( $_POST['id'] ) ):
					$tf_room_gallery = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
					?>
                    <div class="tf-hotel-details-qc-gallelry" style="width: 545px;">
						<?php if ( ! empty( $tf_room_gallery ) ) :
							$tf_room_gallery_ids = explode( ',', $tf_room_gallery );
							?>

                            <div class="tf-details-qc-slider tf-details-qc-slider-single">
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
                            <div class="tf-details-qc-slider tf-details-qc-slider-nav">
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

                            <script>
                                jQuery('.tf-details-qc-slider-single').slick({
                                    slidesToShow: 1,
                                    slidesToScroll: 1,
                                    arrows: true,
                                    fade: false,
                                    adaptiveHeight: true,
                                    infinite: true,
                                    useTransform: true,
                                    speed: 400,
                                    cssEase: 'cubic-bezier(0.77, 0, 0.18, 1)',
                                });

                                jQuery('.tf-details-qc-slider-nav')
                                    .on('init', function (event, slick) {
                                        jQuery('.tf-details-qc-slider-nav .slick-slide.slick-current').addClass('is-active');
                                    })
                                    .slick({
                                        slidesToShow: 7,
                                        slidesToScroll: 7,
                                        dots: false,
                                        focusOnSelect: false,
                                        infinite: false,
                                        responsive: [{
                                            breakpoint: 1024,
                                            settings: {
                                                slidesToShow: 5,
                                                slidesToScroll: 5,
                                            }
                                        }, {
                                            breakpoint: 640,
                                            settings: {
                                                slidesToShow: 4,
                                                slidesToScroll: 4,
                                            }
                                        }, {
                                            breakpoint: 420,
                                            settings: {
                                                slidesToShow: 3,
                                                slidesToScroll: 3,
                                            }
                                        }]
                                    });

                                jQuery('.tf-details-qc-slider-single').on('afterChange', function (event, slick, currentSlide) {
                                    jQuery('.tf-details-qc-slider-nav').slick('slickGoTo', currentSlide);
                                    var currrentNavSlideElem = '.tf-details-qc-slider-nav .slick-slide[data-slick-index="' + currentSlide + '"]';
                                    jQuery('.tf-details-qc-slider-nav .slick-slide.is-active').removeClass('is-active');
                                    jQuery(currrentNavSlideElem).addClass('is-active');
                                });

                                jQuery('.tf-details-qc-slider-nav').on('click', '.slick-slide', function (event) {
                                    event.preventDefault();
                                    var goToSingleSlide = jQuery(this).data('slick-index');

                                    jQuery('.tf-details-qc-slider-single').slick('slickGoTo', goToSingleSlide);
                                });
                            </script>
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
				if ( $key == sanitize_text_field( $_POST['id'] ) ):
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
						if ( ! empty( $tf_room_gallery_ids ) ) {
						foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
						$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
						if(!empty($image_url)){
						?>
						<img src="<?php echo esc_url($image_url); ?>" alt="<?php esc_html_e("Room Image","tourfic"); ?>" class="tf-popup-image">
						<?php } } }else{ 
						$aprt_thumbnail_url = get_the_post_thumbnail_url( $_POST['post_id'] );
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
			'status'  => 'success',
			'message' => '',
		];

		if ( Helper::tfopt( 'date_apartment_search' ) && ( ! isset( $_POST['check-in-out-date'] ) || empty( $_POST['check-in-out-date'] ) ) ) {
			$response['message'] = esc_html__( 'Please select a date', 'tourfic' );
			$response['status'] = 'error';
		}

		if ( Helper::tfopt( 'date_apartment_search' ) ) {
			if ( ! empty( $_POST['check-in-out-date'] ) ) {
				$response['query_string'] = str_replace( '&action=tf_apartments_search', '', http_build_query( $_POST ) );
				$response['status']       = 'success';
			}
		}else{
			$response['query_string'] = str_replace( '&action=tf_apartments_search', '', http_build_query( $_POST ) );
			$response['status']       = 'success';
		}

		echo wp_json_encode( $response );
		wp_die();
	}

	public static function tf_apartment_search_form_horizontal( $classes, $title, $subtitle, $advanced, $design ) {
		if ( isset( $_GET ) ) {
			$_GET = array_map( 'stripslashes_deep', $_GET );
		}
		// Check-in & out date
		$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? esc_html( $_GET['check-in-out-date'] ) : '';

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
		<script>
			(function ($) {
				$(document).ready(function () {

					// flatpickr locale first day of Week
					<?php Helper::tf_flatpickr_locale("root"); ?>

					$(".tf_apartment_check_in_out_date").on("click", function(){
						$(".tf-apartment-check-in-out-date").trigger("click");
					});
					$(".tf-apartment-check-in-out-date").flatpickr({
						enableTime: false,
						mode: "range",
						dateFormat: "Y/m/d",
						minDate: "today",

						// flatpickr locale
						<?php Helper::tf_flatpickr_locale(); ?>
						
						onReady: function (selectedDates, dateStr, instance) {
							instance.element.value = dateStr.replace(/[a-z]+/g, '-');
							dateSetToFields(selectedDates, instance);
						},
						onChange: function (selectedDates, dateStr, instance) {
							instance.element.value = dateStr.replace(/[a-z]+/g, '-');
							dateSetToFields(selectedDates, instance);
						},
					});

					function dateSetToFields(selectedDates, instance) {
						if (selectedDates.length === 2) {
							const monthNames = [
								"Jan", "Feb", "Mar", "Apr", "May", "Jun",
								"Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
							];
							if(selectedDates[0]){
								const startDate = selectedDates[0];
								$(".tf_apartment_check_in_out_date .tf_checkin_dates span.date").html(startDate.getDate());
								$(".tf_apartment_check_in_out_date .tf_checkin_dates span.month span").html(monthNames[startDate.getMonth()]);
							}
							if(selectedDates[1]){
								const endDate = selectedDates[1];
								$(".tf_apartment_check_in_out_date .tf_checkout_dates span.date").html(endDate.getDate());
								$(".tf_apartment_check_in_out_date .tf_checkout_dates span.month span").html(monthNames[endDate.getMonth()]);
							}
						}
					}

				});
			})(jQuery);
		</script>
		<?php } else{ ?>
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
                    <button class="tf_button tf-submit btn-styled" type="submit"><?php esc_html_e( apply_filters("tf_apartment_search_form_submit_button_text", 'Search' ), 'tourfic' ); ?></button>
                </div>

            </div>

        </form>

        <script>
            (function ($) {
                $(document).ready(function () {

                    $("#tf_apartment_booking #check-in-out-date").flatpickr({
                        enableTime: false,
                        mode: "range",
                        dateFormat: "Y/m/d",
                        altInput: true,
                        altFormat: '<?php echo esc_html( $date_format_change_apartments ); ?>',
                        minDate: "today",
                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                        }
                    });

                });
            })(jQuery);
        </script>
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
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
			$tf_ext_booking_type = ! empty( $meta['external-booking-type'] ) ? $meta['external-booking-type'] : 1;
			$tf_booking_code = ! empty( $meta['booking-code'] ) ? $meta['booking-code'] : '';
			$tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
			$tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&room={room}';
			$tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
			$tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
			$tf_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
		}

		// date format for apartment
		$date_format_change_appartments = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";

		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$additional_fees = ! empty( $meta['additional_fees'] ) ? Helper::tf_data_types( $meta['additional_fees'] ) : array();
		} else {
			$additional_fee_label = ! empty( $meta['additional_fee_label'] ) ? $meta['additional_fee_label'] : '';
			$additional_fee       = ! empty( $meta['additional_fee'] ) ? $meta['additional_fee'] : 0;
			$fee_type             = ! empty( $meta['fee_type'] ) ? $meta['fee_type'] : '';
		}

		$adults       = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
		$child        = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
		$infant       = ! empty( $_GET['infant'] ) ? sanitize_text_field( $_GET['infant'] ) : '';
		$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? $_GET['check-in-out-date'] : '';
        $check_in_out_arr = explode(" - ", $check_in_out);
        $check_in = ! empty( $check_in_out_arr[0] ) ? $check_in_out_arr[0] : '';
        $check_out = ! empty( $check_in_out_arr[1] ) ? $check_in_out_arr[1] : '';

		$apt_disable_dates = [];
		$tf_apt_enable_dates = [];
		if ( $enable_availability === '1' && ! empty( $apt_availability ) && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
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
				
				<h2 class="tf-section-title"><?php esc_html_e("Available Date", "tourfic"); ?></h2>
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
				<?php $ptype = isset( $_GET['type'] ) ? esc_attr($_GET['type']) : get_post_type(); ?>
                <input type="hidden" name="type" value="<?php echo esc_attr( $ptype ); ?>" class="tf-post-type"/>
                <input type="hidden" name="post_id" value="<?php echo esc_attr( get_the_ID() ); ?>"/>

                <div class="tf-btn-booking">
					<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' && $tf_ext_booking_type == 1 ) || $tf_booking_type == 1 ) : ?>
							<?php if (!empty($apt_reserve_button_text)) : ?>
								<button class="tf_button tf-submit" type="submit"><?php echo esc_html( $apt_reserve_button_text ); ?></button>
						<?php endif; ?>
					<?php elseif( $tf_booking_type == 2 && $tf_hide_booking_form == 1 ): ?>
						<?php if (!empty($apt_reserve_button_text)) : ?>
							<a href="<?php echo esc_url( $tf_booking_url ); ?>"
							class="tf_button tf-submit" <?php echo ! empty( $tf_booking_attribute ) ? esc_attr( $tf_booking_attribute ) : ''; ?> target="_blank"><?php echo esc_html($apt_reserve_button_text ); ?></a>
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

				<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ): ?>
					<?php foreach ( $additional_fees as $key => $additional_fee ) : ?>
                        <li class="additional-fee-wrap" style="display: none">
                            <span class="additional-fee-label tf-price-list-label"><?php echo esc_html( $additional_fee['additional_fee_label'] ); ?></span>
                            <span class="additional-fee-<?php echo esc_attr( $key ) ?> tf-price-list-price"></span>
                        </li>
					<?php endforeach; ?>
				<?php elseif ( ! empty( $additional_fee_label ) && ! empty( $additional_fee ) ): ?>
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
				<?php $ptype = isset( $_GET['type'] ) ? esc_attr($_GET['type']) : get_post_type(); ?>
                <input type="hidden" name="type" value="<?php echo esc_attr( $ptype); ?>" class="tf-post-type"/>
                <input type="hidden" name="post_id" value="<?php echo esc_attr( get_the_ID() ); ?>"/>

                <div class="tf-btn">
					<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' && $tf_ext_booking_type == 1 ) || $tf_booking_type == 1 ) : ?>
                        <?php if (!empty($apt_reserve_button_text)) : ?>
							<button class="tf-btn-normal btn-primary tf-submit" type="submit"><?php echo esc_html( $apt_reserve_button_text ); ?></button>
						<?php endif; ?>
					<?php elseif( $tf_booking_type == 2 && $tf_hide_booking_form == 1 ): ?>
						<?php if (!empty($apt_reserve_button_text)) : ?>
							<a href="<?php echo esc_url( $tf_booking_url ); ?>"
							class="tf-btn-normal btn-primary tf-submit" <?php echo ! empty( $tf_booking_attribute ) ? esc_attr( $tf_booking_attribute ) : ''; ?> target="_blank"><?php echo esc_html( $apt_reserve_button_text ); ?></a>
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

				<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ): ?>
					<?php foreach ( $additional_fees as $key => $additional_fee ) : ?>
                        <li class="additional-fee-wrap" style="display: none">
                            <span class="additional-fee-label tf-price-list-label"><?php echo esc_html( $additional_fee['additional_fee_label'] ); ?></span>
                            <span class="additional-fee-<?php echo esc_attr( $key ) ?> tf-price-list-price"></span>
                        </li>
					<?php endforeach; ?>
				<?php elseif ( ! empty( $additional_fee_label ) && ! empty( $additional_fee ) ): ?>
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
        <script>
            (function ($) {
                $(document).ready(function () {

					// First Day of Week
					<?php Helper::tf_flatpickr_locale("root"); ?>

                    let minStay = <?php echo esc_js( $min_stay ) ?>;

                    const bookingCalculation = (selectedDates) => {
						<?php if ( ( $pricing_type === 'per_night' && ! empty( $price_per_night ) ) || ( $pricing_type === 'per_person' && ! empty( $adult_price ) ) ): ?>
                        //calculate total days
                        if (selectedDates[0] && selectedDates[1]) {
                            var diff = Math.abs(selectedDates[1] - selectedDates[0]);
                            var days = Math.ceil(diff / (1000 * 60 * 60 * 24));
                            if (days > 0) {
                                var pricing_type = '<?php echo esc_js( $pricing_type ); ?>';
                                var price_per_night = <?php echo esc_js( $price_per_night ); ?>;
                                var adult_price = <?php echo esc_js( $adult_price ); ?>;
                                var child_price = <?php echo esc_js( $child_price ); ?>;
                                var infant_price = <?php echo esc_js( $infant_price ); ?>;
                                var enable_availability = '<?php echo esc_js( $enable_availability ); ?>';
                                var apt_availability = '<?php echo wp_kses_post($apt_availability); ?>';

								if(apt_availability) {
									apt_availability = JSON.parse(apt_availability);
								}

                                if (enable_availability !== '1') {
                                    if (pricing_type === 'per_night') {
                                        var total_price = price_per_night * days;
                                        var total_days_price_html = '<?php echo wp_kses_post(wc_price( 0 ));	; ?>';
                                        var wc_price_per_night = '<?php echo wp_kses_post(wc_price( $price_per_night ));	; ?>';
                                        if (total_price > 0) {
											$('.tf-apartment-price-list').show();
                                            $('.total-days-price-wrap').show();
                                            total_days_price_html = '<?php echo wp_kses_post(wc_price( 0 ));	; ?>'.replace('0.00', total_price.toFixed(2));
                                        }
                                        $('.total-days-price-wrap .total-days').html(wc_price_per_night + ' x ' + days + ' <?php esc_html_e( 'nights', 'tourfic' ); ?>');
                                        $('.total-days-price-wrap .days-total-price').html(total_days_price_html);
                                    } else {
                                        let totalPersonPrice = (adult_price * $('#adults').val()) + (child_price * $('#children').val()) + (infant_price * $('#infant').val());
                                        var total_price = totalPersonPrice * days;
                                        var total_days_price_html = '<?php echo wp_kses_post(wc_price( 0 ));	; ?>';
                                        var wc_price_per_person = '<?php echo wp_kses_post(wc_price( 0 ));	; ?>'.replace('0.00', totalPersonPrice.toFixed(2));
                                        if (total_price > 0) {
											$('.tf-apartment-price-list').show();
                                            $('.total-days-price-wrap').show();
                                            total_days_price_html = '<?php echo wp_kses_post(wc_price( 0 ));	; ?>'.replace('0.00', total_price.toFixed(2));
                                        }
                                        $('.total-days-price-wrap .total-days').html(wc_price_per_person + ' x ' + days + ' <?php esc_html_e( 'nights', 'tourfic' ); ?>');
                                        $('.total-days-price-wrap .days-total-price').html(total_days_price_html);
                                    }
                                } else {
                                    var total_price = 0;
                                    var total_price_html = '<?php echo wp_kses_post(wc_price( 0 ));	; ?>';
                                    var checkInDate = new Date(selectedDates[0]);
                                    var checkOutDate = new Date(selectedDates[1]);

                                    for (var date in apt_availability) {
                                        let d = new Date(date);

                                        if (d.getTime() >= checkInDate.getTime() && d.getTime() <= checkOutDate.getTime()) {

                                            if (d.getTime() !== checkInDate.getTime()) {
                                                var availabilityData = apt_availability[date];
                                                var pricing_type = availabilityData.pricing_type;
                                                var price = availabilityData.price ? parseFloat(availabilityData.price) : 0;
                                                var adultPrice = availabilityData.adult_price ? parseFloat(availabilityData.adult_price) : 0;
                                                var childPrice = availabilityData.child_price ? parseFloat(availabilityData.child_price) : 0;
                                                var infantPrice = availabilityData.infant_price ? parseFloat(availabilityData.infant_price) : 0;

                                                if (pricing_type === 'per_night' && price > 0) {
                                                    total_price += price;
                                                } else if (pricing_type === 'per_person') {
                                                    var totalPersonPrice = (adultPrice * $('#adults').val()) + (childPrice * $('#children').val()) + (infantPrice * $('#infant').val());
                                                    total_price += totalPersonPrice;
                                                    // console.log('total_price', total_price);
                                                }
                                            }
                                        }
                                    }

                                    if (total_price > 0) {
                                        $('.tf-apartment-price-list').show();
                                        $('.total-days-price-wrap').show();
                                        total_price_html = '<?php echo wp_kses_post(wc_price( 0 ));	; ?>'.replace('0.00', total_price.toFixed(2));
                                    }
                                    $('.total-days-price-wrap .total-days').html(days + ' <?php esc_html_e( 'nights', 'tourfic' ); ?>');
                                    $('.total-days-price-wrap .days-total-price').html(total_price_html);
                                }
								//discount
                                var discount = <?php echo esc_html( $discount ); ?>;
								var discountType = "<?php echo esc_html( $discount_type ); ?>";
                                var discount_html = '<?php echo wp_kses_post(wc_price( 0 ));	; ?>';
                                if (discount > 0 && discountType != "none") {
                                    $('.apartment-discount-wrap').show();

									<?php if ( $discount_type == 'percent' ): ?>
                                    discount_html = '<?php echo wp_kses_post(wc_price( 0 ));	; ?>'.replace('0.00', (total_price * discount / 100).toFixed(2));
                                    total_price = total_price - (total_price * discount / 100);
									<?php else: ?>
                                    discount_html = '<?php echo wp_kses_post(wc_price( 0 ));	; ?>'.replace('0.00', discount.toFixed(2));
                                    total_price = total_price - discount;
									<?php endif; ?>
                                }
                                $('.apartment-discount-wrap .apartment-discount').html('-' + discount_html);


                                let totalPerson = parseInt($('.tf_acrselection #adults').val()) + parseInt($('.tf_acrselection #children').val()) + parseInt($('.tf_acrselection #infant').val());

                                //additional fee
								<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ): ?>
								<?php foreach ($additional_fees as $key => $item) : ?>
                                let additional_fee_<?php echo esc_html( $key ) ?> = <?php echo esc_html( $item['additional_fee'] ); ?>;
                                let additional_fee_html_<?php echo esc_html( $key ) ?> = '<?php echo wp_kses_post(wc_price( 0 ));	; ?>';
                                let totalAdditionalFee_<?php echo esc_html ( $key ) ?> = 0;

								<?php if ( $item['fee_type'] == 'per_night' ): ?>
                                totalAdditionalFee_<?php echo esc_html( $key ) ?> = additional_fee_<?php echo esc_html( $key ) ?> * days;
								<?php elseif($item['fee_type'] == 'per_person'): ?>
                                totalAdditionalFee_<?php echo esc_html( $key ) ?> = additional_fee_<?php echo esc_html( $key ) ?> * totalPerson;
								<?php else: ?>
                                totalAdditionalFee_<?php echo esc_html( $key ) ?> = additional_fee_<?php echo esc_html( $key ) ?>;
								<?php endif; ?>

                                if (totalAdditionalFee_<?php echo esc_html( $key ) ?> > 0 ) {
                                    $('.additional-fee-wrap').show();
                                    total_price = total_price + totalAdditionalFee_<?php echo esc_html( $key ) ?>;
                                    additional_fee_html_<?php echo esc_html( $key ) ?> = '<?php echo wp_kses_post(wc_price( 0 ));	; ?>'.replace('0.00', totalAdditionalFee_<?php echo esc_html( $key ) ?>.toFixed(2));
                                }
                                $('.additional-fee-wrap .additional-fee-<?php echo esc_html( $key ) ?>').html(additional_fee_html_<?php echo esc_html( $key ) ?>);
								<?php endforeach; ?>
								<?php else: ?>
								<?php if ( ! empty( $additional_fee ) ): ?>
                                let additional_fee = <?php echo esc_html( $additional_fee ); ?>;
                                let additional_fee_html = '<?php echo wp_kses_post(wc_price( 0 ));	; ?>';
                                let totalAdditionalFee = 0;

								<?php if ( $fee_type == 'per_night' ): ?>
                                totalAdditionalFee = additional_fee * days;
								<?php elseif($fee_type == 'per_person'): ?>
                                totalAdditionalFee = additional_fee * totalPerson;
								<?php else: ?>
                                totalAdditionalFee = additional_fee;
								<?php endif; ?>

                                if (totalAdditionalFee > 0) {
                                    $('.additional-fee-wrap').show();
                                    total_price = total_price + totalAdditionalFee;
                                    additional_fee_html = '<?php echo wp_kses_post(wc_price( 0 ));	; ?>'.replace('0.00', totalAdditionalFee.toFixed(2));
                                }
                                $('.additional-fee-wrap .additional-fee').html(additional_fee_html);
								<?php endif; ?>
								<?php endif; ?>
                                //end additional fee

                                //total price
                                var total_price_html = '<?php echo wp_kses_post(wc_price( 0 ));	; ?>';
                                if (total_price > 0) {
                                    $('.total-price-wrap').show();
                                    total_price_html = '<?php echo wp_kses_post(wc_price( 0 ));	; ?>'.replace('0.00', total_price.toFixed(2));
                                }
                                $('.total-price-wrap .total-price').html(total_price_html);
                            } else {
                                $('.tf-apartment-price-list').hide();
                                $('.total-days-price-wrap').hide();
                                $('.additional-fee-wrap').hide();
                                $('.total-price-wrap').hide();
                            }
                        }
						<?php endif; ?>

                        //minimum stay
                        if (selectedDates[0] && selectedDates[1] && minStay > 0) {
                            var diff = Math.abs(selectedDates[1] - selectedDates[0]);
                            var days = Math.ceil(diff / (1000 * 60 * 60 * 24));

                            if (days < minStay) {
                                $('.tf-submit').attr('disabled', 'disabled');
                                $('.tf-submit').addClass('disabled');
                                $('.tf-check-in-out-date .tf_label-row .tf-err-msg, .tf-apartment-design-one-form .tf_booking-dates .tf-err-msg').remove();
								<?php /* translators: %s minimum stay */ ?>
                                $('.tf-check-in-out-date .tf_label-row, .tf-apartment-design-one-form .tf_booking-dates').append('<span class="tf-err-msg"><?php echo sprintf( esc_html__( 'Minimum stay is %s nights', 'tourfic' ), esc_html( $min_stay ) ); ?></span>');
                            } else {
                                $('.tf-submit').removeAttr('disabled');
                                $('.tf-submit').removeClass('disabled');
                                $('.tf-check-in-out-date .tf_label-row .tf-err-msg, .tf-apartment-design-one-form .tf_booking-dates .tf-err-msg').remove();
                            }
                        }
                    }

                    $(".tf-apartment-design-one-form #check-in-date").on('click', function () {
                        $(".tf-check-out-date .form-control").trigger( "click" );
                    });

                    const checkinoutdateange = flatpickr("#tf-apartment-booking #check-in-out-date", {
                        enableTime: false,
                        mode: "range",
                        minDate: "today",
                        altInput: true,
                        altFormat: '<?php echo esc_html( $date_format_change_appartments ); ?>',
                        dateFormat: "Y/m/d",
                        defaultDate: <?php echo wp_json_encode( explode( '-', $check_in_out ) ) ?>,
                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                            bookingCalculation(selectedDates);
                            dateSetToFields(selectedDates, instance);
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                            bookingCalculation(selectedDates);
                            dateSetToFields(selectedDates, instance);
                        }, 
						<?php if (!empty($checked_enable_dates) && is_array($checked_enable_dates)) : ?>
							enable: [ <?php array_walk($checked_enable_dates, function($date) {echo '"'. esc_html( $date ) . '",';}); ?> ],
						<?php endif; ?>
                        disable: [
							<?php foreach ( $booked_dates as $booked_date ) : ?>
								{
									from: "<?php echo esc_html( $booked_date['check_in'] ); ?>",
									to: "<?php echo esc_html( $booked_date['check_out'] ); ?>"
								},
							<?php endforeach; ?>
							<?php foreach ( $apt_disable_dates as $apt_disable_date ) : ?>
								{
									from: "<?php echo esc_html( $apt_disable_date ); ?>",
									to: "<?php echo esc_html( $apt_disable_date ); ?>"
								},
							<?php endforeach; ?>
                        ],
						<?php Helper::tf_flatpickr_locale(); ?>
                    });

					// Need to change the date format
                    function dateSetToFields(selectedDates, instance) {
						
						var dates = instance.altInput.value.split(' - ');

                        if (dates.length === 2) {
                            if (dates[0]) {
                                $(".tf-apartment-design-one-form #check-in-date").val(dates[0]);
                            }
                            if (dates[1]) {
                                $(".tf-apartment-design-one-form #check-out-date").val(dates[1]);
                            }
                        }
                    }

                    $(document).on('change', '.tf_acrselection #adults, .tf_acrselection #children, .tf_acrselection #infant', function () {
                        if ($('#tf-apartment-booking #check-in-out-date').val() !== '') {
                            bookingCalculation(checkinoutdateange.selectedDates);
                        }
                    });
                });
            })(jQuery);

        </script>
		<?php
	}

	public static function tf_apartment_archive_single_item( array $data = [ 1, 0, 0, '' ] ): void {

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
		$apartment_multiple_tags = !empty($meta['tf-apartment-tags']) ? Helper::tf_data_types($meta['tf-apartment-tags']) : [];
		//Discout Info
		$apartment_discount_type = !empty($meta["discount_type"]) ? $meta["discount_type"] : "none";
		$apartment_discount_amount = !empty($meta["discount"]) ? $meta["discount"] : 0;

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

		$apartment_min_price = Apt_pricing::instance( get_the_ID() )->get_min_max_price();
		$tf_apartment_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] : 'default';
		if ( $tf_apartment_arc_selected_template == "design-1" ) {
		$first_gallery_image = explode(',', $gallery);
		?>
		<div class="tf-available-room">
			<div class="tf-available-room-gallery">                       
				<div class="tf-room-gallery">
						<?php
						if ( has_post_thumbnail($post_id) ) {
							echo get_the_post_thumbnail($post_id, 'full' );
						} else {
							echo '<img src="' . esc_url(TF_ASSETS_APP_URL) . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
						}
						?>
				</div>
				<?php 
				if( !empty($gallery_ids) ){ ?>                                                                     
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
					<?php if ( $featured ): ?>
					<span class="tf-available-labels-featured"><?php esc_html_e("Featured", "tourfic"); ?></span>
					<?php endif; ?>
					<?php
						if(sizeof($apartment_multiple_tags) > 0) {
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
				<div class="tf-available-ratings">
					<?php TF_Review::tf_archive_single_rating($post_id); ?>
					<i class="fa-solid fa-star"></i>
				</div>
			</div>
			<div class="tf-available-room-content">
				<div class="tf-available-room-content-left">
					<div class="tf-card-heading-info">
					<div class="tf-section-title-and-location">
						<a href="<?php echo esc_url( get_the_permalink($post_id) ); ?>"><h2 class="tf-section-title"><?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title($post_id), 55 ) ); ?></h2></a>
						<?php
						if ( ! empty( $address ) ) {
						?>
						<div class="tf-title-location">
							<div class="location-icon">
								<i class="ri-map-pin-line"></i>
							</div>
							<span><?php echo esc_html( Helper::tourfic_character_limit_callback( esc_html( $address ), 65 ) ); ?></span>
						</div>
						<?php } ?>
					</div>
					<div class="tf-mobile tf-pricing-info">
						<?php if ( ! empty( $discount_amount ) ){ ?>
							<div class="tf-available-room-off">
								<span>
									<?php echo esc_html( min( $discount_amount ) ); ?>% <?php esc_html_e( "Off ", "tourfic" ); ?>
								</span>
							</div>
						<?php } ?>
						<div class="tf-available-room-price">
							<span class="tf-price-from">
							<?php
							if(!empty($apartment_min_price['min'])){
								echo esc_html__( "From ", "tourfic" );
								echo wp_kses_post(wc_price( $apartment_min_price['min'] ));	;
							}
							?>
							</span>
						</div>
					</div>
					</div>
					<?php if ( $features ) { ?>
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
					if ( $tfkey < 5 ) {
					?>
						<li>
						<?php
						if ( ! empty( $feature_icon ) ) {
							echo wp_kses_post( $feature_icon );
						} ?>
						<?php echo esc_html( $feature->name ); ?>
						</li>
					<?php } } ?>
					<?php if(count($features)>5){ ?>
						<li><a href="<?php echo esc_url( $url ); ?>"><?php esc_html_e("View More", "tourfic"); ?></a></li>
					<?php } ?>
					</ul>
					<?php } ?>
				</div>
				<div class="tf-available-room-content-right">
					<div class="tf-card-pricing-heading">
					<?php
					if ( ! empty( $apartment_discount_amount ) && $apartment_discount_type!="none" ){ ?>
						<div class="tf-available-room-off">
							<span>
								<?php echo $apartment_discount_type=="percent" ? esc_html( $apartment_discount_amount ).'%' : wp_kses_post(wc_price($apartment_discount_amount));	 ?> <?php esc_html_e( "Off ", "tourfic" ); ?>
							</span>
						</div>
					<?php } ?>
					<div class="tf-available-room-price">
						<span class="tf-price-from">
						<?php
							if(!empty($apartment_min_price['min'])){
								echo esc_html__( "From ", "tourfic" );
								echo wp_kses_post(wc_price( $apartment_min_price['min'] ));	;
							}
						?>
						</span>
					</div>
					</div>              
					<a href="<?php echo esc_url( $url ); ?>" class="view-hotel"><?php esc_html_e("See details", "tourfic"); ?></a>
				</div>
			</div>
		</div>
		<?php }else{ ?>
        <div class="single-tour-wrap <?php echo $featured ? esc_attr( 'tf-featured' ) : '' ?>">
            <div class="single-tour-inner">
				<?php if ( $featured ): ?>
                    <div class="tf-featured-badge">
                        <span><?php echo ! empty( $meta['featured_text'] ) ? esc_html( $meta['featured_text'] ) : esc_html( "HOT DEAL" ); ?></span>
                    </div>
				<?php endif; ?>
                <div class="tourfic-single-left">
                	<div class="default-tags-container">

					<?php
					if(sizeof($apartment_multiple_tags) > 0) {
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
						if ( has_post_thumbnail($post_id) ) {
							echo get_the_post_thumbnail($post_id, 'full' );
						} else {
							echo '<img width="100%" height="100%" src="' . esc_url(TF_ASSETS_APP_URL) . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
						}
						?>
                    </a>
                </div>
                <div class="tourfic-single-right">
                    <div class="tf_property_block_main_row">
                        <div class="tf_item_main_block">
                            <div class="tf-hotel__title-wrap">
                                <a href="<?php echo esc_url( $url ); ?>"><h3 class="tourfic_hotel-title"><?php echo esc_html( get_the_title($post_id) ); ?></h3></a>
                            </div>
							<?php
							if ( !empty($address) ) {
								echo '<div class="tf-map-link">';
								echo '<span class="tf-d-ib"><i class="fas fa-map-marker-alt"></i> ' . strlen($address) > 75 ? esc_html( Helper::tourfic_character_limit_callback($address, 76) ) : esc_html( $address ) . '</span>';
								echo '</div>';
							}
							?>
                        </div>
						<?php TF_Review::tf_archive_single_rating($post_id); ?>
                    </div>

                    <div class="sr_rooms_table_block">
                        <div class="room_details">
                            <div class="featuredRooms">
                                <div class="prco-ltr-right-align-helper">
                                    <div class="tf-archive-shortdesc">
										<?php echo esc_html( substr( wp_strip_all_tags( get_post_field('post_content', $post_id) ), 0, 160 ) ) . '...'; ?>
                                    </div>
                                </div>
                                <div class="roomNameInner">
                                    <div class="room_link">
                                        <div class="roomrow_flex">
											<?php if ( $features ) { ?>
                                                <div class="roomName_flex">
                                                    <ul class="tf-archive-desc">
														<?php foreach ( $features as $feature ) {
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
											<?php } ?>
                                            <div class="roomPrice roomPrice_flex sr_discount" style="<?php echo empty( $features ) ? 'text-align:left' : ''; ?>">
                                                <div class="availability-btn-area">
                                                    <a href="<?php echo esc_url( $url ); ?>" class="tf_button btn-styled"><?php esc_html_e( 'View Details', 'tourfic' ); ?></a>
                                                </div>
                                                <!-- Show minimum price @author - Hena -->
                                                <div class="tf-room-price-area">
                                                    <div class="tf-room-price">
                                                        <h6 class="tf-apartment-price-per-night">
                                                            <span class="tf-apartment-base-price"><?php echo wp_kses_post(wc_price( $apartment_min_price['min'] ));	 ?></span>
                                                            <span><?php echo $pricing_type === 'per_night' ? esc_html__( '/per night', 'tourfic' ) : esc_html__( '/per person', 'tourfic' ) ?></span>
                                                        </h6>
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

											$tfbookedperiod = new DatePeriod(
												new DateTime( $booked_from . ' 00:00' ),
												new DateInterval( 'P1D' ),
												new DateTime( $booked_to . ' 23:59' )
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

}
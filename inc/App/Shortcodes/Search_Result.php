<?php

namespace Tourfic\App\Shortcodes;

defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Car_Rental\Availability;
use Tourfic\Classes\Hotel\Hotel;
use \Tourfic\Classes\Hotel\Pricing as hotelPricing;
use \Tourfic\Classes\Tour\Pricing as tourPricing;
use \Tourfic\Classes\Apartment\Pricing as apartmentPricing;
use Tourfic\Classes\Tour\Tour;
use \Tourfic\Classes\Apartment\Apartment;

class Search_Result extends \Tourfic\Core\Shortcodes {

	use \Tourfic\Traits\Singleton;

	protected $shortcode = 'tf_search_result';

	function render( $atts, $content = null ) {

		// Get post type
		$post_type = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '';
		if ( empty( $post_type ) ) {
			echo '<h3>' . esc_html__(" Please select fields from the search form! ", "tourfic") . '</h3>';

			return;
		}
		// Get hotel location or tour destination
		$taxonomy     = $post_type == 'tf_hotel' ? 'hotel_location' : ( $post_type == 'tf_tours' ? 'tour_destination' : 'apartment_location' );
		$place        = isset( $_GET['place'] ) ? sanitize_text_field( $_GET['place'] ) : '';
		$adults       = isset( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
		$child        = isset( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
		$infant       = isset( $_GET['infant'] ) ? sanitize_text_field( $_GET['infant'] ) : '';
		$room         = isset( $_GET['room'] ) ? sanitize_text_field( $_GET['room'] ) : '';
		$check_in_out = isset( $_GET['check-in-out-date'] ) ? sanitize_text_field( $_GET['check-in-out-date'] ) : '';
		//get children ages
		//$children_ages = isset( $_GET['children_ages'] ) ? sanitize_text_field($_GET['children_ages']) : '';


		// Price Range
		$startprice = isset( $_GET['from'] ) ? absint( sanitize_text_field( $_GET['from'] ) ) : '';
		$endprice   = isset( $_GET['to'] ) ? absint( sanitize_text_field( $_GET['to'] ) ) : '';

		// Cars Data Start
		$pickup   = isset( $_GET['pickup'] ) ? sanitize_text_field( $_GET['pickup'] ) : '';
		$dropoff = isset( $_GET['dropoff'] ) ? sanitize_text_field( $_GET['dropoff'] ) : '';

		$tf_pickup_date  = isset( $_GET['pickup-date'] ) ? sanitize_text_field( $_GET['pickup-date'] ) : '';
		$tf_dropoff_date  = isset( $_GET['dropoff-date'] ) ? sanitize_text_field( $_GET['dropoff-date'] ) : '';
		$tf_pickup_time  = isset( $_GET['pickup-time'] ) ? sanitize_text_field( $_GET['pickup-time'] ) : '';
		$tf_dropoff_time  = isset( $_GET['dropoff-time'] ) ? sanitize_text_field( $_GET['dropoff-time'] ) : '';
		// Cars Data End

		// Author Id if any
		$tf_author_ids = isset( $_GET['tf-author'] ) ? sanitize_key( $_GET['tf-author'] ) : '';

		if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
			if ( $_GET['type'] == "tf_tours" ) {
				$data = array( $adults, $child, $check_in_out, $startprice, $endprice );
			} elseif ( $_GET['type'] == "tf_apartment" ) {
				$data = array( $adults, $child, $infant, $check_in_out, $startprice, $endprice );
			} else {
				$data = array( $adults, $child, $room, $check_in_out, $startprice, $endprice );
			}
		} else {
			if ( $_GET['type'] == "tf_tours" ) {
				$data = array( $adults, $child, $check_in_out );
			} else {
				$data = array( $adults, $child, $room, $check_in_out );
			}
		}

		// Gird or List View
		if(!empty($_GET['type']) && $_GET['type'] == "tf_hotel"){
			$tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_view'] : 'list';
		}elseif(!empty($_GET['type']) && $_GET['type'] == "tf_tours"){
			$tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_view'] : 'list';
		}elseif(!empty($_GET['type']) && $_GET['type'] == "tf_apartment"){
			$tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_view'] : 'list';
		}elseif(!empty($_GET['type']) && $_GET['type'] == "tf_carrental"){
			$tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_view'] : 'grid';
		}else{

		}

		$paged          = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
		$checkInOutDate = ! empty( $_GET['check-in-out-date'] ) ? explode( ' - ', sanitize_text_field( wp_unslash($_GET['check-in-out-date'])) ) : '';
		if ( ! empty( $checkInOutDate ) ) {
			$period = new \DatePeriod(
				new \DateTime( $checkInOutDate[0] ),
				new \DateInterval( 'P1D' ),
				new \DateTime( ! empty( $checkInOutDate[1] ) ? $checkInOutDate[1] : $checkInOutDate[0] . '23:59' )
			);
		} else {
			$period = '';
		}

		$post_per_page = Helper::tfopt( 'posts_per_page' ) ? Helper::tfopt( 'posts_per_page' ) : 10;
		// Main Query args
		if ( $post_type == "tf_tours" ) {
			$tf_expired_tour_showing = ! empty( Helper::tfopt( 't-show-expire-tour' ) ) ? Helper::tfopt( 't-show-expire-tour' ) : '';
			if ( ! empty( $tf_expired_tour_showing ) ) {
				$tf_tour_posts_status = array( 'publish', 'expired' );
			} else {
				$tf_tour_posts_status = array( 'publish' );
			}
			$args = array(
				'post_type'      => $post_type,
				'post_status'    => $tf_tour_posts_status,
				'posts_per_page' => - 1,
				'author'         => $tf_author_ids,
			);
		} else {
			$args = array(
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'author'         => $tf_author_ids,
			);
		}

		$taxonomy_query = new \WP_Term_Query( array(
			'taxonomy'   => $taxonomy,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'hide_empty' => false,
			'slug'       => sanitize_title( $place, '' ),
		) );

		if ( $taxonomy_query ) {

			$place_ids = array();

			// Place IDs array
			foreach ( $taxonomy_query->get_terms() as $term ) {
				$place_ids[] = $term->term_id;
			}

			$args['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => $taxonomy,
					'terms'    => $place_ids,
				)
			);

		} else {
			$args['s'] = $place;
		}


		// Hotel/Apartment Features
		if ( ! empty( $_GET['features'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => $post_type == 'tf_hotel' ? 'hotel_feature' : 'apartment_feature',
				'field'    => 'slug',
				'terms'    => sanitize_text_field( wp_unslash($_GET['features']) ),
			);
		}
		// Hotel/Tour/Apartment Types
		if ( ! empty( $_GET['types'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => $post_type == 'tf_hotel' ? 'hotel_type' : ($post_type == 'tf_tours' ? 'tour_type' : 'apartment_type'),
				'field'    => 'slug',
				'terms'    => sanitize_text_field( wp_unslash($_GET['types']) ),
			);
		}

		// Car Data Filter Start
		if(!empty($pickup)){
			$args['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'carrental_location',
					'field'    => 'slug',
					'terms'    => sanitize_title( $pickup, '' ),
				),
			);
		}

		if(!empty($startprice) && !empty($endprice) && $post_type == 'tf_carrental'){
			$args['meta_query'] = array(
				array(
					'key' => 'tf_search_car_rent',
					'value'    => [$startprice, $endprice],
					'compare'    => 'BETWEEN',
					'type' => 'DECIMAL(10,3)'
				),
			);
		}
		$car_driver_min_age = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['car_archive_driver_min_age'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['car_archive_driver_min_age'] : 18;
        $car_driver_max_age = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['car_archive_driver_max_age'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['car_archive_driver_max_age'] : 40;
		if(!empty($_GET['driver_age']) && 'on'==$_GET['driver_age'] && $post_type == 'tf_carrental'){
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
		// Car Data Filter End

		$loop        = new \WP_Query( $args );
		$total_posts = $loop->found_posts;
		ob_start(); ?>
		<!-- Start Content -->
		<?php

		$tf_tour_arc_selected_template  = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['tour-archive'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['tour-archive'] : 'design-1';
		$tf_hotel_arc_selected_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['hotel-archive'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['hotel-archive'] : 'design-1';
		$tf_apartment_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] : 'default';
		$tf_car_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car-archive'] : 'design-1';

		if ( ( $post_type == "tf_tours" && $tf_tour_arc_selected_template == "design-1" ) || ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template == "design-1" ) ) {
			?>
			<div class="tf-page-content tf-archive-left tf-result-previews">
				<!-- Search Head Section -->
				<div class="tf-archive-head tf-flex tf-flex-align-center tf-flex-space-bttn">
					<div class="tf-search-result tf-flex">
						<span class="tf-counter-title"><?php echo esc_html__( 'Total Results ', 'tourfic' ); ?> </span>
						<span><?php echo ' ('; ?> </span>
						<div class="tf-total-results">
							<span><?php echo esc_html( $total_posts ); ?> </span>
						</div>
						<span><?php echo ')'; ?> </span>
					</div>
					<div class="tf-search-layout tf-flex tf-flex-gap-12">
						<div class="tf-icon tf-serach-layout-list tf-grid-list-layout <?php echo $tf_defult_views=="list" ? esc_attr('active') : ''; ?>" data-id="list-view">
							<div class="defult-view">
								<svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
									<rect width="12" height="2" fill="#0E3DD8"/>
									<rect x="14" width="2" height="2" fill="#0E3DD8"/>
									<rect y="5" width="12" height="2" fill="#0E3DD8"/>
									<rect x="14" y="5" width="2" height="2" fill="#0E3DD8"/>
									<rect y="10" width="12" height="2" fill="#0E3DD8"/>
									<rect x="14" y="10" width="2" height="2" fill="#0E3DD8"/>
								</svg>
							</div>
							<div class="active-view">
								<svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
									<rect width="12" height="2" fill="white"/>
									<rect x="14" width="2" height="2" fill="white"/>
									<rect y="5" width="12" height="2" fill="white"/>
									<rect x="14" y="5" width="2" height="2" fill="white"/>
									<rect y="10" width="12" height="2" fill="white"/>
									<rect x="14" y="10" width="2" height="2" fill="white"/>
								</svg>
							</div>
						</div>
						<div class="tf-icon tf-serach-layout-grid tf-grid-list-layout <?php echo $tf_defult_views=="grid" ? esc_attr('active') : ''; ?>" data-id="grid-view">
							<div class="defult-view">
								<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
									<rect x="10" width="2" height="2" fill="#0E3DD8"/>
									<rect x="10" y="5" width="2" height="2" fill="#0E3DD8"/>
									<rect x="10" y="10" width="2" height="2" fill="#0E3DD8"/>
									<rect x="5" width="2" height="2" fill="#0E3DD8"/>
									<rect x="5" y="5" width="2" height="2" fill="#0E3DD8"/>
									<rect x="5" y="10" width="2" height="2" fill="#0E3DD8"/>
									<rect width="2" height="2" fill="#0E3DD8"/>
									<rect y="5" width="2" height="2" fill="#0E3DD8"/>
									<rect y="10" width="2" height="2" fill="#0E3DD8"/>
								</svg>
							</div>
							<div class="active-view">
								<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
									<rect x="10" width="2" height="2" fill="white"/>
									<rect x="10" y="5" width="2" height="2" fill="white"/>
									<rect x="10" y="10" width="2" height="2" fill="white"/>
									<rect x="5" width="2" height="2" fill="white"/>
									<rect x="5" y="5" width="2" height="2" fill="white"/>
									<rect x="5" y="10" width="2" height="2" fill="white"/>
									<rect width="2" height="2" fill="white"/>
									<rect y="5" width="2" height="2" fill="white"/>
									<rect y="10" width="2" height="2" fill="white"/>
								</svg>
							</div>
						</div>
						<div class="tf-sorting-selection-warper">
                            <form class="tf-archive-ordering" method="get">
                                <select class="tf-orderby" name="tf-orderby" id="tf-orderby">
                                    <option value="default">Default Sorting</option>
                                    <option value="enquiry">Sort By Recommended</option>
                                    <option value="order">Sort By Popularity</option>
                                    <option value="rating">Sort By Average Rating</option>
                                    <option value="latest">Sort By Latest</option>
                                    <option value="price-high">Sort By Price: High to Low</option>
                                    <option value="price-low">Sort By Price: Low to High</option>
                                </select>
								<i class="fas fa-chevron-down"></i>
                            </form>
                        </div>
					</div>
				</div>
				<!-- Loader Image -->
				<div id="tf_ajax_searchresult_loader">
					<div id="tf-searchresult-loader-img">
						<img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
					</div>
				</div>
				<div class="tf-search-results-list tf-mt-30">
					<div class="archive_ajax_result tf-item-cards tf-flex <?php echo $tf_defult_views=="list" ? esc_attr('tf-layout-list') : esc_attr('tf-layout-grid'); ?>">
						<?php
						if ( $loop->have_posts() ) {
							$not_found = [];
							while ( $loop->have_posts() ) {
								$loop->the_post();

								if ( $post_type == 'tf_hotel' ) {

									if ( empty( $check_in_out ) ) {
										Hotel::tf_filter_hotel_without_date( $period, $not_found, $data );
									} else {
										Hotel::tf_filter_hotel_by_date( $period, $not_found, $data );
									}

								} else {

									if ( empty( $check_in_out ) ) {
										Tour::tf_filter_tour_by_without_date( $period, $total_posts, $not_found, $data );
									} else {
										Tour::tf_filter_tour_by_date( $period, $total_posts, $not_found, $data );
									}
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
								echo '<div class="tf-nothing-found" data-post-count="0">' . esc_html__( 'Nothing Found!', 'tourfic' ) . '</div>';
							}
							$post_per_page = Helper::tfopt( 'posts_per_page' ) ? Helper::tfopt( 'posts_per_page' ) : 10;
							// Main Query args
							$filter_args = array(
								'post_type'      => $post_type,
								'post_status'    => 'publish',
								'posts_per_page' => $post_per_page,
								'paged'          => $paged,
							);


							$total_filtered_results = count( $tf_total_filters );
							$current_page           = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
							$offset                 = ( $current_page - 1 ) * $post_per_page;
							$displayed_results      = array_slice( $tf_total_filters, $offset, $post_per_page );
							if ( ! empty( $displayed_results ) ) {
								$filter_args = array(
									'post_type'      => $post_type,
									'post_status'    => 'publish',
									'posts_per_page' => $post_per_page,
									'post__in'       => $displayed_results,
								);


								$result_query = new \WP_Query( $filter_args );
								if ( $result_query->have_posts() ) {
									// Feature Posts
									while ( $result_query->have_posts() ) {
										$result_query->the_post();

										if ( $post_type == 'tf_hotel' ) {
											$hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );

											if ( ! empty( $data ) ) {
												if ( isset( $data[4] ) && isset( $data[5] ) ) {
													[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
													if( $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice );
												} else {
													[ $adults, $child, $room, $check_in_out ] = $data;
													if( $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
												}
											} else {
												if( $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item();
											}

										} elseif ( $post_type == 'tf_tours' ) {
											$tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );
											if ( ! empty( $data ) ) {
												if ( isset( $data[3] ) && isset( $data[4] ) ) {
													[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
													if( $tour_meta["tour_as_featured"] ) Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice );
												} else {
													[ $adults, $child, $check_in_out ] = $data;
													if( $tour_meta["tour_as_featured"] ) Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out );
												}
											} else {
												if( $tour_meta["tour_as_featured"] ) Tour::tf_tour_archive_single_item();
											}
										} else {
											$apartment_meta = get_post_meta( get_the_ID() , 'tf_apartment_opt', true );
											if ( ! empty( $data ) ) {
												if ( isset( $data[4] ) && isset( $data[5] ) ) {
													if( $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item( $data );
												} else {
													if( $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item( $data );
												}
											} else {
												if( $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item();
											}
										}

									}
									// Other Posts
									while ( $result_query->have_posts() ) {
										$result_query->the_post();

										if ( $post_type == 'tf_hotel' ) {
											$hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );
											if ( ! empty( $data ) ) {
												if ( isset( $data[4] ) && isset( $data[5] ) ) {
													[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
													if( ! $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice );
												} else {
													[ $adults, $child, $room, $check_in_out ] = $data;
													if( ! $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
												}
											} else {
												if( ! $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item();
											}

										} elseif ( $post_type == 'tf_tours' ) {
											$tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );
											if ( ! empty( $data ) ) {
												if ( isset( $data[3] ) && isset( $data[4] ) ) {
													[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
													if( ! $tour_meta["tour_as_featured"] ) Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice );
												} else {
													[ $adults, $child, $check_in_out ] = $data;
													if( ! $tour_meta["tour_as_featured"] ) Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out );
												}
											} else {
												if( ! $tour_meta["tour_as_featured"] )	Tour::tf_tour_archive_single_item();
											}
										} else {
											$apartment_meta = get_post_meta( get_the_ID() , 'tf_apartment_opt', true );
											if ( ! empty( $data ) ) {
												if ( isset( $data[4] ) && isset( $data[5] ) ) {
													if( ! $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item( $data );
												} else {
													if( ! $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item( $data );
												}
											} else {
												if( ! $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item();
											}
										}

									}
								}
								$total_pages = ceil( $total_filtered_results / $post_per_page );
								echo "<div class='tf_posts_navigation tf_posts_page_navigation'>";
								echo wp_kses_post(
									paginate_links( array(
										'total'   => $total_pages,
										'current' => $current_page
									) )
								);
								echo "</div>";
							}

						} else {
							echo '<div class="tf-nothing-found" data-post-count="0">' . esc_html__( 'Nothing Found!', 'tourfic' ) . '</div>';
						}
						echo "<span hidden=hidden class='tf-posts-count'>";
						echo ! empty( $tf_total_results ) ? esc_html( $tf_total_results ) : 0;
						echo "</span>";
						?>

					</div>
				</div>
			</div>
			<?php
		} elseif ( ( $post_type == "tf_tours" && $tf_tour_arc_selected_template == "design-2" ) ||
                   ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template == "design-2" ) ||
                   ( $post_type == "tf_apartment" && $tf_apartment_arc_selected_template == "design-1" ) ) { ?>
			<div class="tf-available-archive-hetels-wrapper tf-available-rooms-wrapper" id="tf-hotel-rooms">
				<div class="tf-archive-available-rooms-head tf-available-rooms-head">
					<h3 class="tf-total-results">
							<?php esc_html_e("Total", "tourfic"); ?> <span><?php echo esc_html( $total_posts ); ?></span>
						<?php if($post_type == "tf_hotel"){
							esc_html_e("hotels available", "tourfic");
						}elseif($post_type == "tf_apartment"){
							esc_html_e("apartments available", "tourfic");
						}else{
							esc_html_e("tours available", "tourfic");
						} ?>
					</h3>
					<div class="tf-sorting-selection-warper">
						<form class="tf-archive-ordering" method="get">
							<select class="tf-orderby" name="tf-orderby" id="tf-orderby">
								<option value="default"><?php echo esc_html__( 'Default Sorting', 'tourfic' ); ?></option>
								<option value="enquiry"><?php echo esc_html__( 'Sort By Recommended', 'tourfic' ); ?></option>
								<option value="order"><?php echo esc_html__( 'Sort By Popularity', 'tourfic' ); ?></option>
								<option value="rating"><?php echo esc_html__( 'Sort By Average Rating', 'tourfic' ); ?></option>
								<option value="latest"><?php echo esc_html__( 'Sort By Latest', 'tourfic' ); ?></option>
								<option value="price-high"><?php echo esc_html__( 'Sort By Price: High to Low', 'tourfic' ); ?></option>
								<option value="price-low"><?php echo esc_html__( 'Sort By Price: Low to High', 'tourfic' ); ?></option>
							</select>
							<i class="fas fa-chevron-down"></i>
						</form>
					</div>
					<div class="tf-archive-filter-showing">
						<i class="ri-equalizer-line"></i>
					</div>
					<h3 class="tf-total-results tf-mobile-results">
							<?php esc_html_e("Total", "tourfic"); ?> <span><?php echo esc_html( $total_posts ); ?></span>
						<?php if($post_type == "tf_hotel"){
							esc_html_e("hotels available", "tourfic");
						}elseif($post_type == "tf_apartment"){
							esc_html_e("apartments available", "tourfic");
						}else{
							esc_html_e("tours available", "tourfic");
						} ?>
					</h3>
				</div>

				<!-- Loader Image -->
				<div id="tour_room_details_loader">
					<div id="tour-room-details-loader-img">
						<img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
					</div>
				</div>

				<!--Available rooms start -->
				<div class="tf-archive-available-rooms tf-available-rooms archive_ajax_result">

					<?php
					if ( $loop->have_posts() ) {
						$not_found = [];
						while ( $loop->have_posts() ) {
							$loop->the_post();

							if ( $post_type == 'tf_hotel' ) {

								if ( empty( $check_in_out ) ) {
									Hotel::tf_filter_hotel_without_date( $period, $not_found, $data );
								} else {
									Hotel::tf_filter_hotel_by_date( $period, $not_found, $data );
								}

							} elseif( $post_type == 'tf_tours' ) {

								if ( empty( $check_in_out ) ) {
									Tour::tf_filter_tour_by_without_date( $period, $total_posts, $not_found, $data );
								} else {
									Tour::tf_filter_tour_by_date( $period, $total_posts, $not_found, $data );
								}
							}else {
								if ( empty( $check_in_out ) ) {
									Apartment::tf_filter_apartment_without_date( $period, $not_found, $data );
								} else {
									Apartment::tf_filter_apartment_by_date( $period, $not_found, $data );
								}
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
							echo '<div class="tf-nothing-found" data-post-count="0">' . esc_html__( 'Nothing Found!', 'tourfic' ) . '</div>';
						}
						$post_per_page = Helper::tfopt( 'posts_per_page' ) ? Helper::tfopt( 'posts_per_page' ) : 10;
						// Main Query args
						$filter_args = array(
							'post_type'      => $post_type,
							'post_status'    => 'publish',
							'posts_per_page' => $post_per_page,
							'paged'          => $paged,
						);


						$total_filtered_results = count( $tf_total_filters );
						$current_page           = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
						$offset                 = ( $current_page - 1 ) * $post_per_page;
						$displayed_results      = array_slice( $tf_total_filters, $offset, $post_per_page );
						if ( ! empty( $displayed_results ) ) {
							$filter_args = array(
								'post_type'      => $post_type,
								'post_status'    => 'publish',
								'posts_per_page' => $post_per_page,
								'post__in'       => $displayed_results,
							);

							$result_query = new \WP_Query( $filter_args );
							if ( $result_query->have_posts() ) {
								// Feature Posts
								while ( $result_query->have_posts() ) {
									$result_query->the_post();

									if ( $post_type == 'tf_hotel' ) {
										$hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );

										if ( ! empty( $data ) ) {
											if ( isset( $data[4] ) && isset( $data[5] ) ) {
												[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
												if( $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice );
											} else {
												[ $adults, $child, $room, $check_in_out ] = $data;
												if( $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
											}
										} else {
											if( $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item();
										}

									} elseif ( $post_type == 'tf_tours' ) {
										$tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );
										if ( ! empty( $data ) ) {
											if ( isset( $data[3] ) && isset( $data[4] ) ) {
												[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
												if( $tour_meta["tour_as_featured"] ) Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice );
											} else {
												[ $adults, $child, $check_in_out ] = $data;
												if( $tour_meta["tour_as_featured"] ) Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out );
											}
										} else {
											if( $tour_meta["tour_as_featured"] ) Tour::tf_tour_archive_single_item();
										}
									} else {
										$apartment_meta = get_post_meta( get_the_ID() , 'tf_apartment_opt', true );
										if ( ! empty( $data ) ) {
											if ( isset( $data[4] ) && isset( $data[5] ) ) {
												if( $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item( $data );
											} else {
												if( $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item( $data );
											}
										} else {
											if( $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item();
										}
									}

								}
								// Other Posts
								while ( $result_query->have_posts() ) {
									$result_query->the_post();

									if ( $post_type == 'tf_hotel' ) {
										$hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );
										if ( ! empty( $data ) ) {
											if ( isset( $data[4] ) && isset( $data[5] ) ) {
												[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
												if( ! $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice );
											} else {
												[ $adults, $child, $room, $check_in_out ] = $data;
												if( ! $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
											}
										} else {
											if( ! $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item();
										}

									} elseif ( $post_type == 'tf_tours' ) {
										$tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );
										if ( ! empty( $data ) ) {
											if ( isset( $data[3] ) && isset( $data[4] ) ) {
												[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
												if( ! $tour_meta["tour_as_featured"] ) Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice );
											} else {
												[ $adults, $child, $check_in_out ] = $data;
												if( ! $tour_meta["tour_as_featured"] ) Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out );
											}
										} else {
											if( ! $tour_meta["tour_as_featured"] )	Tour::tf_tour_archive_single_item();
										}
									} else {
										$apartment_meta = get_post_meta( get_the_ID() , 'tf_apartment_opt', true );
										if ( ! empty( $data ) ) {
											if ( isset( $data[4] ) && isset( $data[5] ) ) {
												if( ! $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item( $data );
											} else {
												if( ! $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item( $data );
											}
										} else {
											if( ! $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item();
										}
									}

								}
							}
							$total_pages = ceil( $total_filtered_results / $post_per_page );
							if($total_pages > 1){
								echo "<div class='tf_posts_navigation tf_posts_page_navigation'>";
								echo wp_kses_post(
									paginate_links( array(
										'total'   => $total_pages,
										'current' => $current_page
									) )
								);
								echo "</div>";
							}
						}

					} else {
						echo '<div class="tf-nothing-found" data-post-count="0">' . esc_html__( 'Nothing Found!', 'tourfic' ) . '</div>';
					}
					echo "<span hidden=hidden class='tf-posts-count'>";
					echo ! empty( $tf_total_results ) ? esc_html( $tf_total_results ) : 0;
					echo "</span>";
					?>

				</div>
				<!-- Available rooms end -->

			</div>
		<?php }	elseif ( ( $post_type == "tf_carrental" && $tf_car_arc_selected_template == "design-1" ) ) { ?>

			<div class="tf-archive-header tf-flex tf-flex-space-bttn tf-flex-align-center tf-mb-30">
				<div class="tf-archive-view">
					<ul class="tf-flex tf-flex-gap-16">
						<li class="<?php echo $tf_defult_views=="grid" ? esc_attr('active') : ''; ?>" data-view="grid"><i class="ri-layout-grid-line"></i></li>
						<li class="<?php echo $tf_defult_views=="list" ? esc_attr('active') : ''; ?>" data-view="list"><i class="ri-list-check"></i></li>
					</ul>
				</div>
				<div class="tf-total-result-bar">
					<span>
						<?php echo esc_html__( 'Total Results ', 'tourfic' ); ?>
					</span>
					<span><?php echo ' ('; ?> </span>
					<div class="tf-total-results">
						<span><?php echo esc_html( $total_posts ); ?> </span>
					</div>
					<span><?php echo ')'; ?> </span>
					<div class="tf-archive-filter-showing">
						<i class="ri-equalizer-line"></i>
					</div>
				</div>
			</div>
			<div class="tf-car-details-column tf-flex tf-flex-gap-32">

				<div class="tf-car-archive-sidebar">
					<div class="tf-sidebar-header tf-flex tf-flex-space-bttn tf-flex-align-center">
						<div class="tf-close-sidebar">
                            <i class="fa-solid fa-xmark"></i>
                        </div>
						<h4><?php esc_html_e("Filter", "tourfic") ?></h4>
						<button class="filter-reset-btn"><?php esc_html_e("Reset", "tourfic"); ?></button>
					</div>

					<?php if ( is_active_sidebar( 'tf_search_result' ) ) {
						dynamic_sidebar( 'tf_search_result' );
					} ?>

				</div>

				<div class="tf-car-archive-result">

					<div class="tf-car-result archive_ajax_result tf-flex tf-flex-gap-32 <?php echo $tf_defult_views=="list" ? esc_attr('list-view') : esc_attr('grid-view'); ?>">

						<?php
						if ( $loop->have_posts() ) {
							$not_found = [];
							while ( $loop->have_posts() ) {
								$loop->the_post();

								if ( $post_type == 'tf_carrental' ) {
									$car_meta = get_post_meta( get_the_ID(), 'tf_carrental_opt', true );
									$car_inventory = Availability::tf_car_inventory(get_the_ID(), $car_meta, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time);
									if($car_inventory){
										tf_car_availability_response($car_meta, $not_found, $pickup, $dropoff, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time, $startprice, $endprice);
									}
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
								echo '<div class="tf-nothing-found" data-post-count="0">' . esc_html__( 'Nothing Found!', 'tourfic' ) . '</div>';
							}

							$post_per_page = Helper::tfopt( 'posts_per_page' ) ? Helper::tfopt( 'posts_per_page' ) : 10;

							$total_filtered_results = count( $tf_total_filters );
							$current_page           = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
							$offset                 = ( $current_page - 1 ) * $post_per_page;
							$displayed_results      = array_slice( $tf_total_filters, $offset, $post_per_page );
							if ( ! empty( $displayed_results ) ) {
								$filter_args = array(
									'post_type'      => $post_type,
									'posts_per_page' => $post_per_page,
									'post__in'       => $displayed_results,
								);

								$result_query  = new \WP_Query( $filter_args );
								$result_query2 = $result_query;
								if ( $result_query->have_posts() ) {
									while ( $result_query->have_posts() ) {
										$result_query->the_post();

										if ( $post_type == 'tf_carrental' ) {
											$car_meta = get_post_meta( get_the_ID(), 'tf_carrental_opt', true );
											if ( $car_meta["car_as_featured"] ) {
												tf_car_archive_single_item($pickup, $dropoff, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time);
											}
										}

									}

									while ( $result_query2->have_posts() ) {
										$result_query2->the_post();

										if ( $post_type == 'tf_carrental' ) {
											$car_meta = get_post_meta( get_the_ID(), 'tf_carrental_opt', true );
											if ( ! $car_meta["car_as_featured"] ) {
												tf_car_archive_single_item($pickup, $dropoff, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time);
											}
										}

									}
								}
								$total_pages = ceil( $total_filtered_results / $post_per_page );
								if ( $total_pages > 1 ) {
									echo "<div class='tf_posts_navigation tf_posts_ajax_navigation'>";
									echo wp_kses_post(
										paginate_links( array(
											'total'   => $total_pages,
											'current' => $current_page
										) )
									);
									echo "</div>";
								}
							}
						} else {
							echo '<div class="tf-nothing-found" data-post-count="0">' . esc_html__( 'Nothing Found!', 'tourfic' ) . '</div>';
						}

						echo "<span hidden=hidden class='tf-posts-count'>";
						echo ! empty( $tf_total_results ) ? esc_html( $tf_total_results ) : 0;
						echo "</span>";
						wp_reset_postdata();
					?>

					</div>

				</div>

			</div>
			<?php
		} elseif ( ( $post_type == "tf_tours" && $tf_tour_arc_selected_template == "design-3" && function_exists( 'is_tf_pro' ) && is_tf_pro()) ||
		           ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template == "design-3" && function_exists( 'is_tf_pro' ) && is_tf_pro()) ||
		           ( $post_type == "tf_apartment" && $tf_apartment_arc_selected_template == "design-2" && function_exists( 'is_tf_pro' ) && is_tf_pro()) ) {

			if($post_type == "tf_hotel") {
				$found_post_label = esc_html__( "Hotels", "tourfic" );
				$tf_defult_views = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['hotel_archive_view'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['hotel_archive_view'] : 'list';
			}elseif($post_type == "tf_tours"){
				$found_post_label = esc_html__( "Tours", "tourfic" );
				$tf_defult_views = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['tour_archive_view'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['tour_archive_view'] : 'list';
			}elseif($post_type == "tf_apartment"){
				$found_post_label = esc_html__( "Apartments", "tourfic" );
				$tf_defult_views = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['apartment_archive_view'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['apartment_archive_view'] : 'list';
			}
            ?>
            <div class="tf-archive-top">
                <h5 class="tf-total-results"><?php esc_html_e( "Found", "tourfic" ); ?>
                    <span class="tf-map-item-count"><?php echo esc_html($total_posts); ?></span>
                    <?php echo esc_html($found_post_label)?>
                </h5>
                <a href="" class="tf-mobile-map-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <path d="M17.3327 7.33366V6.68156C17.3327 5.06522 17.3327 4.25705 16.8445 3.75491C16.3564 3.25278 15.5707 3.25278 13.9993 3.25278H12.2671C11.5027 3.25278 11.4964 3.25129 10.8089 2.90728L8.03258 1.51794C6.87338 0.93786 6.29378 0.647818 5.67633 0.667975C5.05888 0.688132 4.49833 1.01539 3.37722 1.66992L2.354 2.2673C1.5305 2.74807 1.11876 2.98846 0.892386 3.38836C0.666016 3.78827 0.666016 4.27527 0.666016 5.24927V12.0968C0.666016 13.3765 0.666016 14.0164 0.951234 14.3725C1.14102 14.6095 1.40698 14.7688 1.70102 14.8216C2.1429 14.901 2.68392 14.5851 3.76591 13.9534C4.50065 13.5245 5.20777 13.079 6.08674 13.1998C6.82326 13.301 7.50768 13.7657 8.16602 14.0952" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5.66602 0.666992L5.66601 13.167" stroke="white" stroke-linejoin="round"/>
                        <path d="M11.5 3.16699V6.91699" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14.2556 17.0696C14.075 17.2388 13.8334 17.3333 13.5821 17.3333C13.3308 17.3333 13.0893 17.2388 12.9086 17.0696C11.254 15.5108 9.0366 13.7695 10.1179 11.2415C10.7026 9.87465 12.1061 9 13.5821 9C15.0581 9 16.4616 9.87465 17.0463 11.2415C18.1263 13.7664 15.9143 15.5162 14.2556 17.0696Z" stroke="white"/>
                        <path d="M13.582 12.75H13.5895" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span><?php echo esc_html__('Map', 'tourfic') ?></span>
                </a>
                <ul class="tf-archive-view">
                    <li class="tf-archive-filter-btn">
                        <i class="ri-equalizer-line"></i>
                        <span><?php esc_html_e( "All Filter", "tourfic" ); ?></span>
                    </li>
                    <li class="tf-archive-view-item tf-archive-list-view <?php echo $tf_defult_views == "list" ? esc_attr( 'active' ) : ''; ?>" data-id="list-view">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M1.33398 7.59996C1.33398 6.82778 1.49514 6.66663 2.26732 6.66663H13.734C14.5062 6.66663 14.6673 6.82778 14.6673 7.59996V8.39996C14.6673 9.17214 14.5062 9.33329 13.734 9.33329H2.26732C1.49514 9.33329 1.33398 9.17214 1.33398 8.39996V7.59996Z"
                                  stroke="#6E655E" stroke-linecap="round"/>
                            <path d="M1.33398 2.26665C1.33398 1.49447 1.49514 1.33331 2.26732 1.33331H13.734C14.5062 1.33331 14.6673 1.49447 14.6673 2.26665V3.06665C14.6673 3.83882 14.5062 3.99998 13.734 3.99998H2.26732C1.49514 3.99998 1.33398 3.83882 1.33398 3.06665V2.26665Z"
                                  stroke="#6E655E" stroke-linecap="round"/>
                            <path d="M1.33398 12.9333C1.33398 12.1612 1.49514 12 2.26732 12H13.734C14.5062 12 14.6673 12.1612 14.6673 12.9333V13.7333C14.6673 14.5055 14.5062 14.6667 13.734 14.6667H2.26732C1.49514 14.6667 1.33398 14.5055 1.33398 13.7333V12.9333Z"
                                  stroke="#6E655E" stroke-linecap="round"/>
                        </svg>
                    </li>
                    <li class="tf-archive-view-item tf-archive-grid-view <?php echo $tf_defult_views == "grid" ? esc_attr( 'active' ) : ''; ?>" data-id="grid-view">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M1.33398 12C1.33398 10.973 1.33398 10.4595 1.56514 10.0823C1.69448 9.87127 1.87194 9.69381 2.08301 9.56447C2.46021 9.33331 2.97369 9.33331 4.00065 9.33331C5.02761 9.33331 5.54109 9.33331 5.9183 9.56447C6.12936 9.69381 6.30682 9.87127 6.43616 10.0823C6.66732 10.4595 6.66732 10.973 6.66732 12C6.66732 13.0269 6.66732 13.5404 6.43616 13.9176C6.30682 14.1287 6.12936 14.3062 5.9183 14.4355C5.54109 14.6666 5.02761 14.6666 4.00065 14.6666C2.97369 14.6666 2.46021 14.6666 2.08301 14.4355C1.87194 14.3062 1.69448 14.1287 1.56514 13.9176C1.33398 13.5404 1.33398 13.0269 1.33398 12Z"
                                  stroke="#6E655E" stroke-width="1.2"/>
                            <path d="M9.33398 12C9.33398 10.973 9.33398 10.4595 9.56514 10.0823C9.69448 9.87127 9.87194 9.69381 10.083 9.56447C10.4602 9.33331 10.9737 9.33331 12.0007 9.33331C13.0276 9.33331 13.5411 9.33331 13.9183 9.56447C14.1294 9.69381 14.3068 9.87127 14.4362 10.0823C14.6673 10.4595 14.6673 10.973 14.6673 12C14.6673 13.0269 14.6673 13.5404 14.4362 13.9176C14.3068 14.1287 14.1294 14.3062 13.9183 14.4355C13.5411 14.6666 13.0276 14.6666 12.0007 14.6666C10.9737 14.6666 10.4602 14.6666 10.083 14.4355C9.87194 14.3062 9.69448 14.1287 9.56514 13.9176C9.33398 13.5404 9.33398 13.0269 9.33398 12Z"
                                  stroke="#6E655E" stroke-width="1.2"/>
                            <path d="M1.33398 3.99998C1.33398 2.97302 1.33398 2.45954 1.56514 2.08233C1.69448 1.87127 1.87194 1.69381 2.08301 1.56447C2.46021 1.33331 2.97369 1.33331 4.00065 1.33331C5.02761 1.33331 5.54109 1.33331 5.9183 1.56447C6.12936 1.69381 6.30682 1.87127 6.43616 2.08233C6.66732 2.45954 6.66732 2.97302 6.66732 3.99998C6.66732 5.02694 6.66732 5.54042 6.43616 5.91762C6.30682 6.12869 6.12936 6.30615 5.9183 6.43549C5.54109 6.66665 5.02761 6.66665 4.00065 6.66665C2.97369 6.66665 2.46021 6.66665 2.08301 6.43549C1.87194 6.30615 1.69448 6.12869 1.56514 5.91762C1.33398 5.54042 1.33398 5.02694 1.33398 3.99998Z"
                                  stroke="#6E655E" stroke-width="1.2"/>
                            <path d="M9.33398 3.99998C9.33398 2.97302 9.33398 2.45954 9.56514 2.08233C9.69448 1.87127 9.87194 1.69381 10.083 1.56447C10.4602 1.33331 10.9737 1.33331 12.0007 1.33331C13.0276 1.33331 13.5411 1.33331 13.9183 1.56447C14.1294 1.69381 14.3068 1.87127 14.4362 2.08233C14.6673 2.45954 14.6673 2.97302 14.6673 3.99998C14.6673 5.02694 14.6673 5.54042 14.4362 5.91762C14.3068 6.12869 14.1294 6.30615 13.9183 6.43549C13.5411 6.66665 13.0276 6.66665 12.0007 6.66665C10.9737 6.66665 10.4602 6.66665 10.083 6.43549C9.87194 6.30615 9.69448 6.12869 9.56514 5.91762C9.33398 5.54042 9.33398 5.02694 9.33398 3.99998Z"
                                  stroke="#6E655E" stroke-width="1.2"/>
                        </svg>
                    </li>
                </ul>
            </div>

            <div class="tf-archive-hotels archive_ajax_result <?php echo $tf_defult_views == "list" ? esc_attr( 'tf-layout-list' ) : esc_attr( 'tf-layout-grid' ); ?>">

				<?php
				if ( $loop->have_posts() ) {
					$not_found = [];
					while ( $loop->have_posts() ) {
						$loop->the_post();

						if ( $post_type == 'tf_hotel' ) {

							if ( empty( $check_in_out ) ) {
								Hotel::tf_filter_hotel_without_date( $period, $not_found, $data );
							} else {
								Hotel::tf_filter_hotel_by_date( $period, $not_found, $data );
							}

						} elseif( $post_type == 'tf_tours' ) {

							if ( empty( $check_in_out ) ) {
								Tour::tf_filter_tour_by_without_date( $period, $total_posts, $not_found, $data );
							} else {
								Tour::tf_filter_tour_by_date( $period, $total_posts, $not_found, $data );
							}
						}else {
							if ( empty( $check_in_out ) ) {
								Apartment::tf_filter_apartment_without_date( $period, $not_found, $data );
							} else {
								Apartment::tf_filter_apartment_by_date( $period, $not_found, $data );
							}
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
                        echo '<div id="map-datas" style="display: none">'. wp_json_encode([]) .'</div>';
						echo '<div class="tf-nothing-found" data-post-count="0">' . esc_html__( 'Nothing Found!', 'tourfic' ) . '</div>';
					}
					$post_per_page = Helper::tfopt( 'posts_per_page' ) ? Helper::tfopt( 'posts_per_page' ) : 10;
					// Main Query args
					$filter_args = array(
						'post_type'      => $post_type,
						'post_status'    => 'publish',
						'posts_per_page' => $post_per_page,
						'paged'          => $paged,
					);


					$total_filtered_results = count( $tf_total_filters );
					$current_page           = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
					$offset                 = ( $current_page - 1 ) * $post_per_page;
					$displayed_results      = array_slice( $tf_total_filters, $offset, $post_per_page );
					if ( ! empty( $displayed_results ) ) {
						$filter_args = array(
							'post_type'      => $post_type,
							'post_status'    => 'publish',
							'posts_per_page' => $post_per_page,
							'post__in'       => $displayed_results,
						);

						$result_query = new \WP_Query( $filter_args );
						if ( $result_query->have_posts() ) {
							$count     = 0;
							$locations = [];

							// Feature Posts
							while ( $result_query->have_posts() ) {
								$result_query->the_post();

								if ( $post_type == 'tf_hotel' ) {
									$hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );
									if ( ! $hotel_meta["featured"] ) {
										continue;
									}

									$count ++;
									$map  = ! empty( $hotel_meta['map'] ) ? Helper::tf_data_types( $hotel_meta['map'] ) : '';

									$min_price_arr = hotelPricing::instance(get_the_ID())->get_min_price();
									$min_sale_price = !empty($min_price_arr['min_sale_price']) ? $min_price_arr['min_sale_price'] : 0;
									$min_regular_price = !empty($min_price_arr['min_regular_price']) ? $min_price_arr['min_regular_price'] : 0;
									$min_discount_type = !empty($min_price_arr['min_discount_type']) ? $min_price_arr['min_discount_type'] : 'none';
									$min_discount_amount = !empty($min_price_arr['min_discount_amount']) ? $min_price_arr['min_discount_amount'] : 0;

									if ( $min_regular_price != 0 ) {
										$price_html = wc_format_sale_price( $min_regular_price, $min_sale_price );
									} else {
										$price_html = wp_kses_post( wc_price( $min_sale_price ) ) . " ";
									}

									if ( ! empty( $map ) ) {
										$lat = $map['latitude'];
										$lng = $map['longitude'];
										ob_start();
										?>
                                        <div class="tf-map-item">
                                            <div class="tf-map-item-thumb">
                                                <a href="<?php echo esc_url(get_the_permalink()); ?>">
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
														<?php echo $min_discount_type == "percent" ? wp_kses_post($min_discount_amount) . '%' : wp_kses_post(wc_price( $min_discount_amount )) ?>
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
													<?php echo wp_kses_post(hotelPricing::instance(get_the_ID())->get_min_price_html()); ?>
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

									if ( ! empty( $data ) ) {
										if ( isset( $data[4] ) && isset( $data[5] ) ) {
											[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
											Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice );
										} else {
											[ $adults, $child, $room, $check_in_out ] = $data;
											Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
										}
									} else {
										Hotel::tf_hotel_archive_single_item();
									}

								} elseif ( $post_type == 'tf_tours' ) {
									$tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );

									if ( ! $tour_meta["tour_as_featured"] ) {
										continue;
									}
									$count ++;
									$map            = ! empty( $tour_meta['location'] ) ? Helper::tf_data_types( $tour_meta['location'] ) : '';
									$discount_type  = ! empty( $tour_meta['discount_type'] ) ? $tour_meta['discount_type'] : '';
									$discount_price = ! empty( $tour_meta['discount_price'] ) ? $tour_meta['discount_price'] : '';

									$min_price_arr     = tourPricing::instance( get_the_ID() )->get_min_price();
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
										ob_start();
										?>
                                        <div class="tf-map-item">
                                            <div class="tf-map-item-thumb">
                                                <a href="<?php echo esc_url(get_the_permalink()); ?>">
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
													<?php echo wp_kses_post(tourPricing::instance( get_the_ID() )->get_min_price_html()); ?>
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

									if ( ! empty( $data ) ) {
										if ( isset( $data[3] ) && isset( $data[4] ) ) {
											[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
											Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice );
										} else {
											[ $adults, $child, $check_in_out ] = $data;
											Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out );
										}
									} else {
										Tour::tf_tour_archive_single_item();
									}
								} else {
									$apartment_meta = get_post_meta( get_the_ID() , 'tf_apartment_opt', true );
									if ( ! $apartment_meta["apartment_as_featured"] ) {
										continue;
									}

									$count ++;
									$map  = ! empty( $apartment_meta['map'] ) ? Helper::tf_data_types( $apartment_meta['map'] ) : '';
									$discount_type  = ! empty( $apartment_meta['discount_type'] ) ? $apartment_meta['discount_type'] : '';
									$discount_price = ! empty( $apartment_meta['discount'] ) ? $apartment_meta['discount'] : '';

									$min_price_arr = apartmentPricing::instance(get_the_ID())->get_min_price();
									$min_sale_price = !empty($min_price_arr['min_sale_price']) ? $min_price_arr['min_sale_price'] : 0;
									$min_regular_price = !empty($min_price_arr['min_regular_price']) ? $min_price_arr['min_regular_price'] : 0;

									// if ( $min_regular_price != 0 ) {
									// 	$price_html = wc_format_sale_price( $min_regular_price, $min_sale_price );
									// } else {
									// 	$price_html = wp_kses_post( wc_price( $min_sale_price ) ) . " ";
									// }

									$price_html = apartmentPricing::instance(get_the_ID())->get_min_price_html();

									if ( ! empty( $map ) ) {
										$lat = $map['latitude'];
										$lng = $map['longitude'];
										ob_start();
										?>
                                        <div class="tf-map-item">
                                            <div class="tf-map-item-thumb">
                                                <a href="<?php echo esc_url(get_the_permalink()); ?>">
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
													<?php echo wp_kses_post(apartmentPricing::instance(get_the_ID())->get_min_price_html()); ?>
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

									if ( ! empty( $data ) ) {
										if ( isset( $data[4] ) && isset( $data[5] ) ) {
											if( $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item( $data );
										} else {
											if( $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item( $data );
										}
									} else {
										if( $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item();
									}
								}

							}
							// Other Posts
							while ( $result_query->have_posts() ) {
								$result_query->the_post();

								if ( $post_type == 'tf_hotel' ) {
									$hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );
									if ( $hotel_meta["featured"] ) {
										continue;
									}
									$count ++;
									$map  = ! empty( $hotel_meta['map'] ) ? Helper::tf_data_types( $hotel_meta['map'] ) : '';

									$min_price_arr = hotelPricing::instance(get_the_ID())->get_min_price();
									$min_sale_price = !empty($min_price_arr['min_sale_price']) ? $min_price_arr['min_sale_price'] : 0;
									$min_regular_price = !empty($min_price_arr['min_regular_price']) ? $min_price_arr['min_regular_price'] : 0;
									$min_discount_type = !empty($min_price_arr['min_discount_type']) ? $min_price_arr['min_discount_type'] : 'none';
									$min_discount_amount = !empty($min_price_arr['min_discount_amount']) ? $min_price_arr['min_discount_amount'] : 0;

									if ( $min_regular_price != 0 ) {
										$price_html = wc_format_sale_price( $min_regular_price, $min_sale_price );
									} else {
										$price_html = wp_kses_post( wc_price( $min_sale_price ) ) . " ";
									}

									if ( ! empty( $map ) ) {
										$lat = $map['latitude'];
										$lng = $map['longitude'];
										ob_start();
										?>
                                        <div class="tf-map-item">
                                            <div class="tf-map-item-thumb">
                                                <a href="<?php echo esc_url(get_the_permalink()); ?>">
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
													<?php echo wp_kses_post(hotelPricing::instance(get_the_ID())->get_min_price_html()); ?>
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

									if ( ! empty( $data ) ) {
										if ( isset( $data[4] ) && isset( $data[5] ) ) {
											[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
											Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice );
										} else {
											[ $adults, $child, $room, $check_in_out ] = $data;
											Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
										}
									} else {
										Hotel::tf_hotel_archive_single_item();
									}

								} elseif ( $post_type == 'tf_tours' ) {
									$tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );

									if ( $tour_meta["tour_as_featured"] ) {
										continue;
									}
									$count ++;
									$map            = ! empty( $tour_meta['location'] ) ? Helper::tf_data_types( $tour_meta['location'] ) : '';
									$discount_type  = ! empty( $tour_meta['discount_type'] ) ? $tour_meta['discount_type'] : '';
									$discount_price = ! empty( $tour_meta['discount_price'] ) ? $tour_meta['discount_price'] : '';

									$min_price_arr     = tourPricing::instance( get_the_ID() )->get_min_price();
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
										ob_start();
										?>
                                        <div class="tf-map-item">
                                            <div class="tf-map-item-thumb">
                                                <a href="<?php echo esc_url(get_the_permalink()); ?>">
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
													<?php echo wp_kses_post(tourPricing::instance( get_the_ID() )->get_min_price_html()); ?>
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

									if ( ! empty( $data ) ) {
										if ( isset( $data[3] ) && isset( $data[4] ) ) {
											[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
											Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice );
										} else {
											[ $adults, $child, $check_in_out ] = $data;
											Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out );
										}
									} else {
										Tour::tf_tour_archive_single_item();
									}
								} else {
									$apartment_meta = get_post_meta( get_the_ID() , 'tf_apartment_opt', true );

									if ( $apartment_meta["apartment_as_featured"] ) {
										continue;
									}

									$count ++;
									$map  = ! empty( $apartment_meta['map'] ) ? Helper::tf_data_types( $apartment_meta['map'] ) : '';
									$discount_type  = ! empty( $apartment_meta['discount_type'] ) ? $apartment_meta['discount_type'] : '';
									$discount_price = ! empty( $apartment_meta['discount'] ) ? $apartment_meta['discount'] : '';

									$min_price_arr = apartmentPricing::instance(get_the_ID())->get_min_price();
									$min_sale_price = !empty($min_price_arr['min_sale_price']) ? $min_price_arr['min_sale_price'] : 0;
									$min_regular_price = !empty($min_price_arr['min_regular_price']) ? $min_price_arr['min_regular_price'] : 0;

									// if ( $min_regular_price != 0 ) {
									// 	$price_html = wc_format_sale_price( $min_regular_price, $min_sale_price );
									// } else {
									// 	$price_html = wp_kses_post( wc_price( $min_sale_price ) ) . " ";
									// }

									$price_html = apartmentPricing::instance(get_the_ID())->get_min_price_html();

									if ( ! empty( $map ) ) {
										$lat = $map['latitude'];
										$lng = $map['longitude'];
										ob_start();
										?>
                                        <div class="tf-map-item">
                                            <div class="tf-map-item-thumb">
                                                <a href="<?php echo esc_url(get_the_permalink()); ?>">
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
													<?php echo wp_kses_post(apartmentPricing::instance(get_the_ID())->get_min_price_html()); ?>
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

									if ( ! empty( $data ) ) {
										if ( isset( $data[4] ) && isset( $data[5] ) ) {
											if( ! $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item( $data );
										} else {
											if( ! $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item( $data );
										}
									} else {
										if( ! $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item();
									}
								}

							}
                            ?>
                            <div id="map-datas" style="display: none"><?php echo array_filter( $locations ) ? wp_json_encode( array_values( $locations ) ) : []; ?></div>
                            <?php
						}
						$total_pages = ceil( $total_filtered_results / $post_per_page );
						if($total_pages > 1){
							echo "<div class='tf_posts_navigation tf_posts_page_navigation'>";
							echo wp_kses_post(
								paginate_links( array(
									'total'   => $total_pages,
									'current' => $current_page
								) )
							);
							echo "</div>";
						}
					}

				} else {
                    echo '<div id="map-datas" style="display: none">'. wp_json_encode([]) .'</div>';
					echo '<div class="tf-nothing-found" data-post-count="0">' . esc_html__( 'Nothing Found!', 'tourfic' ) . '</div>';
				}
				echo "<span hidden=hidden class='tf-posts-count'>";
				echo ! empty( $tf_total_results ) ? esc_html( $tf_total_results ) : 0;
				echo "</span>";
				?>

            </div>

		<?php } else { ?>
			<div class="tf_search_result">
				<div class="tf-action-top">
					<div class="tf-total-results">
						<?php echo esc_html__( 'Total Results ', 'tourfic' ) . '(<span>' . esc_html( $total_posts ) . '</span>)'; ?>
					</div>
					<div class="tf-list-grid">
						<a href="#list-view" data-id="list-view" class="change-view <?php echo $tf_defult_views=="list" ? esc_attr('active') : ''; ?>" title="<?php esc_html_e( 'List View', 'tourfic' ); ?>"><i class="fas fa-list"></i></a>
						<a href="#grid-view" data-id="grid-view" class="change-view <?php echo $tf_defult_views=="grid" ? esc_attr('active') : ''; ?>" title="<?php esc_html_e( 'Grid View', 'tourfic' ); ?>"><i class="fas fa-border-all"></i></a>
						<div class="tf-sorting-selection-warper">
                            <form class="tf-archive-ordering" method="get">
                                <select class="tf-orderby" name="tf-orderby" id="tf-orderby">
                                    <option value="default"><?php echo esc_html__( 'Default Sorting', 'tourfic' ); ?></option>
                                    <option value="enquiry"><?php echo esc_html__( 'Sort By Recommended', 'tourfic' ); ?></option>
                                    <option value="order"><?php echo esc_html__( 'Sort By Popularity', 'tourfic' ); ?></option>
                                    <option value="rating"><?php echo esc_html__( 'Sort By Average Rating', 'tourfic' ); ?></option>
                                    <option value="latest"><?php echo esc_html__( 'Sort By Latest', 'tourfic' ); ?></option>
                                    <option value="price-high"><?php echo esc_html__( 'Sort By Price: High to Low', 'tourfic' ); ?></option>
                                    <option value="price-low"><?php echo esc_html__( 'Sort By Price: Low to High', 'tourfic' ); ?></option>
                                </select>
								<i class="fas fa-chevron-down"></i>
                            </form>
                        </div>
					</div>
				</div>
				<div class="archive_ajax_result <?php echo $tf_defult_views=="grid" ? esc_attr('tours-grid') : '' ?>">
					<?php
					if ( $loop->have_posts() ) {
						$not_found = [];
						while ( $loop->have_posts() ) {
							$loop->the_post();

							if ( $post_type == 'tf_hotel' ) {

								if ( empty( $check_in_out ) ) {
									Hotel::tf_filter_hotel_without_date( $period, $not_found, $data );
								} else {
									Hotel::tf_filter_hotel_by_date( $period, $not_found, $data );
								}

							} elseif ( $post_type == 'tf_tours' ) {
								if ( empty( $check_in_out ) ) {
									Tour::tf_filter_tour_by_without_date( $period, $total_posts, $not_found, $data );
								} else {
									Tour::tf_filter_tour_by_date( $period, $total_posts, $not_found, $data );
								}
							} else {
								if ( empty( $check_in_out ) ) {
									Apartment::tf_filter_apartment_without_date( $period, $not_found, $data );
								} else {
									Apartment::tf_filter_apartment_by_date( $period, $not_found, $data );
								}
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
							echo '<div class="tf-nothing-found" data-post-count="0">' . esc_html__( 'Nothing Found!', 'tourfic' ) . '</div>';
						}
						$post_per_page = Helper::tfopt( 'posts_per_page' ) ? Helper::tfopt( 'posts_per_page' ) : 10;
						// Main Query args
						$filter_args = array(
							'post_type'      => $post_type,
							'posts_per_page' => $post_per_page,
							'paged'          => $paged,
						);


						$total_filtered_results = count( $tf_total_filters );
						$current_page           = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
						$offset                 = ( $current_page - 1 ) * $post_per_page;
						$displayed_results      = array_slice( $tf_total_filters, $offset, $post_per_page );
						if ( ! empty( $displayed_results ) ) {
							$filter_args = array(
								'post_type'      => $post_type,
								'posts_per_page' => $post_per_page,
								'post__in'       => $displayed_results,
							);


							$result_query = new \WP_Query( $filter_args );
							if ( $result_query->have_posts() ) {
								// Feature Posts
								while ( $result_query->have_posts() ) {
									$result_query->the_post();

									if ( $post_type == 'tf_hotel' ) {
										$hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );

										if ( ! empty( $data ) ) {
											if ( isset( $data[4] ) && isset( $data[5] ) ) {
												[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
												if( $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice );
											} else {
												[ $adults, $child, $room, $check_in_out ] = $data;
												if( $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
											}
										} else {
											if( $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item();
										}

									} elseif ( $post_type == 'tf_tours' ) {
										$tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );
										if ( ! empty( $data ) ) {
											if ( isset( $data[3] ) && isset( $data[4] ) ) {
												[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
												if( $tour_meta["tour_as_featured"] ) Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice );
											} else {
												[ $adults, $child, $check_in_out ] = $data;
												if( $tour_meta["tour_as_featured"] ) Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out );
											}
										} else {
											if( $tour_meta["tour_as_featured"] ) Tour::tf_tour_archive_single_item();
										}
									} else {
										$apartment_meta = get_post_meta( get_the_ID() , 'tf_apartment_opt', true );
										if ( ! empty( $data ) ) {
											if ( isset( $data[4] ) && isset( $data[5] ) ) {
												if( $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item( $data );
											} else {
												if( $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item( $data );
											}
										} else {
											if( $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item();
										}
									}

								}
								// Other Posts
								while ( $result_query->have_posts() ) {
									$result_query->the_post();

									if ( $post_type == 'tf_hotel' ) {
										$hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );
										if ( ! empty( $data ) ) {
											if ( isset( $data[4] ) && isset( $data[5] ) ) {
												[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
												if( ! $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice );
											} else {
												[ $adults, $child, $room, $check_in_out ] = $data;
												if( ! $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
											}
										} else {
											if( ! $hotel_meta["featured"] ) Hotel::tf_hotel_archive_single_item();
										}

									} elseif ( $post_type == 'tf_tours' ) {
										$tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );
										if ( ! empty( $data ) ) {
											if ( isset( $data[3] ) && isset( $data[4] ) ) {
												[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
												if( ! $tour_meta["tour_as_featured"] ) Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice );
											} else {
												[ $adults, $child, $check_in_out ] = $data;
												if( ! $tour_meta["tour_as_featured"] ) Tour::tf_tour_archive_single_item( $adults, $child, $check_in_out );
											}
										} else {
											if( ! $tour_meta["tour_as_featured"] )	Tour::tf_tour_archive_single_item();
										}
									} else {
										$apartment_meta = get_post_meta( get_the_ID() , 'tf_apartment_opt', true );
										if ( ! empty( $data ) ) {
											if ( isset( $data[4] ) && isset( $data[5] ) ) {
												if( ! $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item( $data );
											} else {
												if( ! $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item( $data );
											}
										} else {
											if( ! $apartment_meta["apartment_as_featured"] ) Apartment::tf_apartment_archive_single_item();
										}
									}

								}
							}
							$total_pages = ceil( $total_filtered_results / $post_per_page );
							echo "<div class='tf_posts_navigation tf_posts_page_navigation'>";
							echo wp_kses_post(
								paginate_links( array(
									'total'   => $total_pages,
									'current' => $current_page,
								) )
							);
							echo "</div>";
						}

					} else {
						echo '<div class="tf-nothing-found" data-post-count="0">' . esc_html__( 'Nothing Found!', 'tourfic' ) . '</div>';
					}
					echo "<span hidden=hidden class='tf-posts-count'>";
					echo ! empty( $tf_total_results ) ? esc_html( $tf_total_results ) : 0;
					echo "</span>";
					?>
				</div>

			</div>
		<?php } ?>
		<!-- End Content -->

		<?php
		wp_reset_postdata(); ?>
		<?php return ob_get_clean();
	}
}
<?php
/**
 * Hotel Locations Shortcode
 */
function hotel_locations_shortcode( $atts, $content = null ) {

	// Shortcode extract
	extract(
		shortcode_atts(
			array(
				'orderby'    => 'name',
				'order'      => 'ASC',
				'hide_empty' => 0,
				'ids'        => '',
				'limit'      => - 1,
			),
			$atts
		)
	);

	// 1st search on hotel_location taxonomy
	$locations = get_terms( array(
		'taxonomy'     => 'hotel_location',
		'orderby'      => $orderby,
		'order'        => $order,
		'hide_empty'   => $hide_empty,
		'hierarchical' => 0,
		'search'       => '',
		'number'       => $limit == - 1 ? false : $limit,
		'include'      => $ids,
	) );

	ob_start();

	if ( $locations ) { ?>
        <section id="recomended_section_wrapper">
            <div class="recomended_inner">

				<?php foreach ( $locations as $term ) {

					$meta      = get_term_meta( $term->term_id, 'tf_hotel_location', true );
					$image_url = ! empty( $meta['image'] ) ? $meta['image'] : TF_ASSETS_APP_URL . 'images/feature-default.jpg';
					$term_link = get_term_link( $term ); ?>

                    <div class="single_recomended_item">
                        <a href="<?php echo $term_link; ?>">
                            <div class="single_recomended_content" style="background-image: url(<?php echo $image_url; ?>);">
                                <div class="recomended_place_info_header">
                                    <h3><?php echo esc_html( $term->name ); ?></h3>
                                    <p><?php printf( _n( '%s hotel', '%s hotels', $term->count, 'tourfic' ), $term->count ); ?></p>
                                </div>
                            </div>
                        </a>
                    </div>

				<?php } ?>

            </div>
        </section>

	<?php }

	return ob_get_clean();
}

add_shortcode( 'hotel_locations', 'hotel_locations_shortcode' );
// Old compatibility
add_shortcode( 'tourfic_destinations', 'hotel_locations_shortcode' );


/**
 * Tour destinations shortcode
 */
function shortcode_tour_destinations( $atts, $content = null ) {

	// Shortcode extract
	extract(
		shortcode_atts(
			array(
				'orderby'    => 'name',
				'order'      => 'ASC',
				'hide_empty' => 0,
				'ids'        => '',
				'limit'      => - 1,
			),
			$atts
		)
	);

	// 1st search on Destination taxonomy
	$destinations = get_terms( array(
		'taxonomy'     => 'tour_destination',
		'orderby'      => $orderby,
		'order'        => $order,
		'hide_empty'   => $hide_empty,
		'hierarchical' => 0,
		'search'       => '',
		'number'       => $limit == - 1 ? false : $limit,
		'include'      => $ids,
	) );

	shuffle( $destinations );
	ob_start();

	if ( $destinations ) { ?>
        <section id="recomended_section_wrapper">
            <div class="recomended_inner">

				<?php foreach ( $destinations as $term ) {

					$meta      = get_term_meta( $term->term_id, 'tf_tour_destination', true );
					$image_url = ! empty( $meta['image'] ) ? $meta['image'] : TF_ASSETS_APP_URL . 'images/feature-default.jpg';
					$term_link = get_term_link( $term );

					if ( is_wp_error( $term_link ) ) {
						continue;
					} ?>

                    <div class="single_recomended_item">
                        <a href="<?php echo $term_link; ?>">
                            <div class="single_recomended_content" style="background-image: url(<?php echo $image_url; ?>);">
                                <div class="recomended_place_info_header">
                                    <h3><?php echo esc_html( $term->name ); ?></h3>
                                    <p><?php printf( _n( '%s tour', '%s tours', $term->count, 'tourfic' ), $term->count ); ?></p>
                                </div>
                            </div>
                        </a>
                    </div>

				<?php } ?>

            </div>
        </section>
	<?php }

	return ob_get_clean();
}

add_shortcode( 'tour_destinations', 'shortcode_tour_destinations' );

/**
 * Recent Hotel Slider
 */
function tf_recent_hotel_shortcode( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'title'        => '',  //title populer section
				'subtitle'     => '',   // Sub title populer section
				'orderby'      => 'date',
				'order'        => 'DESC',
				'count'        => 10,
				'slidestoshow' => 5,
			),
			$atts
		)
	);

	$args = array(
		'post_type'      => 'tf_hotel',
		'post_status'    => 'publish',
		'orderby'        => $orderby,
		'order'          => $order,
		'posts_per_page' => $count,
	);

	ob_start();

	$hotel_loop = new WP_Query( $args );

	// Generate an Unique ID
	$thisid = uniqid( 'tfpopular_' );

	?>
	<?php if ( $hotel_loop->have_posts() ) : ?>
        <div class="tf-widget-slider recent-hotel-slider">
            <div class="tf-heading">
				<?php
				if ( ! empty( $title ) ) {
					echo '<h2>' . esc_html( $title ) . '</h2>';
				}
				if ( ! empty( $subtitle ) ) {
					echo '<p>' . esc_html( $subtitle ) . '</p>';
				}
				?>
            </div>

            <div class="tf-slider-items-wrapper">
				<?php while ( $hotel_loop->have_posts() ) {
					$hotel_loop->the_post();
					$post_id                = get_the_ID();
					$related_comments_hotel = get_comments( array( 'post_id' => $post_id ) );
					$meta                   = get_post_meta( $post_id, 'tf_hotels_opt', true );
					$rooms                  = ! empty( $meta['room'] ) ? $meta['room'] : '';
					if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
						$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
							return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
						}, $rooms );
						$rooms                = unserialize( $tf_hotel_rooms_value );
					}
					//get and store all the prices for each room
					$room_price = [];
					if ( ! empty( $rooms ) ) {
						foreach ( $rooms as $room ) {

							$pricing_by = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : 1;
							if ( $pricing_by == 1 ) {
								if ( ! empty( $room['price'] ) ) {
									$room_price[] = $room['price'];
								}
								if ( ! empty( $room['avil_by_date'] ) && $room['avil_by_date'] == "1" ) {
									if ( ! empty( $room['avail_date'] ) ) {
                                        $avail_dates = json_decode($room['avail_date'], true);
										foreach ( $avail_dates as $repval ) {
											if ( ! empty( $repval['price'] ) ) {
												$room_price[] = $repval['price'];
											}
										}
									}
								}
							} else if ( $pricing_by == 2 ) {
								if ( ! empty( $room['adult_price'] ) ) {
									$room_price[] = $room['adult_price'];
								}
								if ( ! empty( $room['child_price'] ) ) {
									$room_price[] = $room['child_price'];
								}
								if ( ! empty( $room['avil_by_date'] ) && $room['avil_by_date'] == "1" ) {
									if ( ! empty( $room['avail_date'] ) ) {
										$avail_dates = json_decode($room['avail_date'], true);
										foreach ( $avail_dates as $repval ) {
											if ( ! empty( $repval['adult_price'] ) ) {
												$room_price[] = $repval['adult_price'];
											}
											if ( ! empty( $repval['child_price'] ) ) {
												$room_price[] = $repval['child_price'];
											}
										}
									}
								}
							}
						}
					}

					?>
                    <div class="tf-slider-item"
                         style="background-image: url(<?php echo ! empty( get_the_post_thumbnail_url( $post_id, 'full' ) ) ? get_the_post_thumbnail_url( $post_id, 'full' ) : TF_ASSETS_APP_URL . '/images/feature-default.jpg'; ?>);">
                        <div class="tf-slider-content">
                            <div class="tf-slider-desc">
                                <h3>
                                    <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
                                </h3>
								<?php if ( $related_comments_hotel ) { ?>
                                    <div class="tf-slider-rating-star">
                                        <i class="fas fa-star"></i> <span style="color:#fff;"><?php echo tf_total_avg_rating( $related_comments_hotel ); ?></span>
                                    </div>
								<?php } ?>
                                <p><?php echo wp_trim_words( get_the_content(), 10 ); ?></p>
								<?php if ( ! empty( $rooms ) ): ?>
                                    <div class="tf-recent-room-price">
										<?php
										if ( ! empty( $room_price ) ) {
											//get the lowest price from all available room price
											$lowest_price = wc_price( min( $room_price ) );
											echo __( "From ", "tourfic" ) . $lowest_price;
										}
										?>
                                    </div>
								<?php endif; ?>
                            </div>
                        </div>
                    </div>
				<?php } ?>
            </div>
        </div>
	<?php endif;
	wp_reset_postdata(); ?>

	<?php return ob_get_clean();
}

add_shortcode( 'tf_recent_hotel', 'tf_recent_hotel_shortcode' );
// old
add_shortcode( 'tf_tours', 'tf_recent_hotel_shortcode' );

/**
 * Recent Tour
 */
function tf_recent_tour_shortcode( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'title'        => '',  //title populer section
				'subtitle'     => '',   // Sub title populer section
				'orderby'      => 'date',
				'order'        => 'DESC',
				'count'        => 10,
				'slidestoshow' => 5,
			),
			$atts
		)
	);

	$args = array(
		'post_type'      => 'tf_tours',
		'post_status'    => 'publish',
		'orderby'        => !empty($atts['orderby']) ? $atts['orderby'] : 'date',
		'order'          => !empty($atts['order']) ? $atts['order'] : 'DESC',
		'posts_per_page' => $count,
	);

	ob_start();

	$tour_loop = new WP_Query( $args );

	// Generate an Unique ID
	$thisid = uniqid( 'tfpopular_' );

	?>
	<?php if ( $tour_loop->have_posts() ) : ?>
        <div class="tf-widget-slider recent-tour-slider">
            <div class="tf-heading">
				<?php
				if ( ! empty( $title ) ) {
					echo '<h2>' . esc_html( $title ) . '</h2>';
				}
				if ( ! empty( $subtitle ) ) {
					echo '<p>' . esc_html( $subtitle ) . '</p>';
				}
				?>
            </div>


            <div class="tf-slider-items-wrapper">
				<?php while ( $tour_loop->have_posts() ) {
					$tour_loop->the_post();
					$post_id          = get_the_ID();
					$related_comments = get_comments( array( 'post_id' => $post_id ) );
					?>
                    <div class="tf-slider-item" style="background-image: url(<?php echo get_the_post_thumbnail_url( $post_id, 'full' ); ?>);">
                        <div class="tf-slider-content">
                            <div class="tf-slider-desc">
                                <h3>
                                    <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
                                </h3>
								<?php if ( $related_comments ) { ?>
                                    <div class="tf-slider-rating-star">
                                        <i class="fas fa-star"></i> <span style="color:#fff;"><?php echo tf_total_avg_rating( $related_comments ); ?></span>
                                    </div>
								<?php } ?>
                                <p><?php echo wp_trim_words( get_the_excerpt(), 10 ); ?></p>

                            </div>
                        </div>
                    </div>
				<?php } ?>
            </div>
        </div>
	<?php endif;
	wp_reset_postdata(); ?>

	<?php return ob_get_clean();
}

add_shortcode( 'tf_recent_tour', 'tf_recent_tour_shortcode' );
// Old
add_shortcode( 'tf_tours_grid', 'tf_recent_tour_shortcode' );

/**
 * Search form
 */
function tf_search_form_shortcode( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'style'     => 'default', //recomended, populer
				'type'      => 'all',
				'title'     => '',  //title populer section
				'subtitle'  => '',   // Sub title populer section
				'classes'   => '',
				'fullwidth' => '',
				'advanced'  => '',
				'author'    => '',
				'design'	=> 1
			),
			$atts
		)
	);

	if ( $style == 'default' ) {
		$classes = " default-form ";
	}

	$type             = explode( ',', $type );
	$disable_services = tfopt( 'disable-services' ) ? tfopt( 'disable-services' ) : array();
	$child_age_limit  = tfopt( 'enable_child_age_limit' ) ? tfopt( 'enable_child_age_limit' ) : '';
	if ( $child_age_limit == '1' ) {
		$child_age_limit = ' child-age-limited';
	} else {
		$child_age_limit = '';
	}

	ob_start();
	?>

	<?php tourfic_fullwidth_container_start( $fullwidth ); ?>
    <div id="tf-booking-search-tabs" class="<?php echo 2==$design ? esc_attr('tf-shortcode-design-2-tab') : ''; ?>">

		<?php if ( $title ): ?>
            <div class="tf_widget-title"><h2><?php echo esc_html( $title ); ?></h2></div>
		<?php endif; ?>

		<?php if ( $subtitle ): ?>
            <div class="tf_widget-subtitle"><p><?php echo esc_html( $subtitle ); ?></p></div>
		<?php endif; ?>
        <!-- Booking Form Tabs -->
        <div class="tf-booking-form-tab">
			<?php do_action( 'tf_before_booking_form_tab', $type ) ?>

			<?php if ( ! in_array( 'hotel', $disable_services ) && tf_is_search_form_tab_type( 'hotel', $type ) && ! tf_is_search_form_single_tab( $type ) ) : ?>
                <button class="tf-tablinks active" data-form-id="tf-hotel-booking-form"><?php _e( 'Hotel', 'tourfic' ); ?></button>
			<?php endif; ?>

			<?php if ( ! in_array( 'tour', $disable_services ) && tf_is_search_form_tab_type( 'tour', $type ) && ! tf_is_search_form_single_tab( $type ) ) : ?>
                <button class="tf-tablinks" data-form-id="tf-tour-booking-form"><?php _e( 'Tour', 'tourfic' ); ?></button>
			<?php endif ?>

			<?php if ( ! in_array( 'apartment', $disable_services ) && tf_is_search_form_tab_type( 'apartment', $type ) && ! tf_is_search_form_single_tab( $type ) ) : ?>
                <button class="tf-tablinks" data-form-id="tf-apartment-booking-form"><?php _e( 'Apartment', 'tourfic' ); ?></button>
			<?php endif ?>

			<?php do_action( 'tf_after_booking_form_tab', $type ) ?>
        </div>

		<?php if ( ! tf_is_search_form_single_tab( $type ) ): ?>
            <!-- Booking Form tabs mobile version -->
            <div class="tf-booking-form-tab-mobile">
                <select name="tf-booking-form-tab-select" id="">
					<?php do_action( 'tf_before_booking_form_mobile_tab', $type ) ?>

					<?php if ( ! in_array( 'hotel', $disable_services ) && tf_is_search_form_tab_type( 'hotel', $type ) && ! tf_is_search_form_single_tab( $type ) ) : ?>
                        <option value="tf-hotel-booking-form"><?php _e( 'Hotel', 'tourfic' ); ?></option>
					<?php endif; ?>
					<?php if ( ! in_array( 'tour', $disable_services ) && tf_is_search_form_tab_type( 'tour', $type ) && ! tf_is_search_form_single_tab( $type ) ) : ?>
                        <option value="tf-tour-booking-form"><?php _e( 'Tour', 'tourfic' ); ?></option>
					<?php endif ?>
					<?php if ( ! in_array( 'apartment', $disable_services ) && tf_is_search_form_tab_type( 'apartment', $type ) && ! tf_is_search_form_single_tab( $type ) ) : ?>
                        <option value="tf-apartment-booking-form"><?php _e( 'Apartment', 'tourfic' ); ?></option>
					<?php endif ?>

					<?php do_action( 'tf_after_booking_form_mobile_tab', $type ) ?>
                </select>
            </div>
		<?php endif; ?>

        <!-- Booking Forms -->
        <div class="tf-booking-forms-wrapper">
			<?php
			do_action( 'tf_before_booking_form', $classes, $title, $subtitle, $type );

			if ( ! in_array( 'hotel', $disable_services ) && tf_is_search_form_tab_type( 'hotel', $type ) ) {
				?>
                <div id="tf-hotel-booking-form" style="display:block" class="tf-tabcontent <?php echo esc_attr( $child_age_limit ); ?>">
					<?php
						tf_hotel_search_form_horizontal( $classes, $title, $subtitle, $author, $advanced, $design );
					?>
                </div>
				<?php
			}
			if ( ! in_array( 'tour', $disable_services ) && tf_is_search_form_tab_type( 'tour', $type ) ) {
				?>
                <div id="tf-tour-booking-form" class="tf-tabcontent" <?php echo tf_is_search_form_single_tab( $type ) ? 'style="display:block"' : '' ?><?php echo esc_attr( $child_age_limit ); ?>>
					<?php
						tf_tour_search_form_horizontal( $classes, $title, $subtitle, $author, $advanced, $design );
					?>
                </div>
				<?php
			}
			if ( ! in_array( 'apartment', $disable_services ) && tf_is_search_form_tab_type( 'apartment', $type ) ) {
				?>
                <div id="tf-apartment-booking-form" class="tf-tabcontent" <?php echo tf_is_search_form_single_tab( $type ) ? 'style="display:block"' : '' ?><?php echo esc_attr( $child_age_limit ); ?>>
					<?php
					if ( $advanced == "enabled" ) {
						$advanced_opt = true;
						tf_apartment_search_form_horizontal( $classes, $title, $subtitle, $advanced_opt, $design );
					} else {
						$advanced_opt = false;
						tf_apartment_search_form_horizontal( $classes, $title, $subtitle, $advanced_opt, $design );
					}
					?>
                </div>
				<?php
			}

			do_action( 'tf_after_booking_form', $classes, $title, $subtitle, $type );
			?>
        </div>

    </div>
	<?php tourfic_fullwidth_container_end( $fullwidth );

	return ob_get_clean();
}

add_shortcode( 'tf_search_form', 'tf_search_form_shortcode' );
// Old shortcode
add_shortcode( 'tf_search', 'tf_search_form_shortcode' );

/**
 * Search Result Shortcode Function
 */
function tf_search_result_shortcode( $atts, $content = null ) {

	// Unwanted Slashes Remove
	if ( isset( $_GET ) ) {
		$_GET = array_map( 'stripslashes_deep', $_GET );
	}

	// Get post type
	$post_type = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '';
	if ( empty( $post_type ) ) {
		_e( '<h3>Please select fields from the search form!</h3>', 'tourfic' );

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
	$startprice = isset( $_GET['from'] ) ? absint( sanitize_key( $_GET['from'] ) ) : '';
	$endprice   = isset( $_GET['to'] ) ? absint( sanitize_key( $_GET['to'] ) ) : '';

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
		$tf_defult_views = ! empty( tf_data_types(tfopt( 'tf-template' ))['hotel_archive_view'] ) ? tf_data_types(tfopt( 'tf-template' ))['hotel_archive_view'] : 'list';
	}elseif(!empty($_GET['type']) && $_GET['type'] == "tf_tours"){
		$tf_defult_views = ! empty( tf_data_types(tfopt( 'tf-template' ))['tour_archive_view'] ) ? tf_data_types(tfopt( 'tf-template' ))['tour_archive_view'] : 'list';
	}else{
		$tf_defult_views = 'list';
	}

	$paged          = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
	$checkInOutDate = ! empty( $_GET['check-in-out-date'] ) ? explode( ' - ', $_GET['check-in-out-date'] ) : '';
	if ( ! empty( $checkInOutDate ) ) {
		$period = new DatePeriod(
			new DateTime( $checkInOutDate[0] ),
			new DateInterval( 'P1D' ),
			new DateTime( ! empty( $checkInOutDate[1] ) ? $checkInOutDate[1] : $checkInOutDate[0] . '23:59' )
		);
	} else {
		$period = '';
	}

	$post_per_page = tfopt( 'posts_per_page' ) ? tfopt( 'posts_per_page' ) : 10;
	// Main Query args
	if ( $post_type == "tf_tours" ) {
		$tf_expired_tour_showing = ! empty( tfopt( 't-show-expire-tour' ) ) ? tfopt( 't-show-expire-tour' ) : '';
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

	$taxonomy_query = new WP_Term_Query( array(
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
			'terms'    => $_GET['features'],
		);
	}
	// Hotel/Tour/Apartment Types
	if ( ! empty( $_GET['types'] ) ) {
		$args['tax_query'][] = array(
			'taxonomy' => $post_type == 'tf_hotel' ? 'hotel_type' : ($post_type == 'tf_tours' ? 'tour_type' : 'apartment_type'),
			'field'    => 'slug',
			'terms'    => $_GET['types'],
		);
	}

	$loop        = new WP_Query( $args );
	$total_posts = $loop->found_posts;
    $post_count  = $loop->post_count;
	ob_start(); ?>
    <!-- Start Content -->
	<?php
	
	$tf_tour_arc_selected_template  = ! empty( tf_data_types( tfopt( 'tf-template' ) )['tour-archive'] ) ? tf_data_types( tfopt( 'tf-template' ) )['tour-archive'] : 'design-1';
	$tf_hotel_arc_selected_template = ! empty( tf_data_types( tfopt( 'tf-template' ) )['hotel-archive'] ) ? tf_data_types( tfopt( 'tf-template' ) )['hotel-archive'] : 'design-1';
	
	if ( ( $post_type == "tf_tours" && $tf_tour_arc_selected_template == "design-1" ) || ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template == "design-1" ) ) {
		?>
        <div class="tf-column tf-page-content tf-archive-left tf-result-previews">
            <!-- Search Head Section -->
            <div class="tf-archive-head tf-flex tf-flex-align-center tf-flex-space-bttn">
                <div class="tf-search-result tf-flex">
                    <span class="tf-counter-title"><?php echo __( 'Total Results ', 'tourfic' ); ?> </span>
                    <span><?php echo ' ('; ?> </span>
                    <div class="tf-total-results">
                        <span><?php echo $total_posts; ?> </span>
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
                </div>
            </div>
            <!-- Loader Image -->
            <div id="tf_ajax_searchresult_loader">
                <div id="tf-searchresult-loader-img">
                    <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="">
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
									tf_filter_hotel_without_date( $period, $not_found, $data );
								} else {
									tf_filter_hotel_by_date( $period, $not_found, $data );
								}

							} else {

								if ( empty( $check_in_out ) ) {
									/**
									 * Check if minimum and maximum people limit matches with the search query
									 */
									$total_person = intval( $adults ) + intval( $child );
									$meta         = get_post_meta( get_the_ID(), 'tf_tours_opt', true );

									//skip the tour if the search form total people exceeds the maximum number of people in tour
									if ( ! empty( $meta['cont_max_people'] ) && $meta['cont_max_people'] < $total_person && $meta['cont_max_people'] != 0 ) {
										$total_posts --;
										continue;
									}

									//skip the tour if the search form total people less than the maximum number of people in tour
									if ( ! empty( $meta['cont_min_people'] ) && $meta['cont_min_people'] > $total_person && $meta['cont_min_people'] != 0 ) {
										$total_posts --;
										continue;
									}
									tf_filter_tour_by_without_date( $period, $total_posts, $not_found, $data );
								} else {
									tf_filter_tour_by_date( $period, $total_posts, $not_found, $data );
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
							echo '<div class="tf-nothing-found" data-post-count="0">' . __( 'Nothing Found!', 'tourfic' ) . '</div>';
						}
						$post_per_page = tfopt( 'posts_per_page' ) ? tfopt( 'posts_per_page' ) : 10;
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


							$result_query = new WP_Query( $filter_args );
							if ( $result_query->have_posts() ) {
								while ( $result_query->have_posts() ) {
									$result_query->the_post();

									if ( $post_type == 'tf_hotel' ) {

										if ( ! empty( $data ) ) {
											if ( isset( $data[4] ) && isset( $data[5] ) ) {
												[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
												tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice );
											} else {
												[ $adults, $child, $room, $check_in_out ] = $data;
												tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
											}
										} else {
											tf_hotel_archive_single_item();
										}

									} else {
										if ( ! empty( $data ) ) {
											if ( isset( $data[3] ) && isset( $data[4] ) ) {
												[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
												tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice );
											} else {
												[ $adults, $child, $check_in_out ] = $data;
												tf_tour_archive_single_item( $adults, $child, $check_in_out );
											}
										} else {
											tf_tour_archive_single_item();
										}
									}

								}
							}
							$total_pages = ceil( $total_filtered_results / $post_per_page );
							echo "<div class='tf_posts_navigation tf_posts_page_navigation'>";
							echo paginate_links( array(
								'total'   => $total_pages,
								'current' => $current_page
							) );
							echo "</div>";
						}

					} else {
						echo '<div class="tf-nothing-found" data-post-count="0">' . __( 'Nothing Found!', 'tourfic' ) . '</div>';
					}
					echo "<span hidden=hidden class='tf-posts-count'>";
					echo ! empty( $tf_total_results ) ? $tf_total_results : 0;
					echo "</span>";
					?>

                </div>
            </div>
        </div>
	<?php 
	}
	elseif ( ( $post_type == "tf_tours" && $tf_tour_arc_selected_template == "design-2" ) || ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template == "design-2" ) ) { ?>
		<div class="tf-available-archive-hetels-wrapper tf-available-rooms-wrapper" id="tf-hotel-rooms">
			<div class="tf-archive-available-rooms-head tf-available-rooms-head">
				<?php if($post_type == "tf_hotel"){ ?>
				<h2 class="tf-total-results"><?php _e("Total", "tourfic"); ?> <span><?php echo $total_posts; ?></span> <?php _e("hotels available", "tourfic"); ?></h2>
				<?php } ?>
				<?php if($post_type == "tf_tours"){ ?>
				<h2 class="tf-total-results"><?php _e("Total", "tourfic"); ?> <span><?php echo $total_posts; ?></span> <?php _e("tours available", "tourfic"); ?></h2>
				<?php } ?>
				<div class="tf-archive-filter-showing">
					<i class="ri-equalizer-line"></i>
				</div>
			</div>
			
			<!-- Loader Image -->
			<div id="tour_room_details_loader">
				<div id="tour-room-details-loader-img">
					<img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="">
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
							tf_filter_hotel_without_date( $period, $not_found, $data );
						} else {
							tf_filter_hotel_by_date( $period, $not_found, $data );
						}

					} else {

						if ( empty( $check_in_out ) ) {
							/**
							 * Check if minimum and maximum people limit matches with the search query
							 */
							$total_person = intval( $adults ) + intval( $child );
							$meta         = get_post_meta( get_the_ID(), 'tf_tours_opt', true );

							//skip the tour if the search form total people exceeds the maximum number of people in tour
							if ( ! empty( $meta['cont_max_people'] ) && $meta['cont_max_people'] < $total_person && $meta['cont_max_people'] != 0 ) {
								$total_posts --;
								continue;
							}

							//skip the tour if the search form total people less than the maximum number of people in tour
							if ( ! empty( $meta['cont_min_people'] ) && $meta['cont_min_people'] > $total_person && $meta['cont_min_people'] != 0 ) {
								$total_posts --;
								continue;
							}
							tf_filter_tour_by_without_date( $period, $total_posts, $not_found, $data );
						} else {
							tf_filter_tour_by_date( $period, $total_posts, $not_found, $data );
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
					echo '<div class="tf-nothing-found" data-post-count="0">' . __( 'Nothing Found!', 'tourfic' ) . '</div>';
				}
				$post_per_page = tfopt( 'posts_per_page' ) ? tfopt( 'posts_per_page' ) : 10;
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

					$result_query = new WP_Query( $filter_args );
					if ( $result_query->have_posts() ) {
						while ( $result_query->have_posts() ) {
							$result_query->the_post();

							if ( $post_type == 'tf_hotel' ) {

								if ( ! empty( $data ) ) {
									if ( isset( $data[4] ) && isset( $data[5] ) ) {
										[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
										tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice );
									} else {
										[ $adults, $child, $room, $check_in_out ] = $data;
										tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
									}
								} else {
									tf_hotel_archive_single_item();
								}

							} else {
								if ( ! empty( $data ) ) {
									if ( isset( $data[3] ) && isset( $data[4] ) ) {
										[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
										tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice );
									} else {
										[ $adults, $child, $check_in_out ] = $data;
										tf_tour_archive_single_item( $adults, $child, $check_in_out );
									}
								} else {
									tf_tour_archive_single_item();
								}
							}

						}
					}
					$total_pages = ceil( $total_filtered_results / $post_per_page );
					if($total_pages > 1){
						echo "<div class='tf_posts_navigation tf_posts_page_navigation'>";
						echo paginate_links( array(
							'total'   => $total_pages,
							'current' => $current_page
						) );
						echo "</div>";
					}
				}

			} else {
				echo '<div class="tf-nothing-found" data-post-count="0">' . __( 'Nothing Found!', 'tourfic' ) . '</div>';
			}
			echo "<span hidden=hidden class='tf-posts-count'>";
			echo ! empty( $tf_total_results ) ? $tf_total_results : 0;
			echo "</span>";
			?>
				
			</div>
			<!-- Available rooms end -->

		</div>
	<?php } elseif(( $post_type == "tf_tours" && $tf_tour_arc_selected_template == "design-3" ) || ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template == "design-3" )) { ?>
        <div class="tf-archive-details">
            <div class="tf-details-left">
                <div class="tf-archive-hotels-wrapper">
                    <div class="tf-archive-filter">
                        <div class="tf-archive-filter-sidebar">
                            <div class="tf-filter-wrapper">
                                <div class="tf-filter-title">
                                    <h4 class="tf-section-title"><?php _e( "Filter", "tourfic" ); ?></h4>
                                    <button class="filter-reset-btn"><?php _e( "Reset", "tourfic" ); ?></button>
                                </div>
								<?php if ( is_active_sidebar( 'tf_archive_booking_sidebar' ) ) { ?>
                                    <div id="tf__booking_sidebar">
										<?php dynamic_sidebar( 'tf_archive_booking_sidebar' ); ?>
                                    </div>
								<?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="tf-archive-top">
                        <h5 class="tf-total-results"><?php _e( "Found", "tourfic" ); ?>
                            <span><?php echo $post_count; ?></span> <?php _e( "of", "tourfic" ); ?> <span><?php echo $total_posts; ?></span> <?php _e( "Hotels", "tourfic" ); ?></h5>
						<?php $tf_defult_views = ! empty( tf_data_types( tfopt( 'tf-template' ) )['hotel_archive_view'] ) ? tf_data_types( tfopt( 'tf-template' ) )['hotel_archive_view'] : 'list'; ?>
                        <ul class="tf-archive-view">
                            <li class="tf-archive-filter-btn">
                                <i class="ri-equalizer-line"></i>
                                <span><?php _e( "All Filter", "tourfic" ); ?></span>
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

                    <!-- Loader Image -->
                    <div id="tour_room_details_loader">
                        <div id="tour-room-details-loader-img">
                            <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="">
                        </div>
                    </div>

                    <div class="tf-archive-hotels archive_ajax_result <?php echo $tf_defult_views == "list" ? esc_attr( 'tf-layout-list' ) : esc_attr( 'tf-layout-grid' ); ?>">

	                    <?php
	                    if ( $loop->have_posts() ) {
		                    $not_found = [];
		                    while ( $loop->have_posts() ) {
			                    $loop->the_post();

			                    if ( $post_type == 'tf_hotel' ) {

				                    if ( empty( $check_in_out ) ) {
					                    tf_filter_hotel_without_date( $period, $not_found, $data );
				                    } else {
					                    tf_filter_hotel_by_date( $period, $not_found, $data );
				                    }

			                    } else {

				                    if ( empty( $check_in_out ) ) {
					                    /**
					                     * Check if minimum and maximum people limit matches with the search query
					                     */
					                    $total_person = intval( $adults ) + intval( $child );
					                    $meta         = get_post_meta( get_the_ID(), 'tf_tours_opt', true );

					                    //skip the tour if the search form total people exceeds the maximum number of people in tour
					                    if ( ! empty( $meta['cont_max_people'] ) && $meta['cont_max_people'] < $total_person && $meta['cont_max_people'] != 0 ) {
						                    $total_posts --;
						                    continue;
					                    }

					                    //skip the tour if the search form total people less than the maximum number of people in tour
					                    if ( ! empty( $meta['cont_min_people'] ) && $meta['cont_min_people'] > $total_person && $meta['cont_min_people'] != 0 ) {
						                    $total_posts --;
						                    continue;
					                    }
					                    tf_filter_tour_by_without_date( $period, $total_posts, $not_found, $data );
				                    } else {
					                    tf_filter_tour_by_date( $period, $total_posts, $not_found, $data );
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
			                    echo '<div class="tf-nothing-found" data-post-count="0">' . __( 'Nothing Found!', 'tourfic' ) . '</div>';
		                    }
		                    $post_per_page = tfopt( 'posts_per_page' ) ? tfopt( 'posts_per_page' ) : 10;
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

			                    $count     = 0;
			                    $locations = [];
			                    $result_query = new WP_Query( $filter_args );
			                    if ( $result_query->have_posts() ) {
				                    while ( $result_query->have_posts() ) {
					                    $result_query->the_post();

					                    if ( $post_type == 'tf_hotel' ) {
						                    $count ++;

						                    $meta = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );
						                    $map  = ! empty( $meta['map'] ) ? tf_data_types( $meta['map'] ) : '';

						                    // Archive Page Minimum Price
						                    $archive_page_price_settings = ! empty( tfopt( 'hotel_archive_price_minimum_settings' ) ) ? tfopt( 'hotel_archive_price_minimum_settings' ) : 'all';

						                    // Rooms
						                    $b_rooms = ! empty( $meta['room'] ) ? $meta['room'] : array();
						                    if ( ! empty( $b_rooms ) && gettype( $b_rooms ) == "string" ) {
							                    $tf_hotel_b_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
								                    return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
							                    }, $b_rooms );
							                    $b_rooms                = unserialize( $tf_hotel_b_rooms_value );
						                    }

						                    /**
						                     * Calculate and get the minimum price
						                     * @author - Hena
						                     */
						                    $room_price            = [];
						                    $tf_lowestAmount       = 0;
						                    $tf_lowestAmount_items = null;
						                    if ( ! empty( $b_rooms ) ):
							                    foreach ( $b_rooms as $rkey => $b_room ) {

								                    //hotel room discount data
								                    $hotel_discount_type   = ! empty( $b_room["discount_hotel_type"] ) ? $b_room["discount_hotel_type"] : "none";
								                    $hotel_discount_amount = ! empty( $b_room["discount_hotel_price"] ) ? $b_room["discount_hotel_price"] : 0;
								                    if ( $hotel_discount_type != "none" && ! empty( $hotel_discount_amount ) ) {
									                    $tf_lowestAmount_items['amount'] = $hotel_discount_amount;
									                    $tf_lowestAmount_items['type']   = $hotel_discount_type;

									                    $tf_lowestAmount = intval( $hotel_discount_amount ); // Convert the amount to an integer for comparison
									                    if ( $hotel_discount_amount < $tf_lowestAmount ) {
										                    $tf_lowestAmount                 = $hotel_discount_amount;
										                    $tf_lowestAmount_items['amount'] = $hotel_discount_amount;
										                    $tf_lowestAmount_items['type']   = $hotel_discount_type;
									                    }
								                    }


								                    //room price
								                    $pricing_by = ! empty( $b_room['pricing-by'] ) ? $b_room['pricing-by'] : 1;
								                    if ( $pricing_by == 1 ) {
									                    if ( empty( $check_in_out ) ) {
										                    if ( ! empty( $b_room['price'] ) ) {
											                    $b_room_price = $b_room['price'];

											                    $dicount_b_room_price = 0;

											                    if ( $hotel_discount_type == "percent" ) {
												                    $dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $b_room_price - ( ( (int) $b_room_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
											                    } else if ( $hotel_discount_type == "fixed" ) {
												                    $dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $b_room_price - (int) $hotel_discount_amount ), 2 ) ) );
											                    }
											                    if ( $dicount_b_room_price != 0 ) {
												                    $room_price[] = array(
													                    "regular_price" => $b_room['price'],
													                    "sale_price"    => $dicount_b_room_price,
												                    );
											                    } else {
												                    $room_price[] = array(
													                    "sale_price" => $b_room['price']
												                    );
											                    }
										                    }
									                    } else {
										                    if ( ! empty( $b_room['avil_by_date'] ) && $b_room['avil_by_date'] == "1" ) {
											                    $avail_date = json_decode( $b_room['avail_date'], true );

											                    if ( ! empty( $avail_date ) ) {
												                    foreach ( $avail_date as $repval ) {
													                    //Initial matching date array
													                    $show_hotel = [];
													                    // Check if any date range match with search form date range and set them on array
													                    if ( ! empty( $period ) ) {
														                    foreach ( $period as $date ) {
															                    $show_hotel[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $repval['check_in'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $repval['check_out'] ) );
														                    }
													                    }
													                    if ( ! in_array( 0, $show_hotel ) ) {
														                    if ( ! empty( $repval['price'] ) ) {
															                    $repval_price         = $repval['price'];
															                    $dicount_b_room_price = 0;

															                    if ( $hotel_discount_type == "percent" ) {
																                    $dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $repval_price - ( ( (int) $repval_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
															                    } else if ( $hotel_discount_type == "fixed" ) {
																                    $dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $repval_price - (int) $hotel_discount_amount ), 2 ) ) );
															                    }
															                    if ( $dicount_b_room_price != 0 ) {
																                    $room_price[] = array(
																	                    "regular_price" => $repval['price'],
																	                    "sale_price"    => $dicount_b_room_price
																                    );
															                    } else {
																                    $room_price[] = array(
																	                    "sale_price" => $repval['price'],
																                    );
															                    }
														                    }
													                    } else {
														                    if ( ! empty( $repval['price'] ) ) {
															                    $repval_price         = $repval['price'];
															                    $dicount_b_room_price = 0;

															                    if ( $hotel_discount_type == "percent" ) {
																                    $dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $repval_price - ( ( (int) $repval_price / 100 ) * $hotel_discount_amount ), 2 ) ) );
															                    } else if ( $hotel_discount_type == "fixed" ) {
																                    $dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $repval_price - (int) $hotel_discount_amount ), 2 ) ) );
															                    }

															                    if ( $dicount_b_room_price != 0 ) {
																                    $room_price[] = array(
																	                    "regular_price" => $repval['price'],
																	                    "sale_price"    => $dicount_b_room_price
																                    );
															                    } else {
																                    $room_price[] = array(
																	                    "sale_price" => $repval['price'],
																                    );
															                    }
														                    }
													                    }
												                    }
											                    } else {
												                    $b_room_price         = $b_room['price'];
												                    $room_price[]         = $b_room_price;
												                    $dicount_b_room_price = 0;
												                    if ( $hotel_discount_type == "percent" ) {
													                    $dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $b_room_price - ( ( $b_room_price / 100 ) * $hotel_discount_amount ), 2 ) ) );
												                    } else if ( $hotel_discount_type == "fixed" ) {
													                    $dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $b_room_price - $hotel_discount_amount ), 2 ) ) );
												                    }
												                    if ( $dicount_b_room_price != 0 ) {
													                    $room_price[] = $dicount_b_room_price;
												                    }
											                    }
										                    } else {
											                    if ( ! empty( $b_room['price'] ) ) {
												                    $b_room_price = $b_room['price'];

												                    $dicount_b_room_price = 0;

												                    if ( $hotel_discount_type == "percent" ) {
													                    $dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $b_room_price - ( ( (int) $b_room_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
												                    } else if ( $hotel_discount_type == "fixed" ) {
													                    $dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $b_room_price - (int) $hotel_discount_amount ), 2 ) ) );
												                    }
												                    if ( $dicount_b_room_price != 0 ) {
													                    $room_price[] = array(
														                    "regular_price" => $b_room['price'],
														                    "sale_price"    => $dicount_b_room_price
													                    );
												                    } else {
													                    $room_price[] = array(
														                    "sale_price" => $b_room['price'],
													                    );
												                    }
											                    }
										                    }

									                    }
								                    } else if ( $pricing_by == 2 ) {
									                    if ( empty( $check_in_out ) ) {
										                    $adult_price         = ! empty( $b_room['adult_price'] ) ? $b_room['adult_price'] : 0;
										                    $child_price         = ! empty( $b_room['child_price'] ) ? $b_room['child_price'] : 0;
										                    $dicount_adult_price = 0;
										                    $dicount_child_price = 0;
										                    // discount calculation - start
										                    if ( $hotel_discount_type == "percent" ) {
											                    $dicount_adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
											                    $dicount_child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
										                    } else if ( $hotel_discount_type == "fixed" ) {
											                    $dicount_adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) );
											                    $dicount_child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) );
										                    }

										                    if ( $archive_page_price_settings == "all" ) {
											                    if ( ! empty( $b_room['adult_price'] ) ) {

												                    if ( $dicount_adult_price != 0 ) {
													                    $room_price[] = $room_price[] = array(
														                    "regular_price" => $b_room['adult_price'],
														                    "sale_price"    => $dicount_adult_price
													                    );
												                    } else {
													                    $room_price[] = array(
														                    "sale_price" => $b_room['adult_price'],
													                    );
												                    }
											                    }
											                    if ( ! empty( $b_room['child_price'] ) ) {

												                    if ( $dicount_child_price != 0 ) {
													                    $room_price[] = array(
														                    "regular_price" => $b_room['child_price'],
														                    "sale_price"    => $dicount_child_price
													                    );
												                    } else {
													                    $room_price[] = array(
														                    "sale_price" => $b_room['child_price'],
													                    );
												                    }
											                    }
										                    }
										                    if ( $archive_page_price_settings == "adult" ) {
											                    if ( ! empty( $b_room['adult_price'] ) ) {

												                    if ( $dicount_adult_price != 0 ) {
													                    $room_price[] = array(
														                    "regular_price" => $b_room['adult_price'],
														                    "sale_price"    => $dicount_adult_price
													                    );
												                    } else {
													                    $room_price[] = array(
														                    "sale_price" => $b_room['adult_price'],
													                    );
												                    }
											                    }
										                    }
										                    if ( $archive_page_price_settings == "child" ) {
											                    if ( ! empty( $b_room['child_price'] ) ) {

												                    if ( $dicount_child_price != 0 ) {
													                    $room_price[] = array(
														                    "regular_price" => $b_room['child_price'],
														                    "sale_price"    => $dicount_child_price
													                    );
												                    } else {
													                    $room_price[] = array(
														                    "sale_price" => $b_room['child_price'],
													                    );
												                    }
											                    }
										                    }
									                    } else {
										                    if ( ! empty( $b_room['avil_by_date'] ) && $b_room['avil_by_date'] == "1" ) {
											                    $avail_date = json_decode( $b_room['avail_date'], true );
											                    if ( ! empty( $avail_date ) ) {
												                    foreach ( $avail_date as $repval ) {
													                    //Initial matching date array
													                    $show_hotel = [];
													                    // Check if any date range match with search form date range and set them on array
													                    if ( ! empty( $period ) ) {
														                    foreach ( $period as $date ) {
															                    $show_hotel[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $repval['check_in'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $repval['check_out'] ) );
														                    }
													                    }
													                    if ( ! in_array( 0, $show_hotel ) ) {

														                    // discount calculation - start
														                    $adult_price         = $repval['adult_price'];
														                    $child_price         = $repval['child_price'];
														                    $dicount_adult_price = 0;
														                    $dicount_child_price = 0;

														                    if ( $hotel_discount_type == "percent" ) {
															                    // if ( ! empty( $dicount_adult_price ) && ! empty( $dicount_child_price ) ) {
															                    $dicount_adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
															                    $dicount_child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
															                    // }
														                    } else if ( $hotel_discount_type == "fixed" ) {
															                    // if ( ! empty( $dicount_adult_price ) && ! empty( $dicount_child_price ) ) {
															                    $dicount_adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) );
															                    $dicount_child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) );
															                    // }
														                    }
														                    // end
														                    if ( $archive_page_price_settings == "all" ) {
															                    if ( ! empty( $repval['adult_price'] ) ) {

																                    if ( $dicount_adult_price != 0 ) {
																	                    $room_price[] = array(
																		                    "regular_price" => $repval['adult_price'],
																		                    "sale_price"    => $dicount_adult_price
																	                    );
																                    } else {
																	                    $room_price[] = array(
																		                    "sale_price" => $repval['adult_price'],
																	                    );
																                    }
															                    }
															                    if ( ! empty( $repval['child_price'] ) ) {

																                    if ( $dicount_child_price != 0 ) {
																	                    $room_price[] = array(
																		                    "regular_price" => $repval['child_price'],
																		                    "sale_price"    => $dicount_child_price
																	                    );
																                    } else {
																	                    $room_price[] = array(
																		                    "sale_price" => $repval['child_price'],
																	                    );
																                    }
															                    }
														                    }
														                    if ( $archive_page_price_settings == "adult" ) {
															                    if ( ! empty( $repval['adult_price'] ) ) {

																                    if ( $dicount_adult_price != 0 ) {
																	                    $room_price[] = array(
																		                    "regular_price" => $repval['adult_price'],
																		                    "sale_price"    => $dicount_adult_price
																	                    );
																                    } else {
																	                    $room_price[] = array(
																		                    "sale_price" => $repval['adult_price'],
																	                    );
																                    }
															                    }
														                    }
														                    if ( $archive_page_price_settings == "child" ) {
															                    if ( ! empty( $repval['child_price'] ) ) {

																                    if ( $dicount_child_price != 0 ) {
																	                    $room_price[] = array(
																		                    "regular_price" => $repval['child_price'],
																		                    "sale_price"    => $dicount_child_price
																	                    );
																                    } else {
																	                    $room_price[] = array(
																		                    "sale_price" => $repval['child_price'],
																	                    );
																                    }
															                    }
														                    }
													                    } else {
														                    // discount calculation - start
														                    $adult_price         = $repval['adult_price'];
														                    $child_price         = $repval['child_price'];
														                    $dicount_adult_price = 0;
														                    $dicount_child_price = 0;

														                    if ( $hotel_discount_type == "percent" ) {
															                    $dicount_adult_price = ! empty( $adult_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
															                    $dicount_child_price = ! empty( $child_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
														                    } else if ( $hotel_discount_type == "fixed" ) {
															                    $dicount_adult_price = ! empty( $adult_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
															                    $dicount_child_price = ! empty( $child_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
														                    }
														                    // end
														                    if ( $archive_page_price_settings == "all" ) {
															                    if ( ! empty( $repval['adult_price'] ) ) {

																                    if ( $dicount_adult_price != 0 ) {
																	                    $room_price[] = array(
																		                    "regular_price" => $repval['adult_price'],
																		                    "sale_price"    => $dicount_adult_price
																	                    );
																                    } else {
																	                    $room_price[] = array(
																		                    "sale_price" => $repval['adult_price'],
																	                    );
																                    }
															                    }
															                    if ( ! empty( $repval['child_price'] ) ) {

																                    if ( $dicount_child_price != 0 ) {
																	                    $room_price[] = array(
																		                    "regular_price" => $repval['child_price'],
																		                    "sale_price"    => $dicount_child_price
																	                    );
																                    } else {
																	                    $room_price[] = array(
																		                    "sale_price" => $repval['child_price'],
																	                    );
																                    }
															                    }
														                    }
														                    if ( $archive_page_price_settings == "adult" ) {
															                    if ( ! empty( $repval['adult_price'] ) ) {

																                    if ( $dicount_adult_price != 0 ) {
																	                    $room_price[] = array(
																		                    "regular_price" => $repval['adult_price'],
																		                    "sale_price"    => $dicount_adult_price
																	                    );
																                    } else {
																	                    $room_price[] = array(
																		                    "sale_price" => $repval['adult_price'],
																	                    );
																                    }
															                    }
														                    }
														                    if ( $archive_page_price_settings == "child" ) {
															                    if ( $repval['child_price'] ) {

																                    if ( $dicount_child_price != 0 ) {
																	                    $room_price[] = array(
																		                    "regular_price" => $repval['child_price'],
																		                    "sale_price"    => $dicount_child_price
																	                    );
																                    } else {
																	                    $room_price[] = array(
																		                    "sale_price" => $repval['child_price'],
																	                    );
																                    }
															                    }
														                    }
													                    }
												                    }
											                    }

										                    } else {

											                    $adult_price         = ! empty( $b_room['adult_price'] ) ? $b_room['adult_price'] : '';
											                    $child_price         = ! empty( $b_room['child_price'] ) ? $b_room['child_price'] : '';
											                    $dicount_adult_price = 0;
											                    $dicount_child_price = 0;
											                    // discount calculation - start
											                    if ( $hotel_discount_type == "percent" ) {
												                    $dicount_adult_price = ! empty( $adult_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
												                    $dicount_child_price = ! empty( $child_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
											                    } else if ( $hotel_discount_type == "fixed" ) {
												                    $dicount_adult_price = ! empty( $adult_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
												                    $dicount_child_price = ! empty( $child_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
											                    }

											                    if ( $archive_page_price_settings == "all" ) {
												                    if ( ! empty( $b_room['adult_price'] ) ) {

													                    if ( $dicount_adult_price != 0 ) {
														                    $room_price[] = array(
															                    "regular_price" => $b_room['adult_price'],
															                    "sale_price"    => $dicount_adult_price
														                    );
													                    } else {
														                    $room_price[] = array(
															                    "sale_price" => $b_room['adult_price'],
														                    );
													                    }
												                    }
												                    if ( ! empty( $b_room['child_price'] ) ) {

													                    if ( $dicount_child_price != 0 ) {
														                    $room_price[] = array(
															                    "regular_price" => $b_room['child_price'],
															                    "sale_price"    => $dicount_child_price
														                    );
													                    } else {
														                    $room_price[] = array(
															                    "sale_price" => $b_room['child_price'],
														                    );
													                    }
												                    }
											                    }
											                    if ( $archive_page_price_settings == "adult" ) {
												                    if ( ! empty( $b_room['adult_price'] ) ) {

													                    if ( $dicount_adult_price != 0 ) {
														                    $room_price[] = array(
															                    "regular_price" => $b_room['adult_price'],
															                    "sale_price"    => $dicount_adult_price
														                    );
													                    } else {
														                    $room_price[] = array(
															                    "sale_price" => $b_room['adult_price'],
														                    );
													                    }
												                    }
											                    }
											                    if ( $archive_page_price_settings == "child" ) {
												                    if ( ! empty( $b_room['child_price'] ) ) {

													                    if ( $dicount_child_price != 0 ) {
														                    $room_price[] = array(
															                    "regular_price" => $b_room['child_price'],
															                    "sale_price"    => $dicount_child_price
														                    );
													                    } else {
														                    $room_price[] = array(
															                    "sale_price" => $b_room['child_price'],
														                    );
													                    }
												                    }
											                    }
										                    }
									                    }
								                    }
							                    }
						                    endif;

						                    if ( ! empty( $map ) ) {
							                    $lat = $map['latitude'];
							                    $lng = $map['longitude'];
							                    ob_start();
							                    ?>
                                                <div class="tf-map-item" data-price="<?php echo esc_attr( wc_price( $min_sale_price ) ); ?>">
                                                    <div class="tf-map-item-thumb">
                                                        <a href="<?php echo get_the_permalink(); ?>">
										                    <?php
										                    if ( ! empty( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) ) {
											                    the_post_thumbnail( 'full' );
										                    } else {
											                    echo '<img src="' . TF_ASSETS_APP_URL . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
										                    }
										                    ?>
                                                        </a>

									                    <?php
									                    if ( ! empty( $tf_lowestAmount_items ) ) : ?>
                                                            <div class="tf-map-item-discount">
											                    <?php echo $tf_lowestAmount_items['type'] == "percent" ? $tf_lowestAmount . '%' : wc_price( $tf_lowestAmount ) ?><?php _e( " Off", "tourfic" ); ?>
                                                            </div>
									                    <?php endif; ?>
                                                    </div>
                                                    <div class="tf-map-item-content">
                                                        <h4><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h4>
                                                        <div class="tf-map-item-price">
										                    <?php
										                    $room_price     = array_filter( $room_price );
										                    $min_sale_price = ! empty( $room_price ) ? min( array_column( $room_price, 'sale_price' ) ) : 0;

										                    if ( ! empty( $room_price ) ):
											                    $min_regular_price = 0;

											                    array_walk( $room_price, function ( $value ) use ( $min_sale_price, &$min_regular_price ) {
												                    if ( is_array( $value ) && count( $value ) > 0 ) {
													                    if ( array_key_exists( "regular_price", $value ) ) {
														                    if ( $value["sale_price"] == $min_sale_price ) {
															                    $min_regular_price = $value["regular_price"];
														                    }
													                    }
												                    }
											                    } );
											                    echo __( "From ", "tourfic" );
											                    //get the lowest price from all available room price
											                    $lowest_sale_price = wc_price( $min_sale_price );
											                    if ( $min_regular_price != 0 ) {
												                    $lowest_regular_price = strip_tags( wc_price( $min_regular_price ) );
												                    echo "<del>" . $lowest_regular_price . "</del>" . " " . "<span>" . $lowest_sale_price . "</span>";
											                    } else {
												                    echo " $lowest_sale_price" . " ";
											                    }
										                    endif;
										                    ?>
                                                        </div>
									                    <?php tf_archive_single_rating(); ?>
                                                    </div>
                                                </div>
							                    <?php
							                    $infoWindowtext = ob_get_clean();

							                    $locations[ $count ] = [
								                    'lat'     => (float) $lat,
								                    'lng'     => (float) $lng,
								                    'content' => base64_encode( $infoWindowtext )
							                    ];
						                    }

						                    if ( ! empty( $data ) ) {
							                    if ( isset( $data[4] ) && isset( $data[5] ) ) {
								                    [ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
								                    tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice );
							                    } else {
								                    [ $adults, $child, $room, $check_in_out ] = $data;
								                    tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
							                    }
						                    } else {
							                    tf_hotel_archive_single_item();
						                    }

					                    } else {
						                    if ( ! empty( $data ) ) {
							                    if ( isset( $data[3] ) && isset( $data[4] ) ) {
								                    [ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
								                    tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice );
							                    } else {
								                    [ $adults, $child, $check_in_out ] = $data;
								                    tf_tour_archive_single_item( $adults, $child, $check_in_out );
							                    }
						                    } else {
							                    tf_tour_archive_single_item();
						                    }
					                    }

				                    }
			                    }
			                    $total_pages = ceil( $total_filtered_results / $post_per_page );
			                    if($total_pages > 1){
				                    echo "<div class='tf_posts_navigation tf_posts_page_navigation'>";
				                    echo paginate_links( array(
					                    'total'   => $total_pages,
					                    'current' => $current_page
				                    ) );
				                    echo "</div>";
			                    }
                                ?>
                                <div id="map-datas" style="display: none"><?php echo array_filter( $locations ) ? json_encode( array_values( $locations ) ) : []; ?></div>
                                <?php
                            }

	                    } else {
		                    echo '<div class="tf-nothing-found" data-post-count="0">' . __( 'Nothing Found!', 'tourfic' ) . '</div>';
	                    }
	                    echo "<span hidden=hidden class='tf-posts-count'>";
	                    echo ! empty( $tf_total_results ) ? $tf_total_results : 0;
	                    echo "</span>";
	                    ?>
                    </div>
                </div>
            </div>
            <div class="tf-details-right tf-archive-right">
                <div id="map-marker" data-marker="<?php echo TF_ASSETS_URL . 'app/images/cluster-marker.png'; ?>"></div>
                <div class="tf-hotel-archive-map-wrap">
                    <div id="tf-hotel-archive-map"></div>
                </div>
            </div>
        </div>
	<?php } else { ?>
        <div class="tf_search_result">
            <div class="tf-action-top">
                <div class="tf-total-results">
					<?php echo esc_html__( 'Total Results ', 'tourfic' ) . '(<span>' . $total_posts . '</span>)'; ?>
                </div>
                <div class="tf-list-grid">
                    <a href="#list-view" data-id="list-view" class="change-view <?php echo $tf_defult_views=="list" ? esc_attr('active') : ''; ?>" title="<?php _e( 'List View', 'tourfic' ); ?>"><i class="fas fa-list"></i></a>
                    <a href="#grid-view" data-id="grid-view" class="change-view <?php echo $tf_defult_views=="grid" ? esc_attr('active') : ''; ?>" title="<?php _e( 'Grid View', 'tourfic' ); ?>"><i class="fas fa-border-all"></i></a>
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
								tf_filter_hotel_without_date( $period, $not_found, $data );
							} else {
								tf_filter_hotel_by_date( $period, $not_found, $data );
							}

						} elseif ( $post_type == 'tf_tours' ) {
							if ( empty( $check_in_out ) ) {
								/**
								 * Check if minimum and maximum people limit matches with the search query
								 */
								$total_person = intval( $adults ) + intval( $child );
								$meta         = get_post_meta( get_the_ID(), 'tf_tours_opt', true );

								//skip the tour if the search form total people exceeds the maximum number of people in tour
								if ( ! empty( $meta['cont_max_people'] ) && $meta['cont_max_people'] < $total_person && $meta['cont_max_people'] != 0 ) {
									$total_posts --;
									continue;
								}

								//skip the tour if the search form total people less than the maximum number of people in tour
								if ( ! empty( $meta['cont_min_people'] ) && $meta['cont_min_people'] > $total_person && $meta['cont_min_people'] != 0 ) {
									$total_posts --;
									continue;
								}
								tf_filter_tour_by_without_date( $period, $total_posts, $not_found, $data );
							} else {
								tf_filter_tour_by_date( $period, $total_posts, $not_found, $data );
							}
						} else {
							if ( empty( $check_in_out ) ) {
								tf_filter_apartment_without_date( $period, $not_found, $data );
							} else {
								tf_filter_apartment_by_date( $period, $not_found, $data );
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
						echo '<div class="tf-nothing-found" data-post-count="0">' . __( 'Nothing Found!', 'tourfic' ) . '</div>';
					}
					$post_per_page = tfopt( 'posts_per_page' ) ? tfopt( 'posts_per_page' ) : 10;
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


						$result_query = new WP_Query( $filter_args );
						if ( $result_query->have_posts() ) {
							while ( $result_query->have_posts() ) {
								$result_query->the_post();

								if ( $post_type == 'tf_hotel' ) {

									if ( ! empty( $data ) ) {
										if ( isset( $data[4] ) && isset( $data[5] ) ) {
											[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
											tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice );
										} else {
											[ $adults, $child, $room, $check_in_out ] = $data;
											tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
										}
									} else {
										tf_hotel_archive_single_item();
									}

								} elseif ( $post_type == 'tf_tours' ) {
									if ( ! empty( $data ) ) {
										if ( isset( $data[3] ) && isset( $data[4] ) ) {
											[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
											tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice );
										} else {
											[ $adults, $child, $check_in_out ] = $data;
											tf_tour_archive_single_item( $adults, $child, $check_in_out );
										}
									} else {
										tf_tour_archive_single_item();
									}
								} else {
									if ( ! empty( $data ) ) {
										if ( isset( $data[4] ) && isset( $data[5] ) ) {
											tf_apartment_archive_single_item( $data );
										} else {
											tf_apartment_archive_single_item( $data );
										}
									} else {
										tf_apartment_archive_single_item();
									}
								}

							}
						}
						$total_pages = ceil( $total_filtered_results / $post_per_page );
						echo "<div class='tf_posts_navigation tf_posts_page_navigation'>";
						echo paginate_links( array(
							'total'   => $total_pages,
							'current' => $current_page
						) );
						echo "</div>";
					}

				} else {
					echo '<div class="tf-nothing-found" data-post-count="0">' . __( 'Nothing Found!', 'tourfic' ) . '</div>';
				}
				echo "<span hidden=hidden class='tf-posts-count'>";
				echo ! empty( $tf_total_results ) ? $tf_total_results : 0;
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

add_shortcode( 'tf_search_result', 'tf_search_result_shortcode' );

/**
 * Hotel, Tour review slider shortcode
 * @author Abu Hena
 * @since 2.8.9
 */
add_shortcode( 'tf_reviews', 'tf_reviews_shortcode' );
function tf_reviews_shortcode( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'type'           => 'tf_hotel',
				'number'         => '10',
				'count'          => '3',
				'speed'          => '2000',
				'arrows'         => 'false',
				'dots'           => 'true',
				'autoplay'       => 'false',
				'slidesToScroll' => 1,
				'infinite'       => 'false',
			),
			$atts
		)
	);
	$type == "hotel" ? $type = "tf_hotel" : $type == '';
	$type == "tour" ? $type = "tf_tours" : $type == '';
	$type == "apartment" ? $type = "tf_apartment" : $type == '';
	ob_start();
	?>
    <div class="tf-single-review tf-reviews-slider">

		<?php
		$args     = array(
			'post_type' => $type,
			'number'    => $number,
		);
		$comments = get_comments( $args );


		if ( $comments ) {
			foreach ( $comments as $comment ) {
				// Get rating details
				$tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
				if ( $tf_overall_rate == false ) {
					$tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
					$tf_overall_rate = tf_average_ratings( $tf_comment_meta );
				}
				$base_rate = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
				$c_rating  = tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );

				// Comment details
				$c_avatar      = get_avatar( $comment, '56' );
				$c_author_name = $comment->comment_author;
				$c_date        = $comment->comment_date;
				$c_content     = $comment->comment_content;
				?>
                <div class="tf-single-details">
                    <div class="tf-review-avatar"><?php echo $c_avatar; ?></div>
                    <div class="tf-review-details">
                        <div class="tf-name"><?php echo $c_author_name; ?></div>
                        <div class="tf-date"><?php echo $c_date; ?></div>
                        <div class="tf-rating-stars">
							<?php echo $c_rating; ?>
                        </div>
                        <div class="tf-description"><?php echo wp_trim_words( $c_content, 25 ); ?></div>
                    </div>
                </div>
				<?php
			}
		}
		?>
    </div>
    <script>
        /**
         * Init the reviews slider
         */
        jQuery('document').ready(function ($) {

            $(".tf-reviews-slider").each(function () {
                var $this = $(this);
                $this.slick({
                    dots: <?php echo wp_json_encode( filter_var( $dots, FILTER_VALIDATE_BOOLEAN ) ); ?>,
                    arrows: <?php echo wp_json_encode( filter_var( $arrows, FILTER_VALIDATE_BOOLEAN ) ); ?>,
                    slidesToShow: <?php echo (int) absint( $count ); ?>,
                    infinite: <?php echo wp_json_encode( filter_var( $infinite, FILTER_VALIDATE_BOOLEAN ) ); ?>,
                    speed: <?php echo (int) absint( $speed ); ?>,
                    autoplay: <?php echo wp_json_encode( filter_var( $autoplay, FILTER_VALIDATE_BOOLEAN ) ); ?>,
                    autoplaySpeed: <?php echo (int) absint( $speed ); ?>,
                    slidesToScroll: <?php echo (int) absint( $slidesToScroll ); ?>,
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 1,
                            }
                        },
                        {
                            breakpoint: 600,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });
            })
        })
    </script>
	<?php
	return ob_get_clean();
}

/**
 * Hotel Grid/Slider by locations shortcode
 * @author Abu Hena
 * @since 2.8.9
 */
add_shortcode( 'tf_hotel', 'tf_hotels_grid_slider' );
function tf_hotels_grid_slider( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'title'     => '',
				'subtitle'  => '',
				'locations' => '',
				'count'     => '3',
				'style'     => 'grid',
			),
			$atts
		)
	);

	$args = array(
		'post_type'      => 'tf_hotel',
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'posts_per_page' => $count,
	);


	if ( ! empty( $locations ) && $locations !== 'all' ) {
		$locations         = explode( ',', $locations );
		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'hotel_location',
				'field'    => 'term_id',
				'terms'    => $locations,
			)
		);
	}
	ob_start();

	if ( $style == 'slider' ) {
		$slider_activate = 'tf-slider-activated';
	} else {
		$slider_activate = 'tf-hotel-grid';
	}
	$hotel_loop = new WP_Query( $args );

	?>
	<?php if ( $hotel_loop->have_posts() ) : ?>
        <div class="tf-widget-slider recent-hotel-slider">
            <div class="tf-heading">
		        <?php
		        echo ! empty( $title ) ? '<h2>' . esc_html( $title ) . '</h2>' : '';
		        echo ! empty( $subtitle ) ? '<p>' . esc_html( $subtitle ) . '</p>' : '';
		        ?>
            </div>

            <div class="<?php echo esc_attr( $slider_activate ); ?>">
				<?php while ( $hotel_loop->have_posts() ) {
					$hotel_loop->the_post();
					$post_id                = get_the_ID();
					$related_comments_hotel = get_comments( array( 'post_id' => $post_id ) );
					$meta                   = get_post_meta( $post_id, 'tf_hotels_opt', true );
					$rooms                  = ! empty( $meta['room'] ) ? $meta['room'] : '';
					if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
						$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
							return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
						}, $rooms );
						$rooms                = unserialize( $tf_hotel_rooms_value );
					}
					//get and store all the prices for each room
					$room_price = [];
					if ( $rooms ) {
						foreach ( $rooms as $room ) {

							$pricing_by = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : 1;
							if ( $pricing_by == 1 ) {
								if ( ! empty( $room['price'] ) ) {
									$room_price[] = $room['price'];
								}
								if ( ! empty( $room['avil_by_date'] ) && $room['avil_by_date'] == "1" ) {
									if ( ! empty( $room['avail_date'] ) ) {
										$avail_dates = json_decode($room['avail_date'], true);
										foreach ( $avail_dates as $repval ) {
											if ( ! empty( $repval['price'] ) ) {
												$room_price[] = $repval['price'];
											}
										}
									}
								}
							} else if ( $pricing_by == 2 ) {
								if ( ! empty( $room['adult_price'] ) ) {
									$room_price[] = $room['adult_price'];
								}
								if ( ! empty( $room['child_price'] ) ) {
									$room_price[] = $room['child_price'];
								}
								if ( ! empty( $room['avil_by_date'] ) && $room['avil_by_date'] == "1" ) {
									if ( ! empty( $room['avail_date'] ) ) {
										$avail_dates = json_decode($room['avail_date'], true);
										foreach ( $avail_dates as $repval ) {
											if ( ! empty( $repval['adult_price'] ) ) {
												$room_price[] = $repval['adult_price'];
											}
											if ( ! empty( $repval['child_price'] ) ) {
												$room_price[] = $repval['child_price'];
											}
										}
									}
								}
							}
						}
					}
					?>
                    <div class="tf-slider-item"
                         style="background-image: url(<?php echo ! empty( get_the_post_thumbnail_url( $post_id, 'full' ) ) ? get_the_post_thumbnail_url( $post_id, 'full' ) : TF_ASSETS_APP_URL . '/images/feature-default.jpg'; ?>);">
                        <div class="tf-slider-content">
                            <div class="tf-slider-desc">
                                <h3>
                                    <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
                                </h3>
								<?php if ( $related_comments_hotel ) { ?>
                                    <div class="tf-slider-rating-star">
                                        <i class="fas fa-star"></i> <span style="color:#fff;"><?php echo tf_total_avg_rating( $related_comments_hotel ); ?></span>
                                    </div>
								<?php } ?>
                                <p><?php echo wp_trim_words( get_the_content(), 10 ); ?></p>
								<?php if ( ! empty( $rooms ) ): ?>
                                    <div class="tf-recent-room-price">
										<?php
										if ( ! empty( $room_price ) ) {
											//get the lowest price from all available room price
											$lowest_price = wc_price( min( $room_price ) );
											echo __( "From ", "tourfic" ) . $lowest_price;
										}
										?>
                                    </div>
								<?php endif; ?>
                            </div>
                        </div>
                    </div>
				<?php } ?>
            </div>
        </div>
	<?php endif;
	wp_reset_postdata();

	return ob_get_clean();
}

/**
 * Tour Grid/Slider by locations shortcode
 * @author Abu Hena
 * @since 2.8.9
 */
add_shortcode( 'tf_tour', 'tf_tours_grid_slider' );
function tf_tours_grid_slider( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'title'        => '',
				'subtitle'     => '',
				'destinations' => '',
				'count'        => '3',
				'style'        => 'grid',
			),
			$atts
		)
	);

	$args = array(
		'post_type'      => 'tf_tours',
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'posts_per_page' => $count,
	);
	//Check if destination selected/choosen
	if ( ! empty( $destinations ) && $destinations !== 'all' ) {
		$destinations      = explode( ',', $destinations );
		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'tour_destination',
				'field'    => 'term_id',
				'terms'    => $destinations,
			)
		);
	}
	ob_start();

	if ( $style == 'slider' ) {
		$slider_activate = 'tf-slider-activated';
	} else {
		$slider_activate = 'tf-hotel-grid';
	}
	$tour_loop = new WP_Query( $args );

	?>
	<?php if ( $tour_loop->have_posts() ) : ?>
        <div class="tf-widget-slider recent-tour-slider">
            <div class="tf-heading">
		        <?php
		        echo ! empty( $title ) ? '<h2>' . esc_html( $title ) . '</h2>' : '';
		        echo ! empty( $subtitle ) ? '<p>' . esc_html( $subtitle ) . '</p>' : '';
		        ?>
            </div>

            <div class="<?php echo esc_attr( $slider_activate ); ?>">
				<?php while ( $tour_loop->have_posts() ) {
					$tour_loop->the_post();
					$post_id          = get_the_ID();
					$related_comments = get_comments( array( 'post_id' => $post_id ) );
					?>
                    <div class="tf-slider-item" style="background-image: url(<?php echo get_the_post_thumbnail_url( $post_id, 'full' ); ?>);">
                        <div class="tf-slider-content">
                            <div class="tf-slider-desc">
                                <h3>
                                    <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
                                </h3>
								<?php if ( $related_comments ) { ?>
                                    <div class="tf-slider-rating-star">
                                        <i class="fas fa-star"></i> <span style="color:#fff;"><?php echo tf_total_avg_rating( $related_comments ); ?></span>
                                    </div>
								<?php } ?>
                                <p><?php echo wp_trim_words( get_the_excerpt(), 10 ); ?></p>

                            </div>
                        </div>
                    </div>
				<?php } ?>
            </div>
        </div>
	<?php endif;
	wp_reset_postdata();

	return ob_get_clean();
}

/**
 * Recent blog shortcode
 * @author Abu Hena
 * @since 2.9.0
 */
add_shortcode( 'tf_recent_blog', 'tf_recent_blog_callback' );
function tf_recent_blog_callback( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'title'    => '',
				'subtitle' => '',
				'count'    => '5',
				'cats'     => '',

			),
			$atts
		)
	);

	$args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'posts_per_page' => $count,
	);

	//Check if category selected/choosen
	if ( ! empty( $cats ) && $cats !== 'all' ) {
		$cats              = explode( ',', $cats );
		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => $cats,
			)
		);
	}
	$loop = new WP_Query( $args );

	ob_start();

	?>
	<?php if ( $loop->have_posts() ) { ?>
        <div class="tf-recent-blog-wrapper">
            <div class="tf-heading">
		        <?php
		        echo ! empty( $title ) ? '<h2>' . esc_html( $title ) . '</h2>' : '';
		        echo ! empty( $subtitle ) ? '<p>' . esc_html( $subtitle ) . '</p>' : '';
		        ?>
            </div>


            <div class="recent-blogs">
				<?php while ( $loop->have_posts() ) {
					$loop->the_post();
					$post_id = get_the_ID();

					//different markup for first 3 posts
					if ( $loop->current_post == 0 ) {
						echo "<div class='post-section-one'>";
					}

					if ( $loop->current_post <= 2 ) {
						?>

                        <div class="tf-single-item" style="background-image: url(<?php echo get_the_post_thumbnail_url( $post_id, 'full' ); ?>);">
                            <div class="tf-post-content">
                                <div class="tf-post-desc">
                                    <h3>
                                        <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
                                    </h3>
                                    <p><?php echo wp_trim_words( get_the_excerpt(), 10 ); ?></p>

                                </div>
                            </div>
                        </div>
						<?php
						if ( $loop->current_post == 2 ) {
							echo "</div>";
						}
					} else { ?>
                        <div class="tf-single-item" style="background-image: url(<?php echo get_the_post_thumbnail_url( $post_id, 'full' ); ?>);">
                            <div class="tf-post-content">
                                <div class="tf-post-desc">
                                    <h3>
                                        <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
                                    </h3>
                                    <p><?php echo wp_trim_words( get_the_excerpt(), 10 ); ?></p>

                                </div>
                            </div>
                        </div>

					<?php }
				} ?>
            </div>
        </div>
	<?php } else {
		echo __( 'No posts found', 'tourfic' );
	}
	wp_reset_postdata();

	return ob_get_clean();
}

/**
 * Apartment Grid/Slider by locations shortcode
 * @author Foysal
 */
add_shortcode( 'tf_apartment', 'tf_apartments_grid_slider' );
function tf_apartments_grid_slider( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'title'     => '',
				'subtitle'  => '',
				'locations' => '',
				'count'     => '3',
				'style'     => 'grid',
			),
			$atts
		)
	);

	$args = array(
		'post_type'      => 'tf_apartment',
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'posts_per_page' => $count,
	);

	if ( ! empty( $locations ) ) {
		if($locations === 'all') {
			$locations = get_terms( 'apartment_location', array(
				'hide_empty' => 0,
				'fields' => 'ids'
			) );
		} else {
			$locations = explode( ',', $locations );
		}

		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'apartment_location',
				'field'    => 'term_id',
				'terms'    => $locations,
			)
		);
	}

	ob_start();

	if ( $style == 'slider' ) {
		$slider_activate = 'tf-slider-activated';
	} else {
		$slider_activate = 'tf-hotel-grid';
	}
	$apartment_loop = new WP_Query( $args );
	?>
	<?php if ( $apartment_loop->have_posts() ) : ?>
        <div class="tf-widget-slider recent-apartment-slider">
            <div class="tf-heading">
		        <?php
		        echo ! empty( $title ) ? '<h2>' . esc_html( $title ) . '</h2>' : '';
		        echo ! empty( $subtitle ) ? '<p>' . esc_html( $subtitle ) . '</p>' : '';
		        ?>
            </div>

            <div class="<?php echo esc_attr( $slider_activate ); ?>">
				<?php while ( $apartment_loop->have_posts() ) {
					$apartment_loop->the_post();
					$post_id       = get_the_ID();
					$post_comments = get_comments( array( 'post_id' => $post_id ) );
					?>
                    <div class="tf-slider-item" style="background-image: url(<?php echo get_the_post_thumbnail_url( $post_id, 'full' ); ?>);">
                        <div class="tf-slider-content">
                            <div class="tf-slider-desc">
                                <h3>
                                    <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
                                </h3>
								<?php if ( $post_comments ) { ?>
                                    <div class="tf-slider-rating-star">
                                        <i class="fas fa-star"></i> <span style="color:#fff;"><?php echo tf_total_avg_rating( $post_comments ); ?></span>
                                    </div>
								<?php } ?>
                                <p><?php echo wp_trim_words( get_the_content(), 10 ); ?></p>
                            </div>
                        </div>
                    </div>
				<?php } ?>
            </div>
        </div>
	<?php endif;
	wp_reset_postdata();

	return ob_get_clean();
}

/**
 * Recent Apartment Slider
 * shortcode: [tf_recent_apartment]
 *
 * @author Foysal
 */
add_shortcode( 'tf_recent_apartment', 'tf_recent_apartment_shortcode' );
function tf_recent_apartment_shortcode( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'title'        => '',
				'subtitle'     => '',
				'count'        => 10,
				'slidestoshow' => 5,
			),
			$atts
		)
	);

	$args = array(
		'post_type'      => 'tf_apartment',
		'post_status'    => 'publish',
		'orderby'        => !empty($atts['orderby']) ? $atts['orderby'] : 'date',
		'order'          => !empty($atts["order"]) ? $atts["order"] : "DESC",
		'posts_per_page' => $count,
	);

	ob_start();

	$apartment_loop = new WP_Query( $args );

	// Generate an Unique ID
	$thisid = uniqid( 'tfpopular_' );

	?>
	<?php if ( $apartment_loop->have_posts() ) : ?>
        <div class="tf-widget-slider recent-apartment-slider">
            <div class="tf-heading">
		        <?php
		        echo ! empty( $title ) ? '<h2>' . esc_html( $title ) . '</h2>' : '';
		        echo ! empty( $subtitle ) ? '<p>' . esc_html( $subtitle ) . '</p>' : '';
		        ?>
            </div>

            <div class="tf-slider-items-wrapper">
				<?php while ( $apartment_loop->have_posts() ) {
					$apartment_loop->the_post();
					$post_id                    = get_the_ID();
					$related_comments_apartment = get_comments( array( 'post_id' => $post_id ) );
					$meta                       = get_post_meta( $post_id, 'tf_apartment_opt', true );

					?>
                    <div class="tf-slider-item" style="background-image: url(<?php echo get_the_post_thumbnail_url( $post_id, 'full' ); ?>);">
                        <div class="tf-slider-content">
                            <div class="tf-slider-desc">
                                <h3>
                                    <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
                                </h3>
								<?php if ( $related_comments_apartment ) { ?>
                                    <div class="tf-slider-rating-star">
                                        <i class="fas fa-star"></i> <span style="color:#fff;"><?php echo tf_total_avg_rating( $related_comments_apartment ); ?></span>
                                    </div>
								<?php } ?>
                                <p><?php echo wp_trim_words( get_the_content(), 10 ); ?></p>
                            </div>
                        </div>
                    </div>
				<?php } ?>
            </div>
        </div>
	<?php endif;
	wp_reset_postdata(); ?>

	<?php return ob_get_clean();
}

/**
 * Apartment location shortcode
 * shortcode: [tf_apartment_locations]
 *
 * @author Foysal
 */
add_shortcode( 'tf_apartment_locations', 'shortcode_apartment_location' );
function shortcode_apartment_location( $atts, $content = null ) {

	// Shortcode extract
	extract(
		shortcode_atts(
			array(
				'orderby'    => 'name',
				'order'      => 'ASC',
				'hide_empty' => 0,
				'ids'        => '',
				'limit'      => - 1,
			),
			$atts
		)
	);

	$locations = get_terms( array(
		'taxonomy'     => 'apartment_location',
		'orderby'      => $orderby,
		'order'        => $order,
		'hide_empty'   => $hide_empty,
		'hierarchical' => 0,
		'search'       => '',
		'number'       => $limit == - 1 ? false : $limit,
		'include'      => $ids,
	) );

	shuffle( $locations );
	ob_start();

	if ( $locations ) { ?>
        <section id="recomended_section_wrapper">
            <div class="recomended_inner">

				<?php foreach ( $locations as $term ) {

					$meta      = get_term_meta( $term->term_id, 'tf_apartment_location', true );
					$image_url = ! empty( $meta['image'] ) ? $meta['image'] : TF_ASSETS_APP_URL . 'images/feature-default.jpg';
					$term_link = get_term_link( $term );

					if ( is_wp_error( $term_link ) ) {
						continue;
					} ?>

                    <div class="single_recomended_item">
                        <a href="<?php echo $term_link; ?>">
                            <div class="single_recomended_content" style="background-image: url(<?php echo $image_url; ?>);">
                                <div class="recomended_place_info_header">
                                    <h3><?php echo esc_html( $term->name ); ?></h3>
                                    <p><?php printf( _n( '%s apartment', '%s apartments', $term->count, 'tourfic' ), $term->count ); ?></p>
                                </div>
                            </div>
                        </a>
                    </div>

				<?php } ?>

            </div>
        </section>
	<?php }

	return ob_get_clean();
}

/**
 * Vendor Posts Shortcode
 * @author Jahid
 * @since 2.9.13
 */
add_shortcode( 'tf_vendor_post', 'tf_vendor_post_callback' );
function tf_vendor_post_callback( $atts, $content = null ) {
	ob_start();
	extract(
		shortcode_atts(
			array(
				'type'      => '',
				'style'     => 'grid',
				'count'     => 4,
				'vendor'    => '',
				'vendor_id' => '',
			),
			$atts
		)
	);

	$args = array(
		'post_type'      => $type,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'posts_per_page' => $count,
		'author'         => sanitize_key( $vendor_id ),
	);

	$tf_vendors_posts = new WP_Query( $args );
	if ( $tf_vendors_posts->have_posts() ) :
		?>
        <div class="tf-widget-slider recent-tour-slider">
            <div class="tf-hotel-grid">
				<?php while ( $tf_vendors_posts->have_posts() ) {
					$tf_vendors_posts->the_post();
					$post_id          = get_the_ID();
					$related_comments = get_comments( array( 'post_id' => $post_id ) );
					?>
                    <div class="tf-slider-item"
                         style="background-image: url(<?php echo ! empty( get_the_post_thumbnail_url( $post_id, 'full' ) ) ? get_the_post_thumbnail_url( $post_id, 'full' ) : TF_ASSETS_APP_URL . '/images/feature-default.jpg'; ?>);">
                        <div class="tf-slider-content">
                            <div class="tf-slider-desc">
                                <h3>
                                    <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
                                </h3>
                                <p><?php echo wp_trim_words( get_the_excerpt(), 10 ); ?></p>

                            </div>
                        </div>
                    </div>
				<?php } ?>
            </div>
        </div>
	<?php endif;
	wp_reset_postdata();

	return ob_get_clean();
}

/**
 * External Hotel, Tour, Apartment listing
 * @author Foysal
 * @since 2.10.4
 */
add_shortcode( 'tf_external_listings', 'tf_external_listings_shortcode' );
function tf_external_listings_shortcode( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'title'     => '',
				'subtitle'  => '',
				'locations' => '',
				'count'     => '3',
				'style'     => 'grid',
                'type'      => 'hotel',
			),
			$atts
		)
	);

    $external_post_ids = tf_get_external_post_ids($type, $locations);

	$args = array(
        'post_type'      => $type == 'hotel' ? 'tf_hotel' : ( $type == 'tour' ? 'tf_tours' : 'tf_apartment' ),
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'posts_per_page' => $count,
        'post__in'       => $external_post_ids,
	);

	ob_start();

	if ( $style == 'slider' ) {
		$slider_activate = 'tf-slider-activated';
	} else {
		$slider_activate = 'tf-hotel-grid';
	}
	$post_loop = new WP_Query( $args );
	?>
	<?php if ( $post_loop->have_posts() ) : ?>
        <div class="tf-widget-slider recent-hotel-slider">
            <div class="tf-heading">
				<?php
                echo ! empty( $title ) ? '<h2>' . esc_html( $title ) . '</h2>' : '';
                echo ! empty( $subtitle ) ? '<p>' . esc_html( $subtitle ) . '</p>' : '';
				?>
            </div>

            <div class="<?php echo esc_attr( $slider_activate ); ?>">
				<?php while ( $post_loop->have_posts() ) :
					$post_loop->the_post();
					$post_id       = get_the_ID();
					$post_comments = get_comments( array( 'post_id' => $post_id ) );

                    if($type == 'hotel') {
	                    $meta  = get_post_meta( $post_id, 'tf_hotels_opt', true );
                    } elseif ($type == 'tour'){
                        $meta  = get_post_meta( $post_id, 'tf_tours_opt', true );
                    } elseif ($type == 'apartment'){
                        $meta  = get_post_meta( $post_id, 'tf_apartments_opt', true );
                    }

					if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
						$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
						$tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
						$tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&room={room}';
						$tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
					}
					if ( 2 == $tf_booking_type && ! empty( $tf_booking_url ) ) {
						$external_search_info = array(
							'{adult}'    => ! empty( $adult ) ? $adult : 1,
							'{child}'    => ! empty( $child ) ? $child : 0,
							'{checkin}'  => ! empty( $check_in ) ? $check_in : date( 'Y-m-d' ),
							'{checkout}' => ! empty( $check_out ) ? $check_out : date( 'Y-m-d', strtotime( '+1 day' ) ),
							'{room}'     => ! empty( $room_selected ) ? $room_selected : 1,
						);
						if ( ! empty( $tf_booking_attribute ) ) {
							$tf_booking_query_url = str_replace( array_keys( $external_search_info ), array_values( $external_search_info ), $tf_booking_query_url );
							if ( ! empty( $tf_booking_query_url ) ) {
								$tf_booking_url = $tf_booking_url . '/?' . $tf_booking_query_url;
							}
						}
					}

                    if($tf_booking_type == 2 && !empty($tf_booking_url)):
					?>
                    <div class="tf-slider-item" style="background-image: url(<?php echo ! empty( get_the_post_thumbnail_url( $post_id, 'full' ) ) ? get_the_post_thumbnail_url( $post_id, 'full' ) : TF_ASSETS_APP_URL . '/images/feature-default.jpg'; ?>);">
                        <div class="tf-slider-content">
                            <div class="tf-slider-desc">
                                <h3><a href="<?php echo esc_url($tf_booking_url) ?>" target="_blank"><?php the_title() ?></a></h3>
								<?php if ( $post_comments ) { ?>
                                    <div class="tf-slider-rating-star">
                                        <i class="fas fa-star"></i> <span style="color:#fff;"><?php echo tf_total_avg_rating( $post_comments ); ?></span>
                                    </div>
								<?php } ?>
                                <p><?php echo wp_trim_words( get_the_content(), 10 ); ?></p>
								<?php if ( ! empty( $rooms ) ): ?>
                                    <div class="tf-recent-room-price">
										<?php
										if ( ! empty( $room_price ) ) {
											//get the lowest price from all available room price
											$lowest_price = wc_price( min( $room_price ) );
											echo __( "From ", "tourfic" ) . $lowest_price;
										}
										?>
                                    </div>
								<?php endif; ?>
                            </div>
                        </div>
                    </div>
				<?php endif; endwhile; ?>
            </div>
        </div>
	<?php endif;
	wp_reset_postdata();

	return ob_get_clean();
}

/*
 * Filter external post ids from post type, location
 * @author Foysal
 */
if(!function_exists('tf_get_external_post_ids')){
    function tf_get_external_post_ids($post_type, $location){

        $args = array(
            'post_type'      => $post_type == 'hotel' ? 'tf_hotel' : ( $post_type == 'tour' ? 'tf_tours' : 'tf_apartment' ),
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        );

        if ( ! empty( $location ) && $location !== 'all' ) {
            $locations         = explode( ',', $location );
            $args['tax_query'] = array(
                'relation' => 'AND',
                array(
                    'taxonomy' => $post_type == 'hotel' ? 'hotel_location' : ( $post_type == 'tour' ? 'tour_destination' : 'apartment_location' ),
                    'field'    => 'term_id',
                    'terms'    => $locations,
                )
            );
        }

        $post_loop = new WP_Query( $args );
        $post_ids = [];
        if ( $post_loop->have_posts() ) :
            while ( $post_loop->have_posts() ) :
                $post_loop->the_post();

                if($post_type == 'hotel'){
	                $meta = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );
                } elseif($post_type == 'tour'){
                    $meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
                } elseif($post_type == 'apartment'){
                    $meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
                }

                $tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
                $tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';

                if ( 2 == $tf_booking_type && ! empty( $tf_booking_url ) ) {
                    $post_ids[] = get_the_ID();
                }
            endwhile;
        endif;
        wp_reset_postdata();

        return $post_ids;
    }
}
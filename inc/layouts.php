<?php

/**
 * Archive post layout
 */
function tourfic_archive_single() {
    $tf_room = get_field( 'tf_room' ) ? get_field( 'tf_room' ) : array();
	
	$destination = isset($_GET['destination']) ? $_GET['destination'] : "";
	$adults = isset($_GET['adults']) ? $_GET['adults'] : "1";
	$children = isset($_GET['children']) ? $_GET['children'] : "0";
	$room = isset($_GET['room']) ? $_GET['room'] : "1";
	$check_in_date = isset($_GET['check-in-date']) ? $_GET['check-in-date'] : "";
	$check_out_date = isset($_GET['check-out-date']) ? $_GET['check-out-date'] : "";
    ?>
	<div class="single-tour-wrap">
		<div class="single-tour-inner">
			<div class="tourfic-single-left">
				<?php if ( has_post_thumbnail() ): ?>
					<?php the_post_thumbnail( 'full' );?>
				<?php endif;?>
			</div>
			<div class="tourfic-single-right">
				<!-- Title area Start -->
				<div class="tf_property_block_main_row">
					<div class="tf_item_main_block">
						<div class="tf-hotel__title-wrap">
							<a href="<?php echo get_the_permalink() . '?destination=' . $destination. '&adults=' . $adults . '&children=' . $children . '&room=' . $room . '&check-in-date=' . $check_in_date . '&check-out-date=' . $check_out_date; ?>"><h3 class="tourfic_hotel-title"><?php the_title();?></h3></a>
						</div>
						<?php tourfic_map_link();?>
					</div>
					<?php tourfic_item_review_block();?>
				</div>
				<!-- Title area End -->

				<?php if ( $tf_room ): $i = 0;?>
										<!-- Room details start -->
										<div class="sr_rooms_table_block">
											<?php foreach ( $tf_room as $key => $room_type ): ?>
												<?php
    if ( ++$i > 1 ) {
            break;
        }
        // Array to variable
        extract( $room_type );
        ?>
					<div class="room_details">
						<div class="featuredRooms">
							<div class="prco-ltr-right-align-helper">
								<div class="tf-archive-shortdesc"><?php echo do_shortcode( $short_desc ); ?></div>
							</div>
							<div class="roomNameInner">
								<div class="room_link">
									<div class="roomrow_flex">
										<div class="roomName_flex">
											<div class="tf-archive-roomname"><strong><?php echo esc_html( $name ); ?></strong> <span class="dash">-</span> <span><?php tourfic_pax( $pax );?></span></div>
											<ul class="tf-archive-desc"><?php echo do_shortcode( $desc ); ?></ul>
										</div>

										<div class="roomPrice roomPrice_flex sr_discount">
											<div class="bui-price-display__value prco-inline-block-maker-helper" aria-hidden="true"><?php echo tourfic_price_html( $price, $sale_price ); ?></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach;?>
				</div>
				<!-- Room details end -->

				<div class="availability-btn-area">
					<a href="<?php echo get_the_permalink() . '?destination=' . $destination. '&adults=' . $adults . '&children=' . $children . '&room=' . $room . '&check-in-date=' . $check_in_date . '&check-out-date=' . $check_out_date; ?>" class="button tf_button"><?php esc_html_e( 'Book Now', 'tourfic' );?></a>
				</div>
				<?php endif;?>


			</div>
		</div>
	</div>

	<?php
}

/**
 * Tours Archive
 */
function tf_tours_archive_single() {

	$meta = get_post_meta( get_the_ID(),'tf_tours_option',true );
	$featured = $meta['tour_as_featured'];
	$feature_meta = $meta['tour_feature'];
	$tour_destination = isset($_GET['tour_destination']) ? $_GET['tour_destination'] : "";
	$adults = isset($_GET['adults']) ? $_GET['adults'] : "1";
	$children = isset($_GET['children']) ? $_GET['children'] : "0";
	$infant = isset($_GET['infant']) ? $_GET['infant'] : "0";
	$check_in_date = isset($_GET['check-in-date']) ? $_GET['check-in-date'] : "";
	$check_out_date = isset($_GET['check-out-date']) ? $_GET['check-out-date'] : "";
    ?>
	<div class="single-tour-wrap">
		<div class="single-tour-inner">
			<?php if($featured){ ?>
				<div class="tf-featured"><?php _e( 'Featured','tourfic' ) ?></div>
			<?php }	?>
			<div class="tourfic-single-left">
				<?php if ( has_post_thumbnail() ): ?>
					<?php the_post_thumbnail( 'full' );?>
				<?php endif;?>
			</div>
			<div class="tourfic-single-right">
				<!-- Title area Start -->
				<div class="tf_property_block_main_row">
					<div class="tf_item_main_block">
						<div class="tf-hotel__title-wrap tf-tours-title-wrap">
							<a href="<?php echo get_the_permalink() . '?tour_destination=' . $tour_destination. '&adults=' . $adults . '&children=' . $children . '&infant=' . $infant . '&check-in-date=' . $check_in_date . '&check-out-date=' . $check_out_date; ?>"><h3 class="tourfic_hotel-title"><?php the_title();?></h3></a>
						</div>
						<?php tourfic_map_link();?>
					</div>
					<?php tourfic_item_review_block();?>
				</div>
				<!-- Title area End -->
				<div class="tf-tour-desc">
					<p><?php the_excerpt(); ?></p>
				</div>

				<!-- Tour details start -->
				<div class="tf-tour-details">
					<div class="tf-tour-features">
					<?php if( $feature_meta ) : ?>
						<!-- Start features -->
						<div class="tf_features">
							<div class="tf_feature_list">
								<?php 
								foreach( $feature_meta as $feature ):
									$term_meta = get_term_meta( $feature, 'feature_meta', true );
									$term = get_term_by( 'id', $feature, 'tf_feature' );								
								?>
								<div class="single_feature_box">
									<?php if($term_meta['features_icon']): ?>
									<img src="<?php echo $term_meta['features_icon']; ?>" alt="">
									<?php endif; ?>
									<p class="feature_list_title"><?php echo $term->name;  ?></p>
								</div>
								<?php endforeach; ?>
							</div>
						</div>
						<?php endif; ?>
						<!-- End features -->
					</div>
					<div class="tf-tour-price">
						<?php echo tf_tours_price_html(); ?>
					</div>
				</div>
				<!-- Tour details end -->

				<div class="availability-btn-area">
					<a href="<?php echo get_the_permalink() . '?tour_destination=' . $tour_destination. '&adults=' . $adults . '&children=' . $children . '&infant=' . $infant . '&check-in-date=' . $check_in_date . '&check-out-date=' . $check_out_date; ?>" class="button tf_button"><?php esc_html_e( 'Book Now', 'tourfic' );?></a>
				</div>


			</div>
		</div>
	</div>

	<?php
}

// Review block
function tourfic_item_review_block() {

    $comments = get_comments( array( 'post_id' => get_the_ID() ) );
	$tour_destination = isset($_GET['tour_destination']) ? $_GET['tour_destination'] : "";
	$destination = isset($_GET['tour_destination']) ? $_GET['tour_destination'] : "";
	if('tourfic' == get_post_type()){
		$dest_slug_param = 'destination=' . $destination;
		$room = isset( $_GET['room'] ) ? $_GET['room'] : '';
		$infant = '';
	}else if('tf_tours' == get_post_type()){
		$dest_slug_param = 'tour_destination' . $tour_destination;
		$infant = isset($_GET['infant']) ? $_GET['infant'] : "0";
		$room = '';

	};
	$adults = isset($_GET['adults']) ? $_GET['adults'] : "1";
	$children = isset($_GET['children']) ? $_GET['children'] : "0";
	$check_in_date = isset($_GET['check-in-date']) ? $_GET['check-in-date'] : "";
	$check_out_date = isset($_GET['check-out-date']) ? $_GET['check-out-date'] : "";
    $tf_overall_rate = array();
    $tf_overall_rate['review'] = null;

    $tf_extr_html = '';

    foreach ( $comments as $comment ) {

        $tf_comment_meta = get_comment_meta( $comment->comment_ID, 'tf_comment_meta', true );

        if ( $tf_comment_meta ) {
            foreach ( $tf_comment_meta as $key => $value ) {
                $tf_overall_rate[$key][] = $value ? $value : "5";
            }
        } else {
            $tf_overall_rate['review'][] = "5";
            $tf_overall_rate['sleep'][] = "5";
            $tf_overall_rate['location'][] = "5";
            $tf_overall_rate['services'][] = "5";
            $tf_overall_rate['cleanliness'][] = "5";
            $tf_overall_rate['rooms'][] = "5";
        }
    }

    ?>
	<div class="tf_item_review_block">
		<div class="reviewFloater reviewFloaterBadge__container">
		    <div class="sr-review-score">
		        <a class="sr-review-score__link" href="<?php echo get_the_permalink() . '?'. $dest_slug_param . '&adults=' . $adults . '&children=' . $children . '&room=' . $room . '&check-in-date=' . $check_in_date . '&check-out-date=' . $check_out_date; ?>" target="_blank">
		            <div class="bui-review-score c-score bui-review-score--end">
		                <div class="bui-review-score__badge"> <?php _e( tourfic_avg_ratings( $tf_overall_rate['review'] ) );?> </div>
		                <div class="bui-review-score__content">
		                    <div class="bui-review-score__title"> <?php esc_html_e( 'Customer Rating', 'tourfic' );?> </div>
		                    <div class="bui-review-score__text">
							<?php
$comments_title = apply_filters(
        'tf_comment_form_title',
        sprintf(  // WPCS: XSS OK.
            /* translators: 1: number of comments */
            esc_html( _nx( 'Based on %1$s review', 'Based on %1$s reviews', get_comments_number(), 'comments title', 'tourfic' ) ),
            number_format_i18n( get_comments_number() )
        ) );

    echo esc_html( $comments_title );
    ?>
		                    </div>
		                </div>
		            </div>
		        </a>
		    </div>
		</div>
	</div>
	<?php
}

// Map Link
function tourfic_map_link() {
    if ( 'tf_tours' === get_post_type() ) {
        $meta = get_post_meta( get_the_ID(), 'tf_tours_option', true );
        $location = isset($meta['location']['address']) ? $meta['location']['address'] : '';
		$text_location = isset( $meta['text_location']) ? $meta['text_location'] : '';

		if( empty( $location ) ){
			$location = $text_location;
		}
    } else {
        $location = get_field( 'formatted_location' ) ? get_field( 'formatted_location' ) : null;

    }
    if ( !$location ) {
        return;
    }
    ?>
	<!-- Start map link -->
	<div class="tf_map-link">
		<?php echo tourfic_get_svg( 'checkin' ); ?> <a title="<?php echo esc_attr( $location ); ?>" href="https://www.google.com/maps/search/<?php _e( $location );?>" target="_blank"><?php echo esc_html( $location ); ?></a>

	</div>
	<!-- End map link -->
	<?php
}
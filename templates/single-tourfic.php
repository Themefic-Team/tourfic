<?php
/**
 * The template for displaying all single posts.
 *
 * @package storefront
 */

get_header('tourfic'); ?>
<?php while ( have_posts() ) : the_post(); ?>
<?php

// Get all rooms
$tf_room = get_field('tf_room') ? get_field('tf_room') : array();
$information = get_field('information') ? get_field('information') : null;
$additional_information = get_field('additional_information') ? get_field('additional_information') : null;
$share_text = get_the_title();
$share_link = esc_url( home_url("/?p=").get_the_ID() );
$location = get_field('formatted_location') ? get_field('formatted_location') : null;
$features = array();

$terms_and_conditions = get_post_meta( $post->ID, 'terms_and_conditions', true );
$tf_faqs = ( get_post_meta( $post->ID, 'tf_faqs', true ) ) ? get_post_meta( $post->ID, 'tf_faqs', true ) : array();

?>
<div class="tourfic-wrap default-style" data-fullwidth="true">
	<?php do_action( 'tf_before_container' ); ?>
	<div class="tf_container">
		<div class="tf_row">
			<div class="tf_content tf_content-full mb-15">

				<!-- Start title area -->
				<div class="tf_title-area">
					<h2 class="tf_title"><?php the_title(); ?></h2>
					<div class="tf_title-right">
						<div class="share-tour">
							<a href="#dropdown_share_center" class="share-toggle" data-toggle="true"><?php echo tourfic_get_svg('share'); ?></a>
							<div id="dropdown_share_center" class="share-tour-content">
 								<ul class="tf-dropdown__content">
									<li>
									    <a href="http://www.facebook.com/share.php?u=<?php _e( $share_link ); ?>" class="tf-dropdown__item" target="_blank">
									        <span class="tf-dropdown__item-content"><?php echo tourfic_get_svg('facebook'); ?> <?php esc_html_e( 'Share on Facebook', 'tourfic' ); ?></span>
									    </a>
									</li>
									<li>
									    <a href="http://twitter.com/share?text=<?php _e( $share_text ); ?>&url=<?php _e( $share_link ); ?>" class="tf-dropdown__item" target="_blank">
									        <span class="tf-dropdown__item-content"><?php echo tourfic_get_svg('twitter'); ?> <?php esc_html_e( 'Share on Twitter', 'tourfic' ); ?></span>
									    </a>
									</li>
									<li>
									    <div class="share_center_copy_form tf-dropdown__item" title="Share this link" aria-controls="share_link_button">
									        <label class="share_center_copy_label" for="share_link_input"><?php esc_html_e( 'Share this link', 'tourfic' ); ?></label>
									        <input type="text" id="share_link_input" class="share_center_url share_center_url_input" value="<?php _e( $share_link ); ?>" readonly>
									        <button id="share_link_button" class="share_center_copy_cta" tabindex="0" role="button">
									        	<span class="tf-button__text share_center_copy_message"><?php esc_html_e( 'Copy link', 'tourfic' ); ?></span>
									            <span class="tf-button__text share_center_copied_message"><?php esc_html_e( 'Copied!', 'tourfic' ); ?></span>
									        </button>
									    </div>
									</li>
								</ul>
							</div>
						</div>
						<div class="show-on-map">
							<a href="https://www.google.com/maps/search/<?php _e( $location ); ?>" target="_blank" class="tf_button btn-outline button"><?php esc_html_e( 'Show on map', 'tourfic' ); ?></a>
						</div>
						<div class="reserve-button">
							<a href="#rooms" class="tf_button button"><?php esc_html_e( 'Reserve', 'tourfic' ); ?></a>
						</div>
					</div>
				</div>
				<!-- End title area -->

				<!-- Start map link -->
				<div class="tf_map-link">
					<?php tourfic_map_link(); ?>
				</div>
				<!-- End map link -->
			</div>
		</div>

		<div class="tf_row">
			<!-- Start Content -->
			<div class="tf_content">

				<!-- Start gallery -->
				<div class="tf_gallery-wrap">
					<?php echo tourfic_gallery_slider('tf_gallery_ids'); ?>
				</div>
				<!-- End gallery-->

				<?php if( $additional_information ): ?>
				<!-- Start highlights content -->
				<div class="tf_contents highlights">
					<div class="highlights-title">
						<h4><?php esc_html_e( 'Highlights', 'tourfic' ); ?></h4>
					</div>
					<?php _e( $additional_information ); ?>
				</div>
				<!-- End highlights content -->
				<?php endif; ?>

				<!-- Start content -->
				<div class="tf_contents">
					<div class="listing-title">
						<h4><?php esc_html_e( 'Listing Description', 'tourfic' ); ?></h4>
					</div>
					<?php the_content(); ?>
				</div>
				<!-- End content -->

				<?php if( $features ) : ?>
				<!-- Start features -->
				<div class="tf_features">

				</div>
				<!-- End features -->
				<?php endif; ?>


				<?php if( $tf_room ) : ?>
				<!-- Start Room Type -->
				<div class="tf_room-type" id="rooms">
					<div class="listing-title">
						<h4><?php esc_html_e( 'Availability', 'tourfic' ); ?></h4>
					</div>
					<div class="tf_room-table">
						<table class="availability-table">
							<thead>
							    <tr>
							      <th class="room-type-td"><?php esc_html_e( 'Room Type', 'tourfic' ); ?></th>
							      <th class="pax-td"><?php esc_html_e( 'Pax', 'tourfic' ); ?></th>
							      <th class="total-price-td"><?php esc_html_e( 'Total Price', 'tourfic' ); ?></th>
							      <th class="select-rooms-td"><?php esc_html_e( 'Select Rooms', 'tourfic' ); ?></th>
							    </tr>
							</thead>
							<tbody>
							<!-- Start Single Room -->
							<?php foreach ( $tf_room as $key => $room_type ) : ?>
								<?php
								// Array to variable
								extract( $room_type );
								?>
								<tr>
							      <td class="room-type-td">
							      	<div class="tf-room-type">
										<div class="tf-room-title"><?php echo esc_html( $name ); ?></div>
										<div class="bed-facilities"><?php echo $short_desc; ?></div>

										<div class="room-features">
											<div class="tf-room-title"><?php esc_html_e( 'Room Features', 'tourfic' ); ?></div>
											<ul class="room-feature-list">
												<?php echo do_shortcode( $desc ); ?>
											</ul>
										</div>
									</div>
							      </td>
							      <td class="pax-td">
							      	<?php tourfic_pax( $pax ); ?>
							      </td>
							      <td class="total-price-td">
							      	<div class="tf-price-column">
										<?php echo tourfic_price_html($price, $sale_price); ?>
									</div>
							      </td>
							      <td class="select-rooms-td">
							      	<form class="tf-room" id="tf_room-id-<?php echo esc_attr( $key ); ?>">
								      	<div class="room-selection-wrap">
											<select name="room-selected" id="room-selected">
												<option>1</option>
												<option>2</option>
												<option>3</option>
												<option>4</option>
											</select>
										</div>
										<div class="room-submit-wrap">
											<input type="hidden" name="tour_id" value="<?php echo get_the_ID(); ?>">
											<input type="hidden" name="room_key" value="<?php echo esc_attr( $key ); ?>">
											<?php tourfic_room_booking_submit_button( 'I\'ll reserve' ); ?>
										</div>
										<div class="tf_desc"></div>
									</form>
							      </td>
							    </tr>
							<?php endforeach; ?>
							</tbody>
						</table>

						<?php //ppr( $add_room_type ); ?>
					</div>
				</div>
				<!-- End Room Type -->
				<?php endif; ?>

				<?php if( $tf_faqs ): ?>
					<!-- Start highlights content -->
					<div class="tf_contents faqs">
						<div class="highlights-title">
							<h4><?php esc_html_e( 'FAQs', 'tourfic' ); ?></h4>
						</div>

						<div class="tf-faqs">
						<?php foreach ( $tf_faqs as $key => $faq ): ?>
							<div class="tf-single-faq">
								<div class="faq-head">
									<i class="fa fa-question-circle-o" aria-hidden="true">
									</i> <?php esc_html_e( $faq['name'] ); ?>
									<span class="faq-indicator">
										<i class="fa fa-angle-up" aria-hidden="true"></i>
										<i class="fa fa-angle-down" aria-hidden="true"></i>
									</span>
								</div>
								<div class="faq-content"><?php _e( $faq['desc'] ); ?></div>
							</div>
						<?php endforeach; ?>
						</div>
					</div>
					<!-- End highlights content -->
				<?php endif; ?>

				<!-- Start Review Content -->
				<div class="tf_contents reviews">
					<div class="highlights-title">
						<h4><?php esc_html_e( 'Reviews', 'tourfic' ); ?></h4>
					</div>

					<?php
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
					?>
				</div>



				<!-- End Review Content -->


				<!-- Start TOC Content -->
				<div class="tf_toc-wrap">
					<div class="tf_toc-inner">
						<?php _e( $terms_and_conditions ); ?>
					</div>
				</div>
				<!-- End TOC Content -->


			</div>
			<!-- End Content -->

			<!-- Start Sidebar -->
			<div class="tf_sidebar">
				<?php tourfic_get_sidebar( 'single' ); ?>
			</div>
			<!-- End Sidebar -->
		</div>
	</div>
	<?php do_action( 'tf_after_container' ); ?>
</div>
<?php endwhile; ?>
<?php
get_footer('tourfic');
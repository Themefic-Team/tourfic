<?php
/**
 * Template for hotel single
 */

get_header();

while ( have_posts() ) : the_post(); 

/**
 * Assign all values to variable
 */

// get texonomies
$post_id   = get_the_ID();
// Location
$locations = get_the_terms( $post_id, 'hotel_location' );
if ($locations) {
	$first_location_id   = $locations[0]->term_id;
	$first_location_term = get_term( $first_location_id );
	$first_location_name = $locations[0]->name;
	$first_location_url  = get_term_link( $first_location_term );
}
// Features
$features  = get_the_terms( $post_id, 'hotel_feature' );

// get option meta
$meta = get_post_meta( get_the_ID(), 'tf_hotel', true );

$address  = !empty($meta['address']) ? $meta['address'] : '';
if ($address) {
	$location_name = $address;
} elseif ($first_location_name) {
	$location_name = $first_location_name;
}
$map      = !empty($meta['map']) ? $meta['map'] : '';
// Detail
$featured = !empty($meta['featured']) ? $meta['featured'] : '';
$logo     = !empty($meta['logo']) ? $meta['logo'] : '';
$gallery  = !empty($meta['gallery']) ? $meta['gallery'] : '';
if ($gallery) {
	// Comma seperated list to array
	$gallery_ids = explode( ',', $gallery );
}
$video    = !empty($meta['video']) ? $meta['video'] : '';
$rating   = !empty($meta['rating']) ? $meta['rating'] : '';
// Contact
$c_email = !empty($meta['c-email']) ? $meta['c-email'] : '';
$c_web   = !empty($meta['c-web']) ? $meta['c-web'] : '';
$c_phone = !empty($meta['c-phone']) ? $meta['c-phone'] : '';
$c_fax   = !empty($meta['c-fax']) ? $meta['c-fax'] : '';
// Check in/out
$full_day  = !empty($meta['full-day']) ? $meta['full-day'] : '';
$check_in  = !empty($meta['check-in']) ? $meta['check-in'] : '';
$check_out = !empty($meta['check-out']) ? $meta['check-out'] : '';
// Room
$rooms = !empty($meta['room']) ? $meta['room'] : '';
// FAQ
$faqs = !empty($meta['faq']) ? $meta['faq'] : '';
// Terms & condition
$tc = !empty($meta['tc']) ? $meta['tc'] : '';


$share_text = get_the_title();
$share_link = esc_url( home_url("/?p=").get_the_ID() );

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
						<?php if ($map["address"]) { ?>
						<div class="show-on-map">
							<a href="https://www.google.com/maps/search/<?php echo $map["address"]; ?>" target="_blank" class="tf_button btn-outline button"><?php esc_html_e( 'Show on map', 'tourfic' ); ?></a>
						</div>
						<?php } ?>
						<div class="reserve-button">
							<a href="#rooms" class="tf_button button"><?php esc_html_e( 'Reserve', 'tourfic' ); ?></a>
						</div>
					</div>
				</div>
				<!-- End title area -->

				<?php if ($locations) { ?>
				<!-- Start map link -->				
				<div class="tf_map-link">
					<a href="<?php echo $first_location_url; ?>"><i class="fas fa-map-marker-alt"></i> <?php echo $location_name; ?></a>
				</div>				
				<!-- End map link -->
				<?php } ?>
			</div>
		</div>

		<div class="tf_row">
			<!-- Start Content -->
			<div class="tf_content">

				<?php if ( ! empty( $gallery_ids ) ) { ?>
				<!-- Start gallery -->
				<div class="tf_gallery-wrap">				
					<div class="list-single-main-media fl-wrap" id="sec1">
						<div class="single-slider-wrapper fl-wrap">
							<div class="tf_slider-for fl-wrap">
								<?php foreach ( $gallery_ids as $attachment_id ) {
									echo '<div class="slick-slide-item">';
										echo '<a href="'.wp_get_attachment_url( $attachment_id, 'tf_gallery_thumb' ).'" class="slick-slide-item-link" >';

											echo wp_get_attachment_image( $attachment_id, 'tf_gallery_thumb' );
										echo '</a>';
									echo '</div>';
								} ?>
							</div>
							<div class="swiper-button-prev sw-btn"><i class="fa fa-angle-left"></i></div>
							<div class="swiper-button-next sw-btn"><i class="fa fa-angle-right"></i></div>
						</div>
						<div class="single-slider-wrapper fl-wrap">
							<div class="tf_slider-nav fl-wrap">
								<?php foreach ( (array) $gallery_ids as $attachment_id ) {
									echo '<div class="slick-slide-item">';
										echo wp_get_attachment_image( $attachment_id, 'thumbnail' );
									echo '</div>';
								} ?>

							</div>
						</div>
					</div>				
				</div>
				<!-- End gallery-->
				<?php } ?>

				<!-- Start description -->
				<div class="tf_contents">
					<div class="listing-title">
						<h4><?php esc_html_e( 'Description', 'tourfic' ); ?></h4>
					</div>
					<?php the_content(); ?>
				</div>
				<!-- End description -->

				<?php if( $features ) { ?>
				<!-- Start features -->
				<div class="tf_features">
					<div class="listing-title">
						<h4><?php esc_html_e( 'Features', 'tourfic' ); ?></h4>
					</div>

					<div class="tf_feature_list">
						<?php foreach($features as $feature) {
							$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'hotel_feature', true );
							if ($feature_meta['icon-type'] == 'fa') {
								$feature_icon = '<i class="' .$feature_meta['icon-fa']. '"></i>';
							} elseif ($feature_meta['icon-type'] == 'c') {
								$feature_icon = '<img src="' .$feature_meta['icon-c']["url"]. '" style="width: ' .$feature_meta['dimention']["width"]. 'px; height: ' .$feature_meta['dimention']["width"]. 'px;" />';
							} ?>

                        	<div class="single_feature_box">                        
                           		<?php echo $feature_icon; ?>
                        		<p class="feature_list_title"><?php echo $feature->name; ?></p>
                        	</div>
						<?php } ?>
					</div>
				</div>
				<!-- End features -->
				<?php } ?>

				<?php if( $rooms ) : ?>
				<!-- Start Room Type -->
				<div class="tf_room-type" id="rooms">
					<div class="listing-title">
						<h4><?php esc_html_e( 'Availability', 'tourfic' ); ?></h4>
					</div>

<?php 
//var_dump($rooms); 

foreach ($rooms as $room) {
	echo $room['num-room'];

	echo '<br>';
}
?>



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
							<?php foreach ($rooms as $room) { ?>
								<tr>
							      <td class="room-type-td">
							      	<div class="tf-room-type">
										<div class="tf-room-title"><?php echo esc_html( $room['title'] ); ?></div>
										<div class="bed-facilities"><?php echo $room['description']; ?></div>

										<div class="room-features">
											<div class="tf-room-title"><?php esc_html_e( 'Room Features', 'tourfic' ); ?></div>
											<ul class="room-feature-list">

												<?php foreach ($room['features'] as $feature) {

													$room_f_meta = get_term_meta( $feature, 'hotel_feature', true );

													if ($room_f_meta['icon-type'] == 'fa') {
														$room_feature_icon = '<i class="' .$room_f_meta['icon-fa']. '"></i>';
													} elseif ($room_f_meta['icon-type'] == 'c') {
														$room_feature_icon = '<img src="' .$room_f_meta['icon-c']["url"]. '" style="width: ' .$room_f_meta['dimention']["width"]. 'px; height: ' .$room_f_meta['dimention']["width"]. 'px;" />';
													}

													$room_term = get_term( $feature ); ?>
												<li class="tf-tooltip">
													<?php echo $room_feature_icon; ?>
													<div class="tf-top">
														<?php echo $room_term->name; ?>
														<i class="tool-i"></i>
													</div>
												</li>
												<?php } ?>
											</ul>
										</div>
									</div>
							      </td>
							      <td class="pax-td">
									<div class="tf_pax">
										<?php for ($i=0; $i < $room['person']; $i++) {
											echo '<i class="fa fa-user"></i>';
										} ?>
									</div>
							      </td>
							      <td class="total-price-td">
							      	<div class="tf-price-column">
									  <span class="tf-price"><?php echo wc_price( $room['price'] ); ?></span>
									  <div class="price-per-night"><?php esc_html_e( 'Price per night', 'tourfic' ); ?></div>
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
							<?php } ?>
							</tbody>
						</table>

						<?php //ppr( $add_room_type ); ?>
					</div>
				</div>
				<!-- End Room Type -->
				<?php endif; ?>

				<?php if( $faqs ) { ?>
					<!-- Start highlights content -->
					<div class="tf_contents faqs">
						<div class="highlights-title">
							<h4><?php esc_html_e( 'FAQs', 'tourfic' ); ?></h4>
						</div>

						<div class="tf-faqs">
						<?php foreach ( $faqs as $faq ): ?>
							<div class="tf-single-faq">
									<div class="tf-tours_faq_icon">
									<i class="far fa-question-circle" aria-hidden="true"></i>
									</div>
									<div class="tf-tours_single_faq_inner">
										<div class="faq-head">
											<?php esc_html_e( $faq['title'] ); ?>
											<span class="faq-indicator">
												<i class="fas fa-minus" aria-hidden="true"></i>
												<i class="fas fa-plus" aria-hidden="true"></i>
											</span>
										</div>
										<div class="faq-content"><?php _e( $faq['description'] ); ?></div>
									</div>

								</div>
						<?php endforeach; ?>
						</div>
					</div>
					<!-- End highlights content -->
				<?php } ?>

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

				<?php if ($tc) { ?>
				<!-- Start TOC Content -->
				<div class="tf_toc-wrap">
					<div class="tf_toc-inner">
						<?php echo $tc; ?>
					</div>
				</div>
				<!-- End TOC Content -->
				<?php } ?>

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
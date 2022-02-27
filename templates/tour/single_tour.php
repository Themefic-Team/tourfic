<?php
/**
 * The template for displaying all single posts.
 *
 * @package storefront
 */

get_header('tourfic');

while ( have_posts() ) : the_post();

$post_id   = get_the_ID();
$post_type = substr(get_post_type(), 3, -1);
$has_in_wishlist = tf_has_item_in_wishlist($post_id);

$meta = get_post_meta( $post_id,'tf_tours_option',true );

$location = isset( $meta['location']['address'] ) ? $meta['location']['address'] : '';
$text_location = isset( $meta['text_location']) ? $meta['text_location'] : '';
if( empty( $location ) ){
	$location = $text_location;
}
// Gallery
$gallery = $meta['tour_gallery'] ? $meta['tour_gallery'] : array();
if ($gallery) {
	$gallery_ids = explode( ',', $gallery );
}
$hero_title = !empty($meta['hero_title']) ? $meta['hero_title'] : '';
$additional_information = $meta['additional_information'] ? $meta['additional_information'] : null; 
// Informations
$tour_duration = !empty($meta['duration']) ? $meta['duration'] : '';
$info_type = !empty($meta['info_type']) ? $meta['info_type'] : '';
$group_size = !empty($meta['group_size']) ? $meta['group_size'] : '';
$language = !empty($meta['language']) ? $meta['language'] : '';

$min_days = !empty($meta['min_days']) ? $meta['min_days'] : null;
$external_booking = !empty($meta['external_booking']) ? $meta['external_booking'] : false;
$external_booking_link = !empty($meta['external_booking_link']) ? $meta['external_booking_link'] : null;
//$email = $meta['email'] ? $meta['email'] : null;
//$phone = $meta['phone'] ? $meta['phone'] : null;
//$website = $meta['website'] ? $meta['website'] : null;
//$fax = $meta['fax'] ? $meta['fax'] : null;
$faqs = $meta['faqs'] ? $meta['faqs'] : null;
$inc = $meta['inc'] ? $meta['inc'] : null;
$exc = $meta['exc'] ? $meta['exc'] : null;
$itineraries = $meta['itinerary'] ? $meta['itinerary'] : null;
//continuous tour
$information = get_field('information') ? get_field('information') : null;
$share_text = get_the_title();
$share_link = esc_url( home_url("/?p=").get_the_ID() );
$feature_meta = $meta['tour_feature'];

$terms_and_conditions = $meta['terms_conditions'];
$tf_faqs = ( get_post_meta( $post->ID, 'tf_faqs', true ) ) ? get_post_meta( $post->ID, 'tf_faqs', true ) : array();
$comments = get_comments( array( 'post_id' => get_the_ID() ) );
$tf_overall_rate = array();
$tf_overall_rate['review'] = null;

?>

<div class="tf-page-wrapper">
<?php do_action( 'tf_before_container' ); ?>
	<!-- Hero section Start -->
	<div class="tf-hero-wrapper">
		<div class="tf-container">
			<div class="tf-row">
				<div class="tf-hero-content-wrapper">
					<div class="tf-hero-top-content" style="background-image: url(<?php echo wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ); ?>);">
						<div class="tf-hero-top-content-inner">
							<i class="<?php echo $has_in_wishlist ? 'fas tf-text-red remove-wishlist' : 'far add-wishlist'  ?> fa-heart " data-nonce="<?php echo wp_create_nonce("wishlist-nonce") ?>" data-id="<?php echo $post_id ?>" data-type="<?php echo $post_type ?>"></i>
								<h1><?php echo esc_html__( $hero_title, 'tourfic' ); ?></h1>
								<!-- Start gallery -->
								<div class="tf-tours_gallery-wrap">
									<?php echo tf_single_tour_booking_form( $post->ID ); ?>
								</div>
								<!-- End gallery-->
						</div>
						<div class="tf-hero-bottom-area">					
							<?php 
							$tour_video = $meta['tour_video'] ? $meta['tour_video'] : '';
							if (defined( 'TF_PRO' ) && $tour_video){ 
							?>	
							<div class="tf-hero-btm-icon tf-tour-video" data-fancybox="tour-video" href="<?php echo $tour_video; ?>">	
								<i class="fab fa-youtube"></i>
								<span><?php _e( 'Video','tourfic' ); ?></span>
							</div>
							<?php } 
							// Gallery
							if ( !empty( $gallery_ids ) ) {
								foreach ($gallery_ids as $key => $gallery_item_id) {
									if ($key === array_key_first($gallery_ids)) {
										$image_url = wp_get_attachment_url( $gallery_item_id, 'full' ); ?>
										<div data-fancybox="tour-gallery" class="tf-hero-btm-icon tf-tour-gallery" data-src="<?php echo $image_url; ?>">
											<i class="far fa-images"></i> <span><?php _e( 'Gallery','tourfic' ); ?></span>
										</div>
									<?php } else {
										$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
										echo '<a data-fancybox="tour-gallery" href="' .$image_url. '" style="display:none;"></a>';
									}
								}
							}
							
							?>												
						</div>
					</div>
					<div class="tf-hero-bottom-content">
						<div class="tf-hero-bottom-left">
							<h4><?php the_title(); ?></h4>
							<div class="tf-hero-bottom-left-location">
								<i class="fas fa-map-marker"></i>
								<p><?php echo esc_html( $location ); ?></p>
							</div>
						</div>
						<div class="tf-hero-bottom-right">
							<div class="tf-hero-pricing">
								<span><?php echo esc_html__( 'Price','tourfic' ); ?>: <?php echo tf_tours_price_html();?></span>
							</div>
							<div class="tf-hero-rating">
								<div class="tf-hero-bcr-star">
									<i class="fas fa-star"></i>
								</div>
								<div class="tf-hero-bcr-num reviews">
									<span>5</span>
								</div>
							</div>
							<div class="tf-hero-review-count">
								<p><?php echo number_format_i18n( get_comments_number()); ?> <?php echo __( 'reviews', 'tourfic' );?></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Hero section end -->

	<?php if( $tour_duration ): ?>
	<!-- Square block section Start -->
	<div class="tf-square-block-wrapper">
		<div class="tf-container">
			<div class="tf-row">
				<div class="tf-square-block-content-wrapper">
					<div class="tf-single-square-block">
						<i class="far fa-clock"></i>
						<h5><?php echo __( 'Duration', 'tourfic' ); ?></h5>
						<p><?php echo esc_html__( $tour_duration,'tourfic' ) ?></p>
					</div>
					<div class="tf-single-square-block">
						<img src=<?php echo TF_ASSETS_URL . "img/globe.png" ?> alt="">
						<h5><?php echo __( 'Max People', 'tourfic' ); ?></h5>
						<p><?php echo esc_html__( $info_type,'tourfic' ) ?></p>
					</div>
					<div class="tf-single-square-block">
						<img src=<?php echo TF_ASSETS_URL . "img/users.svg" ?> alt="">
						<h5><?php echo __( 'Group Size', 'tourfic' ); ?></h5>
						<p><?php echo esc_html__( $group_size,'tourfic' ) ?></p>
					</div>
					<div class="tf-single-square-block">
						<img src=<?php echo TF_ASSETS_URL . "img/lang.png" ?> alt="">
						<h5><?php echo __( 'Language', 'tourfic' ); ?></h5>
						<p><?php echo esc_html__( $language,'tourfic' ) ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Square block section end -->

	<!-- Overview and Highlight section Start -->
	<div class="tf-overview-wrapper">
		<div class="tf-container">
			<div class="tf-row">
				<div class="tf-overview-content-wrapper">
					<div class="tf-overview-item">
						<div class="tf-overview-text">
							<h2><?php _e( 'Overview','tourfic' ); ?></h2>
							<?php  the_content(); ?>
							<!-- <a href="#">See Less <i class="fas fa-angle-up"></i></a> -->
						</div>
						<div class="tf-ohi-image">
							<img src="<?php echo wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ); ?>" alt="">
						</div>
					</div>
					<?php if( $additional_information ): ?>
					<div class="tf-overview-item">
						<div class="tf-overview-text">
							<h2><?php echo __( 'Highlights', 'tourfic' ); ?></h2>
							<?php _e( $additional_information, 'tourfic' ); ?>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<!-- Overview and Highlight section end -->
	<?php endif; ?>

	<?php if( $inc || $exc ): ?>
	<!-- Qoted List section Start -->
	<div class="tf-quoted-wrapper">
		<div class="tf-container">
			<div class="tf-row">
				<div class="tf-quoted-content-upper">
					<div class="tf-quoted-content-wrapper">
						<div class="tf-quoted-include">
							<h2><?php _e( 'Included','tourfic' ); ?></h2>
							<ul>
								<?php
									foreach( $inc as $key => $val ){
										echo "<li>". $val['inc'] ."</li>";
									}
								?>
							</ul>
						</div>
						<div class="tf-quoted-exclude">
							<h2><?php _e( 'Excluded','tourfic' ); ?></h2>
							<ul>
								<?php
									foreach( $exc as $key => $val ){
										echo "<li>". $val['exc'] ."</li>";
									}
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Qoted List section end -->
	<?php endif; ?>

	<?php if( $itineraries ): ?>
	<!-- Travel Itinerary section Start -->
	<div class="tf-travel-itinerary-wrapper">
		<div class="tf-container">
			<div class="tf-row">
				<div class="tf-travel-itinerary-content-wrapper">
					<h2><?php _e( "Travel Itinerary", 'tourfic' ); ?></h2>
					<div class="tf-travel-itinerary-items-wrapper">
						<?php foreach( $itineraries as $itinerary ){ ?>
						<div class="tf-travel-itinerary-item">
							<div class="tf-travel-time">
								<span><?php echo esc_html( $itinerary['time'] ) ?></span>
							</div>
							<div class="tf-travel-text">
								<h4><?php echo esc_html( $itinerary['title'] );  ?></h4>
								<div class="tf-travel-contetn">
									<div class="tf-travel-contetn-wrap">
										<img src="<?php echo esc_url( $itinerary['image'] );?>">
										<div class="tf-travel-desc">
											<p><?php echo esc_html( $itinerary['desc'] ); ?></p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Travel Itinerary section end -->
	<?php endif; ?>

	<?php if( $location ):  ?>
	<!-- Map section Start -->
	<div class="tf-map-wrapper">
		<div class="tf-container">
			<div class="tf-row">
				<div class="tf-map-content-wrapper">
				<iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $location ); ?>&output=embed" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
				</div>
			</div>
		</div>
	</div>
	<!-- Map section end -->
	<?php endif; ?>

	<?php if( $faqs ): ?>
	<!-- Accordion section Start -->
	<div class="tf-faq-wrapper">
		<div class="tf-container">
			<div class="tf-row">
				<div class="tf-faq-content-wrapper">
					<div class="tf-faq-sec-title">
						<h2><?php _e( "Frequently asked questions?", 'tourfic' ); ?></h2>
					</div>
					<div class="tf-faq-items-wrapper">
						<?php foreach ( $faqs as $key => $faq ): ?>
							<div class="tf-faq-item">
								<div class="tf-faq-icon">
									<img src=<?php echo TOURFIC_PLUGIN_URL . "assets/img/icon.png"?> alt="">
								</div>
								<div class="tf-faq-text">
									<div class="tf-faq-title">
										<h3><?php esc_html_e( $faq['title'] ); ?></h3>
									</div>
									<div class="tf-faq-desc">
										<p><?php _e( $faq['desc'] ); ?></p>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Accordion section end -->
	<?php endif; ?>

	<!-- Terms and Conditions -->
	<?php if ($terms_and_conditions) : ?>
		<div class="tf-faq-wrapper">
			<div class="tf-container">
				<div class="tf-row">
					<div class="tf-travel-itinerary-content-wrapper">
						<h2><?php _e("Terms and Conditions", 'tourfic'); ?></h2>
						<div class="tf-travel-itinerary-items-wrapper">
							<?php echo $terms_and_conditions; ?>
						</div>
					</div>

				</div>
			</div>
		</div>
	<?php endif; ?>
	<!-- Terms and Conditions -->
	
	<?php 
	$args = array(
		'post_type' => 'tf_tours',
		'post_status' => 'publish',
		'posts_per_page' => 8, 
		'orderby' => 'title', 
		'order' => 'ASC',
		'post__not_in' => array( get_the_ID() ),
	);
	$tours = new WP_Query( $args );
	if ($tours->have_posts()) {
	?>
 	<!-- tours suggestion section Start -->
 	<div class="tf-suggestion-wrapper">
		<div class="tf-container">
			<div class="tf-row">
				<div class="tf-suggestion-content-wrapper">
					<div class="tf-suggestion-sec-head">
						<h2><?php echo __( 'You might also like','tourfic' ) ?></h2>
						<p><?php echo __('Travel is my life. Since 1999, I’ve been traveling around the world nonstop.
						If you also love travel, you’re in the right place!
						','tourfic') ?></p>
					</div>
					<div class="tf-suggestion-items-wrapper">
						<?php
							while($tours->have_posts() ) {
								$tours->the_post();
								$post_id   = get_the_ID();
								$destinations = get_the_terms( $post_id, 'tour_destination' );
								$first_destination_name = $destinations[0]->name;

								$related_comments = get_comments( array( 'post_id' => $post_id ) );								
						?>
						<div class="tf-suggestion-item" style="background-image: url(<?php echo get_the_post_thumbnail_url(get_the_ID(),'full') ?>);">
							<div class="tf-suggestion-content">
								<div class="tf-suggestion-desc">
									<h3>
										<a href="<?php the_permalink() ?>"><?php the_title() ?></a>
										<span><?php echo $first_destination_name; ?></span>
									</h3>
								</div>
								<div class="tf-suggestion-rating">

								<?php 
									if ($related_comments) {
										foreach ($related_comments as $related_comment) {
											$related_comment_meta = get_comment_meta( $related_comment->comment_ID, 'tf_comment_meta', true );
											if ( $related_comment_meta ) {
												foreach ( $related_comment_meta as $key => $value ) {
													$related_overall_rate[$key][] = $value ? $value : "5";
												}
											} else {
												$related_overall_rate['review'][] = "5";
												$related_overall_rate['sleep'][] = "5";
												$related_overall_rate['location'][] = "5";
												$related_overall_rate['services'][] = "5";
												$related_overall_rate['cleanliness'][] = "5";
												$related_overall_rate['rooms'][] = "5";
											}
											?>
											<div class="tf-suggestion-rating-star">
												<i class="fas fa-star"></i> <span style="color:#fff;"><?php echo tourfic_avg_ratings($related_overall_rate['review']); ?></span>
											</div>											
										<?php 
										}
									} else {
										echo '<div class="tf-suggestion-rating-star"><i class="fas fa-star"></i> <span style="color:#fff;">N/A</span></div>';
									}
								?>									
									<div class="tf-suggestion-price">
										<span><?php echo tf_tours_price_html();?></span>
									</div>
								</div>
							</div>
						</div>
						<?php }	?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- tours suggestion section end -->
	<?php }
	wp_reset_postdata();
	?>

	<!-- tours review section Start -->
	<div class="tf-review-wrapper">
		<div class="tf-container">
			<div class="tf-row">
				<div class="tf-review-content-wrapper">
					<?php if($comments): ?>
					<div class="tf-review-sec-head">
						<h2><?php echo esc_html__( 'Customer Reviews','tourfic' );?></h2>
						<p><?php echo esc_html__( 'Reviews given by our customers.','tourfic' );?></p>
					</div>
					<div class="tf-review-items-wrapper">
					<?php
						foreach ( $comments as $comment ) :
					
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
							?>
						<div class="tf-review-item">
							<div class="tf-review-rating">
								<i class="fas fa-star"></i>
								<span><?php _e( tourfic_avg_ratings($tf_overall_rate['review']) ); ?></span>
							</div>
							<div class="tf-review-avater">
								<img src="<?php echo get_avatar_url( $comment->user_id );?>" alt="">
							</div>
							<h3><?php echo get_the_author_meta( 'display_name',$comment->user_id ); ?> <span><?php echo get_the_author_meta( 'description',$comment->user_id ); ?></span></h3>
							<div class="tf-review-desc">
								<img src=<?php echo TOURFIC_PLUGIN_URL . "assets/img/quote3.png"; ?> alt="">
								<p><?php echo $comment->comment_content;?></p>
								<img src=<?php echo TOURFIC_PLUGIN_URL . "assets/img/quote4.png"; ?> alt="">
							</div>
						</div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
					<div class="tf-tours_submit_review">
					<?php
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
					?>				
					</div>		
					

				</div>
			</div>
		</div>
	</div>
	<!-- tours suggestion section end -->
	<?php do_action( 'tf_after_container' ); ?>
</div>

<?php 
endwhile;
?>
<?php
get_footer('tourfic');

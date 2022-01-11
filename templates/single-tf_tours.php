<?php
/**
 * The template for displaying all single posts.
 *
 * @package storefront
 */

get_header('tourfic'); ?>
<?php while ( have_posts() ) : the_post(); ?>
<?php
$meta = get_post_meta( get_the_ID(),'tf_tours_option',true );

$location = isset( $meta['location']['address'] ) ? $meta['location']['address'] : '';
$text_location = isset( $meta['text_location']) ? $meta['text_location'] : '';
if( empty( $location ) ){
	$location = $text_location;
}
$gallery = $meta['tour_gallery'] ? $meta['tour_gallery'] : array();
$additional_information = $meta['additional_information'] ? $meta['additional_information'] : null; 
$tour_duration = $meta['duration'] ? $meta['duration'] : null;
$group_size = $meta['group_size'] ? $meta['group_size'] : null;
$language = $meta['language'] ? $meta['language'] : null;
$min_days = $meta['min_days'] ? $meta['min_days'] : null;
$external_booking = !empty($meta['external_booking']) ? $meta['external_booking'] : false;
$external_booking_link = !empty($meta['external_booking_link']) ? $meta['external_booking_link'] : null;
$min_people = $meta['min_people'] ? $meta['min_people'] : null;
$min_people = $meta['max_people'] ? $meta['max_people'] : null;
$max_person = $meta['max_people'] ? $meta['max_people'] : null;
//$email = $meta['email'] ? $meta['email'] : null;
//$phone = $meta['phone'] ? $meta['phone'] : null;
//$website = $meta['website'] ? $meta['website'] : null;
//$fax = $meta['fax'] ? $meta['fax'] : null;
$faqs = $meta['faqs'] ? $meta['faqs'] : null;
$inc = $meta['inc'] ? $meta['inc'] : null;
$exc = $meta['exc'] ? $meta['exc'] : null;
$itineraries = $meta['itinerary'] ? $meta['itinerary'] : null;
$custom_availability = $meta['custom_availability'] ? $meta['custom_availability'] : null;
//continuous tour
$continuous_availability = $meta['continuous_availability'];
$continuous_availability = json_encode($continuous_availability);
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
<!-- Hero section Start -->
<div class="tf-page-wrapper">
<?php do_action( 'tf_before_container' ); ?>
	<div class="tf-hero-wrapper">
		<div class="tf-container">
			<div class="tf-row">
				<div class="tf-hero-content-wrapper">
					<div class="tf-hero-top-content">
						<div class="tf-hero-top-content-inner">
							<h1><?php the_title(); ?></h1>
							<!-- Start gallery -->
							<div class="tf-tours_gallery-wrap">
								<?php echo tourfic_gallery_slider( false, $post->ID, $gallery); ?>
								<?php echo tf_tours_booking_form( $post->ID ); ?>
							</div>
							<!-- End gallery-->
						</div>
					</div>
					<div class="tf-hero-bottom-content">
						<div class="tf-hero-bottom-left">
							<h4><?php the_title(); ?></h4>
							<div class="tf-hero-bottom-left-location">
								<i class="fas fa-map-marker"></i>
								<p><?php echo $location; ?></p>
							</div>
						</div>
						<div class="tf-hero-bottom-right">
							<div class="tf-hero-pricing">
								<span><?php echo esc_html__( 'Price','tourfic' ); ?>: <?php echo tf_tours_price_html();?></span>
							</div>
							<div class="tf-hero-rating">
								<div class="tf-hero-bcr-star">
									<i class="fas fa-star"></i>
									<i class="fas fa-star"></i>
									<i class="fas fa-star"></i>
									<i class="fas fa-star"></i>
								</div>
								<div class="tf-hero-bcr-num reviews">
									<span>4.9</span>
								</div>
							</div>
							<div class="tf-hero-review-count">
								<p>5 reviews</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php do_action( 'tf_after_container' ); ?>
</div>
<!-- Hero section end -->
<?php endwhile; ?>
<?php
get_footer('tourfic');

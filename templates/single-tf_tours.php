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

$location = $meta['location']['address'];
$gallery = $meta['tour_gallery'];
$additional_information = $meta['additional_information'] ? $meta['additional_information'] : null; 
$tour_duration = $meta['duration'] ? $meta['duration'] : null;
$group_size = $meta['group_size'] ? $meta['group_size'] : null;
$language = $meta['language'] ? $meta['language'] : null;
$min_days = $meta['min_days'] ? $meta['min_days'] : null;
$external_booking = $meta['external_booking'] ? $meta['external_booking'] : false;
$external_booking_link = $meta['external_booking_link'] ? $meta['external_booking_link'] : null;
$min_people = $meta['min_people'] ? $meta['min_people'] : null;
$min_people = $meta['max_people'] ? $meta['max_people'] : null;
$email = $meta['email'] ? $meta['email'] : null;
$phone = $meta['phone'] ? $meta['phone'] : null;
$website = $meta['website'] ? $meta['website'] : null;
$fax = $meta['fax'] ? $meta['fax'] : null;
$faqs = $meta['faqs'] ? $meta['faqs'] : null;
$inc = $meta['inc'] ? $meta['inc'] : null;
$exc = $meta['exc'] ? $meta['exc'] : null;
$itineraries = $meta['itinerary'] ? $meta['itinerary'] : null;
$pricing_rule = $meta['pricing'] ? $meta['pricing'] : null;
$tour_type = $meta['type'] ? $meta['type'] : null;
if( $pricing_rule == 'group'){
	$price = $meta['group_price'] ? $meta['group_price'] : null;
}else{
	$price = $meta['adult_price'] ? $meta['adult_price'] : null;
}
$discount_type = $meta['discount_type'] ? $meta['discount_type'] : null;
$discounted_price = $meta['discount_price'] ? $meta['discount_price'] : NULL;
if( $discount_type == 'percent' ){
	$sale_price = number_format( $price - (( $price / 100 ) * $discounted_price) ,1 ); 
}elseif( $discount_type == 'fixed'){
	$sale_price = number_format( ( $price - $discounted_price ),1 );
}


$information = get_field('information') ? get_field('information') : null;
$share_text = get_the_title();
$share_link = esc_url( home_url("/?p=").get_the_ID() );
$feature_meta = $meta['tour_feature'];

$terms_and_conditions = $meta['terms_conditions'];
$tf_faqs = ( get_post_meta( $post->ID, 'tf_faqs', true ) ) ? get_post_meta( $post->ID, 'tf_faqs', true ) : array();

?>
<div class="tourfic-wrap default-style" data-fullwidth="true">
	<?php do_action( 'tf_before_container' ); ?>
	<div class="tf_container">
		<div class="tf_row">
			<div class="tf_content tf_content-full mb-15">
				<!-- Start gallery -->
				<div class="tf_gallery-wrap">
					<?php echo tourfic_gallery_slider( false, $post_id, $gallery); ?>
				</div>
				<!-- End gallery-->
				<!-- Start title area -->
				<div class="tf_title-area">
					<div class="tf_title-left">
						<h2 class="tf_title"><?php the_title(); ?></h2>
						<!-- Start map link -->
						<div class="tf_map-link">
							<?php tourfic_map_link(); ?>
						</div>
					</div>
					<!-- End map link -->
					<div class="tf_title-right">
						<div class="tf_price">
							<span><?php echo __('Price','tourfic') ?></span>
							<?php echo tf_tours_price_html( $price, $sale_price,$discounted_price );?>
						</div>
						<div class="tf-ratings">
							<div class="star">
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star"></span>
								<span class="fa fa-star"></span>
							</div>
							<div class="reviews">
								<span>4.5</span>
							</div>
						</div>
					</div>
				</div>
				<!-- End title area -->	
			</div>
		</div>

		<div class="tf_row">
			<!-- Start Content -->
			<div class="tf_content">
				<!--Information section start-->
				<div class="tf_informations">
					<?php if( $tour_duration ): ?>
					<div class="item">
						<div class="icon">
							<i class="far fa-clock"></i>
						</div>
						<div class="info">
							<h4 class="title"><?php echo __( 'Duration', 'tourfic' ); ?></h4>
							<p><?php echo esc_html__( $tour_duration,'tourfic' ) ?></p>
						</div>
					</div>
					<?php endif;?>
					<?php if( $tour_type ): ?>
					<div class="item">
						<div class="icon">
							<i class="fas fa-globe"></i>
						</div>
						<div class="info">
							<h4 class="title"><?php echo __( 'Tour type', 'tourfic' ); ?></h4>
							<p><?php echo esc_html__( $tour_type,'tourfic' ) ?></p>
						</div>
					</div>
					<?php endif;?>
					<?php if( $group_size ): ?>
					<div class="item">
						<div class="icon">
							<i class="fas fa-users"></i>
						</div>
						<div class="info">
							<h4 class="title"><?php echo __( 'Group Size', 'tourfic' ); ?></h4>
							<p><?php echo esc_html__( $group_size,'tourfic' ) ?></p>
						</div>
					</div>
					<?php endif;?>
					<?php if( $language ): ?>
					<div class="item">
						<div class="icon">
							<i class="fas fa-language"></i>
						</div>
						<div class="info">
							<h4 class="title"><?php echo __( 'Language', 'tourfic' ); ?></h4>
							<p><?php echo esc_html__( $language,'tourfic' ) ?></p>
						</div>
					</div>
					<?php endif;?>
				</div>
				<!--Information section end-->

				<?php if( $additional_information ): ?>
				<!-- Start highlights content -->
				<div class="tf_contents highlights">
					<div class="highlights-title">
						<h4><?php esc_html_e( 'Highlights', 'tourfic' ); ?></h4>
					</div>
					<?php _e( $additional_information, 'tourfic' ); ?>
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

				<?php if( $itineraries ): ?>
				<!--Iternary start-->
				<div class="tf-itinerary">
					<div class="itinerary-title">
						<h4><?php echo __( 'Itinerary','tourfic' ); ?></h4>
					</div>
					<?php foreach( $itineraries as $itinerary ){ ?>
					<div class="tf-single-itinerary">
						<div class="itinerary-head">							
							<span class="time"><?php echo esc_html( $itinerary['time'] ) ?></span>
							<h5> <?php echo esc_html( $itinerary['title'] );  ?></h5>
							<div class="icon">
								<i class="fa fa-angle-down"></i>
							</div>
						</div>
						<div class="itinerary-content">
							<img src="<?php echo esc_url( $itinerary['image'] );?>" />
							<p><?php echo esc_html( $itinerary['desc'] ); ?></p>
						</div>
					</div>
					<?php } ?>
				</div>
				<!--Iternary end-->
				<?php endif; ?>
				
				<?php if( $feature_meta ) : ?>
				<!-- Start features -->
				<div class="tf_features">
					<div class="listing-title">
						<h4><?php esc_html_e( 'Features', 'tourfic' ); ?></h4>
					</div>

					<div class="tf_feature_list">
						<?php 
						foreach( $feature_meta as $feature ):
							$term_meta = get_term_meta( $feature, 'feature_meta', true );
							$term = get_term_by( 'id', $feature, 'tf_feature' );
						
						?>
                           <div class="single_feature_box">
								<img src="<?php echo $term_meta['fetures_icon']; ?>" alt="">
								<p class="feature_list_title"><?php echo $term->name;  ?></p>
                           </div>
						<?php endforeach; ?>

					</div>
				</div>
				<!-- End features -->
				<?php endif; ?>
				<?php if( $inc || $exc ): ?>			
				<!-- Start Include/Exlude  -->
				<div class="inc-exc-section">
					<div class="inc-exc-content">
						<div class="inc-exc-title">
							<h4><?php esc_html_e( 'Included/Excluded', 'tourfic' ); ?></h4>
						</div>
						<div class="tf-include">
							<ul class="items">
							<?php
								foreach( $inc as $key => $val ){
									echo "<li>". $val['inc'] ."</li>";
								}
							?>
							</ul>
						</div>
						<div class="tf-exclude">
							<ul class="items">
							<?php
								foreach( $exc as $key => $val ){
									echo "<li>". $val['exc'] ."</li>";
								}
							?>
							</ul>
						</div>
					</div>
				</div>
				<!-- End Include/Exlude  -->
				<?php endif;?>
				<!--Start tour map section-->
				<div class="tf_map_section">
					<div class="tf_map">
					<iframe src="https://www.google.com/maps/embed?pb=!1m28!1m12!1m3!1d1891144.1036137978!2d90.26962864671933!3d22.21575206911091!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m13!3e2!4m5!1s0x3755b8b087026b81%3A0x8fa563bbdd5904c2!2sDhaka%2C%20Bangladesh!3m2!1d23.810332!2d90.4125181!4m5!1s0x30ae2363dee2d61b%3A0xfb3463713589d312!2sSt.%20Martin&#39;s%20Island%2C%20Bangladesh!3m2!1d20.6237016!2d92.3233948!5e0!3m2!1sen!2sus!4v1637065617200!5m2!1sen!2sus" width="800" height="600" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
					</div>
				</div>
				<!--End tour map section-->

				<?php if( $faqs ): ?>
					<!-- Start highlights content -->
					<div class="tf_contents faqs">
						<div class="highlights-title">
							<h4><?php esc_html_e( 'FAQs', 'tourfic' ); ?></h4>
						</div>

						<div class="tf-faqs">
						<?php foreach ( $faqs as $key => $faq ): ?>
							<div class="tf-single-faq">
								<div class="faq-head">
									<i class="fa fa-question-circle-o" aria-hidden="true">
									</i> <?php esc_html_e( $faq['title'] ); ?>
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
						<?php _e( $terms_and_conditions,'tourfic' ); ?>
					</div>
				</div>
				<!-- End TOC Content -->


			</div>
			<!-- End Content -->
		</div>
	</div>
	<?php do_action( 'tf_after_container' ); ?>
</div>
<?php endwhile; ?>
<?php
get_footer('tourfic');
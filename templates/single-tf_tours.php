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

$location = $meta['location']['address'] ? $meta['location']['address'] : '';
$gallery = $meta['tour_gallery'] ? $meta['tour_gallery'] : array();
$additional_information = $meta['additional_information'] ? $meta['additional_information'] : null; 
$tour_duration = $meta['duration'] ? $meta['duration'] : null;
$group_size = $meta['group_size'] ? $meta['group_size'] : null;
$language = $meta['language'] ? $meta['language'] : null;
$min_days = $meta['min_days'] ? $meta['min_days'] : null;
$external_booking = $meta['external_booking'] ? $meta['external_booking'] : false;
$external_booking_link = $meta['external_booking_link'] ? $meta['external_booking_link'] : null;
$min_people = $meta['min_people'] ? $meta['min_people'] : null;
$min_people = $meta['max_people'] ? $meta['max_people'] : null;
$max_person = $meta['max_people'] ? $meta['max_people'] : null;
$email = $meta['email'] ? $meta['email'] : null;
$phone = $meta['phone'] ? $meta['phone'] : null;
$website = $meta['website'] ? $meta['website'] : null;
$fax = $meta['fax'] ? $meta['fax'] : null;
$faqs = $meta['faqs'] ? $meta['faqs'] : null;
$inc = $meta['inc'] ? $meta['inc'] : null;
$exc = $meta['exc'] ? $meta['exc'] : null;
$itineraries = $meta['itinerary'] ? $meta['itinerary'] : null;

//continuous tour
$continuous_availability = $meta['continuous_availability'];
$continuous_availability = json_encode($continuous_availability);
$information = get_field('information') ? get_field('information') : null;
$share_text = get_the_title();
$share_link = esc_url( home_url("/?p=").get_the_ID() );
$feature_meta = $meta['tour_feature'];

$terms_and_conditions = $meta['terms_conditions'];
$tf_faqs = ( get_post_meta( $post->ID, 'tf_faqs', true ) ) ? get_post_meta( $post->ID, 'tf_faqs', true ) : array();

?>
<div class="tourfic-wrap tf_tours-single-layout default-style" data-fullwidth="true" data-continuous-array='<?php echo $continuous_availability;?>'>
	<?php do_action( 'tf_before_container' ); ?>
	<div class="tf_container">
		<div class="tf_row">
			<div class="tf-tours-content tf_content-full mb-15">
				<!-- Start gallery -->
				<div class="tf-tours_gallery-wrap">
					<?php echo tourfic_gallery_slider( false, $post->ID, $gallery); ?>
					<?php echo tf_tours_booking_form( $post->ID ); ?>
				</div>
				<!-- End gallery-->
				<!-- Start title area -->
				<div class="tf_tours-title-area">
					<div class="tf_tours-title-left">
						<h2 class="tf_tours-title "><?php the_title(); ?></h2>
						<!-- Start map link -->
						<div class="tf-tours-map-link">
							<?php tourfic_map_link(); ?>
						</div>
					</div>
					<!-- End map link -->
					<div class="tf-tours-title-right">
						<div class="tf-tours-price">
							<span><?php echo __('Price','tourfic') ?></span>
							<?php echo tf_tours_price_html();?>
						</div>
						<div class="tf-tours-ratings">
							<div class="star">
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star checked"></span>
								<span class="fa fa-star"></span>
								<span class="fa fa-star"></span>
							</div>
							<div class="reviews">
								<span>5</span>
							</div>
						</div>
					</div>
				</div>
				<!-- End title area -->	
			</div>
		</div>
	</div>

	<!--Information section start-->
	<div class="tf_container">
		<div class="tf_row">
			<div class="tf-tours-content">
				<div class="tf-tours-informations">
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
					<?php if( $max_person ): ?>
					<div class="item">
						<div class="icon">
							<i class="fas fa-globe"></i>
						</div>
						<div class="info">
							<h4 class="title"><?php echo __( 'Max People', 'tourfic' ); ?></h4>
							<p><?php echo esc_html__( $max_person,'tourfic' ) ?></p>
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
				<?php // echo tf_tours_booking_form($post->ID);?>
			</div>
		</div>
	</div>
   <!-- Information section end -->

	<!--Start Description section-->
	<div class="tf-tours_content_area_wrapper tf-tours_section">
		<div class="tf_container">
			<div class="tf_row">
				<div class="tf-tours-content">
				<div class="tf_tours-content_wrapper">
					<div class="tf_tours-content_wrapper_inner">
						<?php if( $additional_information ): ?>
						<!-- Start highlights content -->
						<div class="tf_contents tf-tours-highlights">
							<div class="highlights-title">
								<h4 class="tf-tours_section_title"><?php esc_html_e( 'Highlights', 'tourfic' ); ?></h4>
							</div>
							<?php _e( $additional_information, 'tourfic' ); ?>
						</div>
						<!-- End highlights content -->
						<?php endif; ?>

						<!-- Start content -->
						<div class="tf_contents">
							<div class="tf-tours-listing-title">
								<h4 class="tf-tours_section_title"><?php esc_html_e( 'Listing Description', 'tourfic' ); ?></h4>
							</div>
							<?php the_content(); ?>
						</div>
						<!-- End content -->
					</div>
					</div>
				</div>
			</div>
		</div>    
	</div>    
    <!--End Decription/highlight section-->

	<!--Start features section-->
	<div class="tf_container">
		<div class="tf_row">
			<div class="tf-tours-content ">
			<?php if( $feature_meta ) : ?>
				<div class="tf_features">
					<div class="listing-title">
						<h4 class="tf-tours_section_title"><?php esc_html_e( 'Features', 'tourfic' ); ?></h4>
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
				<?php endif; ?>
			</div>
		</div>
	</div>    
    <!--End features section-->


	<!--Start Include Exclude section-->
	<div class="tf-tours_in_ex_area_wrapper tf-tours_section">
		<div class="tf_container">
			<div class="tf_row">
				<div class="tf-tours-content">
				<?php if( $inc || $exc ): ?>			
					<div class="inc-exc-section">
						<div class="inc-exc-content">
							<div class="tf-include">
								<div class="inc-title">
									<h4 class="tf-tours_section_title"><?php esc_html_e( 'Included', 'tourfic' ); ?></h4>
								</div>
								<ul class="items">
								<?php
									foreach( $inc as $key => $val ){
										echo "<li>". $val['inc'] ."</li>";
									}
								?>
								</ul>
							</div>
							<div class="tf-exclude">
								<div class="inc-title">
									<h4 class="tf-tours_section_title"><?php esc_html_e( 'Excluded', 'tourfic' ); ?></h4>
								</div>
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
					<?php endif;?>
				</div>
			</div>
		</div>    
	</div>    
    <!--End Include Exclude section-->

	<!--Start Iternary section-->
	<div class="tf-tours_itinerary_area_wrapper tf-tours_section">
		<div class="tf_container">
			<div class="tf_row">
				<div class="tf-tours-content">
				<?php if( $itineraries ): ?>
					<div class="tf-itinerary">
						<div class="itinerary-title">
							<h4 class="tf-tours_section_title"><?php echo __( 'Travel Itinerary','tourfic' ); ?></h4>
						</div>
						<?php foreach( $itineraries as $itinerary ){ ?>
						<div class="tf-single-itinerary">
							<div class="tf-tours_itinerary_time">
								<span class="time"><?php echo esc_html( $itinerary['time'] ) ?></span>
							</div>
							<div class="tf-single-itinerary_inner">
								<div class="itinerary-head">							
									
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
						</div>
						<?php } ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>    
	</div>    
    <!--End Iternary section-->

	<!--Start tour map section-->
	<div class="tf-tours_map_area_wrapper tf-tours_section">
		<div class="tf_container">
			<div class="tf_row">
				<div class="tf-tours-content">
					<div class="tf_map_section">
						<div class="tf_map">
						<iframe src="https://maps.google.com/maps?q=<?php echo esc_attr($location); ?>&hl=es&z=14&amp;output=embed" width="800" height="600" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
						</div>
					</div>
				</div>
			</div>
		</div> 
	</div>
    <!--End tour map section-->

	<!--Start tour Faq section-->
	<div class="tf-tours_faq_area_wrapper tf-tours_section">
		<div class="tf_container">
			<div class="tf_row">
				<!-- Start Content -->
				<div class="tf-tours-content">
				<?php if( $faqs ): ?>
						<div class="faqs tf-tours_faq ">
							<div class="highlights-title">
								<h4 class="tf-tours_section_title"><?php esc_html_e( 'Frequently asked questions?', 'tourfic' ); ?></h4>
							</div>
							<div class="tf-faqs">
							<?php foreach ( $faqs as $key => $faq ): ?>
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
										<div class="faq-content"><?php _e( $faq['desc'] ); ?></div>
									</div>

								</div>
							<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>    
    </div>	
    <!--End tour FAQ section-->

	<!--Start tour recommendation section-->
	<div class="tf-tours_recomendation_area_wrapper tf-tours_section">
		<div class="tf_container">
			<div class="tf_row">
				<!-- Start Content -->
				<div class="tf-tours-content">
					<!-- Start tourbox Content -->
					<div class="tf-tourbox-section">
						<div class="tf-tourbox-title">
							<h4 class="tf-tours_section_title"><?php echo __( 'You might also like','tourfic' ) ?></h4>
							<p><?php echo __('Travel is my life. Since 1999, I’ve been traveling around the world nonstop.
							If you also love travel, you’re in the right place!
							','tourfic') ?></p>						
						</div>
						<div class="tf-tourbox">
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
								while($tours->have_posts() ) : $tours->the_post();

							?>
							<div class="single-tourbox" style="background-image:url(<?php echo get_the_post_thumbnail_url(get_the_ID(),'full') ?>)">
								<div class="tf-tourbox-info">
										<div class="left-info">
											<h3 class="tf-tour-title"><?php the_title(); ?></h3>
											<p class="tf-location"><?php echo __( $location,'tourfic' ) ?></p>
										</div>
										<div class="right-info">
											<div class="tf-rating">
												<div class="star">
													<span class="fa fa-star checked"></span>
													<span class="fa fa-star checked"></span>
													<span class="fa fa-star checked"></span>
													<span class="fa fa-star"></span>
													<span class="fa fa-star"></span>
												</div>
											</div>
											<div class="tf-price">
												<span><?php echo tf_tours_price_html();?></span>
											</div>
										</div>
								</div>
							</div>
							<?php 
								endwhile;
								wp_reset_postdata(); 
							?>
						</div>
					</div>
					<!-- end tourbox Content -->
				</div>
			</div>
		</div> 
	</div>
    <!--End tour recommendation section-->
	<!-- Custom review section start j-->
	<div class="tf_container">
	<div class="tf-custom-review-section-wrapper">
		<div class="tf-custom-review-title-area">
			<h2>Customer Review</h2>
			<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
		</div>
		<?php

			$comments = get_comments( array( 'post_id' => get_the_ID() ) );
			$tf_overall_rate = array();
			$tf_overall_rate['review'] = null;
		?>
			<div class="tf-custom-review-slider-area">
		<?php
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
				?>
					<div class="tf-single-custom-review">
						<div class="tf-cr-reting">
							<span><?php _e( tourfic_avg_ratings($tf_overall_rate['review']) ); ?></span>
							<i class="fas fa-star"></i>
						</div>
						<div class="tf-cr-avater-image">
							<img src="<?php echo get_avatar_url( $comment->user_id );?>">
						</div>
						<div class="tf-cr-avater-meta">
							<h4><?php echo get_the_author_meta( 'display_name',$comment->user_id ); ?> <span><?php echo get_the_author_meta( 'description',$comment->user_id ); ?></span></h4>
						</div>
						<div class="tf-cr-desc">
							<img src="<?php echo plugin_dir_url( __DIR__ ) . 'assets/img/quote.png';?>">
							<p><?php echo $comment->comment_content;?> </p>
							<img src="<?php echo plugin_dir_url( __DIR__ ) . 'assets/img/quote.png';?>">
						</div>
					</div>
					
				
		<?php
			}
		?>
		</div>
	</div>
	</div>
	<!-- Custom review section end j-->

	<!--Start TOC section-->
	<div class="tf-tours_toc_area_wrapper tf-tours_section">
		<div class="tf_container">
			<div class="tf_row">
				<!-- Start Content -->
				<div class="tf-tours-content">
					<!-- Start TOC Content -->
					<div class="tf-tours-toc-wrap">
						<div class="tf-tours-toc-inner">
							<?php _e( $terms_and_conditions,'tourfic' ); ?>
						</div>
					</div>
					<!-- End TOC Content -->
				</div>
			</div>
		</div> 
	</div>
    <!--End TOC section-->

	<!--Start review section-->
	<div class="tf-tours_toc_area_wrapper tf-tours_section">
		<div class="tf_container">
			<div class="tf_row">
				<div class="tf-tours-content">
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
				</div>
			</div>
		</div>    
	</div>
  <!--End review section-->


	<!--Start review section-->
	<div class="tf_container">
		<div class="tf_row">
			<div class="tf-tours-content">
				<div class="tf-tours-toc-wrap">
					<div class="tf-tours-toc-inner">
						<?php _e( $terms_and_conditions,'tourfic' ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>    
    <!--End review section-->


	<?php do_action( 'tf_after_container' ); ?>
</div>
<?php endwhile; ?>
<?php
get_footer('tourfic');
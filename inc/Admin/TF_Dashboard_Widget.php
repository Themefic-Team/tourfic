<?php
namespace Tourfic\Admin;

if(!defined('ABSPATH')) exit;

use Tourfic\Classes\Helper;

/**
 * Tourfic Dashboard Widget
 * @author Jewel Hossain
 * @since 1.21.3
 */
class TF_Dashboard_Widget {

    use \Tourfic\Traits\Singleton;

    public function __construct() {

        add_action('wp_dashboard_setup', [$this,'register_tourfic_dashboard_widget']);
        
    }

    public function register_tourfic_dashboard_widget() {

		wp_add_dashboard_widget('tourfic_dashboard_widget', esc_html__( 'Tourfic Overview', 'tourfic' ), [$this,'tourfic_dashboard_widget'], null, null, 'normal', 'high');

	}

    public static function tf_all_services_disabled( $disabled ) {

        $services = [ 'tour', 'apartment', 'hotel', 'carrentals' ];
        return is_array( $disabled ) && empty( array_diff( $services, $disabled ) );

    }

    public function tourfic_dashboard_widget() {
	    ?>
		<div class="tourfic-widget">

			<?php if ( ! self::tf_all_services_disabled( Helper::tfopt( 'disable-services' ) ) )  { ?>

			<!-- Listings Overview -->
			<div class="tourfic-section-title"><?php echo esc_html__('Listings Overview', 'tourfic'); ?></div>
			<div class="tourfic-grid">
				<?php if ( Helper::tfopt( 'disable-services' ) && in_array( 'tour', Helper::tfopt( 'disable-services' ) ) ) { ?>
				<?php }else { ?>
				<div class="tourfic-card">
					<div class="tourfic-card-title">
						<?php echo esc_html__('Tours', 'tourfic'); ?>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=tf_tours') ); ?>">
							<span aria-hidden="true" class="dashicons dashicons-external"></span>
						</a>
					</div>
					<div class="tourfic-card-value">
						<?php
							$tf_total_tours = array(
								'post_type'      => 'tf_tours',
								'post_status'    => 'publish',
								'posts_per_page' => - 1
							);
							echo count( get_posts ($tf_total_tours ));
						?>
					</div>
				</div>
				<?php } ?>
				<?php if ( Helper::tfopt( 'disable-services' ) && in_array( 'hotel', Helper::tfopt( 'disable-services' ) ) ) { ?>
				<?php }else { ?>
				<div class="tourfic-card">
					<div class="tourfic-card-title">
						<?php echo esc_html__('Hotels', 'tourfic'); ?>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=tf_hotel') ); ?>">
							<span aria-hidden="true" class="dashicons dashicons-external"></span>
						</a>
					</div>
					<div class="tourfic-card-value">
						<?php
							$tf_total_hotels = array(
								'post_type'      => 'tf_hotel',
								'post_status'    => 'publish',
								'posts_per_page' => - 1
							);
							echo count( get_posts ($tf_total_hotels ) );
						?>
					</div>
				</div>
				<?php } ?>
				<?php if ( Helper::tfopt( 'disable-services' ) && in_array( 'apartment', Helper::tfopt( 'disable-services' ) ) ) { ?>
				<?php }else { ?>
				<div class="tourfic-card">
					<div class="tourfic-card-title">
						<?php echo esc_html__('Apartments', 'tourfic'); ?>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=tf_apartment') ); ?>">
							<span aria-hidden="true" class="dashicons dashicons-external"></span>
						</a>
					</div>
					<div class="tourfic-card-value">
						<?php
							$tf_total_apartments = array(
								'post_type'      => 'tf_apartment',
								'post_status'    => 'publish',
								'posts_per_page' => - 1
							);
							echo count( get_posts ($tf_total_apartments ) );
						?>
					</div>
				</div>
				<?php } ?>

				<?php if ( Helper::tfopt( 'disable-services' ) && in_array( 'carrentals', Helper::tfopt( 'disable-services' ) ) ) { ?>
				<?php }else { ?>
				<div class="tourfic-card">
					<div class="tourfic-card-title">
						<?php echo esc_html__('Car Rentals', 'tourfic'); ?>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=tf_carrental') ); ?>">
							<span aria-hidden="true" class="dashicons dashicons-external"></span>
						</a>
					</div>
					<div class="tourfic-card-value">
						<?php
							$tf_total_cars = array(
								'post_type'      => 'tf_carrental',
								'post_status'    => 'publish',
								'posts_per_page' => - 1
							);
							echo count( get_posts ($tf_total_cars ) );
						?>
					</div>
				</div>
				<?php } ?>
			</div>
			<?php } ?>

			<!-- Booking Status -->
			<div class="tourfic-section-title"><?php esc_html_e( 'Booking Status', 'tourfic' ); ?></div>
			<div class="tourfic-revenue">
				<div class="tourfic-card">
					<div class="tourfic-card-title"><?php esc_html_e( 'Total Bookings', 'tourfic' ); ?></div>
					<div class="tourfic-card-value">
						<?php
							if ( Helper::tf_is_woo_active() ) {
								
								$tf_order_query_orders = wc_get_orders( array(
										'limit'  => - 1,
										'type'   => 'shop_order',
										'status' => array( 'wc-completed' ),
									)
								);
								echo count( $tf_order_query_orders );
							} else {
								echo '0';
							}
						?>
					</div>
				</div>
				<div class="tourfic-card">
					<div class="tourfic-card-title"><?php esc_html_e( 'Order in Progress', 'tourfic' ); ?></div>
					<div class="tourfic-card-value">
						<?php
							if ( Helper::tf_is_woo_active() ) {
								
								$tf_order_query_orders = wc_get_orders( array(
										'limit'  => - 1,
										'type'   => 'shop_order',
										'status' => array( 'wc-processing' ),
									)
								);
								echo count( $tf_order_query_orders );
							} else {
								echo '0';
							}
						?>
					</div>
				</div>
			</div>

			<!-- Blog Section -->
			<div class="tourfic-section-title"><?php esc_html_e( 'Latest Blog Posts from Tourfic', 'tourfic' ); ?></div>
			<ul class="tourfic-blog-list">
				<li>
					<span class="tourfic-badge"><?php esc_html_e( 'NEW', 'tourfic' ); ?></span>
					<a href="<?php echo esc_url( 'https://tourfic.com/tourfic-2-21-4-update/' ); ?>" target="_blank"><?php esc_html_e( 'Tourfic 2.21.4 Update: More Flexible Payments, Better Listings, and a Smoother Experience', 'tourfic' ); ?></a>
				</li>
				<li>
					<a href="<?php echo esc_url( 'https://tourfic.com/tourfic-v2-21-2/' ); ?>" target="_blank"><?php esc_html_e( 'Tourfic v2.21.2: Introducing the Single Room Booking Template', 'tourfic' ); ?></a>
				</li>
				<li>
					<a href="<?php echo esc_url( 'https://tourfic.com/tourfic-single-room-booking/' ); ?>" target="_blank"><?php esc_html_e( 'Tourfic v2.21.0: Room Archive Support, Elementor Template Builder & Advanced Room Features', 'tourfic' ); ?></a>
				</li>
			</ul>

			<!-- Footer -->
			<div class="tourfic-footer">
				<a href="<?php echo esc_url( 'https://tourfic.com/blog/' ); ?>" target="_blank">
					<?php esc_html_e( 'Blog', 'tourfic' ); ?>
					<span aria-hidden="true" class="dashicons dashicons-external"></span>
				</a>
				<a href="<?php echo esc_url( 'https://portal.themefic.com/support/' ); ?>" target="_blank">
					<?php esc_html_e( 'Help', 'tourfic' ); ?>
					<span aria-hidden="true" class="dashicons dashicons-external"></span>
				</a>
				<a href="<?php echo esc_url( 'https://tourfic.com/pricing/' ); ?>" target="_blank" class="go-pro">
					<?php esc_html_e( 'Go Pro', 'tourfic' ); ?>
					<span aria-hidden="true" class="dashicons dashicons-external"></span>
				</a>
			</div>

		</div>
	<?php

    }


}



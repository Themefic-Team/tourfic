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

        add_action('wp_dashboard_setup', [$this,'register_torufic_dashboard_widget']);
        
    }

    public function register_torufic_dashboard_widget() {

		wp_add_dashboard_widget('torufic_dashboard_widget', 'Torufic Overview', [$this,'torufic_dashboard_widget'], null, null, 'normal', 'high');

	}

    public static function tf_all_services_disabled( $disabled ) {

        $services = [ 'tour', 'apartment', 'hotel', 'carrentals' ];
        return is_array( $disabled ) && empty( array_diff( $services, $disabled ) );

    }

    public function torufic_dashboard_widget() {
	    ?>
		<style>
			.torufic-widget {
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
			}
			.torufic-section-title {
				font-weight: 500;
				margin: 15px 0 10px;
				color: #1d2327;
				font-size: 16px;
			}
			.torufic-grid {
				display: grid;
				grid-template-columns: 1fr 1fr;
				gap: 10px;
			}
			.torufic-card {
				background: #f6f7f7;
				border: 1px solid #dcdcde;
				border-radius: 6px;
				padding: 12px;
			}
			.torufic-card-title {
				font-size: 16px;
				color: #50575e;
				display: flex;
				align-items: center;
				gap: 5px;
			}
			.torufic-card-title a {
                text-decoration: navajowhite;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
			.torufic-card-title a span{
				font-size: 17px;
                line-height: 20px;
			}
			.torufic-card-value {
				font-size: 20px;
				font-weight: 600;
				margin-top: 5px;
				color: #1d2327;
			}
			.torufic-revenue {
				display: grid;
				grid-template-columns: 1fr 1fr;
				gap: 10px;
			}
			.torufic-footer {
				margin-top: 15px;
				padding-top: 10px;
				border-top: 1px solid #dcdcde;
				display: flex;
				gap: 15px;
				font-size: 13px;
			}
			.torufic-footer span {
				font-size: 17px;
				vertical-align: -4px;
			}
			.torufic-footer a {
				text-decoration: none;
				color: #2271b1;
			}
			.torufic-footer a.go-pro {
				color: #00a32a;
				font-weight: 500;
			}
			.torufic-blog-list {
				margin: 0;
				padding-left: 18px;
			}
			.torufic-blog-list li {
				margin-bottom: 6px;
				list-style: disc;
			}
			.torufic-badge {
				background: #6c5ce7;
				color: #fff;
				font-size: 10px;
				padding: 2px 6px;
				border-radius: 3px;
				margin-right: 5px;
			}
		</style>

		<div class="torufic-widget">

			<?php if ( ! self::tf_all_services_disabled( Helper::tfopt( 'disable-services' ) ) )  { ?>

			<!-- Listings Overview -->
			<div class="torufic-section-title"><?php echo esc_html__('Listings Overview', 'tourfic'); ?></div>
			<div class="torufic-grid">
				<?php if ( Helper::tfopt( 'disable-services' ) && in_array( 'tour', Helper::tfopt( 'disable-services' ) ) ) { ?>
				<?php }else { ?>
				<div class="torufic-card">
					<div class="torufic-card-title">
						<?php echo esc_html__('Tours', 'tourfic'); ?>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=tf_tours') ); ?>" target="_blank">
							<span aria-hidden="true" class="dashicons dashicons-external"></span>
						</a>
					</div>
					<div class="torufic-card-value">
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
				<div class="torufic-card">
					<div class="torufic-card-title">
						<?php echo esc_html__('Hotels', 'tourfic'); ?>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=tf_hotel') ); ?>" target="_blank">
							<span aria-hidden="true" class="dashicons dashicons-external"></span>
						</a>
					</div>
					<div class="torufic-card-value">
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
				<div class="torufic-card">
					<div class="torufic-card-title">
						<?php echo esc_html__('Apartments', 'tourfic'); ?>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=tf_apartment') ); ?>" target="_blank">
							<span aria-hidden="true" class="dashicons dashicons-external"></span>
						</a>
					</div>
					<div class="torufic-card-value">
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
				<div class="torufic-card">
					<div class="torufic-card-title">
						<?php echo esc_html__('Car Rentals', 'tourfic'); ?>
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=tf_carrental') ); ?>" target="_blank">
							<span aria-hidden="true" class="dashicons dashicons-external"></span>
						</a>
					</div>
					<div class="torufic-card-value">
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
			<div class="torufic-section-title"><?php esc_html_e( 'Booking Status', 'tourfic' ); ?></div>
			<div class="torufic-revenue">
				<div class="torufic-card">
					<div class="torufic-card-title"><?php esc_html_e( 'Total Bookings', 'tourfic' ); ?></div>
					<div class="torufic-card-value">
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
				<div class="torufic-card">
					<div class="torufic-card-title"><?php esc_html_e( 'Total Processing', 'tourfic' ); ?></div>
					<div class="torufic-card-value">
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
			<div class="torufic-section-title"><?php esc_html_e( 'Latest Blog Posts from Tourfic', 'tourfic' ); ?></div>
			<ul class="torufic-blog-list">
				<li>
					<span class="torufic-badge"><?php esc_html_e( 'NEW', 'tourfic' ); ?></span>
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
			<div class="torufic-footer">
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



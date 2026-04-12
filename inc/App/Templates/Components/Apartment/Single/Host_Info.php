<?php

namespace Tourfic\App\Templates\Components\Apartment\Single;

use Tourfic\Classes\Helper;
use \Tourfic\Classes\Apartment\Apartment;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single Host Info Component
 * Shared markup for Elementor and Bricks Host Info widgets
 */
class Host_Info {

	/**
	 * Static render method for Host Info component
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type (elementor or bricks)
	 *
	 * @return void
	 */
	public static function render( $settings = [], $builder = '' ) {
		$post_id   = get_the_ID();
		$post_type = get_post_type();

		if ( 'tf_apartment' !== $post_type ) {
			return;
		}

		$meta              = get_post_meta( $post_id, 'tf_apartment_opt', true );
		$post_author_id    = get_post_field( 'post_author', $post_id );
		$author_info       = get_userdata( $post_author_id );

		if ( empty( $author_info ) ) {
			return;
		}
		?>
		<div class="host-details">
			<div class="host-top">
				<img src="<?php echo esc_url( get_avatar_url( $post_author_id ) ); ?>" alt="">
				<div class="host-meta">
					<?php echo sprintf( '<h4>%s %s</h4>', esc_html__( 'Hosted by', 'tourfic' ), esc_html( $author_info->display_name ) ); ?>
					<?php echo sprintf( '<span class="tf-apartment-joined-text">%s <span>:</span> <span>%s</span></span>', esc_html__( 'Joined', 'tourfic' ), wp_kses_post( wp_date( 'F Y', strtotime( $author_info->user_registered ) ) ) ); ?>
					<?php Apartment::tf_apartment_host_rating( $post_author_id ) ?>
				</div>
			</div>
			<div class="host-bottom">
				<?php if ( ! empty( get_the_author_meta( 'description', $post_author_id ) ) ) : ?>
					<h5><?php echo esc_html__( "During Your Stay", 'tourfic' ); ?></h5>
					<p class="host-desc">
						<?php echo wp_kses_post( get_the_author_meta( 'description', $post_author_id ) ); ?>
					</p>
				<?php endif; ?>

				<ul>
					<?php
					if ( ! empty( get_the_author_meta( 'language', $post_author_id ) ) ) {
						echo sprintf( '<li>%s <span>%s</span></li>', esc_html__( 'Language: ', 'tourfic' ), wp_kses_post( get_the_author_meta( 'language', $post_author_id ) ) );
					}
					?>
				</ul>
				<a href="javaScript:void(0);" data-target="#tf-ask-modal" class="tf-modal-btn tf_btn tf_btn_white tf_btn_full"><i class="fas fa-phone"></i><?php esc_html_e( 'Contact Host', 'tourfic' ) ?></a>
			</div>
		</div>
		<?php
	}
}

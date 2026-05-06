<?php

namespace Tourfic\App\Templates\Components\Car_Rental\Single;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single Car Benefits Component
 * Shared markup for Elementor and Bricks Car Benefits widgets
 */
class Car_Benefits {

	/**
	 * Static render method for Car Benefits component
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type (elementor or bricks)
	 *
	 * @return void
	 */
	public static function render( $settings = [], $builder = '' ) {
		$post_id       = get_the_ID();
		$post_type     = get_post_type();
		$wrapper_open  = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';

		if ( 'tf_carrental' !== $post_type ) {
			return;
		}

		$meta = get_post_meta( $post_id, 'tf_carrental_opt', true );

		$benefits_status    = ! empty( $meta['benefits_section'] ) ? $meta['benefits_section'] : '';
		$benefits_sec_title = ! empty( $meta['benefits_sec_title'] ) ? $meta['benefits_sec_title'] : '';
		$benefits           = ! empty( $meta['benefits'] ) ? $meta['benefits'] : '';

		if ( empty( $benefits_status ) || empty( $benefits ) ) {
			return;
		}

		echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';
		?>
		<div class="tf-car-benefits" id="tf-benefits">
			<?php if ( ! empty( $benefits_sec_title ) ) { ?>
				<h3 class="tf-section-title"><?php echo esc_html( $benefits_sec_title ); ?></h3>
			<?php } ?>

			<ul>
				<?php foreach ( $benefits as $singlebenefit ) { ?>
					<li class="tf-flex tf-flex-align-center tf-flex-gap-6">
						<i class="<?php echo ! empty( $singlebenefit['icon'] ) ? esc_attr( $singlebenefit['icon'] ) : 'ri-check-double-line'; ?>"></i>
						<?php echo ! empty( $singlebenefit['title'] ) ? esc_html( $singlebenefit['title'] ) : ''; ?>
					</li>
				<?php } ?>
			</ul>
		</div>
		<?php
		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}
}

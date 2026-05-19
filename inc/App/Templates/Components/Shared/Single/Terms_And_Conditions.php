<?php
namespace Tourfic\App\Templates\Components\Shared\Single;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Terms_And_Conditions Component
 * Handles rendering of terms and conditions section for all post types
 */
class Terms_And_Conditions {

	/**
	 * Render terms and conditions section
	 *
	 * @param array  $settings Widget settings
	 * @param string $builder  'bricks' or 'elementor'
	 */
	public static function render( $settings = [], $builder = '') {
		$post_id   = get_the_ID();
		$post_type = get_post_type();
        $wrapper_open  = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
        $wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';

		if ( 'tf_hotel' === $post_type ) {
			$meta     = get_post_meta( $post_id, 'tf_hotels_opt', true );
			$tc_title = ! empty( $meta['tc-section-title'] ) ? esc_html( $meta['tc-section-title'] ) : esc_html__( 'Hotel Terms & Conditions', 'tourfic' );
			$tc       = ! empty( $meta['tc'] ) ? $meta['tc'] : '';
		} elseif ( 'tf_tours' === $post_type ) {
			$meta     = get_post_meta( $post_id, 'tf_tours_opt', true );
			$tc_title = ! empty( $meta['tc-section-title'] ) ? esc_html( $meta['tc-section-title'] ) : esc_html__( 'Tour Terms & Conditions', 'tourfic' );
			$tc       = ! empty( $meta['terms_conditions'] ) ? $meta['terms_conditions'] : '';
		} elseif ( 'tf_apartment' === $post_type ) {
			$meta     = get_post_meta( $post_id, 'tf_apartment_opt', true );
			$tc_title = ! empty( $meta['terms_title'] ) ? esc_html( $meta['terms_title'] ) : esc_html__( 'Terms & Conditions', 'tourfic' );
			$tc       = ! empty( $meta['terms_and_conditions'] ) ? $meta['terms_and_conditions'] : '';
			$tc = is_array($tc) ? implode("\n", $tc) : (string) $tc;
			$tc_lines = array_filter( array_map( 'trim', explode( "\n", $tc ) ) );
		} elseif ( 'tf_carrental' === $post_type ) {
			$meta     = get_post_meta( $post_id, 'tf_carrental_opt', true );
			$tc_title = ! empty( $meta['car-tc-section-title'] ) ? esc_html( $meta['car-tc-section-title'] ) : '';
			$tc       = ! empty( $meta['terms_conditions'] ) ? $meta['terms_conditions'] : '';
		} else {
			return;
		}

        if( empty( $tc ) ) {
            return;
        }

		// Render wrapper open if provided and should be shown
		echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		// Render car rental format (table)
		if ( 'tf_carrental' === $post_type ) {
			?>
			<div class="tf-single-template__one tf-single-car-terms-and-conditions-style-1 sp-0">
				<div class="tf-car-conditions-section" id="tf-tc">
					<?php if ( ! empty( $tc_title ) ) { ?>
						<h3><?php echo esc_html( $tc_title ); ?></h3>
					<?php } ?>
					<table>
						<?php foreach ( $tc as $singletc ) { ?>
							<tr>
								<th><?php echo ! empty( $singletc['title'] ) ? esc_html( $singletc['title'] ) : ''; ?></th>
								<td><?php echo ! empty( $singletc['content'] ) ? wp_kses_post( $singletc['content'] ) : ''; ?></td>
							</tr>
						<?php } ?>
					</table>
				</div>
			</div>
			<?php
		} else {
			// Render regular format (content)
			?>
			<div class="tf-toc-wrapper">
				<div class="tf-section-head">
					<h2 class="tf-section-title"><?php echo esc_html( $tc_title ); ?></h2>
				</div>
				<div class="tf-toc-content">
					<?php if ( 'tf_apartment' === $post_type && ! empty( $tc_lines ) ) { ?>
						<ul class="tf-policies-list">
							<?php foreach ( $tc_lines as $line ) { ?>
							<li><?php echo wp_kses_post( $line ); ?></li>
							<?php } ?>
						</ul>
					<?php } else { ?>
						<?php echo wp_kses_post( wpautop( $tc ) ); ?>
					<?php } ?>
				</div>
			</div>
			<?php
		}

		// Render wrapper close if provided and should be shown
		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
    }
}

<?php
namespace Tourfic\App\Templates\Components\Global\Single;

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
	 * @param string $wrapper_open Opening wrapper HTML (multi-line supported)
	 * @param string $wrapper_close Closing wrapper HTML (multi-line supported)
	 */
	public static function render( $settings = [], $builder = '', $wrapper_open = '', $wrapper_close = '' ) {
		$post_id   = get_the_ID();
		$post_type = get_post_type();

		// Determine if wrapper should be shown (only in templates, not in builder widgets)
		$show_wrapper = empty( $builder ) ? true : false;

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
		if ( $show_wrapper && ! empty( $wrapper_open ) ) {
			echo wp_kses_post( $wrapper_open );
		}

		// Render car rental format (table)
		if ( 'tf_carrental' === $post_type ) {
			?>
			<div class="tf-single-template__one tf-single-car-terms-and-conditions-style-1">
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
					<?php echo wp_kses_post( wpautop( $tc ) ); ?>
				</div>
			</div>
			<?php
		}

		// Render wrapper close if provided and should be shown
		if ( $show_wrapper && ! empty( $wrapper_close ) ) {
			echo wp_kses_post( $wrapper_close );
		}
    }
}

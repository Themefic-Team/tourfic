<?php

namespace Tourfic\App\Templates\Components\Global\Archive;

defined( 'ABSPATH' ) || exit;

/**
 * Centralized archive sidebar renderer.
 * Other builders and template files should call the methods here so
 * markup is maintained in a single place.
 */
class Sidebar {

	/**
	 * Render the archive sidebar markup.
	 * @param array $settings Optional settings array (from widgets).
	 * @param string $builder Optional builder type (from widgets).
	 */
	public static function render( $settings = [], $builder = '' ) {
		$design  = ! empty( $settings['design'] ) ? $settings['design'] : 'design-1';
		$sidebar = ! empty( $settings['sidebar'] ) ? $settings['sidebar'] : '';

		if ( 'design-1' === $design ) :
			?>
			<div class="tf-sidebar__design-1">
				<?php
				if ( is_post_type_archive( 'tf_carrental' ) ) :
					?>
					<div class="tf-car-archive-sidebar">
						<div class="tf-sidebar-header tf-flex tf-flex-space-bttn tf-flex-align-center">
							<div class="tf-close-sidebar">
								<i class="fa-solid fa-xmark"></i>
							</div>
							<h4><?php esc_html_e( 'Filter', 'tourfic' ); ?></h4>
							<button class="filter-reset-btn"><?php esc_html_e( 'Reset', 'tourfic' ); ?></button>
						</div>
						<?php if ( is_active_sidebar( $sidebar ) ) { ?>
							<?php dynamic_sidebar( $sidebar ); ?>
						<?php } ?>
					</div>
					<?php
				else :
					?>
					<?php if ( is_active_sidebar( $sidebar ) ) { ?>
						<div id="tf__booking_sidebar">
							<?php dynamic_sidebar( $sidebar ); ?>
						</div>
					<?php } ?>
				<?php endif; ?>
			</div>
			<?php
		elseif ( 'design-2' === $design ) :
			?>
			<div class="tf-sidebar__design-2">
				<div class="tf-details-right tf-sitebar-widgets tf-archive-right">
					<div class="tf-filter-wrapper">
						<div class="tf-filter-title">
							<h2 class="tf-section-title"><?php esc_html_e( 'Filter', 'tourfic' ); ?></h2>
							<button class="filter-reset-btn"><?php esc_html_e( 'Reset', 'tourfic' ); ?></button>
						</div>
						<?php if ( is_active_sidebar( $sidebar ) ) { ?>
							<div id="tf__booking_sidebar">
								<?php dynamic_sidebar( $sidebar ); ?>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php
		endif;
	}
}

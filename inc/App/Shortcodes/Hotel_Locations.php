<?php

namespace Tourfic\App\Shortcodes;

defined( 'ABSPATH' ) || exit;

class Hotel_Locations extends \Tourfic\Core\Shortcodes {

	use \Tourfic\Traits\Singleton;

    protected $shortcode = 'hotel_locations';

	public function __construct() {
		parent::__construct();
		// Old compatibility
		add_shortcode( 'tourfic_destinations', [ $this, 'render' ] );
	}

	function render( $atts, $content = null ) {

		// Shortcode extract
		extract(
			shortcode_atts(
				array(
					'orderby'    => 'name',
					'order'      => 'ASC',
					'hide_empty' => 0,
					'ids'        => '',
					'limit'      => - 1,
				),
				$atts
			)
		);

		// 1st search on hotel_location taxonomy
		$locations = get_terms( array(
			'taxonomy'     => 'hotel_location',
			'orderby'      => $orderby,
			'order'        => $order,
			'hide_empty'   => $hide_empty,
			'hierarchical' => 0,
			'search'       => '',
			'number'       => $limit == - 1 ? false : $limit,
			'include'      => $ids,
		) );

		ob_start();

		if ( $locations ) { ?>
			<section id="recomended_section_wrapper">
				<div class="recomended_inner">

					<?php foreach ( $locations as $term ) {

						$meta      = get_term_meta( $term->term_id, 'tf_hotel_location', true );
						$image_url = ! empty( $meta['image'] ) ? $meta['image'] : esc_url(TF_ASSETS_APP_URL . 'images/feature-default.jpg');
						$term_link = get_term_link( $term ); ?>

						<div class="single_recomended_item">
							<a href="<?php echo esc_url( $term_link ); ?>">
								<div class="single_recomended_content" style="background-image: url(<?php echo esc_url( $image_url ); ?>);">
									<div class="recomended_place_info_header">
										<h3><?php echo esc_html( $term->name ); ?></h3>
										<?php /* translators: %s Hotel Count */ ?>
										<p><?php printf( esc_html( _n( '%s hotel', '%s hotels', $term->count, 'tourfic' ) ), esc_html( $term->count ) ); ?></p>
									</div>
								</div>
							</a>
						</div>

					<?php } ?>

				</div>
			</section>

		<?php }

		return ob_get_clean();
	}
}
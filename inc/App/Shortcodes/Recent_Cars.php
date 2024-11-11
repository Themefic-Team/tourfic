<?php

namespace Tourfic\App\Shortcodes;

defined( 'ABSPATH' ) || exit;

use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Car_Rental\Pricing;

class Recent_Cars extends \Tourfic\Core\Shortcodes {

	use \Tourfic\Traits\Singleton;

	protected $shortcode = 'tf_recent_cars';

	function render( $atts, $content = null ) {
		extract(
			shortcode_atts(
				array(
					'title'        => '',
					'subtitle'     => '',
					'count'        => 10,
					'style'        => 'grid',
				),
				$atts
			)
		);

		$args = array(
			'post_type'      => 'tf_carrental',
			'post_status'    => 'publish',
			'orderby'        => !empty($atts['orderby']) ? $atts['orderby'] : 'date',
			'order'          => !empty($atts["order"]) ? $atts["order"] : "DESC",
			'posts_per_page' => $count,
		);

		ob_start();

        if ( $style == 'list' ) {
			$views_activate = 'list-view';
		} else {
			$views_activate = 'grid-view';
		}
		$car_loop = new \WP_Query( $args );
		?>
		<?php if ( $car_loop->have_posts() ) : ?>
			<div class="tf-car-archive-result tf-car-lists-widgets">
				<div class="tf-heading">
					<?php
					echo ! empty( $title ) ? '<h2>' . esc_html( $title ) . '</h2>' : '';
					echo ! empty( $subtitle ) ? '<p>' . esc_html( $subtitle ) . '</p>' : '';
					?>
				</div>
                <?php do_action("tf_car_archive_card_items_before"); ?>
                <div class="tf-car-result archive_ajax_result tf-flex tf-flex-gap-32 <?php echo esc_attr($views_activate); ?>">
                    
                    <?php
                        while ( $car_loop->have_posts() ) {
                            $car_loop->the_post();
                            tf_car_archive_single_item();
                        }
                    ?>

                </div>
                <?php do_action("tf_car_archive_card_items_after"); ?>

            </div>
		<?php endif;
		wp_reset_postdata(); ?>

		<?php return ob_get_clean();
	}
}
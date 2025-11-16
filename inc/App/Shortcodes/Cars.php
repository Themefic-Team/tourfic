<?php

namespace Tourfic\App\Shortcodes;

defined( 'ABSPATH' ) || exit;

use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Helper;

class Cars extends \Tourfic\Core\Shortcodes {

	use \Tourfic\Traits\Singleton;

	protected $shortcode = 'tf_cars';

	function render( $atts, $content = null ) {
		extract(
			shortcode_atts(
				array(
					'title'        => '',
					'subtitle'     => '',
					'destinations' => '',
					'count'        => '3',
					'style'        => 'grid',
				),
				$atts
			)
		);

		$tf_disable_services = ! empty( Helper::tfopt( 'disable-services' ) ) ? Helper::tfopt( 'disable-services' ) : [];
		if (in_array('carrentals', $tf_disable_services)){
			return;
		}

		$args = array(
			'post_type'      => 'tf_carrental',
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
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
		<div class="tf-car-archive-result tf-car-lists-widgets">
			<div class="tf-heading">
				<?php
				echo ! empty( $title ) ? '<h2>' . esc_html( $title ) . '</h2>' : '';
				echo ! empty( $subtitle ) ? '<p>' . esc_html( $subtitle ) . '</p>' : '';
				?>
			</div>
			<?php do_action("tf_car_archive_card_items_before"); ?>
			<?php if ( $car_loop->have_posts() ) { ?>
			<div class="tf-car-result archive_ajax_result tf-flex tf-flex-gap-32 <?php echo esc_attr($views_activate); ?>">
				
				<?php
					while ( $car_loop->have_posts() ) {
						$car_loop->the_post();
						$car_meta = get_post_meta( get_the_ID(), 'tf_carrental_opt', true );
						if ( $car_meta["car_as_featured"] ) {
							tf_car_archive_single_item();
						}
					}
				?>

			</div>
			<?php }else{
				echo esc_html__( 'Cars Not Found', 'tourfic' );
			}
			wp_reset_postdata(); 
			do_action("tf_car_archive_card_items_after"); 
			?>
		</div>

		<?php
		return ob_get_clean();
	}
}
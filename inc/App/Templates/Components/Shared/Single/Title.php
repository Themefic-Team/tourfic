<?php
namespace Tourfic\App\Templates\Components\Shared\Single;

defined( 'ABSPATH' ) || exit;

/**
 * Centralized single title renderer.
 * Other builders and template files should call the methods here so
 * markup is maintained in a single place.
 */
class Title {

	/**
	 * Render the single title markup.
	 * @param array $settings Optional settings array (from widgets).
	 * @param string $builder Optional builder type (from widgets).
	 */
	public static function render( $settings = [], $builder = '' ) {
		$title_tag    = ! empty( $settings['tf-title-tag'] ) ? $settings['tf-title-tag'] : 'h1';
		$post_type    = get_post_type();
		$tf_cars_slug = get_option( 'car_slug' );

		if ( 'tf_carrental' === $post_type ) :
			?>
			<div class="tf-car-title tf-head-title">
				<?php
				printf(
					'<%1$s class="tf-post-title">%2$s</%1$s>',
					esc_attr( $title_tag ),
					esc_html( get_the_title() )
				);
				?>
				<div class="breadcrumb">
					<ul>
						<li>
							<a href="<?php echo esc_url( site_url() ); ?>">
								<?php esc_html_e( 'Home', 'tourfic' ); ?>
							</a>
						</li>
						<li>/</li>
						<li>
							<a href="<?php echo esc_url( trailingslashit( site_url() ) . $tf_cars_slug ); ?>">
								<?php esc_html_e( 'Cars', 'tourfic' ); ?>
							</a>
						</li>
						<li>/</li>
						<li><?php echo esc_html( get_the_title() ); ?></li>
					</ul>
				</div>
			</div>
			<?php
		else :
			printf(
				'<%1$s class="tf-post-title">%2$s</%1$s>',
				esc_attr( $title_tag ),
				esc_html( get_the_title() )
			);
		endif;
	}
}

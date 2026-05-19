<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

use Elementor\Icons_Manager;
use Tourfic\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Single Share Component
 * Shared markup for Elementor and Bricks Share widgets
 */
class Share {

	/**
	 * Static render method for Share component
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type (elementor or bricks)
	 *
	 * @return void
	 */
	public static function render( $settings = [], $builder = '' ) {
		$post_id       = get_the_ID();
		$post_type     = get_post_type();
		$share_text    = get_the_title();
		$share_link    = get_permalink( $post_id );
		$disable_share_opt = '';
        $design  	   = ! empty( $settings['design'] ) ? $settings['design'] : 'design-1';

		// Get post meta based on post type
		if ( 'tf_hotel' === $post_type ) {
			$post_meta         = get_post_meta( $post_id, 'tf_hotels_opt', true );
			$disable_share_opt = ! empty( $post_meta['h-share'] ) ? $post_meta['h-share'] : '';
		} elseif ( 'tf_tours' === $post_type ) {
			$post_meta         = get_post_meta( $post_id, 'tf_tours_opt', true );
			$disable_share_opt = ! empty( $post_meta['t-share'] ) ? $post_meta['t-share'] : 0;
		} elseif ( 'tf_apartment' === $post_type ) {
			$post_meta         = get_post_meta( $post_id, 'tf_apartment_opt', true );
			$disable_share_opt = ! empty( $post_meta['disable-apartment-share'] ) ? $post_meta['disable-apartment-share'] : '';
		} elseif ( 'tf_carrental' === $post_type ) {
			$post_meta         = get_post_meta( $post_id, 'tf_carrental_opt', true );
			$disable_share_opt = ! empty( $post_meta['c-share'] ) ? $post_meta['c-share'] : 0;
		} else {
			return;
		}

        //Share icon
        $share_icon_html = ($design == 'design-1') ? '<i class="ri-share-line"></i>' : '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 12V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V12M16 6L12 2M12 2L8 6M12 2V15" stroke="#8997A9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>';
		if ( 'elementor' === $builder && class_exists( '\Elementor\Icons_Manager' ) ) {
			$share_icon_migrated = isset($settings['__fa4_migrated']['share_icon']);
			$share_icon_is_new = empty($settings['share_icon_comp']);

			if ( $share_icon_is_new || $share_icon_migrated ) {
				ob_start();
				Icons_Manager::render_icon( $settings['share_icon'], [ 'aria-hidden' => 'true' ] );
				$share_icon_html = ob_get_clean();
			} else{
				$share_icon_html = '<i class="' . esc_attr( $settings['share_icon_comp'] ) . '"></i>';
			}
		} elseif ( 'bricks' == $builder ) {
			if ( ! empty( $settings['share_icon']['library'] ) && ! empty( $settings['share_icon']['icon'] ) ) {
				$share_icon_html = '<i class="' . esc_attr( $settings['share_icon']['icon'] ) . '" aria-hidden="true"></i>';
			} elseif ( ! empty( $settings['share_icon']['class'] ) ) {
				$share_icon_html = '<i class="' . esc_attr( $settings['share_icon']['class'] ) . '" aria-hidden="true"></i>';
			} elseif ( ! empty( $settings['share_icon'] ) && is_string( $settings['share_icon'] ) ) {
				$share_icon_html = '<i class="' . esc_attr( $settings['share_icon'] ) . '" aria-hidden="true"></i>';
			}
		}

		// Icon type
		$style     = ! empty( $settings['share_style'] ) ? $settings['share_style'] : 'style1';
		$icon_type = ! empty( $settings['icon_type'] ) ? $settings['icon_type'] : 'rounded';

		if ( '1' !== $disable_share_opt ) :
			// Common social share links
			$social_links = [
				'facebook'  => 'http://www.facebook.com/share.php?u=' . esc_url( $share_link ),
				'twitter'   => 'http://twitter.com/share?text=' . esc_attr( $share_text ) . '&url=' . esc_url( $share_link ),
				'linkedin'  => 'https://www.linkedin.com/cws/share?url=' . esc_url( $share_link ),
				'pinterest' => 'http://pinterest.com/pin/create/button/?url=' . esc_url( $share_link ) . '&media=' . esc_url( get_the_post_thumbnail_url() ) . '&description=' . esc_attr( $share_text ),
			];

			// Style 1: Dropdown with icons only
			if ( 'style1' === $style ) {
				?>
				<div class="tf-single-template__one sp-0">
					<div class="tf-share <?php echo 'tf_carrental' === $post_type ? esc_attr( 'tf-off-canvas-share-box' ) : ''; ?>">
						<a href="#dropdown-share-center" class="<?php echo 'tf_carrental' === $post_type ? esc_attr( 'tf-share-toggle' ) : esc_attr('share-toggle'); ?> tf-icon tf-social-box tf-icon-type-<?php echo esc_attr( $icon_type ); ?>" data-toggle="true">
							<?php echo wp_kses( $share_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
						</a>

						<div id="dropdown-share-center" class="<?php echo 'tf_carrental' === $post_type ? esc_attr( 'share-car-content' ) : esc_attr( 'share-tour-content' ); ?>">
							<div class="tf-dropdown-share-content">
								<h4><?php esc_html_e( 'Share with friends', 'tourfic' ); ?></h4>
								<ul>
									<?php foreach ( [ 'facebook', 'twitter', 'linkedin', 'pinterest' ] as $network ) : ?>
									<li>
										<a href="<?php echo esc_url( $social_links[ $network ] ); ?>" class="tf-dropdown-item" target="_blank">
											<span class="tf-dropdown-item-content">
												<i class="fab fa-<?php echo esc_attr( $network ); ?>"></i>
											</span>
										</a>
									</li>
									<?php endforeach; ?>
									<li>
										<div title="<?php esc_attr_e( 'Share this link', 'tourfic' ); ?>" aria-controls="share_link_button">
											<button id="share_link_button" class="tf_btn tf_btn_small share-center-copy-cta" tabindex="0" role="button">
												<i class="fa fa-link" aria-hidden="true"></i>
												<span class="tf-button-text share-center-copied-message"><?php esc_html_e( 'Link Copied!', 'tourfic' ); ?></span>
											</button>
											<input type="text" id="share_link_input" class="share-center-url share-center-url-input" value="<?php echo esc_attr( $share_link ); ?>" readonly>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			// Style 2: Off-canvas style
			elseif ( 'style2' === $style ) {
				?>
				<div class="tf-share tf-off-canvas-share-box <?php echo !empty( $builder ) ? 'tf-single-template__two' : ''; ?>">
					<ul class="tf-off-canvas-share">
						<?php foreach ( [ 'facebook', 'twitter', 'linkedin', 'pinterest' ] as $network ) : ?>
						<li>
							<a href="<?php echo esc_url( $social_links[ $network ] ); ?>" class="tf-dropdown-item" target="_blank">
								<span class="tf-dropdown-item-content">
									<i class="fab fa-<?php echo esc_attr( $network ); ?>"></i>
								</span>
							</a>
						</li>
						<?php endforeach; ?>
						<li>
							<a href="#" id="share_link_button" class="share-center-copy-cta">
								<i class="ri-links-line"></i>
								<span class="tf-button-text share-center-copied-message"><?php esc_html_e( 'Link Copied!', 'tourfic' ); ?></span>
							</a>
							<input type="text" id="share_link_input" class="share-center-url share-center-url-input" value="<?php echo esc_attr( $share_link ); ?>" readonly>
						</li>
					</ul>
					<a href="#dropdown-share-center" class="tf-share-toggle tf-icon tf-social-box tf-icon-type-<?php echo esc_attr( $icon_type ); ?>" data-toggle="true">
						<?php echo wp_kses( $share_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
					</a>
				</div>
				<?php
			}
			// Style 3: Dropdown with text labels
			elseif ( 'style3' === $style ) {
				$network_labels = [
					'facebook'  => esc_html__( 'Share on Facebook', 'tourfic' ),
					'twitter'   => esc_html__( 'Share on Twitter', 'tourfic' ),
					'linkedin'  => esc_html__( 'Share on Linkedin', 'tourfic' ),
					'pinterest' => esc_html__( 'Share on Pinterest', 'tourfic' ),
				];
				?>
				<div class="tf-share">
					<a href="#dropdown-share-center" class="share-toggle tf-icon-type-<?php echo esc_attr( $icon_type ); ?>" data-toggle="true">
						<?php echo wp_kses( $share_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
                        <?php if( $post_type == 'tf_apartment' && empty( $builder ) ) : ?>
                            <span class="share-text"><?php esc_html_e( 'Share', 'tourfic' ); ?></span>
                        <?php endif; ?>
					</a>
					<div id="dropdown-share-center" class="share-tour-content">
						<ul class="tf-dropdown-content">
							<?php foreach ( [ 'facebook', 'twitter', 'linkedin', 'pinterest' ] as $network ) : ?>
							<li>
								<a href="<?php echo esc_url( $social_links[ $network ] ); ?>" class="tf-dropdown-item" target="_blank">
									<span class="tf-dropdown-item-content">
										<i class="fab fa-<?php echo esc_attr( $network ); ?>-square"></i>
										<?php echo esc_html( $network_labels[ $network ] ); ?>
									</span>
								</a>
							</li>
							<?php endforeach; ?>
							<li>
								<div class="share-center-copy-form tf-dropdown-item" title="<?php esc_attr_e( 'Share this link', 'tourfic' ); ?>" aria-controls="share_link_button">
									<label class="share-center-copy-label" for="share_link_input"><?php esc_html_e( 'Share this link', 'tourfic' ); ?></label>
									<input type="text" id="share_link_input" class="share-center-url share-center-url-input" value="<?php echo esc_attr( $share_link ); ?>" readonly>
									<button id="share_link_button" class="tf_btn tf_btn_small share-center-copy-cta" tabindex="0" role="button">
										<span class="tf-button-text share-center-copy-message"><?php esc_html_e( 'Copy link', 'tourfic' ); ?></span>
										<span class="tf-button-text share-center-copied-message"><?php esc_html_e( 'Link Copied!', 'tourfic' ); ?></span>
									</button>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<?php
			}
		endif;
	}
}

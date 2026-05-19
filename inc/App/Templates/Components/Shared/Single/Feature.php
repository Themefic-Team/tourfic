<?php
namespace Tourfic\App\Templates\Components\Shared\Single;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Feature Component
 * Handles rendering of feature/amenities section for all post types
 */
class Feature {

	/**
	 * Render feature section
	 *
	 * @param array  $settings Widget settings
	 * @param string $builder  'bricks' or 'elementor'
	 */
	public static function render( $settings = [], $builder = '' ) {
		$post_id = get_the_ID();
		$post_type = get_post_type();

		switch ( $post_type ) {
			case 'tf_hotel':
				self::render_hotel_features( $settings, $post_id, $builder );
				break;
			case 'tf_room':
				self::render_room_features( $settings, $post_id, $builder );
				break;
			case 'tf_tours':
				self::render_tour_features( $settings, $post_id, $builder );
				break;
			case 'tf_apartment':
				self::render_apartment_features( $settings, $post_id, $builder );
				break;
		}
	}

	/**
	 * Render hotel features
	 */
	private static function render_hotel_features( $settings, $post_id, $builder = '' ) {
		$style = ! empty( $settings['feature_style'] ) ? $settings['feature_style'] : 'style1';
		$meta  = get_post_meta( $post_id, 'tf_hotels_opt', true );
		$wrapper = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';
        $wrapper_open = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';
		$feature_title = ! empty( $meta['popular-section-title'] ) ? esc_html( $meta['popular-section-title'] ) : '';
		$features      = ! empty( get_the_terms( $post_id, 'hotel_feature' ) ) ? get_the_terms( $post_id, 'hotel_feature' ) : '';

		echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( 'style1' === $style && $features ) {
            echo ($wrapper === 'yes') ? '<div class="tf-single-template__one tf-single-feature-style1 sp-0">' : '';
			?>
            <div class="tf-hotel-single-features tf-template-section">
                <h2 class="tf-title tf-section-title"><?php echo esc_html( $feature_title ); ?></h2>
                <ul>
                    <?php foreach ( $features as $feature_id ) {
                        $feature_meta = get_term_meta( $feature_id, 'tf_hotel_feature', true );
                        $feature      = get_term( $feature_id );
                        $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';

                        if ( 'fa' === $f_icon_type && ! empty( $feature_meta['icon-fa'] ) ) {
                            $feature_icon = '<i class="' . esc_attr( $feature_meta['icon-fa'] ) . '"></i>';
                        } elseif ( 'c' === $f_icon_type && ! empty( $feature_meta['icon-c'] ) ) {
                            $feature_icon = '<img src="' . esc_url( $feature_meta['icon-c'] ) . '" style="width: ' . intval( $feature_meta['dimention'] ) . 'px; height: ' . intval( $feature_meta['dimention'] ) . 'px;" />';
                        } else {
                            $feature_icon = '';
                        }
                        ?>
                        <li>
                            <?php echo ! empty( $feature_meta ) && ! empty( $feature_icon ) ? wp_kses_post( $feature_icon ) : ''; ?>
                            <?php echo esc_html( $feature->name ); ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
			<?php
            echo ($wrapper === 'yes') ? '</div>' : '';
		} elseif ( 'style2' === $style && $features ) {
            echo ($wrapper === 'yes') ? '<div class="tf-single-template__two tf-single-feature-section tf-single-feature-style2">' : '';
			?>
            <div class="tf-overview-wrapper">
                <div class="tf-overview-popular-facilities tf-feature-column-3">
                    <h2 class="tf-title tf-section-title"><?php echo esc_html( $feature_title ); ?></h2>
                    <ul>
                        <?php foreach ( $features as $feature_id ) {
                            $feature_meta = get_term_meta( $feature_id, 'tf_hotel_feature', true );
                            $feature      = get_term( $feature_id );
                            $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';

                            if ( 'fa' === $f_icon_type && ! empty( $feature_meta['icon-fa'] ) ) {
                                $feature_icon = '<i class="' . esc_attr( $feature_meta['icon-fa'] ) . '"></i>';
                            } elseif ( 'c' === $f_icon_type && ! empty( $feature_meta['icon-c'] ) ) {
                                $feature_icon = '<img src="' . esc_url( $feature_meta['icon-c'] ) . '" style="width: ' . intval( $feature_meta['dimention'] ) . 'px; height: ' . intval( $feature_meta['dimention'] ) . 'px;" />';
                            } else {
                                $feature_icon = '';
                            }
                            ?>
                            <li>
                                <?php echo ! empty( $feature_meta ) && ! empty( $feature_icon ) ? wp_kses_post( $feature_icon ) : ''; ?>
                                <?php echo esc_html( $feature->name ); ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
			<?php
            echo ($wrapper === 'yes') ? '</div>' : '';
		}

		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}

	/**
	 * Render room features
	 */
	private static function render_room_features( $settings, $post_id, $builder = '' ) {
		$style = ! empty( $settings['feature_style'] ) ? $settings['feature_style'] : 'style1';
		$meta  = get_post_meta( $post_id, 'tf_room_opt', true );
		$wrapper = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';
        $wrapper_open = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';
		$feature_title = ! empty( $meta['room-feature-section-title'] ) ? esc_html( $meta['room-feature-section-title'] ) : '';
		$features      = ! empty( $meta['features'] ) ? $meta['features'] : '';

		echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( 'style1' === $style && $features ) {
            echo ($wrapper === 'yes') ? '<div class="tf-single-template__one tf-single-feature-style1">' : '';
			?>
			<div class="tf-hotel-single-features tf-template-section">
				<h2 class="tf-title tf-section-title"><?php echo esc_html( $feature_title ); ?></h2>
				<ul>
                    <?php foreach ( $features as $feature_id ) {
                        $feature_meta = get_term_meta( $feature_id, 'tf_hotel_feature', true );
                        $feature      = get_term( $feature_id );
                        $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';

                        if ( 'fa' === $f_icon_type && ! empty( $feature_meta['icon-fa'] ) ) {
                            $feature_icon = '<i class="' . esc_attr( $feature_meta['icon-fa'] ) . '"></i>';
                        } elseif ( 'c' === $f_icon_type && ! empty( $feature_meta['icon-c'] ) ) {
                            $feature_icon = '<img src="' . esc_url( $feature_meta['icon-c'] ) . '" style="width: ' . intval( $feature_meta['dimention'] ) . 'px; height: ' . intval( $feature_meta['dimention'] ) . 'px;" />';
                        } else {
                            $feature_icon = '';
                        }
                        ?>
                        <li>
                            <?php echo ! empty( $feature_meta ) && ! empty( $feature_icon ) ? wp_kses_post( $feature_icon ) : ''; ?>
                            <?php echo esc_html( $feature->name ); ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
			<?php
            echo ($wrapper === 'yes') ? '</div>' : '';
		} elseif ( 'style2' === $style && $features ) {
            echo ($wrapper === 'yes') ? '<div class="tf-single-template__two tf-single-feature-section tf-single-feature-style2">' : '';
			?>
            <div class="tf-overview-wrapper">
                <div class="tf-overview-popular-facilities tf-feature-column-3">
                    <h2 class="tf-title tf-section-title"><?php echo esc_html( $feature_title ); ?></h2>
                    <ul>
                        <?php foreach ( $features as $feature_id ) {
                            $feature_meta = get_term_meta( $feature_id, 'tf_hotel_feature', true );
                            $feature      = get_term( $feature_id );
                            $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';

                            if ( 'fa' === $f_icon_type && ! empty( $feature_meta['icon-fa'] ) ) {
                                $feature_icon = '<i class="' . esc_attr( $feature_meta['icon-fa'] ) . '"></i>';
                            } elseif ( 'c' === $f_icon_type && ! empty( $feature_meta['icon-c'] ) ) {
                                $feature_icon = '<img src="' . esc_url( $feature_meta['icon-c'] ) . '" style="width: ' . intval( $feature_meta['dimention'] ) . 'px; height: ' . intval( $feature_meta['dimention'] ) . 'px;" />';
                            } else {
                                $feature_icon = '';
                            }
                            ?>
                            <li>
                                <?php echo ! empty( $feature_meta ) && ! empty( $feature_icon ) ? wp_kses_post( $feature_icon ) : ''; ?>
                                <?php echo esc_html( $feature->name ); ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
			<?php
            echo ($wrapper === 'yes') ? '</div>' : '';
		}

		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}

	/**
	 * Render tour features
	 */
	private static function render_tour_features( $settings, $post_id, $builder = '' ) {
		$style = ! empty( $settings['feature_style'] ) ? $settings['feature_style'] : 'style1';
		$meta  = get_post_meta( $post_id, 'tf_tours_opt', true );
        $wrapper = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';
		$wrapper_open = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';
		$feature_title = ! empty( $meta['tour-features-section-title'] ) ? esc_html( $meta['tour-features-section-title'] ) : '';
		$features      = ! empty( get_the_terms( $post_id, 'tour_features' ) ) ? get_the_terms( $post_id, 'tour_features' ) : '';

		echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( 'style1' === $style && $features ) {
            echo ($wrapper === 'yes') ? '<div class="tf-single-template__one tf-single-feature-style1 sp-0">' : '';
			?>
            <div class="tf-tour-features tf-template-section">
                <div class="tf-tour-features-container">
                    <?php if ( ! empty( $feature_title ) ) : ?>
                        <h2 class="tf-title tf-section-title"><?php echo esc_html( $feature_title ); ?></h2>
                    <?php endif; ?>
                    <ul class="tf-tour-feature-list">
                        <?php foreach ( $features as $feature ) {
                            $feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tour_features', true );
                            $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';

                            if ( 'fa' === $f_icon_type && ! empty( $feature_meta['icon-fa'] ) ) {
                                $feature_icon = '<i class="' . esc_attr( $feature_meta['icon-fa'] ) . '"></i>';
                            } elseif ( 'c' === $f_icon_type && ! empty( $feature_meta['icon-c'] ) ) {
                                $feature_icon = '<img src="' . esc_url( $feature_meta['icon-c'] ) . '" style="width: ' . intval( $feature_meta['dimention'] ) . 'px; height: ' . intval( $feature_meta['dimention'] ) . 'px;" />';
                            } else {
                                $feature_icon = '';
                            }
                            ?>
                            <?php if ( ! empty( $feature->name ) ) : ?>
                                <li class="single-feature-box">
                                    <span class="tf-tour-features-icon"><?php echo ! empty( $feature_icon ) ? wp_kses_post( $feature_icon ) : ''; ?></span>
                                    <span><?php echo esc_html( $feature->name ); ?></span>
                                </li>
                            <?php endif; ?>
                        <?php } ?>
                    </ul>
                </div>
            </div>
			<?php
            echo ($wrapper === 'yes') ? '</div>' : '';
		} elseif ( 'style2' === $style && $features ) {
            echo ($wrapper === 'yes') ? '<div class="tf-single-feature-section tf-single-feature-style2">' : '';
			?>
            <div class="tf-overview-popular-facilities">
                <span class="tf-popular-facilities-title"><?php echo esc_html( $feature_title ); ?></span>
                <ul>
                    <?php foreach ( $features as $feature ) {
                        $feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tour_features', true );
                        $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';

                        if ( 'fa' === $f_icon_type && ! empty( $feature_meta['icon-fa'] ) ) {
                            $feature_icon = '<i class="' . esc_attr( $feature_meta['icon-fa'] ) . '"></i>';
                        } elseif ( 'c' === $f_icon_type && ! empty( $feature_meta['icon-c'] ) ) {
                            $feature_icon = '<img src="' . esc_url( $feature_meta['icon-c'] ) . '" style="width: ' . intval( $feature_meta['dimention'] ) . 'px; height: ' . intval( $feature_meta['dimention'] ) . 'px;" />';
                        } else {
                            $feature_icon = '';
                        }
                        ?>
                        <li>
                            <?php echo ! empty( $feature_icon ) ? wp_kses_post( $feature_icon ) : ''; ?>
                            <?php echo esc_html( $feature->name ); ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
			<?php
            echo ($wrapper === 'yes') ? '</div>' : '';
		}

		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}

	/**
	 * Render apartment features/amenities
	 */
	private static function render_apartment_features( $settings, $post_id, $builder = '' ) {
		$style = ! empty( $settings['feature_style'] ) ? $settings['feature_style'] : 'style1';
		$meta  = get_post_meta( $post_id, 'tf_apartment_opt', true );
		$wrapper = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';
		$wrapper_open = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';
		$amenities = \Tourfic\Classes\Helper::tf_data_types( $meta['amenities'] ?? [] );

		echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( 'style1' === $style && $amenities ) {

			$fav_amenities = [];
			foreach ( $amenities as $amenity ) {
				if ( ! isset( $amenity['favorite'] ) || '1' !== $amenity['favorite'] ) {
					continue;
				}
				$fav_amenities[] = $amenity;
			}

			if ( ! empty( $fav_amenities ) ) {
				?>
				<div class="tf-apartment-feature-style1 tf-place-offer-section">
					<h2 class="tf-section-title"><?php echo ! empty( $meta['amenities_title'] ) ? esc_html( $meta['amenities_title'] ) : ''; ?></h2>
					<div class="place-offer-items">
						<?php foreach ( array_slice( $fav_amenities, 0, 10 ) as $amenity ) : ?>
							<?php
							$feature       = get_term_by( 'id', $amenity['feature'], 'apartment_feature' );
							$feature_meta  = get_term_meta( $amenity['feature'], 'tf_apartment_feature', true );
							$f_icon_type   = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';

							if ( 'icon' === $f_icon_type && ! empty( $feature_meta['apartment-feature-icon'] ) ) {
								$feature_icon = '<i class="' . esc_attr( $feature_meta['apartment-feature-icon'] ) . '"></i>';
							} elseif ( 'custom' === $f_icon_type && ! empty( $feature_meta['apartment-feature-icon-custom'] ) ) {
								$feature_icon = '<img src="' . esc_url( $feature_meta['apartment-feature-icon-custom'] ) . '" style="width: ' . intval( $feature_meta['apartment-feature-icon-dimension'] ) . 'px; height: ' . intval( $feature_meta['apartment-feature-icon-dimension'] ) . 'px;" />';
							} else {
								$feature_icon = '';
							}
							?>
							<div class="tf-apt-amenity">
								<?php echo ! empty( $feature_icon ) ? "<div class='tf-apt-amenity-icon'>" . wp_kses_post( $feature_icon ) . "</div>" : ''; ?>
								<span><?php echo esc_html( $feature->name ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php
			}
		}

		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}
}

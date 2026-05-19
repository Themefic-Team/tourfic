<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

use Tourfic\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Single Amenities Component
 * Shared markup for Elementor and Bricks Amenities widgets
 */
class Amenities {

	/**
	 * Static render method for Amenities component
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type (elementor or bricks)
	 *
	 * @return void
	 */
	public static function render( $settings = [], $builder = '' ) {
		$post_id   = get_the_ID();
		$post_type = get_post_type();

		if ( 'tf_hotel' === $post_type ) {
			self::render_hotel_amenities( $settings, $builder );
		} elseif ( 'tf_apartment' === $post_type ) {
			self::render_apartment_amenities( $settings, $builder );
		}
	}

	/**
	 * Render hotel amenities
	 *
	 * @param array $settings Settings from widget
	 *
	 * @return void
	 */
	private static function render_hotel_amenities( $settings, $builder = '' ) {
		$post_id                = get_the_ID();
		$style                  = ! empty( $settings['amenities_style'] ) ? $settings['amenities_style'] : 'style1';
		$meta                   = get_post_meta( $post_id, 'tf_hotels_opt', true );
		$facilities_categories  = ! empty( Helper::tf_data_types( Helper::tfopt( 'hotel_facilities_cats' ) ) ) ? Helper::tf_data_types( Helper::tfopt( 'hotel_facilities_cats' ) ) : '';
		$facilities_title       = ! empty( $meta['facilities-section-title'] ) ? esc_html( $meta['facilities-section-title'] ) : esc_html__( 'Property facilities', 'tourfic' );
		$facilities             = ! empty( $meta['hotel-facilities'] ) ? Helper::tf_data_types( $meta['hotel-facilities'] ) : [];
        $container              = ! empty( $settings['container'] ) ? $settings['container'] : 'no';
        $wrapper_open           = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
        $wrapper_close          = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';

		echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( 'style1' === $style && ! empty( $facilities_categories ) && ! empty( $facilities ) ) {
			?>
			<div class="tf-facilities-wrapper tf-single-amenities-style1" id="tf-hotel-facilities">
				<h2 class="tf-section-title"><?php echo esc_html( $facilities_title ); ?></h2>
				<div class="tf-facilities">
					<?php
					$facilities_list = [];
					if ( ! empty( $facilities ) && is_array( $facilities ) ) {
						foreach ( $facilities as $facility ) {
							$facilities_list[ $facility['facilities-category'] ] = $facility['facilities-category'];
						}
					}
					if ( ! empty( $facilities_list ) ) {
						foreach ( $facilities_list as $catkey => $single_feature ) {
							?>
							<div class="tf-facility-item">
								<?php $f_icon_single = ! empty( $facilities_categories[ $catkey ]['hotel_facilities_cat_icon'] ) ? esc_attr( $facilities_categories[ $catkey ]['hotel_facilities_cat_icon'] ) : ''; ?>
								<span class="single-facilities-title">
									<?php echo ! empty( $f_icon_single ) ? '<i class="' . esc_attr( $f_icon_single ) . '"></i>' : ''; ?>
									<?php echo ! empty( $facilities_categories[ $catkey ]['hotel_facilities_cat_name'] ) ? esc_html( $facilities_categories[ $catkey ]['hotel_facilities_cat_name'] ) : ''; ?>
								</span>
								<ul>
									<?php
									foreach ( $meta['hotel-facilities'] as $facility ) {
										if ( $facility['facilities-category'] == $catkey ) {
											$features_details = ! empty( $facility['facilities-feature'] ) ? get_term( $facility['facilities-feature'] ) : '';
											if ( ! empty( $features_details->name ) ) {
												?>
												<li>
													<?php echo esc_html( $features_details->name ); ?>
												</li>
												<?php
											}
										}
									}
									?>
								</ul>
							</div>
							<?php
						}
					}
					?>
				</div>
			</div>
			<?php
		} elseif ( 'style2' === $style && ! empty( $facilities_categories ) && ! empty( $facilities ) ) {
			?>
			<div class="tf-hotel-facilities-section tf-template-section tf-single-hotel-amenities-style2">
                <?php echo $container === 'yes' ? '<div class="tf-container">' : ''; ?>
                <div class="tf-hotel-facilities-container">
                    <div class="tf-hotel-facilities-title">
                        <h2 class="section-heading"><?php echo esc_html( $facilities_title ); ?></h2>
                    </div>
                    <div class="tf-hotel-facilities-content-area">
                        <?php
                        $facilities_list = [];
                        if ( ! empty( $facilities ) && is_array( $facilities ) ) {
                            foreach ( $facilities as $facility ) {
                                $facilities_list[ $facility['facilities-category'] ] = $facility['facilities-category'];
                            }
                        }

                        if ( ! empty( $facilities_list ) ) {
                            foreach ( $facilities_list as $key => $single_feature ) {
                                $f_icon_single = ! empty( $facilities_categories[ $key ]['hotel_facilities_cat_icon'] ) ? esc_attr( $facilities_categories[ $key ]['hotel_facilities_cat_icon'] ) : '';
                                ?>
                                <div class="hotel-facility-item">
                                    <div class="hotel-single-facility-title">
                                        <?php echo ! empty( $facilities_categories[ $key ]['hotel_facilities_cat_name'] ) ? esc_html( $facilities_categories[ $key ]['hotel_facilities_cat_name'] ) : ''; ?>
                                    </div>
                                    <ul>
                                        <?php
                                        foreach ( $facilities as $facility ) :
                                            if ( $facility['facilities-category'] == $key ) {
                                                $features_details = ! empty( $facility['facilities-feature'] ) ? get_term( $facility['facilities-feature'] ) : '';
                                                $feature_meta     = get_term_meta( $facility['facilities-feature'], 'tf_hotel_feature', true );

                                                $f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
                                                if ( 'fa' === $f_icon_type && ! empty( $feature_meta['icon-fa'] ) ) {
                                                    $feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
                                                } elseif ( 'c' === $f_icon_type && ! empty( $feature_meta['icon-c'] ) ) {
                                                    $feature_icon = '<img src="' . esc_url( $feature_meta['icon-c'] ) . '" style="width: ' . esc_attr( $feature_meta['dimention'] ) . 'px; height: ' . esc_attr( $feature_meta['dimention'] ) . 'px;" />';
                                                } else {
                                                    $feature_icon = '<i class="ri-check-line"></i>';
                                                }

                                                if ( ! empty( $features_details->name ) ) {
                                                    ?>
                                                    <li>
                                                        <span><?php echo ! empty( $feature_meta ) && ! empty( $feature_icon ) ? wp_kses_post( $feature_icon ) : ''; ?></span>
                                                        <?php echo esc_html( $features_details->name ); ?>
                                                    </li>
                                                    <?php
                                                }
                                            }
                                        endforeach;
                                        ?>
                                    </ul>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
				</div>
                <?php echo $container === 'yes' ? '</div>' : ''; ?>
			</div>
			<?php
		}

		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}

	/**
	 * Render apartment amenities
	 *
	 * @param array $settings Settings from widget
	 *
	 * @return void
	 */
	private static function render_apartment_amenities( $settings, $builder = '' ) {
		$post_id                = get_the_ID();
		$style                  = ! empty( $settings['amenities_style'] ) ? $settings['amenities_style'] : 'style1';
		$meta                   = get_post_meta( $post_id, 'tf_apartment_opt', true );
		$facilities_categories  = ! empty( Helper::tf_data_types( Helper::tfopt( 'amenities_cats' ) ) ) ? Helper::tf_data_types( Helper::tfopt( 'amenities_cats' ) ) : '';
		$facilities_title       = ! empty( $meta['amenities_title'] ) ? esc_html( $meta['amenities_title'] ) : esc_html__( 'What this place offers', 'tourfic' );
		$facilities             = ! empty( $meta['amenities'] ) ? Helper::tf_data_types( $meta['amenities'] ) : [];
        $wrapper_open           = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
        $wrapper_close          = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';

        echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( 'style1' === $style && isset( $facilities ) && ! empty( $facilities ) ) {
			?>
			<div class="tf-facilities-wrapper tf-single-amenities-style1" id="tf-apartment-facilities">
				<h2 class="tf-section-title"><?php echo esc_html( $facilities_title ); ?></h2>
				<div class="tf-facilities">
					<?php
					$facilities_list = [];
					if ( ! empty( $facilities ) && is_array( $facilities ) ) {
						foreach ( $facilities as $facility ) {
							$facilities_list[ $facility['cat'] ] = $facility['cat'];
						}
					}
					if ( ! empty( $facilities_list ) ) {
						foreach ( $facilities_list as $catkey => $single_feature ) {
							?>
							<div class="tf-facility-item">
								<?php $f_icon_single = ! empty( $facilities_categories[ $catkey ]['amenities_cat_icon'] ) ? $facilities_categories[ $catkey ]['amenities_cat_icon'] : ''; ?>
								<span class="single-facilities-title">
									<?php echo ! empty( $f_icon_single ) ? '<i class="' . esc_attr( $f_icon_single ) . '"></i>' : ''; ?>
									<?php echo ! empty( $facilities_categories[ $catkey ]['amenities_cat_name'] ) ? esc_html( $facilities_categories[ $catkey ]['amenities_cat_name'] ) : ''; ?>
								</span>
								<ul>
									<?php
									if ( ! empty( $facilities ) ) {
										foreach ( $facilities as $facility ) {
											if ( $facility['cat'] == $catkey ) {
												$features_details = get_term( $facility['feature'] );
												if ( ! empty( $features_details->name ) ) {
													?>
													<li>
														<?php echo esc_html( $features_details->name ); ?>
													</li>
													<?php
												}
											}
										}
									}
									?>
								</ul>
							</div>
							<?php
						}
					}
					?>
				</div>
			</div>
			<?php
		} elseif ( 'style2' === $style && isset( $facilities ) && ! empty( $facilities ) ) {
			$fav_amenities  = [];
			$other_amenities = [];
			foreach ( Helper::tf_data_types( $meta['amenities'] ) as $amenity ) {
				if ( ! isset( $amenity['favorite'] ) || '1' !== $amenity['favorite'] ) {
					$other_amenities[] = $amenity;
				} else {
					$fav_amenities[] = $amenity;
				}
			}
			$all_amenities = array_merge( $fav_amenities, $other_amenities );
			?>
			<div class="tf-apartment-amenities-section tf-single-apartment-amenities-style2">
				<h2 class="section-heading"><?php echo esc_html( $facilities_title ); ?></h2>
				<div class="tf-apartment-amenities-inner">
					<div class="tf-apartment-amenities">
						<?php if ( ! empty( $all_amenities ) ) :
							foreach ( array_slice( $all_amenities, 0, 10 ) as $amenity ) :
								$feature       = isset( $amenity['feature'] ) ? get_term_by( 'id', $amenity['feature'], 'apartment_feature' ) : '';
								$feature_meta  = isset( $amenity['feature'] ) ? get_term_meta( $amenity['feature'], 'tf_apartment_feature', true ) : '';
								$f_icon_type   = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
								if ( 'icon' === $f_icon_type ) {
									$feature_icon = '<i class="' . $feature_meta['apartment-feature-icon'] . '"></i>';
								} elseif ( 'custom' === $f_icon_type ) {
									$feature_icon = '<img src="' . esc_url( $feature_meta['apartment-feature-icon-custom'] ) . '" style="width: ' . esc_attr( $feature_meta['apartment-feature-icon-dimension'] ) . 'px; height: ' . esc_attr( $feature_meta['apartment-feature-icon-dimension'] ) . 'px;" />';
								}
								?>
								<div class="tf-apt-amenity">
									<?php echo ! empty( $feature_meta['apartment-feature-icon'] ) || ! empty( $feature_meta['apartment-feature-icon-custom'] ) ? '<div class="tf-apt-amenity-icon">' . wp_kses_post( $feature_icon ) . '</div>' : ''; ?>
									<?php if ( ! empty( $feature->name ) ) { ?>
										<span><?php echo esc_html( $feature->name ); ?></span>
									<?php } ?>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
					<?php if ( count( Helper::tf_data_types( $meta['amenities'] ) ) > 10 ) : ?>
						<div class="tf-apartment-amenities-more">
							<a class="tf-modal-btn" data-target="#tf-amenities-modal">
								<?php esc_html_e( 'All Amenities', 'tourfic' ); ?>
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
									<path d="M10.0252 4.22852L9.08457 5.17353L11.2647 7.34351L2.1947 7.35263L2.19604 8.68597L11.2412 8.67686L9.09779 10.8304L10.0428 11.771L13.8052 7.99092L10.0252 4.22852Z"
										fill="#2A3343"/>
								</svg>
							</a>
						</div>

						<!-- Modal -->
						<div class="tf-modal" id="tf-amenities-modal">
							<div class="tf-modal-dialog">
								<div class="tf-modal-content">
									<div class="tf-modal-header">
										<a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
									</div>
									<div class="tf-modal-body">
										<h2 class="section-heading"><?php echo ! empty( $meta['amenities_title'] ) ? esc_html( $meta['amenities_title'] ) : ''; ?></h2>
										<?php
										$categories     = [];
										$amenities_cats = ! empty( Helper::tf_data_types( Helper::tfopt( 'amenities_cats' ) ) ) ? Helper::tf_data_types( Helper::tfopt( 'amenities_cats' ) ) : '';
										foreach ( Helper::tf_data_types( $meta['amenities'] ) as $amenity ) {
											$cat     = $amenity['cat'];
											$feature = $amenity['feature'];

											// Check if the category exists in the $categories array
											if ( ! isset( $categories[ $cat ] ) ) {
												$categories[ $cat ] = [];
											}

											// Add the feature to the category
											$categories[ $cat ][] = $feature;
										}

										foreach ( $categories as $cat => $features ) :
											?>
											<div class="tf-apartment-amenity-cat">
												<h3><?php echo ! empty( $amenities_cats[ $cat ]['amenities_cat_name'] ) ? esc_html( $amenities_cats[ $cat ]['amenities_cat_name'] ) : ''; ?></h3>
												<div class="tf-apartment-amenities">
													<?php foreach ( $features as $feature_id ) :
														$_feature      = get_term_by( 'id', $feature_id, 'apartment_feature' );
														$_feature_meta = get_term_meta( $feature_id, 'tf_apartment_feature', true );
														$f_icon_type   = ! empty( $_feature_meta['icon-type'] ) ? $_feature_meta['icon-type'] : '';
														if ( 'icon' === $f_icon_type ) {
															$feature_icon = '<i class="' . $_feature_meta['apartment-feature-icon'] . '"></i>';
														} elseif ( 'custom' === $f_icon_type ) {
															$feature_icon = '<img src="' . esc_url( $_feature_meta['apartment-feature-icon-custom'] ) . '" style="width: ' . esc_attr( $_feature_meta['apartment-feature-icon-dimension'] ) . 'px; height: ' . esc_attr( $_feature_meta['apartment-feature-icon-dimension'] ) . 'px;" />';
														}
														?>
														<div class="tf-apt-amenity">
															<?php echo ! empty( $_feature_meta['apartment-feature-icon'] ) || ! empty( $_feature_meta['apartment-feature-icon-custom'] ) ? '<div class="tf-apt-amenity-icon">' . wp_kses_post( $feature_icon ) . '</div>' : ''; ?>
															<span><?php echo esc_html( $_feature->name ); ?></span>
														</div>
													<?php endforeach; ?>
												</div>
											</div>
										<?php endforeach; ?>

									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<?php
		}

        echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}
}

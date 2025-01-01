<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_icon' ) ) {
	class TF_icon extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );

			//tf_icon_modal method load single time
			static $tf_icon_modal;
			if ( ! $tf_icon_modal ) {
				$tf_icon_modal = true;
				add_action( 'admin_footer', array( $this, 'tf_icon_modal' ) );
			}
		}

		public function render() {

			$default       = isset( $this->field['default'] ) ? $this->field['default'] : '';
			$value         = $this->value ? $this->value : '';
			$preview_class = $value ? 'tf-icon-preview' : 'tf-icon-preview tf-hide';
			$uniqueid      = uniqid();
			?>
            <div class="tf-icon-select" id="tf-icon-<?php echo esc_attr( $this->field['id'] . $uniqueid ); ?>">
                <div class="<?php echo esc_attr( $preview_class ); ?>">
                    <span class="tf-icon-preview-wrap tf-modal-btn">
                        <i class="<?php echo esc_attr( $value ); ?>"></i>
                    </span>
                    <span class="remove-icon">
                        <i class="ri-close-line"></i>
                    </span>
                </div>
                <a href="#" class="tf-admin-btn tf-modal-btn"><i class="ri-add-fill"></i><?php esc_html_e( 'Add Icon', 'tourfic' ); ?></a>
                <input type="hidden" class="tf-icon-value" name="<?php echo esc_attr( $this->field_name() ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php echo wp_kses_post($this->field_attributes()) ?>/>
            </div>
			<?php
		}

		public function tf_icon_modal() {
			?>
            <div class="tf-modal" id="tf-icon-modal" data-icon-field="">
                <div class="tf-modal-dialog">
                    <div class="container tf-modal-content">
                        <div class="tf-modal-header">
                            <div class="tf-icon-search">
                                <input type="text" placeholder="<?php esc_html_e( 'Search', 'tourfic' ); ?>" class="tf-icon-search-input"/>
                            </div>
                            <a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
                        </div>
                        <div class="tf-modal-body">
                            <div class="tf-icon-wrapper">
                                <ul class="tf-icon-tab-list">
									<?php
									$count     = 0;
									$icon_list = $this->get_icon_list();
									foreach ( $icon_list as $key => $value ) :
										if ( $value['icons'] ):
											?>
                                            <li class="tf-icon-tab <?php echo $count == 0 ? 'active' : '' ?>" data-tab="tf-icon-tab-<?php echo esc_attr( $key ) ?>">
                                                <i class="<?php echo esc_attr( $value['label_icon'] ) ?>"></i><?php echo esc_html( $value['label'] ); ?>
                                            </li>
										<?php
										endif;
										$count ++;
									endforeach; ?>
                                </ul>
                                <div class="tf-icon-tab-content">
									<?php
									$count     = 0;
									$icon_list = $this->get_icon_list();
									foreach ( $icon_list as $key => $value ) :
										?>
                                        <div class="tf-icon-tab-pane <?php echo $count == 0 ? 'active' : '' ?>"
                                             id="tf-icon-tab-<?php echo esc_attr( $key ) ?>"
                                             data-type="<?php echo esc_attr( $key ) ?>"
                                             data-max="<?php echo esc_attr( count( $value['icons'] ) ) ?>"
                                        >
                                            <ul class="tf-icon-list">
												<?php
												if ( $value['icons'] ):
													$this->load_icons( $value['icons'], 0, 100 );
												endif; ?>
                                            </ul>
                                        </div>
										<?php $count ++;
									endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="tf-modal-footer">
                            <a class="tf-icon-insert tf-admin-btn tf-btn-secondary disabled"><?php esc_html_e( 'Insert', 'tourfic' ); ?></a>
                        </div>
                    </div>
                </div>
            </div>
			<?php
		}

		public function get_icon_list() {
			$icons = array(
				'all'           => array(
					'label'      => esc_html__( 'All Icons', 'tourfic' ),
					'label_icon' => 'ri-grid-fill',
					'icons'      => array_merge( fontawesome_four_icons(), fontawesome_five_icons(), fontawesome_six_icons(), remix_icon() ),
				),
				'fontawesome_4' => array(
					'label'      => esc_html__( 'Font Awesome 4', 'tourfic' ),
					'label_icon' => 'fa-regular fa-font-awesome',
					'icons'      => fontawesome_four_icons(),
				),
				'fontawesome_5' => array(
					'label'      => esc_html__( 'Font Awesome 5', 'tourfic' ),
					'label_icon' => 'fa-regular fa-font-awesome',
					'icons'      => fontawesome_five_icons(),
				),
				'fontawesome_6' => array(
					'label'      => esc_html__( 'Font Awesome 6', 'tourfic' ),
					'label_icon' => 'fa-regular fa-font-awesome',
					'icons'      => fontawesome_six_icons(),
				),
				'remixicon'     => array(
					'label'      => esc_html__( 'Remix Icon', 'tourfic' ),
					'label_icon' => 'ri-remixicon-line',
					'icons'      => remix_icon(),
				),
			);

			$icons = apply_filters( 'tf_icon_list', $icons );

			return $icons;
		}

		function load_icons( $icons, $start_index, $count ) {
			$limited_icons = array_slice( $icons, $start_index, $count );
			foreach ( $limited_icons as $key => $icon ) {
				?>
                <li data-icon="<?php echo esc_attr( $icon ); ?>">
                    <div class="tf-icon-inner">
                        <i title="<?php echo esc_attr( $icon ); ?>" class="tf-main-icon <?php echo esc_attr( $icon ); ?>"></i>

                        <span class="check-icon">
                            <i class="ri-check-line"></i>
                        </span>
                    </div>
                </li>
				<?php
			}
		}
	}
}
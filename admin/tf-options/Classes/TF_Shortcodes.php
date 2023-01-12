<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if( ! class_exists( 'TF_Shortcodes' )){
    class TF_Shortcodes{

        private static $instance = null;

		/**
		 * Singleton instance
		 * @since 1.0.0
		 */
		public static function instance() {
			if ( self::$instance == null ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

        public function __construct( ){
            //register admin menus
            //add_action( 'admin_menu', array( $this, 'tf_submenu') );
        }

        public static function tf_shortcode_callback()
        {
            echo '<div class="tf-setting-dashboard">';
            //dashboard-header-include
            echo tf_dashboard_header();
            ?>
            <div class="tf-shortcode-generator-section">
                <div class="tf-shortcode-generators">
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label>Tours</label>
                                <p><?php echo __( 'Display tours in specific location', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo __( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close">X</div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Tour', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_tour"><?php _e( 'Tour', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Tour Count', 'tourfic' ) ?></h3>
                                            <input type="number" data-count="count" class="post-count tf-setting-field">                                                
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Tour style', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="style='grid'"><?php _e( 'Grid', 'tourfic' ); ?></option>
                                                <option value="style='slider'"><?php _e( 'Slider', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                    <div class="tf-generate-tour">
                                        <button class="tf-btn"><?php echo __( 'Generate', 'tourfic' ); ?></button>
                                    </div>
                                    </div>
                                </div>
                                <div class="tf-shortcode-field copy-shortcode">
                                    <input type="text" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly />
                                    <button type="button" class="tf-copy-btn tf-btn">
                                        <span class="dashicons dashicons-editor-code"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

    }
}
TF_Shortcodes::instance();
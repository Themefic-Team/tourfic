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
                                <label><?php echo __( 'Tours','tourfic' );?></label>
                                <p><?php echo __( 'Display tours in specific location', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo __( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="fa-thin fa-x"></i></div>
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
                                            <input type="number" value="5" data-count="count" class="post-count tf-setting-field">                                                
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Tour', 'tourfic' ) ?></h3>
                                            <?php
                                            //Dynamic Taxonomy dropdown list
                                            tf_terms_dropdown( 'tour_destination','destinations','tf-setting-field tf-select-field',true );
                                            ?>
                                        </div>
                                    </div>
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
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly />
                                        <button type="button" class="tf-copy-btn tf-btn">
                                            <span class="dashicons dashicons-category"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Hotel locations Shortcodes section-->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo __( 'Hotel Location','tourfic' );?></label>
                                <p><?php echo __( 'Display hotel locations', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo __( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="fa-thin fa-x"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Hotel Locations', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="hotel_locations"><?php _e( 'Hotel Locations', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Orderby', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="orderby='name'"><?php _e( 'Name', 'tourfic' ); ?></option>
                                                <option value="orderby='title'"><?php _e( 'Title', 'tourfic' ); ?></option>
                                                <option value="orderby='date'"><?php _e( 'Date', 'tourfic' ); ?></option>
                                                <option value="orderby='ID'"><?php _e( 'ID', 'tourfic' ); ?></option>
                                                <option value="orderby='rand'"><?php _e( 'Rand', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>                                   
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Order', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="order='ASC'"><?php _e( 'ASC', 'tourfic' ); ?></option>
                                                <option value="order='DESC'"><?php _e( 'DESC', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Location limit', 'tourfic' ) ?></h3>
                                            <input type="number" value="-1" data-count="limit" class="post-count tf-setting-field">                                                
                                        </div>
                                    </div>                             
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Choose Locations', 'tourfic' ) ?></h3>
                                            <?php
                                            //Dynamic Taxonomy dropdown list
                                            tf_terms_dropdown( 'hotel_location','ids','tf-setting-field tf-select-field',true );
                                            ?>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Hide Empty', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="hide_empty='0'"><?php _e( 'No', 'tourfic' ); ?></option>
                                                <option value="hide_empty='1'"><?php _e( 'Yes', 'tourfic' ); ?></option>
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
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly />
                                        <button type="button" class="tf-copy-btn tf-btn">
                                            <span class="dashicons dashicons-category"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Tour Destinations Shortcodes section-->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo __( 'Tour Destination','tourfic' );?></label>
                                <p><?php echo __( 'Display tour destinations', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo __( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="fa-thin fa-x"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Tour Destinations', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tour_destinations"><?php _e( 'Tour Destinations', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Orderby', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="orderby='name'"><?php _e( 'Name', 'tourfic' ); ?></option>
                                                <option value="orderby='title'"><?php _e( 'Title', 'tourfic' ); ?></option>
                                                <option value="orderby='date'"><?php _e( 'Date', 'tourfic' ); ?></option>
                                                <option value="orderby='ID'"><?php _e( 'ID', 'tourfic' ); ?></option>
                                                <option value="orderby='rand'"><?php _e( 'Rand', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>                                   
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Order', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="order='ASC'"><?php _e( 'ASC', 'tourfic' ); ?></option>
                                                <option value="order='DESC'"><?php _e( 'DESC', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Destination limit', 'tourfic' ) ?></h3>
                                            <input type="number" value="-1" data-count="limit" class="post-count tf-setting-field">                                                
                                        </div>
                                    </div>                             
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Choose Destination', 'tourfic' ) ?></h3>
                                            <?php
                                            //Dynamic Taxonomy dropdown list
                                            tf_terms_dropdown( 'tour_destination','ids','tf-setting-field tf-select-field',true );
                                            ?>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Hide Empty', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="hide_empty='0'"><?php _e( 'No', 'tourfic' ); ?></option>
                                                <option value="hide_empty='1'"><?php _e( 'Yes', 'tourfic' ); ?></option>
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
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly />
                                        <button type="button" class="tf-copy-btn tf-btn">
                                            <span class="dashicons dashicons-category"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                      <!--Recent hotels Shortcodes section-->
                      <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo __( 'Recent hotels','tourfic' );?></label>
                                <p><?php echo __( 'Display recent hotels', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo __( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="fa-thin fa-x"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Hotels', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_recent_hotel"><?php _e( 'Recent Hotel', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Orderby', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="orderby='name'"><?php _e( 'Name', 'tourfic' ); ?></option>
                                                <option value="orderby='title'"><?php _e( 'Title', 'tourfic' ); ?></option>
                                                <option value="orderby='date'"><?php _e( 'Date', 'tourfic' ); ?></option>
                                                <option value="orderby='ID'"><?php _e( 'ID', 'tourfic' ); ?></option>
                                                <option value="orderby='rand'"><?php _e( 'Rand', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>                                   
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Order', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="order='ASC'"><?php _e( 'ASC', 'tourfic' ); ?></option>
                                                <option value="order='DESC'"><?php _e( 'DESC', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Hotel limit', 'tourfic' ) ?></h3>
                                            <input type="number" value="-1" data-count="count" class="post-count tf-setting-field">                                                
                                        </div>
                                    </div>                             
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Slides to show', 'tourfic' ) ?></h3>
                                            <input type="number" value="5" data-count="slidestoshow" class="post-count tf-setting-field">                                                
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
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly />
                                        <button type="button" class="tf-copy-btn tf-btn">
                                            <span class="dashicons dashicons-category"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Recent tour Shortcodes section-->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo __( 'Recent Tours','tourfic' );?></label>
                                <p><?php echo __( 'Display recent tours', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo __( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="fa-thin fa-x"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Recent Tours', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_recent_tour"><?php _e( 'Recent tour', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Orderby', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="orderby='name'"><?php _e( 'Name', 'tourfic' ); ?></option>
                                                <option value="orderby='title'"><?php _e( 'Title', 'tourfic' ); ?></option>
                                                <option value="orderby='date'"><?php _e( 'Date', 'tourfic' ); ?></option>
                                                <option value="orderby='ID'"><?php _e( 'ID', 'tourfic' ); ?></option>
                                                <option value="orderby='rand'"><?php _e( 'Rand', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>                                   
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Order', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="order='ASC'"><?php _e( 'ASC', 'tourfic' ); ?></option>
                                                <option value="order='DESC'"><?php _e( 'DESC', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Tour limit', 'tourfic' ) ?></h3>
                                            <input type="number" value="-1" data-count="count" class="post-count tf-setting-field">                                                
                                        </div>
                                    </div>                             
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Slides to show', 'tourfic' ) ?></h3>
                                            <input type="number" value="5" data-count="slidestoshow" class="post-count tf-setting-field">                                                
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
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly />
                                        <button type="button" class="tf-copy-btn tf-btn">
                                            <span class="dashicons dashicons-category"></span>
                                        </button>
                                    </div>
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
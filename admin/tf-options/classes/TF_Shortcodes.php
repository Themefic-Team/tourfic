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
        
        /**
         * All shortcode generator callback function
         * @author Abu Hena
         * @since 2.9.5
         */
        public static function tf_shortcode_callback()
        {
            echo '<div class="tf-setting-dashboard">';
            //dashboard-header-include
            echo tf_dashboard_header();
            ?>
            <div class="tf-shortcode-generator-section">
                <div class="tf-shortcode-generators">
                    <!--Tours Shortcodes section-->
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
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
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
                                            <h3><?php echo __( 'Tour Destination', 'tourfic' ) ?></h3>
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
                    <!--Hotels Shortcodes section-->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo __( 'Hotels','tourfic' );?></label>
                                <p><?php echo __( 'Display Hotels in specific location', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo __( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Hotels', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_hotel"><?php _e( 'Hotels', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Hotel Count', 'tourfic' ) ?></h3>
                                            <input type="number" value="5" data-count="count" class="post-count tf-setting-field">                                                
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Hotel location', 'tourfic' ) ?></h3>
                                            <?php
                                            //Dynamic Taxonomy dropdown list
                                            tf_terms_dropdown( 'hotel_location','locations','tf-setting-field tf-select-field',true );
                                            ?>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Hotel style', 'tourfic' ) ?></h3>
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
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
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
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
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
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
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
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
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

                    <!--Search form Shortcodes section-->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo __( 'Search Form','tourfic' );?></label>
                                <p><?php echo __( 'Display Search Form', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo __( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Search Form', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_search_form"><?php _e( 'Search form', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Style', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="style='default'"><?php _e( 'Default', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>                             
                                </div>
                                <div class="tf-sg-row">                                    
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Type', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="type='all'"><?php _e( 'All', 'tourfic' ); ?></option>
                                                <option value="type='hotel'"><?php _e( 'Hotel', 'tourfic' ); ?></option>
                                                <option value="type='tour'"><?php _e( 'Tour', 'tourfic' ); ?></option>
                                                <option value="type='booking'"><?php _e( 'Booking', 'tourfic' ); ?></option>
                                                <option value="type='tp-hotel'"><?php _e( 'Travel Payout Hotels', 'tourfic' ); ?></option>
                                                <option value="type='tp-flight'"><?php _e( 'Travel Payout Flights', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>      
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Fullwidth', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="fullwidth='true'"><?php _e( 'Yes', 'tourfic' ); ?></option>
                                                <option value="fullwidth='false'"><?php _e( 'No', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div> 
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Classes', 'tourfic' ) ?></h3>
                                            <input type="text" value="" data-count="classes" placeholder="Input classes with space" class="post-count tf-setting-field">                                                
                                        </div>
                                    </div>                                         
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Advanced', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="advanced='disabled'"><?php _e( 'Disabled', 'tourfic' ); ?></option>
                                                <option value="advanced='enabled'"><?php _e( 'Enabled', 'tourfic' ); ?></option>
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

                    <!--Reviews Shortcodes section-->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo __( 'Reviews','tourfic' );?></label>
                                <p><?php echo __( 'Display reviews', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo __( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Reviews', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_reviews"><?php _e( 'Reviews', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Type', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="type=tf_hotel"><?php _e( 'Hotel', 'tourfic' ); ?></option>
                                                <option value="type=tf_tours"><?php _e( 'Tours', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>                                                                  
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Total Post Number', 'tourfic' ) ?></h3>
                                            <input type="number" value="10" data-count="number" class="post-count tf-setting-field">                                                
                                        </div>
                                    </div>  
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Sliders To Show', 'tourfic' ) ?></h3>
                                            <input type="number" value="3" data-count="count" class="post-count tf-setting-field">                                                
                                        </div>
                                    </div>                                                                  
                                </div>  
                                    
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Speed', 'tourfic' ) ?></h3>
                                            <input type="number" value="200" data-count="speed" class="post-count tf-setting-field">                                                
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Arrows', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="arrows='true'"><?php _e( 'Yes', 'tourfic' ); ?></option>
                                                <option value="arrows='false'"><?php _e( 'No', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                    <div class="tf-sg-row">
                                        <div class="tf-col-lg-6">
                                            <div class="tf-sg-field-wrap">
                                                <h3><?php echo __( 'Dots', 'tourfic' ) ?></h3>
                                                <select class="tf-select-field tf-setting-field">
                                                    <option value="dots='true'"><?php _e( 'Yes', 'tourfic' ); ?></option>
                                                    <option value="dots='false'"><?php _e( 'No', 'tourfic' ); ?></option>
                                                </select>
                                            </div>
                                        </div>     
                                        <div class="tf-col-lg-6">
                                            <div class="tf-sg-field-wrap">
                                                <h3><?php echo __( 'Autoplay', 'tourfic' ) ?></h3>
                                                <select class="tf-select-field tf-setting-field">
                                                    <option value="autoplay='true'"><?php _e( 'Yes', 'tourfic' ); ?></option>
                                                    <option value="autoplay='false'"><?php _e( 'No', 'tourfic' ); ?></option>
                                                </select>
                                            </div>
                                        </div>          
                                        <div class="tf-col-lg-6">
                                            <div class="tf-sg-field-wrap">
                                                <h3><?php echo __( 'Infinite', 'tourfic' ) ?></h3>
                                                <select class="tf-select-field tf-setting-field">
                                                    <option value="infinite='true'"><?php _e( 'Yes', 'tourfic' ); ?></option>
                                                    <option value="infinite='false'"><?php _e( 'No', 'tourfic' ); ?></option>
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

                    <!--Recent blog Shortcodes section-->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo __( 'Recent blog','tourfic' );?></label>
                                <p><?php echo __( 'Display Recent Blogs in specific location', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo __( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Recent Blogs', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_recent_blog"><?php _e( 'Recent Blogs', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Blog Count', 'tourfic' ) ?></h3>
                                            <input type="number" value="5" data-count="count" class="post-count tf-setting-field">                                                
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Categories', 'tourfic' ) ?></h3>
                                            <?php
                                            //Dynamic Taxonomy dropdown list
                                            tf_terms_dropdown( 'category','cats','tf-setting-field tf-select-field',true );
                                            ?>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Blog style', 'tourfic' ) ?></h3>
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

                    <!--Vendor Post Genarate Shortcodes section-->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo __( 'Vendor Hotels & Tours','tourfic' );?></label>
                                <p><?php echo __( 'Display Hotels & Tours in specific Vendor', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo __( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Vendor Posts', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_vendor_post"><?php _e( 'Vendor Posts', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Type', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="type='tf_hotel'"><?php _e( 'Hotel', 'tourfic' ); ?></option>
                                                <option value="type='tf_tours'"><?php _e( 'Tour', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Number of Posts', 'tourfic' ) ?></h3>
                                            <input type="number" value="4" data-count="count" class="post-count tf-setting-field">                     
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo __( 'Vendor Name', 'tourfic' ) ?></h3>
                                            <?php 
                                            $tf_vendor_query_lists = get_users( array( 'role__in' => array( 'tf_vendor', 'administrator' ) ) );
                                            if(!empty($tf_vendor_query_lists)){
                                            ?>
                                            <select class="tf-select-field tf-setting-field">
                                                <?php 
                                                foreach($tf_vendor_query_lists as $single){ ?>
                                                <option value="vendor='<?php echo $single->user_nicename; ?>' vendor_id='<?php echo $single->ID; ?>' "><?php echo $single->user_nicename; ?></option>
                                                <?php } ?>
                                            </select>
                                            <?php }else{
                                                echo __( 'Not Found', 'tourfic' );
                                            }
                                            ?>
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
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_vendor_post]" readonly />
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
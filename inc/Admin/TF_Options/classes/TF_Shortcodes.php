<?php
// don't load directly
defined( 'ABSPATH' ) || exit;
use \Tourfic\Classes\Helper;

if ( ! class_exists( 'TF_Shortcodes' ) ) {
	class TF_Shortcodes {

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
		public static function tf_shortcode_callback() {
			echo '<div class="tf-setting-dashboard">';
			//dashboard-header-include
			\Tourfic\Classes\Helper::tf_dashboard_header();
			?>
            <div class="tf-shortcode-generator-section">
                <div class="tf-shortcode-generators">

                    <!-- Common shortcode Section -->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label shortcode-section-heading">
                            <h2><?php echo esc_html__('General Shortcodes', 'tourfic'); ?></h2>
                        </div>
                    </div>

                    <!--Search form Shortcodes section-->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo esc_html__( 'Search Form', 'tourfic' ); ?></label>
                                <p><?php echo esc_html__( 'Display Search Form', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo esc_html__( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Search Form', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_search_form"><?php esc_html_e( 'Search form', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Style', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="style='default'"><?php esc_html_e( 'Default', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Type', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="type='all'"><?php esc_html_e( 'All', 'tourfic' ); ?></option>
                                                <option value="type='hotel'"><?php esc_html_e( 'Hotel', 'tourfic' ); ?></option>
                                                <option value="type='tour'"><?php esc_html_e( 'Tour', 'tourfic' ); ?></option>
                                                <option value="type='apartment'"><?php esc_html_e( 'Apartment', 'tourfic' ); ?></option>
                                                <option value="type='booking'"><?php esc_html_e( 'Booking', 'tourfic' ); ?></option>
                                                <option value="type='tp-hotel'"><?php esc_html_e( 'Travel Payout Hotels', 'tourfic' ); ?></option>
                                                <option value="type='tp-flight'"><?php esc_html_e( 'Travel Payout Flights', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Fullwidth', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="fullwidth='true'"><?php esc_html_e( 'Yes', 'tourfic' ); ?></option>
                                                <option value="fullwidth='false'"><?php esc_html_e( 'No', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Title', 'tourfic' ) ?></h3>
                                            <input type="text" value data-title="title" placeholder="Enter a Title" class="tf-shortcode-title-field tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Subtitle', 'tourfic' ) ?></h3>
                                            <input type="text" value data-subtitle="subtitle" placeholder="Enter a Subtitle" class="tf-shortcode-subtitle-field tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Classes', 'tourfic' ) ?></h3>
                                            <input type="text" value="" data-count="classes" placeholder="Input classes with space" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Advanced', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="advanced='disabled'"><?php esc_html_e( 'Disabled', 'tourfic' ); ?></option>
                                                <option value="advanced='enabled'"><?php esc_html_e( 'Enabled', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-4">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Tour Tab Title', 'tourfic' ) ?></h3>
                                            <input type="text" value="" data-tour-tab-title="tour_tab_title" placeholder="Input the title here." class="tf-shortcode-tour-tab-title-field tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-4">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Hotel Tab Title', 'tourfic' ) ?></h3>
                                            <input type="text" value="" data-hotel-tab-title="hotel_tab_title" placeholder="Input the title here." class="tf-shortcode-hotel-tab-title-field tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-4">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Apartment Tab Title', 'tourfic' ) ?></h3>
                                            <input type="text" value="" data-apartment-tab-title="apartment_tab_title" placeholder="Input the title here." class="tf-shortcode-apartment-tab-title-field tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-generate-tour">
                                            <button class="tf-btn"><?php echo esc_html__( 'Generate', 'tourfic' ); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly/>
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
                                <label><?php echo esc_html__( 'Reviews', 'tourfic' ); ?></label>
                                <p><?php echo esc_html__( 'Display reviews', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo esc_html__( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Reviews', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_reviews"><?php esc_html_e( 'Reviews', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Type', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="type=tf_hotel"><?php esc_html_e( 'Hotel', 'tourfic' ); ?></option>
                                                <option value="type=tf_tours"><?php esc_html_e( 'Tours', 'tourfic' ); ?></option>
                                                <option value="type=tf_apartment"><?php esc_html_e( 'Apartment', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Total Post Number', 'tourfic' ) ?></h3>
                                            <input type="number" value="10" data-count="number" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Sliders To Show', 'tourfic' ) ?></h3>
                                            <input type="number" value="3" data-count="count" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                </div>

                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Speed', 'tourfic' ) ?></h3>
                                            <input type="number" value="2000" data-count="speed" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Arrows', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="arrows='true'"><?php esc_html_e( 'Yes', 'tourfic' ); ?></option>
                                                <option value="arrows='false'"><?php esc_html_e( 'No', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Dots', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="dots='true'"><?php esc_html_e( 'Yes', 'tourfic' ); ?></option>
                                                <option value="dots='false'"><?php esc_html_e( 'No', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Autoplay', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="autoplay='true'"><?php esc_html_e( 'Yes', 'tourfic' ); ?></option>
                                                <option value="autoplay='false'"><?php esc_html_e( 'No', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Infinite', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="infinite='true'"><?php esc_html_e( 'Yes', 'tourfic' ); ?></option>
                                                <option value="infinite='false'"><?php esc_html_e( 'No', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-generate-tour">
                                            <button class="tf-btn"><?php echo esc_html__( 'Generate', 'tourfic' ); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly/>
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
                                <label><?php echo esc_html__( 'Recent blog', 'tourfic' ); ?></label>
                                <p><?php echo esc_html__( 'Display Recent Blogs in specific location', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo esc_html__( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Recent Blogs', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_recent_blog"><?php esc_html_e( 'Recent Blogs', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Blog Count', 'tourfic' ) ?></h3>
                                            <input type="number" value="5" data-count="count" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Categories', 'tourfic' ) ?></h3>
							                <?php
							                //Dynamic Taxonomy dropdown list
							                Helper::tf_terms_dropdown( 'category', 'cats', 'tf_recent_blog_shortcode', 'tf-setting-field tf-select-field', true );
							                ?>
                                        </div>
                                    </div>
                                    <!--<div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php /*echo esc_html__( 'Blog style', 'tourfic' ) */?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="style='grid'"><?php /*esc_html_e( 'Grid', 'tourfic' ); */?></option>
                                                <option value="style='slider'"><?php /*esc_html_e( 'Slider', 'tourfic' ); */?></option>
                                            </select>
                                        </div>
                                    </div>-->
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-generate-tour">
                                            <button class="tf-btn"><?php echo esc_html__( 'Generate', 'tourfic' ); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly/>
                                        <button type="button" class="tf-copy-btn tf-btn">
                                            <span class="dashicons dashicons-category"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Tours Shortcodes section-->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label shortcode-section-heading">
                            <h2><?php echo esc_html__('Shortcode for Tours', 'tourfic'); ?></h2>
                        </div>
                    </div>
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo esc_html__( 'Tours', 'tourfic' ); ?></label>
                                <p><?php echo esc_html__( 'Display tours in specific location', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo esc_html__( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Tour', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_tour"><?php esc_html_e( 'Tour', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Tour Count', 'tourfic' ) ?></h3>
                                            <input type="number" value="5" data-count="count" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Tour Destination', 'tourfic' ) ?></h3>
											<?php
											//Dynamic Taxonomy dropdown list
											Helper::tf_terms_dropdown( 'tour_destination', 'destinations', 'tf_tour_shortcode','tf-setting-field tf-select-field', true );
											?>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Tour style', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="style='grid'"><?php esc_html_e( 'Grid', 'tourfic' ); ?></option>
                                                <option value="style='slider'"><?php esc_html_e( 'Slider', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                 <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Title', 'tourfic' ) ?></h3>
                                            <input type="text" value data-title="title" placeholder="Enter a Title" class="tf-shortcode-title-field tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Subtitle', 'tourfic' ) ?></h3>
                                            <input type="text" value data-subtitle="subtitle" placeholder="Enter a Subtitle" class="tf-shortcode-subtitle-field tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-generate-tour">
                                            <button class="tf-btn"><?php echo esc_html__( 'Generate', 'tourfic' ); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly/>
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
                                <label><?php echo esc_html__( 'Tour Destination', 'tourfic' ); ?></label>
                                <p><?php echo esc_html__( 'Display tour destinations', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo esc_html__( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Tour Destinations', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tour_destinations"><?php esc_html_e( 'Tour Destinations', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Orderby', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="orderby='name'"><?php esc_html_e( 'Name', 'tourfic' ); ?></option>
                                                <option value="orderby='title'"><?php esc_html_e( 'Title', 'tourfic' ); ?></option>
                                                <option value="orderby='date'"><?php esc_html_e( 'Date', 'tourfic' ); ?></option>
                                                <option value="orderby='ID'"><?php esc_html_e( 'ID', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Order', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="order='ASC'"><?php esc_html_e( 'ASC', 'tourfic' ); ?></option>
                                                <option value="order='DESC'"><?php esc_html_e( 'DESC', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Destination limit', 'tourfic' ) ?></h3>
                                            <input type="number" value="-1" data-count="limit" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Choose Destination', 'tourfic' ) ?></h3>
											<?php
											//Dynamic Taxonomy dropdown list
											Helper::tf_terms_dropdown( 'tour_destination', 'ids', 'tf_tour_destination_shortcode', 'tf-setting-field tf-select-field', true );
											?>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Hide Empty', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="hide_empty='0'"><?php esc_html_e( 'No', 'tourfic' ); ?></option>
                                                <option value="hide_empty='1'"><?php esc_html_e( 'Yes', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-generate-tour">
                                            <button class="tf-btn"><?php echo esc_html__( 'Generate', 'tourfic' ); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly/>
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
                                <label><?php echo esc_html__( 'Recent Tours', 'tourfic' ); ?></label>
                                <p><?php echo esc_html__( 'Display recent tours', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo esc_html__( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Recent Tours', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_recent_tour"><?php esc_html_e( 'Recent tour', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Orderby', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="orderby='name'"><?php esc_html_e( 'Name', 'tourfic' ); ?></option>
                                                <option value="orderby='title'"><?php esc_html_e( 'Title', 'tourfic' ); ?></option>
                                                <option value="orderby='date'"><?php esc_html_e( 'Date', 'tourfic' ); ?></option>
                                                <option value="orderby='ID'"><?php esc_html_e( 'ID', 'tourfic' ); ?></option>
                                                <option value="orderby='rand'"><?php esc_html_e( 'Rand', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Title', 'tourfic' ) ?></h3>
                                            <input type="text" value data-title="title" placeholder="Enter a Title" class="tf-shortcode-title-field tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Subtitle', 'tourfic' ) ?></h3>
                                            <input type="text" value data-subtitle="subtitle" placeholder="Enter a Subtitle" class="tf-shortcode-subtitle-field tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Order', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="order='ASC'"><?php esc_html_e( 'ASC', 'tourfic' ); ?></option>
                                                <option value="order='DESC'"><?php esc_html_e( 'DESC', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Tour limit', 'tourfic' ) ?></h3>
                                            <input type="number" value="-1" data-count="count" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Slides to show', 'tourfic' ) ?></h3>
                                            <input type="number" value="5" data-count="slidestoshow" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-generate-tour">
                                            <button class="tf-btn"><?php echo esc_html__( 'Generate', 'tourfic' ); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly/>
                                        <button type="button" class="tf-copy-btn tf-btn">
                                            <span class="dashicons dashicons-category"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--External Listing Shortcodes section-->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo esc_html__( 'External Listing', 'tourfic' ); ?></label>
                                <p><?php echo esc_html__( 'Display external listing', 'tourfic' ); ?>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo esc_html__( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'External Listing', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_tour_external_listings"><?php esc_html_e( 'External Listing', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Title', 'tourfic' ) ?></h3>
                                            <input type="text" value data-title="title" placeholder="Enter a Title" class="tf-shortcode-title-field tf-setting-field">
                                        </div>
                                    </div>
                                </div>

                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Subtitle', 'tourfic' ) ?></h3>
                                            <input type="text" value data-subtitle="subtitle" placeholder="Enter a Subtitle" class="tf-shortcode-subtitle-field tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Choose Locations', 'tourfic' ) ?></h3>
							                <?php
							                //Dynamic Taxonomy dropdown list
							                Helper::tf_terms_dropdown( 'tour_destination', 'locations', 'tf_listing_location_shortcode', 'tf-setting-field tf-select-field', true );
							                ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Total Post Number', 'tourfic' ) ?></h3>
                                            <input type="number" value="5" data-count="count" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Post Style', 'tourfic' ) ?></h3>

                                            <select class="tf-select-field tf-setting-field">
                                                <option value="style='grid'"><?php esc_html_e( 'Grid', 'tourfic' ); ?></option>
                                                <option value="style='slider'"><?php esc_html_e( 'Slider', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-generate-tour">
                                            <button class="tf-btn"><?php echo esc_html__( 'Generate', 'tourfic' ); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly/>
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
                        <div class="tf-shortcode-generator-label shortcode-section-heading">
                            <h2><?php echo esc_html__('Shortcode for Hotels', 'tourfic'); ?></h2>
                        </div>
                    </div>
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo esc_html__( 'Hotels', 'tourfic' ); ?></label>
                                <p><?php echo esc_html__( 'Display Hotels in specific location', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo esc_html__( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Hotels', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_hotel"><?php esc_html_e( 'Hotels', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Hotel Count', 'tourfic' ) ?></h3>
                                            <input type="number" value="5" data-count="count" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Hotel location', 'tourfic' ) ?></h3>
											<?php
											//Dynamic Taxonomy dropdown list
											Helper::tf_terms_dropdown( 'hotel_location', 'locations', 'tf_hotel_shortcode', 'tf-setting-field tf-select-field', true );
											?>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Hotel style', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="style='grid'"><?php esc_html_e( 'Grid', 'tourfic' ); ?></option>
                                                <option value="style='slider'"><?php esc_html_e( 'Slider', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Title', 'tourfic' ) ?></h3>
                                            <input type="text" value data-title="title" placeholder="Enter a Title" class="tf-shortcode-title-field tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Subtitle', 'tourfic' ) ?></h3>
                                            <input type="text" value data-subtitle="subtitle" placeholder="Enter a Subtitle" class="tf-shortcode-subtitle-field tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-generate-tour">
                                            <button class="tf-btn"><?php echo esc_html__( 'Generate', 'tourfic' ); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly/>
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
                                <label><?php echo esc_html__( 'Hotel Location', 'tourfic' ); ?></label>
                                <p><?php echo esc_html__( 'Display hotel locations', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo esc_html__( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Hotel Locations', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="hotel_locations"><?php esc_html_e( 'Hotel Locations', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Orderby', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="orderby='name'"><?php esc_html_e( 'Name', 'tourfic' ); ?></option>
                                                <option value="orderby='title'"><?php esc_html_e( 'Title', 'tourfic' ); ?></option>
                                                <option value="orderby='date'"><?php esc_html_e( 'Date', 'tourfic' ); ?></option>
                                                <option value="orderby='ID'"><?php esc_html_e( 'ID', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Order', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="order='ASC'"><?php esc_html_e( 'ASC', 'tourfic' ); ?></option>
                                                <option value="order='DESC'"><?php esc_html_e( 'DESC', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Location limit', 'tourfic' ) ?></h3>
                                            <input type="number" value="-1" data-count="limit" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Choose Locations', 'tourfic' ) ?></h3>
											<?php
											//Dynamic Taxonomy dropdown list
											Helper::tf_terms_dropdown( 'hotel_location', 'ids', 'tf_hotel_location_shortcode', 'tf-setting-field tf-select-field', true );
											?>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Hide Empty', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="hide_empty='0'"><?php esc_html_e( 'No', 'tourfic' ); ?></option>
                                                <option value="hide_empty='1'"><?php esc_html_e( 'Yes', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-generate-tour">
                                            <button class="tf-btn"><?php echo esc_html__( 'Generate', 'tourfic' ); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly/>
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
                                <label><?php echo esc_html__( 'Recent Hotels', 'tourfic' ); ?></label>
                                <p><?php echo esc_html__( 'Display recent hotels', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo esc_html__( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Hotels', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_recent_hotel"><?php esc_html_e( 'Recent Hotel', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Orderby', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="orderby='name'"><?php esc_html_e( 'Name', 'tourfic' ); ?></option>
                                                <option value="orderby='title'"><?php esc_html_e( 'Title', 'tourfic' ); ?></option>
                                                <option value="orderby='date'"><?php esc_html_e( 'Date', 'tourfic' ); ?></option>
                                                <option value="orderby='ID'"><?php esc_html_e( 'ID', 'tourfic' ); ?></option>
                                                <option value="orderby='rand'"><?php esc_html_e( 'Rand', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div> 
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Title', 'tourfic' ) ?></h3>
                                            <input type="text" value data-title="title" placeholder="Enter a Title" class="tf-shortcode-title-field tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Subtitle', 'tourfic' ) ?></h3>
                                            <input type="text" value data-subtitle="subtitle" placeholder="Enter a Subtitle" class="tf-shortcode-subtitle-field tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Order', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="order='ASC'"><?php esc_html_e( 'ASC', 'tourfic' ); ?></option>
                                                <option value="order='DESC'"><?php esc_html_e( 'DESC', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Hotel limit', 'tourfic' ) ?></h3>
                                            <input type="number" value="-1" data-count="count" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Slides to show', 'tourfic' ) ?></h3>
                                            <input type="number" value="5" data-count="slidestoshow" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-generate-tour">
                                            <button class="tf-btn"><?php echo esc_html__( 'Generate', 'tourfic' ); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly/>
                                        <button type="button" class="tf-copy-btn tf-btn">
                                            <span class="dashicons dashicons-category"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--External Listing Shortcodes section-->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo esc_html__( 'External Listing', 'tourfic' ); ?></label>
                                <p><?php echo esc_html__( 'Display external listing', 'tourfic' ); ?>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo esc_html__( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'External Listing', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_hotel_external_listings"><?php esc_html_e( 'External Listing', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Title', 'tourfic' ) ?></h3>
                                            <input type="text" value data-title="title" placeholder="Enter a Title" class="tf-shortcode-title-field tf-setting-field">
                                        </div>
                                    </div>
                                </div>

                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Subtitle', 'tourfic' ) ?></h3>
                                            <input type="text" value data-subtitle="subtitle" placeholder="Enter a Subtitle" class="tf-shortcode-subtitle-field tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Choose Locations', 'tourfic' ) ?></h3>
							                <?php
							                //Dynamic Taxonomy dropdown list
							                Helper::tf_terms_dropdown( 'hotel_location', 'locations', 'tf_listing_location_shortcode', 'tf-setting-field tf-select-field', true );
							                ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Total Post Number', 'tourfic' ) ?></h3>
                                            <input type="number" value="5" data-count="count" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Post Style', 'tourfic' ) ?></h3>

                                            <select class="tf-select-field tf-setting-field">
                                                <option value="style='grid'"><?php esc_html_e( 'Grid', 'tourfic' ); ?></option>
                                                <option value="style='slider'"><?php esc_html_e( 'Slider', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-generate-tour">
                                            <button class="tf-btn"><?php echo esc_html__( 'Generate', 'tourfic' ); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly/>
                                        <button type="button" class="tf-copy-btn tf-btn">
                                            <span class="dashicons dashicons-category"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!--Apartments Shortcodes section-->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label shortcode-section-heading">
                            <h2><?php echo esc_html__('Shortcode for Apartments', 'tourfic'); ?></h2>
                        </div>
                    </div>
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo esc_html__( 'Apartments', 'tourfic' ); ?></label>
                                <p><?php echo esc_html__( 'Display Apartments in specific location', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo esc_html__( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Apartments', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_apartment"><?php esc_html_e( 'Apartments', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Apartment Count', 'tourfic' ) ?></h3>
                                            <input type="number" value="5" data-count="count" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Apartment location', 'tourfic' ) ?></h3>
											<?php
											//Dynamic Taxonomy dropdown list
											Helper::tf_terms_dropdown( 'apartment_location', 'locations', 'tf_apartment_shortcode', 'tf-setting-field tf-select-field', true );
											?>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Apartment style', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="style='grid'"><?php esc_html_e( 'Grid', 'tourfic' ); ?></option>
                                                <option value="style='slider'"><?php esc_html_e( 'Slider', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                 <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Title', 'tourfic' ) ?></h3>
                                            <input type="text" value data-title="title" placeholder="Enter a Title" class="tf-shortcode-title-field tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Subtitle', 'tourfic' ) ?></h3>
                                            <input type="text" value data-subtitle="subtitle" placeholder="Enter a Subtitle" class="tf-shortcode-subtitle-field tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-generate-tour">
                                            <button class="tf-btn"><?php echo esc_html__( 'Generate', 'tourfic' ); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly/>
                                        <button type="button" class="tf-copy-btn tf-btn">
                                            <span class="dashicons dashicons-category"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!--Apartment locations Shortcodes section-->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo esc_html__( 'Apartment Location', 'tourfic' ); ?></label>
                                <p><?php echo esc_html__( 'Display apartment locations', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo esc_html__( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Apartment Locations', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_apartment_locations"><?php esc_html_e( 'Apartment Locations', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Orderby', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="orderby='name'"><?php esc_html_e( 'Name', 'tourfic' ); ?></option>
                                                <option value="orderby='title'"><?php esc_html_e( 'Title', 'tourfic' ); ?></option>
                                                <option value="orderby='date'"><?php esc_html_e( 'Date', 'tourfic' ); ?></option>
                                                <option value="orderby='ID'"><?php esc_html_e( 'ID', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Order', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="order='ASC'"><?php esc_html_e( 'ASC', 'tourfic' ); ?></option>
                                                <option value="order='DESC'"><?php esc_html_e( 'DESC', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Location limit', 'tourfic' ) ?></h3>
                                            <input type="number" value="-1" data-count="limit" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Choose Locations', 'tourfic' ) ?></h3>
											<?php
											//Dynamic Taxonomy dropdown list
											Helper::tf_terms_dropdown( 'apartment_location', 'ids', 'tf_apartment_location_shortcode', 'tf-setting-field tf-select-field', true );
											?>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Hide Empty', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="hide_empty='0'"><?php esc_html_e( 'No', 'tourfic' ); ?></option>
                                                <option value="hide_empty='1'"><?php esc_html_e( 'Yes', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-generate-tour">
                                            <button class="tf-btn"><?php echo esc_html__( 'Generate', 'tourfic' ); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly/>
                                        <button type="button" class="tf-copy-btn tf-btn">
                                            <span class="dashicons dashicons-category"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!--Recent Apartments Shortcodes section-->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo esc_html__( 'Recent Apartments', 'tourfic' ); ?></label>
                                <p><?php echo esc_html__( 'Display recent apartments', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo esc_html__( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Apartments', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_recent_apartment"><?php esc_html_e( 'Recent Apartment', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Orderby', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="orderby='name'"><?php esc_html_e( 'Name', 'tourfic' ); ?></option>
                                                <option value="orderby='title'"><?php esc_html_e( 'Title', 'tourfic' ); ?></option>
                                                <option value="orderby='date'"><?php esc_html_e( 'Date', 'tourfic' ); ?></option>
                                                <option value="orderby='ID'"><?php esc_html_e( 'ID', 'tourfic' ); ?></option>
                                                <option value="orderby='rand'"><?php esc_html_e( 'Rand', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Title', 'tourfic' ) ?></h3>
                                            <input type="text" value data-title="title" placeholder="Enter a Title" class="tf-shortcode-title-field tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Subtitle', 'tourfic' ) ?></h3>
                                            <input type="text" value data-subtitle="subtitle" placeholder="Enter a Subtitle" class="tf-shortcode-subtitle-field tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Order', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="order='ASC'"><?php esc_html_e( 'ASC', 'tourfic' ); ?></option>
                                                <option value="order='DESC'"><?php esc_html_e( 'DESC', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Apartment limit', 'tourfic' ) ?></h3>
                                            <input type="number" value="-1" data-count="count" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Slides to show', 'tourfic' ) ?></h3>
                                            <input type="number" value="5" data-count="slidestoshow" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-generate-tour">
                                            <button class="tf-btn"><?php echo esc_html__( 'Generate', 'tourfic' ); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly/>
                                        <button type="button" class="tf-copy-btn tf-btn">
                                            <span class="dashicons dashicons-category"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--External Listing Shortcodes section-->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo esc_html__( 'External Listing', 'tourfic' ); ?></label>
                                <p><?php echo esc_html__( 'Display external listing', 'tourfic' ); ?>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo esc_html__( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'External Listing', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_apartment_external_listings"><?php esc_html_e( 'External Listing', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Title', 'tourfic' ) ?></h3>
                                            <input type="text" value data-title="title" placeholder="Enter a Title" class="tf-shortcode-title-field tf-setting-field">
                                        </div>
                                    </div>
                                </div>

                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Subtitle', 'tourfic' ) ?></h3>
                                            <input type="text" value data-subtitle="subtitle" placeholder="Enter a Subtitle" class="tf-shortcode-subtitle-field tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Choose Locations', 'tourfic' ) ?></h3>
							                <?php
							                //Dynamic Taxonomy dropdown list
							                Helper::tf_terms_dropdown( 'apartment_location', 'locations', 'tf_listing_location_shortcode', 'tf-setting-field tf-select-field', true );
							                ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Total Post Number', 'tourfic' ) ?></h3>
                                            <input type="number" value="5" data-count="count" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Post Style', 'tourfic' ) ?></h3>

                                            <select class="tf-select-field tf-setting-field">
                                                <option value="style='grid'"><?php esc_html_e( 'Grid', 'tourfic' ); ?></option>
                                                <option value="style='slider'"><?php esc_html_e( 'Slider', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-generate-tour">
                                            <button class="tf-btn"><?php echo esc_html__( 'Generate', 'tourfic' ); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_tours]" readonly/>
                                        <button type="button" class="tf-copy-btn tf-btn">
                                            <span class="dashicons dashicons-category"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                     <!-- Vendor shortcode Section -->
                     <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label shortcode-section-heading">
                            <h2><?php echo esc_html__('Shortcodes for Vendors', 'tourfic'); ?></h2>
                        </div>
                    </div>

                    <!--Vendor Post Genarate Shortcodes section-->
                    <div class="tf-shortcode-generator-single">
                        <div class="tf-shortcode-generator-label">
                            <div class="tf-labels">
                                <label><?php echo esc_html__( 'Vendor Hotels, Tours & Apartments', 'tourfic' ); ?></label>
                                <p><?php echo esc_html__( 'Display Hotels, Tours & Apartments in specific Vendor', 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-shortcode-btn tf-generate-tour">
                                <button><?php echo esc_html__( 'Generate Shortcode', 'tourfic' ); ?></button>
                            </div>
                        </div>
                        <div class="tf-sg-form-wrapper">
                            <div class="tf-shortcode-generator-form">
                                <div class="tf-sg-close"><i class="far fa-times"></i></div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Vendor Posts', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="tf_vendor_post"><?php esc_html_e( 'Vendor Posts', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Type', 'tourfic' ) ?></h3>
                                            <select class="tf-select-field tf-setting-field">
                                                <option value="type='tf_hotel'"><?php esc_html_e( 'Hotel', 'tourfic' ); ?></option>
                                                <option value="type='tf_tours'"><?php esc_html_e( 'Tour', 'tourfic' ); ?></option>
                                                <option value="type='tf_apartment'"><?php esc_html_e( 'Apartment', 'tourfic' ); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="tf-sg-row">
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Number of Posts', 'tourfic' ) ?></h3>
                                            <input type="number" value="4" data-count="count" class="post-count tf-setting-field">
                                        </div>
                                    </div>
                                    <div class="tf-col-lg-6">
                                        <div class="tf-sg-field-wrap">
                                            <h3><?php echo esc_html__( 'Vendor Name', 'tourfic' ) ?></h3>
											<?php
											$tf_vendor_query_lists = get_users( array( 'role__in' => array( 'tf_vendor', 'administrator' ) ) );
											if ( ! empty( $tf_vendor_query_lists ) ) {
												?>
                                                <select class="tf-select-field tf-setting-field">
													<?php
													foreach ( $tf_vendor_query_lists as $single ) { ?>
                                                        <option value="vendor='<?php echo esc_attr($single->user_nicename); ?>' vendor_id='<?php echo esc_attr($single->ID); ?>' "><?php echo esc_html($single->user_nicename); ?></option>
													<?php } ?>
                                                </select>
											<?php } else {
												echo esc_html__( 'Not Found', 'tourfic' );
											}
											?>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-sg-row">

                                    <div class="tf-col-lg-6">
                                        <div class="tf-generate-tour">
                                            <button class="tf-btn"><?php echo esc_html__( 'Generate', 'tourfic' ); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-copy-item">
                                    <div class="tf-shortcode-field copy-shortcode">
                                        <input type="text" id="tf-shortcode" name="tf_generated_shortcode" class="tf-shortcode-value" value="[tf_vendor_post]" readonly/>
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
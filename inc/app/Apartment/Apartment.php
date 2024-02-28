<?php
namespace Tourfic\App\Apartment;

class Apartment{

    use \Tourfic\Traits\Singleton;

    public function __construct() {

    }

    function tf_apartment_single_booking_form( $comments, $disable_review_sec ) {

        $meta                = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
        $min_stay            = ! empty( $meta['min_stay'] ) ? $meta['min_stay'] : 1;
        $pricing_type        = ! empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : 'per_night';
        $price_per_night     = ! empty( $meta['price_per_night'] ) ? $meta['price_per_night'] : 0;
        $adult_price         = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
        $child_price         = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
        $infant_price        = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;
        $discount_type       = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : 'none';
        $discount            = ! empty( $meta['discount'] ) ? $meta['discount'] : 0;
        $enable_availability = ! empty( $meta['enable_availability'] ) ? $meta['enable_availability'] : '';
        $apt_availability    = ! empty( $meta['apt_availability'] ) ? $meta['apt_availability'] : '';
        $booked_dates        = tf_apartment_booked_days( get_the_ID() );
        $apt_reserve_button_text = !empty(tfopt('apartment_booking_form_button_text')) ? stripslashes(sanitize_text_field(tfopt('apartment_booking_form_button_text'))) : __("Reserve", 'tourfic');

        $tf_booking_type = '1';
        $tf_booking_url  = $tf_booking_query_url = $tf_booking_attribute = $tf_hide_booking_form = $tf_hide_price = '';
        if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
            $tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
            $tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
            $tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&room={room}';
            $tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
            $tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
            $tf_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
        }

        // date format for apartment
        $date_format_change_appartments = ! empty( tfopt( "tf-date-format-for-users" ) ) ? tfopt( "tf-date-format-for-users" ) : "Y/m/d";

        if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
            $additional_fees = ! empty( $meta['additional_fees'] ) ? $meta['additional_fees'] : array();
        } else {
            $additional_fee_label = ! empty( $meta['additional_fee_label'] ) ? $meta['additional_fee_label'] : '';
            $additional_fee       = ! empty( $meta['additional_fee'] ) ? $meta['additional_fee'] : 0;
            $fee_type             = ! empty( $meta['fee_type'] ) ? $meta['fee_type'] : '';
        }

        $adults       = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
        $child        = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
        $infant       = ! empty( $_GET['infant'] ) ? sanitize_text_field( $_GET['infant'] ) : '';
        $check_in_out = ! empty( $_GET['check-in-out-date'] ) ? $_GET['check-in-out-date'] : '';

        $apt_disable_dates = [];
        $tf_apt_enable_dates = [];
        if ( $enable_availability === '1' && ! empty( $apt_availability ) && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
            $apt_availability_arr = json_decode( $apt_availability, true );
            //iterate all the available disabled dates
            if ( ! empty( $apt_availability_arr ) && is_array( $apt_availability_arr ) ) {
                foreach ( $apt_availability_arr as $date ) {
                    if ( $date['status'] === 'unavailable' ) {
                        $apt_disable_dates[] = $date['check_in'];
                    }
                    if ( $date['status'] === 'available' ) {
                        $tf_apt_enable_dates[] = $date['check_in'];
                    }
                }
            }
        }

        $apartment_min_price = get_apartment_min_max_price( get_the_ID() );

        $tf_apartment_layout_conditions = ! empty( $meta['tf_single_apartment_layout_opt'] ) ? $meta['tf_single_apartment_layout_opt'] : 'global';
        if("single"==$tf_apartment_layout_conditions){
            $tf_apartment_single_template = ! empty( $meta['tf_single_apartment_template'] ) ? $meta['tf_single_apartment_template'] : 'default';
        }
        $tf_apartment_global_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['single-apartment'] ) ? tf_data_types(tfopt( 'tf-template' ))['single-apartment'] : 'default';

        $tf_apartment_selected_check = !empty($tf_apartment_single_template) ? $tf_apartment_single_template : $tf_apartment_global_template;

        $tf_apartment_selected_template = $tf_apartment_selected_check;
        if($tf_apartment_selected_template=="design-1"){
        ?>
        <form id="tf-apartment-booking" class="tf-apartment-side-booking" method="get" autocomplete="off">
            
            <div class="tf-apartment-form-header">
                <?php if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 ) : ?>
                    <h3 class="tf-apartment-price-per-night">
                        <span class="tf-apartment-base-price">
                        <?php
                            //get the lowest price from all available room price
                            $apartment_min_main_price = $apartment_min_price["min"];
                            if ( ! empty( $discount_type ) && ! empty( $apartment_min_price["min"]  ) && ! empty( $discount ) ) {
                                if ( $discount_type == "percent" ) {
                                    $apartment_min_discount = ( $apartment_min_price["min"] * (int) $discount ) / 100;
                                    $apartment_min_price    = $apartment_min_price["min"] - $apartment_min_discount;
                                }
                                if ( $discount_type == "fixed" ) {
                                    $apartment_min_discount = $discount;
                                    $apartment_min_price    = $apartment_min_price["min"] - (int) $apartment_min_discount;
                                }
                            }
                            $lowest_price = wc_price( $apartment_min_price );
                            
                            if ( ! empty( $apartment_min_discount ) ) {
                                echo "<b>" . __("From ", "tourfic") . "</b>" . "<del>" . strip_tags(wc_price( $apartment_min_main_price )) . "</del>" . " " . $lowest_price;
                            } else {
                                echo __("From ", "tourfic") . wc_price( $apartment_min_main_price );
                            }
                            ?>
                        </span>
                        <?php if ( $pricing_type == "per_night") : ?>
                            <span class="per-pricing-type"><?php _e( '/per night', 'tourfic' ) ?></span>
                        <?php else : ?>
                            <span class="per-pricing-type"><?php _e( '/per person', 'tourfic' ) ?></span>
                        <?php endif; ?>

                    </h3>
                <?php endif; ?>
            </div>

            <?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' ) || $tf_booking_type == 1 ) : ?>
                
                <h2 class="tf-section-title"><?php _e("Available Date", "tourfic"); ?></h2>
                <div class="tf-apartment-form-fields">
                    <div class="tf_booking-dates tf-check-in-out-date">
                        <div class="tf-aprtment-check-in-out-date">
                            <label class="tf_label_rows">
                                <i class="fa-sharp fa-solid fa-calendar-days"></i>
                                <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Choose date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . $check_in_out . '"' : '' ?> required>
                            </label>
                        </div>
                        <div class="tf_label-row"></div>
                    </div>

                    <div class="tf_form-row tf-apartment-guest-row">
                        <label class="tf_label-row">
                            <div class="tf_form-inner">
                                <div class="tf_selectperson-wrap">
                                <div class="tf-form-title">
                                    <h3 class="tf-person-info-title"><?php _e( 'Person Info', 'tourfic' ); ?></h3>
                                </div>
                                    <div class="tf_acrselection">
                                        <div class="acr-label"><?php _e( 'Adults', 'tourfic' ); ?></div>
                                        <div class="acr-select">
                                            <div class="acr-dec">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13094)">
                                                    <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13094">
                                                    <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                                </svg>
                                            </div>
                                            <input type="tel" name="adults" id="adults" min="1" value="<?php echo ! empty( $adults ) ? $adults : '1' ?>" readonly/>
                                            <div class="acr-inc">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13100)">
                                                    <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13100">
                                                    <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tf_acrselection">
                                        <div class="acr-label"><?php _e( 'Children', 'tourfic' ); ?></div>
                                        <div class="acr-select">
                                            <div class="acr-dec">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13094)">
                                                    <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13094">
                                                    <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                                </svg>
                                            </div>
                                            <input type="tel" name="children" id="children" min="0" value="<?php echo ! empty( $child ) ? $child : '0' ?>" readonly/>
                                            <div class="acr-inc">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13100)">
                                                    <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13100">
                                                    <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tf_acrselection">
                                        <div class="acr-label"><?php _e( 'Infant', 'tourfic' ); ?></div>
                                        <div class="acr-select">
                                            <div class="acr-dec">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13094)">
                                                    <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13094">
                                                    <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                                </svg>
                                            </div>
                                            <input type="tel" name="infant" id="infant" min="0" value="<?php echo ! empty( $infant ) ? $infant : '0' ?>" readonly/>
                                            <div class="acr-inc">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13100)">
                                                    <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13100">
                                                    <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            <?php endif; ?>

            <div class="tf_form-row">
                <?php $ptype = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type(); ?>
                <input type="hidden" name="type" value="<?php echo $ptype; ?>" class="tf-post-type"/>
                <input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>"/>

                <div class="tf-btn-booking">
                    <?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' ) || $tf_booking_type == 1 ) : ?>
                        <?php if (!empty($apt_reserve_button_text)) : ?>
                            <button class="tf_button tf-submit" type="submit"><?php esc_html_e( $apt_reserve_button_text, 'tourfic' ); ?></button>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (!empty($apt_reserve_button_text)) : ?>
                            <a href="<?php echo esc_url( $tf_booking_url ); ?>"
                            class="tf_button tf-submit" <?php echo ! empty( $tf_booking_attribute ) ? $tf_booking_attribute : ''; ?> target="_blank"><?php esc_html_e( $apt_reserve_button_text , 'tourfic' ); ?></a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <ul class="tf-apartment-price-list" style="display: none">
                <li class="total-days-price-wrap" style="display: none">
                    <span class="total-days tf-price-list-label"></span>
                    <span class="days-total-price tf-price-list-price"></span>
                </li>

                <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ): ?>
                    <?php foreach ( $additional_fees as $key => $additional_fee ) : ?>
                        <li class="additional-fee-wrap" style="display: none">
                            <span class="additional-fee-label tf-price-list-label"><?php echo $additional_fee['additional_fee_label']; ?></span>
                            <span class="additional-fee-<?php echo esc_attr( $key ) ?> tf-price-list-price"></span>
                        </li>
                    <?php endforeach; ?>
                <?php elseif ( ! empty( $additional_fee_label ) && ! empty( $additional_fee ) ): ?>
                    <li class="additional-fee-wrap" style="display: none">
                        <span class="additional-fee-label tf-price-list-label"><?php echo $additional_fee_label; ?></span>
                        <span class="additional-fee tf-price-list-price"></span>
                    </li>
                <?php endif; ?>

                <?php if ( ! empty( $discount ) ): ?>
                    <li class="apartment-discount-wrap" style="display: none">
                        <span class="apartment-discount-label tf-price-list-label"><?php _e( 'Discount', 'tourfic' ); ?></span>
                        <span class="apartment-discount tf-price-list-price"></span>
                    </li>
                <?php endif; ?>

                <li class="total-price-wrap" style="display: none">
                    <span class="total-price-label tf-price-list-label"><?php _e( 'Total Price', 'tourfic' ); ?></span>
                    <span class="total-price"></span>
                </li>
            </ul>

            <?php wp_nonce_field( 'tf_apartment_booking', 'tf_apartment_nonce' ); ?>
        </form>
        <?php }else{ ?>
        <!-- Start Booking widget -->
        <form id="tf-apartment-booking" class="tf-apartment-side-booking" method="get" autocomplete="off">
            <h4><?php ! empty( $meta['booking_form_title'] ) ? _e( $meta['booking_form_title'] ) : _e( 'Book your Apartment', 'tourfic' ); ?></h4>
            <div class="tf-apartment-form-header">
                <?php if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 ) : ?>
                    <h3 class="tf-apartment-price-per-night">
                        <span class="tf-apartment-base-price">
                        <?php
                            //get the lowest price from all available room price
                            $apartment_min_main_price = $apartment_min_price["min"];
                            if ( ! empty( $discount_type ) && ! empty( $apartment_min_price["min"]  ) && ! empty( $discount ) ) {
                                if ( $discount_type == "percent" ) {
                                    $apartment_min_discount = ( $apartment_min_price["min"] * (int) $discount ) / 100;
                                    $apartment_min_price    = $apartment_min_price["min"] - $apartment_min_discount;
                                }
                                if ( $discount_type == "fixed" ) {
                                    $apartment_min_discount = $discount;
                                    $apartment_min_price    = $apartment_min_price["min"] - (int) $apartment_min_discount;
                                }
                            }
                            $lowest_price = wc_price( $apartment_min_price );
                            
                            if ( ! empty( $apartment_min_discount ) ) {
                                echo "<b>" . __("From ", "tourfic") . "</b>" . "<del>" . strip_tags(wc_price( $apartment_min_main_price )) . "</del>" . " " . $lowest_price;
                            } else {
                                echo __("From ", "tourfic") . wc_price( $apartment_min_main_price );
                            }
                            ?>
                        </span>
                        <?php if ( $pricing_type == "per_night") : ?>
                            <span><?php _e( '/per night', 'tourfic' ) ?></span>
                        <?php else : ?>
                            <span><?php _e( '/per person', 'tourfic' ) ?></span>
                        <?php endif; ?>

                    </h3>
                <?php endif; ?>
                <?php if ( $comments && ! $disable_review_sec == '1' ): ?>
                    <div class="tf-top-review">
                        <a href="#tf-review">
                            <div class="tf-single-rating">
                                <i class="fas fa-star"></i> <span><?php echo tf_total_avg_rating( $comments ); ?></span>
                                (<?php tf_based_on_text( count( $comments ) ); ?>)
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' ) || $tf_booking_type == 1 ) : ?>
                <div class="tf-apartment-form-fields">
                    <div class="tf_booking-dates">
                        <div class="tf-check-in-out-date">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php _e( 'Check in & out date', 'tourfic' ); ?></span>
                                <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                        placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . $check_in_out . '"' : '' ?>
                                        required>
                            </label>
                        </div>
                    </div>

                    <div class="tf_form-row tf-apartment-guest-row">
                        <label class="tf_label-row">
                            <span class="tf-label"><?php _e( 'Guests', 'tourfic' ); ?></span>
                            <div class="tf_form-inner">
                                <div class="tf_selectperson-wrap">
                                    <div class="tf_input-inner">
                                        <div class="adults-text"><?php echo sprintf( __( '%s Adults', 'tourfic' ), ! empty( $adults ) ? $adults : 1 ); ?></div>
                                        <div class="person-sep"></div>
                                        <div class="child-text"><?php echo sprintf( __( '%s Children', 'tourfic' ), ! empty( $child ) ? $child : 0 ); ?></div>
                                        <div class="person-sep"></div>
                                        <div class="infant-text"><?php echo sprintf( __( '%s Infant', 'tourfic' ), ! empty( $infant ) ? $infant : 0 ); ?></div>
                                    </div>
                                    <div class="tf_acrselection-wrap">
                                        <div class="tf_acrselection-inner">
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php _e( 'Adults', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">-</div>
                                                    <input type="number" name="adults" id="adults" min="1" value="<?php echo ! empty( $adults ) ? $adults : '1' ?>"/>
                                                    <div class="acr-inc">+</div>
                                                </div>
                                            </div>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php _e( 'Children', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">-</div>
                                                    <input type="number" name="children" id="children" min="0" value="<?php echo ! empty( $child ) ? $child : '0' ?>"/>
                                                    <div class="acr-inc">+</div>
                                                </div>
                                            </div>
                                            <div class="tf_acrselection">
                                                <div class="acr-label"><?php _e( 'Infant', 'tourfic' ); ?></div>
                                                <div class="acr-select">
                                                    <div class="acr-dec">-</div>
                                                    <input type="number" name="infant" id="infant" min="0" value="<?php echo ! empty( $infant ) ? $infant : '0' ?>"/>
                                                    <div class="acr-inc">+</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            <?php endif; ?>

            <div class="tf_form-row">
                <?php $ptype = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type(); ?>
                <input type="hidden" name="type" value="<?php echo $ptype; ?>" class="tf-post-type"/>
                <input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>"/>

                <div class="tf-btn">
                    <?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' ) || $tf_booking_type == 1 ) : ?>
                        <?php if (!empty($apt_reserve_button_text)) : ?>
                            <button class="tf_button tf-submit btn-styled" type="submit"><?php esc_html_e( $apt_reserve_button_text, 'tourfic' ); ?></button>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (!empty($apt_reserve_button_text)) : ?>
                            <a href="<?php echo esc_url( $tf_booking_url ); ?>"
                            class="tf_button tf-submit btn-styled" <?php echo ! empty( $tf_booking_attribute ) ? $tf_booking_attribute : ''; ?> target="_blank"><?php esc_html_e( $apt_reserve_button_text , 'tourfic' ); ?></a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <ul class="tf-apartment-price-list" style="display: none">
                <li class="total-days-price-wrap" style="display: none">
                    <span class="total-days tf-price-list-label"></span>
                    <span class="days-total-price tf-price-list-price"></span>
                </li>

                <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ): ?>
                    <?php foreach ( $additional_fees as $key => $additional_fee ) : ?>
                        <li class="additional-fee-wrap" style="display: none">
                            <span class="additional-fee-label tf-price-list-label"><?php echo $additional_fee['additional_fee_label']; ?></span>
                            <span class="additional-fee-<?php echo esc_attr( $key ) ?> tf-price-list-price"></span>
                        </li>
                    <?php endforeach; ?>
                <?php elseif ( ! empty( $additional_fee_label ) && ! empty( $additional_fee ) ): ?>
                    <li class="additional-fee-wrap" style="display: none">
                        <span class="additional-fee-label tf-price-list-label"><?php echo $additional_fee_label; ?></span>
                        <span class="additional-fee tf-price-list-price"></span>
                    </li>
                <?php endif; ?>

                <?php if ( ! empty( $discount ) ): ?>
                    <li class="apartment-discount-wrap" style="display: none">
                        <span class="apartment-discount-label tf-price-list-label"><?php _e( 'Discount', 'tourfic' ); ?></span>
                        <span class="apartment-discount tf-price-list-price"></span>
                    </li>
                <?php endif; ?>

                <li class="total-price-wrap" style="display: none">
                    <span class="total-price-label tf-price-list-label"><?php _e( 'Total Price', 'tourfic' ); ?></span>
                    <span class="total-price"></span>
                </li>
            </ul>

            <?php wp_nonce_field( 'tf_apartment_booking', 'tf_apartment_nonce' ); ?>
        </form>
        <?php } ?>
        <script>
            (function ($) {
                $(document).ready(function () {

                    // First Day of Week
                    <?php tf_flatpickr_locale("root"); ?>

                    let minStay = <?php echo $min_stay ?>;

                    const bookingCalculation = (selectedDates) => {
                        <?php if ( ( $pricing_type === 'per_night' && ! empty( $price_per_night ) ) || ( $pricing_type === 'per_person' && ! empty( $adult_price ) ) ): ?>
                        //calculate total days
                        if (selectedDates[0] && selectedDates[1]) {
                            var diff = Math.abs(selectedDates[1] - selectedDates[0]);
                            var days = Math.ceil(diff / (1000 * 60 * 60 * 24));
                            if (days > 0) {
                                var pricing_type = '<?php echo $pricing_type; ?>';
                                var price_per_night = <?php echo $price_per_night; ?>;
                                var adult_price = <?php echo $adult_price; ?>;
                                var child_price = <?php echo $child_price; ?>;
                                var infant_price = <?php echo $infant_price; ?>;
                                var enable_availability = '<?php echo $enable_availability; ?>';
                                var apt_availability = '<?php echo $apt_availability; ?>';
                                
                                if(apt_availability) {
                                    apt_availability = JSON.parse(apt_availability);
                                }

                                if (enable_availability !== '1') {
                                    if (pricing_type === 'per_night') {
                                        var total_price = price_per_night * days;
                                        var total_days_price_html = '<?php echo wc_price( 0 ); ?>';
                                        var wc_price_per_night = '<?php echo wc_price( $price_per_night ); ?>';
                                        if (total_price > 0) {
                                            $('.tf-apartment-price-list').show();
                                            $('.total-days-price-wrap').show();
                                            total_days_price_html = '<?php echo wc_price( 0 ); ?>'.replace('0.00', total_price.toFixed(2));
                                        }
                                        $('.total-days-price-wrap .total-days').html(wc_price_per_night + ' x ' + days + ' <?php _e( 'nights', 'tourfic' ); ?>');
                                        $('.total-days-price-wrap .days-total-price').html(total_days_price_html);
                                    } else {
                                        let totalPersonPrice = (adult_price * $('#adults').val()) + (child_price * $('#children').val()) + (infant_price * $('#infant').val());
                                        var total_price = totalPersonPrice * days;
                                        var total_days_price_html = '<?php echo wc_price( 0 ); ?>';
                                        var wc_price_per_person = '<?php echo wc_price( 0 ); ?>'.replace('0.00', totalPersonPrice.toFixed(2));
                                        if (total_price > 0) {
                                            $('.tf-apartment-price-list').show();
                                            $('.total-days-price-wrap').show();
                                            total_days_price_html = '<?php echo wc_price( 0 ); ?>'.replace('0.00', total_price.toFixed(2));
                                        }
                                        $('.total-days-price-wrap .total-days').html(wc_price_per_person + ' x ' + days + ' <?php _e( 'nights', 'tourfic' ); ?>');
                                        $('.total-days-price-wrap .days-total-price').html(total_days_price_html);
                                    }
                                } else {
                                    var total_price = 0;
                                    var total_price_html = '<?php echo wc_price( 0 ); ?>';
                                    var checkInDate = new Date(selectedDates[0]);
                                    var checkOutDate = new Date(selectedDates[1]);

                                    for (var date in apt_availability) {
                                        let d = new Date(date);

                                        if (d.getTime() >= checkInDate.getTime() && d.getTime() <= checkOutDate.getTime()) {

                                            if (d.getTime() !== checkInDate.getTime()) {
                                                var availabilityData = apt_availability[date];
                                                var pricing_type = availabilityData.pricing_type;
                                                var price = availabilityData.price ? parseFloat(availabilityData.price) : 0;
                                                var adultPrice = availabilityData.adult_price ? parseFloat(availabilityData.adult_price) : 0;
                                                var childPrice = availabilityData.child_price ? parseFloat(availabilityData.child_price) : 0;
                                                var infantPrice = availabilityData.infant_price ? parseFloat(availabilityData.infant_price) : 0;

                                                if (pricing_type === 'per_night' && price > 0) {
                                                    total_price += price;
                                                } else if (pricing_type === 'per_person') {
                                                    var totalPersonPrice = (adultPrice * $('#adults').val()) + (childPrice * $('#children').val()) + (infantPrice * $('#infant').val());
                                                    total_price += totalPersonPrice;
                                                    // console.log('total_price', total_price);
                                                }
                                            }
                                        }
                                    }

                                    if (total_price > 0) {
                                        $('.tf-apartment-price-list').show();
                                        $('.total-days-price-wrap').show();
                                        total_price_html = '<?php echo wc_price( 0 ); ?>'.replace('0.00', total_price.toFixed(2));
                                    }
                                    $('.total-days-price-wrap .total-days').html(days + ' <?php _e( 'nights', 'tourfic' ); ?>');
                                    $('.total-days-price-wrap .days-total-price').html(total_price_html);
                                }
                                //discount
                                var discount = <?php echo $discount; ?>;
                                var discountType = "<?php echo $discount_type; ?>";
                                var discount_html = '<?php echo wc_price( 0 ); ?>';
                                if (discount > 0 && discountType != "none") {
                                    $('.apartment-discount-wrap').show();

                                    <?php if ( $discount_type == 'percent' ): ?>
                                    discount_html = '<?php echo wc_price( 0 ); ?>'.replace('0.00', (total_price * discount / 100).toFixed(2));
                                    total_price = total_price - (total_price * discount / 100);
                                    <?php else: ?>
                                    discount_html = '<?php echo wc_price( 0 ); ?>'.replace('0.00', discount.toFixed(2));
                                    total_price = total_price - discount;
                                    <?php endif; ?>
                                }
                                $('.apartment-discount-wrap .apartment-discount').html('-' + discount_html);


                                let totalPerson = parseInt($('.tf_acrselection #adults').val()) + parseInt($('.tf_acrselection #children').val()) + parseInt($('.tf_acrselection #infant').val());

                                //additional fee
                                <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ): ?>
                                <?php foreach ($additional_fees as $key => $item) : ?>
                                let additional_fee_<?php echo $key ?> = <?php echo $item['additional_fee']; ?>;
                                let additional_fee_html_<?php echo $key ?> = '<?php echo wc_price( 0 ); ?>';
                                let totalAdditionalFee_<?php echo $key ?> = 0;

                                <?php if ( $item['fee_type'] == 'per_night' ): ?>
                                totalAdditionalFee_<?php echo $key ?> = additional_fee_<?php echo $key ?> * days;
                                <?php elseif($item['fee_type'] == 'per_person'): ?>
                                totalAdditionalFee_<?php echo $key ?> = additional_fee_<?php echo $key ?> * totalPerson;
                                <?php else: ?>
                                totalAdditionalFee_<?php echo $key ?> = additional_fee_<?php echo $key ?>;
                                <?php endif; ?>

                                if (totalAdditionalFee_<?php echo $key ?> > 0 ) {
                                    $('.additional-fee-wrap').show();
                                    total_price = total_price + totalAdditionalFee_<?php echo $key ?>;
                                    additional_fee_html_<?php echo $key ?> = '<?php echo wc_price( 0 ); ?>'.replace('0.00', totalAdditionalFee_<?php echo $key ?>.toFixed(2));
                                }
                                $('.additional-fee-wrap .additional-fee-<?php echo $key ?>').html(additional_fee_html_<?php echo $key ?>);
                                <?php endforeach; ?>
                                <?php else: ?>
                                <?php if ( ! empty( $additional_fee ) ): ?>
                                let additional_fee = <?php echo $additional_fee; ?>;
                                let additional_fee_html = '<?php echo wc_price( 0 ); ?>';
                                let totalAdditionalFee = 0;

                                <?php if ( $fee_type == 'per_night' ): ?>
                                totalAdditionalFee = additional_fee * days;
                                <?php elseif($fee_type == 'per_person'): ?>
                                totalAdditionalFee = additional_fee * totalPerson;
                                <?php else: ?>
                                totalAdditionalFee = additional_fee;
                                <?php endif; ?>

                                if (totalAdditionalFee > 0) {
                                    $('.additional-fee-wrap').show();
                                    total_price = total_price + totalAdditionalFee;
                                    additional_fee_html = '<?php echo wc_price( 0 ); ?>'.replace('0.00', totalAdditionalFee.toFixed(2));
                                }
                                $('.additional-fee-wrap .additional-fee').html(additional_fee_html);
                                <?php endif; ?>
                                <?php endif; ?>
                                //end additional fee

                                //total price
                                var total_price_html = '<?php echo wc_price( 0 ); ?>';
                                if (total_price > 0) {
                                    $('.total-price-wrap').show();
                                    total_price_html = '<?php echo wc_price( 0 ); ?>'.replace('0.00', total_price.toFixed(2));
                                }
                                $('.total-price-wrap .total-price').html(total_price_html);
                            } else {
                                $('.tf-apartment-price-list').hide();
                                $('.total-days-price-wrap').hide();
                                $('.additional-fee-wrap').hide();
                                $('.total-price-wrap').hide();
                            }
                        }
                        <?php endif; ?>

                        //minimum stay
                        if (selectedDates[0] && selectedDates[1] && minStay > 0) {
                            var diff = Math.abs(selectedDates[1] - selectedDates[0]);
                            var days = Math.ceil(diff / (1000 * 60 * 60 * 24));

                            if (days < minStay) {
                                $('.tf-submit').attr('disabled', 'disabled');
                                $('.tf-submit').addClass('disabled');
                                $('.tf-check-in-out-date .tf_label-row .tf-err-msg').remove();
                                $('.tf-check-in-out-date .tf_label-row').append('<span class="tf-err-msg"><?php echo sprintf( __( 'Minimum stay is %s nights', 'tourfic' ), $min_stay ); ?></span>');
                            } else {
                                $('.tf-submit').removeAttr('disabled');
                                $('.tf-submit').removeClass('disabled');
                                $('.tf-check-in-out-date .tf_label-row .tf-err-msg').remove();
                            }
                        }
                    }

                    const checkinoutdateange = flatpickr("#tf-apartment-booking #check-in-out-date", {
                        enableTime: false,
                        mode: "range",
                        minDate: "today",
                        altInput: true,
                        altFormat: '<?php echo $date_format_change_appartments; ?>',
                        dateFormat: "Y/m/d",
                        defaultDate: <?php echo json_encode( explode( '-', $check_in_out ) ) ?>,
                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                            bookingCalculation(selectedDates);
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                            bookingCalculation(selectedDates);
                        }, 
                        <?php if (!empty($tf_apt_enable_dates) && is_array($tf_apt_enable_dates)) : ?>
                            enable: [ <?php array_walk($tf_apt_enable_dates, function($date) {echo '"'. $date . '",';}); ?> ],
                        <?php endif; ?>
                        disable: [
                            <?php foreach ( $booked_dates as $booked_date ) : ?>
                            {
                                from: "<?php echo $booked_date['check_in']; ?>",
                                to: "<?php echo $booked_date['check_out']; ?>"
                            },
                            <?php endforeach; ?>
                            <?php foreach ( $apt_disable_dates as $apt_disable_date ) : ?>
                            {
                                from: "<?php echo $apt_disable_date; ?>",
                                to: "<?php echo $apt_disable_date; ?>"
                            },
                            <?php endforeach; ?>
                        ],
                        <?php tf_flatpickr_locale(); ?>
                    });

                    $(document).on('change', '.tf_acrselection #adults, .tf_acrselection #children, .tf_acrselection #infant', function () {
                        if ($('#tf-apartment-booking #check-in-out-date').val() !== '') {
                            bookingCalculation(checkinoutdateange.selectedDates);
                        }
                    });
                });
            })(jQuery);

        </script>
        <?php
    }
    

    
    function tf_apartment_archive_single_item( array $data = [ 1, 0, 0, '' ] ): void {

        $post_id  = get_the_ID();
        $features = ! empty( get_the_terms( $post_id, 'apartment_feature' ) ) ? get_the_terms( $post_id, 'apartment_feature' ) : '';

        // Form Data
        if ( isset( $data[4] ) && isset( $data[5] ) ) {
            [ $adults, $child, $infant, $check_in_out, $startprice, $endprice ] = $data;
        } else {
            [ $adults, $child, $infant, $check_in_out ] = $data;
        }

        // Get apartment meta options
        $meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
        if ( empty( $meta ) ) {
            return;
        }

        // Location
        $map = ! empty( $meta['map'] ) ? $meta['map'] : '';
        if ( ! empty( $map ) && gettype( $map ) == "string" ) {
            $tf_apartment_map_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
                return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
            }, $map );
            $map                    = unserialize( $tf_apartment_map_value );
            $address                = ! empty( $map['address'] ) ? $map['address'] : '';
        }else{
            $address                = ! empty( $map['address'] ) ? $map['address'] : '';
        }
        $featured        = ! empty( $meta['apartment_as_featured'] ) ? $meta['apartment_as_featured'] : '';
        $pricing_type    = ! empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : 'per_night';
        $apartment_multiple_tags = !empty($meta['tf-apartment-tags']) ? tf_data_types($meta['tf-apartment-tags']) : [];
        //Discout Info
        $apartment_discount_type = !empty($meta["discount_type"]) ? $meta["discount_type"] : "none";
        $apartment_discount_amount = !empty($meta["discount"]) ? $meta["discount"] : 0;

        // Gallery Image
        $gallery = ! empty( $meta['apartment_gallery'] ) ? $meta['apartment_gallery'] : '';
        if ( $gallery ) {
            $gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
        }
        
        // Single link
        $url = get_the_permalink();
        $url = add_query_arg( array(
            'adults'            => $adults,
            'children'          => $child,
            'infant'            => $infant,
            'check-in-out-date' => $check_in_out,
        ), $url );

        $apartment_min_price = get_apartment_min_max_price( get_the_ID() );
        $tf_apartment_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['apartment-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['apartment-archive'] : 'default';
        if ( $tf_apartment_arc_selected_template == "design-1" ) {
        $first_gallery_image = explode(',', $gallery);	
        ?>
        <div class="tf-available-room">
            <div class="tf-available-room-gallery">                       
                <div class="tf-room-gallery">
                        <?php
                        if ( has_post_thumbnail($post_id) ) {
                            echo get_the_post_thumbnail($post_id, 'full' );
                        } else {
                            echo '<img src="' . TF_APP_ASSETS_URL . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
                        }
                        ?>
                </div>
                <?php 
                if( !empty($gallery_ids) ){ ?>                                                                     
                <div data-id="<?php echo get_the_ID(); ?>" data-type="tf_apartment" class="tf-room-gallery tf-popup-buttons tf-hotel-room-popup" style="<?php echo !empty($first_gallery_image[0]) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.70) 0%, rgba(48, 40, 28, 0.70) 100%), url('.esc_url(wp_get_attachment_image_url($first_gallery_image[0])).'), lightgray 50% / cover no-repeat; background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
                    <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="content">
                    <path id="Rectangle 2111" d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5" stroke="#FDF9F4" stroke-width="1.5"></path>
                    <path id="Rectangle 2109" d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z" stroke="#FDF9F4" stroke-width="1.5"></path>
                    <path id="Vector" d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17" stroke="#FDF9F4" stroke-width="1.5" stroke-linejoin="round"></path>
                    <path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </g>
                    </svg>
                </div>
                <?php } ?>
                <div class="tf-available-labels">
                    <?php if ( $featured ): ?>
                    <span class="tf-available-labels-featured"><?php _e("Featured", "tourfic"); ?></span>
                    <?php endif; ?>
                    <?php
                        if(sizeof($apartment_multiple_tags) > 0) {
                            foreach($apartment_multiple_tags as $tag) {
                                $apartment_tag_name = !empty($tag['apartment-tag-title']) ? __($tag['apartment-tag-title'], "tourfic") : '';
                                $tag_background_color = !empty($tag["apartment-tag-color-settings"]["background"]) ? $tag["apartment-tag-color-settings"]["background"] : "#003162";
                                $tag_font_color = !empty($tag["apartment-tag-color-settings"]["font"]) ? $tag["apartment-tag-color-settings"]["font"] : "#fff";

                                if (!empty($apartment_tag_name)) {
                                    echo <<<EOD
                                        <span class="tf-multiple-tag" style="color: $tag_font_color; background-color: $tag_background_color ">$apartment_tag_name</span>
                                    EOD;
                                }
                            }
                        }
                    ?>
                </div>  
                <div class="tf-available-ratings">
                    <?php tf_archive_single_rating(); ?>
                    <i class="fa-solid fa-star"></i>
                </div>  
            </div>
            <div class="tf-available-room-content">
                <div class="tf-available-room-content-left">
                    <div class="tf-card-heading-info">
                    <div class="tf-section-title-and-location">
                        <a href="<?php echo esc_url( get_the_permalink($post_id) ); ?>"><h2 class="tf-section-title"><?php echo tourfic_character_limit_callback( get_the_title($post_id), 55 ); ?></h2></a>
                        <?php
                        if ( ! empty( $address ) ) {
                        ?>
                        <div class="tf-title-location">
                            <div class="location-icon">
                                <i class="ri-map-pin-line"></i>
                            </div>
                            <span><?php echo tourfic_character_limit_callback( esc_html( $address ), 65 ); ?></span>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="tf-mobile tf-pricing-info">
                        <?php if ( ! empty( $discount_amount ) ){ ?>
                            <div class="tf-available-room-off">
                                <span>
                                    <?php echo min( $discount_amount ); ?>% <?php _e( "Off ", "tourfic" ); ?>
                                </span>
                            </div>
                        <?php } ?>
                        <div class="tf-available-room-price">
                            <span class="tf-price-from">
                            <?php
                            if(!empty($apartment_min_price['min'])){
                                echo __( "From ", "tourfic" );
                                echo wc_price( $apartment_min_price['min'] );
                            }
                            ?>
                            </span>
                        </div>
                    </div>
                    </div>
                    <?php if ( $features ) { ?>
                    <ul class="features">
                    <?php foreach ( $features as $tfkey => $feature ) {
                    $feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_apartment_feature', true );
                    if ( ! empty( $feature_meta ) ) {
                        $f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
                    }
                    if ( ! empty( $f_icon_type ) && $f_icon_type == 'icon' ) {
                        $feature_icon = ! empty( $feature_meta['apartment-feature-icon'] ) ? '<i class="' . $feature_meta['apartment-feature-icon'] . '"></i>' : '';
                    } elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'custom' ) {
                        $feature_icon = ! empty( $feature_meta['apartment-feature-icon-custom'] ) ? '<img src="' . $feature_meta['apartment-feature-icon-custom'] . '" style="min-width: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px; height: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px;" />' : '';
                    }
                    if ( $tfkey < 5 ) {
                    ?>
                        <li>
                        <?php
                        if ( ! empty( $feature_icon ) ) {
                            echo $feature_icon;
                        } ?>
                        <?php echo $feature->name; ?>
                        </li>
                    <?php } } ?>
                    <?php if(count($features)>5){ ?>
                        <li><a href="<?php echo esc_url( $url ); ?>"><?php _e("View More", "tourfic"); ?></a></li>
                    <?php } ?>
                    </ul>
                    <?php } ?>
                </div>
                <div class="tf-available-room-content-right">
                    <div class="tf-card-pricing-heading">
                    <?php
                    if ( ! empty( $apartment_discount_amount ) && $apartment_discount_type!="none" ){ ?>
                        <div class="tf-available-room-off">
                            <span>
                                <?php echo $apartment_discount_type=="percent" ? $apartment_discount_amount.'%' : wc_price($apartment_discount_amount) ?> <?php _e( "Off ", "tourfic" ); ?>
                            </span>
                        </div>
                    <?php } ?>
                    <div class="tf-available-room-price">
                        <span class="tf-price-from">
                        <?php
                            if(!empty($apartment_min_price['min'])){
                                echo __( "From ", "tourfic" );
                                echo wc_price( $apartment_min_price['min'] );
                            }
                        ?>
                        </span>
                    </div>
                    </div>              
                    <a href="<?php echo esc_url( $url ); ?>" class="view-hotel"><?php _e("See details", "tourfic"); ?></a>
                </div>
            </div>
        </div>
        <?php }else{ ?>
        <div class="single-tour-wrap <?php echo $featured ? esc_attr( 'tf-featured' ) : '' ?>">
            <div class="single-tour-inner">
                <?php if ( $featured ): ?>
                    <div class="tf-featured-badge">
                        <span><?php echo ! empty( $meta['featured_text'] ) ? $meta['featured_text'] : esc_html( "HOT DEAL" ); ?></span>
                    </div>
                <?php endif; ?>
                <div class="tourfic-single-left">
                    <div class="default-tags-container">

                    <?php
                    if(sizeof($apartment_multiple_tags) > 0) {
                        foreach($apartment_multiple_tags as $tag) {
                            $tag_title = !empty($tag["apartment-tag-title"]) ? __($tag["apartment-tag-title"], 'tourfic') : '';
                            $tag_background_color = !empty($tag["apartment-tag-color-settings"]["background"]) ? $tag["apartment-tag-color-settings"]["background"] : "#003162";
                            $tag_font_color = !empty($tag["apartment-tag-color-settings"]["font"]) ? $tag["apartment-tag-color-settings"]["font"] : "#fff";

                            if (!empty($tag_title)) {
                                echo <<<EOD
                                    <span class="default-single-tag" style="color: $tag_font_color; background-color: $tag_background_color">$tag_title</span>
                                EOD;
                            }
                        }
                    }
                    ?>
                    </div>
                    <a href="<?php echo $url; ?>">
                        <?php
                        if ( has_post_thumbnail($post_id) ) {
                            echo get_the_post_thumbnail($post_id, 'full' );
                        } else {
                            echo '<img width="100%" height="100%" src="' . TF_APP_ASSETS_URL . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
                        }
                        ?>
                    </a>
                </div>
                <div class="tourfic-single-right">
                    <div class="tf_property_block_main_row">
                        <div class="tf_item_main_block">
                            <div class="tf-hotel__title-wrap">
                                <a href="<?php echo $url; ?>"><h3 class="tourfic_hotel-title"><?php echo get_the_title($post_id); ?></h3></a>
                            </div>
                            <?php
                            if ( !empty($address) ) {
                                echo '<div class="tf-map-link">';
                                echo '<span class="tf-d-ib"><i class="fas fa-map-marker-alt"></i> ' . strlen($address) > 75 ? tourfic_character_limit_callback($address, 76) : $address . '</span>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                        <?php tf_archive_single_rating(); ?>
                    </div>

                    <div class="sr_rooms_table_block">
                        <div class="room_details">
                            <div class="featuredRooms">
                                <div class="prco-ltr-right-align-helper">
                                    <div class="tf-archive-shortdesc">
                                        <?php echo substr( wp_strip_all_tags( get_the_content($post_id) ), 0, 160 ) . '...'; ?>
                                    </div>
                                </div>
                                <div class="roomNameInner">
                                    <div class="room_link">
                                        <div class="roomrow_flex">
                                            <?php if ( $features ) { ?>
                                                <div class="roomName_flex">
                                                    <ul class="tf-archive-desc">
                                                        <?php foreach ( $features as $feature ) {
                                                            $feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_apartment_feature', true );
                                                            if ( ! empty( $feature_meta ) ) {
                                                                $f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
                                                            }
                                                            if ( ! empty( $f_icon_type ) && $f_icon_type == 'icon' ) {
                                                                $feature_icon = ! empty( $feature_meta['apartment-feature-icon'] ) ? '<i class="' . $feature_meta['apartment-feature-icon'] . '"></i>' : '<i class="fas fa-bread-slice"></i>';
                                                            } elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'custom' ) {
                                                                $feature_icon = ! empty( $feature_meta['apartment-feature-icon-custom'] ) ? '<img src="' . $feature_meta['apartment-feature-icon-custom'] . '" style="min-width: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px; height: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px;" />' : '<i class="fas fa-bread-slice"></i>';
                                                            } else {
                                                                $feature_icon = '<i class="fas fa-bread-slice"></i>';
                                                            }
                                                            ?>
                                                            <li class="tf-tooltip">
                                                                <?php
                                                                if ( ! empty( $feature_icon ) ) {
                                                                    echo $feature_icon;
                                                                } ?>
                                                                <div class="tf-top">
                                                                    <?php echo $feature->name; ?>
                                                                    <i class="tool-i"></i>
                                                                </div>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                            <?php } ?>
                                            <div class="roomPrice roomPrice_flex sr_discount" style="<?php echo empty( $features ) ? 'text-align:left' : ''; ?>">
                                                <div class="availability-btn-area">
                                                    <a href="<?php echo $url; ?>" class="tf_button btn-styled"><?php esc_html_e( 'View Details', 'tourfic' ); ?></a>
                                                </div>
                                                <!-- Show minimum price @author - Hena -->
                                                <div class="tf-room-price-area">
                                                    <div class="tf-room-price">
                                                        <h6 class="tf-apartment-price-per-night">
                                                            <span class="tf-apartment-base-price"><?php echo wc_price( $apartment_min_price['min'] ) ?></span>
                                                            <span><?php echo $pricing_type === 'per_night' ? __( '/per night', 'tourfic' ) : __( '/per person', 'tourfic' ) ?></span>
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
    
    function get_apartment_locations() {

        $locations = array();

        $location_terms = get_terms( array(
            'taxonomy'   => 'apartment_location',
            'hide_empty' => true,
        ) );

        foreach ( $location_terms as $location_term ) {
            if ( ! empty( $location_term->slug ) ) {
                $locations[ $location_term->slug ] = $location_term->name;
            }
        }

        return $locations;
    }
    
}
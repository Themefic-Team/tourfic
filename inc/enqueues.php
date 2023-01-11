<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Dequeue frontend scripts to avoid conflict
 * 
 * @since 1.0
 */
if ( !function_exists('tf_dequeue_scripts') ) {
    function tf_dequeue_scripts(){

        // Flatpickr
        wp_deregister_script( 'flatpickr' );
		wp_dequeue_script( 'flatpickr' );
        wp_deregister_style('flatpickr');
        wp_dequeue_style( 'flatpickr' );
        // Fancybox
		wp_deregister_script( 'fancyBox' );
		wp_dequeue_script( 'fancyBox' );
        // Slick
        wp_deregister_style('slick');
        wp_dequeue_style( 'slick' );      
		wp_deregister_script( 'slick' );
		wp_dequeue_script( 'slick' );
		
    }
    add_filter( 'wp_enqueue_scripts', 'tf_dequeue_scripts', 9999 );
}

/**
 * Enqueue Frontend scripts
 * 
 * @since 1.0
 */
if ( !function_exists('tf_enqueue_scripts') ) {
	function tf_enqueue_scripts() {

        $flatpickr_cdn = !empty(tfopt( 'ftpr_cdn' )) ? tfopt( 'ftpr_cdn' ) : false;
        $flatpickr_locale = !empty(get_locale()) ? get_locale() : 'en_US';
        $allowed_locale = array('ar', 'bn_BD', 'de_DE', 'es_ES', 'fr_FR', 'hi_IN', 'it_IT', 'nl_NL', 'ru_RU', 'zh_CN');
        $fancy_cdn = !empty(tfopt( 'fnybx_cdn' )) ? tfopt( 'fnybx_cdn' ) : false;
        $slick_cdn = !empty(tfopt( 'slick_cdn' )) ? tfopt( 'slick_cdn' ) : false;
        $fa_cdn = !empty(tfopt( 'fa_cdn' )) ? tfopt( 'fa_cdn' ) : false;
        $min_css = (function_exists('is_tf_pro') && is_tf_pro() && !empty(tfopt( 'css_min' ))) ? '.min' : '';
		$min_js = (function_exists('is_tf_pro') && is_tf_pro() && !empty(tfopt( 'js_min' ))) ? '.min' : '';

        //Updated CSS
        wp_enqueue_style( 'tf-app-style', TF_ASSETS_URL . 'app/css/tourfic-style' . $min_css . '.css', null, TOURFIC );
        /*if ( get_post_type() == 'tf_hotel' ){
            wp_enqueue_style( 'tf-hotel-style', TF_ASSETS_URL . 'css/hotel' . $min_css . '.css', null, '' );
        }*/
        /*if ( get_post_type() == 'tf_tours' ){
            wp_enqueue_style( 'tf-tour-style', TF_ASSETS_URL . 'css/tour' . $min_css . '.css', null, '' );

            if(function_exists('is_tf_pro') && is_tf_pro()){
                wp_enqueue_script( 'Chart', '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js', array( 'jquery' ), '2.6.0', true );
                $meta = get_post_meta( get_the_ID(),'tf_tours_opt',true );
	            $itineraries = !empty($meta['itinerary']) ? $meta['itinerary'] : null;
                if( !empty($itineraries) && gettype($itineraries)=="string" ){
                    $tf_hotel_itineraries_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
                        return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                    }, $itineraries );
                    $itineraries = unserialize( $tf_hotel_itineraries_value );
                }
                $itinerarayday = [];
                $itineraraymeter = [];
                if( $itineraries ) {
                    foreach( $itineraries as $itinerary ){
                        $itinerarayday[] = !empty($itinerary['time']) ? $itinerary['time'] : '';
                        $itineraraymeter[] = !empty($itinerary['altitude']) ? intval($itinerary['altitude']) : '';
                    }
                }
                $showxaxis = !empty(tf_data_types(tfopt( 'itinerary-builder-setings' ))['itinerary-x-axis']) ? tf_data_types(tfopt( 'itinerary-builder-setings' ))['itinerary-x-axis'] : false;
                $showyaxis = !empty(tf_data_types(tfopt( 'itinerary-builder-setings' ))['itinerary-y-axis']) ? tf_data_types(tfopt( 'itinerary-builder-setings' ))['itinerary-y-axis'] : false;
                $showlinegraph = !empty(tf_data_types(tfopt( 'itinerary-builder-setings' ))['itinerary-line-graph']) ? tf_data_types(tfopt( 'itinerary-builder-setings' ))['itinerary-line-graph'] : false;
                $showitinerarychart = !empty(tf_data_types(tfopt( 'itinerary-builder-setings' ))['itinerary-chart']) ? tf_data_types(tfopt( 'itinerary-builder-setings' ))['itinerary-chart'] : false;
                $showitinerarystatus = !empty(tf_data_types(tfopt( 'itinerary-builder-setings' ))['itinerary-status']) ? tf_data_types(tfopt( 'itinerary-builder-setings' ))['itinerary-status'] : false;
                $elevvationmode = !empty(tf_data_types(tfopt( 'itinerary-builder-setings' ))['elevtion_type']) && tf_data_types(tfopt( 'itinerary-builder-setings' ))['elevtion_type']=="Feet" ? "Feet" : "Meter";
            }
        }*/
//        wp_enqueue_style( 'tf-search-style', TF_ASSETS_URL . 'css/search-result.css', null, '' );
//        wp_enqueue_style( 'tf-shortcode-style', TF_ASSETS_URL . 'css/shortcode.css', null, '' );

        /**
         * Flatpickr
         * v4.6.13
         */
        if ( $flatpickr_cdn == true && function_exists('is_tf_pro') && is_tf_pro() ) {
			wp_enqueue_style( 'flatpickr', '//cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css', array(), TOURFIC );
			wp_enqueue_script( 'flatpickr', '//cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js', array( 'jquery' ), TOURFIC, true );
            if (in_array($flatpickr_locale, $allowed_locale)) {
                wp_enqueue_script( 'flatpickr-locale', TF_ASSETS_URL . 'app/libs/flatpickr/l10n/' .$flatpickr_locale. '.min.js', array( 'jquery' ), TOURFIC, true );
            }
		} else {
            wp_enqueue_style( 'tf-flatpickr', TF_ASSETS_URL . 'app/libs/flatpickr/flatpickr.min.css', '', TOURFIC );
			wp_enqueue_script( 'tf-flatpickr', TF_ASSETS_URL . 'app/libs/flatpickr/flatpickr.min.js', array( 'jquery' ), TOURFIC, true );
            if (in_array($flatpickr_locale, $allowed_locale)) {
                wp_enqueue_script( 'tf-flatpickr-locale', TF_ASSETS_URL . 'app/libs/flatpickr/l10n/' .$flatpickr_locale. '.min.js', array( 'jquery' ), TOURFIC, true );
            }
        }

        /**
         * Range Slider
         */
        wp_enqueue_style( 'al-range-slider', TF_ASSETS_URL . 'app/libs/range-slider/al-range-slider.css', '', TOURFIC );
        wp_enqueue_script( 'al-range-slider', TF_ASSETS_URL . 'app/libs/range-slider/al-range-slider.js', array( 'jquery' ), TOURFIC, true );
        wp_enqueue_script( 'jquery-ui-autocomplete' );


        /**
         * Fancybox
         * v3.5.7
         */
        if ( $fancy_cdn == true && function_exists('is_tf_pro') && is_tf_pro() ) {
			wp_enqueue_style( 'fancyBox', '//cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css', array(), TOURFIC );
			wp_enqueue_script( 'fancyBox', '//cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js', array( 'jquery' ), TOURFIC, true );
		} else {
            wp_enqueue_style( 'fancybox', TF_ASSETS_URL . 'app/libs/fancybox/jquery.fancybox.min.css', '', TOURFIC );
			wp_enqueue_script( 'fancybox', TF_ASSETS_URL . 'app/libs/fancybox/jquery.fancybox.min.js', array( 'jquery' ), TOURFIC, true );
		}  
        
        /**
         * Slick
         * v1.8.1
         */
        if ( $slick_cdn == true && function_exists('is_tf_pro') && is_tf_pro() ) {
			wp_enqueue_style( 'slick', '//cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css', array(), TOURFIC );
			wp_enqueue_script( 'slick', '//cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', array( 'jquery' ), TOURFIC, true );
		} else {
            wp_enqueue_style( 'tf-slick', TF_ASSETS_URL . 'app/libs/slick/slick.css', '', TOURFIC );
			wp_enqueue_script( 'tf-slick', TF_ASSETS_URL . 'app/libs/slick/slick.min.js', array( 'jquery' ), TOURFIC, true );
		}  

        /**
         * Font Awesome Free
         * v5.15.4
         */ 
        if ( $fa_cdn == true && function_exists('is_tf_pro') && is_tf_pro() ) {
            wp_enqueue_style( 'font-awesome-5', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', '', TOURFIC );
        } else {
            wp_enqueue_style( 'font-awesome-5', TF_ASSETS_URL . 'app/libs/font-awesome/css/all.min.css', '', TOURFIC );
        }

        /**
         * Notyf
         * v3.0
         */
        wp_enqueue_style( 'notyf', TF_ASSETS_URL . 'app/libs/notyf/notyf.min.css', '', TOURFIC );
		wp_enqueue_script( 'notyf', TF_ASSETS_URL . 'app/libs/notyf/notyf.min.js', array( 'jquery' ), TOURFIC, true );


        /**
         * Hotel Location
         */
        $tf_hotellocationlists=array();
        $tf_hotellocation = get_terms( array(
            'taxonomy' => 'hotel_location',
            'orderby' => 'title',
            'order' => 'ASC',
            'hide_empty' => false,
            'hierarchical' => 0,
        ) );
        if ( $tf_hotellocation ) { 
        foreach( $tf_hotellocation as $term ) {
             $tf_hotellocationlists[] = $term->slug;
        } }

        $tfhotel_min_max = array(
            'posts_per_page'=> -1,
            'post_type'     => 'tf_hotel',
            'post_status' => 'publish'
        );
        $tfhotel_min_max_query = new WP_Query( $tfhotel_min_max ); 
        $tfhotel_min_maxprices = array();

        if( $tfhotel_min_max_query->have_posts() ):
            while( $tfhotel_min_max_query->have_posts() ) : $tfhotel_min_max_query->the_post();
                
                $meta = get_post_meta( get_the_ID( ), 'tf_hotels_opt', true );
                $rooms = !empty($meta['room']) ? $meta['room'] : '';
                if( !empty($rooms) && gettype($rooms)=="string" ){
                    $tf_hotel_rooms_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
                        return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                    }, $rooms );
                    $rooms = unserialize( $tf_hotel_rooms_value );
                }
                if(!empty($rooms)){
                    foreach($rooms as $singleroom){
                        if(!empty($singleroom['price'])){
                            $tfhotel_min_maxprices[]=$singleroom['price'];
                        }
                        if(!empty($singleroom['adult_price'])){
                            $tfhotel_min_maxprices[]=$singleroom['adult_price'];
                        }
                        if(!empty($singleroom['child_price'])){
                            $tfhotel_min_maxprices[]=$singleroom['child_price'];
                        }
                        if(!empty($singleroom['repeat_by_date'])){
                            foreach($singleroom['repeat_by_date'] as $singleavailroom){
                                if(!empty($singleavailroom['price'])){
                                    $tfhotel_min_maxprices[]=$singleavailroom['price'];
                                }
                                if(!empty($singleavailroom['adult_price'])){
                                    $tfhotel_min_maxprices[]=$singleavailroom['adult_price'];
                                }
                                if(!empty($singleavailroom['child_price'])){
                                    $tfhotel_min_maxprices[]=$singleavailroom['child_price'];
                                }
                            }
                        }
                    }
                }
                
            endwhile;

        endif; wp_reset_query(); 
        if( !empty( $tfhotel_min_maxprices ) && count($tfhotel_min_maxprices) > 1 ){
            $hotel_max_price_val = max($tfhotel_min_maxprices);
            $hotel_min_price_val = min($tfhotel_min_maxprices);
            if( $hotel_max_price_val==$hotel_min_price_val ){
                $hotel_max_price = max($tfhotel_min_maxprices);
                $hotel_min_price = 1;
            }else{
                $hotel_max_price = max($tfhotel_min_maxprices);
                $hotel_min_price = min($tfhotel_min_maxprices);
            }
        }
        if( !empty( $tfhotel_min_maxprices ) && count($tfhotel_min_maxprices) == 1 ){
            $hotel_max_price = max($tfhotel_min_maxprices);
            $hotel_min_price = 1;
        }
        if( empty( $tfhotel_min_maxprices ) ){
            $hotel_max_price = 0;
            $hotel_min_price = 0;
        }

        /**
         * Tour Destination
         */ 

        $tf_tourdestinationlists=array();
        $tf_tourdestination = get_terms( array(
            'taxonomy' => 'tour_destination',
            'orderby' => 'title',
            'order' => 'ASC',
            'hide_empty' => false,
            'hierarchical' => 0,
        ) );
        if ( $tf_tourdestination ) { 
        foreach( $tf_tourdestination as $term ) {
             $tf_tourdestinationlists[] = $term->slug;
        } }

        $tftours_min_max = array(
            'posts_per_page'=> -1,
            'post_type'     => 'tf_tours',
            'post_status' => 'publish'
        );

        $tftours_min_max_query = new WP_Query( $tftours_min_max );
        $tftours_min_maxprices = array();

        if( $tftours_min_max_query->have_posts() ):
            while( $tftours_min_max_query->have_posts() ) : $tftours_min_max_query->the_post();
                
                $meta = get_post_meta( get_the_ID( ), 'tf_tours_opt', true );
                if(!empty($meta['adult_price'])){
                    $tftours_min_maxprices[]=$meta['adult_price'];
                }
                if(!empty($meta['child_price'])){
                    $tftours_min_maxprices[]=$meta['child_price'];
                }
                if(!empty($meta['infant_price'])){
                    $tftours_min_maxprices[]=$meta['infant_price'];
                }
                if(!empty($meta['group_price'])){
                    $tftours_min_maxprices[]=$meta['group_price'];
                }
                
            endwhile;

        endif; wp_reset_query(); 
        if( !empty( $tftours_min_maxprices ) && count($tftours_min_maxprices) > 1 ){
            $tour_max_price_val = max($tftours_min_maxprices);
            $tour_min_price_val = min($tftours_min_maxprices);
            if( $tour_max_price_val==$tour_min_price_val ){
                $tour_max_price = max($tftours_min_maxprices);
                $tour_min_price = 1;
            }else{
                $tour_max_price = max($tftours_min_maxprices);
                $tour_min_price = min($tftours_min_maxprices);
            }
        }
        if( !empty( $tftours_min_maxprices ) && count($tftours_min_maxprices) == 1 ){
            $tour_max_price = max($tftours_min_maxprices);
            $tour_min_price = 1;
        }
        if( empty( $tftours_min_maxprices ) ){
            $tour_max_price = 0;
            $tour_min_price = 0;
        }


        /**
         * Custom
         */       

        wp_enqueue_script( 'tourfic', TF_ASSETS_URL . 'js/tourfic' . $min_js . '.js', '', TOURFIC, true );
        wp_localize_script( 'tourfic', 'tf_params',
            array(
                'nonce'        => wp_create_nonce( 'tf_ajax_nonce' ),
                'ajax_url'     => admin_url( 'admin-ajax.php' ),
                'single' => is_single(),
                'locations' => get_hotel_locations(),
                'tour_destinations' => get_tour_destinations(),
                'ajax_result_success' => __('Refreshed Successfully!', 'tourfic'),
                'wishlist_add' => __('Adding to wishlist...', 'tourfic'),
                'wishlist_added' => __('Item added to wishlist.', 'tourfic'),
                'wishlist_add_error' => __('Failed to add in wishlist!', 'tourfic'),
                'wishlist_removed' => __('Item removed from wishlist', 'tourfic'),
                'wishlist_remove_error' => __('Failed to remove from wishlist!', 'tourfic'),
                'field_required' => __('This field is required!', 'tourfic'),
                'adult' => __('Adult', 'tourfic'),
                'children' => __('Children', 'tourfic'),
                'infant' => __('Infant', 'tourfic'),
                'room' => __('Room', 'tourfic'),
                'sending_ques' => __('Sending your question...', 'tourfic'),
                'no_found' => __('Not Found', 'tourfic'),
                'tf_hotellocationlists' => isset($tf_hotellocationlists) ? $tf_hotellocationlists : '',
                'tf_hotel_max_price' => isset($hotel_max_price) ? $hotel_max_price : '',
                'tf_hotel_min_price' => isset($hotel_min_price) ? $hotel_min_price : '',
                'tf_tourdestinationlists' => isset($tf_tourdestinationlists) ? $tf_tourdestinationlists : '',
                'tf_tour_max_price' => isset($tour_max_price) ? $tour_max_price : '',
                'tf_tour_min_price' => isset($tour_min_price) ? $tour_min_price : '',
                'itinerarayday' => isset($itinerarayday) ? $itinerarayday : '',
                'itineraraymeter' => isset($itineraraymeter) ? $itineraraymeter : '',
                'showxaxis' => isset($showxaxis) ? $showxaxis : '',
                'showyaxis' => isset($showyaxis) ? $showyaxis : '',
                'showlinegraph' => isset($showlinegraph) ? $showlinegraph : '',
                'elevvationmode' => isset($elevvationmode) ? $elevvationmode : '',
                'showitinerarychart' => isset($showitinerarychart) ? $showitinerarychart : '',
                'showitinerarystatus' => isset($showitinerarystatus) ? $showitinerarystatus : ''
            )
        );
        //wp_enqueue_style( 'tf-responsive', TF_ASSETS_URL . 'css/old/responsive.css', '', TOURFIC );

        /**
         * Inline scripts
         */
        // Get single tour meta data
        global $post;
        if (!is_404() && !empty($post)) {
            $meta = !empty(get_post_meta( $post->ID, 'tf_tours_opt', true )) ? get_post_meta( $post->ID, 'tf_tours_opt', true ) : '';
        }
        $tour_type = !empty($meta['type']) ? $meta['type'] : '';

        # Inline scripts
        $inline_scripts = '';
        // JS Start
        $inline_scripts .= '(function ($) { $(document).ready(function () {';

        if ($tour_type == 'fixed') {
            // Disable date selection in calendar
            $inline_scripts .= '$(".flatpickr-day").css("pointer-events", "none"); ';
        }

        // JS end
        $inline_scripts .= '}); })(jQuery);';

        wp_add_inline_script( 'tourfic', $inline_scripts );

    }
    add_action( 'wp_enqueue_scripts', 'tf_enqueue_scripts' );
}

/**
 * Enqueue Admin scripts
 * 
 * @since 1.0
 */
if ( !function_exists('tf_enqueue_admin_scripts') ) {
    function tf_enqueue_admin_scripts($hook){ 

        /**
         * Notyf
         * v3.0
         */
        wp_enqueue_style( 'notyf', TF_ASSETS_URL . 'app/libs/notyf/notyf.min.css', '', TOURFIC );
		wp_enqueue_script( 'notyf', TF_ASSETS_URL . 'app/libs/notyf/notyf.min.js', array( 'jquery' ), TOURFIC, true );

        if ( $hook == "widgets.php" && function_exists( 'is_woocommerce' ) ) {

            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ?: '.min';

            $assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
            wp_register_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), '4.0.3' );
            wp_register_style( 'select2', WC()->plugin_url() . '/assets/css/select2.css' );

            wp_enqueue_script( 'select2' );
            wp_enqueue_style( 'select2' );

            $output = "
            (function($) {
                'use strict';
                jQuery(document).ready(function() { ";

            $output .= "$(document).on('tf_select2 widget-added widget-updated', function() {

                        jQuery('.tf-select2').each(function(){
                            if( !$(this).hasClass('select2-hidden-accessible') ){
                                $(this).select2({ width: '100%' });
                            }
                        });

                    });";

            $output .= "
                });
            })(jQuery);";

            wp_add_inline_script( 'select2', $output );

        }
            
    }
    add_action( 'admin_enqueue_scripts', 'tf_enqueue_admin_scripts' );
}

<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Single;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Tourfic\Classes\Helper;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Itinerary
 */
class Itinerary extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-itinerary';
	}

	public function get_title() {
		return esc_html__( 'Itinerary', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-google-maps';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'itinerary',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-itinerary'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-itinerary/before-style-controls', $this );
		$this->tf_itinerary_style_controls();
		do_action( 'tf/single-itinerary/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_itinerary_content',[
            'label' => esc_html__('Itinerary', 'tourfic'),
        ]);

        do_action( 'tf/single-itinerary/before-content/controls', $this );

		$this->add_control('itinerary_style',[
            'label' => esc_html__('Itinerary Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => [
                'style1' => esc_html__('Style 1', 'tourfic'),
                'style2' => esc_html__('Style 2', 'tourfic'),
                'style3' => esc_html__('Style 3', 'tourfic'),
            ],
        ]);

	    do_action( 'tf/single-itinerary/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_itinerary_style_controls() {
		$this->start_controls_section( 'itinerary_style_section', [
			'label' => esc_html__( 'Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control( "bg_color", [
			'label'     => __( 'Card Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-trip-feature-blocks .tf-feature-block" => 'background-color: {{VALUE}};',
			],
		] );

        // $this->add_control( "btn_color", [
		// 	'label'     => __( 'Text Color', 'tourfic' ),
		// 	'type'      => Controls_Manager::COLOR,
		// 	'selectors' => [
		// 		"{{WRAPPER}} .tf-single-action-btns a" => 'color: {{VALUE}};',
		// 		"{{WRAPPER}} .tf-single-action-btns a svg path" => 'fill: {{VALUE}};',
		// 	],
		// ] );

        // $this->add_group_control( Group_Control_Typography::get_type(), [
		// 	'name'     => "btn_typography",
		// 	'selector' => "{{WRAPPER}} .tf-single-action-btns a",
		// ] );

		$this->end_controls_section();
	}

	protected function render() {
        $settings  = $this->get_settings_for_display();
        $this->post_id   = get_the_ID();
        $this->post_type = get_post_type();

        if($this->post_type !== 'tf_tours'){
            return;
        }
	    $meta = get_post_meta( $this->post_id, 'tf_tours_opt', true );
        $itineraries     = !empty(Helper::tf_data_types($meta['itinerary'])) ? Helper::tf_data_types($meta['itinerary']) : null;
        $itinerary_map = ! empty( Helper::tfopt('itinerary_map') ) && function_exists('is_tf_pro') && is_tf_pro() ? Helper::tfopt('itinerary_map') : 0;
		$style = !empty($settings['itinerary_style']) ? $settings['itinerary_style'] : 'style1';
       
        if ( $itineraries ) {

            $itinerary_status = ! empty( tf_pro_data_types( tfopt( 'itinerary-builder-setings' ) )['itinerary-status'] ) ? tf_pro_data_types( tfopt( 'itinerary-builder-setings' ) )['itinerary-status'] : '';
            $itinerary_chart  = ! empty( tf_pro_data_types( tfopt( 'itinerary-builder-setings' ) )['itinerary-chart'] ) ? tf_pro_data_types( tfopt( 'itinerary-builder-setings' ) )['itinerary-chart'] : '';
            $tf_itinearay_downloader = isset( $meta['itinerary-downloader'] ) ? $meta['itinerary-downloader'] : '';

            // Map Type & Map Address
            $tf_openstreet_map = ! empty( tfopt( 'google-page-option' ) ) ? tfopt( 'google-page-option' ) : "default";
            $tf_google_map_key = !empty( tfopt( 'tf-googlemapapi' ) ) ? tfopt( 'tf-googlemapapi' ) : '';

            // Itinary Downloader Global Settings
            $itinary_download_global_opt = !empty(tfopt("itinerary-builder-setings")["itinerary-downloader"]) ? tfopt("itinerary-builder-setings")["itinerary-downloader"] : 0;

            $global_or_custom = "";
            if(!isset($meta["itenary_download_glbal_settings"])) {
                if($itinary_download_global_opt == 1) {
                    $global_or_custom = "global";   
                } 
                
                if( !empty($meta["itinerary-downloader"]) && $meta["itinerary-downloader"] == 1) {
                        $global_or_custom = "custom";
                }
            }
            $itinary_download_meta_setting = !empty($meta["itenary_download_glbal_settings"])? $meta["itenary_download_glbal_settings"] : $global_or_custom;

            if($itinary_download_meta_setting == "global" && $itinary_download_global_opt == 1) {
                $itinary_download_title = !empty(tfopt("itinerary-builder-setings")["itinerary-downloader-title"]) ? tfopt("itinerary-builder-setings")["itinerary-downloader-title"] : "";
                $itinary_download_des = !empty(tfopt("itinerary-builder-setings")["itinerary-downloader-desc"]) ? tfopt("itinerary-builder-setings")["itinerary-downloader-desc"] : "";
                $itinary_download_button = !empty(tfopt("itinerary-builder-setings")["itinerary-downloader-button"]) ? tfopt("itinerary-builder-setings")["itinerary-downloader-button"] : "";
            }else {
                $itinary_download_title = !empty($meta['itinerary-downloader-title']) ? esc_html($meta['itinerary-downloader-title']) : "";
                $itinary_download_des = !empty($meta['itinerary-downloader-desc']) ? esc_html($meta['itinerary-downloader-desc']) : "";
                $itinary_download_button = !empty($meta['itinerary-downloader-button']) ? esc_html($meta['itinerary-downloader-button']) : "";
            }

            $location = '';
            if( !empty($meta['location']) && Helper::tf_data_types($meta['location'])){
                $location = !empty( Helper::tf_data_types($meta['location'])['address'] ) ? Helper::tf_data_types($meta['location'])['address'] : $location;
        
                $location_latitude = !empty( Helper::tf_data_types($meta['location'])['latitude'] ) ? Helper::tf_data_types($meta['location'])['latitude'] : '';
                $location_longitude = !empty( Helper::tf_data_types($meta['location'])['longitude'] ) ? Helper::tf_data_types($meta['location'])['longitude'] : '';
                $location_zoom = !empty( Helper::tf_data_types($meta['location'])['zoom'] ) ? Helper::tf_data_types($meta['location'])['zoom'] : 2;
            }
            
            if( $style == "style1" ){ ?>
                <div class="tf-single-template__one">
                    <div class="tf-itinerary-wrapper">
                        <div class="section-title">
                            <h2 class="tf-title tf-section-title"><?php echo !empty($meta['itinerary-section-title']) ? esc_html($meta['itinerary-section-title']) : ''; ?></h2>
                        </div>
                        <?php  if ( ! empty( $itinerary_chart ) ) : ?>
                            <div class="chart">
                                <canvas id="tour-itinerary-chart" width="400" height="200"></canvas>
                            </div>
                        <?php endif; ?>
                        <div class="tf-itinerary-box tf-box">
                            <div class="tf-itinerary-items">
                                <?php 
                                $itineray_key = 1;
                                $itinerary_data = [];
                            
                                foreach ( $itineraries as $key => $itinerary ) {
                                    $locations = !empty($itinerary['loacation']) ? $itinerary['loacation'] : '';
                                    $location_lat = !empty($itinerary['loacation-latitude']) ? $itinerary['loacation-latitude'] : '';
                                    $location_long = !empty($itinerary['loacation-longitude']) ? $itinerary['loacation-longitude'] : '';
                                    $itn_title = !empty($itinerary['title']) ? $itinerary['title'] : '';
                                    $itn_time = !empty($itinerary['time']) ? $itinerary['time'] : '';
                                    $itn_image = !empty($itinerary['image']) ? $itinerary['image'] : '';
                                    $itn_desc = !empty($itinerary['desc']) ? $itinerary['desc'] : '';
                                    $itn_gal = !empty($itinerary['gallery_image']) ? explode(',', $itinerary['gallery_image']) : array();
                                    $itn_options = !empty($itinerary['itinerary-sleep-mode']) ? $itinerary['itinerary-sleep-mode'] : array();
                                    $sleepmodedata = Helper::tf_data_types( tfopt( 'itinerary-builder-setings' ) );

                                    $itn_gal_images = [];
                                    $itn_sleepmode = [];

                                    if( !empty($itn_desc) && strlen($itn_desc) > 75){

                                        $itn_desc =  Helper::tourfic_character_limit_callback($itn_desc, 75);
                                    }

                                    foreach($itn_gal as $img_id) {
                                        $itn_gal_images[] = wp_get_attachment_url( $img_id, 'full' );
                                    }
                                    $sleep_mode_arr = [];
                                    if( !empty( $sleepmodedata['itinerary-field'] )) {
                                        foreach ($sleepmodedata['itinerary-field'] as $key => $value) {
                                            $sleep_mode_arr[$key] = $value['sleep-mode-title'];
                                        }
                                    }
                                    foreach($itn_options as $options) {

                                        $search_itinerary_result = '';
                                        if ( ! empty( $options['sleepmode'] ) && ! empty( $sleepmodedata['itinerary-field'] ) ) {
                                            $search_itinerary_result = array_search( $options['sleepmode'], $sleep_mode_arr );
                                        }

                                        $itn_sleepmode[] = array(
                                        "mode" => !empty($options["sleepmode"]) ? $options["sleepmode"] : '',
                                        "icon" => ! empty( $sleepmodedata['itinerary-field'][ $search_itinerary_result ]['sleep-mode-icon'] ) ? $sleepmodedata['itinerary-field'][ $search_itinerary_result ]['sleep-mode-icon'] : 'fas fa-bed'
                                        );
                                    }

                                    $itinerary_data[$key]["title"] = $itn_title;
                                    $itinerary_data[$key]["location"] = $locations;
                                    $itinerary_data[$key]["latiude"] = $location_lat;
                                    $itinerary_data[$key]["longitude"] = $location_long;
                                    $itinerary_data[$key]["time"] = $itn_time;
                                    $itinerary_data[$key]["desc"] = $itn_desc;
                                    $itinerary_data[$key]["images"] = $itn_image;
                                    $itinerary_data[$key]["gal_imgs"] = $itn_gal_images;
                                    $itinerary_data[$key]["itn_options"] = $itn_sleepmode;

                                ?>
                                <div class="tf-single-itinerary-item <?php echo $itineray_key==1 ? esc_attr( 'active' ) : ''; ?>">
                                    <div class="tf-itinerary-title">
                                        <h4>
                                            <span class="accordion-checke"></span>
                                            <span class="itinerary-day"><?php echo esc_html( $itinerary['time'] ) ?> - </span> <?php echo esc_html( $itinerary['title'] ); ?>
                                        </h4>
                                    </div>
                                    <div class="tf-itinerary-content-box" style="<?php echo $itineray_key==1 ? esc_attr( 'display: block;' ) : ''; ?>">
                                        <div class="tf-itinerary-content tf-mt-16 tf-flex-gap-16 tf-flex">
                                            <?php if ( $itinerary['image'] ) { ?>
                                                <div class="tf-itinerary-content-img">
                                                    <img src="<?php echo esc_url( $itinerary['image'] ); ?>" alt="<?php esc_html_e("Itinerary Image","tourfic"); ?>" />
                                                </div>
                                            <?php } ?>
                                            <div class="<?php echo !empty($itinerary['image']) ? esc_attr('tf-itinerary-content-details') : ''; ?>">
                                            <p><?php echo wp_kses_post( wpautop($itinerary['desc']) ); ?></p>
                                            </div>
                                        </div>
                                        <?php if ( ! empty( $itinerary['gallery_image'] ) ) {
                                            $tf_itinerary_gallery_ids = explode( ',', $itinerary['gallery_image'] );
                                            ?>
                                            <div class="ininerary-other-gallery">
                                                <?php
                                                if ( ! empty( $itinerary['gallery_image'] ) && ! empty( $tf_itinerary_gallery_ids ) ) {
                                                    
                                                    $gallery_count = 1;
                                                    foreach ( $tf_itinerary_gallery_ids as $key => $gallery_item_id ) {
                                                    $itinerary_gallery_image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
                                                    ?>
                                                    <a class="<?php echo  $gallery_count==5 ? esc_attr( 'tf-gallery-more' ) : ''; ?>" data-fancybox="tf-itinerary-gallery" href="<?php echo esc_url( $itinerary_gallery_image_url); ?>">
                                                        <img src="<?php echo esc_url( $itinerary_gallery_image_url); ?>" alt="" />
                                                    </a>
                                                    <?php $gallery_count++;}
                                                } ?>
                                            </div>
                                        <?php } ?>
                                        <div class="tf-itinerary-meta-data tf-flex tf-flex-gap-16 tf-mt-24">
                                            
                                            <?php if ( ! empty( $itinerary['duration'] ) && ! empty( $itinerary['timetype'] ) ) { ?>
                                            <div class="tf-itinerary-single-meta">
                                                <li class="tf-flex tf-flex-gap-8">
                                                    <i class="fa-regular fa-clock"></i> 
                                                    <?php esc_html_e( $itinerary['duration'] ); ?> <?php esc_html_e( $itinerary['timetype'] ); ?><?php echo ! empty( intval( $itinerary['duration'] ) ) && intval( $itinerary['duration'] ) > 1 ? 's' : ''; ?>
                                                </li>
                                            </div>
                                            <?php } ?>
                                            <?php if ( ! empty( $itinerary['meals'] ) ) { ?>
                                            <div class="tf-itinerary-single-meta">
                                                <li class="tf-flex tf-flex-gap-8">
                                                    <i class="fas fa-utensils"></i>
                                                    <?php 
                                                    $itinerary_options = ! empty( tf_pro_data_types ( tfopt( 'itinerary-builder-setings' ) ) ) ? tf_pro_data_types( tfopt( 'itinerary-builder-setings' ) ) : '';
                                                    $tf_total_meal =  [];
                                                    if( !empty( $itinerary_options['meals'] ) ){
                                                        $meals = $itinerary_options['meals'];
                                                        foreach ( $meals as $key => $meal ){
                                                            if (in_array($meal['meal'].$key, $itinerary['meals'])){
                                                                $tf_total_meal[] = $meal['meal'];
                                                            }
                                                        }
                                                    }
                                                    echo esc_attr(join(", ",$tf_total_meal));
                                                    ?>
                                                </li>
                                            </div>
                                            <?php } ?>
                                            <?php
                                            if ( ! empty( $itinerary['itinerary-sleep-mode'] ) ) {
                                                $sleep_mode_arr = [];
                                                if(!empty($sleepmodedata['itinerary-field'])){
                                                    foreach ($sleepmodedata['itinerary-field'] as $key => $value) {
                                                        $sleep_mode_arr[$key] = $value['sleep-mode-title'];
                                                    }
                                                }
                                            
                                                foreach ( $itinerary['itinerary-sleep-mode'] as $key => $sleepmodesingle ) {
                                                    $sleepmodedata = Helper::tf_data_types( tfopt( 'itinerary-builder-setings' ) );
                                                    $search_itinerary_result = '';
                                                    if ( ! empty( $sleepmodesingle['sleepmode'] ) && ! empty( $sleepmodedata['itinerary-field'] ) ) {
                                                        $search_itinerary_result = array_search( $sleepmodesingle['sleepmode'], $sleep_mode_arr);
                                                    }
                                                    ?>
                                                    <?php
                                                    if ( ! empty( $sleepmodesingle['sleepmode'] ) ) { ?>
                                                    <div class="tf-itinerary-single-meta" style="position: relative;">
                                                        <ul>
                                                            <li class="tf-flex tf-flex-gap-8">
                                                                <a <?php echo !empty($sleepmodesingle['sleep']) ? 'data-fancybox' : ''; ?> id="<?php echo sanitize_title( $sleepmodesingle['sleepmode'] ); ?><?php echo sanitize_key( $key ); ?><?php echo sanitize_key( $itineray_key ); ?>" href="javascript:void(0);">
                                                                <i class="<?php echo ! empty( $sleepmodedata['itinerary-field'][ $search_itinerary_result ]['sleep-mode-icon'] ) ? wp_kses_post($sleepmodedata['itinerary-field'][ $search_itinerary_result ]['sleep-mode-icon']) : 'fas fa-bed'; ?>"></i>
                                                                <?php esc_html_e( $sleepmodesingle['sleepmode'] ); ?>
                                                                <?php if (!empty( $sleepmodesingle['sleep'] )) : ?>
                                                                    <i class="fas fa-info-circle" style="padding-left: 2px;font-size: 14px;"></i>
                                                                <?php endif; ?>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                        <div style="display: none;" id="<?php echo sanitize_title( $sleepmodesingle['sleepmode'] ); ?><?php echo sanitize_key( $key ); ?><?php echo sanitize_key( $itineray_key ); ?>" class="tour-itinerary-sleep">
                                                            <div class="tf-tours-booking-deposit">
                                                                <div class="tf-tours-booking-deposit-text">
                                                                <?php
                                                                if ( ! empty( $sleepmodesingle['sleep'] ) ) { ?>
                                                                    <?php echo wp_kses_post( $sleepmodesingle['sleep'] ); ?>
                                                                <?php } ?>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                    
                                                <?php }
                                            } ?>
                                        </div>
                                    </div>
                                </div>
                                <?php $itineray_key++; } ?>
                            </div>
                        </div>
                        <?php if ( $tf_itinearay_downloader === '1' || $itinary_download_global_opt == 1) { ?>
                        <div class="tf-itinerary-downloader-option tf-mt-30 tf-box">
                            <div class="tf-itinerary-downloader-inner tf-flex tf-flex-align-center tf-flex-gap-8 tf-flex-space-bttn">
                                <div class="itinerary-downloader-left">
                                    <h3><?php echo  esc_html__($itinary_download_title, "tourfic"); ?></h3>
                                    <p><?php echo esc_html__(stripslashes($itinary_download_des), "tourfic"); ?></p>
                                </div>
                                <div class="itinerary-downloader-right">
                                    <a class="tf_btn" id="<?php echo esc_attr(get_the_ID()); ?>" href="?tours=<?php echo esc_attr(get_the_ID()); ?>"><?php echo esc_html__($itinary_download_button, "tourfic") ?> <i class="fa-solid fa-download"></i></a>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if( $itinerary_map == 1 && $tf_openstreet_map == "googlemap" && !empty($tf_google_map_key) ){ ?>
                            <div class="tf-trip-map-wrapper tf-mt-30" id="tf-map" data-itn-datas="<?php echo htmlspecialchars(wp_json_encode( $itinerary_data), ENT_QUOTES); ?>"></div>
                        <?php }else{ ?>
                            <div class="tf-trip-map-wrapper tf-mt-30 tf-template-section" id="tf-tour-map">
                                <div class="tf-map-area">
                                    <?php if( $tf_openstreet_map!="default" ){ ?>
                                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                    <?php } ?>

                                    <?php if ( $tf_openstreet_map=="default" && (empty($location_latitude) || empty($location_longitude)) ) {  ?>
                                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php 
            } elseif( $style == "style2" ){ 
                ?>
                <div class="tf-single-template__two">
                    <div class="tf-itinerary-wrapper" id="tf-tour-itinerary">
                        <div class="section-title">
                            <h2 class="tf-title tf-section-title"><?php echo !empty($meta['itinerary-section-title']) ? esc_html($meta['itinerary-section-title']) : ''; ?></h2>
                            <?php if ( $tf_itinearay_downloader === '1' || $itinary_download_global_opt == 1) { ?>
                            <a href="?tours=<?php echo esc_attr(get_the_ID()); ?>"><?php esc_html_e("Download Plan", "tourfic"); ?> <i class="ri-download-line"></i></a>
                            <?php } ?>
                        </div>
                        <?php  if ( ! empty( $itinerary_chart ) ) : ?>
                            <div class="chart">
                                <canvas id="tour-itinerary-chart" width="400" height="200"></canvas>
                            </div>
                        <?php endif; ?>
                        <div class="tf-itinerary-wrapper">

                        <?php
                        $itineray_key = 1;
                        $itinerary_data = [];

                        foreach ( $itineraries as $key => $itinerary ) {

                            $locations = !empty($itinerary['loacation']) ? $itinerary['loacation'] : '';
                            $location_lat = !empty($itinerary['loacation-latitude']) ? $itinerary['loacation-latitude'] : '';
                            $location_long = !empty($itinerary['loacation-longitude']) ? $itinerary['loacation-longitude'] : '';
                            $itn_title = !empty($itinerary['title']) ? $itinerary['title'] : '';
                            $itn_time = !empty($itinerary['time']) ? $itinerary['time'] : '';
                            $itn_image = !empty($itinerary['image']) ? $itinerary['image'] : '';
                            $itn_desc = !empty($itinerary['desc']) ? $itinerary['desc'] : '';
                            $itn_gal = !empty($itinerary['gallery_image']) ? explode(',', $itinerary['gallery_image']) : array();
                            $itn_options = !empty($itinerary['itinerary-sleep-mode']) ? $itinerary['itinerary-sleep-mode'] : array();
                            $sleepmodedata = Helper::tf_data_types( tfopt( 'itinerary-builder-setings' ) );

                            $itn_gal_images = [];
                            $itn_sleepmode = [];

                            if( !empty($itn_desc) && strlen($itn_desc) > 75){
                                $itn_desc =  Helper::tourfic_character_limit_callback($itn_desc, 75);
                            }

                            foreach($itn_gal as $img_id) {
                                $itn_gal_images[] = wp_get_attachment_url( $img_id, 'full' );
                            }
                            $sleep_mode_arr = [];
                            if(!empty($sleepmodedata) && is_array($sleepmodedata)){
                                if( array_key_exists('itinerary-field', $sleepmodedata) && !empty( $sleepmodedata['itinerary-field'] ) ) {
                                    foreach ($sleepmodedata['itinerary-field'] as $key => $value) {
                                        $sleep_mode_arr[$key] = $value['sleep-mode-title'];
                                    }
                                }
                            }
                            foreach($itn_options as $options) {
                                $search_itinerary_result = '';

                                if ( ! empty( $options['sleepmode'] ) && ! empty( $sleepmodedata['itinerary-field'] ) ) {
                                    $search_itinerary_result = array_search( $options['sleepmode'], $sleep_mode_arr );
                                }

                                $itn_sleepmode[] = array(
                                "mode" => !empty($options["sleepmode"]) ? $options["sleepmode"] : '',
                                "icon" => ! empty( $sleepmodedata['itinerary-field'][ $search_itinerary_result ]['sleep-mode-icon'] ) ? $sleepmodedata['itinerary-field'][ $search_itinerary_result ]['sleep-mode-icon'] : 'fas fa-bed'
                                );
                            }

                            $itinerary_data[$key]["title"] = $itn_title;
                            $itinerary_data[$key]["location"] = $locations;
                            $itinerary_data[$key]["latiude"] = $location_lat;
                            $itinerary_data[$key]["longitude"] = $location_long;
                            $itinerary_data[$key]["time"] = $itn_time;
                            $itinerary_data[$key]["desc"] = $itn_desc;
                            $itinerary_data[$key]["images"] = $itn_image;
                            $itinerary_data[$key]["gal_imgs"] = $itn_gal_images;
                            $itinerary_data[$key]["itn_options"] = $itn_sleepmode;

                        ?>
                            <div class="tf-single-itinerary">
                                <div class="tf-itinerary-title">
                                    <span class="tf-head-title">
                                        <span class="tf-itinerary-time">
                                            <?php echo esc_html( $itinerary['time'] ) ?>
                                        </span>
                                        <span class="tf-itinerary-title-text">
                                            <?php echo esc_html( $itinerary['title'] ); ?>
                                        </span>
                                    </span>
                                    <i class="fa-solid fa-chevron-down"></i>
                                </div>
                                <div class="tf-itinerary-content-wrap" style="display: none;">
                                    <div class="tf-itinerary-content">
                                        <div class="tf-itinerary-content-details">
                                        <?php echo wp_kses_post( $itinerary['desc'] ); ?>
                                        <div class="tf-itinerary-more-offer">
                                        <?php if ( ! empty( $itinerary['duration'] ) && ! empty( $itinerary['timetype'] ) ) { ?>
                                            <div class="tf-itinerary-single-meta">
                                                <li class="tf-flex tf-flex-gap-8">
                                                    <i class="fa-regular fa-clock"></i> 
                                                    <?php esc_html_e( $itinerary['duration'] ); ?> <?php esc_html_e( $itinerary['timetype'] ); ?><?php echo ! empty( intval( $itinerary['duration'] ) ) && intval( $itinerary['duration'] ) > 1 ? 's' : ''; ?>
                                                </li>
                                            </div>
                                            <?php } ?>
                                            <?php if ( ! empty( $itinerary['meals'] ) ) { ?>
                                            <div class="tf-itinerary-single-meta">
                                                <li class="tf-flex tf-flex-gap-8">
                                                    <i class="fas fa-utensils"></i>
                                                    <?php 
                                                    $itinerary_options = ! empty( tf_pro_data_types ( tfopt( 'itinerary-builder-setings' ) ) ) ? tf_pro_data_types( tfopt( 'itinerary-builder-setings' ) ) : '';
                                                    $tf_total_meal =  [];
                                                    if( !empty( $itinerary_options['meals'] ) ){
                                                        $meals = $itinerary_options['meals'];
                                                        foreach ( $meals as $key => $meal ){
                                                            if (in_array($meal['meal'].$key, $itinerary['meals'])){
                                                                $tf_total_meal[] = $meal['meal'];
                                                            }
                                                        }
                                                    }
                                                    echo esc_attr(join(", ",$tf_total_meal));
                                                    ?>
                                                </li>
                                            </div>
                                            <?php } ?>
                                            <?php
                                            if ( ! empty( $itinerary['itinerary-sleep-mode'] ) ) {
                                                $sleep_mode_arr = [];
                                                foreach ($sleepmodedata['itinerary-field'] as $key => $value) {
                                                    $sleep_mode_arr[$key] = $value['sleep-mode-title'];
                                                }
                                                foreach ( $itinerary['itinerary-sleep-mode'] as $key => $sleepmodesingle ) {
                                                    $sleepmodedata = Helper::tf_data_types( tfopt( 'itinerary-builder-setings' ) );
                                                    $search_itinerary_result = '';
                                                    if ( ! empty( $sleepmodesingle['sleepmode'] ) && ! empty( $sleepmodedata['itinerary-field'] ) ) {
                                                        $search_itinerary_result = array_search( $sleepmodesingle['sleepmode'], $sleep_mode_arr );
                                                    }
                                                    ?>
                                                    <?php
                                                    if ( ! empty( $sleepmodesingle['sleepmode'] ) ) { ?>
                                                    <div class="tf-itinerary-single-meta">
                                                    <ul>
                                                            <li class="tf-flex tf-flex-gap-8">
                                                                <a <?php echo !empty($sleepmodesingle['sleep']) ? 'data-fancybox' : ''; ?> id="<?php echo sanitize_title( $sleepmodesingle['sleepmode'] ); ?><?php echo sanitize_key( $key ); ?><?php echo sanitize_key( $itineray_key ); ?>" href="javascript:void(0);">
                                                                <i class="<?php echo ! empty( $sleepmodedata['itinerary-field'][ $search_itinerary_result ]['sleep-mode-icon'] ) ? wp_kses_post($sleepmodedata['itinerary-field'][ $search_itinerary_result ]['sleep-mode-icon']) : 'fas fa-bed'; ?>"></i>
                                                                <?php esc_html_e( $sleepmodesingle['sleepmode'] ); ?>
                                                                <?php if (!empty( $sleepmodesingle['sleep'] )) : ?>
                                                                    <i class="fas fa-info-circle" style="padding-left: 5px;font-size: 14px;"></i>
                                                                <?php endif; ?>

                                                                </a>
                                                            </li>
                                                    </ul>
                                                    <div style="display: none;" id="<?php echo sanitize_title( $sleepmodesingle['sleepmode'] ); ?><?php echo sanitize_key( $key ); ?><?php echo sanitize_key( $itineray_key ); ?>" class="tour-itinerary-sleep">
                                                            <div class="tf-tours-booking-deposit">
                                                                <div class="tf-tours-booking-deposit-text">
                                                                <?php
                                                                if ( ! empty( $sleepmodesingle['sleep'] ) ) { ?>
                                                                    <?php echo wp_kses_post( $sleepmodesingle['sleep'] ); ?>
                                                                <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <?php } ?>
                                            
                                                <?php }
                                            } ?>
                                            </div>
                                        </div>
                                        <div class="tf-itinerary-content-images">
                                            <?php if ( $itinerary['image'] ) { ?>
                                                <img src="<?php echo esc_url( $itinerary['image'] ); ?>" alt="<?php esc_html_e("Itinerary Image","tourfic"); ?>" />
                                            <?php } ?>
                                            <?php
                                            if ( ! empty( $itinerary['gallery_image'] ) ) {
                                                $tf_itinerary_gallery_ids = explode( ',', $itinerary['gallery_image'] );
                                            ?>
                                            <div class="ininerary-other-gallery">
                                                <?php
                                                if ( ! empty( $itinerary['gallery_image'] ) && ! empty( $tf_itinerary_gallery_ids ) ) {
                                                    
                                                    $gallery_count = 1;
                                                    foreach ( $tf_itinerary_gallery_ids as $key => $gallery_item_id ) {
                                                    $itinerary_gallery_image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
                                                    ?>
                                                    
                                                    <?php 
                                                    if($gallery_count==2){ ?>
                                                        <a class="tf-itinerary-gallery-more" data-fancybox="tf-itinerary-gallery" href="<?php echo esc_url( $itinerary_gallery_image_url); ?>" style="background: linear-gradient(0deg, rgba(48, 40, 28, 0.70) 0%, rgba(48, 40, 28, 0.70) 100%), url(<?php echo esc_url( $itinerary_gallery_image_url); ?>), lightgray 50% / cover no-repeat;">
                                                            <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g id="content">
                                                                <path id="Rectangle 2111" d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5" stroke="#FDF9F4" stroke-width="1.5"></path>
                                                                <path id="Rectangle 2109" d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z" stroke="#FDF9F4" stroke-width="1.5"></path>
                                                                <path id="Vector" d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17" stroke="#FDF9F4" stroke-width="1.5" stroke-linejoin="round"></path>
                                                                <path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                </g>
                                                            </svg>
                                                        </a>
                                                    <?php }else{ ?>
                                                        <a data-fancybox="tf-itinerary-gallery" href="<?php echo esc_url( $itinerary_gallery_image_url); ?>">
                                                        <img src="<?php echo esc_url( $itinerary_gallery_image_url); ?>" alt="" />
                                                    </a>
                                                    <?php } ?>
                                                    <?php $gallery_count++;}
                                                } ?>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php $itineray_key++; } ?>

                        </div>
                        <?php if ( $location): ?>
                        <!-- Map start -->
                        <div id="tf-map" class="tf-itinerary-map" data-itn-datas="<?php echo htmlspecialchars(wp_json_encode( $itinerary_data), ENT_QUOTES); ?>">
                        <?php if ( $tf_openstreet_map=="default" && !empty($location_latitude) && !empty($location_longitude) && empty($tf_google_map_key) ) {  ?>
                            <div id="tour-location" style="height: 450px;"></div>
                        <?php } ?>
                        <?php if ( $tf_openstreet_map=="default" && (empty($location_latitude) || empty($location_longitude)) && empty($tf_google_map_key) ) {  ?>
                            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        <?php } ?>
                        <?php if( $tf_openstreet_map!="default" && !empty($tf_google_map_key) ){ ?>
                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        <?php } ?>
                        </div>
                        <!-- Map End -->
                        <?php endif; ?>
                    </div>
                </div>
                <?php 
            } elseif( $style == "style3" ) { 
                ?>
                <div class="tf-single-template__legacy">
                    <div class="tf-travel-itinerary-wrapper gray-wrap">
                        <div class="tf-container">
                            <div class="tf-travel-itinerary-content">
                                <h2 class="section-heading"><?php echo !empty($meta['itinerary-section-title']) ? esc_html($meta['itinerary-section-title']) : ''; ?></h2>
                                <div class="tf-itineraray-chart">
                                    <?php
                                    if ( ! empty( $itinerary_chart ) ) { ?>
                                        <div class="chart">
                                            <canvas id="tour-itinerary-chart" width="400" height="200"></canvas>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="tf-accordion-switcher">
                                    <label class="switch">
                                        <input type="checkbox" id="itinerary-switcher" <?php echo ! empty( $itinerary_status ) ? 'checked' : ''; ?>>
                                        <span class="switcher round"></span>
                                    </label>
                                    <span><?php esc_html_e( "Expand/Close", 'tourfic' ); ?></span>
                                </div>
                                <div class="tf-travel-itinerary-items-wrapper">
                                    <?php $itinerary_uni_key=0; 
                                    //store all the locations of itinerary
                                    $itinerary_data = [];

                                    foreach ( $itineraries as $key => $itinerary ) { 

                                        $locations = !empty($itinerary['loacation']) ? $itinerary['loacation'] : '';
                                        $location_lat = !empty($itinerary['loacation-latitude']) ? $itinerary['loacation-latitude'] : '';
                                        $location_long = !empty($itinerary['loacation-longitude']) ? $itinerary['loacation-longitude'] : '';
                                        $itn_title = !empty($itinerary['title']) ? $itinerary['title'] : '';
                                        $itn_time = !empty($itinerary['time']) ? $itinerary['time'] : '';
                                        $itn_image = !empty($itinerary['image']) ? $itinerary['image'] : '';
                                        $itn_desc = !empty($itinerary['desc']) ? $itinerary['desc'] : '';
                                        $itn_gal = !empty($itinerary['gallery_image']) ? explode(',', $itinerary['gallery_image']) : array();
                                        $itn_options = !empty($itinerary['itinerary-sleep-mode']) ? $itinerary['itinerary-sleep-mode'] : array();
                                        $sleepmodedata = Helper::tf_data_types( tfopt( 'itinerary-builder-setings' ) );

                                        $itn_gal_images = [];
                                        $itn_sleepmode = [];

                                        if( !empty($itn_desc) && strlen($itn_desc) > 75){
                                            $itn_desc =  Helper::tourfic_character_limit_callback($itn_desc, 75);
                                        }

                                        foreach($itn_gal as $img_id) {
                                            $itn_gal_images[] = wp_get_attachment_url( $img_id, 'full' );
                                        }
                                        $sleep_mode_arr = [];
                                        if( !empty( $sleepmodedata['itinerary-field'] ) ){
                                            foreach ($sleepmodedata['itinerary-field'] as $key => $value) {
                                                $sleep_mode_arr[$key] = $value['sleep-mode-title'];
                                            }
                                        }
                                        foreach($itn_options as $options) {
                                            $search_itinerary_result = '';

                                            if ( ! empty( $options['sleepmode'] ) && ! empty( $sleepmodedata['itinerary-field'] ) ) {
                                                $search_itinerary_result = array_search( $options['sleepmode'], $sleep_mode_arr );
                                            }

                                            $itn_sleepmode[] = array(
                                            "mode" => !empty($options["sleepmode"]) ? $options["sleepmode"] : '',
                                            "icon" => ! empty( $sleepmodedata['itinerary-field'][ $search_itinerary_result ]['sleep-mode-icon'] ) ? $sleepmodedata['itinerary-field'][ $search_itinerary_result ]['sleep-mode-icon'] : 'fas fa-bed'
                                            );
                                        }

                                        $itinerary_data[$key]["title"] = $itn_title;
                                        $itinerary_data[$key]["location"] = $locations;
                                        $itinerary_data[$key]["latiude"] = $location_lat;
                                        $itinerary_data[$key]["longitude"] = $location_long;
                                        $itinerary_data[$key]["time"] = $itn_time;
                                        $itinerary_data[$key]["desc"] = $itn_desc;
                                        $itinerary_data[$key]["images"] = $itn_image;
                                        $itinerary_data[$key]["gal_imgs"] = $itn_gal_images;
                                        $itinerary_data[$key]["itn_options"] = $itn_sleepmode;

                                        ?>
                                    
                                        <div id="tf-accordion-wrapper" class="tf-ininerary-accordion-wrapper">
                                            <div class="tf-accordion-head tf-ininerary-accordion-head">
                                                <div class="tf-travel-time">
                                                    <span><?php echo esc_html( $itinerary['time'] ) ?></span>
                                                </div>
                                                <h4><?php echo esc_html( $itinerary['title'] ); ?></h4>
                                                <i class="fas fa-angle-down arrow <?php echo ! empty( $itinerary_status ) ? 'arrow-animate' : ''; ?>"></i>
                                            </div>
                                            <div class="tf-accordion-content tf-ininerary-content" style="<?php echo ! empty( $itinerary_status ) ? 'display:block' : ''; ?>">
                                                <div class="tf-travel-desc">
                                                    <?php
                                                    if ( ! empty( $itinerary['image'] ) ) {
                                                        echo '<div class="tf-ititnerary-img"><a class="tf-itinerary-gallery" href="' . esc_url( $itinerary['image'] ) . '"><img src="' . esc_url( $itinerary['image'] ) . '"></a></div>';
                                                    } ?>
                                                    <div class="trav-cont tf-travel-description">    
                                                        <p><?php echo wp_kses_post( $itinerary['desc'] ); ?></p>
                                                    </div>
                                                </div>
                                                <?php
                                                if ( ! empty( $itinerary['gallery_image'] ) ) {
                                                    $tf_itinerary_gallery_ids = explode( ',', $itinerary['gallery_image'] );
                                                
                                                ?>
                                                <div class="ininerary-other-gallery">
                                                    <?php
                                                    if ( ! empty( $itinerary['gallery_image'] ) && ! empty( $tf_itinerary_gallery_ids ) ) {
                                                        foreach ( $tf_itinerary_gallery_ids as $key => $gallery_item_id ) {
                                                            ?>
                                                            <div class="ininerary-gallery-single">
                                                                <?php
                                                                $itinerary_gallery_image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
                                                                echo '<a data-fancybox="tf-itinerary-gallery" href="' . esc_url( $itinerary_gallery_image_url ) . '"><img src="' . esc_url( $itinerary_gallery_image_url ) . '" alt="" /></a>';
                                                                ?>
                                                            </div>
                                                        <?php }
                                                    } ?>
                                                </div>
                                                <?php } ?>
                                                <div class="ininerary-other-info">
                                                    <ul>
                                                        <?php if ( ! empty( $itinerary['duration'] ) && ! empty( $itinerary['timetype'] ) ) { ?>
                                                            <li>
                                                                <i class="far fa-clock"></i> <?php esc_html_e( $itinerary['duration'] ); ?> <?php esc_html_e( $itinerary['timetype'] ); ?><?php echo ! empty( intval( $itinerary['duration'] ) ) && intval( $itinerary['duration'] ) > 1 ? 's' : ''; ?>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if ( ! empty( $itinerary['meals'] ) ) { ?>
                                                            <li><i class="fas fa-utensils"></i>
                                                                <?php 
                                                                $itinerary_options = ! empty( tf_pro_data_types ( tfopt( 'itinerary-builder-setings' ) ) ) ? tf_pro_data_types( tfopt( 'itinerary-builder-setings' ) ) : '';
                                                                $tf_total_meal =  [];
                                                                if( !empty( $itinerary_options['meals'] ) ){
                                                                    $meals = $itinerary_options['meals'];
                                                                    foreach ( $meals as $key => $meal ){
                                                                        if (in_array($meal['meal'].$key, $itinerary['meals'])){
                                                                            $tf_total_meal[] = $meal['meal'];
                                                                        }
                                                                    }
                                                                }
                                                                echo esc_attr(join(", ",$tf_total_meal));
                                                                ?>
                                                            </li>
                                                        <?php } ?>
                                                        <?php
                                                        if ( ! empty( $itinerary['itinerary-sleep-mode'] ) ) {
                                                            $sleep_mode_arr = [];
                                                            foreach ($sleepmodedata['itinerary-field'] as $key => $value) {
                                                                $sleep_mode_arr[$key] = $value['sleep-mode-title'];
                                                            }
                                                            foreach ( $itinerary['itinerary-sleep-mode'] as $key => $sleepmodesingle ) {
                                                                $sleepmodedata = Helper::tf_data_types( tfopt( 'itinerary-builder-setings' ) );
                                                                $search_itinerary_result = '';
                                                                if ( ! empty( $sleepmodesingle['sleepmode'] ) && ! empty( $sleepmodedata['itinerary-field'] ) ) {
                                                                    $search_itinerary_result = array_search( $sleepmodesingle['sleepmode'], $sleep_mode_arr );
                                                                }
                                                                ?>
                                                                <?php
                                                                if ( ! empty( $sleepmodesingle['sleepmode'] ) ) { ?>
                                                                <li style="position: relative;">
                                                                    <a <?php echo !empty($sleepmodesingle['sleep']) ? 'data-fancybox' : ''; ?> id="<?php echo sanitize_title( $sleepmodesingle['sleepmode'] ); ?><?php echo sanitize_key( $key ); ?><?php echo sanitize_key( $itinerary_uni_key ); ?>" href="javascript:void(0);"><i
                                                                                class="<?php echo ! empty( $sleepmodedata['itinerary-field'][ $search_itinerary_result ]['sleep-mode-icon'] ) ? wp_kses_post($sleepmodedata['itinerary-field'][ $search_itinerary_result ]['sleep-mode-icon']) : 'fas fa-bed'; ?>"></i> <?php esc_html_e( $sleepmodesingle['sleepmode'] ); ?>
                                                                        <?php if(!empty($sleepmodesingle['sleep'])): ?>
                                                                            <i class="fas fa-info-circle"></i>    
                                                                        <?php endif; ?>
                                                                    </a>
                                                                    <div style="display: none;" id="<?php echo sanitize_title( $sleepmodesingle['sleepmode'] ); ?><?php echo sanitize_key( $key ); ?><?php echo sanitize_key( $itinerary_uni_key ); ?>" class="tour-itinerary-sleep">
                                                                        <div class="tf-tours-booking-deposit">
                                                                            <div class="tf-tours-booking-deposit-text">
                                                                            <?php
                                                                            if ( ! empty( $sleepmodesingle['sleep'] ) ) { ?>
                                                                                <?php echo wp_kses_post( $sleepmodesingle['sleep'] ); ?>
                                                                            <?php } ?>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                <?php
                                                                } ?>
                                                                
                                                            <?php }
                                                        } ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    <?php $itinerary_uni_key++; } ?>
                                </div>
                            </div>
                            <?php
                            if ( $tf_itinearay_downloader === '1' || $itinary_download_global_opt == 1) {
                            ?>
                            <div class="tf-itinerary-downloader">
                                <div class="tf-itinerary-downloader-left">
                                    <div class="tf-itinerary-downloader-icon">
                                        <img src="<?php echo esc_url(TF_ASSETS_URL) ?>app/images/pdf-downoader.png" alt="">
                                    </div>
                                    <div class="tf-itinerary-downloader-title">
                                        <span class="title"><?php echo  esc_html__($itinary_download_title,"tourfic"); ?></span>
                                        <p class="description"><?php echo esc_html__($itinary_download_des,"tourfic"); ?></p>
                                    </div>
                                </div>
                                <div class="tf-itinerary-downloader-button">
                                    <div class="tf-btn-wrap"><a class="tf_btn tf_btn_full" id="<?php echo esc_attr(get_the_ID()); ?>" href="?tours=<?php echo esc_attr(get_the_ID()); ?>"><?php echo esc_html__($itinary_download_button,"tourfic"); ?></a></div>
                                </div>
                            </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php if( $itinerary_map == 1 && $tf_openstreet_map == "googlemap" && !empty($tf_google_map_key) ){ ?>
                        <!-- Itinerary map -->
                        <div id="tf-map" data-itn-datas="<?php echo htmlspecialchars(wp_json_encode( $itinerary_data), ENT_QUOTES); ?>"></div>
                    <?php }else{ ?>
                        <div class="tf-trip-map-wrapper tf-template-section" id="tf-tour-map">
                            <div class="tf-map-area">
                                <?php if( $tf_openstreet_map!="default" ){ ?>
                                    <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                <?php } ?>
                                <?php 
                                if ( $tf_openstreet_map=="default" && !empty($location_latitude) && !empty($location_longitude) ) {  ?>
                                    <div id="tour-location"></div>
                                <?php } ?>

                                <?php if ( $tf_openstreet_map=="default" && (empty($location_latitude) || empty($location_longitude)) ) {  ?>
                                    <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php 
            }
            
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ): ?>
            <script>
                jQuery(document).ready(function ($) {
                    'use strict';
                
                    if (jQuery('#tour-itinerary-chart').length > 0) {
                        var ctx = document.getElementById('tour-itinerary-chart').getContext('2d');
                        var chart = new Chart(ctx, {
                            type: 'line',
                        });
                    }
                });	
            </script>
            <?php endif;
        }
    }

    /**
	 * Apply CSS property to the widget
     * @param $css_property
     * @return string
     */
	public function tf_apply_dim( $css_property, $important = false ) {
		return "{$css_property}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} " . ($important ? '!important' : '') . ";";
	}

	/**
	 * Get the current post type being previewed in Elementor editor
	 */
	protected function get_current_post_type() {
		// Check if we're in Elementor editor and have a preview post ID
		if (isset($_GET['tf_preview_post_id']) && !empty($_GET['tf_preview_post_id'])) {
			$preview_post_id = intval($_GET['tf_preview_post_id']);
			$preview_post = get_post($preview_post_id);
			
			if ($preview_post && in_array($preview_post->post_type, ['tf_hotel', 'tf_tours', 'tf_apartment', 'tf_carrental'])) {
				return $preview_post->post_type;
			}
		}
		
		// Fallback to regular post type detection
		return get_post_type();
	}
}

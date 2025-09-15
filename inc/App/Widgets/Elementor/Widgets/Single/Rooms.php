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
use \Tourfic\Classes\Hotel\Pricing;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Rooms
 */
class Rooms extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-room';
	}

	public function get_title() {
		return esc_html__( 'Rooms', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-contact';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'room',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-room'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-room/before-style-controls', $this );
		$this->tf_room_style_controls();
		do_action( 'tf/single-room/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_room_content',[
            'label' => esc_html__('Rooms', 'tourfic'),
        ]);

        do_action( 'tf/single-room/before-content/controls', $this );

		$post_type = $this->get_current_post_type();
		$options = [
			'style1' => esc_html__('Style 1', 'tourfic'),
			'style2' => esc_html__('Style 2', 'tourfic')
		];
		if($post_type == 'tf_hotel'){
			$options['style3'] = esc_html__('Style 3', 'tourfic');
		}
		$this->add_control('room_style',[
			'label' => esc_html__('Rooms Style', 'tourfic'),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'style1',
			'options' => $options,
		]);

	    do_action( 'tf/single-room/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_room_style_controls() {
		$this->start_controls_section( 'room_style_section', [
			'label' => esc_html__( 'Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

        $this->add_control( 'tf_title_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Title', 'tourfic' ),
		] );

		$this->add_control( 'tf_title_color', [
			'label'     => esc_html__( 'Title Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-section-title' => 'color: {{VALUE}};',
				'{{WRAPPER}} .section-heading' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Title Typography', 'tourfic' ),
			'name'     => "tf_title_typography",
			'selector' => "{{WRAPPER}} .tf-section-title, {{WRAPPER}} .section-heading",
		]);

		$this->end_controls_section();
	}

	protected function render() {
        $settings  = $this->get_settings_for_display();
        $this->post_id   = get_the_ID();
        $this->post_type = get_post_type();

		if($this->post_type == 'tf_hotel'){
			$this->tf_hotel_room($settings);
        } elseif($this->post_type == 'tf_apartment'){
			$this->tf_apartment_room($settings);
        } else {
			return;
		}   
    }

	private function tf_hotel_room($settings) {
        $style = !empty($settings['room_style']) ? $settings['room_style'] : 'style1';
		$meta = get_post_meta($this->post_id, 'tf_hotels_opt', true);
	    $rooms = \Tourfic\Classes\Room\Room::get_hotel_rooms( $this->post_id );

        $tf_hotel_reserve_button_text   = ! empty( Helper::tfopt( 'hotel_booking_form_button_text' ) ) ? stripslashes( sanitize_text_field( Helper::tfopt( 'hotel_booking_form_button_text' ) ) ) : esc_html__( "Reserve Now", 'tourfic' );
		$price_settings = ! empty( Helper::tfopt( 'hotel_archive_price_minimum_settings' ) ) ? Helper::tfopt( 'hotel_archive_price_minimum_settings' ) : 'all';
        $rm_features = [];
        foreach ( $rooms as $_room ) {
            $room = get_post_meta( $_room->ID, 'tf_room_opt', true );
            //merge for each room's selected features
            if ( ! empty( $room['features'] ) ) {
                $rm_features = array_unique( array_merge( $rm_features, $room['features'] ) );
            }
        }

        $tf_booking_url = $tf_booking_query_url = $tf_booking_attribute = $tf_hide_booking_form = $tf_hide_price = $tf_ext_booking_type = $tf_ext_booking_code = '';
        $tf_hide_external_price = "1";
        if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
            $tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
            $tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
            $tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&room={room}';
            $tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
            $tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
            $tf_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
            $tf_hide_external_price = !empty( $meta["booking-by"] ) && $meta["booking-by"] == 2 ? ( !empty( $meta["hide_external_price"] ) ? $meta["hide_external_price"] : true ) : true;
            $tf_ext_booking_type  = ! empty( $meta['external-booking-type'] ) ? $meta['external-booking-type'] : '1';
            $tf_ext_booking_code  = ! empty( $meta['booking-code'] ) ? $meta['booking-code'] : '';
            $adults_name = apply_filters( 'tf_hotel_adults_title_change', esc_html__( 'Adult', 'tourfic' ) );
        }
        if ( 2 == $tf_booking_type && ! empty( $tf_booking_url ) ) {
            $external_search_info = array(
                '{adult}'    => ! empty( $adult ) ? $adult : 1,
                '{child}'    => ! empty( $child ) ? $child : 0,
                '{checkin}'  => ! empty( $check_in ) ? $check_in : gmdate( 'Y-m-d' ),
                '{checkout}' => ! empty( $check_out ) ? $check_out : gmdate( 'Y-m-d', strtotime( '+1 day' ) ),
                '{room}'     => ! empty( $room_selected ) ? $room_selected : 1,
            );
            if ( ! empty( $tf_booking_attribute ) ) {
                $tf_booking_query_url = str_replace( array_keys( $external_search_info ), array_values( $external_search_info ), $tf_booking_query_url );
                if ( ! empty( $tf_booking_query_url ) ) {
                    $tf_booking_url = $tf_booking_url . '/?' . $tf_booking_query_url;
                }
            }
        }

        $total_room_option_count = \Tourfic\Classes\Room\Room::get_room_options_count($rooms);
        
		if ($style == 'style1' && $rooms) {
            ?>
            <div class="tf-single-template__one tf-single-hotel-room__style-1">
                <div class="tf-rooms-sections">
                    <h2 class="section-heading tf-section-title"><?php echo ! empty( $meta['room-section-title'] ) ? esc_html( $meta['room-section-title'] ) : ''; ?></h2>
                    <?php do_action( 'tf_hotel_features_filter', $rm_features, 10 ) ?>

                    <div class="tf-rooms" id="rooms">
                        <div id="tour_room_details_loader">
                            <div id="tour-room-details-loader-img">
                                <img src="<?php echo esc_url( TF_ASSETS_APP_URL ) ?>images/loader.gif" alt="">
                            </div>
                        </div>

                        <table class="tf-availability-table" cellpadding="0" cellspacing="0">
                            <thead>
                            <tr>
                                <th class="description" colspan="4"><?php esc_html_e( 'Room Details', 'tourfic' ); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ( $rooms as $_room ) {
                                $room_id = $_room->ID;
                                $room    = get_post_meta( $room_id, 'tf_room_opt', true );
                                $enable  = ! empty( $room['enable'] ) ? $room['enable'] : '';
                                if ( $enable == '1' ) {
                                    $footage                 = ! empty( $room['footage'] ) ? $room['footage'] : '';
                                    $bed                     = ! empty( $room['bed'] ) ? $room['bed'] : '';
                                    $adult_number            = ! empty( $room['adult'] ) ? $room['adult'] : '0';
                                    $child_number            = ! empty( $room['child'] ) ? $room['child'] : '0';
                                    $total_person            = $adult_number + $child_number;
                                    $pricing_by              = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
                                    $avil_by_date            = ! empty( $room['avil_by_date'] ) ? ! empty( $room['avil_by_date'] ) : false;
                                    $multi_by_date           = ! empty( $room['price_multi_day'] ) ? ! empty( $room['price_multi_day'] ) : false;
                                    $child_age_limit         = ! empty( $room['children_age_limit'] ) ? $room['children_age_limit'] : "";
                                    $room_options            = ! empty( $room['room-options'] ) ? $room['room-options'] : [];

                                    // Hotel Room Discount Data
                                    $hotel_discount_type   = ! empty( $room["discount_hotel_type"] ) ? $room["discount_hotel_type"] : "none";
                                    $hotel_discount_amount = ! empty( $room["discount_hotel_price"] ) ? $room["discount_hotel_price"] : 0;
                                    ?>
                                    <tr>
                                    <td class="description" rowspan="<?php echo ( $pricing_by == '3' && ! empty( $room_options ) ) ? count( $room_options ) : 1; ?>">
                                        <div class="tf-room-description-box tf-flex">
                                            <?php
                                            $tour_room_details_gall = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
                                            if ( $tour_room_details_gall ) {
                                                $tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
                                            }
                                            ?>

                                            <?php
                                            $room_preview_img = get_the_post_thumbnail_url( $room_id, 'full' );
                                            if ( ! empty( $room_preview_img ) ) { ?>
                                                <div class="tf-room-preview-img">
                                                    <?php
                                                    if ( $tour_room_details_gall ) {
                                                        ?>
                                                        <a href="#" class="tf-room-detail-qv" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                                        data-hotel="<?php echo esc_attr( $this->post_id ); ?>">
                                                            <img src="<?php echo esc_url( $room_preview_img ); ?>" alt="<?php esc_html_e( "Room Image", "tourfic" ); ?>">
                                                            <!-- <span><?php //esc_html_e("Best Offer", "tourfic");
                                                            ?></span> -->
                                                        </a>
                                                        <?php
                                                    } else { ?>
                                                        <img src="<?php echo esc_url( $room_preview_img ); ?>" alt="<?php esc_html_e( "Room Image", "tourfic" ); ?>">
                                                        <!-- <span><?php //esc_html_e("Best Offer", "tourfic");
                                                        ?></span> -->
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                            <div class="tf-features-infos" style="<?php echo ! empty( $room_preview_img ) ? 'width: 70%' : ''; ?>">
                                                <div class="tf-room-type">
                                                    <div class="tf-room-title">
                                                        <?php
                                                        if ( $tour_room_details_gall ) {
                                                            ?>
                                                            <h3>
                                                                <a href="#" class="tf-room-detail-qv" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                                                data-hotel="<?php echo esc_attr( $this->post_id ); ?>"><?php echo esc_html( get_the_title( $room_id ) ); ?></a>
                                                            </h3>
                                                            <?php
                                                        } else { ?>
                                                            <h3><?php echo esc_html( get_the_title( $room_id ) ); ?></h3>
                                                        <?php } ?>
                                                    </div>
                                                    <?php if ( ! empty( get_post_field( 'post_content', $room_id ) ) ) : ?>
                                                        <div class="bed-facilities">
                                                            <p>
                                                                <?php echo wp_kses_post( substr( wp_strip_all_tags( get_post_field( 'post_content', $room_id ) ), 0, 120 ) . '...' ); ?>
                                                            </p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <ul>
                                                    <?php if ( $footage ) { ?>
                                                        <li><i class="fas fa-ruler-combined"></i> <?php echo esc_html( $footage ); ?><?php esc_html_e( 'sft', 'tourfic' ); ?></li>
                                                    <?php } ?>
                                                    <?php if ( $bed ) { ?>
                                                        <li><i class="fas fa-bed"></i> <?php echo esc_html( $bed ); ?><?php esc_html_e( ' Number of Beds', 'tourfic' ); ?></li>
                                                    <?php } ?>
                                                    <?php
                                                    if ( ! empty( $room['features'] ) ) {
                                                        $tf_room_fec_key = 1;
                                                        foreach ( $room['features'] as $feature ) {
                                                            if ( $tf_room_fec_key < 5 ) {
                                                                $room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
                                                                if ( ! empty( $room_f_meta ) ) {
                                                                    $room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';
                                                                }
                                                                if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' && ! empty( $room_f_meta['icon-fa'] ) ) {
                                                                    $room_feature_icon = '<i class="' . $room_f_meta['icon-fa'] . '"></i>';
                                                                } elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' && ! empty( $room_f_meta['icon-c'] ) ) {
                                                                    $room_feature_icon = '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />';
                                                                }

                                                                $room_term = get_term( $feature ); 
                                                                if ( ! is_wp_error($room_term) && ! empty( $room_term->name ) ) : ?>
                                                                    <li>
                                                                        <?php echo ! empty( $room_feature_icon ) ? wp_kses_post( $room_feature_icon ) : ''; ?>
                                                                        <?php echo esc_html( $room_term->name ); ?>
                                                                    </li>
                                                                <?php endif; 
                                                            }
                                                            $tf_room_fec_key ++;
                                                        }
                                                    } ?>
                                                    <?php
                                                    if ( ! empty( $room['features'] ) ) {
                                                        if ( count( $room['features'] ) > 3 ) {
                                                            echo '<span>More....</span>';
                                                        }
                                                    }
                                                    ?>
                                                </ul>

                                                <?php
                                                if ( $tour_room_details_gall ) {
                                                    ?>
                                                    <a href="#" class="tf-room-detail-qv tf-room-gallery-info" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                                    data-hotel="<?php echo esc_attr( $this->post_id ); ?>">
                                                        <?php esc_html_e( "Room Photos & Details", "tourfic" ); ?>
                                                    </a>

                                                    <div id="tour_room_details_qv" class="tf-hotel-design-1-popup">

                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </td>

                                    <?php
                                    if ( $pricing_by == '3' && ! empty( $room_options ) ):
                                        foreach ( $room_options as $room_option_key => $room_option ):
                                            ?>
                                            <td class="options">
                                                <ul>
                                                    <?php if ( ! empty( $room_option['room-facilities'] ) ) :
                                                        foreach ( $room_option['room-facilities'] as $room_facility ) :
                                                            ?>
                                                            <li>
                                                                <span class="room-extra-icon"><i class="<?php echo esc_attr( $room_facility['room_facilities_icon'] ); ?>"></i></span>
                                                                <span class="room-extra-label"><?php echo wp_kses_post( $room_facility['room_facilities_label'] ); ?></span>
                                                            </li>
                                                        <?php endforeach;
                                                    endif; ?>
                                                </ul>
                                            </td>
                                            <td class="pax">
                                                <div><?php echo esc_html__( "Pax:", "tourfic" ); ?></div>
                                                <?php if ( $adult_number ) { ?>
                                                    <div class="tf-tooltip tf-d-b">
                                                        <div class="room-detail-icon">
                                                            <span class="room-icon-wrap">
                                                                <i class="fas fa-male"></i>
                                                                <i class="fas fa-female"></i>
                                                            </span>
                                                            <span class="icon-text tf-d-b">x<?php echo esc_html($adult_number); ?></span>
                                                        </div>
                                                        <div class="tf-top">
                                                            <?php esc_html_e( 'Number of '. $adults_name .'s', 'tourfic' ); ?>
                                                            <i class="tool-i"></i>
                                                        </div>
                                                    </div>
                                                <?php }
                                                if ( $child_number ) { ?>
                                                    <div class="tf-tooltip tf-d-b">
                                                        <div class="room-detail-icon">
                                                            <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                                                            <span class="icon-text tf-d-b">x<?php echo esc_html($child_number); ?></span>
                                                        </div>
                                                        <div class="tf-top">
                                                            <?php
                                                            if ( ! empty( $child_age_limit ) ) {
                                                                /* translators: Children age limit */
                                                                printf( esc_html__( 'Children Age Limit %s Years', 'tourfic' ), esc_html($child_age_limit) );
                                                            } else {
                                                                esc_html_e( 'Number of Children', 'tourfic' );
                                                            }
                                                            ?>
                                                            <i class="tool-i"></i>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                            <td class="reserve tf-t-c">
                                                <?php if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 ) {
                                                    Pricing::instance( get_the_ID(), $_room->ID )->get_per_price_html( $room_option_key );
                                                } ?>
                                                <?php if ( $tf_booking_type == 2 && ! empty( $tf_booking_url ) ): ?>
                                                    <a href="<?php echo esc_url( $tf_booking_url ); ?>" class="tf_btn tf_btn_gray" target="_blank">
                                                        <?php echo esc_html( $tf_hotel_reserve_button_text ); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <button class="hotel-room-availability tf_btn tf_btn_gray" type="submit" style="margin: 0 auto;">
                                                        <?php esc_html_e( 'Check Availability', 'tourfic' ); ?>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                            </tr>

                                            <?php if ( $room_option_key < count( $room_options ) - 1 ) : ?>
                                            <tr>
                                        <?php endif;
                                        endforeach;
                                    else:
                                        ?>
                                        <?php if ( $total_room_option_count > 0 ) : ?>
                                        <td class="options"></td>
                                    <?php endif; ?>
                                        <td class="pax">
                                            <div><?php echo esc_html__( "Pax:", "tourfic" ); ?></div>
                                            <?php if ( $adult_number ) { ?>
                                                <div class="tf-tooltip tf-d-b">
                                                    <div class="room-detail-icon">
                                                        <span class="room-icon-wrap"><i class="fas fa-male"></i><i class="fas fa-female"></i></span>
                                                        <span class="icon-text tf-d-b">x<?php echo esc_html($adult_number); ?></span>
                                                    </div>
                                                    <div class="tf-top">
                                                        <?php esc_html_e( 'Number of ' . $adults_name . 's', 'tourfic' ); ?>
                                                        <i class="tool-i"></i>
                                                    </div>
                                                </div>
                                            <?php }
                                            if ( $child_number ) { ?>
                                                <div class="tf-tooltip tf-d-b">
                                                    <div class="room-detail-icon">
                                                        <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                                                        <span class="icon-text tf-d-b">x<?php echo esc_html($child_number); ?></span>
                                                    </div>
                                                    <div class="tf-top">
                                                        <?php
                                                        if ( ! empty( $child_age_limit ) ) {
                                                            /* translators: Children age limit */
                                                            printf( esc_html__( 'Children Age Limit %s Years', 'tourfic' ), esc_html($child_age_limit) );
                                                        } else {
                                                            esc_html_e( 'Number of Children', 'tourfic' );
                                                        }
                                                        ?>
                                                        <i class="tool-i"></i>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </td>
                                        <td class="reserve tf-t-c">
                                            <?php
                                            if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 ) {
                                                Pricing::instance(get_the_ID(), $room_id)->get_per_price_html();
                                            }
                                            ?>
                                            <?php if ( $tf_booking_type == 2 && ! empty( $tf_booking_url ) && $tf_ext_booking_type == 1 ): ?>
                                                <a href="<?php echo esc_url( $tf_booking_url ); ?>" class="tf_btn tf_btn_gray" target="_blank">
                                                    <?php echo esc_html( $tf_hotel_reserve_button_text ); ?>
                                                </a>
                                            <?php elseif ( $tf_booking_type == 2 && $tf_ext_booking_type == 2 && ! empty( $tf_ext_booking_code ) ): ?>
                                                <a href="<?php echo esc_url( "#tf-external-booking-embaded-form" ); ?>" class="tf_btn tf_btn_gray" target="_blank">
                                                    <?php echo esc_html( $tf_hotel_reserve_button_text ); ?>
                                                </a>
                                            <?php else: ?>
                                                <button class="hotel-room-availability tf_btn tf_btn_gray" type="submit">
                                                    <?php esc_html_e( 'Check Availability', 'tourfic' ); ?>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                        </tr>
                                    <?php
                                    endif;
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php
        } elseif ($style == 'style2' && $rooms) {
            $feature_filter = ! empty( Helper::tfopt( 'feature-filter' ) ) ? Helper::tfopt( 'feature-filter' ) : false;
            ?>
			<div class="tf-single-template__two">
                <div class="tf-available-rooms-wrapper" id="tf-hotel-rooms">
                    <div class="tf-available-rooms-head">
                        <h3 class=""><?php echo ! empty( $meta["room-section-title"] ) ? esc_html( $meta["room-section-title"] ) : ''; ?></h3>
                        <?php if($feature_filter): ?>
                            <div class="tf-filter">
                                <i class="ri-equalizer-line"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php do_action( 'tf_hotel_features_filter', $rm_features, 10 ) ?>

                    <div class="tf-available-rooms tf-rooms" id="rooms">
                        <!-- Loader Image -->
                        <div id="tour_room_details_loader">
                            <div id="tour-room-details-loader-img">
                                <img src="<?php echo esc_url( TF_ASSETS_APP_URL ) ?>images/loader.gif" alt="">
                            </div>
                        </div>
                        <?php if ( $rooms ) : ?>
                            <?php foreach ( $rooms as $_room ) {
                                $room_id = $_room->ID;
                                $room    = get_post_meta( $_room->ID, 'tf_room_opt', true );
                                $enable  = ! empty( $room['enable'] ) ? $room['enable'] : '';
                                if ( $enable == '1' ) {
                                    $footage         = ! empty( $room['footage'] ) ? $room['footage'] : '';
                                    $bed             = ! empty( $room['bed'] ) ? $room['bed'] : '';
                                    $adult_number    = ! empty( $room['adult'] ) ? $room['adult'] : '0';
                                    $child_number    = ! empty( $room['child'] ) ? $room['child'] : '0';
                                    $total_person    = $adult_number + $child_number;
                                    $pricing_by      = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
                                    $avil_by_date    = ! empty( $room['avil_by_date'] ) ? $room['avil_by_date'] : false;
                                    $multi_by_date   = ! empty( $room['price_multi_day'] ) ? $room['price_multi_day'] : false;
                                    $child_age_limit = ! empty( $room['children_age_limit'] ) ? $room['children_age_limit'] : "";
                                    $room_options    = ! empty( $room['room-options'] ) ? $room['room-options'] : [];

                                    // Hotel Room Discount Data
                                    $hotel_discount_type   = ! empty( $room["discount_hotel_type"] ) ? $room["discount_hotel_type"] : "none";
                                    $hotel_discount_amount = ! empty( $room["discount_hotel_price"] ) ? $room["discount_hotel_price"] : 0;
                                    ?>
                                    <div class="tf-available-room tf-desktop-room">
                                        <div class="tf-available-room-gallery">
                                            <?php
                                            $tour_room_details_gall = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
                                            $room_preview_img       = get_the_post_thumbnail_url( $room_id, 'full' );
                                            if ( ! empty( $room_preview_img ) ) { ?>
                                                <div class="tf-room-gallery <?php echo empty( $tour_room_details_gall ) ? esc_attr( 'tf-no-room-gallery' ) : ''; ?>">
                                                    <img src="<?php echo esc_url( $room_preview_img ); ?>" alt="<?php esc_html_e( "Room Image", "tourfic" ); ?>">
                                                </div>
                                            <?php } ?>
                                            <?php
                                            if ( ! empty( $tour_room_details_gall ) ) {
                                                if ( ! empty( $tour_room_details_gall ) ) {
                                                    $tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
                                                }
                                                $gallery_limit = 1;
                                                foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
                                                    $image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
                                                    if ( $gallery_limit < 3 ) {
                                                        ?>
                                                        <?php
                                                        if ( count( $tf_room_gallery_ids ) > 1 ) { ?>
                                                            <?php if ( 1 == $gallery_limit ) { ?>
                                                                <div class="tf-room-gallery">
                                                                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_html_e( "Room Image", "tourfic" ); ?>">
                                                                </div>
                                                            <?php } ?>
                                                            <?php if ( 2 == $gallery_limit ) { ?>
                                                                <div class="tf-room-gallery tf-popup-buttons tf-room-detail-popup"
                                                                    data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>" 
                                                                    data-hotel="<?php echo esc_attr( $this->post_id ); ?>"
                                                                    data-design="design-2"
                                                                    style="background-image: url('<?php echo esc_url( $image_url ); ?>'); ">
                                                                    <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <g id="content">
                                                                            <path id="Rectangle 2111"
                                                                                d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5"
                                                                                stroke="#FDF9F4" stroke-width="1.5"></path>
                                                                            <path id="Rectangle 2109"
                                                                                d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z"
                                                                                stroke="#FDF9F4" stroke-width="1.5"></path>
                                                                            <path id="Vector"
                                                                                d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17"
                                                                                stroke="#FDF9F4" stroke-width="1.5" stroke-linejoin="round"></path>
                                                                            <path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                        </g>
                                                                    </svg>
                                                                </div>
                                                            <?php } ?>
                                                        <?php } ?>

                                                    <?php }
                                                    $gallery_limit ++;
                                                }
                                            } ?>
                                        </div>
                                        <?php
                                        if ( $pricing_by == '3' && ! empty( $room_options ) ):
                                            echo '<div class="tf-available-room-contents">';
                                            echo '<h2 class="tf-section-title">' . esc_html( get_the_title( $room_id ) ) . '</h2>';
                                            foreach ( $room_options as $room_option_key => $room_option ):
                                                ?>
                                                <div class="tf-available-room-content tf-room-options-content">
                                                    <div class="tf-room-options-content-inner">
                                                        <div class="tf-available-room-content-left">
                                                            <h4><?php echo esc_html( $room_option['option_title'] ); ?></h4>
                                                            <ul class="tf-option-list">
                                                                <?php if ( ! empty( $room_option['room-facilities'] ) ) :
                                                                    foreach ( $room_option['room-facilities'] as $room_facility ) :
                                                                        ?>
                                                                        <li>
                                                                            <span class="room-extra-icon"><i class="<?php echo esc_attr( $room_facility['room_facilities_icon'] ); ?>"></i></span>
                                                                            <span class="room-extra-label"><?php echo wp_kses_post( $room_facility['room_facilities_label'] ); ?></span>
                                                                        </li>
                                                                    <?php endforeach;
                                                                endif; ?>
                                                            </ul>
                                                            <ul class="tf-room-info-list">
                                                                <?php if ( $footage ) { ?>
                                                                    <li><i class="ri-pencil-ruler-2-line"></i> <?php echo esc_html( $footage ); ?><?php esc_html_e( 'sft', 'tourfic' ); ?></li>
                                                                <?php } ?>
                                                                <?php if ( $bed ) { ?>
                                                                    <li><i class="ri-hotel-bed-line"></i> <?php echo esc_html( $bed ); ?><?php esc_html_e( ' Beds', 'tourfic' ); ?></li>
                                                                <?php } ?>
                                                                <?php if ( $adult_number ) { ?>
                                                                    <li><i class="ri-user-2-line"></i> <?php echo esc_html( $adult_number ); ?>
                                                                    <?php 
                                                                    echo ' ' . esc_html( apply_filters( 'tf_hotel_adults_title_change', esc_html__( 'Adult', 'tourfic' ) ) ) . 's';
                                                                    ?>
                                                                <?php } ?>
                                                                <?php if ( $child_number ) { ?>
                                                                    <li><i class="ri-user-smile-line"></i> <?php echo esc_html( $child_number ); ?><?php esc_html_e( ' Child', 'tourfic' ); ?></li>
                                                                <?php } ?>
                                                                <li><a href="#" class="tf-room-detail-popup" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                                                    data-design="design-2" data-hotel="<?php echo esc_attr( $this->post_id ); ?>"><?php esc_html_e( "View room details", "tourfic" ); ?></a></li>

                                                            </ul>
                                                            <span class="tf-others-benefits-title"><?php esc_html_e( "Other benefits", "tourfic" ); ?></span>
                                                            <ul>
                                                                <?php
                                                                if ( ! empty( $room['features'] ) ) {
                                                                    $tf_room_fec_key = 1;
                                                                    foreach ( $room['features'] as $feature ) {
                                                                        if ( $tf_room_fec_key < 6 ) {
                                                                            $room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
                                                                            if ( ! empty( $room_f_meta ) ) {
                                                                                $room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';
                                                                            }
                                                                            if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' && ! empty( $room_f_meta['icon-fa'] ) ) {
                                                                                $room_feature_icon = '<i class="' . $room_f_meta['icon-fa'] . '"></i>';
                                                                            } elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' && ! empty( $room_f_meta['icon-c'] ) ) {
                                                                                $room_feature_icon = '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />';
                                                                            }

                                                                            $room_term = get_term( $feature ); ?>
                                                                            <li>
                                                                                <?php echo ! empty( $room_feature_icon ) ? wp_kses_post( $room_feature_icon ) : ''; ?>
                                                                                <?php echo ! empty( $room_term->name ) ? esc_html( $room_term->name ) : ''; ?>
                                                                            </li>
                                                                        <?php }
                                                                        $tf_room_fec_key ++;
                                                                    }
                                                                } ?>
                                                                <?php
                                                                if ( ! empty( $room['features'] ) ) {
                                                                    if ( count( $room['features'] ) >= 6 ) {
                                                                        ?>

                                                                        <li><a href="#" class="tf-room-detail-popup" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                                                            data-design="design-2" data-hotel="<?php echo esc_attr( $this->post_id ); ?>"><?php esc_html_e( "See all benefits", "tourfic" ); ?></a></li>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                        <div class="tf-available-room-content-right">
                                                            <?php
                                                            if ( ! empty( $hotel_discount_type ) && ! empty( $hotel_discount_amount ) && ( "percent" == $hotel_discount_type || "fixed" == $hotel_discount_type ) ) { ?>
                                                                <div class="tf-available-room-off">
                                                                <span>
                                                                    <?php echo ( "percent" == $hotel_discount_type ) ? esc_html( $hotel_discount_amount ) . '% off' : wp_kses_post( wc_price( $hotel_discount_amount ) . ' off' ); ?>
                                                                </span>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if ( $tf_hide_external_price ) : ?>
                                                                <div class="tf-available-room-price">
                                                                    <?php Pricing::instance( get_the_ID(), $room_id )->get_per_price_html($room_option_key, 'design-2'); ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            <a href="<?php echo $tf_booking_type == 2 ? ( ! empty( $tf_booking_url ) ? esc_url( $tf_booking_url ) : '' ) : esc_url( '#room-availability' ) ?>"
                                                            class="tf_btn tf_btn_large tf_btn_sharp"><?php $tf_booking_type == 2 ? ( ! empty( $tf_booking_url ) && ( $tf_hide_booking_form == 1 ) ? esc_html_e( 'Book Now', 'tourfic' ) : esc_html_e( "Check Availability", "tourfic" ) ) : esc_html_e( "Check Availability", "tourfic" ) ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                            endforeach;
                                            echo '</div>';
                                        else:
                                            ?>
                                            <div class="tf-available-room-content">
                                                <div class="tf-available-room-content-left">
                                                    <h2 class="tf-section-title"><?php echo esc_html( get_the_title( $room_id ) ); ?></h2>
                                                    <ul>
                                                        <?php if ( $footage ) { ?>
                                                            <li><i class="ri-pencil-ruler-2-line"></i> <?php echo esc_html( $footage ); ?><?php esc_html_e( 'sft', 'tourfic' ); ?></li>
                                                        <?php } ?>
                                                        <?php if ( $bed ) { ?>
                                                            <li><i class="ri-hotel-bed-line"></i> <?php echo esc_html( $bed ); ?><?php esc_html_e( ' Beds', 'tourfic' ); ?></li>
                                                        <?php } ?>
                                                        <?php if ( $adult_number ) { ?>
                                                            <li><i class="ri-user-2-line"></i> <?php echo esc_html( $adult_number ); ?>
                                                            <?php 
                                                            echo ' ' . esc_html( apply_filters( 'tf_hotel_adults_title_change', esc_html__( 'Adult', 'tourfic' ) ) ) . 's';
                                                            ?>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if ( $child_number ) { ?>
                                                            <li><i class="ri-user-smile-line"></i> <?php echo esc_html( $child_number ); ?><?php esc_html_e( ' Child', 'tourfic' ); ?></li>
                                                        <?php } ?>
                                                        <li><a href="#" class="tf-room-detail-popup" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                                            data-design="design-2" data-hotel="<?php echo esc_attr( $this->post_id ); ?>"><?php esc_html_e( "View room details", "tourfic" ); ?></a></li>

                                                    </ul>
                                                    <span class="tf-others-benefits-title"><?php esc_html_e( "Other benefits", "tourfic" ); ?></span>
                                                    <ul>
                                                        <?php
                                                        if ( ! empty( $room['features'] ) ) {
                                                            $tf_room_fec_key = 1;
                                                            foreach ( $room['features'] as $feature ) {
                                                                if ( $tf_room_fec_key < 6 ) {
                                                                    $room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
                                                                    if ( ! empty( $room_f_meta ) ) {
                                                                        $room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';
                                                                    }
                                                                    if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' && ! empty( $room_f_meta['icon-fa'] ) ) {
                                                                        $room_feature_icon = '<i class="' . $room_f_meta['icon-fa'] . '"></i>';
                                                                    } elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' && ! empty( $room_f_meta['icon-c'] ) ) {
                                                                        $room_feature_icon = '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />';
                                                                    }

                                                                    $room_term = get_term( $feature ); ?>
                                                                    <li>
                                                                        <?php echo ! empty( $room_feature_icon ) ? wp_kses_post( $room_feature_icon ) : ''; ?>
                                                                        <?php echo ! empty( $room_term->name ) ? esc_html( $room_term->name ) : ''; ?>
                                                                    </li>
                                                                <?php }
                                                                $tf_room_fec_key ++;
                                                            }
                                                        } ?>
                                                        <?php
                                                        if ( ! empty( $room['features'] ) ) {
                                                            if ( count( $room['features'] ) >= 6 ) {
                                                                ?>

                                                                <li><a href="#" class="tf-room-detail-popup" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                                                    data-design="design-2" data-hotel="<?php echo esc_attr( $this->post_id ); ?>"><?php esc_html_e( "See all benefits", "tourfic" ); ?></a></li>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                                <div class="tf-available-room-content-right">
                                                    <?php
                                                    if ( ! empty( $hotel_discount_type ) && ! empty( $hotel_discount_amount ) && ( "percent" == $hotel_discount_type || "fixed" == $hotel_discount_type ) ) { ?>
                                                        <div class="tf-available-room-off">
                                                            <span>
                                                                <?php echo ( "percent" == $hotel_discount_type ) ? esc_html( $hotel_discount_amount ) . '% off' : wp_kses_post( wc_price( $hotel_discount_amount ) . ' off' ); ?>
                                                            </span>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if ( $tf_hide_external_price ) : ?>
                                                        <div class="tf-available-room-price">
                                                            <?php Pricing::instance( get_the_ID(), $room_id)->get_per_price_html('', 'design-2'); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <a href="<?php echo $tf_booking_type == 2 ? ( !empty( $tf_booking_url ) && $tf_ext_booking_type == 1 ? esc_url( $tf_booking_url ) : ( $tf_ext_booking_type == 2 && !empty( $tf_ext_booking_code) ? esc_url("#tf-external-booking-embaded-form") : '' ) ) : esc_url( '#room-availability' ) ?>" class="tf_btn tf_btn_large tf_btn_sharp"><?php $tf_booking_type == 2 ? ( !empty( $tf_booking_url ) && ( $tf_hide_booking_form == 1 && $tf_ext_booking_type == 1 ) ? esc_html_e( 'Book Now', 'tourfic') : ($tf_ext_booking_type == 2 && !empty( $tf_ext_booking_code ) ? esc_html_e("Book Now", "tourfic") : esc_html_e("Check Availability", "tourfic") ) ) :  esc_html_e("Check Availability", "tourfic") ?></a>
                                                    <!--TODO: Need to add external booking code Book now Button  -->
                                                </div>

                                            </div>
                                        <?php endif; ?>
                                    </div>


                                    <div class="tf-available-room tf-tabs-room">
                                        <div class="tf-available-room-gallery <?php echo empty( $tour_room_details_gall ) ? esc_attr( 'tf-no-room-gallery' ) : ''; ?>">
                                            <?php
                                            $tour_room_details_gall = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
                                            $room_preview_img       = get_the_post_thumbnail_url( $room_id, 'full' );
                                            if ( ! empty( $room_preview_img ) ) { ?>
                                                <div class="tf-room-image">
                                                    <?php
                                                    if ( ! empty( $hotel_discount_type ) && ! empty( $hotel_discount_amount ) && ( "percent" == $hotel_discount_type || "fixed" == $hotel_discount_type ) ) { ?>
                                                        <div class="tf-available-room-off">
                                                            <span>
                                                                <?php echo ( "percent" == $hotel_discount_type ) ? esc_html( $hotel_discount_amount ) . '% off' : wp_kses_post( wc_price( $hotel_discount_amount ) . ' off' ); ?>
                                                            </span>
                                                        </div>
                                                    <?php } ?>
                                                    <img src="<?php echo esc_url( $room_preview_img ); ?>" alt="<?php esc_html_e( "Room Image", "tourfic" ); ?>">
                                                </div>
                                            <?php } ?>
                                            <div class="tf-room-gallerys">
                                                <?php
                                                if ( ! empty( $tour_room_details_gall ) ) {
                                                    if ( ! empty( $tour_room_details_gall ) ) {
                                                        $tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
                                                    }
                                                    $gallery_limit = 1;
                                                    foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
                                                        $image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
                                                        if ( $gallery_limit < 3 ) {
                                                            ?>
                                                            <?php
                                                            if ( count( $tf_room_gallery_ids ) > 1 ) { ?>
                                                                <?php if ( 1 == $gallery_limit ) { ?>
                                                                    <div class="tf-room-gallery">
                                                                        <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_html_e( "Room Image", "tourfic" ); ?>">
                                                                    </div>
                                                                <?php } ?>
                                                                <?php if ( 2 == $gallery_limit ) { ?>
                                                                    <div class="tf-room-gallery tf-popup-buttons tf-room-detail-popup"
                                                                        data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>" 
                                                                        data-hotel="<?php echo esc_attr( $this->post_id ); ?>"
                                                                        data-design="design-2"
                                                                        style="background-image: url('<?php echo esc_url( $image_url ); ?>'); ">
                                                                        <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <g id="content">
                                                                                <path id="Rectangle 2111"
                                                                                    d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5"
                                                                                    stroke="#FDF9F4" stroke-width="1.5"></path>
                                                                                <path id="Rectangle 2109"
                                                                                    d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z"
                                                                                    stroke="#FDF9F4" stroke-width="1.5"></path>
                                                                                <path id="Vector"
                                                                                    d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17"
                                                                                    stroke="#FDF9F4" stroke-width="1.5" stroke-linejoin="round"></path>
                                                                                <path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                            </g>
                                                                        </svg>
                                                                    </div>
                                                                <?php } ?>
                                                            <?php } ?>

                                                        <?php }
                                                        $gallery_limit ++;
                                                    }
                                                } ?>
                                            </div>
                                        </div>
                                        <?php
                                        if ( $pricing_by == '3' && ! empty( $room_options ) ):
                                            echo '<div class="tf-available-room-contents">';
                                            echo '<h2 class="tf-section-title">' . esc_html( get_the_title( $room_id ) ) . '</h2>';
                                            foreach ( $room_options as $room_option_key => $room_option ):
                                                ?>
                                                <div class="tf-available-room-content tf-room-options-content">
                                                    <div class="tf-room-options-content-inner">
                                                        <div class="tf-available-room-content-left">
                                                            <div class="room-heading-price">
                                                                <h4><?php echo esc_html( $room_option['option_title'] ); ?></h4>
                                                                <?php if ( $tf_hide_external_price ) : ?>
                                                                    <div class="tf-available-room-price">
                                                                        <?php Pricing::instance( get_the_ID(), $room_id )->get_per_price_html($room_option_key, 'design-2'); ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <ul class="tf-option-list">
                                                                <?php if ( ! empty( $room_option['room-facilities'] ) ) :
                                                                    foreach ( $room_option['room-facilities'] as $room_facility ) :
                                                                        ?>
                                                                        <li>
                                                                            <span class="room-extra-icon"><i class="<?php echo esc_attr( $room_facility['room_facilities_icon'] ); ?>"></i></span>
                                                                            <span class="room-extra-label"><?php echo wp_kses_post( $room_facility['room_facilities_label'] ); ?></span>
                                                                        </li>
                                                                    <?php endforeach;
                                                                endif; ?>
                                                            </ul>
                                                            <ul class="tf-room-info-list">
                                                                <?php if ( $footage ) { ?>
                                                                    <li><i class="ri-pencil-ruler-2-line"></i> <?php echo esc_html( $footage ); ?><?php esc_html_e( 'sft', 'tourfic' ); ?></li>
                                                                <?php } ?>
                                                                <?php if ( $bed ) { ?>
                                                                    <li><i class="ri-hotel-bed-line"></i> <?php echo esc_html( $bed ); ?><?php esc_html_e( ' Beds', 'tourfic' ); ?></li>
                                                                <?php } ?>
                                                                <?php if ( $adult_number ) { ?>
                                                                    <li><i class="ri-user-2-line"></i> <?php echo esc_html( $adult_number ); ?><?php 
                                                                    echo ' ' . esc_html( apply_filters( 'tf_hotel_adults_title_change', esc_html__( 'Adult', 'tourfic' ) ) ) . 's';
                                                                    ?>
                                                                    </li>
                                                                <?php } ?>
                                                                <?php if ( $child_number ) { ?>
                                                                    <li><i class="ri-user-smile-line"></i><?php echo esc_html( $child_number ); ?><?php esc_html_e( ' Child', 'tourfic' ); ?></li>
                                                                <?php } ?>
                                                                <li><a href="#" class="tf-room-detail-popup" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                                                    data-design="design-2" data-hotel="<?php echo esc_attr( $this->post_id ); ?>"><?php esc_html_e( "View room details", "tourfic" ); ?></a></li>

                                                            </ul>
                                                            <span class="tf-others-benefits-title"><?php esc_html_e( "Other benefits", "tourfic" ); ?></span>
                                                            <ul>
                                                                <?php
                                                                if ( ! empty( $room['features'] ) ) {
                                                                    $tf_room_fec_key = 1;
                                                                    foreach ( $room['features'] as $feature ) {
                                                                        if ( $tf_room_fec_key < 6 ) {
                                                                            $room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
                                                                            if ( ! empty( $room_f_meta ) ) {
                                                                                $room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';
                                                                            }
                                                                            if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' && ! empty( $room_f_meta['icon-fa'] ) ) {
                                                                                $room_feature_icon = '<i class="' . $room_f_meta['icon-fa'] . '"></i>';
                                                                            } elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' && ! empty( $room_f_meta['icon-c'] ) ) {
                                                                                $room_feature_icon = '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />';
                                                                            }

                                                                            $room_term = get_term( $feature ); ?>
                                                                            <li>
                                                                                <?php echo ! empty( $room_feature_icon ) ? wp_kses_post( $room_feature_icon ) : ''; ?>
                                                                                <?php echo ! empty( $room_term->name ) ? esc_html( $room_term->name ) : ''; ?>
                                                                            </li>
                                                                        <?php }
                                                                        $tf_room_fec_key ++;
                                                                    }
                                                                } ?>
                                                                <?php
                                                                if ( ! empty( $room['features'] ) ) {
                                                                    if ( count( $room['features'] ) >= 6 ) {
                                                                        ?>

                                                                        <li><a href="#" class="tf-room-detail-popup" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                                                            data-design="design-2" data-hotel="<?php echo esc_attr( $this->post_id ); ?>"><?php esc_html_e( "See all benefits", "tourfic" ); ?></a></li>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                        <div class="tf-available-room-content-right">
                                                            <a href="#room-availability" class="tf_btn tf_btn_large tf_btn_sharp"><?php esc_html_e( "Check Availability", "tourfic" ); ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                            endforeach;
                                            echo '</div>';
                                        else:
                                            ?>
                                            <div class="tf-available-room-content">
                                                <div class="tf-available-room-content-left">
                                                    <div class="room-heading-price">
                                                        <h2 class="tf-section-title"><?php echo esc_html( get_the_title( $room_id ) ); ?></h2>
                                                        <?php if ( $tf_hide_external_price ) : ?>
                                                            <div class="tf-available-room-price">
                                                                <?php Pricing::instance( get_the_ID(), $room_id )->get_per_price_html('', 'design-2'); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <ul>
                                                        <?php if ( $footage ) { ?>
                                                            <li><i class="ri-pencil-ruler-2-line"></i> <?php echo esc_html( $footage ); ?><?php esc_html_e( 'sft', 'tourfic' ); ?></li>
                                                        <?php } ?>
                                                        <?php if ( $bed ) { ?>
                                                            <li><i class="ri-hotel-bed-line"></i> <?php echo esc_html( $bed ); ?><?php esc_html_e( ' Beds', 'tourfic' ); ?></li>
                                                        <?php } ?>
                                                        <?php if ( $adult_number ) { ?>
                                                            <li><i class="ri-user-2-line"></i> <?php echo esc_html( $adult_number ); ?><?php 
                                                            echo ' ' . esc_html( apply_filters( 'tf_hotel_adults_title_change', esc_html__( 'Adult', 'tourfic' ) ) ) . 's';
                                                            ?>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if ( $child_number ) { ?>
                                                            <li><i class="ri-user-smile-line"></i><?php echo esc_html( $child_number ); ?><?php esc_html_e( ' Child', 'tourfic' ); ?></li>
                                                        <?php } ?>
                                                        <li><a href="#" class="tf-room-detail-popup" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                                            data-design="design-2" data-hotel="<?php echo esc_attr( $this->post_id ); ?>"><?php esc_html_e( "View room details", "tourfic" ); ?></a></li>

                                                    </ul>
                                                    <span class="tf-others-benefits-title"><?php esc_html_e( "Other benefits", "tourfic" ); ?></span>
                                                    <ul>
                                                        <?php
                                                        if ( ! empty( $room['features'] ) ) {
                                                            $tf_room_fec_key = 1;
                                                            foreach ( $room['features'] as $feature ) {
                                                                if ( $tf_room_fec_key < 6 ) {
                                                                    $room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
                                                                    if ( ! empty( $room_f_meta ) ) {
                                                                        $room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';
                                                                    }
                                                                    if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' && ! empty( $room_f_meta['icon-fa'] ) ) {
                                                                        $room_feature_icon = '<i class="' . $room_f_meta['icon-fa'] . '"></i>';
                                                                    } elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' && ! empty( $room_f_meta['icon-c'] ) ) {
                                                                        $room_feature_icon = '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />';
                                                                    }

                                                                    $room_term = get_term( $feature ); ?>
                                                                    <li>
                                                                        <?php echo ! empty( $room_feature_icon ) ? wp_kses_post( $room_feature_icon ) : ''; ?>
                                                                        <?php echo ! empty( $room_term->name ) ? esc_html( $room_term->name ) : ''; ?>
                                                                    </li>
                                                                <?php }
                                                                $tf_room_fec_key ++;
                                                            }
                                                        } ?>
                                                        <?php
                                                        if ( ! empty( $room['features'] ) ) {
                                                            if ( count( $room['features'] ) >= 6 ) {
                                                                ?>

                                                                <li><a href="#" class="tf-room-detail-popup" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $room_id ) : '' ?>"
                                                                    data-design="design-2" data-hotel="<?php echo esc_attr( $this->post_id ); ?>"><?php esc_html_e( "See all benefits", "tourfic" ); ?></a></li>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                                <div class="tf-available-room-content-right">
                                                    <a href="#room-availability" class="tf_btn tf_btn_large tf_btn_sharp"><?php esc_html_e( "Check Availability", "tourfic" ); ?></a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php }
                            } ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="tf-popup-wrapper tf-room-popup"></div>
            </div>
			<?php
        } elseif ($style == 'style3' && $rooms) {
            ?>
            <div class="tf-single-template__legacy tf-single-hotel-room__style-legacy">
                <div class="tf-room-section">
                    <h2 class="section-heading"><?php echo ! empty( $meta['room-section-title'] ) ? esc_html( $meta['room-section-title'] ) : ''; ?></h2>
                    
                    <?php do_action( 'tf_hotel_features_filter', $rm_features, 10 ) ?>
                    <div class="tf-room-type" id="rooms">
                        <div class="tf-room-table hotel-room-wrap">
                            <div id="tour_room_details_loader">
                                <div id="tour-room-details-loader-img">
                                    <img src="<?php echo esc_url( TF_ASSETS_APP_URL ) ?>images/loader.gif" alt="">
                                </div>
                            </div>
                            <table class="availability-table" cellpadding="0" cellspacing="0">
                                <thead>
                                <tr>
                                    <th class="description"><?php esc_html_e( 'Room Details', 'tourfic' ); ?></th>
                                    <?php if ( $total_room_option_count > 0 ) : ?>
                                        <th class="options"><?php esc_html_e( 'Options', 'tourfic' ); ?></th>
                                    <?php endif; ?>
                                    <th class="pax"><?php esc_html_e( 'Pax', 'tourfic' ); ?></th>
                                    <?php if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 ) : ?>
                                        <th class="pricing"><?php esc_html_e( 'Price', 'tourfic' ); ?></th>
                                    <?php endif; ?>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- Start Single Room -->
                                <?php foreach ( $rooms as $_room ) {
                                    $room = get_post_meta($_room->ID, 'tf_room_opt', true);
                                    $enable = ! empty( $room['enable'] ) ? $room['enable'] : '';
                                    if ( $enable == '1' ) {
                                        $unique_id         = ! empty( $room['unique_id'] ) ? $room['unique_id'] : '';
                                        $footage         = ! empty( $room['footage'] ) ? $room['footage'] : '';
                                        $bed             = ! empty( $room['bed'] ) ? $room['bed'] : '';
                                        $adult_number    = ! empty( $room['adult'] ) ? $room['adult'] : '0';
                                        $child_number    = ! empty( $room['child'] ) ? $room['child'] : '0';
                                        $total_person    = $adult_number + $child_number;
                                        $pricing_by      = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
                                        $avil_by_date    = ! empty( $room['avil_by_date'] ) ? $room['avil_by_date'] : false;
                                        $multi_by_date   = ! empty( $room['price_multi_day'] ) ?  $room['price_multi_day'] : false;
                                        $child_age_limit = ! empty( $room['children_age_limit'] ) ? $room['children_age_limit'] : "";
                                        $room_options    = ! empty( $room['room-options'] ) ? $room['room-options'] : [];

                                        // Hotel Room Discount Data
                                        $hotel_discount_type   = ! empty( $room["discount_hotel_type"] ) ? $room["discount_hotel_type"] : "none";
                                        $hotel_discount_amount = ! empty( $room["discount_hotel_price"] ) ? $room["discount_hotel_price"] : 0;
                                        ?>
                                        <tr>
                                            <td class="description" rowspan="<?php echo $room_options ? count( $room_options ) : 1; ?>">
                                                <div class="tf-room-type">
                                                    <div class="tf-room-title">
                                                        <?php
                                                        $tour_room_details_gall = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
                                                        if ( $tour_room_details_gall ) {
                                                            $tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
                                                        }
                                                        if ( $tour_room_details_gall ){
                                                            ?>
                                                            <h3><a href="#" class="tf-room-detail-qv" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? esc_attr( $room['unique_id'] . $_room->ID ) : '' ?>"
                                                                data-hotel="<?php echo esc_attr( $this->post_id ); ?>">
                                                                    <?php echo esc_html( get_the_title($_room->ID) ); ?>
                                                                </a></h3>

                                                            <div id="tour_room_details_qv" class="">

                                                            </div>
                                                        <?php } else{ ?>
                                                        <h3><?php echo esc_html( get_the_title($_room->ID) ); ?><h3>
                                                                <?php } ?>
                                                    </div>
                                                    <div class="bed-facilities"><p><?php echo wp_kses_post( get_post_field('post_content', $_room->ID) ); ?></p></div>
                                                </div>

                                                <?php if ( $footage ) { ?>
                                                    <div class="tf-tooltip tf-d-ib">
                                                        <div class="room-detail-icon">
                                                <span class="room-icon-wrap"><i
                                                            class="fas fa-ruler-combined"></i></span>
                                                            <span class="icon-text tf-d-b"><?php echo esc_html( $footage ); ?><?php esc_html_e( 'sft', 'tourfic' ); ?></span>
                                                        </div>
                                                        <div class="tf-top">
                                                            <?php esc_html_e( 'Room Footage', 'tourfic' ); ?>
                                                            <i class="tool-i"></i>
                                                        </div>
                                                    </div>
                                                <?php }
                                                if ( $bed ) { ?>
                                                    <div class="tf-tooltip tf-d-ib">
                                                        <div class="room-detail-icon">
                                                            <span class="room-icon-wrap"><i class="fas fa-bed"></i></span>
                                                            <span class="icon-text tf-d-b">x<?php echo esc_html( $bed ); ?></span>
                                                        </div>
                                                        <div class="tf-top">
                                                            <?php esc_html_e( 'Number of Beds', 'tourfic' ); ?>
                                                            <i class="tool-i"></i>
                                                        </div>
                                                    </div>
                                                <?php } ?>

                                                <?php if ( ! empty( $room['features'] ) ) { ?>
                                                    <div class="room-features">
                                                        <div class="tf-room-title"><h4><?php esc_html_e( 'Amenities', 'tourfic' ); ?></h4>
                                                        </div>
                                                        <ul class="room-feature-list">
                                                            <?php
                                                            foreach ( $room['features'] as $feature ) {

                                                                $room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
                                                                if ( ! empty( $room_f_meta ) ) {
                                                                    $room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';
                                                                }
                                                                if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' ) {
                                                                    $room_feature_icon = ! empty( $room_f_meta['icon-fa'] ) ? '<i class="' . $room_f_meta['icon-fa'] . '"></i>' : '<i class="fas fa-bread-slice"></i>';
                                                                } elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' ) {
                                                                    $room_feature_icon = ! empty( $room_f_meta['icon-c'] ) ? '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />' : '<i class="fas fa-bread-slice"></i>';
                                                                } else {
                                                                    $room_feature_icon = '<i class="fas fa-bread-slice"></i>';
                                                                }

                                                                $room_term = get_term( $feature );
                                                                if ( ! empty( $room_term->name ) ) {
                                                                    ?>
                                                                    <li class="tf-tooltip">
                                                                        <?php echo ! empty( $room_feature_icon ) ? wp_kses_post( $room_feature_icon ) : ''; ?>
                                                                        <div class="tf-top">
                                                                            <?php echo esc_html( $room_term->name ); ?>
                                                                            <i class="tool-i"></i>
                                                                        </div>
                                                                    </li>
                                                                <?php }
                                                            } ?>
                                                        </ul>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                        <?php
                                        if ( $pricing_by == '3' && !empty($room_options) ):
                                            foreach ( $room_options as $room_option_key => $room_option ):
                                                ?>
                                                <td class="options">
                                                    <ul>
                                                        <?php if ( ! empty( $room_option['room-facilities'] ) ) :
                                                            foreach ( $room_option['room-facilities'] as $room_facility ) :
                                                                ?>
                                                                <li>
                                                                    <span class="room-extra-icon"><i class="<?php echo esc_attr( $room_facility['room_facilities_icon'] ); ?>"></i></span>
                                                                    <span class="room-extra-label"><?php echo wp_kses_post( $room_facility['room_facilities_label'] ); ?></span>
                                                                </li>
                                                            <?php endforeach;
                                                        endif; ?>
                                                    </ul>
                                                </td>
                                                <td class="pax">
                                                    <?php if ( $adult_number ) { ?>
                                                        <div class="tf-tooltip tf-d-b">
                                                            <div class="room-detail-icon">
                                                                <span class="room-icon-wrap">
                                                                    <i class="fas fa-male"></i>
                                                                    <i class="fas fa-female"></i>
                                                                </span>
                                                                <span class="icon-text tf-d-b">x<?php echo esc_html($adult_number); ?></span>
                                                            </div>
                                                            <div class="tf-top">
                                                                <?php esc_html_e( 'Number of Adults', 'tourfic' ); ?>
                                                                <i class="tool-i"></i>
                                                            </div>
                                                        </div>
                                                    <?php }
                                                    if ( $child_number ) { ?>
                                                        <div class="tf-tooltip tf-d-b">
                                                            <div class="room-detail-icon">
                                                                <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                                                                <span class="icon-text tf-d-b">x<?php echo esc_html($child_number); ?></span>
                                                            </div>
                                                            <div class="tf-top">
                                                                <?php
                                                                if ( ! empty( $child_age_limit ) ) {
                                                                    /* translators: Children age limit */
                                                                    printf( esc_html__( 'Children Age Limit %s Years', 'tourfic' ), esc_html($child_age_limit) );
                                                                } else {
                                                                    esc_html_e( 'Number of Children', 'tourfic' );
                                                                }
                                                                ?>
                                                                <i class="tool-i"></i>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </td>
                                                <?php if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 ) : ?>
                                                    <td class="pricing">
                                                        <div class="tf-price-column">
                                                            <?php Pricing::instance(get_the_ID(), $_room->ID)->get_per_price_html($room_option_key); ?>
                                                        </div>
                                                    </td>
                                                <?php endif; ?>
                                                <td class="reserve tf-t-c">
                                                    <div class="tf-btn-wrap">
                                                        <?php if ( $tf_booking_type == 2 && ! empty( $tf_booking_url ) ): ?>
                                                            <a href="<?php echo esc_url( $tf_booking_url ); ?>" class="tf_btn tf_btn_full" target="_blank">
                                                                <?php echo esc_html( $tf_hotel_reserve_button_text ); ?>
                                                            </a>
                                                        <?php else: ?>
                                                            <button class="tf_btn tf_btn_full hotel-room-availability" type="submit">
                                                                <?php esc_html_e( 'Check Availability', 'tourfic' ); ?>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                </tr>
                                                <?php if ( $room_option_key < count( $room_options ) - 1 ) : ?>
                                                <tr>
                                            <?php endif;
                                            endforeach;
                                        else:
                                            ?>
                                            <?php if ( $total_room_option_count > 0 ) : ?>
                                                <td class="options"></td>
                                            <?php endif; ?>
                                            <td class="pax">
                                                <?php if ( $adult_number ) { ?>
                                                    <div class="tf-tooltip tf-d-b">
                                                        <div class="room-detail-icon">
                                                    <span class="room-icon-wrap"><i class="fas fa-male"></i><i
                                                                class="fas fa-female"></i></span>
                                                            <span class="icon-text tf-d-b">x<?php echo esc_html($adult_number); ?></span>
                                                        </div>
                                                        <div class="tf-top">
                                                            <?php esc_html_e( 'Number of Adults', 'tourfic' ); ?>
                                                            <i class="tool-i"></i>
                                                        </div>
                                                    </div>
                                                <?php }
                                                if ( $child_number ) { ?>
                                                    <div class="tf-tooltip tf-d-b">
                                                        <div class="room-detail-icon">
                                                            <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                                                            <span class="icon-text tf-d-b">x<?php echo esc_html($child_number); ?></span>
                                                        </div>
                                                        <div class="tf-top">
                                                            <?php
                                                            if ( ! empty( $child_age_limit ) ) {
                                                                /* translators: Children age limit */
                                                                printf( esc_html__( 'Children Age Limit %s Years', 'tourfic' ), esc_html($child_age_limit) );
                                                            } else {
                                                                esc_html_e( 'Number of Children', 'tourfic' );
                                                            }
                                                            ?>
                                                            <i class="tool-i"></i>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                            <?php if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 ) : ?>
                                            <td class="pricing">
                                                <div class="tf-price-column">
                                                    <?php Pricing::instance(get_the_ID(), $_room->ID)->get_per_price_html(); ?>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                            <td class="reserve tf-t-c">
                                                <div class="tf-btn-wrap">
                                                    <?php if ( $tf_booking_type == 2 && ! empty( $tf_booking_url ) && $tf_ext_booking_type == 1 ): ?>
                                                        <a href="<?php echo esc_url( $tf_booking_url ); ?>" class="tf_btn tf_btn_full" target="_blank">
                                                            <?php echo esc_html( $tf_hotel_reserve_button_text ); ?>
                                                        </a>
                                                    <?php elseif( $tf_booking_type == 2 && $tf_ext_booking_type == 2 && !empty( $tf_ext_booking_code ) ): ?>
                                                        <a href="<?php echo esc_url( "#tf-external-booking-embaded-form" ); ?>" class="tf_btn tf_btn_full" target="_blank">
                                                            <?php echo esc_html( $tf_hotel_reserve_button_text ); ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="tf_btn tf_btn_full hotel-room-availability" type="submit">
                                                            <?php esc_html_e( 'Check Availability', 'tourfic' ); ?>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            </tr>
                                        <?php endif;
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
			<?php
        }
	}

	private function tf_apartment_room($settings) {
        $style = !empty($settings['room_style']) ? $settings['room_style'] : 'style1';
		$meta = get_post_meta($this->post_id, 'tf_apartment_opt', true);
		
		if ($style == 'style1' && ! empty( Helper::tf_data_types( $meta['rooms'] ) )) {
            ?>
			<div class="tf-single-template__two tf-single-apartment-room__style-1">
                <div class="tf-apartment-rooms-section" id="tf-apartment-rooms">
                    <div class="tf-apartment-room-details">
                    <h4><?php echo esc_html( $meta['room_details_title'] ) ?></h4>
                    <div class="tf-apartment-room-slider tf-slick-slider">
                    <?php foreach ( Helper::tf_data_types( $meta['rooms'] ) as $key => $room ) : ?>
                        <div class="tf-apartment-room-item">
                            <div class="tf-apartment-room-item-thumb">
                                <a href="#" class="tf-apt-room-qv-desgin-1" data-id="<?php echo esc_attr( $key ); ?>" data-post-id="<?php echo esc_attr( $this->post_id ); ?>">
                                    <img src="<?php echo !empty($room['thumbnail']) ? esc_url( $room['thumbnail'] ) : esc_url(TF_ASSETS_APP_URL) . "images/feature-default.jpg" ?>" alt="room-thumbnail">
                                </a>
                            </div>
                            <div class="tf-apartment-room-item-content">
                                <?php if(!empty($room['title'])): ?>
                                    <a href="#" class="tf-apt-room-qv-desgin-1" data-id="<?php echo esc_attr( $key ); ?>" data-post-id="<?php echo esc_attr( $this->post_id ); ?>">
                                        <span><?php echo esc_html( $room['title'] ) ?></span>
                                    </a>
                                <?php endif; ?>
                                <?php echo ! empty( $room['subtitle'] ) ? '<p>' . esc_html( $room['subtitle'] ) . '</p>' : ''; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                    <!-- Loader Image -->
                    <div id="tour_room_details_loader">
                        <div id="tour-room-details-loader-img">
                            <img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
                        </div>
                    </div>
                    </div>
                </div>
                <div class="tf-popup-wrapper tf-room-popup"></div>
            </div>
			<?php
        } elseif ($style == 'style2' && ! empty( Helper::tf_data_types( $meta['rooms'] ) )) {
            ?>
            <div class="tf-single-template__legacy tf-single-apartment-room__style-legacy">
                <div class="tf-apartment-rooms">
                    <?php if ( ! empty( $meta['room_details_title'] ) ): ?>
                        <h2 class="section-heading"><?php echo esc_html( $meta['room_details_title'] ) ?></h2>
                    <?php endif; ?>
                    <div class="tf-apartment-default-design-room-slider tf-slick-slider">
                        <?php foreach ( Helper::tf_data_types( $meta['rooms'] ) as $key => $room ) : ?>
                            <div class="tf-apartment-room-item">
                                <div class="tf-apartment-room-item-thumb">
                                    <a href="#" class="tf-apt-room-qv" data-id="<?php echo esc_attr( $key ); ?>" data-post-id="<?php echo esc_attr( $this->post_id ); ?>">
                                        <img src="<?php echo ! empty( $room['thumbnail'] ) ? esc_url( $room['thumbnail'] ) : esc_url( TF_ASSETS_APP_URL . "images/feature-default.jpg" ) ?>" alt="room-thumbnail">
                                    </a>
                                </div>
                                <div class="tf-apartment-room-item-content">
                                    <?php if(!empty($room['title'])){ ?>
                                    <a href="#" class="tf-apt-room-qv" data-id="<?php echo esc_attr( $key ); ?>" data-post-id="<?php echo esc_attr( $this->post_id ); ?>">
                                        <h3><?php echo esc_html( $room['title'] ) ?></h3>
                                    </a>
                                    <?php } ?>
                                    <p class="tf-apartment-room-item-price">
                                        <?php echo ! empty( $room['price'] ) ? '<span>' . esc_html( $room['price'] ) . '</span>' : ''; ?>
                                        <?php echo ! empty( $room['price_label'] ) ? '<span>' . esc_html( $room['price_label'] ) . '</span>' : ''; ?>
                                    </p>
                                    <?php echo ! empty( $room['subtitle'] ) ? '<p>' . esc_html( $room['subtitle'] ) . '</p>' : ''; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="tf_apt_room_details_qv" class=""></div>
                    <!-- Loader Image -->
                    <div id="tour_room_details_loader">
                        <div id="tour-room-details-loader-img">
                            <img src="<?php echo esc_url( TF_ASSETS_APP_URL ) ?>images/loader.gif" alt="">
                        </div>
                    </div>
                </div>
            </div>
			<?php
        }
        if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ): ?>
			<script>
				jQuery(document).ready(function ($) {
					'use strict';
				
					/**
                     * Apartment room slider
                     * @author Foysal
                     */
                    $('.tf-apartment-room-slider').slick({
                        dots: true,
                        arrows: false,
                        infinite: true,
                        speed: 300,
                        autoplay: false,
                        autoplaySpeed: 3000,
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        responsive: [
                            {
                                breakpoint: 1024,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 1,
                                    infinite: true,
                                    dots: true
                                }
                            },
                            {
                                breakpoint: 600,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 1
                                }
                            },
                            {
                                breakpoint: 480,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1
                                }
                            }
                        ]
                    });
				});	
			</script>
		<?php endif;
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

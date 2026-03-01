<?php
namespace Tourfic\Admin\Booking_Details;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

class Tour_Booking_Details extends \Tourfic\Core\TF_Booking_Details
{
    use \Tourfic\Traits\Singleton;

    public function __construct()
    {

        $booking_args = array(
            'post_type' => 'tf_tours',
            'menu_title' => 'Tour Booking Details',
            'menu_slug' => 'tf_tours_booking',
            'capability' => 'edit_tf_tourss',
			'booking_type' => 'tour',
            'booking_title' => 'Tour'
        );

        parent::__construct($booking_args);

    }


    function voucher_details( $tf_tour_details, $tf_order_details, $tf_billing_details ) {
		?>
            <div class="customers-order-date details-box">
                <h4>
                    <?php esc_html_e("Voucher details", "tourfic"); ?>
                    <div class="others-button">
                        <?php 
                        $tf_qr_download_link = !empty($tf_tour_details->unique_id) ? $tf_tour_details->unique_id : '';
                        if(!empty($tf_qr_download_link)){
                        ?>
                        <a href="<?php echo !empty($tf_qr_download_link) ? esc_url(site_url().'?qr_id='.esc_attr($tf_qr_download_link)) : '#'; ?>" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M13 10H18L12 16L6 10H11V3H13V10ZM4 19H20V12H22V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V12H4V19Z" fill="#003C79"/>
                            </svg>
                        </a>
                        <?php } ?>
                    </div>
                </h4>
                <div class="tf-grid-box">
                    <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro()){ ?>
                    <div class="tf-grid-single">
                        <h3><?php esc_html_e("Your voucher", "tourfic"); ?></h3>
                        <?php 
                            $meta = get_post_meta( $tf_order_details->post_id, 'tf_tours_opt', true );
                            $tour_ides = !empty($tf_tour_details->unique_id) ? $tf_tour_details->unique_id : '';

                            // Address
                            $location = '';
                            if( !empty($meta['location']) && Helper::tf_data_types($meta['location'])){
                                $location = !empty( Helper::tf_data_types($meta['location'])['address'] ) ? Helper::tf_data_types($meta['location'])['address'] : $location;
                            }
                            // Tour Date
                            $tour_date = !empty($tf_tour_details->tour_date) ? $tf_tour_details->tour_date : '';
                            if ( $tour_date ) {
                                $tour_date_duration = explode( ' - ', $tour_date );
                                if(!empty($tour_date_duration[0])){
                                    $tour_in = $tour_date_duration[0];
                                }
                                if(!empty($tour_date_duration[1])){
                                    $tour_out = $tour_date_duration[1];
                                }
                            } else {
                                $tour_in = $tf_tour_details->check_in;
                                $tour_out = $tf_tour_details->check_out;
                            }
                            $tour_duration = !empty($tour_out) && !empty( $tour_in ) ? gmdate('d F, Y', strtotime($tour_in)).' - '. gmdate('d F, Y', strtotime($tour_out)) : gmdate('d F, Y', strtotime($tour_in));
                            $tour_time = !empty($tf_tour_details->tour_time) ? $tf_tour_details->tour_time : '';

                            // Contact Information
                            $tour_email    = ! empty( $meta['email'] ) ? $meta['email'] : '';
                            $tour_phone    = ! empty( $meta['phone'] ) ? $meta['phone'] : '';

                            $width = '120';
                            $height = '120'; 
                            $uri = $tour_ides;
                            $title = get_the_title( $tf_order_details->post_id );
                            
                            $tf_qr_watermark = ! empty( Helper::tfopt( 'qr_background' ) ) ? Helper::tfopt( 'qr_background' ) : TF_ASSETS_APP_URL.'images/ticket-banner.png';
                            if(!empty($tour_ides)){
                        ?>
                        <div class="tf-single-box tf-voucher-preview">
                            <div class="tf-visitor-vouchers" style="background-image: url(<?php echo esc_url($tf_qr_watermark); ?>);">
                                <div class="tf-voucher-header">
                                    <?php
                                    $tf_qr_logo = ! empty( Helper::tfopt( 'qr_logo' ) ) ? Helper::tfopt( 'qr_logo' ) : '';
                                    if(!empty($tf_qr_logo)){ ?>
                                    <img style="max-width: 140px;" src="<?php echo esc_url($tf_qr_logo); ?>" />
                                    <?php } 
                                    $tf_ticket_prefix = ! empty( Helper::tfopt( "qr-ticket-prefix" ) ) ? Helper::tfopt( "qr-ticket-prefix" ).'-' : "";
                                    $tf_ticket_title = ! empty( Helper::tfopt( "qr-ticket-title" ) ) ? Helper::tfopt( "qr-ticket-title" ) : "Booking ID";
                                    ?>
                                    <div class="title">
                                        <h1><?php echo esc_html( $title ); ?></h1>
                                        <span>
                                            <?php echo esc_html( $tf_ticket_title ) .': '. esc_html( $tf_ticket_prefix.$tour_ides ); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="tf-voucher-qr-code">
                                    <div class="time-info">
                                        <h5><?php esc_html_e("Date:", "tourfic"); ?> <b><?php echo esc_html( $tour_duration ); ?></b></h5>
                                        <?php if(!empty($tour_time)){ ?>
                                            <h5><?php esc_html_e("Time:", "tourfic"); ?> <b style="color: #002043;"><?php echo esc_html( $tour_time ); ?></b></h5>
                                        <?php } ?>
                                        <h5><?php esc_html_e("Address:", "tourfic"); ?> <b style="color: #002043;"><?php echo esc_html( $location ) ?></b></h5>
                                    </div>
                                    <img style="border: 1px solid #ccc;" src="//quickchart.io/qr?size=<?php echo esc_attr($width);?>&format=png&text=<?php echo esc_html( (string) $uri); ?>" alt="<?php echo esc_attr( $title ); ?>"/>
                                </div>
                                <div class="tf-voucher-billing-info">
                                    <div class="tf-billing-details">
                                        <?php 
                                        $billing_first_name = !empty($tf_billing_details->billing_first_name) ? $tf_billing_details->billing_first_name : '';
                                        $billing_last_name = !empty($tf_billing_details->billing_last_name) ? $tf_billing_details->billing_last_name : '';
                                        ?>
                                        <h5><?php esc_html_e("Name:", "tourfic"); ?> <?php echo esc_html( $billing_first_name.' '.$billing_last_name ); ?></h5>
                                        <h5><?php esc_html_e("Price:", "tourfic"); ?> <?php echo wp_kses_post( wc_price( $tf_tour_details->total_price ) ); ?></h5>
                                        <?php if(!empty($tf_tour_details->due_price)){ ?>
                                        <h5><?php esc_html_e("Due Price:", "tourfic"); ?> <?php echo wp_kses_post(wc_price( $tf_tour_details->due_price )) ?></h5>
                                        <?php } ?>
                                        <h5 style="text-transform: uppercase;"><?php esc_html_e("Payment Status:", "tourfic"); ?> <?php echo esc_html( $tf_order_details->payment_method ) ?></h5>
                                        <?php 
                                        if(!empty($tf_total_adult[0])){ ?>
                                            <h5><?php esc_html_e("Adult:", "tourfic"); ?> <?php echo esc_html( $tf_total_adult[0] ) ?></h5>
                                            <?php
                                        }
                                        if(!empty($tf_total_children[0])){ ?>
                                            <h5><?php esc_html_e("Child:", "tourfic"); ?> <?php echo esc_html( $tf_total_children[0] ) ?></h5>
                                            <?php
                                        }
                                        if(!empty($tf_total_infants[0])){
                                            ?>
                                            <h5><?php esc_html_e("Infant:", "tourfic"); ?> <?php echo esc_html( $tf_total_infants[0] ) ?></h5>
                                            <?php
                                        } ?>
                                    </div>
                                    <div class="tf-cta-info">
                                    <?php
                                    if(!empty($tour_phone) || !empty($tour_email)){ ?>
                                        <h4><b><?php esc_html_e("Contact Information:", "tourfic"); ?></b></h4>
                                        <h5><?php esc_html_e("For any inquiries or assistance,", "tourfic"); ?></h5>
                                        <h5><?php esc_html_e("Phone:", "tourfic"); ?> <?php echo esc_html( $tour_phone ) ?></h5>
                                        <h5><?php esc_html_e("Email:", "tourfic"); ?> <?php echo esc_html( $tour_email ) ?></h5>
                                        <?php
                                    } ?>
                                    </div>
                                </div>
                                <div class="tf-voucher-footer-qoute">
                                    <?php
                                    $tf_ticket_qottation = ! empty( Helper::tfopt( "qr-ticket-content" ) ) ? Helper::tfopt( "qr-ticket-content" ) : "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s."; ?>
                                    <p><?php echo esc_html( $tf_ticket_qottation ); ?></p>
                                </div>
                            </div>
                            <div class="tf-preview-btn">
                                <a href="#"><?php esc_html_e("Preview", "tourfic"); ?></a>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                    <?php } ?>
                </div>
            </div>
        <?php
	}

    function voucher_quick_view( $tf_tour_details, $tf_order_details, $tf_billing_details ) {
        ?>
        <?php 
            $meta = get_post_meta( $tf_order_details->post_id, 'tf_tours_opt', true );
            $tour_ides = !empty($tf_tour_details->unique_id) ? $tf_tour_details->unique_id : '';

            // Address
            $location = '';
            if( !empty($meta['location']) && Helper::tf_data_types($meta['location'])){
                $location = !empty( Helper::tf_data_types($meta['location'])['address'] ) ? Helper::tf_data_types($meta['location'])['address'] : $location;
            }
            // Tour Date
            $tour_date = !empty($tf_tour_details->tour_date) ? $tf_tour_details->tour_date : '';
            if ( $tour_date ) {
                $tour_date_duration = explode( ' - ', $tour_date );
                if(!empty($tour_date_duration[0])){
                    $tour_in = $tour_date_duration[0];
                }
                if(!empty($tour_date_duration[1])){
                    $tour_out = $tour_date_duration[1];
                }
            } else {
                $tour_in = $tf_tour_details->check_in;
                $tour_out = $tf_tour_details->check_out;
            }
            $tour_duration = !empty($tour_out) && !empty( $tour_in ) ? gmdate('d F, Y', strtotime($tour_in)).' - '. gmdate('d F, Y', strtotime($tour_out)) : gmdate('d F, Y', strtotime($tour_in));
            $tour_time = !empty($tf_tour_details->tour_time) ? $tf_tour_details->tour_time : '';

            // Contact Information
            $tour_email    = ! empty( $meta['email'] ) ? $meta['email'] : '';
            $tour_phone    = ! empty( $meta['phone'] ) ? $meta['phone'] : '';

            $width = '120';
            $height = '120'; 
            $uri = $tour_ides;
            $title = get_the_title( $tf_order_details->post_id );
            
            $tf_qr_watermark = ! empty( Helper::tfopt( 'qr_background' ) ) ? Helper::tfopt( 'qr_background' ) : TF_ASSETS_APP_URL.'images/ticket-banner.png';
        ?>
        <div class="tf-voucher-quick-view-box">
            <div class="voucher-quick-view">
                <div class="tf-voucher-details-preview-header">
                    <h2>
                        <?php esc_html_e("Voucher", "tourfic"); ?>
                    </h2>
                    <div class="tf-quick-view-times">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" fill="#FCFDFF"/>
                            <path d="M12 11.1111L15.1111 8L16 8.88889L12.8889 12L16 15.1111L15.1111 16L12 12.8889L8.88889 16L8 15.1111L11.1111 12L8 8.88889L8.88889 8L12 11.1111Z" fill="#666D74"/>
                            <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" stroke="#FCFDFF"/>
                            </svg>
                        </span>
                    </div>
                </div>
                
                <div class="tf-visitor-vouchers" style="background-image: url(<?php echo esc_url($tf_qr_watermark); ?>);">
                    <div class="tf-voucher-header">
                        <?php
                        $tf_qr_logo = ! empty( Helper::tfopt( 'qr_logo' ) ) ? Helper::tfopt( 'qr_logo' ) : '';
                        if(!empty($tf_qr_logo)){ ?>
                        <img style="max-width: 140px;" src="<?php echo esc_url($tf_qr_logo); ?>" />
                        <?php } 
                        $tf_ticket_prefix = ! empty( Helper::tfopt( "qr-ticket-prefix" ) ) ? Helper::tfopt( "qr-ticket-prefix" ).'-' : "";
                        $tf_ticket_title = ! empty( Helper::tfopt( "qr-ticket-title" ) ) ? Helper::tfopt( "qr-ticket-title" ) : "Booking ID";
                        ?>
                        <div class="title">
                            <h1><?php echo esc_html( $title ); ?></h1>
                            <span>
                                <?php echo esc_html( $tf_ticket_title ) .': '. esc_html( $tf_ticket_prefix.$tour_ides ); ?>
                            </span>
                        </div>
                    </div>
                    <div class="tf-voucher-qr-code">
                        <div class="time-info">
                            <h5><?php esc_html_e("Date:", "tourfic"); ?> <b><?php echo esc_html( $tour_duration ); ?></b></h5>
                            <?php if(!empty($tour_time)){ ?>
                                <h5><?php esc_html_e("Time:", "tourfic"); ?> <b style="color: #002043;"><?php echo esc_html( $tour_time ); ?></b></h5>
                            <?php } ?>
                            <h5><?php esc_html_e("Address:", "tourfic"); ?> <b style="color: #002043;"><?php echo esc_html( $location ) ?></b></h5>
                        </div>
                        <img style="border: 1px solid #ccc;" src="//quickchart.io/qr?size=<?php echo esc_attr($width);?>&format=png&text=<?php echo esc_html( (string) $uri ); ?>" alt="<?php echo esc_attr( htmlspecialchars( $title ) ); ?>"/>
                    </div>
                    <div class="tf-voucher-billing-info">
                        <div class="tf-billing-details">
                            <?php 
                            $billing_first_name = !empty($tf_billing_details->billing_first_name) ? $tf_billing_details->billing_first_name : '';
                            $billing_last_name = !empty($tf_billing_details->billing_last_name) ? $tf_billing_details->billing_last_name : '';
                            ?>
                            <h5><?php esc_html_e("Name:", "tourfic"); ?> <?php echo esc_html( $billing_first_name.' '.$billing_last_name ); ?></h5>
                            <h5><?php esc_html_e("Price:", "tourfic"); ?> <?php echo wp_kses_post(wc_price( $tf_tour_details->total_price )) ?></h5>
                            <?php if(!empty($tf_tour_details->due_price)){ ?>
                            <h5><?php esc_html_e("Due Price:", "tourfic"); ?> <?php echo wp_kses_post(wc_price( $tf_tour_details->due_price )) ?></h5>
                            <?php } ?>
                            <h5 style="text-transform: uppercase;"><?php esc_html_e("Payment Status:", "tourfic"); ?> <?php echo esc_html( $tf_order_details->payment_method ) ?></h5>
                            <?php 
                            if(!empty($tf_total_adult[0])){ ?>
                                <h5><?php esc_html_e("Adult:", "tourfic"); ?> <?php echo esc_html( $tf_total_adult[0] ) ?></h5>
                                <?php
                            }
                            if(!empty($tf_total_children[0])){ ?>
                                <h5><?php esc_html_e("Child:", "tourfic"); ?> <?php echo esc_html( $tf_total_children[0] ) ?></h5>
                                <?php
                            }
                            if(!empty($tf_total_infants[0])){
                                ?>
                                <h5><?php esc_html_e("Infant:", "tourfic"); ?> <?php echo esc_html( $tf_total_infants[0] ) ?></h5>
                                <?php
                            } ?>
                        </div>
                        <div class="tf-cta-info">
                        <?php
                        if(!empty($tour_phone) || !empty($tour_email)){ ?>
                            <h4><b><?php esc_html_e("Contact Information:", "tourfic"); ?></b></h4>
                            <h5><?php esc_html_e("For any inquiries or assistance,", "tourfic"); ?></h5>
                            <h5><?php esc_html_e("Phone:", "tourfic"); ?> <?php echo esc_html( $tour_phone ) ?></h5>
                            <h5><?php esc_html_e("Email:", "tourfic"); ?> <?php echo esc_html( $tour_email ) ?></h5>
                            <?php
                        } ?>
                        </div>
                    </div>
                    <div class="tf-voucher-footer-qoute">
                        <?php
                        $tf_ticket_qottation = ! empty( Helper::tfopt( "qr-ticket-content" ) ) ? Helper::tfopt( "qr-ticket-content" ) : "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s."; ?>
                        <p><?php echo esc_html( $tf_ticket_qottation ); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    function check_in_out_status( $tf_order_details) {
        ?>
        <div class="customers-order-date details-box">
            <div class="tf-grid-box">
                <div class="tf-grid-single">
                    <h4><?php esc_html_e("Others information", "tourfic"); ?></h4>
                    <div class="tf-single-box tf-checkin-by">
                        <table class="table" cellpadding="0" callspacing="0">
                            <tr>
                                <th><?php esc_html_e("Checked status", "tourfic"); ?></th>
                                <td>:</td>
                                <td>
                                    <?php 
                                        if( !empty($tf_order_details->checkinout) ){
                                            if( "in"==$tf_order_details->checkinout ){
                                                esc_html_e("Checked in", "tourfic");
                                            }elseif( "out"==$tf_order_details->checkinout ){
                                                esc_html_e("Checked Out", "tourfic");
                                            }elseif( "not"==$tf_order_details->checkinout ){
                                                esc_html_e("Not checked in", "tourfic");
                                            }
                                        }else{
                                            esc_html_e("Not checked in", "tourfic");
                                        }
                                    ?>
                                </td>
                            </tr>
                            <?php 
                            $tf_checkinout_by = !empty($tf_order_details->checkinout_by) ? json_decode($tf_order_details->checkinout_by) : '';
                            ?>
                            <tr>
                                <th><?php esc_html_e("Checked in by", "tourfic"); ?></th>
                                <td>:</td>
                                <td>
                                    <?php
                                    if(!empty($tf_checkinout_by->userid)){
                                        $tf_checkin_by = get_user_by('id', $tf_checkinout_by->userid);
                                        echo !empty($tf_checkin_by->display_name) ? esc_html($tf_checkin_by->display_name) : "";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e("Checked Time", "tourfic"); ?></th>
                                <td>:</td>
                                <td>
                                    <?php
                                    if(!empty($tf_checkinout_by->time)){
                                        echo !empty($tf_checkinout_by->time) ? esc_html($tf_checkinout_by->time) : "";
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

}
// $endTime = microtime(true);

// $totalTime = $endTime - $startTime;

// echo '<pre style="float: right; margin-right: 20px">';
// print_r($totalTime);
// echo "</pre>";
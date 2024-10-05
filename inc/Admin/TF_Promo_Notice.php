<?php

namespace Tourfic\Admin;

defined( 'ABSPATH' ) || exit;

class TF_Promo_Notice {

    
    use \Tourfic\Traits\Singleton;

    // private $api_url = 'http://tf-api.test/';
    private $api_url = 'https://api.themefic.com/';
    private $args = array();
    private $responsed = false; 
    private $tf_promo_option = false; 
    private $error_message = ''; 

    private $promo_side_data = array();

    private $months = [
        'January',  
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ];
    private $plugins_existes = ['ins', 'uacf7', 'beaf', 'ebef'];

    public function __construct() {   
        if(in_array(gmdate('F'), $this->months) && !function_exists('is_tf_pro') ){ 
 
            $tf_promo__schudle_start_from = !empty(get_option( 'tf_promo__schudle_start_from' )) ? get_option( 'tf_promo__schudle_start_from' ) : 0;
       
            if($tf_promo__schudle_start_from == 0){
                // delete option
                delete_option('tf_promo__schudle_option');
            }elseif($tf_promo__schudle_start_from  != 0 && $tf_promo__schudle_start_from > time()){
                return;
            }   
           
             
            add_filter('cron_schedules', array($this, 'tf_custom_cron_interval'));
        
            if (!wp_next_scheduled('tf_promo__schudle')) {
                wp_schedule_event(time(), 'every_day', 'tf_promo__schudle');
            }
            
            add_action('tf_promo__schudle', array($this, 'tf_promo__schudle_callback'));
          

            if(get_option( 'tf_promo__schudle_option' )){
                $this->tf_promo_option = get_option( 'tf_promo__schudle_option' );
            } 

            $tf_existes = get_option( 'tf_promo_notice_exists' );
             
            $dashboard_banner = isset($this->tf_promo_option['dashboard_banner']) ? $this->tf_promo_option['dashboard_banner'] : '';

            // Admin Notice 
            if( ! in_array($tf_existes, $this->plugins_existes) && is_array($dashboard_banner) && strtotime($dashboard_banner['end_date']) > time() && strtotime($dashboard_banner['start_date']) < time() && $dashboard_banner['enable_status'] == true){
                add_action( 'admin_notices', array( $this, 'tf_promo_dashboard_admin_notice' ) );
                add_action( 'wp_ajax_tf_promo_dashboard_admin_notice_dismiss_callback', array($this, 'tf_promo_dashboard_admin_notice_dismiss_callback') );
            }
            
            // side Notice 
            $service_banner = isset($this->tf_promo_option['service_banner']) ? $this->tf_promo_option['service_banner'] : array();
            $promo_banner = isset($this->tf_promo_option['promo_banner']) ? $this->tf_promo_option['promo_banner'] : array();

            $current_day = date('l'); 
            if(isset($service_banner['enable_status']) && $service_banner['enable_status'] == true && in_array($current_day, $service_banner['display_days'])){ 
             
                $start_date = isset($service_banner['start_date']) ? $service_banner['start_date'] : '';
                $end_date = isset($service_banner['end_date']) ? $service_banner['end_date'] : '';
                $enable_side = isset($service_banner['enable_status']) ? $service_banner['enable_status'] : false;
            }else{  
                $start_date = isset($promo_banner['start_date']) ? $promo_banner['start_date'] : '';
                $end_date = isset($promo_banner['end_date']) ? $promo_banner['end_date'] : '';
                $enable_side = isset($promo_banner['enable_status']) ? $promo_banner['enable_status'] : false;
            } 


            if(is_array($this->tf_promo_option) && strtotime($end_date) > time() && strtotime($start_date) < time()  && $enable_side == true){ 
                add_action( 'add_meta_boxes', array( $this,  'tf_promo_notice_hotel_tour_docs' ) );
                add_action( 'wp_ajax_tf_promo_notice_custom_post_meta_callback', array($this, 'tf_promo_notice_custom_post_meta_callback') );
            } 


            register_deactivation_hook( TF_PATH . 'tourfic.php', array($this, 'tf_promo_notice_deactivation_hook') );
        }

        
       
    }

    public function tf_get_api_response(){
        $query_params = array(
            'plugin' => 'tf', 
        );
        $response = wp_remote_post($this->api_url, array(
            'body'    => wp_json_encode($query_params),
            'headers' => array('Content-Type' => 'application/json'),
        )); 
        if (is_wp_error($response)) {
            // Handle API request error
            $this->responsed = false;
            $this->error_message = esc_html($response->get_error_message());
 
        } else {
            // API request successful, handle the response content
            $data = wp_remote_retrieve_body($response);
           
            $this->responsed = json_decode($data, true); 

            $tf_promo__schudle_option = get_option( 'tf_promo__schudle_option' ); 
            if(isset($ins_promo__schudle_option['notice_name']) && $tf_promo__schudle_option['notice_name'] != $this->responsed['notice_name']){ 
                // Unset the cookie variable in the current script 

                update_option( 'tf_dismiss_admin_notice', 1);
                update_option( 'tf_hotel_promo_sidebar_notice', 1); 
                update_option( 'tf_tour_promo_sidebar_notice', 1); 
                update_option( 'tf_apartment_promo_sidebar_notice', 1); 
                update_option( 'tf_room_promo_sidebar_notice', 1); 

                update_option( 'tf_promo__schudle_start_from', time() + 43200);
            }elseif(empty($tf_promo__schudle_option)){
                update_option( 'tf_promo__schudle_start_from', time() + 43200);
            }
            update_option( 'tf_promo__schudle_option', $this->responsed);
            
        } 
    }

    // Define the custom interval
    public function tf_custom_cron_interval($schedules) {
        $schedules['every_day'] = array(
            'interval' => 86400, // Every 24 hours
            // 'interval' => 5, // Every 24 hours
            'display' => esc_html__('Every 24 hours')
        );
        return $schedules;
    }

    public function tf_promo__schudle_callback() {  

        $this->tf_get_api_response();

    }
 

    /**
     * Black Friday Deals 2023
     */
    
    public function tf_promo_dashboard_admin_notice(){ 
        
        $dashboard_banner = isset($this->tf_promo_option['dashboard_banner']) ? $this->tf_promo_option['dashboard_banner'] : '';
        $image_url = isset($dashboard_banner['banner_url']) ? esc_url($dashboard_banner['banner_url']) : '';
        $deal_link = isset($dashboard_banner['redirect_url']) ? esc_url($dashboard_banner['redirect_url']) : ''; 
        
        $tf_dismiss_admin_notice = get_option( 'tf_dismiss_admin_notice' );
        $get_current_screen = get_current_screen();  
        if(($tf_dismiss_admin_notice == 1  || time() >  $tf_dismiss_admin_notice ) && $get_current_screen->base == 'dashboard'   ){ 
          
         // if very fist time then set the dismiss for our other plugins
           update_option( 'tf_promo_notice_exists', 'tf' );
           
           ?>
            <style> 
                .tf_black_friday_20222_admin_notice a:focus {
                    box-shadow: none;
                } 
                .tf_black_friday_20222_admin_notice {
                    padding: 7px;
                    position: relative;
                    z-index: 10;
                    max-width: 825px;
                } 
                .tf_black_friday_20222_admin_notice button:before {
                    color: #fff !important;
                }
                .tf_black_friday_20222_admin_notice button:hover::before {
                    color: #d63638 !important;
                }
            </style>
            <div class="notice notice-success tf_black_friday_20222_admin_notice"> 
                <a href="<?php echo esc_attr( $deal_link ); ?>" style="display: block; line-height: 0;" target="_blank" >
                    <img  style="width: 100%;" src="<?php echo esc_attr($image_url) ?>" alt="">
                </a> 
                <?php if( isset($dashboard_banner['dismiss_status']) && $dashboard_banner['dismiss_status'] == true): ?>
                <button type="button" class="notice-dismiss tf_black_friday_notice_dismiss"><span class="screen-reader-text"><?php echo esc_html__('Dismiss this notice.', 'ultimate-addons-cf7' ) ?></span></button>
                <?php  endif; ?>
            </div>
            <script>
                jQuery(document).ready(function($) {
                    $(document).on('click', '.tf_black_friday_notice_dismiss', function( event ) {
                        jQuery('.tf_black_friday_20222_admin_notice').css('display', 'none')
                        data = {
                            action : 'tf_promo_dashboard_admin_notice_dismiss_callback',
                        };

                        $.ajax({
                            url: ajaxurl,
                            type: 'post',
                            data: data,
                            success: function (data) { ;
                            },
                            error: function (data) { 
                            }
                        });
                    });
                });
            </script>
        
        <?php 
        }
        
    } 


    public function tf_promo_dashboard_admin_notice_dismiss_callback() {  

        $tf_promo_option = get_option( 'tf_promo__schudle_option' );
        $restart = isset($tf_promo_option['dasboard_restart']) && $tf_promo_option['dasboard_restart'] != false ? $tf_promo_option['dasboard_restart'] : false; 
        if($restart == false){
            update_option( 'tf_dismiss_admin_notice', strtotime($tf_promo_option['end_date']) ); 
        }else{
            update_option( 'tf_dismiss_admin_notice', time() + (86400 * $restart) );  
        } 
		wp_die();
	}

    public function tf_promo_notice_hotel_tour_docs() {
        $tf_hotel_promo_sidebar_notice = get_option( 'tf_hotel_promo_sidebar_notice' );  
         
		if ( $tf_hotel_promo_sidebar_notice == 1  || time() >  $tf_hotel_promo_sidebar_notice ) { 
			add_meta_box( 'tfhotel_promo_notice_docs', ' ', array($this, 'tf_promo_notice_callback_hotel'), 'tf_hotel', 'side',  'high' );
		}

        $tf_tour_promo_sidebar_notice = get_option( 'tf_tour_promo_sidebar_notice' );  
		if ( $tf_tour_promo_sidebar_notice == 1  || time() >  $tf_tour_promo_sidebar_notice ) { 
			add_meta_box( 'tftour_promo_notice_docs', ' ', array($this, 'tf_promo_notice_callback_tour'), 'tf_tours', 'side', 'high' );
		}

        $tf_apartment_promo_sidebar_notice = get_option( 'tf_apartment_promo_sidebar_notice' );  
		if ( $tf_apartment_promo_sidebar_notice == 1  || time() >  $tf_apartment_promo_sidebar_notice ) {  
			add_meta_box( 'tfapartment_promo_notice_docs', ' ', array($this, 'tf_promo_notice_callback_apartment'), 'tf_apartment', 'side', 'high' );
		}

        $tf_room_promo_sidebar_notice = get_option( 'tf_room_promo_sidebar_notice' );  
		if ( $tf_room_promo_sidebar_notice == 1  || time() >  $tf_room_promo_sidebar_notice ) {  
			add_meta_box( 'tfroom_promo_notice_docs', ' ', array($this, 'tf_promo_notice_callback_room'), 'tf_room', 'side', 'high' );
		}
	}

    public function set_promo_side_data(){
        $service_banner = isset($this->tf_promo_option['service_banner']) ? $this->tf_promo_option['service_banner'] : array();
        $promo_banner = isset($this->tf_promo_option['promo_banner']) ? $this->tf_promo_option['promo_banner'] : array();

        $current_day = date('l'); 
        if($service_banner['enable_status'] == true && in_array($current_day, $service_banner['display_days'])){ 
           
            $this->promo_side_data['image_url'] = esc_url($service_banner['banner_url']);
            $this->promo_side_data['deal_link'] = esc_url($service_banner['redirect_url']);  
            $this->promo_side_data['dismiss_status']  = $service_banner['dismiss_status'];
        }else{
            $this->promo_side_data['image_url']= esc_url($promo_banner['banner_url']);
            $this->promo_side_data['deal_link'] = esc_url($promo_banner['redirect_url']); 
            $this->promo_side_data['dismiss_status']  = $promo_banner['dismiss_status'];  
        }  
    }

    public function tf_promo_notice_callback_hotel() {
        $this->set_promo_side_data();

        
		?>
        <style>
			#tfhotel_promo_notice_docs{
				border: 0px solid;
				box-shadow: none;
				background: transparent;
			}
            .tf_promo_notice_side_preview a:focus {
                box-shadow: none;
            }

            .tf_promo_notice_side_preview a {
                display: inline-block;
            }

            #tfhotel_promo_notice_docs .inside {
                padding: 0;
                margin-top: 0;
            }

            #tfhotel_promo_notice_docs .postbox-header {
                display: none;
                visibility: hidden;
            }
        </style>
        <div class="tf_promo_notice_side_preview" style="text-align: center; overflow: hidden;">
            <a href="<?php echo esc_attr($this->promo_side_data['deal_link']); ?>" target="_blank" >
                <img  style="width: 100%;" src="<?php echo esc_attr($this->promo_side_data['image_url']); ?>" alt="">
            </a>  
            <?php if( isset($this->promo_side_data['dismiss_status']) && $this->promo_side_data['dismiss_status'] == true): ?>
                <button type="button" class="notice-dismiss tf_promo_notice_dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            <?php  endif; ?>
          
			<script>
                jQuery(document).ready(function($) {
                    $(document).on('click', '.tf_promo_notice_dismiss', function( event ) {  
                        jQuery('.tf_promo_notice_side_preview').css('display', 'none');
                        data = {
                            action : 'tf_promo_notice_custom_post_meta_callback',
                            post_type : 'tf_hotel',
                        };

                        $.ajax({
                            url: ajaxurl,
                            type: 'post',
                            data: data,
                            success: function (data) { ;
                            },
                            error: function (data) { 
                            }
                        });
                        
                    });
                });
        	</script>
        </div>
		<?php
	}

	public function tf_promo_notice_callback_tour() { 
        $this->set_promo_side_data(); 
		?>
        <style>
			#tftour_promo_notice_docs{
				border: 0px solid;
				box-shadow: none;
				background: transparent;
			}
            .tf_promo_notice_side_preview a:focus {
                box-shadow: none;
            }

            .tf_promo_notice_side_preview a {
                display: inline-block;
            }

            #tftour_promo_notice_docs .inside {
                padding: 0;
                margin-top: 0;
            }

            #tftour_promo_notice_docs .postbox-header {
                display: none;
                visibility: hidden;
            }
        </style>
        <div class="tf_promo_notice_side_preview" style="text-align: center; overflow: hidden;">
            <a href="<?php echo esc_attr($this->promo_side_data['deal_link']); ?>" target="_blank" >
                <img  style="width: 100%;" src="<?php echo esc_attr($this->promo_side_data['image_url']); ?>" alt="">
            </a>  
            <?php if( isset($this->promo_side_data['dismiss_status']) && $this->promo_side_data['dismiss_status']== true): ?>
                <button type="button" class="notice-dismiss tf_promo_notice_dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            <?php  endif; ?>
        </div>

		<script>
		jQuery(document).ready(function($) {
			$(document).on('click', '.tf_promo_notice_dismiss', function( event ) { 
				jQuery('.tf_promo_notice_side_preview').css('display', 'none')
                data = {
                    action : 'tf_promo_notice_custom_post_meta_callback',
                    post_type : 'tf_tour',
                };

                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    data: data,
                    success: function (data) { ;
                    },
                    error: function (data) { 
                    }
                });
			});
		});
		</script>
		<?php
	}
	public function tf_promo_notice_callback_apartment() {
        $this->set_promo_side_data();
		?>
        <style>
			#tfapartment_promo_notice_docs{
				border: 0px solid;
				box-shadow: none;
				background: transparent;
			}
            .tf_promo_notice_side_preview a:focus {
                box-shadow: none;
            }

            .tf_promo_notice_side_preview a {
                display: inline-block;
            }

            #tfapartment_promo_notice_docs .inside {
                padding: 0;
                margin-top: 0;
            }

            #tfapartment_promo_notice_docs .postbox-header {
                display: none;
                visibility: hidden;
            }
        </style>
        <div class="tf_promo_notice_side_preview" style="text-align: center; overflow: hidden;">
            <a href="<?php echo esc_attr($this->promo_side_data['deal_link']); ?>" target="_blank" >
                <img  style="width: 100%;" src="<?php echo esc_attr($this->promo_side_data['image_url']); ?>" alt="">
            </a>  
            <?php if( isset($this->promo_side_data['dismiss_status']) && $this->promo_side_data['dismiss_status'] == true): ?>
                <button type="button" class="notice-dismiss tf_promo_notice_dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            <?php  endif; ?>
        </div>

		<script>
		jQuery(document).ready(function($) {
			$(document).on('click', '.tf_promo_notice_dismiss', function( event ) { 
				jQuery('.tf_promo_notice_side_preview').css('display', 'none');
                data = {
                    action : 'tf_promo_notice_custom_post_meta_callback',
                    post_type : 'tf_apartment',
                };

                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    data: data,
                    success: function (data) { ;
                    },
                    error: function (data) { 
                    }
                });
			});
		});
		</script>
		<?php
	}
	public function tf_promo_notice_callback_room() {
        $this->set_promo_side_data();
		?>
        <style>
			#tfroom_promo_notice_docs{
				border: 0px solid;
				box-shadow: none;
				background: transparent;
			}
            .tf_promo_notice_side_preview a:focus {
                box-shadow: none;
            }

            .tf_promo_notice_side_preview a {
                display: inline-block;
            }

            #tfroom_promo_notice_docs .inside {
                padding: 0;
                margin-top: 0;
            }

            #tfroom_promo_notice_docs .postbox-header {
                display: none;
                visibility: hidden;
            }
        </style>
        <div class="tf_promo_notice_side_preview" style="text-align: center; overflow: hidden;">
            <a href="<?php echo esc_attr($this->promo_side_data['deal_link']); ?>" target="_blank" >
                <img  style="width: 100%;" src="<?php echo esc_attr($this->promo_side_data['image_url']); ?>" alt="">
            </a>  
            <?php if( isset($this->promo_side_data['dismiss_status']) && $this->promo_side_data['dismiss_status'] == true): ?>
                <button type="button" class="notice-dismiss tf_promo_notice_dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            <?php  endif; ?>
        </div>

		<script>
		jQuery(document).ready(function($) {
			$(document).on('click', '.tf_promo_notice_dismiss', function( event ) { 
				jQuery('.tf_promo_notice_side_preview').css('display', 'none');
                data = {
                    action : 'tf_promo_notice_custom_post_meta_callback',
                    post_type : 'tf_room',
                };

                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    data: data,
                    success: function (data) { ;
                    },
                    error: function (data) { 
                    }
                });
			});
		});
		</script>
		<?php
	}

    public  function tf_promo_notice_custom_post_meta_callback() {   
         
        $tf_promo_option = get_option( 'tf_promo__schudle_option' );
        $start_date = isset($tf_promo_option['start_date']) ? strtotime($tf_promo_option['start_date']) : time();
        $restart = isset($tf_promo_option['side_restart']) && $tf_promo_option['side_restart'] != false ? $tf_promo_option['side_restart'] : 5;
        if(isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'tf_hotel'){ 
            update_option( 'tf_hotel_promo_sidebar_notice', time() + (86400 * $restart) );  
        }

        if(isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'tf_tour'){ 
            update_option( 'tf_tour_promo_sidebar_notice', time() + (86400 * $restart) );  
        }
        

        if(isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'tf_apartment'){ 
            update_option( 'tf_apartment_promo_sidebar_notice', time() + (86400 * $restart) );  
        }

        if(isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'tf_room'){ 
            update_option( 'tf_room_promo_sidebar_notice', time() + (86400 * $restart) );  
        }
        
        wp_die();
    }
     // Deactivation Hook
     public function tf_promo_notice_deactivation_hook() {
        wp_clear_scheduled_hook('tf_promo__schudle'); 

        delete_option('tf_promo__schudle_option');
        delete_option('tf_hotel_promo_sidebar_notice');
        delete_option('tf_tour_promo_sidebar_notice');
        delete_option('tf_apartment_promo_sidebar_notice');
        delete_option('tf_room_promo_sidebar_notice');
        delete_option('tf_promo__schudle_start_from');
        delete_option('tf_promo_notice_exists');
    }
 
}
 

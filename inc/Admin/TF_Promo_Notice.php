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

    private $months = ['January', 'June', 'November', 'December']; 
    private $plugins_existes = ['ins', 'uacf7', 'beaf', 'ebef'];

    public function __construct() { 
        if(in_array(gmdate('F'), $this->months) && !function_exists('is_tf_pro') ){
             
            add_filter('cron_schedules', array($this, 'tf_custom_cron_interval'));
        
            if (!wp_next_scheduled('tf_promo__schudle')) {
                wp_schedule_event(time(), 'every_day', 'tf_promo__schudle');
            }
            
            add_action('tf_promo__schudle', array($this, 'tf_promo__schudle_callback'));
          

            if(get_option( 'tf_promo__schudle_option' )){
                $this->tf_promo_option = get_option( 'tf_promo__schudle_option' );
            } 

            $tf_existes = get_option( 'tf_promo_notice_exists' );
             
            // Admin Notice 
            if( ! in_array($tf_existes, $this->plugins_existes) && is_array($this->tf_promo_option) && strtotime($this->tf_promo_option['end_date']) > time() && strtotime($this->tf_promo_option['start_date']) < time()){
           
                add_action( 'admin_notices', array( $this, 'tf_black_friday_2023_admin_notice' ) );
                add_action( 'wp_ajax_tf_black_friday_notice_dismiss_callback', array($this, 'tf_black_friday_notice_dismiss_callback') );
            }
            
            // side Notice 
            if(is_array($this->tf_promo_option) && strtotime($this->tf_promo_option['end_date']) > time() && strtotime($this->tf_promo_option['start_date']) < time()){ 
                add_action( 'add_meta_boxes', array( $this,  'tf_black_friday_2023_hotel_tour_docs' ) );
                add_action( 'wp_ajax_tf_black_friday_notice_dismiss_custom_post_meta_callback', array($this, 'tf_black_friday_notice_dismiss_custom_post_meta_callback') );
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
            if(isset($ins_promo__schudle_option['notice_name']) || $tf_promo__schudle_option['notice_name'] != $this->responsed['notice_name']){ 
                // Unset the cookie variable in the current script
                update_option( 'tf_dismiss_admin_notice', 1);
                update_option( 'tf_hotel_friday_sidbar_notice', 1); 
                update_option( 'tf_tour_friday_sidbar_notice', 1); 
                update_option( 'tf_apartment_friday_sidbar_notice', 1); 
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
    
    public function tf_black_friday_2023_admin_notice(){ 
        
        $image_url = isset($this->tf_promo_option['dasboard_url']) ? esc_url($this->tf_promo_option['dasboard_url']) : '';
        $deal_link = isset($this->tf_promo_option['promo_url']) ? esc_url($this->tf_promo_option['promo_url']) : ''; 

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
                <?php if( isset($this->tf_promo_option['dasboard_dismiss']) && $this->tf_promo_option['dasboard_dismiss'] == true): ?>
                <button type="button" class="notice-dismiss tf_black_friday_notice_dismiss"><span class="screen-reader-text"><?php echo esc_html__('Dismiss this notice.', 'ultimate-addons-cf7' ) ?></span></button>
                <?php  endif; ?>
            </div>
            <script>
                jQuery(document).ready(function($) {
                    $(document).on('click', '.tf_black_friday_notice_dismiss', function( event ) {
                        jQuery('.tf_black_friday_20222_admin_notice').css('display', 'none')
                        data = {
                            action : 'tf_black_friday_notice_dismiss_callback',
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


    public function tf_black_friday_notice_dismiss_callback() {  

        $tf_promo_option = get_option( 'tf_promo__schudle_option' );
        $restart = isset($tf_promo_option['dasboard_restart']) && $tf_promo_option['dasboard_restart'] != false ? $tf_promo_option['dasboard_restart'] : false; 
        if($restart == false){
            update_option( 'tf_dismiss_admin_notice', strtotime($tf_promo_option['end_date']) ); 
        }else{
            update_option( 'tf_dismiss_admin_notice', time() + (86400 * $restart) );  
        } 
		wp_die();
	}

    public function tf_black_friday_2023_hotel_tour_docs() {
        $tf_hotel_friday_sidbar_notice = get_option( 'tf_hotel_friday_sidbar_notice' );  
		if ( $tf_hotel_friday_sidbar_notice == 1  || time() >  $tf_hotel_friday_sidbar_notice ) {
			add_meta_box( 'tfhotel_black_friday_docs', '', array($this, 'tf_black_friday_2023_callback_hotel'), 'tf_hotel', 'side', 'high' );
		}

        $tf_tour_friday_sidbar_notice = get_option( 'tf_tour_friday_sidbar_notice' );  
		if ( $tf_tour_friday_sidbar_notice == 1  || time() >  $tf_tour_friday_sidbar_notice ) { 
			add_meta_box( 'tftour_black_friday_docs', '', array($this, 'tf_black_friday_2023_callback_tour'), 'tf_tours', 'side', 'high' );
		}

        $tf_apartment_friday_sidbar_notice = get_option( 'tf_apartment_friday_sidbar_notice' );  
		if ( $tf_apartment_friday_sidbar_notice == 1  || time() >  $tf_apartment_friday_sidbar_notice ) {  
			add_meta_box( 'tfapartment_black_friday_docs', '', array($this, 'tf_black_friday_2023_callback_apartment'), 'tf_apartment', 'side', 'high' );
		}
	}

    public function tf_black_friday_2023_callback_hotel() {
        $image_url = isset($this->tf_promo_option['side_url']) ? esc_url($this->tf_promo_option['side_url']) : '';
        $deal_link = isset($this->tf_promo_option['promo_url']) ? esc_url($this->tf_promo_option['promo_url']) : '';
		?>
        <style>
			#tfhotel_black_friday_docs{
				border: 0px solid;
				box-shadow: none;
				background: transparent;
			}
            .back_friday_2023_preview a:focus {
                box-shadow: none;
            }

            .back_friday_2023_preview a {
                display: inline-block;
            }

            #tfhotel_black_friday_docs .inside {
                padding: 0;
                margin-top: 0;
            }

            #tfhotel_black_friday_docs .postbox-header {
                display: none;
                visibility: hidden;
            }
        </style>
        <div class="back_friday_2023_preview" style="text-align: center; overflow: hidden;">
            <a href="<?php echo esc_attr($deal_link); ?>" target="_blank" >
                <img  style="width: 100%;" src="<?php echo esc_attr($image_url); ?>" alt="">
            </a>  
            <?php if( isset($this->tf_promo_option['side_dismiss']) && $this->tf_promo_option['side_dismiss'] == true): ?>
                <button type="button" class="notice-dismiss ins_friday_notice_dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            <?php  endif; ?>
          
			<script>
                jQuery(document).ready(function($) {
                    $(document).on('click', '.tf_hotel_friday_notice_dismiss', function( event ) { 
                        jQuery('.back_friday_2023_preview').css('display', 'none');
                        data = {
                            action : 'tf_black_friday_notice_dismiss_custom_post_meta_callback',
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

	public function tf_black_friday_2023_callback_tour() {
        $image_url = isset($this->tf_promo_option['side_url']) ? esc_url($this->tf_promo_option['side_url']) : '';
        $deal_link = isset($this->tf_promo_option['promo_url']) ? esc_url($this->tf_promo_option['promo_url']) : '';
		?>
        <style>
			#tftour_black_friday_docs{
				border: 0px solid;
				box-shadow: none;
				background: transparent;
			}
            .back_friday_2023_preview a:focus {
                box-shadow: none;
            }

            .back_friday_2023_preview a {
                display: inline-block;
            }

            #tftour_black_friday_docs .inside {
                padding: 0;
                margin-top: 0;
            }

            #tftour_black_friday_docs .postbox-header {
                display: none;
                visibility: hidden;
            }
        </style>
        <div class="back_friday_2023_preview" style="text-align: center; overflow: hidden;">
            <a href="<?php echo esc_attr($deal_link); ?>" target="_blank" >
                <img  style="width: 100%;" src="<?php echo esc_attr($image_url); ?>" alt="">
            </a>  
            <?php if( isset($this->tf_promo_option['side_dismiss']) && $this->tf_promo_option['side_dismiss'] == true): ?>
                <button type="button" class="notice-dismiss ins_friday_notice_dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            <?php  endif; ?>
        </div>

		<script>
		jQuery(document).ready(function($) {
			$(document).on('click', '.tf_tour_friday_notice_dismiss', function( event ) { 
				jQuery('.back_friday_2023_preview').css('display', 'none')
                data = {
                    action : 'tf_black_friday_notice_dismiss_custom_post_meta_callback',
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
	public function tf_black_friday_2023_callback_apartment() {
        $image_url = isset($this->tf_promo_option['side_url']) ? esc_url($this->tf_promo_option['side_url']) : '';
        $deal_link = isset($this->tf_promo_option['promo_url']) ? esc_url($this->tf_promo_option['promo_url']) : ''; 
		?>
        <style>
			#tfapartment_black_friday_docs{
				border: 0px solid;
				box-shadow: none;
				background: transparent;
			}
            .back_friday_2023_preview a:focus {
                box-shadow: none;
            }

            .back_friday_2023_preview a {
                display: inline-block;
            }

            #tfapartment_black_friday_docs .inside {
                padding: 0;
                margin-top: 0;
            }

            #tfapartment_black_friday_docs .postbox-header {
                display: none;
                visibility: hidden;
            }
        </style>
        <div class="back_friday_2023_preview" style="text-align: center; overflow: hidden;">
            <a href="<?php echo esc_attr($deal_link); ?>" target="_blank" >
                <img  style="width: 100%;" src="<?php echo esc_attr($image_url); ?>" alt="">
            </a>  
            <?php if( isset($this->tf_promo_option['side_dismiss']) && $this->tf_promo_option['side_dismiss'] == true): ?>
                <button type="button" class="notice-dismiss ins_friday_notice_dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            <?php  endif; ?>
        </div>

		<script>
		jQuery(document).ready(function($) {
			$(document).on('click', '.tf_apartment_friday_notice_dismiss', function( event ) { 
				jQuery('.back_friday_2023_preview').css('display', 'none');
                data = {
                    action : 'tf_black_friday_notice_dismiss_custom_post_meta_callback',
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

    public  function tf_black_friday_notice_dismiss_custom_post_meta_callback() {   
         
        if(isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'tf_hotel'){
            $tf_promo_option = get_option( 'tf_promo__schudle_option' );
            $start_date = isset($tf_promo_option['start_date']) ? strtotime($tf_promo_option['start_date']) : time();
            $restart = isset($tf_promo_option['side_restart']) && $tf_promo_option['side_restart'] != false ? $tf_promo_option['side_restart'] : 5;
            update_option( 'tf_hotel_friday_sidbar_notice', time() + (86400 * $restart) );  
        }

        if(isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'tf_tour'){
            $tf_promo_option = get_option( 'tf_promo__schudle_option' );
            $start_date = isset($tf_promo_option['start_date']) ? strtotime($tf_promo_option['start_date']) : time();
            $restart = isset($tf_promo_option['side_restart']) && $tf_promo_option['side_restart'] != false ? $tf_promo_option['side_restart'] : 5;
            update_option( 'tf_tour_friday_sidbar_notice', time() + (86400 * $restart) );  
        }
        

        if(isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'tf_apartment'){
            $tf_promo_option = get_option( 'tf_promo__schudle_option' );
            $start_date = isset($tf_promo_option['start_date']) ? strtotime($tf_promo_option['start_date']) : time();
            $restart = isset($tf_promo_option['side_restart']) && $tf_promo_option['side_restart'] != false ? $tf_promo_option['side_restart'] : 5;
            update_option( 'tf_apartment_friday_sidbar_notice', time() + (86400 * $restart) );  
        }
        
        wp_die();
    }
     // Deactivation Hook
     public function tf_promo_notice_deactivation_hook() {
        wp_clear_scheduled_hook('tf_promo__schudle'); 

        delete_option('tf_promo__schudle_option');
        delete_option('tf_hotel_friday_sidbar_notice');
        delete_option('tf_tour_friday_sidbar_notice');
        delete_option('tf_apartment_friday_sidbar_notice');
        delete_option('tf_promo_notice_exists');
    }
 
}

new TF_PROMO_NOTICE();

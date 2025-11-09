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
    private $plugins_existes = ['ins', 'uacf7', 'beaf', 'ebef', 'uawpf', 'hydra'];

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

            $current_day = gmdate('l'); 
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

            $tf_widget_exists = get_option('tf_promo_widget_exists');
            $dashboard_widget = isset($this->tf_promo_option['dashboard_widget']) ? $this->tf_promo_option['dashboard_widget'] : [];
            if (
                !in_array($tf_widget_exists, $this->plugins_existes) && 
                isset($dashboard_widget['enable_status']) && 
                $dashboard_widget['enable_status'] == true
            ) {
                // Mark that one Themefic widget already exists
                update_option('tf_promo_widget_exists', 'tf');
                
                add_action('wp_dashboard_setup', [$this, 'register_dashboard_notice_widget']);
                add_action('wp_ajax_tf_dashboard_widget_dismiss', [$this, 'tf_dashboard_widget_dismiss']);
            }


            register_deactivation_hook( TF_PATH . 'tourfic.php', array($this, 'tf_promo_notice_deactivation_hook') );
        }

        
       
    }


    public function register_dashboard_notice_widget() {

        $dashboard_banner = isset($this->tf_promo_option['dashboard_widget'])
        ? $this->tf_promo_option['dashboard_widget']
        : [];

        $widget_title = !empty($dashboard_banner['title'])
        ? esc_html($dashboard_banner['title'])
        : __('Themefic Deals & Services', 'tourfic');


		wp_add_dashboard_widget(
			'tf_promo_dashboard_widget',
			$widget_title,
			[$this, 'render_dashboard_notice_widget'],
            null, null, 'side', 'high'
		);
	}

    public function render_dashboard_notice_widget() {
        $dashboard_widget = isset($this->tf_promo_option['dashboard_widget']) ? $this->tf_promo_option['dashboard_widget'] : [];

        if (empty($dashboard_widget) || empty($dashboard_widget['enable_status'])) {
            echo '<p>' . esc_html__('No active widget promotion.', 'tourfic') . '</p>';
            return;
        }

        $highlight = isset($dashboard_widget['highlight']) ? $dashboard_widget['highlight'] : [];
        $links     = isset($dashboard_widget['links']) ? $dashboard_widget['links'] : [];
        $footer    = isset($dashboard_widget['footer']) ? $dashboard_widget['footer'] : [];

        ?>
        <div class="tf-dashboard-widget" style="position:relative;">
            <?php if (!empty($dashboard_widget['dismiss_status'])) : ?>
                <button type="button" class="notice-dismiss tf-dashboard-dismiss" style="position:absolute; top:10px; right:10px;"></button>
            <?php endif; ?>

            <?php if (!empty($highlight)) : ?>
                <div class="highlight">
                    <?php if (!empty($highlight['before_image'])) : ?>
                        <img class="before-img" src="<?php echo esc_url($highlight['before_image']); ?>" style="max-width:100%; height:auto;" alt="">
                    <?php endif; ?>
                    <div class="content">
                        <?php if (!empty($highlight['title'])) : ?>
                        <p style="font-weight:600; margin:5px 0;"><?php echo esc_html($highlight['title']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($highlight['button_text']) && !empty($highlight['button_url'])) : ?>
                            <a href="<?php echo esc_url($highlight['button_url']); ?>" target="_blank" class="button button-primary"><?php echo esc_html($highlight['button_text']); ?></a>
                        <?php endif; ?>
                    </div>
                     <?php if (!empty($highlight['after_image'])) : ?>
                        <img class="after-img" src="<?php echo esc_url($highlight['after_image']); ?>" style="max-width:100%; height:auto;" alt="">
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($links)) : ?>
                <ul>
                    <?php foreach ($links as $link) : ?>
                        <li>
                            <a href="<?php echo esc_url($link['url']); ?>" target="_blank">
                                <?php if (!empty($link['tag'])) echo ' <span class="new-tag">' . esc_html($link['tag']) . '</span>'; ?>
                                <?php echo esc_html($link['text']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if (!empty($footer)) : ?>
                <div class="footer" style="display:flex; justify-content:space-between; margin-top:15px; font-size:13px;">
                    <?php if (!empty($footer['left'])) : ?>
                        <a href="<?php echo esc_url($footer['left']['url']); ?>" target="_blank"><?php echo esc_html($footer['left']['text']); ?>
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.5 0H6.66667V1.25H10.3667L5.39167 6.225L6.275 7.10833L11.25 2.13333V5.83333H12.5V0ZM1.66667 0.833333C1.22464 0.833333 0.800716 1.00893 0.488155 1.32149C0.175595 1.63405 0 2.05797 0 2.5V10.8333C0 11.2754 0.175595 11.6993 0.488155 12.0118C0.800716 12.3244 1.22464 12.5 1.66667 12.5H10C10.442 12.5 10.8659 12.3244 11.1785 12.0118C11.4911 11.6993 11.6667 11.2754 11.6667 10.8333V8.33333H10.4167V10.8333C10.4167 10.9438 10.3728 11.0498 10.2946 11.128C10.2165 11.2061 10.1105 11.25 10 11.25H1.66667C1.55616 11.25 1.45018 11.2061 1.37204 11.128C1.2939 11.0498 1.25 10.9438 1.25 10.8333V2.5C1.25 2.38949 1.2939 2.28351 1.37204 2.20537C1.45018 2.12723 1.55616 2.08333 1.66667 2.08333H4.16667V0.833333H1.66667Z" fill="#2271B1"/>
                        </svg>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($footer['right'])) : ?>
                        <a href="<?php echo esc_url($footer['right']['url']); ?>" target="_blank"><?php echo esc_html($footer['right']['text']); ?>
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.5 0H6.66667V1.25H10.3667L5.39167 6.225L6.275 7.10833L11.25 2.13333V5.83333H12.5V0ZM1.66667 0.833333C1.22464 0.833333 0.800716 1.00893 0.488155 1.32149C0.175595 1.63405 0 2.05797 0 2.5V10.8333C0 11.2754 0.175595 11.6993 0.488155 12.0118C0.800716 12.3244 1.22464 12.5 1.66667 12.5H10C10.442 12.5 10.8659 12.3244 11.1785 12.0118C11.4911 11.6993 11.6667 11.2754 11.6667 10.8333V8.33333H10.4167V10.8333C10.4167 10.9438 10.3728 11.0498 10.2946 11.128C10.2165 11.2061 10.1105 11.25 10 11.25H1.66667C1.55616 11.25 1.45018 11.2061 1.37204 11.128C1.2939 11.0498 1.25 10.9438 1.25 10.8333V2.5C1.25 2.38949 1.2939 2.28351 1.37204 2.20537C1.45018 2.12723 1.55616 2.08333 1.66667 2.08333H4.16667V0.833333H1.66667Z" fill="#2271B1"/>
                        </svg>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <style>
            .tf-dashboard-widget {
                background: #fff;
                border-radius: 4px;
                padding: 0;
                font-family: Arial, sans-serif;
                font-size: 13px;
                color: #23282d;
            }

            .tf-dashboard-widget .header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                background: #f8f9fa;
                padding: 14px;
                border-bottom: 1px solid #ddd;
            }

            .tf-dashboard-widget .highlight {
                display: flex;
                align-items: center;
                justify-content: space-between;
                background-color: #fff;
                border-bottom:1px solid #ccd0d4; 
                padding:12px 0px; 
                margin-bottom:8px; 
                text-align:left;
                gap: 10px;
                padding-top: 0px;
            }

            .tf-dashboard-widget .highlight .before-img {
                width: 58px;
                height: 58px;
                flex : 0 0 58px;
            }
            .tf-dashboard-widget .highlight .after-img {
                width: 100px;
                height: 60px;
            }
            .tf-dashboard-widget .highlight .content {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                flex-direction: column;
                flex: 1;
                width: 100%;
            }
            .tf-dashboard-widget .highlight .content p{
                color: #1D2327;
                font-family: "Roboto", sans-serif;
                font-size: 13px;
                font-weight: 500;
                line-height: 19.6px;
            }
            .tf-dashboard-widget .highlight .content .button{
                height: 30px;
                color: #FFF;
                font-family: "Roboto", sans-serif;
                font-size: 13px;
                font-weight: 500;
                border-radius: 3px;
                background: #2271B1;
            }

            .tf-dashboard-widget ul li a {
                color: #2271B1;
                font-family: "Roboto", sans-serif;
                font-size: 13px;
                font-weight: 400;
                line-height: 120%;
            }

            .tf-dashboard-widget .new-tag {
                padding: 3px 6px;
                border-radius: 3px;
                background-color: #0A875A;
                font-family: "Roboto", sans-serif;
                font-size: 10.5px;
                font-weight: 500;
                line-height: 12.6px;
                line-height: 12.6px;
                color: #fff;
                text-transform: uppercase;
            }

            .tf-dashboard-widget .footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-top: 1px solid #ddd;
                padding: 10px 0px;
                background: #fff;
            }

            .tf-dashboard-widget .footer a {
                text-decoration: none;
                font-weight: 500;
                color: #2271B1;
                font-family: "Roboto", sans-serif;
                font-size: 13px;
                font-weight: 400;
                line-height: 15.6px;
            }

            .tf-dashboard-widget .footer a svg {
                padding-left: 4px;
            }

        </style>

        <script>
        jQuery(document).ready(function($){
            $(document).on('click', '.tf-dashboard-dismiss', function(){
                $(this).closest('.tf-dashboard-widget').fadeOut(300);
                $.post(ajaxurl, { action: 'ins_dashboard_widget_dismissed' });
            });
        });
        </script>
        <?php
    }



    public function tf_dashboard_widget_dismiss() {
        // Dismiss control - 7 days
		update_option('ins_dashboard_widget_dismissed', time() + (86400 * 7));
		wp_die();
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
            'display' => esc_html__('Every 24 hours', 'tourfic')
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
                    color: #fff;
                }
                .tf_black_friday_20222_admin_notice button:hover::before {
                    color: #d63638;
                }
            </style>
            <div class="notice notice-success tf_black_friday_20222_admin_notice"> 
                <a href="<?php echo esc_attr( $deal_link ); ?>" style="display: block; line-height: 0;" target="_blank" >
                    <img  style="width: 100%;" src="<?php echo esc_attr($image_url) ?>" alt="">
                </a> 
                <?php if( isset($dashboard_banner['dismiss_status']) && $dashboard_banner['dismiss_status'] == true): ?>
                <button type="button" class="notice-dismiss tf_black_friday_notice_dismiss"><span class="screen-reader-text"><?php echo esc_html__('Dismiss this notice.', 'tourfic' ) ?></span></button>
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
        $restart = isset($tf_promo_option['dashboard_banner']['restart']) && $tf_promo_option['dashboard_banner']['restart'] != false ? $tf_promo_option['dashboard_banner']['restart'] : false; 
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

        $current_day = gmdate('l'); 
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
        delete_option('tf_promo_widget_exists');
    }
 
}
 

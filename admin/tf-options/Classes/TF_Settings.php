<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Settings' ) ) {
	class TF_Settings {

		public $option_id = null;
		public $option_title = null;
		public $option_icon = null;
		public $option_position = null;
		public $option_sections = array();

		public function __construct( $key, $params = array() ) {
			$this->option_id       = $key;
			$this->option_title    = ! empty( $params['title'] ) ? $params['title'] : '';
			$this->option_icon     = ! empty( $params['icon'] ) ? $params['icon'] : '';
			$this->option_position = ! empty( $params['position'] ) ? $params['position'] : 5;
			$this->option_sections = ! empty( $params['sections'] ) ? $params['sections'] : array();

			// run only is admin panel options, avoid performance loss
			$this->pre_tabs     = $this->pre_tabs( $this->option_sections );
			$this->pre_fields   = $this->pre_fields( $this->option_sections );
			$this->pre_sections = $this->pre_sections( $this->option_sections );

			//options
			add_action( 'admin_menu', array( $this, 'tf_options' ) );

			//save options
			add_action( 'admin_init', array( $this, 'save_options' ) );

			//ajax save options
			add_action( 'wp_ajax_tf_options_save', array( $this, 'tf_ajax_save_options' ) );
		}

		public static function option( $key, $params = array() ) {
			return new self( $key, $params );
		}

		public function pre_tabs( $sections ) {

			$result  = array();
			$parents = array();

			foreach ( $sections as $key => $section ) {
				if ( ! empty( $section['parent'] ) ) {
					$parents[ $section['parent'] ][ $key ] = $section;
					unset( $sections[ $key ] );
				}
			}

			foreach ( $sections as $key => $section ) {
				if ( ! empty( $key ) && ! empty( $parents[ $key ] ) ) {
					$section['sub_section'] = $parents[ $key ];
				}
				$result[ $key ] = $section;
			}

			return $result;
		}

		public function pre_fields( $sections ) {

			$result = array();

			foreach ( $sections as $key => $section ) {
				if ( ! empty( $section['fields'] ) ) {
					foreach ( $section['fields'] as $field ) {
						$result[] = $field;
					}
				}
			}

			return $result;
		}

		public function pre_sections( $sections ) {

			$result = array();

			foreach ( $this->pre_tabs as $tab ) {
				if ( ! empty( $tab['subs'] ) ) {
					foreach ( $tab['subs'] as $sub ) {
						$sub['ptitle'] = $tab['title'];
						$result[]      = $sub;
					}
				}
				if ( empty( $tab['subs'] ) ) {
					$result[] = $tab;
				}
			}

			return $result;
		}

		/**
		 * Options Page menu
		 * @author Foysal
		 */
		public function tf_options() {
			add_menu_page(
				$this->option_title,
				$this->option_title,
				'manage_options',
				$this->option_id,
				array( $this, 'tf_options_page' ),
				$this->option_icon,
				$this->option_position
			);

            //Dashboard submenu
			add_submenu_page(
				$this->option_id,
				__('Dashboard', 'tourfic'),
				__('Dashboard', 'tourfic'),
				'manage_options',
				$this->option_id . '&dashboard=1',
				'__return_null',
			);

			//sections as submenus
			if ( ! empty( $this->pre_tabs ) ) {
				foreach ( $this->pre_tabs as $key => $section ) {
					$parent_tab_key = ! empty( $section['fields'] ) ? $key : array_key_first( $section['sub_section'] );
					add_submenu_page(
						$this->option_id,
						$section['title'],
						$section['title'],
						'manage_options',
						$this->option_id . '#tab=' . esc_attr( $parent_tab_key ),
						'__return_null',
					);
				}
            }

			// remove first submenu
			remove_submenu_page( $this->option_id, $this->option_id );

		}

		/**
		 * Options Page HTML
		 * @author Jahid, Foysal
		 */
		public function tf_dashboard_page() {
            $current_page_url = $this->get_current_page_url();
            $query_string = $this->get_query_string($current_page_url);

			?>
			<div class="tf-setting-dashboard">
				<!-- deshboard-top-section -->
				<div class="tf-setting-top-bar">
                    <div class="version">
						<img src="<?php echo TF_ASSETS_URL; ?>/img/tourfic-logo.webp" alt="logo">
						<span>v<?php echo esc_attr( TOURFIC ); ?></span>
					</div>
					<div class="other-document">
						<svg width="26" height="25" viewBox="0 0 26 25" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #003c79;background: ;">
							<path d="M19.2106 0H6.57897C2.7895 0 0.263184 2.52632 0.263184 6.31579V13.8947C0.263184 17.6842 2.7895 20.2105 6.57897 20.2105V22.9011C6.57897 23.9116 7.70318 24.5179 8.53687 23.9495L14.1579 20.2105H19.2106C23 20.2105 25.5263 17.6842 25.5263 13.8947V6.31579C25.5263 2.52632 23 0 19.2106 0ZM12.8948 15.3726C12.3642 15.3726 11.9474 14.9432 11.9474 14.4253C11.9474 13.9074 12.3642 13.4779 12.8948 13.4779C13.4253 13.4779 13.8421 13.9074 13.8421 14.4253C13.8421 14.9432 13.4253 15.3726 12.8948 15.3726ZM14.4863 10.1305C13.9937 10.4589 13.8421 10.6737 13.8421 11.0274V11.2926C13.8421 11.8105 13.4127 12.24 12.8948 12.24C12.3769 12.24 11.9474 11.8105 11.9474 11.2926V11.0274C11.9474 9.56211 13.0211 8.84211 13.4253 8.56421C13.8927 8.24842 14.0442 8.03368 14.0442 7.70526C14.0442 7.07368 13.5263 6.55579 12.8948 6.55579C12.2632 6.55579 11.7453 7.07368 11.7453 7.70526C11.7453 8.22316 11.3158 8.65263 10.7979 8.65263C10.28 8.65263 9.85055 8.22316 9.85055 7.70526C9.85055 6.02526 11.2148 4.66105 12.8948 4.66105C14.5748 4.66105 15.939 6.02526 15.939 7.70526C15.939 9.14526 14.8779 9.86526 14.4863 10.1305Z" fill="#003c79"></path>
						</svg>

						<div class="dropdown">
							<div class="list-item">
								<a href="#" target="_blank">
									<svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M10.0482 4.37109H4.30125C4.06778 4.37109 3.84329 4.38008 3.62778 4.40704C1.21225 4.6137 0 6.04238 0 8.6751V12.2693C0 15.8634 1.43674 16.5733 4.30125 16.5733H4.66044C4.85799 16.5733 5.1184 16.708 5.23514 16.8608L6.3127 18.2985C6.78862 18.9364 7.56087 18.9364 8.03679 18.2985L9.11435 16.8608C9.24904 16.6811 9.46456 16.5733 9.68905 16.5733H10.0482C12.6793 16.5733 14.107 15.3692 14.3136 12.9432C14.3405 12.7275 14.3495 12.5029 14.3495 12.2693V8.6751C14.3495 5.80876 12.9127 4.37109 10.0482 4.37109ZM4.04084 11.5594C3.53798 11.5594 3.14288 11.1551 3.14288 10.6609C3.14288 10.1667 3.54696 9.76233 4.04084 9.76233C4.53473 9.76233 4.93881 10.1667 4.93881 10.6609C4.93881 11.1551 4.53473 11.5594 4.04084 11.5594ZM7.17474 11.5594C6.67188 11.5594 6.27678 11.1551 6.27678 10.6609C6.27678 10.1667 6.68086 9.76233 7.17474 9.76233C7.66862 9.76233 8.07271 10.1667 8.07271 10.6609C8.07271 11.1551 7.6776 11.5594 7.17474 11.5594ZM10.3176 11.5594C9.81476 11.5594 9.41966 11.1551 9.41966 10.6609C9.41966 10.1667 9.82374 9.76233 10.3176 9.76233C10.8115 9.76233 11.2156 10.1667 11.2156 10.6609C11.2156 11.1551 10.8115 11.5594 10.3176 11.5594Z" fill="#003c79"></path>
										<path d="M17.9423 5.08086V8.67502C17.9423 10.4721 17.3855 11.6941 16.272 12.368C16.0026 12.5298 15.6884 12.3141 15.6884 11.9996L15.6973 8.67502C15.6973 5.08086 13.641 3.0232 10.0491 3.0232L4.58048 3.03219C4.26619 3.03219 4.05067 2.7177 4.21231 2.44814C4.88578 1.33395 6.10702 0.776855 7.89398 0.776855H13.641C16.5055 0.776855 17.9423 2.21452 17.9423 5.08086Z" fill="#003c79"></path>
									</svg> 
								<span><?php _e("Need Help?","tourfic"); ?></span>
								</a>
								<a href="#" target="_blank">
									<svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M16.1896 7.57803H13.5902C11.4586 7.57803 9.72274 5.84103 9.72274 3.70803V1.10703C9.72274 0.612031 9.318 0.207031 8.82332 0.207031H5.00977C2.23956 0.207031 0 2.00703 0 5.22003V13.194C0 16.407 2.23956 18.207 5.00977 18.207H12.0792C14.8494 18.207 17.089 16.407 17.089 13.194V8.47803C17.089 7.98303 16.6843 7.57803 16.1896 7.57803ZM8.09478 14.382H4.4971C4.12834 14.382 3.82254 14.076 3.82254 13.707C3.82254 13.338 4.12834 13.032 4.4971 13.032H8.09478C8.46355 13.032 8.76935 13.338 8.76935 13.707C8.76935 14.076 8.46355 14.382 8.09478 14.382ZM9.89363 10.782H4.4971C4.12834 10.782 3.82254 10.476 3.82254 10.107C3.82254 9.73803 4.12834 9.43203 4.4971 9.43203H9.89363C10.2624 9.43203 10.5682 9.73803 10.5682 10.107C10.5682 10.476 10.2624 10.782 9.89363 10.782Z" fill="#003c79"></path>
									</svg>
									<span><?php _e("Documentation","tourfic"); ?></span>

								</a>
								<a href="#" target="_blank">
									<svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" clip-rule="evenodd" d="M13.5902 7.57803H16.1896C16.6843 7.57803 17.089 7.98303 17.089 8.47803V13.194C17.089 16.407 14.8494 18.207 12.0792 18.207H5.00977C2.23956 18.207 0 16.407 0 13.194V5.22003C0 2.00703 2.23956 0.207031 5.00977 0.207031H8.82332C9.318 0.207031 9.72274 0.612031 9.72274 1.10703V3.70803C9.72274 5.84103 11.4586 7.57803 13.5902 7.57803ZM11.9613 0.396012C11.5926 0.0270125 10.954 0.279013 10.954 0.792013V3.93301C10.954 5.24701 12.0693 6.33601 13.4274 6.33601C14.2818 6.34501 15.4689 6.34501 16.4852 6.34501H16.4854C16.998 6.34501 17.2679 5.74201 16.9081 5.38201C16.4894 4.96018 15.9637 4.42927 15.3988 3.85888L15.3932 3.85325L15.3913 3.85133L15.3905 3.8505L15.3902 3.85016C14.2096 2.65803 12.86 1.29526 11.9613 0.396012ZM3.0145 12.0732C3.0145 11.7456 3.28007 11.48 3.60768 11.48H5.32132V9.76639C5.32132 9.43879 5.58689 9.17321 5.9145 9.17321C6.2421 9.17321 6.50768 9.43879 6.50768 9.76639V11.48H8.22131C8.54892 11.48 8.8145 11.7456 8.8145 12.0732C8.8145 12.4008 8.54892 12.6664 8.22131 12.6664H6.50768V14.38C6.50768 14.7076 6.2421 14.9732 5.9145 14.9732C5.58689 14.9732 5.32132 14.7076 5.32132 14.38V12.6664H3.60768C3.28007 12.6664 3.0145 12.4008 3.0145 12.0732Z" fill="#003c79"></path>
									</svg>
									<span><?php _e("Feature Request","tourfic"); ?></span>
								</a>
							</div>
						</div>
					</div>
                </div>
				<!-- deshboard-top-section -->


				<!-- deshboard-banner-section -->
				<div class="tf-setting-banner">
					<div class="tf-setting-banner-content">
						<img src="<?php echo TF_ASSETS_URL; ?>/img/tourfic-logo.webp" alt="logo">
						<span>Express your <b>Hotel and Tour Booking Website</b>with Tourfic</span>
					</div>
					<div class="tf-setting-banner-image">
						<img src="<?php echo TF_ASSETS_URL; ?>/img/hotel-booking-management-system@2x.webp" alt="Banner Image">
					</div>
				</div>
				<!-- deshboard-banner-section -->

				<!-- deshboar-performance-section -->

				<div class="tf-setting-performace-section">
					<h2><?php _e("Performance","tourfic"); ?></h2>
					<div class="tf-performance-grid">
						<div class="tf-single-performance-grid">
							<div class="tf-single-performance-icon">
							<svg width="70" height="70" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle opacity="0.15" cx="35" cy="35" r="35" fill="#24A4EC"></circle>
								<path d="M41.5614 24.1171V22.0121C41.5614 21.4588 41.1024 21 40.5489 21C39.9954 21 39.5365 21.4588 39.5365 22.0121V24.0362H30.7619V22.0121C30.7619 21.4588 30.3029 21 29.7495 21C29.196 21 28.737 21.4588 28.737 22.0121V24.1171C25.0922 24.4545 23.3238 26.627 23.0538 29.8521C23.0268 30.2435 23.3508 30.5673 23.7288 30.5673H46.5696C46.9611 30.5673 47.2851 30.23 47.2446 29.8521C46.9746 26.627 45.2062 24.4545 41.5614 24.1171Z" fill="#24A4EC"></path>
								<path d="M45.9489 32.5913H24.3499C23.6075 32.5913 23 33.1985 23 33.9407V42.2531C23 46.3014 25.0249 49.0002 29.7497 49.0002H40.5491C45.2739 49.0002 47.2988 46.3014 47.2988 42.2531V33.9407C47.2988 33.1985 46.6913 32.5913 45.9489 32.5913ZM31.3831 43.8859C31.2481 44.0073 31.0996 44.1018 30.9376 44.1693C30.7756 44.2368 30.6001 44.2772 30.4246 44.2772C30.2491 44.2772 30.0737 44.2368 29.9117 44.1693C29.7497 44.1018 29.6012 44.0073 29.4662 43.8859C29.2232 43.6295 29.0747 43.2787 29.0747 42.9278C29.0747 42.577 29.2232 42.2261 29.4662 41.9697C29.6012 41.8483 29.7497 41.7538 29.9117 41.6864C30.2356 41.5514 30.6136 41.5514 30.9376 41.6864C31.0996 41.7538 31.2481 41.8483 31.3831 41.9697C31.6261 42.2261 31.7746 42.577 31.7746 42.9278C31.7746 43.2787 31.6261 43.6295 31.3831 43.8859ZM31.6666 38.7176C31.5991 38.8796 31.5046 39.028 31.3831 39.163C31.2481 39.2844 31.0996 39.3789 30.9376 39.4463C30.7756 39.5138 30.6001 39.5543 30.4246 39.5543C30.2491 39.5543 30.0737 39.5138 29.9117 39.4463C29.7497 39.3789 29.6012 39.2844 29.4662 39.163C29.3447 39.028 29.2502 38.8796 29.1827 38.7176C29.1152 38.5557 29.0747 38.3803 29.0747 38.2049C29.0747 38.0294 29.1152 37.854 29.1827 37.6921C29.2502 37.5302 29.3447 37.3817 29.4662 37.2468C29.6012 37.1253 29.7497 37.0309 29.9117 36.9634C30.2356 36.8285 30.6136 36.8285 30.9376 36.9634C31.0996 37.0309 31.2481 37.1253 31.3831 37.2468C31.5046 37.3817 31.5991 37.5302 31.6666 37.6921C31.7341 37.854 31.7746 38.0294 31.7746 38.2049C31.7746 38.3803 31.7341 38.5557 31.6666 38.7176ZM36.1079 39.163C35.9729 39.2844 35.8244 39.3789 35.6624 39.4463C35.5004 39.5138 35.3249 39.5543 35.1494 39.5543C34.9739 39.5543 34.7984 39.5138 34.6364 39.4463C34.4744 39.3789 34.3259 39.2844 34.191 39.163C33.948 38.9066 33.7995 38.5557 33.7995 38.2049C33.7995 37.854 33.948 37.5032 34.191 37.2468C34.3259 37.1253 34.4744 37.0309 34.6364 36.9634C34.9604 36.815 35.3384 36.815 35.6624 36.9634C35.8244 37.0309 35.9729 37.1253 36.1079 37.2468C36.3509 37.5032 36.4993 37.854 36.4993 38.2049C36.4993 38.5557 36.3509 38.9066 36.1079 39.163Z" fill="#24A4EC"></path>
							</svg>
							</div>
							<div class="tf-single-performance-content">
								<p><?php _e("Reservations","tourfic"); ?></p>
								<h3>0</h3>
							</div>
						</div>
						
						<div class="tf-single-performance-grid">
							<div class="tf-single-performance-icon">
							<svg width="70" height="70" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle opacity="0.17" cx="35" cy="35" r="35" fill="#FF6B00"></circle>
								<path d="M33.9497 29.5122V33.5162L32.5357 33.0262C31.8217 32.7742 31.3877 32.5362 31.3877 31.3182C31.3877 30.3242 32.1297 29.5122 33.0397 29.5122H33.9497Z" fill="#F07B27"></path>
								<path d="M38.6118 38.6824C38.6118 39.6764 37.8698 40.4884 36.9598 40.4884H36.0498V36.4844L37.4638 36.9744C38.1778 37.2264 38.6118 37.4644 38.6118 38.6824Z" fill="#F07B27"></path>
								<path d="M40.866 21H29.134C24.038 21 21 24.038 21 29.134V40.866C21 45.962 24.038 49 29.134 49H40.866C45.962 49 49 45.962 49 40.866V29.134C49 24.038 45.962 21 40.866 21ZM38.164 35C39.256 35.378 40.712 36.176 40.712 38.682C40.712 40.838 39.032 42.588 36.96 42.588H36.05V43.4C36.05 43.974 35.574 44.45 35 44.45C34.426 44.45 33.95 43.974 33.95 43.4V42.588H33.446C31.15 42.588 29.288 40.642 29.288 38.262C29.288 37.688 29.75 37.212 30.338 37.212C30.912 37.212 31.388 37.688 31.388 38.262C31.388 39.494 32.312 40.488 33.446 40.488H33.95V35.742L31.836 35C30.744 34.622 29.288 33.824 29.288 31.318C29.288 29.162 30.968 27.412 33.04 27.412H33.95V26.6C33.95 26.026 34.426 25.55 35 25.55C35.574 25.55 36.05 26.026 36.05 26.6V27.412H36.554C38.85 27.412 40.712 29.358 40.712 31.738C40.712 32.312 40.25 32.788 39.662 32.788C39.088 32.788 38.612 32.312 38.612 31.738C38.612 30.506 37.688 29.512 36.554 29.512H36.05V34.258L38.164 35Z" fill="#F07B27"></path>
							</svg>
							</div>
							<div class="tf-single-performance-content">
								<p><?php _e("Total Sales","tourfic"); ?></p>
								<h3>0</h3>
							</div>
						</div>
						
						<div class="tf-single-performance-grid">
							<div class="tf-single-performance-icon">
							<svg width="70" height="70" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle opacity="0.15" cx="35" cy="35" r="35" fill="#01AF28"></circle>
								<path d="M35.9497 27.5122V31.5162L34.5357 31.0262C33.8217 30.7742 33.3877 30.5362 33.3877 29.3182C33.3877 28.3242 34.1297 27.5122 35.0397 27.5122H35.9497Z" fill="#1DBE41"></path>
								<path d="M40.6118 36.6824C40.6118 37.6764 39.8698 38.4884 38.9598 38.4884H38.0498V34.4844L39.4638 34.9744C40.1778 35.2264 40.6118 35.4644 40.6118 36.6824Z" fill="#1DBE41"></path>
								<path fill-rule="evenodd" clip-rule="evenodd" d="M31.134 19H42.866C47.962 19 51 22.038 51 27.134V38.866C51 43.962 47.962 47 42.866 47H31.9451C31.4477 42.5003 27.6329 39.0004 23.0007 39C23.0002 38.9555 23 38.9108 23 38.866V27.134C23 22.038 26.038 19 31.134 19ZM42.712 36.682C42.712 34.176 41.256 33.378 40.164 33L38.05 32.258V27.512H38.554C39.688 27.512 40.612 28.506 40.612 29.738C40.612 30.312 41.088 30.788 41.662 30.788C42.25 30.788 42.712 30.312 42.712 29.738C42.712 27.358 40.85 25.412 38.554 25.412H38.05V24.6C38.05 24.026 37.574 23.55 37 23.55C36.426 23.55 35.95 24.026 35.95 24.6V25.412H35.04C32.968 25.412 31.288 27.162 31.288 29.318C31.288 31.824 32.744 32.622 33.836 33L35.95 33.742V38.488H35.446C34.312 38.488 33.388 37.494 33.388 36.262C33.388 35.688 32.912 35.212 32.338 35.212C31.75 35.212 31.288 35.688 31.288 36.262C31.288 38.642 33.15 40.588 35.446 40.588H35.95V41.4C35.95 41.974 36.426 42.45 37 42.45C37.574 42.45 38.05 41.974 38.05 41.4V40.588H38.96C41.032 40.588 42.712 38.838 42.712 36.682Z" fill="#1DBE41"></path>
								<path d="M23 41C21.215 41 19.57 41.6825 18.3275 42.8025C18.0475 43.0475 17.785 43.31 17.5575 43.6075C16.5775 44.815 16 46.3375 16 48C16 51.8675 19.15 55 23 55C24.68 55 26.2025 54.405 27.41 53.4425C28.39 52.62 29.16 51.5525 29.58 50.3275C29.86 49.61 30 48.8225 30 48C30 44.15 26.8675 41 23 41ZM27.1475 51.0625C27.095 51.15 27.025 51.255 26.955 51.325L25.695 52.5675C25.5375 52.7425 25.31 52.83 25.1 52.83C24.8725 52.83 24.645 52.76 24.4875 52.585C24.2075 52.3225 24.1725 51.9025 24.3475 51.5875H20.83C19.71 51.5875 18.8 50.6775 18.8 49.54V49.365C18.8 48.875 19.185 48.5075 19.6575 48.5075C20.13 48.5075 20.515 48.875 20.515 49.365V49.54C20.515 49.7325 20.655 49.89 20.8475 49.89H24.365C24.19 49.5575 24.225 49.155 24.505 48.875C24.68 48.7175 24.89 48.63 25.1 48.63C25.31 48.63 25.5375 48.7175 25.695 48.875L26.955 50.135C27.025 50.205 27.095 50.31 27.1475 50.415C27.2175 50.625 27.2175 50.8525 27.1475 51.0625ZM26.3425 47.51C25.87 47.51 25.485 47.1425 25.485 46.6525V46.4775C25.485 46.285 25.345 46.1275 25.1525 46.1275H21.6525C21.8275 46.46 21.7925 46.8625 21.5125 47.1425C21.355 47.3 21.145 47.3875 20.9 47.3875C20.69 47.3875 20.4625 47.3 20.305 47.1425L19.045 45.8825C18.975 45.8125 18.905 45.7075 18.8525 45.6025C18.7825 45.3925 18.7825 45.165 18.8525 44.955C18.905 44.8675 18.9575 44.745 19.045 44.675L20.305 43.4325C20.4625 43.2575 20.69 43.17 20.9 43.17C21.11 43.17 21.3375 43.2575 21.495 43.4325C21.775 43.695 21.81 44.115 21.635 44.43H25.1525C26.2725 44.43 27.1825 45.34 27.1825 46.4775V46.6525H27.2C27.2 47.1425 26.815 47.51 26.3425 47.51Z" fill="#1DBE41"></path>
							</svg>
							</div>
							<div class="tf-single-performance-content">
								<p><?php _e("Refunds","tourfic"); ?></p>
								<h3>0</h3>
							</div>
						</div>
						<div class="tf-single-performance-grid">
							<div class="tf-single-performance-icon">
							<svg width="70" height="70" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle opacity="0.15" cx="35" cy="35" r="35" fill="#AD00FF"></circle>
								<path d="M40.92 22.3335H29.7467C24.8933 22.3335 22 25.2268 22 30.0802V41.2535C22 45.0002 23.72 47.5735 26.7467 48.5468C27.6267 48.8535 28.64 49.0002 29.7467 49.0002H40.92C42.0267 49.0002 43.04 48.8535 43.92 48.5468C46.9467 47.5735 48.6667 45.0002 48.6667 41.2535V30.0802C48.6667 25.2268 45.7733 22.3335 40.92 22.3335ZM46.6667 41.2535C46.6667 44.1068 45.5467 45.9068 43.2933 46.6535C42 44.1068 38.9333 42.2935 35.3333 42.2935C31.7333 42.2935 28.68 44.0935 27.3733 46.6535H27.36C25.1333 45.9335 24 44.1202 24 41.2668V30.0802C24 26.3202 25.9867 24.3335 29.7467 24.3335H40.92C44.68 24.3335 46.6667 26.3202 46.6667 30.0802V41.2535Z" fill="#AB2BE7"></path>
								<path d="M35.3358 30.3335C32.6958 30.3335 30.5625 32.4668 30.5625 35.1068C30.5625 37.7468 32.6958 39.8935 35.3358 39.8935C37.9758 39.8935 40.1092 37.7468 40.1092 35.1068C40.1092 32.4668 37.9758 30.3335 35.3358 30.3335Z" fill="#AB2BE7"></path>
								<circle cx="46" cy="26.3335" r="4" fill="white"></circle>
								<path fill-rule="evenodd" clip-rule="evenodd" d="M45.9993 31.6667C48.9449 31.6667 51.3327 29.2789 51.3327 26.3333C51.3327 23.3878 48.9449 21 45.9993 21C43.0538 21 40.666 23.3878 40.666 26.3333C40.666 29.2789 43.0538 31.6667 45.9993 31.6667ZM45.0098 24.3442C45.0098 23.792 45.4575 23.3442 46.0098 23.3442C46.5621 23.3442 47.0098 23.792 47.0098 24.3442V25.3442H48.0098C48.5621 25.3442 49.0098 25.792 49.0098 26.3442C49.0098 26.8965 48.5621 27.3442 48.0098 27.3442H47.0098V28.3442C47.0098 28.8965 46.5621 29.3442 46.0098 29.3442C45.4575 29.3442 45.0098 28.8965 45.0098 28.3442V27.3442H44.0098C43.4575 27.3442 43.0098 26.8965 43.0098 26.3442C43.0098 25.792 43.4575 25.3442 44.0098 25.3442H45.0098V24.3442Z" fill="#AB2BE7"></path>
							</svg>
							</div>
							<div class="tf-single-performance-content">
								<p><?php _e("New Customers","tourfic"); ?></p>
								<h3>0</h3>
							</div>
						</div>
					</div>
				</div>

				<!-- deshboar-performance-section -->
			</div>
            <div class="tf-deshboard-wrapper" style="display: <?php echo isset($query_string['dashboard']) ? 'block' : 'none' ?>">
                <div class="tf-deshboard-version">
                    <span><?php _e( "Tourfic", "tourfic" ); ?><div class="version"><?php echo esc_attr( TOURFIC ); ?></div></span>
                </div>
                <div class="tf-deshboard-overview">
                    <div class="tf-details-overview">
						<span class="tf-details-overview-title">
							<?php _e( "Hotels Overview", "tourfic" ); ?>
						</span>
                        <div class="tf-details-overview-items">
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-hotel"></i></span><?php _e( "Hotels", "tourfic" ); ?></h3>
                                <span>
									<?php
									$tf_total_hotels = array(
										'post_type'      => 'tf_hotel',
										'post_status'    => 'publish',
										'posts_per_page' => - 1
									);
									echo count( get_posts ($tf_total_hotels ) );
									?>
								</span>
                                <a href="<?php echo get_admin_url() . 'edit.php?post_type=tf_hotel'; ?>"><?php _e( "View All", "tourfic" ); ?></a>
                            </div>
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-door-closed"></i></span><?php _e( "Features", "tourfic" ); ?></h3>
                                <span>
									<?php
									$tf_total_features = get_terms( [
										'taxonomy'   => 'hotel_feature',
										'hide_empty' => false,
									] );
									echo count( $tf_total_features );
									?>
								</span>
                                <a href="<?php echo get_admin_url() . 'edit-tags.php?taxonomy=hotel_feature&post_type=tf_hotel'; ?>"><?php _e( "View All", "tourfic" ); ?></a>
                            </div>
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-door-open"></i></span><?php _e( "Location", "tourfic" ); ?></h3>
                                <span>
									<?php
									$tf_total_locations = get_terms( [
										'taxonomy'   => 'hotel_location',
										'hide_empty' => false,
									] );
									echo count( $tf_total_locations );
									?>
								</span>
                                <a href="<?php echo get_admin_url() . 'edit-tags.php?taxonomy=hotel_location&post_type=tf_hotel'; ?>"><?php _e( "View All", "tourfic" ); ?></a>
                            </div>
                        </div>


                        <span class="tf-details-overview-title">
							<?php _e( "Tours Overview", "tourfic" ); ?>
						</span>
                        <div class="tf-details-overview-items">
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-hiking"></i></span><?php _e( "Tours", "tourfic" ); ?></h3>
                                <span>
									<?php
									$tf_total_tours = array(
										'post_type'      => 'tf_tours',
										'post_status'    => 'publish',
										'posts_per_page' => - 1
									);
									echo count( get_posts ($tf_total_tours ));
									?>
								</span>
                                <a href="<?php echo get_admin_url() . 'edit.php?post_type=tf_tours'; ?>"><?php _e( "View All", "tourfic" ); ?></a>
                            </div>
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-map-marker"></i></span><?php _e( "Destinations", "tourfic" ); ?></h3>
                                <span>
									<?php
									$tf_total_destinations = get_terms( [
										'taxonomy'   => 'tour_destination',
										'hide_empty' => false,
									] );
									echo count( $tf_total_destinations );
									?>
								</span>
                                <a href="<?php echo get_admin_url() . 'edit-tags.php?taxonomy=tour_destination&post_type=tf_tours'; ?>"><?php _e( "View All", "tourfic" ); ?></a>
                            </div>
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-heartbeat"></i></span><?php _e( "Activities", "tourfic" ); ?><p><?php _e( "Upcoming", "tourfic" ); ?></p></h3>
                                <span><?php echo esc_html( "0" ); ?></span>
                            </div>
                        </div>


                        <span class="tf-details-overview-title">
							<?php _e( "Common Overview", "tourfic" ); ?>
						</span>
                        <div class="tf-details-overview-items">
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-book"></i></span><?php _e( "Bookings", "tourfic" ); ?></h3>
                                <span>
									<?php
									$tf_order_query_orders = wc_get_orders( array(
											'limit'  => - 1,
											'type'   => 'shop_order',
											'status' => array( 'wc-completed' ),
										)
									);
									echo count( $tf_order_query_orders );
									?>
								</span>
                                <a href="<?php echo get_admin_url() . 'edit.php?post_status=wc-completed&post_type=shop_order'; ?>"><?php _e( "View All", "tourfic" ); ?></a>
                            </div>
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-user-alt"></i></span><?php _e( "Customers", "tourfic" ); ?></h3>
                                <span>
									<?php
									$tf_customer_query = new WP_User_Query(
										array(
											'role' => 'customer',
										)
									);
									echo count( $tf_customer_query->get_results() );
									?>
								</span>
                                <a href="<?php echo get_admin_url() . 'users.php?role=customer'; ?>"><?php _e( "View All", "tourfic" ); ?></a>
                            </div>
                            <div class="tf-details-single-items">
                                <h3><span><i class="fa-solid fa-question"></i></span><?php _e( "Enquiries", "tourfic" ); ?><p><?php _e( "Upcoming", "tourfic" ); ?></p></h3>
                                <span><?php echo esc_html( "0" ); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="tf-details-instractions">
						<div class="tf-video-instractions">
							<span class="tf-details-overview-title">
								<?php _e( "Video Instruction", "tourfic" ); ?>
							</span>
							<iframe width="100%" height="300" src="https://www.youtube.com/embed/xeVkabWobDU" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
						</div>

						<div class="tf-community-info">
							<span class="tf-details-overview-title">
								<?php _e( "Facebook Community", "tourfic" ); ?>
							</span>
							<div class="tf-facebook-community">
								<div class="icon">
									<i class="fab fa-facebook-f"></i>
								</div>
								<span><?php echo sprintf( "Join our <a target='_blank' href='https://www.facebook.com/groups/tourfic'>Tourfic - Travel Booking Solution for Woocommerce Community </a> Facebook Group for your query or share your thoughts about the plugin with user and us." ); ?></span>
							</div>
							<span class="tf-details-overview-title">
								<?php _e( "Plugin Documentation", "tourfic" ); ?>
							</span>
							<div class="tf-plugin-documentation">
								<div class="tf-plugin-details-info">
									<i class="fa-solid fa-file-alt"></i>
									<span><?php _e( "You’ll get every detailed document regarding the plugin in our documentation website described by our Engineers.", "tourfic" ); ?></span>
								</div>
								<div class="tf-plugin-document-link">
									<?php echo sprintf( "<a target='_blank' href='https://themefic.com/docs/tourfic/'>Visit Documentation</a>" ); ?>
								</div>
							</div>
						</div>
                    </div>
                </div>
            </div>
			<?php
		}

		/**
		 * Options Page
		 * @author Foysal
		 */
		public function tf_options_page() {

			// Retrieve an existing value from the database.
			$tf_option_value = get_option( $this->option_id );
			$current_page_url = $this->get_current_page_url();
			$query_string = $this->get_query_string($current_page_url);

			// Set default values.
			if ( empty( $tf_option_value ) ) {
				$tf_option_value = array();
			}

			$this->tf_dashboard_page();

            $ajax_save_class = 'tf-ajax-save';

			if ( ! empty( $this->option_sections ) ) :
				?>
                <div class="tf-option-wrapper" style="display: <?php echo !isset($query_string['dashboard']) ? 'block' : 'none' ?>">
                    <form method="post" action="" class="tf-option-form <?php echo esc_attr($ajax_save_class) ?>" enctype="multipart/form-data">
                        <!-- Header -->
                        <div class="tf-option-header">
                            <div class="tf-option-header-left">
                                <h2>
								<img src="<?php echo TF_ASSETS_URL; ?>img/tourfic-logo.webp" alt="Tourfic">
								<a href="#" class="tf-mobile-tabs"><i class="fa-solid fa-bars"></i></a>
								</h2>
                            </div>
                            <div class="tf-option-header-right">
                                <div class="tf-option-header-actions">
                                    <button type="submit" class="tf-admin-btn tf-btn-secondary tf-submit-btn"><?php esc_attr_e( 'Save', 'tourfic' ); ?></button>
                                </div>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="tf-option">
                            <div class="tf-admin-tab tf-option-nav">
								<?php
								$section_count = 0;
								foreach ( $this->pre_tabs as $key => $section ) :
									$parent_tab_key = ! empty( $section['fields'] ) ? $key : array_key_first( $section['sub_section'] );
									?>
                                    <div class="tf-admin-tab-item<?php echo ! empty( $section['sub_section'] ) ? ' tf-has-submenu' : '' ?>">
                                        <a href="#<?php echo esc_attr( $parent_tab_key ); ?>"
                                           class="tf-tablinks <?php echo $section_count == 0 ? 'active' : ''; ?>"
                                           data-tab="<?php echo esc_attr( $parent_tab_key ) ?>">
											<?php echo ! empty( $section['icon'] ) ? '<span class="tf-sec-icon"><i class="' . esc_attr( $section['icon'] ) . '"></i></span>' : ''; ?>
											<?php echo $section['title']; ?>
                                        </a>

										<?php if ( ! empty( $section['sub_section'] ) ): ?>
                                            <ul class="tf-submenu">
												<?php foreach ( $section['sub_section'] as $sub_key => $sub ): ?>
                                                    <li>
                                                        <a href="#<?php echo esc_attr( $sub_key ); ?>"
                                                           class="tf-tablinks <?php echo $section_count == 0 ? 'active' : ''; ?>"
                                                           data-tab="<?php echo esc_attr( $sub_key ) ?>">
														<span class="tf-tablinks-inner">
                                                            <?php echo ! empty( $sub['icon'] ) ? '<span class="tf-sec-icon"><i class="' . esc_attr( $sub['icon'] ) . '"></i></span>' : ''; ?>
                                                            <?php echo $sub['title']; ?>
                                                        </span>
                                                        </a>
                                                    </li>
												<?php endforeach; ?>
                                            </ul>
										<?php endif; ?>
                                    </div>
									<?php $section_count ++; endforeach; ?>
                            </div>

                            <div class="tf-tab-wrapper">
								<?php
								$content_count = 0;
								foreach ( $this->option_sections as $key => $section ) : ?>
                                    <div id="<?php echo esc_attr( $key ) ?>" class="tf-tab-content <?php echo $content_count == 0 ? 'active' : ''; ?>">

										<?php
										if ( ! empty( $section['fields'] ) ):
											foreach ( $section['fields'] as $field ) :

												$default = isset( $field['default'] ) ? $field['default'] : '';
												$value   = isset( $tf_option_value[ $field['id'] ] ) ? $tf_option_value[ $field['id'] ] : $default;

												$tf_option = new TF_Options();
												$tf_option->field( $field, $value, $this->option_id );
											endforeach;
										endif; ?>

                                    </div>
									<?php $content_count ++; endforeach; ?>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="tf-option-footer">
                            <button type="submit" class="tf-admin-btn tf-btn-secondary tf-submit-btn"><?php _e( 'Save', 'tourfic' ); ?></button>
                        </div>

						<?php wp_nonce_field( 'tf_option_nonce_action', 'tf_option_nonce' ); ?>
                    </form>
                </div>
			<?php
			endif;
		}

		/**
		 * Save Options
		 * @author Foysal
		 */
		public function save_options() {

			// Add nonce for security and authentication.
			$nonce_name   = isset( $_POST['tf_option_nonce'] ) ? $_POST['tf_option_nonce'] : '';
			$nonce_action = 'tf_option_nonce_action';

			// Check if a nonce is set.
			if ( ! isset( $nonce_name ) ) {
				return;
			}

			// Check if a nonce is valid.
			if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
				return;
			}

			$tf_option_value = array();
			$option_request  = ( ! empty( $_POST[ $this->option_id ] ) ) ? $_POST[ $this->option_id ] : array();
			if ( ! empty( $option_request ) && ! empty( $this->option_sections ) ) {
				foreach ( $this->option_sections as $section ) {
					if ( ! empty( $section['fields'] ) ) {

						foreach ( $section['fields'] as $field ) {

							if ( ! empty( $field['id'] ) ) {
								$data = isset( $option_request[ $field['id'] ] ) ? $option_request[ $field['id'] ] : '';

								$fieldClass = 'TF_' . $field['type'];
								if($fieldClass != 'TF_file'){
									$data       = $fieldClass == 'TF_repeater' || $fieldClass == 'TF_map' || $fieldClass == 'TF_tab' || $fieldClass == 'TF_color' ? serialize( $data ) : $data;
								}
								if($fieldClass == 'TF_file'){
									$tf_upload_dir = wp_upload_dir();

									if ( ! empty( $tf_upload_dir['basedir'] ) ) {
									$tf_itinerary_fonts = $tf_upload_dir['basedir'].'/itinerary-fonts';
									if ( ! file_exists( $tf_itinerary_fonts ) ) {
									wp_mkdir_p( $tf_itinerary_fonts );
									}
									if (!empty($_FILES['file'])) {
										$tf_fonts_extantions = array('application/octet-stream');
										for($i = 0; $i < count($_FILES['file']['name']); $i++) {
										if (in_array($_FILES['file']['type'][$i], $tf_fonts_extantions)) {
											$tf_font_filename = $_FILES['file']['name'][$i];
											move_uploaded_file($_FILES['file']['tmp_name'][$i], $tf_itinerary_fonts .'/'. $tf_font_filename);
											}
										}
									}
									}
								}

								if ( class_exists( $fieldClass ) ) {
									$_field                          = new $fieldClass( $field, $data, $this->option_id );
									$tf_option_value[ $field['id'] ] = $_field->sanitize();
								}

							}
						}
					}
				}
			}

			if ( ! empty( $tf_option_value ) ) {
				update_option( $this->option_id, $tf_option_value );
			} else {
				delete_option( $this->option_id );
			}
		}

		/*
		 * Ajax Save Options
		 * @author Foysal
		 */
		public function tf_ajax_save_options() {
			$response    = [
				'status'  => 'error',
				'message' => __( 'Something went wrong!', 'tourfic' ),
			];

            if( ! empty( $_POST['tf_option_nonce'] ) && wp_verify_nonce( $_POST['tf_option_nonce'], 'tf_option_nonce_action' ) ) {
                $this->save_options();
                $response = [
                    'status'  => 'success',
                    'message' => __( 'Options saved successfully!', 'tourfic' ),
                ];
            }

            echo json_encode( $response );
            wp_die();
		}

		/*
		 * Get current page url
		 * @return string
		 * @author Foysal
		 */
		public function get_current_page_url() {
            $page_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            return $page_url;
        }

        /*
         * Get query string from url
         * @return array
         * @author Foysal
         */
        public function get_query_string( $url ) {
	        $url_parts = parse_url( $url );
	        parse_str( $url_parts['query'], $query_string );

            return $query_string;
        }
	}
}

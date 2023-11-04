<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_icon' ) ) {
	class TF_icon extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );

			//tf_icon_modal method load single time
			static $tf_icon_modal;
			if ( ! $tf_icon_modal ) {
				$tf_icon_modal = true;
				add_action( 'admin_footer', array( $this, 'tf_icon_modal' ) );
			}
		}

		public function render() {

			$default       = isset( $this->field['default'] ) ? $this->field['default'] : '';
			$value         = $this->value ? $this->value : '';
			$preview_class = $value ? 'tf-icon-preview' : 'tf-icon-preview tf-hide';
			$uniqueid      = uniqid();
			?>
            <div class="tf-icon-select" id="tf-icon-<?php echo esc_attr( $this->field['id'] . $uniqueid ); ?>">
                <div class="<?php echo esc_attr( $preview_class ); ?>">
                    <span class="tf-icon-preview-wrap tf-modal-btn">
                        <i class="<?php echo esc_attr( $value ); ?>"></i>
                    </span>
                    <span class="remove-icon">
                        <i class="ri-close-line"></i>
                    </span>
                </div>
                <a href="#" class="tf-admin-btn tf-modal-btn"><i class="ri-add-fill"></i><?php esc_html_e( 'Add Icon', 'tourfic' ); ?></a>
                <input type="hidden" class="tf-icon-value" name="<?php echo esc_attr( $this->field_name() ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php echo $this->field_attributes() ?>/>
            </div>
			<?php
		}

		public function tf_icon_modal() {
			?>
            <div class="tf-modal" id="tf-icon-modal" data-icon-field="">
                <div class="tf-modal-dialog">
                    <div class="container tf-modal-content">
                        <div class="tf-modal-header">
                            <div class="tf-icon-search">
                                <input type="text" placeholder="<?php esc_html_e( 'Search', 'tourfic' ); ?>" class="tf-icon-search-input"/>
                            </div>
                            <a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
                        </div>
                        <div class="tf-modal-body">
                            <div class="tf-icon-wrapper">
                                <ul class="tf-icon-tab-list">
									<?php
									$count     = 0;
									$icon_list = $this->get_icon_list();
									foreach ( $icon_list as $key => $value ) :
										if ( $value['icons'] ):
											?>
                                            <li class="tf-icon-tab <?php echo $count == 0 ? 'active' : '' ?>" data-tab="tf-icon-tab-<?php echo esc_attr( $key ) ?>">
                                                <i class="<?php echo esc_attr( $value['label_icon'] ) ?>"></i><?php echo esc_html( $value['label'] ); ?>
                                            </li>
										<?php
										endif;
										$count ++;
									endforeach; ?>
                                </ul>
                                <div class="tf-icon-tab-content">
									<?php
									$count     = 0;
									$icon_list = $this->get_icon_list();
									foreach ( $icon_list as $key => $value ) :
										?>
                                        <div class="tf-icon-tab-pane <?php echo $count == 0 ? 'active' : '' ?>" id="tf-icon-tab-<?php echo esc_attr( $key ) ?>">
                                            <ul class="tf-icon-list">
												<?php
												if ( $value['icons'] ):
													foreach ( $value['icons'] as $icon ) :
														?>
                                                        <li data-icon="<?php echo esc_attr( $icon ); ?>">
                                                            <div class="tf-icon-inner">
                                                                <i title="<?php echo esc_attr( $icon ); ?>" class="tf-main-icon <?php echo esc_attr( $icon ); ?>"></i>

                                                                <span class="check-icon">
                                                                    <i class="ri-check-line"></i>
                                                                </span>
                                                            </div>
                                                        </li>
													<?php endforeach;
												endif; ?>
                                            </ul>
                                        </div>
										<?php $count ++;
									endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="tf-modal-footer">
                            <a class="tf-icon-insert tf-admin-btn tf-btn-secondary disabled"><?php esc_html_e( 'Insert', 'tourfic' ); ?></a>
                        </div>
                    </div>
                </div>
            </div>
			<?php
		}

		public function get_icon_list() {
			$icons = array(
				'fontawesome_4' => array(
					'label'      => __( 'Font Awesome 4', 'tourfic' ),
					'label_icon' => 'fa-regular fa-font-awesome',
					'icons'      => $this->fontawesome_four_icons(),
				),
				'fontawesome_5' => array(
					'label'      => __( 'Font Awesome 5', 'tourfic' ),
					'label_icon' => 'fa-regular fa-font-awesome',
					'icons'      => $this->fontawesome_five_icons(),
				),
				'fontawesome_6' => array(
					'label'      => __( 'Font Awesome 6', 'tourfic' ),
					'label_icon' => 'fa-regular fa-font-awesome',
					'icons'      => $this->fontawesome_six_icons(),
				),
				'remixicon'     => array(
					'label'      => __( 'Remix Icon', 'tourfic' ),
					'label_icon' => 'ri-remixicon-line',
					'icons'      => $this->remix_icon(),
				),
			);

			$icons = apply_filters( 'tf_icon_list', $icons );

			return $icons;
		}

		public function fontawesome_four_icons() {
			$icons = array(
				'fa fa-glass',
				'fa fa-music',
				'fa fa-search',
				'fa fa-envelope-o',
				'fa fa-heart',
				'fa fa-star',
				'fa fa-star-o',
				'fa fa-user',
				'fa fa-film',
				'fa fa-th-large',
				'fa fa-th',
				'fa fa-th-list',
				'fa fa-check',
				'fa fa-times',
				'fa fa-search-plus',
				'fa fa-search-minus',
				'fa fa-power-off',
				'fa fa-signal',
				'fa fa-cog',
				'fa fa-trash-o',
				'fa fa-home',
				'fa fa-file-o',
				'fa fa-clock-o',
				'fa fa-road',
				'fa fa-download',
				'fa fa-arrow-circle-o-down',
				'fa fa-arrow-circle-o-up',
				'fa fa-inbox',
				'fa fa-play-circle-o',
				'fa fa-repeat',
				'fa fa-refresh',
				'fa fa-list-alt',
				'fa fa-lock',
				'fa fa-flag',
				'fa fa-headphones',
				'fa fa-volume-off',
				'fa fa-volume-down',
				'fa fa-volume-up',
				'fa fa-qrcode',
				'fa fa-barcode',
				'fa fa-tag',
				'fa fa-tags',
				'fa fa-book',
				'fa fa-bookmark',
				'fa fa-print',
				'fa fa-camera',
				'fa fa-font',
				'fa fa-bold',
				'fa fa-italic',
				'fa fa-text-height',
				'fa fa-text-width',
				'fa fa-align-left',
				'fa fa-align-center',
				'fa fa-align-right',
				'fa fa-align-justify',
				'fa fa-list',
				'fa fa-outdent',
				'fa fa-indent',
				'fa fa-video-camera',
				'fa fa-picture-o',
				'fa fa-pencil',
				'fa fa-map-marker',
				'fa fa-adjust',
				'fa fa-tint',
				'fa fa-pencil-square-o',
				'fa fa-share-square-o',
				'fa fa-check-square-o',
				'fa fa-arrows',
				'fa fa-step-backward',
				'fa fa-fast-backward',
				'fa fa-backward',
				'fa fa-play',
				'fa fa-pause',
				'fa fa-stop',
				'fa fa-forward',
				'fa fa-fast-forward',
				'fa fa-step-forward',
				'fa fa-eject',
				'fa fa-chevron-left',
				'fa fa-chevron-right',
				'fa fa-plus-circle',
				'fa fa-minus-circle',
				'fa fa-times-circle',
				'fa fa-check-circle',
				'fa fa-question-circle',
				'fa fa-info-circle',
				'fa fa-crosshairs',
				'fa fa-times-circle-o',
				'fa fa-check-circle-o',
				'fa fa-ban',
				'fa fa-arrow-left',
				'fa fa-arrow-right',
				'fa fa-arrow-up',
				'fa fa-arrow-down',
				'fa fa-share',
				'fa fa-expand',
				'fa fa-compress',
				'fa fa-plus',
				'fa fa-minus',
				'fa fa-asterisk',
				'fa fa-exclamation-circle',
				'fa fa-gift',
				'fa fa-leaf',
				'fa fa-fire',
				'fa fa-eye',
				'fa fa-eye-slash',
				'fa fa-exclamation-triangle',
				'fa fa-plane',
				'fa fa-calendar',
				'fa fa-random',
				'fa fa-comment',
				'fa fa-magnet',
				'fa fa-chevron-up',
				'fa fa-chevron-down',
				'fa fa-retweet',
				'fa fa-shopping-cart',
				'fa fa-folder',
				'fa fa-folder-open',
				'fa fa-arrows-v',
				'fa fa-arrows-h',
				'fa fa-bar-chart',
				'fa fa-twitter-square',
				'fa fa-facebook-square',
				'fa fa-camera-retro',
				'fa fa-key',
				'fa fa-cogs',
				'fa fa-comments',
				'fa fa-thumbs-o-up',
				'fa fa-thumbs-o-down',
				'fa fa-star-half',
				'fa fa-heart-o',
				'fa fa-sign-out',
				'fa fa-linkedin-square',
				'fa fa-thumb-tack',
				'fa fa-external-link',
				'fa fa-sign-in',
				'fa fa-trophy',
				'fa fa-github-square',
				'fa fa-upload',
				'fa fa-lemon-o',
				'fa fa-phone',
				'fa fa-square-o',
				'fa fa-bookmark-o',
				'fa fa-phone-square',
				'fa fa-twitter',
				'fa fa-facebook',
				'fa fa-github',
				'fa fa-unlock',
				'fa fa-credit-card',
				'fa fa-rss',
				'fa fa-hdd-o',
				'fa fa-bullhorn',
				'fa fa-bell',
				'fa fa-certificate',
				'fa fa-hand-o-right',
				'fa fa-hand-o-left',
				'fa fa-hand-o-up',
				'fa fa-hand-o-down',
				'fa fa-arrow-circle-left',
				'fa fa-arrow-circle-right',
				'fa fa-arrow-circle-up',
				'fa fa-arrow-circle-down',
				'fa fa-globe',
				'fa fa-wrench',
				'fa fa-tasks',
				'fa fa-filter',
				'fa fa-briefcase',
				'fa fa-arrows-alt',
				'fa fa-users',
				'fa fa-link',
				'fa fa-cloud',
				'fa fa-flask',
				'fa fa-scissors',
				'fa fa-files-o',
				'fa fa-paperclip',
				'fa fa-floppy-o',
				'fa fa-square',
				'fa fa-bars',
				'fa fa-list-ul',
				'fa fa-list-ol',
				'fa fa-strikethrough',
				'fa fa-underline',
				'fa fa-table',
				'fa fa-magic',
				'fa fa-truck',
				'fa fa-pinterest',
				'fa fa-pinterest-square',
				'fa fa-google-plus-square',
				'fa fa-google-plus',
				'fa fa-money',
				'fa fa-caret-down',
				'fa fa-caret-up',
				'fa fa-caret-left',
				'fa fa-caret-right',
				'fa fa-columns',
				'fa fa-sort',
				'fa fa-sort-desc',
				'fa fa-sort-asc',
				'fa fa-envelope',
				'fa fa-linkedin',
				'fa fa-undo',
				'fa fa-gavel',
				'fa fa-tachometer',
				'fa fa-comment-o',
				'fa fa-comments-o',
				'fa fa-bolt',
				'fa fa-sitemap',
				'fa fa-umbrella',
				'fa fa-clipboard',
				'fa fa-lightbulb-o',
				'fa fa-exchange',
				'fa fa-cloud-download',
				'fa fa-cloud-upload',
				'fa fa-user-md',
				'fa fa-stethoscope',
				'fa fa-suitcase',
				'fa fa-bell-o',
				'fa fa-coffee',
				'fa fa-cutlery',
				'fa fa-file-text-o',
				'fa fa-building-o',
				'fa fa-hospital-o',
				'fa fa-ambulance',
				'fa fa-medkit',
				'fa fa-fighter-jet',
				'fa fa-beer',
				'fa fa-h-square',
				'fa fa-plus-square',
				'fa fa-angle-double-left',
				'fa fa-angle-double-right',
				'fa fa-angle-double-up',
				'fa fa-angle-double-down',
				'fa fa-angle-left',
				'fa fa-angle-right',
				'fa fa-angle-up',
				'fa fa-angle-down',
				'fa fa-desktop',
				'fa fa-laptop',
				'fa fa-tablet',
				'fa fa-mobile',
				'fa fa-circle-o',
				'fa fa-quote-left',
				'fa fa-quote-right',
				'fa fa-spinner',
				'fa fa-circle',
				'fa fa-reply',
				'fa fa-github-alt',
				'fa fa-folder-o',
				'fa fa-folder-open-o',
				'fa fa-smile-o',
				'fa fa-frown-o',
				'fa fa-meh-o',
				'fa fa-gamepad',
				'fa fa-keyboard-o',
				'fa fa-flag-o',
				'fa fa-flag-checkered',
				'fa fa-terminal',
				'fa fa-code',
				'fa fa-reply-all',
				'fa fa-star-half-o',
				'fa fa-location-arrow',
				'fa fa-crop',
				'fa fa-code-fork',
				'fa fa-chain-broken',
				'fa fa-question',
				'fa fa-info',
				'fa fa-exclamation',
				'fa fa-superscript',
				'fa fa-subscript',
				'fa fa-eraser',
				'fa fa-puzzle-piece',
				'fa fa-microphone',
				'fa fa-microphone-slash',
				'fa fa-shield',
				'fa fa-calendar-o',
				'fa fa-fire-extinguisher',
				'fa fa-rocket',
				'fa fa-maxcdn',
				'fa fa-chevron-circle-left',
				'fa fa-chevron-circle-right',
				'fa fa-chevron-circle-up',
				'fa fa-chevron-circle-down',
				'fa fa-html5',
				'fa fa-css3',
				'fa fa-anchor',
				'fa fa-unlock-alt',
				'fa fa-bullseye',
				'fa fa-ellipsis-h',
				'fa fa-ellipsis-v',
				'fa fa-rss-square',
				'fa fa-play-circle',
				'fa fa-ticket',
				'fa fa-minus-square',
				'fa fa-minus-square-o',
				'fa fa-level-up',
				'fa fa-level-down',
				'fa fa-check-square',
				'fa fa-pencil-square',
				'fa fa-external-link-square',
				'fa fa-share-square',
				'fa fa-compass',
				'fa fa-caret-square-o-down',
				'fa fa-caret-square-o-up',
				'fa fa-caret-square-o-right',
				'fa fa-eur',
				'fa fa-gbp',
				'fa fa-usd',
				'fa fa-inr',
				'fa fa-jpy',
				'fa fa-rub',
				'fa fa-krw',
				'fa fa-btc',
				'fa fa-file',
				'fa fa-file-text',
				'fa fa-sort-alpha-asc',
				'fa fa-sort-alpha-desc',
				'fa fa-sort-amount-asc',
				'fa fa-sort-amount-desc',
				'fa fa-sort-numeric-asc',
				'fa fa-sort-numeric-desc',
				'fa fa-thumbs-up',
				'fa fa-thumbs-down',
				'fa fa-youtube-square',
				'fa fa-youtube',
				'fa fa-xing',
				'fa fa-xing-square',
				'fa fa-youtube-play',
				'fa fa-dropbox',
				'fa fa-stack-overflow',
				'fa fa-instagram',
				'fa fa-flickr',
				'fa fa-adn',
				'fa fa-bitbucket',
				'fa fa-bitbucket-square',
				'fa fa-tumblr',
				'fa fa-tumblr-square',
				'fa fa-long-arrow-down',
				'fa fa-long-arrow-up',
				'fa fa-long-arrow-left',
				'fa fa-long-arrow-right',
				'fa fa-apple',
				'fa fa-windows',
				'fa fa-android',
				'fa fa-linux',
				'fa fa-dribbble',
				'fa fa-skype',
				'fa fa-foursquare',
				'fa fa-trello',
				'fa fa-female',
				'fa fa-male',
				'fa fa-gratipay',
				'fa fa-sun-o',
				'fa fa-moon-o',
				'fa fa-archive',
				'fa fa-bug',
				'fa fa-vk',
				'fa fa-weibo',
				'fa fa-renren',
				'fa fa-pagelines',
				'fa fa-stack-exchange',
				'fa fa-arrow-circle-o-right',
				'fa fa-arrow-circle-o-left',
				'fa fa-caret-square-o-left',
				'fa fa-dot-circle-o',
				'fa fa-wheelchair',
				'fa fa-vimeo-square',
				'fa fa-try',
				'fa fa-plus-square-o',
				'fa fa-space-shuttle',
				'fa fa-slack',
				'fa fa-envelope-square',
				'fa fa-wordpress',
				'fa fa-openid',
				'fa fa-university',
				'fa fa-graduation-cap',
				'fa fa-yahoo',
				'fa fa-google',
				'fa fa-reddit',
				'fa fa-reddit-square',
				'fa fa-stumbleupon-circle',
				'fa fa-stumbleupon',
				'fa fa-delicious',
				'fa fa-digg',
				'fa fa-pied-piper-pp',
				'fa fa-pied-piper-alt',
				'fa fa-drupal',
				'fa fa-joomla',
				'fa fa-language',
				'fa fa-fax',
				'fa fa-building',
				'fa fa-child',
				'fa fa-paw',
				'fa fa-spoon',
				'fa fa-cube',
				'fa fa-cubes',
				'fa fa-behance',
				'fa fa-behance-square',
				'fa fa-steam',
				'fa fa-steam-square',
				'fa fa-recycle',
				'fa fa-car',
				'fa fa-taxi',
				'fa fa-tree',
				'fa fa-spotify',
				'fa fa-deviantart',
				'fa fa-soundcloud',
				'fa fa-database',
				'fa fa-file-pdf-o',
				'fa fa-file-word-o',
				'fa fa-file-excel-o',
				'fa fa-file-powerpoint-o',
				'fa fa-file-image-o',
				'fa fa-file-archive-o',
				'fa fa-file-audio-o',
				'fa fa-file-video-o',
				'fa fa-file-code-o',
				'fa fa-vine',
				'fa fa-codepen',
				'fa fa-jsfiddle',
				'fa fa-life-ring',
				'fa fa-circle-o-notch',
				'fa fa-rebel',
				'fa fa-empire',
				'fa fa-git-square',
				'fa fa-git',
				'fa fa-hacker-news',
				'fa fa-tencent-weibo',
				'fa fa-qq',
				'fa fa-weixin',
				'fa fa-paper-plane',
				'fa fa-paper-plane-o',
				'fa fa-history',
				'fa fa-circle-thin',
				'fa fa-header',
				'fa fa-paragraph',
				'fa fa-sliders',
				'fa fa-share-alt',
				'fa fa-share-alt-square',
				'fa fa-bomb',
				'fa fa-futbol-o',
				'fa fa-tty',
				'fa fa-binoculars',
				'fa fa-plug',
				'fa fa-slideshare',
				'fa fa-twitch',
				'fa fa-yelp',
				'fa fa-newspaper-o',
				'fa fa-wifi',
				'fa fa-calculator',
				'fa fa-paypal',
				'fa fa-google-wallet',
				'fa fa-cc-visa',
				'fa fa-cc-mastercard',
				'fa fa-cc-discover',
				'fa fa-cc-amex',
				'fa fa-cc-paypal',
				'fa fa-cc-stripe',
				'fa fa-bell-slash',
				'fa fa-bell-slash-o',
				'fa fa-trash',
				'fa fa-copyright',
				'fa fa-at',
				'fa fa-eyedropper',
				'fa fa-paint-brush',
				'fa fa-birthday-cake',
				'fa fa-area-chart',
				'fa fa-pie-chart',
				'fa fa-line-chart',
				'fa fa-lastfm',
				'fa fa-lastfm-square',
				'fa fa-toggle-off',
				'fa fa-toggle-on',
				'fa fa-bicycle',
				'fa fa-bus',
				'fa fa-ioxhost',
				'fa fa-angellist',
				'fa fa-cc',
				'fa fa-ils',
				'fa fa-meanpath',
				'fa fa-buysellads',
				'fa fa-connectdevelop',
				'fa fa-dashcube',
				'fa fa-forumbee',
				'fa fa-leanpub',
				'fa fa-sellsy',
				'fa fa-shirtsinbulk',
				'fa fa-simplybuilt',
				'fa fa-skyatlas',
				'fa fa-cart-plus',
				'fa fa-cart-arrow-down',
				'fa fa-diamond',
				'fa fa-ship',
				'fa fa-user-secret',
				'fa fa-motorcycle',
				'fa fa-street-view',
				'fa fa-heartbeat',
				'fa fa-venus',
				'fa fa-mars',
				'fa fa-mercury',
				'fa fa-transgender',
				'fa fa-transgender-alt',
				'fa fa-venus-double',
				'fa fa-mars-double',
				'fa fa-venus-mars',
				'fa fa-mars-stroke',
				'fa fa-mars-stroke-v',
				'fa fa-mars-stroke-h',
				'fa fa-neuter',
				'fa fa-genderless',
				'fa fa-facebook-official',
				'fa fa-pinterest-p',
				'fa fa-whatsapp',
				'fa fa-server',
				'fa fa-user-plus',
				'fa fa-user-times',
				'fa fa-bed',
				'fa fa-viacoin',
				'fa fa-train',
				'fa fa-subway',
				'fa fa-medium',
				'fa fa-y-combinator',
				'fa fa-optin-monster',
				'fa fa-opencart',
				'fa fa-expeditedssl',
				'fa fa-battery-full',
				'fa fa-battery-three-quarters',
				'fa fa-battery-half',
				'fa fa-battery-quarter',
				'fa fa-battery-empty',
				'fa fa-mouse-pointer',
				'fa fa-i-cursor',
				'fa fa-object-group',
				'fa fa-object-ungroup',
				'fa fa-sticky-note',
				'fa fa-sticky-note-o',
				'fa fa-cc-jcb',
				'fa fa-cc-diners-club',
				'fa fa-clone',
				'fa fa-balance-scale',
				'fa fa-hourglass-o',
				'fa fa-hourglass-start',
				'fa fa-hourglass-half',
				'fa fa-hourglass-end',
				'fa fa-hourglass',
				'fa fa-hand-rock-o',
				'fa fa-hand-paper-o',
				'fa fa-hand-scissors-o',
				'fa fa-hand-lizard-o',
				'fa fa-hand-spock-o',
				'fa fa-hand-pointer-o',
				'fa fa-hand-peace-o',
				'fa fa-trademark',
				'fa fa-registered',
				'fa fa-creative-commons',
				'fa fa-gg',
				'fa fa-gg-circle',
				'fa fa-tripadvisor',
				'fa fa-odnoklassniki',
				'fa fa-odnoklassniki-square',
				'fa fa-get-pocket',
				'fa fa-wikipedia-w',
				'fa fa-safari',
				'fa fa-chrome',
				'fa fa-firefox',
				'fa fa-opera',
				'fa fa-internet-explorer',
				'fa fa-television',
				'fa fa-contao',
				'fa fa-500px',
				'fa fa-amazon',
				'fa fa-calendar-plus-o',
				'fa fa-calendar-minus-o',
				'fa fa-calendar-times-o',
				'fa fa-calendar-check-o',
				'fa fa-industry',
				'fa fa-map-pin',
				'fa fa-map-signs',
				'fa fa-map-o',
				'fa fa-map',
				'fa fa-commenting',
				'fa fa-commenting-o',
				'fa fa-houzz',
				'fa fa-vimeo',
				'fa fa-black-tie',
				'fa fa-fonticons',
				'fa fa-reddit-alien',
				'fa fa-edge',
				'fa fa-credit-card-alt',
				'fa fa-codiepie',
				'fa fa-modx',
				'fa fa-fort-awesome',
				'fa fa-usb',
				'fa fa-product-hunt',
				'fa fa-mixcloud',
				'fa fa-scribd',
				'fa fa-pause-circle',
				'fa fa-pause-circle-o',
				'fa fa-stop-circle',
				'fa fa-stop-circle-o',
				'fa fa-shopping-bag',
				'fa fa-shopping-basket',
				'fa fa-hashtag',
				'fa fa-bluetooth',
				'fa fa-bluetooth-b',
				'fa fa-percent',
				'fa fa-gitlab',
				'fa fa-wpbeginner',
				'fa fa-wpforms',
				'fa fa-envira',
				'fa fa-universal-access',
				'fa fa-wheelchair-alt',
				'fa fa-question-circle-o',
				'fa fa-blind',
				'fa fa-audio-description',
				'fa fa-volume-control-phone',
				'fa fa-braille',
				'fa fa-assistive-listening-systems',
				'fa fa-american-sign-language-interpreting',
				'fa fa-deaf',
				'fa fa-glide',
				'fa fa-glide-g',
				'fa fa-sign-language',
				'fa fa-low-vision',
				'fa fa-viadeo',
				'fa fa-viadeo-square',
				'fa fa-snapchat',
				'fa fa-snapchat-ghost',
				'fa fa-snapchat-square',
				'fa fa-pied-piper',
				'fa fa-first-order',
				'fa fa-yoast',
				'fa fa-themeisle',
				'fa fa-google-plus-official',
				'fa fa-font-awesome',
				'fa fa-handshake-o',
				'fa fa-envelope-open',
				'fa fa-envelope-open-o',
				'fa fa-linode',
				'fa fa-address-book',
				'fa fa-address-book-o',
				'fa fa-address-card',
				'fa fa-address-card-o',
				'fa fa-user-circle',
				'fa fa-user-circle-o',
				'fa fa-user-o',
				'fa fa-id-badge',
				'fa fa-id-card',
				'fa fa-id-card-o',
				'fa fa-quora',
				'fa fa-free-code-camp',
				'fa fa-telegram',
				'fa fa-thermometer-full',
				'fa fa-thermometer-three-quarters',
				'fa fa-thermometer-half',
				'fa fa-thermometer-quarter',
				'fa fa-thermometer-empty',
				'fa fa-shower',
				'fa fa-bath',
				'fa fa-podcast',
				'fa fa-window-maximize',
				'fa fa-window-minimize',
				'fa fa-window-restore',
				'fa fa-window-close',
				'fa fa-window-close-o',
				'fa fa-bandcamp',
				'fa fa-grav',
				'fa fa-etsy',
				'fa fa-imdb',
				'fa fa-ravelry',
				'fa fa-eercast',
				'fa fa-microchip',
				'fa fa-snowflake-o',
				'fa fa-superpowers',
				'fa fa-wpexplorer',
				'fa fa-meetup'
			);

			return $icons;
		}

		public function fontawesome_five_icons() {
			$icons = array(
				'fab fa-500px',
				'fab fa-accessible-icon',
				'fab fa-accusoft',
				'fab fa-acquisitions-incorporated',
				'fas fa-ad',
				'fas fa-address-book',
				'far fa-address-book',
				'fas fa-address-card',
				'far fa-address-card',
				'fas fa-adjust',
				'fab fa-adn',
				'fab fa-adversal',
				'fab fa-affiliatetheme',
				'fas fa-air-freshener',
				'fab fa-airbnb',
				'fab fa-algolia',
				'fas fa-align-center',
				'fas fa-align-justify',
				'fas fa-align-left',
				'fas fa-align-right',
				'fab fa-alipay',
				'fas fa-allergies',
				'fab fa-amazon',
				'fab fa-amazon-pay',
				'fas fa-ambulance',
				'fas fa-american-sign-language-interpreting',
				'fab fa-amilia',
				'fas fa-anchor',
				'fab fa-android',
				'fab fa-angellist',
				'fas fa-angle-double-down',
				'fas fa-angle-double-left',
				'fas fa-angle-double-right',
				'fas fa-angle-double-up',
				'fas fa-angle-down',
				'fas fa-angle-left',
				'fas fa-angle-right',
				'fas fa-angle-up',
				'fas fa-angry',
				'far fa-angry',
				'fab fa-angrycreative',
				'fab fa-angular',
				'fas fa-ankh',
				'fab fa-app-store',
				'fab fa-app-store-ios',
				'fab fa-apper',
				'fab fa-apple',
				'fas fa-apple-alt',
				'fab fa-apple-pay',
				'fas fa-archive',
				'fas fa-archway',
				'fas fa-arrow-alt-circle-down',
				'far fa-arrow-alt-circle-down',
				'fas fa-arrow-alt-circle-left',
				'far fa-arrow-alt-circle-left',
				'fas fa-arrow-alt-circle-right',
				'far fa-arrow-alt-circle-right',
				'fas fa-arrow-alt-circle-up',
				'far fa-arrow-alt-circle-up',
				'fas fa-arrow-circle-down',
				'fas fa-arrow-circle-left',
				'fas fa-arrow-circle-right',
				'fas fa-arrow-circle-up',
				'fas fa-arrow-down',
				'fas fa-arrow-left',
				'fas fa-arrow-right',
				'fas fa-arrow-up',
				'fas fa-arrows-alt',
				'fas fa-arrows-alt-h',
				'fas fa-arrows-alt-v',
				'fab fa-artstation',
				'fas fa-assistive-listening-systems',
				'fas fa-asterisk',
				'fab fa-asymmetrik',
				'fas fa-at',
				'fas fa-atlas',
				'fab fa-atlassian',
				'fas fa-atom',
				'fab fa-audible',
				'fas fa-audio-description',
				'fab fa-autoprefixer',
				'fab fa-avianex',
				'fab fa-aviato',
				'fas fa-award',
				'fab fa-aws',
				'fas fa-baby',
				'fas fa-baby-carriage',
				'fas fa-backspace',
				'fas fa-backward',
				'fas fa-bacon',
				'fas fa-bacteria',
				'fas fa-bacterium',
				'fas fa-bahai',
				'fas fa-balance-scale',
				'fas fa-balance-scale-left',
				'fas fa-balance-scale-right',
				'fas fa-ban',
				'fas fa-band-aid',
				'fab fa-bandcamp',
				'fas fa-barcode',
				'fas fa-bars',
				'fas fa-baseball-ball',
				'fas fa-basketball-ball',
				'fas fa-bath',
				'fas fa-battery-empty',
				'fas fa-battery-full',
				'fas fa-battery-half',
				'fas fa-battery-quarter',
				'fas fa-battery-three-quarters',
				'fab fa-battle-net',
				'fas fa-bed',
				'fas fa-beer',
				'fab fa-behance',
				'fab fa-behance-square',
				'fas fa-bell',
				'far fa-bell',
				'fas fa-bell-slash',
				'far fa-bell-slash',
				'fas fa-bezier-curve',
				'fas fa-bible',
				'fas fa-bicycle',
				'fas fa-biking',
				'fab fa-bimobject',
				'fas fa-binoculars',
				'fas fa-biohazard',
				'fas fa-birthday-cake',
				'fab fa-bitbucket',
				'fab fa-bitcoin',
				'fab fa-bity',
				'fab fa-black-tie',
				'fab fa-blackberry',
				'fas fa-blender',
				'fas fa-blender-phone',
				'fas fa-blind',
				'fas fa-blog',
				'fab fa-blogger',
				'fab fa-blogger-b',
				'fab fa-bluetooth',
				'fab fa-bluetooth-b',
				'fas fa-bold',
				'fas fa-bolt',
				'fas fa-bomb',
				'fas fa-bone',
				'fas fa-bong',
				'fas fa-book',
				'fas fa-book-dead',
				'fas fa-book-medical',
				'fas fa-book-open',
				'fas fa-book-reader',
				'fas fa-bookmark',
				'far fa-bookmark',
				'fab fa-bootstrap',
				'fas fa-border-all',
				'fas fa-border-none',
				'fas fa-border-style',
				'fas fa-bowling-ball',
				'fas fa-box',
				'fas fa-box-open',
				'fas fa-box-tissue',
				'fas fa-boxes',
				'fas fa-braille',
				'fas fa-brain',
				'fas fa-bread-slice',
				'fas fa-briefcase',
				'fas fa-briefcase-medical',
				'fas fa-broadcast-tower',
				'fas fa-broom',
				'fas fa-brush',
				'fab fa-btc',
				'fab fa-buffer',
				'fas fa-bug',
				'fas fa-building',
				'far fa-building',
				'fas fa-bullhorn',
				'fas fa-bullseye',
				'fas fa-burn',
				'fab fa-buromobelexperte',
				'fas fa-bus',
				'fas fa-bus-alt',
				'fas fa-business-time',
				'fab fa-buy-n-large',
				'fab fa-buysellads',
				'fas fa-calculator',
				'fas fa-calendar',
				'far fa-calendar',
				'fas fa-calendar-alt',
				'far fa-calendar-alt',
				'fas fa-calendar-check',
				'far fa-calendar-check',
				'fas fa-calendar-day',
				'fas fa-calendar-minus',
				'far fa-calendar-minus',
				'fas fa-calendar-plus',
				'far fa-calendar-plus',
				'fas fa-calendar-times',
				'far fa-calendar-times',
				'fas fa-calendar-week',
				'fas fa-camera',
				'fas fa-camera-retro',
				'fas fa-campground',
				'fab fa-canadian-maple-leaf',
				'fas fa-candy-cane',
				'fas fa-cannabis',
				'fas fa-capsules',
				'fas fa-car',
				'fas fa-car-alt',
				'fas fa-car-battery',
				'fas fa-car-crash',
				'fas fa-car-side',
				'fas fa-caravan',
				'fas fa-caret-down',
				'fas fa-caret-left',
				'fas fa-caret-right',
				'fas fa-caret-square-down',
				'far fa-caret-square-down',
				'fas fa-caret-square-left',
				'far fa-caret-square-left',
				'fas fa-caret-square-right',
				'far fa-caret-square-right',
				'fas fa-caret-square-up',
				'far fa-caret-square-up',
				'fas fa-caret-up',
				'fas fa-carrot',
				'fas fa-cart-arrow-down',
				'fas fa-cart-plus',
				'fas fa-cash-register',
				'fas fa-cat',
				'fab fa-cc-amazon-pay',
				'fab fa-cc-amex',
				'fab fa-cc-apple-pay',
				'fab fa-cc-diners-club',
				'fab fa-cc-discover',
				'fab fa-cc-jcb',
				'fab fa-cc-mastercard',
				'fab fa-cc-paypal',
				'fab fa-cc-stripe',
				'fab fa-cc-visa',
				'fab fa-centercode',
				'fab fa-centos',
				'fas fa-certificate',
				'fas fa-chair',
				'fas fa-chalkboard',
				'fas fa-chalkboard-teacher',
				'fas fa-charging-station',
				'fas fa-chart-area',
				'fas fa-chart-bar',
				'far fa-chart-bar',
				'fas fa-chart-line',
				'fas fa-chart-pie',
				'fas fa-check',
				'fas fa-check-circle',
				'far fa-check-circle',
				'fas fa-check-double',
				'fas fa-check-square',
				'far fa-check-square',
				'fas fa-cheese',
				'fas fa-chess',
				'fas fa-chess-bishop',
				'fas fa-chess-board',
				'fas fa-chess-king',
				'fas fa-chess-knight',
				'fas fa-chess-pawn',
				'fas fa-chess-queen',
				'fas fa-chess-rook',
				'fas fa-chevron-circle-down',
				'fas fa-chevron-circle-left',
				'fas fa-chevron-circle-right',
				'fas fa-chevron-circle-up',
				'fas fa-chevron-down',
				'fas fa-chevron-left',
				'fas fa-chevron-right',
				'fas fa-chevron-up',
				'fas fa-child',
				'fab fa-chrome',
				'fab fa-chromecast',
				'fas fa-church',
				'fas fa-circle',
				'far fa-circle',
				'fas fa-circle-notch',
				'fas fa-city',
				'fas fa-clinic-medical',
				'fas fa-clipboard',
				'far fa-clipboard',
				'fas fa-clipboard-check',
				'fas fa-clipboard-list',
				'fas fa-clock',
				'far fa-clock',
				'fas fa-clone',
				'far fa-clone',
				'fas fa-closed-captioning',
				'far fa-closed-captioning',
				'fas fa-cloud',
				'fas fa-cloud-download-alt',
				'fas fa-cloud-meatball',
				'fas fa-cloud-moon',
				'fas fa-cloud-moon-rain',
				'fas fa-cloud-rain',
				'fas fa-cloud-showers-heavy',
				'fas fa-cloud-sun',
				'fas fa-cloud-sun-rain',
				'fas fa-cloud-upload-alt',
				'fab fa-cloudflare',
				'fab fa-cloudscale',
				'fab fa-cloudsmith',
				'fab fa-cloudversify',
				'fas fa-cocktail',
				'fas fa-code',
				'fas fa-code-branch',
				'fab fa-codepen',
				'fab fa-codiepie',
				'fas fa-coffee',
				'fas fa-cog',
				'fas fa-cogs',
				'fas fa-coins',
				'fas fa-columns',
				'fas fa-comment',
				'far fa-comment',
				'fas fa-comment-alt',
				'far fa-comment-alt',
				'fas fa-comment-dollar',
				'fas fa-comment-dots',
				'far fa-comment-dots',
				'fas fa-comment-medical',
				'fas fa-comment-slash',
				'fas fa-comments',
				'far fa-comments',
				'fas fa-comments-dollar',
				'fas fa-compact-disc',
				'fas fa-compass',
				'far fa-compass',
				'fas fa-compress',
				'fas fa-compress-alt',
				'fas fa-compress-arrows-alt',
				'fas fa-concierge-bell',
				'fab fa-confluence',
				'fab fa-connectdevelop',
				'fab fa-contao',
				'fas fa-cookie',
				'fas fa-cookie-bite',
				'fas fa-copy',
				'far fa-copy',
				'fas fa-copyright',
				'far fa-copyright',
				'fab fa-cotton-bureau',
				'fas fa-couch',
				'fab fa-cpanel',
				'fab fa-creative-commons',
				'fab fa-creative-commons-by',
				'fab fa-creative-commons-nc',
				'fab fa-creative-commons-nc-eu',
				'fab fa-creative-commons-nc-jp',
				'fab fa-creative-commons-nd',
				'fab fa-creative-commons-pd',
				'fab fa-creative-commons-pd-alt',
				'fab fa-creative-commons-remix',
				'fab fa-creative-commons-sa',
				'fab fa-creative-commons-sampling',
				'fab fa-creative-commons-sampling-plus',
				'fab fa-creative-commons-share',
				'fab fa-creative-commons-zero',
				'fas fa-credit-card',
				'far fa-credit-card',
				'fab fa-critical-role',
				'fas fa-crop',
				'fas fa-crop-alt',
				'fas fa-cross',
				'fas fa-crosshairs',
				'fas fa-crow',
				'fas fa-crown',
				'fas fa-crutch',
				'fab fa-css3',
				'fab fa-css3-alt',
				'fas fa-cube',
				'fas fa-cubes',
				'fas fa-cut',
				'fab fa-cuttlefish',
				'fab fa-d-and-d',
				'fab fa-d-and-d-beyond',
				'fab fa-dailymotion',
				'fab fa-dashcube',
				'fas fa-database',
				'fas fa-deaf',
				'fab fa-deezer',
				'fab fa-delicious',
				'fas fa-democrat',
				'fab fa-deploydog',
				'fab fa-deskpro',
				'fas fa-desktop',
				'fab fa-dev',
				'fab fa-deviantart',
				'fas fa-dharmachakra',
				'fab fa-dhl',
				'fas fa-diagnoses',
				'fab fa-diaspora',
				'fas fa-dice',
				'fas fa-dice-d20',
				'fas fa-dice-d6',
				'fas fa-dice-five',
				'fas fa-dice-four',
				'fas fa-dice-one',
				'fas fa-dice-six',
				'fas fa-dice-three',
				'fas fa-dice-two',
				'fab fa-digg',
				'fab fa-digital-ocean',
				'fas fa-digital-tachograph',
				'fas fa-directions',
				'fab fa-discord',
				'fab fa-discourse',
				'fas fa-disease',
				'fas fa-divide',
				'fas fa-dizzy',
				'far fa-dizzy',
				'fas fa-dna',
				'fab fa-dochub',
				'fab fa-docker',
				'fas fa-dog',
				'fas fa-dollar-sign',
				'fas fa-dolly',
				'fas fa-dolly-flatbed',
				'fas fa-donate',
				'fas fa-door-closed',
				'fas fa-door-open',
				'fas fa-dot-circle',
				'far fa-dot-circle',
				'fas fa-dove',
				'fas fa-download',
				'fab fa-draft2digital',
				'fas fa-drafting-compass',
				'fas fa-dragon',
				'fas fa-draw-polygon',
				'fab fa-dribbble',
				'fab fa-dribbble-square',
				'fab fa-dropbox',
				'fas fa-drum',
				'fas fa-drum-steelpan',
				'fas fa-drumstick-bite',
				'fab fa-drupal',
				'fas fa-dumbbell',
				'fas fa-dumpster',
				'fas fa-dumpster-fire',
				'fas fa-dungeon',
				'fab fa-dyalog',
				'fab fa-earlybirds',
				'fab fa-ebay',
				'fab fa-edge',
				'fab fa-edge-legacy',
				'fas fa-edit',
				'far fa-edit',
				'fas fa-egg',
				'fas fa-eject',
				'fab fa-elementor',
				'fas fa-ellipsis-h',
				'fas fa-ellipsis-v',
				'fab fa-ello',
				'fab fa-ember',
				'fab fa-empire',
				'fas fa-envelope',
				'far fa-envelope',
				'fas fa-envelope-open',
				'far fa-envelope-open',
				'fas fa-envelope-open-text',
				'fas fa-envelope-square',
				'fab fa-envira',
				'fas fa-equals',
				'fas fa-eraser',
				'fab fa-erlang',
				'fab fa-ethereum',
				'fas fa-ethernet',
				'fab fa-etsy',
				'fas fa-euro-sign',
				'fab fa-evernote',
				'fas fa-exchange-alt',
				'fas fa-exclamation',
				'fas fa-exclamation-circle',
				'fas fa-exclamation-triangle',
				'fas fa-expand',
				'fas fa-expand-alt',
				'fas fa-expand-arrows-alt',
				'fab fa-expeditedssl',
				'fas fa-external-link-alt',
				'fas fa-external-link-square-alt',
				'fas fa-eye',
				'far fa-eye',
				'fas fa-eye-dropper',
				'fas fa-eye-slash',
				'far fa-eye-slash',
				'fab fa-facebook',
				'fab fa-facebook-f',
				'fab fa-facebook-messenger',
				'fab fa-facebook-square',
				'fas fa-fan',
				'fab fa-fantasy-flight-games',
				'fas fa-fast-backward',
				'fas fa-fast-forward',
				'fas fa-faucet',
				'fas fa-fax',
				'fas fa-feather',
				'fas fa-feather-alt',
				'fab fa-fedex',
				'fab fa-fedora',
				'fas fa-female',
				'fas fa-fighter-jet',
				'fab fa-figma',
				'fas fa-file',
				'far fa-file',
				'fas fa-file-alt',
				'far fa-file-alt',
				'fas fa-file-archive',
				'far fa-file-archive',
				'fas fa-file-audio',
				'far fa-file-audio',
				'fas fa-file-code',
				'far fa-file-code',
				'fas fa-file-contract',
				'fas fa-file-csv',
				'fas fa-file-download',
				'fas fa-file-excel',
				'far fa-file-excel',
				'fas fa-file-export',
				'fas fa-file-image',
				'far fa-file-image',
				'fas fa-file-import',
				'fas fa-file-invoice',
				'fas fa-file-invoice-dollar',
				'fas fa-file-medical',
				'fas fa-file-medical-alt',
				'fas fa-file-pdf',
				'far fa-file-pdf',
				'fas fa-file-powerpoint',
				'far fa-file-powerpoint',
				'fas fa-file-prescription',
				'fas fa-file-signature',
				'fas fa-file-upload',
				'fas fa-file-video',
				'far fa-file-video',
				'fas fa-file-word',
				'far fa-file-word',
				'fas fa-fill',
				'fas fa-fill-drip',
				'fas fa-film',
				'fas fa-filter',
				'fas fa-fingerprint',
				'fas fa-fire',
				'fas fa-fire-alt',
				'fas fa-fire-extinguisher',
				'fab fa-firefox',
				'fab fa-firefox-browser',
				'fas fa-first-aid',
				'fab fa-first-order',
				'fab fa-first-order-alt',
				'fab fa-firstdraft',
				'fas fa-fish',
				'fas fa-fist-raised',
				'fas fa-flag',
				'far fa-flag',
				'fas fa-flag-checkered',
				'fas fa-flag-usa',
				'fas fa-flask',
				'fab fa-flickr',
				'fab fa-flipboard',
				'fas fa-flushed',
				'far fa-flushed',
				'fab fa-fly',
				'fas fa-folder',
				'far fa-folder',
				'fas fa-folder-minus',
				'fas fa-folder-open',
				'far fa-folder-open',
				'fas fa-folder-plus',
				'fas fa-font',
				'fab fa-font-awesome',
				'fab fa-font-awesome-alt',
				'fab fa-font-awesome-flag',
				'far fa-font-awesome-logo-full',
				'fas fa-font-awesome-logo-full',
				'fab fa-font-awesome-logo-full',
				'fab fa-fonticons',
				'fab fa-fonticons-fi',
				'fas fa-football-ball',
				'fab fa-fort-awesome',
				'fab fa-fort-awesome-alt',
				'fab fa-forumbee',
				'fas fa-forward',
				'fab fa-foursquare',
				'fab fa-free-code-camp',
				'fab fa-freebsd',
				'fas fa-frog',
				'fas fa-frown',
				'far fa-frown',
				'fas fa-frown-open',
				'far fa-frown-open',
				'fab fa-fulcrum',
				'fas fa-funnel-dollar',
				'fas fa-futbol',
				'far fa-futbol',
				'fab fa-galactic-republic',
				'fab fa-galactic-senate',
				'fas fa-gamepad',
				'fas fa-gas-pump',
				'fas fa-gavel',
				'fas fa-gem',
				'far fa-gem',
				'fas fa-genderless',
				'fab fa-get-pocket',
				'fab fa-gg',
				'fab fa-gg-circle',
				'fas fa-ghost',
				'fas fa-gift',
				'fas fa-gifts',
				'fab fa-git',
				'fab fa-git-alt',
				'fab fa-git-square',
				'fab fa-github',
				'fab fa-github-alt',
				'fab fa-github-square',
				'fab fa-gitkraken',
				'fab fa-gitlab',
				'fab fa-gitter',
				'fas fa-glass-cheers',
				'fas fa-glass-martini',
				'fas fa-glass-martini-alt',
				'fas fa-glass-whiskey',
				'fas fa-glasses',
				'fab fa-glide',
				'fab fa-glide-g',
				'fas fa-globe',
				'fas fa-globe-africa',
				'fas fa-globe-americas',
				'fas fa-globe-asia',
				'fas fa-globe-europe',
				'fab fa-gofore',
				'fas fa-golf-ball',
				'fab fa-goodreads',
				'fab fa-goodreads-g',
				'fab fa-google',
				'fab fa-google-drive',
				'fab fa-google-pay',
				'fab fa-google-play',
				'fab fa-google-plus',
				'fab fa-google-plus-g',
				'fab fa-google-plus-square',
				'fab fa-google-wallet',
				'fas fa-gopuram',
				'fas fa-graduation-cap',
				'fab fa-gratipay',
				'fab fa-grav',
				'fas fa-greater-than',
				'fas fa-greater-than-equal',
				'fas fa-grimace',
				'far fa-grimace',
				'fas fa-grin',
				'far fa-grin',
				'fas fa-grin-alt',
				'far fa-grin-alt',
				'fas fa-grin-beam',
				'far fa-grin-beam',
				'fas fa-grin-beam-sweat',
				'far fa-grin-beam-sweat',
				'fas fa-grin-hearts',
				'far fa-grin-hearts',
				'fas fa-grin-squint',
				'far fa-grin-squint',
				'fas fa-grin-squint-tears',
				'far fa-grin-squint-tears',
				'fas fa-grin-stars',
				'far fa-grin-stars',
				'fas fa-grin-tears',
				'far fa-grin-tears',
				'fas fa-grin-tongue',
				'far fa-grin-tongue',
				'fas fa-grin-tongue-squint',
				'far fa-grin-tongue-squint',
				'fas fa-grin-tongue-wink',
				'far fa-grin-tongue-wink',
				'fas fa-grin-wink',
				'far fa-grin-wink',
				'fas fa-grip-horizontal',
				'fas fa-grip-lines',
				'fas fa-grip-lines-vertical',
				'fas fa-grip-vertical',
				'fab fa-gripfire',
				'fab fa-grunt',
				'fab fa-guilded',
				'fas fa-guitar',
				'fab fa-gulp',
				'fas fa-h-square',
				'fab fa-hacker-news',
				'fab fa-hacker-news-square',
				'fab fa-hackerrank',
				'fas fa-hamburger',
				'fas fa-hammer',
				'fas fa-hamsa',
				'fas fa-hand-holding',
				'fas fa-hand-holding-heart',
				'fas fa-hand-holding-medical',
				'fas fa-hand-holding-usd',
				'fas fa-hand-holding-water',
				'fas fa-hand-lizard',
				'far fa-hand-lizard',
				'fas fa-hand-middle-finger',
				'fas fa-hand-paper',
				'far fa-hand-paper',
				'fas fa-hand-peace',
				'far fa-hand-peace',
				'fas fa-hand-point-down',
				'far fa-hand-point-down',
				'fas fa-hand-point-left',
				'far fa-hand-point-left',
				'fas fa-hand-point-right',
				'far fa-hand-point-right',
				'fas fa-hand-point-up',
				'far fa-hand-point-up',
				'fas fa-hand-pointer',
				'far fa-hand-pointer',
				'fas fa-hand-rock',
				'far fa-hand-rock',
				'fas fa-hand-scissors',
				'far fa-hand-scissors',
				'fas fa-hand-sparkles',
				'fas fa-hand-spock',
				'far fa-hand-spock',
				'fas fa-hands',
				'fas fa-hands-helping',
				'fas fa-hands-wash',
				'fas fa-handshake',
				'far fa-handshake',
				'fas fa-handshake-alt-slash',
				'fas fa-handshake-slash',
				'fas fa-hanukiah',
				'fas fa-hard-hat',
				'fas fa-hashtag',
				'fas fa-hat-cowboy',
				'fas fa-hat-cowboy-side',
				'fas fa-hat-wizard',
				'fas fa-hdd',
				'far fa-hdd',
				'fas fa-head-side-cough',
				'fas fa-head-side-cough-slash',
				'fas fa-head-side-mask',
				'fas fa-head-side-virus',
				'fas fa-heading',
				'fas fa-headphones',
				'fas fa-headphones-alt',
				'fas fa-headset',
				'fas fa-heart',
				'far fa-heart',
				'fas fa-heart-broken',
				'fas fa-heartbeat',
				'fas fa-helicopter',
				'fas fa-highlighter',
				'fas fa-hiking',
				'fas fa-hippo',
				'fab fa-hips',
				'fab fa-hire-a-helper',
				'fas fa-history',
				'fab fa-hive',
				'fas fa-hockey-puck',
				'fas fa-holly-berry',
				'fas fa-home',
				'fab fa-hooli',
				'fab fa-hornbill',
				'fas fa-horse',
				'fas fa-horse-head',
				'fas fa-hospital',
				'far fa-hospital',
				'fas fa-hospital-alt',
				'fas fa-hospital-symbol',
				'fas fa-hospital-user',
				'fas fa-hot-tub',
				'fas fa-hotdog',
				'fas fa-hotel',
				'fab fa-hotjar',
				'fas fa-hourglass',
				'far fa-hourglass',
				'fas fa-hourglass-end',
				'fas fa-hourglass-half',
				'fas fa-hourglass-start',
				'fas fa-house-damage',
				'fas fa-house-user',
				'fab fa-houzz',
				'fas fa-hryvnia',
				'fab fa-html5',
				'fab fa-hubspot',
				'fas fa-i-cursor',
				'fas fa-ice-cream',
				'fas fa-icicles',
				'fas fa-icons',
				'fas fa-id-badge',
				'far fa-id-badge',
				'fas fa-id-card',
				'far fa-id-card',
				'fas fa-id-card-alt',
				'fab fa-ideal',
				'fas fa-igloo',
				'fas fa-image',
				'far fa-image',
				'fas fa-images',
				'far fa-images',
				'fab fa-imdb',
				'fas fa-inbox',
				'fas fa-indent',
				'fas fa-industry',
				'fas fa-infinity',
				'fas fa-info',
				'fas fa-info-circle',
				'fab fa-innosoft',
				'fab fa-instagram',
				'fab fa-instagram-square',
				'fab fa-instalod',
				'fab fa-intercom',
				'fab fa-internet-explorer',
				'fab fa-invision',
				'fab fa-ioxhost',
				'fas fa-italic',
				'fab fa-itch-io',
				'fab fa-itunes',
				'fab fa-itunes-note',
				'fab fa-java',
				'fas fa-jedi',
				'fab fa-jedi-order',
				'fab fa-jenkins',
				'fab fa-jira',
				'fab fa-joget',
				'fas fa-joint',
				'fab fa-joomla',
				'fas fa-journal-whills',
				'fab fa-js',
				'fab fa-js-square',
				'fab fa-jsfiddle',
				'fas fa-kaaba',
				'fab fa-kaggle',
				'fas fa-key',
				'fab fa-keybase',
				'fas fa-keyboard',
				'far fa-keyboard',
				'fab fa-keycdn',
				'fas fa-khanda',
				'fab fa-kickstarter',
				'fab fa-kickstarter-k',
				'fas fa-kiss',
				'far fa-kiss',
				'fas fa-kiss-beam',
				'far fa-kiss-beam',
				'fas fa-kiss-wink-heart',
				'far fa-kiss-wink-heart',
				'fas fa-kiwi-bird',
				'fab fa-korvue',
				'fas fa-landmark',
				'fas fa-language',
				'fas fa-laptop',
				'fas fa-laptop-code',
				'fas fa-laptop-house',
				'fas fa-laptop-medical',
				'fab fa-laravel',
				'fab fa-lastfm',
				'fab fa-lastfm-square',
				'fas fa-laugh',
				'far fa-laugh',
				'fas fa-laugh-beam',
				'far fa-laugh-beam',
				'fas fa-laugh-squint',
				'far fa-laugh-squint',
				'fas fa-laugh-wink',
				'far fa-laugh-wink',
				'fas fa-layer-group',
				'fas fa-leaf',
				'fab fa-leanpub',
				'fas fa-lemon',
				'far fa-lemon',
				'fab fa-less',
				'fas fa-less-than',
				'fas fa-less-than-equal',
				'fas fa-level-down-alt',
				'fas fa-level-up-alt',
				'fas fa-life-ring',
				'far fa-life-ring',
				'fas fa-lightbulb',
				'far fa-lightbulb',
				'fab fa-line',
				'fas fa-link',
				'fab fa-linkedin',
				'fab fa-linkedin-in',
				'fab fa-linode',
				'fab fa-linux',
				'fas fa-lira-sign',
				'fas fa-list',
				'fas fa-list-alt',
				'far fa-list-alt',
				'fas fa-list-ol',
				'fas fa-list-ul',
				'fas fa-location-arrow',
				'fas fa-lock',
				'fas fa-lock-open',
				'fas fa-long-arrow-alt-down',
				'fas fa-long-arrow-alt-left',
				'fas fa-long-arrow-alt-right',
				'fas fa-long-arrow-alt-up',
				'fas fa-low-vision',
				'fas fa-luggage-cart',
				'fas fa-lungs',
				'fas fa-lungs-virus',
				'fab fa-lyft',
				'fab fa-magento',
				'fas fa-magic',
				'fas fa-magnet',
				'fas fa-mail-bulk',
				'fab fa-mailchimp',
				'fas fa-male',
				'fab fa-mandalorian',
				'fas fa-map',
				'far fa-map',
				'fas fa-map-marked',
				'fas fa-map-marked-alt',
				'fas fa-map-marker',
				'fas fa-map-marker-alt',
				'fas fa-map-pin',
				'fas fa-map-signs',
				'fab fa-markdown',
				'fas fa-marker',
				'fas fa-mars',
				'fas fa-mars-double',
				'fas fa-mars-stroke',
				'fas fa-mars-stroke-h',
				'fas fa-mars-stroke-v',
				'fas fa-mask',
				'fab fa-mastodon',
				'fab fa-maxcdn',
				'fab fa-mdb',
				'fas fa-medal',
				'fab fa-medapps',
				'fab fa-medium',
				'fab fa-medium-m',
				'fas fa-medkit',
				'fab fa-medrt',
				'fab fa-meetup',
				'fab fa-megaport',
				'fas fa-meh',
				'far fa-meh',
				'fas fa-meh-blank',
				'far fa-meh-blank',
				'fas fa-meh-rolling-eyes',
				'far fa-meh-rolling-eyes',
				'fas fa-memory',
				'fab fa-mendeley',
				'fas fa-menorah',
				'fas fa-mercury',
				'fas fa-meteor',
				'fab fa-microblog',
				'fas fa-microchip',
				'fas fa-microphone',
				'fas fa-microphone-alt',
				'fas fa-microphone-alt-slash',
				'fas fa-microphone-slash',
				'fas fa-microscope',
				'fab fa-microsoft',
				'fas fa-minus',
				'fas fa-minus-circle',
				'fas fa-minus-square',
				'far fa-minus-square',
				'fas fa-mitten',
				'fab fa-mix',
				'fab fa-mixcloud',
				'fab fa-mixer',
				'fab fa-mizuni',
				'fas fa-mobile',
				'fas fa-mobile-alt',
				'fab fa-modx',
				'fab fa-monero',
				'fas fa-money-bill',
				'fas fa-money-bill-alt',
				'far fa-money-bill-alt',
				'fas fa-money-bill-wave',
				'fas fa-money-bill-wave-alt',
				'fas fa-money-check',
				'fas fa-money-check-alt',
				'fas fa-monument',
				'fas fa-moon',
				'far fa-moon',
				'fas fa-mortar-pestle',
				'fas fa-mosque',
				'fas fa-motorcycle',
				'fas fa-mountain',
				'fas fa-mouse',
				'fas fa-mouse-pointer',
				'fas fa-mug-hot',
				'fas fa-music',
				'fab fa-napster',
				'fab fa-neos',
				'fas fa-network-wired',
				'fas fa-neuter',
				'fas fa-newspaper',
				'far fa-newspaper',
				'fab fa-nimblr',
				'fab fa-node',
				'fab fa-node-js',
				'fas fa-not-equal',
				'fas fa-notes-medical',
				'fab fa-npm',
				'fab fa-ns8',
				'fab fa-nutritionix',
				'fas fa-object-group',
				'far fa-object-group',
				'fas fa-object-ungroup',
				'far fa-object-ungroup',
				'fab fa-octopus-deploy',
				'fab fa-odnoklassniki',
				'fab fa-odnoklassniki-square',
				'fas fa-oil-can',
				'fab fa-old-republic',
				'fas fa-om',
				'fab fa-opencart',
				'fab fa-openid',
				'fab fa-opera',
				'fab fa-optin-monster',
				'fab fa-orcid',
				'fab fa-osi',
				'fas fa-otter',
				'fas fa-outdent',
				'fab fa-page4',
				'fab fa-pagelines',
				'fas fa-pager',
				'fas fa-paint-brush',
				'fas fa-paint-roller',
				'fas fa-palette',
				'fab fa-palfed',
				'fas fa-pallet',
				'fas fa-paper-plane',
				'far fa-paper-plane',
				'fas fa-paperclip',
				'fas fa-parachute-box',
				'fas fa-paragraph',
				'fas fa-parking',
				'fas fa-passport',
				'fas fa-pastafarianism',
				'fas fa-paste',
				'fab fa-patreon',
				'fas fa-pause',
				'fas fa-pause-circle',
				'far fa-pause-circle',
				'fas fa-paw',
				'fab fa-paypal',
				'fas fa-peace',
				'fas fa-pen',
				'fas fa-pen-alt',
				'fas fa-pen-fancy',
				'fas fa-pen-nib',
				'fas fa-pen-square',
				'fas fa-pencil-alt',
				'fas fa-pencil-ruler',
				'fab fa-penny-arcade',
				'fas fa-people-arrows',
				'fas fa-people-carry',
				'fas fa-pepper-hot',
				'fab fa-perbyte',
				'fas fa-percent',
				'fas fa-percentage',
				'fab fa-periscope',
				'fas fa-person-booth',
				'fab fa-phabricator',
				'fab fa-phoenix-framework',
				'fab fa-phoenix-squadron',
				'fas fa-phone',
				'fas fa-phone-alt',
				'fas fa-phone-slash',
				'fas fa-phone-square',
				'fas fa-phone-square-alt',
				'fas fa-phone-volume',
				'fas fa-photo-video',
				'fab fa-php',
				'fab fa-pied-piper',
				'fab fa-pied-piper-alt',
				'fab fa-pied-piper-hat',
				'fab fa-pied-piper-pp',
				'fab fa-pied-piper-square',
				'fas fa-piggy-bank',
				'fas fa-pills',
				'fab fa-pinterest',
				'fab fa-pinterest-p',
				'fab fa-pinterest-square',
				'fas fa-pizza-slice',
				'fas fa-place-of-worship',
				'fas fa-plane',
				'fas fa-plane-arrival',
				'fas fa-plane-departure',
				'fas fa-plane-slash',
				'fas fa-play',
				'fas fa-play-circle',
				'far fa-play-circle',
				'fab fa-playstation',
				'fas fa-plug',
				'fas fa-plus',
				'fas fa-plus-circle',
				'fas fa-plus-square',
				'far fa-plus-square',
				'fas fa-podcast',
				'fas fa-poll',
				'fas fa-poll-h',
				'fas fa-poo',
				'fas fa-poo-storm',
				'fas fa-poop',
				'fas fa-portrait',
				'fas fa-pound-sign',
				'fas fa-power-off',
				'fas fa-pray',
				'fas fa-praying-hands',
				'fas fa-prescription',
				'fas fa-prescription-bottle',
				'fas fa-prescription-bottle-alt',
				'fas fa-print',
				'fas fa-procedures',
				'fab fa-product-hunt',
				'fas fa-project-diagram',
				'fas fa-pump-medical',
				'fas fa-pump-soap',
				'fab fa-pushed',
				'fas fa-puzzle-piece',
				'fab fa-python',
				'fab fa-qq',
				'fas fa-qrcode',
				'fas fa-question',
				'fas fa-question-circle',
				'far fa-question-circle',
				'fas fa-quidditch',
				'fab fa-quinscape',
				'fab fa-quora',
				'fas fa-quote-left',
				'fas fa-quote-right',
				'fas fa-quran',
				'fab fa-r-project',
				'fas fa-radiation',
				'fas fa-radiation-alt',
				'fas fa-rainbow',
				'fas fa-random',
				'fab fa-raspberry-pi',
				'fab fa-ravelry',
				'fab fa-react',
				'fab fa-reacteurope',
				'fab fa-readme',
				'fab fa-rebel',
				'fas fa-receipt',
				'fas fa-record-vinyl',
				'fas fa-recycle',
				'fab fa-red-river',
				'fab fa-reddit',
				'fab fa-reddit-alien',
				'fab fa-reddit-square',
				'fab fa-redhat',
				'fas fa-redo',
				'fas fa-redo-alt',
				'fas fa-registered',
				'far fa-registered',
				'fas fa-remove-format',
				'fab fa-renren',
				'fas fa-reply',
				'fas fa-reply-all',
				'fab fa-replyd',
				'fas fa-republican',
				'fab fa-researchgate',
				'fab fa-resolving',
				'fas fa-restroom',
				'fas fa-retweet',
				'fab fa-rev',
				'fas fa-ribbon',
				'fas fa-ring',
				'fas fa-road',
				'fas fa-robot',
				'fas fa-rocket',
				'fab fa-rocketchat',
				'fab fa-rockrms',
				'fas fa-route',
				'fas fa-rss',
				'fas fa-rss-square',
				'fas fa-ruble-sign',
				'fas fa-ruler',
				'fas fa-ruler-combined',
				'fas fa-ruler-horizontal',
				'fas fa-ruler-vertical',
				'fas fa-running',
				'fas fa-rupee-sign',
				'fab fa-rust',
				'fas fa-sad-cry',
				'far fa-sad-cry',
				'fas fa-sad-tear',
				'far fa-sad-tear',
				'fab fa-safari',
				'fab fa-salesforce',
				'fab fa-sass',
				'fas fa-satellite',
				'fas fa-satellite-dish',
				'fas fa-save',
				'far fa-save',
				'fab fa-schlix',
				'fas fa-school',
				'fas fa-screwdriver',
				'fab fa-scribd',
				'fas fa-scroll',
				'fas fa-sd-card',
				'fas fa-search',
				'fas fa-search-dollar',
				'fas fa-search-location',
				'fas fa-search-minus',
				'fas fa-search-plus',
				'fab fa-searchengin',
				'fas fa-seedling',
				'fab fa-sellcast',
				'fab fa-sellsy',
				'fas fa-server',
				'fab fa-servicestack',
				'fas fa-shapes',
				'fas fa-share',
				'fas fa-share-alt',
				'fas fa-share-alt-square',
				'fas fa-share-square',
				'far fa-share-square',
				'fas fa-shekel-sign',
				'fas fa-shield-alt',
				'fas fa-shield-virus',
				'fas fa-ship',
				'fas fa-shipping-fast',
				'fab fa-shirtsinbulk',
				'fas fa-shoe-prints',
				'fab fa-shopify',
				'fas fa-shopping-bag',
				'fas fa-shopping-basket',
				'fas fa-shopping-cart',
				'fab fa-shopware',
				'fas fa-shower',
				'fas fa-shuttle-van',
				'fas fa-sign',
				'fas fa-sign-in-alt',
				'fas fa-sign-language',
				'fas fa-sign-out-alt',
				'fas fa-signal',
				'fas fa-signature',
				'fas fa-sim-card',
				'fab fa-simplybuilt',
				'fas fa-sink',
				'fab fa-sistrix',
				'fas fa-sitemap',
				'fab fa-sith',
				'fas fa-skating',
				'fab fa-sketch',
				'fas fa-skiing',
				'fas fa-skiing-nordic',
				'fas fa-skull',
				'fas fa-skull-crossbones',
				'fab fa-skyatlas',
				'fab fa-skype',
				'fab fa-slack',
				'fab fa-slack-hash',
				'fas fa-slash',
				'fas fa-sleigh',
				'fas fa-sliders-h',
				'fab fa-slideshare',
				'fas fa-smile',
				'far fa-smile',
				'fas fa-smile-beam',
				'far fa-smile-beam',
				'fas fa-smile-wink',
				'far fa-smile-wink',
				'fas fa-smog',
				'fas fa-smoking',
				'fas fa-smoking-ban',
				'fas fa-sms',
				'fab fa-snapchat',
				'fab fa-snapchat-ghost',
				'fab fa-snapchat-square',
				'fas fa-snowboarding',
				'fas fa-snowflake',
				'far fa-snowflake',
				'fas fa-snowman',
				'fas fa-snowplow',
				'fas fa-soap',
				'fas fa-socks',
				'fas fa-solar-panel',
				'fas fa-sort',
				'fas fa-sort-alpha-down',
				'fas fa-sort-alpha-down-alt',
				'fas fa-sort-alpha-up',
				'fas fa-sort-alpha-up-alt',
				'fas fa-sort-amount-down',
				'fas fa-sort-amount-down-alt',
				'fas fa-sort-amount-up',
				'fas fa-sort-amount-up-alt',
				'fas fa-sort-down',
				'fas fa-sort-numeric-down',
				'fas fa-sort-numeric-down-alt',
				'fas fa-sort-numeric-up',
				'fas fa-sort-numeric-up-alt',
				'fas fa-sort-up',
				'fab fa-soundcloud',
				'fab fa-sourcetree',
				'fas fa-spa',
				'fas fa-space-shuttle',
				'fab fa-speakap',
				'fab fa-speaker-deck',
				'fas fa-spell-check',
				'fas fa-spider',
				'fas fa-spinner',
				'fas fa-splotch',
				'fab fa-spotify',
				'fas fa-spray-can',
				'fas fa-square',
				'far fa-square',
				'fas fa-square-full',
				'fas fa-square-root-alt',
				'fab fa-squarespace',
				'fab fa-stack-exchange',
				'fab fa-stack-overflow',
				'fab fa-stackpath',
				'fas fa-stamp',
				'fas fa-star',
				'far fa-star',
				'fas fa-star-and-crescent',
				'fas fa-star-half',
				'far fa-star-half',
				'fas fa-star-half-alt',
				'fas fa-star-of-david',
				'fas fa-star-of-life',
				'fab fa-staylinked',
				'fab fa-steam',
				'fab fa-steam-square',
				'fab fa-steam-symbol',
				'fas fa-step-backward',
				'fas fa-step-forward',
				'fas fa-stethoscope',
				'fab fa-sticker-mule',
				'fas fa-sticky-note',
				'far fa-sticky-note',
				'fas fa-stop',
				'fas fa-stop-circle',
				'far fa-stop-circle',
				'fas fa-stopwatch',
				'fas fa-stopwatch-20',
				'fas fa-store',
				'fas fa-store-alt',
				'fas fa-store-alt-slash',
				'fas fa-store-slash',
				'fab fa-strava',
				'fas fa-stream',
				'fas fa-street-view',
				'fas fa-strikethrough',
				'fab fa-stripe',
				'fab fa-stripe-s',
				'fas fa-stroopwafel',
				'fab fa-studiovinari',
				'fab fa-stumbleupon',
				'fab fa-stumbleupon-circle',
				'fas fa-subscript',
				'fas fa-subway',
				'fas fa-suitcase',
				'fas fa-suitcase-rolling',
				'fas fa-sun',
				'far fa-sun',
				'fab fa-superpowers',
				'fas fa-superscript',
				'fab fa-supple',
				'fas fa-surprise',
				'far fa-surprise',
				'fab fa-suse',
				'fas fa-swatchbook',
				'fab fa-swift',
				'fas fa-swimmer',
				'fas fa-swimming-pool',
				'fab fa-symfony',
				'fas fa-synagogue',
				'fas fa-sync',
				'fas fa-sync-alt',
				'fas fa-syringe',
				'fas fa-table',
				'fas fa-table-tennis',
				'fas fa-tablet',
				'fas fa-tablet-alt',
				'fas fa-tablets',
				'fas fa-tachometer-alt',
				'fas fa-tag',
				'fas fa-tags',
				'fas fa-tape',
				'fas fa-tasks',
				'fas fa-taxi',
				'fab fa-teamspeak',
				'fas fa-teeth',
				'fas fa-teeth-open',
				'fab fa-telegram',
				'fab fa-telegram-plane',
				'fas fa-temperature-high',
				'fas fa-temperature-low',
				'fab fa-tencent-weibo',
				'fas fa-tenge',
				'fas fa-terminal',
				'fas fa-text-height',
				'fas fa-text-width',
				'fas fa-th',
				'fas fa-th-large',
				'fas fa-th-list',
				'fab fa-the-red-yeti',
				'fas fa-theater-masks',
				'fab fa-themeco',
				'fab fa-themeisle',
				'fas fa-thermometer',
				'fas fa-thermometer-empty',
				'fas fa-thermometer-full',
				'fas fa-thermometer-half',
				'fas fa-thermometer-quarter',
				'fas fa-thermometer-three-quarters',
				'fab fa-think-peaks',
				'fas fa-thumbs-down',
				'far fa-thumbs-down',
				'fas fa-thumbs-up',
				'far fa-thumbs-up',
				'fas fa-thumbtack',
				'fas fa-ticket-alt',
				'fab fa-tiktok',
				'fas fa-times',
				'fas fa-times-circle',
				'far fa-times-circle',
				'fas fa-tint',
				'fas fa-tint-slash',
				'fas fa-tired',
				'far fa-tired',
				'fas fa-toggle-off',
				'fas fa-toggle-on',
				'fas fa-toilet',
				'fas fa-toilet-paper',
				'fas fa-toilet-paper-slash',
				'fas fa-toolbox',
				'fas fa-tools',
				'fas fa-tooth',
				'fas fa-torah',
				'fas fa-torii-gate',
				'fas fa-tractor',
				'fab fa-trade-federation',
				'fas fa-trademark',
				'fas fa-traffic-light',
				'fas fa-trailer',
				'fas fa-train',
				'fas fa-tram',
				'fas fa-transgender',
				'fas fa-transgender-alt',
				'fas fa-trash',
				'fas fa-trash-alt',
				'far fa-trash-alt',
				'fas fa-trash-restore',
				'fas fa-trash-restore-alt',
				'fas fa-tree',
				'fab fa-trello',
				'fas fa-trophy',
				'fas fa-truck',
				'fas fa-truck-loading',
				'fas fa-truck-monster',
				'fas fa-truck-moving',
				'fas fa-truck-pickup',
				'fas fa-tshirt',
				'fas fa-tty',
				'fab fa-tumblr',
				'fab fa-tumblr-square',
				'fas fa-tv',
				'fab fa-twitch',
				'fab fa-twitter',
				'fab fa-twitter-square',
				'fab fa-typo3',
				'fab fa-uber',
				'fab fa-ubuntu',
				'fab fa-uikit',
				'fab fa-umbraco',
				'fas fa-umbrella',
				'fas fa-umbrella-beach',
				'fab fa-uncharted',
				'fas fa-underline',
				'fas fa-undo',
				'fas fa-undo-alt',
				'fab fa-uniregistry',
				'fab fa-unity',
				'fas fa-universal-access',
				'fas fa-university',
				'fas fa-unlink',
				'fas fa-unlock',
				'fas fa-unlock-alt',
				'fab fa-unsplash',
				'fab fa-untappd',
				'fas fa-upload',
				'fab fa-ups',
				'fab fa-usb',
				'fas fa-user',
				'far fa-user',
				'fas fa-user-alt',
				'fas fa-user-alt-slash',
				'fas fa-user-astronaut',
				'fas fa-user-check',
				'fas fa-user-circle',
				'far fa-user-circle',
				'fas fa-user-clock',
				'fas fa-user-cog',
				'fas fa-user-edit',
				'fas fa-user-friends',
				'fas fa-user-graduate',
				'fas fa-user-injured',
				'fas fa-user-lock',
				'fas fa-user-md',
				'fas fa-user-minus',
				'fas fa-user-ninja',
				'fas fa-user-nurse',
				'fas fa-user-plus',
				'fas fa-user-secret',
				'fas fa-user-shield',
				'fas fa-user-slash',
				'fas fa-user-tag',
				'fas fa-user-tie',
				'fas fa-user-times',
				'fas fa-users',
				'fas fa-users-cog',
				'fas fa-users-slash',
				'fab fa-usps',
				'fab fa-ussunnah',
				'fas fa-utensil-spoon',
				'fas fa-utensils',
				'fab fa-vaadin',
				'fas fa-vector-square',
				'fas fa-venus',
				'fas fa-venus-double',
				'fas fa-venus-mars',
				'fas fa-vest',
				'fas fa-vest-patches',
				'fab fa-viacoin',
				'fab fa-viadeo',
				'fab fa-viadeo-square',
				'fas fa-vial',
				'fas fa-vials',
				'fab fa-viber',
				'fas fa-video',
				'fas fa-video-slash',
				'fas fa-vihara',
				'fab fa-vimeo',
				'fab fa-vimeo-square',
				'fab fa-vimeo-v',
				'fab fa-vine',
				'fas fa-virus',
				'fas fa-virus-slash',
				'fas fa-viruses',
				'fab fa-vk',
				'fab fa-vnv',
				'fas fa-voicemail',
				'fas fa-volleyball-ball',
				'fas fa-volume-down',
				'fas fa-volume-mute',
				'fas fa-volume-off',
				'fas fa-volume-up',
				'fas fa-vote-yea',
				'fas fa-vr-cardboard',
				'fab fa-vuejs',
				'fas fa-walking',
				'fas fa-wallet',
				'fas fa-warehouse',
				'fab fa-watchman-monitoring',
				'fas fa-water',
				'fas fa-wave-square',
				'fab fa-waze',
				'fab fa-weebly',
				'fab fa-weibo',
				'fas fa-weight',
				'fas fa-weight-hanging',
				'fab fa-weixin',
				'fab fa-whatsapp',
				'fab fa-whatsapp-square',
				'fas fa-wheelchair',
				'fab fa-whmcs',
				'fas fa-wifi',
				'fab fa-wikipedia-w',
				'fas fa-wind',
				'fas fa-window-close',
				'far fa-window-close',
				'fas fa-window-maximize',
				'far fa-window-maximize',
				'fas fa-window-minimize',
				'far fa-window-minimize',
				'fas fa-window-restore',
				'far fa-window-restore',
				'fab fa-windows',
				'fas fa-wine-bottle',
				'fas fa-wine-glass',
				'fas fa-wine-glass-alt',
				'fab fa-wix',
				'fab fa-wizards-of-the-coast',
				'fab fa-wodu',
				'fab fa-wolf-pack-battalion',
				'fas fa-won-sign',
				'fab fa-wordpress',
				'fab fa-wordpress-simple',
				'fab fa-wpbeginner',
				'fab fa-wpexplorer',
				'fab fa-wpforms',
				'fab fa-wpressr',
				'fas fa-wrench',
				'fas fa-x-ray',
				'fab fa-xbox',
				'fab fa-xing',
				'fab fa-xing-square',
				'fab fa-y-combinator',
				'fab fa-yahoo',
				'fab fa-yammer',
				'fab fa-yandex',
				'fab fa-yandex-international',
				'fab fa-yarn',
				'fab fa-yelp',
				'fas fa-yen-sign',
				'fas fa-yin-yang',
				'fab fa-yoast',
				'fab fa-youtube',
				'fab fa-youtube-square',
				'fab fa-zhihu'
			);

			return $icons;
		}

		public function fontawesome_six_icons() {
			$icons = array(
				'fa-solid fa-fill-drip',

				'fa-solid fa-arrows-to-circle',

				'fa-solid fa-chevron-circle-right fa-circle-chevron-right',

				'fa-solid fa-at',

				'fa-solid fa-trash-alt fa-trash-can',

				'fa-solid fa-text-height',

				'fa-solid fa-user-times fa-user-xmark',

				'fa-solid fa-stethoscope',

				'fa-solid fa-comment-alt fa-message',

				'fa-solid fa-info',

				'fa-solid fa-compress-alt fa-down-left-and-up-right-to-center',

				'fa-solid fa-explosion',

				'fa-solid fa-file-alt fa-file-lines fa-file-text',

				'fa-solid fa-wave-square',

				'fa-solid fa-ring',

				'fa-solid fa-building-un',

				'fa-solid fa-dice-three',

				'fa-solid fa-calendar-alt fa-calendar-days',

				'fa-solid fa-anchor-circle-check',

				'fa-solid fa-building-circle-arrow-right',

				'fa-solid fa-volleyball-ball fa-volleyball',

				'fa-solid fa-arrows-up-to-line',

				'fa-solid fa-sort-desc fa-sort-down',

				'fa-solid fa-circle-minus fa-minus-circle',

				'fa-solid fa-door-open',

				'fa-solid fa-right-from-bracket fa-sign-out-alt',

				'fa-solid fa-atom',

				'fa-solid fa-soap',

				'fa-solid fa-heart-music-camera-bolt fa-icons',

				'fa-solid fa-microphone-alt-slash fa-microphone-lines-slash',

				'fa-solid fa-bridge-circle-check',

				'fa-solid fa-pump-medical',

				'fa-solid fa-fingerprint',

				'fa-solid fa-hand-point-right',

				'fa-solid fa-magnifying-glass-location fa-search-location',

				'fa-solid fa-forward-step fa-step-forward',

				'fa-solid fa-face-smile-beam fa-smile-beam',

				'fa-solid fa-flag-checkered',

				'fa-solid fa-football-ball fa-football',

				'fa-solid fa-school-circle-exclamation',

				'fa-solid fa-crop',

				'fa-solid fa-angle-double-down fa-angles-down',

				'fa-solid fa-users-rectangle',

				'fa-solid fa-people-roof',

				'fa-solid fa-people-line',

				'fa-solid fa-beer-mug-empty fa-beer',

				'fa-solid fa-diagram-predecessor',

				'fa-solid fa-arrow-up-long fa-long-arrow-up',

				'fa-solid fa-burn fa-fire-flame-simple',

				'fa-solid fa-male fa-person',

				'fa-solid fa-laptop',

				'fa-solid fa-file-csv',

				'fa-solid fa-menorah',

				'fa-solid fa-truck-plane',

				'fa-solid fa-record-vinyl',

				'fa-solid fa-face-grin-stars fa-grin-stars',

				'fa-solid fa-bong',

				'fa-solid fa-pastafarianism fa-spaghetti-monster-flying',

				'fa-solid fa-arrow-down-up-across-line',

				'fa-solid fa-spoon fa-utensil-spoon',

				'fa-solid fa-jar-wheat',

				'fa-solid fa-envelopes-bulk fa-mail-bulk',

				'fa-solid fa-file-circle-exclamation',

				'fa-solid fa-circle-h fa-hospital-symbol',

				'fa-solid fa-pager',

				'fa-solid fa-address-book fa-contact-book',

				'fa-solid fa-strikethrough',

				'fa-solid fa-k',

				'fa-solid fa-landmark-flag',

				'fa-solid fa-pencil-alt fa-pencil',

				'fa-solid fa-backward',

				'fa-solid fa-caret-right',

				'fa-solid fa-comments',

				'fa-solid fa-file-clipboard fa-paste',

				'fa-solid fa-code-pull-request',

				'fa-solid fa-clipboard-list',

				'fa-solid fa-truck-loading fa-truck-ramp-box',

				'fa-solid fa-user-check',

				'fa-solid fa-vial-virus',

				'fa-solid fa-sheet-plastic',

				'fa-solid fa-blog',

				'fa-solid fa-user-ninja',

				'fa-solid fa-person-arrow-up-from-line',

				'fa-solid fa-scroll-torah fa-torah',

				'fa-solid fa-broom-ball fa-quidditch-broom-ball fa-quidditch',

				'fa-solid fa-toggle-off',

				'fa-solid fa-archive fa-box-archive',

				'fa-solid fa-person-drowning',

				'fa-solid fa-arrow-down-9-1 fa-sort-numeric-desc fa-sort-numeric-down-alt',

				'fa-solid fa-face-grin-tongue-squint fa-grin-tongue-squint',

				'fa-solid fa-spray-can',

				'fa-solid fa-truck-monster',

				'fa-solid fa-w',

				'fa-solid fa-earth-africa fa-globe-africa',

				'fa-solid fa-rainbow',

				'fa-solid fa-circle-notch',

				'fa-solid fa-tablet-alt fa-tablet-screen-button',

				'fa-solid fa-paw',

				'fa-solid fa-cloud',

				'fa-solid fa-trowel-bricks',

				'fa-solid fa-face-flushed fa-flushed',

				'fa-solid fa-hospital-user',

				'fa-solid fa-tent-arrow-left-right',

				'fa-solid fa-gavel fa-legal',

				'fa-solid fa-binoculars',

				'fa-solid fa-microphone-slash',

				'fa-solid fa-box-tissue',

				'fa-solid fa-motorcycle',

				'fa-solid fa-bell-concierge fa-concierge-bell',

				'fa-solid fa-pen-ruler fa-pencil-ruler',

				'fa-solid fa-people-arrows-left-right fa-people-arrows',

				'fa-solid fa-mars-and-venus-burst',

				'fa-solid fa-caret-square-right fa-square-caret-right',

				'fa-solid fa-cut fa-scissors',

				'fa-solid fa-sun-plant-wilt',

				'fa-solid fa-toilets-portable',

				'fa-solid fa-hockey-puck',

				'fa-solid fa-table',

				'fa-solid fa-magnifying-glass-arrow-right',

				'fa-solid fa-digital-tachograph fa-tachograph-digital',

				'fa-solid fa-users-slash',

				'fa-solid fa-clover',

				'fa-solid fa-mail-reply fa-reply',

				'fa-solid fa-star-and-crescent',

				'fa-solid fa-house-fire',

				'fa-solid fa-minus-square fa-square-minus',

				'fa-solid fa-helicopter',

				'fa-solid fa-compass',

				'fa-solid fa-caret-square-down fa-square-caret-down',

				'fa-solid fa-file-circle-question',

				'fa-solid fa-laptop-code',

				'fa-solid fa-swatchbook',

				'fa-solid fa-prescription-bottle',

				'fa-solid fa-bars fa-navicon',

				'fa-solid fa-people-group',

				'fa-solid fa-hourglass-3 fa-hourglass-end',

				'fa-solid fa-heart-broken fa-heart-crack',

				'fa-solid fa-external-link-square-alt fa-square-up-right',

				'fa-solid fa-face-kiss-beam fa-kiss-beam',

				'fa-solid fa-film',

				'fa-solid fa-ruler-horizontal',

				'fa-solid fa-people-robbery',

				'fa-solid fa-lightbulb',

				'fa-solid fa-caret-left',

				'fa-solid fa-circle-exclamation fa-exclamation-circle',

				'fa-solid fa-school-circle-xmark',

				'fa-solid fa-arrow-right-from-bracket fa-sign-out',

				'fa-solid fa-chevron-circle-down fa-circle-chevron-down',

				'fa-solid fa-unlock-alt fa-unlock-keyhole',

				'fa-solid fa-cloud-showers-heavy',

				'fa-solid fa-headphones-alt fa-headphones-simple',

				'fa-solid fa-sitemap',

				'fa-solid fa-circle-dollar-to-slot fa-donate',

				'fa-solid fa-memory',

				'fa-solid fa-road-spikes',

				'fa-solid fa-fire-burner',

				'fa-solid fa-flag',

				'fa-solid fa-hanukiah',

				'fa-solid fa-feather',

				'fa-solid fa-volume-down fa-volume-low',

				'fa-solid fa-comment-slash',

				'fa-solid fa-cloud-sun-rain',

				'fa-solid fa-compress',

				'fa-solid fa-wheat-alt fa-wheat-awn',

				'fa-solid fa-ankh',

				'fa-solid fa-hands-holding-child',

				'fa-solid fa-asterisk',

				'fa-solid fa-check-square fa-square-check',

				'fa-solid fa-peseta-sign',

				'fa-solid fa-header fa-heading',

				'fa-solid fa-ghost',

				'fa-solid fa-list-squares fa-list',

				'fa-solid fa-phone-square-alt fa-square-phone-flip',

				'fa-solid fa-cart-plus',

				'fa-solid fa-gamepad',

				'fa-solid fa-circle-dot fa-dot-circle',

				'fa-solid fa-dizzy fa-face-dizzy',

				'fa-solid fa-egg',

				'fa-solid fa-house-medical-circle-xmark',

				'fa-solid fa-campground',

				'fa-solid fa-folder-plus',

				'fa-solid fa-futbol-ball fa-futbol fa-soccer-ball',

				'fa-solid fa-paint-brush fa-paintbrush',

				'fa-solid fa-lock',

				'fa-solid fa-gas-pump',

				'fa-solid fa-hot-tub-person fa-hot-tub',

				'fa-solid fa-map-location fa-map-marked',

				'fa-solid fa-house-flood-water',

				'fa-solid fa-tree',

				'fa-solid fa-bridge-lock',

				'fa-solid fa-sack-dollar',

				'fa-solid fa-edit fa-pen-to-square',

				'fa-solid fa-car-side',

				'fa-solid fa-share-alt fa-share-nodes',

				'fa-solid fa-heart-circle-minus',

				'fa-solid fa-hourglass-2 fa-hourglass-half',

				'fa-solid fa-microscope',

				'fa-solid fa-sink',

				'fa-solid fa-bag-shopping fa-shopping-bag',

				'fa-solid fa-arrow-down-z-a fa-sort-alpha-desc fa-sort-alpha-down-alt',

				'fa-solid fa-mitten',

				'fa-solid fa-person-rays',

				'fa-solid fa-users',

				'fa-solid fa-eye-slash',

				'fa-solid fa-flask-vial',

				'fa-solid fa-hand-paper fa-hand',

				'fa-solid fa-om',

				'fa-solid fa-worm',

				'fa-solid fa-house-circle-xmark',

				'fa-solid fa-plug',

				'fa-solid fa-chevron-up',

				'fa-solid fa-hand-spock',

				'fa-solid fa-stopwatch',

				'fa-solid fa-face-kiss fa-kiss',

				'fa-solid fa-bridge-circle-xmark',

				'fa-solid fa-face-grin-tongue fa-grin-tongue',

				'fa-solid fa-chess-bishop',

				'fa-solid fa-face-grin-wink fa-grin-wink',

				'fa-solid fa-deaf fa-deafness fa-ear-deaf fa-hard-of-hearing',

				'fa-solid fa-road-circle-check',

				'fa-solid fa-dice-five',

				'fa-solid fa-rss-square fa-square-rss',

				'fa-solid fa-land-mine-on',

				'fa-solid fa-i-cursor',

				'fa-solid fa-stamp',

				'fa-solid fa-stairs',

				'fa-solid fa-i',

				'fa-solid fa-hryvnia-sign fa-hryvnia',

				'fa-solid fa-pills',

				'fa-solid fa-face-grin-wide fa-grin-alt',

				'fa-solid fa-tooth',

				'fa-solid fa-v',

				'fa-solid fa-bangladeshi-taka-sign',

				'fa-solid fa-bicycle',

				'fa-solid fa-rod-asclepius fa-rod-snake fa-staff-aesculapius fa-staff-snake',

				'fa-solid fa-head-side-cough-slash',

				'fa-solid fa-ambulance fa-truck-medical',

				'fa-solid fa-wheat-awn-circle-exclamation',

				'fa-solid fa-snowman',

				'fa-solid fa-mortar-pestle',

				'fa-solid fa-road-barrier',

				'fa-solid fa-school',

				'fa-solid fa-igloo',

				'fa-solid fa-joint',

				'fa-solid fa-angle-right',

				'fa-solid fa-horse',

				'fa-solid fa-q',

				'fa-solid fa-g',

				'fa-solid fa-notes-medical',

				'fa-solid fa-temperature-2 fa-temperature-half fa-thermometer-2 fa-thermometer-half',

				'fa-solid fa-dong-sign',

				'fa-solid fa-capsules',

				'fa-solid fa-poo-bolt fa-poo-storm',

				'fa-solid fa-face-frown-open fa-frown-open',

				'fa-solid fa-hand-point-up',

				'fa-solid fa-money-bill',

				'fa-solid fa-bookmark',

				'fa-solid fa-align-justify',

				'fa-solid fa-umbrella-beach',

				'fa-solid fa-helmet-un',

				'fa-solid fa-bullseye',

				'fa-solid fa-bacon',

				'fa-solid fa-hand-point-down',

				'fa-solid fa-arrow-up-from-bracket',

				'fa-solid fa-folder-blank fa-folder',

				'fa-solid fa-file-medical-alt fa-file-waveform',

				'fa-solid fa-radiation',

				'fa-solid fa-chart-simple',

				'fa-solid fa-mars-stroke',

				'fa-solid fa-vial',

				'fa-solid fa-dashboard fa-gauge-med fa-gauge fa-tachometer-alt-average',

				'fa-solid fa-magic-wand-sparkles fa-wand-magic-sparkles',

				'fa-solid fa-e',

				'fa-solid fa-pen-alt fa-pen-clip',

				'fa-solid fa-bridge-circle-exclamation',

				'fa-solid fa-user',

				'fa-solid fa-school-circle-check',

				'fa-solid fa-dumpster',

				'fa-solid fa-shuttle-van fa-van-shuttle',

				'fa-solid fa-building-user',

				'fa-solid fa-caret-square-left fa-square-caret-left',

				'fa-solid fa-highlighter',

				'fa-solid fa-key',

				'fa-solid fa-bullhorn',

				'fa-solid fa-globe',

				'fa-solid fa-synagogue',

				'fa-solid fa-person-half-dress',

				'fa-solid fa-road-bridge',

				'fa-solid fa-location-arrow',

				'fa-solid fa-c',

				'fa-solid fa-tablet-button',

				'fa-solid fa-building-lock',

				'fa-solid fa-pizza-slice',

				'fa-solid fa-money-bill-wave',

				'fa-solid fa-area-chart fa-chart-area',

				'fa-solid fa-house-flag',

				'fa-solid fa-person-circle-minus',

				'fa-solid fa-ban fa-cancel',

				'fa-solid fa-camera-rotate',

				'fa-solid fa-air-freshener fa-spray-can-sparkles',

				'fa-solid fa-star',

				'fa-solid fa-repeat',

				'fa-solid fa-cross',

				'fa-solid fa-box',

				'fa-solid fa-venus-mars',

				'fa-solid fa-arrow-pointer fa-mouse-pointer',

				'fa-solid fa-expand-arrows-alt fa-maximize',

				'fa-solid fa-charging-station',

				'fa-solid fa-shapes fa-triangle-circle-square',

				'fa-solid fa-random fa-shuffle',

				'fa-solid fa-person-running fa-running',

				'fa-solid fa-mobile-retro',

				'fa-solid fa-grip-lines-vertical',

				'fa-solid fa-spider',

				'fa-solid fa-hands-bound',

				'fa-solid fa-file-invoice-dollar',

				'fa-solid fa-plane-circle-exclamation',

				'fa-solid fa-x-ray',

				'fa-solid fa-spell-check',

				'fa-solid fa-slash',

				'fa-solid fa-computer-mouse fa-mouse',

				'fa-solid fa-arrow-right-to-bracket fa-sign-in',

				'fa-solid fa-shop-slash fa-store-alt-slash',

				'fa-solid fa-server',

				'fa-solid fa-virus-covid-slash',

				'fa-solid fa-shop-lock',

				'fa-solid fa-hourglass-1 fa-hourglass-start',

				'fa-solid fa-blender-phone',

				'fa-solid fa-building-wheat',

				'fa-solid fa-person-breastfeeding',

				'fa-solid fa-right-to-bracket fa-sign-in-alt',

				'fa-solid fa-venus',

				'fa-solid fa-passport',

				'fa-solid fa-heart-pulse fa-heartbeat',

				'fa-solid fa-people-carry-box fa-people-carry',

				'fa-solid fa-temperature-high',

				'fa-solid fa-microchip',

				'fa-solid fa-crown',

				'fa-solid fa-weight-hanging',

				'fa-solid fa-xmarks-lines',

				'fa-solid fa-file-prescription',

				'fa-solid fa-weight-scale fa-weight',

				'fa-solid fa-user-friends fa-user-group',

				'fa-solid fa-arrow-up-a-z fa-sort-alpha-up',

				'fa-solid fa-chess-knight',

				'fa-solid fa-face-laugh-squint fa-laugh-squint',

				'fa-solid fa-wheelchair',

				'fa-solid fa-arrow-circle-up fa-circle-arrow-up',

				'fa-solid fa-toggle-on',

				'fa-solid fa-person-walking fa-walking',

				'fa-solid fa-l',

				'fa-solid fa-fire',

				'fa-solid fa-bed-pulse fa-procedures',

				'fa-solid fa-shuttle-space fa-space-shuttle',

				'fa-solid fa-face-laugh fa-laugh',

				'fa-solid fa-folder-open',

				'fa-solid fa-heart-circle-plus',

				'fa-solid fa-code-fork',

				'fa-solid fa-city',

				'fa-solid fa-microphone-alt fa-microphone-lines',

				'fa-solid fa-pepper-hot',

				'fa-solid fa-unlock',

				'fa-solid fa-colon-sign',

				'fa-solid fa-headset',

				'fa-solid fa-store-slash',

				'fa-solid fa-road-circle-xmark',

				'fa-solid fa-user-minus',

				'fa-solid fa-mars-stroke-up fa-mars-stroke-v',

				'fa-solid fa-champagne-glasses fa-glass-cheers',

				'fa-solid fa-clipboard',

				'fa-solid fa-house-circle-exclamation',

				'fa-solid fa-file-arrow-up fa-file-upload',

				'fa-solid fa-wifi-3 fa-wifi-strong fa-wifi',

				'fa-solid fa-bath fa-bathtub',

				'fa-solid fa-underline',

				'fa-solid fa-user-edit fa-user-pen',

				'fa-solid fa-signature',

				'fa-solid fa-stroopwafel',

				'fa-solid fa-bold',

				'fa-solid fa-anchor-lock',

				'fa-solid fa-building-ngo',

				'fa-solid fa-manat-sign',

				'fa-solid fa-not-equal',

				'fa-solid fa-border-style fa-border-top-left',

				'fa-solid fa-map-location-dot fa-map-marked-alt',

				'fa-solid fa-jedi',

				'fa-solid fa-poll fa-square-poll-vertical',

				'fa-solid fa-mug-hot',

				'fa-solid fa-battery-car fa-car-battery',

				'fa-solid fa-gift',

				'fa-solid fa-dice-two',

				'fa-solid fa-chess-queen',

				'fa-solid fa-glasses',

				'fa-solid fa-chess-board',

				'fa-solid fa-building-circle-check',

				'fa-solid fa-person-chalkboard',

				'fa-solid fa-mars-stroke-h fa-mars-stroke-right',

				'fa-solid fa-hand-back-fist fa-hand-rock',

				'fa-solid fa-caret-square-up fa-square-caret-up',

				'fa-solid fa-cloud-showers-water',

				'fa-solid fa-bar-chart fa-chart-bar',

				'fa-solid fa-hands-bubbles fa-hands-wash',

				'fa-solid fa-less-than-equal',

				'fa-solid fa-train',

				'fa-solid fa-eye-low-vision fa-low-vision',

				'fa-solid fa-crow',

				'fa-solid fa-sailboat',

				'fa-solid fa-window-restore',

				'fa-solid fa-plus-square fa-square-plus',

				'fa-solid fa-torii-gate',

				'fa-solid fa-frog',

				'fa-solid fa-bucket',

				'fa-solid fa-image',

				'fa-solid fa-microphone',

				'fa-solid fa-cow',

				'fa-solid fa-caret-up',

				'fa-solid fa-screwdriver',

				'fa-solid fa-folder-closed',

				'fa-solid fa-house-tsunami',

				'fa-solid fa-square-nfi',

				'fa-solid fa-arrow-up-from-ground-water',

				'fa-solid fa-glass-martini-alt fa-martini-glass',

				'fa-solid fa-rotate-back fa-rotate-backward fa-rotate-left fa-undo-alt',

				'fa-solid fa-columns fa-table-columns',

				'fa-solid fa-lemon',

				'fa-solid fa-head-side-mask',

				'fa-solid fa-handshake',

				'fa-solid fa-gem',

				'fa-solid fa-dolly-box fa-dolly',

				'fa-solid fa-smoking',

				'fa-solid fa-compress-arrows-alt fa-minimize',

				'fa-solid fa-monument',

				'fa-solid fa-snowplow',

				'fa-solid fa-angle-double-right fa-angles-right',

				'fa-solid fa-cannabis',

				'fa-solid fa-circle-play fa-play-circle',

				'fa-solid fa-tablets',

				'fa-solid fa-ethernet',

				'fa-solid fa-eur fa-euro-sign fa-euro',

				'fa-solid fa-chair',

				'fa-solid fa-check-circle fa-circle-check',

				'fa-solid fa-circle-stop fa-stop-circle',

				'fa-solid fa-compass-drafting fa-drafting-compass',

				'fa-solid fa-plate-wheat',

				'fa-solid fa-icicles',

				'fa-solid fa-person-shelter',

				'fa-solid fa-neuter',

				'fa-solid fa-id-badge',

				'fa-solid fa-marker',

				'fa-solid fa-face-laugh-beam fa-laugh-beam',

				'fa-solid fa-helicopter-symbol',

				'fa-solid fa-universal-access',

				'fa-solid fa-chevron-circle-up fa-circle-chevron-up',

				'fa-solid fa-lari-sign',

				'fa-solid fa-volcano',

				'fa-solid fa-person-walking-dashed-line-arrow-right',

				'fa-solid fa-gbp fa-pound-sign fa-sterling-sign',

				'fa-solid fa-viruses',

				'fa-solid fa-square-person-confined',

				'fa-solid fa-user-tie',

				'fa-solid fa-arrow-down-long fa-long-arrow-down',

				'fa-solid fa-tent-arrow-down-to-line',

				'fa-solid fa-certificate',

				'fa-solid fa-mail-reply-all fa-reply-all',

				'fa-solid fa-suitcase',

				'fa-solid fa-person-skating fa-skating',

				'fa-solid fa-filter-circle-dollar fa-funnel-dollar',

				'fa-solid fa-camera-retro',

				'fa-solid fa-arrow-circle-down fa-circle-arrow-down',

				'fa-solid fa-arrow-right-to-file fa-file-import',

				'fa-solid fa-external-link-square fa-square-arrow-up-right',

				'fa-solid fa-box-open',

				'fa-solid fa-scroll',

				'fa-solid fa-spa',

				'fa-solid fa-location-pin-lock',

				'fa-solid fa-pause',

				'fa-solid fa-hill-avalanche',

				'fa-solid fa-temperature-0 fa-temperature-empty fa-thermometer-0 fa-thermometer-empty',

				'fa-solid fa-bomb',

				'fa-solid fa-registered',

				'fa-solid fa-address-card fa-contact-card fa-vcard',

				'fa-solid fa-balance-scale-right fa-scale-unbalanced-flip',

				'fa-solid fa-subscript',

				'fa-solid fa-diamond-turn-right fa-directions',

				'fa-solid fa-burst',

				'fa-solid fa-house-laptop fa-laptop-house',

				'fa-solid fa-face-tired fa-tired',

				'fa-solid fa-money-bills',

				'fa-solid fa-smog',

				'fa-solid fa-crutch',

				'fa-solid fa-cloud-arrow-up fa-cloud-upload-alt fa-cloud-upload',

				'fa-solid fa-palette',

				'fa-solid fa-arrows-turn-right',

				'fa-solid fa-vest',

				'fa-solid fa-ferry',

				'fa-solid fa-arrows-down-to-people',

				'fa-solid fa-seedling fa-sprout',

				'fa-solid fa-arrows-alt-h fa-left-right',

				'fa-solid fa-boxes-packing',

				'fa-solid fa-arrow-circle-left fa-circle-arrow-left',

				'fa-solid fa-group-arrows-rotate',

				'fa-solid fa-bowl-food',

				'fa-solid fa-candy-cane',

				'fa-solid fa-arrow-down-wide-short fa-sort-amount-asc fa-sort-amount-down',

				'fa-solid fa-cloud-bolt fa-thunderstorm',

				'fa-solid fa-remove-format fa-text-slash',

				'fa-solid fa-face-smile-wink fa-smile-wink',

				'fa-solid fa-file-word',

				'fa-solid fa-file-powerpoint',

				'fa-solid fa-arrows-h fa-arrows-left-right',

				'fa-solid fa-house-lock',

				'fa-solid fa-cloud-arrow-down fa-cloud-download-alt fa-cloud-download',

				'fa-solid fa-children',

				'fa-solid fa-blackboard fa-chalkboard',

				'fa-solid fa-user-alt-slash fa-user-large-slash',

				'fa-solid fa-envelope-open',

				'fa-solid fa-handshake-alt-slash fa-handshake-simple-slash',

				'fa-solid fa-mattress-pillow',

				'fa-solid fa-guarani-sign',

				'fa-solid fa-arrows-rotate fa-refresh fa-sync',

				'fa-solid fa-fire-extinguisher',

				'fa-solid fa-cruzeiro-sign',

				'fa-solid fa-greater-than-equal',

				'fa-solid fa-shield-alt fa-shield-halved',

				'fa-solid fa-atlas fa-book-atlas',

				'fa-solid fa-virus',

				'fa-solid fa-envelope-circle-check',

				'fa-solid fa-layer-group',

				'fa-solid fa-arrows-to-dot',

				'fa-solid fa-archway',

				'fa-solid fa-heart-circle-check',

				'fa-solid fa-house-chimney-crack fa-house-damage',

				'fa-solid fa-file-archive fa-file-zipper',

				'fa-solid fa-square',

				'fa-solid fa-glass-martini fa-martini-glass-empty',

				'fa-solid fa-couch',

				'fa-solid fa-cedi-sign',

				'fa-solid fa-italic',

				'fa-solid fa-church',

				'fa-solid fa-comments-dollar',

				'fa-solid fa-democrat',

				'fa-solid fa-z',

				'fa-solid fa-person-skiing fa-skiing',

				'fa-solid fa-road-lock',

				'fa-solid fa-a',

				'fa-solid fa-temperature-arrow-down fa-temperature-down',

				'fa-solid fa-feather-alt fa-feather-pointed',

				'fa-solid fa-p',

				'fa-solid fa-snowflake',

				'fa-solid fa-newspaper',

				'fa-solid fa-ad fa-rectangle-ad',

				'fa-solid fa-arrow-circle-right fa-circle-arrow-right',

				'fa-solid fa-filter-circle-xmark',

				'fa-solid fa-locust',

				'fa-solid fa-sort fa-unsorted',

				'fa-solid fa-list-1-2 fa-list-numeric fa-list-ol',

				'fa-solid fa-person-dress-burst',

				'fa-solid fa-money-check-alt fa-money-check-dollar',

				'fa-solid fa-vector-square',

				'fa-solid fa-bread-slice',

				'fa-solid fa-language',

				'fa-solid fa-face-kiss-wink-heart fa-kiss-wink-heart',

				'fa-solid fa-filter',

				'fa-solid fa-question',

				'fa-solid fa-file-signature',

				'fa-solid fa-arrows-alt fa-up-down-left-right',

				'fa-solid fa-house-chimney-user',

				'fa-solid fa-hand-holding-heart',

				'fa-solid fa-puzzle-piece',

				'fa-solid fa-money-check',

				'fa-solid fa-star-half-alt fa-star-half-stroke',

				'fa-solid fa-code',

				'fa-solid fa-glass-whiskey fa-whiskey-glass',

				'fa-solid fa-building-circle-exclamation',

				'fa-solid fa-magnifying-glass-chart',

				'fa-solid fa-arrow-up-right-from-square fa-external-link',

				'fa-solid fa-cubes-stacked',

				'fa-solid fa-krw fa-won-sign fa-won',

				'fa-solid fa-virus-covid',

				'fa-solid fa-austral-sign',

				'fa-solid fa-f',

				'fa-solid fa-leaf',

				'fa-solid fa-road',

				'fa-solid fa-cab fa-taxi',

				'fa-solid fa-person-circle-plus',

				'fa-solid fa-chart-pie fa-pie-chart',

				'fa-solid fa-bolt-lightning',

				'fa-solid fa-sack-xmark',

				'fa-solid fa-file-excel',

				'fa-solid fa-file-contract',

				'fa-solid fa-fish-fins',

				'fa-solid fa-building-flag',

				'fa-solid fa-face-grin-beam fa-grin-beam',

				'fa-solid fa-object-ungroup',

				'fa-solid fa-poop',

				'fa-solid fa-location-pin fa-map-marker',

				'fa-solid fa-kaaba',

				'fa-solid fa-toilet-paper',

				'fa-solid fa-hard-hat fa-hat-hard fa-helmet-safety',

				'fa-solid fa-eject',

				'fa-solid fa-arrow-alt-circle-right fa-circle-right',

				'fa-solid fa-plane-circle-check',

				'fa-solid fa-face-rolling-eyes fa-meh-rolling-eyes',

				'fa-solid fa-object-group',

				'fa-solid fa-chart-line fa-line-chart',

				'fa-solid fa-mask-ventilator',

				'fa-solid fa-arrow-right',

				'fa-solid fa-map-signs fa-signs-post',

				'fa-solid fa-cash-register',

				'fa-solid fa-person-circle-question',

				'fa-solid fa-h',

				'fa-solid fa-tarp',

				'fa-solid fa-screwdriver-wrench fa-tools',

				'fa-solid fa-arrows-to-eye',

				'fa-solid fa-plug-circle-bolt',

				'fa-solid fa-heart',

				'fa-solid fa-mars-and-venus',

				'fa-solid fa-home-user fa-house-user',

				'fa-solid fa-dumpster-fire',

				'fa-solid fa-house-crack',

				'fa-solid fa-cocktail fa-martini-glass-citrus',

				'fa-solid fa-face-surprise fa-surprise',

				'fa-solid fa-bottle-water',

				'fa-solid fa-circle-pause fa-pause-circle',

				'fa-solid fa-toilet-paper-slash',

				'fa-solid fa-apple-alt fa-apple-whole',

				'fa-solid fa-kitchen-set',

				'fa-solid fa-r',

				'fa-solid fa-temperature-1 fa-temperature-quarter fa-thermometer-1 fa-thermometer-quarter',

				'fa-solid fa-cube',

				'fa-solid fa-bitcoin-sign',

				'fa-solid fa-shield-dog',

				'fa-solid fa-solar-panel',

				'fa-solid fa-lock-open',

				'fa-solid fa-elevator',

				'fa-solid fa-money-bill-transfer',

				'fa-solid fa-money-bill-trend-up',

				'fa-solid fa-house-flood-water-circle-arrow-right',

				'fa-solid fa-poll-h fa-square-poll-horizontal',

				'fa-solid fa-circle',

				'fa-solid fa-backward-fast fa-fast-backward',

				'fa-solid fa-recycle',

				'fa-solid fa-user-astronaut',

				'fa-solid fa-plane-slash',

				'fa-solid fa-trademark',

				'fa-solid fa-basketball-ball fa-basketball',

				'fa-solid fa-satellite-dish',

				'fa-solid fa-arrow-alt-circle-up fa-circle-up',

				'fa-solid fa-mobile-alt fa-mobile-screen-button',

				'fa-solid fa-volume-high fa-volume-up',

				'fa-solid fa-users-rays',

				'fa-solid fa-wallet',

				'fa-solid fa-clipboard-check',

				'fa-solid fa-file-audio',

				'fa-solid fa-burger fa-hamburger',

				'fa-solid fa-wrench',

				'fa-solid fa-bugs',

				'fa-solid fa-rupee-sign fa-rupee',

				'fa-solid fa-file-image',

				'fa-solid fa-circle-question fa-question-circle',

				'fa-solid fa-plane-departure',

				'fa-solid fa-handshake-slash',

				'fa-solid fa-book-bookmark',

				'fa-solid fa-code-branch',

				'fa-solid fa-hat-cowboy',

				'fa-solid fa-bridge',

				'fa-solid fa-phone-alt fa-phone-flip',

				'fa-solid fa-truck-front',

				'fa-solid fa-cat',

				'fa-solid fa-anchor-circle-exclamation',

				'fa-solid fa-truck-field',

				'fa-solid fa-route',

				'fa-solid fa-clipboard-question',

				'fa-solid fa-panorama',

				'fa-solid fa-comment-medical',

				'fa-solid fa-teeth-open',

				'fa-solid fa-file-circle-minus',

				'fa-solid fa-tags',

				'fa-solid fa-wine-glass',

				'fa-solid fa-fast-forward fa-forward-fast',

				'fa-solid fa-face-meh-blank fa-meh-blank',

				'fa-solid fa-parking fa-square-parking',

				'fa-solid fa-house-signal',

				'fa-solid fa-bars-progress fa-tasks-alt',

				'fa-solid fa-faucet-drip',

				'fa-solid fa-cart-flatbed fa-dolly-flatbed',

				'fa-solid fa-ban-smoking fa-smoking-ban',

				'fa-solid fa-terminal',

				'fa-solid fa-mobile-button',

				'fa-solid fa-house-medical-flag',

				'fa-solid fa-basket-shopping fa-shopping-basket',

				'fa-solid fa-tape',

				'fa-solid fa-bus-alt fa-bus-simple',

				'fa-solid fa-eye',

				'fa-solid fa-face-sad-cry fa-sad-cry',

				'fa-solid fa-audio-description',

				'fa-solid fa-person-military-to-person',

				'fa-solid fa-file-shield',

				'fa-solid fa-user-slash',

				'fa-solid fa-pen',

				'fa-solid fa-tower-observation',

				'fa-solid fa-file-code',

				'fa-solid fa-signal-5 fa-signal-perfect fa-signal',

				'fa-solid fa-bus',

				'fa-solid fa-heart-circle-xmark',

				'fa-solid fa-home-lg fa-house-chimney',

				'fa-solid fa-window-maximize',

				'fa-solid fa-face-frown fa-frown',

				'fa-solid fa-prescription',

				'fa-solid fa-shop fa-store-alt',

				'fa-solid fa-floppy-disk fa-save',

				'fa-solid fa-vihara',

				'fa-solid fa-balance-scale-left fa-scale-unbalanced',

				'fa-solid fa-sort-asc fa-sort-up',

				'fa-solid fa-comment-dots fa-commenting',

				'fa-solid fa-plant-wilt',

				'fa-solid fa-diamond',

				'fa-solid fa-face-grin-squint fa-grin-squint',

				'fa-solid fa-hand-holding-dollar fa-hand-holding-usd',

				'fa-solid fa-bacterium',

				'fa-solid fa-hand-pointer',

				'fa-solid fa-drum-steelpan',

				'fa-solid fa-hand-scissors',

				'fa-solid fa-hands-praying fa-praying-hands',

				'fa-solid fa-arrow-right-rotate fa-arrow-rotate-forward fa-arrow-rotate-right fa-redo',

				'fa-solid fa-biohazard',

				'fa-solid fa-location-crosshairs fa-location',

				'fa-solid fa-mars-double',

				'fa-solid fa-child-dress',

				'fa-solid fa-users-between-lines',

				'fa-solid fa-lungs-virus',

				'fa-solid fa-face-grin-tears fa-grin-tears',

				'fa-solid fa-phone',

				'fa-solid fa-calendar-times fa-calendar-xmark',

				'fa-solid fa-child-reaching',

				'fa-solid fa-head-side-virus',

				'fa-solid fa-user-cog fa-user-gear',

				'fa-solid fa-arrow-up-1-9 fa-sort-numeric-up',

				'fa-solid fa-door-closed',

				'fa-solid fa-shield-virus',

				'fa-solid fa-dice-six',

				'fa-solid fa-mosquito-net',

				'fa-solid fa-bridge-water',

				'fa-solid fa-person-booth',

				'fa-solid fa-text-width',

				'fa-solid fa-hat-wizard',

				'fa-solid fa-pen-fancy',

				'fa-solid fa-digging fa-person-digging',

				'fa-solid fa-trash',

				'fa-solid fa-gauge-simple-med fa-gauge-simple fa-tachometer-average',

				'fa-solid fa-book-medical',

				'fa-solid fa-poo',

				'fa-solid fa-quote-right-alt fa-quote-right',

				'fa-solid fa-shirt fa-t-shirt fa-tshirt',

				'fa-solid fa-cubes',

				'fa-solid fa-divide',

				'fa-solid fa-tenge-sign fa-tenge',

				'fa-solid fa-headphones',

				'fa-solid fa-hands-holding',

				'fa-solid fa-hands-clapping',

				'fa-solid fa-republican',

				'fa-solid fa-arrow-left',

				'fa-solid fa-person-circle-xmark',

				'fa-solid fa-ruler',

				'fa-solid fa-align-left',

				'fa-solid fa-dice-d6',

				'fa-solid fa-restroom',

				'fa-solid fa-j',

				'fa-solid fa-users-viewfinder',

				'fa-solid fa-file-video',

				'fa-solid fa-external-link-alt fa-up-right-from-square',

				'fa-solid fa-table-cells fa-th',

				'fa-solid fa-file-pdf',

				'fa-solid fa-bible fa-book-bible',

				'fa-solid fa-o',

				'fa-solid fa-medkit fa-suitcase-medical',

				'fa-solid fa-user-secret',

				'fa-solid fa-otter',

				'fa-solid fa-female fa-person-dress',

				'fa-solid fa-comment-dollar',

				'fa-solid fa-briefcase-clock fa-business-time',

				'fa-solid fa-table-cells-large fa-th-large',

				'fa-solid fa-book-tanakh fa-tanakh',

				'fa-solid fa-phone-volume fa-volume-control-phone',

				'fa-solid fa-hat-cowboy-side',

				'fa-solid fa-clipboard-user',

				'fa-solid fa-child',

				'fa-solid fa-lira-sign',

				'fa-solid fa-satellite',

				'fa-solid fa-plane-lock',

				'fa-solid fa-tag',

				'fa-solid fa-comment',

				'fa-solid fa-birthday-cake fa-cake-candles fa-cake',

				'fa-solid fa-envelope',

				'fa-solid fa-angle-double-up fa-angles-up',

				'fa-solid fa-paperclip',

				'fa-solid fa-arrow-right-to-city',

				'fa-solid fa-ribbon',

				'fa-solid fa-lungs',

				'fa-solid fa-arrow-up-9-1 fa-sort-numeric-up-alt',

				'fa-solid fa-litecoin-sign',

				'fa-solid fa-border-none',

				'fa-solid fa-circle-nodes',

				'fa-solid fa-parachute-box',

				'fa-solid fa-indent',

				'fa-solid fa-truck-field-un',

				'fa-solid fa-hourglass-empty fa-hourglass',

				'fa-solid fa-mountain',

				'fa-solid fa-user-doctor fa-user-md',

				'fa-solid fa-circle-info fa-info-circle',

				'fa-solid fa-cloud-meatball',

				'fa-solid fa-camera-alt fa-camera',

				'fa-solid fa-square-virus',

				'fa-solid fa-meteor',

				'fa-solid fa-car-on',

				'fa-solid fa-sleigh',

				'fa-solid fa-arrow-down-1-9 fa-sort-numeric-asc fa-sort-numeric-down',

				'fa-solid fa-hand-holding-droplet fa-hand-holding-water',

				'fa-solid fa-water',

				'fa-solid fa-calendar-check',

				'fa-solid fa-braille',

				'fa-solid fa-prescription-bottle-alt fa-prescription-bottle-medical',

				'fa-solid fa-landmark',

				'fa-solid fa-truck',

				'fa-solid fa-crosshairs',

				'fa-solid fa-person-cane',

				'fa-solid fa-tent',

				'fa-solid fa-vest-patches',

				'fa-solid fa-check-double',

				'fa-solid fa-arrow-down-a-z fa-sort-alpha-asc fa-sort-alpha-down',

				'fa-solid fa-money-bill-wheat',

				'fa-solid fa-cookie',

				'fa-solid fa-arrow-left-rotate fa-arrow-rotate-back fa-arrow-rotate-backward fa-arrow-rotate-left fa-undo',

				'fa-solid fa-hard-drive fa-hdd',

				'fa-solid fa-face-grin-squint-tears fa-grin-squint-tears',

				'fa-solid fa-dumbbell',

				'fa-solid fa-list-alt fa-rectangle-list',

				'fa-solid fa-tarp-droplet',

				'fa-solid fa-house-medical-circle-check',

				'fa-solid fa-person-skiing-nordic fa-skiing-nordic',

				'fa-solid fa-calendar-plus',

				'fa-solid fa-plane-arrival',

				'fa-solid fa-arrow-alt-circle-left fa-circle-left',

				'fa-solid fa-subway fa-train-subway',

				'fa-solid fa-chart-gantt',

				'fa-solid fa-indian-rupee-sign fa-indian-rupee fa-inr',

				'fa-solid fa-crop-alt fa-crop-simple',

				'fa-solid fa-money-bill-1 fa-money-bill-alt',

				'fa-solid fa-left-long fa-long-arrow-alt-left',

				'fa-solid fa-dna',

				'fa-solid fa-virus-slash',

				'fa-solid fa-minus fa-subtract',

				'fa-solid fa-chess',

				'fa-solid fa-arrow-left-long fa-long-arrow-left',

				'fa-solid fa-plug-circle-check',

				'fa-solid fa-street-view',

				'fa-solid fa-franc-sign',

				'fa-solid fa-volume-off',

				'fa-solid fa-american-sign-language-interpreting fa-asl-interpreting fa-hands-american-sign-language-interpreting fa-hands-asl-interpreting',

				'fa-solid fa-cog fa-gear',

				'fa-solid fa-droplet-slash fa-tint-slash',

				'fa-solid fa-mosque',

				'fa-solid fa-mosquito',

				'fa-solid fa-star-of-david',

				'fa-solid fa-person-military-rifle',

				'fa-solid fa-cart-shopping fa-shopping-cart',

				'fa-solid fa-vials',

				'fa-solid fa-plug-circle-plus',

				'fa-solid fa-place-of-worship',

				'fa-solid fa-grip-vertical',

				'fa-solid fa-arrow-turn-up fa-level-up',

				'fa-solid fa-u',

				'fa-solid fa-square-root-alt fa-square-root-variable',

				'fa-solid fa-clock-four fa-clock',

				'fa-solid fa-backward-step fa-step-backward',

				'fa-solid fa-pallet',

				'fa-solid fa-faucet',

				'fa-solid fa-baseball-bat-ball',

				'fa-solid fa-s',

				'fa-solid fa-timeline',

				'fa-solid fa-keyboard',

				'fa-solid fa-caret-down',

				'fa-solid fa-clinic-medical fa-house-chimney-medical',

				'fa-solid fa-temperature-3 fa-temperature-three-quarters fa-thermometer-3 fa-thermometer-three-quarters',

				'fa-solid fa-mobile-android-alt fa-mobile-screen',

				'fa-solid fa-plane-up',

				'fa-solid fa-piggy-bank',

				'fa-solid fa-battery-3 fa-battery-half',

				'fa-solid fa-mountain-city',

				'fa-solid fa-coins',

				'fa-solid fa-khanda',

				'fa-solid fa-sliders-h fa-sliders',

				'fa-solid fa-folder-tree',

				'fa-solid fa-network-wired',

				'fa-solid fa-map-pin',

				'fa-solid fa-hamsa',

				'fa-solid fa-cent-sign',

				'fa-solid fa-flask',

				'fa-solid fa-person-pregnant',

				'fa-solid fa-wand-sparkles',

				'fa-solid fa-ellipsis-v fa-ellipsis-vertical',

				'fa-solid fa-ticket',

				'fa-solid fa-power-off',

				'fa-solid fa-long-arrow-alt-right fa-right-long',

				'fa-solid fa-flag-usa',

				'fa-solid fa-laptop-file',

				'fa-solid fa-teletype fa-tty',

				'fa-solid fa-diagram-next',

				'fa-solid fa-person-rifle',

				'fa-solid fa-house-medical-circle-exclamation',

				'fa-solid fa-closed-captioning',

				'fa-solid fa-hiking fa-person-hiking',

				'fa-solid fa-venus-double',

				'fa-solid fa-images',

				'fa-solid fa-calculator',

				'fa-solid fa-people-pulling',

				'fa-solid fa-n',

				'fa-solid fa-cable-car fa-tram',

				'fa-solid fa-cloud-rain',

				'fa-solid fa-building-circle-xmark',

				'fa-solid fa-ship',

				'fa-solid fa-arrows-down-to-line',

				'fa-solid fa-download',

				'fa-solid fa-face-grin fa-grin',

				'fa-solid fa-backspace fa-delete-left',

				'fa-solid fa-eye-dropper-empty fa-eye-dropper fa-eyedropper',

				'fa-solid fa-file-circle-check',

				'fa-solid fa-forward',

				'fa-solid fa-mobile-android fa-mobile-phone fa-mobile',

				'fa-solid fa-face-meh fa-meh',

				'fa-solid fa-align-center',

				'fa-solid fa-book-dead fa-book-skull',

				'fa-solid fa-drivers-license fa-id-card',

				'fa-solid fa-dedent fa-outdent',

				'fa-solid fa-heart-circle-exclamation',

				'fa-solid fa-home-alt fa-home-lg-alt fa-home fa-house',

				'fa-solid fa-calendar-week',

				'fa-solid fa-laptop-medical',

				'fa-solid fa-b',

				'fa-solid fa-file-medical',

				'fa-solid fa-dice-one',

				'fa-solid fa-kiwi-bird',

				'fa-solid fa-arrow-right-arrow-left fa-exchange',

				'fa-solid fa-redo-alt fa-rotate-forward fa-rotate-right',

				'fa-solid fa-cutlery fa-utensils',

				'fa-solid fa-arrow-up-wide-short fa-sort-amount-up',

				'fa-solid fa-mill-sign',

				'fa-solid fa-bowl-rice',

				'fa-solid fa-skull',

				'fa-solid fa-broadcast-tower fa-tower-broadcast',

				'fa-solid fa-truck-pickup',

				'fa-solid fa-long-arrow-alt-up fa-up-long',

				'fa-solid fa-stop',

				'fa-solid fa-code-merge',

				'fa-solid fa-upload',

				'fa-solid fa-hurricane',

				'fa-solid fa-mound',

				'fa-solid fa-toilet-portable',

				'fa-solid fa-compact-disc',

				'fa-solid fa-file-arrow-down fa-file-download',

				'fa-solid fa-caravan',

				'fa-solid fa-shield-cat',

				'fa-solid fa-bolt fa-zap',

				'fa-solid fa-glass-water',

				'fa-solid fa-oil-well',

				'fa-solid fa-vault',

				'fa-solid fa-mars',

				'fa-solid fa-toilet',

				'fa-solid fa-plane-circle-xmark',

				'fa-solid fa-cny fa-jpy fa-rmb fa-yen-sign fa-yen',

				'fa-solid fa-rouble fa-rub fa-ruble-sign fa-ruble',

				'fa-solid fa-sun',

				'fa-solid fa-guitar',

				'fa-solid fa-face-laugh-wink fa-laugh-wink',

				'fa-solid fa-horse-head',

				'fa-solid fa-bore-hole',

				'fa-solid fa-industry',

				'fa-solid fa-arrow-alt-circle-down fa-circle-down',

				'fa-solid fa-arrows-turn-to-dots',

				'fa-solid fa-florin-sign',

				'fa-solid fa-arrow-down-short-wide fa-sort-amount-desc fa-sort-amount-down-alt',

				'fa-solid fa-less-than',

				'fa-solid fa-angle-down',

				'fa-solid fa-car-tunnel',

				'fa-solid fa-head-side-cough',

				'fa-solid fa-grip-lines',

				'fa-solid fa-thumbs-down',

				'fa-solid fa-user-lock',

				'fa-solid fa-arrow-right-long fa-long-arrow-right',

				'fa-solid fa-anchor-circle-xmark',

				'fa-solid fa-ellipsis-h fa-ellipsis',

				'fa-solid fa-chess-pawn',

				'fa-solid fa-first-aid fa-kit-medical',

				'fa-solid fa-person-through-window',

				'fa-solid fa-toolbox',

				'fa-solid fa-hands-holding-circle',

				'fa-solid fa-bug',

				'fa-solid fa-credit-card-alt fa-credit-card',

				'fa-solid fa-automobile fa-car',

				'fa-solid fa-hand-holding-hand',

				'fa-solid fa-book-open-reader fa-book-reader',

				'fa-solid fa-mountain-sun',

				'fa-solid fa-arrows-left-right-to-line',

				'fa-solid fa-dice-d20',

				'fa-solid fa-truck-droplet',

				'fa-solid fa-file-circle-xmark',

				'fa-solid fa-temperature-arrow-up fa-temperature-up',

				'fa-solid fa-medal',

				'fa-solid fa-bed',

				'fa-solid fa-h-square fa-square-h',

				'fa-solid fa-podcast',

				'fa-solid fa-temperature-4 fa-temperature-full fa-thermometer-4 fa-thermometer-full',

				'fa-solid fa-bell',

				'fa-solid fa-superscript',

				'fa-solid fa-plug-circle-xmark',

				'fa-solid fa-star-of-life',

				'fa-solid fa-phone-slash',

				'fa-solid fa-paint-roller',

				'fa-solid fa-hands-helping fa-handshake-angle',

				'fa-solid fa-location-dot fa-map-marker-alt',

				'fa-solid fa-file',

				'fa-solid fa-greater-than',

				'fa-solid fa-person-swimming fa-swimmer',

				'fa-solid fa-arrow-down',

				'fa-solid fa-droplet fa-tint',

				'fa-solid fa-eraser',

				'fa-solid fa-earth-america fa-earth-americas fa-earth fa-globe-americas',

				'fa-solid fa-person-burst',

				'fa-solid fa-dove',

				'fa-solid fa-battery-0 fa-battery-empty',

				'fa-solid fa-socks',

				'fa-solid fa-inbox',

				'fa-solid fa-section',

				'fa-solid fa-gauge-high fa-tachometer-alt-fast fa-tachometer-alt',

				'fa-solid fa-envelope-open-text',

				'fa-solid fa-hospital-alt fa-hospital-wide fa-hospital',

				'fa-solid fa-wine-bottle',

				'fa-solid fa-chess-rook',

				'fa-solid fa-bars-staggered fa-reorder fa-stream',

				'fa-solid fa-dharmachakra',

				'fa-solid fa-hotdog',

				'fa-solid fa-blind fa-person-walking-with-cane',

				'fa-solid fa-drum',

				'fa-solid fa-ice-cream',

				'fa-solid fa-heart-circle-bolt',

				'fa-solid fa-fax',

				'fa-solid fa-paragraph',

				'fa-solid fa-check-to-slot fa-vote-yea',

				'fa-solid fa-star-half',

				'fa-solid fa-boxes-alt fa-boxes-stacked fa-boxes',

				'fa-solid fa-chain fa-link',

				'fa-solid fa-assistive-listening-systems fa-ear-listen',

				'fa-solid fa-tree-city',

				'fa-solid fa-play',

				'fa-solid fa-font',

				'fa-solid fa-rupiah-sign',

				'fa-solid fa-magnifying-glass fa-search',

				'fa-solid fa-ping-pong-paddle-ball fa-table-tennis-paddle-ball fa-table-tennis',

				'fa-solid fa-diagnoses fa-person-dots-from-line',

				'fa-solid fa-trash-can-arrow-up fa-trash-restore-alt',

				'fa-solid fa-naira-sign',

				'fa-solid fa-cart-arrow-down',

				'fa-solid fa-walkie-talkie',

				'fa-solid fa-file-edit fa-file-pen',

				'fa-solid fa-receipt',

				'fa-solid fa-pen-square fa-pencil-square fa-square-pen',

				'fa-solid fa-suitcase-rolling',

				'fa-solid fa-person-circle-exclamation',

				'fa-solid fa-chevron-down',

				'fa-solid fa-battery-5 fa-battery-full fa-battery',

				'fa-solid fa-skull-crossbones',

				'fa-solid fa-code-compare',

				'fa-solid fa-list-dots fa-list-ul',

				'fa-solid fa-school-lock',

				'fa-solid fa-tower-cell',

				'fa-solid fa-down-long fa-long-arrow-alt-down',

				'fa-solid fa-ranking-star',

				'fa-solid fa-chess-king',

				'fa-solid fa-person-harassing',

				'fa-solid fa-brazilian-real-sign',

				'fa-solid fa-landmark-alt fa-landmark-dome',

				'fa-solid fa-arrow-up',

				'fa-solid fa-television fa-tv-alt fa-tv',

				'fa-solid fa-shrimp',

				'fa-solid fa-list-check fa-tasks',

				'fa-solid fa-jug-detergent',

				'fa-solid fa-circle-user fa-user-circle',

				'fa-solid fa-user-shield',

				'fa-solid fa-wind',

				'fa-solid fa-car-burst fa-car-crash',

				'fa-solid fa-y',

				'fa-solid fa-person-snowboarding fa-snowboarding',

				'fa-solid fa-shipping-fast fa-truck-fast',

				'fa-solid fa-fish',

				'fa-solid fa-user-graduate',

				'fa-solid fa-adjust fa-circle-half-stroke',

				'fa-solid fa-clapperboard',

				'fa-solid fa-circle-radiation fa-radiation-alt',

				'fa-solid fa-baseball-ball fa-baseball',

				'fa-solid fa-jet-fighter-up',

				'fa-solid fa-diagram-project fa-project-diagram',

				'fa-solid fa-copy',

				'fa-solid fa-volume-mute fa-volume-times fa-volume-xmark',

				'fa-solid fa-hand-sparkles',

				'fa-solid fa-grip-horizontal fa-grip',

				'fa-solid fa-share-from-square fa-share-square',

				'fa-solid fa-child-combatant fa-child-rifle',

				'fa-solid fa-gun',

				'fa-solid fa-phone-square fa-square-phone',

				'fa-solid fa-add fa-plus',

				'fa-solid fa-expand',

				'fa-solid fa-computer',

				'fa-solid fa-close fa-multiply fa-remove fa-times fa-xmark',

				'fa-solid fa-arrows-up-down-left-right fa-arrows',

				'fa-solid fa-chalkboard-teacher fa-chalkboard-user',

				'fa-solid fa-peso-sign',

				'fa-solid fa-building-shield',

				'fa-solid fa-baby',

				'fa-solid fa-users-line',

				'fa-solid fa-quote-left-alt fa-quote-left',

				'fa-solid fa-tractor',

				'fa-solid fa-trash-arrow-up fa-trash-restore',

				'fa-solid fa-arrow-down-up-lock',

				'fa-solid fa-lines-leaning',

				'fa-solid fa-ruler-combined',

				'fa-solid fa-copyright',

				'fa-solid fa-equals',

				'fa-solid fa-blender',

				'fa-solid fa-teeth',

				'fa-solid fa-ils fa-shekel-sign fa-shekel fa-sheqel-sign fa-sheqel',

				'fa-solid fa-map',

				'fa-solid fa-rocket',

				'fa-solid fa-photo-film fa-photo-video',

				'fa-solid fa-folder-minus',

				'fa-solid fa-store',

				'fa-solid fa-arrow-trend-up',

				'fa-solid fa-plug-circle-minus',

				'fa-solid fa-sign-hanging fa-sign',

				'fa-solid fa-bezier-curve',

				'fa-solid fa-bell-slash',

				'fa-solid fa-tablet-android fa-tablet',

				'fa-solid fa-school-flag',

				'fa-solid fa-fill',

				'fa-solid fa-angle-up',

				'fa-solid fa-drumstick-bite',

				'fa-solid fa-holly-berry',

				'fa-solid fa-chevron-left',

				'fa-solid fa-bacteria',

				'fa-solid fa-hand-lizard',

				'fa-solid fa-notdef',

				'fa-solid fa-disease',

				'fa-solid fa-briefcase-medical',

				'fa-solid fa-genderless',

				'fa-solid fa-chevron-right',

				'fa-solid fa-retweet',

				'fa-solid fa-car-alt fa-car-rear',

				'fa-solid fa-pump-soap',

				'fa-solid fa-video-slash',

				'fa-solid fa-battery-2 fa-battery-quarter',

				'fa-solid fa-radio',

				'fa-solid fa-baby-carriage fa-carriage-baby',

				'fa-solid fa-traffic-light',

				'fa-solid fa-thermometer',

				'fa-solid fa-vr-cardboard',

				'fa-solid fa-hand-middle-finger',

				'fa-solid fa-percent fa-percentage',

				'fa-solid fa-truck-moving',

				'fa-solid fa-glass-water-droplet',

				'fa-solid fa-display',

				'fa-solid fa-face-smile fa-smile',

				'fa-solid fa-thumb-tack fa-thumbtack',

				'fa-solid fa-trophy',

				'fa-solid fa-person-praying fa-pray',

				'fa-solid fa-hammer',

				'fa-solid fa-hand-peace',

				'fa-solid fa-rotate fa-sync-alt',

				'fa-solid fa-spinner',

				'fa-solid fa-robot',

				'fa-solid fa-peace',

				'fa-solid fa-cogs fa-gears',

				'fa-solid fa-warehouse',

				'fa-solid fa-arrow-up-right-dots',

				'fa-solid fa-splotch',

				'fa-solid fa-face-grin-hearts fa-grin-hearts',

				'fa-solid fa-dice-four',

				'fa-solid fa-sim-card',

				'fa-solid fa-transgender-alt fa-transgender',

				'fa-solid fa-mercury',

				'fa-solid fa-arrow-turn-down fa-level-down',

				'fa-solid fa-person-falling-burst',

				'fa-solid fa-award',

				'fa-solid fa-ticket-alt fa-ticket-simple',

				'fa-solid fa-building',

				'fa-solid fa-angle-double-left fa-angles-left',

				'fa-solid fa-qrcode',

				'fa-solid fa-clock-rotate-left fa-history',

				'fa-solid fa-face-grin-beam-sweat fa-grin-beam-sweat',

				'fa-solid fa-arrow-right-from-file fa-file-export',

				'fa-solid fa-shield-blank fa-shield',

				'fa-solid fa-arrow-up-short-wide fa-sort-amount-up-alt',

				'fa-solid fa-house-medical',

				'fa-solid fa-golf-ball-tee fa-golf-ball',

				'fa-solid fa-chevron-circle-left fa-circle-chevron-left',

				'fa-solid fa-house-chimney-window',

				'fa-solid fa-pen-nib',

				'fa-solid fa-tent-arrow-turn-left',

				'fa-solid fa-tents',

				'fa-solid fa-magic fa-wand-magic',

				'fa-solid fa-dog',

				'fa-solid fa-carrot',

				'fa-solid fa-moon',

				'fa-solid fa-wine-glass-alt fa-wine-glass-empty',

				'fa-solid fa-cheese',

				'fa-solid fa-yin-yang',

				'fa-solid fa-music',

				'fa-solid fa-code-commit',

				'fa-solid fa-temperature-low',

				'fa-solid fa-biking fa-person-biking',

				'fa-solid fa-broom',

				'fa-solid fa-shield-heart',

				'fa-solid fa-gopuram',

				'fa-solid fa-earth-oceania fa-globe-oceania',

				'fa-solid fa-square-xmark fa-times-square fa-xmark-square',

				'fa-solid fa-hashtag',

				'fa-solid fa-expand-alt fa-up-right-and-down-left-from-center',

				'fa-solid fa-oil-can',

				'fa-solid fa-t',

				'fa-solid fa-hippo',

				'fa-solid fa-chart-column',

				'fa-solid fa-infinity',

				'fa-solid fa-vial-circle-check',

				'fa-solid fa-person-arrow-down-to-line',

				'fa-solid fa-voicemail',

				'fa-solid fa-fan',

				'fa-solid fa-person-walking-luggage',

				'fa-solid fa-arrows-alt-v fa-up-down',

				'fa-solid fa-cloud-moon-rain',

				'fa-solid fa-calendar',

				'fa-solid fa-trailer',

				'fa-solid fa-bahai fa-haykal',

				'fa-solid fa-sd-card',

				'fa-solid fa-dragon',

				'fa-solid fa-shoe-prints',

				'fa-solid fa-circle-plus fa-plus-circle',

				'fa-solid fa-face-grin-tongue-wink fa-grin-tongue-wink',

				'fa-solid fa-hand-holding',

				'fa-solid fa-plug-circle-exclamation',

				'fa-solid fa-chain-broken fa-chain-slash fa-link-slash fa-unlink',

				'fa-solid fa-clone',

				'fa-solid fa-person-walking-arrow-loop-left',

				'fa-solid fa-arrow-up-z-a fa-sort-alpha-up-alt',

				'fa-solid fa-fire-alt fa-fire-flame-curved',

				'fa-solid fa-tornado',

				'fa-solid fa-file-circle-plus',

				'fa-solid fa-book-quran fa-quran',

				'fa-solid fa-anchor',

				'fa-solid fa-border-all',

				'fa-solid fa-angry fa-face-angry',

				'fa-solid fa-cookie-bite',

				'fa-solid fa-arrow-trend-down',

				'fa-solid fa-feed fa-rss',

				'fa-solid fa-draw-polygon',

				'fa-solid fa-balance-scale fa-scale-balanced',

				'fa-solid fa-gauge-simple-high fa-tachometer-fast fa-tachometer',

				'fa-solid fa-shower',

				'fa-solid fa-desktop-alt fa-desktop',

				'fa-solid fa-m',

				'fa-solid fa-table-list fa-th-list',

				'fa-solid fa-comment-sms fa-sms',

				'fa-solid fa-book',

				'fa-solid fa-user-plus',

				'fa-solid fa-check',

				'fa-solid fa-battery-4 fa-battery-three-quarters',

				'fa-solid fa-house-circle-check',

				'fa-solid fa-angle-left',

				'fa-solid fa-diagram-successor',

				'fa-solid fa-truck-arrow-right',

				'fa-solid fa-arrows-split-up-and-left',

				'fa-solid fa-fist-raised fa-hand-fist',

				'fa-solid fa-cloud-moon',

				'fa-solid fa-briefcase',

				'fa-solid fa-person-falling',

				'fa-solid fa-image-portrait fa-portrait',

				'fa-solid fa-user-tag',

				'fa-solid fa-rug',

				'fa-solid fa-earth-europe fa-globe-europe',

				'fa-solid fa-cart-flatbed-suitcase fa-luggage-cart',

				'fa-solid fa-rectangle-times fa-rectangle-xmark fa-times-rectangle fa-window-close',

				'fa-solid fa-baht-sign',

				'fa-solid fa-book-open',

				'fa-solid fa-book-journal-whills fa-journal-whills',

				'fa-solid fa-handcuffs',

				'fa-solid fa-exclamation-triangle fa-triangle-exclamation fa-warning',

				'fa-solid fa-database',

				'fa-solid fa-arrow-turn-right fa-mail-forward fa-share',

				'fa-solid fa-bottle-droplet',

				'fa-solid fa-mask-face',

				'fa-solid fa-hill-rockslide',

				'fa-solid fa-exchange-alt fa-right-left',

				'fa-solid fa-paper-plane',

				'fa-solid fa-road-circle-exclamation',

				'fa-solid fa-dungeon',

				'fa-solid fa-align-right',

				'fa-solid fa-money-bill-1-wave fa-money-bill-wave-alt',

				'fa-solid fa-life-ring',

				'fa-solid fa-hands fa-sign-language fa-signing',

				'fa-solid fa-calendar-day',

				'fa-solid fa-ladder-water fa-swimming-pool fa-water-ladder',

				'fa-solid fa-arrows-up-down fa-arrows-v',

				'fa-solid fa-face-grimace fa-grimace',

				'fa-solid fa-wheelchair-alt fa-wheelchair-move',

				'fa-solid fa-level-down-alt fa-turn-down',

				'fa-solid fa-person-walking-arrow-right',

				'fa-solid fa-envelope-square fa-square-envelope',

				'fa-solid fa-dice',

				'fa-solid fa-bowling-ball',

				'fa-solid fa-brain',

				'fa-solid fa-band-aid fa-bandage',

				'fa-solid fa-calendar-minus',

				'fa-solid fa-circle-xmark fa-times-circle fa-xmark-circle',

				'fa-solid fa-gifts',

				'fa-solid fa-hotel',

				'fa-solid fa-earth-asia fa-globe-asia',

				'fa-solid fa-id-card-alt fa-id-card-clip',

				'fa-solid fa-magnifying-glass-plus fa-search-plus',

				'fa-solid fa-thumbs-up',

				'fa-solid fa-user-clock',

				'fa-solid fa-allergies fa-hand-dots',

				'fa-solid fa-file-invoice',

				'fa-solid fa-window-minimize',

				'fa-solid fa-coffee fa-mug-saucer',

				'fa-solid fa-brush',

				'fa-solid fa-mask',

				'fa-solid fa-magnifying-glass-minus fa-search-minus',

				'fa-solid fa-ruler-vertical',

				'fa-solid fa-user-alt fa-user-large',

				'fa-solid fa-train-tram',

				'fa-solid fa-user-nurse',

				'fa-solid fa-syringe',

				'fa-solid fa-cloud-sun',

				'fa-solid fa-stopwatch-20',

				'fa-solid fa-square-full',

				'fa-solid fa-magnet',

				'fa-solid fa-jar',

				'fa-solid fa-note-sticky fa-sticky-note',

				'fa-solid fa-bug-slash',

				'fa-solid fa-arrow-up-from-water-pump',

				'fa-solid fa-bone',

				'fa-solid fa-user-injured',

				'fa-solid fa-face-sad-tear fa-sad-tear',

				'fa-solid fa-plane',

				'fa-solid fa-tent-arrows-down',

				'fa-solid fa-exclamation',

				'fa-solid fa-arrows-spin',

				'fa-solid fa-print',

				'fa-solid fa-try fa-turkish-lira-sign fa-turkish-lira',

				'fa-solid fa-dollar-sign fa-dollar fa-usd',

				'fa-solid fa-x',

				'fa-solid fa-magnifying-glass-dollar fa-search-dollar',

				'fa-solid fa-users-cog fa-users-gear',

				'fa-solid fa-person-military-pointing',

				'fa-solid fa-bank fa-building-columns fa-institution fa-museum fa-university',

				'fa-solid fa-umbrella',

				'fa-solid fa-trowel',

				'fa-solid fa-d',

				'fa-solid fa-stapler',

				'fa-solid fa-masks-theater fa-theater-masks',

				'fa-solid fa-kip-sign',

				'fa-solid fa-hand-point-left',

				'fa-solid fa-handshake-alt fa-handshake-simple',

				'fa-solid fa-fighter-jet fa-jet-fighter',

				'fa-solid fa-share-alt-square fa-square-share-nodes',

				'fa-solid fa-barcode',

				'fa-solid fa-plus-minus',

				'fa-solid fa-video-camera fa-video',

				'fa-solid fa-graduation-cap fa-mortar-board',

				'fa-solid fa-hand-holding-medical',

				'fa-solid fa-person-circle-check',

				'fa-solid fa-level-up-alt fa-turn-up',

				'fa-solid fa-monero',

				'fa-solid fa-hooli',

				'fa-solid fa-yelp',

				'fa-solid fa-cc-visa',

				'fa-solid fa-lastfm',

				'fa-solid fa-shopware',

				'fa-solid fa-creative-commons-nc',

				'fa-solid fa-aws',

				'fa-solid fa-redhat',

				'fa-solid fa-yoast',

				'fa-solid fa-cloudflare',

				'fa-solid fa-ups',

				'fa-solid fa-wpexplorer',

				'fa-solid fa-dyalog',

				'fa-solid fa-bity',

				'fa-solid fa-stackpath',

				'fa-solid fa-buysellads',

				'fa-solid fa-first-order',

				'fa-solid fa-modx',

				'fa-solid fa-guilded',

				'fa-solid fa-vnv',

				'fa-solid fa-js-square fa-square-js',

				'fa-solid fa-microsoft',

				'fa-solid fa-qq',

				'fa-solid fa-orcid',

				'fa-solid fa-java',

				'fa-solid fa-invision',

				'fa-solid fa-creative-commons-pd-alt',

				'fa-solid fa-centercode',

				'fa-solid fa-glide-g',

				'fa-solid fa-drupal',

				'fa-solid fa-hire-a-helper',

				'fa-solid fa-creative-commons-by',

				'fa-solid fa-unity',

				'fa-solid fa-whmcs',

				'fa-solid fa-rocketchat',

				'fa-solid fa-vk',

				'fa-solid fa-untappd',

				'fa-solid fa-mailchimp',

				'fa-solid fa-css3-alt',

				'fa-solid fa-reddit-square fa-square-reddit',

				'fa-solid fa-vimeo-v',

				'fa-solid fa-contao',

				'fa-solid fa-square-font-awesome',

				'fa-solid fa-deskpro',

				'fa-solid fa-sistrix',

				'fa-solid fa-instagram-square fa-square-instagram',

				'fa-solid fa-battle-net',

				'fa-solid fa-the-red-yeti',

				'fa-solid fa-hacker-news-square fa-square-hacker-news',

				'fa-solid fa-edge',

				'fa-solid fa-threads',

				'fa-solid fa-napster',

				'fa-solid fa-snapchat-square fa-square-snapchat',

				'fa-solid fa-google-plus-g',

				'fa-solid fa-artstation',

				'fa-solid fa-markdown',

				'fa-solid fa-sourcetree',

				'fa-solid fa-google-plus',

				'fa-solid fa-diaspora',

				'fa-solid fa-foursquare',

				'fa-solid fa-stack-overflow',

				'fa-solid fa-github-alt',

				'fa-solid fa-phoenix-squadron',

				'fa-solid fa-pagelines',

				'fa-solid fa-algolia',

				'fa-solid fa-red-river',

				'fa-solid fa-creative-commons-sa',

				'fa-solid fa-safari',

				'fa-solid fa-google',

				'fa-solid fa-font-awesome-alt fa-square-font-awesome-stroke',

				'fa-solid fa-atlassian',

				'fa-solid fa-linkedin-in',

				'fa-solid fa-digital-ocean',

				'fa-solid fa-nimblr',

				'fa-solid fa-chromecast',

				'fa-solid fa-evernote',

				'fa-solid fa-hacker-news',

				'fa-solid fa-creative-commons-sampling',

				'fa-solid fa-adversal',

				'fa-solid fa-creative-commons',

				'fa-solid fa-watchman-monitoring',

				'fa-solid fa-fonticons',

				'fa-solid fa-weixin',

				'fa-solid fa-shirtsinbulk',

				'fa-solid fa-codepen',

				'fa-solid fa-git-alt',

				'fa-solid fa-lyft',

				'fa-solid fa-rev',

				'fa-solid fa-windows',

				'fa-solid fa-wizards-of-the-coast',

				'fa-solid fa-square-viadeo fa-viadeo-square',

				'fa-solid fa-meetup',

				'fa-solid fa-centos',

				'fa-solid fa-adn',

				'fa-solid fa-cloudsmith',

				'fa-solid fa-pied-piper-alt',

				'fa-solid fa-dribbble-square fa-square-dribbble',

				'fa-solid fa-codiepie',

				'fa-solid fa-node',

				'fa-solid fa-mix',

				'fa-solid fa-steam',

				'fa-solid fa-cc-apple-pay',

				'fa-solid fa-scribd',

				'fa-solid fa-debian',

				'fa-solid fa-openid',

				'fa-solid fa-instalod',

				'fa-solid fa-expeditedssl',

				'fa-solid fa-sellcast',

				'fa-solid fa-square-twitter fa-twitter-square',

				'fa-solid fa-r-project',

				'fa-solid fa-delicious',

				'fa-solid fa-freebsd',

				'fa-solid fa-vuejs',

				'fa-solid fa-accusoft',

				'fa-solid fa-ioxhost',

				'fa-solid fa-fonticons-fi',

				'fa-solid fa-app-store',

				'fa-solid fa-cc-mastercard',

				'fa-solid fa-itunes-note',

				'fa-solid fa-golang',

				'fa-solid fa-kickstarter',

				'fa-solid fa-grav',

				'fa-solid fa-weibo',

				'fa-solid fa-uncharted',

				'fa-solid fa-firstdraft',

				'fa-solid fa-square-youtube fa-youtube-square',

				'fa-solid fa-wikipedia-w',

				'fa-solid fa-rendact fa-wpressr',

				'fa-solid fa-angellist',

				'fa-solid fa-galactic-republic',

				'fa-solid fa-nfc-directional',

				'fa-solid fa-skype',

				'fa-solid fa-joget',

				'fa-solid fa-fedora',

				'fa-solid fa-stripe-s',

				'fa-solid fa-meta',

				'fa-solid fa-laravel',

				'fa-solid fa-hotjar',

				'fa-solid fa-bluetooth-b',

				'fa-solid fa-sticker-mule',

				'fa-solid fa-creative-commons-zero',

				'fa-solid fa-hips',

				'fa-solid fa-behance',

				'fa-solid fa-reddit',

				'fa-solid fa-discord',

				'fa-solid fa-chrome',

				'fa-solid fa-app-store-ios',

				'fa-solid fa-cc-discover',

				'fa-solid fa-wpbeginner',

				'fa-solid fa-confluence',

				'fa-solid fa-mdb',

				'fa-solid fa-dochub',

				'fa-solid fa-accessible-icon',

				'fa-solid fa-ebay',

				'fa-solid fa-amazon',

				'fa-solid fa-unsplash',

				'fa-solid fa-yarn',

				'fa-solid fa-square-steam fa-steam-square',

				'fa-solid fa-500px',

				'fa-solid fa-square-vimeo fa-vimeo-square',

				'fa-solid fa-asymmetrik',

				'fa-solid fa-font-awesome-flag fa-font-awesome-logo-full fa-font-awesome',

				'fa-solid fa-gratipay',

				'fa-solid fa-apple',

				'fa-solid fa-hive',

				'fa-solid fa-gitkraken',

				'fa-solid fa-keybase',

				'fa-solid fa-apple-pay',

				'fa-solid fa-padlet',

				'fa-solid fa-amazon-pay',

				'fa-solid fa-github-square fa-square-github',

				'fa-solid fa-stumbleupon',

				'fa-solid fa-fedex',

				'fa-solid fa-phoenix-framework',

				'fa-solid fa-shopify',

				'fa-solid fa-neos',

				'fa-solid fa-square-threads',

				'fa-solid fa-hackerrank',

				'fa-solid fa-researchgate',

				'fa-solid fa-swift',

				'fa-solid fa-angular',

				'fa-solid fa-speakap',

				'fa-solid fa-angrycreative',

				'fa-solid fa-y-combinator',

				'fa-solid fa-empire',

				'fa-solid fa-envira',

				'fa-solid fa-gitlab-square fa-square-gitlab',

				'fa-solid fa-studiovinari',

				'fa-solid fa-pied-piper',

				'fa-solid fa-wordpress',

				'fa-solid fa-product-hunt',

				'fa-solid fa-firefox',

				'fa-solid fa-linode',

				'fa-solid fa-goodreads',

				'fa-solid fa-odnoklassniki-square fa-square-odnoklassniki',

				'fa-solid fa-jsfiddle',

				'fa-solid fa-sith',

				'fa-solid fa-themeisle',

				'fa-solid fa-page4',

				'fa-solid fa-hashnode',

				'fa-solid fa-react',

				'fa-solid fa-cc-paypal',

				'fa-solid fa-squarespace',

				'fa-solid fa-cc-stripe',

				'fa-solid fa-creative-commons-share',

				'fa-solid fa-bitcoin',

				'fa-solid fa-keycdn',

				'fa-solid fa-opera',

				'fa-solid fa-itch-io',

				'fa-solid fa-umbraco',

				'fa-solid fa-galactic-senate',

				'fa-solid fa-ubuntu',

				'fa-solid fa-draft2digital',

				'fa-solid fa-stripe',

				'fa-solid fa-houzz',

				'fa-solid fa-gg',

				'fa-solid fa-dhl',

				'fa-solid fa-pinterest-square fa-square-pinterest',

				'fa-solid fa-xing',

				'fa-solid fa-blackberry',

				'fa-solid fa-creative-commons-pd',

				'fa-solid fa-playstation',

				'fa-solid fa-quinscape',

				'fa-solid fa-less',

				'fa-solid fa-blogger-b',

				'fa-solid fa-opencart',

				'fa-solid fa-vine',

				'fa-solid fa-paypal',

				'fa-solid fa-gitlab',

				'fa-solid fa-typo3',

				'fa-solid fa-reddit-alien',

				'fa-solid fa-yahoo',

				'fa-solid fa-dailymotion',

				'fa-solid fa-affiliatetheme',

				'fa-solid fa-pied-piper-pp',

				'fa-solid fa-bootstrap',

				'fa-solid fa-odnoklassniki',

				'fa-solid fa-nfc-symbol',

				'fa-solid fa-ethereum',

				'fa-solid fa-speaker-deck',

				'fa-solid fa-creative-commons-nc-eu',

				'fa-solid fa-patreon',

				'fa-solid fa-avianex',

				'fa-solid fa-ello',

				'fa-solid fa-gofore',

				'fa-solid fa-bimobject',

				'fa-solid fa-facebook-f',

				'fa-solid fa-google-plus-square fa-square-google-plus',

				'fa-solid fa-mandalorian',

				'fa-solid fa-first-order-alt',

				'fa-solid fa-osi',

				'fa-solid fa-google-wallet',

				'fa-solid fa-d-and-d-beyond',

				'fa-solid fa-periscope',

				'fa-solid fa-fulcrum',

				'fa-solid fa-cloudscale',

				'fa-solid fa-forumbee',

				'fa-solid fa-mizuni',

				'fa-solid fa-schlix',

				'fa-solid fa-square-xing fa-xing-square',

				'fa-solid fa-bandcamp',

				'fa-solid fa-wpforms',

				'fa-solid fa-cloudversify',

				'fa-solid fa-usps',

				'fa-solid fa-megaport',

				'fa-solid fa-magento',

				'fa-solid fa-spotify',

				'fa-solid fa-optin-monster',

				'fa-solid fa-fly',

				'fa-solid fa-aviato',

				'fa-solid fa-itunes',

				'fa-solid fa-cuttlefish',

				'fa-solid fa-blogger',

				'fa-solid fa-flickr',

				'fa-solid fa-viber',

				'fa-solid fa-soundcloud',

				'fa-solid fa-digg',

				'fa-solid fa-tencent-weibo',

				'fa-solid fa-symfony',

				'fa-solid fa-maxcdn',

				'fa-solid fa-etsy',

				'fa-solid fa-facebook-messenger',

				'fa-solid fa-audible',

				'fa-solid fa-think-peaks',

				'fa-solid fa-bilibili',

				'fa-solid fa-erlang',

				'fa-solid fa-x-twitter',

				'fa-solid fa-cotton-bureau',

				'fa-solid fa-dashcube',

				'fa-solid fa-42-group fa-innosoft',

				'fa-solid fa-stack-exchange',

				'fa-solid fa-elementor',

				'fa-solid fa-pied-piper-square fa-square-pied-piper',

				'fa-solid fa-creative-commons-nd',

				'fa-solid fa-palfed',

				'fa-solid fa-superpowers',

				'fa-solid fa-resolving',

				'fa-solid fa-xbox',

				'fa-solid fa-searchengin',

				'fa-solid fa-tiktok',

				'fa-solid fa-facebook-square fa-square-facebook',

				'fa-solid fa-renren',

				'fa-solid fa-linux',

				'fa-solid fa-glide',

				'fa-solid fa-linkedin',

				'fa-solid fa-hubspot',

				'fa-solid fa-deploydog',

				'fa-solid fa-twitch',

				'fa-solid fa-ravelry',

				'fa-solid fa-mixer',

				'fa-solid fa-lastfm-square fa-square-lastfm',

				'fa-solid fa-vimeo',

				'fa-solid fa-mendeley',

				'fa-solid fa-uniregistry',

				'fa-solid fa-figma',

				'fa-solid fa-creative-commons-remix',

				'fa-solid fa-cc-amazon-pay',

				'fa-solid fa-dropbox',

				'fa-solid fa-instagram',

				'fa-solid fa-cmplid',

				'fa-solid fa-facebook',

				'fa-solid fa-gripfire',

				'fa-solid fa-jedi-order',

				'fa-solid fa-uikit',

				'fa-solid fa-fort-awesome-alt',

				'fa-solid fa-phabricator',

				'fa-solid fa-ussunnah',

				'fa-solid fa-earlybirds',

				'fa-solid fa-trade-federation',

				'fa-solid fa-autoprefixer',

				'fa-solid fa-whatsapp',

				'fa-solid fa-slideshare',

				'fa-solid fa-google-play',

				'fa-solid fa-viadeo',

				'fa-solid fa-line',

				'fa-solid fa-google-drive',

				'fa-solid fa-servicestack',

				'fa-solid fa-simplybuilt',

				'fa-solid fa-bitbucket',

				'fa-solid fa-imdb',

				'fa-solid fa-deezer',

				'fa-solid fa-raspberry-pi',

				'fa-solid fa-jira',

				'fa-solid fa-docker',

				'fa-solid fa-screenpal',

				'fa-solid fa-bluetooth',

				'fa-solid fa-gitter',

				'fa-solid fa-d-and-d',

				'fa-solid fa-microblog',

				'fa-solid fa-cc-diners-club',

				'fa-solid fa-gg-circle',

				'fa-solid fa-pied-piper-hat',

				'fa-solid fa-kickstarter-k',

				'fa-solid fa-yandex',

				'fa-solid fa-readme',

				'fa-solid fa-html5',

				'fa-solid fa-sellsy',

				'fa-solid fa-sass',

				'fa-solid fa-wirsindhandwerk fa-wsh',

				'fa-solid fa-buromobelexperte',

				'fa-solid fa-salesforce',

				'fa-solid fa-octopus-deploy',

				'fa-solid fa-medapps',

				'fa-solid fa-ns8',

				'fa-solid fa-pinterest-p',

				'fa-solid fa-apper',

				'fa-solid fa-fort-awesome',

				'fa-solid fa-waze',

				'fa-solid fa-cc-jcb',

				'fa-solid fa-snapchat-ghost fa-snapchat',

				'fa-solid fa-fantasy-flight-games',

				'fa-solid fa-rust',

				'fa-solid fa-wix',

				'fa-solid fa-behance-square fa-square-behance',

				'fa-solid fa-supple',

				'fa-solid fa-rebel',

				'fa-solid fa-css3',

				'fa-solid fa-staylinked',

				'fa-solid fa-kaggle',

				'fa-solid fa-space-awesome',

				'fa-solid fa-deviantart',

				'fa-solid fa-cpanel',

				'fa-solid fa-goodreads-g',

				'fa-solid fa-git-square fa-square-git',

				'fa-solid fa-square-tumblr fa-tumblr-square',

				'fa-solid fa-trello',

				'fa-solid fa-creative-commons-nc-jp',

				'fa-solid fa-get-pocket',

				'fa-solid fa-perbyte',

				'fa-solid fa-grunt',

				'fa-solid fa-weebly',

				'fa-solid fa-connectdevelop',

				'fa-solid fa-leanpub',

				'fa-solid fa-black-tie',

				'fa-solid fa-themeco',

				'fa-solid fa-python',

				'fa-solid fa-android',

				'fa-solid fa-bots',

				'fa-solid fa-free-code-camp',

				'fa-solid fa-hornbill',

				'fa-solid fa-js',

				'fa-solid fa-ideal',

				'fa-solid fa-git',

				'fa-solid fa-dev',

				'fa-solid fa-sketch',

				'fa-solid fa-yandex-international',

				'fa-solid fa-cc-amex',

				'fa-solid fa-uber',

				'fa-solid fa-github',

				'fa-solid fa-php',

				'fa-solid fa-alipay',

				'fa-solid fa-youtube',

				'fa-solid fa-skyatlas',

				'fa-solid fa-firefox-browser',

				'fa-solid fa-replyd',

				'fa-solid fa-suse',

				'fa-solid fa-jenkins',

				'fa-solid fa-twitter',

				'fa-solid fa-rockrms',

				'fa-solid fa-pinterest',

				'fa-solid fa-buffer',

				'fa-solid fa-npm',

				'fa-solid fa-yammer',

				'fa-solid fa-btc',

				'fa-solid fa-dribbble',

				'fa-solid fa-stumbleupon-circle',

				'fa-solid fa-internet-explorer',

				'fa-solid fa-stubber',

				'fa-solid fa-telegram-plane fa-telegram',

				'fa-solid fa-old-republic',

				'fa-solid fa-odysee',

				'fa-solid fa-square-whatsapp fa-whatsapp-square',

				'fa-solid fa-node-js',

				'fa-solid fa-edge-legacy',

				'fa-solid fa-slack-hash fa-slack',

				'fa-solid fa-medrt',

				'fa-solid fa-usb',

				'fa-solid fa-tumblr',

				'fa-solid fa-vaadin',

				'fa-solid fa-quora',

				'fa-solid fa-square-x-twitter',

				'fa-solid fa-reacteurope',

				'fa-solid fa-medium-m fa-medium',

				'fa-solid fa-amilia',

				'fa-solid fa-mixcloud',

				'fa-solid fa-flipboard',

				'fa-solid fa-viacoin',

				'fa-solid fa-critical-role',

				'fa-solid fa-sitrox',

				'fa-solid fa-discourse',

				'fa-solid fa-joomla',

				'fa-solid fa-mastodon',

				'fa-solid fa-airbnb',

				'fa-solid fa-wolf-pack-battalion',

				'fa-solid fa-buy-n-large',

				'fa-solid fa-gulp',

				'fa-solid fa-creative-commons-sampling-plus',

				'fa-solid fa-strava',

				'fa-solid fa-ember',

				'fa-solid fa-canadian-maple-leaf',

				'fa-solid fa-teamspeak',

				'fa-solid fa-pushed',

				'fa-solid fa-wordpress-simple',

				'fa-solid fa-nutritionix',

				'fa-solid fa-wodu',

				'fa-solid fa-google-pay',

				'fa-solid fa-intercom',

				'fa-solid fa-zhihu',

				'fa-solid fa-korvue',

				'fa-solid fa-pix',

				'fa-solid fa-steam-symbol',
			);

			return $icons;
		}

		public function remix_icon() {
			$remix_icons = array(
				'ri-24-hours-fill',
				'ri-24-hours-line',
				'ri-4k-fill',
				'ri-4k-line',
				'ri-a-b',
				'ri-account-box-fill',
				'ri-account-box-line',
				'ri-account-circle-fill',
				'ri-account-circle-line',
				'ri-account-pin-box-fill',
				'ri-account-pin-box-line',
				'ri-account-pin-circle-fill',
				'ri-account-pin-circle-line',
				'ri-add-box-fill',
				'ri-add-box-line',
				'ri-add-circle-fill',
				'ri-add-circle-line',
				'ri-add-fill',
				'ri-add-line',
				'ri-admin-fill',
				'ri-admin-line',
				'ri-advertisement-fill',
				'ri-advertisement-line',
				'ri-airplay-fill',
				'ri-airplay-line',
				'ri-alarm-fill',
				'ri-alarm-line',
				'ri-alarm-warning-fill',
				'ri-alarm-warning-line',
				'ri-album-fill',
				'ri-album-line',
				'ri-alert-fill',
				'ri-alert-line',
				'ri-aliens-fill',
				'ri-aliens-line',
				'ri-align-bottom',
				'ri-align-center',
				'ri-align-justify',
				'ri-align-left',
				'ri-align-right',
				'ri-align-top',
				'ri-align-vertically',
				'ri-alipay-fill',
				'ri-alipay-line',
				'ri-amazon-fill',
				'ri-amazon-line',
				'ri-anchor-fill',
				'ri-anchor-line',
				'ri-ancient-gate-fill',
				'ri-ancient-gate-line',
				'ri-ancient-pavilion-fill',
				'ri-ancient-pavilion-line',
				'ri-android-fill',
				'ri-android-line',
				'ri-angularjs-fill',
				'ri-angularjs-line',
				'ri-anticlockwise-2-fill',
				'ri-anticlockwise-2-line',
				'ri-anticlockwise-fill',
				'ri-anticlockwise-line',
				'ri-app-store-fill',
				'ri-app-store-line',
				'ri-apple-fill',
				'ri-apple-line',
				'ri-apps-2-fill',
				'ri-apps-2-line',
				'ri-apps-fill',
				'ri-apps-line',
				'ri-archive-drawer-fill',
				'ri-archive-drawer-line',
				'ri-archive-fill',
				'ri-archive-line',
				'ri-arrow-down-circle-fill',
				'ri-arrow-down-circle-line',
				'ri-arrow-down-fill',
				'ri-arrow-down-line',
				'ri-arrow-down-s-fill',
				'ri-arrow-down-s-line',
				'ri-arrow-drop-down-fill',
				'ri-arrow-drop-down-line',
				'ri-arrow-drop-left-fill',
				'ri-arrow-drop-left-line',
				'ri-arrow-drop-right-fill',
				'ri-arrow-drop-right-line',
				'ri-arrow-drop-up-fill',
				'ri-arrow-drop-up-line',
				'ri-arrow-go-back-fill',
				'ri-arrow-go-back-line',
				'ri-arrow-go-forward-fill',
				'ri-arrow-go-forward-line',
				'ri-arrow-left-circle-fill',
				'ri-arrow-left-circle-line',
				'ri-arrow-left-down-fill',
				'ri-arrow-left-down-line',
				'ri-arrow-left-fill',
				'ri-arrow-left-line',
				'ri-arrow-left-right-fill',
				'ri-arrow-left-right-line',
				'ri-arrow-left-s-fill',
				'ri-arrow-left-s-line',
				'ri-arrow-left-up-fill',
				'ri-arrow-left-up-line',
				'ri-arrow-right-circle-fill',
				'ri-arrow-right-circle-line',
				'ri-arrow-right-down-fill',
				'ri-arrow-right-down-line',
				'ri-arrow-right-fill',
				'ri-arrow-right-line',
				'ri-arrow-right-s-fill',
				'ri-arrow-right-s-line',
				'ri-arrow-right-up-fill',
				'ri-arrow-right-up-line',
				'ri-arrow-up-circle-fill',
				'ri-arrow-up-circle-line',
				'ri-arrow-up-down-fill',
				'ri-arrow-up-down-line',
				'ri-arrow-up-fill',
				'ri-arrow-up-line',
				'ri-arrow-up-s-fill',
				'ri-arrow-up-s-line',
				'ri-artboard-2-fill',
				'ri-artboard-2-line',
				'ri-artboard-fill',
				'ri-artboard-line',
				'ri-article-fill',
				'ri-article-line',
				'ri-aspect-ratio-fill',
				'ri-aspect-ratio-line',
				'ri-asterisk',
				'ri-at-fill',
				'ri-at-line',
				'ri-attachment-2',
				'ri-attachment-fill',
				'ri-attachment-line',
				'ri-auction-fill',
				'ri-auction-line',
				'ri-award-fill',
				'ri-award-line',
				'ri-baidu-fill',
				'ri-baidu-line',
				'ri-ball-pen-fill',
				'ri-ball-pen-line',
				'ri-bank-card-2-fill',
				'ri-bank-card-2-line',
				'ri-bank-card-fill',
				'ri-bank-card-line',
				'ri-bank-fill',
				'ri-bank-line',
				'ri-bar-chart-2-fill',
				'ri-bar-chart-2-line',
				'ri-bar-chart-box-fill',
				'ri-bar-chart-box-line',
				'ri-bar-chart-fill',
				'ri-bar-chart-grouped-fill',
				'ri-bar-chart-grouped-line',
				'ri-bar-chart-horizontal-fill',
				'ri-bar-chart-horizontal-line',
				'ri-bar-chart-line',
				'ri-barcode-box-fill',
				'ri-barcode-box-line',
				'ri-barcode-fill',
				'ri-barcode-line',
				'ri-barricade-fill',
				'ri-barricade-line',
				'ri-base-station-fill',
				'ri-base-station-line',
				'ri-basketball-fill',
				'ri-basketball-line',
				'ri-battery-2-charge-fill',
				'ri-battery-2-charge-line',
				'ri-battery-2-fill',
				'ri-battery-2-line',
				'ri-battery-charge-fill',
				'ri-battery-charge-line',
				'ri-battery-fill',
				'ri-battery-line',
				'ri-battery-low-fill',
				'ri-battery-low-line',
				'ri-battery-saver-fill',
				'ri-battery-saver-line',
				'ri-battery-share-fill',
				'ri-battery-share-line',
				'ri-bear-smile-fill',
				'ri-bear-smile-line',
				'ri-behance-fill',
				'ri-behance-line',
				'ri-bell-fill',
				'ri-bell-line',
				'ri-bike-fill',
				'ri-bike-line',
				'ri-bilibili-fill',
				'ri-bilibili-line',
				'ri-bill-fill',
				'ri-bill-line',
				'ri-billiards-fill',
				'ri-billiards-line',
				'ri-bit-coin-fill',
				'ri-bit-coin-line',
				'ri-blaze-fill',
				'ri-blaze-line',
				'ri-bluetooth-connect-fill',
				'ri-bluetooth-connect-line',
				'ri-bluetooth-fill',
				'ri-bluetooth-line',
				'ri-blur-off-fill',
				'ri-blur-off-line',
				'ri-body-scan-fill',
				'ri-body-scan-line',
				'ri-bold',
				'ri-book-2-fill',
				'ri-book-2-line',
				'ri-book-3-fill',
				'ri-book-3-line',
				'ri-book-fill',
				'ri-book-line',
				'ri-book-mark-fill',
				'ri-book-mark-line',
				'ri-book-open-fill',
				'ri-book-open-line',
				'ri-book-read-fill',
				'ri-book-read-line',
				'ri-booklet-fill',
				'ri-booklet-line',
				'ri-bookmark-2-fill',
				'ri-bookmark-2-line',
				'ri-bookmark-3-fill',
				'ri-bookmark-3-line',
				'ri-bookmark-fill',
				'ri-bookmark-line',
				'ri-boxing-fill',
				'ri-boxing-line',
				'ri-braces-fill',
				'ri-braces-line',
				'ri-brackets-fill',
				'ri-brackets-line',
				'ri-briefcase-2-fill',
				'ri-briefcase-2-line',
				'ri-briefcase-3-fill',
				'ri-briefcase-3-line',
				'ri-briefcase-4-fill',
				'ri-briefcase-4-line',
				'ri-briefcase-5-fill',
				'ri-briefcase-5-line',
				'ri-briefcase-fill',
				'ri-briefcase-line',
				'ri-bring-forward',
				'ri-bring-to-front',
				'ri-broadcast-fill',
				'ri-broadcast-line',
				'ri-brush-2-fill',
				'ri-brush-2-line',
				'ri-brush-3-fill',
				'ri-brush-3-line',
				'ri-brush-4-fill',
				'ri-brush-4-line',
				'ri-brush-fill',
				'ri-brush-line',
				'ri-bubble-chart-fill',
				'ri-bubble-chart-line',
				'ri-bug-2-fill',
				'ri-bug-2-line',
				'ri-bug-fill',
				'ri-bug-line',
				'ri-building-2-fill',
				'ri-building-2-line',
				'ri-building-3-fill',
				'ri-building-3-line',
				'ri-building-4-fill',
				'ri-building-4-line',
				'ri-building-fill',
				'ri-building-line',
				'ri-bus-2-fill',
				'ri-bus-2-line',
				'ri-bus-fill',
				'ri-bus-line',
				'ri-bus-wifi-fill',
				'ri-bus-wifi-line',
				'ri-cactus-fill',
				'ri-cactus-line',
				'ri-cake-2-fill',
				'ri-cake-2-line',
				'ri-cake-3-fill',
				'ri-cake-3-line',
				'ri-cake-fill',
				'ri-cake-line',
				'ri-calculator-fill',
				'ri-calculator-line',
				'ri-calendar-2-fill',
				'ri-calendar-2-line',
				'ri-calendar-check-fill',
				'ri-calendar-check-line',
				'ri-calendar-event-fill',
				'ri-calendar-event-line',
				'ri-calendar-fill',
				'ri-calendar-line',
				'ri-calendar-todo-fill',
				'ri-calendar-todo-line',
				'ri-camera-2-fill',
				'ri-camera-2-line',
				'ri-camera-3-fill',
				'ri-camera-3-line',
				'ri-camera-fill',
				'ri-camera-lens-fill',
				'ri-camera-lens-line',
				'ri-camera-line',
				'ri-camera-off-fill',
				'ri-camera-off-line',
				'ri-camera-switch-fill',
				'ri-camera-switch-line',
				'ri-capsule-fill',
				'ri-capsule-line',
				'ri-car-fill',
				'ri-car-line',
				'ri-car-washing-fill',
				'ri-car-washing-line',
				'ri-caravan-fill',
				'ri-caravan-line',
				'ri-cast-fill',
				'ri-cast-line',
				'ri-cellphone-fill',
				'ri-cellphone-line',
				'ri-celsius-fill',
				'ri-celsius-line',
				'ri-centos-fill',
				'ri-centos-line',
				'ri-character-recognition-fill',
				'ri-character-recognition-line',
				'ri-charging-pile-2-fill',
				'ri-charging-pile-2-line',
				'ri-charging-pile-fill',
				'ri-charging-pile-line',
				'ri-chat-1-fill',
				'ri-chat-1-line',
				'ri-chat-2-fill',
				'ri-chat-2-line',
				'ri-chat-3-fill',
				'ri-chat-3-line',
				'ri-chat-4-fill',
				'ri-chat-4-line',
				'ri-chat-check-fill',
				'ri-chat-check-line',
				'ri-chat-delete-fill',
				'ri-chat-delete-line',
				'ri-chat-download-fill',
				'ri-chat-download-line',
				'ri-chat-follow-up-fill',
				'ri-chat-follow-up-line',
				'ri-chat-forward-fill',
				'ri-chat-forward-line',
				'ri-chat-heart-fill',
				'ri-chat-heart-line',
				'ri-chat-history-fill',
				'ri-chat-history-line',
				'ri-chat-new-fill',
				'ri-chat-new-line',
				'ri-chat-off-fill',
				'ri-chat-off-line',
				'ri-chat-poll-fill',
				'ri-chat-poll-line',
				'ri-chat-private-fill',
				'ri-chat-private-line',
				'ri-chat-quote-fill',
				'ri-chat-quote-line',
				'ri-chat-settings-fill',
				'ri-chat-settings-line',
				'ri-chat-smile-2-fill',
				'ri-chat-smile-2-line',
				'ri-chat-smile-3-fill',
				'ri-chat-smile-3-line',
				'ri-chat-smile-fill',
				'ri-chat-smile-line',
				'ri-chat-upload-fill',
				'ri-chat-upload-line',
				'ri-chat-voice-fill',
				'ri-chat-voice-line',
				'ri-check-double-fill',
				'ri-check-double-line',
				'ri-check-fill',
				'ri-check-line',
				'ri-checkbox-blank-circle-fill',
				'ri-checkbox-blank-circle-line',
				'ri-checkbox-blank-fill',
				'ri-checkbox-blank-line',
				'ri-checkbox-circle-fill',
				'ri-checkbox-circle-line',
				'ri-checkbox-fill',
				'ri-checkbox-indeterminate-fill',
				'ri-checkbox-indeterminate-line',
				'ri-checkbox-line',
				'ri-checkbox-multiple-blank-fill',
				'ri-checkbox-multiple-blank-line',
				'ri-checkbox-multiple-fill',
				'ri-checkbox-multiple-line',
				'ri-china-railway-fill',
				'ri-china-railway-line',
				'ri-chrome-fill',
				'ri-chrome-line',
				'ri-clapperboard-fill',
				'ri-clapperboard-line',
				'ri-clipboard-fill',
				'ri-clipboard-line',
				'ri-clockwise-2-fill',
				'ri-clockwise-2-line',
				'ri-clockwise-fill',
				'ri-clockwise-line',
				'ri-close-circle-fill',
				'ri-close-circle-line',
				'ri-close-fill',
				'ri-close-line',
				'ri-closed-captioning-fill',
				'ri-closed-captioning-line',
				'ri-cloud-fill',
				'ri-cloud-line',
				'ri-cloud-off-fill',
				'ri-cloud-off-line',
				'ri-cloud-windy-fill',
				'ri-cloud-windy-line',
				'ri-cloudy-2-fill',
				'ri-cloudy-2-line',
				'ri-cloudy-fill',
				'ri-cloudy-line',
				'ri-code-box-fill',
				'ri-code-box-line',
				'ri-code-fill',
				'ri-code-line',
				'ri-code-s-fill',
				'ri-code-s-line',
				'ri-code-s-slash-fill',
				'ri-code-s-slash-line',
				'ri-code-view',
				'ri-codepen-fill',
				'ri-codepen-line',
				'ri-coin-fill',
				'ri-coin-line',
				'ri-coins-fill',
				'ri-coins-line',
				'ri-collage-fill',
				'ri-collage-line',
				'ri-command-fill',
				'ri-command-line',
				'ri-community-fill',
				'ri-community-line',
				'ri-compass-2-fill',
				'ri-compass-2-line',
				'ri-compass-3-fill',
				'ri-compass-3-line',
				'ri-compass-4-fill',
				'ri-compass-4-line',
				'ri-compass-discover-fill',
				'ri-compass-discover-line',
				'ri-compass-fill',
				'ri-compass-line',
				'ri-compasses-2-fill',
				'ri-compasses-2-line',
				'ri-compasses-fill',
				'ri-compasses-line',
				'ri-computer-fill',
				'ri-computer-line',
				'ri-contacts-book-2-fill',
				'ri-contacts-book-2-line',
				'ri-contacts-book-fill',
				'ri-contacts-book-line',
				'ri-contacts-book-upload-fill',
				'ri-contacts-book-upload-line',
				'ri-contacts-fill',
				'ri-contacts-line',
				'ri-contrast-2-fill',
				'ri-contrast-2-line',
				'ri-contrast-drop-2-fill',
				'ri-contrast-drop-2-line',
				'ri-contrast-drop-fill',
				'ri-contrast-drop-line',
				'ri-contrast-fill',
				'ri-contrast-line',
				'ri-copper-coin-fill',
				'ri-copper-coin-line',
				'ri-copper-diamond-fill',
				'ri-copper-diamond-line',
				'ri-copyleft-fill',
				'ri-copyleft-line',
				'ri-copyright-fill',
				'ri-copyright-line',
				'ri-coreos-fill',
				'ri-coreos-line',
				'ri-coupon-2-fill',
				'ri-coupon-2-line',
				'ri-coupon-3-fill',
				'ri-coupon-3-line',
				'ri-coupon-4-fill',
				'ri-coupon-4-line',
				'ri-coupon-5-fill',
				'ri-coupon-5-line',
				'ri-coupon-fill',
				'ri-coupon-line',
				'ri-cpu-fill',
				'ri-cpu-line',
				'ri-creative-commons-by-fill',
				'ri-creative-commons-by-line',
				'ri-creative-commons-fill',
				'ri-creative-commons-line',
				'ri-creative-commons-nc-fill',
				'ri-creative-commons-nc-line',
				'ri-creative-commons-nd-fill',
				'ri-creative-commons-nd-line',
				'ri-creative-commons-sa-fill',
				'ri-creative-commons-sa-line',
				'ri-creative-commons-zero-fill',
				'ri-creative-commons-zero-line',
				'ri-criminal-fill',
				'ri-criminal-line',
				'ri-crop-2-fill',
				'ri-crop-2-line',
				'ri-crop-fill',
				'ri-crop-line',
				'ri-css3-fill',
				'ri-css3-line',
				'ri-cup-fill',
				'ri-cup-line',
				'ri-currency-fill',
				'ri-currency-line',
				'ri-cursor-fill',
				'ri-cursor-line',
				'ri-customer-service-2-fill',
				'ri-customer-service-2-line',
				'ri-customer-service-fill',
				'ri-customer-service-line',
				'ri-dashboard-2-fill',
				'ri-dashboard-2-line',
				'ri-dashboard-3-fill',
				'ri-dashboard-3-line',
				'ri-dashboard-fill',
				'ri-dashboard-line',
				'ri-database-2-fill',
				'ri-database-2-line',
				'ri-database-fill',
				'ri-database-line',
				'ri-delete-back-2-fill',
				'ri-delete-back-2-line',
				'ri-delete-back-fill',
				'ri-delete-back-line',
				'ri-delete-bin-2-fill',
				'ri-delete-bin-2-line',
				'ri-delete-bin-3-fill',
				'ri-delete-bin-3-line',
				'ri-delete-bin-4-fill',
				'ri-delete-bin-4-line',
				'ri-delete-bin-5-fill',
				'ri-delete-bin-5-line',
				'ri-delete-bin-6-fill',
				'ri-delete-bin-6-line',
				'ri-delete-bin-7-fill',
				'ri-delete-bin-7-line',
				'ri-delete-bin-fill',
				'ri-delete-bin-line',
				'ri-delete-column',
				'ri-delete-row',
				'ri-device-fill',
				'ri-device-line',
				'ri-device-recover-fill',
				'ri-device-recover-line',
				'ri-dingding-fill',
				'ri-dingding-line',
				'ri-direction-fill',
				'ri-direction-line',
				'ri-disc-fill',
				'ri-disc-line',
				'ri-discord-fill',
				'ri-discord-line',
				'ri-discuss-fill',
				'ri-discuss-line',
				'ri-dislike-fill',
				'ri-dislike-line',
				'ri-disqus-fill',
				'ri-disqus-line',
				'ri-divide-fill',
				'ri-divide-line',
				'ri-donut-chart-fill',
				'ri-donut-chart-line',
				'ri-door-closed-fill',
				'ri-door-closed-line',
				'ri-door-fill',
				'ri-door-line',
				'ri-door-lock-box-fill',
				'ri-door-lock-box-line',
				'ri-door-lock-fill',
				'ri-door-lock-line',
				'ri-door-open-fill',
				'ri-door-open-line',
				'ri-dossier-fill',
				'ri-dossier-line',
				'ri-douban-fill',
				'ri-douban-line',
				'ri-double-quotes-l',
				'ri-double-quotes-r',
				'ri-download-2-fill',
				'ri-download-2-line',
				'ri-download-cloud-2-fill',
				'ri-download-cloud-2-line',
				'ri-download-cloud-fill',
				'ri-download-cloud-line',
				'ri-download-fill',
				'ri-download-line',
				'ri-draft-fill',
				'ri-draft-line',
				'ri-drag-drop-fill',
				'ri-drag-drop-line',
				'ri-drag-move-2-fill',
				'ri-drag-move-2-line',
				'ri-drag-move-fill',
				'ri-drag-move-line',
				'ri-dribbble-fill',
				'ri-dribbble-line',
				'ri-drive-fill',
				'ri-drive-line',
				'ri-drizzle-fill',
				'ri-drizzle-line',
				'ri-drop-fill',
				'ri-drop-line',
				'ri-dropbox-fill',
				'ri-dropbox-line',
				'ri-dual-sim-1-fill',
				'ri-dual-sim-1-line',
				'ri-dual-sim-2-fill',
				'ri-dual-sim-2-line',
				'ri-dv-fill',
				'ri-dv-line',
				'ri-dvd-fill',
				'ri-dvd-line',
				'ri-e-bike-2-fill',
				'ri-e-bike-2-line',
				'ri-e-bike-fill',
				'ri-e-bike-line',
				'ri-earth-fill',
				'ri-earth-line',
				'ri-earthquake-fill',
				'ri-earthquake-line',
				'ri-edge-fill',
				'ri-edge-line',
				'ri-edit-2-fill',
				'ri-edit-2-line',
				'ri-edit-box-fill',
				'ri-edit-box-line',
				'ri-edit-circle-fill',
				'ri-edit-circle-line',
				'ri-edit-fill',
				'ri-edit-line',
				'ri-eject-fill',
				'ri-eject-line',
				'ri-emotion-2-fill',
				'ri-emotion-2-line',
				'ri-emotion-fill',
				'ri-emotion-happy-fill',
				'ri-emotion-happy-line',
				'ri-emotion-laugh-fill',
				'ri-emotion-laugh-line',
				'ri-emotion-line',
				'ri-emotion-normal-fill',
				'ri-emotion-normal-line',
				'ri-emotion-sad-fill',
				'ri-emotion-sad-line',
				'ri-emotion-unhappy-fill',
				'ri-emotion-unhappy-line',
				'ri-empathize-fill',
				'ri-empathize-line',
				'ri-emphasis-cn',
				'ri-emphasis',
				'ri-english-input',
				'ri-equalizer-fill',
				'ri-equalizer-line',
				'ri-eraser-fill',
				'ri-eraser-line',
				'ri-error-warning-fill',
				'ri-error-warning-line',
				'ri-evernote-fill',
				'ri-evernote-line',
				'ri-exchange-box-fill',
				'ri-exchange-box-line',
				'ri-exchange-cny-fill',
				'ri-exchange-cny-line',
				'ri-exchange-dollar-fill',
				'ri-exchange-dollar-line',
				'ri-exchange-fill',
				'ri-exchange-funds-fill',
				'ri-exchange-funds-line',
				'ri-exchange-line',
				'ri-external-link-fill',
				'ri-external-link-line',
				'ri-eye-2-fill',
				'ri-eye-2-line',
				'ri-eye-close-fill',
				'ri-eye-close-line',
				'ri-eye-fill',
				'ri-eye-line',
				'ri-eye-off-fill',
				'ri-eye-off-line',
				'ri-facebook-box-fill',
				'ri-facebook-box-line',
				'ri-facebook-circle-fill',
				'ri-facebook-circle-line',
				'ri-facebook-fill',
				'ri-facebook-line',
				'ri-fahrenheit-fill',
				'ri-fahrenheit-line',
				'ri-feedback-fill',
				'ri-feedback-line',
				'ri-file-2-fill',
				'ri-file-2-line',
				'ri-file-3-fill',
				'ri-file-3-line',
				'ri-file-4-fill',
				'ri-file-4-line',
				'ri-file-add-fill',
				'ri-file-add-line',
				'ri-file-chart-2-fill',
				'ri-file-chart-2-line',
				'ri-file-chart-fill',
				'ri-file-chart-line',
				'ri-file-cloud-fill',
				'ri-file-cloud-line',
				'ri-file-code-fill',
				'ri-file-code-line',
				'ri-file-copy-2-fill',
				'ri-file-copy-2-line',
				'ri-file-copy-fill',
				'ri-file-copy-line',
				'ri-file-damage-fill',
				'ri-file-damage-line',
				'ri-file-download-fill',
				'ri-file-download-line',
				'ri-file-edit-fill',
				'ri-file-edit-line',
				'ri-file-excel-2-fill',
				'ri-file-excel-2-line',
				'ri-file-excel-fill',
				'ri-file-excel-line',
				'ri-file-fill',
				'ri-file-forbid-fill',
				'ri-file-forbid-line',
				'ri-file-gif-fill',
				'ri-file-gif-line',
				'ri-file-history-fill',
				'ri-file-history-line',
				'ri-file-hwp-fill',
				'ri-file-hwp-line',
				'ri-file-info-fill',
				'ri-file-info-line',
				'ri-file-line',
				'ri-file-list-2-fill',
				'ri-file-list-2-line',
				'ri-file-list-3-fill',
				'ri-file-list-3-line',
				'ri-file-list-fill',
				'ri-file-list-line',
				'ri-file-lock-fill',
				'ri-file-lock-line',
				'ri-file-mark-fill',
				'ri-file-mark-line',
				'ri-file-music-fill',
				'ri-file-music-line',
				'ri-file-paper-2-fill',
				'ri-file-paper-2-line',
				'ri-file-paper-fill',
				'ri-file-paper-line',
				'ri-file-pdf-fill',
				'ri-file-pdf-line',
				'ri-file-ppt-2-fill',
				'ri-file-ppt-2-line',
				'ri-file-ppt-fill',
				'ri-file-ppt-line',
				'ri-file-reduce-fill',
				'ri-file-reduce-line',
				'ri-file-search-fill',
				'ri-file-search-line',
				'ri-file-settings-fill',
				'ri-file-settings-line',
				'ri-file-shield-2-fill',
				'ri-file-shield-2-line',
				'ri-file-shield-fill',
				'ri-file-shield-line',
				'ri-file-shred-fill',
				'ri-file-shred-line',
				'ri-file-text-fill',
				'ri-file-text-line',
				'ri-file-transfer-fill',
				'ri-file-transfer-line',
				'ri-file-unknow-fill',
				'ri-file-unknow-line',
				'ri-file-upload-fill',
				'ri-file-upload-line',
				'ri-file-user-fill',
				'ri-file-user-line',
				'ri-file-warning-fill',
				'ri-file-warning-line',
				'ri-file-word-2-fill',
				'ri-file-word-2-line',
				'ri-file-word-fill',
				'ri-file-word-line',
				'ri-file-zip-fill',
				'ri-file-zip-line',
				'ri-film-fill',
				'ri-film-line',
				'ri-filter-2-fill',
				'ri-filter-2-line',
				'ri-filter-3-fill',
				'ri-filter-3-line',
				'ri-filter-fill',
				'ri-filter-line',
				'ri-filter-off-fill',
				'ri-filter-off-line',
				'ri-find-replace-fill',
				'ri-find-replace-line',
				'ri-finder-fill',
				'ri-finder-line',
				'ri-fingerprint-2-fill',
				'ri-fingerprint-2-line',
				'ri-fingerprint-fill',
				'ri-fingerprint-line',
				'ri-fire-fill',
				'ri-fire-line',
				'ri-firefox-fill',
				'ri-firefox-line',
				'ri-first-aid-kit-fill',
				'ri-first-aid-kit-line',
				'ri-flag-2-fill',
				'ri-flag-2-line',
				'ri-flag-fill',
				'ri-flag-line',
				'ri-flashlight-fill',
				'ri-flashlight-line',
				'ri-flask-fill',
				'ri-flask-line',
				'ri-flight-land-fill',
				'ri-flight-land-line',
				'ri-flight-takeoff-fill',
				'ri-flight-takeoff-line',
				'ri-flood-fill',
				'ri-flood-line',
				'ri-flow-chart',
				'ri-flutter-fill',
				'ri-flutter-line',
				'ri-focus-2-fill',
				'ri-focus-2-line',
				'ri-focus-3-fill',
				'ri-focus-3-line',
				'ri-focus-fill',
				'ri-focus-line',
				'ri-foggy-fill',
				'ri-foggy-line',
				'ri-folder-2-fill',
				'ri-folder-2-line',
				'ri-folder-3-fill',
				'ri-folder-3-line',
				'ri-folder-4-fill',
				'ri-folder-4-line',
				'ri-folder-5-fill',
				'ri-folder-5-line',
				'ri-folder-add-fill',
				'ri-folder-add-line',
				'ri-folder-chart-2-fill',
				'ri-folder-chart-2-line',
				'ri-folder-chart-fill',
				'ri-folder-chart-line',
				'ri-folder-download-fill',
				'ri-folder-download-line',
				'ri-folder-fill',
				'ri-folder-forbid-fill',
				'ri-folder-forbid-line',
				'ri-folder-history-fill',
				'ri-folder-history-line',
				'ri-folder-info-fill',
				'ri-folder-info-line',
				'ri-folder-keyhole-fill',
				'ri-folder-keyhole-line',
				'ri-folder-line',
				'ri-folder-lock-fill',
				'ri-folder-lock-line',
				'ri-folder-music-fill',
				'ri-folder-music-line',
				'ri-folder-open-fill',
				'ri-folder-open-line',
				'ri-folder-received-fill',
				'ri-folder-received-line',
				'ri-folder-reduce-fill',
				'ri-folder-reduce-line',
				'ri-folder-settings-fill',
				'ri-folder-settings-line',
				'ri-folder-shared-fill',
				'ri-folder-shared-line',
				'ri-folder-shield-2-fill',
				'ri-folder-shield-2-line',
				'ri-folder-shield-fill',
				'ri-folder-shield-line',
				'ri-folder-transfer-fill',
				'ri-folder-transfer-line',
				'ri-folder-unknow-fill',
				'ri-folder-unknow-line',
				'ri-folder-upload-fill',
				'ri-folder-upload-line',
				'ri-folder-user-fill',
				'ri-folder-user-line',
				'ri-folder-warning-fill',
				'ri-folder-warning-line',
				'ri-folder-zip-fill',
				'ri-folder-zip-line',
				'ri-folders-fill',
				'ri-folders-line',
				'ri-font-color',
				'ri-font-size-2',
				'ri-font-size',
				'ri-football-fill',
				'ri-football-line',
				'ri-footprint-fill',
				'ri-footprint-line',
				'ri-forbid-2-fill',
				'ri-forbid-2-line',
				'ri-forbid-fill',
				'ri-forbid-line',
				'ri-format-clear',
				'ri-fridge-fill',
				'ri-fridge-line',
				'ri-fullscreen-exit-fill',
				'ri-fullscreen-exit-line',
				'ri-fullscreen-fill',
				'ri-fullscreen-line',
				'ri-function-fill',
				'ri-function-line',
				'ri-functions',
				'ri-funds-box-fill',
				'ri-funds-box-line',
				'ri-funds-fill',
				'ri-funds-line',
				'ri-gallery-fill',
				'ri-gallery-line',
				'ri-gallery-upload-fill',
				'ri-gallery-upload-line',
				'ri-game-fill',
				'ri-game-line',
				'ri-gamepad-fill',
				'ri-gamepad-line',
				'ri-gas-station-fill',
				'ri-gas-station-line',
				'ri-gatsby-fill',
				'ri-gatsby-line',
				'ri-genderless-fill',
				'ri-genderless-line',
				'ri-ghost-2-fill',
				'ri-ghost-2-line',
				'ri-ghost-fill',
				'ri-ghost-line',
				'ri-ghost-smile-fill',
				'ri-ghost-smile-line',
				'ri-gift-2-fill',
				'ri-gift-2-line',
				'ri-gift-fill',
				'ri-gift-line',
				'ri-git-branch-fill',
				'ri-git-branch-line',
				'ri-git-commit-fill',
				'ri-git-commit-line',
				'ri-git-merge-fill',
				'ri-git-merge-line',
				'ri-git-pull-request-fill',
				'ri-git-pull-request-line',
				'ri-git-repository-commits-fill',
				'ri-git-repository-commits-line',
				'ri-git-repository-fill',
				'ri-git-repository-line',
				'ri-git-repository-private-fill',
				'ri-git-repository-private-line',
				'ri-github-fill',
				'ri-github-line',
				'ri-gitlab-fill',
				'ri-gitlab-line',
				'ri-global-fill',
				'ri-global-line',
				'ri-globe-fill',
				'ri-globe-line',
				'ri-goblet-fill',
				'ri-goblet-line',
				'ri-google-fill',
				'ri-google-line',
				'ri-google-play-fill',
				'ri-google-play-line',
				'ri-government-fill',
				'ri-government-line',
				'ri-gps-fill',
				'ri-gps-line',
				'ri-gradienter-fill',
				'ri-gradienter-line',
				'ri-grid-fill',
				'ri-grid-line',
				'ri-group-2-fill',
				'ri-group-2-line',
				'ri-group-fill',
				'ri-group-line',
				'ri-guide-fill',
				'ri-guide-line',
				'ri-h-1',
				'ri-h-2',
				'ri-h-3',
				'ri-h-4',
				'ri-h-5',
				'ri-h-6',
				'ri-hail-fill',
				'ri-hail-line',
				'ri-hammer-fill',
				'ri-hammer-line',
				'ri-hand-coin-fill',
				'ri-hand-coin-line',
				'ri-hand-heart-fill',
				'ri-hand-heart-line',
				'ri-hand-sanitizer-fill',
				'ri-hand-sanitizer-line',
				'ri-handbag-fill',
				'ri-handbag-line',
				'ri-hard-drive-2-fill',
				'ri-hard-drive-2-line',
				'ri-hard-drive-fill',
				'ri-hard-drive-line',
				'ri-hashtag',
				'ri-haze-2-fill',
				'ri-haze-2-line',
				'ri-haze-fill',
				'ri-haze-line',
				'ri-hd-fill',
				'ri-hd-line',
				'ri-heading',
				'ri-headphone-fill',
				'ri-headphone-line',
				'ri-health-book-fill',
				'ri-health-book-line',
				'ri-heart-2-fill',
				'ri-heart-2-line',
				'ri-heart-3-fill',
				'ri-heart-3-line',
				'ri-heart-add-fill',
				'ri-heart-add-line',
				'ri-heart-fill',
				'ri-heart-line',
				'ri-heart-pulse-fill',
				'ri-heart-pulse-line',
				'ri-hearts-fill',
				'ri-hearts-line',
				'ri-heavy-showers-fill',
				'ri-heavy-showers-line',
				'ri-history-fill',
				'ri-history-line',
				'ri-home-2-fill',
				'ri-home-2-line',
				'ri-home-3-fill',
				'ri-home-3-line',
				'ri-home-4-fill',
				'ri-home-4-line',
				'ri-home-5-fill',
				'ri-home-5-line',
				'ri-home-6-fill',
				'ri-home-6-line',
				'ri-home-7-fill',
				'ri-home-7-line',
				'ri-home-8-fill',
				'ri-home-8-line',
				'ri-home-fill',
				'ri-home-gear-fill',
				'ri-home-gear-line',
				'ri-home-heart-fill',
				'ri-home-heart-line',
				'ri-home-line',
				'ri-home-smile-2-fill',
				'ri-home-smile-2-line',
				'ri-home-smile-fill',
				'ri-home-smile-line',
				'ri-home-wifi-fill',
				'ri-home-wifi-line',
				'ri-honor-of-kings-fill',
				'ri-honor-of-kings-line',
				'ri-honour-fill',
				'ri-honour-line',
				'ri-hospital-fill',
				'ri-hospital-line',
				'ri-hotel-bed-fill',
				'ri-hotel-bed-line',
				'ri-hotel-fill',
				'ri-hotel-line',
				'ri-hotspot-fill',
				'ri-hotspot-line',
				'ri-hq-fill',
				'ri-hq-line',
				'ri-html5-fill',
				'ri-html5-line',
				'ri-ie-fill',
				'ri-ie-line',
				'ri-image-2-fill',
				'ri-image-2-line',
				'ri-image-add-fill',
				'ri-image-add-line',
				'ri-image-edit-fill',
				'ri-image-edit-line',
				'ri-image-fill',
				'ri-image-line',
				'ri-inbox-archive-fill',
				'ri-inbox-archive-line',
				'ri-inbox-fill',
				'ri-inbox-line',
				'ri-inbox-unarchive-fill',
				'ri-inbox-unarchive-line',
				'ri-increase-decrease-fill',
				'ri-increase-decrease-line',
				'ri-indent-decrease',
				'ri-indent-increase',
				'ri-indeterminate-circle-fill',
				'ri-indeterminate-circle-line',
				'ri-information-fill',
				'ri-information-line',
				'ri-infrared-thermometer-fill',
				'ri-infrared-thermometer-line',
				'ri-ink-bottle-fill',
				'ri-ink-bottle-line',
				'ri-input-cursor-move',
				'ri-input-method-fill',
				'ri-input-method-line',
				'ri-insert-column-left',
				'ri-insert-column-right',
				'ri-insert-row-bottom',
				'ri-insert-row-top',
				'ri-instagram-fill',
				'ri-instagram-line',
				'ri-install-fill',
				'ri-install-line',
				'ri-invision-fill',
				'ri-invision-line',
				'ri-italic',
				'ri-kakao-talk-fill',
				'ri-kakao-talk-line',
				'ri-key-2-fill',
				'ri-key-2-line',
				'ri-key-fill',
				'ri-key-line',
				'ri-keyboard-box-fill',
				'ri-keyboard-box-line',
				'ri-keyboard-fill',
				'ri-keyboard-line',
				'ri-keynote-fill',
				'ri-keynote-line',
				'ri-knife-blood-fill',
				'ri-knife-blood-line',
				'ri-knife-fill',
				'ri-knife-line',
				'ri-landscape-fill',
				'ri-landscape-line',
				'ri-layout-2-fill',
				'ri-layout-2-line',
				'ri-layout-3-fill',
				'ri-layout-3-line',
				'ri-layout-4-fill',
				'ri-layout-4-line',
				'ri-layout-5-fill',
				'ri-layout-5-line',
				'ri-layout-6-fill',
				'ri-layout-6-line',
				'ri-layout-bottom-2-fill',
				'ri-layout-bottom-2-line',
				'ri-layout-bottom-fill',
				'ri-layout-bottom-line',
				'ri-layout-column-fill',
				'ri-layout-column-line',
				'ri-layout-fill',
				'ri-layout-grid-fill',
				'ri-layout-grid-line',
				'ri-layout-left-2-fill',
				'ri-layout-left-2-line',
				'ri-layout-left-fill',
				'ri-layout-left-line',
				'ri-layout-line',
				'ri-layout-masonry-fill',
				'ri-layout-masonry-line',
				'ri-layout-right-2-fill',
				'ri-layout-right-2-line',
				'ri-layout-right-fill',
				'ri-layout-right-line',
				'ri-layout-row-fill',
				'ri-layout-row-line',
				'ri-layout-top-2-fill',
				'ri-layout-top-2-line',
				'ri-layout-top-fill',
				'ri-layout-top-line',
				'ri-leaf-fill',
				'ri-leaf-line',
				'ri-lifebuoy-fill',
				'ri-lifebuoy-line',
				'ri-lightbulb-fill',
				'ri-lightbulb-flash-fill',
				'ri-lightbulb-flash-line',
				'ri-lightbulb-line',
				'ri-line-chart-fill',
				'ri-line-chart-line',
				'ri-line-fill',
				'ri-line-height',
				'ri-line-line',
				'ri-link-m',
				'ri-link-unlink-m',
				'ri-link-unlink',
				'ri-link',
				'ri-linkedin-box-fill',
				'ri-linkedin-box-line',
				'ri-linkedin-fill',
				'ri-linkedin-line',
				'ri-links-fill',
				'ri-links-line',
				'ri-list-check-2',
				'ri-list-check',
				'ri-list-ordered',
				'ri-list-settings-fill',
				'ri-list-settings-line',
				'ri-list-unordered',
				'ri-live-fill',
				'ri-live-line',
				'ri-loader-2-fill',
				'ri-loader-2-line',
				'ri-loader-3-fill',
				'ri-loader-3-line',
				'ri-loader-4-fill',
				'ri-loader-4-line',
				'ri-loader-5-fill',
				'ri-loader-5-line',
				'ri-loader-fill',
				'ri-loader-line',
				'ri-lock-2-fill',
				'ri-lock-2-line',
				'ri-lock-fill',
				'ri-lock-line',
				'ri-lock-password-fill',
				'ri-lock-password-line',
				'ri-lock-unlock-fill',
				'ri-lock-unlock-line',
				'ri-login-box-fill',
				'ri-login-box-line',
				'ri-login-circle-fill',
				'ri-login-circle-line',
				'ri-logout-box-fill',
				'ri-logout-box-line',
				'ri-logout-box-r-fill',
				'ri-logout-box-r-line',
				'ri-logout-circle-fill',
				'ri-logout-circle-line',
				'ri-logout-circle-r-fill',
				'ri-logout-circle-r-line',
				'ri-luggage-cart-fill',
				'ri-luggage-cart-line',
				'ri-luggage-deposit-fill',
				'ri-luggage-deposit-line',
				'ri-lungs-fill',
				'ri-lungs-line',
				'ri-mac-fill',
				'ri-mac-line',
				'ri-macbook-fill',
				'ri-macbook-line',
				'ri-magic-fill',
				'ri-magic-line',
				'ri-mail-add-fill',
				'ri-mail-add-line',
				'ri-mail-check-fill',
				'ri-mail-check-line',
				'ri-mail-close-fill',
				'ri-mail-close-line',
				'ri-mail-download-fill',
				'ri-mail-download-line',
				'ri-mail-fill',
				'ri-mail-forbid-fill',
				'ri-mail-forbid-line',
				'ri-mail-line',
				'ri-mail-lock-fill',
				'ri-mail-lock-line',
				'ri-mail-open-fill',
				'ri-mail-open-line',
				'ri-mail-send-fill',
				'ri-mail-send-line',
				'ri-mail-settings-fill',
				'ri-mail-settings-line',
				'ri-mail-star-fill',
				'ri-mail-star-line',
				'ri-mail-unread-fill',
				'ri-mail-unread-line',
				'ri-mail-volume-fill',
				'ri-mail-volume-line',
				'ri-map-2-fill',
				'ri-map-2-line',
				'ri-map-fill',
				'ri-map-line',
				'ri-map-pin-2-fill',
				'ri-map-pin-2-line',
				'ri-map-pin-3-fill',
				'ri-map-pin-3-line',
				'ri-map-pin-4-fill',
				'ri-map-pin-4-line',
				'ri-map-pin-5-fill',
				'ri-map-pin-5-line',
				'ri-map-pin-add-fill',
				'ri-map-pin-add-line',
				'ri-map-pin-fill',
				'ri-map-pin-line',
				'ri-map-pin-range-fill',
				'ri-map-pin-range-line',
				'ri-map-pin-time-fill',
				'ri-map-pin-time-line',
				'ri-map-pin-user-fill',
				'ri-map-pin-user-line',
				'ri-mark-pen-fill',
				'ri-mark-pen-line',
				'ri-markdown-fill',
				'ri-markdown-line',
				'ri-markup-fill',
				'ri-markup-line',
				'ri-mastercard-fill',
				'ri-mastercard-line',
				'ri-mastodon-fill',
				'ri-mastodon-line',
				'ri-medal-2-fill',
				'ri-medal-2-line',
				'ri-medal-fill',
				'ri-medal-line',
				'ri-medicine-bottle-fill',
				'ri-medicine-bottle-line',
				'ri-medium-fill',
				'ri-medium-line',
				'ri-men-fill',
				'ri-men-line',
				'ri-mental-health-fill',
				'ri-mental-health-line',
				'ri-menu-2-fill',
				'ri-menu-2-line',
				'ri-menu-3-fill',
				'ri-menu-3-line',
				'ri-menu-4-fill',
				'ri-menu-4-line',
				'ri-menu-5-fill',
				'ri-menu-5-line',
				'ri-menu-add-fill',
				'ri-menu-add-line',
				'ri-menu-fill',
				'ri-menu-fold-fill',
				'ri-menu-fold-line',
				'ri-menu-line',
				'ri-menu-unfold-fill',
				'ri-menu-unfold-line',
				'ri-merge-cells-horizontal',
				'ri-merge-cells-vertical',
				'ri-message-2-fill',
				'ri-message-2-line',
				'ri-message-3-fill',
				'ri-message-3-line',
				'ri-message-fill',
				'ri-message-line',
				'ri-messenger-fill',
				'ri-messenger-line',
				'ri-meteor-fill',
				'ri-meteor-line',
				'ri-mic-2-fill',
				'ri-mic-2-line',
				'ri-mic-fill',
				'ri-mic-line',
				'ri-mic-off-fill',
				'ri-mic-off-line',
				'ri-mickey-fill',
				'ri-mickey-line',
				'ri-microscope-fill',
				'ri-microscope-line',
				'ri-microsoft-fill',
				'ri-microsoft-line',
				'ri-mind-map',
				'ri-mini-program-fill',
				'ri-mini-program-line',
				'ri-mist-fill',
				'ri-mist-line',
				'ri-money-cny-box-fill',
				'ri-money-cny-box-line',
				'ri-money-cny-circle-fill',
				'ri-money-cny-circle-line',
				'ri-money-dollar-box-fill',
				'ri-money-dollar-box-line',
				'ri-money-dollar-circle-fill',
				'ri-money-dollar-circle-line',
				'ri-money-euro-box-fill',
				'ri-money-euro-box-line',
				'ri-money-euro-circle-fill',
				'ri-money-euro-circle-line',
				'ri-money-pound-box-fill',
				'ri-money-pound-box-line',
				'ri-money-pound-circle-fill',
				'ri-money-pound-circle-line',
				'ri-moon-clear-fill',
				'ri-moon-clear-line',
				'ri-moon-cloudy-fill',
				'ri-moon-cloudy-line',
				'ri-moon-fill',
				'ri-moon-foggy-fill',
				'ri-moon-foggy-line',
				'ri-moon-line',
				'ri-more-2-fill',
				'ri-more-2-line',
				'ri-more-fill',
				'ri-more-line',
				'ri-motorbike-fill',
				'ri-motorbike-line',
				'ri-mouse-fill',
				'ri-mouse-line',
				'ri-movie-2-fill',
				'ri-movie-2-line',
				'ri-movie-fill',
				'ri-movie-line',
				'ri-music-2-fill',
				'ri-music-2-line',
				'ri-music-fill',
				'ri-music-line',
				'ri-mv-fill',
				'ri-mv-line',
				'ri-navigation-fill',
				'ri-navigation-line',
				'ri-netease-cloud-music-fill',
				'ri-netease-cloud-music-line',
				'ri-netflix-fill',
				'ri-netflix-line',
				'ri-newspaper-fill',
				'ri-newspaper-line',
				'ri-node-tree',
				'ri-notification-2-fill',
				'ri-notification-2-line',
				'ri-notification-3-fill',
				'ri-notification-3-line',
				'ri-notification-4-fill',
				'ri-notification-4-line',
				'ri-notification-badge-fill',
				'ri-notification-badge-line',
				'ri-notification-fill',
				'ri-notification-line',
				'ri-notification-off-fill',
				'ri-notification-off-line',
				'ri-npmjs-fill',
				'ri-npmjs-line',
				'ri-number-0',
				'ri-number-1',
				'ri-number-2',
				'ri-number-3',
				'ri-number-4',
				'ri-number-5',
				'ri-number-6',
				'ri-number-7',
				'ri-number-8',
				'ri-number-9',
				'ri-numbers-fill',
				'ri-numbers-line',
				'ri-nurse-fill',
				'ri-nurse-line',
				'ri-oil-fill',
				'ri-oil-line',
				'ri-omega',
				'ri-open-arm-fill',
				'ri-open-arm-line',
				'ri-open-source-fill',
				'ri-open-source-line',
				'ri-opera-fill',
				'ri-opera-line',
				'ri-order-play-fill',
				'ri-order-play-line',
				'ri-organization-chart',
				'ri-outlet-2-fill',
				'ri-outlet-2-line',
				'ri-outlet-fill',
				'ri-outlet-line',
				'ri-page-separator',
				'ri-pages-fill',
				'ri-pages-line',
				'ri-paint-brush-fill',
				'ri-paint-brush-line',
				'ri-paint-fill',
				'ri-paint-line',
				'ri-palette-fill',
				'ri-palette-line',
				'ri-pantone-fill',
				'ri-pantone-line',
				'ri-paragraph',
				'ri-parent-fill',
				'ri-parent-line',
				'ri-parentheses-fill',
				'ri-parentheses-line',
				'ri-parking-box-fill',
				'ri-parking-box-line',
				'ri-parking-fill',
				'ri-parking-line',
				'ri-passport-fill',
				'ri-passport-line',
				'ri-patreon-fill',
				'ri-patreon-line',
				'ri-pause-circle-fill',
				'ri-pause-circle-line',
				'ri-pause-fill',
				'ri-pause-line',
				'ri-pause-mini-fill',
				'ri-pause-mini-line',
				'ri-paypal-fill',
				'ri-paypal-line',
				'ri-pen-nib-fill',
				'ri-pen-nib-line',
				'ri-pencil-fill',
				'ri-pencil-line',
				'ri-pencil-ruler-2-fill',
				'ri-pencil-ruler-2-line',
				'ri-pencil-ruler-fill',
				'ri-pencil-ruler-line',
				'ri-percent-fill',
				'ri-percent-line',
				'ri-phone-camera-fill',
				'ri-phone-camera-line',
				'ri-phone-fill',
				'ri-phone-find-fill',
				'ri-phone-find-line',
				'ri-phone-line',
				'ri-phone-lock-fill',
				'ri-phone-lock-line',
				'ri-picture-in-picture-2-fill',
				'ri-picture-in-picture-2-line',
				'ri-picture-in-picture-exit-fill',
				'ri-picture-in-picture-exit-line',
				'ri-picture-in-picture-fill',
				'ri-picture-in-picture-line',
				'ri-pie-chart-2-fill',
				'ri-pie-chart-2-line',
				'ri-pie-chart-box-fill',
				'ri-pie-chart-box-line',
				'ri-pie-chart-fill',
				'ri-pie-chart-line',
				'ri-pin-distance-fill',
				'ri-pin-distance-line',
				'ri-ping-pong-fill',
				'ri-ping-pong-line',
				'ri-pinterest-fill',
				'ri-pinterest-line',
				'ri-pinyin-input',
				'ri-pixelfed-fill',
				'ri-pixelfed-line',
				'ri-plane-fill',
				'ri-plane-line',
				'ri-plant-fill',
				'ri-plant-line',
				'ri-play-circle-fill',
				'ri-play-circle-line',
				'ri-play-fill',
				'ri-play-line',
				'ri-play-list-2-fill',
				'ri-play-list-2-line',
				'ri-play-list-add-fill',
				'ri-play-list-add-line',
				'ri-play-list-fill',
				'ri-play-list-line',
				'ri-play-mini-fill',
				'ri-play-mini-line',
				'ri-playstation-fill',
				'ri-playstation-line',
				'ri-plug-2-fill',
				'ri-plug-2-line',
				'ri-plug-fill',
				'ri-plug-line',
				'ri-polaroid-2-fill',
				'ri-polaroid-2-line',
				'ri-polaroid-fill',
				'ri-polaroid-line',
				'ri-police-car-fill',
				'ri-police-car-line',
				'ri-price-tag-2-fill',
				'ri-price-tag-2-line',
				'ri-price-tag-3-fill',
				'ri-price-tag-3-line',
				'ri-price-tag-fill',
				'ri-price-tag-line',
				'ri-printer-cloud-fill',
				'ri-printer-cloud-line',
				'ri-printer-fill',
				'ri-printer-line',
				'ri-product-hunt-fill',
				'ri-product-hunt-line',
				'ri-profile-fill',
				'ri-profile-line',
				'ri-projector-2-fill',
				'ri-projector-2-line',
				'ri-projector-fill',
				'ri-projector-line',
				'ri-psychotherapy-fill',
				'ri-psychotherapy-line',
				'ri-pulse-fill',
				'ri-pulse-line',
				'ri-pushpin-2-fill',
				'ri-pushpin-2-line',
				'ri-pushpin-fill',
				'ri-pushpin-line',
				'ri-qq-fill',
				'ri-qq-line',
				'ri-qr-code-fill',
				'ri-qr-code-line',
				'ri-qr-scan-2-fill',
				'ri-qr-scan-2-line',
				'ri-qr-scan-fill',
				'ri-qr-scan-line',
				'ri-question-answer-fill',
				'ri-question-answer-line',
				'ri-question-fill',
				'ri-question-line',
				'ri-question-mark',
				'ri-questionnaire-fill',
				'ri-questionnaire-line',
				'ri-quill-pen-fill',
				'ri-quill-pen-line',
				'ri-radar-fill',
				'ri-radar-line',
				'ri-radio-2-fill',
				'ri-radio-2-line',
				'ri-radio-button-fill',
				'ri-radio-button-line',
				'ri-radio-fill',
				'ri-radio-line',
				'ri-rainbow-fill',
				'ri-rainbow-line',
				'ri-rainy-fill',
				'ri-rainy-line',
				'ri-reactjs-fill',
				'ri-reactjs-line',
				'ri-record-circle-fill',
				'ri-record-circle-line',
				'ri-record-mail-fill',
				'ri-record-mail-line',
				'ri-recycle-fill',
				'ri-recycle-line',
				'ri-red-packet-fill',
				'ri-red-packet-line',
				'ri-reddit-fill',
				'ri-reddit-line',
				'ri-refresh-fill',
				'ri-refresh-line',
				'ri-refund-2-fill',
				'ri-refund-2-line',
				'ri-refund-fill',
				'ri-refund-line',
				'ri-registered-fill',
				'ri-registered-line',
				'ri-remixicon-fill',
				'ri-remixicon-line',
				'ri-remote-control-2-fill',
				'ri-remote-control-2-line',
				'ri-remote-control-fill',
				'ri-remote-control-line',
				'ri-repeat-2-fill',
				'ri-repeat-2-line',
				'ri-repeat-fill',
				'ri-repeat-line',
				'ri-repeat-one-fill',
				'ri-repeat-one-line',
				'ri-reply-all-fill',
				'ri-reply-all-line',
				'ri-reply-fill',
				'ri-reply-line',
				'ri-reserved-fill',
				'ri-reserved-line',
				'ri-rest-time-fill',
				'ri-rest-time-line',
				'ri-restart-fill',
				'ri-restart-line',
				'ri-restaurant-2-fill',
				'ri-restaurant-2-line',
				'ri-restaurant-fill',
				'ri-restaurant-line',
				'ri-rewind-fill',
				'ri-rewind-line',
				'ri-rewind-mini-fill',
				'ri-rewind-mini-line',
				'ri-rhythm-fill',
				'ri-rhythm-line',
				'ri-riding-fill',
				'ri-riding-line',
				'ri-road-map-fill',
				'ri-road-map-line',
				'ri-roadster-fill',
				'ri-roadster-line',
				'ri-robot-fill',
				'ri-robot-line',
				'ri-rocket-2-fill',
				'ri-rocket-2-line',
				'ri-rocket-fill',
				'ri-rocket-line',
				'ri-rotate-lock-fill',
				'ri-rotate-lock-line',
				'ri-rounded-corner',
				'ri-route-fill',
				'ri-route-line',
				'ri-router-fill',
				'ri-router-line',
				'ri-rss-fill',
				'ri-rss-line',
				'ri-ruler-2-fill',
				'ri-ruler-2-line',
				'ri-ruler-fill',
				'ri-ruler-line',
				'ri-run-fill',
				'ri-run-line',
				'ri-safari-fill',
				'ri-safari-line',
				'ri-safe-2-fill',
				'ri-safe-2-line',
				'ri-safe-fill',
				'ri-safe-line',
				'ri-sailboat-fill',
				'ri-sailboat-line',
				'ri-save-2-fill',
				'ri-save-2-line',
				'ri-save-3-fill',
				'ri-save-3-line',
				'ri-save-fill',
				'ri-save-line',
				'ri-scales-2-fill',
				'ri-scales-2-line',
				'ri-scales-3-fill',
				'ri-scales-3-line',
				'ri-scales-fill',
				'ri-scales-line',
				'ri-scan-2-fill',
				'ri-scan-2-line',
				'ri-scan-fill',
				'ri-scan-line',
				'ri-scissors-2-fill',
				'ri-scissors-2-line',
				'ri-scissors-cut-fill',
				'ri-scissors-cut-line',
				'ri-scissors-fill',
				'ri-scissors-line',
				'ri-screenshot-2-fill',
				'ri-screenshot-2-line',
				'ri-screenshot-fill',
				'ri-screenshot-line',
				'ri-sd-card-fill',
				'ri-sd-card-line',
				'ri-sd-card-mini-fill',
				'ri-sd-card-mini-line',
				'ri-search-2-fill',
				'ri-search-2-line',
				'ri-search-eye-fill',
				'ri-search-eye-line',
				'ri-search-fill',
				'ri-search-line',
				'ri-secure-payment-fill',
				'ri-secure-payment-line',
				'ri-seedling-fill',
				'ri-seedling-line',
				'ri-send-backward',
				'ri-send-plane-2-fill',
				'ri-send-plane-2-line',
				'ri-send-plane-fill',
				'ri-send-plane-line',
				'ri-send-to-back',
				'ri-sensor-fill',
				'ri-sensor-line',
				'ri-separator',
				'ri-server-fill',
				'ri-server-line',
				'ri-service-fill',
				'ri-service-line',
				'ri-settings-2-fill',
				'ri-settings-2-line',
				'ri-settings-3-fill',
				'ri-settings-3-line',
				'ri-settings-4-fill',
				'ri-settings-4-line',
				'ri-settings-5-fill',
				'ri-settings-5-line',
				'ri-settings-6-fill',
				'ri-settings-6-line',
				'ri-settings-fill',
				'ri-settings-line',
				'ri-shape-2-fill',
				'ri-shape-2-line',
				'ri-shape-fill',
				'ri-shape-line',
				'ri-share-box-fill',
				'ri-share-box-line',
				'ri-share-circle-fill',
				'ri-share-circle-line',
				'ri-share-fill',
				'ri-share-forward-2-fill',
				'ri-share-forward-2-line',
				'ri-share-forward-box-fill',
				'ri-share-forward-box-line',
				'ri-share-forward-fill',
				'ri-share-forward-line',
				'ri-share-line',
				'ri-shield-check-fill',
				'ri-shield-check-line',
				'ri-shield-cross-fill',
				'ri-shield-cross-line',
				'ri-shield-fill',
				'ri-shield-flash-fill',
				'ri-shield-flash-line',
				'ri-shield-keyhole-fill',
				'ri-shield-keyhole-line',
				'ri-shield-line',
				'ri-shield-star-fill',
				'ri-shield-star-line',
				'ri-shield-user-fill',
				'ri-shield-user-line',
				'ri-ship-2-fill',
				'ri-ship-2-line',
				'ri-ship-fill',
				'ri-ship-line',
				'ri-shirt-fill',
				'ri-shirt-line',
				'ri-shopping-bag-2-fill',
				'ri-shopping-bag-2-line',
				'ri-shopping-bag-3-fill',
				'ri-shopping-bag-3-line',
				'ri-shopping-bag-fill',
				'ri-shopping-bag-line',
				'ri-shopping-basket-2-fill',
				'ri-shopping-basket-2-line',
				'ri-shopping-basket-fill',
				'ri-shopping-basket-line',
				'ri-shopping-cart-2-fill',
				'ri-shopping-cart-2-line',
				'ri-shopping-cart-fill',
				'ri-shopping-cart-line',
				'ri-showers-fill',
				'ri-showers-line',
				'ri-shuffle-fill',
				'ri-shuffle-line',
				'ri-shut-down-fill',
				'ri-shut-down-line',
				'ri-side-bar-fill',
				'ri-side-bar-line',
				'ri-signal-tower-fill',
				'ri-signal-tower-line',
				'ri-signal-wifi-1-fill',
				'ri-signal-wifi-1-line',
				'ri-signal-wifi-2-fill',
				'ri-signal-wifi-2-line',
				'ri-signal-wifi-3-fill',
				'ri-signal-wifi-3-line',
				'ri-signal-wifi-error-fill',
				'ri-signal-wifi-error-line',
				'ri-signal-wifi-fill',
				'ri-signal-wifi-line',
				'ri-signal-wifi-off-fill',
				'ri-signal-wifi-off-line',
				'ri-sim-card-2-fill',
				'ri-sim-card-2-line',
				'ri-sim-card-fill',
				'ri-sim-card-line',
				'ri-single-quotes-l',
				'ri-single-quotes-r',
				'ri-sip-fill',
				'ri-sip-line',
				'ri-skip-back-fill',
				'ri-skip-back-line',
				'ri-skip-back-mini-fill',
				'ri-skip-back-mini-line',
				'ri-skip-forward-fill',
				'ri-skip-forward-line',
				'ri-skip-forward-mini-fill',
				'ri-skip-forward-mini-line',
				'ri-skull-2-fill',
				'ri-skull-2-line',
				'ri-skull-fill',
				'ri-skull-line',
				'ri-skype-fill',
				'ri-skype-line',
				'ri-slack-fill',
				'ri-slack-line',
				'ri-slice-fill',
				'ri-slice-line',
				'ri-slideshow-2-fill',
				'ri-slideshow-2-line',
				'ri-slideshow-3-fill',
				'ri-slideshow-3-line',
				'ri-slideshow-4-fill',
				'ri-slideshow-4-line',
				'ri-slideshow-fill',
				'ri-slideshow-line',
				'ri-smartphone-fill',
				'ri-smartphone-line',
				'ri-snapchat-fill',
				'ri-snapchat-line',
				'ri-snowy-fill',
				'ri-snowy-line',
				'ri-sort-asc',
				'ri-sort-desc',
				'ri-sound-module-fill',
				'ri-sound-module-line',
				'ri-soundcloud-fill',
				'ri-soundcloud-line',
				'ri-space-ship-fill',
				'ri-space-ship-line',
				'ri-space',
				'ri-spam-2-fill',
				'ri-spam-2-line',
				'ri-spam-3-fill',
				'ri-spam-3-line',
				'ri-spam-fill',
				'ri-spam-line',
				'ri-speaker-2-fill',
				'ri-speaker-2-line',
				'ri-speaker-3-fill',
				'ri-speaker-3-line',
				'ri-speaker-fill',
				'ri-speaker-line',
				'ri-spectrum-fill',
				'ri-spectrum-line',
				'ri-speed-fill',
				'ri-speed-line',
				'ri-speed-mini-fill',
				'ri-speed-mini-line',
				'ri-split-cells-horizontal',
				'ri-split-cells-vertical',
				'ri-spotify-fill',
				'ri-spotify-line',
				'ri-spy-fill',
				'ri-spy-line',
				'ri-stack-fill',
				'ri-stack-line',
				'ri-stack-overflow-fill',
				'ri-stack-overflow-line',
				'ri-stackshare-fill',
				'ri-stackshare-line',
				'ri-star-fill',
				'ri-star-half-fill',
				'ri-star-half-line',
				'ri-star-half-s-fill',
				'ri-star-half-s-line',
				'ri-star-line',
				'ri-star-s-fill',
				'ri-star-s-line',
				'ri-star-smile-fill',
				'ri-star-smile-line',
				'ri-steam-fill',
				'ri-steam-line',
				'ri-steering-2-fill',
				'ri-steering-2-line',
				'ri-steering-fill',
				'ri-steering-line',
				'ri-stethoscope-fill',
				'ri-stethoscope-line',
				'ri-sticky-note-2-fill',
				'ri-sticky-note-2-line',
				'ri-sticky-note-fill',
				'ri-sticky-note-line',
				'ri-stock-fill',
				'ri-stock-line',
				'ri-stop-circle-fill',
				'ri-stop-circle-line',
				'ri-stop-fill',
				'ri-stop-line',
				'ri-stop-mini-fill',
				'ri-stop-mini-line',
				'ri-store-2-fill',
				'ri-store-2-line',
				'ri-store-3-fill',
				'ri-store-3-line',
				'ri-store-fill',
				'ri-store-line',
				'ri-strikethrough-2',
				'ri-strikethrough',
				'ri-subscript-2',
				'ri-subscript',
				'ri-subtract-fill',
				'ri-subtract-line',
				'ri-subway-fill',
				'ri-subway-line',
				'ri-subway-wifi-fill',
				'ri-subway-wifi-line',
				'ri-suitcase-2-fill',
				'ri-suitcase-2-line',
				'ri-suitcase-3-fill',
				'ri-suitcase-3-line',
				'ri-suitcase-fill',
				'ri-suitcase-line',
				'ri-sun-cloudy-fill',
				'ri-sun-cloudy-line',
				'ri-sun-fill',
				'ri-sun-foggy-fill',
				'ri-sun-foggy-line',
				'ri-sun-line',
				'ri-superscript-2',
				'ri-superscript',
				'ri-surgical-mask-fill',
				'ri-surgical-mask-line',
				'ri-surround-sound-fill',
				'ri-surround-sound-line',
				'ri-survey-fill',
				'ri-survey-line',
				'ri-swap-box-fill',
				'ri-swap-box-line',
				'ri-swap-fill',
				'ri-swap-line',
				'ri-switch-fill',
				'ri-switch-line',
				'ri-sword-fill',
				'ri-sword-line',
				'ri-syringe-fill',
				'ri-syringe-line',
				'ri-t-box-fill',
				'ri-t-box-line',
				'ri-t-shirt-2-fill',
				'ri-t-shirt-2-line',
				'ri-t-shirt-air-fill',
				'ri-t-shirt-air-line',
				'ri-t-shirt-fill',
				'ri-t-shirt-line',
				'ri-table-2',
				'ri-table-alt-fill',
				'ri-table-alt-line',
				'ri-table-fill',
				'ri-table-line',
				'ri-tablet-fill',
				'ri-tablet-line',
				'ri-takeaway-fill',
				'ri-takeaway-line',
				'ri-taobao-fill',
				'ri-taobao-line',
				'ri-tape-fill',
				'ri-tape-line',
				'ri-task-fill',
				'ri-task-line',
				'ri-taxi-fill',
				'ri-taxi-line',
				'ri-taxi-wifi-fill',
				'ri-taxi-wifi-line',
				'ri-team-fill',
				'ri-team-line',
				'ri-telegram-fill',
				'ri-telegram-line',
				'ri-temp-cold-fill',
				'ri-temp-cold-line',
				'ri-temp-hot-fill',
				'ri-temp-hot-line',
				'ri-terminal-box-fill',
				'ri-terminal-box-line',
				'ri-terminal-fill',
				'ri-terminal-line',
				'ri-terminal-window-fill',
				'ri-terminal-window-line',
				'ri-test-tube-fill',
				'ri-test-tube-line',
				'ri-text-direction-l',
				'ri-text-direction-r',
				'ri-text-spacing',
				'ri-text-wrap',
				'ri-text',
				'ri-thermometer-fill',
				'ri-thermometer-line',
				'ri-thumb-down-fill',
				'ri-thumb-down-line',
				'ri-thumb-up-fill',
				'ri-thumb-up-line',
				'ri-thunderstorms-fill',
				'ri-thunderstorms-line',
				'ri-ticket-2-fill',
				'ri-ticket-2-line',
				'ri-ticket-fill',
				'ri-ticket-line',
				'ri-time-fill',
				'ri-time-line',
				'ri-timer-2-fill',
				'ri-timer-2-line',
				'ri-timer-fill',
				'ri-timer-flash-fill',
				'ri-timer-flash-line',
				'ri-timer-line',
				'ri-todo-fill',
				'ri-todo-line',
				'ri-toggle-fill',
				'ri-toggle-line',
				'ri-tools-fill',
				'ri-tools-line',
				'ri-tornado-fill',
				'ri-tornado-line',
				'ri-trademark-fill',
				'ri-trademark-line',
				'ri-traffic-light-fill',
				'ri-traffic-light-line',
				'ri-train-fill',
				'ri-train-line',
				'ri-train-wifi-fill',
				'ri-train-wifi-line',
				'ri-translate-2',
				'ri-translate',
				'ri-travesti-fill',
				'ri-travesti-line',
				'ri-treasure-map-fill',
				'ri-treasure-map-line',
				'ri-trello-fill',
				'ri-trello-line',
				'ri-trophy-fill',
				'ri-trophy-line',
				'ri-truck-fill',
				'ri-truck-line',
				'ri-tumblr-fill',
				'ri-tumblr-line',
				'ri-tv-2-fill',
				'ri-tv-2-line',
				'ri-tv-fill',
				'ri-tv-line',
				'ri-twitch-fill',
				'ri-twitch-line',
				'ri-twitter-fill',
				'ri-twitter-line',
				'ri-typhoon-fill',
				'ri-typhoon-line',
				'ri-u-disk-fill',
				'ri-u-disk-line',
				'ri-ubuntu-fill',
				'ri-ubuntu-line',
				'ri-umbrella-fill',
				'ri-umbrella-line',
				'ri-underline',
				'ri-uninstall-fill',
				'ri-uninstall-line',
				'ri-unsplash-fill',
				'ri-unsplash-line',
				'ri-upload-2-fill',
				'ri-upload-2-line',
				'ri-upload-cloud-2-fill',
				'ri-upload-cloud-2-line',
				'ri-upload-cloud-fill',
				'ri-upload-cloud-line',
				'ri-upload-fill',
				'ri-upload-line',
				'ri-usb-fill',
				'ri-usb-line',
				'ri-user-2-fill',
				'ri-user-2-line',
				'ri-user-3-fill',
				'ri-user-3-line',
				'ri-user-4-fill',
				'ri-user-4-line',
				'ri-user-5-fill',
				'ri-user-5-line',
				'ri-user-6-fill',
				'ri-user-6-line',
				'ri-user-add-fill',
				'ri-user-add-line',
				'ri-user-fill',
				'ri-user-follow-fill',
				'ri-user-follow-line',
				'ri-user-heart-fill',
				'ri-user-heart-line',
				'ri-user-line',
				'ri-user-location-fill',
				'ri-user-location-line',
				'ri-user-received-2-fill',
				'ri-user-received-2-line',
				'ri-user-received-fill',
				'ri-user-received-line',
				'ri-user-search-fill',
				'ri-user-search-line',
				'ri-user-settings-fill',
				'ri-user-settings-line',
				'ri-user-shared-2-fill',
				'ri-user-shared-2-line',
				'ri-user-shared-fill',
				'ri-user-shared-line',
				'ri-user-smile-fill',
				'ri-user-smile-line',
				'ri-user-star-fill',
				'ri-user-star-line',
				'ri-user-unfollow-fill',
				'ri-user-unfollow-line',
				'ri-user-voice-fill',
				'ri-user-voice-line',
				'ri-video-add-fill',
				'ri-video-add-line',
				'ri-video-chat-fill',
				'ri-video-chat-line',
				'ri-video-download-fill',
				'ri-video-download-line',
				'ri-video-fill',
				'ri-video-line',
				'ri-video-upload-fill',
				'ri-video-upload-line',
				'ri-vidicon-2-fill',
				'ri-vidicon-2-line',
				'ri-vidicon-fill',
				'ri-vidicon-line',
				'ri-vimeo-fill',
				'ri-vimeo-line',
				'ri-vip-crown-2-fill',
				'ri-vip-crown-2-line',
				'ri-vip-crown-fill',
				'ri-vip-crown-line',
				'ri-vip-diamond-fill',
				'ri-vip-diamond-line',
				'ri-vip-fill',
				'ri-vip-line',
				'ri-virus-fill',
				'ri-virus-line',
				'ri-visa-fill',
				'ri-visa-line',
				'ri-voice-recognition-fill',
				'ri-voice-recognition-line',
				'ri-voiceprint-fill',
				'ri-voiceprint-line',
				'ri-volume-down-fill',
				'ri-volume-down-line',
				'ri-volume-mute-fill',
				'ri-volume-mute-line',
				'ri-volume-off-vibrate-fill',
				'ri-volume-off-vibrate-line',
				'ri-volume-up-fill',
				'ri-volume-up-line',
				'ri-volume-vibrate-fill',
				'ri-volume-vibrate-line',
				'ri-vuejs-fill',
				'ri-vuejs-line',
				'ri-walk-fill',
				'ri-walk-line',
				'ri-wallet-2-fill',
				'ri-wallet-2-line',
				'ri-wallet-3-fill',
				'ri-wallet-3-line',
				'ri-wallet-fill',
				'ri-wallet-line',
				'ri-water-flash-fill',
				'ri-water-flash-line',
				'ri-webcam-fill',
				'ri-webcam-line',
				'ri-wechat-2-fill',
				'ri-wechat-2-line',
				'ri-wechat-fill',
				'ri-wechat-line',
				'ri-wechat-pay-fill',
				'ri-wechat-pay-line',
				'ri-weibo-fill',
				'ri-weibo-line',
				'ri-whatsapp-fill',
				'ri-whatsapp-line',
				'ri-wheelchair-fill',
				'ri-wheelchair-line',
				'ri-wifi-fill',
				'ri-wifi-line',
				'ri-wifi-off-fill',
				'ri-wifi-off-line',
				'ri-window-2-fill',
				'ri-window-2-line',
				'ri-window-fill',
				'ri-window-line',
				'ri-windows-fill',
				'ri-windows-line',
				'ri-windy-fill',
				'ri-windy-line',
				'ri-wireless-charging-fill',
				'ri-wireless-charging-line',
				'ri-women-fill',
				'ri-women-line',
				'ri-wubi-input',
				'ri-xbox-fill',
				'ri-xbox-line',
				'ri-xing-fill',
				'ri-xing-line',
				'ri-youtube-fill',
				'ri-youtube-line',
				'ri-zcool-fill',
				'ri-zcool-line',
				'ri-zhihu-fill',
				'ri-zhihu-line',
				'ri-zoom-in-fill',
				'ri-zoom-in-line',
				'ri-zoom-out-fill',
				'ri-zoom-out-line',
				'ri-zzz-fill',
				'ri-zzz-line',
			);

			return $remix_icons;
		}
	}
}
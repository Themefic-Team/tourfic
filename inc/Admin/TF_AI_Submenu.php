<?php
namespace Tourfic\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Adds "Create With AI" buttons to the Hotels and Tours sidebar submenus.
 * When Tourfic Pro is active, this class does nothing (Pro handles its own AI submenu).
 * When Pro is NOT active, this shows the buttons with an upsell prompt on click.
 */
class TF_AI_Submenu {

	use \Tourfic\Traits\Singleton;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_ai_submenu' ) );
		add_action( 'admin_menu', array( $this, 'reorder_ai_submenu' ), 1000 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_ai_submenu_assets' ) );
		add_action( 'admin_footer', array( $this, 'ai_submenu_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_ai_list_button_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_ai_editor_button_assets' ) );
	}

	/**
	 * Register visible "Create With AI" submenu items under Hotels and Tours.
	 * Skips when Pro is active (Pro registers its own with full modal functionality).
	 */
	public function register_ai_submenu() {
		if ( defined( 'TF_PRO' ) ) {
			return;
		}
		add_submenu_page(
			'edit.php?post_type=tf_hotel',
			__( 'Create With AI', 'tourfic' ),
			'<span class="tf-ai-submenu-item" data-post-type="tf_hotel">' . esc_html__( 'Create With AI', 'tourfic' ) . '</span>',
			'manage_options',
			'tf_ai_generate_hotel',
			'__return_null'
		);

		add_submenu_page(
			'edit.php?post_type=tf_tours',
			__( 'Create With AI', 'tourfic' ),
			'<span class="tf-ai-submenu-item" data-post-type="tf_tours">' . esc_html__( 'Create With AI', 'tourfic' ) . '</span>',
			'manage_options',
			'tf_ai_generate_tour',
			'__return_null'
		);
	}

	/**
	 * Move AI items to appear above the docs link at the bottom of submenus.
	 */
	public function reorder_ai_submenu() {
		if ( defined( 'TF_PRO' ) ) {
			return;
		}

		global $submenu;

		$menus = array( 'edit.php?post_type=tf_hotel', 'edit.php?post_type=tf_tours' );

		foreach ( $menus as $parent ) {
			if ( empty( $submenu[ $parent ] ) ) {
				continue;
			}

			$ai_key  = null;
			$doc_key = null;

			foreach ( $submenu[ $parent ] as $key => $item ) {
				if ( isset( $item[0] ) && strpos( $item[0], 'tf-ai-submenu-item' ) !== false ) {
					$ai_key = $key;
				}
				if ( isset( $item[0] ) && strpos( $item[0], 'tf-go-docs' ) !== false ) {
					$doc_key = $key;
				}
			}

			if ( $ai_key !== null && $doc_key !== null ) {
				$ai_item  = $submenu[ $parent ][ $ai_key ];
				$doc_item = $submenu[ $parent ][ $doc_key ];

				unset( $submenu[ $parent ][ $ai_key ] );
				unset( $submenu[ $parent ][ $doc_key ] );

				$submenu[ $parent ][] = $ai_item;
				$submenu[ $parent ][] = $doc_item;
			}
		}
	}

	/**
	 * Enqueue CSS and JS for the AI submenu (free users only).
	 */
	public function enqueue_ai_submenu_assets() {
		if ( defined( 'TF_PRO' ) ) {
			return;
		}

		wp_enqueue_style(
			'tf-ai-submenu',
			TF_ASSETS_ADMIN_URL . 'css/tf-ai-submenu.css',
			array(),
			filemtime( TF_ASSETS_PATH . 'admin/css/tf-ai-submenu.css' )
		);

		wp_enqueue_style(
			'tf-ai-upsell-modal',
			TF_ASSETS_ADMIN_URL . 'css/tf-ai-upsell-modal.css',
			array(),
			filemtime( TF_ASSETS_PATH . 'admin/css/tf-ai-upsell-modal.css' )
		);

		wp_enqueue_script(
			'tf-ai-submenu',
			TF_ASSETS_ADMIN_URL . 'js/tf-ai-submenu.js',
			array(),
			filemtime( TF_ASSETS_PATH . 'admin/js/tf-ai-submenu.js' ),
			true
		);
	}

	/**
	 * Output the upsell modal HTML (free users only).
	 */
	public function ai_submenu_assets() {
		if ( defined( 'TF_PRO' ) ) {
			return;
		}

		$upgrade_url = 'https://tourfic.com/pricing/';
		?>
		<div class="tf-ai-upsell-overlay" id="tf-ai-upsell-overlay">
			<div class="tf-ai-upsell-box">
				<button class="tf-ai-upsell-close tf-ai-upsell-dismiss-action" type="button" aria-label="<?php esc_attr_e( 'Close', 'tourfic' ); ?>">
					<svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 1l12 12M13 1L1 13"/></svg>
				</button>
				<div class="tf-ai-upsell-icon-wrap">
					<svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M26.667 2.665v5.334M29.334 5.332h-5.334M14.69 3.751a1.34 1.34 0 0 1 2.62 0l1.402 7.412a3.34 3.34 0 0 0 2.584 2.584l7.412 1.402a1.34 1.34 0 0 1 0 2.62l-7.412 1.402a3.34 3.34 0 0 0-2.584 2.584l-1.402 7.412a1.34 1.34 0 0 1-2.62 0l-1.402-7.412a3.34 3.34 0 0 0-2.584-2.584l-7.412-1.402a1.34 1.34 0 0 1 0-2.62l7.412-1.402a3.34 3.34 0 0 0 2.584-2.584l1.402-7.412Z" stroke="url(#tf-sparkle-grad)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<circle cx="5.334" cy="26.665" r="2.667" stroke="url(#tf-sparkle-grad)" stroke-width="2"/>
						<defs>
							<linearGradient id="tf-sparkle-grad" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
								<stop stop-color="#002472"/><stop offset="0.5" stop-color="#0077F1"/><stop offset="1" stop-color="#1AC2FF"/>
							</linearGradient>
						</defs>
					</svg>
				</div>
				<div class="tf-ai-upsell-badge"><?php esc_html_e( 'Pro Feature', 'tourfic' ); ?></div>
				<h2><?php esc_html_e( 'Create With AI', 'tourfic' ); ?></h2>
				<p><?php esc_html_e( 'Generate complete tours and hotels in minutes with AI. Upgrade to Tourfic Pro to unlock this powerful feature.', 'tourfic' ); ?></p>
				<ul class="tf-ai-upsell-features">
					<li>
						<svg viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="9" fill="#3b82f6" opacity=".1"/><path d="M5.5 9.5l2 2 5-5" stroke="#3b82f6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
						<?php esc_html_e( 'AI-generated content, descriptions & itineraries', 'tourfic' ); ?>
					</li>
					<li>
						<svg viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="9" fill="#3b82f6" opacity=".1"/><path d="M5.5 9.5l2 2 5-5" stroke="#3b82f6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
						<?php esc_html_e( 'Auto-generated images & gallery', 'tourfic' ); ?>
					</li>
					<li>
						<svg viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="9" fill="#3b82f6" opacity=".1"/><path d="M5.5 9.5l2 2 5-5" stroke="#3b82f6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
						<?php esc_html_e( 'Smart pricing, FAQ & SEO meta fields', 'tourfic' ); ?>
					</li>
					<li>
						<svg viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="9" fill="#3b82f6" opacity=".1"/><path d="M5.5 9.5l2 2 5-5" stroke="#3b82f6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
						<?php esc_html_e( 'One-click publish to your site', 'tourfic' ); ?>
					</li>
				</ul>
				<a class="tf-ai-upsell-btn" href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" rel="noopener">
					<svg viewBox="0 0 16 16" fill="none"><path d="M13.334 1.333v2.666M14.667 2.666h-2.666M7.345 1.875a.67.67 0 0 1 1.31 0l.701 3.706a1.67 1.67 0 0 0 1.292 1.292l3.706.701a.67.67 0 0 1 0 1.31l-3.706.701a1.67 1.67 0 0 0-1.292 1.292l-.701 3.706a.67.67 0 0 1-1.31 0l-.701-3.706a1.67 1.67 0 0 0-1.292-1.292l-3.706-.701a.67.67 0 0 1 0-1.31l3.706-.701a1.67 1.67 0 0 0 1.292-1.292l.701-3.706ZM4 13.333a1.333 1.333 0 1 1-2.667 0 1.333 1.333 0 0 1 2.667 0Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
					<?php esc_html_e( 'Upgrade to Pro', 'tourfic' ); ?>
				</a>
				<button class="tf-ai-upsell-dismiss tf-ai-upsell-dismiss-action" type="button"><?php esc_html_e( 'Maybe later', 'tourfic' ); ?></button>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue CSS/JS for the AI button on post list and classic editor screens.
	 * Only loads on tf_hotel / tf_tours edit screens.
	 */
	public function enqueue_ai_list_button_assets() {
		if ( defined( 'TF_PRO' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		$valid_post_types = array( 'tf_hotel', 'tf_tours' );

		if ( ! in_array( $screen->post_type, $valid_post_types, true ) ) {
			return;
		}

		// Determine which screen type we're on.
		if ( 'edit' === $screen->base ) {
			$screen_type = 'edit';
		} elseif ( 'post' === $screen->base ) {
			// Only for classic editor (not Gutenberg).
			if ( ! $screen->is_block_editor ) {
				$screen_type = 'post';
			} else {
				return;
			}
		} else {
			return;
		}

		wp_enqueue_style(
			'tf-ai-buttons',
			TF_ASSETS_ADMIN_URL . 'css/tf-ai-buttons.css',
			array(),
			filemtime( TF_ASSETS_PATH . 'admin/css/tf-ai-buttons.css' )
		);

		wp_enqueue_script(
			'tf-ai-buttons',
			TF_ASSETS_ADMIN_URL . 'js/tf-ai-buttons.js',
			array( 'jquery' ),
			filemtime( TF_ASSETS_PATH . 'admin/js/tf-ai-buttons.js' ),
			true
		);

		wp_localize_script( 'tf-ai-buttons', 'tfAiButtons', array(
			'post_type'   => $screen->post_type,
			'button_text' => __( 'Create With AI', 'tourfic' ),
			'screen'      => $screen_type,
		) );
	}

	/**
	 * Enqueue CSS/JS for the AI button inside the Gutenberg block editor header.
	 * Only loads on tf_hotel / tf_tours block editor screens.
	 */
	public function enqueue_ai_editor_button_assets() {
		if ( defined( 'TF_PRO' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		$valid_post_types = array( 'tf_hotel', 'tf_tours' );

		if ( ! in_array( $screen->post_type, $valid_post_types, true ) ) {
			return;
		}

		wp_enqueue_style(
			'tf-ai-buttons',
			TF_ASSETS_ADMIN_URL . 'css/tf-ai-buttons.css',
			array(),
			filemtime( TF_ASSETS_PATH . 'admin/css/tf-ai-buttons.css' )
		);

		wp_enqueue_script(
			'tf-ai-editor-button',
			TF_ASSETS_ADMIN_URL . 'js/tf-ai-editor-button.js',
			array(),
			filemtime( TF_ASSETS_PATH . 'admin/js/tf-ai-editor-button.js' ),
			true
		);
	}
}

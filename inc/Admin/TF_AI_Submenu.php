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
		add_action( 'admin_footer', array( $this, 'ai_submenu_assets' ) );
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
	 * Output CSS for the AI submenu buttons and JS upsell handler (free users only).
	 */
	public function ai_submenu_assets() {
		if ( defined( 'TF_PRO' ) ) {
			return;
		}

		$upgrade_url = 'https://tourfic.com/pricing/';
		?>
		<style>
			/* ── AI Submenu – "Create With AI" button ── */
			#adminmenu .wp-submenu li a:has(.tf-ai-submenu-item) {
				position: relative;
				display: inline-flex !important;
				align-items: center;
				gap: 8px;
				margin: 8px 14px 8px;
				background: #2a2d35;
				border: none !important;
				border-radius: 4px;
				color: #fff !important;
				letter-spacing: 0.01em;
				cursor: pointer;
				z-index: 0;
				transition: transform 0.2s ease, box-shadow 0.2s ease;
				overflow: visible;
				font-family: Inter, sans-serif;
				font-size: 12px;
				font-style: normal;
				font-weight: 400;
				line-height: 20px;
				width: 110px;
			}
			#adminmenu .wp-submenu li a:has(.tf-ai-submenu-item)::before {
				content: '';
				position: absolute;
				inset: -1px;
				border-radius: 4px;
				background: linear-gradient(135deg, #3b82f6 0%, #0464C8 25%, #3b82f6 40%, #f59e0b 75%, #eab308 100%);
				z-index: -1;
				pointer-events: none;
			}
			#adminmenu .wp-submenu li a:has(.tf-ai-submenu-item)::after {
				content: '';
				position: absolute;
				inset: 0;
				border-radius: 4px;
				background: #2a2d35;
				z-index: -1;
				pointer-events: none;
			}
			#adminmenu .wp-submenu li a:has(.tf-ai-submenu-item):hover,
			#adminmenu .wp-submenu li a:has(.tf-ai-submenu-item):focus {
				box-shadow: 0 0 20px rgba(96, 165, 250, 0.35), 0 0 20px rgba(234, 179, 8, 0.2);
				color: #fff !important;
				background: #2a2d35;
			}
			@keyframes tfBorderSpin {
				0%   { background-position: 0% 50%; }
				50%  { background-position: 100% 50%; }
				100% { background-position: 0% 50%; }
			}
			#adminmenu .wp-submenu li a:has(.tf-ai-submenu-item):hover::before {
				background: linear-gradient(135deg, #3b82f6 0%, #0464C8 25%, #3b82f6 40%, #f59e0b 75%, #eab308 100%);
				background-size: 300% 300%;
				animation: tfBorderSpin 2.5s ease infinite;
			}
			.tf-ai-submenu-item {
				display: inline-flex;
				align-items: center;
				gap: 6px;
				position: relative;
				z-index: 1;
			}
			.tf-ai-submenu-item::before {
				content: '';
				display: inline-block;
				width: 15px;
				height: 15px;
				background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none'%3E%3Cg clip-path='url(%23clip0_2501_5494)'%3E%3Cmask id='mask0_2501_5494' style='mask-type:luminance' maskUnits='userSpaceOnUse' x='0' y='0' width='16' height='16'%3E%3Cpath d='M16 0H0V16H16V0Z' fill='white'/%3E%3C/mask%3E%3Cg mask='url(%23mask0_2501_5494)'%3E%3Cpath d='M13.3336 1.33265V3.99932M14.667 2.66598H12.0003M7.34496 1.87532C7.37353 1.72239 7.45468 1.58426 7.57436 1.48486C7.69404 1.38546 7.84472 1.33105 8.00032 1.33105C8.15584 1.33105 8.30656 1.38546 8.42624 1.48486C8.54592 1.58426 8.62704 1.72239 8.6556 1.87532L9.35632 5.58066C9.40608 5.84409 9.53408 6.0864 9.72368 6.27597C9.9132 6.46554 10.1555 6.59355 10.419 6.64332L14.1243 7.34398C14.2772 7.37255 14.4154 7.4537 14.5147 7.57338C14.6142 7.69306 14.6686 7.84374 14.6686 7.99932C14.6686 8.15486 14.6142 8.30558 14.5147 8.42526C14.4154 8.54494 14.2772 8.62606 14.1243 8.65462L10.419 9.35534C10.1555 9.4051 9.9132 9.5331 9.72368 9.7227C9.53408 9.91222 9.40608 10.1545 9.35632 10.418L8.6556 14.1233C8.62704 14.2762 8.54592 14.4144 8.42624 14.5137C8.30656 14.6132 8.15584 14.6676 8.00032 14.6676C7.84472 14.6676 7.69404 14.6132 7.57436 14.5137C7.45468 14.4144 7.37353 14.2762 7.34496 14.1233L6.6443 10.418C6.59453 10.1545 6.46651 9.91222 6.27694 9.7227C6.08738 9.5331 5.84506 9.4051 5.58162 9.35534L1.8763 8.65462C1.72336 8.62606 1.58524 8.54494 1.48584 8.42526C1.38644 8.30558 1.33203 8.15486 1.33203 7.99932C1.33203 7.84374 1.38644 7.69306 1.48584 7.57338C1.58524 7.4537 1.72336 7.37255 1.8763 7.34398L5.58162 6.64332C5.84506 6.59355 6.08738 6.46554 6.27694 6.27597C6.46651 6.0864 6.59453 5.84409 6.6443 5.58066L7.34496 1.87532ZM4.0003 13.3326C4.0003 14.069 3.40334 14.666 2.66696 14.666C1.93058 14.666 1.33362 14.069 1.33362 13.3326C1.33362 12.5963 1.93058 11.9993 2.66696 11.9993C3.40334 11.9993 4.0003 12.5963 4.0003 13.3326Z' stroke='white' stroke-width='1.2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/g%3E%3C/g%3E%3Cdefs%3E%3CclipPath id='clip0_2501_5494'%3E%3Crect width='16' height='16' fill='white'/%3E%3C/clipPath%3E%3C/defs%3E%3C/svg%3E") no-repeat center / contain;
				flex-shrink: 0;
				transition: filter 0.2s ease 0.15s, width 0.2s ease 0.15s, height 0.2s ease 0.15s;
			}
			.tf-ai-submenu-item:hover::before {
				width: 16px;
				height: 16px;
			}

			/* ── Upsell Modal ── */
			.tf-ai-upsell-overlay {
				display: none;
				position: fixed;
				top: 0; left: 0;
				width: 100%; height: 100%;
				background: rgba(0, 0, 0, 0.65);
				z-index: 999999;
				backdrop-filter: blur(6px);
				-webkit-backdrop-filter: blur(6px);
			}
			.tf-ai-upsell-overlay.active {
				display: flex;
				align-items: center;
				justify-content: center;
			}
			.tf-ai-upsell-box {
				position: relative;
				background: #fff;
				border-radius: 20px;
				padding: 48px 40px 40px;
				max-width: 460px;
				width: 92%;
				overflow: hidden;
				z-index: 0;
				text-align: center;
				box-shadow:
					0 24px 80px rgba(0, 0, 0, 0.25),
					0 8px 24px rgba(59, 130, 246, 0.08);
				animation: tfUpsellIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
			}
			@keyframes tfUpsellIn {
				from { opacity: 0; transform: scale(0.92) translateY(24px); }
				to   { opacity: 1; transform: scale(1) translateY(0); }
			}

			/* Close button */
			.tf-ai-upsell-close {
				position: absolute;
				top: 16px;
				right: 16px;
				width: 32px;
				height: 32px;
				border: none;
				background: #f1f5f9;
				border-radius: 8px;
				cursor: pointer;
				display: flex;
				align-items: center;
				justify-content: center;
				transition: background 0.2s, transform 0.2s;
				padding: 0;
				z-index: 2;
			}
			.tf-ai-upsell-close:hover {
				background: #e2e8f0;
				transform: scale(1.05);
			}
			.tf-ai-upsell-close svg {
				width: 14px;
				height: 14px;
				color: #64748b;
			}

			/* Icon circle */
			.tf-ai-upsell-icon-wrap {
				width: 72px;
				height: 72px;
				margin: 0 auto 20px;
				border-radius: 50%;
				background: linear-gradient(135deg, rgba(0,36,114,0.08) 0%, rgba(0,119,241,0.1) 50%, rgba(26,194,255,0.1) 100%);
				display: flex;
				align-items: center;
				justify-content: center;
				position: relative;
				z-index: 1;
				isolation: isolate;
			}
			.tf-ai-upsell-icon-wrap::after {
				content: '';
				position: absolute;
				inset: -1px;
				border-radius: 50%;
				/* background: linear-gradient(135deg, rgba(0,36,114,0.25), rgba(26,194,255,0.3)); */
				z-index: -1;
			}
			.tf-ai-upsell-icon-wrap svg {
				width: 32px;
				height: 32px;
				position: relative;
				z-index: 1;
			}

			/* PRO badge */
			.tf-ai-upsell-badge {
				display: inline-flex;
				align-items: center;
				gap: 4px;
				padding: 4px 12px;
				border-radius: 100px;
				background: linear-gradient(135deg, #3b82f6, #0464C8);
				color: #fff;
				font-size: 11px;
				font-weight: 700;
				letter-spacing: 0.08em;
				text-transform: uppercase;
				margin-bottom: 16px;
			}

			.tf-ai-upsell-box h2 {
				margin: 0 0 10px;
				font-size: 21px;
				font-weight: 700;
				color: #0f172a;
				line-height: 1.3;
			}
			.tf-ai-upsell-box > p {
				margin: 0 0 20px;
				color: #64748b;
				font-size: 14px;
				line-height: 1.65;
			}

			/* Feature list */
			.tf-ai-upsell-features {
				list-style: none;
				margin: 0 0 28px;
				padding: 0;
				text-align: left;
				display: flex;
				flex-direction: column;
				gap: 10px;
			}
			.tf-ai-upsell-features li {
				display: flex;
				align-items: center;
				gap: 10px;
				font-size: 13.5px;
				color: #334155;
				line-height: 1.4;
			}
			.tf-ai-upsell-features li svg {
				flex-shrink: 0;
				width: 18px;
				height: 18px;
			}

			/* CTA button – matches Pro AI generate button design */
			.tf-ai-upsell-box .tf-ai-upsell-btn {
				position: relative;
				display: inline-flex;
				align-items: center;
				justify-content: center;
				gap: 8px;
				width: 100%;
				height: 48px;
				padding: 12px 24px;
				color: #fff;
				text-decoration: none;
				border-radius: 12px;
				border: 1.2px solid transparent;
				background:
					linear-gradient(271deg, #0051A2 1.03%, #0077F1 50%, #0051A2 98.97%) padding-box,
					linear-gradient(135deg, #002472, #1AC2FF, #1AC2FF, #1AC2FF, #002472) border-box;
				box-shadow:
					-4px 2px 4px 0 rgba(132, 185, 255, 0.25) inset,
					4px 2px 4px 0 rgba(132, 185, 255, 0.25) inset,
					0 4px 4px 0 rgba(108, 191, 255, 0.25) inset,
					4px 12px 8px 0 rgba(0, 40, 71, 0.04),
					4px 12px 8px 0 rgba(0, 105, 186, 0.08);
				font-size: 15px;
				font-weight: 600;
				font-family: Inter, sans-serif;
				letter-spacing: 0.01em;
				cursor: pointer;
				overflow: visible;
				z-index: 1;
				box-sizing: border-box;
				transition: all 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
			}
			.tf-ai-upsell-box .tf-ai-upsell-btn::after {
				content: '';
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				border-radius: 10px;
				background: linear-gradient(271deg, #0051A2 1.03%, #0077F1 50%, #0051A2 98.97%);
				z-index: -1;
			}
			
			.tf-ai-upsell-box .tf-ai-upsell-btn:active {
				transform: translateY(0);
			}
			.tf-ai-upsell-box .tf-ai-upsell-btn svg {
				width: 18px;
				height: 18px;
				position: relative;
				z-index: 1;
			}

			/* Dismiss */
			.tf-ai-upsell-box .tf-ai-upsell-dismiss {
				display: block;
				width: 100%;
				margin-top: 12px;
				padding: 10px;
				color: #94a3b8;
				font-size: 13px;
				cursor: pointer;
				background: none;
				border: none;
				transition: color 0.2s;
			}
			.tf-ai-upsell-box .tf-ai-upsell-dismiss:hover {
				color: #475569;
			}
		</style>

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

		<script>
		(function(){
			var links = document.querySelectorAll('.tf-ai-submenu-item');
			if (!links.length) return;

			var overlay = document.getElementById('tf-ai-upsell-overlay');

			function openUpsell() {
				overlay.classList.add('active');
				document.body.style.overflow = 'hidden';
			}

			function closeUpsell() {
				overlay.classList.remove('active');
				document.body.style.overflow = '';
			}

			links.forEach(function(span) {
				var a = span.closest('a');
				if (!a) return;
				a.addEventListener('click', function(e) {
					e.preventDefault();
					openUpsell();
				});
			});

			overlay.addEventListener('click', function(e) {
				if (e.target === overlay) closeUpsell();
			});

			var dismissBtns = overlay.querySelectorAll('.tf-ai-upsell-dismiss-action');
			dismissBtns.forEach(function(btn) {
				btn.addEventListener('click', closeUpsell);
			});

			document.addEventListener('keydown', function(e) {
				if (e.key === 'Escape' && overlay.classList.contains('active')) closeUpsell();
			});
		})();
		</script>
		<?php
	}
}

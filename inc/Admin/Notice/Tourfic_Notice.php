<?php

namespace Tourfic\Admin\Notice;

defined( 'ABSPATH' ) || exit;

class Tourfic_Notice {

	private const DISMISS_ACTION = 'tourfic_dismiss_notice';
	private const SCREENS = array(
		'dashboard',
		'edit-page',
		'edit-post',
		'plugins',
		'edit-tf_tours',
		'edit-tf_hotel',
		'edit-tf_room',
		'edit-tf_apartment',
		'edit-tf_carrental',
	);

	public function __construct() {
		add_action( 'admin_notices', array( $this, 'render' ) );
		add_action( 'admin_post_' . self::DISMISS_ACTION, array( $this, 'dismiss' ) );
	}

	public function render() {
		if ( defined( 'TF_PRO' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->id, self::SCREENS, true ) || ! current_user_can( 'manage_options' ) || get_option( self::DISMISS_ACTION ) ) {
			return;
		}
		?>
		<style>
			#tourfic-notice {
				position: relative;
				display: flex;
				align-items: flex-start;
				gap: 16px;
				padding: 22px 44px 22px 22px;
				border: 1px solid #c9d4f7;
				border-inline-start: 4px solid #0e3dd8;
				border-radius: 8px;
				background: #f5f7ff;
				color: #494d59;
			}
			#tourfic-notice > .dashicons {
				width: 26px;
				height: 26px;
				font-size: 26px;
				color: #0e3dd8;
			}
			#tourfic-notice h2 {
				margin: 0;
				color: #1c2130;
				font-size: 18px;
				line-height: 1.4;
			}
			#tourfic-notice p {
				margin: 6px 0 14px;
			}
			#tourfic-notice .tourfic-notice-actions {
				display: flex;
				flex-wrap: wrap;
				gap: 8px 12px;
			}
			#tourfic-notice .button {
				border-color: #0e3dd8;
				border-radius: 6px;
				padding-inline: 16px;
			}
			#tourfic-notice .button-primary {
				background: #0e3dd8;
			}
			#tourfic-notice .button-primary:hover,
			#tourfic-notice .button-primary:focus {
				background: #0a2b99;
				border-color: #0a2b99;
			}
			#tourfic-notice .button-secondary {
				background: transparent;
				color: #0e3dd8;
			}
			#tourfic-notice .button-secondary:hover,
			#tourfic-notice .button-secondary:focus {
				background: #f3f5fd;
				border-color: #0a2b99;
				color: #0a2b99;
			}
			#tourfic-notice .notice-dismiss {
				inset-inline: auto 10px;
			}
		</style>
		<div id="tourfic-notice" class="notice">
			<span class="dashicons dashicons-megaphone" aria-hidden="true"></span>
			<div>
				<h2><?php esc_html_e( 'Themefic Black Friday / Cyber Monday Sale: Up to 80% OFF!', 'tourfic' ); ?></h2>
				<p><?php esc_html_e( 'Get 70% OFF Tourfic lifetime plans + double the site licenses, or save up to 80% with the Themefic Bundle. Pay once for lifetime updates and support!', 'tourfic' ); ?></p>
				<div class="tourfic-notice-actions">
					<a class="button button-primary" target="_blank" rel="noopener" href="<?php echo esc_url( 'https://themefic.com/bfcm26/#single-deal-wrapper' ); ?>">
						<?php esc_html_e( 'Get Lifetime Deals', 'tourfic' ); ?>
					</a>
					<a class="button button-secondary" target="_blank" rel="noopener" href="<?php echo esc_url( 'https://themefic.com/bfcm26/' ); ?>">
						<?php esc_html_e( 'Explore all Offers', 'tourfic' ); ?>
					</a>
				</div>
			</div>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="<?php echo esc_attr( self::DISMISS_ACTION ); ?>">
				<?php wp_nonce_field( self::DISMISS_ACTION ); ?>
				<button type="submit" class="notice-dismiss">
					<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this Tourfic notice.', 'tourfic' ); ?></span>
				</button>
			</form>
		</div>
		<?php
	}

	public function dismiss() {
		if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized request.', 'tourfic' ), '', array( 'response' => 403 ) );
		}

		check_admin_referer( self::DISMISS_ACTION );
		update_option( self::DISMISS_ACTION, 1 );

		wp_safe_redirect( wp_get_referer() ?: admin_url( 'index.php' ), 303 );
		exit;
	}
}

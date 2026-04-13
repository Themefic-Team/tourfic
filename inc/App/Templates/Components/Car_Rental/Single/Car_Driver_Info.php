<?php

namespace Tourfic\App\Templates\Components\Car_Rental\Single;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Car Driver Info Component
 */
class Car_Driver_Info {

	public static function render( $settings = [], $builder = '' ) {
		$post_id       = get_the_ID();
		$post_type     = get_post_type();
		$wrapper_open  = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';

		if ( $post_type !== 'tf_carrental' ) {
			return;
		}

		$meta = get_post_meta( $post_id, 'tf_carrental_opt', true );
		if ( empty( $meta ) || ! is_array( $meta ) ) {
			return;
		}

		$driver_sec_title      = ! empty( $meta['driver_sec_title'] ) ? $meta['driver_sec_title'] : '';
		$car_driver_incude     = ! empty( $meta['driver_included'] ) ? $meta['driver_included'] : '';
		$car_driverinfo_status = ! empty( $meta['car_driverinfo_section'] ) ? $meta['car_driverinfo_section'] : '';
		$driver_name           = ! empty( $meta['driver_name'] ) ? $meta['driver_name'] : '';
		$driver_email          = ! empty( $meta['driver_email'] ) ? $meta['driver_email'] : '';
		$driver_phone          = ! empty( $meta['driver_phone'] ) ? $meta['driver_phone'] : '';
		$driver_age            = ! empty( $meta['driver_age'] ) ? $meta['driver_age'] : '';
		$driver_address        = ! empty( $meta['driver_address'] ) ? $meta['driver_address'] : '';
		$driver_image          = ! empty( $meta['driver_image'] ) ? $meta['driver_image'] : '';

		if ( empty( $car_driver_incude ) || empty( $car_driverinfo_status ) ) {
			return;
		}

		echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';
		?>
		<div class="tf-driver-details tf-flex tf-flex-direction-column tf-flex-gap-16">
			<div class="tf-driver-details-header tf-flex tf-flex-space-bttn tf-flex-align-center">
				<?php if ( ! empty( $driver_sec_title ) ) : ?>
					<h3 class="tf-section-title"><?php echo esc_html( $driver_sec_title ); ?></h3>
				<?php endif; ?>
				<span>
					<i class="ri-shield-check-line"></i> <?php esc_html_e( 'Verified', 'tourfic' ); ?>
				</span>
			</div>
			<div class="tf-driver-photo tf-flex tf-flex-gap-16">
				<?php if ( ! empty( $driver_image ) ) : ?>
					<img src="<?php echo esc_url( $driver_image ); ?>" alt="<?php echo esc_attr( $driver_name ); ?>">
				<?php endif; ?>
				<div class="tf-driver-info">
					<?php if ( ! empty( $driver_name ) ) : ?>
						<h4><?php echo esc_html( $driver_name ); ?></h4>
					<?php endif; ?>
					<?php if ( ! empty( $driver_age ) ) : ?>
						<p><?php esc_html_e( 'Age', 'tourfic' ); ?> <?php echo esc_html( $driver_age ); ?> <?php esc_html_e( 'Years', 'tourfic' ); ?></p>
					<?php endif; ?>

					<div class="tf-driver-contact-info">
						<ul class="tf-flex tf-flex-gap-16">
							<?php if ( ! empty( $driver_email ) ) : ?>
								<li>
									<a href="mailto:<?php echo esc_attr( $driver_email ); ?>">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M18.3333 5.8335L10.8583 10.5835C10.601 10.7447 10.3036 10.8302 9.99996 10.8302C9.69636 10.8302 9.3989 10.7447 9.14163 10.5835L1.66663 5.8335M3.33329 3.3335H16.6666C17.5871 3.3335 18.3333 4.07969 18.3333 5.00016V15.0002C18.3333 15.9206 17.5871 16.6668 16.6666 16.6668H3.33329C2.41282 16.6668 1.66663 15.9206 1.66663 15.0002V5.00016C1.66663 4.07969 2.41282 3.3335 3.33329 3.3335Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
										<div class="tf-tooltip-info">
											<p><?php echo esc_html( $driver_email ); ?></p>
										</div>
									</a>
								</li>
							<?php endif; ?>
							<?php if ( ! empty( $driver_phone ) ) : ?>
								<li>
									<a href="tel:<?php echo esc_attr( $driver_phone ); ?>">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M18.3333 14.0999V16.5999C18.3343 16.832 18.2867 17.0617 18.1937 17.2744C18.1008 17.487 17.9644 17.6779 17.7934 17.8348C17.6224 17.9917 17.4205 18.1112 17.2006 18.1855C16.9808 18.2599 16.7478 18.2875 16.5167 18.2666C13.9523 17.988 11.4892 17.1117 9.32498 15.7083C7.31151 14.4288 5.60443 12.7217 4.32499 10.7083C2.91663 8.53426 2.04019 6.05908 1.76665 3.48325C1.74583 3.25281 1.77321 3.02055 1.84707 2.80127C1.92092 2.58199 2.03963 2.38049 2.19562 2.2096C2.35162 2.03871 2.54149 1.90218 2.75314 1.80869C2.9648 1.7152 3.1936 1.6668 3.42499 1.66658H5.92499C6.32941 1.6626 6.72148 1.80582 7.02812 2.06953C7.33476 2.33324 7.53505 2.69946 7.59165 3.09992C7.69717 3.89997 7.89286 4.68552 8.17499 5.44158C8.2871 5.73985 8.31137 6.06401 8.24491 6.37565C8.17844 6.68729 8.02404 6.97334 7.79998 7.19992L6.74165 8.25825C7.92795 10.3445 9.65536 12.072 11.7417 13.2583L12.8 12.1999C13.0266 11.9759 13.3126 11.8215 13.6243 11.755C13.9359 11.6885 14.26 11.7128 14.5583 11.8249C15.3144 12.107 16.0999 12.3027 16.9 12.4083C17.3048 12.4654 17.6745 12.6693 17.9388 12.9812C18.203 13.2931 18.3435 13.6912 18.3333 14.0999Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
										<div class="tf-tooltip-info">
											<p><?php echo esc_html( $driver_phone ); ?></p>
										</div>
									</a>
								</li>
							<?php endif; ?>
							<?php if ( ! empty( $driver_address ) ) : ?>
								<li>
									<a href="https://maps.google.com/maps?q=<?php echo esc_attr( $driver_address ); ?>" target="_blank" rel="noopener noreferrer">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M16.6667 8.33317C16.6667 12.494 12.0509 16.8273 10.5009 18.1657C10.3565 18.2742 10.1807 18.333 10 18.333C9.81938 18.333 9.6436 18.2742 9.49921 18.1657C7.94921 16.8273 3.33337 12.494 3.33337 8.33317C3.33337 6.56506 4.03575 4.86937 5.286 3.61913C6.53624 2.36888 8.23193 1.6665 10 1.6665C11.7682 1.6665 13.4638 2.36888 14.7141 3.61913C15.9643 4.86937 16.6667 6.56506 16.6667 8.33317Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M10 10.8332C11.3808 10.8332 12.5 9.71388 12.5 8.33317C12.5 6.95246 11.3808 5.83317 10 5.83317C8.61933 5.83317 7.50004 6.95246 7.50004 8.33317C7.50004 9.71388 8.61933 10.8332 10 10.8332Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
										<div class="tf-tooltip-info">
											<p><?php echo esc_html( $driver_address ); ?></p>
										</div>
									</a>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<?php
		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}
}

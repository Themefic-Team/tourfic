<?php

add_action( 'wp_ajax_tf_add_new_room', 'tourfic_add_room_data_action' );
function tourfic_add_room_data_action(){

	$key = sanitize_text_field( $_POST['key'] );

	ob_start();

	echo tourfic_add_single_room_wrap( array(
		'key' => $key,
	) );

	$output = ob_get_clean();

	echo $output;

	die();
}

// Single room data
function tourfic_add_single_room_wrap( $args ){
    $defaults = array (
        'key' => '',
        'room' => array(),
    );

    // Parse incoming $args into an array and merge it with $defaults
    $args = wp_parse_args( $args, $defaults );

    // Let's extract the array
    extract( $args['room'] );

    // Array key
    $key =  isset( $args['key'] ) ? $args['key'] : "";

    $room_title = ( $args['room']['name'] ) ? $args['room']['name'] : __( '# Room Title', 'tourfic' );
    $pax = isset( $args['room']['pax'] ) ? $args['room']['pax'] : '2';

	ob_start();
	?>
	<div class="tf_postbox tf-add-single-room-wrap">
		<div class="tf-add-single-room-head">
			<div class="tf_postbox-title tf-room-title"><?php echo esc_html( $room_title ); ?></div>

			<span class="room-action-btns">
				<a href="#" class="room-remove tf_remove_postdiv"><span class="dashicons dashicons-no-alt"></span></a>
			</span>

			<a href="#" class="room-expend tf_expend_postdiv"><span class="dashicons dashicons-arrow-down-alt2"></span></a>
		</div>

		<div class="tf_postbox-inside tf-add-single-room-body">
			<div class="tf-room-field-holder">

				<div class="tf-field-wrap label-left">
					<div class="tf-label">
						<label for="tf_room-name-<?php _e( $key ); ?>"><?php esc_html_e( 'Room Name', 'tourfic' ); ?></label>
					</div>
				     <input type="text" name="tf_room[<?php _e( $key ); ?>][name]" class="tf_postbox-title-get tf_room-name" id="tf_room-name-<?php _e( $key ); ?>" value="<?php echo esc_attr( $name ); ?>">
				</div>

				<div class="tf-field-wrap label-left">
					<div class="tf-label">
						<label for="tf_room-short_desc-<?php _e( $key ); ?>"><?php esc_html_e( 'Short Description', 'tourfic' ); ?></label>
					</div>
				    <textarea name="tf_room[<?php _e( $key ); ?>][short_desc]" class="tf_room-short_desc" id="tf_room-short_desc-<?php _e( $key ); ?>" rows="5"><?php _e( $short_desc ); ?></textarea>
				</div>

				<div class="tf-field-wrap label-left">
					<div class="tf-label">
						<label for="tf_room-desc-<?php _e( $key ); ?>"><?php esc_html_e( 'Room Features', 'tourfic' ); ?></label>
					</div>
					<div class="field-desc">
					    <textarea name="tf_room[<?php _e( $key ); ?>][desc]" class="tf_room-desc" rows="5" id="tf_room-desc-<?php _e( $key ); ?>"><?php _e( $desc ); ?></textarea>
				    	<p>You can find icon class <a href="https://fontawesome.com/v4.7.0/icons/" target="_blank">Here</a>. Example Shortcode: <code><?php esc_attr_e( '[tf_list icon="fa-wifi" text="Free Wifi"]' ); ?></code></p></div>
				</div>

				<div class="tf-row">
					<div class="tf-col-6">
						<div class="tf-field-wrap">
							<div class="tf-label">
								<label for="tf_room-price-<?php _e( $key ); ?>"><?php esc_html_e( 'Price', 'tourfic' ); ?></label>
							</div>
						    <input type="number" step="any" min="0" name="tf_room[<?php _e( $key ); ?>][price]" class="tf_room-price" id="tf_room-price-<?php _e( $key ); ?>" value="<?php echo esc_attr( $price ); ?>">
						</div>
					</div>
					<div class="tf-col-6">
						<div class="tf-field-wrap">
							<div class="tf-label">
								<label for="tf_room-sale_price-<?php _e( $key ); ?>"><?php esc_html_e( 'Sale Price',  'tourfic' ); ?></label>
							</div>
						    <input type="number" step="any" min="0" name="tf_room[<?php _e( $key ); ?>][sale_price]" class="tf_room-sale_price" id="tf_room-sale-price-<?php _e( $key ); ?>" value="<?php echo esc_attr( $sale_price ); ?>">
						</div>
					</div>
				</div>

				<div class="tf-field-wrap label-left">
					<div class="tf-label">
						<label for="tf_room-pax-<?php _e( $key ); ?>"><?php esc_html_e( 'Pax',  'tourfic' ); ?></label>
					</div>
					<input type="number" step="any" min="0" name="tf_room[<?php _e( $key ); ?>][pax]" class="tf_room-pax" id="tf_room-sale-price-<?php _e( $key ); ?>" value="<?php echo esc_attr( $pax ); ?>">
				</div>

			</div>
		</div>
	</div>

	<?php
	$output = ob_get_clean();

	return $output;

}

add_action( 'wp_ajax_tf_add_new_faq', 'tourfic_add_faq_data_action' );
function tourfic_add_faq_data_action(){

	$key = sanitize_text_field( $_POST['key'] );

	ob_start();

	echo tourfic_add_single_faq( array(
		'key' => $key,
	) );

	$output = ob_get_clean();

	echo $output;

	die();
}

// Single room data
function tourfic_add_single_faq( $args ){
    $defaults = array (
        'key' => '',
        'faq' => '',
    );

    // Parse incoming $args into an array and merge it with $defaults
    $args = wp_parse_args( $args, $defaults );

    // Let's extract the array
    extract( $args['faq'] );

    // Array key
    $key =  isset( $args['key'] ) ? $args['key'] : "";

    $room_title = ( $args['faq']['name'] ) ? $args['faq']['name'] : __( '# FAQ Title', 'tourfic' );

	ob_start();
	?>
	<div class="tf_postbox tf-add-single-faq-wrap">
		<div class="tf_postbox_head tf-add-single-faq-head">
			<div class="tf_postbox-title tf-faq-title"><?php echo esc_html( $room_title ); ?></div>

			<span class="faq-action-btns">
				<a href="#" class="faq-remove tf_remove_postdiv"><span class="dashicons dashicons-no-alt"></span></a>
			</span>

			<a href="#" class="faq-expend tf_expend_postdiv"><span class="dashicons dashicons-arrow-down-alt2"></span></a>
		</div>

		<div class="tf_postbox-inside tf-add-single-faq-body">
			<div class="tf-faq-field-holder">

				<div class="tf-field-wrap label-left">
					<div class="tf-label">
						<label for="tf_faq-name-<?php _e( $key ); ?>"><?php esc_html_e( 'FAQ Title', 'tourfic' ); ?></label>
					</div>
				     <input type="text" name="tf_faqs[<?php _e( $key ); ?>][name]" class="tf_postbox-title-get tf_faq-name" id="tf_faq-name-<?php _e( $key ); ?>" value="<?php echo esc_attr( $name ); ?>">
				</div>

				<div class="tf-field-wrap label-left">
					<div class="tf-label">
						<label for="tf_faq-desc-<?php _e( $key ); ?>"><?php esc_html_e( 'Description', 'tourfic' ); ?></label>
					</div>
				    <textarea name="tf_faqs[<?php _e( $key ); ?>][desc]" class="tf_faq-desc" rows="5" id="tf_faq-desc-<?php _e( $key ); ?>"><?php _e( $desc ); ?></textarea>
				</div>

			</div>
		</div>
	</div>

	<?php
	$output = ob_get_clean();

	return $output;

}


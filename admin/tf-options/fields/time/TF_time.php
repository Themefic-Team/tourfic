<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_time' ) ) {
	class TF_time extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '' ) {
			parent::__construct( $field, $value, $settings_id );
		}

		public function render() {

			$args = wp_parse_args( $this->field, array(
				'format' => 'Y-m-d',
			) );

			$format = ( ! empty( $args['format'] ) ) ? $args['format'] : 'Y-m-d';
			?>
			<input type="text" name="<?php echo esc_attr( $this->field_name() ); ?>" placeholder="<?php echo esc_attr($this->field['placeholder']) ?>" value="<?php echo esc_attr( $this->value ); ?>" class="flatpickr " data-format="<?php echo esc_attr( $format ); ?>" />
            <i class="fa-regular fa-clock"></i>
            <?php
		}


	}
}
<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

class TF_text {

	public $field;
	public $value;

	public function __construct( $field, $value = '' ) {
		$this->field  = $field;
		$this->value  = $value;
	}

	public function render() {
		$type = ( ! empty( $field['attributes']['type'] ) ) ? $field['attributes']['type'] : 'text';
		echo '<input type="'. esc_attr( $type ) .'" name="'. esc_attr( $field['id'] ) .'" value="'. $this->value .'" />';
	}

}
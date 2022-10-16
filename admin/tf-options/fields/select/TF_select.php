<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

class TF_select {

	public $field;
	public $value;

	public function __construct( $field, $value = '' ) {
		$this->field  = $field;
		$this->value  = $value;
	}

	public function render() {
		echo '<select name="'. esc_attr( $this->field['id'] ) .'">';
		foreach ( $this->field['options'] as $key => $value ) {
			echo '<option value="'. esc_attr( $key ) .'" '. selected( $this->value, $key, false ) .'>'. $value .'</option>';
		}
		echo '</select>';
	}

}
<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

class TF_textarea {

	public $field;
	public $value;

	public function __construct( $field, $value = '' ) {
		$this->field  = $field;
		$this->value  = $value;
	}

	public function render() {
		echo '<textarea name="'. esc_attr( $this->field['id'] ) .'">'. $this->value .'</textarea>';
	}

}
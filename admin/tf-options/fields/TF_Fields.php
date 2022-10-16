<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

class TF_Fields {

	public function __construct( $field = array(), $value = '', $unique = '', $where = '', $parent = '' ) {
		$this->field  = $field;
		$this->value  = $value;
		$this->unique = $unique;
		$this->where  = $where;
		$this->parent = $parent;
	}

	public function field_name( $nested_name = '' ) {

		$field_id   = ( ! empty( $this->field['id'] ) ) ? $this->field['id'] : '';
		$unique_id  = ( ! empty( $this->unique ) ) ? $this->unique . '[' . $field_id . ']' : $field_id;
		$field_name = ( ! empty( $this->field['name'] ) ) ? $this->field['name'] : $unique_id;
		$tag_prefix = ( ! empty( $this->field['tag_prefix'] ) ) ? $this->field['tag_prefix'] : '';

		if ( ! empty( $tag_prefix ) ) {
			$nested_name = str_replace( '[', '[' . $tag_prefix, $nested_name );
		}

		return $field_name . $nested_name;

	}
}
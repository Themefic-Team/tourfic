<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Fields' ) ) {
	class TF_Fields {

		public function __construct( $field = array(), $value = '', $settings_id = '', $parent_field = '') {
			$this->field       = $field;
			$this->value       = $value;
			$this->settings_id = $settings_id;
			$this->parent_field = $parent_field;
		}

		public function field_name() {

			$field_id   = ( ! empty( $this->field['id'] ) ) ? $this->field['id'] : '';
			if(!empty($field_id)){ 
				$field_name = ( ! empty( $this->settings_id ) ) ? $this->settings_id . $this->parent_field . '[' . $field_id . ']' : $field_id;
			}else{ 
				$field_name = ( ! empty( $this->settings_id ) ) ? $this->settings_id . '[' . $field_id . ']' : $field_id;
			}

			return $field_name;

		}

		//sanitize
		public function sanitize() {
			return sanitize_text_field( $this->value );
		}
	}
}
//a:10:{s:12:"featuressdas";s:0:"";s:16:"disable-services";a:1:{i:0;s:4:"tour";}s:7:"address";s:12:"Sydur Rahman";s:7:"gallery";s:0:"";s:8:"featured";s:0:"";s:4:"room";a:2:{i:0;a:19:{s:9:"unique_id";s:13:"1666245418295";s:8:"order_id";s:0:"";s:6:"enable";s:1:"1";s:5:"title";s:10:"Room title";s:8:"num-room";s:0:"";i:0;s:0:"";s:7:"gallery";s:0:"";s:3:"bed";s:0:"";s:5:"adult";s:0:"";s:5:"child";s:0:"";s:7:"footage";s:0:"";s:11:"description";s:0:"";s:10:"pricing-by";s:1:"1";s:5:"price";s:0:"";i:1;s:0:"";i:2;s:0:"";s:15:"price_multi_day";s:1:"1";i:3;s:0:"";i:4;s:1:"1";}i:1;a:19:{s:9:"unique_id";s:13:"1666245442085";s:8:"order_id";s:0:"";s:6:"enable";s:1:"1";s:5:"title";s:12:"Room title 2";s:8:"num-room";s:0:"";i:0;s:0:"";s:7:"gallery";s:0:"";s:3:"bed";s:0:"";s:5:"adult";s:0:"";s:5:"child";s:0:"";s:7:"footage";s:0:"";s:11:"description";s:0:"";s:10:"pricing-by";s:1:"1";s:5:"price";s:0:"";i:1;s:0:"";i:2;s:0:"";s:15:"price_multi_day";s:1:"1";i:3;s:0:"";i:4;s:1:"1";}}s:3:"faq";s:0:"";s:2:"tc";s:0:"";s:8:"h-review";s:0:"";s:7:"h-share";s:0:"";}
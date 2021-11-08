<?php if ( !defined( 'ABSPATH' ) ) {die;} // Cannot access directly.

/**
 *
 * Field: calendar
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( !class_exists( 'CSF_Field_Calendar' ) ) {
	class CSF_Field_calendar extends CSF_Fields {

		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		public function render() {

			echo $this->field_before();
			echo '<div id="calendar" name="' . $this->field_name() . '" '. $this->field_attributes() .' value="' . $this->value . '"></div>';

			echo $this->field_after();

		}

	}
}

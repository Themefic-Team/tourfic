<?php
// don't load directly

use Tourfic\Classes\Helper;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Tab_Switch' ) ) {
	class TF_Tab_Switch extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			?>
            <div id="<?php echo isset( $this->field['id'] ) ? esc_attr( $this->field['id'] ) : '' ?>" class="tf-tab-switch-box">
                <div class="tf-tab-field-header">
                    <div class="field-label">
                        <?php echo esc_html( $this->field['label'] ) ?>
                    </div>
                    <div class="tf-field-collapas">
                    <i class="fa fa-angle-down" aria-hidden="true"></i>
                    <?php foreach ( $this->field['fields'] as $key => $field ) :
                    if($key==0){
                        $parent  = '[' . $this->field['id'] . ']';
                        $default = isset( $field['default'] ) ? $field['default'] : '';
                        $value   = isset( $tf_meta_box_value[ $field['id'] ] ) ? $tf_meta_box_value[ $field['id'] ] : $default;

                        if ( ! empty( $this->value ) ) {
                                            
                            $data = ( ! is_array( $this->value ) ) ? unserialize( $this->value ) : $this->value;
                            if ( is_array( $data ) ) {
                                if ( isset( $data[ $field['id'] ] ) ) {
                                    $value = ( isset( $field['id'] ) ) ? $data[ $field['id'] ] : '';
                                    $value = ($field['type'] == 'text' || $field['type'] == 'textarea') ? stripslashes($value) : $value;
                                } else {
                                    $value = $default;
                                }
                            }
                        }
                        
                        // sanitize Wp Editor Field
                        $value = ( $field['type'] == 'editor' ) ? wp_kses_post($value) : $value;

                        $tf_option = new \Tourfic\Admin\TF_Options\TF_Options();
                        $tf_option->field( $field, $value, $this->settings_id, $parent );
                    }

                    endforeach; ?>
                    </div>
                </div>
                <div class="tf-tab-field-content">
                <?php
                foreach ( $this->field['fields'] as $key => $field ) :
                    if($key==0)
                    continue;
                    $parent  = '[' . $this->field['id'] . ']';
                    $default = isset( $field['default'] ) ? $field['default'] : '';
                    $value   = isset( $tf_meta_box_value[ $field['id'] ] ) ? $tf_meta_box_value[ $field['id'] ] : $default;
                    
                    if ( ! empty( $this->value ) ) {
										
                        $data = ( ! is_array( $this->value ) ) ? unserialize( $this->value ) : $this->value;
                        if ( is_array( $data ) ) {
                            if ( isset( $data[ $field['id'] ] ) ) {
                                $value = ( isset( $field['id'] ) ) ? $data[ $field['id'] ] : '';
                                $value = ($field['type'] == 'text' || $field['type'] == 'textarea') ? stripslashes($value) : $value;
                            } else {
                                $value = $default;
                            }
                        }
                    }
                    
                    // sanitize Wp Editor Field
                    $value = ( $field['type'] == 'editor' ) ? wp_kses_post($value) : $value;

                    $tf_option = new \Tourfic\Admin\TF_Options\TF_Options();
                    $tf_option->field( $field, $value, $this->settings_id, $parent );

                endforeach;
                ?>
                </div>
            </div>
			<?php
		}
		public function sanitize() {
			return $this->value;
		}
	}		

}
<?php
// don't load directly

use Tourfic\Classes\Helper;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Accordion' ) ) {
	class TF_Accordion extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {

            if(!is_array($this->value)){
                $tf_rep_value = preg_replace_callback('!s:(\d+):"(.*?)";!', function ($match) {
                    return 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
                }, $this->value);

                if (@unserialize($tf_rep_value) !== false || $tf_rep_value === 'b:0;') {
                    $data = @unserialize($tf_rep_value);
                } else {
                    $data = [];
                }
            }else{
                $data = $this->value;
            }
			?>
            <div id="<?php echo isset( $this->field['id'] ) ? esc_attr( $this->field['id'] ) : '' ?>" class="tf-tab-switch-box">
                <div class="tf-tab-field-header">
                    <div class="tf-field-collapas">
                        <div class="field-label">
                            <?php echo esc_html( $this->field['label'] ) ?>
                        </div>
                        <i class="fa fa-angle-up" aria-hidden="true"></i>
                    </div>
                    <?php foreach ( $this->field['fields'] as $key => $field ) :
                    if($key==0 && $this->field['enable_disable']){
                        if(!empty($this->parent_field)){
                            $parent = $this->parent_field.'[' . $this->field['id'] . '][' . $key . ']';
                        }else{
                            $parent = '[' . $this->field['id'] . '][' . $key . ']';
                        }
                        
                        $id = ( ! empty( $this->settings_id ) ) ? $this->settings_id . '[' . $this->field['id'] . '][00]' . '[' . $field['id'] . ']' : $this->field['id'] . '[00]' . '[' . $field['id'] . ']';

                        $default = isset( $field['default'] ) ? $field['default'] : '';

                        if ( isset( $tf_meta_box_value[ $id ] ) ) {
                            $value = isset( $tf_meta_box_value[ $id ] ) ? $tf_meta_box_value[ $id ] : $default;
                        } else {
                            $value = ( isset( $field['id'] ) && isset( $data[ $key ][ $field['id'] ] ) ) ? $data[ $key ][ $field['id'] ] : $default;
                        }

                        // sanitize Wp Editor Field
                        $value = ( $field['type'] == 'editor' ) ? wp_kses_post($value) : $value;

                        $tf_option = new \Tourfic\Admin\TF_Options\TF_Options();
                        $tf_option->field( $field, $value, $this->settings_id, $parent );
                    }

                    endforeach; ?>
                </div>
                <div class="tf-tab-field-content">
                <?php
                foreach ( $this->field['fields'] as $key => $field ) :
                    if($key==0)
                    continue;
                    if(!empty($this->parent_field)){
                        $parent = $this->parent_field.'[' . $this->field['id'] . '][' . $key . ']';
                    }else{
                        $parent = '[' . $this->field['id'] . '][' . $key . ']';
                    }
                    
                    $id = ( ! empty( $this->settings_id ) ) ? $this->settings_id . '[' . $this->field['id'] . '][00]' . '[' . $field['id'] . ']' : $this->field['id'] . '[00]' . '[' . $field['id'] . ']';

                    $default = isset( $field['default'] ) ? $field['default'] : '';

                    if ( isset( $tf_meta_box_value[ $id ] ) ) {
                        $value = isset( $tf_meta_box_value[ $id ] ) ? $tf_meta_box_value[ $id ] : $default;
                    } else {
                        $value = ( isset( $field['id'] ) && isset( $data[ $key ][ $field['id'] ] ) ) ? $data[ $key ][ $field['id'] ] : $default;
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
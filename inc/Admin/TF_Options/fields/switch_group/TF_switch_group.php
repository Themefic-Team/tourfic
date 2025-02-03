<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

use Tourfic\Classes\Helper;

/**
 *
 * Field: switch_group
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'TF_Switch_Group' ) ) {
	class TF_Switch_Group extends TF_Fields {
		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field);
		}
		public function render() {
			$args = wp_parse_args( $this->field, array(
				'label_on'  => esc_html__( 'On', 'tourfic' ),
				'label_off' => esc_html__( 'Off', 'tourfic' ),
			) );

			$on    = ( ! empty( $args['label_on'] ) ) ? $args['label_on'] : esc_html__( 'On', 'tourfic' );
			$off   = ( ! empty( $args['label_off'] ) ) ? $args['label_off'] : esc_html__( 'Off', 'tourfic' );
			$width = ( ! empty( $this->field['width'] ) ) ? ' style="width: ' . esc_attr( $this->field['width'] ) . 'px;"' : '';

			$class = 'tf-switch';
			if ( preg_match( '/class="([^"]*)"/', wp_kses_post($this->field_attributes()), $matches ) ) {
				$class_attribute_value = $matches[1];
				$class                 = $class . ' ' . $class_attribute_value;
			}
            $label = ( ! empty( $this->field['label'] ) ) ? $this->field['label'] : '';
            $column = ( ! empty( $this->field['column'] ) ) ? $this->field['column'] : 3;
			$value  = ( ! empty( $this->value ) && ! is_array( $this->value ) ) ? unserialize( $this->value ) : $this->value;
			?>
			<div class="tf-switch-group-wrap tf-switch-group-wrap-<?php echo esc_attr($this->field['id']);?> tf-switch-column-<?php echo esc_attr($column); ?>">
				<?php 
				if ( ! empty( $this->field['options'] ) && is_array($this->field['options']) ):
					foreach ( $this->field['options'] as $key => $option ) :
						$_value = ! empty( $value[ $key ] ) ? $value[ $key ] : '';
						$status_value = isset($_value['status']) ? esc_attr($_value['status']) : 0;
						// Helper::tf_var_dump($option);
						// Helper::tf_var_dump($_value);
						?>
						<div class="tf-switch-column">
							<label class="tf-switch-gr-label" for="<?php echo esc_attr( $this->field_name() ) . '[' . esc_attr($key) . '][status]'; ?>">
								<?php echo esc_html($option['label']) ?>
							</label>
							<input 
								type="hidden"
								name="<?php echo esc_attr( $this->field_name() ) . '[' . esc_attr($key) . '][slug]'; ?>" 
								value="<?php echo esc_attr($option['slug']); ?>"
							/>
							<label for="<?php echo esc_attr( $this->field_name() ) . '[' . esc_attr($key) . '][status]'; ?>" class="tf-switch-label" <?php echo wp_kses_post( $width ); ?>>
								<input
									type="checkbox" 
									id="<?php echo esc_attr( $this->field_name() ) . '[' . esc_attr($key) . '][status]'; ?>" 
									name="<?php echo esc_attr( $this->field_name() ) . '[' . esc_attr($key) . '][status]'; ?>" 
									value="<?php echo esc_attr($status_value); ?>" 
									class="<?php echo esc_attr($class) ?>"
									<?php checked( $status_value, 1 ); ?>
									<?php echo wp_kses_post($this->field_attributes()) ?>
								/>
								<span class="tf-switch-slider">
									<span class="tf-switch-on"><?php echo esc_html( $on ); ?></span>
									<span class="tf-switch-off"><?php echo esc_html( $off ); ?></span>
								</span>
							</label>
						</div>
						<?php 
					endforeach; 
				endif;
				?>
			</div>
			<?php

		}
		public function sanitize() {
			return $this->value;
		}
	}
}

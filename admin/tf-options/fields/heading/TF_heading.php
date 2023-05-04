<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_heading' ) ) {
	class TF_heading extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			if ( empty( $this->field['content'] ) && empty( $this->field['title'] ) && empty( $this->field['sub_title'] ) ) {
				return;
			}
			?>
            <div class="tf-field-heading-inner">
				<?php if ( ! empty( $this->field['icon'] ) ): ?>
                    <div class="tf-field-heading-icon">
                        <i class="<?php echo esc_attr( $this->field['icon'] ); ?>"></i>
                    </div>
				<?php endif; ?>
                <div class="tf-field-heading-content <?php echo ! empty( $this->field['content'] ) ? 'has-content' : '' ?>">
					<?php if ( ! empty( $this->field['title'] ) ): ?>
                        <h3><?php echo esc_html( $this->field['title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $this->field['sub_title'] ) ): ?>
                        <span class="tf-field-sub-title"><?php echo esc_html( $this->field['sub_title'] ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $this->field['content'] ) ): ?>
                        <div class="tf-field-heading-main-content"><?php echo wp_kses_post( $this->field['content'] ); ?></div>
					<?php endif; ?>
                </div>

            </div>
			<?php
		}

	}
}
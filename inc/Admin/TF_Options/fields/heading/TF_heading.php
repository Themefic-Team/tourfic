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
                        <div class="tf-field-heading-main-content">
							<p><?php echo wp_kses_post( $this->field['content'] ); ?></p>
						</div>
					<?php endif; ?>
                </div>
				<?php if(!empty($this->field['docs'])){ ?>
				<div class="tf-heading-docs">
					<a href="<?php echo esc_url($this->field['docs']); ?>" target="_blank">
						<?php echo esc_html__( 'Documentation', 'tourfic' ); ?>
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M5.8335 5.83203H14.1668V14.1654" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M5.8335 14.1654L14.1668 5.83203" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				</div>
				<?php } ?>

            </div>
			<?php
		}

	}
}
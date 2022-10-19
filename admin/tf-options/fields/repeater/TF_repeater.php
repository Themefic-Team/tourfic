<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: repeater
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'TF_Repeater' ) ) {
  class TF_Repeater extends TF_Fields {
    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
		parent::__construct( $field, $value, $unique, $where, $parent );
	  }
  
	  public function render() { 
		?>
		<div id="tf-repeater-1" class="tf-repeater">
			<div class="tf-repeater-wrap"> 
			</div>
			<div class=" tf-single-repeater-clone">
				<div class="tf-single-repeater">
					<div class="tf-repeater-header">
						<span class="tf-repeater-icon tf-repeater-icon-collapse">
							<svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M1.75781 1.75501L6.00045 5.99765L10.2431 1.7522" stroke="#5A5B6A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg> 
						</span>
						<span class="tf-repeater-title">Zip of the Hotel</span>
						<div class="tf-repeater-icon-absulate">
							<span class="tf-repeater-icon tf-repeater-icon-move">
								<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M9.50515 11.4213L7.44276 13.4836C7.19867 13.7277 6.80297 13.7277 6.55888 13.4836L4.49649 11.4213C4.10277 11.0275 4.38162 10.3543 4.93844 10.3543H6.27078L6.27075 7.72921H3.64566V9.06154C3.64566 9.61837 2.97246 9.89722 2.57871 9.5035L0.516318 7.44111C0.27223 7.19702 0.27223 6.80129 0.516318 6.55723L2.57871 4.49484C2.97243 4.10112 3.64566 4.37997 3.64566 4.93679V6.27088H6.27075V3.64578H4.93664C4.37982 3.64578 4.10097 2.97258 4.49469 2.57883L6.55708 0.51644C6.80117 0.272352 7.19687 0.272352 7.44096 0.51644L9.50335 2.57883C9.89707 2.97255 9.61822 3.64578 9.0614 3.64578H7.72906V6.27088H10.3542V4.93854C10.3542 4.38171 11.0274 4.10286 11.4211 4.49658L13.4835 6.55897C13.7276 6.80306 13.7276 7.19879 13.4835 7.44285L11.4211 9.50524C11.0274 9.89896 10.3541 9.62011 10.3542 9.06329V7.72921H7.72908V10.3543H9.06319C9.62002 10.3543 9.89887 11.0275 9.50515 11.4213Z" fill="#76A9FF"/>
								</svg>
							</span>
							<span class="tf-repeater-icon tf-repeater-icon-clone">
								<svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M8.50008 12V13.0417C8.50008 13.3869 8.22026 13.6667 7.87508 13.6667H0.791748C0.446566 13.6667 0.166748 13.3869 0.166748 13.0417V3.45837C0.166748 3.11319 0.446566 2.83337 0.791748 2.83337H2.66675V10.5417C2.66675 11.3458 3.32094 12 4.12508 12H8.50008ZM8.50008 3.04171V0.333374H4.12508C3.7799 0.333374 3.50008 0.613192 3.50008 0.958374V10.5417C3.50008 10.8869 3.7799 11.1667 4.12508 11.1667H11.2084C11.5536 11.1667 11.8334 10.8869 11.8334 10.5417V3.66671H9.12508C8.78133 3.66671 8.50008 3.38546 8.50008 3.04171ZM11.6504 2.23366L9.93313 0.516421C9.81592 0.399218 9.65695 0.333375 9.4912 0.333374L9.33341 0.333374V2.83337H11.8334V2.67559C11.8334 2.50983 11.7676 2.35087 11.6504 2.23366V2.23366Z" fill="#76A9FF"/>
								</svg>
							</span>
							<span class="tf-repeater-icon tf-repeater-icon-delete">
								<svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M1.00008 12.4167C1.00008 12.7482 1.13178 13.0662 1.3662 13.3006C1.60062 13.535 1.91856 13.6667 2.25008 13.6667H9.75008C10.0816 13.6667 10.3995 13.535 10.634 13.3006C10.8684 13.0662 11.0001 12.7482 11.0001 12.4167V3.66671H1.00008V12.4167ZM8.08341 5.75004C8.08341 5.63954 8.12731 5.53356 8.20545 5.45542C8.28359 5.37728 8.38957 5.33338 8.50008 5.33338C8.61059 5.33338 8.71657 5.37728 8.79471 5.45542C8.87285 5.53356 8.91674 5.63954 8.91674 5.75004V11.5834C8.91674 11.6939 8.87285 11.7999 8.79471 11.878C8.71657 11.9561 8.61059 12 8.50008 12C8.38957 12 8.28359 11.9561 8.20545 11.878C8.12731 11.7999 8.08341 11.6939 8.08341 11.5834V5.75004ZM5.58341 5.75004C5.58341 5.63954 5.62731 5.53356 5.70545 5.45542C5.78359 5.37728 5.88957 5.33338 6.00008 5.33338C6.11059 5.33338 6.21657 5.37728 6.29471 5.45542C6.37285 5.53356 6.41675 5.63954 6.41675 5.75004V11.5834C6.41675 11.6939 6.37285 11.7999 6.29471 11.878C6.21657 11.9561 6.11059 12 6.00008 12C5.88957 12 5.78359 11.9561 5.70545 11.878C5.62731 11.7999 5.58341 11.6939 5.58341 11.5834V5.75004ZM3.08341 5.75004C3.08341 5.63954 3.12731 5.53356 3.20545 5.45542C3.28359 5.37728 3.38957 5.33338 3.50008 5.33338C3.61059 5.33338 3.71657 5.37728 3.79471 5.45542C3.87285 5.53356 3.91675 5.63954 3.91675 5.75004V11.5834C3.91675 11.6939 3.87285 11.7999 3.79471 11.878C3.71657 11.9561 3.61059 12 3.50008 12C3.38957 12 3.28359 11.9561 3.20545 11.878C3.12731 11.7999 3.08341 11.6939 3.08341 11.5834V5.75004ZM11.4167 1.16671H8.29174L8.04695 0.679733C7.9951 0.575622 7.91522 0.488046 7.81631 0.426857C7.71739 0.365669 7.60337 0.333295 7.48706 0.333379H4.5105C4.39445 0.332932 4.28062 0.365185 4.18206 0.426442C4.0835 0.487699 4.00418 0.575481 3.9532 0.679733L3.70841 1.16671H0.583415C0.472908 1.16671 0.366927 1.21061 0.288787 1.28875C0.210647 1.36689 0.166748 1.47287 0.166748 1.58338L0.166748 2.41671C0.166748 2.52722 0.210647 2.6332 0.288787 2.71134C0.366927 2.78948 0.472908 2.83338 0.583415 2.83338H11.4167C11.5273 2.83338 11.6332 2.78948 11.7114 2.71134C11.7895 2.6332 11.8334 2.52722 11.8334 2.41671V1.58338C11.8334 1.47287 11.7895 1.36689 11.7114 1.28875C11.6332 1.21061 11.5273 1.16671 11.4167 1.16671V1.16671Z" fill="#F8877F"/>
								</svg>
							</span>
						</div>
					</div>
					<div class="tf-repeater-content-wrap">
						
						<?php foreach ( $this->field['fields'] as $field ) :
							$id = $this->metabox_id . '[' . $field['id'] . ']';
							$value = isset( $tf_meta_box_value[ $field['id'] ] ) ? $tf_meta_box_value[ $field['id'] ] : '';
							?>

							<div class="tf-field tf-field-<?php echo esc_attr( $field['type'] ); ?>">
								<label for="<?php echo esc_attr( $id ) ?>" class="tf-field-title"><?php echo esc_html( $field['title'] ) ?></label>
								<?php if ( $field['subtitle'] ) : ?>
									<span class="tf-field-sub-title"><?php echo wp_kses_post( $field['subtitle'] ) ?></span>
								<?php endif; ?>

								<div class="tf-fieldset">
									<?php
									$fieldClass = 'TF_' . $field['type'];
									if ( class_exists( $fieldClass ) ) {
										$_field = new $fieldClass( $field, $value, $this->metabox_id );
										$_field->render();
									} else {
										echo '<p>' . __( 'Field not found!', 'tourfic' ) . '</p>';
									}
									?>
								</div>
								<p class="description"><?php echo wp_kses_post( $field['description'] ) ?></p>
							</div>

						<?php endforeach; ?>
						<div class="tf-field tf-field-text">
							<label for="tf_hotels[address]" class="tf-field-title">Address</label>
							
							<div class="tf-fieldset">
								<input type="text" name="hello" id="hello" placeholder="Address of the hotel" value="Ut perspiciatis rec">                                    </div>
							<p class="description">Address of the hotel</p>
						</div>
					</div>
				</div>
				
			</div>
			
			<div class="tf-repeater-add">
				<span class="tf-repeater-icon tf-repeater-icon-add">
				<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M8.03166 8.77607e-07C8.58394 0.000724776 9.03107 0.449026 9.03035 1.00131L9.02249 7H15C15.5523 7 16 7.44772 16 8C16 8.55228 15.5523 9 15 9H9.01987L9.012 15.0013C9.01128 15.5536 8.56297 16.0007 8.01069 16C7.4584 15.9993 7.01128 15.551 7.012 14.9987L7.01986 9H1C0.447715 9 0 8.55228 0 8C0 7.44772 0.447715 7 1 7H7.02248L7.03035 0.998689C7.03107 0.446405 7.47938 -0.000722961 8.03166 8.77607e-07Z" fill="#2979FF"/>
				</svg>
				</span>
			</div>
		</div>
		<?php
  
	  }
  
	  public function enqueue() {
  
		if ( ! wp_script_is( 'jquery-ui-sortable' ) ) {
		  wp_enqueue_script( 'jquery-ui-sortable' );
		}
  
	  }
  }
}

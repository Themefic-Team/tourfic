<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

$tc = !empty($meta['terms_and_conditions']) ? $meta['terms_and_conditions'] : '';
$tc = is_array($tc) ? implode("\n", $tc) : (string) $tc;
if ( $tc ) {
	$tc_lines = array_filter( array_map( 'trim', explode( "\n", $tc ) ) );
?>
<!-- apartment Policies Starts -->        
<div class="tf-policies-wrapper tf-section" id="tf-apartment-policies">            
    <h2 class="tf-section-title">
        <?php echo !empty($meta['tc-section-title']) ? esc_html($meta['tc-section-title']) : esc_html__("Policies","tourfic"); ?>
    </h2>  
    <div class="tf-policies">
        <?php if ( ! empty( $tc_lines ) ) { ?>
        <ul class="tf-policies-list">
            <?php foreach ( $tc_lines as $line ) { ?>
            <li><?php echo wp_kses_post( $line ); ?></li>
            <?php } ?>
        </ul>
        <?php } ?>
    </div>
</div>
<!-- apartment Policies end -->
<?php } ?>
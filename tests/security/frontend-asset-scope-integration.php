<?php
/** Real WordPress query and asset-registration checks in the marked disposable site. */
if ( ! defined( 'ABSPATH' ) || ! file_exists( ABSPATH . '.tourfic-booking-wishlist-test' ) ) {
	throw new RuntimeException( 'Disposable synthetic site required.' );
}
global $checks, $failed;
$checks = 0; $failed = array();
function tourfic_asset_fixture_expect( $condition, $label ) {
	++$GLOBALS['checks']; if ( ! $condition ) { $GLOBALS['failed'][] = $label; }
}
function tourfic_asset_fixture_query( $id ) {
	$GLOBALS['wp_query'] = new WP_Query( array( 'p' => $id, 'post_type' => 'any', 'post_status' => 'any' ) );
	$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
	$GLOBALS['post'] = get_post( $id );
}
$enqueue = \Tourfic\Classes\Enqueue::instance();
foreach ( array(
	array( 'post', '<div class="tf-apt-highlights-slider" data-slick="{}">Inert text</div>', false ),
	array( 'page', 'Ordinary page', false ),
	array( 'page', '[tourfic_apartment]', true ),
	array( 'tf_hotel', '', true ), array( 'tf_room', '', true ), array( 'tf_tours', '', true ),
	array( 'tf_apartment', '', true ), array( 'tf_carrental', '', true ),
) as $case ) {
	$id = wp_insert_post( array( 'post_type' => $case[0], 'post_status' => 'publish', 'post_title' => 'Synthetic asset fixture', 'post_content' => $case[1] ) );
	tourfic_asset_fixture_query( $id );
	tourfic_asset_fixture_expect( $enqueue->needs_frontend_assets() === $case[2], $case[0] . ' content scope' );
}
$id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Synthetic builder fixture' ) );
tourfic_asset_fixture_query( $id );
update_post_meta( $id, '_elementor_data', wp_json_encode( array( array( 'widgetType' => 'text-editor', 'settings' => array( 'editor' => 'tf-apt-highlights-slider' ) ) ) ) );
tourfic_asset_fixture_expect( ! $enqueue->needs_frontend_assets(), 'Text widget lookalike is not a Tourfic component' );
update_post_meta( $id, '_elementor_data', wp_json_encode( array( array( 'widgetType' => 'tf-single-highlights' ) ) ) );
tourfic_asset_fixture_expect( $enqueue->needs_frontend_assets(), 'Elementor Tourfic component supported' );
delete_post_meta( $id, '_elementor_data' );
update_post_meta( $id, '_bricks_page_content_2', array( array( 'name' => 'tft-rooms' ) ) );
tourfic_asset_fixture_expect( $enqueue->needs_frontend_assets(), 'Bricks maintained companion component supported' );
delete_post_meta( $id, '_bricks_page_content_2' );
$reusable = wp_insert_post( array( 'post_type' => 'wp_block', 'post_status' => 'publish', 'post_content' => '[tourfic_search_form]' ) );
wp_update_post( array( 'ID' => $id, 'post_content' => '<!-- wp:block {"ref":' . $reusable . '} /-->' ) );
tourfic_asset_fixture_query( $id );
tourfic_asset_fixture_expect( $enqueue->needs_frontend_assets(), 'Reusable block containing a supported shortcode' );
wp_update_post( array( 'ID' => $id, 'post_content' => 'Ordinary preview', 'post_status' => 'draft' ) );
tourfic_asset_fixture_query( $id ); $GLOBALS['wp_query']->is_preview = true;
tourfic_asset_fixture_expect( ! $enqueue->needs_frontend_assets(), 'Ordinary preview excluded' );
wp_scripts()->queue = array(); wp_styles()->queue = array();
$enqueue->tf_enqueue_scripts();
tourfic_asset_fixture_expect( ! wp_script_is( 'tourfic', 'enqueued' ) && ! wp_script_is( 'tf-slick', 'enqueued' ), 'Ordinary preview loads neither runtime' );
$enqueue->register_frontend_dependencies();
tourfic_asset_fixture_expect( wp_script_is( 'tf-slick', 'registered' ), 'Shared dependency remains registered for companion consumers' );
$enqueue->shortcode_assets( false, 'tourfic_search_form' );
tourfic_asset_fixture_expect( wp_script_is( 'tourfic', 'enqueued' ) && wp_script_is( 'tf-slick', 'enqueued' ), 'Late shortcode render receives assets' );
tourfic_asset_fixture_expect( in_array( 'tf-slick', wp_scripts()->registered['tourfic']->deps, true ), 'Slick executes before the Tourfic bundle' );
echo wp_json_encode( array( 'checks' => $checks, 'failures' => $failed ) ) . "\n";
if ( $failed ) { throw new RuntimeException( 'Frontend asset assertions failed.' ); }

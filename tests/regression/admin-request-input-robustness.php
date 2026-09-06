<?php
/**
 * Regression checks for robust admin pagination and setup-step input handling.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/admin-request-input-robustness.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

function add_action() {}
function add_filter() {}

function absint( $value ) {
	return abs( (int) $value );
}

function sanitize_key( string $key ) {
	return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', $key ) );
}

function wp_unslash( $value ) {
	return stripslashes( $value );
}

class WP_List_Table {
	public $items = array();
	protected $_pagination_args = array();

	public function __construct() {}

	protected function set_pagination_args( $args ) {
		$this->_pagination_args = $args;
	}

	public function get_pagenum() {
		$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0;

		if ( isset( $this->_pagination_args['total_pages'] ) && $pagenum > $this->_pagination_args['total_pages'] ) {
			$pagenum = $this->_pagination_args['total_pages'];
		}

		return max( 1, $pagenum );
	}

	public function get_sortable_columns() {
		return array();
	}
}

require_once ABSPATH . 'inc/Admin/TF_List_Table.php';
require_once ABSPATH . 'inc/Admin/TF_Setup_Wizard.php';

class Tourfic_Test_List_Table extends \Tourfic\Admin\TF_List_Table {
	public function get_columns() {
		return array();
	}

	public function get_items() {
		return $this->items;
	}
}

function tourfic_admin_request_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

$items = range( 1, 45 );

$_REQUEST['paged'] = 'not-a-page';
$list_table        = new Tourfic_Test_List_Table( $items );
$list_table->prepare_items();
tourfic_admin_request_assert(
	range( 1, 20 ) === $list_table->get_items(),
	'Nonnumeric pagination input must safely resolve to the first page.'
);

$_REQUEST['paged'] = '999';
$list_table        = new Tourfic_Test_List_Table( $items );
$list_table->prepare_items();
tourfic_admin_request_assert(
	range( 41, 45 ) === $list_table->get_items(),
	'Out-of-range pagination input must safely resolve to the final page.'
);

$_REQUEST['paged'] = '1';
$list_table        = new Tourfic_Test_List_Table( array() );
$list_table->prepare_items();
tourfic_admin_request_assert(
	array() === $list_table->get_items(),
	'An empty list must produce an empty iterable item set.'
);

$current_step = new ReflectionProperty( \Tourfic\Admin\TF_Setup_Wizard::class, 'current_step' );
$current_step->setAccessible( true );

foreach ( array( 'welcome', 'step_1', 'step_2', 'step_3', 'step_4', 'step_5', 'step_6', 'finish' ) as $allowed_step ) {
	$_GET['step'] = $allowed_step;
	new \Tourfic\Admin\TF_Setup_Wizard();
	tourfic_admin_request_assert(
		$allowed_step === $current_step->getValue(),
		"The {$allowed_step} setup route must remain available."
	);
}

foreach ( array( 'unknown', array( 'step_1' ), new stdClass() ) as $invalid_step ) {
	$_GET['step'] = $invalid_step;
	new \Tourfic\Admin\TF_Setup_Wizard();
	tourfic_admin_request_assert(
		'welcome' === $current_step->getValue(),
		'Invalid or non-scalar setup routes must fall back to welcome.'
	);
}

echo "Tourfic admin request input robustness checks passed.\n";

<?php
/**
 * Exercise the real order query helper and wpdb::prepare() with synthetic SQLite data.
 *
 * Run: php tests/security/order-query-parameterization.php [wordpress-root]
 * Core libraries only; no WordPress bootstrap, network or persistent database.
 */

if ( 'cli' !== PHP_SAPI || defined( 'ABSPATH' ) ) {
	exit( 1 );
}

$tourfic_root   = dirname( __DIR__, 2 );
$wordpress_root = $argv[1] ?? dirname( $tourfic_root, 3 );
if ( ! is_readable( $wordpress_root . '/wp-includes/class-wpdb.php' ) ) {
	echo "Pass the path to a WordPress installation; its database will not be loaded.\n";
	exit( 1 );
}
if ( ! extension_loaded( 'pdo_sqlite' ) ) {
	echo "This isolated regression requires pdo_sqlite.\n";
	exit( 1 );
}

define( 'ABSPATH', rtrim( $wordpress_root, '/' ) . '/' );
define( 'WPINC', 'wp-includes' );

set_error_handler( function( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

function get_option( $key, $default = false ) { return 'blog_charset' === $key ? 'UTF-8' : $default; }
function is_utf8_charset( $charset = null ) { return _is_utf8_charset( $charset ?? 'UTF-8' ); }
function absint( $value ) { return abs( (int) $value ); }
function wp_load_translations_early() {}
function __( $text, $domain = 'default' ) { return $text; }
function _doing_it_wrong( $function, $message, $version ) {
	throw new RuntimeException( $function . ': ' . $message );
}

foreach ( array(
	'compat.php', 'compat-utf8.php', 'class-wp-token-map.php', 'utf8.php',
	'plugin.php', 'formatting.php', 'class-wpdb.php',
) as $tourfic_library ) {
	if ( is_readable( ABSPATH . WPINC . '/' . $tourfic_library ) ) {
		require ABSPATH . WPINC . '/' . $tourfic_library;
	}
}

require $tourfic_root . '/inc/Traits/Singleton.php';
require $tourfic_root . '/inc/Traits/TF_Fonts.php';
require $tourfic_root . '/inc/Traits/Action_Helper.php';
require $tourfic_root . '/inc/Classes/Helper.php';

class Tourfic_Test_Order_Query_DB extends wpdb {
	public $connection;
	public $prepared_query;
	public $prepared_values;
	public $executed_query;

	public function __construct() {
		$this->prefix     = 'synthetic_';
		$this->posts      = 'synthetic_posts';
		$this->connection = new PDO( 'sqlite::memory:' );
		$this->connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	}

	public function prepare( $query, ...$args ) {
		$this->prepared_query  = $query;
		$this->prepared_values = 1 === count( $args ) && is_array( $args[0] ) ? $args[0] : $args;

		return parent::prepare( $query, ...$args );
	}

	public function _real_escape( $data ) {
		// Only connection-specific quoting changes; core still parses and binds placeholders.
		$quoted = $this->connection->quote( (string) $data );

		return $this->add_placeholder_escape( substr( $quoted, 1, -1 ) );
	}

	public function get_results( $query = null, $output = OBJECT ) {
		if ( ARRAY_A !== $output || ! is_string( $query ) || '' === $query ) {
			throw new RuntimeException( 'Expected a prepared query returning associative rows.' );
		}
		$this->executed_query = $this->remove_placeholder_escape( $query );

		return $this->connection->query( $this->executed_query )->fetchAll( PDO::FETCH_ASSOC );
	}
}

$wpdb = new Tourfic_Test_Order_Query_DB();
$wpdb->connection->exec(
	'CREATE TABLE synthetic_tf_order_data (
		id INTEGER PRIMARY KEY, order_id INTEGER, post_id INTEGER, post_type TEXT,
		room_number INTEGER, room_id TEXT, check_in TEXT, check_out TEXT,
		billing_details TEXT, shipping_details TEXT, order_details TEXT, customer_id INTEGER,
		payment_method TEXT, ostatus TEXT, order_date TEXT, checkinout TEXT, checkinout_by TEXT
	)'
);
$wpdb->connection->exec( 'CREATE TABLE synthetic_posts (ID INTEGER PRIMARY KEY, post_author INTEGER, post_type TEXT)' );

$tourfic_insert = $wpdb->connection->prepare(
	'INSERT INTO synthetic_tf_order_data
		(id, order_id, post_id, post_type, room_id, customer_id, ostatus, checkinout, check_in, check_out, order_date)
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
foreach ( array(
	array( 1, 1001, 11, 'hotel', "Harbor's 50% room", 71, 'completed', 'in', '2026-11-20', '2026-11-21', '2026-11-01' ),
	array( 2, 1002, 12, 'hotel', 'Garden room', 72, 'processing', 'not', '2026-11-21', '2026-11-22', '2026-11-02' ),
	array( 3, 1003, 13, 'tour', 'Day trip', 71, 'completed', 'out', '2026-11-21', '2026-11-22', '2026-11-02' ),
	array( 4, 1004, 14, 'hotel', 'Balcony room', 71, 'completed', 'in', '2026-11-22', '2026-11-23', '2026-11-03' ),
) as $tourfic_record ) {
	$tourfic_insert->execute( $tourfic_record );
}
$tourfic_insert = $wpdb->connection->prepare( 'INSERT INTO synthetic_posts VALUES (?, ?, ?)' );
foreach ( array( array( 11, 7, 'tf_hotel' ), array( 12, 8, 'tf_hotel' ),
	array( 13, 7, 'tf_tours' ), array( 14, 7, 'tf_hotel' ) ) as $tourfic_record ) {
	$tourfic_insert->execute( $tourfic_record );
}

$tourfic_query_checks = 0;
function tourfic_order_query_expect( $condition, $label ) {
	++$GLOBALS['tourfic_query_checks'];
	if ( ! $condition ) {
		throw new RuntimeException( $label );
	}
}
function tourfic_order_query_rows( $args = array() ) {
	return \Tourfic\Classes\Helper::tourfic_order_table_data(
		array_merge( array( 'select' => '*', 'post_type' => 'hotel' ), $args )
	);
}
function tourfic_order_query_ids( $rows ) {
	return array_map( 'intval', array_column( $rows, 'id' ) );
}

tourfic_order_query_expect( array( 1, 2, 4 ) === tourfic_order_query_ids( tourfic_order_query_rows() ), 'Post type excludes unrelated bookings.' );
tourfic_order_query_expect( array( 'hotel' ) === $wpdb->prepared_values, 'Post type remains a separately prepared value.' );

$tourfic_rows = tourfic_order_query_rows( array(
	'where' => array( 'order_id' => '1001', 'post_id' => '11', 'customer_id' => '71' ),
) );
tourfic_order_query_expect( array( 1 ) === tourfic_order_query_ids( $tourfic_rows ), 'Numeric predicates select the intended booking.' );
tourfic_order_query_expect( array( 'hotel', 1001, 11, 71 ) === $wpdb->prepared_values, 'Numeric predicates are normalized and bound separately.' );
tourfic_order_query_expect( 3 === substr_count( $wpdb->prepared_query, '%d' ), 'Every numeric predicate retains its integer placeholder.' );

$tourfic_room = "Harbor's 50% room";
$tourfic_rows = tourfic_order_query_rows( array(
	'where' => array( 'room_id' => $tourfic_room, 'ostatus' => 'completed', 'checkinout' => 'in' ),
) );
tourfic_order_query_expect( array( 1 ) === tourfic_order_query_ids( $tourfic_rows ), 'Ordinary apostrophe and percent characters preserve exact text matching.' );
tourfic_order_query_expect( array( 'hotel', $tourfic_room, 'completed', 'in' ) === $wpdb->prepared_values, 'Text predicates remain separate bound values.' );
tourfic_order_query_expect( false === strpos( $wpdb->prepared_query, $tourfic_room ), 'Text is absent from the unprepared SQL template.' );
tourfic_order_query_expect( 4 === substr_count( $wpdb->prepared_query, '%s' ), 'Every text predicate retains its string placeholder.' );

$tourfic_rows = tourfic_order_query_rows( array( 'post_author' => '7', 'author_post_type' => 'tf_hotel' ) );
tourfic_order_query_expect( array( 1, 4 ) === tourfic_order_query_ids( $tourfic_rows ), 'Author subquery excludes another listing owner.' );
tourfic_order_query_expect( array( 'hotel', 7, 'tf_hotel' ) === $wpdb->prepared_values, 'Author ID and listing type are separately prepared.' );
tourfic_order_query_expect( false !== strpos( $wpdb->prepared_query, 'post_id IN (SELECT ID FROM synthetic_posts WHERE post_author = %d AND post_type = %s)' ), 'Ownership uses the scoped posts subquery.' );
tourfic_order_query_expect( array() === tourfic_order_query_rows( array( 'post_author' => 7, 'author_post_type' => 'tf_tours' ) ), 'Author subquery also enforces the listing post type.' );

foreach ( array( 'id', 'order_id', 'order_date', 'check_in', 'check_out' ) as $tourfic_sort ) {
	foreach ( array( 'ASC', 'DESC' ) as $tourfic_direction ) {
		$tourfic_rows = tourfic_order_query_rows( array(
			'select' => ' id, order_id ', 'orderby' => $tourfic_sort, 'order' => $tourfic_direction,
		) );
		$tourfic_expected = 'ASC' === $tourfic_direction ? array( 1, 2, 4 ) : array( 4, 2, 1 );
		tourfic_order_query_expect( $tourfic_expected === tourfic_order_query_ids( $tourfic_rows ), 'Approved sort preserves result order: ' . $tourfic_sort . ' ' . $tourfic_direction );
		tourfic_order_query_expect( array( 'id', 'order_id' ) === array_keys( $tourfic_rows[0] ), 'Approved projections retain only requested fields.' );
		tourfic_order_query_expect( false !== strpos( $wpdb->prepared_query, ' ORDER BY ' . $tourfic_sort . ' ' . $tourfic_direction ), 'Approved sort is emitted from the allowlist.' );
	}
}

$tourfic_rows = tourfic_order_query_rows( array(
	'orderby' => 'id', 'order' => 'ASC', 'limit' => array( 'offset' => '1', 'per_page' => '1' ),
) );
tourfic_order_query_expect( array( 2 ) === tourfic_order_query_ids( $tourfic_rows ), 'Limit and offset select the expected page.' );
tourfic_order_query_expect( array( 'hotel', 1, 1 ) === $wpdb->prepared_values, 'Pagination values are bound as integers.' );
tourfic_order_query_expect( false !== strpos( $wpdb->prepared_query, ' LIMIT %d, %d' ), 'Pagination retains placeholders.' );
tourfic_order_query_expect( array( 1 ) === tourfic_order_query_ids( tourfic_order_query_rows( array(
	'orderby' => 'id', 'order' => 'ASC', 'limit' => array( 'offset' => 0, 'per_page' => 1 ),
) ) ), 'The first page supports a zero offset.' );
tourfic_order_query_expect( 3 === count( tourfic_order_query_rows( array( 'limit' => array( 'per_page' => 0 ) ) ) ), 'A zero page size keeps the existing unbounded contract.' );

$tourfic_marker = 'unused_query_marker';
$tourfic_rows   = tourfic_order_query_rows( array(
	'select' => 'id, friendly_column, order_id',
	'where' => array( 'friendly_column' => $tourfic_marker, 'post_id' => '' ),
	'orderby' => 'friendly_column', 'order' => 'sideways', 'query' => $tourfic_marker,
) );
tourfic_order_query_expect( array( 1, 2, 4 ) === tourfic_order_query_ids( $tourfic_rows ), 'Undeclared fields and legacy raw fragments cannot change the result set.' );
tourfic_order_query_expect( array( 'id', 'order_id' ) === array_keys( $tourfic_rows[0] ), 'An unrecognized projection is omitted beside approved fields.' );
tourfic_order_query_expect( false === strpos( $wpdb->executed_query, 'friendly_column' ), 'Unrecognized identifiers never reach SQL.' );
tourfic_order_query_expect( false === strpos( $wpdb->executed_query, $tourfic_marker ), 'Caller raw query fragments are ignored.' );
tourfic_order_query_expect( false === strpos( $wpdb->executed_query, 'ORDER BY' ), 'An unrecognized sort column is omitted.' );
tourfic_order_query_expect( array( 'hotel' ) === $wpdb->prepared_values, 'Ignored fields add no placeholder arguments.' );

$tourfic_rows = tourfic_order_query_rows( array( 'select' => 'friendly_column' ) );
tourfic_order_query_expect( isset( $tourfic_rows[0]['post_type'], $tourfic_rows[0]['customer_id'] ), 'An entirely unrecognized projection retains the existing wildcard fallback.' );
$tourfic_rows = tourfic_order_query_rows( array( 'orderby' => 'id', 'order' => 'sideways' ) );
tourfic_order_query_expect( array( 4, 2, 1 ) === tourfic_order_query_ids( $tourfic_rows ), 'An unrecognized direction falls back to descending order.' );
tourfic_order_query_expect( false !== strpos( $wpdb->prepared_query, 'ORDER BY id DESC' ), 'The fallback direction is a fixed SQL token.' );

restore_error_handler();
echo 'PASS: ' . $tourfic_query_checks . " order-query assertions with real wpdb::prepare() and in-memory SQLite.\n";

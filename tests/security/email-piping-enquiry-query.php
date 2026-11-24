<?php
/**
 * Synthetic regression for the companion's inbound enquiry identifier boundary.
 * Run: php tests/security/email-piping-enquiry-query.php
 */

define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
require dirname( ABSPATH ) . '/tourfic-email-piping/includes/Traits/Helper.php';
set_error_handler( function( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );
class Tourfic_Test_Email_Enquiry {
	use \TFEP\Traits\Helper;
}
class Tourfic_Test_Email_DB {
	public $prefix = 'synthetic_';
	public $queries = array();
	public $updates = array();
	public $reply_data = '[]';
	public $exists = true;
	public function prepare( $sql, $id ) {
		if ( 1 !== substr_count( $sql, '%d' ) || ! is_int( $id ) || $id < 1 ) {
			throw new RuntimeException( 'Expected one separately bound positive identifier.' );
		}
		$this->queries[] = array( $sql, $id );
		return str_replace( '%d', (string) $id, $sql );
	}
	public function get_row( $query ) {
		return $this->exists ? (object) array( 'reply_data' => $this->reply_data, 'enquiry_status' => 'replied' ) : null;
	}
	public function update( $table, $data, $where ) {
		$this->updates[] = array( $table, $data, $where );
		return 1;
	}
}
$GLOBALS['wpdb'] = new Tourfic_Test_Email_DB();
$GLOBALS['tourfic_email_notices'] = array();
function update_option( $key, $value ) { $GLOBALS['tourfic_email_notices'][] = array( $key, $value ); }
$count = 0;
function tourfic_email_expect( $condition, $label ) {
	++$GLOBALS['count'];
	if ( ! $condition ) { throw new RuntimeException( $label ); }
}
foreach ( array(
	'Re: Response to Your Enquiry About Hotel - #7',
	'Re: Response to Your Enquiry About Trip - City - #7',
) as $subject ) {
	$wpdb->queries = $wpdb->updates = array();
	Tourfic_Test_Email_Enquiry::set_mail_data_from_db( array( array( 'reply_subject' => $subject, 'reply_mail' => 'synthetic@example.invalid' ) ) );
	tourfic_email_expect( 1 === count( $wpdb->queries ) && 7 === $wpdb->queries[0][1], 'Subject suffix is a separately bound identifier.' );
	tourfic_email_expect( 1 === count( $wpdb->updates ) && 7 === $wpdb->updates[0][2]['id'], 'Valid reply is stored against the intended synthetic enquiry.' );
	tourfic_email_expect( 'responded' === $wpdb->updates[0][1]['enquiry_status'], 'Existing response-status contract is preserved.' );
}
foreach ( array(
	'Response to Hotel - #-7', 'Response to Hotel - #0', 'Response to Hotel - #1.5',
	'Response to Hotel - #1e2', 'Response to Hotel - #7\'', 'Response to Hotel - #7 [MALFORMED_TEST_VALUE]',
	'Response to Hotel - #' . str_repeat( '9', 40 ), 'Response to Hotel - #7' . "\n", array( 'Response to Hotel - #7' ),
) as $subject ) {
	$wpdb->queries = $wpdb->updates = array();
	Tourfic_Test_Email_Enquiry::set_mail_data_from_db( array( array( 'reply_subject' => $subject ) ) );
	tourfic_email_expect( ! $wpdb->queries && ! $wpdb->updates, 'Malformed subjects are rejected before querying or updating.' );
}
$wpdb->exists = false;
$wpdb->updates = array();
Tourfic_Test_Email_Enquiry::set_mail_data_from_db( array( array( 'reply_subject' => 'Response to Hotel - #999' ) ) );
tourfic_email_expect( ! $wpdb->updates, 'Missing synthetic record is not updated.' );
$wpdb->exists = true;
foreach ( array( null, '{malformed', 'null' ) as $reply_data ) {
	$wpdb->reply_data = $reply_data;
	$wpdb->updates = array();
	Tourfic_Test_Email_Enquiry::set_mail_data_from_db( array( array( 'reply_subject' => 'Response to Hotel - #7' ) ) );
	tourfic_email_expect( 1 === count( $wpdb->updates ), 'Empty or malformed historical reply data does not break ingestion.' );
}
echo 'PASS: ' . $count . " Email Piping query-boundary assertions using synthetic data.\n";

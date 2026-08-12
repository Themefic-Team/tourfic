<?php
/**
 * Regression checks for the dbDelta-managed Tourfic tables.
 */

$tf_test_root     = dirname( __DIR__, 2 );
$tf_database_file = file_get_contents( $tf_test_root . '/inc/Traits/Database.php' );
$tf_base_file     = file_get_contents( $tf_test_root . '/inc/Classes/Base.php' );
$tf_migrator_file = file_get_contents( $tf_test_root . '/inc/Classes/Migrator.php' );

function tourfic_database_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

tourfic_database_assert(
	false === strpos( $tf_database_file, 'CREATE TABLE IF NOT EXISTS' ),
	'dbDelta schemas must use CREATE TABLE without IF NOT EXISTS.'
);
tourfic_database_assert(
	false === strpos( $tf_database_file, 'ALTER TABLE' ),
	'Order table upgrades must not use a separate raw ALTER TABLE query.'
);
tourfic_database_assert(
	false === strpos( $tf_base_file, 'tf_admin_table_alter_order_data' ),
	'The redundant raw order-table migration must not be registered.'
);
tourfic_database_assert(
	false !== strpos( $tf_database_file, 'reply_data LONGTEXT NULL' ),
	'The enquiry schema must keep reply data optional for enquiries without replies.'
);
tourfic_database_assert(
	false === strpos( $tf_database_file, "reply_data LONGTEXT NOT NULL DEFAULT ''" ),
	'The enquiry schema must not assign a default value to a LONGTEXT column.'
);
tourfic_database_assert(
	false !== strpos( $tf_migrator_file, 'ADD COLUMN `reply_data` LONGTEXT NULL' ),
	'The enquiry migration must add reply data without a TEXT default value.'
);
tourfic_database_assert(
	false === strpos( $tf_migrator_file, "ADD COLUMN `reply_data` LONGTEXT NOT NULL DEFAULT ''" ),
	'The enquiry migration must remain compatible with databases that reject TEXT defaults.'
);

foreach ( array( 'checkinout', 'checkinout_by', 'room_id' ) as $tf_column ) {
	tourfic_database_assert(
		false !== strpos( $tf_database_file, $tf_column . ' varchar(255) NULL' ),
		"The dbDelta schema must retain the {$tf_column} column."
	);
}

echo "Database schema migration regression checks passed.\n";

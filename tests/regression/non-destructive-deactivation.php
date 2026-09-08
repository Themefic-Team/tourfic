<?php
/**
 * Regression checks for non-destructive plugin deactivation.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/non-destructive-deactivation.php
 */

$tourfic_test_root        = dirname( __DIR__, 2 );
$tourfic_base_file        = file_get_contents( $tourfic_test_root . '/inc/Classes/Base.php' );
$tourfic_deactivator_path = $tourfic_test_root . '/inc/Classes/Deactivator.php';
$tourfic_activator_file   = file_get_contents( $tourfic_test_root . '/inc/Classes/Activator.php' );

function tourfic_deactivation_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

tourfic_deactivation_assert(
	false === strpos( $tourfic_base_file, 'Deactivator::instance()' ),
	'Tourfic must not register the obsolete destructive deactivation callback.'
);
tourfic_deactivation_assert(
	! file_exists( $tourfic_deactivator_path ),
	'The obsolete page-deleting deactivator implementation must remain removed.'
);
tourfic_deactivation_assert(
	false !== strpos( $tourfic_activator_file, 'if ( $option_value > 0 && get_post( $option_value ) )' ),
	'Reactivation must continue reusing pages referenced by existing page ID options.'
);
tourfic_deactivation_assert(
	false === strpos( $tourfic_activator_file, 'wp_delete_post(' ),
	'Activation and reactivation must not delete preserved Tourfic pages.'
);

echo "Tourfic non-destructive deactivation regression checks passed.\n";

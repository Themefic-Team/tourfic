<?php
defined( 'ABSPATH' ) || exit;

spl_autoload_register( 'tourfic_autoloader' );

function tourfic_autoloader( $class ) {
	$namespace = 'Tourfic\\';

	if ( 0 !== strpos( $class, $namespace ) ) {
		return;
	}

	$main_class_name = substr( $class, strlen( $namespace ) );
	$class_file      = TF_INC_PATH . str_replace( '\\', '/', $main_class_name ) . '.php';

	// if the file exists, require it
	if ( file_exists( $class_file ) ) {
		require_once $class_file;
	}
}
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

	// Attempt to load the class file with original case
	if ( file_exists( $class_file ) ) {
		require_once $class_file;
		return;
	}

	// Attempt to load the class file with lowercase
	$main_class_name = strpos( $main_class_name, 'Classes' ) !== false ? str_replace( 'Classes', 'classes', $main_class_name ) : $main_class_name;
	$class_file_lowercase = TF_INC_PATH . str_replace( '\\', '/', $main_class_name ) . '.php';

	if ( file_exists( $class_file_lowercase ) ) {
		require_once $class_file_lowercase;
		return;
	}
}
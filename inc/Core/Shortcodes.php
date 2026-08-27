<?php

namespace Tourfic\Core;

defined( 'ABSPATH' ) || exit;

abstract class Shortcodes {

	protected $shortcode = '';

	public function __construct() {
		add_shortcode( $this->shortcode, array( $this, 'render' ) );
	}

	abstract public function render( $atts, $content = '' );
}

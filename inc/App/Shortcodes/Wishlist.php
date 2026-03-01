<?php

namespace Tourfic\App\Shortcodes;

defined( 'ABSPATH' ) || exit;

use \Tourfic\App\Wishlist as WishlistApp;

class Wishlist extends \Tourfic\Core\Shortcodes {

	use \Tourfic\Traits\Singleton;

	protected $shortcode = 'tf-wishlist';

	function render( $atts, $content = null ) {
		$defaults = array(
			'type' => null,
		);
		$atts     = wp_parse_args( $atts, $defaults );
		$type     = $atts['type'];
		if ( is_user_logged_in() ) {
			return (new WishlistApp())->tf_generate_table_for_user( $type );
		} else {
			// create a  holder for guest items
			return '<div class="tf-wishlist-holder" data-type="' . $atts['type'] . '" data-nonce="' . wp_create_nonce( 'populate-wishlist-guest-nonce' ) . '"> <table class="table"> <thead> <tr> <th>#</th> <th>Name</th> <th></th> </tr> </thead> <tbody> <tr> <td class="tf-text-preloader"> <div class="tf-bar"></div> </td> <td class="tf-text-preloader"> <div class="tf-bar"></div> </td> <td class="tf-text-preloader"> <div class="tf-bar"></div> </td> </tr> </tbody> </table></div>';
		}
	}
}
<?php

namespace Tourfic\Classes\Tour;

defined( 'ABSPATH' ) || exit;

class Pricing {
	use \Tourfic\Traits\Singleton;

	protected $post_id;
	protected $date;
	protected $persons;

	function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	/*
	 * Set date
	 */
	function set_date($date) {
		$this->date = $date;
		return $this;
	}

	/*
	 * Set persons
	 */
	function set_persons(array $persons) {
		$this->persons = $persons;
		return $this;
	}
}
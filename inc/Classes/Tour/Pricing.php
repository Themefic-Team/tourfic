<?php

namespace Tourfic\Classes\Tour;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

class Pricing {
	use \Tourfic\Traits\Singleton;

	protected $post_id;
	protected $meta;
	protected $date;
	protected $time;
	protected $persons;

	function __construct( $post_id ) {
		$this->post_id = $post_id;
		$this->meta = get_post_meta( $post_id, 'tf_tours_opt', true );
	}

	/*
	 * Set date
	 */
	function set_date($date) {
		$this->date = $date;
		return $this;
	}

	/*
	 * Set time
	 */
	function set_time($time){
		$this->time = $time;
		return $this;
	}

	/*
	 * Set persons
	 */
	public function set_persons(int $adult, int $child, int $infant) {
		$this->persons = array(
			'adult' => !empty($adult) ? $adult : 0,
			'child' => !empty($child) ? $child : 0,
			'infant' => !empty($infant) ? $infant : 0,
		);

		return $this;
	}

	/*
	 * Get tour type
	 */
	function get_type() {
		return ! empty( $this->meta['type'] ) ? $this->meta['type'] : '';
	}

	/*
	 * Get adult price
	 */
	function get_adult_price() {

	}

	function get_total_price(  ) {

	}
}
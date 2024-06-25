<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

/**
 * Search form action url
 */
function tf_booking_search_action() {

	// get data from global settings else default
	$search_result_action = ! empty( Helper::tfopt( 'search-result-page' ) ) ? get_permalink( Helper::tfopt( 'search-result-page' ) ) : home_url( '/search-result/' );

	// can be override by filter
	return apply_filters( 'tf_booking_search_action', $search_result_action );

}


// doc link Positioning end

/**
 * Notice wrapper
 */
function tourfic_notice_wrapper() {
	?>
    <div class="tf-container">
        <div class="tf-notice-wrapper"></div>
    </div>
	<?php
}

add_action( 'tf_before_container', 'tourfic_notice_wrapper', 10 );

/**
 * Function: tf_term_count
 *
 * @return number of available terms
 */
if ( ! function_exists( 'tf_term_count' ) ) {
	function tf_term_count( $filter, $destination, $default_count ) {

		if ( $destination == '' ) {
			return $default_count;
		}

		$term_count = array();

		$args = array(
			'post_type'      => 'tf_hotel',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'tax_query'      => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'hotel_location',
					'field'    => 'slug',
					'terms'    => $destination
				)
			)
		);

		$loop = new WP_Query( $args );

		if ( $loop->have_posts() ) :
			while ( $loop->have_posts() ) : $loop->the_post();

				if ( has_term( $filter, 'tf_filters', get_the_ID() ) == true ) {
					$term_count[] = 'true';
				}

			endwhile;
		endif;

		return count( $term_count );

		wp_reset_postdata();
	}
}

/**
 * Set search reult page
 */
function tourfic_booking_set_search_result( $url ) {

	$search_result_page = Helper::tfopt( 'search-result-page' );

	if ( isset( $search_result_page ) ) {
		$url = get_permalink( $search_result_page );
	}

	return $url;

}

add_filter( 'tf_booking_search_action', 'tourfic_booking_set_search_result' );

/**
 * Dropdown Multiple Support
 */
add_filter( 'wp_dropdown_cats', 'tourfic_wp_dropdown_cats_multiple', 10, 2 );
function tourfic_wp_dropdown_cats_multiple( $output, $r ) {
	if ( isset( $r['multiple'] ) && $r['multiple'] ) {
		$output = preg_replace( '/^<select/i', '<select multiple', $output );
		$output = str_replace( "name='{$r['name']}'", "name='{$r['name']}[]'", $output );
		//if( is_array($r['selected']) ):
		foreach ( array_map( 'trim', explode( ",", $r['selected'] ) ) as $value ) {
			$output = str_replace( "value=\"{$value}\"", "value=\"{$value}\" selected", $output );
		}
		//endif;
	}

	return $output;
}

/**
 * Filter the excerpt "read more" string.
 *
 * @param string $more "Read more" excerpt string.
 *
 * @return string (Maybe) modified "read more" excerpt string.
 */
function tf_tours_excerpt_more( $more ) {

	if ( 'tf_tours' === get_post_type() ) {
		return '...';
	}

}

add_filter( 'excerpt_more', 'tf_tours_excerpt_more' );


/**
 * Pagination for the search page
 * @since 2.9.0
 * @author Abu Hena
 */
function tourfic_posts_navigation( $wp_query = '' ) {
	if ( empty( $wp_query ) ) {
		global $wp_query;
	}
	$max_num_pages = $wp_query->max_num_pages;
	$paged         = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
	if ( $max_num_pages > 1 ) {
		echo "<div id='tf_posts_navigation_bar'>";
		echo wp_kses_post(
			paginate_links( array(
				'current'   => $paged,
				'total'     => $max_num_pages,
				'mid_size'  => 2,
				'prev_next' => true,
			) )
		);
		echo "</div>";
	}

}

/**
 * Flatpickr locale
 */
if ( ! function_exists( 'tf_flatpickr_locale' ) ) {
	function tf_flatpickr_locale( $placement = 0 ) {

		$flatpickr_locale     = ! empty( get_locale() ) ? get_locale() : 'en_US';
		$allowed_locale       = array( 'ar', 'bn_BD', 'de_DE', 'es_ES', 'fr_FR', 'hi_IN', 'it_IT', 'nl_NL', 'ru_RU', 'zh_CN' );
		$tf_first_day_of_week = ! empty( Helper::tfopt( "tf-week-day-flatpickr" ) ) ? Helper::tfopt( "tf-week-day-flatpickr" ) : 0;

		if ( in_array( $flatpickr_locale, $allowed_locale ) ) {

			switch ( $flatpickr_locale ) {
				case "bn_BD":
					$flatpickr_locale = 'bn';
					break;
				case "de_DE":
					$flatpickr_locale = 'de';
					break;
				case "es_ES":
					$flatpickr_locale = 'es';
					break;
				case "fr_FR":
					$flatpickr_locale = 'fr';
					break;
				case "hi_IN":
					$flatpickr_locale = 'hi';
					break;
				case "it_IT":
					$flatpickr_locale = 'it';
					break;
				case "nl_NL":
					$flatpickr_locale = 'nl';
					break;
				case "ru_RU":
					$flatpickr_locale = 'ru';
					break;
				case "zh_CN":
					$flatpickr_locale = 'zh';
					break;
			}
		} else {
			$flatpickr_locale = 'default';
		}

		if ( ! empty( $placement ) && ! empty( $flatpickr_locale ) && $placement == "root" ) {

			echo esc_html( <<<EOD
				window.flatpickr.l10ns.$flatpickr_locale.firstDayOfWeek = $tf_first_day_of_week;
			EOD
			);

		} else {
			echo 'locale: "' . esc_html( $flatpickr_locale ) . '",';
		}
	}
}


/**
 * Flatten a multi-dimensional array into a single level.
 *
 * @param iterable $array
 * @param int $depth
 *
 * @return array
 * @author devkabir
 *
 */
if ( ! function_exists( 'tf_array_flatten' ) ) {
	function tf_array_flatten( $array, $depth = INF ) {

		$result = [];

		foreach ( $array as $item ) {
			if ( ! is_array( $item ) ) {
				$result[] = $item;
			} else {
				$values = $depth === 1
					? array_values( $item )
					: tf_array_flatten( $item, $depth - 1 );

				foreach ( $values as $value ) {
					$result[] = $value;
				}
			}
		}

		return $result;

	}
}
/**
 * Calculate the deposit amount based on the deposit type and pricing. This function will return $deposit_amount and
 * $has_deposit
 *
 * @param array $room collection of room data
 * @param float|integer $price calculated price
 * @param float|integer $deposit_amount calculated deposit amount
 * @param boolean $has_deposit is deposit allowed for this room?
 *
 *
 * @author Dev Kabir <dev.kabir01@gmail.com>
 */
if ( ! function_exists( 'tf_get_deposit_amount' ) ) {
	function tf_get_deposit_amount( $room, $price, &$deposit_amount, &$has_deposit, $discount = 0 ) {
		$deposit_amount = null;
		if ( $discount > 0 ) {
			$price = $discount;
		}
		$has_deposit = ! empty( $room['allow_deposit'] ) && $room['allow_deposit'] == true;
		if ( $has_deposit == true ) {
			if ( $room['deposit_type'] == 'percent' ) {
				$deposit_amount = $price * ( intval( $room['deposit_amount'] ) / 100 );
			} else {
				$deposit_amount = $room['deposit_amount'];
			}
		}
	}
};
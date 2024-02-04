<?php 
if ( ! function_exists( 'tf_taxable_option_callback' ) ) {
	function tf_taxable_option_callback() {
		$tax_classes = WC_Tax::get_tax_classes();
		$all_classes = array(
			'standard' => 'Standard Rates'
		);
    	foreach($tax_classes as $tax){
			$all_classes [sanitize_title($tax)] = $tax. ' Rates';
		}

		return $all_classes;
	}
}
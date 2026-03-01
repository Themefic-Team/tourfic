<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_map' ) ) {
	class TF_map extends TF_Fields {

		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '') {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
            if ( ! empty( $this->value ) ):
             $mapdata = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
                return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
              }, $this->value );
              if(gettype($mapdata)=="string"){
                $mapdata = unserialize( $mapdata );
              }
            endif;
            $args              = wp_parse_args( $this->field, array(
                'placeholder'    => esc_html__( 'Search Address...', 'tourfic' ),
                'latitude_text'  => esc_html__( 'Latitude', 'tourfic' ),
                'longitude_text' => esc_html__( 'Longitude', 'tourfic' ),
                'address_field'  => '',
                'height'         => '250',
              ) );
        
              $value             = wp_parse_args( $this->value, array(
                'address'        => '',
                'latitude'       => '20',
                'longitude'      => '0',
                'zoom'           => '5',
              ) );
            if( !empty($mapdata) ){
              $default_settings   = array(
                'center'          => array( $mapdata['latitude'], $mapdata['longitude'] ),
                'zoom'            => !empty($mapdata['zoom']) ? $mapdata['zoom'] : '5',
                'scrollWheelZoom' => true,
              );
            }else{
                $default_settings   = array(
                'center'          => array( $value['latitude'], $value['longitude'] ),
                'zoom'            => $value['zoom'],
                'scrollWheelZoom' => true,
                ); 
            }
        
              $settings = ( ! empty( $this->field['settings'] ) ) ? $this->field['settings'] : array();
              $settings = wp_parse_args( $settings, $default_settings );


              if ( empty( $args['address_field'] ) ) {
                echo '<div class="tf--map-search">';
                if( !empty($mapdata['address']) ){
                    echo '<input type="text" class="tf_gmap_address" name="'. esc_attr( $this->field_name( ) ). '[address]' .'" value="'. esc_attr( $mapdata['address'] ) .'" placeholder="' . esc_attr( $args['placeholder'] ) . '" />';
                }else{
                    echo '<input type="text" class="tf_gmap_address" name="'. esc_attr( $this->field_name( ) ). '[address]' .'" value="'. esc_attr( $value['address'] ) .'" placeholder="' . esc_attr( $args['placeholder'] ) . '" />';
                }
                echo '</div>';
              } else {
                echo '<div class="tf--address-field" data-address-field="'. esc_attr( $args['address_field'] ) .'"></div>';
              }
        
              echo '<div class="tf--map-osm-wrap"><div class="tf--map-osm" data-map="'. esc_attr( wp_json_encode( $settings ) ) .'"></div></div>';
        
              echo '<div class="tf--map-inputs">';
        
                echo '<div class="tf--map-input">';
                echo '<label>'. esc_attr( $args['latitude_text'] ) .'</label>';
                if( !empty($mapdata['latitude']) ){
                    echo '<input type="text" name="'. esc_attr( $this->field_name( ) ). '[latitude]' .'" value="'. esc_attr( $mapdata['latitude'] ) .'" class="tf--latitude" />';
                }else{
                    echo '<input type="text" name="'. esc_attr( $this->field_name( ) ). '[latitude]' .'" value="'. esc_attr( $value['latitude'] ) .'" class="tf--latitude" />';
                }
                echo '</div>';
        
                echo '<div class="tf--map-input">';
                echo '<label>'. esc_attr( $args['longitude_text'] ) .'</label>';
                if( !empty($mapdata['longitude']) ){
                    echo '<input type="text" name="'. esc_attr( $this->field_name( ) ). '[longitude]' .'" value="'. esc_attr( $mapdata['longitude'] ) .'" class="tf--longitude" />'; 
                }else{
                    echo '<input type="text" name="'. esc_attr( $this->field_name( ) ). '[longitude]' .'" value="'. esc_attr( $value['longitude'] ) .'" class="tf--longitude" />';
                }
                echo '</div>';
        
              echo '</div>';
              if( !empty($mapdata['zoom']) ){
                echo '<input type="hidden" name="'. esc_attr( $this->field_name( ) ). '[zoom]' .'" value="'. esc_attr( $mapdata['zoom'] ) .'" class="tf--zoom" />';
              }else{
                echo '<input type="hidden" name="'. esc_attr( $this->field_name( ) ). '[zoom]' .'" value="'. esc_attr( $value['zoom'] ) .'" class="tf--zoom" />';
              }
              
		}

	}
}
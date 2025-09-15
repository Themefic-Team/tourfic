<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Archive;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Tourfic\Classes\Helper;
use Tourfic\Classes\Hotel\Hotel;
use Tourfic\Classes\Tour\Tour;
use \Tourfic\Classes\Apartment\Apartment;
use \Tourfic\Classes\Hotel\Pricing as HotelPricing;
use Tourfic\Classes\Tour\Pricing as TourPricing;
use Tourfic\Classes\Apartment\Pricing as AptPricing;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Search Form Horizontal
 */
class Listings extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	public function get_name() {
		return 'tf-listings';
	}

	public function get_title() {
		return esc_html__( 'Archive Listings', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-post-list';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'hotels',
            'tours',
            'apartments',
            'cars',
            'rentals',
            'services',
			'tourfic',
			'tf'
        ];
    }

	protected function register_controls() {
        $this->tf_content_layout_controls();
        $this->tf_content_listing_settings_controls();
        $this->tf_pagination_controls();

		do_action( 'tf/listing/before-style-controls', $this );
		$this->tf_general_style_controls();
		$this->tf_toggle_btn_style_controls();
		$this->tf_card_style_controls();
		$this->tf_thumbnail_style_controls();
		$this->tf_featured_badge_style_controls();
		$this->tf_discount_tag_style_controls();
		$this->tf_promotional_tag_style_controls();
		$this->tf_title_style_controls();
		$this->tf_location_style_controls();
		$this->tf_review_style_controls();
		$this->tf_gallery_style_controls();
		$this->tf_features_style_controls();
		$this->tf_excerpt_style_controls();
		$this->tf_car_info_style_controls();
		$this->tf_price_style_controls();
		$this->tf_button_style_controls();
		$this->tf_pagination_style_controls();
		do_action( 'tf/listing/after-style-controls', $this );
	}
	
	protected function render() {
		$settings           = $this->get_settings_for_display();
		$service            = !empty( $settings['service'] ) ? $settings['service'] : 'tf_hotel';
		$design_hotel       = !empty( $settings['design_hotel'] ) ? $settings['design_hotel'] : 'design-1';
		$design_tours       = !empty( $settings['design_tours'] ) ? $settings['design_tours'] : 'design-1';
		$design_apartment   = !empty( $settings['design_apartment'] ) ? $settings['design_apartment'] : 'design-1';
		$design_car   		= !empty( $settings['design_car'] ) ? $settings['design_car'] : 'design-1';
		
		if($service == 'tf_hotel'){
			$design = $design_hotel;
		} elseif($service == 'tf_tours'){
			$design = $design_tours;
		} elseif($service == 'tf_apartment'){
			$design = $design_apartment;
		} elseif($service == 'tf_carrental'){
			$design = $design_car;
		}

		$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
        $query_args = array(
            'post_type' => $service,
            'posts_per_page' => !empty( $settings['posts_per_page'] ) ? absint($settings['posts_per_page']) : 10,
            'post_status' => 'publish',
			'orderby' => !empty( $settings['orderby'] ) ? sanitize_text_field($settings['orderby']) : 'date',
			'order' => !empty( $settings['order'] ) ? sanitize_text_field($settings['order']) : 'desc',
			'paged' => $paged,
        );

		// Check if we're on a taxonomy archive page for the same service
		if (is_tax()) {
			$current_taxonomy = get_queried_object()->taxonomy;
			$current_term = get_queried_object()->slug;
			
			// Get all taxonomies for this service
			$service_taxonomies = get_object_taxonomies($service);
			
			// If current taxonomy belongs to this service, filter by it
			if (in_array($current_taxonomy, $service_taxonomies)) {
				$query_args['tax_query'] = array(
					array(
						'taxonomy' => $current_taxonomy,
						'field'    => 'slug',
						'terms'    => $current_term,
					)
				);
			}
		}

		$query = new \WP_Query( $query_args );

		if ( $service == 'tf_hotel' && $design == "design-1" ) {
			$this->tf_hotel_design_1( $settings, $query );
		} elseif ( $service == 'tf_hotel' && $design == "design-2" ) {
			$this->tf_hotel_design_2( $settings, $query );
		} elseif ( $service == 'tf_hotel' && $design == "design-3" ) {
			$this->tf_hotel_design_3( $settings, $query );
		} elseif ( $service == 'tf_hotel' && $design == "default" ) {
			$this->tf_hotel_design_legacy( $settings, $query );
		} elseif ( $service == 'tf_tours' && $design == "design-1" ) {
			$this->tf_tour_design_1( $settings, $query );
		} elseif ( $service == 'tf_tours' && $design == "design-2" ) {
			$this->tf_tour_design_2( $settings, $query );
		} elseif ( $service == 'tf_tours' && $design == "design-3" ) {
			$this->tf_tour_design_3( $settings, $query );
		} elseif ( $service == 'tf_tours' && $design == "default" ) {
			$this->tf_tour_design_legacy( $settings, $query );
		} elseif ( $service == 'tf_apartment' && $design == "design-1" ) {
			$this->tf_apartment_design_1( $settings, $query );
		} elseif ( $service == 'tf_apartment' && $design == "design-2" ) {
			$this->tf_apartment_design_2( $settings, $query );
		} elseif ( $service == 'tf_apartment' && $design == "default" ) {
			$this->tf_apartment_design_legacy( $settings, $query );
		} elseif ( $service == 'tf_carrental' && $design == "design-1" ) {
			$this->tf_car_design_1( $settings, $query );
		}

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ): ?>
			<script>
				var zoomLvl = 5;
				var zoomChangeEnabled = false;
				var centerLvl = new google.maps.LatLng(23.8697847, 90.4219536);
				var markersById = {};
				var markers = [];
				var mapChanged = false;
				var hotelMap;

				const googleMapInit = (mapLocations, mapLat = 23.8697847, mapLng = 90.4219536) => {
					// Clear existing markers
					clearMarkers();

					var locations = mapLocations ? JSON.parse(mapLocations) : [];

					if(!hotelMap){
						hotelMap = new google.maps.Map(document.getElementById("tf-hotel-archive-map"), {
							zoom: zoomLvl,
							minZoom: 3,
							maxZoom: 18,
							center: new google.maps.LatLng(mapLat, mapLng),
							mapTypeId: google.maps.MapTypeId.ROADMAP,
							styles: [
								{elementType: 'labels.text.fill', stylers: [{color: '#44348F'}]},
							],
							fullscreenControl: false
						});
					}

					var infowindow = new google.maps.InfoWindow({
						maxWidth: 262,
						disableAutoPan: true,
					});

					var bounds = new google.maps.LatLngBounds();
					locations.map(function (location, i) {
						var marker = new MarkerWithLabel({
							position: new google.maps.LatLng(location['lat'], location['lng']),
							map: hotelMap,
							icon: {
								url: document.getElementById('map-marker').dataset.marker,
								scaledSize: new google.maps.Size(tf_params.map_marker_width, tf_params.map_marker_height),
							},
							labelContent: '<div class="tf_price_inner" data-post-id="' + location['id'] + '">' + window.atob(location['price']) + '</div>',
							labelAnchor: new google.maps.Point(0, 0),
							labelClass: "tf_map_price",
						});

						markersById[location['id']] = marker;
						markers.push(marker);
						bounds.extend(marker.position);

						// Define an OverlayView to use the projection for pixel calculation
						const overlay = new google.maps.OverlayView();
						overlay.draw = function () {};
						overlay.setMap(hotelMap);

						google.maps.event.addListener(marker, 'mouseover', function () {
							infowindow.setContent(window.atob(location['content']));

							// Convert LatLng to pixel coordinates
							const markerPosition = marker.getPosition();
							const markerProjection = overlay.getProjection();
							const markerPixel = markerProjection.fromLatLngToDivPixel(markerPosition);

							// Infowindow dimensions
							const infoWindowHeight = 265;
							const infoWindowWidth = 262;

							// Check each edge
							const isNearLeftEdge = markerPixel.x <= -120;
							const isNearRightEdge = markerPixel.x >= 120;
							const isNearTopEdge = (markerPixel.y - (infoWindowHeight+40)) <= -infoWindowHeight;

							let anchorX = 0.5;
							let anchorY = 0;

							if (isNearLeftEdge) {
								anchorX = 0.9;
							} else if (isNearRightEdge) {
								anchorX = 0.1;
							}

							if (isNearTopEdge) {
								anchorY = infoWindowHeight+90
							}

							infowindow.setOptions({
								pixelOffset: new google.maps.Size((anchorX - 0.5) * infoWindowWidth, anchorY)
							});

							infowindow.open(hotelMap, marker);
						});

						// Hide the infowindow on mouse leave
						google.maps.event.addListener(marker, 'mouseout', function () {
							infowindow.close();
						});

						google.maps.event.addListener(marker, 'click', function () {
							window.open(location?.url, '_blank')
						});
					});

					// Trigger filter on map drag
					google.maps.event.addListener(hotelMap, "dragend", function () {
						zoomLvl = hotelMap.getZoom();
						centerLvl = hotelMap.getCenter();
						mapChanged = true;

						filterVisibleHotels(hotelMap);
					});

					google.maps.event.addListener(hotelMap, "zoom_changed", function () {
						if (zoomChangeEnabled) return;

						zoomLvl = hotelMap.getZoom();
						centerLvl = hotelMap.getCenter();
						mapChanged = true;

						filterVisibleHotels(hotelMap);

					});

					var listener = google.maps.event.addListener(hotelMap, "idle", function() {
						zoomChangeEnabled = true;
						if (!mapChanged) {
							hotelMap.fitBounds(bounds);
							centerLvl = bounds.getCenter();
							hotelMap.setCenter(centerLvl);

						} else {
							hotelMap.setZoom(zoomLvl);
							hotelMap.setCenter({lat: centerLvl.lat(), lng: centerLvl.lng()});
							google.maps.event.removeListener(listener);
						}
						zoomChangeEnabled = false;
					});
				}

				function filterVisibleHotels(map) {
					var bounds = map.getBounds();

					if (bounds) {
						var sw = bounds.getSouthWest();
						var ne = bounds.getNorthEast();
					}

					makeFilter('', [sw.lat(), sw.lng(), ne.lat(), ne.lng()]);
				}

				function clearMarkers() {
					markers.forEach(marker => marker.setMap(null)); // Remove each marker from the map
					markers = []; // Clear the array to prevent duplication
				}

				// GOOGLE MAP INITIALIZE
				var mapLocations = jQuery('#map-datas').html();
				if (jQuery('#map-datas').length && mapLocations.length) {
					googleMapInit(mapLocations);
				}
			</script>
		<?php
		endif;
		wp_reset_postdata();
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_listing_layouts',[
			'label' => esc_html__('Service & Layouts', 'tourfic'),
		]);

        do_action( 'tf/listings/before-layout/controls', $this );

		//service
		$this->add_control('service',[
			'type'     => Controls_Manager::SELECT,
			'label'    => esc_html__( 'Service', 'tourfic' ),
			'options'  => [
				'tf_hotel'     => esc_html__( 'Hotel', 'tourfic' ),
				'tf_tours'     => esc_html__( 'Tour', 'tourfic' ),
				'tf_apartment' => esc_html__( 'Apartment', 'tourfic' ),
				'tf_carrental' => esc_html__( 'Car', 'tourfic' ),
			],
			'default'  => 'tf_hotel',
		]);
		
		// Design options for Hotel
		$this->add_control('design_hotel',[
			'type'     => Controls_Manager::SELECT,
			'label'    => esc_html__( 'Design', 'tourfic' ),
			'options'  => [
				'design-1' => esc_html__( 'Design 1', 'tourfic' ),
				'design-2' => esc_html__( 'Design 2', 'tourfic' ),
				'design-3' => esc_html__( 'Design 3', 'tourfic' ),
				'default'  => esc_html__( 'Legacy', 'tourfic' ),
			],
			'default'  => 'design-1',
			'condition' => [
				'service' => 'tf_hotel',
			],
		]);
		
		// Design options for Tour
		$this->add_control('design_tours',[
			'type'     => Controls_Manager::SELECT,
			'label'    => esc_html__( 'Design', 'tourfic' ),
			'options'  => [
				'design-1' => esc_html__( 'Design 1', 'tourfic' ),
				'design-2' => esc_html__( 'Design 2', 'tourfic' ),
				'design-3' => esc_html__( 'Design 3', 'tourfic' ),
				'default'  => esc_html__( 'Legacy', 'tourfic' ),
			],
			'default'  => 'design-1',
			'condition' => [
				'service' => 'tf_tours',
			],
		]);
		
		// Design options for Apartment
		$this->add_control('design_apartment',[
			'type'     => Controls_Manager::SELECT,
			'label'    => esc_html__( 'Design', 'tourfic' ),
			'options'  => [
				'design-1' => esc_html__( 'Design 1', 'tourfic' ),
				'design-2' => esc_html__( 'Design 2', 'tourfic' ),
				'default'  => esc_html__( 'Legacy', 'tourfic' ),
			],
			'default'  => 'design-1',
			'condition' => [
				'service' => 'tf_apartment',
			],
		]);
		
		// Design options for Car Rental
		$this->add_control('design_carrental',[
			'type'     => Controls_Manager::SELECT,
			'label'    => esc_html__( 'Design', 'tourfic' ),
			'options'  => [
				'design-1' => esc_html__( 'Design 1', 'tourfic' ),
			],
			'default'  => 'design-1',
			'condition' => [
				'service' => 'tf_carrental',
			],
		]);

		$this->add_control( 'listing_layout_toggle', [
			'label' => esc_html__( 'Layout Toggle', 'tourfic' ),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__( 'Show', 'tourfic' ),
			'label_off' => esc_html__( 'Hide', 'tourfic' ),
			'return_value' => 'yes',
			'default' => 'yes',
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-3', 'default'],
                'tf_tours' => ['design-1', 'design-3', 'default'],
                'tf_apartment' => ['design-2', 'default'],
                'tf_carrental' => ['design-1'],
            ]),
		]);

		$this->add_control( 'listing_default_layout', [
			'label'   => esc_html__( 'Deafult Layout', 'tourfic' ),
			'type'    => Controls_Manager::CHOOSE,
			'options' => [
				'grid' => [
					'title' => esc_html__( 'Grid', 'tourfic' ),
					'icon'  => 'eicon-gallery-grid',
				],
				'list' => [
					'title' => esc_html__( 'List', 'tourfic' ),
					'icon'  => 'eicon-post-list',
				],
			],
			'default' => 'list',
			'toggle'  => false,
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-3', 'default'],
                'tf_tours' => ['design-1', 'design-3', 'default'],
                'tf_apartment' => ['design-2', 'default'],
                'tf_carrental' => ['design-1'],
            ],[
				'listing_layout_toggle' => 'yes',
			]),
		]);

        $this->add_control('listing_layout',[
			'label'   => esc_html__( 'Layout', 'tourfic' ),
			'type'    => Controls_Manager::CHOOSE,
			'options' => [
				'grid' => [
					'title' => esc_html__( 'Grid', 'tourfic' ),
					'icon'  => 'eicon-gallery-grid',
				],
				'list' => [
					'title' => esc_html__( 'List', 'tourfic' ),
					'icon'  => 'eicon-post-list',
				],
			],
			'default' => 'list',
			'toggle'  => false,
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-3', 'default'],
                'tf_tours' => ['design-1', 'design-3', 'default'],
                'tf_apartment' => ['design-2', 'default'],
                'tf_carrental' => ['design-1'],
            ],[
				'listing_layout_toggle!' => 'yes',
			]),
		]);

        $this->add_responsive_control('grid_column', [
			'label' => esc_html__('Columns', 'tourfic'),
			'type' => Controls_Manager::SELECT,
			'default' => '2',
			'options' => [
				'1' => esc_html__('1', 'tourfic'),
				'2' => esc_html__('2', 'tourfic'),
				'3' => esc_html__('3', 'tourfic'),
			],
			'toggle' => true,
			'conditions' => [
				'relation' => 'or',
				'terms' => [
					[
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'listing_layout_toggle',
								'operator' => '==',
								'value' => 'yes',
							],
							[
								'name' => 'listing_default_layout',
								'operator' => '==',
								'value' => 'grid',
							],
						],
					],
					[
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'listing_layout_toggle',
								'operator' => '!=',
								'value' => 'yes',
							],
							[
								'name' => 'listing_layout',
								'operator' => '==',
								'value' => 'grid',
							],
						],
					],
				],
			],
		]);

		$this->add_control( 'show_total_result', [
			'label' => esc_html__( 'Show Total Result', 'tourfic' ),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__( 'Show', 'tourfic' ),
			'label_off' => esc_html__( 'Hide', 'tourfic' ),
			'return_value' => 'yes',
			'default' => 'yes',
		]);

		$this->add_control( 'show_sorting', [
			'label' => esc_html__( 'Show Sorting', 'tourfic' ),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__( 'Show', 'tourfic' ),
			'label_off' => esc_html__( 'Hide', 'tourfic' ),
			'return_value' => 'yes',
			'default' => 'yes',
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-2', 'default'],
                'tf_tours' => ['design-1', 'design-2', 'default'],
                'tf_apartment' => ['design-1', 'default'],
            ]),
		]);

		$this->add_control( 'show_sidebar', [
			'label' => esc_html__( 'Show Sidebar', 'tourfic' ),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__( 'Show', 'tourfic' ),
			'label_off' => esc_html__( 'Hide', 'tourfic' ),
			'return_value' => 'yes',
			'default' => 'yes',
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-3'],
                'tf_tours' => ['design-3'],
                'tf_apartment' => ['design-2'],
            ]),
		]);

	    do_action( 'tf/listings/before-layout/controls', $this );

        $this->end_controls_section();
    }

	protected function tf_content_listing_settings_controls(){
        $this->start_controls_section('tf_section_listings_settings', [
            'label' => esc_html__('Listing Settings', 'tourfic'),
        ]);

		$this->add_control('posts_per_page', [
            'label' => esc_html__('Posts Per Page', 'tourfic'),
            'type' => Controls_Manager::NUMBER,
            'default' => 10,
            'min' => 1,
            'max' => 1000,
            'step' => 1,
        ]);

        $this->add_control('orderby', [
            'label' => esc_html__('Order By', 'tourfic'),
            'type' => Controls_Manager::SELECT,
            'options' => [
				'ID'            => esc_html__( 'Post ID', 'tourfic' ),
				'author'        => esc_html__( 'Post Author', 'tourfic' ),
				'title'         => esc_html__( 'Title', 'tourfic' ),
				'date'          => esc_html__( 'Date', 'tourfic' ),
				'modified'      => esc_html__( 'Last Modified Date', 'tourfic' ),
				'rand'          => esc_html__( 'Random', 'tourfic' ),
				'comment_count' => esc_html__( 'Comment Count', 'tourfic' ),
				'menu_order'    => esc_html__( 'Menu Order', 'tourfic' )
			],
            'default' => 'date',
        ]);

        $this->add_control('order', [
			'label'   => esc_html__( 'Order', 'tourfic' ),
			'type'    => Controls_Manager::CHOOSE,
			'options' => [
				'asc' => [
					'title' => esc_html__( 'Ascending', 'tourfic' ),
					'icon'  => 'fas fa-sort-amount-up-alt',
				],
				'desc' => [
					'title' => esc_html__( 'Descending', 'tourfic' ),
					'icon'  => 'fas fa-sort-amount-down',
				],
			],
			'default' => 'desc',
			'toggle'  => false,
		]);

		// $taxonomies = get_taxonomies([], 'objects');
		// foreach ($taxonomies as $taxonomy => $object) {
        //     if (!isset($object->object_type[0]) || !in_array($object->object_type[0], ['tf_hotel', 'tf_tours', 'tf_apartment', 'tf_carrental'])) {
        //         continue;
        //     }

		// 	// Get terms for this taxonomy
		// 	$terms = get_terms([
		// 		'taxonomy' => $taxonomy,
		// 		'hide_empty' => false,
		// 	]);
	
		// 	$taxonomy_label = $object ? $object->labels->name : esc_html__('Terms', 'tourfic');
		// 	$term_options = [
		// 		'all' => sprintf(esc_html__('All %s', 'tourfic'), $taxonomy_label)
		// 	];
		// 	if (!is_wp_error($terms)) {
		// 		foreach ($terms as $term) {
		// 			$term_options[$term->term_id] = $term->name;
		// 		}
		// 	}

        //     $this->add_control(
        //         $taxonomy . '_ids',
        //         [
        //             'label' => $object->label,
        //             'type' => Controls_Manager::SELECT2,
        //             'label_block' => true,
        //             'multiple' => true,
		// 			'options' => $term_options,
        //             'condition' => [
        //                 'service' => $object->object_type,
        //             ],
		// 			'description' => sprintf(esc_html__('Leave as "All %s" to include all items', 'tourfic'), $taxonomy_label)
        //         ]
        //     );
        // }
		
		$this->add_control('show_image',[
			'label' => esc_html__('Show Image', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
		]);

		$this->add_group_control(Group_Control_Image_Size::get_type(), [
			'name' => 'image',
			'exclude' => ['custom'],
			'default' => 'large',
			'condition' => [
				'show_image' => 'yes',
			],
		]);

        $this->add_control('show_fallback_img',[
			'label' => esc_html__('Default Image', 'tourfic'),
			'description' => esc_html__('Default image will be used if the post does not have a featured image.', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => '',
			'condition' => [
				'show_image' => 'yes',
			],
		]);

		$this->add_control('fallback_img',[
			'label'             => esc_html__( 'Image', 'tourfic' ),
			'type'              => Controls_Manager::MEDIA,
			'condition'         => [
				'show_fallback_img'    => 'yes',
				'show_image' => 'yes',
			],
			'ai' => [
				'active' => false,
			],
		]);

		$this->add_control('gallery', [
			'label' => esc_html__('Gallery', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-2'],
                'tf_tours' => ['design-2'],
                'tf_apartment' => ['design-1',],
            ], [
				'show_image' => 'yes',
			]),
		]);

		$this->add_control('tour_infos', [
			'label' => esc_html__('Tour Information', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
			'conditions' => $this->tf_display_conditionally([
                'tf_tours' => ['design-2', 'design-3'],
            ]),
		]);

		$this->add_control('featured_badge', [
			'label' => esc_html__('Featured Badge', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
			'condition'         => [
				'show_image' => 'yes',
				'service!' => 'tf_carrental',
			],
		]);

		$this->add_control('discount_tag', [
			'label' => esc_html__('Discount Tag', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-2', 'design-3'],
                'tf_tours' => ['design-1', 'design-2', 'design-3'],
                'tf_apartment' => ['design-1', 'design-2'],
            ],[
				'show_image' => 'yes',
			]),
		]);

		$this->add_control('promotional_tags', [
			'label' => esc_html__('Promotional Tags', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
			'condition'         => [
				'show_image' => 'yes',
			],
		]);

		$this->add_control('show_title',[
			'label' => esc_html__('Show Title', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
		]);

		$this->add_control('title_length',[
			'label' => esc_html__('Title Length', 'tourfic'),
			'type' => Controls_Manager::NUMBER,
			'default' => 55,
            'min' => 1,
            'max' => 100,
            'step' => 1,
			'condition' => [
				'show_title' => 'yes',
			],
		]);

		$this->add_control('show_excerpt',[
			'label' => esc_html__('Show Excerpt', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'default'],
                'tf_tours' => ['design-1', 'default'],
                'tf_apartment' => ['default'],
            ]),
		]);

		$this->add_control('excerpt_length',[
			'label' => esc_html__('Excerpt Characters', 'tourfic'),
			'type' => Controls_Manager::NUMBER,
			'default' => 100,
			'min' => 1,
            'max' => 400,
            'step' => 1,
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'default'],
                'tf_tours' => ['design-1', 'default'],
                'tf_apartment' => ['default'],
            ],[
				'show_excerpt' => 'yes',
			]),
		]);

	    $this->add_control('show_location',[
			'label' => esc_html__('Show Location', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
			'condition' => [
				'service!' => 'tf_carrental',
			],
		]);

		$this->add_control('location_icon',[
			'label' => esc_html__('Location Icon', 'tourfic'),
			'default' => [
				'value' => 'fas fa-map-marker-alt',
				'library' => 'fa-solid',
			],
			'label_block' => true,
			'type' => Controls_Manager::ICONS,
			'fa4compatibility' => 'location_icon_comp',
			'condition' => [
				'show_location' => 'yes',
				'service!' => 'tf_carrental',
			],
		]);

		$this->add_control('location_length',[
			'label' => esc_html__('Location Characters', 'tourfic'),
			'type' => Controls_Manager::NUMBER,
			'default' => 120,
			'min' => 1,
            'max' => 200,
            'step' => 1,
			'condition' => [
				'show_location' => 'yes',
				'service!' => 'tf_carrental',
			],
		]);

		$this->add_control('show_features',[
			'label' => esc_html__('Show Features', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-2', 'design-3', 'default'],
                'tf_tours' => ['design-2', 'design-3', 'default'],
                'tf_apartment' => ['design-1', 'design-2', 'default'],
            ]),
		]);

		$this->add_control('features_count',[
			'label' => esc_html__('Features Count', 'tourfic'),
			'type' => Controls_Manager::NUMBER,
			'default' => 4,
            'min' => 1,
            'max' => 10,
            'step' => 1,
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-2', 'design-3', 'default'],
                'tf_tours' => ['design-2', 'design-3', 'default'],
                'tf_apartment' => ['design-1', 'design-2', 'default'],
            ],[
				'show_features' => 'yes',
			]),
		]);

		$this->add_control('show_review',[
			'label' => esc_html__('Show Review', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
		]);

		$this->add_control('show_price',[
			'label' => esc_html__('Show Price', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
		]);

		//car infos select2
		$this->add_control('car_infos', [
			'label' => esc_html__('Car Information', 'tourfic'),
			'type' => Controls_Manager::SELECT2,
			'label_block' => true,
			'multiple' => true,
			'options' => [
				'mileage' => esc_html__('Mileage', 'tourfic'),
				'fuel_type' => esc_html__('Fuel Type', 'tourfic'),
				'engine_year' => esc_html__('Engine Year', 'tourfic'),
				'transmission_type' => esc_html__('Transmission Type', 'tourfic'),
				'passenger_capacity' => esc_html__('Passenger Capacity', 'tourfic'),
				'luggage_capacity' => esc_html__('Luggage Capacity', 'tourfic'),
			],
			'default' => ['mileage', 'fuel_type', 'engine_year', 'transmission_type', 'luggage_capacity', 'passenger_capacity'],
			'condition' => [
				'service' => 'tf_carrental',
			],
		]);

		$this->add_control('show_view_details', [
			'label' => esc_html__('Show View Details', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
		]);

        $this->add_control('view_details_text',[
			'label' => esc_html__('View Details Text', 'tourfic'),
			'type' => Controls_Manager::TEXT,
			'dynamic'     => [ 'active' => true ],
			'label_block' => false,
			'default' => esc_html__('View Details', 'tourfic'),
			'condition' => [
				'show_view_details' => 'yes',
			],
		]);

        $this->end_controls_section();
    }

	protected function tf_pagination_controls(){
        $this->start_controls_section('tf_pagination_section', [
			'label' => esc_html__('Pagination', 'tourfic'),
			'tab' => Controls_Manager::TAB_CONTENT,
		]);

        $this->add_control( 'show_pagination', [
			'label' => esc_html__('Show pagination', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
		]);

        $this->add_control( 'pagination_prev_label', [
			'label' => esc_html__('Previous Label', 'tourfic'),
			'default' => esc_html__('<< Previous', 'tourfic'),
			'condition' => [
				'show_pagination' => 'yes',
			]
		]);

        $this->add_control( 'pagination_next_label', [
			'label' => esc_html__('Next Label', 'tourfic'),
			'default' => esc_html__('Next >>', 'tourfic'),
			'condition' => [
				'show_pagination' => 'yes',
			]
		]);

        $this->end_controls_section();
    }

	protected function tf_general_style_controls() {
		$this->start_controls_section( 'listing_style_general', [
			'label' => esc_html__( 'General', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		//result
		$this->add_control( 'result_style_toggle', [
			'label'        => esc_html__( 'Search Result', 'tourfic' ),
			'type'         => Controls_Manager::POPOVER_TOGGLE,
			'label_off'    => esc_html__( 'Controls', 'tourfic' ),
			'label_on'     => esc_html__( 'Custom', 'tourfic' ),
			'return_value' => 'yes',
			'condition' => [
				'show_total_result' => 'yes',
			],
		] );

		$this->start_popover();
		$this->add_control( 'tf_result_color', [
			'label'     => esc_html__( 'Text Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-archive-head .tf-search-result" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-available-rooms-head>span.tf-total-results" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-top .tf-total-results" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-result-counter-info" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-total-result-bar" => 'color: {{VALUE}};',
			],
			'condition'  => [
				'result_style_toggle' => 'yes',
				'show_total_result' => 'yes',
			],
		] );
		$this->add_responsive_control( "result_margin", [
			'label'      => esc_html__( 'Margin', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-archive-head .tf-search-result" => $this->tf_apply_dim( 'margin' ),
				"{{WRAPPER}} .tf-available-rooms-head>span.tf-total-results" => $this->tf_apply_dim( 'margin' ),
				"{{WRAPPER}} .tf-archive-top .tf-total-results" => $this->tf_apply_dim( 'margin' ),
				"{{WRAPPER}} .tf-action-top .tf-result-counter-info" => $this->tf_apply_dim( 'margin' ),
				"{{WRAPPER}} .tf-archive-header .tf-total-result-bar" => $this->tf_apply_dim( 'margin' ),
			],
			'condition'  => [
				'result_style_toggle' => 'yes',
				'show_total_result' => 'yes',
			],
		] );
		$this->end_popover();

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Result Typography', 'tourfic' ),
			'name'     => "tf_result_typography",
			'selector' => "{{WRAPPER}} .tf-archive-head .tf-search-result, {{WRAPPER}} .tf-available-rooms-head>span.tf-total-results, {{WRAPPER}} .tf-archive-top .tf-total-results, {{WRAPPER}} .tf-action-top .tf-result-counter-info, {{WRAPPER}} .tf-archive-header .tf-total-result-bar",
			'separator' => 'after',
			'condition' => [
				'show_total_result' => 'yes',
			],
		]);

		//sorting
		$this->add_control( 'sorting_style_toggle', [
			'label'        => esc_html__( 'Sorting', 'tourfic' ),
			'type'         => Controls_Manager::POPOVER_TOGGLE,
			'label_off'    => esc_html__( 'Controls', 'tourfic' ),
			'label_on'     => esc_html__( 'Custom', 'tourfic' ),
			'return_value' => 'yes',
			'condition' => [
				'show_sorting' => 'yes',
				'service!' => 'tf_carrental',
			],
		] );

		$this->start_popover();
		$this->add_control( 'tf_sorting_color', [
			'label'     => esc_html__( 'Text Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-sorting-selection-warper form select#tf-orderby" => 'color: {{VALUE}};',
			],
			'condition'  => [
				'sorting_style_toggle' => 'yes',
				'show_sorting' => 'yes',
			],
		]);
		$this->add_responsive_control( "sorting_margin", [
			'label'      => esc_html__( 'Margin', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-sorting-selection-warper form select#tf-orderby" => $this->tf_apply_dim( 'margin' ),
			],
			'condition'  => [
				'sorting_style_toggle' => 'yes',
				'show_sorting' => 'yes',
			],
		]);
		$this->add_responsive_control( "sorting_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-sorting-selection-warper form select#tf-orderby" => $this->tf_apply_dim( 'padding', true ),
			],
			'condition'  => [
				'sorting_style_toggle' => 'yes',
				'show_sorting' => 'yes',
			],
		] );
		$this->add_control( 'tf_sorting_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-sorting-selection-warper form select#tf-orderby" => 'background-color: {{VALUE}} !important;',
			],
			'condition'  => [
				'sorting_style_toggle' => 'yes',
				'show_sorting' => 'yes',
			],
		]);
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "sorting_border",
			'selector' => "{{WRAPPER}} .tf-sorting-selection-warper form select#tf-orderby, 
							{{WRAPPER}} .tf-action-top .tf-list-grid .tf-archive-ordering #tf-orderby",
		] );
		$this->add_control( "sorting_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-sorting-selection-warper form select#tf-orderby" => $this->tf_apply_dim( 'border-radius', true ),
			],
			'condition' => [
				'show_sorting' => 'yes',
			],
		] );
		$this->end_popover();

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Sorting Typography', 'tourfic' ),
			'name'     => "tf_sorting_typography",
			'selector' => "{{WRAPPER}} .tf-sorting-selection-warper form select#tf-orderby",
			'condition' => [
				'show_sorting' => 'yes',
				'service!' => 'tf_carrental',
			],
		]);

		$this->end_controls_section();
	}

	protected function tf_toggle_btn_style_controls() {
		$this->start_controls_section( 'toggle_btn_style', [
			'label' => esc_html__( 'Layout Toggle Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-3', 'default'],
                'tf_tours' => ['design-1', 'design-3', 'default'],
                'tf_apartment' => ['design-2', 'default'],
                'tf_carrental' => ['design-1'],
            ],[
				'listing_layout_toggle' => 'yes',
			]),
		] );
		
		$this->add_responsive_control( "toggle_btn_width", [
			'label'      => esc_html__( 'Button width', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'em',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				],
				'em'  => [
					'min' => 0,
					'max' => 50,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-archive-head .tf-icon" => 'width: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item" => 'width: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view" => 'width: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li" => 'width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( "toggle_btn_height", [
			'label'      => esc_html__( 'Button Height', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'em',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				],
				'em'  => [
					'min' => 0,
					'max' => 50,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-archive-head .tf-icon" => 'height: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item" => 'height: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view" => 'height: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li" => 'height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->start_controls_tabs( "tabs_toggle_icon_style" );
		/*-----Button NORMAL state------ */
		$this->start_controls_tab( "tab_toggle_icon_normal", [
			'label' => esc_html__( 'Normal', 'tourfic' ),
		] );
		$this->add_control( 'tf_toggle_icon_color', [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-archive-head .tf-icon i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-head .tf-icon svg path, {{WRAPPER}} .tf-archive-head .tf-icon svg rect" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item svg path" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view svg path" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li svg path" => 'fill: {{VALUE}};',
			],
		] );
		$this->add_control( 'toggle_icon_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-archive-head .tf-icon" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li" => 'background-color: {{VALUE}};',
			],
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "toggle_icon_border",
			'selector' => "{{WRAPPER}} .tf-archive-head .tf-icon, 
							{{WRAPPER}} .tf-archive-view li.tf-archive-view-item, 
							{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view, 
							{{WRAPPER}} .tf-archive-header .tf-archive-view ul li",
		] );
		$this->add_control( "toggle_icon_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-archive-head .tf-icon" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li" => $this->tf_apply_dim( 'border-radius' ),
			],
		] );
		$this->end_controls_tab();

		/*-----Button HOVER state------ */
		$this->start_controls_tab( "tab_toggle_icon_hover", [
			'label' => esc_html__( 'Active/Hover', 'tourfic' ),
		] );
		$this->add_control( "toggle_icon_color_hover", [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-archive-head .tf-icon.active i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-head .tf-icon.active svg path, {{WRAPPER}} .tf-archive-head .tf-icon.active svg rect" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-head .tf-icon:hover i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-head .tf-icon:hover svg path, {{WRAPPER}} .tf-archive-head .tf-icon:hover svg rect" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item.active i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item.active svg path" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item:hover i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item:hover svg path" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view.active i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view.active svg path" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view:hover i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view:hover svg path" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li.active i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li.active svg path" => 'fill: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li:hover i" => 'color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li:hover svg path" => 'fill: {{VALUE}};',
			],
		] );
		$this->add_control( 'toggle_icon_bg_color_hover', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-archive-head .tf-icon:hover" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-head .tf-icon.active" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item:hover" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item.active" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view:hover" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view.active" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li:hover" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li.active" => 'background-color: {{VALUE}};',
			],
		] );
		$this->add_control( 'toggle_icon_border_color_hover', [
			'label'     => esc_html__( 'Border Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-archive-head .tf-icon:hover" => 'border-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-head .tf-icon.active" => 'border-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item:hover" => 'border-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-view li.tf-archive-view-item.active" => 'border-color: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view:hover" => 'border-color: {{VALUE}};',
				"{{WRAPPER}} .tf-action-top .tf-list-grid a.change-view.active" => 'border-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li:hover" => 'border-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-header .tf-archive-view ul li.active" => 'border-color: {{VALUE}};',
			],
		] );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		/*-----ends Button tabs--------*/

		$this->add_control( 'tf_filter_btn_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => esc_html__( 'Filter Button', 'tourfic' ),
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-3'],
                'tf_tours' => ['design-3'],
                'tf_apartment' => ['design-2'],
            ]),
		] );

		$this->add_responsive_control( "filter_btn_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-archive-view li.tf-archive-filter-btn" => $this->tf_apply_dim( 'padding' ),
			],
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-3'],
                'tf_tours' => ['design-3'],
                'tf_apartment' => ['design-2'],
            ]),
		] );

		$this->start_controls_tabs( "tabs_filter_btn_style" );
		/*-----Button NORMAL state------ */
		$this->start_controls_tab( "tab_filter_btn_normal", [
			'label' => esc_html__( 'Normal', 'tourfic' ),
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-3'],
                'tf_tours' => ['design-3'],
                'tf_apartment' => ['design-2'],
            ]),
		] );
		$this->add_control( 'tf_filter_btn_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-archive-view li.tf-archive-filter-btn" => 'color: {{VALUE}};',
			],
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-3'],
                'tf_tours' => ['design-3'],
                'tf_apartment' => ['design-2'],
            ]),
		] );
		$this->add_control( 'filter_btn_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-archive-view li.tf-archive-filter-btn" => 'background-color: {{VALUE}};',
			],
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-3'],
                'tf_tours' => ['design-3'],
                'tf_apartment' => ['design-2'],
            ]),
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "filter_btn_border",
			'selector' => "{{WRAPPER}} .tf-archive-view li.tf-archive-filter-btn",
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-3'],
                'tf_tours' => ['design-3'],
                'tf_apartment' => ['design-2'],
            ]),
		] );
		$this->add_control( "filter_btn_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-archive-view li.tf-archive-filter-btn" => $this->tf_apply_dim( 'border-radius' ),
			],
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-3'],
                'tf_tours' => ['design-3'],
                'tf_apartment' => ['design-2'],
            ]),
		] );
		$this->end_controls_tab();

		/*-----Button HOVER state------ */
		$this->start_controls_tab( "tab_filter_btn_hover", [
			'label' => esc_html__( 'Hover', 'tourfic' ),
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-3'],
                'tf_tours' => ['design-3'],
                'tf_apartment' => ['design-2'],
            ]),
		] );
		$this->add_control( "filter_btn_color_hover", [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-archive-view li.tf-archive-filter-btn:hover" => 'color: {{VALUE}};',
			],
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-3'],
                'tf_tours' => ['design-3'],
                'tf_apartment' => ['design-2'],
            ]),
		] );
		$this->add_control( 'filter_btn_bg_color_hover', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-archive-view li.tf-archive-filter-btn:hover" => 'background-color: {{VALUE}};',
			],
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-3'],
                'tf_tours' => ['design-3'],
                'tf_apartment' => ['design-2'],
            ]),
		] );
		$this->add_control( 'filter_btn_border_color_hover', [
			'label'     => esc_html__( 'Border Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-archive-view li.tf-archive-filter-btn:hover" => 'border-color: {{VALUE}};',
			],
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-3'],
                'tf_tours' => ['design-3'],
                'tf_apartment' => ['design-2'],
            ]),
		] );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		/*-----ends Button tabs--------*/

		$this->end_controls_section();
	}

	protected function tf_card_style_controls() {
		$this->start_controls_section( 'card_style', [
			'label' => esc_html__( 'Card Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( "card_gap", [
			'label'      => esc_html__( 'Card Gap', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'em',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
				'em'  => [
					'min' => 0,
					'max' => 20,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-search-results-list .tf-item-cards" => 'gap: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-available-rooms-wrapper .tf-available-rooms" => 'gap: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-archive-details .tf-archive-hotels" => 'gap: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .single-tour-wrap" => 'margin-bottom: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-car-archive-result .tf-car-result" => 'gap: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control( "card_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-search-results-list .tf-item-card" => $this->tf_apply_dim( 'padding' ),
				"{{WRAPPER}} .tf-archive-available-rooms .tf-available-room" => $this->tf_apply_dim( 'padding' ),
				"{{WRAPPER}} .tf-archive-details .tf-archive-hotel" => $this->tf_apply_dim( 'padding' ),
				"{{WRAPPER}} .single-tour-wrap .single-tour-inner" => $this->tf_apply_dim( 'padding' ),
				"{{WRAPPER}} .tf-car-result .tf-single-car-view" => $this->tf_apply_dim( 'padding' ),
			],
		] );

		/*-----start Card tabs--------*/
		$this->start_controls_tabs( "tabs_card_style" );
		/*-----Card Normal State------ */
		$this->start_controls_tab( "tab_card_normal", [
			'label' => esc_html__( 'Normal', 'tourfic' ),
		] );
		$this->add_control( 'card_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-search-results-list .tf-item-card" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-available-rooms .tf-available-room" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-details .tf-archive-hotel" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .single-tour-wrap .single-tour-inner" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-car-result .tf-single-car-view" => 'background-color: {{VALUE}};',
			],
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "card_border",
			'selector' => "{{WRAPPER}} .tf-search-results-list .tf-item-card, 
							{{WRAPPER}} .tf-archive-available-rooms .tf-available-room, 
							{{WRAPPER}} .tf-archive-details .tf-archive-hotel, 
							{{WRAPPER}} .single-tour-wrap .single-tour-inner, 
							{{WRAPPER}} .tf-car-result .tf-single-car-view",
		] );
		$this->add_control( "card_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-search-results-list .tf-item-card" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-archive-available-rooms .tf-available-room" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-archive-details .tf-archive-hotel" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .single-tour-wrap .single-tour-inner" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-car-result .tf-single-car-view" => $this->tf_apply_dim( 'border-radius' ),
			],
		] );
		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name' => 'card_shadow',
			'selector' => '{{WRAPPER}} .tf-search-results-list .tf-item-card, 
							{{WRAPPER}} .tf-archive-available-rooms .tf-available-room, 
							{{WRAPPER}} .tf-archive-details .tf-archive-hotel, 
							{{WRAPPER}} .single-tour-wrap .single-tour-inner, 
							{{WRAPPER}} .tf-car-result .tf-single-car-view',
		]);
		$this->end_controls_tab();

		/*-----Card Hover State------ */
		$this->start_controls_tab( "tab_card_hover", [
			'label' => esc_html__( 'Hover', 'tourfic' ),
		] );
		$this->add_control( 'card_bg_color_hover', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-search-results-list .tf-item-card:hover" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-available-rooms .tf-available-room:hover" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-details .tf-archive-hotel:hover" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .single-tour-wrap .single-tour-inner:hover" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-car-result .tf-single-car-view:hover" => 'background-color: {{VALUE}};',
			],
		] );
		$this->add_control( 'card_border_hover', [
			'label'     => esc_html__( 'Border Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-search-results-list .tf-item-card:hover" => 'border-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-available-rooms .tf-available-room:hover" => 'border-color: {{VALUE}};',
				"{{WRAPPER}} .tf-archive-details .tf-archive-hotel:hover" => 'border-color: {{VALUE}};',
				"{{WRAPPER}} .single-tour-wrap .single-tour-inner:hover" => 'border-color: {{VALUE}};',
				"{{WRAPPER}} .tf-car-result .tf-single-car-view:hover" => 'border-color: {{VALUE}};',
			],
		] );
		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name' => 'card_hover_shadow',
			'selector' => '{{WRAPPER}} .tf-search-results-list .tf-item-card:hover, 
							{{WRAPPER}} .tf-archive-available-rooms .tf-available-room:hover, 
							{{WRAPPER}} .tf-archive-details .tf-archive-hotel:hover, 
							{{WRAPPER}} .single-tour-wrap .single-tour-inner:hover, 
							{{WRAPPER}} .tf-car-result .tf-single-car-view:hover',
		]);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		/*-----ends Card tabs--------*/

		$this->add_control( 'tf_content_wrapper_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => esc_html__( 'Content Wrapper', 'tourfic' ),
		] );

		$this->add_responsive_control( "content_wrapper_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-item-card .tf-item-details" => $this->tf_apply_dim( 'padding' ),
				"{{WRAPPER}} .tf-archive-available-rooms .tf-available-room .tf-available-room-content" => $this->tf_apply_dim( 'padding' ),
				"{{WRAPPER}} .tf-archive-hotel .tf-archive-hotel-content" => $this->tf_apply_dim( 'padding' ),
				"{{WRAPPER}} .single-tour-wrap .single-tour-inner .tourfic-single-right" => $this->tf_apply_dim( 'padding' ),
				"{{WRAPPER}} .tf-single-car-view .tf-car-details" => $this->tf_apply_dim( 'padding' ),
			],
		] );

		$this->end_controls_section();
	}

	protected function tf_thumbnail_style_controls() {
		$this->start_controls_section( 'thumbnail_style', [
			'label' => esc_html__( 'Thumbnail Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_image' => 'yes',
			],
		] );
		
		$this->add_responsive_control('thumbnail_height',[
			'label'      => esc_html__('Thumbnail Height', 'tourfic'),
			'type'       => Controls_Manager::SLIDER,
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 600,
					'step' => 1,
				],
				'em' => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
				'%'  => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
			],
			'default'   => [
				'unit' => '%',
				'size' => 100,
			],
			'size_units' => ['px', 'em', '%'],
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-featured img' => 'height: {{SIZE}}{{UNIT}} !important; min-height: {{SIZE}}{{UNIT}} !important;', //design-1
				'{{WRAPPER}} .tf-available-room-gallery .tf-room-gallery img' => 'height: {{SIZE}}{{UNIT}} !important; min-height: {{SIZE}}{{UNIT}} !important;', //design-2
				'{{WRAPPER}} .tf-archive-hotel .tf-archive-hotel-thumb img' => 'height: {{SIZE}}{{UNIT}} !important; min-height: {{SIZE}}{{UNIT}} !important;', //design-3
				'{{WRAPPER}} .single-tour-inner .tourfic-single-left img' => 'height: {{SIZE}}{{UNIT}} !important; min-height: {{SIZE}}{{UNIT}} !important;', //default
				'{{WRAPPER}} .tf-single-car-view .tf-car-image img' => 'height: {{SIZE}}{{UNIT}} !important; min-height: {{SIZE}}{{UNIT}} !important;', //default
			],
		]);
		
		$this->add_responsive_control('thumbnail_width',[
			'label'      => esc_html__('Thumbnail Width', 'tourfic'),
			'type'       => Controls_Manager::SLIDER,
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 600,
					'step' => 1,
				],
				'em' => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
				'%'  => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
			],
			'default'   => [
				'unit' => '%',
				'size' => 100,
			],
			'size_units' => ['px', 'em', '%'],
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-featured img' => 'width: {{SIZE}}{{UNIT}} !important;', //design-1
				'{{WRAPPER}} .tf-available-room-gallery .tf-room-gallery img' => 'width: {{SIZE}}{{UNIT}} !important;', //design-2
				'{{WRAPPER}} .tf-archive-hotel .tf-archive-hotel-thumb img' => 'width: {{SIZE}}{{UNIT}} !important;', //design-3
				'{{WRAPPER}} .single-tour-inner .tourfic-single-left img' => 'width: {{SIZE}}{{UNIT}} !important;', //default
				'{{WRAPPER}} .tf-single-car-view .tf-car-image img' => 'width: {{SIZE}}{{UNIT}} !important;', //default
			],
		]);

		$this->add_control( "thumbnail_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-featured img' => $this->tf_apply_dim( 'border-radius', true ), //design-1
				'{{WRAPPER}} .tf-available-room-gallery .tf-room-gallery img' => $this->tf_apply_dim( 'border-radius', true ), //design-2
				'{{WRAPPER}} .tf-archive-hotel .tf-archive-hotel-thumb img' => $this->tf_apply_dim( 'border-radius', true ), //design-3
				'{{WRAPPER}} .single-tour-inner .tourfic-single-left img' => $this->tf_apply_dim( 'border-radius', true ), //default
				'{{WRAPPER}} .tf-single-car-view .tf-car-image img' => $this->tf_apply_dim( 'border-radius', true ), //default
			],
		] );

		$this->end_controls_section();
	}

	protected function tf_featured_badge_style_controls() {
		$this->start_controls_section( 'featured_badge_style', [
			'label' => esc_html__( 'Featured Badge Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_image' => 'yes',
				'featured_badge' => 'yes',
                'service!' => 'tf_carrental',
			],
		] );

		$this->add_control( 'tf_badge_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-featured .tf-features-box .tf-feature' => 'color: {{VALUE}};', //design-1
				'{{WRAPPER}} .tf-available-room .tf-available-room-gallery .tf-available-labels span.tf-available-labels-featured' => 'color: {{VALUE}};', //design-2
				'{{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item-featured' => 'color: {{VALUE}};', //design-3
				'{{WRAPPER}} .single-tour-inner .tf-featured-badge span' => 'color: {{VALUE}};', //default
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_badge_typography",
			'selector' => "{{WRAPPER}} .tf-item-card .tf-item-featured .tf-features-box .tf-feature, {{WRAPPER}} .tf-available-room .tf-available-room-gallery .tf-available-labels span.tf-available-labels-featured, {{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item-featured, {{WRAPPER}} .single-tour-inner .tf-featured-badge span",
		]);

		$this->add_responsive_control( "featured_badge_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-featured .tf-features-box .tf-feature' => $this->tf_apply_dim( 'padding' ), //design-1
				'{{WRAPPER}} .tf-available-room-gallery .tf-available-labels>span' => $this->tf_apply_dim( 'padding', true ), //design-2
				'{{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item-featured' => $this->tf_apply_dim( 'padding', true ), //design-3
				'{{WRAPPER}} .single-tour-inner .tf-featured-badge span' => $this->tf_apply_dim( 'padding', true ), //default
			],
		]);

		$this->add_control( 'featured_badge_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-featured .tf-features-box .tf-feature' => 'background-color: {{VALUE}};', //design-1
				'{{WRAPPER}} .tf-available-room .tf-available-room-gallery .tf-available-labels span.tf-available-labels-featured' => 'background-color: {{VALUE}};', //design-2
				'{{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item-featured' => 'background-color: {{VALUE}};', //design-3
				'{{WRAPPER}} .single-tour-inner .tf-featured-badge span' => 'background-color: {{VALUE}};', //default
			],
		]);

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "featured_badge_border",
			'selector' => "{{WRAPPER}} .tf-item-card .tf-item-featured .tf-features-box .tf-feature, {{WRAPPER}} .tf-available-room-gallery .tf-available-labels>span, {{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item-featured, {{WRAPPER}} .single-tour-inner .tf-featured-badge span",
		]);

		$this->add_control( "featured_badge_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-featured .tf-features-box .tf-feature' => $this->tf_apply_dim( 'border-radius' ), //design-1
				'{{WRAPPER}} .tf-available-room-gallery .tf-available-labels>span' => $this->tf_apply_dim( 'border-radius' ), //design-2
				'{{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item-featured' => $this->tf_apply_dim( 'border-radius' ), //design-3
				'{{WRAPPER}} .single-tour-inner .tf-featured-badge span' => $this->tf_apply_dim( 'border-radius' ), //default
			],
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-2', 'design-3'],
                'tf_tours' => ['design-1', 'design-2', 'design-3'],
                'tf_apartment' => ['design-1', 'design-2'],
            ]),
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name' => 'featured_badge_shadow',
			'selector' => "{{WRAPPER}} .tf-item-card .tf-item-featured .tf-features-box .tf-feature, {{WRAPPER}} .tf-available-room-gallery .tf-available-labels>span, {{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item-featured, {{WRAPPER}} .single-tour-inner .tf-featured-badge span",
		]);

		$this->end_controls_section();
	}

	protected function tf_discount_tag_style_controls() {
		$this->start_controls_section( 'discount_tag_style', [
			'label' => esc_html__( 'Discount Tag Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-2', 'design-3'],
                'tf_tours' => ['design-1', 'design-2', 'design-3'],
                'tf_apartment' => ['design-1', 'design-2'],
            ],[
				'discount_tag' => 'yes',
			]),
		] );

		$this->add_control( 'tf_discount_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-featured .tf-features-box .tf-discount' => 'color: {{VALUE}};', //design-1
				'{{WRAPPER}} .tf-available-rooms-wrapper .tf-available-room-off' => 'color: {{VALUE}};', //design-2
				'{{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item.tf-tag-item-discount' => 'color: {{VALUE}};', //design-3
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_discount_typography",
			'selector' => "{{WRAPPER}} .tf-item-card .tf-item-featured .tf-features-box .tf-discount, {{WRAPPER}} .tf-available-rooms-wrapper .tf-available-room-off, {{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item.tf-tag-item-discount",
		]);

		$this->add_responsive_control( "discount_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-featured .tf-features-box .tf-discount' => $this->tf_apply_dim( 'padding' ), //design-1
				'{{WRAPPER}} .tf-available-rooms-wrapper .tf-available-room-off' => $this->tf_apply_dim( 'padding', true ), //design-2
				'{{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item.tf-tag-item-discount' => $this->tf_apply_dim( 'padding', true ), //design-3
			],
		]);

		$this->add_control( 'discount_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-featured .tf-features-box .tf-discount' => 'background-color: {{VALUE}};', //design-1
				'{{WRAPPER}} .tf-available-rooms-wrapper .tf-available-room-off' => 'background-color: {{VALUE}};', //design-2
				'{{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item.tf-tag-item-discount' => 'background-color: {{VALUE}};', //design-3
			],
		]);

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "discount_border",
			'selector' => "{{WRAPPER}} .tf-item-card .tf-item-featured .tf-features-box .tf-discount, {{WRAPPER}} .tf-available-rooms-wrapper .tf-available-room-off, {{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item.tf-tag-item-discount",
		]);

		$this->add_control( "discount_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-featured .tf-features-box .tf-discount' => $this->tf_apply_dim( 'border-radius' ), //design-1
				'{{WRAPPER}} .tf-available-rooms-wrapper .tf-available-room-off' => $this->tf_apply_dim( 'border-radius' ), //design-2
				'{{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item.tf-tag-item-discount' => $this->tf_apply_dim( 'border-radius' ), //design-3
			],
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name' => 'discount_shadow',
			'selector' => "{{WRAPPER}} .tf-item-card .tf-item-featured .tf-features-box .tf-discount, {{WRAPPER}} .tf-available-rooms-wrapper .tf-available-room-off, {{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item.tf-tag-item-discount",
		]);

		$this->end_controls_section();
	}

	protected function tf_promotional_tag_style_controls() {
		$this->start_controls_section( 'promotional_tag_style', [
			'label' => esc_html__( 'Promotional Tag Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_image' => 'yes',
				'promotional_tags' => 'yes',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_promotional_tag_typography",
			'selector' => "{{WRAPPER}} .tf-item-featured .tf-tag-items .tf-multiple-tag-item, 
							{{WRAPPER}} .tf-available-room-gallery .tf-available-labels>span.tf-multiple-tag, 
							{{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item.tf-multiple-tag, 
							{{WRAPPER}} .tourfic-single-left .default-tags-container .default-single-tag, 
							{{WRAPPER}} .tf-car-image .tf-other-infos .tf-tags-box ul li",
		]);

		$this->add_responsive_control( "promotional_tag_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-item-featured .tf-tag-items .tf-multiple-tag-item' => $this->tf_apply_dim( 'padding' ), //design-1
				'{{WRAPPER}} .tf-available-room-gallery .tf-available-labels>span.tf-multiple-tag' => $this->tf_apply_dim( 'padding', true ), //design-2
				'{{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item.tf-multiple-tag' => $this->tf_apply_dim( 'padding', true ), //design-3
				'{{WRAPPER}} .tourfic-single-left .default-tags-container .default-single-tag' => $this->tf_apply_dim( 'padding', true ), //default
				'{{WRAPPER}} .tf-car-image .tf-other-infos .tf-tags-box ul li' => $this->tf_apply_dim( 'padding', true ), //default
			],
		]);

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "promotional_tag_border",
			'selector' => "{{WRAPPER}} .tf-item-featured .tf-tag-items .tf-multiple-tag-item, 
							{{WRAPPER}} .tf-available-room-gallery .tf-available-labels>span.tf-multiple-tag, 
							{{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item.tf-multiple-tag, 
							{{WRAPPER}} .tourfic-single-left .default-tags-container .default-single-tag, 
							{{WRAPPER}} .tf-car-image .tf-other-infos .tf-tags-box ul li",
		]);

		$this->add_control( "promotional_tag_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-item-featured .tf-tag-items .tf-multiple-tag-item' => $this->tf_apply_dim( 'border-radius' ), //design-1
				'{{WRAPPER}} .tf-available-room-gallery .tf-available-labels>span.tf-multiple-tag' => $this->tf_apply_dim( 'border-radius' ), //design-2
				'{{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item.tf-multiple-tag' => $this->tf_apply_dim( 'border-radius' ), //design-3
				'{{WRAPPER}} .tourfic-single-left .default-tags-container .default-single-tag' => $this->tf_apply_dim( 'border-radius' ), //default
				'{{WRAPPER}} .tf-single-car-view .tf-car-image .tf-other-infos .tf-tags-box ul li' => $this->tf_apply_dim( 'border-radius' ), //default
			],
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name' => 'promotional_tag_shadow',
			'selector' => "{{WRAPPER}} .tf-item-featured .tf-tag-items .tf-multiple-tag-item, 
							{{WRAPPER}} .tf-available-room-gallery .tf-available-labels>span.tf-multiple-tag, 
							{{WRAPPER}} .tf-archive-hotel-thumb .tf-tag-items .tf-tag-item.tf-multiple-tag, 
							{{WRAPPER}} .tourfic-single-left .default-tags-container .default-single-tag, 
							{{WRAPPER}} .tf-car-image .tf-other-infos .tf-tags-box ul li",
		]);

		$this->end_controls_section();
	}

	protected function tf_title_style_controls() {
		$this->start_controls_section( 'title_style', [
			'label' => esc_html__( 'Title Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_title' => 'yes',
			],
		]);

		$this->add_control( 'tf_title_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-details .tf-title h2 a' => 'color: {{VALUE}};', //design-1
				'{{WRAPPER}} .tf-available-room .tf-available-room-content .tf-available-room-content-left h2' => 'color: {{VALUE}};', //design-2
				'{{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left .tf-section-title a' => 'color: {{VALUE}};', //design-3
				'{{WRAPPER}} .tf_item_main_block .tf-hotel__title-wrap .tourfic_hotel-title a' => 'color: {{VALUE}};', //default
				'{{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details h3 a' => 'color: {{VALUE}};', //default
			],
		]);

		$this->add_control( 'tf_title_hover_color', [
			'label'     => esc_html__( 'Hover Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-details .tf-title h2 a:hover' => 'color: {{VALUE}};', //design-1
				'{{WRAPPER}} .tf-available-room .tf-available-room-content .tf-available-room-content-left h2:hover' => 'color: {{VALUE}};', //design-2
				'{{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left .tf-section-title a:hover' => 'color: {{VALUE}};', //design-3
				'{{WRAPPER}} .tf_item_main_block .tf-hotel__title-wrap .tourfic_hotel-title a:hover' => 'color: {{VALUE}};', //default
				'{{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details h3 a:hover' => 'color: {{VALUE}};', //default
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_title_typography",
			'selector' => "{{WRAPPER}} .tf-item-card .tf-item-details .tf-title h2 a, 
							{{WRAPPER}} .tf-available-room-content .tf-available-room-content-left .tf-section-title-and-location .tf-section-title a, 
							{{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left .tf-section-title a, 
							{{WRAPPER}} .tf_item_main_block .tf-hotel__title-wrap .tourfic_hotel-title a, 
							{{WRAPPER}} .tf-car-details h3 a",
		]);

		$this->add_responsive_control( "title_margin", [
			'label'      => esc_html__( 'Margin', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-details .tf-title' => $this->tf_apply_dim( 'margin' ), //design-1
				'{{WRAPPER}} .tf-available-archive-hetels-wrapper .tf-archive-available-rooms .tf-available-room .tf-available-room-content .tf-available-room-content-left .tf-section-title-and-location .tf-section-title' => $this->tf_apply_dim( 'margin' ), //design-1
				'{{WRAPPER}} .tf-archive-hotel .tf-archive-hotel-content .tf-archive-hotel-content-left .tf-section-title' => $this->tf_apply_dim( 'margin' ), //design-1
				'{{WRAPPER}} .tf_item_main_block .tf-hotel__title-wrap .tourfic_hotel-title' => $this->tf_apply_dim( 'margin' ), //design-1
				'{{WRAPPER}} .tf-car-details h3 a' => $this->tf_apply_dim( 'margin' ), //design-1
			],
		]);

		$this->end_controls_section();
	}

	protected function tf_location_style_controls() {
		$this->start_controls_section( 'location_style', [
			'label' => esc_html__( 'Location Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_location' => 'yes',
                'service!' => 'tf_carrental',
			],
		]);

		$this->add_control( 'tf_location_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-details .tf-title-meta p' => 'color: {{VALUE}};', //design-1
				'{{WRAPPER}} .tf-available-room-content-left .tf-section-title-and-location .tf-title-location span' => 'color: {{VALUE}};', //design-2
				'{{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left .tf-title-location' => 'color: {{VALUE}};', //design-3
				'{{WRAPPER}} .tf_item_main_block .tf-map-link .tf-d-ib' => 'color: {{VALUE}};', //default
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_location_typography",
			'selector' => "{{WRAPPER}} .tf-item-card .tf-item-details .tf-title-meta p, {{WRAPPER}} .tf-available-room-content-left .tf-section-title-and-location .tf-title-location span, {{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left .tf-title-location, {{WRAPPER}} .tf_item_main_block .tf-map-link .tf-d-ib",
		]);

		$this->add_responsive_control( "location_margin", [
			'label'      => esc_html__( 'Margin', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-details .tf-title-meta' => $this->tf_apply_dim( 'margin' ), //design-1
				'{{WRAPPER}} .tf-archive-available-rooms .tf-available-room .tf-available-room-content .tf-available-room-content-left .tf-section-title-and-location .tf-title-location' => $this->tf_apply_dim( 'margin' ), //design-1
				'{{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left .tf-title-location' => $this->tf_apply_dim( 'margin' ), //design-1
				'{{WRAPPER}} .tf_item_main_block .tf-map-link .tf-d-ib' => $this->tf_apply_dim( 'margin' ), //design-1
			],
		]);

		$this->add_responsive_control( "tf_location_icon_size", [
			'label'      => esc_html__( 'Icon Size', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'rem',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-item-details .tf-title-meta i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-item-details .tf-title-meta svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-archive-available-rooms .tf-available-room .tf-available-room-content .tf-available-room-content-left .tf-section-title-and-location .tf-title-location i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-archive-available-rooms .tf-available-room .tf-available-room-content .tf-available-room-content-left .tf-section-title-and-location .tf-title-location svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left .tf-title-location i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left .tf-title-location svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf_item_main_block .tf-map-link .tf-d-ib i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf_item_main_block .tf-map-link .tf-d-ib svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_control( "tf_location_icon_color", [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-item-details .tf-title-meta i" => 'color: {{VALUE}}',
				"{{WRAPPER}} .tf-item-details .tf-title-meta svg path" => 'fill: {{VALUE}}',
				"{{WRAPPER}} .tf-archive-available-rooms .tf-available-room .tf-available-room-content .tf-available-room-content-left .tf-section-title-and-location .tf-title-location i" => 'color: {{VALUE}}',
				"{{WRAPPER}} .tf-archive-available-rooms .tf-available-room .tf-available-room-content .tf-available-room-content-left .tf-section-title-and-location .tf-title-location svg path" => 'fill: {{VALUE}}',
				"{{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left .tf-title-location i" => 'color: {{VALUE}}',
				"{{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left .tf-title-location svg path" => 'fill: {{VALUE}}',
				"{{WRAPPER}} .tf_item_main_block .tf-map-link .tf-d-ib i" => 'color: {{VALUE}}',
				"{{WRAPPER}} .tf_item_main_block .tf-map-link .tf-d-ib svg path" => 'fill: {{VALUE}}',
			],
		] );

		$this->end_controls_section();
	}

	protected function tf_review_style_controls() {
		$this->start_controls_section( 'review_style', [
			'label' => esc_html__( 'Review Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_review' => 'yes',
			],
		]);

		$this->add_control( 'tf_review_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-avarage-review' => 'color: {{VALUE}};', //design-1, design-3
				'{{WRAPPER}} .tf-available-rating-number' => 'color: {{VALUE}};', //design-2
				'{{WRAPPER}} .tf-archive-rating-wrapper .tf-archive-rating' => 'color: {{VALUE}};', //default
				'{{WRAPPER}} .tf-archive-hotel .tf-archive-hotel-content .tf-archive-hotel-content-left .tf-reviews .tf-avarage-review .tf-no-review-count' => 'color: {{VALUE}};', //design-3
				'{{WRAPPER}} .tf-single-car-view .tf-car-image .tf-other-infos .tf-reviews-box span' => 'color: {{VALUE}};', //design-3
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_review_typography",
			'selector' => "{{WRAPPER}} .tf-item-details .tf-reviews .tf-avarage-review, 
							{{WRAPPER}} .tf-available-rating-number, 
							{{WRAPPER}} .tf-archive-rating-wrapper .tf-archive-rating, 
							{{WRAPPER}} .tf-reviews .tf-avarage-review .tf-no-review-count, 
							{{WRAPPER}} .tf-archive-hotel-content-left .tf-reviews .tf-avarage-review, 
							{{WRAPPER}} .tf-single-car-view .tf-car-image .tf-other-infos .tf-reviews-box span",
		]);

		$this->add_responsive_control( "review_margin", [
			'label'      => esc_html__( 'Margin', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-details .tf-reviews' => $this->tf_apply_dim( 'margin' ), //design-1
				'{{WRAPPER}} .tf-available-room-gallery .tf-available-ratings' => $this->tf_apply_dim( 'margin' ), //design-1
				'{{WRAPPER}} .tf-archive-hotel-content-left .tf-reviews' => $this->tf_apply_dim( 'margin' ), //design-1
				'{{WRAPPER}} .tf-archive-rating-wrapper .tf-archive-rating' => $this->tf_apply_dim( 'margin' ), //design-1
				'{{WRAPPER}} .tf-single-car-view .tf-car-image .tf-other-infos .tf-reviews-box span' => $this->tf_apply_dim( 'margin' ), //design-1
			],
		]);

		$this->add_responsive_control( "tf_review_icon_size", [
			'label'      => esc_html__( 'Icon Size', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'rem',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-item-card .tf-item-details .tf-reviews i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-archive-hotel-content-left .tf-reviews .tf-review-items i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-available-room-gallery .tf-available-ratings i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-single-car-view .tf-car-image .tf-other-infos .tf-reviews-box span i" => 'font-size: {{SIZE}}{{UNIT}}',
			],
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-2', 'design-3'],
                'tf_tours' => ['design-1', 'design-2', 'design-3'],
                'tf_apartment' => ['design-1', 'design-2'],
                'tf_carrental' => ['design-1'],
            ]),
		] );

		$this->add_control( "tf_review_icon_color", [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-item-card .tf-item-details .tf-reviews i" => 'color: {{VALUE}}',
				"{{WRAPPER}} .tf-archive-hotel-content-left .tf-reviews .tf-review-items i" => 'color: {{VALUE}}',
				"{{WRAPPER}} .tf-archive-available-rooms .tf-available-room .tf-available-room-gallery .tf-available-ratings i" => 'color: {{VALUE}}',
				"{{WRAPPER}} .tf-single-car-view .tf-car-image .tf-other-infos .tf-reviews-box span i" => 'color: {{VALUE}}',
			],
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-2', 'design-3'],
                'tf_tours' => ['design-1', 'design-2', 'design-3'],
                'tf_apartment' => ['design-1', 'design-2'],
                'tf_carrental' => ['design-1'],
            ]),
		] );

		$this->add_control( "review_bg_color", [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-archive-rating-wrapper .tf-archive-rating" => 'background-color: {{VALUE}}',
				"{{WRAPPER}} .tf-archive-available-rooms .tf-available-room .tf-available-room-gallery .tf-available-ratings" => 'background-color: {{VALUE}}',
				"{{WRAPPER}} .tf-single-car-view .tf-car-image .tf-other-infos .tf-reviews-box span" => 'background-color: {{VALUE}}',
			],
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-2', 'default'],
                'tf_tours' => ['design-2', 'default'],
                'tf_apartment' => ['design-1', 'default'],
                'tf_carrental' => ['design-1'],
            ]),
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "review_border",
			'selector' => "{{WRAPPER}} .tf-available-room-gallery .tf-available-ratings, 
							{{WRAPPER}} .tf-archive-rating-wrapper .tf-archive-rating, 
							{{WRAPPER}} .tf-single-car-view .tf-car-image .tf-other-infos .tf-reviews-box span",
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-2', 'default'],
                'tf_tours' => ['design-2', 'default'],
                'tf_apartment' => ['design-1', 'default'],
                'tf_carrental' => ['design-1'],
            ]),
		]);

		$this->add_control( "review_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-available-room-gallery .tf-available-ratings' => $this->tf_apply_dim( 'border-radius' ), //design-2
				'{{WRAPPER}} .tf-archive-rating-wrapper .tf-archive-rating' => $this->tf_apply_dim( 'border-radius' ), //design-3
				'{{WRAPPER}} .tf-single-car-view .tf-car-image .tf-other-infos .tf-reviews-box span' => $this->tf_apply_dim( 'border-radius' ), //design-3
			],
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-2', 'default'],
                'tf_tours' => ['design-2', 'default'],
                'tf_apartment' => ['design-1', 'default'],
                'tf_carrental' => ['design-1'],
            ]),
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name' => 'review_shadow',
			'selector' => "{{WRAPPER}} .tf-available-room-gallery .tf-available-ratings, 
							{{WRAPPER}} .tf-archive-rating-wrapper .tf-archive-rating, 
							{{WRAPPER}} .tf-single-car-view .tf-car-image .tf-other-infos .tf-reviews-box span",
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-2', 'default'],
                'tf_tours' => ['design-2', 'default'],
                'tf_apartment' => ['design-1', 'default'],
                'tf_carrental' => ['design-1'],
            ]),
		]);

		$this->end_controls_section();
	}

	protected function tf_gallery_style_controls() {
		$this->start_controls_section( 'gallery_style', [
			'label' => esc_html__( 'Gallery Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-2'],
                'tf_tours' => ['design-2'],
                'tf_apartment' => ['design-1'],
            ],[
				'show_image' => 'yes',
				'gallery' => 'yes',
			]),
		]);

		$this->add_responsive_control( "tf_gallery_icon_size", [
			'label'      => esc_html__( 'Icon Size', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'rem',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-archive-available-rooms .tf-available-room .tf-available-room-gallery .tf-room-gallery:nth-child(n+2) svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( "gallery_margin", [
			'label'      => esc_html__( 'Margin', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-archive-available-rooms .tf-available-room .tf-available-room-gallery .tf-room-gallery:nth-child(n+2)' => $this->tf_apply_dim( 'margin' ),
			],
		]);

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "gallery_border",
			'selector' => "{{WRAPPER}} .tf-archive-available-rooms .tf-available-room .tf-available-room-gallery .tf-room-gallery:nth-child(n+2)",
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name' => 'gallery_shadow',
			'selector' => "{{WRAPPER}} .tf-archive-available-rooms .tf-available-room .tf-available-room-gallery .tf-room-gallery:nth-child(n+2)",
		]);

		$this->end_controls_section();
	}

	protected function tf_features_style_controls() {
		$this->start_controls_section( 'features_style', [
			'label' => esc_html__( 'Features Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-2', 'design-3', 'default'],
                'tf_tours' => ['design-2', 'design-3', 'default'],
                'tf_apartment' => ['design-1', 'design-2', 'default'],
            ],[
				'show_features' => 'yes',
			]),
		] );

		$this->add_control( 'tf_icon_features_color', [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .sr_rooms_table_block .room_details .featuredRooms .roomrow_flex .roomName_flex .tf-archive-desc i' => 'color: {{VALUE}};', //design-3
			],
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['default'],
                'tf_tours' => ['default'],
                'tf_apartment' => ['default'],
            ]),
		]);

		$this->add_control( 'tf_features_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-item-details .tf-archive-features ul li' => 'color: {{VALUE}};', //design-1
				'{{WRAPPER}} .tf-available-room-content .tf-available-room-content-left ul li' => 'color: {{VALUE}};', //design-2
				'{{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left ul li' => 'color: {{VALUE}};', //design-3
				'{{WRAPPER}} .tf-tooltip .tf-top' => 'color: {{VALUE}};', //design-3
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_features_typography",
			'selector' => "{{WRAPPER}} .tf-item-details .tf-archive-features ul li, {{WRAPPER}} .tf-available-room-content .tf-available-room-content-left ul li, {{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left ul li, {{WRAPPER}} .tf-tooltip .tf-top",
		]);

		$this->add_responsive_control( "features_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-item-details .tf-archive-features ul li' => $this->tf_apply_dim( 'padding' ), //design-1
				'{{WRAPPER}} .tf-available-room-content .tf-available-room-content-left ul li' => $this->tf_apply_dim( 'padding' ), //design-2
				'{{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left ul li' => $this->tf_apply_dim( 'padding' ), //design-3
				'{{WRAPPER}} .tf-tooltip .tf-top' => $this->tf_apply_dim( 'padding' ), //design-3
			],
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'default'],
                'tf_tours' => ['design-1', 'default'],
                'tf_apartment' => ['default'],
            ]),
		]);

		$this->add_responsive_control( "features_margin", [
			'label'      => esc_html__( 'Margin', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-item-details .tf-archive-features ul li' => $this->tf_apply_dim( 'margin' ), //design-1
				'{{WRAPPER}} .tf-available-room-content .tf-available-room-content-left ul li' => $this->tf_apply_dim( 'margin' ), //design-2
				'{{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left ul li' => $this->tf_apply_dim( 'margin' ), //design-3
				'{{WRAPPER}} .featuredRooms .roomrow_flex .roomName_flex .tf-archive-desc li' => $this->tf_apply_dim( 'margin' ), //default
			],
		]);

		$this->add_control( 'features_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-item-details .tf-archive-features ul li' => 'background-color: {{VALUE}};', //design-1
				'{{WRAPPER}} .tf-available-room-content .tf-available-room-content-left ul li' => 'background-color: {{VALUE}};', //design-2
				'{{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left ul li' => 'background-color: {{VALUE}};', //design-3
				'{{WRAPPER}} .tf-tooltip .tf-top' => 'background-color: {{VALUE}};', //design-3
				'{{WRAPPER}} .tf-tooltip .tf-top i.tool-i' => 'background-color: {{VALUE}};', //design-3
			],
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'default'],
                'tf_tours' => ['design-1', 'default'],
                'tf_apartment' => ['default'],
            ]),
		]);

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "features_border",
			'selector' => "{{WRAPPER}} .tf-item-details .tf-archive-features ul li, {{WRAPPER}} .tf-available-room-content .tf-available-room-content-left ul li, {{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left ul li, {{WRAPPER}} .tf-tooltip .tf-top",
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'default'],
                'tf_tours' => ['design-1', 'default'],
                'tf_apartment' => ['default'],
            ]),
		]);

		$this->add_control( "features_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-item-details .tf-archive-features ul li' => $this->tf_apply_dim( 'border-radius' ), //design-1
				'{{WRAPPER}} .tf-available-room-content .tf-available-room-content-left ul li' => $this->tf_apply_dim( 'border-radius' ), //design-2
				'{{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left ul li' => $this->tf_apply_dim( 'border-radius' ), //design-3
				'{{WRAPPER}} .tf-tooltip .tf-top' => $this->tf_apply_dim( 'border-radius' ), //design-3
			],
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'default'],
                'tf_tours' => ['design-1', 'default'],
                'tf_apartment' => ['default'],
            ]),
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name' => 'features_shadow',
			'selector' => "{{WRAPPER}} .tf-item-details .tf-archive-features ul li, {{WRAPPER}} .tf-available-room-content .tf-available-room-content-left ul li, {{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left ul li",
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1'],
                'tf_tours' => ['design-1'],
            ]),
		]);

		$this->add_control( 'tf_features_view_more_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => esc_html__( 'Features View More', 'tourfic' ),
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-2', 'design-3'],
                'tf_tours' => ['design-1', 'design-2', 'design-3'],
                'tf_apartment' => ['design-1', 'design-2'],
            ]),
		] );
		$this->add_control( 'tf_features_more_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-item-details .tf-archive-features ul span' => 'color: {{VALUE}};', //design-1
				'{{WRAPPER}} .tf-available-room-content .tf-available-room-content-left ul li a' => 'color: {{VALUE}};', //design-2
				'{{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left ul li a' => 'color: {{VALUE}};', //design-3
			],
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-2', 'design-3'],
                'tf_tours' => ['design-1', 'design-2', 'design-3'],
                'tf_apartment' => ['design-1', 'design-2'],
            ]),
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_features_more_typography",
			'selector' => "{{WRAPPER}} .tf-item-details .tf-archive-features ul span, {{WRAPPER}} .tf-available-room-content .tf-available-room-content-left ul li a, {{WRAPPER}} .tf-archive-hotel-content .tf-archive-hotel-content-left ul li a",
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'design-2', 'design-3'],
                'tf_tours' => ['design-1', 'design-2', 'design-3'],
                'tf_apartment' => ['design-1', 'design-2'],
            ]),
		]);

		$this->end_controls_section();
	}

	protected function tf_excerpt_style_controls() {
		$this->start_controls_section( 'excerpt_style', [
			'label' => esc_html__( 'Excerpt Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'conditions' => $this->tf_display_conditionally([
                'tf_hotel' => ['design-1', 'default'],
                'tf_tours' => ['design-1', 'default'],
                'tf_apartment' => ['default'],
            ],[
				'show_excerpt' => 'yes',
			]),
		]);

		$this->add_control( 'tf_excerpt_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-details .tf-details p' => 'color: {{VALUE}};', //design-1
				'{{WRAPPER}} .sr_rooms_table_block .room_details .featuredRooms .tf-archive-shortdesc' => 'color: {{VALUE}};', //design-2
				'{{WRAPPER}} .sr_rooms_table_block .room_details .featuredRooms .tf-archive-shortdesc p' => 'color: {{VALUE}};', //design-2
				'{{WRAPPER}} .tourfic-single-right .tf-tour-desc p' => 'color: {{VALUE}};', //design-2
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_excerpt_typography",
			'selector' => "{{WRAPPER}} .tf-item-card .tf-item-details .tf-details p, {{WRAPPER}} .sr_rooms_table_block .room_details .featuredRooms .tf-archive-shortdesc, {{WRAPPER}} .sr_rooms_table_block .room_details .featuredRooms .tf-archive-shortdesc p, {{WRAPPER}} .tourfic-single-right .tf-tour-desc p",
		]);

		$this->add_responsive_control( "excerpt_margin", [
			'label'      => esc_html__( 'Margin', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-item-details .tf-details' => $this->tf_apply_dim( 'margin' ), //design-1
				'{{WRAPPER}} .sr_rooms_table_block .room_details .featuredRooms .tf-archive-shortdesc' => $this->tf_apply_dim( 'margin' ), //default
				'{{WRAPPER}} .tourfic-single-right .tf-tour-desc' => $this->tf_apply_dim( 'margin' ), //default
			],
		]);

		$this->end_controls_section();
	}

	protected function tf_car_info_style_controls() {
		$this->start_controls_section( 'car_info_style', [
			'label' => esc_html__( 'Car Info Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'condition' => [
				'service' => 'tf_carrental',
			],
		]);

		$this->add_control( 'tf_car_info_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details ul li' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_car_info_typography",
			'selector' => "{{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details ul li",
		]);

		$this->add_responsive_control( "tf_car_info_icon_size", [
			'label'      => esc_html__( 'Icon Size', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'rem',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details ul li svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( "tf_car_info_icon_color", [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details ul li svg path" => 'stroke: {{VALUE}}',
			],
		] );

		$this->add_responsive_control( "car_info_item_gap", [
			'label'      => esc_html__( 'Item Gap', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'em',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
				'em'  => [
					'min' => 0,
					'max' => 20,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details ul" => 'gap: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control( "car_info_icon_gap", [
			'label'      => esc_html__( 'Icon Gap', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'em',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
				'em'  => [
					'min' => 0,
					'max' => 20,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details ul li p" => 'padding-left: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control( "car_info_margin", [
			'label'      => esc_html__( 'Margin', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details ul' => $this->tf_apply_dim( 'margin' ),
			],
		]);

		$this->end_controls_section();
	}

	protected function tf_price_style_controls() {
		$this->start_controls_section( 'price_style', [
			'label' => esc_html__( 'Price Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_price' => 'yes',
			],
		]);

		$this->add_control( 'tf_price_from_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => esc_html__( 'From Text', 'tourfic' ),
			'condition' => [
                'service!' => 'tf_carrental',
			],
		] );
		$this->add_control( 'tf_price_from_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-post-footer .tf-pricing' => 'color: {{VALUE}};', //design-1
				'{{WRAPPER}} .tf-available-room-price .tf-price-from' => 'color: {{VALUE}};', //design-2
				'{{WRAPPER}} .tf-archive-hotel .tf-archive-hotel-content .tf-archive-hotel-content-right .tf-archive-hotel-price' => 'color: {{VALUE}};', //design-3
				'{{WRAPPER}} .single-tour-wrap .single-tour-inner .tourfic-single-right .tf-room-price' => 'color: {{VALUE}};', //default
				'{{WRAPPER}} .tourfic-single-right .tf-tour-price' => 'color: {{VALUE}};', //default
			],
			'condition' => [
                'service!' => 'tf_carrental',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_price_from_typography",
			'selector' => "{{WRAPPER}} .tf-item-card .tf-post-footer .tf-pricing, 
							{{WRAPPER}} .tf-available-room-price .tf-price-from, 
							{{WRAPPER}} .tf-archive-hotel .tf-archive-hotel-content .tf-archive-hotel-content-right .tf-archive-hotel-price, 
							{{WRAPPER}} .single-tour-wrap .single-tour-inner .tourfic-single-right .tf-room-price, 
							{{WRAPPER}} .tourfic-single-right .tf-tour-price",
			'condition' => [
				'service!' => 'tf_carrental',
			],
		]);

		$this->add_control( 'tf_price_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => esc_html__( 'Price', 'tourfic' ),
		] );
		$this->add_control( 'tf_price_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-post-footer .tf-pricing span' => 'color: {{VALUE}};', //design-1
				'{{WRAPPER}} .tf-archive-available-rooms .tf-available-room .tf-available-room-content .tf-available-room-content-right .tf-available-room-price .tf-price-from .amount' => 'color: {{VALUE}};', //design-2
				'{{WRAPPER}} .tf-archive-hotel .tf-archive-hotel-content .tf-archive-hotel-content-right .tf-archive-hotel-price .woocommerce-Price-amount' => 'color: {{VALUE}};', //design-3
				'{{WRAPPER}} .single-tour-wrap .single-tour-inner .tourfic-single-right .tf-room-price .amount' => 'color: {{VALUE}};', //default
				'{{WRAPPER}} .tourfic-single-right .tf-tour-price span' => 'color: {{VALUE}};', //default
				'{{WRAPPER}} .tf-car-details .tf-booking-btn .tf-price-info h3 .amount' => 'color: {{VALUE}};', //default
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_price_typography",
			'selector' => "{{WRAPPER}} .tf-item-card .tf-post-footer .tf-pricing span, 
							{{WRAPPER}} .tf-archive-available-rooms .tf-available-room .tf-available-room-content .tf-available-room-content-right .tf-available-room-price .tf-price-from .amount, 
							{{WRAPPER}} .tf-archive-hotel .tf-archive-hotel-content .tf-archive-hotel-content-right .tf-archive-hotel-price .woocommerce-Price-amount, 
							{{WRAPPER}} .single-tour-wrap .single-tour-inner .tourfic-single-right .tf-room-price .amount, 
							{{WRAPPER}} .tourfic-single-right .tf-tour-price span, 
							{{WRAPPER}} .tf-car-details .tf-booking-btn .tf-price-info h3 .amount",
		]);

		//price type
		$this->add_control( 'tf_price_type_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => esc_html__( 'Price Type', 'tourfic' ),
			'condition' => [
				'service' => 'tf_carrental',
			],
		] );
		$this->add_control( 'tf_price_type_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details .tf-booking-btn .tf-price-info h3 small' => 'color: {{VALUE}};', //default
			],
			'condition' => [
				'service' => 'tf_carrental',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_price_type_typography",
			'selector' => "{{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details .tf-booking-btn .tf-price-info h3 small",
			'condition' => [
				'service' => 'tf_carrental',
			],
		]);

		$this->add_responsive_control( "price_margin", [
			'label'      => esc_html__( 'Margin', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-item-card .tf-post-footer .tf-pricing' => $this->tf_apply_dim( 'margin' ), //design-1
				'{{WRAPPER}} .tf-available-room-price' => $this->tf_apply_dim( 'margin' ), //design-2
				'{{WRAPPER}} .tf-archive-hotel-content-right .tf-archive-hotel-price' => $this->tf_apply_dim( 'margin' ), //design-3
				'{{WRAPPER}} .single-tour-wrap .single-tour-inner .tourfic-single-right .tf-room-price' => $this->tf_apply_dim( 'margin' ), //default
				'{{WRAPPER}} .tourfic-single-right .tf-tour-price' => $this->tf_apply_dim( 'margin' ), //default
				'{{WRAPPER}} .tf-car-details .tf-booking-btn .tf-price-info' => $this->tf_apply_dim( 'margin' ), //default
			],
		]);

		$this->end_controls_section();
	}

	protected function tf_button_style_controls() {
		$this->start_controls_section( "button_style", [
			'label'      => esc_html__( 'View Details Button', 'tourfic' ),
			'tab'        => Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_view_details' => 'yes',
			],
		] );
		
		$this->add_responsive_control( "view_btn_margin", [
			'label'      => esc_html__( 'Margin', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf_btn, {{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details .tf-booking-btn a.view-more" => $this->tf_apply_dim( 'margin' ),
			],
		] );

		$this->add_responsive_control( "view_btn_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf_btn, {{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details .tf-booking-btn a.view-more" => $this->tf_apply_dim( 'padding' ),
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => "view_btn_typography",
			'selector' => "{{WRAPPER}} .tf_btn, {{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details .tf-booking-btn a.view-more",
		] );
		
		$this->add_control( "tabs_view_btn_colors_heading", [
			'type'      => Controls_Manager::HEADING,
			'label'     => esc_html__( 'Colors & Border', 'tourfic' ),
			'separator' => 'before',
		] );

		$this->start_controls_tabs( "tabs_view_btn_style" );
		/*-----Button NORMAL state------ */
		$this->start_controls_tab( "tab_view_btn_normal", [
			'label' => esc_html__( 'Normal', 'tourfic' ),
		] );
		$this->add_control( "btn_color", [
			'label'     => esc_html__( 'Text Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn, {{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details .tf-booking-btn a.view-more" => 'color: {{VALUE}};',
			],
		] );
		$this->add_control( "btn_bg_color", [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn, {{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details .tf-booking-btn a.view-more" => 'background-color: {{VALUE}};',
			],
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "btn_border",
			'selector' => "{{WRAPPER}} .tf_btn, {{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details .tf-booking-btn a.view-more",
		] );
		$this->add_control( "btn_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf_btn, {{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details .tf-booking-btn a.view-more" => $this->tf_apply_dim( 'border-radius' ),
			],
		] );
		$this->end_controls_tab();

		/*-----Button HOVER state------ */
		$this->start_controls_tab( "tab_search_button_hover", [
			'label' => esc_html__( 'Hover', 'tourfic' ),
		] );
		$this->add_control( "button_color_hover", [
			'label'     => esc_html__( 'Text Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn:hover, {{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details .tf-booking-btn a.view-more:hover" => 'color: {{VALUE}};',
			],
		] );
		
		$this->add_control( "btn_hover_bg_color", [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn:hover, {{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details .tf-booking-btn a.view-more:hover" => 'background-color: {{VALUE}};',
			],
		] );
		$this->add_control( "btn_hover_border_color", [
			'label'     => esc_html__( 'Border Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf_btn:hover, {{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details .tf-booking-btn a.view-more:hover" => 'border-color: {{VALUE}};',
			],
		] );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		/*-----ends Button tabs--------*/

		$this->add_responsive_control( "view_btn_width", [
			'label'      => esc_html__( 'Button width', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 800,
					'step' => 5,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf_btn, {{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details .tf-booking-btn a.view-more" => 'width: {{SIZE}}{{UNIT}};',
			],
			'separator'  => 'before',
		] );
		$this->add_responsive_control( "view_btn_height", [
			'label'      => esc_html__( 'Button Height', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 500,
					'step' => 5,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf_btn, {{WRAPPER}} .tf-car-result .tf-single-car-view .tf-car-details .tf-booking-btn a.view-more" => 'height: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function tf_pagination_style_controls(){
        $this->start_controls_section( 'pagination_style', [
			'label' => esc_html__('Pagination', 'tourfic'),
			'tab'   => Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_pagination' => 'yes',
			],
		]);

        $this->add_responsive_control('pagination_alignment',[
			'label' => esc_html__('Alignment', 'tourfic'),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'flex-start' => [
					'title' => esc_html__('Left', 'tourfic'),
					'icon' => 'eicon-text-align-left',
				],
				'center' => [
					'title' => esc_html__('Center', 'tourfic'),
					'icon' => 'eicon-text-align-center',
				],
				'flex-end' => [
					'title' => esc_html__('Right', 'tourfic'),
					'icon' => 'eicon-text-align-right',
				],
			],
			'default' => 'flex-start',
			'selectors' => [
				'{{WRAPPER}} .tf-pagination-bar #tf_posts_navigation_bar' => 'justify-content: {{VALUE}};',
			],
		]);

        $this->add_responsive_control('pagination_top_spacing',[
			'label' => esc_html__('Top Spacing', 'tourfic'),
			'type' => Controls_Manager::SLIDER,
			'range' => [
				'px' => [
					'max' => 100,
				],
			],
			'default' => [
				'size' => 40,
			],
			'selectors' => [
				'{{WRAPPER}} .tf-pagination-bar #tf_posts_navigation_bar' => 'margin-top: {{SIZE}}px;',
			],
		]);

        $this->add_responsive_control('pagination_item_gap',[
			'label' => esc_html__('Item Gap', 'tourfic'),
			'type' => Controls_Manager::SLIDER,
			'range' => [
				'px' => [
					'max' => 50,
				],
			],
			'default' => [
				'size' => 10,
			],
			'selectors' => [
				'{{WRAPPER}} .tf-pagination-bar #tf_posts_navigation_bar' => 'gap: {{SIZE}}px;',
			],
		]);

        $this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'pagination_typography',
			'selector' => '{{WRAPPER}} .tf-pagination-bar #tf_posts_navigation_bar .page-numbers',
		]);

		$this->add_responsive_control( "pagination_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-pagination-bar #tf_posts_navigation_bar .page-numbers' => $this->tf_apply_dim( 'padding' ),
			],
		] );

        $this->start_controls_tabs('pagination_tabs');

        // Normal State Tab
        $this->start_controls_tab('pagination_normal', ['label' => esc_html__('Normal', 'tourfic')]);
        $this->add_control('pagination_normal_text_color',[
			'label' => esc_html__('Text Color', 'tourfic'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .tf-pagination-bar #tf_posts_navigation_bar .page-numbers' => 'color: {{VALUE}};',
			],
		]);

        $this->add_control('pagination_normal_bg_color',[
			'label' => esc_html__('Background Color', 'tourfic'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .tf-pagination-bar #tf_posts_navigation_bar .page-numbers' => 'background: {{VALUE}};',
			],
		]);

        $this->add_group_control(Group_Control_Border::get_type(),[
			'name' => 'pagination_normal_border',
			'label' => esc_html__('Border', 'tourfic'),
			'selector' => '{{WRAPPER}} .tf-pagination-bar #tf_posts_navigation_bar .page-numbers',
		]);

        $this->end_controls_tab();

        // Hover State Tab
        $this->start_controls_tab('pagination_hover', ['label' => esc_html__('Hover', 'tourfic')]);

        $this->add_control('pagination_hover_text_color',[
			'label' => esc_html__('Text Color', 'tourfic'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .tf-pagination-bar #tf_posts_navigation_bar .page-numbers:hover' => 'color: {{VALUE}};',
			],
		]);

        $this->add_control('pagination_hover_bg_color',[
			'label' => esc_html__('Background Color', 'tourfic'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .tf-pagination-bar #tf_posts_navigation_bar .page-numbers:hover' => 'background: {{VALUE}};',
			],
		]);

        $this->add_control('pagination_hover_border_color',[
			'label' => esc_html__('Border Color', 'tourfic'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .tf-pagination-bar #tf_posts_navigation_bar .page-numbers:hover' => 'border-color: {{VALUE}};',
			],
			'condition' => [
				'pagination_normal_border_border!' => '',
			]
		]);
        $this->end_controls_tab();

        // Active State Tab
        $this->start_controls_tab('pagination_active', ['label' => esc_html__('Active', 'tourfic')]);

        $this->add_control('pagination_hover_text_active',[
			'label' => esc_html__('Text Color', 'tourfic'),
			'type' => Controls_Manager::COLOR,
			'default' => '#fff',
			'selectors' => [
				'{{WRAPPER}} .tf-pagination-bar #tf_posts_navigation_bar .page-numbers.current' => 'color: {{VALUE}};',
			],
		]);

        $this->add_control('pagination_active_bg_color',[
			'label' => esc_html__('Background Color', 'tourfic'),
			'type' => Controls_Manager::COLOR,
			'default' => '#8040FF',
			'selectors' => [
				'{{WRAPPER}} .tf-pagination-bar #tf_posts_navigation_bar .page-numbers.current' => 'background: {{VALUE}};',
			],
		]);

        $this->add_control('pagination_active_border_color',[
			'label' => esc_html__('Border Color', 'tourfic'),
			'type' => Controls_Manager::COLOR,
			'default' => '',
			'selectors' => [
				'{{WRAPPER}} .tf-pagination-bar #tf_posts_navigation_bar .page-numbers.current' => 'border-color: {{VALUE}};',
			],
			'condition' => [
				'pagination_normal_border_border!' => '',
			]
		]);
        $this->end_controls_tab();
        $this->end_controls_tabs();

		$this->add_control( "pagination_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-pagination-bar #tf_posts_navigation_bar .page-numbers" => $this->tf_apply_dim( 'border-radius' ),
			],
		] );

        $this->end_controls_section();
    }

	protected function tf_search_elementor_settings($settings){
		return [
			'service' => $settings['service'] ?? 'tf_hotel',
			'design_hotel' => $settings['design_hotel'] ?? 'design-1',
			'design_tours' => $settings['design_tours'] ?? 'design-1',
			'design_apartment' => $settings['design_apartment'] ?? 'design-1',
			'design_carrental' => $settings['design_carrental'] ?? 'design-1',
			'listing_layout_toggle' => $settings['listing_layout_toggle'] ?? 'yes',
			'listing_default_layout' => $settings['listing_default_layout'] ?? 'list',
			'listing_layout' => $settings['listing_layout'] ?? 'list',
			'grid_column' => $settings['grid_column'] ?? '2',
			'show_total_result' => $settings['show_total_result'] ?? 'yes',
			'show_sorting' => $settings['show_sorting'] ?? 'yes',
			'posts_per_page' => $settings['posts_per_page'] ?? 10,
			'orderby' => $settings['orderby'] ?? 'date',
			'order' => $settings['order'] ?? 'desc',
			'show_image' => $settings['show_image'] ?? 'yes',
			'image_size' => $settings['image_size'] ?? 'medium',
			'show_fallback_img' => $settings['show_fallback_img'] ?? '',
			'fallback_img' => $settings['fallback_img'] ?? '',
			'gallery' => $settings['gallery'] ?? 'yes',
			'tour_infos' => $settings['tour_infos'] ?? 'yes',
			'featured_badge' => $settings['featured_badge'] ?? 'yes',
			'discount_tag' => $settings['discount_tag'] ?? 'yes',
			'promotional_tags' => $settings['promotional_tags'] ?? 'yes',
			'show_title' => $settings['show_title'] ?? 'yes',
			'title_length' => $settings['title_length'] ?? 55,
			'show_excerpt' => $settings['show_excerpt'] ?? 'yes',
			'excerpt_length' => $settings['excerpt_length'] ?? 100,
			'show_location' => $settings['show_location'] ?? 'yes',
			'location_icon' => $settings['location_icon'] ?? ['value' => 'fa-solid fa-location-dot', 'library' => 'fa-solid'],
			'location_length' => $settings['location_length'] ?? 120,
			'show_features' => $settings['show_features'] ?? 'yes',
			'features_count' => $settings['features_count'] ?? 4,
			'show_review' => $settings['show_review'] ?? 'yes',
			'show_price' => $settings['show_price'] ?? 'yes',
			'show_view_details' => $settings['show_view_details'] ?? 'yes',
			'view_details_text' => $settings['view_details_text'] ?? 'View Details'
		];
	}

	protected function tf_hotel_design_1($settings, $query) {
		$post_count = $query->post_count;
		$show_total_result = isset( $settings['show_total_result'] ) ? $settings['show_total_result'] : 'yes';
		$show_sorting = isset( $settings['show_sorting'] ) ? $settings['show_sorting'] : 'yes';
		$grid_column = isset( $settings['grid_column'] ) ? absint($settings['grid_column']) : 2;
		$listing_layout_toggle = isset( $settings['listing_layout_toggle'] ) ? $settings['listing_layout_toggle'] : 'yes';
		if($listing_layout_toggle == 'yes'){
			$listing_layout = isset( $settings['listing_default_layout'] ) ? $settings['listing_default_layout'] : 'list';
		} else {
			$listing_layout = isset( $settings['listing_layout'] ) ? $settings['listing_layout'] : 'list';
		}
		$show_pagination = isset( $settings['show_pagination'] ) ? $settings['show_pagination'] : 'yes';
		$pagination_prev_label = isset( $settings['pagination_prev_label'] ) ? $settings['pagination_prev_label'] : '';
		$pagination_next_label = isset( $settings['pagination_next_label'] ) ? $settings['pagination_next_label'] : '';
		?>
        <div class="tf-archive-listing-wrap tf-archive-listing__one" data-design="design-1">
            <!-- Search Head Section -->
            <div class="tf-archive-head tf-flex tf-flex-align-center tf-flex-space-bttn">
				<?php if($show_total_result == 'yes') : ?>
				<div class="tf-search-result tf-flex">
                    <span class="tf-counter-title"><?php echo esc_html__( 'Total Results ', 'tourfic' ); ?> </span>
                    <span><?php echo ' ('; ?> </span>
                    <div class="tf-total-results">
                        <span><?php echo esc_html( $post_count ); ?> </span>
                    </div>
                    <span><?php echo ')'; ?> </span>
                </div>
				<?php endif; ?>
                <div class="tf-search-layout tf-flex tf-flex-gap-12">
					<?php if($listing_layout_toggle == 'yes') : ?>
						<div class="tf-icon tf-serach-layout-list tf-list-active tf-grid-list-layout <?php echo $listing_layout=="list" ? esc_attr('active') : ''; ?>" data-id="list-view">
							<div class="defult-view">
								<svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect width="12" height="2" fill="white"/>
								<rect x="14" width="2" height="2" fill="white"/>
								<rect y="5" width="12" height="2" fill="white"/>
								<rect x="14" y="5" width="2" height="2" fill="white"/>
								<rect y="10" width="12" height="2" fill="white"/>
								<rect x="14" y="10" width="2" height="2" fill="white"/>
								</svg>
							</div>
							<div class="active-view">
								<svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect width="12" height="2" fill="#0E3DD8"/>
								<rect x="14" width="2" height="2" fill="#0E3DD8"/>
								<rect y="5" width="12" height="2" fill="#0E3DD8"/>
								<rect x="14" y="5" width="2" height="2" fill="#0E3DD8"/>
								<rect y="10" width="12" height="2" fill="#0E3DD8"/>
								<rect x="14" y="10" width="2" height="2" fill="#0E3DD8"/>
								</svg>
							</div>
						</div>
						<div class="tf-icon tf-serach-layout-grid tf-grid-list-layout <?php echo $listing_layout=="grid" ? esc_attr('active') : ''; ?>" data-id="grid-view">
							<div class="defult-view">
								<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect x="10" width="2" height="2" fill="#0E3DD8"/>
								<rect x="10" y="5" width="2" height="2" fill="#0E3DD8"/>
								<rect x="10" y="10" width="2" height="2" fill="#0E3DD8"/>
								<rect x="5" width="2" height="2" fill="#0E3DD8"/>
								<rect x="5" y="5" width="2" height="2" fill="#0E3DD8"/>
								<rect x="5" y="10" width="2" height="2" fill="#0E3DD8"/>
								<rect width="2" height="2" fill="#0E3DD8"/>
								<rect y="5" width="2" height="2" fill="#0E3DD8"/>
								<rect y="10" width="2" height="2" fill="#0E3DD8"/>
								</svg>
							</div>
							<div class="active-view">
								<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect x="10" width="2" height="2" fill="white"/>
								<rect x="10" y="5" width="2" height="2" fill="white"/>
								<rect x="10" y="10" width="2" height="2" fill="white"/>
								<rect x="5" width="2" height="2" fill="white"/>
								<rect x="5" y="5" width="2" height="2" fill="white"/>
								<rect x="5" y="10" width="2" height="2" fill="white"/>
								<rect width="2" height="2" fill="white"/>
								<rect y="5" width="2" height="2" fill="white"/>
								<rect y="10" width="2" height="2" fill="white"/>
								</svg>
							</div>
						</div>
					<?php endif; ?>
					
					<?php if($show_sorting == 'yes') : ?>
                    <div class="tf-sorting-selection-warper">
                        <form class="tf-archive-ordering" method="get">
                            <select class="tf-orderby" name="tf-orderby" id="tf-orderby">
                                <option value="default"><?php echo esc_html__( 'Default Sorting', 'tourfic' ); ?></option>
                                <option value="enquiry"><?php echo esc_html__( 'Sort By Recommended', 'tourfic' ); ?></option>
                                <option value="order"><?php echo esc_html__( 'Sort By Popularity', 'tourfic' ); ?></option>
                                <option value="rating"><?php echo esc_html__( 'Sort By Average Rating', 'tourfic' ); ?></option>
                                <option value="latest"><?php echo esc_html__( 'Sort By Latest', 'tourfic' ); ?></option>
                                <option value="price-high"><?php echo esc_html__( 'Sort By Price: High to Low', 'tourfic' ); ?></option>
                                <option value="price-low"><?php echo esc_html__( 'Sort By Price: Low to High', 'tourfic' ); ?></option>
                            </select>
                        </form>
                    </div>
					<?php endif; ?>
                </div>
            </div>
            <!-- Loader Image -->
            <div id="tf_ajax_searchresult_loader">
                <div id="tf-searchresult-loader-img">
                    <img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
                </div>
            </div>
            <div class="tf-search-results-list tf-mt-30">
                <div class="archive_ajax_result tf-item-cards tf-flex <?php echo $listing_layout=="list" ? esc_attr('tf-layout-list') : esc_attr('tf-layout-grid'); ?> tf-grid-<?php echo esc_attr($grid_column); ?>">

                <?php
                if ( $query->have_posts() ) {
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );
                        if ( !empty( $hotel_meta[ "featured" ] ) && $hotel_meta[ "featured" ] == 1 ) {
                            Hotel::tf_hotel_archive_single_item('', '', '', '', '', '', $settings);
                        }
                    }
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );
                        if ( empty($hotel_meta[ "featured" ]) ) {
                            Hotel::tf_hotel_archive_single_item('', '', '', '', '', '', $settings);
                        }
                    }
                } else {
                    echo '<div class="tf-nothing-found" data-post-count="0" >' .esc_html__("No Hotels Found!", "tourfic"). '</div>';
                }
                ?>
				<?php if($show_pagination == 'yes') : ?>
                    <div class="tf-pagination-bar">
                        <?php Helper::tourfic_posts_navigation($query, $pagination_prev_label, $pagination_next_label); ?>
                    </div>
				<?php endif; ?>
                </div>
            </div>
			<?php $elementor_settings = $this->tf_search_elementor_settings($settings); ?>
			<div id="tf-elementor-settings" style="display: none"><?php echo !empty($elementor_settings) ? wp_json_encode($elementor_settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : wp_json_encode([]); ?></div>
        </div>
        <?php
	}

	protected function tf_hotel_design_2($settings, $query) {
		$post_count = $query->post_count;
		$show_total_result = isset( $settings['show_total_result'] ) ? $settings['show_total_result'] : 'yes';
		$show_sorting = isset( $settings['show_sorting'] ) ? $settings['show_sorting'] : 'yes';
		$show_pagination = isset( $settings['show_pagination'] ) ? $settings['show_pagination'] : 'yes';
		$pagination_prev_label = isset( $settings['pagination_prev_label'] ) ? $settings['pagination_prev_label'] : '';
		$pagination_next_label = isset( $settings['pagination_next_label'] ) ? $settings['pagination_next_label'] : '';
		?>
        <div class="tf-archive-listing-wrap tf-archive-listing__two" data-design="design-2">
			<!--Available rooms start -->
			<div class="tf-available-archive-hetels-wrapper tf-available-rooms-wrapper" id="tf-hotel-rooms">
				<div class="tf-archive-available-rooms-head tf-available-rooms-head">
					<?php if($show_total_result == 'yes') : ?>
					<span class="tf-total-results"><?php esc_html_e("Total", "tourfic"); ?> <span><?php echo esc_html( $post_count ); ?></span> <?php esc_html_e("hotels available", "tourfic"); ?></span>
					<?php endif; ?>
					
					<a class="tf-archive-filter-showing" href="#tf__booking_sidebar">
						<i class="ri-equalizer-line"></i>
					</a>

					<?php if($show_sorting == 'yes') : ?>
					<div class="tf-sorting-selection-warper">
						<form class="tf-archive-ordering" method="get">
							<select class="tf-orderby" name="tf-orderby" id="tf-orderby">
								<option value="default"><?php echo esc_html__( 'Default Sorting', 'tourfic' ); ?></option>
								<option value="enquiry"><?php echo esc_html__( 'Sort By Recommended', 'tourfic' ); ?></option>
								<option value="order"><?php echo esc_html__( 'Sort By Popularity', 'tourfic' ); ?></option>
								<option value="rating"><?php echo esc_html__( 'Sort By Average Rating', 'tourfic' ); ?></option>
								<option value="latest"><?php echo esc_html__( 'Sort By Latest', 'tourfic' ); ?></option>
								<option value="price-high"><?php echo esc_html__( 'Sort By Price: High to Low', 'tourfic' ); ?></option>
								<option value="price-low"><?php echo esc_html__( 'Sort By Price: Low to High', 'tourfic' ); ?></option>
							</select>
						</form>
					</div>
					<?php endif; ?>
				</div>
				
				<!-- Loader Image -->
				<div id="tour_room_details_loader">
					<div id="tour-room-details-loader-img">
						<img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
					</div>
				</div>
				
				<!--Available rooms start -->
				<div class="tf-archive-available-rooms tf-available-rooms archive_ajax_result">
					<?php
					if ( $query->have_posts() ) {
						while ( $query->have_posts() ) {
							$query->the_post();
							$hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );
							if ( !empty( $hotel_meta[ "featured" ] ) && $hotel_meta[ "featured" ] == 1 ) {
								Hotel::tf_hotel_archive_single_item('', '', '', '', '', '', $settings);
							}
						}
						while ( $query->have_posts() ) {
							$query->the_post();
							$hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );
							if ( empty($hotel_meta[ "featured" ]) ) {
								Hotel::tf_hotel_archive_single_item('', '', '', '', '', '', $settings);
							}
						}
					} else {
						echo '<div class="tf-nothing-found" data-post-count="0" >' .esc_html__("No Hotels Found!", "tourfic"). '</div>';
					}
					?>
					
					<?php if($show_pagination == 'yes') : ?>
						<div class="tf-pagination-bar">
							<?php Helper::tourfic_posts_navigation($query, $pagination_prev_label, $pagination_next_label); ?>
						</div>
					<?php endif; ?>
				</div>
				<!-- Available rooms end -->

			</div>

			<div class="tf-popup-wrapper tf-hotel-popup">
				<div class="tf-popup-inner">
					<div class="tf-popup-body">
						
					</div>                
					<div class="tf-popup-close">
						<i class="fa-solid fa-xmark"></i>
					</div>
				</div>
			</div>
			<?php $elementor_settings = $this->tf_search_elementor_settings($settings); ?>
			<div id="tf-elementor-settings" style="display: none"><?php echo !empty($elementor_settings) ? wp_json_encode($elementor_settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : wp_json_encode([]); ?></div>
		</div>
		<?php
	}

	protected function tf_hotel_design_3($settings, $query) {
        $post_count = $query->post_count;
        $tf_map_settings = !empty(Helper::tfopt('google-page-option')) ? Helper::tfopt('google-page-option') : "default";
        $tf_map_api = !empty(Helper::tfopt('tf-googlemapapi')) ? Helper::tfopt('tf-googlemapapi') : '';
		$show_total_result = isset( $settings['show_total_result'] ) ? $settings['show_total_result'] : 'yes';
		$show_sidebar = isset( $settings['show_sidebar'] ) ? $settings['show_sidebar'] : 'yes';
		$grid_column = isset( $settings['grid_column'] ) ? absint($settings['grid_column']) : 2;
		$listing_layout_toggle = isset( $settings['listing_layout_toggle'] ) ? $settings['listing_layout_toggle'] : 'yes';
		if($listing_layout_toggle == 'yes'){
			$listing_layout = isset( $settings['listing_default_layout'] ) ? $settings['listing_default_layout'] : 'list';
		} else {
			$listing_layout = isset( $settings['listing_layout'] ) ? $settings['listing_layout'] : 'list';
		}
		$show_pagination = isset( $settings['show_pagination'] ) ? $settings['show_pagination'] : 'yes';
		$pagination_prev_label = isset( $settings['pagination_prev_label'] ) ? $settings['pagination_prev_label'] : '';
		$pagination_next_label = isset( $settings['pagination_next_label'] ) ? $settings['pagination_next_label'] : '';
		?>
		<div class="tf-archive-listing-wrap tf-archive-listing__three tf-archive-template__three"  data-design="design-3">
			<?php if ($query->have_posts()) : ?>
				<div class="tf-archive-details-wrap">
					<div class="tf-archive-details">
					<?php if ($tf_map_settings == "googlemap") :
						if (empty($tf_map_api)):
							?>
							<div class="tf-notice tf-mt-24 tf-mb-30">
								<?php
								if (current_user_can('administrator')) {
									echo '<p>' . esc_html__('Google Maps is selected but the API key is missing. Please configure the API key', 'tourfic') . '<a href="' . esc_url(admin_url('admin.php?page=tf_settings#tab=map_settings')) . '" target="_blank">' . esc_html__('Map Settings', 'tourfic') . '</a></p>';
								} else {
									echo '<p>' . esc_html__('Access is restricted as Google Maps API key is not configured. Please contact the site administrator.', 'tourfic') . '</p>';
								}
								?>
							</div>
						<?php else: ?>
							<div class="tf-details-left">
								
								<!-- Loader Image -->
								<div id="tf_ajax_searchresult_loader">
									<div id="tf-searchresult-loader-img">
										<img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
									</div>
								</div>
								<!--Available rooms start -->
								<div class="tf-archive-hotels-wrapper">
									<?php if($show_sidebar == 'yes') : ?>
									<div class="tf-archive-filter">
										<div class="tf-archive-filter-sidebar">
											<div class="tf-filter-wrapper">
												<div class="tf-filter-title">
													<h4 class="tf-section-title"><?php echo esc_html__("Filter", "tourfic"); ?></h4>
													<button class="filter-reset-btn"><?php echo esc_html__("Reset", "tourfic"); ?></button>
												</div>
												<?php if (is_active_sidebar('tf_archive_booking_sidebar')) { ?>
													<div id="tf__booking_sidebar">
														<?php dynamic_sidebar('tf_archive_booking_sidebar'); ?>
													</div>
												<?php } ?>
												<?php if (is_active_sidebar('tf_map_popup_sidebar')) { ?>
													<div id="tf_map_popup_sidebar">
														<?php dynamic_sidebar('tf_map_popup_sidebar'); ?>
													</div>
												<?php } ?>
											</div>
										</div>
									</div>
									<?php endif; ?>
									<div class="tf-archive-top">
										<?php if($show_total_result == 'yes') : ?>
										<h5 class="tf-total-results"><?php esc_html_e("Found", "tourfic"); ?>
											<span class="tf-map-item-count"><?php echo esc_html($post_count); ?></span> <?php esc_html_e("of", "tourfic"); ?> <?php echo esc_html($query->found_posts); ?> <?php esc_html_e("Hotels", "tourfic"); ?></h5>
										<?php endif; ?>
										<a href="" class="tf-mobile-map-btn">
											<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
												<path d="M17.3327 7.33366V6.68156C17.3327 5.06522 17.3327 4.25705 16.8445 3.75491C16.3564 3.25278 15.5707 3.25278 13.9993 3.25278H12.2671C11.5027 3.25278 11.4964 3.25129 10.8089 2.90728L8.03258 1.51794C6.87338 0.93786 6.29378 0.647818 5.67633 0.667975C5.05888 0.688132 4.49833 1.01539 3.37722 1.66992L2.354 2.2673C1.5305 2.74807 1.11876 2.98846 0.892386 3.38836C0.666016 3.78827 0.666016 4.27527 0.666016 5.24927V12.0968C0.666016 13.3765 0.666016 14.0164 0.951234 14.3725C1.14102 14.6095 1.40698 14.7688 1.70102 14.8216C2.1429 14.901 2.68392 14.5851 3.76591 13.9534C4.50065 13.5245 5.20777 13.079 6.08674 13.1998C6.82326 13.301 7.50768 13.7657 8.16602 14.0952"
													stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M5.66602 0.666992L5.66601 13.167" stroke="white" stroke-linejoin="round"/>
												<path d="M11.5 3.16699V6.91699" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M14.2556 17.0696C14.075 17.2388 13.8334 17.3333 13.5821 17.3333C13.3308 17.3333 13.0893 17.2388 12.9086 17.0696C11.254 15.5108 9.0366 13.7695 10.1179 11.2415C10.7026 9.87465 12.1061 9 13.5821 9C15.0581 9 16.4616 9.87465 17.0463 11.2415C18.1263 13.7664 15.9143 15.5162 14.2556 17.0696Z"
													stroke="white"/>
												<path d="M13.582 12.75H13.5895" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
											<span><?php echo esc_html__('Map', 'tourfic') ?></span>
										</a>

										<ul class="tf-archive-view">
											<?php if($show_sidebar == 'yes') : ?>
											<li class="tf-archive-filter-btn">
												<i class="ri-equalizer-line"></i>
												<span><?php esc_html_e("All Filter", "tourfic"); ?></span>
											</li>
											<?php endif; ?>

											<?php if($listing_layout_toggle == 'yes') : ?>
											<li class="tf-archive-view-item tf-archive-list-view <?php echo $listing_layout == "list" ? esc_attr('active') : ''; ?>" data-id="list-view">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
													<path d="M1.33398 7.59996C1.33398 6.82778 1.49514 6.66663 2.26732 6.66663H13.734C14.5062 6.66663 14.6673 6.82778 14.6673 7.59996V8.39996C14.6673 9.17214 14.5062 9.33329 13.734 9.33329H2.26732C1.49514 9.33329 1.33398 9.17214 1.33398 8.39996V7.59996Z"
														stroke="#6E655E" stroke-linecap="round"/>
													<path d="M1.33398 2.26665C1.33398 1.49447 1.49514 1.33331 2.26732 1.33331H13.734C14.5062 1.33331 14.6673 1.49447 14.6673 2.26665V3.06665C14.6673 3.83882 14.5062 3.99998 13.734 3.99998H2.26732C1.49514 3.99998 1.33398 3.83882 1.33398 3.06665V2.26665Z"
														stroke="#6E655E" stroke-linecap="round"/>
													<path d="M1.33398 12.9333C1.33398 12.1612 1.49514 12 2.26732 12H13.734C14.5062 12 14.6673 12.1612 14.6673 12.9333V13.7333C14.6673 14.5055 14.5062 14.6667 13.734 14.6667H2.26732C1.49514 14.6667 1.33398 14.5055 1.33398 13.7333V12.9333Z"
														stroke="#6E655E" stroke-linecap="round"/>
												</svg>
											</li>
											<li class="tf-archive-view-item tf-archive-grid-view <?php echo $listing_layout == "grid" ? esc_attr('active') : ''; ?>" data-id="grid-view">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
													<path d="M1.33398 12C1.33398 10.973 1.33398 10.4595 1.56514 10.0823C1.69448 9.87127 1.87194 9.69381 2.08301 9.56447C2.46021 9.33331 2.97369 9.33331 4.00065 9.33331C5.02761 9.33331 5.54109 9.33331 5.9183 9.56447C6.12936 9.69381 6.30682 9.87127 6.43616 10.0823C6.66732 10.4595 6.66732 10.973 6.66732 12C6.66732 13.0269 6.66732 13.5404 6.43616 13.9176C6.30682 14.1287 6.12936 14.3062 5.9183 14.4355C5.54109 14.6666 5.02761 14.6666 4.00065 14.6666C2.97369 14.6666 2.46021 14.6666 2.08301 14.4355C1.87194 14.3062 1.69448 14.1287 1.56514 13.9176C1.33398 13.5404 1.33398 13.0269 1.33398 12Z"
														stroke="#6E655E" stroke-width="1.2"/>
													<path d="M9.33398 12C9.33398 10.973 9.33398 10.4595 9.56514 10.0823C9.69448 9.87127 9.87194 9.69381 10.083 9.56447C10.4602 9.33331 10.9737 9.33331 12.0007 9.33331C13.0276 9.33331 13.5411 9.33331 13.9183 9.56447C14.1294 9.69381 14.3068 9.87127 14.4362 10.0823C14.6673 10.4595 14.6673 10.973 14.6673 12C14.6673 13.0269 14.6673 13.5404 14.4362 13.9176C14.3068 14.1287 14.1294 14.3062 13.9183 14.4355C13.5411 14.6666 13.0276 14.6666 12.0007 14.6666C10.9737 14.6666 10.4602 14.6666 10.083 14.4355C9.87194 14.3062 9.69448 14.1287 9.56514 13.9176C9.33398 13.5404 9.33398 13.0269 9.33398 12Z"
														stroke="#6E655E" stroke-width="1.2"/>
													<path d="M1.33398 3.99998C1.33398 2.97302 1.33398 2.45954 1.56514 2.08233C1.69448 1.87127 1.87194 1.69381 2.08301 1.56447C2.46021 1.33331 2.97369 1.33331 4.00065 1.33331C5.02761 1.33331 5.54109 1.33331 5.9183 1.56447C6.12936 1.69381 6.30682 1.87127 6.43616 2.08233C6.66732 2.45954 6.66732 2.97302 6.66732 3.99998C6.66732 5.02694 6.66732 5.54042 6.43616 5.91762C6.30682 6.12869 6.12936 6.30615 5.9183 6.43549C5.54109 6.66665 5.02761 6.66665 4.00065 6.66665C2.97369 6.66665 2.46021 6.66665 2.08301 6.43549C1.87194 6.30615 1.69448 6.12869 1.56514 5.91762C1.33398 5.54042 1.33398 5.02694 1.33398 3.99998Z"
														stroke="#6E655E" stroke-width="1.2"/>
													<path d="M9.33398 3.99998C9.33398 2.97302 9.33398 2.45954 9.56514 2.08233C9.69448 1.87127 9.87194 1.69381 10.083 1.56447C10.4602 1.33331 10.9737 1.33331 12.0007 1.33331C13.0276 1.33331 13.5411 1.33331 13.9183 1.56447C14.1294 1.69381 14.3068 1.87127 14.4362 2.08233C14.6673 2.45954 14.6673 2.97302 14.6673 3.99998C14.6673 5.02694 14.6673 5.54042 14.4362 5.91762C14.3068 6.12869 14.1294 6.30615 13.9183 6.43549C13.5411 6.66665 13.0276 6.66665 12.0007 6.66665C10.9737 6.66665 10.4602 6.66665 10.083 6.43549C9.87194 6.30615 9.69448 6.12869 9.56514 5.91762C9.33398 5.54042 9.33398 5.02694 9.33398 3.99998Z"
														stroke="#6E655E" stroke-width="1.2"/>
												</svg>
											</li>
											<?php endif; ?>
										</ul>
									</div>

									<!--Available rooms start -->
									<div class="tf-archive-hotels archive_ajax_result <?php echo $listing_layout == "list" ? esc_attr('tf-layout-list') : esc_attr('tf-layout-grid'); ?> tf-grid-<?php echo esc_attr($grid_column); ?>">

										<?php
										$count = 0;
										$locations = [];
										while ($query->have_posts()) {
											$query->the_post();

											$meta = get_post_meta(get_the_ID(), 'tf_hotels_opt', true);
											if (!$meta["featured"]) {
												continue;
											}

											$count++;
											$map = !empty($meta['map']) ? Helper::tf_data_types($meta['map']) : '';

											$min_price_arr = HotelPricing::instance(get_the_ID())->get_min_price();
											$min_sale_price = !empty($min_price_arr['min_sale_price']) ? $min_price_arr['min_sale_price'] : 0;
											$min_regular_price = !empty($min_price_arr['min_regular_price']) ? $min_price_arr['min_regular_price'] : 0;
											$min_discount_type = !empty($min_price_arr['min_discount_type']) ? $min_price_arr['min_discount_type'] : 'none';
											$min_discount_amount = !empty($min_price_arr['min_discount_amount']) ? $min_price_arr['min_discount_amount'] : 0;

											if ($min_regular_price != 0) {
												$price_html = wc_format_sale_price($min_regular_price, $min_sale_price);
											} else {
												$price_html = wp_kses_post(wc_price($min_sale_price)) . " ";
											}

											if (!empty($map)) {
												$lat = $map['latitude'];
												$lng = $map['longitude'];
												ob_start();
												?>
												<div class="tf-map-item">
													<div class="tf-map-item-thumb">
														<a href="<?php the_permalink(); ?>">
															<?php
															if (!empty(wp_get_attachment_url(get_post_thumbnail_id(), 'tf_gallery_thumb'))) {
																the_post_thumbnail('full');
															} else {
																echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
															}
															?>
														</a>

														<?php
														if (!empty($min_discount_amount)) : ?>
															<div class="tf-map-item-discount">
																<?php echo $min_discount_type == "percent" ? wp_kses_post($min_discount_amount . '%') : wp_kses_post(wc_price($min_discount_amount)) ?><?php esc_html_e(" Off", "tourfic"); ?>
															</div>
														<?php endif; ?>
													</div>
													<div class="tf-map-item-content">
														<h4>
															<a href="<?php the_permalink(); ?>">
																<?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
															</a>
														</h4>
														<div class="tf-map-item-price">
															<?php echo wp_kses_post(HotelPricing::instance(get_the_ID())->get_min_price_html()); ?>
														</div>
														<?php \Tourfic\App\TF_Review::tf_archive_single_rating('', $settings['design_hotel']); ?>
													</div>
												</div>
												<?php
												$infoWindowtext = ob_get_clean();

												$locations[$count] = [
													'id' => get_the_ID(),
													'url'	  => get_the_permalink(),
													'lat' => (float)$lat,
													'lng' => (float)$lng,
													'price' => base64_encode($price_html),
													'content' => base64_encode($infoWindowtext)
												];
											}
											Hotel::tf_hotel_archive_single_item('', '', '', '', '', '', $settings);
										}
										while ($query->have_posts()) {
											$query->the_post();

											$meta = get_post_meta(get_the_ID(), 'tf_hotels_opt', true);
											if ($meta["featured"]) {
												continue;
											}

											$count++;
											$map = !empty($meta['map']) ? Helper::tf_data_types($meta['map']) : '';

											$min_price_arr = HotelPricing::instance(get_the_ID())->get_min_price();
											$min_sale_price = !empty($min_price_arr['min_sale_price']) ? $min_price_arr['min_sale_price'] : 0;
											$min_regular_price = !empty($min_price_arr['min_regular_price']) ? $min_price_arr['min_regular_price'] : 0;
											$min_discount_type = !empty($min_price_arr['min_discount_type']) ? $min_price_arr['min_discount_type'] : 'none';
											$min_discount_amount = !empty($min_price_arr['min_discount_amount']) ? $min_price_arr['min_discount_amount'] : 0;

											if ($min_regular_price != 0) {
												$price_html = wc_format_sale_price($min_regular_price, $min_sale_price);
											} else {
												$price_html = wp_kses_post(wc_price($min_sale_price)) . " ";
											}

											if (!empty($map)) {
												$lat = $map['latitude'];
												$lng = $map['longitude'];
												ob_start();
												?>
												<div class="tf-map-item">
													<div class="tf-map-item-thumb">
														<a href="<?php the_permalink(); ?>">
															<?php
															if (!empty(wp_get_attachment_url(get_post_thumbnail_id(), 'tf_gallery_thumb'))) {
																the_post_thumbnail('full');
															} else {
																echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
															}
															?>
														</a>

														<?php
														if (!empty($min_discount_amount)) : ?>
															<div class="tf-map-item-discount">
																<?php echo $min_discount_type == "percent" ? wp_kses_post($min_discount_amount . '%') : wp_kses_post(wc_price($min_discount_amount)) ?>
																<?php esc_html_e(" Off", "tourfic"); ?>
															</div>
														<?php endif; ?>
													</div>
													<div class="tf-map-item-content">
														<h4>
															<a href="<?php the_permalink(); ?>">
																<?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
															</a>
														</h4>
														<div class="tf-map-item-price">
															<?php echo wp_kses_post(HotelPricing::instance(get_the_ID())->get_min_price_html()); ?>
														</div>
														<?php \Tourfic\App\TF_Review::tf_archive_single_rating('', $settings['design_hotel']); ?>
													</div>
												</div>
												<?php
												$infoWindowtext = ob_get_clean();

												$locations[$count] = [
													'id' => get_the_ID(),
													'url'	  => get_the_permalink(),
													'lat' => (float)$lat,
													'lng' => (float)$lng,
													'price' => base64_encode($price_html),
													'content' => base64_encode($infoWindowtext)
												];
											}
											Hotel::tf_hotel_archive_single_item('', '', '', '', '', '', $settings);
										}
										wp_reset_query();
										?>
										<div id="map-datas" style="display: none"><?php echo array_filter($locations) ? wp_json_encode(array_values($locations)) : wp_json_encode([]); ?></div>
										
										<?php if($show_pagination == 'yes') : ?>
											<div class="tf-pagination-bar">
												<?php Helper::tourfic_posts_navigation($query, $pagination_prev_label, $pagination_next_label); ?>
											</div>
										<?php endif; ?>
									</div>
									<!-- Available rooms end -->

								</div>
								<!-- Available rooms end -->
							</div>
							<div class="tf-details-right tf-archive-right">
								<a href="" class="tf-mobile-list-btn">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
										<path d="M1.33398 7.59935C1.33398 6.82717 1.49514 6.66602 2.26732 6.66602H13.734C14.5062 6.66602 14.6673 6.82717 14.6673 7.59935V8.39935C14.6673 9.17153 14.5062 9.33268 13.734 9.33268H2.26732C1.49514 9.33268 1.33398 9.17153 1.33398 8.39935V7.59935Z"
											stroke="#FEF9F6" stroke-linecap="round"/>
										<path d="M1.33398 2.26634C1.33398 1.49416 1.49514 1.33301 2.26732 1.33301H13.734C14.5062 1.33301 14.6673 1.49416 14.6673 2.26634V3.06634C14.6673 3.83852 14.5062 3.99967 13.734 3.99967H2.26732C1.49514 3.99967 1.33398 3.83852 1.33398 3.06634V2.26634Z"
											stroke="#FEF9F6" stroke-linecap="round"/>
										<path d="M1.33398 12.9333C1.33398 12.1612 1.49514 12 2.26732 12H13.734C14.5062 12 14.6673 12.1612 14.6673 12.9333V13.7333C14.6673 14.5055 14.5062 14.6667 13.734 14.6667H2.26732C1.49514 14.6667 1.33398 14.5055 1.33398 13.7333V12.9333Z"
											stroke="#FEF9F6" stroke-linecap="round"/>
									</svg>
									<span><?php echo esc_html__('List view', 'tourfic') ?></span>
								</a>
								<div id="map-marker" data-marker="<?php echo esc_url(TF_ASSETS_URL . 'app/images/cluster-marker.png'); ?>"></div>
								<div class="tf-hotel-archive-map-wrap">
									<div id="tf-hotel-archive-map"></div>
								</div>
							</div>
						<?php endif; ?>
					<?php else: ?>
						<div class="tf-container">
							<div class="tf-notice tf-mt-24 tf-mb-30">
								<?php
								if (current_user_can('administrator')) {
									echo '<p>' . esc_html__('Google Maps is not selected. Please configure it ', 'tourfic') . '<a href="' . esc_url(admin_url('admin.php?page=tf_settings#tab=map_settings')) . '" target="_blank">' . esc_html__('Map Settings', 'tourfic') . '</a></p>';
								} else {
									echo '<p>' . esc_html__('Access is restricted as Google Maps is not enabled. Please contact the site administrator', 'tourfic') . '</p>';
								}
								?>
							</div>
						</div>
					<?php endif; ?>
					</div>
				</div>
			<?php else: ?>
				<div id="map-datas" style="display: none"><?php echo wp_json_encode([]); ?></div>
				<div class="tf-container">
					<div class="tf-notice tf-mt-24 tf-mb-30">
						<div class="tf-nothing-found" data-post-count="0"><?php echo esc_html__("No Hotels Found!", "tourfic"); ?></div>
					</div>
				</div>
			<?php endif; ?>

			<?php $elementor_settings = $this->tf_search_elementor_settings($settings); ?>
			<div id="tf-elementor-settings" style="display: none"><?php echo !empty($elementor_settings) ? wp_json_encode($elementor_settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : wp_json_encode([]); ?></div>
		</div>
		<?php
	}

	protected function tf_hotel_design_legacy($settings, $query) {
		$post_count = $query->post_count;
		$show_total_result = isset( $settings['show_total_result'] ) ? $settings['show_total_result'] : 'yes';
		$show_sorting = isset( $settings['show_sorting'] ) ? $settings['show_sorting'] : 'yes';
		$grid_column = isset( $settings['grid_column'] ) ? absint($settings['grid_column']) : 2;
		$listing_layout_toggle = isset( $settings['listing_layout_toggle'] ) ? $settings['listing_layout_toggle'] : 'yes';
		if($listing_layout_toggle == 'yes'){
			$listing_layout = isset( $settings['listing_default_layout'] ) ? $settings['listing_default_layout'] : 'list';
		} else {
			$listing_layout = isset( $settings['listing_layout'] ) ? $settings['listing_layout'] : 'list';
		}
		$show_pagination = isset( $settings['show_pagination'] ) ? $settings['show_pagination'] : 'yes';
		$pagination_prev_label = isset( $settings['pagination_prev_label'] ) ? $settings['pagination_prev_label'] : '';
		$pagination_next_label = isset( $settings['pagination_next_label'] ) ? $settings['pagination_next_label'] : '';
		?>
		<div class="tf-archive-listing-wrap tf-archive-listing__legacy" data-design="default">
			<div class="tf-search-left">
				<div class="tf-action-top">
					<?php if($show_total_result == 'yes') : ?>
						<div class="tf-result-counter-info">
							<span class="tf-counter-title"><?php echo esc_html__( 'Total Results', 'tourfic' ); ?> </span>
							<span><?php echo '('; ?> </span>
							<div class="tf-total-results">
								<span><?php echo esc_html( $post_count ); ?> </span>
							</div>
							<span><?php echo ')'; ?> </span>
						</div>
					<?php endif; ?>

					<div class="tf-list-grid">
						<?php if($listing_layout_toggle == 'yes') : ?>
						<a href="#list-view" data-id="list-view" class="change-view <?php echo $listing_layout=="list" ? esc_attr('active') : ''; ?>" title="<?php esc_html_e('List View', 'tourfic'); ?>"><i class="fas fa-list"></i></a>
						<a href="#grid-view" data-id="grid-view" class="change-view <?php echo $listing_layout=="grid" ? esc_attr('active') : ''; ?>" title="<?php esc_html_e('Grid View', 'tourfic'); ?>"><i class="fas fa-border-all"></i></a>
						<?php endif; ?>
						
						<?php if($show_sorting == 'yes') : ?>
						<div class="tf-sorting-selection-warper">
							<form class="tf-archive-ordering" method="get">
								<select class="tf-orderby" name="tf-orderby" id="tf-orderby">
									<option value="default"><?php echo esc_html__( 'Default Sorting', 'tourfic' ); ?></option>
									<option value="enquiry"><?php echo esc_html__( 'Sort By Recommended', 'tourfic' ); ?></option>
									<option value="order"><?php echo esc_html__( 'Sort By Popularity', 'tourfic' ); ?></option>
									<option value="rating"><?php echo esc_html__( 'Sort By Average Rating', 'tourfic' ); ?></option>
									<option value="latest"><?php echo esc_html__( 'Sort By Latest', 'tourfic' ); ?></option>
									<option value="price-high"><?php echo esc_html__( 'Sort By Price: High to Low', 'tourfic' ); ?></option>
									<option value="price-low"><?php echo esc_html__( 'Sort By Price: Low to High', 'tourfic' ); ?></option>
								</select>
							</form>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="archive_ajax_result <?php echo $listing_layout=="grid" ? esc_attr('tours-grid') : '' ?> tf-grid-<?php echo esc_attr($grid_column); ?>">
					<?php
					if ( $query->have_posts() ) {
						while ( $query->have_posts() ) {
							$query->the_post();
							$hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );
							if ( !empty( $hotel_meta[ "featured" ] ) && $hotel_meta[ "featured" ] == 1 ) {
								Hotel::tf_hotel_archive_single_item('', '', '', '', '', '', $settings);
							}
						}
						while ( $query->have_posts() ) {
							$query->the_post();
							$hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );
							if ( empty($hotel_meta[ "featured" ]) ) {
								Hotel::tf_hotel_archive_single_item('', '', '', '', '', '', $settings);
							}
						}
					} else {
						echo '<div class="tf-nothing-found" data-post-count="0">' .esc_html__("No Hotels Found!", "tourfic"). '</div>';
					}
					?>
					
					<?php if($show_pagination == 'yes') : ?>
						<div class="tf_posts_navigation">
							<?php Helper::tourfic_posts_navigation($query, $pagination_prev_label, $pagination_next_label); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<?php $elementor_settings = $this->tf_search_elementor_settings($settings); ?>
			<div id="tf-elementor-settings" style="display: none"><?php echo !empty($elementor_settings) ? wp_json_encode($elementor_settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : wp_json_encode([]); ?></div>
		</div>
		<?php
	}

	protected function tf_tour_design_1($settings, $query) {
		$tf_total_results = 0;
		$show_total_result = isset( $settings['show_total_result'] ) ? $settings['show_total_result'] : 'yes';
		$show_sorting = isset( $settings['show_sorting'] ) ? $settings['show_sorting'] : 'yes';
		$grid_column = isset( $settings['grid_column'] ) ? absint($settings['grid_column']) : 2;
		$listing_layout_toggle = isset( $settings['listing_layout_toggle'] ) ? $settings['listing_layout_toggle'] : 'yes';
		if($listing_layout_toggle == 'yes'){
			$listing_layout = isset( $settings['listing_default_layout'] ) ? $settings['listing_default_layout'] : 'list';
		} else {
			$listing_layout = isset( $settings['listing_layout'] ) ? $settings['listing_layout'] : 'list';
		}
		$show_pagination = isset( $settings['show_pagination'] ) ? $settings['show_pagination'] : 'yes';
		$pagination_prev_label = isset( $settings['pagination_prev_label'] ) ? $settings['pagination_prev_label'] : '';
		$pagination_next_label = isset( $settings['pagination_next_label'] ) ? $settings['pagination_next_label'] : '';
		?>
		<div class="tf-archive-listing-wrap tf-archive-listing__one" data-design="design-1">
			<!-- Search Head Section -->
			<div class="tf-archive-head tf-flex tf-flex-align-center tf-flex-space-bttn">
				<?php if($show_total_result == 'yes') : ?>
				<div class="tf-search-result tf-flex">
					<span class="tf-counter-title"><?php echo esc_html__( 'Total Results ', 'tourfic' ); ?> </span>
					<span><?php echo ' ('; ?> </span>
					<div class="tf-total-results">
						<span><?php echo esc_html($tf_total_results); ?> </span>
					</div>
					<span><?php echo ')'; ?> </span>
				</div>
				<?php endif; ?>

				<div class="tf-search-layout tf-flex tf-flex-gap-12">
					<?php if($listing_layout_toggle == 'yes') : ?>
					<div class="tf-icon tf-serach-layout-list tf-list-active tf-grid-list-layout <?php echo $listing_layout=="list" ? esc_attr('active') : ''; ?>" data-id="list-view">
						<div class="defult-view">
							<svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect width="12" height="2" fill="white"/>
							<rect x="14" width="2" height="2" fill="white"/>
							<rect y="5" width="12" height="2" fill="white"/>
							<rect x="14" y="5" width="2" height="2" fill="white"/>
							<rect y="10" width="12" height="2" fill="white"/>
							<rect x="14" y="10" width="2" height="2" fill="white"/>
							</svg>
						</div>
						<div class="active-view">
							<svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect width="12" height="2" fill="#0E3DD8"/>
							<rect x="14" width="2" height="2" fill="#0E3DD8"/>
							<rect y="5" width="12" height="2" fill="#0E3DD8"/>
							<rect x="14" y="5" width="2" height="2" fill="#0E3DD8"/>
							<rect y="10" width="12" height="2" fill="#0E3DD8"/>
							<rect x="14" y="10" width="2" height="2" fill="#0E3DD8"/>
							</svg>
						</div>
					</div>
					<div class="tf-icon tf-serach-layout-grid tf-grid-list-layout <?php echo $listing_layout=="grid" ? esc_attr('active') : ''; ?>" data-id="grid-view">
						<div class="defult-view">
							<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect x="10" width="2" height="2" fill="#0E3DD8"/>
							<rect x="10" y="5" width="2" height="2" fill="#0E3DD8"/>
							<rect x="10" y="10" width="2" height="2" fill="#0E3DD8"/>
							<rect x="5" width="2" height="2" fill="#0E3DD8"/>
							<rect x="5" y="5" width="2" height="2" fill="#0E3DD8"/>
							<rect x="5" y="10" width="2" height="2" fill="#0E3DD8"/>
							<rect width="2" height="2" fill="#0E3DD8"/>
							<rect y="5" width="2" height="2" fill="#0E3DD8"/>
							<rect y="10" width="2" height="2" fill="#0E3DD8"/>
							</svg>
						</div>
						<div class="active-view">
							<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect x="10" width="2" height="2" fill="white"/>
							<rect x="10" y="5" width="2" height="2" fill="white"/>
							<rect x="10" y="10" width="2" height="2" fill="white"/>
							<rect x="5" width="2" height="2" fill="white"/>
							<rect x="5" y="5" width="2" height="2" fill="white"/>
							<rect x="5" y="10" width="2" height="2" fill="white"/>
							<rect width="2" height="2" fill="white"/>
							<rect y="5" width="2" height="2" fill="white"/>
							<rect y="10" width="2" height="2" fill="white"/>
							</svg>
						</div>
					</div>
					<?php endif; ?>

					<?php if($show_sorting == 'yes') : ?>
					<div class="tf-sorting-selection-warper">
						<form class="tf-archive-ordering" method="get">
							<select class="tf-orderby" name="tf-orderby" id="tf-orderby">
								<option value="default"><?php echo esc_html__( 'Default Sorting', 'tourfic' ); ?></option>
								<option value="enquiry"><?php echo esc_html__( 'Sort By Recommended', 'tourfic' ); ?></option>
								<option value="order"><?php echo esc_html__( 'Sort By Popularity', 'tourfic' ); ?></option>
								<option value="rating"><?php echo esc_html__( 'Sort By Average Rating', 'tourfic' ); ?></option>
								<option value="latest"><?php echo esc_html__( 'Sort By Latest', 'tourfic' ); ?></option>
								<option value="price-high"><?php echo esc_html__( 'Sort By Price: High to Low', 'tourfic' ); ?></option>
								<option value="price-low"><?php echo esc_html__( 'Sort By Price: Low to High', 'tourfic' ); ?></option>
							</select>
						</form>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<!-- Loader Image -->
			<div id="tf_ajax_searchresult_loader">
				<div id="tf-searchresult-loader-img">
					<img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
				</div>
			</div>
			<div class="tf-search-results-list tf-mt-30">
				<div class="archive_ajax_result tf-item-cards tf-flex <?php echo $listing_layout=="list" ? esc_attr('tf-layout-list') : esc_attr('tf-layout-grid'); ?> tf-grid-<?php echo esc_attr($grid_column); ?>">

					<?php
					if ( $query->have_posts() ) {          
						while ( $query->have_posts() ) {
							$query->the_post();
							$tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );
							
							if(!empty($tour_meta["tour_as_featured"])) {
								Tour::tf_tour_archive_single_item('', '', '', '', '', $settings);
								$featured_post_id[] = get_the_ID(); 
							}

							$tf_total_results+=1;
						}
						
						while ( $query->have_posts() ) {
							$query->the_post();
							$tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );
							
							if( empty($tour_meta["tour_as_featured"]) ) {
								Tour::tf_tour_archive_single_item('', '', '', '', '', $settings);
							}
						}
						
					} else {
						echo '<div class="tf-nothing-found" data-post-count="0" >' .esc_html__("No Tours Found!", "tourfic"). '</div>';
					}
					?>
					<span class="tf-posts-count" hidden="hidden">
						<?php echo esc_html($tf_total_results); ?>
					</span>
					<?php if($show_pagination == 'yes') : ?>
						<div class="tf-pagination-bar">
							<?php Helper::tourfic_posts_navigation($query, $pagination_prev_label, $pagination_next_label); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<?php $elementor_settings = $this->tf_search_elementor_settings($settings); ?>
			<div id="tf-elementor-settings" style="display: none"><?php echo !empty($elementor_settings) ? wp_json_encode($elementor_settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : wp_json_encode([]); ?></div>
		</div>
		<?php
	}

	protected function tf_tour_design_2($settings, $query) {
		$show_total_result = isset( $settings['show_total_result'] ) ? $settings['show_total_result'] : 'yes';
		$show_sorting = isset( $settings['show_sorting'] ) ? $settings['show_sorting'] : 'yes';
		$show_pagination = isset( $settings['show_pagination'] ) ? $settings['show_pagination'] : 'yes';
		$pagination_prev_label = isset( $settings['pagination_prev_label'] ) ? $settings['pagination_prev_label'] : '';
		$pagination_next_label = isset( $settings['pagination_next_label'] ) ? $settings['pagination_next_label'] : '';
		$tf_total_results = 0;
		?>
        <div class="tf-archive-listing-wrap tf-archive-listing__two" data-design="design-2">
			<div class="tf-available-archive-hetels-wrapper tf-available-rooms-wrapper" id="tf-hotel-rooms">
				<div class="tf-archive-available-rooms-head tf-available-rooms-head">
					<?php if($show_total_result == 'yes') : ?>
					<span class="tf-total-results"><?php esc_html_e("Total", "tourfic"); ?> <span><?php echo esc_html($tf_total_results); ?></span> <?php esc_html_e("Tours available", "tourfic"); ?></span>
					<?php endif; ?>

					<a class="tf-archive-filter-showing" href="#tf__booking_sidebar">
						<i class="ri-equalizer-line"></i>
					</a>

					<?php if($show_sorting == 'yes') : ?>
					<div class="tf-sorting-selection-warper">
						<form class="tf-archive-ordering" method="get">
							<select class="tf-orderby" name="tf-orderby" id="tf-orderby">
								<option value="default"><?php echo esc_html__( 'Default Sorting', 'tourfic' ); ?></option>
								<option value="enquiry"><?php echo esc_html__( 'Sort By Recommended', 'tourfic' ); ?></option>
								<option value="order"><?php echo esc_html__( 'Sort By Popularity', 'tourfic' ); ?></option>
								<option value="rating"><?php echo esc_html__( 'Sort By Average Rating', 'tourfic' ); ?></option>
								<option value="latest"><?php echo esc_html__( 'Sort By Latest', 'tourfic' ); ?></option>
								<option value="price-high"><?php echo esc_html__( 'Sort By Price: High to Low', 'tourfic' ); ?></option>
								<option value="price-low"><?php echo esc_html__( 'Sort By Price: Low to High', 'tourfic' ); ?></option>
							</select>
						</form>
					</div>
					<?php endif; ?>
				</div>
				
				<!-- Loader Image -->
				<div id="tour_room_details_loader">
					<div id="tour-room-details-loader-img">
						<img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
					</div>
				</div>
				
				<!--Available rooms start -->
				<div class="tf-archive-available-rooms tf-available-rooms archive_ajax_result">

					<?php
					if ( $query->have_posts() ) {          
						while ( $query->have_posts() ) {
							$query->the_post();
							$tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );
							
							if(!empty($tour_meta["tour_as_featured"])) {
								Tour::tf_tour_archive_single_item('', '', '', '', '', $settings);
								$featured_post_id[] = get_the_ID(); 
							}

							$tf_total_results+=1;
						}
						
						while ( $query->have_posts() ) {
							$query->the_post();
							$tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );
							
							if( empty($tour_meta["tour_as_featured"]) ) {
								Tour::tf_tour_archive_single_item('', '', '', '', '', $settings);
							}
						}
					} else {
						echo '<div class="tf-nothing-found" data-post-count="0" >' .esc_html__("No Tours Found!", "tourfic"). '</div>';
					}
					?>
					<span class="tf-posts-count" hidden="hidden">
						<?php echo esc_html($tf_total_results); ?>
					</span>
					<?php if($show_pagination == 'yes') : ?>
						<div class="tf-pagination-bar">
							<?php Helper::tourfic_posts_navigation($query, $pagination_prev_label, $pagination_next_label); ?>
						</div>
					<?php endif; ?>
				</div>
				<!-- Available rooms end -->

			</div>

			<div class="tf-popup-wrapper tf-hotel-popup">
				<div class="tf-popup-inner">
					<div class="tf-popup-body">
						
					</div>
					<div class="tf-popup-close">
						<i class="fa-solid fa-xmark"></i>
					</div>
				</div>
			</div>
			<?php $elementor_settings = $this->tf_search_elementor_settings($settings); ?>
			<div id="tf-elementor-settings" style="display: none"><?php echo !empty($elementor_settings) ? wp_json_encode($elementor_settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : wp_json_encode([]); ?></div>
		</div>
		<?php
	}

	protected function tf_tour_design_3($settings, $query) {
        $post_count = $query->post_count;
        $tf_map_settings = !empty(Helper::tfopt('google-page-option')) ? Helper::tfopt('google-page-option') : "default";
        $tf_map_api = !empty(Helper::tfopt('tf-googlemapapi')) ? Helper::tfopt('tf-googlemapapi') : '';
		$show_total_result = isset( $settings['show_total_result'] ) ? $settings['show_total_result'] : 'yes';
		$grid_column = isset( $settings['grid_column'] ) ? absint($settings['grid_column']) : 2;
		$listing_layout_toggle = isset( $settings['listing_layout_toggle'] ) ? $settings['listing_layout_toggle'] : 'yes';
		if($listing_layout_toggle == 'yes'){
			$listing_layout = isset( $settings['listing_default_layout'] ) ? $settings['listing_default_layout'] : 'list';
		} else {
			$listing_layout = isset( $settings['listing_layout'] ) ? $settings['listing_layout'] : 'list';
		}
		$show_pagination = isset( $settings['show_pagination'] ) ? $settings['show_pagination'] : 'yes';
		$pagination_prev_label = isset( $settings['pagination_prev_label'] ) ? $settings['pagination_prev_label'] : '';
		$pagination_next_label = isset( $settings['pagination_next_label'] ) ? $settings['pagination_next_label'] : '';
		?>
		<div class="tf-archive-listing-wrap tf-archive-listing__three tf-archive-template__three" data-design="design-3">
			<?php if ($query->have_posts()) : ?>
				<div class="tf-archive-details-wrap">
					<div class="tf-archive-details">

						<?php if ($tf_map_settings == "googlemap") :
							if (empty($tf_map_api)):
								?>
								<div class="tf-container">
									<div class="tf-notice tf-mt-24 tf-mb-30">
										<?php
										if (current_user_can('administrator')) {
											echo '<p>' . esc_html__('Google Maps is selected but the API key is missing. Please configure the API key ', 'tourfic') . '<a href="' . esc_url(admin_url('admin.php?page=tf_settings#tab=map_settings')) . '" target="_blank">' . esc_html__('Map Settings', 'tourfic') . '</a></p>';
										} else {
											echo '<p>' . esc_html__('Access is restricted as Google Maps API key is not configured. Please contact the site administrator.', 'tourfic') . '</p>';
										}
										?>
									</div>
								</div>
							<?php else: ?>
								<div class="tf-details-left">
									
									<!-- Loader Image -->
									<div id="tf_ajax_searchresult_loader">
										<div id="tf-searchresult-loader-img">
											<img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
										</div>
									</div>
									<!--Available rooms start -->
									<div class="tf-archive-hotels-wrapper">
										<div class="tf-archive-filter">
											<div class="tf-archive-filter-sidebar">
												<div class="tf-filter-wrapper">
													<div class="tf-filter-title">
														<h4 class="tf-section-title"><?php echo esc_html__("Filter", "tourfic"); ?></h4>
														<button class="filter-reset-btn"><?php echo esc_html__("Reset", "tourfic"); ?></button>
													</div>
													<?php if (is_active_sidebar('tf_archive_booking_sidebar')) { ?>
														<div id="tf__booking_sidebar">
															<?php dynamic_sidebar('tf_archive_booking_sidebar'); ?>
														</div>
													<?php } ?>
													<?php if (is_active_sidebar('tf_map_popup_sidebar')) { ?>
														<div id="tf_map_popup_sidebar">
															<?php dynamic_sidebar('tf_map_popup_sidebar'); ?>
														</div>
													<?php } ?>
												</div>
											</div>
										</div>
										<div class="tf-archive-top">
											<?php if($show_total_result == 'yes') : ?>
											<h5 class="tf-total-results"><?php esc_html_e("Found", "tourfic"); ?>
												<span class="tf-map-item-count"><?php echo esc_html($post_count); ?></span> <?php esc_html_e("of", "tourfic"); ?> <?php echo esc_html($query->found_posts); ?> <?php esc_html_e("Tours", "tourfic"); ?></h5>
											<?php endif; ?>

											<a href="" class="tf-mobile-map-btn">
												<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
													<path d="M17.3327 7.33366V6.68156C17.3327 5.06522 17.3327 4.25705 16.8445 3.75491C16.3564 3.25278 15.5707 3.25278 13.9993 3.25278H12.2671C11.5027 3.25278 11.4964 3.25129 10.8089 2.90728L8.03258 1.51794C6.87338 0.93786 6.29378 0.647818 5.67633 0.667975C5.05888 0.688132 4.49833 1.01539 3.37722 1.66992L2.354 2.2673C1.5305 2.74807 1.11876 2.98846 0.892386 3.38836C0.666016 3.78827 0.666016 4.27527 0.666016 5.24927V12.0968C0.666016 13.3765 0.666016 14.0164 0.951234 14.3725C1.14102 14.6095 1.40698 14.7688 1.70102 14.8216C2.1429 14.901 2.68392 14.5851 3.76591 13.9534C4.50065 13.5245 5.20777 13.079 6.08674 13.1998C6.82326 13.301 7.50768 13.7657 8.16602 14.0952"
														stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
													<path d="M5.66602 0.666992L5.66601 13.167" stroke="white" stroke-linejoin="round"/>
													<path d="M11.5 3.16699V6.91699" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
													<path d="M14.2556 17.0696C14.075 17.2388 13.8334 17.3333 13.5821 17.3333C13.3308 17.3333 13.0893 17.2388 12.9086 17.0696C11.254 15.5108 9.0366 13.7695 10.1179 11.2415C10.7026 9.87465 12.1061 9 13.5821 9C15.0581 9 16.4616 9.87465 17.0463 11.2415C18.1263 13.7664 15.9143 15.5162 14.2556 17.0696Z"
														stroke="white"/>
													<path d="M13.582 12.75H13.5895" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
												<span><?php echo esc_html__('Map', 'tourfic') ?></span>
											</a>

											<ul class="tf-archive-view">
												<li class="tf-archive-filter-btn">
													<i class="ri-equalizer-line"></i>
													<span><?php esc_html_e("All Filter", "tourfic"); ?></span>
												</li>
												<?php if($listing_layout_toggle == 'yes') : ?>
												<li class="tf-archive-view-item tf-archive-list-view <?php echo $listing_layout == "list" ? esc_attr('active') : ''; ?>" data-id="list-view">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
														<path d="M1.33398 7.59996C1.33398 6.82778 1.49514 6.66663 2.26732 6.66663H13.734C14.5062 6.66663 14.6673 6.82778 14.6673 7.59996V8.39996C14.6673 9.17214 14.5062 9.33329 13.734 9.33329H2.26732C1.49514 9.33329 1.33398 9.17214 1.33398 8.39996V7.59996Z"
															stroke="#6E655E" stroke-linecap="round"/>
														<path d="M1.33398 2.26665C1.33398 1.49447 1.49514 1.33331 2.26732 1.33331H13.734C14.5062 1.33331 14.6673 1.49447 14.6673 2.26665V3.06665C14.6673 3.83882 14.5062 3.99998 13.734 3.99998H2.26732C1.49514 3.99998 1.33398 3.83882 1.33398 3.06665V2.26665Z"
															stroke="#6E655E" stroke-linecap="round"/>
														<path d="M1.33398 12.9333C1.33398 12.1612 1.49514 12 2.26732 12H13.734C14.5062 12 14.6673 12.1612 14.6673 12.9333V13.7333C14.6673 14.5055 14.5062 14.6667 13.734 14.6667H2.26732C1.49514 14.6667 1.33398 14.5055 1.33398 13.7333V12.9333Z"
															stroke="#6E655E" stroke-linecap="round"/>
													</svg>
												</li>
												<li class="tf-archive-view-item tf-archive-grid-view <?php echo $listing_layout == "grid" ? esc_attr('active') : ''; ?>" data-id="grid-view">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
														<path d="M1.33398 12C1.33398 10.973 1.33398 10.4595 1.56514 10.0823C1.69448 9.87127 1.87194 9.69381 2.08301 9.56447C2.46021 9.33331 2.97369 9.33331 4.00065 9.33331C5.02761 9.33331 5.54109 9.33331 5.9183 9.56447C6.12936 9.69381 6.30682 9.87127 6.43616 10.0823C6.66732 10.4595 6.66732 10.973 6.66732 12C6.66732 13.0269 6.66732 13.5404 6.43616 13.9176C6.30682 14.1287 6.12936 14.3062 5.9183 14.4355C5.54109 14.6666 5.02761 14.6666 4.00065 14.6666C2.97369 14.6666 2.46021 14.6666 2.08301 14.4355C1.87194 14.3062 1.69448 14.1287 1.56514 13.9176C1.33398 13.5404 1.33398 13.0269 1.33398 12Z"
															stroke="#6E655E" stroke-width="1.2"/>
														<path d="M9.33398 12C9.33398 10.973 9.33398 10.4595 9.56514 10.0823C9.69448 9.87127 9.87194 9.69381 10.083 9.56447C10.4602 9.33331 10.9737 9.33331 12.0007 9.33331C13.0276 9.33331 13.5411 9.33331 13.9183 9.56447C14.1294 9.69381 14.3068 9.87127 14.4362 10.0823C14.6673 10.4595 14.6673 10.973 14.6673 12C14.6673 13.0269 14.6673 13.5404 14.4362 13.9176C14.3068 14.1287 14.1294 14.3062 13.9183 14.4355C13.5411 14.6666 13.0276 14.6666 12.0007 14.6666C10.9737 14.6666 10.4602 14.6666 10.083 14.4355C9.87194 14.3062 9.69448 14.1287 9.56514 13.9176C9.33398 13.5404 9.33398 13.0269 9.33398 12Z"
															stroke="#6E655E" stroke-width="1.2"/>
														<path d="M1.33398 3.99998C1.33398 2.97302 1.33398 2.45954 1.56514 2.08233C1.69448 1.87127 1.87194 1.69381 2.08301 1.56447C2.46021 1.33331 2.97369 1.33331 4.00065 1.33331C5.02761 1.33331 5.54109 1.33331 5.9183 1.56447C6.12936 1.69381 6.30682 1.87127 6.43616 2.08233C6.66732 2.45954 6.66732 2.97302 6.66732 3.99998C6.66732 5.02694 6.66732 5.54042 6.43616 5.91762C6.30682 6.12869 6.12936 6.30615 5.9183 6.43549C5.54109 6.66665 5.02761 6.66665 4.00065 6.66665C2.97369 6.66665 2.46021 6.66665 2.08301 6.43549C1.87194 6.30615 1.69448 6.12869 1.56514 5.91762C1.33398 5.54042 1.33398 5.02694 1.33398 3.99998Z"
															stroke="#6E655E" stroke-width="1.2"/>
														<path d="M9.33398 3.99998C9.33398 2.97302 9.33398 2.45954 9.56514 2.08233C9.69448 1.87127 9.87194 1.69381 10.083 1.56447C10.4602 1.33331 10.9737 1.33331 12.0007 1.33331C13.0276 1.33331 13.5411 1.33331 13.9183 1.56447C14.1294 1.69381 14.3068 1.87127 14.4362 2.08233C14.6673 2.45954 14.6673 2.97302 14.6673 3.99998C14.6673 5.02694 14.6673 5.54042 14.4362 5.91762C14.3068 6.12869 14.1294 6.30615 13.9183 6.43549C13.5411 6.66665 13.0276 6.66665 12.0007 6.66665C10.9737 6.66665 10.4602 6.66665 10.083 6.43549C9.87194 6.30615 9.69448 6.12869 9.56514 5.91762C9.33398 5.54042 9.33398 5.02694 9.33398 3.99998Z"
															stroke="#6E655E" stroke-width="1.2"/>
													</svg>
												</li>
												<?php endif; ?>
											</ul>
										</div>

										<!--Available rooms start -->
										<div class="tf-archive-hotels archive_ajax_result <?php echo $listing_layout == "list" ? esc_attr('tf-layout-list') : esc_attr('tf-layout-grid'); ?> tf-grid-<?php echo esc_attr($grid_column); ?>">

											<?php
											$count = 0;
											$locations = [];
											while ($query->have_posts()) {
												$query->the_post();

												$meta = get_post_meta(get_the_ID(), 'tf_tours_opt', true);
												if (!$meta["tour_as_featured"]) {
													continue;
												}

												$count++;
												$map = !empty($meta['location']) ? Helper::tf_data_types($meta['location']) : '';
												$discount_type = !empty($meta['discount_type']) ? $meta['discount_type'] : '';
												$discount_price = !empty($meta['discount_price']) ? $meta['discount_price'] : '';

												$min_price_arr = TourPricing::instance(get_the_ID())->get_min_price();
												$min_sale_price = !empty($min_price_arr['min_sale_price']) ? $min_price_arr['min_sale_price'] : 0;
												$min_regular_price = !empty($min_price_arr['min_regular_price']) ? $min_price_arr['min_regular_price'] : 0;
												$min_discount = !empty($min_price_arr['min_discount']) ? $min_price_arr['min_discount'] : 0;

												if (!empty($min_discount)) {
													$price_html = wc_format_sale_price($min_regular_price, $min_sale_price);
												} else {
													$price_html = wp_kses_post(wc_price($min_sale_price)) . " ";
												}

												if (!empty($map)) {
													$lat = $map['latitude'];
													$lng = $map['longitude'];
													ob_start();
													?>
													<div class="tf-map-item">
														<div class="tf-map-item-thumb">
															<a href="<?php the_permalink(); ?>">
																<?php
																if (!empty(wp_get_attachment_url(get_post_thumbnail_id(), 'tf_gallery_thumb'))) {
																	the_post_thumbnail('full');
																} else {
																	echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
																}
																?>
															</a>

															<?php if ($discount_type !== 'none' && !empty($discount_price)) : ?>
																<div class="tf-map-item-discount">
																	<?php echo $discount_type == "percent" ? wp_kses_post($discount_price . '%') : wp_kses_post(wc_price($discount_price)) ?>
																	<?php esc_html_e(" Off", "tourfic"); ?>
																</div>
															<?php endif; ?>
														</div>
														<div class="tf-map-item-content">
															<h4>
																<a href="<?php the_permalink(); ?>">
																	<?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
																</a>
															</h4>
															<div class="tf-map-item-price">
																<?php echo wp_kses_post(TourPricing::instance(get_the_ID())->get_min_price_html()); ?>
															</div>
															<?php \Tourfic\App\TF_Review::tf_archive_single_rating('', $settings['design_tours']); ?>
														</div>
													</div>
													<?php
													$infoWindowtext = ob_get_clean();

													$locations[$count] = [
														'id' => get_the_ID(),
														'url'	  => get_the_permalink(),
														'lat' => (float)$lat,
														'lng' => (float)$lng,
														'price' => base64_encode($price_html),
														'content' => base64_encode($infoWindowtext)
													];
												}
												Tour::tf_tour_archive_single_item('', '', '', '', '', $settings);
											}
											while ($query->have_posts()) {
												$query->the_post();

												$meta = get_post_meta(get_the_ID(), 'tf_tours_opt', true);
												if ($meta["tour_as_featured"]) {
													continue;
												}

												$count++;
												$map = !empty($meta['location']) ? Helper::tf_data_types($meta['location']) : '';
												$discount_type = !empty($meta['discount_type']) ? $meta['discount_type'] : '';
												$discount_price = !empty($meta['discount_price']) ? $meta['discount_price'] : '';

												$min_price_arr = TourPricing::instance(get_the_ID())->get_min_price();
												$min_sale_price = !empty($min_price_arr['min_sale_price']) ? $min_price_arr['min_sale_price'] : 0;
												$min_regular_price = !empty($min_price_arr['min_regular_price']) ? $min_price_arr['min_regular_price'] : 0;
												$min_discount = !empty($min_price_arr['min_discount']) ? $min_price_arr['min_discount'] : 0;

												if (!empty($min_discount)) {
													$price_html = wc_format_sale_price($min_regular_price, $min_sale_price);
												} else {
													$price_html = wp_kses_post(wc_price($min_sale_price)) . " ";
												}

												if (!empty($map)) {
													$lat = $map['latitude'];
													$lng = $map['longitude'];
													ob_start();
													?>
													<div class="tf-map-item" data-price="<?php //echo esc_attr( wc_price( $min_sale_price ) ); ?>">
														<div class="tf-map-item-thumb">
															<a href="<?php the_permalink(); ?>">
																<?php
																if (!empty(wp_get_attachment_url(get_post_thumbnail_id(), 'tf_gallery_thumb'))) {
																	the_post_thumbnail('full');
																} else {
																	echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
																}
																?>
															</a>

															<?php if ($discount_type !== 'none' && !empty($discount_price)) : ?>
																<div class="tf-map-item-discount">
																	<?php echo $discount_type == "percent" ? wp_kses_post($discount_price . '%') : wp_kses_post(wc_price($discount_price)) ?>
																	<?php esc_html_e(" Off", "tourfic"); ?>
																</div>
															<?php endif; ?>
														</div>
														<div class="tf-map-item-content">
															<h4>
																<a href="<?php the_permalink(); ?>">
																	<?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
																</a>
															</h4>
															<div class="tf-map-item-price">
																<?php echo wp_kses_post(TourPricing::instance(get_the_ID())->get_min_price_html()); ?>
															</div>
															<?php \Tourfic\App\TF_Review::tf_archive_single_rating('', $settings['design_tours']); ?>
														</div>
													</div>
													<?php
													$infoWindowtext = ob_get_clean();

													$locations[$count] = [
														'id' => get_the_ID(),
														'url'	  => get_the_permalink(),
														'lat' => (float)$lat,
														'lng' => (float)$lng,
														'price' => base64_encode($price_html),
														'content' => base64_encode($infoWindowtext)
													];
												}
												Tour::tf_tour_archive_single_item('', '', '', '', '', $settings);
											}
											wp_reset_query();
											?>
											<div id="map-datas" style="display: none"><?php echo array_filter($locations) ? wp_json_encode(array_values($locations)) : []; ?></div>
											
											<?php if($show_pagination == 'yes') : ?>
												<div class="tf-pagination-bar">
													<?php Helper::tourfic_posts_navigation($query, $pagination_prev_label, $pagination_next_label); ?>
												</div>
											<?php endif; ?>
										</div>

									</div>
								</div>
								<div class="tf-details-right tf-archive-right">
									<a href="" class="tf-mobile-list-btn">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
											<path d="M1.33398 7.59935C1.33398 6.82717 1.49514 6.66602 2.26732 6.66602H13.734C14.5062 6.66602 14.6673 6.82717 14.6673 7.59935V8.39935C14.6673 9.17153 14.5062 9.33268 13.734 9.33268H2.26732C1.49514 9.33268 1.33398 9.17153 1.33398 8.39935V7.59935Z"
												stroke="#FEF9F6" stroke-linecap="round"/>
											<path d="M1.33398 2.26634C1.33398 1.49416 1.49514 1.33301 2.26732 1.33301H13.734C14.5062 1.33301 14.6673 1.49416 14.6673 2.26634V3.06634C14.6673 3.83852 14.5062 3.99967 13.734 3.99967H2.26732C1.49514 3.99967 1.33398 3.83852 1.33398 3.06634V2.26634Z"
												stroke="#FEF9F6" stroke-linecap="round"/>
											<path d="M1.33398 12.9333C1.33398 12.1612 1.49514 12 2.26732 12H13.734C14.5062 12 14.6673 12.1612 14.6673 12.9333V13.7333C14.6673 14.5055 14.5062 14.6667 13.734 14.6667H2.26732C1.49514 14.6667 1.33398 14.5055 1.33398 13.7333V12.9333Z"
												stroke="#FEF9F6" stroke-linecap="round"/>
										</svg>
										<span><?php echo esc_html__('List view', 'tourfic') ?></span>
									</a>
									<div id="map-marker" data-marker="<?php echo esc_url(TF_ASSETS_URL . 'app/images/cluster-marker.png'); ?>"></div>
									<div class="tf-hotel-archive-map-wrap">
										<div id="tf-hotel-archive-map"></div>
									</div>
								</div>
							<?php endif; ?>
						<?php else: ?>
							<div class="tf-container">
								<div class="tf-notice tf-mt-24 tf-mb-30">
									<?php
									if (current_user_can('administrator')) {
										echo '<p>' . esc_html__('Google Maps is not selected. Please configure it ', 'tourfic') . '<a href="' . esc_url(admin_url('admin.php?page=tf_settings#tab=map_settings')) . '" target="_blank">' . esc_html__('Map Settings', 'tourfic') . '</a></p>';
									} else {
										echo '<p>' . esc_html__('Access is restricted as Google Maps is not enabled. Please contact the site administrator', 'tourfic') . '</p>';
									}
									?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php else: ?>
				<div id="map-datas" style="display: none"><?php echo wp_json_encode([]); ?></div>
				<div class="tf-container">
					<div class="tf-notice tf-mt-24 tf-mb-30">
						<div class="tf-nothing-found" data-post-count="0"><?php echo esc_html__("No Tours Found!", "tourfic"); ?></div>
					</div>
				</div>
			<?php endif; ?>
			<?php $elementor_settings = $this->tf_search_elementor_settings($settings); ?>
			<div id="tf-elementor-settings" style="display: none"><?php echo !empty($elementor_settings) ? wp_json_encode($elementor_settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : wp_json_encode([]); ?></div>
		</div>
		<?php
	}

	protected function tf_tour_design_legacy($settings, $query) {
		$tf_total_results = 0;
		$show_total_result = isset( $settings['show_total_result'] ) ? $settings['show_total_result'] : 'yes';
		$show_sorting = isset( $settings['show_sorting'] ) ? $settings['show_sorting'] : 'yes';
		$grid_column = isset( $settings['grid_column'] ) ? absint($settings['grid_column']) : 2;
		$listing_layout_toggle = isset( $settings['listing_layout_toggle'] ) ? $settings['listing_layout_toggle'] : 'yes';
		if($listing_layout_toggle == 'yes'){
			$listing_layout = isset( $settings['listing_default_layout'] ) ? $settings['listing_default_layout'] : 'list';
		} else {
			$listing_layout = isset( $settings['listing_layout'] ) ? $settings['listing_layout'] : 'list';
		}
		$show_pagination = isset( $settings['show_pagination'] ) ? $settings['show_pagination'] : 'yes';
		$pagination_prev_label = isset( $settings['pagination_prev_label'] ) ? $settings['pagination_prev_label'] : '';
		$pagination_next_label = isset( $settings['pagination_next_label'] ) ? $settings['pagination_next_label'] : '';
		?>
		<div class="tf-archive-listing-wrap tf-archive-listing__legacy" data-design="default">
			<div class="tf-search-left">				
				<div class="tf-action-top">
					<?php if($show_total_result == 'yes') : ?>
					<div class="tf-result-counter-info">
						<span class="tf-counter-title"><?php echo esc_html__( 'Total Results', 'tourfic' ); ?> </span>
						<span><?php echo '('; ?> </span>
						<div class="tf-total-results">
							<span><?php echo esc_html($tf_total_results); ?> </span>
						</div>
						<span><?php echo ')'; ?> </span>
					</div>
					<?php endif; ?>

		            <div class="tf-list-grid">
						<?php if($listing_layout_toggle == 'yes') : ?>
		                <a href="#list-view" data-id="list-view" class="change-view <?php echo $listing_layout=="list" ? esc_attr('active') : ''; ?>" title="<?php esc_html_e('List View', 'tourfic'); ?>"><i class="fas fa-list"></i></a>
		                <a href="#grid-view" data-id="grid-view" class="change-view <?php echo $listing_layout=="grid" ? esc_attr('active') : ''; ?>" title="<?php esc_html_e('Grid View', 'tourfic'); ?>"><i class="fas fa-border-all"></i></a>
						<?php endif; ?>

						<?php if($show_sorting == 'yes') : ?>
						<div class="tf-sorting-selection-warper">
                            <form class="tf-archive-ordering" method="get">
                                <select class="tf-orderby" name="tf-orderby" id="tf-orderby">
                                    <option value="default"><?php echo esc_html__( 'Default Sorting', 'tourfic' ); ?></option>
                                    <option value="enquiry"><?php echo esc_html__( 'Sort By Recommended', 'tourfic' ); ?></option>
                                    <option value="order"><?php echo esc_html__( 'Sort By Popularity', 'tourfic' ); ?></option>
                                    <option value="rating"><?php echo esc_html__( 'Sort By Average Rating', 'tourfic' ); ?></option>
                                    <option value="latest"><?php echo esc_html__( 'Sort By Latest', 'tourfic' ); ?></option>
                                    <option value="price-high"><?php echo esc_html__( 'Sort By Price: High to Low', 'tourfic' ); ?></option>
                                    <option value="price-low"><?php echo esc_html__( 'Sort By Price: Low to High', 'tourfic' ); ?></option>
                                </select>
                            </form>
                        </div>
						<?php endif; ?>
		            </div>
		        </div>
				<div class="archive_ajax_result <?php echo $listing_layout=="grid" ? esc_attr('tours-grid') : '' ?> tf-grid-<?php echo esc_attr($grid_column); ?>">
					<?php
                    if ( $query->have_posts() ) {          
                        while ( $query->have_posts() ) {
                            $query->the_post();
                            $tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );
                            
                            if(!empty($tour_meta["tour_as_featured"])) {
                                Tour::tf_tour_archive_single_item('', '', '', '', '', $settings);
                                $featured_post_id[] = get_the_ID(); 
                            }

                            $tf_total_results+=1;
                        }
						
                        while ( $query->have_posts() ) {
                            $query->the_post();
                            $tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );
                            
                            if( empty($tour_meta["tour_as_featured"]) ) {
                                Tour::tf_tour_archive_single_item('', '', '', '', '', $settings);
                            }
                        }
					} else {
						echo '<div class="tf-nothing-found" data-post-count="0" >' .esc_html__("No Tours Found!", "tourfic"). '</div>';
					}
					?>
					<span class="tf-posts-count" hidden="hidden">
					<?php echo esc_html($tf_total_results); ?>
					</span>
					<?php if($show_pagination == 'yes') : ?>
						<div class="tf_posts_navigation">
							<?php Helper::tourfic_posts_navigation($query, $pagination_prev_label, $pagination_next_label); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<?php $elementor_settings = $this->tf_search_elementor_settings($settings); ?>
			<div id="tf-elementor-settings" style="display: none"><?php echo !empty($elementor_settings) ? wp_json_encode($elementor_settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : wp_json_encode([]); ?></div>
		</div>
		<?php
	}

	protected function tf_apartment_design_1($settings, $query) {
		$post_count = $query->post_count;
		$show_total_result = isset( $settings['show_total_result'] ) ? $settings['show_total_result'] : 'yes';
		$show_sorting = isset( $settings['show_sorting'] ) ? $settings['show_sorting'] : 'yes';
		$show_pagination = isset( $settings['show_pagination'] ) ? $settings['show_pagination'] : 'yes';
		$pagination_prev_label = isset( $settings['pagination_prev_label'] ) ? $settings['pagination_prev_label'] : '';
		$pagination_next_label = isset( $settings['pagination_next_label'] ) ? $settings['pagination_next_label'] : '';
		?>
        <div class="tf-archive-listing-wrap tf-archive-listing__two" data-design="design-1">
			<div class="tf-available-archive-hetels-wrapper tf-available-rooms-wrapper" id="tf-hotel-rooms">
				<div class="tf-archive-available-rooms-head tf-available-rooms-head">
					<?php if($show_total_result == 'yes') : ?>
					<span class="tf-total-results"><?php esc_html_e("Total", "tourfic"); ?> <span><?php echo esc_html( $post_count ); ?></span> <?php esc_html_e("apartments available", "tourfic"); ?></span>
					<?php endif; ?>

					<a class="tf-archive-filter-showing" href="#tf__booking_sidebar">
						<i class="ri-equalizer-line"></i>
					</a>
					
					<?php if($show_sorting == 'yes') : ?>
					<div class="tf-sorting-selection-warper">
						<form class="tf-archive-ordering" method="get">
							<select class="tf-orderby" name="tf-orderby" id="tf-orderby">
								<option value="default"><?php echo esc_html__( 'Default Sorting', 'tourfic' ); ?></option>
								<option value="enquiry"><?php echo esc_html__( 'Sort By Recommended', 'tourfic' ); ?></option>
								<option value="order"><?php echo esc_html__( 'Sort By Popularity', 'tourfic' ); ?></option>
								<option value="rating"><?php echo esc_html__( 'Sort By Average Rating', 'tourfic' ); ?></option>
								<option value="latest"><?php echo esc_html__( 'Sort By Latest', 'tourfic' ); ?></option>
								<option value="price-high"><?php echo esc_html__( 'Sort By Price: High to Low', 'tourfic' ); ?></option>
								<option value="price-low"><?php echo esc_html__( 'Sort By Price: Low to High', 'tourfic' ); ?></option>
							</select>
						</form>
					</div>
					<?php endif; ?>

				</div>
				
				<!-- Loader Image -->
				<div id="tour_room_details_loader">
					<div id="tour-room-details-loader-img">
						<img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
					</div>
				</div>

				<?php do_action("tf_apartment_archive_card_items_before"); ?>
				
				<!--Available rooms start -->
				<div class="tf-archive-available-rooms tf-available-rooms archive_ajax_result">

					<?php
					if ( $query->have_posts() ) {
						while ( $query->have_posts() ) {
							$query->the_post();
							$apartment_meta = get_post_meta( get_the_ID() , 'tf_apartment_opt', true );
							if ( !empty($apartment_meta[ "apartment_as_featured" ] )) {
								echo wp_kses(apply_filters("tf_apartment_archive_single_featured_card_design_one", Apartment::tf_apartment_archive_single_item([ 1, 0, 0, '' ], $settings)), Helper::tf_custom_wp_kses_allow_tags());
							}
						} 
						while ( $query->have_posts() ) {
							$query->the_post();
							$apartment_meta = get_post_meta( get_the_ID() , 'tf_apartment_opt', true );
							if ( empty($apartment_meta[ "apartment_as_featured" ] )) {
								echo wp_kses(apply_filters("tf_apartment_archive_single_card_design_one", Apartment::tf_apartment_archive_single_item([ 1, 0, 0, '' ], $settings)), Helper::tf_custom_wp_kses_allow_tags());
							}
						}
					} else {
						echo '<div class="tf-nothing-found" data-post-count="0" >' .esc_html__("No Apartments Found!", "tourfic"). '</div>';
					}
					?>
					<?php if($show_pagination == 'yes') : ?>
						<div class="tf-pagination-bar">
							<?php Helper::tourfic_posts_navigation($query, $pagination_prev_label, $pagination_next_label); ?>
						</div>
					<?php endif; ?>
				</div>
				<!-- Available rooms end -->

				<?php do_action("tf_apartment_archive_card_items_after"); ?>

			</div>
			<?php $elementor_settings = $this->tf_search_elementor_settings($settings); ?>
			<div id="tf-elementor-settings" style="display: none"><?php echo !empty($elementor_settings) ? wp_json_encode($elementor_settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : wp_json_encode([]); ?></div>
		</div>
		<?php
	}

	protected function tf_apartment_design_2($settings, $query) {
        $post_count = $query->post_count;
        $tf_map_settings = !empty(Helper::tfopt('google-page-option')) ? Helper::tfopt('google-page-option') : "default";
        $tf_map_api = !empty(Helper::tfopt('tf-googlemapapi')) ? Helper::tfopt('tf-googlemapapi') : '';
		$show_total_result = isset( $settings['show_total_result'] ) ? $settings['show_total_result'] : 'yes';
		$grid_column = isset( $settings['grid_column'] ) ? absint($settings['grid_column']) : 2;
		$listing_layout_toggle = isset( $settings['listing_layout_toggle'] ) ? $settings['listing_layout_toggle'] : 'yes';
		if($listing_layout_toggle == 'yes'){
			$listing_layout = isset( $settings['listing_default_layout'] ) ? $settings['listing_default_layout'] : 'list';
		} else {
			$listing_layout = isset( $settings['listing_layout'] ) ? $settings['listing_layout'] : 'list';
		}
		$show_pagination = isset( $settings['show_pagination'] ) ? $settings['show_pagination'] : 'yes';
		$pagination_prev_label = isset( $settings['pagination_prev_label'] ) ? $settings['pagination_prev_label'] : '';
		$pagination_next_label = isset( $settings['pagination_next_label'] ) ? $settings['pagination_next_label'] : '';
		?>
		<div class="tf-archive-listing-wrap tf-archive-listing__three tf-archive-template__three" data-design="design-3">
			<?php if ($query->have_posts()) : ?>
				<div class="tf-archive-details-wrap">
					<div class="tf-archive-details">
						
						<?php if ($tf_map_settings == "googlemap") :
							if (empty($tf_map_api)):
								?>
								<div class="tf-container">
									<div class="tf-notice tf-mt-24 tf-mb-30">
										<?php
										if (current_user_can('administrator')) {
											echo '<p>' . esc_html__('Google Maps is selected but the API key is missing. Please configure the API key ', 'tourfic') . '<a href="' . esc_url(admin_url('admin.php?page=tf_settings#tab=map_settings')) . '" target="_blank">' . esc_html__('Map Settings', 'tourfic') . '</a></p>';
										} else {
											echo '<p>' . esc_html__('Access is restricted as Google Maps API key is not configured. Please contact the site administrator.', 'tourfic') . '</p>';
										}
										?>
									</div>
								</div>
							<?php else: ?>
								<div class="tf-details-left">
									<!-- Loader Image -->
									<div id="tf_ajax_searchresult_loader">
										<div id="tf-searchresult-loader-img">
											<img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
										</div>
									</div>
									<!--Available rooms start -->
									<div class="tf-archive-hotels-wrapper">
										<div class="tf-archive-filter">
											<div class="tf-archive-filter-sidebar">
												<div class="tf-filter-wrapper">
													<div class="tf-filter-title">
														<h4 class="tf-section-title"><?php echo esc_html__("Filter", "tourfic"); ?></h4>
														<button class="filter-reset-btn"><?php echo esc_html__("Reset", "tourfic"); ?></button>
													</div>
													<?php if (is_active_sidebar('tf_archive_booking_sidebar')) { ?>
														<div id="tf__booking_sidebar">
															<?php dynamic_sidebar('tf_archive_booking_sidebar'); ?>
														</div>
													<?php } ?>
													<?php if (is_active_sidebar('tf_map_popup_sidebar')) { ?>
														<div id="tf_map_popup_sidebar">
															<?php dynamic_sidebar('tf_map_popup_sidebar'); ?>
														</div>
													<?php } ?>
												</div>
											</div>
										</div>
										<div class="tf-archive-top">
											<?php if($show_total_result == 'yes') : ?>
											<h5 class="tf-total-results"><?php esc_html_e("Found", "tourfic"); ?>
												<span class="tf-map-item-count"><?php echo esc_html($post_count); ?></span> <?php esc_html_e("of", "tourfic"); ?> <?php echo esc_html($query->found_posts); ?> <?php esc_html_e("Apartments", "tourfic"); ?></h5>
											<?php endif; ?>

											<a href="" class="tf-mobile-map-btn">
												<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
													<path d="M17.3327 7.33366V6.68156C17.3327 5.06522 17.3327 4.25705 16.8445 3.75491C16.3564 3.25278 15.5707 3.25278 13.9993 3.25278H12.2671C11.5027 3.25278 11.4964 3.25129 10.8089 2.90728L8.03258 1.51794C6.87338 0.93786 6.29378 0.647818 5.67633 0.667975C5.05888 0.688132 4.49833 1.01539 3.37722 1.66992L2.354 2.2673C1.5305 2.74807 1.11876 2.98846 0.892386 3.38836C0.666016 3.78827 0.666016 4.27527 0.666016 5.24927V12.0968C0.666016 13.3765 0.666016 14.0164 0.951234 14.3725C1.14102 14.6095 1.40698 14.7688 1.70102 14.8216C2.1429 14.901 2.68392 14.5851 3.76591 13.9534C4.50065 13.5245 5.20777 13.079 6.08674 13.1998C6.82326 13.301 7.50768 13.7657 8.16602 14.0952"
														stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
													<path d="M5.66602 0.666992L5.66601 13.167" stroke="white" stroke-linejoin="round"/>
													<path d="M11.5 3.16699V6.91699" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
													<path d="M14.2556 17.0696C14.075 17.2388 13.8334 17.3333 13.5821 17.3333C13.3308 17.3333 13.0893 17.2388 12.9086 17.0696C11.254 15.5108 9.0366 13.7695 10.1179 11.2415C10.7026 9.87465 12.1061 9 13.5821 9C15.0581 9 16.4616 9.87465 17.0463 11.2415C18.1263 13.7664 15.9143 15.5162 14.2556 17.0696Z"
														stroke="white"/>
													<path d="M13.582 12.75H13.5895" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
												<span><?php echo esc_html__('Map', 'tourfic') ?></span>
											</a>

											<ul class="tf-archive-view">
												<li class="tf-archive-filter-btn">
													<i class="ri-equalizer-line"></i>
													<span><?php esc_html_e("All Filter", "tourfic"); ?></span>
												</li>
												<?php if($listing_layout_toggle == 'yes') : ?>
												<li class="tf-archive-view-item tf-archive-list-view <?php echo $listing_layout == "list" ? esc_attr('active') : ''; ?>" data-id="list-view">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
														<path d="M1.33398 7.59996C1.33398 6.82778 1.49514 6.66663 2.26732 6.66663H13.734C14.5062 6.66663 14.6673 6.82778 14.6673 7.59996V8.39996C14.6673 9.17214 14.5062 9.33329 13.734 9.33329H2.26732C1.49514 9.33329 1.33398 9.17214 1.33398 8.39996V7.59996Z"
															stroke="#6E655E" stroke-linecap="round"/>
														<path d="M1.33398 2.26665C1.33398 1.49447 1.49514 1.33331 2.26732 1.33331H13.734C14.5062 1.33331 14.6673 1.49447 14.6673 2.26665V3.06665C14.6673 3.83882 14.5062 3.99998 13.734 3.99998H2.26732C1.49514 3.99998 1.33398 3.83882 1.33398 3.06665V2.26665Z"
															stroke="#6E655E" stroke-linecap="round"/>
														<path d="M1.33398 12.9333C1.33398 12.1612 1.49514 12 2.26732 12H13.734C14.5062 12 14.6673 12.1612 14.6673 12.9333V13.7333C14.6673 14.5055 14.5062 14.6667 13.734 14.6667H2.26732C1.49514 14.6667 1.33398 14.5055 1.33398 13.7333V12.9333Z"
															stroke="#6E655E" stroke-linecap="round"/>
													</svg>
												</li>
												<li class="tf-archive-view-item tf-archive-grid-view <?php echo $listing_layout == "grid" ? esc_attr('active') : ''; ?>" data-id="grid-view">
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
														<path d="M1.33398 12C1.33398 10.973 1.33398 10.4595 1.56514 10.0823C1.69448 9.87127 1.87194 9.69381 2.08301 9.56447C2.46021 9.33331 2.97369 9.33331 4.00065 9.33331C5.02761 9.33331 5.54109 9.33331 5.9183 9.56447C6.12936 9.69381 6.30682 9.87127 6.43616 10.0823C6.66732 10.4595 6.66732 10.973 6.66732 12C6.66732 13.0269 6.66732 13.5404 6.43616 13.9176C6.30682 14.1287 6.12936 14.3062 5.9183 14.4355C5.54109 14.6666 5.02761 14.6666 4.00065 14.6666C2.97369 14.6666 2.46021 14.6666 2.08301 14.4355C1.87194 14.3062 1.69448 14.1287 1.56514 13.9176C1.33398 13.5404 1.33398 13.0269 1.33398 12Z"
															stroke="#6E655E" stroke-width="1.2"/>
														<path d="M9.33398 12C9.33398 10.973 9.33398 10.4595 9.56514 10.0823C9.69448 9.87127 9.87194 9.69381 10.083 9.56447C10.4602 9.33331 10.9737 9.33331 12.0007 9.33331C13.0276 9.33331 13.5411 9.33331 13.9183 9.56447C14.1294 9.69381 14.3068 9.87127 14.4362 10.0823C14.6673 10.4595 14.6673 10.973 14.6673 12C14.6673 13.0269 14.6673 13.5404 14.4362 13.9176C14.3068 14.1287 14.1294 14.3062 13.9183 14.4355C13.5411 14.6666 13.0276 14.6666 12.0007 14.6666C10.9737 14.6666 10.4602 14.6666 10.083 14.4355C9.87194 14.3062 9.69448 14.1287 9.56514 13.9176C9.33398 13.5404 9.33398 13.0269 9.33398 12Z"
															stroke="#6E655E" stroke-width="1.2"/>
														<path d="M1.33398 3.99998C1.33398 2.97302 1.33398 2.45954 1.56514 2.08233C1.69448 1.87127 1.87194 1.69381 2.08301 1.56447C2.46021 1.33331 2.97369 1.33331 4.00065 1.33331C5.02761 1.33331 5.54109 1.33331 5.9183 1.56447C6.12936 1.69381 6.30682 1.87127 6.43616 2.08233C6.66732 2.45954 6.66732 2.97302 6.66732 3.99998C6.66732 5.02694 6.66732 5.54042 6.43616 5.91762C6.30682 6.12869 6.12936 6.30615 5.9183 6.43549C5.54109 6.66665 5.02761 6.66665 4.00065 6.66665C2.97369 6.66665 2.46021 6.66665 2.08301 6.43549C1.87194 6.30615 1.69448 6.12869 1.56514 5.91762C1.33398 5.54042 1.33398 5.02694 1.33398 3.99998Z"
															stroke="#6E655E" stroke-width="1.2"/>
														<path d="M9.33398 3.99998C9.33398 2.97302 9.33398 2.45954 9.56514 2.08233C9.69448 1.87127 9.87194 1.69381 10.083 1.56447C10.4602 1.33331 10.9737 1.33331 12.0007 1.33331C13.0276 1.33331 13.5411 1.33331 13.9183 1.56447C14.1294 1.69381 14.3068 1.87127 14.4362 2.08233C14.6673 2.45954 14.6673 2.97302 14.6673 3.99998C14.6673 5.02694 14.6673 5.54042 14.4362 5.91762C14.3068 6.12869 14.1294 6.30615 13.9183 6.43549C13.5411 6.66665 13.0276 6.66665 12.0007 6.66665C10.9737 6.66665 10.4602 6.66665 10.083 6.43549C9.87194 6.30615 9.69448 6.12869 9.56514 5.91762C9.33398 5.54042 9.33398 5.02694 9.33398 3.99998Z"
															stroke="#6E655E" stroke-width="1.2"/>
													</svg>
												</li>
												<?php endif; ?>
											</ul>
										</div>

										<!--Available rooms start -->
										<div class="tf-archive-hotels archive_ajax_result <?php echo $listing_layout == "list" ? esc_attr('tf-layout-list') : esc_attr('tf-layout-grid'); ?> tf-grid-<?php echo esc_attr($grid_column); ?>">

											<?php
											$count = 0;
											$locations = [];
											while ($query->have_posts()) {
												$query->the_post();

												$meta = get_post_meta(get_the_ID(), 'tf_apartment_opt', true);
												if (!$meta["apartment_as_featured"]) {
													continue;
												}

												$count++;
												$map = !empty($meta['map']) ? Helper::tf_data_types($meta['map']) : '';
												$discount_type = !empty($meta['discount_type']) ? $meta['discount_type'] : '';
												$discount_price = !empty($meta['discount']) ? $meta['discount'] : '';

												$min_price_arr = AptPricing::instance(get_the_ID())->get_min_price();
												$price_html = AptPricing::instance(get_the_ID())->get_min_price_html();

												if (!empty($map)) {
													$lat = $map['latitude'];
													$lng = $map['longitude'];
													ob_start();
													?>
													<div class="tf-map-item" data-price="<?php //echo esc_attr( wc_price( $min_sale_price ) ); ?>">
														<div class="tf-map-item-thumb">
															<a href="<?php the_permalink(); ?>">
																<?php
																if (!empty(wp_get_attachment_url(get_post_thumbnail_id(), 'tf_gallery_thumb'))) {
																	the_post_thumbnail('full');
																} else {
																	echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
																}
																?>
															</a>

															<?php
															if (!empty($discount_price)) : ?>
																<div class="tf-map-item-discount">
																	<?php echo $discount_type == "percent" ? wp_kses_post($discount_price . '%') : wp_kses_post(wc_price($discount_price)) ?>
																	<?php esc_html_e(" Off", "tourfic"); ?>
																</div>
															<?php endif; ?>
														</div>
														<div class="tf-map-item-content">
															<h4>
																<a href="<?php the_permalink(); ?>">
																	<?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
																</a>
															</h4>
															<div class="tf-map-item-price">
																<?php echo wp_kses_post(AptPricing::instance(get_the_ID())->get_min_price_html()); ?>
															</div>
															<?php \Tourfic\App\TF_Review::tf_archive_single_rating('', $settings['design_apartment']); ?>
														</div>
													</div>
													<?php
													$infoWindowtext = ob_get_clean();

													$locations[$count] = [
														'id' => get_the_ID(),
														'url'	  => get_the_permalink(),
														'lat' => (float)$lat,
														'lng' => (float)$lng,
														'price' => base64_encode($price_html),
														'content' => base64_encode($infoWindowtext)
													];
												}
												echo wp_kses(apply_filters("tf_apartment_archive_single_featured_card_design_one", Apartment::tf_apartment_archive_single_item([ 1, 0, 0, '' ], $settings)), Helper::tf_custom_wp_kses_allow_tags());
											}
											while ($query->have_posts()) {
												$query->the_post();

												$meta = get_post_meta(get_the_ID(), 'tf_apartment_opt', true);
												if ($meta["apartment_as_featured"]) {
													continue;
												}

												$count++;
												$map = !empty($meta['map']) ? Helper::tf_data_types($meta['map']) : '';
												$discount_type = !empty($meta['discount_type']) ? $meta['discount_type'] : '';
												$discount_price = !empty($meta['discount']) ? $meta['discount'] : '';

												$min_price_arr = AptPricing::instance(get_the_ID())->get_min_price();
												$price_html = AptPricing::instance(get_the_ID())->get_min_price_html();

												if (!empty($map)) {
													$lat = $map['latitude'];
													$lng = $map['longitude'];
													ob_start();
													?>
													<div class="tf-map-item" data-price="<?php //echo esc_attr( wc_price( $min_sale_price ) ); ?>">
														<div class="tf-map-item-thumb">
															<a href="<?php the_permalink(); ?>">
																<?php
																if (!empty(wp_get_attachment_url(get_post_thumbnail_id(), 'tf_gallery_thumb'))) {
																	the_post_thumbnail('full');
																} else {
																	echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
																}
																?>
															</a>

															<?php
															if (!empty($discount_price)) : ?>
																<div class="tf-map-item-discount">
																	<?php echo $discount_type == "percent" ? wp_kses_post($discount_price . '%') : wp_kses_post(wc_price($discount_price)) ?>
																	<?php esc_html_e(" Off", "tourfic"); ?>
																</div>
															<?php endif; ?>
														</div>
														<div class="tf-map-item-content">
															<h4>
																<a href="<?php the_permalink(); ?>">
																	<?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
																</a>
															</h4>
															<div class="tf-map-item-price">
																<?php echo wp_kses_post(AptPricing::instance(get_the_ID())->get_min_price_html()); ?>
															</div>
															<?php \Tourfic\App\TF_Review::tf_archive_single_rating('', $settings['design_apartment']); ?>
														</div>
													</div>
													<?php
													$infoWindowtext = ob_get_clean();

													$locations[$count] = [
														'id' => get_the_ID(),
														'url'	  => get_the_permalink(),
														'lat' => (float)$lat,
														'lng' => (float)$lng,
														'price' => base64_encode($price_html),
														'content' => base64_encode($infoWindowtext)
													];
												}
												echo wp_kses(apply_filters("tf_apartment_archive_single_card_design_one", Apartment::tf_apartment_archive_single_item([ 1, 0, 0, '' ], $settings)), Helper::tf_custom_wp_kses_allow_tags());
											}
											wp_reset_query();
											?>
											<div id="map-datas" style="display: none"><?php echo array_filter($locations) ? wp_json_encode(array_values($locations)) : []; ?></div>
											<?php if($show_pagination == 'yes') : ?>
												<div class="tf-pagination-bar">
													<?php Helper::tourfic_posts_navigation($query, $pagination_prev_label, $pagination_next_label); ?>
												</div>
											<?php endif; ?>
										</div>
										<!-- Available rooms end -->

									</div>
									<!-- Available rooms end -->
								</div>
								<div class="tf-details-right tf-archive-right">
									<a href="" class="tf-mobile-list-btn">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
											<path d="M1.33398 7.59935C1.33398 6.82717 1.49514 6.66602 2.26732 6.66602H13.734C14.5062 6.66602 14.6673 6.82717 14.6673 7.59935V8.39935C14.6673 9.17153 14.5062 9.33268 13.734 9.33268H2.26732C1.49514 9.33268 1.33398 9.17153 1.33398 8.39935V7.59935Z"
												stroke="#FEF9F6" stroke-linecap="round"/>
											<path d="M1.33398 2.26634C1.33398 1.49416 1.49514 1.33301 2.26732 1.33301H13.734C14.5062 1.33301 14.6673 1.49416 14.6673 2.26634V3.06634C14.6673 3.83852 14.5062 3.99967 13.734 3.99967H2.26732C1.49514 3.99967 1.33398 3.83852 1.33398 3.06634V2.26634Z"
												stroke="#FEF9F6" stroke-linecap="round"/>
											<path d="M1.33398 12.9333C1.33398 12.1612 1.49514 12 2.26732 12H13.734C14.5062 12 14.6673 12.1612 14.6673 12.9333V13.7333C14.6673 14.5055 14.5062 14.6667 13.734 14.6667H2.26732C1.49514 14.6667 1.33398 14.5055 1.33398 13.7333V12.9333Z"
												stroke="#FEF9F6" stroke-linecap="round"/>
										</svg>
										<span><?php echo esc_html__('List view', 'tourfic') ?></span>
									</a>
									<div id="map-marker" data-marker="<?php echo esc_url(TF_ASSETS_URL . 'app/images/cluster-marker.png'); ?>"></div>
									<div class="tf-hotel-archive-map-wrap">
										<div id="tf-hotel-archive-map"></div>
									</div>
								</div>
							<?php endif; ?>
						<?php else: ?>
							<div class="tf-container">
								<div class="tf-notice tf-mt-24 tf-mb-30">
									<?php
									if (current_user_can('administrator')) {
										echo '<p>' . esc_html__('Google Maps is not selected. Please configure it ', 'tourfic') . '<a href="' . esc_url(admin_url('admin.php?page=tf_settings#tab=map_settings')) . '" target="_blank">' . esc_html__('Map Settings', 'tourfic') . '</a></p>';
									} else {
										echo '<p>' . esc_html__('Access is restricted as Google Maps is not enabled. Please contact the site administrator', 'tourfic') . '</p>';
									}
									?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php else: ?>
				<div id="map-datas" style="display: none"><?php echo wp_json_encode([]); ?></div>
				<div class="tf-container">
					<div class="tf-notice tf-mt-24 tf-mb-30">
						<div class="tf-nothing-found" data-post-count="0"><?php echo esc_html__("No Apartment Found!", "tourfic"); ?></div>
					</div>
				</div>
			<?php endif; ?>
			<?php $elementor_settings = $this->tf_search_elementor_settings($settings); ?>
			<div id="tf-elementor-settings" style="display: none"><?php echo !empty($elementor_settings) ? wp_json_encode($elementor_settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : wp_json_encode([]); ?></div>
		</div>
		<?php
	}

	protected function tf_apartment_design_legacy($settings, $query) {
		$post_count = $query->post_count;
		$show_total_result = isset( $settings['show_total_result'] ) ? $settings['show_total_result'] : 'yes';
		$show_sorting = isset( $settings['show_sorting'] ) ? $settings['show_sorting'] : 'yes';
		$grid_column = isset( $settings['grid_column'] ) ? absint($settings['grid_column']) : 2;
		$listing_layout_toggle = isset( $settings['listing_layout_toggle'] ) ? $settings['listing_layout_toggle'] : 'yes';
		if($listing_layout_toggle == 'yes'){
			$listing_layout = isset( $settings['listing_default_layout'] ) ? $settings['listing_default_layout'] : 'list';
		} else {
			$listing_layout = isset( $settings['listing_layout'] ) ? $settings['listing_layout'] : 'list';
		}
		$show_pagination = isset( $settings['show_pagination'] ) ? $settings['show_pagination'] : 'yes';
		$pagination_prev_label = isset( $settings['pagination_prev_label'] ) ? $settings['pagination_prev_label'] : '';
		$pagination_next_label = isset( $settings['pagination_next_label'] ) ? $settings['pagination_next_label'] : '';
		?>
		<div class="tf-archive-listing-wrap tf-archive-listing__legacy" data-design="default">
			<div class="tf-search-left">
				<div class="tf-action-top">
					<?php if($show_total_result == 'yes') : ?>
                    <div class="tf-result-counter-info">
                        <span class="tf-counter-title"><?php echo esc_html__( 'Total Results', 'tourfic' ); ?> </span>
                        <span><?php echo '('; ?> </span>
                        <div class="tf-total-results">
                            <span><?php echo esc_html( $post_count ); ?> </span>
                        </div>
                        <span><?php echo ')'; ?> </span>
                    </div>
					<?php endif; ?>

					<div class="tf-list-grid">
						<?php if($listing_layout_toggle == 'yes') : ?>
		                <a href="#list-view" data-id="list-view" class="change-view <?php echo $listing_layout=="list" ? esc_attr('active') : ''; ?>" title="<?php esc_html_e('List View', 'tourfic'); ?>"><i class="fas fa-list"></i></a>
		                <a href="#grid-view" data-id="grid-view" class="change-view <?php echo $listing_layout=="grid" ? esc_attr('active') : ''; ?>" title="<?php esc_html_e('Grid View', 'tourfic'); ?>"><i class="fas fa-border-all"></i></a>
						<?php endif; ?>

						<?php if($show_sorting == 'yes') : ?>
						<div class="tf-sorting-selection-warper">
                            <form class="tf-archive-ordering" method="get">
                                <select class="tf-orderby" name="tf-orderby" id="tf-orderby">
                                    <option value="default"><?php echo esc_html__( 'Default Sorting', 'tourfic' ); ?></option>
                                    <option value="enquiry"><?php echo esc_html__( 'Sort By Recommended', 'tourfic' ); ?></option>
                                    <option value="order"><?php echo esc_html__( 'Sort By Popularity', 'tourfic' ); ?></option>
                                    <option value="rating"><?php echo esc_html__( 'Sort By Average Rating', 'tourfic' ); ?></option>
                                    <option value="latest"><?php echo esc_html__( 'Sort By Latest', 'tourfic' ); ?></option>
                                    <option value="price-high"><?php echo esc_html__( 'Sort By Price: High to Low', 'tourfic' ); ?></option>
                                    <option value="price-low"><?php echo esc_html__( 'Sort By Price: Low to High', 'tourfic' ); ?></option>
                                </select>
                            </form>
                        </div>
						<?php endif; ?>
		            </div>
		        </div>
				<?php do_action("tf_apartment_archive_card_items_before"); ?>
				<div class="archive_ajax_result <?php echo $listing_layout=="grid" ? esc_attr('tours-grid') : '' ?> tf-grid-<?php echo esc_attr($grid_column); ?>">
					<?php
					if ( $query->have_posts() ) {
						while ( $query->have_posts() ) {
							$query->the_post();
							$apartment_meta = get_post_meta( get_the_ID() , 'tf_apartment_opt', true );
							if (!empty($apartment_meta[ "apartment_as_featured" ])) {
								Apartment::tf_apartment_archive_single_item([ 1, 0, 0, '' ], $settings);
							}
						}
						while ( $query->have_posts() ) {
							$query->the_post();
							$apartment_meta = get_post_meta( get_the_ID() , 'tf_apartment_opt', true );
							if ( empty($apartment_meta[ "apartment_as_featured" ])) {
								Apartment::tf_apartment_archive_single_item([ 1, 0, 0, '' ], $settings);
							}
						}
					} else {
						echo '<div class="tf-nothing-found" data-post-count="0">' .esc_html__("No Apartments Found!", "tourfic"). '</div>';
					}
					?>
				</div>
				<?php do_action("tf_apartment_archive_card_items_after"); ?>
				<?php if($show_pagination == 'yes') : ?>
					<div class="tf_posts_navigation">
						<?php Helper::tourfic_posts_navigation($query, $pagination_prev_label, $pagination_next_label); ?>
					</div>
				<?php endif; ?>

			</div>
			<?php $elementor_settings = $this->tf_search_elementor_settings($settings); ?>
			<div id="tf-elementor-settings" style="display: none"><?php echo !empty($elementor_settings) ? wp_json_encode($elementor_settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : wp_json_encode([]); ?></div>
		</div>
		<?php
	}

	protected function tf_car_design_1($settings, $query) {
		$post_count = $query->post_count;
		$show_total_result = isset( $settings['show_total_result'] ) ? $settings['show_total_result'] : 'yes';
		$grid_column = isset( $settings['grid_column'] ) ? absint($settings['grid_column']) : 2;
		$listing_layout_toggle = isset( $settings['listing_layout_toggle'] ) ? $settings['listing_layout_toggle'] : 'yes';
		if($listing_layout_toggle == 'yes'){
			$listing_layout = isset( $settings['listing_default_layout'] ) ? $settings['listing_default_layout'] : 'list';
		} else {
			$listing_layout = isset( $settings['listing_layout'] ) ? $settings['listing_layout'] : 'list';
		}
		$show_pagination = isset( $settings['show_pagination'] ) ? $settings['show_pagination'] : 'yes';
		$pagination_prev_label = isset( $settings['pagination_prev_label'] ) ? $settings['pagination_prev_label'] : '';
		$pagination_next_label = isset( $settings['pagination_next_label'] ) ? $settings['pagination_next_label'] : '';
		?>
		<div class="tf-archive-listing-wrap tf-archive-listing__one tf-archive-car-listing__one" data-design="design-1">
			<div class="tf-archive-header tf-flex tf-flex-space-bttn tf-flex-align-center tf-mb-30">
				<?php if($listing_layout_toggle == 'yes') : ?>
					<div class="tf-archive-view">
						<ul class="tf-flex tf-flex-gap-16">
							<li class="<?php echo $listing_layout=="grid" ? esc_attr('active') : ''; ?>" data-view="grid"><i class="ri-layout-grid-line"></i></li>
							<li class="<?php echo $listing_layout=="list" ? esc_attr('active') : ''; ?>" data-view="list"><i class="ri-list-check"></i></li>
						</ul>
					</div>
				<?php endif; ?>

				<?php if($show_total_result == 'yes') : ?>
					<div class="tf-total-result-bar">
						<span>
							<?php echo esc_html__( 'Total Results ', 'tourfic' ); ?>
						</span>
						<span><?php echo ' ('; ?> </span>
						<div class="tf-total-results">
							<span><?php echo esc_html( $post_count ); ?> </span>
						</div>
						<span><?php echo ')'; ?> </span>
					</div>
				<?php endif; ?>
			</div>
			<div class="tf-car-details-column">
				<div class="tf-car-archive-result" style="width: 100%;">
					<?php do_action("tf_car_archive_card_items_before"); ?>
					<div class="tf-car-result archive_ajax_result tf-flex tf-flex-gap-32 <?php echo $listing_layout=="list" ? esc_attr('list-view') : esc_attr('grid-view'); ?> tf-grid-<?php echo esc_attr($grid_column); ?>">
						<?php
						if ( $query->have_posts() ) {
							while ( $query->have_posts() ) {
								$query->the_post();
								$car_meta = get_post_meta( get_the_ID() , 'tf_carrental_opt', true );
								if ( !empty( $car_meta[ "car_as_featured" ] ) && $car_meta[ "car_as_featured" ] == 1 ) {
									tf_car_archive_single_item('','','','','','', $settings);
								}
							}
							while ( $query->have_posts() ) {
								$query->the_post();
								$car_meta = get_post_meta( get_the_ID() , 'tf_carrental_opt', true );
								if ( empty($car_meta[ "car_as_featured" ]) ) {
									tf_car_archive_single_item('','','','','','', $settings);
								}
							}
						} else {
							echo '<div class="tf-nothing-found" data-post-count="0" >' .esc_html__("No Cars Found!", "tourfic"). '</div>';
						}
						?>
						
						<?php if($show_pagination == 'yes') : ?>
							<div class="tf-pagination-bar">
								<?php Helper::tourfic_posts_navigation($query, $pagination_prev_label, $pagination_next_label); ?>
							</div>
						<?php endif; ?>
					</div>
					<?php do_action("tf_car_archive_card_items_after"); ?>
				</div>
			</div>
			<?php $elementor_settings = $this->tf_search_elementor_settings($settings); ?>
			<div id="tf-elementor-settings" style="display: none"><?php echo !empty($elementor_settings) ? wp_json_encode($elementor_settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : wp_json_encode([]); ?></div>
		</div>
		<?php
	}

	/**
     * Generates conditional display rules for controls based on service and design
     * 
     * @param array $design Array of design conditions in format ['service' => 'design_value']
     * @return array Condition array for Elementor controls
     */
    protected function tf_display_conditionally($design, $extra_conditions = []) {
        $terms = [];
        
        foreach ($design as $service_key => $design_values) {
			// Detect if this is a "NOT" condition
            $is_not = false;
            if ( substr( $service_key, -1 ) === '!' ) {
                $is_not = true;
                $service = rtrim( $service_key, '!' );
            } else {
                $service = $service_key;
            }

            // Convert to array if it's not already
            $design_values = (array) $design_values;
            $design_control = 'design_' . str_replace('tf_', '', $service);

            foreach ($design_values as $design_value) {
                $service_terms = [
					[
						'name' => 'service',
						'operator' => $is_not ? '!=' : '==',
						'value' => $service,
					],
					[
						'name' => $design_control,
						'operator' => '==',
						'value' => $design_value,
					]
				];

				// Add extra conditions if provided
				if (!empty($extra_conditions)) {
					foreach ($extra_conditions as $key => $value) {
						$operator = '==';
						$actual_key = $key;
						
						// Handle negation operator
						if (substr($key, -1) === '!') {
							$operator = '!=';
							$actual_key = substr($key, 0, -1);
						}
						
						$service_terms[] = [
							'name' => $actual_key,
							'operator' => $operator,
							'value' => $value,
						];
					}
				}
				
				$terms[] = [
					'relation' => 'and',
					'terms' => $service_terms,
				];
            }
        }

        return [
            'relation' => 'or',
            'terms' => $terms,
        ];
    }

	/**
	 * Apply CSS property to the widget
     * @param $css_property
     * @return string
     */
	public function tf_apply_dim( $css_property, $important = false ) {
		return "{$css_property}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} " . ($important ? '!important' : '') . ";";
	}
}

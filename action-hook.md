# Tourfic Developer Hooks & Filters Documentation

Welcome to the official developer hooks and filters reference guide for the **Tourfic** (Free & Pro) WordPress plugins. This document provides a complete list of action and filter hooks available in the plugin, fully expanded dynamic hooks for all 5 services (**Tours, Hotels, Apartments, Car Rentals, and Rooms**), along with copy-pasteable code examples for **every single hook** to aid your custom development.

---

## Table of Contents
1. [CPT, Taxonomy & Options Registration Hooks](#1-cpt-taxonomy--options-registration-hooks)
2. [Search Form Hooks](#2-search-form-hooks)
3. [Booking & Single Booking Details Hooks](#3-booking--single-booking-details-hooks)
4. [Backend Booking Hooks](#4-backend-booking-hooks)
5. [Enquiry & Admin Email Hooks](#5-enquiry--admin-email-hooks)
6. [Integration & API Hooks](#6-integration--api-hooks)
7. [WooCommerce & Payment Hooks](#7-woocommerce--payment-hooks)
8. [Archive & Shortcode Card Customization Hooks](#8-archive--shortcode-card-customization-hooks)
9. [Front-End Single Template & Booking Form Customization Hooks](#9-front-end-single-template--booking-form-customization-hooks)

---

## 1. CPT, Taxonomy & Options Registration Hooks

These filter hooks allow developers to customize post type arguments, supported features, custom labels, custom slugs, custom taxonomy properties, and option panel field assets (like the registered icons list) for all Tourfic services.

### Post Type Support Filters
Customize standard features supported by the custom post types (e.g. adding `revisions`, removing `comments`).

*   **`tf_tours_supports`**
    ```php
    add_filter( 'tf_tours_supports', function( $supports ) {
        $supports[] = 'revisions';
        return $supports;
    } );
    ```
*   **`tf_hotel_supports`**
    ```php
    add_filter( 'tf_hotel_supports', function( $supports ) {
        $supports[] = 'author';
        return $supports;
    } );
    ```
*   **`tf_apartment_supports`**
    ```php
    add_filter( 'tf_apartment_supports', function( $supports ) {
        $supports[] = 'custom-fields';
        return $supports;
    } );
    ```
*   **`tf_carrental_supports`**
    ```php
    add_filter( 'tf_carrental_supports', function( $supports ) {
        $supports[] = 'revisions';
        return $supports;
    } );
    ```
*   **`tf_room_supports`**
    ```php
    add_filter( 'tf_room_supports', function( $supports ) {
        $supports[] = 'page-attributes';
        return $supports;
    } );
    ```

---

### Post Type Slug Filters
Modify custom URL slugs for each service post type.

*   **`tf_tours_slug`**
    ```php
    add_filter( 'tf_tours_slug', function( $slug ) {
        return 'destinations-trips';
    } );
    ```
*   **`tf_hotel_slug`**
    ```php
    add_filter( 'tf_hotel_slug', function( $slug ) {
        return 'resorts-lodging';
    } );
    ```
*   **`tf_apartment_slug`**
    ```php
    add_filter( 'tf_apartment_slug', function( $slug ) {
        return 'villas-rentals';
    } );
    ```
*   **`tf_car_slug`**
    ```php
    add_filter( 'tf_car_slug', function( $slug ) {
        return 'vehicle-hire';
    } );
    ```
*   **`tf_room_slug`**
    ```php
    add_filter( 'tf_room_slug', function( $slug ) {
        return 'suites-rooms';
    } );
    ```

---

### Taxonomy Rewrite Slug Filters
Modify taxonomy URL rewrite slugs registered for each custom post type.

*   **`tf_hotel_location_slug` / `tf_hotel_feature_slug` / `tf_hotel_type_slug`**
    ```php
    add_filter( 'tf_hotel_location_slug', function( $slug ) { return 'resort-cities'; } );
    add_filter( 'tf_hotel_feature_slug', function( $slug ) { return 'resort-amenities'; } );
    add_filter( 'tf_hotel_type_slug', function( $slug ) { return 'resort-categories'; } );
    ```
*   **`tf_apartment_location_slug` / `tf_apartment_feature_slug` / `tf_apartment_type_slug`**
    ```php
    add_filter( 'tf_apartment_location_slug', function( $slug ) { return 'villa-cities'; } );
    add_filter( 'tf_apartment_feature_slug', function( $slug ) { return 'villa-amenities'; } );
    add_filter( 'tf_apartment_type_slug', function( $slug ) { return 'villa-categories'; } );
    ```
*   **`tf_carrental_location_slug` / `tf_carrental_brand_slug` / `tf_carrental_fuel_type_slug` / `tf_carrental_category_slug` / `tf_carrental_engine_year_slug`**
    ```php
    add_filter( 'tf_carrental_location_slug', function( $slug ) { return 'vehicle-cities'; } );
    add_filter( 'tf_carrental_brand_slug', function( $slug ) { return 'vehicle-makes'; } );
    add_filter( 'tf_carrental_fuel_type_slug', function( $slug ) { return 'vehicle-specs'; } );
    add_filter( 'tf_carrental_category_slug', function( $slug ) { return 'vehicle-categories'; } );
    add_filter( 'tf_carrental_engine_year_slug', function( $slug ) { return 'vehicle-model-years'; } );
    ```
*   **`tf_tour_destination_slug` / `tf_tour_attraction_slug` / `tf_tour_activities_slug` / `tf_tour_features_slug` / `tf_tour_type_slug`**
    ```php
    add_filter( 'tf_tour_destination_slug', function( $slug ) { return 'trip-destinations'; } );
    add_filter( 'tf_tour_attraction_slug', function( $slug ) { return 'trip-attractions'; } );
    add_filter( 'tf_tour_activities_slug', function( $slug ) { return 'trip-activities'; } );
    add_filter( 'tf_tour_features_slug', function( $slug ) { return 'trip-features'; } );
    add_filter( 'tf_tour_type_slug', function( $slug ) { return 'trip-categories'; } );
    ```
*   **`tf_room_type_slug`**
    ```php
    add_filter( 'tf_room_type_slug', function( $slug ) { return 'suite-types'; } );
    ```

---

### Post Type Registration Args Filters
Override full registration parameters before the CPT is registered.

*   **`tf_tour_post_type_args`**
    ```php
    add_filter( 'tf_tour_post_type_args', function( $args ) {
        $args['exclude_from_search'] = false;
        return $args;
    } );
    ```
*   **`tf_hotel_post_type_args`**
    ```php
    add_filter( 'tf_hotel_post_type_args', function( $args ) {
        $args['menu_position'] = 25;
        return $args;
    } );
    ```
*   **`tf_apartment_args`**
    ```php
    add_filter( 'tf_apartment_args', function( $args ) {
        $args['hierarchical'] = true;
        return $args;
    } );
    ```
*   **`tf_hotel_post_type_name_change_singular` / `tf_hotel_post_type_name_change_plural`**
    ```php
    add_filter( 'tf_hotel_post_type_name_change_singular', function( $name ) {
        return esc_html__( 'Resort', 'tourfic' );
    } );
    add_filter( 'tf_hotel_post_type_name_change_plural', function( $name ) {
        return esc_html__( 'Resorts', 'tourfic' );
    } );
    ```

---

### Custom Taxonomy Labels & Args Filters
Change taxonomy labels and arguments dynamically during registration.

*   **`tf_tour_destination_labels` / `tf_tour_destination_args`**
    ```php
    add_filter( 'tf_tour_destination_labels', function( $labels ) {
        $labels['name'] = 'Holiday Destinations';
        return $labels;
    } );
    add_filter( 'tf_tour_destination_args', function( $args ) {
        $args['hierarchical'] = false;
        return $args;
    } );
    ```
*   **`tf_tour_attraction_labels` / `tf_tour_attraction_args`**
    ```php
    add_filter( 'tf_tour_attraction_labels', function( $labels ) {
        $labels['singular_name'] = 'Points of Interest';
        return $labels;
    } );
    add_filter( 'tf_tour_attraction_args', function( $args ) {
        $args['show_in_rest'] = false;
        return $args;
    } );
    ```
*   **`tf_tour_activities_labels` / `tf_tour_activities_args`**
    ```php
    add_filter( 'tf_tour_activities_labels', function( $labels ) {
        $labels['name'] = 'Tour Experiences';
        return $labels;
    } );
    add_filter( 'tf_tour_activities_args', function( $args ) {
        $args['rewrite'] = array( 'slug' => 'experience' );
        return $args;
    } );
    ```
*   **`tf_tour_features_labels` / `tf_tour_features_args`**
    ```php
    add_filter( 'tf_tour_features_labels', function( $labels ) {
        $labels['name'] = 'Tour Amenities';
        return $labels;
    } );
    add_filter( 'tf_tour_features_args', function( $args ) {
        $args['show_admin_column'] = false;
        return $args;
    } );
    ```
*   **`tf_tour_type_labels` / `tf_tour_type_args`**
    ```php
    add_filter( 'tf_tour_type_labels', function( $labels ) {
        $labels['name'] = 'Trip Categorizations';
        return $labels;
    } );
    add_filter( 'tf_tour_type_args', function( $args ) {
        $args['hierarchical'] = false;
        return $args;
    } );
    ```
*   **`tf_hotel_location_labels` / `tf_hotel_location_args`**
    ```php
    add_filter( 'tf_hotel_location_labels', function( $labels ) {
        $labels['name'] = 'Hotel Cities';
        return $labels;
    } );
    add_filter( 'tf_hotel_location_args', function( $args ) {
        $args['rewrite'] = array( 'slug' => 'hotel-cities' );
        return $args;
    } );
    add_filter( 'tf_hotel_location_metabox_args', function( $args ) {
        // Change metabox title or arguments for Hotel Location taxonomy fields
        $args['title'] = 'Resort Locations Details';
        return $args;
    } );
    ```
*   **`tf_hotel_feature_labels` / `tf_hotel_feature_args`**
    ```php
    add_filter( 'tf_hotel_feature_labels', function( $labels ) {
        $labels['name'] = 'Resort Features';
        return $labels;
    } );
    add_filter( 'tf_hotel_feature_args', function( $args ) {
        $args['show_in_rest'] = true;
        return $args;
    } );
    ```
*   **`tf_hotel_type_labels` / `tf_hotel_type_args`**
    ```php
    add_filter( 'tf_hotel_type_labels', function( $labels ) {
        $labels['name'] = 'Resort Classifications';
        return $labels;
    } );
    add_filter( 'tf_hotel_type_args', function( $args ) {
        $args['hierarchical'] = true;
        return $args;
    } );
    ```
*   **`tf_apartment_location_labels` / `tf_apartment_location_args`**
    ```php
    add_filter( 'tf_apartment_location_labels', function( $labels ) {
        $labels['name'] = 'Apartment Regions';
        return $labels;
    } );
    add_filter( 'tf_apartment_location_args', function( $args ) {
        $args['rewrite'] = array( 'slug' => 'rentals-region' );
        return $args;
    } );
    ```
*   **`tf_apartment_feature_labels` / `tf_apartment_feature_args`**
    ```php
    add_filter( 'tf_apartment_feature_labels', function( $labels ) {
        $labels['name'] = 'Villa Amenities';
        return $labels;
    } );
    add_filter( 'tf_apartment_feature_args', function( $args ) {
        $args['show_admin_column'] = true;
        return $args;
    } );
    ```
*   **`tf_apartment_type_labels` / `tf_apartment_type_args`**
    ```php
    add_filter( 'tf_apartment_type_labels', function( $labels ) {
        $labels['name'] = 'Villa Styles';
        return $labels;
    } );
    add_filter( 'tf_apartment_type_args', function( $args ) {
        $args['hierarchical'] = false;
        return $args;
    } );
    ```
*   **`tf_carrental_location_labels` / `tf_carrental_location_args`**
    ```php
    add_filter( 'tf_carrental_location_labels', function( $labels ) {
        $labels['name'] = 'Pickup Locations';
        return $labels;
    } );
    add_filter( 'tf_carrental_location_args', function( $args ) {
        $args['show_in_nav_menus'] = true;
        return $args;
    } );
    ```
*   **`tf_carrental_brand_labels` / `tf_carrental_brand_args`**
    ```php
    add_filter( 'tf_carrental_brand_labels', function( $labels ) {
        $labels['name'] = 'Car Makes & Brands';
        return $labels;
    } );
    add_filter( 'tf_carrental_brand_args', function( $args ) {
        $args['hierarchical'] = true;
        return $args;
    } );
    ```
*   **`tf_carrental_category_labels` / `tf_carrental_category_args`**
    ```php
    add_filter( 'tf_carrental_category_labels', function( $labels ) {
        $labels['name'] = 'Fleet Categories';
        return $labels;
    } );
    add_filter( 'tf_carrental_category_args', function( $args ) {
        $args['rewrite'] = array( 'slug' => 'fleet' );
        return $args;
    } );
    ```
*   **`tf_get_terms_dropdown_args`**
    ```php
    add_filter( 'tf_get_terms_dropdown_args', function( $args, $taxonomy ) {
        // Customize get_terms query arguments for taxonomy drop-down options fields
        if ( 'hotel_location' === $taxonomy ) {
            $args['orderby'] = 'count';
            $args['order'] = 'DESC';
        }
        return $args;
    }, 10, 2 );
    ```

---

### Option Panel & Fields Filters
Customize field options and registered assets within settings panels and custom post type metaboxes.

*   **`tf_icon_list`**
    ```php
    add_filter( 'tf_icon_list', function( $icons ) {
        // Register custom FontAwesome icon packs or individual icons inside the Tourfic metabox icon-picker field
        $icons['my_custom_pack'] = array(
            'label'      => esc_html__( 'My Custom Icons', 'tourfic' ),
            'label_icon' => 'fa fa-star',
            'icons'      => array( 'fa fa-user-shield', 'fa fa-award', 'fa fa-shield-alt' ),
        );
        return $icons;
    } );
    ```

*   **`{metabox_key}_title` / `{metabox_key}_sections`**
    Dynamically filters the metabox title or sections array for Tourfic custom post types.
    *   **Valid Metabox Keys:**
        *   `tf_tours_opt` (Tours Metabox)
        *   `tf_hotels_opt` (Hotels Metabox)
        *   `tf_room_opt` (Rooms Metabox)
        *   `tf_apartment_opt` (Apartments Metabox)
        *   `tf_carrental_opt` (Car Rentals Metabox)
    ```php
    add_filter( 'tf_hotels_opt_title', function( $title ) {
        return 'Hotel Configurations';
    } );
    add_filter( 'tf_hotels_opt_sections', function( $sections ) {
        // Inject or remove settings sections programmatically
        return $sections;
    } );
    ```

*   **`{settings_key}_title` / `{settings_key}_icon` / `{settings_key}_position` / `{settings_key}_sections`**
    Dynamically filters the main settings panel title, menu icon, menu position, or the full sections schema array.
    *   **Valid Settings Keys:**
        *   `tf_settings` (Tourfic Global Settings Panel)
    ```php
    add_filter( 'tf_settings_title', function( $title ) {
        return 'Tourfic Global Settings';
    } );
    add_filter( 'tf_settings_sections', function( $sections ) {
        // Customize admin panel settings panels dynamically
        return $sections;
    } );
    ```

*   **`tf_post_types`**
    ```php
    add_filter( 'tf_post_types', function( $post_types ) {
        // Customize the default post types array and their required taxonomy warnings
        $post_types['tf_hotel']['hotel_location']['message'] = 'Please select a city location before publishing this hotel.';
        return $post_types;
    } );
    ```

---

## 2. Search Form Hooks

Use these hooks to alter search tabs, placeholder values, and customize form submit buttons.

### Search Form Tabs Labels Filters
Change the frontend display label for each service search tab.

*   **`tf_hotel_search_form_tab_button_text`**
    ```php
    add_filter( 'tf_hotel_search_form_tab_button_text', function( $text ) {
        return esc_html__( 'Find Resorts', 'tourfic' );
    } );
    ```
*   **`tf_tour_search_form_tab_button_text`**
    ```php
    add_filter( 'tf_tour_search_form_tab_button_text', function( $text ) {
        return esc_html__( 'Find Trips', 'tourfic' );
    } );
    ```
*   **`tf_apartment_search_form_tab_button_text`**
    ```php
    add_filter( 'tf_apartment_search_form_tab_button_text', function( $text ) {
        return esc_html__( 'Find Rentals', 'tourfic' );
    } );
    ```
*   **`tf_car_search_form_tab_button_text`**
    ```php
    add_filter( 'tf_car_search_form_tab_button_text', function( $text ) {
        return esc_html__( 'Find Cars', 'tourfic' );
    } );
    ```
*   **`tf_room_search_form_tab_button_text`**
    ```php
    add_filter( 'tf_room_search_form_tab_button_text', function( $text ) {
        return esc_html__( 'Find Suites', 'tourfic' );
    } );
    ```

---

### Search Form Submit Buttons Filters
Change search form execution buttons text values.

*   **`tf_hotel_search_form_submit_button_text`**
    ```php
    add_filter( 'tf_hotel_search_form_submit_button_text', function( $text ) {
        return esc_html__( 'Check Rooms availability', 'tourfic' );
    } );
    ```
*   **`tf_tour_search_form_submit_button_text`**
    ```php
    add_filter( 'tf_tour_search_form_submit_button_text', function( $text ) {
        return esc_html__( 'Check Dates', 'tourfic' );
    } );
    ```
*   **`tf_apartment_search_form_submit_button_text`**
    ```php
    add_filter( 'tf_apartment_search_form_submit_button_text', function( $text ) {
        return esc_html__( 'Verify Availability', 'tourfic' );
    } );
    ```
*   **`tf_car_search_form_submit_button_text`**
    ```php
    add_filter( 'tf_car_search_form_submit_button_text', function( $text ) {
        return esc_html__( 'Find Best Cars', 'tourfic' );
    } );
    ```
*   **`tf_room_search_form_submit_button_text`**
    ```php
    add_filter( 'tf_room_search_form_submit_button_text', function( $text ) {
        return esc_html__( 'Select Rooms', 'tourfic' );
    } );
    ```

---

### Search Form Placeholders & Filters Labels
Customize default placeholder strings, search inputs, and filter panel toggles.

*   **`tf_location_placeholder`**
    ```php
    add_filter( 'tf_location_placeholder', function( $placeholder ) {
        // Change default location search input placeholder text
        return esc_html__( 'Search by destination or hotel name...', 'tourfic' );
    } );
    ```
*   **`tf_search_form_advance_filter_label`**
    ```php
    add_filter( 'tf_search_form_advance_filter_label', function( $label ) {
        // Customize the text label of the advance filters dropdown drawer trigger
        return esc_html__( 'More Options', 'tourfic' );
    } );
    ```
*   **`tf_hotel_search_form_advance_type_args`**
    ```php
    add_filter( 'tf_hotel_search_form_advance_type_args', function( $args ) {
        // Customize taxonomy query arguments for terms shown in search advance filters
        $args['hide_empty'] = false;
        $args['orderby'] = 'count';
        return $args;
    } );
    ```

---

### Search Form Lifecycle & Tab Content Action Hooks
Allows developers to inject custom links, tab navigation items, forms, banners, or logic before/after search form components.

*   **`tf_before_booking_form_tab`**
    ```php
    add_action( 'tf_before_booking_form_tab', function( $type ) {
        // Echo custom navigation tabs before the standard tabs
        echo '<li class="tf-tabnav-custom"><a href="#">Custom Tab Info</a></li>';
    }, 10, 1 );
    ```
*   **`tf_after_booking_form_tab`**
    ```php
    add_action( 'tf_after_booking_form_tab', function( $type ) {
        // Echo custom navigation tabs after the standard tabs
        echo '<li class="tf-tabnav-custom"><a href="#">Support Center</a></li>';
    }, 10, 1 );
    ```
*   **`tf_before_booking_form_mobile_tab`**
    ```php
    add_action( 'tf_before_booking_form_mobile_tab', function( $type ) {
        // Render custom mobile menu items before standard options
        echo '<option value="custom">Special Mobile Offers</option>';
    }, 10, 1 );
    ```
*   **`tf_after_booking_form_mobile_tab`**
    ```php
    add_action( 'tf_after_booking_form_mobile_tab', function( $type ) {
        // Render custom mobile menu items after standard options
        echo '<option value="custom-support">Call Helpline</option>';
    }, 10, 1 );
    ```
*   **`tf_before_booking_form`**
    ```php
    add_action( 'tf_before_booking_form', function( $classes, $title, $subtitle, $type ) {
        // Output custom container banner before the search form layout starts
        echo '<div class="search-promo-header">Get 10% off on advanced bookings!</div>';
    }, 10, 4 );
    ```
*   **`tf_after_booking_form`**
    ```php
    add_action( 'tf_after_booking_form', function( $classes, $title, $subtitle, $type ) {
        // Output footer links or trust badges after the search form container
        echo '<div class="search-secure-badge">100% Secure reservation guarantee</div>';
    }, 10, 4 );
    ```
*   **`tf_booking_search_action`**
    ```php
    add_filter( 'tf_booking_search_action', function( $action_url ) {
        // Redirect search queries to a custom portal or external tracking system
        return 'https://custom-portal.com/search-results';
    } );
    ```

---

## 3. Booking & Single Booking Details Hooks

These hooks are triggered within the WordPress Admin Dashboard under Tourfic Booking Details panels.

### Booking Details Main Title Filters
*   **`tf_hotel_booking_details_main_title`**
    ```php
    add_filter( 'tf_hotel_booking_details_main_title', function( $title ) {
        return esc_html__( 'Resort Booking Log', 'tourfic' );
    } );
    ```
*   **`tf_tours_booking_details_main_title`**
    ```php
    add_filter( 'tf_tours_booking_details_main_title', function( $title ) {
        return esc_html__( 'Trip Booking Log', 'tourfic' );
    } );
    ```
*   **`tf_apartment_booking_details_main_title`**
    ```php
    add_filter( 'tf_apartment_booking_details_main_title', function( $title ) {
        return esc_html__( 'Rental Booking Log', 'tourfic' );
    } );
    ```
*   **`tf_carrental_booking_details_main_title`**
    ```php
    add_filter( 'tf_carrental_booking_details_main_title', function( $title ) {
        return esc_html__( 'Vehicle Booking Log', 'tourfic' );
    } );
    ```

---

### Booking Details Header Action Hooks
Triggered right above the booking orders table in the admin dashboard panel, useful for injecting custom navigation buttons, custom alerts, or bulk operations shortcuts.

*   **`tf_before_hotel_booking_details`**
    ```php
    add_action( 'tf_before_hotel_booking_details', function() {
        echo '<div class="notice notice-info is-dismissible"><p>Reminder: Check guest check-in schedules daily.</p></div>';
    } );
    ```
*   **`tf_before_tour_booking_details`**
    ```php
    add_action( 'tf_before_tour_booking_details', function() {
        echo '<div class="notice notice-warning"><p>Attention: Verify tour operator details before departure.</p></div>';
    } );
    ```
*   **`tf_before_apartment_booking_details`**
    ```php
    add_action( 'tf_before_apartment_booking_details', function() {
        echo '<div class="notice notice-success"><p>Tip: Generate weekly clean-up task lists.</p></div>';
    } );
    ```

---

### Booking Details Table Wrapper Action Hooks
Triggered immediately before and after rendering the WordPress admin order details data list table for each post type. Useful for wrapping the list grid in custom container tags, injecting custom scripts, styling, or custom data summaries.

*   **`tf_hotel_before_booking_order_table` / `tf_hotel_after_booking_order_table`**
    ```php
    add_action( 'tf_hotel_before_booking_order_table', function() {
        echo '<div class="custom-hotel-table-wrapper">';
    } );
    add_action( 'tf_hotel_after_booking_order_table', function() {
        echo '</div><!-- .custom-hotel-table-wrapper -->';
    } );
    ```
*   **`tf_tours_before_booking_order_table` / `tf_tours_after_booking_order_table`**
    ```php
    add_action( 'tf_tours_before_booking_order_table', function() {
        echo '<div class="custom-tours-table-wrapper">';
    } );
    add_action( 'tf_tours_after_booking_order_table', function() {
        echo '</div><!-- .custom-tours-table-wrapper -->';
    } );
    ```
*   **`tf_apartment_before_booking_order_table` / `tf_apartment_after_booking_order_table`**
    ```php
    add_action( 'tf_apartment_before_booking_order_table', function() {
        echo '<div class="custom-apt-table-wrapper">';
    } );
    add_action( 'tf_apartment_after_booking_order_table', function() {
        echo '</div><!-- .custom-apt-table-wrapper -->';
    } );
    ```
*   **`tf_carrental_before_booking_order_table` / `tf_carrental_after_booking_order_table`**
    ```php
    add_action( 'tf_carrental_before_booking_order_table', function() {
        echo '<div class="custom-cars-table-wrapper">';
    } );
    add_action( 'tf_carrental_after_booking_order_table', function() {
        echo '</div><!-- .custom-cars-table-wrapper -->';
    } );
    ```

---

### Single Booking Details Subtitle Action Hooks
Allows displaying extra metadata directly under the title in the admin booking modal window.

*   **`tf_hotel_single_booking_details_after_title_text`**
    ```php
    add_action( 'tf_hotel_single_booking_details_after_title_text', function() {
        echo '<p style="color: #666;">Note: Please check-in guest before 2:00 PM.</p>';
    } );
    ```
*   **`tf_tours_single_booking_details_after_title_text`**
    ```php
    add_action( 'tf_tours_single_booking_details_after_title_text', function() {
        echo '<span class="badge">Eco Tour Option Included</span>';
    } );
    ```
*   **`tf_apartment_single_booking_details_after_title_text`**
    ```php
    add_action( 'tf_apartment_single_booking_details_after_title_text', function() {
        echo '<p>Verified Luxury Apartment Booking</p>';
    } );
    ```
*   **`tf_carrental_single_booking_details_after_title_text`**
    ```php
    add_action( 'tf_carrental_single_booking_details_after_title_text', function() {
        echo '<p>Includes roadside recovery assistance</p>';
    } );
    ```

---

### Custom Card / Content Action Hooks
Allows injecting custom content blocks at the top of the single booking preview screen.

*   **`tf_hotel_single_booking_details_card_first`**
    ```php
    add_action( 'tf_hotel_single_booking_details_card_first', function() {
        echo '<div class="alert alert-info">Special Request: Vegetarian meals only.</div>';
    } );
    ```
*   **`tf_tour_single_booking_details_card_first`**
    ```php
    add_action( 'tf_tour_single_booking_details_card_first', function() {
        echo '<div class="alert">Guide Assignment Status: Not Assigned</div>';
    } );
    ```
*   **`tf_apartment_single_booking_details_card_first`**
    ```php
    add_action( 'tf_apartment_single_booking_details_card_first', function() {
        echo '<div class="alert">Door lock pin code has been generated.</div>';
    } );
    ```
*   **`tf_car_single_booking_details_card_first`**
    ```php
    add_action( 'tf_car_single_booking_details_card_first', function() {
        echo '<div class="alert alert-warning">Driver license needs verification.</div>';
    } );
    ```

---

### Admin Booking Customer Section Title Filters
*   **`tf_hotelbooking_details_customer_section_title_change`**
    ```php
    add_filter( 'tf_hotelbooking_details_customer_section_title_change', function( $title ) {
        return esc_html__( 'Main Guest Contact', 'tourfic' );
    } );
    ```
*   **`tf_tourbooking_details_customer_section_title_change`**
    ```php
    add_filter( 'tf_tourbooking_details_customer_section_title_change', function( $title ) {
        return esc_html__( 'Lead Passenger Contact', 'tourfic' );
    } );
    ```
*   **`tf_apartmentbooking_details_customer_section_title_change`**
    ```php
    add_filter( 'tf_apartmentbooking_details_customer_section_title_change', function( $title ) {
        return esc_html__( 'Tenant Details', 'tourfic' );
    } );
    ```
*   **`tf_carbooking_details_customer_section_title_change`**
    ```php
    add_filter( 'tf_carbooking_details_customer_section_title_change', function( $title ) {
        return esc_html__( 'Driver Details', 'tourfic' );
    } );
    ```

---

### Admin Booking Pricing Section Title Filters
*   **`tf_hotelbooking_details_pricing_section_title_change`**
    ```php
    add_filter( 'tf_hotelbooking_details_pricing_section_title_change', function( $title ) {
        return esc_html__( 'Lodging Financial Breakdown', 'tourfic' );
    } );
    ```
*   **`tf_tourbooking_details_pricing_section_title_change`**
    ```php
    add_filter( 'tf_tourbooking_details_pricing_section_title_change', function( $title ) {
        return esc_html__( 'Trip Fees Breakdown', 'tourfic' );
    } );
    ```
*   **`tf_apartmentbooking_details_pricing_section_title_change`**
    ```php
    add_filter( 'tf_apartmentbooking_details_pricing_section_title_change', function( $title ) {
        return esc_html__( 'Lease Statement details', 'tourfic' );
    } );
    ```
*   **`tf_carbooking_details_pricing_section_title_change`**
    ```php
    add_filter( 'tf_carbooking_details_pricing_section_title_change', function( $title ) {
        return esc_html__( 'Rental Pricing Breakdown', 'tourfic' );
    } );
    ```

---

### Frontend Label & Text Filters
Override dynamic labels, strings, and comment form headers on single layout details pages.

*   **`tf_hotel_adults_title_change`**
    ```php
    add_filter( 'tf_hotel_adults_title_change', function( $label ) {
        // Change default "Adult" string across room options and availability rows
        return esc_html__( 'Adult Guest', 'tourfic' );
    } );
    ```
*   **`tf_comment_form_title`**
    ```php
    add_filter( 'tf_comment_form_title', function( $title ) {
        // Customize the average review count header title
        return 'Customer feedback and reviews';
    } );
    ```
*   **`tf_car_booking_form_submit_button_text`**
    ```php
    add_filter( 'tf_car_booking_form_submit_button_text', function( $text ) {
        // Change the text of the main car booking form submit/continue button
        return esc_html__( 'Proceed to Reservation', 'tourfic' );
    } );
    ```
*   **`tf_hotel_guest_name_change`**
    ```php
    add_filter( 'tf_hotel_guest_name_change', function( $name ) {
        // Customize the default "guest" or "Guest" labels used in search selectors and booking summaries
        return esc_html__( 'visitor', 'tourfic' );
    } );
    ```
*   **`tourfic_add_review_button_text`**
    ```php
    add_filter( 'tourfic_add_review_button_text', function( $text ) {
        // Change the text of the "Add Review" button on the single layout review blocks
        return esc_html__( 'Submit Rating', 'tourfic' );
    } );
    ```
*   **`tf_rating_modal_header_content`**
    ```php
    add_filter( 'tf_rating_modal_header_content', function( $html ) {
        // Prepend custom descriptions or headers to the review rating modal popup form
        return '<p class="rating-modal-disclaimer">Please share your honest experience with us!</p>';
    } );
    ```

---

## 4. Backend Booking Hooks

Developers can inject custom booking fields, classes, styles, and action handlers for WordPress backend booking forms.

### Backend Booking Form Title Filters
*   **`tf_hotel_backend_booking_form_title`**
    ```php
    add_filter( 'tf_hotel_backend_booking_form_title', function( $title ) {
        return esc_html__( 'Internal Resort booking', 'tourfic' );
    } );
    ```
*   **`tf_tour_backend_booking_form_title`**
    ```php
    add_filter( 'tf_tour_backend_booking_form_title', function( $title ) {
        return esc_html__( 'Internal Tour booking', 'tourfic' );
    } );
    ```
*   **`tf_apartment_backend_booking_form_title`**
    ```php
    add_filter( 'tf_apartment_backend_booking_form_title', function( $title ) {
        return esc_html__( 'Internal Rental booking', 'tourfic' );
    } );
    ```

---

### Backend Booking Form Class Filters
*   **`tf_hotel_backend_booking_form_class`**
    ```php
    add_filter( 'tf_hotel_backend_booking_form_class', function( $class ) {
        return $class . ' custom-resort-order';
    } );
    ```
*   **`tf_tour_backend_booking_form_class`**
    ```php
    add_filter( 'tf_tour_backend_booking_form_class', function( $class ) {
        return $class . ' custom-trip-order';
    } );
    ```
*   **`tf_apartment_backend_booking_form_class`**
    ```php
    add_filter( 'tf_apartment_backend_booking_form_class', function( $class ) {
        return $class . ' custom-lease-order';
    } );
    ```

---

### Backend Booking Form Card Filters
*   **`tf_hotel_backend_booking_form_card`**
    ```php
    add_filter( 'tf_hotel_backend_booking_form_card', function( $fields ) {
        $fields[] = '<h4>Custom Field Info Placeholder</h4>';
        return $fields;
    } );
    ```
*   **`tf_tour_backend_booking_form_card`**
    ```php
    add_filter( 'tf_tour_backend_booking_form_card', function( $fields ) {
        $fields[] = '<p>Note: Check active local weather forecasts.</p>';
        return $fields;
    } );
    ```
*   **`tf_apartment_backend_booking_form_card`**
    ```php
    add_filter( 'tf_apartment_backend_booking_form_card', function( $fields ) {
        $fields[] = '<div>Custom security instructions.</div>';
        return $fields;
    } );
    ```

---

### Before / After Backend Form Submission Action Hooks
*   **`tf_before_hotel_backend_booking_form` / `tf_after_hotel_backend_booking_form`**
    ```php
    add_action( 'tf_before_hotel_backend_booking_form', function() {
        echo '<div>Before Hotel Booking Panel</div>';
    } );
    add_action( 'tf_after_hotel_backend_booking_form', function() {
        echo '<div>After Hotel Booking Panel</div>';
    } );
    ```
*   **`tf_before_tour_backend_booking_form` / `tf_after_tour_backend_booking_form`**
    ```php
    add_action( 'tf_before_tour_backend_booking_form', function() {
        echo '<div>Before Tour Booking Panel</div>';
    } );
    add_action( 'tf_after_tour_backend_booking_form', function() {
        echo '<div>After Tour Booking Panel</div>';
    } );
    ```
*   **`tf_before_apartment_backend_booking_form` / `tf_after_apartment_backend_booking_form`**
    ```php
    add_action( 'tf_before_apartment_backend_booking_form', function() {
        echo '<div>Before Rental Booking Panel</div>';
    } );
    add_action( 'tf_after_apartment_backend_booking_form', function() {
        echo '<div>After Rental Booking Panel</div>';
    } );
    ```

---

## 5. Enquiry & Admin Email Hooks

Use these hooks to manage enquiry menu pages, customer question submissions, automated notification email texts, character sets, and email automation triggers.

### Enquiry Dashboard Admin Panel Filters
*   **`tf_hotel_enquiry_page_title` / `tf_hotel_enquiry_menu_title` / `tf_hotel_enquiry_page_heading`**
    ```php
    add_filter( 'tf_hotel_enquiry_page_title', function() { return 'Resort Inquiries'; } );
    add_filter( 'tf_hotel_enquiry_menu_title', function() { return 'Resort Inquiries'; } );
    add_filter( 'tf_hotel_enquiry_page_heading', function() { return 'Manage Resort Questions'; } );
    ```
*   **`tf_tour_enquiry_page_title` / `tf_tour_enquiry_menu_title` / `tf_tour_enquiry_page_heading`**
    ```php
    add_filter( 'tf_tour_enquiry_page_title', function() { return 'Trip Questions'; } );
    add_filter( 'tf_tour_enquiry_menu_title', function() { return 'Trip Questions'; } );
    add_filter( 'tf_tour_enquiry_page_heading', function() { return 'Manage Trip Inquiries'; } );
    ```
*   **`tf_apartment_enquiry_page_title` / `tf_apartment_enquiry_menu_title` / `tf_apartment_enquiry_page_heading`**
    ```php
    add_filter( 'tf_apartment_enquiry_page_title', function() { return 'Rental Questions'; } );
    add_filter( 'tf_apartment_enquiry_menu_title', function() { return 'Rental Questions'; } );
    add_filter( 'tf_apartment_enquiry_page_heading', function() { return 'Manage Rental Inquiries'; } );
    ```

---

### Enquiry Details Hooks (Action Hooks)
*   **`tf_before_enquiry_details`**
    ```php
    add_action( 'tf_before_enquiry_details', function() {
        echo '<p style="font-weight:bold;">Review customer security questions carefully.</p>';
    } );
    ```
*   **`tf_before_hotel_enquiry_details` / `tf_after_hotel_enquiry_details`**
    ```php
    add_action( 'tf_before_hotel_enquiry_details', function() { echo '<div>Before resort detail</div>'; } );
    add_action( 'tf_after_hotel_enquiry_details', function() { echo '<div>After resort detail</div>'; } );
    ```
*   **`tf_before_tour_enquiry_details` / `tf_after_tour_enquiry_details`**
    ```php
    add_action( 'tf_before_tour_enquiry_details', function() { echo '<div>Before trip details</div>'; } );
    add_action( 'tf_after_tour_enquiry_details', function() { echo '<div>After trip details</div>'; } );
    ```
*   **`tf_before_apartment_enquiry_details` / `tf_after_apartment_enquiry_details`**
    ```php
    add_action( 'tf_before_apartment_enquiry_details', function() { echo '<div>Before rental details</div>'; } );
    add_action( 'tf_after_apartment_enquiry_details', function() { echo '<div>After rental details</div>'; } );
    ```

---

### Email Customization & Charset Filters
These filters let developers change headers, character sets, and customize the text parameters (headings, greetings, and body descriptions) sent inside automated notification emails.

*   **`tourfic_mail_charset`**
    ```php
    add_filter( 'tourfic_mail_charset', function( $content_type_charset ) {
        // Change the character set of outgoing email templates to UTF-8 or custom headers
        return 'Content-Type: text/html; charset=UTF-8';
    } );
    ```
*   **`tf_email_strings`**
    ```php
    add_filter( 'tf_email_strings', function( $strings ) {
        // Override standard heading and greeting lines for Admin, Vendor, or Customer emails
        $strings['order']['admin']['heading'] = 'Attention Admin: A New Order has been Received!';
        return $strings;
    } );
    ```

---

## 6. Integration & API Hooks

Automate interactions with CRMs, Webhooks, Google Calendar integrations, or general custom settings storage events.

### Webhook Automation Hooks (Action Hooks)
*   **`enquiry_pabbly_form_trigger` / `enquiry_zapier_form_trigger`**
    ```php
    add_action( 'enquiry_pabbly_form_trigger', function( $post_id, $name, $email, $question ) {
        // Send enquiry parameters to external systems manually
    }, 10, 4 );
    add_action( 'enquiry_zapier_form_trigger', function( $post_id, $name, $email, $question ) {
        // Log custom Zapier payload data
    }, 10, 4 );
    ```
*   **`tf_services_pabbly_form_trigger` / `tf_services_zapier_form_trigger`**
    ```php
    add_action( 'tf_services_pabbly_form_trigger', function( $post_id, $post_basic_info, $tf_metabox_request ) {
        // Handle third-party configuration updates for services on save
    }, 10, 3 );
    add_action( 'tf_services_zapier_form_trigger', function( $post_id, $post_basic_info, $tf_metabox_request ) {
        // Sync custom service options with Zapier webhooks
    }, 10, 3 );
    ```
*   **`tf_new_order_pabbly_form_trigger` / `tf_new_order_zapier_form_trigger`**
    ```php
    add_action( 'tf_new_order_pabbly_form_trigger', function( $order_data, $billing, $shipping, $status ) {
        // Route order confirmation details to automation systems
    }, 10, 4 );
    add_action( 'tf_new_order_zapier_form_trigger', function( $order_data, $billing, $shipping, $status ) {
        // Custom order dispatch mapping for Zapier
    }, 10, 4 );
    ```

---

### Calendar Sync & General Action Hooks
*   **`tf_google_calendar_notice_box`**
    ```php
    add_action( 'tf_google_calendar_notice_box', function() {
        echo '<div class="notice">Sync status: Syncing in the background.</div>';
    } );
    ```
*   **`tf_after_booking_completed_calendar_data`**
    ```php
    add_filter( 'tf_after_booking_completed_calendar_data', function( $order_id, $order_data, $type ) {
        // Perform custom background tasks or calendar synchronization using order ID, order data, and service type
        return $order_id;
    }, 10, 3 );
    ```
*   **`tourfic_settings_save_hook`**
    ```php
    add_action( 'tourfic_settings_save_hook', function() {
        // Flush object caches when Tourfic options panel updates
        wp_cache_flush();
    } );
    ```
*   **`tf_setting_import_before_save`**
    ```php
    add_action( 'tf_setting_import_before_save', function( $settings_data ) {
        // Back up parameters or validate keys before actual database save
    } );
    ```

---

## 7. WooCommerce & Payment Hooks

Integrate custom fee processing, checkout workflows, and calendar tracking inside WooCommerce.

### Offline & Cart Hooks (Action/Filter Hooks)
*   **`tf_offline_payment_booking_confirmation`**
    ```php
    add_action( 'tf_offline_payment_booking_confirmation', function( $order_id, $order_data ) {
        // Log manual/offline order details to bookkeeping systems
    }, 10, 2 );
    ```
*   **`tf_hotel_booking_after_validation`**
    ```php
    add_action( 'tf_hotel_booking_after_validation', function( $post_id, $room_id, $check_in, $check_out, $adult, $child, $rooms ) {
        // Check extra reservation slots constraints
    }, 10, 7 );
    ```
*   **`tf_hotel_booking_processed`**
    ```php
    add_action( 'tf_hotel_booking_processed', function( $order_id, $order, $item_id, $item, $billing, $shipping, $info ) {
        // Custom invoice generation hooks
    }, 10, 7 );
    ```
*   **`tf_hotel_before_booking_added_to_cart` / `tf_hotel_after_booking_added_to_cart`**
    ```php
    add_action( 'tf_hotel_before_booking_added_to_cart', function( $post_id, $tf_room_data, $product_id ) {
        // Perform final checks or log data before room booking is added to WooCommerce cart
    }, 10, 3 );
    add_action( 'tf_hotel_after_booking_added_to_cart', function( $post_id, $tf_room_data, $product_id, $added_to_cart ) {
        // Log status or perform actions after hotel room is successfully added to cart
    }, 10, 4 );
    ```

---

## 8. Archive & Shortcode Card Customization Hooks

These action hooks allow developers to inject custom HTML content, badges, or scripts immediately before or after specific item listings inside archive and shortcode grids, or wrap global page template blocks.

### Global Template Wrapper Layout Hooks
Fired inside taxonomy, single item layouts, search results, and legacy design templates to wrap the main container blocks, perfect for breadcrumbs, notices, banner ads, or wrapper elements.

*   **`tf_before_container`**
    ```php
    add_action( 'tf_before_container', function() {
        echo '<div class="global-tourfic-container-wrapper">';
    } );
    ```
*   **`tf_after_container`**
    ```php
    add_action( 'tf_after_container', function() {
        echo '</div><!-- .global-tourfic-container-wrapper -->';
    } );
    ```

---

### Archive & Shortcode Card Customization Hooks
Allow injecting badges and footers around cards:

*   **`tf_car_archive_card_items_before`**
    ```php
    add_action( 'tf_car_archive_card_items_before', function() {
        echo '<div class="custom-card-badge">Instant Booking Available</div>';
    } );
    ```
*   **`tf_car_archive_card_items_after`**
    ```php
    add_action( 'tf_car_archive_card_items_after', function() {
        echo '<div class="custom-card-footer">Best Price Guaranteed</div>';
    } );
    ```
*   **`tf_apartment_archive_card_items_before`**
    ```php
    add_action( 'tf_apartment_archive_card_items_before', function() {
        echo '<div class="featured-banner">Top Rated Villa</div>';
    } );
    ```
*   **`tf_apartment_archive_card_items_after`**
    ```php
    add_action( 'tf_apartment_archive_card_items_after', function() {
        echo '<span class="deposit-notice">Flexible Cancellation</span>';
    } );
    ```
*   **`tf_apartment_archive_single_featured_card_design_one` / `tf_apartment_archive_single_card_design_one`**
    ```php
    add_filter( 'tf_apartment_archive_single_featured_card_design_one', function( $html ) {
        // Wrap or customize the HTML of single featured apartment archive card in Design 1
        return '<div class="featured-wrapper">' . $html . '</div>';
    } );
    add_filter( 'tf_apartment_archive_single_card_design_one', function( $html ) {
        // Wrap or customize the HTML of single standard apartment archive card in Design 1
        return '<div class="standard-card-wrapper">' . $html . '</div>';
    } );
    ```
*   **`tf_room_archive_roomd_items_before`**
    ```php
    add_action( 'tf_room_archive_roomd_items_before', function() {
        echo '<div class="room-special-deal">Weekend Discount Applied!</div>';
    } );
    ```
*   **`tf_room_archive_roomd_items_after`**
    ```php
    add_action( 'tf_room_archive_roomd_items_after', function() {
        echo '<div class="room-extra-amenities">Free Wi-Fi & Breakfast Included</div>';
    } );
    ```

---

### Shortcode & Archive Query Arguments Filters
Customize standard database queries when fetching service items via shortcode blocks and archive queries.

*   **`tf_archive_post_orderby` / `tf_archive_post_order`**
    ```php
    add_filter( 'tf_archive_post_orderby', function( $orderby ) {
        // Change default sorting criteria to sort by product title
        return 'title';
    } );
    add_filter( 'tf_archive_post_order', function( $order ) {
        // Change default sorting direction of archive posts to ascending order
        return 'ASC';
    } );
    ```

*   **`tf_apartment_external_booking_shortcode_args`**
    ```php
    add_filter( 'tf_apartment_external_booking_shortcode_args', function( $args ) {
        // Limit external listings query to a specific set of IDs or exclude test listings
        $args['post__not_in'] = array( 99, 102 );
        return $args;
    } );
    ```
*   **`tf_apartment_shortcode_query_args`**
    ```php
    add_filter( 'tf_apartment_shortcode_query_args', function( $args ) {
        // Customize the taxonomy parameters or order of queried apartments in the shortcode listings
        $args['orderby'] = 'meta_value_num';
        $args['meta_key'] = 'tf_apartment_min_price';
        $args['order'] = 'ASC';
        return $args;
    } );
    ```

---

## 9. Front-End Single Template & Booking Form Customization Hooks

These frontend templates action hooks allow developers to inject custom tabs, fields, information blocks, cancellation policies, itineraries, or style logic into single product view layout templates.

*   **`tf_car_cancellation`**
    ```php
    add_action( 'tf_car_cancellation', function( $post_id ) {
        // Output custom cancellation details on front-end single car rental views
        echo '<div class="car-cancel-policy">Cancellation: Free within 24 hours of booking.</div>';
    }, 10, 1 );
    ```
*   **`tf_car_extras`**
    ```php
    add_action( 'tf_car_extras', function( $car_extras, $post_id, $car_extra_sec_title ) {
        // Inject or append extra vehicle booking options
        echo '<p class="car-extra-sec-desc">Select optional GPS, child seats, or additional drivers below.</p>';
    }, 10, 3 );
    ```
*   **`after_itinerary_builder`**
    ```php
    add_action( 'after_itinerary_builder', function( $itineraries, $itinerary_map, $settings, $style ) {
        // Append terms, advice or warnings below tour itinerary builder schedules
        echo '<div class="itinerary-footer-note">Note: Timings are subject to local traffic and weather conditions.</div>';
    }, 10, 4 );
    ```
*   **`tf_hotel_features_filter`**
    ```php
    add_action( 'tf_hotel_features_filter', function( $rm_features ) {
        // Intercept or append feature highlights on single hotel rooms layouts
        echo '<span class="feature-highlight-badge">Eco Friendly Room</span>';
    }, 10, 1 );
    ```
*   **`tf_apartment_before_single_booking_form`**
    ```php
    add_action( 'tf_apartment_before_single_booking_form', function() {
        // Output trust badges above single apartment reserve button form
        echo '<div class="apt-trust-badge">Verified Host • 100% Secure Payment</div>';
    } );
    ```
*   **`tf_apartment_after_single_booking_form`**
    ```php
    add_action( 'tf_apartment_after_single_booking_form', function() {
        // Output assistance helpline notice under single apartment booking form
        echo '<p class="apt-support-notice">Need help booking? Call us 24/7 at +1-800-555-0199.</p>';
    } );
    ```
*   **`tf_car_before_single_booking_form`**
    ```php
    add_action( 'tf_car_before_single_booking_form', function() {
        // Output notices, alerts, or details before front-end car booking form wrapper
        echo '<div class="car-booking-pre-notice"><p>Note: Standard driver minimum age requirement is 21.</p></div>';
    } );
    ```
*   **`tf_car_after_single_booking_form`**
    ```php
    add_action( 'tf_car_after_single_booking_form', function() {
        // Output notices, alerts, or details after front-end car booking form wrapper
        echo '<div class="car-booking-post-notice"><p>Included: Unlimited mileage & third-party liability cover.</p></div>';
    } );
    ```
*   **`tf_hotel_single_widgets`**
    ```php
    add_action( 'tf_hotel_single_widgets', function() {
        // Render custom widget markup or shortcodes inside the hotel single layout sidebar
        echo '<div class="tf-single-widget-card"><h4>Featured Deals Widget</h4><p>Sign up to get 15% discount!</p></div>';
    } );
    ```
*   **`tf_single_hotel_sidebar_area_with_args`**
    ```php
    add_action( 'tf_single_hotel_sidebar_area_with_args', function( $post_id ) {
        // Render advanced dynamic widgets utilizing current hotel post ID
        $hotel_city = get_post_meta( $post_id, 'tf_hotel_city', true );
        echo '<div class="tf-custom-meta-widget"><p>Welcome to Hotel in ' . esc_html( $hotel_city ) . '!</p></div>';
    }, 10, 1 );
    ```
*   **`tf_hotel_gallery_video_url`**
    ```php
    add_filter( 'tf_hotel_gallery_video_url', function( $video_url ) {
        // Filter or modify the gallery video URL shown in the hotel hero layout
        return 'https://www.youtube.com/embed/dQw4w9WgXcQ';
    } );
    ```

---

## 10. Styling, Assets & Scripts Hooks

These styling filter hooks allow developers to intercept the base font-size, override the enqueued global inline CSS variable styles, and append custom conflict resolution styles.

*   **`tf_base_font_size`**
    ```php
    add_filter( 'tf_base_font_size', function( $size ) {
        // Adjust the base font size used across the Tourfic frontend pages
        return '15px';
    } );
    ```
*   **`tf-global-css`**
    ```php
    add_filter( 'tf-global-css', function( $css ) {
        // Append additional custom CSS variable overrides to root container variables
        $css .= ':root { --tf-custom-header-color: #333; }';
        return $css;
    } );
    ```
*   **`tf-custom-css-conflict-resolve`**
    ```php
    add_filter( 'tf-custom-css-conflict-resolve', function( $css ) {
        // Inject custom theme compatibility styles to fix element alignment or overlaps
        $css .= '.flatpickr-calendar { z-index: 99999 !important; }';
        return $css;
    } );
    ```

---

## 11. User Registration, Login & Role Customization Hooks

These hooks allow developers to inject custom HTML content before/after registration and login form wrappers, modify the auto-logout redirect destination URL, or customize the default custom user manager role display name.

*   **`tf_before_login_form` / `tf_after_login_form`**
    ```php
    add_filter( 'tf_before_login_form', function( $content ) {
        // Prepend announcements, links, or notices before Tourfic frontend login forms
        return $content . '<div class="login-notice"><p>Welcome back! Sign in to manage your active itineraries.</p></div>';
    } );
    add_filter( 'tf_after_login_form', function( $content ) {
        // Append helpline links or terms checkboxes after login form structures
        return $content . '<div class="login-helpline"><p>Having troubles? Contact support@themefic.com</p></div>';
    } );
    ```
*   **`tf_before_reg_form` / `tf_after_reg_form`**
    ```php
    add_filter( 'tf_before_reg_form', function( $content ) {
        // Prepend instructions or register promo texts above registration forms
        return $content . '<div class="reg-promo"><p>Sign up now and receive 50 bonus booking reward points!</p></div>';
    } );
    add_filter( 'tf_after_reg_form', function( $content ) {
        // Append additional details or disclaimer notices below registration form inputs
        return $content . '<small class="reg-disclaimer">By signing up you agree to our Terms of Service.</small>';
    } );
    ```
*   **`tf_auto_logout_redirect_url`**
    ```php
    add_filter( 'tf_auto_logout_redirect_url', function( $redirect_url ) {
        // Override the redirect destination URL after an automatic session logout occurs
        return home_url( '/session-expired-notice/' );
    } );
    ```
*   **`tf_manager_role_name`**
    ```php
    add_filter( 'tf_manager_role_name', function( $role_name ) {
        // Customize the user role label registered in WordPress for the Tourfic manager
        return esc_html__( 'Resort Booking Coordinator', 'tourfic' );
    } );
    ```

---

*This guide is maintained continuously. For additional development support, please refer to the official Tourfic documentation.*

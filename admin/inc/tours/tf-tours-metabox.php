<?php
//can't access directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Control core classes for avoid errors
if ( class_exists( 'CSF' ) ) {

    // Set a unique slug-like ID
    $prefix = 'tf_tours_option';

    // Create a metabox
    CSF::createMetabox( $prefix, array(
        'title'     => __( 'Tours Setting','tourfic' ),
        'post_type' => 'tf_tours',
        'context'   => 'advanced',
        'priority'  => 'high',
    ) );

    // Create a section
    CSF::createSection( $prefix, array(
        'title'  => __( 'General','tourfic' ),
        'fields' => array(

            // A text field
            array(
                'id'       => 'tour_as_featured',
                'type'     => 'switcher',
                'title'    => __( 'Set this tour as featured', 'tourfic' ),
                'subtitle' => __( 'To show the feature label', 'tourfic' ),
            ),

            array(
                'id'      => 'booking_type',
                'type'    => 'select',
                'title'   => __( 'Booking type', 'tourfic' ),
                'options' => array(
                    'instant'         => __( 'Instant Booking', 'tourfic' ),
                    'enquire'         => __( 'Enquire Booking', 'tourfic' ),
                    'instant_enquire' => __( 'Instant and Enquire Booking', 'tourfic' ),
                ),
            ),
            array(
                'id'      => 'tour_single_page',
                'type'    => 'select',
                'title'   => __( 'Tour single page layout', 'tourfic' ),
                'options' => array(
                    'instant' => __( 'Default', 'tourfic' ),
                ),
            ),
            array(
                'id'    => 'tour_gallery',
                'type'  => 'gallery',
                'title' => __( 'Tour Gallery', 'tourfic' ),
            ),
            array(
                'id'       => 'tour_video',
                'type'     => 'text',
                'title'    => __( 'Tour video', 'tourfic' ),
                'subtitle' => __( 'Place Youtube or Vimeo video url', 'tourfic' ),
            ),

        ),
    ) );

    // Create a section
    CSF::createSection( $prefix, array(
        'title'  => __( 'Location', 'tourfic' ),
        'fields' => array(

            array(
                'id'       => 'location',
                'type'     => 'map',
                'title'    => __( 'Tour Location', 'tourfic' ),
                'subtitle' => __( 'Select tour location', 'tourfic' ),
            ),
            array(
                'id'       => 'nearby_properties',
                'type'     => 'text',
                'title'    => __( 'Nearby properties', 'tourfic' ),
                'subtitle' => __( 'Input nearby properties', 'tourfic' ),
            ),

        ),
    ) );
    // Create a section
    CSF::createSection( $prefix, array(
        'title'  => __( 'Hightlights', 'tourfic' ),
        'fields' => array(

            array(
                'id'       => 'additional_information',
                'type'     => 'wp_editor',
                'title'    => __( 'Hightlights', 'tourfic' ),
            ),

        ),
    ) );

    // Create a section
    CSF::createSection( $prefix, array(
        'title'  => __( 'Information', 'tourfic' ),
        'fields' => array(

            array(
                'id'       => 'duration',
                'type'     => 'text',
                'title'    => __( 'Tour duration', 'tourfic' ),
                'subtitle' => __( 'Tour duration eg. 3 days', 'tourfic' ),
            ),
            array(
                'id'       => 'group_size',
                'type'     => 'text',
                'title'    => __( 'Group size', 'tourfic' ),
                'subtitle' => __( 'Group size eg. 10 people', 'tourfic' ),
            ),
            array(
                'id'       => 'language',
                'type'     => 'text',
                'title'    => __( 'Languages', 'tourfic' ),
                'subtitle' => __( 'Input languages seperated by comma(,)', 'tourfic' ),
            ),
            array(
                'id'       => 'min_days',
                'type'     => 'slider',
                'max'      => '30',
                'title'    => __( 'Minimum days to book before departure', 'tourfic' ),
                'subtitle' => __( 'Minimum days to book before departure', 'tourfic' ),
            ),
            array(
                'id'       => 'external_booking',
                'type'     => 'switcher',
                'title'    => __( 'Allow external booking', 'tourfic' ),
                'subtitle' => __( 'Clik to allow external booking', 'tourfic' ),
            ),
            array(
                'id'         => 'external_booking_link',
                'type'       => 'text',
                'title'      => __( 'External booking link', 'tourfic' ),
                'subtitle'   => __( 'Input external booking link', 'tourfic' ),
                'dependency' => array( 'external_booking', '==', 'true' ),
            ),
            array(
                'id'       => 'min_people',
                'type'     => 'text',
                'title'    => __( 'Minimum person', 'tourfic' ),
                'subtitle' => __( 'Minimum person to travel', 'tourfic' ),
            ),
            array(
                'id'       => 'max_people',
                'type'     => 'text',
                'title'    => __( 'Maximum person', 'tourfic' ),
                'subtitle' => __( 'Maximum person to travel', 'tourfic' ),
            ),

        ),
    ) );

    // Create a section
    CSF::createSection( $prefix, array(
        'title'  => __( 'Contact Info', 'tourfic' ),
        'fields' => array(

            array(
                'id'       => 'email',
                'type'     => 'text',
                'title'    => __( 'Email address', 'tourfic' ),
                'subtitle' => __( 'Input email address', 'tourfic' ),
            ),
            array(
                'id'       => 'phone',
                'type'     => 'text',
                'title'    => __( 'Phone Number', 'tourfic' ),
                'subtitle' => __( 'Input Phone Number', 'tourfic' ),
            ),
            array(
                'id'       => 'website',
                'type'     => 'text',
                'title'    => __( 'Website Url', 'tourfic' ),
                'subtitle' => __( 'Input website url', 'tourfic' ),
            ),
            array(
                'id'       => 'fax',
                'type'     => 'text',
                'title'    => __( 'Fax Number', 'tourfic' ),
                'subtitle' => __( 'Input Fax Number', 'tourfic' ),
            ),

        ),
    ) );

    CSF::createSection( $prefix, array(
        'title'  => __( 'Availability', 'tourfic' ),
        'fields' => array(
            array(
                'id'    => 'check_in',
                'type'  => 'date',
                'title' => __( 'Check In', 'tourfic' ),
            ),
            array(
                'id'    => 'check_out',
                'type'  => 'date',
                'title' => __( 'Check Out', 'tourfic' ),
            ),
        ),
    ) );

    CSF::createSection( $prefix, array(
        'title'  => __( 'Itinerary', 'tourfic' ),
        'fields' => array(
            array(
                'id'     => 'itinerary',
                'type'   => 'repeater',
                'title'  => __( 'Itinerary', 'tourfic' ),
                'fields' => array(
                    array(
                        'id'        => 'time',
                        'type'      => 'text',
                        'title'     => __( 'Time or Day', 'tourfic' ),
                        'subtitle'  => __( 'You can place the tour plan', 'tourfic' ),
                    ),
                    array(
                        'id'           => 'image',
                        'type'         => 'upload',
                        'title'        => 'Upload Image',
                        'library'      => 'image',
                        'placeholder'  => 'http://',
                        'button_title' => 'Add Image',
                        'remove_title' => 'Remove Image',
                      ),
                    array(
                        'id'    => 'desc',
                        'type'  => 'textarea',
                        'title' => __( 'Description', 'tourfic' ),
                    ),
                ),
            ),
        ),
    ) );

    CSF::createSection( $prefix, array(
        'title'  => __( 'FAQs', 'tourfic' ),
        'fields' => array(
            array(
                'id'     => 'faqs',
                'type'   => 'repeater',
                'title'  => __( 'FAQs', 'tourfic' ),
                'fields' => array(
                    array(
                        'id'    => 'title',
                        'type'  => 'text',
                        'title' => __( 'FAQ title', 'tourfic' ),
                    ),
                    array(
                        'id'    => 'desc',
                        'type'  => 'textarea',
                        'title' => __( 'FAQ description', 'tourfic' ),
                    ),
                ),
            ),
        ),
    ) );

    CSF::createSection( $prefix, array(
        'title'  => __( 'Terms & Conditions', 'tourfic' ),
        'fields' => array(
            array(
                'id'     => 'terms_conditions',
                'type'   => 'wp_editor',
                'title'  => __( 'Terms & Conditions', 'tourfic' )
            )
        )
    ) );

}

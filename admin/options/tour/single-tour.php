<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

$badge_up = '<div class="tf-csf-badge"><span class="tf-upcoming">' .__("Upcoming", "tourfic"). '</span></div>';
$badge_pro = '<div class="tf-csf-badge"><span class="tf-pro">' .__("Pro Feature", "tourfic"). '</span></div>';
$badge_up_pro = '<div class="tf-csf-badge"><span class="tf-upcoming">' .__("Upcoming", "tourfic"). '</span><span class="tf-pro">' .__("Pro Feature", "tourfic"). '</span></div>';

if ( class_exists( 'CSF' ) ) {

    $prefix = 'tf_tours_option';

    // Create a metabox
    CSF::createMetabox( $prefix, array(
        'title'     => __( 'Tour Setting', 'tourfic' ),
        'post_type' => 'tf_tours',
        'context'   => 'advanced',
        'priority'  => 'high',
        'theme'     => 'dark',
    ) );

    // General
    CSF::createSection( $prefix, array(
        'title'  => __( 'General', 'tourfic' ),
        'fields' => array(

            array(
                'id'       => 'tour_as_featured',
                'type'     => 'switcher',
                'title'    => __( 'Set this tour as featured', 'tourfic' ),
                'subtitle' => __( 'Tour will be shown under featured sections', 'tourfic' ),
            ),

            array(
                'id'      => 'tour_single_page',
                'type'    => 'select',
                'title'   => __( 'Single Tour Page Layout', 'tourfic' ),
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
                'id'       => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'     => 'text',
                'title'    => __( 'Tour video', 'tourfic' ),
                'subtitle'   => $badge_pro,
            ),

        ),
    ) );

    // Location
    CSF::createSection( $prefix, array(
        'title'  => __( 'Location', 'tourfic' ),
        'fields' => array(

            array(
                'id'       => 'text_location',
                'type'     => 'textarea',
                'title'    => __( 'Tour Location', 'tourfic' ),
                'subtitle' => __( 'Manually enter your tour location', 'tourfic' ),
                'attributes' => array(
                    'required' => 'required',
                ),
            ),

            array(
                'id'       => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'     => 'map',
                'title'    => __( 'Tour Location Pro (Auto Suggestion)', 'tourfic' ),
                'subtitle' => __( 'Location suggestions will be provided from Google' .$badge_pro, 'tourfic' ),
                'height'   => '250px',
                'settings' => array(
                    'scrollWheelZoom' => true,
                )
            ),

        ),
    ) );

    // Information
    CSF::createSection( $prefix, array(
        'title'  => __( 'Information', 'tourfic' ),
        'fields' => array(

            array(
                'id'       => 'duration',
                'type'     => 'text',
                'title'    => __( 'Tour Duration', 'tourfic' ),
                'subtitle' => __( 'E.g. 3 days', 'tourfic' ),
            ),
            array(
                'id'       => 'info_type',
                'type'     => 'text',
                'title'    => __( 'Tour Type', 'tourfic' ),
                'subtitle' => __( 'E.g. Fixed Tour', 'tourfic' ),
            ),
            array(
                'id'       => 'group_size',
                'type'     => 'text',
                'title'    => __( 'Group Size', 'tourfic' ),
                'subtitle' => __( 'E.g. 10 people', 'tourfic' ),
            ),
            array(
                'id'       => 'language',
                'type'     => 'text',
                'title'    => __( 'Languages', 'tourfic' ),
                'subtitle' => __( 'Input languages seperated by comma (,)', 'tourfic' ),
            ),

            array(
                'id'    => 'additional_information',
                'type'  => 'wp_editor',
                'title' => __( 'Tour Hightlights', 'tourfic' ),
				'subtitle' => __( 'Enter a summary or full description of your tour', 'tourfic' ),
            ),

        ),
    ) );

    // Contact Info
    CSF::createSection( $prefix, array(
        'title'  => __( 'Contact Info', 'tourfic' ),
        'fields' => array(

            array(
                'id'       => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'     => 'text',
                'title'    => __( 'Email address', 'tourfic' ),
                'subtitle'   => $badge_up_pro,
            ),
            array(
                'id'       => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'     => 'text',
                'title'    => __( 'Phone Number', 'tourfic' ),
                'subtitle'   => $badge_up_pro,
            ),
            array(
                'id'       => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'     => 'text',
                'title'    => __( 'Website Url', 'tourfic' ),
                'subtitle'   => $badge_up_pro,
            ),
            array(
                'id'       => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'     => 'text',
                'title'    => __( 'Fax Number', 'tourfic' ),
                'subtitle'   => $badge_up_pro,
            ),

        ),
    ) );

    // Tour Extra
    CSF::createSection( $prefix, array(
        'title'  => __( 'Tour Extra', 'tourfic' ),
        'fields' => array(
            array(
                'id'     => 'tour-extra',
                'type'   => 'repeater',
                'title'  => __( 'Extra Services on Tour', 'tourfic' ),
                'fields' => array(
                    array(
                        'id'    => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'  => 'text',
                        'title' => __( 'Title', 'tourfic' ),
                        'subtitle'   => $badge_pro,
                    ),
                    array(
                        'id'    => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'  => 'textarea',
                        'title' => __( 'Short Description', 'tourfic' ),
                        'subtitle'   => $badge_pro,
                    ),
                    array(
                        'id'         => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'       => 'text',
                        'title'      => __( 'Price', 'tourfic' ),
                        'subtitle'   => $badge_pro,
                        'attributes' => array(
                            'min' => '0',
                        ),
                    ),
                ),
            ),
        ),
    ) );

    // Price
    CSF::createSection($prefix, array(
        'title'  => __('Price Settings', 'tourfic'),
        'fields' => array(
            array(
                'id'       => 'pricing',
                'type'     => 'select',
                'title'    => __('Pricing rule', 'tourfic'),
                'subtitle' => __('Input pricing rule', 'tourfic'),
                'class'    => 'pricing',
                'options'  => [
                    'person' => __('Person', 'tourfic'),
                    'group'  => __('Group (Pro)', 'tourfic'),
                ],
                'default' => 'person',
            ),
            array(
                'id'         => 'adult_price',
                'type'       => 'number',
                'title'      => __('Price for Adult', 'tourfic'),
                'subtitle'   => __('Input adult price', 'tourfic'),
                'dependency' => [
                    array('pricing', '==', 'person'),
                    ['disable_adult_price', '==', 'false']
                ],
                'attributes' => array(
                    'min' => '0',
                ),
            ),
            array(
                'id'         => 'child_price',
                'type'       => 'number',
                'dependency' => [
                    array('pricing', '==', 'person'),
                    ['disable_child_price', '==', 'false']
                ],
                'title'      => __('Price for Child', 'tourfic'),
                'subtitle'   => __('Input child price', 'tourfic'),
                'attributes' => array(
                    'min' => '0',
                ),
            ),
            array(
                'id'         => 'infant_price',
                'type'       => 'number',
                'dependency' => [
                    array('pricing', '==', 'person'),
                    ['disable_infant_price', '==', 'false'],
                    ['disable_adult_price', '==', 'false'],
                ],
                'title'      => __('Price for Infant', 'tourfic'),
                'subtitle'   => __('Input infant price', 'tourfic'),
                'attributes' => array(
                    'min' => '0',
                ),
            ),
            array(
                'id'         => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'       => 'number',
                'dependency' => array('pricing', '==', 'group'),
                'title'      => __('Group Price', 'tourfic'),
                'subtitle'   => __('Input group price' .$badge_pro, 'tourfic'),
                'attributes' => array(
                    'min' => '0',
                ),
            ),
            array(
                'id'       => 'discount_type',
                'type'     => 'select',
                'title'    => __('Discount Type', 'tourfic'),
                'subtitle' => __('Select discount type Percent or Fixed', 'tourfic'),
                'options'  => array(
                    'none'    => __('None', 'tourfic'),
                    'percent' => __('Percent', 'tourfic'),
                    'fixed'   => __('Fixed', 'tourfic'),
                ),
                'default'  => 'none',
            ),
            array(
                'id'         => 'discount_price',
                'type'       => 'number',
                'title'      => __('Discount Price', 'tourfic'),
                'subtitle'   => __('Input discount price in number', 'tourfic'),
                'attributes' => array(
                    'min' => '0',
                ),
                'dependency' => array(
                    array('discount_type', '!=', 'none'),
                ),
            ),
            array(
                'id'       => 'disable_adult_price',
                'type'     => 'switcher',
                'title'    => __('Disable adult price', 'tourfic'),
                'subtitle' => __('Hide No of adult in booking form', 'tourfic'),
            ),
            array(
                'id'       => 'disable_child_price',
                'type'     => 'switcher',
                'title'    => __('Disable children price', 'tourfic'),
                'subtitle' => __('Hide No of children in booking form', 'tourfic'),
            ),
            array(
                'id'       => 'disable_infant_price',
                'type'     => 'switcher',
                'title'    => __('Disable infant price', 'tourfic'),
                'subtitle' => __('Hide No of infant in booking form', 'tourfic'),
            ),

        ),
    ));

    // Availability
    CSF::createSection($prefix, array(
        'title'  => __('Availability', 'tourfic'),
        'fields' => array(
            array(
                'id'       => 'type',
                'type'     => 'select',
                'title'    => __('Tour Type', 'tourfic'),
                'subtitle' => __('Fixed: Tour will be available on a fixed date. Continous: Tour will be available every month within the mentioned range.', 'tourfic'),
                'class'    => 'tour-type',
                'options'  => [
                    'continuous' => __('Continuous', 'tourfic'),
                    'fixed'      => __('Fixed (Pro)', 'tourfic'),              
                ],
                'default' => 'continuous',
            ),          

            /**
             * Continuous Avaialbility
             */
            array(
                'id'         => 'custom_avail',
                'type'       => 'switcher',
                'title'      => __('Custom Availability', 'tourfic'),
                'subtitle'   => $badge_pro,
                'dependency' => array('type', '==', 'continuous'),
                'text_on'  => __('Yes', 'tourfic'),
                'text_off' => __('No', 'tourfic'),
            ),

            /**
             * Custom: Yes
             * 
             * Continuous Avaialbility
             */
            array(
                'id'         => 'cont_custom_date',
                'type'       => 'repeater',
                'title'      => __('Allowed Dates', 'tourfic'),
                'subtitle'   => $badge_pro,
                'dependency' => array(
                    array('type', '==', 'continuous'),
                    array('custom_avail', '==', 'true'),
                ),
                'fields'     => array(
                    array(
                        'id'    => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'  => 'datetime',
                        'title' => __('Date Range', 'tourfic'),
                        'subtitle'   => $badge_pro,
                        'settings' => array(
                            'dateFormat'      => 'Y/m/d'
                        ),
                        'from_to'   => true,
                        'attributes' => array(
                            'autocomplete' => 'off',
                        ),
                    ),
                    array(
                        'id'    => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'  => 'number',
                        'title' => __('Min people', 'tourfic'),
                        'subtitle'   => $badge_pro,
                    ),
                    array(
                        'id'    => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'  => 'number',
                        'title' => __('Maximum people', 'tourfic'),
                        'subtitle'   => $badge_pro,
                    ),
                    array(
                        'id'       => 'pricing',
                        'type'     => 'select',
                        'title'    => __('Pricing rule', 'tourfic'),
                        'subtitle' => __('Input pricing rule' .$badge_pro, 'tourfic'),
                        'class'    => 'pricing',
                        'options'  => [
                            'person' => __('Person', 'tourfic'),
                            'group'  => __('Group', 'tourfic'),
                        ],
                        'default' => 'person',
                    ),
                    array(
                        'id'         => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'       => 'number',
                        'title'      => __('Price for Adult', 'tourfic'),
                        'subtitle'   => __('Input adult price' .$badge_pro, 'tourfic'),
                        'dependency' => array( 'pricing', '==', 'person' ),
                        'attributes' => array(
                            'min' => '0',
                        ),
                    ),
                    array(
                        'id'         => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'       => 'number',
                        'title'      => __('Price for Child', 'tourfic'),
                        'subtitle'   => __('Input child price' .$badge_pro, 'tourfic'),
                        'dependency' =>  array('pricing', '==', 'person'),
                        'attributes' => array(
                            'min' => '0',
                        ),
                    ),
                    array(
                        'id'         => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'       => 'number',
                        'title'      => __('Price for Infant', 'tourfic'),
                        'subtitle'   => __('Input infant price' .$badge_pro, 'tourfic'),
                        'dependency' => array('pricing', '==', 'person'),
                        'attributes' => array(
                            'min' => '0',
                        ),
                    ),
                    array(
                        'id'         => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'       => 'number',
                        'dependency' => array('pricing', '==', 'group'),
                        'title'      => __('Group Price', 'tourfic'),
                        'subtitle'   => __('Input group price' .$badge_pro, 'tourfic'),
                        'attributes' => array(
                            'min' => '0',
                        ),
                    ),
                    array(
                        'id'     => 'allowed_time',
                        'type'   => 'repeater',
                        'title'  => __('Allowed Time', 'tourfic'),
                        'subtitle'   => $badge_pro,
                        'fields' => array(

                            array(
                                'id'       => '',
                                'class' => 'tf-csf-disable tf-csf-pro',
                                'type'     => 'datetime',
                                'title'    => __('Time', 'tourfic'),
                                'subtitle' => __('Only Time' .$badge_pro, 'tourfic'),
                                'settings' => array(
                                    'noCalendar' => true,
                                    'enableTime' => true,
                                    'dateFormat' => "h:i K"
                                ),
                            ),


                        ),
                    ),

                ),
            ),

            /**
             * Custom: No
             * 
             * Continuous Avaialbility
             */
            array(
                'id'       => 'cont_min_people',
                'type'     => 'number',
                'title'    => __('Minimum Person', 'tourfic'),
                'subtitle' => __('Minimum person to travel', 'tourfic'),
                'dependency' => array(
                    array('type', '==', 'continuous'),
                    array('custom_avail', '==', 'false'),
                ),
            ),
            array(
                'id'       => 'cont_max_people',
                'type'     => 'number',
                'title'    => __('Maximum Person', 'tourfic'),
                'subtitle' => __('Maximum person to travel', 'tourfic'),
                'dependency' => array(
                    array('type', '==', 'continuous'),
                    array('custom_avail', '==', 'false'),
                ),
            ),
            array(
                'id'     => 'allowed_time',
                'type'   => 'repeater',
                'title'  => __('Allowed Time', 'tourfic'),
                'subtitle'   => $badge_pro,
                'dependency' => array(
                    array('type', '==', 'continuous'),
                    array('custom_avail', '==', 'false'),
                ),
                'fields' => array(

                    array(
                        'id'       => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'     => 'datetime',
                        'title'    => __('Time', 'tourfic'),
                        'subtitle' => __('Only Time' .$badge_pro, 'tourfic'),
                        'settings' => array(
                            'noCalendar' => true,
                            'enableTime' => true,
                            'dateFormat' => "h:i K"
                        ),
                    ),


                ),
            ),

            array(
                'type'    => 'subheading',
                'content' => __('Disabled Dates', 'tourfic'),
                'dependency' => array(
                    array('type', '==', 'continuous'),
                    array('custom_avail', '==', 'false'),
                ),
            ),

            array(
                'id'         => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'       => 'checkbox',
                'title'      => __('Select day to disable', 'tourfic'),
                'subtitle'   => $badge_pro,
                'dependency' => array(
                    array('type', '==', 'continuous'),
                    array('custom_avail', '==', 'false'),
                ),
                'inline'     => true,
                'options'    => array(
                    '0' => __('Sunday', 'tourfic'),
                    '1' => __('Monday', 'tourfic'),
                    '2' => __('Tuesday', 'tourfic'),
                    '3' => __('Wednesday', 'tourfic'),
                    '4' => __('Thursday', 'tourfic'),
                    '5' => __('Friday', 'tourfic'),
                    '6' => __('Saturday', 'tourfic'),
                ),
            ),


            array(
                'id'     => 'disable_range',
                'type'   => 'repeater',
                'title'  => __('Disabled Date Range', 'tourfic'),
                'max' => 2,
                'dependency' => array(
                    array('type', '==', 'continuous'),
                    array('custom_avail', '==', 'false'),
                ),
                'fields' => array(

                    array(
                        'id'       => 'date',
                        'type'     => 'datetime',
                        'title'    => __('Select date range', 'tourfic'),
                        'from_to'  => true,
                        'settings' => array(
                            'dateFormat' => 'Y/m/d',
                        ),
                        'attributes' => array(
                            'autocomplete' => 'off',
                        ),
                    ),

                ),
            ),

            array(
                'id'    => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'  => 'datetime',
                'title' => __('Disable Specific Dates', 'tourfic'),
                'subtitle'   => $badge_pro,
                'dependency' => array(
                    array('type', '==', 'continuous'),
                    array('custom_avail', '==', 'false'),
                ),
                'settings' => array(
                    'mode' => 'multiple',
                    'dateFormat' => 'Y/m/d',
                ),
                'attributes' => array(
                    'autocomplete' => 'off',
                ),
            ),

            /**
             * Fixed Availability
             */
            array(
                'id'         => 'fixed_availability',
                'type'       => 'fieldset',
                'title'      => __('Availability', 'tourfic'),
                'subtitle'   => __('Input your availability' .$badge_pro, 'tourfic'),
                'dependency' => array('type', '==', 'fixed'),
                'class'      => 'fixed_availability',
                'fields'     => array(
                    array(
                        'id'       => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'     => 'datetime',
                        'title'    => __('Check In', 'tourfic'),
                        'subtitle' => __('Select check in date' .$badge_pro, 'tourfic'),
                        'class'    => 'check-in',
                        'settings' => array(
                            'dateFormat'      => 'Y/m/d'
                        ),
                        'attributes' => array(
                            'autocomplete' => 'off',
                        ),
                        'from_to'   => true,
                    ),
                    array(
                        'id'       => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'     => 'number',
                        'title'    => __('Minimum People', 'tourfic'),
                        'subtitle' => __('Minimum seat number' .$badge_pro, 'tourfic'),
                    ),
                    array(
                        'id'       => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'     => 'number',
                        'title'    => __('Maximum People', 'tourfic'),
                        'subtitle' => __('Maximum seat number' .$badge_pro, 'tourfic'),
                    ),
                ),
            ),

        ),
    ));

    CSF::createSection( $prefix, array(
        'title'  => __( 'Booking', 'tourfic' ),
        'fields' => array(

            array(
                'id'       => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'     => 'slider',
                'max'      => '30',
                'title'    => __( 'Minimum days to book before departure', 'tourfic' ),
                'subtitle' => __( 'Customer can not book after this date' .$badge_pro, 'tourfic' ),
            ),
        ),
    ) );

    CSF::createSection( $prefix, array(
        'title'  => __( 'Exclude/Include', 'tourfic' ),
        'fields' => array(
            array(
                'id'     => 'inc',
                'type'   => 'repeater',
                'title'  => __( 'Include', 'tourfic' ),
                'max' => 5,
                'fields' => array(
                    array(
                        'id'       => 'inc',
                        'type'     => 'text',
                        'title'    => __( 'Included', 'tourfic' ),
                        'subtitle' => __( 'Included facilites', 'tourfic' ),
                    ),
                ),
            ),
            array(
                'id'     => 'exc',
                'type'   => 'repeater',
                'title'  => __( 'Exclude', 'tourfic' ),
                'max' => 5,
                'fields' => array(
                    array(
                        'id'       => 'exc',
                        'type'     => 'text',
                        'title'    => __( 'Excluded', 'tourfic' ),
                        'subtitle' => __( 'Excluded facilites', 'tourfic' ),
                    ),
                ),
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
                'max' => 5,
                'fields' => array(
                    array(
                        'id'       => 'time',
                        'type'     => 'text',
                        'title'    => __( 'Time or Day', 'tourfic' ),
                        'subtitle' => __( 'You can place the tour plan', 'tourfic' ),
                    ),
                    array(
                        'id'       => 'title',
                        'type'     => 'text',
                        'title'    => __( 'Title', 'tourfic' ),
                        'subtitle' => __( 'Input the title here', 'tourfic' ),
                    ),
                    array(
                        'id'           => 'image',
                        'type'         => 'upload',
                        'title'        => __('Upload Image', 'tourfic' ),
                        'library'      => 'image',
                        'placeholder'  => 'http://',
                        'button_title' => __('Add Image', 'tourfic' ),
                        'remove_title' => __('Remove Image', 'tourfic' ),
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
                        'title' => __( 'FAQ Title', 'tourfic' ),
                    ),
                    array(
                        'id'    => 'desc',
                        'type'  => 'textarea',
                        'title' => __( 'FAQ Description', 'tourfic' ),
                    ),
                ),
            ),
        ),
    ) );

    CSF::createSection( $prefix, array(
        'title'  => __( 'Terms & Conditions', 'tourfic' ),
        'fields' => array(
            array(
                'id'    => 'terms_conditions',
                'type'  => 'wp_editor',
                'title' => __( 'Terms & Conditions', 'tourfic' ),
            ),
        ),
    ) );

}

?>
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
        'title'     => __( 'Tour Setting', TFD ),
        'post_type' => 'tf_tours',
        'context'   => 'advanced',
        'priority'  => 'high',
        'theme'     => 'dark',
    ) );

    // General
    CSF::createSection( $prefix, array(
        'title'  => __( 'General', TFD ),
        'fields' => array(

            array(
                'id'       => 'tour_as_featured',
                'type'     => 'switcher',
                'title'    => __( 'Set this tour as featured', TFD ),
                'subtitle' => __( 'Tour will be shown under featured sections', TFD ),
            ),

            array(
                'id'      => 'tour_single_page',
                'type'    => 'select',
                'title'   => __( 'Single Tour Page Layout', TFD ),
                'options' => array(
                    'instant' => __( 'Default', TFD ),
                ),
            ),

            array(
                'id'    => 'tour_gallery',
                'type'  => 'gallery',
                'title' => __( 'Tour Gallery', TFD ),
            ),

            array(
                'id'       => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'     => 'text',
                'title'    => __( 'Tour video', TFD ),
                'subtitle'   => $badge_pro,
            ),

        ),
    ) );

    // Location
    CSF::createSection( $prefix, array(
        'title'  => __( 'Location', TFD ),
        'fields' => array(

            array(
                'id'       => 'text_location',
                'type'     => 'textarea',
                'title'    => __( 'Tour Location', TFD ),
                'subtitle' => __( 'Manually enter your tour location', TFD ),
                'attributes' => array(
                    'required' => 'required',
                ),
            ),

            array(
                'id'       => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'     => 'map',
                'title'    => __( 'Tour Location Pro (Auto Suggestion)', TFD ),
                'subtitle' => __( 'Location suggestions will be provided from Google' .$badge_pro, TFD ),
                'height'   => '250px',
                'settings' => array(
                    'scrollWheelZoom' => true,
                )
            ),

        ),
    ) );

    // Information
    CSF::createSection( $prefix, array(
        'title'  => __( 'Information', TFD ),
        'fields' => array(

            array(
                'id'       => 'duration',
                'type'     => 'text',
                'title'    => __( 'Tour Duration', TFD ),
                'subtitle' => __( 'E.g. 3 days', TFD ),
            ),
            array(
                'id'       => 'info_type',
                'type'     => 'text',
                'title'    => __( 'Tour Type', TFD ),
                'subtitle' => __( 'E.g. Fixed Tour', TFD ),
            ),
            array(
                'id'       => 'group_size',
                'type'     => 'text',
                'title'    => __( 'Group Size', TFD ),
                'subtitle' => __( 'E.g. 10 people', TFD ),
            ),
            array(
                'id'       => 'language',
                'type'     => 'text',
                'title'    => __( 'Languages', TFD ),
                'subtitle' => __( 'Input languages seperated by comma (,)', TFD ),
            ),

            array(
                'id'    => 'additional_information',
                'type'  => 'wp_editor',
                'title' => __( 'Tour Hightlights', TFD ),
				'subtitle' => __( 'Enter a summary or full description of your tour', TFD ),
            ),

        ),
    ) );

    // Contact Info
    CSF::createSection( $prefix, array(
        'title'  => __( 'Contact Info', TFD ),
        'fields' => array(

            array(
                'id'       => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'     => 'text',
                'title'    => __( 'Email address', TFD ),
                'subtitle'   => $badge_up_pro,
            ),
            array(
                'id'       => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'     => 'text',
                'title'    => __( 'Phone Number', TFD ),
                'subtitle'   => $badge_up_pro,
            ),
            array(
                'id'       => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'     => 'text',
                'title'    => __( 'Website Url', TFD ),
                'subtitle'   => $badge_up_pro,
            ),
            array(
                'id'       => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'     => 'text',
                'title'    => __( 'Fax Number', TFD ),
                'subtitle'   => $badge_up_pro,
            ),

        ),
    ) );

    // Tour Extra
    CSF::createSection( $prefix, array(
        'title'  => __( 'Tour Extra', TFD ),
        'fields' => array(
            array(
                'id'     => 'tour-extra',
                'type'   => 'repeater',
                'title'  => __( 'Extra Services on Tour', TFD ),
                'fields' => array(
                    array(
                        'id'    => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'  => 'text',
                        'title' => __( 'Title', TFD ),
                        'subtitle'   => $badge_pro,
                    ),
                    array(
                        'id'    => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'  => 'textarea',
                        'title' => __( 'Short Description', TFD ),
                        'subtitle'   => $badge_pro,
                    ),
                    array(
                        'id'         => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'       => 'text',
                        'title'      => __( 'Price', TFD ),
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
        'title'  => __('Price Settings', TFD),
        'fields' => array(
            array(
                'id'       => 'pricing',
                'type'     => 'select',
                'title'    => __('Pricing rule', TFD),
                'subtitle' => __('Input pricing rule', TFD),
                'class'    => 'pricing',
                'options'  => [
                    'person' => __('Person', TFD),
                    'group'  => __('Group (Pro)', TFD),
                ],
                'default' => 'person',
            ),
            array(
                'id'         => 'adult_price',
                'type'       => 'number',
                'title'      => __('Price for Adult', TFD),
                'subtitle'   => __('Input adult price', TFD),
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
                'title'      => __('Price for Child', TFD),
                'subtitle'   => __('Input child price', TFD),
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
                'title'      => __('Price for Infant', TFD),
                'subtitle'   => __('Input infant price', TFD),
                'attributes' => array(
                    'min' => '0',
                ),
            ),
            array(
                'id'         => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'       => 'number',
                'dependency' => array('pricing', '==', 'group'),
                'title'      => __('Group Price', TFD),
                'subtitle'   => __('Input group price' .$badge_pro, TFD),
                'attributes' => array(
                    'min' => '0',
                ),
            ),
            array(
                'id'       => 'discount_type',
                'type'     => 'select',
                'title'    => __('Discount Type', TFD),
                'subtitle' => __('Select discount type Percent or Fixed', TFD),
                'options'  => array(
                    'none'    => __('None', TFD),
                    'percent' => __('Percent', TFD),
                    'fixed'   => __('Fixed', TFD),
                ),
                'default'  => 'none',
            ),
            array(
                'id'         => 'discount_price',
                'type'       => 'number',
                'title'      => __('Discount Price', TFD),
                'subtitle'   => __('Input discount price in number', TFD),
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
                'title'    => __('Disable adult price', TFD),
                'subtitle' => __('Hide No of adult in booking form', TFD),
            ),
            array(
                'id'       => 'disable_child_price',
                'type'     => 'switcher',
                'title'    => __('Disable children price', TFD),
                'subtitle' => __('Hide No of children in booking form', TFD),
            ),
            array(
                'id'       => 'disable_infant_price',
                'type'     => 'switcher',
                'title'    => __('Disable infant price', TFD),
                'subtitle' => __('Hide No of infant in booking form', TFD),
            ),

        ),
    ));

    // Availability
    CSF::createSection($prefix, array(
        'title'  => __('Availability', TFD),
        'fields' => array(
            array(
                'id'       => 'type',
                'type'     => 'select',
                'title'    => __('Tour Type', TFD),
                'subtitle' => __('Fixed: Tour will be available on a fixed date. Continous: Tour will be available every month within the mentioned range.', TFD),
                'class'    => 'tour-type',
                'options'  => [
                    'continuous' => __('Continuous', TFD),
                    'fixed'      => __('Fixed (Pro)', TFD),              
                ],
                'default' => 'continuous',
            ),          

            /**
             * Continuous Avaialbility
             */
            array(
                'id'         => 'custom_avail',
                'type'       => 'switcher',
                'title'      => __('Custom Availability', TFD),
                'subtitle'   => $badge_pro,
                'dependency' => array('type', '==', 'continuous'),
                'text_on'  => __('Yes', TFD),
                'text_off' => __('No', TFD),
            ),

            /**
             * Custom: Yes
             * 
             * Continuous Avaialbility
             */
            array(
                'id'         => 'cont_custom_date',
                'type'       => 'repeater',
                'title'      => __('Allowed Dates', TFD),
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
                        'title' => __('Date Range', TFD),
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
                        'title' => __('Min people', TFD),
                        'subtitle'   => $badge_pro,
                    ),
                    array(
                        'id'    => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'  => 'number',
                        'title' => __('Maximum people', TFD),
                        'subtitle'   => $badge_pro,
                    ),
                    array(
                        'id'       => 'pricing',
                        'type'     => 'select',
                        'title'    => __('Pricing rule', TFD),
                        'subtitle' => __('Input pricing rule' .$badge_pro, TFD),
                        'class'    => 'pricing',
                        'options'  => [
                            'person' => __('Person', TFD),
                            'group'  => __('Group', TFD),
                        ],
                        'default' => 'person',
                    ),
                    array(
                        'id'         => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'       => 'number',
                        'title'      => __('Price for Adult', TFD),
                        'subtitle'   => __('Input adult price' .$badge_pro, TFD),
                        'dependency' => array( 'pricing', '==', 'person' ),
                        'attributes' => array(
                            'min' => '0',
                        ),
                    ),
                    array(
                        'id'         => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'       => 'number',
                        'title'      => __('Price for Child', TFD),
                        'subtitle'   => __('Input child price' .$badge_pro, TFD),
                        'dependency' =>  array('pricing', '==', 'person'),
                        'attributes' => array(
                            'min' => '0',
                        ),
                    ),
                    array(
                        'id'         => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'       => 'number',
                        'title'      => __('Price for Infant', TFD),
                        'subtitle'   => __('Input infant price' .$badge_pro, TFD),
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
                        'title'      => __('Group Price', TFD),
                        'subtitle'   => __('Input group price' .$badge_pro, TFD),
                        'attributes' => array(
                            'min' => '0',
                        ),
                    ),
                    array(
                        'id'     => 'allowed_time',
                        'type'   => 'repeater',
                        'title'  => __('Allowed Time', TFD),
                        'subtitle'   => $badge_pro,
                        'fields' => array(

                            array(
                                'id'       => '',
                                'class' => 'tf-csf-disable tf-csf-pro',
                                'type'     => 'datetime',
                                'title'    => __('Time', TFD),
                                'subtitle' => __('Only Time' .$badge_pro, TFD),
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
                'title'    => __('Minimum Person', TFD),
                'subtitle' => __('Minimum person to travel', TFD),
                'dependency' => array(
                    array('type', '==', 'continuous'),
                    array('custom_avail', '==', 'false'),
                ),
            ),
            array(
                'id'       => 'cont_max_people',
                'type'     => 'number',
                'title'    => __('Maximum Person', TFD),
                'subtitle' => __('Maximum person to travel', TFD),
                'dependency' => array(
                    array('type', '==', 'continuous'),
                    array('custom_avail', '==', 'false'),
                ),
            ),
            array(
                'id'     => 'allowed_time',
                'type'   => 'repeater',
                'title'  => __('Allowed Time', TFD),
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
                        'title'    => __('Time', TFD),
                        'subtitle' => __('Only Time' .$badge_pro, TFD),
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
                'content' => __('Disabled Dates', TFD),
                'dependency' => array(
                    array('type', '==', 'continuous'),
                    array('custom_avail', '==', 'false'),
                ),
            ),

            array(
                'id'         => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'       => 'checkbox',
                'title'      => __('Select day to disable', TFD),
                'subtitle'   => $badge_pro,
                'dependency' => array(
                    array('type', '==', 'continuous'),
                    array('custom_avail', '==', 'false'),
                ),
                'inline'     => true,
                'options'    => array(
                    '0' => __('Sunday', TFD),
                    '1' => __('Monday', TFD),
                    '2' => __('Tuesday', TFD),
                    '3' => __('Wednesday', TFD),
                    '4' => __('Thursday', TFD),
                    '5' => __('Friday', TFD),
                    '6' => __('Saturday', TFD),
                ),
            ),


            array(
                'id'     => 'disable_range',
                'type'   => 'repeater',
                'title'  => __('Disabled Date Range', TFD),
                'max' => 2,
                'dependency' => array(
                    array('type', '==', 'continuous'),
                    array('custom_avail', '==', 'false'),
                ),
                'fields' => array(

                    array(
                        'id'       => 'date',
                        'type'     => 'datetime',
                        'title'    => __('Select date range', TFD),
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
                'title' => __('Disable Specific Dates', TFD),
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
                'title'      => __('Availability', TFD),
                'subtitle'   => __('Input your availability' .$badge_pro, TFD),
                'dependency' => array('type', '==', 'fixed'),
                'class'      => 'fixed_availability',
                'fields'     => array(
                    array(
                        'id'       => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'     => 'datetime',
                        'title'    => __('Check In', TFD),
                        'subtitle' => __('Select check in date' .$badge_pro, TFD),
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
                        'title'    => __('Minimum People', TFD),
                        'subtitle' => __('Minimum seat number' .$badge_pro, TFD),
                    ),
                    array(
                        'id'       => '',
                        'class' => 'tf-csf-disable tf-csf-pro',
                        'type'     => 'number',
                        'title'    => __('Maximum People', TFD),
                        'subtitle' => __('Maximum seat number' .$badge_pro, TFD),
                    ),
                ),
            ),

        ),
    ));

    CSF::createSection( $prefix, array(
        'title'  => __( 'Booking', TFD ),
        'fields' => array(

            array(
                'id'       => '',
                'class' => 'tf-csf-disable tf-csf-pro',
                'type'     => 'slider',
                'max'      => '30',
                'title'    => __( 'Minimum days to book before departure', TFD ),
                'subtitle' => __( 'Customer can not book after this date' .$badge_pro, TFD ),
            ),
        ),
    ) );

    CSF::createSection( $prefix, array(
        'title'  => __( 'Exclude/Include', TFD ),
        'fields' => array(
            array(
                'id'     => 'inc',
                'type'   => 'repeater',
                'title'  => __( 'Include', TFD ),
                'max' => 5,
                'fields' => array(
                    array(
                        'id'       => 'inc',
                        'type'     => 'text',
                        'title'    => __( 'Included', TFD ),
                        'subtitle' => __( 'Included facilites', TFD ),
                    ),
                ),
            ),
            array(
                'id'     => 'exc',
                'type'   => 'repeater',
                'title'  => __( 'Exclude', TFD ),
                'max' => 5,
                'fields' => array(
                    array(
                        'id'       => 'exc',
                        'type'     => 'text',
                        'title'    => __( 'Excluded', TFD ),
                        'subtitle' => __( 'Excluded facilites', TFD ),
                    ),
                ),
            ),
        ),
    ) );

    CSF::createSection( $prefix, array(
        'title'  => __( 'Itinerary', TFD ),
        'fields' => array(
            array(
                'id'     => 'itinerary',
                'type'   => 'repeater',
                'title'  => __( 'Itinerary', TFD ),
                'max' => 5,
                'fields' => array(
                    array(
                        'id'       => 'time',
                        'type'     => 'text',
                        'title'    => __( 'Time or Day', TFD ),
                        'subtitle' => __( 'You can place the tour plan', TFD ),
                    ),
                    array(
                        'id'       => 'title',
                        'type'     => 'text',
                        'title'    => __( 'Title', TFD ),
                        'subtitle' => __( 'Input the title here', TFD ),
                    ),
                    array(
                        'id'           => 'image',
                        'type'         => 'upload',
                        'title'        => __('Upload Image', TFD ),
                        'library'      => 'image',
                        'placeholder'  => 'http://',
                        'button_title' => __('Add Image', TFD ),
                        'remove_title' => __('Remove Image', TFD ),
                    ),
                    array(
                        'id'    => 'desc',
                        'type'  => 'textarea',
                        'title' => __( 'Description', TFD ),
                    ),
                ),
            ),
        ),
    ) );

    CSF::createSection( $prefix, array(
        'title'  => __( 'FAQs', TFD ),
        'fields' => array(
            array(
                'id'     => 'faqs',
                'type'   => 'repeater',
                'title'  => __( 'FAQs', TFD ),
                'fields' => array(
                    array(
                        'id'    => 'title',
                        'type'  => 'text',
                        'title' => __( 'FAQ Title', TFD ),
                    ),
                    array(
                        'id'    => 'desc',
                        'type'  => 'textarea',
                        'title' => __( 'FAQ Description', TFD ),
                    ),
                ),
            ),
        ),
    ) );

    CSF::createSection( $prefix, array(
        'title'  => __( 'Terms & Conditions', TFD ),
        'fields' => array(
            array(
                'id'    => 'terms_conditions',
                'type'  => 'wp_editor',
                'title' => __( 'Terms & Conditions', TFD ),
            ),
        ),
    ) );

    // Settings
    CSF::createSection(
        $prefix,
        array(
            'title'  => __('Settings', 'tourfic'),
            'fields' => array(

              array(
                'type'    => 'subheading',
                'content' => __('Settings', 'tourfic' ),
              ),
      
              array(
                'id'       => 't-review',
                'type'     => 'switcher',
                'title'    => __('Disable Review Section', 'tourfic' ),
                'text_on'  => __('Yes', 'tourfic' ),
                'text_off' => __('No', 'tourfic' ),
            ),
    
            array(
                'id'       => 't-related',
                'type'     => 'switcher',
                'title'    => __('Disable Related Tour Section', 'tourfic' ),
                'text_on'  => __('Yes', 'tourfic' ),
                'text_off' => __('No', 'tourfic' ),
            ),

              array(
                'type'    => 'notice',
                'style'   => 'info',
                'content' => __('These settings will overwrite global settings', 'tourfic' ),
              ),

            ),
        )
    );

}

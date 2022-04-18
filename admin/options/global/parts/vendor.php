<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

$badge_up = '<div class="tf-csf-badge"><span class="tf-upcoming">' .__("Upcoming", "tourfic"). '</span></div>';
$badge_pro = '<div class="tf-csf-badge"><span class="tf-pro">' .__("Pro Feature", "tourfic"). '</span></div>';
$badge_up_pro = '<div class="tf-csf-badge"><span class="tf-upcoming">' .__("Upcoming", "tourfic"). '</span><span class="tf-pro">' .__("Pro Feature", "tourfic"). '</span></div>';

CSF::createSection( $prefix, array(
    'id'     => 'vendor',
    'title'  => __( 'Multi Vendor', TFD ),
    'icon'   => 'fas fa-handshake',
    'fields' => array(

      // Registration
      array(
        'type'    => 'subheading',
        'content' => __('Registration', TFD ),
      ),

      array(
        'id'         => 'reg-pop',
        'class'      => 'tf-csf-disable tf-csf-pro',
        'type'       => 'switcher',
        'title'      => __('Registration Form Popup', TFD ),
        'subtitle'   => __('Add class <code>tf-reg-popup</code> to trigger the popup' .$badge_pro, TFD ),
        'text_on'    => __('Enabled', TFD ),
        'text_off'   => __('Disabled', TFD ),
        'text_width' => 100,
        'default'    => true,
      ),

      array(
        'type'    => 'content',
        'content' => __('Use shortcode <code>[tf_registration_form]</code> to show registration form in post/page.', TFD ),
      ),

      array(
        'id'       => 'email-verify',
        'class'    => 'tf-csf-disable tf-csf-pro',
        'type'     => 'switcher',
        'title'    => __('Enable Email Verification', TFD ),
        'subtitle' => $badge_pro,
        'default'  => true,
      ),

      array(
        'id'         => 'prevent-login',
        'class' => 'tf-csf-disable tf-csf-pro',
        'type'       => 'switcher',
        'title'      => __('Login Restriction', TFD ),
        'subtitle'   => __('Prevent unverified user to login' .$badge_pro, TFD ),
        'dependency' => array( 'email-verify', '==', 'true' ),
        'default'    => true,
      ),
      
      // Vendor
      array(
        'type'    => 'subheading',
        'content' => __('Vendor', TFD ),
      ),

      array(
        'id'       => 'vendor-reg',
        'class' => 'tf-csf-disable tf-csf-pro',
        'type'     => 'switcher',
        'title'    => __('Enable Vendor Registration', TFD ),
        'subtitle' => __('Visitor can register as vendor using the registration form' .$badge_pro, TFD ),
        'default'  => true,
      ),

      array(
        'id'       => 'vendor-tax-add',
        'class'    => 'tf-csf-disable tf-csf-pro',
        'type'     => 'checkbox',
        'title'    => __('Vendor Can Add', TFD ),
        'subtitle' => $badge_pro,
        'options'  => array(
          'hl' => __('Hotel Location', TFD ),
          'hf' => __('Hotel Feature', TFD ),
          'td' => __('Tour Destination', TFD ),
        ),
      ),

    )    
  ) );

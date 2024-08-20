<?php

use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Car_Rental\Pricing;
?>

<div class="tf-single-car-section">
    
    <div class="tf-car-template-container">
        <div class="tf-container-inner">
            <div class="tf-single-car-details-warper">
                <div class="tf-car-details-column">
                    <div class="tf-car-title">
                        <h1><?php the_title(); ?></h1>
                        <div class="breadcrumb">
                            <ul>
                                <li><a href="<?php echo site_url(); ?>"><?php esc_html_e( "Home", "tourfic" ) ?></a></li>
                                <li>/</li>
                                <li><a href="<?php echo site_url(); ?>"><?php esc_html_e( "Car", "tourfic" ) ?></a></li>
                                <li>/</li>
                                <li><?php the_title(); ?></li>
                            </ul>
                        </div>
                    </div>
                    <div class="tf-car-hero-gallery">
                        <div class="tf-featured-car">
                            <img src="<?php echo !empty(wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' )) ? esc_url( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) : esc_url(TF_ASSETS_APP_URL.'/images/feature-default.jpg'); ?>" alt="<?php esc_html_e( 'Car Image', 'tourfic' ); ?>">

                            <div class="tf-featured-reviews">
                                <a href="#tf-review" class="tf-single-rating">
                                    <span>
                                        <?php esc_html_e( "5.0", "tourfic" ) ?>
                                        <i class="fa-solid fa-star"></i>
                                    </span> (<?php esc_html_e( "1 Trips", "tourfic" ) ?>)
                                </a>
                            </div>
                        </div>

                        <div class="tf-gallery tf-flex tf-flex-gap-16">
                        <?php 
                        $gallery_count = 1;
                            if ( ! empty( $gallery_ids ) ) {
                            foreach ( $gallery_ids as $key => $gallery_item_id ) {
                            $image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
                        ?>
                            <a class="<?php echo $gallery_count==4 ? esc_attr( 'tf-gallery-more' ) : ''; ?> " href="<?php echo esc_url($image_url); ?>" id="tour-gallery" data-fancybox="tour-gallery">
                                <img src="<?php echo esc_url($image_url); ?>">
                            </a>
                        <?php $gallery_count++; } } ?>
                        </div>
                    </div>

                    <div class="tf-details-menu">
                        <ul>
                            <li class="active">
                                <a class="tf-hashlink" href="#tf-description">
                                    <?php esc_html_e("Description", "tourfic"); ?>
                                </a>
                            </li>
                            <li>
                                <a class="tf-hashlink" href="#tf-car-info">
                                    <?php esc_html_e("Car info", "tourfic"); ?>
                                </a>
                            </li>
                            <li>
                                <a class="tf-hashlink" href="#tf-benefits">
                                    <?php esc_html_e("Benefits", "tourfic"); ?>
                                </a>
                            </li>
                            <li>
                                <a class="tf-hashlink" href="#tf-inc-exc">
                                    <?php esc_html_e("Include/Excluce", "tourfic"); ?>
                                </a>
                            </li>
                            <li>
                                <a class="tf-hashlink" href="#tf-location">
                                    <?php esc_html_e("Location", "tourfic"); ?>
                                </a>
                            </li>
                            <li>
                                <a class="tf-hashlink" href="#tf-reviews">
                                    <?php esc_html_e("Reviews", "tourfic"); ?>
                                </a>
                            </li>
                            <li>
                                <a class="tf-hashlink" href="#tf-faq">
                                    <?php esc_html_e("FAQ's", "tourfic"); ?>
                                </a>
                            </li>

                        </ul>
                    </div>

                    <?php
                    if ( ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-car-layout'] ) ) {
                        foreach ( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-car-layout'] as $section ) {
                            if ( ! empty( $section['car-section-status'] ) && $section['car-section-status'] == "1" && ! empty( $section['car-section-slug'] ) ) {
                                include TF_TEMPLATE_PART_PATH . 'car/design-1/' . $section['car-section-slug'] . '.php';
                            }
                        }
                    } else {
                        include TF_TEMPLATE_PART_PATH . 'car/design-1/description.php';
                        include TF_TEMPLATE_PART_PATH . 'car/design-1/car-info.php';
                        include TF_TEMPLATE_PART_PATH . 'car/design-1/benefits.php';
                        include TF_TEMPLATE_PART_PATH . 'car/design-1/inc-exc.php';
                        include TF_TEMPLATE_PART_PATH . 'car/design-1/location.php';
                        include TF_TEMPLATE_PART_PATH . 'car/design-1/reviews.php';
                        include TF_TEMPLATE_PART_PATH . 'car/design-1/faq.php';
                    }
                    ?>

                </div>

                <div class="tf-car-booking-form">

                    <div class="tf-price-header tf-mb-30">
                        <?php
                        $tf_pickup_date = !empty($_GET['pickup']) ? $_GET['pickup'] : '2024/08/23';
                        $tf_dropoff_date = !empty($_GET['dropoff']) ? $_GET['dropoff'] : '2024/08/27';
                        $total_prices = Pricing::set_total_price($meta, $tf_pickup_date, $tf_dropoff_date); ?>
                        <h2>Total: 
                        <?php if(!empty($total_prices['sale_price'])){ ?><del><?php echo wc_price($total_prices['sale_price']); ?></del>  <?php } ?>
                        <?php echo $total_prices['regular_price'] ? wc_price($total_prices['regular_price']) : '' ?></h2>
                        <p>Without taxes</p>
                    </div>

                    <div class="tf-extra-added-info">
                        <div class="tf-extra-added-box tf-flex tf-flex-gap-16 tf-flex-direction-column">
                            <h3>Extras added</h3>
                            <div class="tf-single-added-extra tf-flex tf-flex-align-center tf-flex-space-bttn">
                                <h4>Need additional driver</h4>
                                <div class="qty-price tf-flex">
                                    <i class="ri-close-line"></i> 
                                    <span class="qty">1</span> 
                                    <span class="price">$50</span>
                                    <span class="delete">
                                        <i class="ri-delete-bin-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="tf-single-added-extra tf-flex tf-flex-align-center tf-flex-space-bttn">
                                <h4>Need additional driver lorem ipsum doller set amet</h4>
                                <div class="qty-price tf-flex">
                                    <i class="ri-close-line"></i> 
                                    <span class="qty">1</span> 
                                    <span class="price">$50</span>
                                    <span class="delete">
                                        <i class="ri-delete-bin-line"></i>
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="tf-date-select-box">

                        <div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4 tf-flex-align-baseline">
                                    <i class="ri-map-pin-line"></i>
                                    <div class="info-select">
                                        <h5>Pick-up</h5>
                                        <input type="text" placeholder="Pick Up Location">
                                    </div>
                                </div>
                            </div>

                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4 tf-flex-align-baseline">
                                    <i class="ri-map-pin-line"></i>
                                    <div class="info-select">
                                        <h5>Drop-off</h5>
                                        <input type="text" placeholder="Drop Off Location">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4 tf-flex-align-baseline">
                                    <i class="ri-calendar-2-line"></i>
                                    <div class="info-select">
                                        <h5>Pick-up date</h5>
                                        <input type="text" placeholder="Pick Up Date">
                                    </div>
                                </div>
                            </div>

                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4 tf-flex-align-baseline">
                                    <i class="ri-time-line"></i>
                                    <div class="info-select">
                                        <h5>Time</h5>
                                        <select name="" id="">
                                            <option value="">Select Time</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4 tf-flex-align-baseline">
                                    <i class="ri-calendar-2-line"></i>
                                    <div class="info-select">
                                        <h5>Drop-off date</h5>
                                        <input type="text" placeholder="Drop Off Date">
                                    </div>
                                </div>
                            </div>

                            <div class="tf-select-date">
                                <div class="tf-flex tf-flex-gap-4 tf-flex-align-baseline">
                                    <i class="ri-time-line"></i>
                                    <div class="info-select">
                                        <h5>Time</h5>
                                        <select name="" id="">
                                            <option value="">Select Time</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tf-form-submit-btn">
                            <button class="tf-flex tf-flex-align-center tf-flex-justify-center">
                                Continue
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 15L12.5 10L7.5 5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                        <div class="tf-instraction-btn tf-mt-16">
                            <a href="#">Pick-up and Drop-off instructions</a>
                        </div>
                        
                    </div>

                    <div class="tf-car-booking-popup">
                        <div class="tf-booking-popup-warp">

                            <div class="tf-booking-popup-header tf-flex tf-flex-align-center tf-flex-space-bttn">
                                <h3>Additional information</h3>
                                <div class="tf-close-popup">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15 5L5 15M5 5L15 15" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="tf-booking-content-wraper">

                                <div class="tf-cancellation-notice">
                                    <span class="tf-flex tf-flex-align-center tf-flex-gap-16">
                                        <i class="ri-information-line"></i>
                                        <b>Free cancellation</b>
                                        Full refund if you cancel your plan anytime before pick-up
                                    </span>
                                </div>

                                <div class="tf-booking-tabs">
                                    <ul>
                                        <li class="active">Protections</li>
                                        <li>Booking</li>
                                    </ul>
                                </div>

                                <div class="tf-protection-content tf-flex tf-flex-gap-24 tf-flex-direction-column">
                                    <p>At the counter, the car hire company will block a deposit amount on your credit card. You could lose your whole deposit if the car is damaged or stolen, but as long as you have our Full Protection, Rentalcover.com will refund you! (The protection price you see includes all applicable taxes and fees).
                                    </p>

                                    <div class="tf-protection-featured">
                                        <table>
                                            <tr>
                                                <td>What is covered</td>
                                                <td align="center">No protection</td>
                                                <td align="center">With protection</td>
                                            </tr>

                                            
                                            <tr>
                                                <th>
                                                    <div class="tf-flex">
                                                        The car's excess
                                                        <div class="tf-info-tooltip">
                                                            <i class="ri-information-line"></i>
                                                        </div>
                                                    </div>
                                                </th>
                                                <td align="center">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12.5001 7.49996L7.50008 12.5M7.50008 7.49996L12.5001 12.5M18.3334 9.99996C18.3334 14.6023 14.6025 18.3333 10.0001 18.3333C5.39771 18.3333 1.66675 14.6023 1.66675 9.99996C1.66675 5.39759 5.39771 1.66663 10.0001 1.66663C14.6025 1.66663 18.3334 5.39759 18.3334 9.99996Z" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </td>
                                                <td align="center">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M18.3334 9.2333V9.99997C18.3324 11.797 17.7505 13.5455 16.6745 14.9848C15.5986 16.4241 14.0862 17.477 12.3629 17.9866C10.6396 18.4961 8.7978 18.4349 7.11214 17.8121C5.42648 17.1894 3.98729 16.0384 3.00922 14.5309C2.03114 13.0233 1.56657 11.24 1.68481 9.4469C1.80305 7.65377 2.49775 5.94691 3.66531 4.58086C4.83288 3.21482 6.41074 2.26279 8.16357 1.86676C9.91641 1.47073 11.7503 1.65192 13.3918 2.3833M7.50009 9.16664L10.0001 11.6666L18.3334 3.3333" stroke="#27BE69" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    <div class="tf-flex">
                                                        The car's excess
                                                        <div class="tf-info-tooltip">
                                                            <i class="ri-information-line"></i>
                                                        </div>
                                                    </div>
                                                </th>
                                                <td align="center">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12.5001 7.49996L7.50008 12.5M7.50008 7.49996L12.5001 12.5M18.3334 9.99996C18.3334 14.6023 14.6025 18.3333 10.0001 18.3333C5.39771 18.3333 1.66675 14.6023 1.66675 9.99996C1.66675 5.39759 5.39771 1.66663 10.0001 1.66663C14.6025 1.66663 18.3334 5.39759 18.3334 9.99996Z" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </td>
                                                <td align="center">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M18.3334 9.2333V9.99997C18.3324 11.797 17.7505 13.5455 16.6745 14.9848C15.5986 16.4241 14.0862 17.477 12.3629 17.9866C10.6396 18.4961 8.7978 18.4349 7.11214 17.8121C5.42648 17.1894 3.98729 16.0384 3.00922 14.5309C2.03114 13.0233 1.56657 11.24 1.68481 9.4469C1.80305 7.65377 2.49775 5.94691 3.66531 4.58086C4.83288 3.21482 6.41074 2.26279 8.16357 1.86676C9.91641 1.47073 11.7503 1.65192 13.3918 2.3833M7.50009 9.16664L10.0001 11.6666L18.3334 3.3333" stroke="#27BE69" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </td>
                                            </tr>

                                            <tfoot>
                                                <tr>
                                                    <th>Charge:</th>
                                                    <th>$000</th>
                                                    <th>$700</th>
                                                </tr>
                                            </tfoot>

                                        </table>
                                    </div>
                                </div>

                                <div class="tf-booking-bar tf-flex tf-flex-gap-24">
                                    <button class="without-charge">
                                        Book without protection
                                        <i class="ri-arrow-right-s-line"></i>
                                    </button>
                                    <button class="with-charge">
                                        Book with protection
                                        <i class="ri-arrow-right-s-line"></i>
                                    </button>
                                </div>

                            </div>

                        </div>
                    </div>

                    <?php if(!empty($car_extras)){ ?>
                    <div class="tf-add-extra-section">
                        <h3>
                            <?php esc_html_e( "Add extras", "tourfic" ) ?>
                        </h3>

                        <div class="tf-car-extra-infos tf-flex tf-flex-direction-column tf-flex-gap-16">

                        <?php foreach($car_extras as $extra){ ?>
                            <div class="tf-car-single-extra tf-flex tf-flex-space-bttn tf-flex-align-center">

                                <div class="tf-extra-title">
                                    <?php if(!empty($extra['title'])){ ?>
                                    <h4><?php echo esc_html($extra['title']); ?>
                                    <i class="ri-information-line"></i>
                                    </h4>
                                    <?php } ?>
                                    <div class="acr-select">
                                        <div class="acr-dec">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.16663 10H15.8333" stroke="#0866C4" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <input type="number" name="adults" id="adults" value="0">
                                        <div class="acr-inc">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.16663 9.99996H15.8333M9.99996 4.16663V15.8333" stroke="#0866C4" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-extra-price">
                                    <h4><?php echo wc_price($extra['price']); ?>
                                        <small>each/<?php echo esc_html($extra['price_type']); ?></small>
                                    </h4>
                                </div>

                            </div>
                        <?php } ?>

                            <div class="tf-extra-apply-btn">
                                <button>Apply</button>
                            </div>

                        </div>
                    </div>
                    <?php } ?>

                    <?php if(!empty($car_driverinfo_status)){ ?>
                    <div class="tf-driver-details tf-flex tf-flex-direction-column tf-flex-gap-16">
                        <div class="tf-driver-details-header tf-flex tf-flex-space-bttn tf-flex-align-center">
                            <h3>Driver details</h3>
                            <span>
                            <i class="ri-shield-check-line"></i> Verified
                            </span>
                        </div>
                        <div class="tf-driver-photo tf-flex tf-flex-gap-16">
                            <?php if(!empty($driver_image)){ ?>
                            <img src="<?php echo esc_url($driver_image); ?>">
                            <?php } ?>
                            <div class="tf-driver-info">
                                <?php if(!empty($driver_name)){ ?>
                                <h4><?php echo esc_attr($driver_name); ?></h4>
                                <?php } ?>
                                <p>Joined May 2024</p>
                            </div>
                        </div>
                        <div class="tf-driver-contact-info">
                            <ul class="tf-flex tf-flex-direction-column tf-flex-gap-16">
                                <?php if(!empty($driver_email)){ ?>
                                <li class="tf-flex tf-flex-gap-8">
                                    <i class="ri-mail-line"></i>
                                    <?php echo esc_attr($driver_email); ?>
                                </li>
                                <?php } ?>
                                <?php if(!empty($driver_phone)){ ?>
                                <li class="tf-flex tf-flex-gap-8">
                                    <i class="ri-phone-line"></i>
                                    <?php echo esc_attr($driver_phone); ?>
                                </li>
                                <?php } ?>
                                <?php if(!empty($driver_age)){ ?>
                                <li class="tf-flex tf-flex-gap-8">
                                    <i class="ri-user-line"></i>
                                    <?php esc_html_e("Age", "tourfic"); ?> <?php echo esc_attr($driver_age); ?> <?php esc_html_e("Years", "tourfic"); ?>
                                </li>
                                <?php } ?>
                                <?php if(!empty($driver_address)){ ?>
                                <li class="tf-flex tf-flex-gap-8">
                                    <i class="ri-map-pin-user-line"></i>
                                    <?php echo esc_attr($driver_address); ?>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <?php } ?>

                </div>
            </div>
            <?php 
            if(!empty($tc)){ ?>
            <div class="tf-car-conditions-section">
                <?php if(!empty($tc_title)){ ?>
                <h3><?php echo esc_html($tc_title); ?></h3>
                <?php } ?>
                <table>
                    <?php 
                    foreach($tc as $singletc){ ?>
                    <tr>
                        <th><?php echo !empty($singletc['title']) ? esc_html($singletc['title']) : ''; ?></th>
                        <td><?php echo !empty($singletc['content']) ? wp_kses_post($singletc['content']) : ''; ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

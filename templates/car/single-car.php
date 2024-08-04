<?php
/**
 * Template: Single Car (Full Width)
 */

get_header();
?>

<div class="tf-single-car-section">
    <div class="tf-single-car-banner">
        <div class="tf-banner-content tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-direction-column">
            <h1>Hundai 354 2024</h1>
            <div class="breadcrumb">
                <ul>
                    <li><a href="">Home</a></li>
                    <li>/</li>
                    <li><a href="">Car</a></li>
                    <li>/</li>
                    <li>Hundai 354 2024</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="tf-car-template-container">
        <div class="tf-container-inner">
            <div class="tf-single-car-details-warper">
                <div class="tf-car-details-column">

                    <div class="tf-car-hero-gallery">
                        <div class="tf-featured-car">
                            <img src="<?php echo esc_url(TF_ASSETS_APP_URL.'/images/feature-default.jpg'); ?>" alt="<?php esc_html_e( 'Car Image', 'tourfic' ); ?>">

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
                            <a href="#" id="tour-gallery" data-fancybox="tour-gallery">
                                <img src="<?php echo esc_url(TF_ASSETS_APP_URL.'/images/feature-default.jpg'); ?>">
                            </a>
                            <a href="#" id="tour-gallery" data-fancybox="tour-gallery">
                                <img src="<?php echo esc_url(TF_ASSETS_APP_URL.'/images/feature-default.jpg'); ?>">
                            </a>
                            <a href="#" id="tour-gallery" data-fancybox="tour-gallery">
                                <img src="<?php echo esc_url(TF_ASSETS_APP_URL.'/images/feature-default.jpg'); ?>">
                            </a>
                            <a class="tf-gallery-more" href="#" id="tour-gallery" data-fancybox="tour-gallery">
                                <img src="<?php echo esc_url(TF_ASSETS_APP_URL.'/images/feature-default.jpg'); ?>">
                            </a>
                        </div>
                    </div>

                    <div class="tf-details-menu">
                        <ul>
                            <li class="active">
                                <a class="tf-hashlink" href="#tf-tour-overview">
                                    <?php esc_html_e("Description", "tourfic"); ?>
                                </a>
                            </li>
                            <li>
                                <a class="tf-hashlink" href="#tf-tour-overview">
                                    <?php esc_html_e("Car info", "tourfic"); ?>
                                </a>
                            </li>
                            <li>
                                <a class="tf-hashlink" href="#tf-tour-overview">
                                    <?php esc_html_e("Benefits", "tourfic"); ?>
                                </a>
                            </li>
                            <li>
                                <a class="tf-hashlink" href="#tf-tour-overview">
                                    <?php esc_html_e("Specification", "tourfic"); ?>
                                </a>
                            </li>
                            <li>
                                <a class="tf-hashlink" href="#tf-tour-overview">
                                    <?php esc_html_e("Location", "tourfic"); ?>
                                </a>
                            </li>
                            <li>
                                <a class="tf-hashlink" href="#tf-tour-overview">
                                    <?php esc_html_e("Reviews", "tourfic"); ?>
                                </a>
                            </li>
                            <li>
                                <a class="tf-hashlink" href="#tf-tour-overview">
                                    <?php esc_html_e("FAQ's", "tourfic"); ?>
                                </a>
                            </li>

                        </ul>
                    </div>

                    <div class="tf-short-description">
                        It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).
                    </div>

                    <div class="tf-car-info">
                        <h3><?php esc_html_e("Car info", "tourfic"); ?></h3>

                        <ul>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6"><i class="ri-speed-up-fill"></i>20 KM</li>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6"><i class="ri-speed-up-fill"></i>20 KM</li>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6"><i class="ri-speed-up-fill"></i>20 KM</li>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6"><i class="ri-speed-up-fill"></i>20 KM</li>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6"><i class="ri-speed-up-fill"></i>20 KM</li>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6"><i class="ri-speed-up-fill"></i>20 KM</li>
                        </ul>
                    </div>

                    <div class="tf-car-benefits">
                        <h3><?php esc_html_e("Benefits", "tourfic"); ?></h3>

                        <ul>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 5L5.83332 14.1667L1.66666 10M18.3333 8.33333L12.0833 14.5833L10.8333 13.3333" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                                Most popular fuel policy
                            </li>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 5L5.83332 14.1667L1.66666 10M18.3333 8.33333L12.0833 14.5833L10.8333 13.3333" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                                Most popular fuel policy
                            </li>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 5L5.83332 14.1667L1.66666 10M18.3333 8.33333L12.0833 14.5833L10.8333 13.3333" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                                Most popular fuel policy
                            </li>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 5L5.83332 14.1667L1.66666 10M18.3333 8.33333L12.0833 14.5833L10.8333 13.3333" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                                Most popular fuel policy
                            </li>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 5L5.83332 14.1667L1.66666 10M18.3333 8.33333L12.0833 14.5833L10.8333 13.3333" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                                Most popular fuel policy
                            </li>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 5L5.83332 14.1667L1.66666 10M18.3333 8.33333L12.0833 14.5833L10.8333 13.3333" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                                Most popular fuel policy
                            </li>
                            
                        </ul>
                    </div>

                    <div class="tf-car-specification">
                        <h3><?php esc_html_e("Specification", "tourfic"); ?></h3>

                        <ul>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                                <span class="label">Make</span>
                                <span class="value">Hundai</span>
                            </li>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                                <span class="label">Engine(CM3)</span>
                                <span class="value">1500</span>
                            </li>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                                <span class="label">Make</span>
                                <span class="value">Hundai</span>
                            </li>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                                <span class="label">Engine(CM3)</span>
                                <span class="value">1500</span>
                            </li>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                                <span class="label">Make</span>
                                <span class="value">Hundai</span>
                            </li>
                            <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                                <span class="label">Engine(CM3)</span>
                                <span class="value">1500</span>
                            </li>
                        </ul>
                    </div>

                    <div class="tf-car-location">
                        <h3><?php esc_html_e("Location", "tourfic"); ?></h3>

                        <div class="tf-car-location-map">

                        </div>
                    </div>


                    <div class="tf-car-faq-section">
                        <h3><?php esc_html_e("FAQ’s", "tourfic"); ?></h3>

                        <div class="tf-faq-col">
                            <div class="tf-faq-head">
                                <span class="tf-flex tf-flex-space-bttn tf-flex-align-center">
                                How long does it take to recharge a Volt?
                                <i class="fa-solid fa-chevron-down"></i>
                                </span>
                            </div>
                            <div class="tf-question-desc">
                                The car rental company will have the car filled with fuel for you and you have to return the car with a full tank. The car rental company will charge you for every missing quarter of a tank and a refueling fee might apply. A fuel deposit might be required, which will be refunded at the end of the rental if the car is returned with a full tank of fuel.
                            </div>
                        </div>

                        <div class="tf-faq-col">
                            <div class="tf-faq-head">
                                <span class="tf-flex tf-flex-space-bttn tf-flex-align-center">
                                How long does it take to recharge a Volt?
                                <i class="fa-solid fa-chevron-down"></i>
                                </span>
                            </div>
                            <div class="tf-question-desc">
                                The car rental company will have the car filled with fuel for you and you have to return the car with a full tank. The car rental company will charge you for every missing quarter of a tank and a refueling fee might apply. A fuel deposit might be required, which will be refunded at the end of the rental if the car is returned with a full tank of fuel.
                            </div>
                        </div>
                        <div class="tf-faq-col">
                            <div class="tf-faq-head">
                                <span class="tf-flex tf-flex-space-bttn tf-flex-align-center">
                                How long does it take to recharge a Volt?
                                <i class="fa-solid fa-chevron-down"></i>
                                </span>
                            </div>
                            <div class="tf-question-desc">
                                The car rental company will have the car filled with fuel for you and you have to return the car with a full tank. The car rental company will charge you for every missing quarter of a tank and a refueling fee might apply. A fuel deposit might be required, which will be refunded at the end of the rental if the car is returned with a full tank of fuel.
                            </div>
                        </div>

                    </div>
                </div>

                <div class="tf-car-booking-form">

                    <div class="tf-add-extra-section">
                        <h3>
                            Add extras
                        </h3>

                        <div class="tf-car-extra-infos tf-flex tf-flex-direction-column tf-flex-gap-16">

                            <div class="tf-car-single-extra tf-flex tf-flex-space-bttn tf-flex-align-center">

                                <div class="tf-extra-title">
                                    <h4>Need additional driver
                                    <i class="ri-information-line"></i>
                                    </h4>
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
                                    <h4>$25
                                        <small>each/rental</small>
                                    </h4>
                                </div>

                            </div>

                            <div class="tf-car-single-extra tf-flex tf-flex-space-bttn tf-flex-align-center">

                                <div class="tf-extra-title">
                                    <h4>Need additional driver
                                    <i class="ri-information-line"></i>
                                    </h4>
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
                                    <h4>$25
                                        <small>each/rental</small>
                                    </h4>
                                </div>

                            </div>

                            <div class="tf-extra-apply-btn">
                                <button>Apply</button>
                            </div>

                        </div>
                    </div>

                    <div class="tf-driver-details tf-flex tf-flex-direction-column tf-flex-gap-16">
                        <div class="tf-driver-details-header tf-flex tf-flex-space-bttn tf-flex-align-center">
                            <h3>Driver details</h3>
                            <span>
                            <i class="ri-shield-check-line"></i> Verified
                            </span>
                        </div>
                        <div class="tf-driver-photo tf-flex tf-flex-gap-16">
                            <img src="<?php echo esc_url(TF_ASSETS_APP_URL.'/images/feature-default.jpg'); ?>">
                            <div class="tf-driver-info">
                                <h4>Eusuf Abdullah</h4>
                                <p>Joined May 2024</p>
                            </div>
                        </div>
                        <div class="tf-driver-contact-info">
                            <ul class="tf-flex tf-flex-direction-column tf-flex-gap-16">
                                <li class="tf-flex tf-flex-gap-8">
                                    <i class="ri-mail-line"></i>
                                    eusufabdullah@gmail.com
                                </li>
                                <li class="tf-flex tf-flex-gap-8">
                                    <i class="ri-mail-line"></i>
                                    +33 469 968 4796
                                </li>
                                <li class="tf-flex tf-flex-gap-8">
                                    <i class="ri-mail-line"></i>
                                    Age 32 years
                                </li>
                                <li class="tf-flex tf-flex-gap-8">
                                    <i class="ri-mail-line"></i>
                                    2118 Thornridge Cir. Syracuse, Connecticut 35624
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tf-car-conditions-section">
                <h3><?php esc_html_e("Terms & Conditions", "tourfic"); ?></h3>
                <table>
                    <tr>
                        <th>Fuel Policy</th>
                        <td>The car rental company will have the car filled with fuel for you and you have to return the car with a full tank. The car rental company will charge you for every missing quarter of a tank and a refueling fee might apply. A fuel deposit might be required, which will be refunded at the end of the rental if the car is returned with a full tank of fuel.</td>
                    </tr>
                    <tr>
                        <th>Driver requirements</th>
                        <td>Rentals originating in British Columbia may only be driven in the provinces of British Columbia and Alberta and the state of Washington.</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
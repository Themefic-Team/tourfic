<?php
/**
 * Template: Car Archive
 *
 * Display all Cars here
 * 
 * Default slug: /cars 
 */


get_header(); 
?>
<div class="tf-archive-car-section">
    <div class="tf-archive-car-banner">
        <div class="tf-banner-content tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-direction-column">
            <h1>Search results</h1>
        </div>
    </div>

    <div class="tf-car-template-container">
        <div class="tf-container-inner">
            <div class="tf-archive-car-details-warper">
                <div class="tf-archive-header tf-flex tf-flex-space-bttn tf-flex-align-center tf-mb-30">
                    <div class="tf-archive-view">
                        <ul class="tf-flex tf-flex-gap-16">
                            <li class="active"><i class="ri-layout-grid-line"></i></li>
                            <li><i class="ri-list-check"></i></li>
                        </ul>
                    </div>
                    <div class="tf-total-result-bar">
                        <span>Showing 8 of 15 Results</span>
                    </div>
                </div>
                <div class="tf-car-details-column tf-flex tf-flex-gap-32">
                    
                    <div class="tf-car-archive-sidebar">
                        <div class="tf-sidebar-header tf-flex tf-flex-space-bttn tf-flex-align-center">
                            <h4>Filter</h4>
                            <button>Reset</button>
                        </div>

                        <div class="tf-category-widget">
                            <div class="tf-category-heading">
                                <h3>Car type</h3>
                            </div>
                            <div class="tf-category-lists">
                                <ul>
                                    <li>
                                        <label>Sports coupe
                                            <input type="checkbox" checked>
                                            <span class="checkmark"></span>
                                        </label>
                                    </li>
                                    <li>
                                        <label>Sports coupe
                                            <input type="checkbox">
                                            <span class="checkmark"></span>
                                        </label>
                                    </li>
                                    <li>
                                        <label>Sports coupe
                                            <input type="checkbox">
                                            <span class="checkmark"></span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tf-category-widget">
                            <div class="tf-category-heading">
                                <h3>Car type</h3>
                            </div>
                            <div class="tf-category-lists">
                                <ul>
                                    <li>
                                        <label>Sports coupe
                                            <input type="checkbox" checked>
                                            <span class="checkmark"></span>
                                        </label>
                                    </li>
                                    <li>
                                        <label>Sports coupe
                                            <input type="checkbox">
                                            <span class="checkmark"></span>
                                        </label>
                                    </li>
                                    <li>
                                        <label>Sports coupe
                                            <input type="checkbox">
                                            <span class="checkmark"></span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>

                    <div class="tf-car-archive-result">
                        
                        <div class="tf-car-result grid-view tf-flex tf-flex-gap-32">

                            <div class="tf-single-car-view">
                                <div class="tf-car-image">
                                    <img src="<?php echo esc_url(TF_ASSETS_APP_URL.'/images/feature-default.jpg'); ?>">
                                    <div class="tf-other-infos tf-flex tf-flex-gap-64">
                                        <div class="tf-reviews-box">
                                            <span>5.0 <i class="fa-solid fa-star"></i> (7 trips)</span>
                                        </div>
                                        <div class="tf-tags-box">
                                            <ul>
                                                <li>Driver included</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-car-details">
                                    <h3 class="tf-mb-24">Hundai 354 2024</h3>
                                    <ul class="tf-flex tf-mb-24">
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                    </ul>
                                    <div class="tf-booking-btn tf-flex tf-flex-space-bttn">
                                        <div class="tf-price-info">
                                            <h3>$250 <small>/ Day</small></h3>
                                        </div>
                                        <button>Book now</button>
                                    </div>
                                </div>
                            </div>
                            <div class="tf-single-car-view">
                                <div class="tf-car-image">
                                    <img src="<?php echo esc_url(TF_ASSETS_APP_URL.'/images/feature-default.jpg'); ?>">
                                </div>
                                <div class="tf-car-details">
                                    <h3 class="tf-mb-24">Hundai 354 2024</h3>
                                    <ul class="tf-flex tf-mb-24">
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                    </ul>
                                    <div class="tf-booking-btn tf-flex tf-flex-space-bttn">
                                        <div class="tf-price-info">
                                            <h3>$250 <small>/ Day</small></h3>
                                        </div>
                                        <button>Book now</button>
                                    </div>
                                </div>
                            </div>
                            <div class="tf-single-car-view">
                                <div class="tf-car-image">
                                    <img src="<?php echo esc_url(TF_ASSETS_APP_URL.'/images/feature-default.jpg'); ?>">
                                </div>
                                <div class="tf-car-details">
                                    <h3 class="tf-mb-24">Hundai 354 2024</h3>
                                    <ul class="tf-flex tf-mb-24">
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                    </ul>
                                    <div class="tf-booking-btn tf-flex tf-flex-space-bttn">
                                        <div class="tf-price-info">
                                            <h3>$250 <small>/ Day</small></h3>
                                        </div>
                                        <button>Book now</button>
                                    </div>
                                </div>
                            </div>
                            <div class="tf-single-car-view">
                                <div class="tf-car-image">
                                    <img src="<?php echo esc_url(TF_ASSETS_APP_URL.'/images/feature-default.jpg'); ?>">
                                </div>
                                <div class="tf-car-details">
                                    <h3 class="tf-mb-24">Hundai 354 2024</h3>
                                    <ul class="tf-flex tf-mb-24">
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                        <li class="tf-flex tf-flex-gap-8 tf-flex-align-center"><i class="ri-speed-up-fill"></i>20 KM</li>
                                    </ul>
                                    <div class="tf-booking-btn tf-flex tf-flex-space-bttn">
                                        <div class="tf-price-info">
                                            <h3>$250 <small>/ Day</small></h3>
                                        </div>
                                        <button>Book now</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
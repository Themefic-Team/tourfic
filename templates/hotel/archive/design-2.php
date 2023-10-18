<div class="tf-template-3">


    <!--Hero section start -->
    <div class="tf-hero-section-wrap" style="background-image: url(./assets/image/hero.png);">
        <div class="tf-container">
            <div class="tf-hero-content tf-archive-hero-content">
                <div class="tf-head-title">
                    <h1>Los Angeles</h1>
                    <div class="tf-title-meta">
                        <p>(2 room, 18 aug,2023 - 20 aug,2023)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Hero section End -->


    <!--Content section end -->
    <div class="tf-content-wrapper">
        <?php
            do_action( 'tf_before_container' );
            $post_count = $GLOBALS['wp_query']->post_count;
        ?>
        <div class="tf-container">
        
            <!-- Hotel details Srart -->
            <div class="tf-archive-details tf-details" id="tf-hotel-overview">                    
                <!-- Booking form Start -->
                <div class="tf-archive-search-form tf-booking-form-wrapper">
                    <form action="" class="tf-booking-form">
                        <div class="tf-booking-form-fields">
                            <div class="tf-booking-form-location">
                                <span class="tf-booking-form-title">Location</span>
                                <label for="tf-search-location" class="tf-booking-location-wrap">
                                    <img src="./assets/image/location-icon.svg" alt="">
                                    <input type="text" id="tf-search-location" placeholder="Los angeles">
                                </label>
                            </div>
                            <div class="tf-booking-form-checkin">
                                <span class="tf-booking-form-title"><?php _e("Check in", "tourfic"); ?></span>
                                <div class="tf-booking-date-wrap">
                                    <span class="tf-booking-date"><?php _e("00", "tourfic"); ?></span>
                                    <span class="tf-booking-month">
                                        <span><?php _e("Month", "tourfic"); ?></span>
                                        <img src="<?php echo TF_ASSETS_APP_URL ?>images/select-arrow-dark.svg" alt="">
                                    </span>
                                </div>
                                <div class="tf_booking-dates">
                                    <div class="tf_label-row"></div>
                                </div>
                            </div>
                            <div class="tf-booking-form-checkout">
                                <span class="tf-booking-form-title"><?php _e("Check out", "tourfic"); ?></span>
                                <div class="tf-booking-date-wrap">
                                    <span class="tf-booking-date"><?php _e("00", "tourfic"); ?></span>
                                    <span class="tf-booking-month">
                                        <span><?php _e("Month", "tourfic"); ?></span>
                                        <img src="<?php echo TF_ASSETS_APP_URL ?>images/select-arrow-dark.svg" alt="">
                                    </span>
                                </div>
                                <input type="text" name="check-in-out-date" class="tf-check-in-out-date" onkeypress="return false;" placeholder="<?php _e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . $check_in_out . '"' : '' ?> required>

                            </div>
                            <div class="tf-booking-form-guest-and-room">
                                <div class="tf-booking-form-guest-and-room-inner">
                                    <span class="tf-booking-form-title"><?php _e("Guests", "tourfic"); ?></span>
                                    <div class="tf-booking-guest-and-room-wrap">
                                        <span class="tf-guest tf-booking-date"><?php _e("01", "tourfic"); ?></span> 
                                        <span class="tf-booking-month">
                                            <span><?php _e("Guest", "tourfic"); ?></span>
                                            <img src="<?php echo TF_ASSETS_APP_URL ?>images/select-arrow-dark.svg" alt="">
                                        </span>
                                    </div>
                                </div>

                                
                                <div class="tf_acrselection-wrap">
                                    <div class="tf_acrselection-inner">
                                        <div class="tf_acrselection">
                                            <div class="acr-label"><?php _e("Adults", "tourfic"); ?></div>
                                            <div class="acr-select">
                                                <div class="acr-dec">-</div>
                                                <input type="number" name="adults" id="adults" min="1" value="<?php echo ! empty( $adults ) ? $adults : '1'; ?>">
                                                <div class="acr-inc">+</div>
                                            </div>
                                        </div>
                                        <div class="tf_acrselection">
                                            <div class="acr-label"><?php _e("Children", "tourfic"); ?></div>
                                            <div class="acr-select">
                                                <div class="acr-dec">-</div>
                                                <input type="number" name="children" id="children" min="0" value="<?php echo ! empty( $child ) ? $child : '0'; ?>">
                                                <div class="acr-inc">+</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tf-booking-form-submit">
                            <button>Check <br>availability</button>
                        </div>
                    </form>
                </div>
                <!-- Booking form end -->                    


                <div class="tf-details-left tf-result-previews">
                    
                    <!--Available rooms start -->
                    <div class="tf-available-archive-hetels-wrapper tf-available-rooms-wrapper" id="tf-hotel-rooms">
                        <div class="tf-archive-available-rooms-head tf-available-rooms-head">
                            <h2 class=""><?php _e("Total", "tourfic"); ?> <?php echo $post_count; ?> <?php _e("hotels available", "tourfic"); ?></h2>
                            <div class="tf-filter">
                                <span><?php _e("Best match", "tourfic"); ?></span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </div>
                        </div>
                        
                        <!-- Loader Image -->
                        <div id="tour_room_details_loader">
                            <div id="tour-room-details-loader-img">
                                <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="">
                            </div>
                        </div>
                        
                        <!--Available rooms start -->
                        <div class="tf-archive-available-rooms tf-available-rooms">

                            <?php
                            if ( have_posts() ) {
                                while ( have_posts() ) {
                                    the_post();
                                    tf_hotel_archive_single_item();
                                }
                            } else {
                                echo '<div class="tf-nothing-found" data-post-count="0" >' .__("No Tours Found!", "tourfic"). '</div>';
                            }
                            ?>
                            
                        </div>
                        <!-- Available rooms end -->

                    </div>
                    <!-- Available rooms end -->

                </div>
                <div class="tf-details-right tf-sitebar-widgets tf-archive-right">
                    <div class="tf-filter-wrapper">
                        <div class="tf-filter-title">
                            <h2 class="tf-section-title">Filter</h2>
                            <button>Reset</button>
                        </div>
                        <div class="tf-filter-list-item">                                
                            <h4>Property name</h4>
                            <label class="tf-filter-location">
                                <img src="./assets/image/search-lg.svg" alt="">
                                <input type="text">
                            </label>
                        </div>                         
                        
                        <?php if ( is_active_sidebar( 'tf_archive_booking_sidebar' ) ) { ?>
                        <div id="tf__booking_sidebar">
                            <?php dynamic_sidebar( 'tf_archive_booking_sidebar' ); ?>
                        </div>
                        <?php } ?>
        
                    </div> 

                    
    
                </div>        
            </div>        
            <!-- Hotel details End -->


            <!-- Room PopUp Starts -->        
            <div class="tf-popup-wrapper tf-room-popup">
                <div class="tf-popup-inner">
                    <div class="tf-popup-head">
                        <ul>
                            <li data-filter>All images</li>
                            <li data-filter="rooms">Rooms</li>
                            <li data-filter="common-areas">Common areas</li>
                            <li data-filter="pool">Pool</li>
                            <li data-filter="dining">Dining</li>
                        </ul>                    
                    </div>
                    <div class="tf-popup-body">
                        <div class="tf-popup-left">
                            <img data-tags="common-areas" src="./assets/image/gallery-image-1.png" alt="" class="tf-popup-image">
                            <img data-tags="rooms,dining" src="./assets/image/gallery-image-2.png" alt="" class="tf-popup-image">
                            <img data-tags="common-areas" src="./assets/image/gallery-image-3.png" alt="" class="tf-popup-image">
                            <img data-tags="rooms" src="./assets/image/gallery-image-4.png" alt="" class="tf-popup-image">
                            <img data-tags="dining" src="./assets/image/gallery-image-5.png" alt="" class="tf-popup-image">
                            <img data-tags="dining,rooms,common-areas" src="./assets/image/gallery-image-6.png" alt="" class="tf-popup-image">
                            <img data-tags="pool" src="./assets/image/gallery-image-7-pool.jpg" alt="" class="tf-popup-image">
                            <img data-tags="pool,rooms" src="./assets/image/gallery-image-8-pool.jpg" alt="" class="tf-popup-image">
                        </div>
                        <div class="tf-popup-right">
                            <h4 class="tf-popup-info-title">Room details</h4>
                            <ul>
                                <li><i class="fas fa-ruler-combined"></i> 25 m2sft</li>
                                <li><i class="fas fa-bed"></i> 2 Number of Beds</li>
                                <li><i class="fab fa-creative-commons-zero"></i> Breakfast Included </li>
                                <li><i class="fas fa-road"></i> Carpeted </li>
                                <li><i class="fas fa-road"></i> Carpeted </li>
                                <li><i class="fas fa-tshirt"></i> Clothes rack </li>
                                <li><i class="fas fa-bed"></i> Double Bed </li>
                                <li><a href="#">View room details</a></li>                            
                            </ul> 
                            <a class="tf-all-benefits" href="#">All benefits</a>   
                            <h4 class="tf-popup-info-title"><i class="fa-solid fa-bed"></i> Bedroom</h4>
                            <ul>
                                <li>Linens</li>
                                <li>Wardrobe or closet</li>
                                <li>Air conditioning (climate-controlled)</li>
                                <li>Blackout drapes/curtains </li>
                                <li>Carpeted </li>
                                <li>Down comforter</li>
                                <li>Free cots/infant beds</li>                          
                            </ul> 
                            <h4 class="tf-popup-info-title"><i class="fa-solid fa-cookie-bite"></i> Food and drink</h4>
                            <ul>
                                <li>Linens</li>
                                <li>Wardrobe or closet</li>
                                <li>Air conditioning (climate-controlled)</li>
                                <li>Blackout drapes/curtains </li>
                                <li>Carpeted </li>
                                <li>Down comforter</li>
                                <li>Free cots/infant beds</li>                          
                            </ul> 
                        </div>
                    </div>                
                    <div class="tf-popup-close">
                        <i class="fa-solid fa-xmark"></i>
                    </div>
                </div>
            </div>
            <!-- Room PopUp end --> 
    

        </div>
    </div>
    <!--Content section end -->

    
    <!-- Hotel PopUp Starts -->       
    <div class="tf-popup-wrapper tf-hotel-popup">
        <div class="tf-popup-inner">
            <div class="tf-popup-body">
                
            </div>                
            <div class="tf-popup-close">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>
    </div>
    <!-- Hotel PopUp end -->  

</div>
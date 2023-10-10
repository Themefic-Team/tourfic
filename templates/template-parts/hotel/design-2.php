<div class="tf-template-3">
<!--Hero section start -->
<div class="tf-hero-section-wrap" style="<?php echo !empty(get_the_post_thumbnail_url()) ? 'background-image: url('.esc_url(get_the_post_thumbnail_url()).');' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
    <div class="tf-container">
        <div class="tf-hero-content">
            <div class="tf-wish-and-share">
                <?php
                // Wishlist
                if ( tfopt( 'wl-bt-for' ) && in_array( '2', tfopt( 'wl-bt-for' ) ) ) { 
                    if ( is_user_logged_in() ) {
                    if ( tfopt( 'wl-for' ) && in_array( 'li', tfopt( 'wl-for' ) ) ) {
                ?>
                <a class="tf-icon tf-wishlist-box tf-wishlist">
                    <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>" data-id="<?php echo $post_id ?>" data-type="<?php echo $post_type ?>" <?php if ( tfopt( 'wl-page' ) ) { echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"'; } ?>></i>
                </a>
                <?php } } else{ 
                if ( tfopt( 'wl-for' ) && in_array( 'lo', tfopt( 'wl-for' ) ) ) {    
                ?>
                <a class="tf-icon tf-wishlist-box tf-wishlist">
                    <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>" data-id="<?php echo $post_id ?>" data-type="<?php echo $post_type ?>" <?php if ( tfopt( 'wl-page' ) ) { echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"'; } ?>></i>
                </a>
                <?php } } } ?>
            
                <a class="tf-share">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M14 4.33203C14 5.4366 13.1046 6.33203 12 6.33203C10.8954 6.33203 10 5.4366 10 4.33203C10 3.22746 10.8954 2.33203 12 2.33203C13.1046 2.33203 14 3.22746 14 4.33203Z" stroke="#FDF9F4" stroke-width="1.5"/>
                    <path d="M6 8C6 9.10457 5.10457 10 4 10C2.89543 10 2 9.10457 2 8C2 6.89543 2.89543 6 4 6C5.10457 6 6 6.89543 6 8Z" stroke="#FDF9F4" stroke-width="1.5"/>
                    <path d="M14 11.6641C14 12.7686 13.1046 13.6641 12 13.6641C10.8954 13.6641 10 12.7686 10 11.6641C10 10.5595 10.8954 9.66406 12 9.66406C13.1046 9.66406 14 10.5595 14 11.6641Z" stroke="#FDF9F4" stroke-width="1.5"/>
                    <path d="M5.81836 7.16371L10.1517 5.16406M5.81836 8.83073L10.1517 10.8304" stroke="#FDF9F4" stroke-width="1.5"/>
                    </svg>
                </a>
            </div>
            <div class="tf-hero-bottom-area">
                <div class="tf-head-title">
                    <h1><?php echo get_the_title(); ?></h1>
                    <div class="tf-title-meta">
                        <i class="ri-map-pin-line"></i>
                        <a href="#tf-map"><?php echo esc_html( $address ); ?></a>
                    </div>
                </div>
                <div class="tf-hero-gallery-videos">
                    <?php
                    $hotel_video = ! empty( $meta['video'] ) ? $meta['video'] : '';
                    if ( !empty($hotel_video) ) { ?>
                    <div class="tf-hero-video tf-popup-buttons">
                        <a href="#">
                            <svg width="23" height="18" viewBox="0 0 23 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="content">
                            <path id="Vector 3570" d="M10.5 5L12.5 5" stroke="#FDF9F4" stroke-width="1.5" stroke-linecap="round"/>
                            <path id="Rectangle 368" d="M1.5 8C1.5 4.70017 1.5 3.05025 2.52513 2.02513C3.55025 1 5.20017 1 8.5 1H9.5C12.7998 1 14.4497 1 15.4749 2.02513C16.5 3.05025 16.5 4.70017 16.5 8V10C16.5 13.2998 16.5 14.9497 15.4749 15.9749C14.4497 17 12.7998 17 9.5 17H8.5C5.20017 17 3.55025 17 2.52513 15.9749C1.5 14.9497 1.5 13.2998 1.5 10V8Z" stroke="#FDF9F4" stroke-width="1.5"/>
                            <path id="Rectangle 369" d="M16.5 5.90585L16.6259 5.80196C18.7417 4.05623 19.7996 3.18336 20.6498 3.60482C21.5 4.02628 21.5 5.42355 21.5 8.21808V9.78192C21.5 12.5765 21.5 13.9737 20.6498 14.3952C19.7996 14.8166 18.7417 13.9438 16.6259 12.198L16.5 12.0941" stroke="#FDF9F4" stroke-width="1.5" stroke-linecap="round"/>
                            </g>
                            </svg>
                        </a>
                    </div>
                    <?php } ?>
                    <?php 
                    if ( ! empty( $gallery_ids ) ) {
                    ?>
                    <div class="tf-hero-hotel tf-popup-buttons">
                        <a href="#">
                            <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="content">
                            <path id="Rectangle 2111" d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5" stroke="#FDF9F4" stroke-width="1.5"/>
                            <path id="Rectangle 2109" d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z" stroke="#FDF9F4" stroke-width="1.5"/>
                            <path id="Vector" d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17" stroke="#FDF9F4" stroke-width="1.5" stroke-linejoin="round"/>
                            <path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            </svg>
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Hero section End -->


<!--Content section end -->
<div class="tf-content-wrapper">
    
    <div class="tf-container">
    
    <!-- Hotel details Srart -->
    <div class="tf-details" id="tf-hotel-overview">
        <div class="tf-details-left">
            <!-- menu section Start -->
            <div class="tf-details-menu">
                <ul>
                    <li><a class="tf-hashlink" href="#tf-hotel-overview">Overview</a></li>
                    <li><a href="#tf-hotel-rooms">Rooms</a></li>
                    <li><a href="#tf-hotel-facilities">Facilities</a></li>
                    <li><a href="#tf-hotel-reviews">Reviews</a></li>
                    <li><a href="#tf-hotel-faq">FAQ's</a></li>
                    <li><a href="#tf-hotel-policies">Policies</a></li>
                </ul>
            </div>
            <!-- menu section End -->


            <!--Overview Start -->
            <div class="tf-overview-wrapper">
                <div class="tf-overview-description">
                    <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there
                        live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics,
                        a large language ocean. A small river named Duden flows by their place and supplies it with the
                        necessary regelialia. It is a paradisematic country... See more</p>
                </div>
                <div class="tf-overview-popular-facilities">
                    <h3>Popular facilities</h3>
                    <ul>
                        <li>
                            <i class="fa-solid fa-water-ladder"></i>
                            <span>Swimming pool </span>
                        </li>
                        <li>
                            <i class="fa-solid fa-person-praying"></i>
                            <span>Prayer zone</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-water-ladder"></i>
                            <span>Swimming pool</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-person-praying"></i>
                            <span>Prayer zone</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-water-ladder"></i>
                            <span>Swimming pool</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-person-praying"></i>
                            <span>Prayer zone</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-water-ladder"></i>
                            <span>Swimming pool</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-person-praying"></i>
                            <span>Prayer zone</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-water-ladder"></i>
                            <span>Swimming pool</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-person-praying"></i>
                            <span>Prayer zone</span>
                        </li>
                    </ul>
                </div>
            </div>
            <!--Overview End -->

            <!--Booking form start -->
            <div class="tf-booking-form-wrapper">
                <form action="" class="tf-booking-form">
                    <div class="tf-booking-form-fields">
                        <div class="tf-booking-form-checkin">
                            <span class="tf-booking-form-title">Check in</span>
                            <div class="tf-booking-date-wrap">
                                <span class="tf-booking-date">00</span>
                                <span class="tf-booking-month">
                                    <span>Mon</span>
                                    <img src="./assets/image/select-arrow-dark.svg" alt="">
                                </span>
                            </div>
                            <input id="tf_checkin_date" type="text">
                        </div>
                        <div class="tf-booking-form-checkout">
                            <span class="tf-booking-form-title">Check out</span>
                            <div class="tf-booking-date-wrap">
                                <span class="tf-booking-date">00</span>
                                <span class="tf-booking-month">
                                    <span>Mon</span>
                                    <img src="./assets/image/select-arrow-dark.svg" alt="">
                                </span>
                            </div>
                            <input id="tf_checkout_date" type="text">
                        </div>
                        <div class="tf-booking-form-guest-and-room">
                            <div class="tf-booking-form-guest-and-room-inner">
                                <span class="tf-booking-form-title">Guests & rooms</span>
                                <div class="tf-booking-guest-and-room-wrap">
                                    <span class="tf-guest">01</span> guest <span class="tf-room">01</span> Rooms
                                </div>
                                <div class="tf-booking-person-count">
                                    <span>3 adults 1 children</span>
                                    <img src="./assets/image/select-arrow-dark.svg" alt="">
                                </div>
                            </div>

                            
                            <div class="tf_acrselection-wrap">
                                <div class="tf_acrselection-inner">
                                    <div class="tf_acrselection">
                                        <div class="acr-label">Adults</div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="adults" id="adults" min="1" value="1">
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
                                    <div class="tf_acrselection">
                                        <div class="acr-label">Children</div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="children" id="children" min="0" value="0">
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
                                    <div class="tf_acrselection">
                                        <div class="acr-label">Rooms</div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="room" id="room" min="1" value="1">
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

            <!--Available rooms start -->
            <div class="tf-available-rooms-wrapper" id="tf-hotel-rooms">
                <div class="tf-available-rooms-head">
                    <h2 class="       ">Available rooms</h2>
                    <div class="tf-filter">
                        <img src="./assets/image/filter.png" alt="">
                    </div>
                </div>

                <!--Available rooms start -->
                <div class="tf-available-rooms">
                    <div class="tf-available-room">
                        <div class="tf-available-room-gallery">                       
                            <div class="tf-room-gallery">
                                <img src="./assets/image/room1.png" alt="">
                            </div>                                                   
                            <div class="tf-room-gallery">
                                <img src="./assets/image/room2.png" alt="">                                
                            </div>                          
                            <div class="tf-room-gallery tf-popup-buttons" style="background-image: url('./assets/image/room3.png'); ">                                
                                <img src="./assets/image/gallery-icon.svg" alt="">
                            </div>
                        </div>
                        <div class="tf-available-room-content">
                            <div class="tf-available-room-content-left">
                                <h2 class="tf-section-title">Premium Deluxe Twin</h2>
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
                                <h4>Other benefits</h4>
                                <ul>
                                    <li><i class="fas fa-ruler-combined"></i> 25 m2sft</li>
                                    <li><i class="fas fa-bed"></i> 2 Number of Beds</li>
                                    <li><i class="fab fa-creative-commons-zero"></i> Breakfast Included </li>
                                    <li><i class="fas fa-road"></i> Carpeted </li>
                                    <li><i class="fas fa-tshirt"></i> Clothes rack </li>
                                    <li><a href="#">See all benefits</a></li>
                                </ul>
                            </div>
                            <div class="tf-available-room-content-right">
                                <div class="tf-cancellation-refundable-text">
                                    <span>Free cancellation <i class="fa-solid fa-info"></i></span>
                                    <span>Free refundable <i class="fa-solid fa-info"></i></span>
                                </div>
                                <div class="tf-available-room-off">
                                    <span>60% off</span>
                                </div>
                                <div class="tf-available-room-price">
                                    <span class="tf-price-from">From $450</span>
                                    <span class="tf-price"><span>$250</span>/night</span>
                                </div>
                                
                                <div class="tf-available-room-select">
                                    <span>Select your room</span>
                                    <select name="" id="">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                </div>   
                                <div class="tf-available-room-purchase-summery">
                                    <span>Total $450 for 3 nights, 1 room</span>
                                </div>                            
                                <button>Continue</button>
                            </div>

                        </div>
                    </div>
                    <div class="tf-available-room">
                        <div class="tf-available-room-gallery">                       
                            <div class="tf-room-gallery">
                                <img src="./assets/image/room1.png" alt="">
                            </div>                                                   
                            <div class="tf-room-gallery">
                                <img src="./assets/image/room2.png" alt="">                                
                            </div>                          
                            <div class="tf-room-gallery tf-popup-buttons" style="background-image: url('./assets/image/room3.png'); ">                                
                                <img src="./assets/image/gallery-icon.svg" alt="">
                            </div>
                        </div>
                        <div class="tf-available-room-content">
                            <div class="tf-available-room-content-left">
                                <h2 class="tf-section-title">Premium Deluxe Twin</h2>
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
                                <h4>Other benefits</h4>
                                <ul>
                                    <li><i class="fas fa-ruler-combined"></i> 25 m2sft</li>
                                    <li><i class="fas fa-bed"></i> 2 Number of Beds</li>
                                    <li><i class="fab fa-creative-commons-zero"></i> Breakfast Included </li>
                                    <li><i class="fas fa-road"></i> Carpeted </li>
                                    <li><i class="fas fa-tshirt"></i> Clothes rack </li>
                                    <li><a href="#">See all benefits</a></li>
                                </ul>
                            </div>
                            <div class="tf-available-room-content-right">
                                <div class="tf-cancellation-refundable-text">
                                    <span>Free cancellation <i class="fa-solid fa-info"></i></span>
                                    <span>Free refundable <i class="fa-solid fa-info"></i></span>
                                </div>
                                <div class="tf-available-room-off">
                                    <span>60% off</span>
                                </div>
                                <div class="tf-available-room-price">
                                    <span class="tf-price-from">From $450</span>
                                    <span class="tf-price"><span>$250</span>/night</span>
                                </div>
                                
                                <div class="tf-available-room-select">
                                    <span>Select your room</span>
                                    <select name="" id="">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                </div>   
                                <div class="tf-available-room-purchase-summery">
                                    <span>Total $450 for 3 nights, 1 room</span>
                                </div>                            
                                <button>Continue</button>
                            </div>

                        </div>
                    </div>
                    <div class="tf-available-room">
                        <div class="tf-available-room-gallery">                       
                            <div class="tf-room-gallery">
                                <img src="./assets/image/room1.png" alt="">
                            </div>                                                   
                            <div class="tf-room-gallery">
                                <img src="./assets/image/room2.png" alt="">                                
                            </div>                          
                            <div class="tf-room-gallery tf-popup-buttons" style="background-image: url('./assets/image/room3.png'); ">                                
                                <img src="./assets/image/gallery-icon.svg" alt="">
                            </div>
                        </div>
                        <div class="tf-available-room-content">
                            <div class="tf-available-room-content-left">
                                <h2 class="tf-section-title">Premium Deluxe Twin</h2>
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
                                <h4>Other benefits</h4>
                                <ul>
                                    <li><i class="fas fa-ruler-combined"></i> 25 m2sft</li>
                                    <li><i class="fas fa-bed"></i> 2 Number of Beds</li>
                                    <li><i class="fab fa-creative-commons-zero"></i> Breakfast Included </li>
                                    <li><i class="fas fa-road"></i> Carpeted </li>
                                    <li><i class="fas fa-tshirt"></i> Clothes rack </li>
                                    <li><a href="#">See all benefits</a></li>
                                </ul>
                            </div>
                            <div class="tf-available-room-content-right">
                                <div class="tf-cancellation-refundable-text">
                                    <span>Free cancellation <i class="fa-solid fa-info"></i></span>
                                    <span>Free refundable <i class="fa-solid fa-info"></i></span>
                                </div>
                                <div class="tf-available-room-off">
                                    <span>60% off</span>
                                </div>
                                <div class="tf-available-room-price">
                                    <span class="tf-price-from">From $450</span>
                                    <span class="tf-price"><span>$250</span>/night</span>
                                </div>
                                
                                <div class="tf-available-room-select">
                                    <span>Select your room</span>
                                    <select name="" id="">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                </div>   
                                <div class="tf-available-room-purchase-summery">
                                    <span>Total $450 for 3 nights, 1 room</span>
                                </div>                            
                                <button>Continue</button>
                            </div>

                        </div>
                    </div>
                    <div class="tf-available-room">
                        <div class="tf-available-room-gallery">                       
                            <div class="tf-room-gallery">
                                <img src="./assets/image/room1.png" alt="">
                            </div>                                                   
                            <div class="tf-room-gallery">
                                <img src="./assets/image/room2.png" alt="">                                
                            </div>                          
                            <div class="tf-room-gallery tf-popup-buttons" style="background-image: url('./assets/image/room3.png'); ">                                
                                <img src="./assets/image/gallery-icon.svg" alt="">
                            </div>
                        </div>
                        <div class="tf-available-room-content">
                            <div class="tf-available-room-content-left">
                                <h2 class="tf-section-title">Premium Deluxe Twin</h2>
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
                                <h4>Other benefits</h4>
                                <ul>
                                    <li><i class="fas fa-ruler-combined"></i> 25 m2sft</li>
                                    <li><i class="fas fa-bed"></i> 2 Number of Beds</li>
                                    <li><i class="fab fa-creative-commons-zero"></i> Breakfast Included </li>
                                    <li><i class="fas fa-road"></i> Carpeted </li>
                                    <li><i class="fas fa-tshirt"></i> Clothes rack </li>
                                    <li><a href="#">See all benefits</a></li>
                                </ul>
                            </div>
                            <div class="tf-available-room-content-right">
                                <div class="tf-cancellation-refundable-text">
                                    <span>Free cancellation <i class="fa-solid fa-info"></i></span>
                                    <span>Free refundable <i class="fa-solid fa-info"></i></span>
                                </div>
                                <div class="tf-available-room-off">
                                    <span>60% off</span>
                                </div>
                                <div class="tf-available-room-price">
                                    <span class="tf-price-from">From $450</span>
                                    <span class="tf-price"><span>$250</span>/night</span>
                                </div>
                                
                                <div class="tf-available-room-select">
                                    <span>Select your room</span>
                                    <select name="" id="">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                </div>   
                                <div class="tf-available-room-purchase-summery">
                                    <span>Total $450 for 3 nights, 1 room</span>
                                </div>                            
                                <button>Continue</button>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Available rooms end -->

            </div>
            <!-- Available rooms end -->



        </div>
        <div class="tf-details-right tf-sitebar-widgets">
            <div class="tf-whats-around tf-single-widgets">
                <h2 class="tf-section-title">What’s around?</h2>
                <ul>
                    <li>
                        <span><i class="fa-solid fa-umbrella-beach"></i> Patenga sea beach</span>
                        <span>5 km in drive</span>
                    </li>
                    <li>
                        <span><i class="fa-solid fa-mosque"></i> Al aksha mosque</span>
                        <span>10 min walking</span>
                    </li>
                    
                    <li>
                        <span><i class="fa-solid fa-plane-up"></i> Singapore airport</span>
                        <span>2 km in drive</span>
                    </li>
                    
                    <li>
                        <span><i class="fa-solid fa-shop"></i> Jamuna future park</span>
                        <span>3 km in drive</span>
                    </li>
                </ul>
            </div>  
            
            <div class="tf-location tf-single-widgets">
                <h2 class="tf-section-title">Location</h2>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193595.2528001631!2d-74.14448723354508!3d40.69763123329699!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2sbd!4v1696128927921!5m2!1sen!2sbd" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>   
            
            
            <div class="tf-location tf-single-widgets">
                <h2 class="tf-section-title">Overall reviews</h2>
                <div class="tf-review-data-inner">
                    <div class="tf-review-data">
                        <div class="tf-review-data-average">
                            <p><span>8.6</span>/10</p>
                        </div>
                        <div class="tf-review-all-info">
                            <p>Excellent <span>Total 110 reviews</span></p>
                        </div>
                    </div>
                    <div class="tf-review-data-features">
                        <div class="tf-percent-progress">
                            <div class="tf-progress-item">                                    
                                <div class="tf-review-feature-label">
                                    <p class="feature-label">Staff</p>
                                    <p class="feature-rating"> 9.5</p>
                                </div>
                                <div class="tf-progress-bar">
                                    <span class="percent-progress" style="width: 95.00%"></span>
                                </div>
                            </div>
                            <div class="tf-progress-item">                                    
                                <div class="tf-review-feature-label">
                                    <p class="feature-label">Facilities</p>
                                    <p class="feature-rating"> 7.5</p>
                                </div>
                                <div class="tf-progress-bar">
                                    <span class="percent-progress" style="width: 75.00%"></span>
                                </div>
                            </div>
                            <div class="tf-progress-item">                                    
                                <div class="tf-review-feature-label">
                                    <p class="feature-label">Cleanliness</p>
                                    <p class="feature-rating"> 9.5</p>
                                </div>
                                <div class="tf-progress-bar">
                                    <span class="percent-progress" style="width: 95.00%"></span>
                                </div>
                            </div>
                            <div class="tf-progress-item">                                    
                                <div class="tf-review-feature-label">
                                    <p class="feature-label">Comfort</p>
                                    <p class="feature-rating"> 6.5</p>
                                </div>
                                <div class="tf-progress-bar">
                                    <span class="percent-progress" style="width: 65.00%"></span>
                                </div>
                            </div>
                            <div class="tf-progress-item">                                    
                                <div class="tf-review-feature-label">
                                    <p class="feature-label">Value for money</p>
                                    <p class="feature-rating"> 8.5</p>
                                </div>
                                <div class="tf-progress-bar">
                                    <span class="percent-progress" style="width: 85.00%"></span>
                                </div>
                            </div>
                                    
                        </div>
                    </div>
                </div>
                <a class="tf-all-reviews" href="#">See all reviews</a>

                <button class="tf-review-open button">Leave your review</button>

                <div class="tf-review-form-wrapper" action="">
                    <h3>Leave your review</h3>
                    <p>Your email address will not be published. Required fields are marked.</p>
                    <form action="#">
                        <div class="tf-reting-item">
                            <span class="tf-reting-title">Staff</span>
                            <span class="tf-reting-stars">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </span>
                        </div>
                        <div class="tf-reting-item">
                            <span class="tf-reting-title">Facilities</span>
                            <span class="tf-reting-stars">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </span>
                        </div>
                        <div class="tf-reting-item">
                            <span class="tf-reting-title">Cleanliness</span>
                            <span class="tf-reting-stars">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </span>
                        </div>
                        <div class="tf-reting-item">
                            <span class="tf-reting-title">Comfort</span>
                            <span class="tf-reting-stars">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </span>
                        </div>
                        <div class="tf-reting-item">
                            <span class="tf-reting-title">Service</span>
                            <span class="tf-reting-stars">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </span>
                        </div>
                        <div class="tf-reting-item">
                            <span class="tf-reting-title">Value for money</span>
                            <span class="tf-reting-stars">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </span>
                        </div>
                        <div class="tf-reting-field">
                            <span>Description</span>
                            <textarea name="" id="" cols="30" rows="10"></textarea>
                        </div>
                        <div class="tf-reting-field">
                            <span>Email</span>
                            <input type="email">
                        </div>
                        <div class="tf-reting-field">
                            <span>Name</span>
                            <input type="text">
                        </div>                            
                        <div class="tf-reting-field">
                            <button type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>       
        </div>        
    </div>        
    <!-- Hotel details End -->

    <!-- Hotel facilities Srart -->
    <div class="tf-facilities-wrapper" id="tf-hotel-facilities">              
        <h2 class="tf-section-title">Property facilities</h2>                
        <div class="tf-facilities">  
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-headset"></i> Services</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-chess"></i>Sports and Leisure</h4>
                <ul>
                    <li>Table tennis</li>
                    <li>Coffee shop</li>
                    <li>BBQ facilities</li>
                    <li>Garden & terrace</li>
                    <li>Gym</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-brands fa-gripfire"></i>Safety & security</h4>
                <ul>
                    <li>Fire extinguishers</li>
                    <li>CCTV in common areas</li>
                    <li>Smoke alarms</li>
                    <li>Key access</li>
                    <li>Doctor/nurse on call</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-network-wired"></i>Internet access</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>

            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-chess"></i>Sports and Leisure</h4>
                <ul>
                    <li>Table tennis</li>
                    <li>Coffee shop</li>
                    <li>BBQ facilities</li>
                    <li>Garden & terrace</li>
                    <li>Gym</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-brands fa-gripfire"></i>Safety & security</h4>
                <ul>
                    <li>Fire extinguishers</li>
                    <li>CCTV in common areas</li>
                    <li>Smoke alarms</li>
                    <li>Key access</li>
                    <li>Doctor/nurse on call</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-network-wired"></i>Internet access</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>
        </div>
        <h2 class="tf-section-title">Room facilities</h2>                
        <div class="tf-facilities">  
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-person-snowboarding"></i>Services</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-person-snowboarding"></i>Services</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-person-snowboarding"></i>Services</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-person-snowboarding"></i>Services</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-person-snowboarding"></i>Services</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>
            <div class="tf-facility-item">
                <h4><i class="fa-solid fa-person-snowboarding"></i>Services</h4>
                <ul>
                    <li>Car rental</li>
                    <li>Elevator</li>
                    <li>24 hours security</li>
                    <li>Ironing service(Chargable)</li>
                </ul>
            </div>
        </div>
    </div>
    <!--Content facilities end -->

    
    <!-- Hotel reviews Srart -->
    <div class="tf-reviews-wrapper tf-section" id="tf-hotel-reviews">         
        <h2 class="tf-section-title">Guest reviews</h2> 
        <p>Total 6 reviews</p>
        <div class="tf-reviews-slider">
            <div class="tf-reviews-item">
                <div class="tf-reviews-avater">
                    <img src="./assets/image/review-avater.png" alt="">
                </div>
                <div class="tf-reviews-text">
                    <h3>8.5 Excellent</h3>
                    <span class="tf-reviews-meta">Jon doe, July 2023</span>
                    <p>When I reached hotel I go to reception counter I told to one of receptionist lady that I have a reservation please complete my formalities. She told me wait just.</p>
                </div>
            </div>
            <div class="tf-reviews-item">
                <div class="tf-reviews-avater">
                    <img src="./assets/image/review-avater.png" alt="">
                </div>
                <div class="tf-reviews-text">
                    <h3>8.5 Excellent</h3>
                    <span class="tf-reviews-meta">Jon doe, July 2023</span>
                    <p>When I reached hotel I go to reception counter I told to one of receptionist lady that I have a reservation please complete my formalities. She told me wait just.</p>
                </div>
            </div>
            <div class="tf-reviews-item">
                <div class="tf-reviews-avater">
                    <img src="./assets/image/review-avater.png" alt="">
                </div>
                <div class="tf-reviews-text">
                    <h3>8.5 Excellent</h3>
                    <span class="tf-reviews-meta">Jon doe, July 2023</span>
                    <p>When I reached hotel I go to reception counter I told to one of receptionist lady that I have a reservation please complete my formalities. She told me wait just.</p>
                </div>
            </div>
            <div class="tf-reviews-item">
                <div class="tf-reviews-avater">
                    <img src="./assets/image/review-avater.png" alt="">
                </div>
                <div class="tf-reviews-text">
                    <h3>8.5 Excellent</h3>
                    <span class="tf-reviews-meta">Jon doe, July 2023</span>
                    <p>When I reached hotel I go to reception counter I told to one of receptionist lady that I have a reservation please complete my formalities. She told me wait just.</p>
                </div>
            </div>
            <div class="tf-reviews-item">
                <div class="tf-reviews-avater">
                    <img src="./assets/image/review-avater.png" alt="">
                </div>
                <div class="tf-reviews-text">
                    <h3>8.5 Excellent</h3>
                    <span class="tf-reviews-meta">Jon doe, July 2023</span>
                    <p>When I reached hotel I go to reception counter I told to one of receptionist lady that I have a reservation please complete my formalities. She told me wait just.</p>
                </div>
            </div>
        </div>

    </div>
    <!--Content reviews end -->


    <?php if ( $faqs ): ?>
    <!-- Hotel Questions Srart -->
    <div class="tf-questions-wrapper tf-section" id="tf-hotel-faq">
        <h2 class="tf-section-title">
        <?php echo !empty($meta['faq-section-title']) ? esc_html($meta['faq-section-title']) : __( "Faq’s", 'tourfic' ); ?>
        </h2>            
        <div class="tf-questions">
            <div class="tf-questions-col">
                <?php 
                $faq_key = 1;    
                foreach ( $faqs as $key => $faq ): ?>
                <div class="tf-question <?php echo $faq_key==1 ? esc_attr( 'tf-active' ) : ''; ?>">
                    <h3><?php echo esc_html( $faq['title'] ); ?></h3>
                    <div class="tf-question-desc" style="<?php echo $faq_key==1 ? esc_attr( 'display: block;' ) : ''; ?>">
                        <?php echo wp_kses_post( $faq['description'] ); ?>
                    </div>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <?php $faq_key++; endforeach; ?>
                
            </div>
            
        </div>
    </div>

    <!-- Hotel Questions end -->
    <?php endif; ?>

    <?php if ( $tc ) { ?>
    <!-- Hotel Policies Starts -->        
    <div class="tf-policies-wrapper tf-section" id="tf-hotel-policies">            
        <h2 class="tf-section-title">
            <?php echo !empty($meta['tc-section-title']) ? esc_html($meta['tc-section-title']) : __("Hotel Terms & Conditions","tourfic"); ?>
        </h2>  
        <div class="tf-policies">
            <?php echo wpautop( $tc ); ?>
        </div>
    </div>
    <!-- Hotel Policies end -->
    <?php } ?>

    <?php 
    if ( ! empty( $gallery_ids ) ) {
    ?>
    <!-- Hotel PopUp Starts -->       
    <div class="tf-popup-wrapper tf-hotel-popup">
        <div class="tf-popup-inner">
            
            <div class="tf-popup-body">
                <?php 
                    if ( ! empty( $gallery_ids ) ) {
                    foreach ( $gallery_ids as $key => $gallery_item_id ) {
                    $image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
                ?>
                <img src="<?php echo esc_url($image_url); ?>" alt="" class="tf-popup-image">
                <?php } } ?>
            </div>                
            <div class="tf-popup-close">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>
    </div>
    <!-- Hotel PopUp end -->  
    <?php } ?>

    <?php 
    if ( !empty($hotel_video) ) { ?>
    <!-- Video PopUp Starts -->        
    <div class="tf-popup-wrapper tf-video-popup">
        <div class="tf-popup-inner">
            
            <div class="tf-popup-body">                   
                <div class="tf-popup-video-item">
                    <iframe src="<?php echo esc_url($hotel_video); ?>"></iframe>
                </div>                 
            </div>                
            <div class="tf-popup-close">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>
    </div>
    <!-- Video PopUp end --> 
    <?php } ?>


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
</div>
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


                <div class="tf-details-left">

                    <!--Available rooms start -->
                    <div class="tf-available-archive-hetels-wrapper tf-available-rooms-wrapper" id="tf-hotel-rooms">
                        <div class="tf-archive-available-rooms-head tf-available-rooms-head">
                            <h2 class="">Total 45 hotels available </h2>
                            <div class="tf-filter">
                                <span>Best match</span>
                                <i class="fa-solid fa-chevron-down"></i>
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
                        <div class="tf-filter-list-item">                                
                            <h4>Price range</h4>
                            <div class="tf-filter-pricing-range"></div>
                        </div>  
                        <div class="tf-filter-list-item">
                            <h4>Popular filter for Bangkok</h4>
                            <ul>
                                <li>
                                    <input type="checkbox" name="" id="Breakfast">
                                    <label for="Breakfast">Breakfast</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="Pool">
                                    <label for="Pool">Pool</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="Hotel">
                                    <label for="Hotel">Hotel</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="condition">
                                    <label for="condition">Air condition</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="cancelation">
                                    <label for="cancelation">Free cancelation</label>                                        
                                </li>
                            </ul>
                        </div>
                        <div class="tf-filter-list-item">
                            <h4>Payment type</h4>
                            <ul>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Fully refundable</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Reserve now, pay later</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Free cancelation</label>                                        
                                </li>
                            </ul>
                        </div>                            
                        <div class="tf-filter-list-item">
                            <h4>Meals</h4>
                            <ul>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Fully refundable</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Breakfast included</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Free cancelation</label>                                        
                                </li>
                            </ul>
                        </div>
                        <div class="tf-filter-list-item tf-filter-rating">
                            <h4>rating</h4>
                            <ul>
                                <li>                                   
                                    <input type="checkbox" name="" id="">
                                    <label>                                                                     
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                    </label>
                                </li>
                                <li>                                    
                                    <input type="checkbox" name="" id="">
                                    <label>                                       
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                    </label>
                                </li>
                                <li>                                   
                                    <input type="checkbox" name="" id="">
                                    <label>                                         
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                    </label>
                                </li>
                                <li>                                   
                                    <input type="checkbox" name="" id="">
                                    <label>                                         
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                    </label>
                                </li>
                                <li>                                   
                                    <input type="checkbox" name="" id="">
                                    <label>                                      
                                        <i class="fa-solid fa-star"></i>
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div class="tf-filter-list-item">
                            <h4>Facilities</h4>
                            <ul>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Breakfast</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Fireplace</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Non-smoking</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Breakfast</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Fireplace</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Non-smoking</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Breakfast</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Fireplace</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Non-smoking</label>                                        
                                </li>
                                <li>
                                    <a href="" class="tf-filter-list-see-less">See less</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tf-filter-list-item">
                            <h4>Bed type</h4>
                            <ul>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Twin bed</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Double bed</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">King bed</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Single bed</label>                                        
                                </li>
                                <li>
                                    <input type="checkbox" name="" id="">
                                    <label for="asd">Fireplace</label>                                        
                                </li>
                            </ul>
                        </div>    
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
 
</div>
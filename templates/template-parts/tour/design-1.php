<div class="tf-single-page tf-template-global">
        <div class="tf-tour-single">
            <div class="tf-template-container">
                <div class="tf-container-inner">
                    <!-- Single Tour Heading Section start -->
                    <div class="tf-section tf-single-head">
                        <div class="tf-head-info tf-flex tf-flex-space-bttn tf-flex-gap-24">
                            <div class="tf-head-title">
                                <h1><?php the_title(); ?></h1>
                                <div class="tf-title-meta tf-flex tf-flex-align-center tf-flex-gap-8">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <?php if ( $location ) {
                                        echo '<a href="#tour-map"><p>' . $location . '.</p></a>';
                                    } ?>
                                </div>
                            </div>
                            <div class="tf-head-social tf-flex tf-flex-gap-8 tf-flex-align-center">
                                <div class="tf-icon tf-wishlist-box">
                                    <i class="fa-regular fa-heart"></i>
                                </div>
                                <div class="tf-icon tf-social-box">
                                    <i class="fa-solid fa-share-nodes"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Tour Heading Section End -->

                    <!-- Single Tour Body details start -->
                    <div class="tf-single-details-wrapper tf-mrtop-30">
                        <div class="tf-single-details-inner tf-flex">
                            <div class="tf-column tf-tour-details-left">

                            <?php 
                            if( !empty(tf_data_types(tfopt( 'tf-template' ))['single-tour-layout']) ){
                                foreach(tf_data_types(tfopt( 'tf-template' ))['single-tour-layout'] as $section){
                                    if( !empty($section['tour-section-status']) && $section['tour-section-status']=="1" && !empty($section['tour-section-slug']) ){
                                        include TF_TEMPLATE_PART_PATH . 'tour/design-1/'.$section['tour-section-slug'].'.php';
                                    }
                                }
                            }
                            ?>
                                
                            </div>
    
                            <!-- SIdebar Tour single -->
                            <div class="tf-column tf-tour-details-right">
                               <div class="tf-tour-booking-box tf-box">
                                    <!-- Tourfic Pricing Head -->
                                    <div class="tf-booking-form-data">
                                        <div class="tf-booking-block">
                                            <div class="tf-booking-price tf-padbtm-12">
                                                <p> <span>From</span> $634.00</p>
                                            </div>
                                        </div>
                                    </div>
    
                                    <!-- Tourfic Booking form -->
                                    <div class="tf-booking-form">
                                        <div class="tf-booking-form-inner tf-mrtop-24">
                                            <h3>Book This Tour</h3>
                                            <form action="" class="tf-mrtop-16">
    
                                                <div class="tf-field-group tf-mrtop-8">
                                                    <i class="fa-solid fa-location-dot"></i>
                                                    <input type="text" class="tf-field location" name="location" placeholder="Select Date">
                                                </div>
                                                <div class="tf-field-group tf-mrtop-8">
                                                    <i class="fa-regular fa-clock"></i>
                                                    <input type="text" class="tf-field time" name="time" placeholder="Select Date">
                                                </div>
    
                                                <div class="tf-booking-person tf-mrtop-30">
                                                    <div class="tf-form-title">
                                                        <p>Tickets</p>
                                                    </div>
                                                    <div class="tf-field-group tf-mrtop-16">
                                                        <i class="fa-regular fa-user"></i>
                                                        <select class="tf-field adult" id="adult">
                                                            <option value="option1" selected>Option 1</option>
                                                            <option value="option2">Option 2</option>
                                                            <option value="option3">Option 3</option>
                                                        </select>
                                                    </div>
                                                    <div class="tf-field-group tf-mrtop-16">
                                                        <i class="fa-solid fa-child"></i>
                                                        <select class="tf-field child" id="child">
                                                            <option value="option1" selected>Option 1</option>
                                                            <option value="option2">Option 2</option>
                                                            <option value="option3">Option 3</option>
                                                        </select>
                                                    </div>
                                                    <div class="tf-field-group tf-mrtop-16">
                                                        <i class="fa-solid fa-baby"></i>
                                                        <select class="tf-field child" id="child">
                                                            <option value="option1" selected>Option 1</option>
                                                            <option value="option2">Option 2</option>
                                                            <option value="option3">Option 3</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="tf-tour-extra-area tf-mrtop-30">
                                                    <div class="tf-form-title">
                                                        <p>Tour Extra</p>
                                                    </div>
                                                    <div class="tf-tour-extra tf-mrtop-8">
                                                        <div class="tf-tour-extra-price tf-flex tf-flex-align-top tf-flex-space-bttn">
                                                            <div class="tf-tour-extra-input tf-flex tf-flex-align-top tf-flex-gap-8">
                                                                <input type="checkbox" name="" id="">
                                                                <p>Extra Service 1</p>
                                                            </div>
                                                            <div class="tf-tour-extra-price">
                                                                $120.00
                                                            </div>
                                                        </div>
                                                        <div class="tf-tour-extra-details tf-mrtop-8">
                                                            <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.</p>
                                                        </div>
                                                    </div>
                                                    <div class="tf-tour-extra tf-mrtop-8">
                                                        <div class="tf-tour-extra-price tf-flex tf-flex-align-top tf-flex-space-bttn">
                                                            <div class="tf-tour-extra-input tf-flex tf-flex-align-top tf-flex-gap-8">
                                                                <input type="checkbox" name="" id="">
                                                                <p>Extra Service 1</p>
                                                            </div>
                                                            <div class="tf-tour-extra-price">
                                                                $120.00
                                                            </div>
                                                        </div>
                                                        <div class="tf-tour-extra-details tf-mrtop-8">
                                                            <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tf-booking-bttns tf-mrtop-30">
                                                    <a class="tf-bttn-normal bttn-primary" href="#">Book This Tour Now</a>
                                                    <a class="tf-bttn-normal bttn-secondary" href="#">Make a Partial Payment</a>
                                                </div>
    
                                                <div class="tf-booking-data-info tf-mrtop-30">
                                                    <div class="tf-form-title">
                                                        <p>Tour Extra</p>
                                                    </div>
                                                    <div class="tf-data-info">
                                                        <div class="data-info-deails tf-flex tf-mrtop-8 tf-flex-space-bttn">
                                                            <p>$15.00 X 1 Nights</p>
                                                            <p>$15.00</p>
                                                        </div>
                                                        <div class="data-info-deails tf-flex tf-mrtop-8 tf-flex-space-bttn">
                                                            <p>Service Fee</p>
                                                            <p>$15.00</p>
                                                        </div>
                                                        <div class="data-info-deails tf-flex tf-mrtop-8 tf-flex-space-bttn">
                                                            <p>Service Fee</p>
                                                            <p>$15.00</p>
                                                        </div>
                                                        <div class="data-info-deails tf-flex tf-mrtop-8 tf-flex-space-bttn">
                                                            <p>Service Fee</p>
                                                            <p>$15.00</p>
                                                        </div>
                                                        <div class="data-info-deails tf-flex tf-mrtop-8 tf-flex-space-bttn">
                                                            <p>Service Fee</p>
                                                            <p>$15.00</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                               </div>
                               <div class="tf-tour-booking-advantages tf-box tf-mrtop-30">
                                    <div class="tf-head-title">
                                        <h3>Why Book With Us?</h3>
                                    </div>
                                    <div class="tf-booking-advantage-items">
                                        <ul class="tf-list">
                                            <li> <i class="fa-solid fa-headphones"></i> Customer care available 24/7 </li>
                                            <li> <i class="fa-solid fa-plane"></i> Free Travel Insurance </li>
                                            <li> <i class="fa-solid fa-sun"></i> Hand - picked Tours & Activities </li>
                                            <li> <i class="fa-solid fa-bolt"></i> No - hassle best price guarantee </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Tour Body details End -->
                </div>
                <!--  Upcoming tours  -->
            </div>
        </div>

        <!-- Tourfic upcomming tours tours -->
        <div class="upcomming-tours">
            <div class="tf-template-container">
                <div class="tf-container-inner">
                    <div class="section-title">
                        <h2 class="tf-title">Upcoming Tour</h2>
                    </div>
                    <div class="tf-upcomming-tours-list-outter tf-mrtop-40 tf-flex tf-flex-gap-24">
                        <div class="tf-post-box-lists">
                            <div class="tf-post-single-box">
                                <div class="tf-image-data">
                                    <img src="/assets/img/upcomming.png" alt="">
                                    <div class="tf-meta-data-price">
                                        From <span>$181</span> 
                                    </div>
                                </div>
                                <div class="tf-meta-info tf-mrtop-30">
                                    <div class="tf-meta-location">
                                        <i class="fa-solid fa-location-dot"></i> Sigiriya, Colombo
                                    </div>
                                    <div class="tf-meta-title">
                                        <h2>Holiday Inn Dubai Al-Maktoum Airport, an IHG hotel</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tf-post-box-lists">
                            <div class="tf-post-single-box">
                                <div class="tf-image-data">
                                    <img src="/assets/img/itinerary.png" alt="">
                                    <div class="tf-meta-data-price">
                                        From <span>$181</span> 
                                    </div>
                                </div>
                                <div class="tf-meta-info tf-mrtop-30">
                                    <div class="tf-meta-location">
                                        <i class="fa-solid fa-location-dot"></i> Sigiriya, Colombo
                                    </div>
                                    <div class="tf-meta-title">
                                        <h2>Holiday Inn Dubai Al-Maktoum Airport, an IHG hotel</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tf-post-box-lists">
                            <div class="tf-post-single-box">
                                <div class="tf-image-data">
                                    <img src="/assets/img/upcomming.png" alt="">
                                    <div class="tf-meta-data-price">
                                        From <span>$181</span> 
                                    </div>
                                </div>
                                <div class="tf-meta-info tf-mrtop-30">
                                    <div class="tf-meta-location">
                                        <i class="fa-solid fa-location-dot"></i> Sigiriya, Colombo
                                    </div>
                                    <div class="tf-meta-title">
                                        <h2>Holiday Inn Dubai Al-Maktoum Airport, an IHG hotel</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
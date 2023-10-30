<div class="tf-template-3">

    <!--Hero section start -->
    <div class="tf-hero-section-wrap" style="background-image: url(./assets/image/tour-hero.png);">
        <div class="tf-container">
            <div class="tf-hero-content">
                <div class="tf-wish-and-share">
                    <a class="tf-wishlist">
                        <img src="./assets/image/Heart.png" alt="">
                    </a>
                    <a class="tf-share">
                        <img src="./assets/image/share.png" alt="">
                    </a>
                </div>
                <div class="tf-hero-bottom-area">
                    <div class="tf-head-title">
                        <h1>Hilton Los Angeles Airport</h1>
                        <div class="tf-title-meta">
                            <img src="./assets/image/map-icon.svg" alt="" class="tf-map-icon">
                            <a href="#tf-map">Los Angeles, California, USA</a>
                        </div>
                    </div>
                    <div class="tf-hero-gallery-videos">
                        <div class="tf-hero-video tf-popup-buttons"
                            style="background-image: url(./assets/image/video.svg);">
                            <a href="#">
                                <img src="./assets/image/video-icon.svg" alt="">
                            </a>
                        </div>
                        <div class="tf-hero-hotel tf-popup-buttons"
                            style="background-image: url(./assets/image/gallery.svg);">
                            <a href="#">
                                <img src="./assets/image/gallery-icon.svg" alt="">
                            </a>
                        </div>
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
                            <p>With the Cruise Ship tour of 2-Days Glencoe, Glenfinnan Viaduct & St Andrews Tour,
                                lorem ipsum dolor sit amet consectetuer adipiscing elit sed diam nonummy nibh
                                euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad
                                minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut
                                aliquip... <a href="#">See more</a></p>
                        </div>
                        <!--features block Start -->
                        <div class="tf-features-block-wrapper">
                            <div class="tf-feature-block">
                                <i class="fa-regular fa-clock"></i>
                                <div class="tf-feature-block-details">
                                    <h5>Duration</h5>
                                    <p>2 days s </p>
                                </div>
                            </div>
                            <div class="tf-feature-block">
                                <i class="fa-regular fa-clock"></i>
                                <div class="tf-feature-block-details">
                                    <h5>Duration</h5>
                                    <p>2 days s </p>
                                </div>
                            </div>
                            <div class="tf-feature-block">
                                <i class="fa-regular fa-clock"></i>
                                <div class="tf-feature-block-details">
                                    <h5>Duration</h5>
                                    <p>2 days s </p>
                                </div>
                            </div>
                            <div class="tf-feature-block">
                                <i class="fa-regular fa-clock"></i>
                                <div class="tf-feature-block-details">
                                    <h5>Duration</h5>
                                    <p>2 days s </p>
                                </div>
                            </div>
                        </div>
                        <!--features block End -->
                        <div class="tf-highlights-wrapper">
                            <div class="tf-highlights-icon">
                                <img src="./assets/image/highlights.svg" alt="Highlights Icon">
                            </div>
                            <div class="ft-highlights-details">
                                <h2 class="tf-section-title">Highlights</h2>
                                <ul>
                                    <li>Includes accommodation, an expert guide, meals, transport and more</li>
                                    <li>Sullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</li>
                                    <li>Meugiat nulla facilisis at vero eros et accumsan et iusto odio.</li>
                                    <li>Meugiat nulla facilisis at vero eros et accumsan et iusto odio.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--Overview End -->

                    <?php if($inc || $exc){ ?>
                    <!-- Include Exclude srart -->
                    <div class="tf-include-exclude-wrapper">
                        <h2 class="tf-section-title">Include/Exclude</h2>
                        <div class="tf-include-exclude-innter">
                            <?php if ( $inc ) { ?>
                            <div class="tf-include">
                                <ul>
                                    <?php
                                    foreach ( $inc as $key => $val ) {
                                    ?>
                                    <li>
                                        <i class="<?php echo !empty($inc_icon) ? esc_attr( $inc_icon ) : 'fa-regular fa-circle-check'; ?>"></i>
                                        <?php echo $val['inc']; ?>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <?php } ?>
                            <?php if ( $exc ) { ?>
                            <div class="tf-exclude">
                                <ul>
                                    <?php
                                    foreach ( $exc as $key => $val ) {
                                    ?>
                                    <li>
                                        <i class="<?php echo !empty($exc_icon) ? esc_attr( $exc_icon ) : 'fa-regular fa-circle-check'; ?>"></i>
                                        <?php echo $val['exc']; ?>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <!-- Include Exclude End -->
                    <?php } ?>


                    <div class="tf-itinerary-wrapper">
                        <div class="section-title">
                            <h2 class="tf-title tf-section-title">Travel Itinerary</h2>
                            <button>Download plan <img src="./assets/image/download.svg" alt=""></button>
                        </div>
                        <div class="tf-itinerary-wrapper">
                            <div class="tf-single-itinerary">
                                <div class="tf-itinerary-title">
                                    <h4>
                                        <span class="tf-itinerary-time">Day 01</span>
                                        <span class="tf-itinerary-title-text">Meet & Greet</span>
                                    </h4>
                                    <i class="fa-solid fa-chevron-down"></i>
                                </div>
                                <div class="tf-itinerary-content-wrap" style="display: none;">
                                    <div class="tf-itinerary-content">
                                        <div class="tf-itinerary-content-details">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eligendi
                                                eius
                                                ducimus delectus ex, voluptatum illo culpa accusantium doloribus
                                                quam
                                                consequatur ut voluptatibus maxime quidem. Beatae, sequi?</p>
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
                                            </ul>
                                        </div>
                                        <div class="tf-itinerary-content-images">
                                            <img src="./assets/image/gallery-image-1.png" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tf-single-itinerary">
                                <div class="tf-itinerary-title">
                                    <h4>
                                        <span class="tf-itinerary-time">Day 01</span>
                                        <span class="tf-itinerary-title-text">Meet & Greet</span>
                                    </h4>
                                    <i class="fa-solid fa-chevron-down"></i>
                                </div>
                                <div class="tf-itinerary-content-wrap" style="display: none;">
                                    <div class="tf-itinerary-content">
                                        <div class="tf-itinerary-content-details">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eligendi
                                                eius
                                                ducimus delectus ex, voluptatum illo culpa accusantium doloribus
                                                quam
                                                consequatur ut voluptatibus maxime quidem. Beatae, sequi?</p>
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
                                            </ul>
                                        </div>
                                        <div class="tf-itinerary-content-images">
                                            <img src="./assets/image/gallery-image-1.png" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tf-single-itinerary">
                                <div class="tf-itinerary-title">
                                    <h4>
                                        <span class="tf-itinerary-time">Day 01</span>
                                        <span class="tf-itinerary-title-text">Meet & Greet</span>
                                    </h4>
                                    <i class="fa-solid fa-chevron-down"></i>
                                </div>
                                <div class="tf-itinerary-content-wrap" style="display: none;">
                                    <div class="tf-itinerary-content">
                                        <div class="tf-itinerary-content-details">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eligendi
                                                eius
                                                ducimus delectus ex, voluptatum illo culpa accusantium doloribus
                                                quam
                                                consequatur ut voluptatibus maxime quidem. Beatae, sequi?</p>
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
                                            </ul>
                                        </div>
                                        <div class="tf-itinerary-content-images">
                                            <img src="./assets/image/gallery-image-1.png" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                (function ($) {
                                    "use strict";
                                    $(document).ready(function () {
                                        $('.tf-itinerary-title').click(function () {
                                            $(this).closest('.tf-single-itinerary').addClass('active')
                                            $(this).siblings().slideDown()
                                            $(this).closest('.tf-single-itinerary').siblings().removeClass('active').find('.tf-itinerary-content-wrap')
                                                .slideUp()
                                        });
                                    });
                                }(jQuery));
                            </script>

                        </div>
                        <!-- Map start -->
                        <div class="tf-itinerary-map">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193595.2528001631!2d-74.14448723354508!3d40.69763123329699!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2sbd!4v1697563394680!5m2!1sen!2sbd"
                                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <!-- Map End -->
                    </div>


                    <div class="tf-tour-wrapper">
                        <h2 class="tf-section-title">Calendar & prices</h2>
                        <div id="tf-tour3-caleandar"></div>
                    </div>

                    <script type="text/javascript" src="./assets/js/caleandar.min.js"></script>
                    <script>
                        var events = [{
                                'Date': new Date(2023, 9, 7),
                                'Title': '$200',
                                'Link': 'https://github.com/joynal05'
                            },
                            {
                                'Date': new Date(2023, 9, 8),
                                'Title': '$300',
                                'Link': 'https://github.com/joynal05'
                            },
                            {
                                'Date': new Date(2023, 9, 9),
                                'Title': '$250',
                                'Link': 'https://github.com/joynal05'
                            }, {
                                'Date': new Date(2023, 9, 10),
                                'Title': '$200',
                                'Link': 'https://github.com/joynal05'
                            },
                            {
                                'Date': new Date(2023, 9, 11),
                                'Title': '$300',
                                'Link': 'https://github.com/joynal05'
                            },
                            {
                                'Date': new Date(2023, 9, 12),
                                'Title': '$250',
                                'Link': 'https://github.com/joynal05'
                            }, {
                                'Date': new Date(2023, 9, 17),
                                'Title': '$200',
                                'Link': 'https://github.com/joynal05'
                            },
                            {
                                'Date': new Date(2023, 9, 18),
                                'Title': '$300',
                                'Link': 'https://github.com/joynal05'
                            },
                            {
                                'Date': new Date(2023, 9, 19),
                                'Title': '$250',
                                'Link': 'https://github.com/joynal05'
                            },
                        ];
                        var settings = {};
                        var element = document.getElementById('tf-tour3-caleandar');
                        caleandar(element, events, settings);



                        (function ($) {
                            "use strict";
                            $(document).ready(function () {
                                $("#tf-tour3-caleandar li.cld-day.currMonth").click(function () {
                                    var link = $(this).find("a").attr("href");
                                    if (link) {
                                        window.open(link, "_blank");
                                    }
                                });
                            });

                        }(jQuery));
                    </script>
                </div>
                <div class="tf-details-right tf-sitebar-widgets">
                    <div class="tf-search-date-wrapper tf-single-widgets">
                        <h2 class="tf-section-title">Available date</h2>
                        <form action="">
                            <label for="tf-search-date" class="tf-booking-date-wrap">
                                <img src="./assets/image/date.svg" alt="">
                                <input type="text" id="tf-search-date" placeholder="Los angeles">
                            </label>
                            <span class="tf-search-date-info">Available date</span>
                            <div class="tf_acrselection-wrap">
                                <div class="tf_acrselection-inner">
                                    <div class="tf_acrselection">
                                        <div class="acr-label">Adults $400 <span>Age 30-80 years</span></div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="adults" id="adults" min="1" value="1">
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
                                    <div class="tf_acrselection">
                                        <div class="acr-label">Children $250 <span>Age 10-20 years</span></div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="children" id="children" min="0" value="0">
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
                                    <div class="tf_acrselection">
                                        <div class="acr-label">Infant $120 <span>Age under 10</span></div>

                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="infant" id="infant" min="1" value="1">
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                    <div class="tf-reviews tf-single-widgets">
                    <?php
                    global $current_user;
                    // Check if user is logged in
                    $is_user_logged_in = $current_user->exists();
                    $post_id           = $post->ID;
                    // Get settings value
                    $tf_ratings_for = tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
                    $tf_settings_base = ! empty ( tfopt( 'r-base' ) ) ? tfopt( 'r-base' ) : 5;
                    if ( $comments ) {
                        $tf_overall_rate        = [];
                        tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
                        tf_get_review_fields( $fields );
                    ?>
                    <h2 class="tf-section-title"><?php _e("Overall reviews", "tourfic"); ?></h2>
                    <div class="tf-review-data-inner">
                        <div class="tf-review-data">
                            <div class="tf-review-data-average">
                                <h2><span>
                                    <?php _e( sprintf( '%.1f', $total_rating ) ); ?>
                                </span>/<?php echo $tf_settings_base; ?></h2>
                            </div>
                            <div class="tf-review-all-info">
                                <p><?php _e("Excellent", "tourfic"); ?> <span><?php _e("Total", "tourfic"); ?> <?php tf_based_on_text( count( $comments ) ); ?></span></p>
                            </div>
                        </div>
                        <div class="tf-review-data-features">
                            <div class="tf-percent-progress">
                            <?php 
                            if ( $tf_overall_rate ) {
                            foreach ( $tf_overall_rate as $key => $value ) {
                            if ( empty( $value ) || ! in_array( $key, $fields ) ) {
                                continue;
                            }
                            $value = tf_average_ratings( $value );
                            ?>
                                <div class="tf-progress-item">                                    
                                    <div class="tf-review-feature-label">
                                        <p class="feature-label"><?php esc_html_e( $key, "tourfic" ); ?></p>
                                        <p class="feature-rating"> <?php echo $value; ?></p>
                                    </div>
                                    <div class="tf-progress-bar">
                                        <span class="percent-progress" style="width: <?php echo tf_average_rating_percent( $value, tfopt( 'r-base' ) ); ?>%"></span>
                                    </div>
                                </div>
                                <?php } } ?>
                                    
                            </div>
                        </div>
                    </div>
                    <a class="tf-all-reviews" href="#"><?php _e("See all reviews", "tourfic"); ?></a>
                    <?php } ?>
                    <button class="tf-review-open button">
                        <?php _e("Leave your review", "tourfic"); ?>
                    </button>
                    <?php
                    // Review moderation notice
                    echo tf_pending_review_notice( $post_id );
                    ?>
                    <?php
                    if ( ! empty( $tf_ratings_for ) ) {
                        if ( $is_user_logged_in ) {
                        if ( in_array( 'li', $tf_ratings_for ) && ! tf_user_has_comments() ) {
                        ?>
                    <div class="tf-review-form-wrapper" action="">
                        <h3><?php _e("Leave your review", "tourfic"); ?></h3>
                        <p><?php _e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
                        <?php tf_review_form(); ?>
                    </div>
                    <?php
                        }
                    } else {
                    if ( in_array( 'lo', $tf_ratings_for ) ) {
                    ?>
                    <div class="tf-review-form-wrapper" action="">
                        <h3><?php _e("Leave your review", "tourfic"); ?></h3>
                        <p><?php _e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
                        <?php tf_review_form(); ?>
                    </div>
                    <?php } } } ?>
                        <script>
                            $(".tf-template-3 .tf-review-open").click(function () {
                                $(".tf-template-3 .tf-review-form-wrapper").slideToggle();
                            });
                        </script>
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


            <?php
            if ( $comments ) { ?>
            <!-- Hotel reviews Srart -->
            <div class="tf-reviews-wrapper tf-section" id="tf-hotel-reviews">         
                <h2 class="tf-section-title"><?php _e("Guest reviews", "tourfic"); ?></h2> 
                <p><?php _e("Total", "tourfic"); ?> <?php tf_based_on_text( count( $comments ) ); ?></p>
                <div class="tf-reviews-slider">
                    <?php
                    foreach ( $comments as $comment ) {
                    // Get rating details
                    $tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
                    if ( $tf_overall_rate == false ) {
                        $tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
                        $tf_overall_rate = tf_average_ratings( $tf_comment_meta );
                    }
                    $base_rate = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
                    $c_rating  = tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );

                    // Comment details
                    $c_avatar      = get_avatar( $comment, '56' );
                    $c_author_name = $comment->comment_author;
                    $c_date        = $comment->comment_date;
                    $c_content     = $comment->comment_content;
                    ?>
                    <div class="tf-reviews-item">
                        <div class="tf-reviews-avater">
                            <?php echo $c_avatar; ?>
                        </div>
                        <div class="tf-reviews-text">
                            <h3><?php echo $c_rating; ?></h3>
                            <span class="tf-reviews-meta"><?php echo $c_author_name; ?>, <?php echo $c_date; ?></span>
                            <p><?php echo $c_content; ?></p>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <!--Content reviews end -->
            <?php } ?>


            <?php if ( $faqs ): ?>
            <!-- Hotel Questions Srart -->
            <div class="tf-questions-wrapper tf-section" id="tf-hotel-faq">
                <h2 class="tf-section-title">
                <?php echo !empty($meta['faq-section-title']) ? esc_html($meta['faq-section-title']) : __( "Faq’s", 'tourfic' ); ?>
                </h2>            
                <div class="tf-questions">
                    
                    <?php 
                    $faqs_itemsPerColumn = ceil(count($faqs) / 2);
                    ?>
                    <div class="tf-questions-col">
                        <?php 
                        for ($i = 0; $i < $faqs_itemsPerColumn; $i++) { ?>
                        <div class="tf-question <?php echo $i==0 ? esc_attr( 'tf-active' ) : ''; ?>">
                            <div class="tf-faq-head">
                                <h3><?php echo esc_html( $faqs[$i]['title'] ); ?>
                                <i class="fa-solid fa-chevron-down"></i></h3>
                            </div>
                            <div class="tf-question-desc" style="<?php echo $i==0 ? esc_attr( 'display: block;' ) : ''; ?>">
                            <?php echo wp_kses_post( $faqs[$i]['desc'] ); ?>
                            </div>
                        </div>
                        <?php } ?>
                        
                    </div>
                    <div class="tf-questions-col">
                        <?php 
                        for ($i = $faqs_itemsPerColumn; $i < count($faqs); $i++) { ?>
                        <div class="tf-question">
                            <div class="tf-faq-head">
                                <h3><?php echo esc_html( $faqs[$i]['title'] ); ?>
                                <i class="fa-solid fa-chevron-down"></i></h3>
                            </div>
                            <div class="tf-question-desc">
                            <?php echo wp_kses_post( $faqs[$i]['desc'] ); ?>
                            </div>
                        </div>
                        <?php } ?>
                        
                    </div>
                </div>
            </div>

            <!-- Hotel Questions end -->
            <?php endif; ?>
            

            <?php
            if($terms_and_conditions){ ?>
            <!-- Hotel Policies Starts -->
            <div class="tf-policies-wrapper tf-section" id="tf-hotel-policies">
                <h2 class="tf-section-title">
                <?php echo !empty($meta['tc-section-title']) ? esc_html($meta['tc-section-title']) : __("Tour Terms & Conditions","tourfic"); ?>
                </h2>
                <div class="tf-policies">
                    <?php echo wpautop( $terms_and_conditions ); ?>
                </div>
            </div>
            <!-- Hotel Policies end -->
            <?php } ?>


            <!-- Hotel PopUp Starts -->
            <div class="tf-popup-wrapper tf-hotel-popup">
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
                    <div class="tf-popup-body tf-scroll-bar">
                        <img data-tags="common-areas" src="./assets/image/gallery-image-1.png" alt=""
                            class="tf-popup-image">
                        <img data-tags="rooms,dining" src="./assets/image/gallery-image-2.png" alt=""
                            class="tf-popup-image">
                        <img data-tags="common-areas" src="./assets/image/gallery-image-3.png" alt=""
                            class="tf-popup-image">
                        <img data-tags="rooms" src="./assets/image/gallery-image-4.png" alt=""
                            class="tf-popup-image">
                        <img data-tags="dining" src="./assets/image/gallery-image-5.png" alt=""
                            class="tf-popup-image">
                        <img data-tags="dining,rooms,common-areas" src="./assets/image/gallery-image-6.png" alt=""
                            class="tf-popup-image">
                        <img data-tags="pool" src="./assets/image/gallery-image-7-pool.jpg" alt=""
                            class="tf-popup-image">
                        <img data-tags="pool,rooms" src="./assets/image/gallery-image-8-pool.jpg" alt=""
                            class="tf-popup-image">
                    </div>
                    <div class="tf-popup-close">
                        <i class="fa-solid fa-xmark"></i>
                    </div>
                </div>
            </div>
            <!-- Hotel PopUp end -->


            <!-- Video PopUp Starts -->
            <div class="tf-popup-wrapper tf-video-popup">
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
                    <div class="tf-popup-body tf-scroll-bar">

                        <div data-tags="common-areas" class="tf-popup-video-item">
                            <a class="tf-tour-video" id="featured-video" data-fancybox="tour-video"
                                href="https://www.youtube.com/embed/BCuH9jTMK58">
                                <img src="./assets/image/video1.jpg" alt="">
                                <i class="fa-regular fa-circle-play"></i>
                            </a>
                        </div>

                        <div data-tags="rooms" class="tf-popup-video-item">
                            <a class="tf-tour-video" id="featured-video" data-fancybox="tour-video"
                                href="https://www.youtube.com/embed/BCuH9jTMK58">
                                <img src="./assets/image/video3.jpg" alt="">
                                <i class="fa-regular fa-circle-play"></i>
                            </a>
                        </div>

                        <div data-tags="dining" class="tf-popup-video-item">
                            <a class="tf-tour-video" id="featured-video" data-fancybox="tour-video"
                                href="https://www.youtube.com/embed/BCuH9jTMK58">
                                <img src="./assets/image/video2.jpg" alt="">
                                <i class="fa-regular fa-circle-play"></i>
                            </a>
                        </div>

                        <div data-tags="pool" class="tf-popup-video-item">
                            <a class="tf-tour-video" id="featured-video" data-fancybox="tour-video"
                                href="https://www.youtube.com/embed/BCuH9jTMK58">
                                <img src="./assets/image/video3.jpg" alt="">
                                <i class="fa-regular fa-circle-play"></i>
                            </a>
                        </div>

                    </div>
                    <div class="tf-popup-close">
                        <i class="fa-solid fa-xmark"></i>
                    </div>
                </div>
            </div>
            <!-- Video PopUp end -->


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
                        <div class="tf-popup-left tf-scroll-bar">
                            <img data-tags="common-areas" src="./assets/image/gallery-image-1.png" alt=""
                                class="tf-popup-image">
                            <img data-tags="rooms,dining" src="./assets/image/gallery-image-2.png" alt=""
                                class="tf-popup-image">
                            <img data-tags="common-areas" src="./assets/image/gallery-image-3.png" alt=""
                                class="tf-popup-image">
                            <img data-tags="rooms" src="./assets/image/gallery-image-4.png" alt=""
                                class="tf-popup-image">
                            <img data-tags="dining" src="./assets/image/gallery-image-5.png" alt=""
                                class="tf-popup-image">
                            <img data-tags="dining,rooms,common-areas" src="./assets/image/gallery-image-6.png"
                                alt="" class="tf-popup-image">
                            <img data-tags="pool" src="./assets/image/gallery-image-7-pool.jpg" alt=""
                                class="tf-popup-image">
                            <img data-tags="pool,rooms" src="./assets/image/gallery-image-8-pool.jpg" alt=""
                                class="tf-popup-image">
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
                            <h4 class="tf-popup-info-title"><i class="fa-solid fa-cookie-bite"></i> Food and drink
                            </h4>
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
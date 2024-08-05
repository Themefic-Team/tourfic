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
                <div class="tf-car-details-column">

                    <div class="tf-car-archive-result">
                        <div class="tf-total-result-bar">
                            <span>Showing 8 of 15 Results</span>
                        </div>

                        <div class="tf-car-result grid-view">
                            <div class="tf-single-car-view">
                                <div class="tf-car-image">
                                    <img src="<?php echo esc_url(TF_ASSETS_APP_URL.'/images/feature-default.jpg'); ?>">
                                </div>
                                <div class="tf-car-details">
                                    <h3>Hundai 354 2024</h3>
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
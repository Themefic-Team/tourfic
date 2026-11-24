<?php
if ( ! file_exists( __DIR__ . '/.tourfic-booking-wishlist-test' ) ) { exit; }
require __DIR__ . '/wp-load.php';
$posts = get_posts( array( 'post_type' => 'tf_apartment', 'posts_per_page' => 1 ) );
$fixture_post = $posts[0]; $GLOBALS['post'] = $fixture_post; setup_postdata( $GLOBALS['post'] );
$meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
$meta['highlights'] = array_fill( 0, 5, array( 'title' => 'Synthetic highlight', 'subtitle' => 'Inert fixture' ) );
update_post_meta( get_the_ID(), 'tf_apartment_opt', $meta );
$enqueue = \Tourfic\Classes\Enqueue::instance();
if ( isset( $_GET['min'] ) && '1' === $_GET['min'] ) { $property = new ReflectionProperty( $enqueue, 'js_min' ); $property->setAccessible( true ); $property->setValue( $enqueue, '.min' ); }
$enqueue->tf_enqueue_scripts( true );
if ( isset( $_GET['baseline'] ) && '1' === $_GET['baseline'] ) {
 wp_deregister_script( 'tf-slick' );
 wp_register_script( 'tf-slick', home_url( '/slick-baseline.min.js' ), array( 'jquery' ), '1.8.1', true );
 wp_enqueue_script( 'tf-slick' );
}
?><!doctype html><html><head><meta charset="utf-8"><title>Tourfic slider validation</title><?php wp_head(); ?></head><body>
<h1>Tourfic slider validation</h1>
<div id="forged" class="tf-apt-highlights-slider" data-slick='{"dotsClass":"untrusted-marker","arrows":true,"prevArrow":"<button class=untrusted-arrow>Inert</button>"}'><div>A</div><div>B</div><div>C</div><div>D</div></div>
<?php $GLOBALS['post'] = $fixture_post; \Tourfic\App\Templates\Components\Shared\Single\Highlights::render( array( 'highlights_style' => 'style2' ), 'fixture' ); ?>
<div id="dependency" data-slick='{"dotsClass":"untrusted-marker","prevArrow":"<button class=untrusted-arrow>Inert</button>"}'><div>A</div><div>B</div><div>C</div><div>D</div></div>
<?php wp_footer(); ?>
<pre id="fixture-results">Waiting for slider checks</pre>
<script>
jQuery(function($) {
 $('#dependency').data('slick', { dotsClass: 'untrusted-marker', prevArrow: '<button class="untrusted-arrow">Inert</button>' });
 $('#dependency').slick({ dots: true, arrows: true, slidesToShow: 1 });
 const dynamic = document.createElement('div'); dynamic.id = 'dynamic'; dynamic.className = 'tf-apt-highlights-slider';
 for (let i = 0; i < 5; i++) { const slide = document.createElement('div'); slide.className = 'tf-apt-highlight'; slide.textContent = 'Dynamic trusted highlight'; dynamic.appendChild(slide); }
 document.body.appendChild(dynamic); window.tourfic_init_highlight_sliders([dynamic]);
 const results = { forgedUntouched: !document.getElementById('forged').classList.contains('slick-initialized'),
   renderedInitialized: document.querySelector('.tf-apt-highlights-wrapper .slick-initialized') !== null,
   dynamicInitialized: dynamic.classList.contains('slick-initialized'),
   untrustedMarkupAbsent: document.querySelector('.untrusted-arrow,.untrusted-marker') === null,
   normalNavigationPresent: document.querySelector('#dependency .slick-prev') !== null,
   slidesToShow: $('#dependency').slick('getSlick').options.slidesToShow };
 document.getElementById('fixture-results').textContent = JSON.stringify(results);
 document.title = 'Tourfic slider fixture ready';
});
</script>
</body></html>

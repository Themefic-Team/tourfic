<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Tourfic
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="tf_comments-area">
	<?php
	$comments = get_comments( array( 'post_id' => get_the_ID() ) );

	$tf_overall_rate = array();
	$tf_extr_html = '';

	foreach ( $comments as $comment ) {

	    $tf_comment_meta = get_comment_meta( $comment->comment_ID, 'tf_comment_meta', true );

	    if( $tf_comment_meta ) {
	    	foreach ($tf_comment_meta as $key => $value) {
	    		$tf_overall_rate[$key][] = $value ? $value : "5";
	    	}
	    } else {
	    	$tf_overall_rate['review'][] = "5";
	    	$tf_overall_rate['sleep'][] = "5";
	    	$tf_overall_rate['location'][] = "5";
	    	$tf_overall_rate['services'][] = "5";
	    	$tf_overall_rate['cleanliness'][] = "5";
	    	$tf_overall_rate['rooms'][] = "5";
	    }
	}

    if( $tf_overall_rate ) {
    	$tf_extr_html .= '<div class="tf_comment-metas">';
    		foreach ($tf_overall_rate as $key => $value) {
    			if ( $key == "review" ) {
    				continue;
    			}
    			$tf_extr_html .= '<div class="comment-meta">';
					$tf_extr_html .= '<label class="tf_comment_meta-key">'.$key.'</label>';
					$tf_extr_html .= '<div class="tf_comment_meta-percent"><div class="percent-progress" data-width="'.tourfic_avg_rating_percent( tourfic_avg_ratings($value) ).'"></div></div>';
					$tf_extr_html .= '<div class="tf_comment_meta-ratings">'.tourfic_avg_ratings($value).'/5</div>';
				$tf_extr_html .= '</div>';
    		}
    	$tf_extr_html .= '</div>';
    }

    ?>
	<?php if ( have_comments() ) : ?>
		<div class="tf-comments-count-wrapper">

			<div class="tf-overall-ratings">
				<div class="overall-rate"><?php _e( tourfic_avg_ratings($tf_overall_rate['review']) ); ?>/5</div>
				<div class="overall-rate-stars">
					<div class="star-ratings">
					  	<div class="fill-ratings" style="width: <?php _e( tourfic_avg_rating_percent(tourfic_avg_ratings($tf_overall_rate['review'])) ); ?>%" data-width="<?php _e( tourfic_avg_rating_percent(tourfic_avg_ratings($tf_overall_rate['review'])) ); ?>">
					  	  	<span>★★★★★</span>
					  	</div>
					  	<div class="empty-ratings">
					  	  	<span>★★★★★</span>
					  	</div>
					</div>
				</div>
				<div class="based-on-title">
					<?php
					$comments_title = apply_filters(
						'tf_comment_form_title',
						sprintf( // WPCS: XSS OK.
							/* translators: 1: number of comments */
							esc_html( _nx( 'Based on %1$s review', 'Based on %1$s reviews', get_comments_number(), 'comments title', 'tourfic' ) ),
							number_format_i18n( get_comments_number() )
						)
					);

					echo esc_html( $comments_title );
					?>
				</div>
			</div>

			<div class="tf-extra-ratings">
				<?php _e( $tf_extr_html ); ?>
			</div>

		</div>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
		<nav id="comment-nav-above" class="navigation comment-navigation" aria-label="<?php esc_attr_e( 'Comments Navigation', 'tourfic' ); ?>">
			<h3 class="screen-reader-text"><?php echo esc_html( astra_default_strings( 'string-comment-navigation-next', false ) ); ?></h3>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( astra_default_strings( 'string-comment-navigation-previous', false ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( astra_default_strings( 'string-comment-navigation-next', false ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-above -->
		<?php endif; ?>

		<ol class="tf-comment-list">
			<?php
			wp_list_comments(
				array(
					//'callback' => 'tf_comment_callback',
					'style'    => 'ol',
					'type'      => 'comment',
					'max_depth' => 1,
				)
			);
			?>
		</ol><!-- .ast-comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
		<nav id="comment-nav-below" class="navigation comment-navigation" aria-label="<?php esc_attr_e( 'Comments Navigation', 'tourfic' ); ?>">
			<h3 class="screen-reader-text"><?php echo esc_html( astra_default_strings( 'string-comment-navigation-next', false ) ); ?></h3>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( astra_default_strings( 'string-comment-navigation-previous', false ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( astra_default_strings( 'string-comment-navigation-next', false ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-below -->
		<?php endif; ?>

	<?php endif; ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
		?>
		<p class="no-comments"><?php echo esc_html( astra_default_strings( 'string-comment-closed', false ) ); ?></p>
	<?php endif; ?>

	<?php tourfic_get_review_form(); ?>

</div><!-- #comments -->

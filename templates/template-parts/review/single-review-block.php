<div class="tf_item_review_block">
	<div class="reviewFloater reviewFloaterBadge__container">
		<div class="sr-review-score">
			<a class="sr-review-score__link" href="<?php
			echo get_the_permalink() . '?' . $dest_slug_param . '&adults=' . $adults . '&children=' . $children . '&room=' . $room . '&check-in-date=' . $check_in_date . '&check-out-date=' . $check_out_date; ?>"
			   target="_blank">
				<div class="bui-review-score c-score bui-review-score--end">
					<div class="bui-review-score__badge"> <?php
						_e( tf_average_ratings( array_values( $tf_overall_rate ?? [] ) ) ); ?> </div>
					<div class="bui-review-score__content">
						<div class="bui-review-score__title"> <?php
							esc_html_e( 'Customer Rating', TFD ); ?> </div>
						<div class="bui-review-score__text">
							<?php
							$comments_title = apply_filters(
								'tf_comment_form_title',
								sprintf(  // WPCS: XSS OK.
								/* translators: 1: number of comments */
									esc_html( _nx( 'Based on %1$s review', 'Based on %1$s reviews', count( $comments ), 'comments title', TFD ) ),
									number_format_i18n( count( $comments ) )
								)
							);
							echo esc_html( $comments_title );
							?>
						</div>
					</div>
				</div>
			</a>
		</div>
	</div>
</div>
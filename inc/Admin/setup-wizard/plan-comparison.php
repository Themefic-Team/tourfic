<?php
/**
 * Tourfic setup wizard plan comparison.
 *
 * @var array $features Feature labels paired with Remix Icon classes.
 * @var bool $is_template_import Whether Toolkit owns completion.
 */

defined( 'ABSPATH' ) || exit;
?>
<?php if ( $is_template_import ) : ?>
	<div class="tourfic-setup-comparison-shell" data-travelfic-import-interstitial hidden>
		<?php $this->render_setup_header(); ?>
		<div class="tourfic-setup-comparison tourfic-setup-comparison--import">
<?php else : ?>
	<div class="tf-setup-step-container tf-setup-step-7 tourfic-setup-comparison" data-step="7">
		<div class="back-to-dashboard">
			<button type="button" class="tf-back-btn tf-setup-prev-btn">
				<i class="ri-arrow-left-line" aria-hidden="true"></i>
				<span><?php esc_html_e( 'Back', 'tourfic' ); ?></span>
			</button>
		</div>
<?php endif; ?>
	<section class="tf-setup-step-layout" aria-labelledby="tourfic-setup-comparison-title">
		<?php $this->tf_setup_wizard_steps_header( 4 ); ?>
		<h1 class="tf-setup-step-title" id="tourfic-setup-comparison-title" tabindex="-1">
			<?php esc_html_e( "Hang tight! You've only scratched the surface", 'tourfic' ); ?>
		</h1>
		<p class="tf-setup-step-desc">
			<?php esc_html_e( 'Start growing your business today! Save time, delight customers, and increase conversions with features designed to make managing your business effortless', 'tourfic' ); ?>
		</p>
		<div class="tourfic-setup-comparison-card">
			<table class="tourfic-setup-comparison-table">
				<caption class="screen-reader-text"><?php esc_html_e( 'Compare Tourfic Lite and Tourfic Master', 'tourfic' ); ?></caption>
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Features', 'tourfic' ); ?></th>
						<th scope="col">
							<?php esc_html_e( 'Tourfic Lite', 'tourfic' ); ?>
							<span class="tourfic-setup-plan-current"><?php esc_html_e( 'Currently', 'tourfic' ); ?></span>
						</th>
						<th scope="col">
							<?php esc_html_e( 'Tourfic Master', 'tourfic' ); ?>
							<span class="tourfic-setup-plan-badge"><?php esc_html_e( 'Recommended', 'tourfic' ); ?></span>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $features as list( $label, $icon ) ) : ?>
						<tr>
							<th scope="row">
								<span class="tourfic-setup-feature">
									<i class="tourfic-setup-feature-icon <?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
									<span><?php echo esc_html( $label ); ?></span>
								</span>
							</th>
							<td>
								<i class="tourfic-setup-plan-mark ri-close-line" aria-hidden="true"></i>
								<span class="screen-reader-text"><?php esc_html_e( 'Not included', 'tourfic' ); ?></span>
							</td>
							<td>
								<i class="tourfic-setup-plan-mark ri-check-line" aria-hidden="true"></i>
								<span class="screen-reader-text"><?php esc_html_e( 'Included', 'tourfic' ); ?></span>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<div class="tourfic-setup-comparison-cta">
				<a class="tf-link-skip-btn tourfic-setup-learn-more" href="https://tourfic.com/highlights/" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Learn More', 'tourfic' ); ?>
					<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'tourfic' ); ?></span>
					<i class="ri-arrow-right-up-line" aria-hidden="true"></i>
				</a>
			</div>
		</div>
		<div class="tf-setup-action-btn-wrapper">
			<div class="tf-setup-action-btn-next">
				<button type="button" class="tf-link-skip-btn tourfic-setup-comparison-continue" data-travelfic-import-continue>
					<?php esc_html_e( 'Skip', 'tourfic' ); ?>
					<i class="ri-arrow-right-line" aria-hidden="true"></i>
				</button>
				<button type="button" class="tf-quick-setup-btn tourfic-setup-comparison-continue" data-travelfic-import-continue>
					<?php esc_html_e( 'Next', 'tourfic' ); ?>
					<i class="ri-arrow-right-line" aria-hidden="true"></i>
				</button>
			</div>
		</div>
	</section>
</div>
<?php if ( $is_template_import ) : ?>
	</div>
<?php endif; ?>

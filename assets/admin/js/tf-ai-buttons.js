/**
 * Tourfic Free – AI "Create With AI" buttons on post list & classic editor.
 * Clicking any button opens the upsell modal (#tf-ai-upsell-overlay).
 */
(function ($) {
	'use strict';

	if (typeof tfAiButtons === 'undefined') return;

	var postType = tfAiButtons.post_type || '';
	var buttonText = tfAiButtons.button_text || 'Create With AI';
	var screen = tfAiButtons.screen || '';

	var sparkSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20" fill="none">' +
		'<g clip-path="url(#clip0_tf_free)">' +
		'<path d="M16.667 1.66655V4.99988M18.3337 3.33321H15.0004M9.1812 2.34488C9.21691 2.15372 9.31835 1.98106 9.46795 1.85681C9.61755 1.73256 9.8059 1.66455 10.0004 1.66455C10.1948 1.66455 10.3832 1.73256 10.5328 1.85681C10.6824 1.98106 10.7838 2.15372 10.8195 2.34488L11.6954 6.97655C11.7576 7.30584 11.9176 7.60873 12.1546 7.84569C12.3915 8.08265 12.6944 8.24267 13.0237 8.30488L17.6554 9.18071C17.8465 9.21642 18.0192 9.31786 18.1434 9.46746C18.2677 9.61706 18.3357 9.80541 18.3357 9.99988C18.3357 10.1943 18.2677 10.3827 18.1434 10.5323C18.0192 10.6819 17.8465 10.7833 17.6554 10.819L13.0237 11.6949C12.6944 11.7571 12.3915 11.9171 12.1546 12.1541C11.9176 12.391 11.7576 12.6939 11.6954 13.0232L10.8195 17.6549C10.7838 17.846 10.6824 18.0187 10.5328 18.1429C10.3832 18.2672 10.1948 18.3352 10.0004 18.3352C9.8059 18.3352 9.61755 18.2672 9.46795 18.1429C9.31835 18.0187 9.21691 17.846 9.1812 17.6549L8.30537 13.0232C8.24316 12.6939 8.08314 12.391 7.84618 12.1541C7.60922 11.9171 7.30632 11.7571 6.97703 11.6949L2.34537 10.819C2.1542 10.7833 1.98155 10.6819 1.8573 10.5323C1.73305 10.3827 1.66504 10.1943 1.66504 9.99988C1.66504 9.80541 1.73305 9.61706 1.8573 9.46746C1.98155 9.31786 2.1542 9.21642 2.34537 9.18071L6.97703 8.30488C7.30632 8.24267 7.60922 8.08265 7.84618 7.84569C8.08314 7.60873 8.24316 7.30584 8.30537 6.97655L9.1812 2.34488ZM5.00037 16.6665C5.00037 17.587 4.25418 18.3332 3.3337 18.3332C2.41323 18.3332 1.66703 17.587 1.66703 16.6665C1.66703 15.7461 2.41323 14.9999 3.3337 14.9999C4.25418 14.9999 5.00037 15.7461 5.00037 16.6665Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>' +
		'</g>' +
		'<defs><clipPath id="clip0_tf_free"><rect width="20" height="20" fill="white"/></clipPath></defs>' +
		'</svg>';

	function openUpsell(e) {
		e.preventDefault();
		var overlay = document.getElementById('tf-ai-upsell-overlay');
		if (overlay) {
			overlay.classList.add('active');
			document.body.style.overflow = 'hidden';
		}
	}

	// Post list page (edit.php)
	if (screen === 'edit') {
		var btn = '<a href="#" class="tf-generate-ai-pro-btn page-title-action" id="tf-open-ai-upsell">' +
			sparkSvg + buttonText + '</a>';

		var $addNew = $(".wrap a.page-title-action").first();
		if ($addNew.length) {
			$addNew.after(btn);
		}

		$(document).on('click', '#tf-open-ai-upsell', openUpsell);
	}

	// Classic editor (post.php / post-new.php)
	if (screen === 'post') {
		var btn = '<a href="#" class="tf-generate-ai-pro-btn page-title-action" id="tf-classic-open-ai-upsell">' +
			sparkSvg + buttonText + '</a>';

		var $addNew = $(".wrap .page-title-action").first();
		if ($addNew.length) {
			$addNew.after(btn);
		} else {
			$(".wrap h1, .wrap .wp-heading-inline").first().after(btn);
		}

		$(document).on('click', '#tf-classic-open-ai-upsell', openUpsell);
	}

})(jQuery);

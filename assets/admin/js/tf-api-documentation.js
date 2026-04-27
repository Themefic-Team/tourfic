jQuery(function($) {
	const config = window.tfApiDocs || {};
	const i18n = config.i18n || {};
	const $list = $('#tf-api-keys-container');

	function t(key, fallback) {
		return Object.prototype.hasOwnProperty.call(i18n, key) ? i18n[key] : fallback;
	}

	function esc(value) {
		return $('<div />').text(value == null ? '' : String(value)).html();
	}

	function renderKeys(keys) {
		if (!Array.isArray(keys) || !keys.length) {
			$list.html('<p class="description">' + esc(t('noApiKeys', 'No API keys found for this user.')) + '</p>');
			return;
		}

		let cards = keys.map(function(key) {
			const permissions = Array.isArray(key.permissions) ? key.permissions.join(', ') : '';
			const revokeDisabled = key.status !== 'active' ? 'disabled' : '';
			return '<article class="tf-api-key-item">' +
				'<div class="tf-api-key-item__head">' +
					'<h4 class="tf-api-key-item__title">' + esc(key.name || t('untitledKey', 'Untitled Key')) + '</h4>' +
					'<span class="tf-api-key-item__status tf-api-key-item__status-' + esc((key.status || '').toLowerCase()) + '">' + esc(key.status || t('unknown', 'unknown')) + '</span>' +
				'</div>' +
				'<div class="tf-api-key-item__meta">' +
					'<p><strong>' + esc(t('apiKey', 'API Key:')) + '</strong> <code>' + esc(key.api_key_preview) + '</code></p>' +
					'<p><strong>' + esc(t('permissions', 'Permissions:')) + '</strong> ' + esc(permissions || t('none', 'None')) + '</p>' +
					'<p><strong>' + esc(t('lastUsed', 'Last Used:')) + '</strong> ' + esc(key.last_used || t('never', 'Never')) + '</p>' +
					'<p><strong>' + esc(t('created', 'Created:')) + '</strong> ' + esc(key.created_at || t('unknownDate', 'Unknown')) + '</p>' +
				'</div>' +
				'<div class="tf-api-key-item__actions">' +
					'<button type="button" class="button tf-revoke-api-key" data-key-id="' + esc(key.id) + '" ' + revokeDisabled + '>' + esc(t('revoke', 'Revoke')) + '</button>' +
				'</div>' +
			'</article>';
		}).join('');

		$list.html('<div class="tf-api-key-list">' + cards + '</div>');
	}

	function loadKeys() {
		$.post(config.ajaxUrl, {
			action: 'tf_get_api_keys',
			nonce: config.nonce
		}).done(function(response) {
			if (response && response.success) {
				renderKeys(response.data || []);
			}
		});
	}

	$('#tf-generate-api-key-form').on('submit', function(event) {
		event.preventDefault();

		const data = $(this).serializeArray();
		data.push({ name: 'action', value: 'tf_generate_api_key' });
		data.push({ name: 'nonce', value: config.nonce });

		$.post(config.ajaxUrl, $.param(data)).done(function(response) {
			if (!response) {
				return;
			}

			if (!response.success) {
				window.alert(response.data || t('unableGenerateKey', 'Unable to generate API key.'));
				return;
			}

			$('#tf-generate-api-key-form')[0].reset();
			loadKeys();
		});
	});

	$(document).on('click', '.tf-revoke-api-key', function() {
		const keyId = $(this).data('key-id');
		if (!keyId) {
			return;
		}

		if (!window.confirm(t('confirmRevoke', 'Revoke this API key?'))) {
			return;
		}

		$.post(config.ajaxUrl, {
			action: 'tf_revoke_api_key',
			nonce: config.nonce,
			key_id: keyId
		}).done(function(response) {
			if (!response || !response.success) {
				window.alert((response && response.data) || t('unableRevokeKey', 'Unable to revoke API key.'));
				return;
			}

			loadKeys();
		});
	});

	$(document).on('click', '.tf-api-copy-btn', function() {
		const $btn = $(this);
		const url = $btn.data('url');

		if (!url) {
			return;
		}

		// Use modern clipboard API if available, fallback to older method
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(url).then(function() {
				$btn.addClass('copied').text(t('copied', 'Copied!'));
				setTimeout(function() {
					$btn.removeClass('copied').text(t('copy', 'Copy'));
				}, 2000);
			}).catch(function() {
				window.alert(t('copyFailed', 'Failed to copy URL to clipboard.'));
			});
		} else {
			// Fallback for older browsers
			const $temp = $('<textarea />').text(url).appendTo('body');
			$temp.select();
			try {
				document.execCommand('copy');
				$btn.addClass('copied').text(t('copied', 'Copied!'));
				setTimeout(function() {
					$btn.removeClass('copied').text(t('copy', 'Copy'));
				}, 2000);
			} catch(err) {
				window.alert(t('copyFailed', 'Failed to copy URL to clipboard.'));
			}
			$temp.remove();
		}
	});

	loadKeys();
});
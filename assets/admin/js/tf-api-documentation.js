jQuery(function($) {
	const config = window.tfApiDocs || {};
	const $list = $('#tf-api-keys-container');

	function esc(value) {
		return $('<div />').text(value == null ? '' : String(value)).html();
	}

	function renderKeys(keys) {
		if (!Array.isArray(keys) || !keys.length) {
			$list.html('<p class="description">No API keys found for this user.</p>');
			return;
		}

		let cards = keys.map(function(key) {
			const permissions = Array.isArray(key.permissions) ? key.permissions.join(', ') : '';
			const revokeDisabled = key.status !== 'active' ? 'disabled' : '';
			return '<article class="tf-api-key-item">' +
				'<div class="tf-api-key-item__head">' +
					'<h4 class="tf-api-key-item__title">' + esc(key.name || 'Untitled Key') + '</h4>' +
					'<span class="tf-api-key-item__status tf-api-key-item__status-' + esc((key.status || '').toLowerCase()) + '">' + esc(key.status || 'unknown') + '</span>' +
				'</div>' +
				'<div class="tf-api-key-item__meta">' +
					'<p><strong>API Key:</strong> <code>' + esc(key.api_key_preview) + '</code></p>' +
					'<p><strong>Permissions:</strong> ' + esc(permissions || 'None') + '</p>' +
					'<p><strong>Last Used:</strong> ' + esc(key.last_used || 'Never') + '</p>' +
					'<p><strong>Created:</strong> ' + esc(key.created_at || 'Unknown') + '</p>' +
				'</div>' +
				'<div class="tf-api-key-item__actions">' +
					'<button type="button" class="button tf-revoke-api-key" data-key-id="' + esc(key.id) + '" ' + revokeDisabled + '>Revoke</button>' +
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
				window.alert(response.data || 'Unable to generate API key.');
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

		if (!window.confirm('Revoke this API key?')) {
			return;
		}

		$.post(config.ajaxUrl, {
			action: 'tf_revoke_api_key',
			nonce: config.nonce,
			key_id: keyId
		}).done(function(response) {
			if (!response || !response.success) {
				window.alert((response && response.data) || 'Unable to revoke API key.');
				return;
			}

			loadKeys();
		});
	});

	loadKeys();
});
jQuery(function($) {
	const config = window.tfApiDocs || {};
	const $list = $('#tf-api-keys-container');
	const $result = $('#tf-api-generated-credentials');

	function esc(value) {
		return $('<div />').text(value == null ? '' : String(value)).html();
	}

	function renderKeys(keys) {
		if (!Array.isArray(keys) || !keys.length) {
			$list.html('<p class="description">No API keys found for this user.</p>');
			return;
		}

		let rows = keys.map(function(key) {
			const permissions = Array.isArray(key.permissions) ? key.permissions.join(', ') : '';
			const revokeDisabled = key.status !== 'active' ? 'disabled' : '';
			return '<tr>' +
				'<td>' + esc(key.name) + '</td>' +
				'<td><code>' + esc(key.api_key_preview) + '</code></td>' +
				'<td>' + esc(permissions) + '</td>' +
				'<td>' + esc(key.status) + '</td>' +
				'<td>' + esc(key.last_used || 'Never') + '</td>' +
				'<td>' + esc(key.created_at || '') + '</td>' +
				'<td><button type="button" class="button tf-revoke-api-key" data-key-id="' + esc(key.id) + '" ' + revokeDisabled + '>Revoke</button></td>' +
			'</tr>';
		}).join('');

		$list.html(
			'<table class="widefat striped tf-api-table tf-api-keys-table">' +
				'<thead><tr><th>Name</th><th>API Key</th><th>Permissions</th><th>Status</th><th>Last Used</th><th>Created</th><th>Action</th></tr></thead>' +
				'<tbody>' + rows + '</tbody>' +
			'</table>'
		);
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

			const key = response.data || {};
			$result.html(
				'<div class="notice notice-success inline"><p><strong>API Key:</strong> <code>' + esc(key.api_key) + '</code><br><strong>API Secret:</strong> <code>' + esc(key.api_secret) + '</code><br>Store the secret now. It will not be shown again.</p></div>'
			).show();

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
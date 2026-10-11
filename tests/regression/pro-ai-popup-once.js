/**
 * Exercise the PHP-rendered AI popup script without a live WordPress site.
 * Run: node tests/regression/pro-ai-popup-once.js
 */
const assert = require('node:assert/strict');
const path = require('node:path');
const { spawnSync } = require('node:child_process');
const vm = require('node:vm');

const proRoot = path.resolve(__dirname, '../../../tourfic-pro');
const source = process.env.TOURFIC_AI_POPUP_SOURCE || path.join(proRoot, 'inc/functions/function_ai_pro_tour.php');
const renderer = `
define('ABSPATH', '/unused/');
define('TF_PRO_INC_PATH', $argv[2] . '/inc/');
define('TOURFIC_PRO_SETTINGS_MENU_SLUG', 'tourfic_settings');
function add_action(...$args) {}
function is_tf_pro() { return $GLOBALS['license']; }
function admin_url($url = '') { return 'https://example.test/wp-admin/' . $url; }
function plugins_url($url = '') { return 'https://example.test/wp-content/plugins/' . $url; }
function rest_url($url) { return 'https://example.test/wp-json/' . $url; }
function wp_create_nonce($action) { return 'test-' . $action; }
function get_current_user_id() { return 42; }
function get_user_meta(...$args) { return $GLOBALS['shown'] ? '1' : ''; }
function esc_html__($text, $domain) { return $text; }
function esc_html_e($text, $domain) { echo $text; }
function esc_html($text) { return $text; }
function esc_url($url) { return $url; }
function wp_json_encode($value) { return json_encode($value); }
$GLOBALS['license'] = '1' === $argv[3];
$GLOBALS['shown'] = '1' === $argv[4];
$GLOBALS['current_screen'] = (object) array('post_type' => $argv[5]);
require $argv[1];
TF_AI_Pro_Tour_Generator::get_instance()->tf_add_ai_pro_button();
`;

function visit({ url = 'https://example.test/wp-admin/edit.php?post_type=tf_tours', shown = false,
	license = true, storage = new Map(), storageBlocked = false, postType = 'tf_tours' } = {}) {
	const output = spawnSync('php', ['-r', renderer, source, proRoot, license ? '1' : '0', shown ? '1' : '0', postType], { encoding: 'utf8' });
	assert.equal(output.status, 0, output.stderr);
	const script = output.stdout.match(/<script type="text\/javascript">([\s\S]*?)<\/script>/);
	assert.ok(script, 'The real PHP method must render its popup script.');
	const handlers = new Map();
	const timers = [];
	const posts = [];
	const messages = new Map();
	const document = {};
	let opens = 0;
	let active = false;
	let iframeSrc = '';
	let persistedShown = shown;
	const historyState = { preserved: true };
	const window = {
		location: new URL(url),
		addEventListener: (type, callback) => messages.set(type, callback),
		history: {
			state: historyState,
			replaceState(state, title, nextUrl) {
				assert.equal(state, historyState, 'Keep unrelated history state.');
				window.location = new URL(nextUrl);
			},
		},
	};
	function jquery(selector) {
		const element = {
			length: selector === '.tf-generate-ai-btn' ? 0 : 1,
			ready: (callback) => callback(jquery),
			first: () => element,
			after: () => element,
			on(type, selectorOrCallback, callback) {
				if (callback) handlers.set(type + ':' + selectorOrCallback, callback);
				return element;
			},
			trigger(type) {
				handlers.get(type + ':' + selector)?.({ preventDefault() {} });
				return element;
			},
			attr(name, value) {
				if (value === undefined) return iframeSrc;
				iframeSrc = value;
				return element;
			},
			data: () => 'https://example.test/ai-client/index.html',
			addClass() { active = true; opens++; return element; },
			removeClass() { active = false; return element; },
			hasClass: () => active,
			css: () => element,
			append: () => element,
		};
		return element;
	}
	jquery.post = (url, payload) => {
		posts.push(payload);
		if (payload.action === 'tf_mark_ai_pro_modal_shown') persistedShown = true;
	};
	const browserStorage = {
		getItem(key) { if (storageBlocked) throw Error('Storage blocked'); return storage.get(key) || null; },
		setItem(key, value) { if (storageBlocked) throw Error('Storage blocked'); storage.set(key, value); },
	};
	vm.runInNewContext(script[1], {
		jQuery: jquery, document, window, URL, URLSearchParams,
		localStorage: browserStorage, sessionStorage: { removeItem() {} },
		ajaxurl: 'https://example.test/wp-admin/admin-ajax.php',
		setTimeout: (callback) => timers.push(callback),
	}, { timeout: 1000 });
	const scheduled = timers.length;
	timers.forEach((callback) => callback());
	return {
		get opens() { return opens; }, get shown() { return persistedShown; },
		get active() { return active; }, url: window.location, scheduled, posts,
		click: () => jquery('#tf-open-ai-pro-modal').trigger('click'),
		close: () => messages.get('message')({ data: { type: 'tf-ai-pro-close' } }),
	};
}

for (const postType of ['tf_tours', 'tf_hotel', 'tf_apartment', 'tf_carrental']) {
	const storage = new Map();
	const first = visit({ postType, storage });
	assert.equal(first.opens, 1, `${postType}: first visit opens once.`);
	assert.equal(first.scheduled, 1);
	assert.equal(first.shown, true, 'Persist the shown marker through the existing AJAX contract.');
	first.close();
	assert.equal(first.active, false, 'Iframe close dismisses the popup.');
	const repeat = visit({ postType, storage, shown: first.shown });
	assert.equal(repeat.opens, 0, `${postType}: repeat visits stay closed.`);
	repeat.click();
	assert.equal(repeat.opens, 1, 'The explicit AI button still opens the popup.');
}

const legacyUrl = 'https://example.test/wp-admin/edit.php?post_type=tf_tours&openAI=1&paged=2#keep';
const legacyRepeat = visit({ url: legacyUrl, shown: true });
assert.equal(legacyRepeat.opens, 0, 'Legacy openAI must not override persistent first-visit state.');
assert.equal(legacyRepeat.url.searchParams.has('openAI'), false, 'Consume the legacy flag.');
assert.equal(legacyRepeat.url.searchParams.get('paged'), '2');
assert.equal(legacyRepeat.url.hash, '#keep');
assert.equal(visit({ url: legacyUrl }).opens, 1, 'Legacy first visit still opens once.');

for (const shown of [false, true]) {
	for (const url of [
		'https://example.test/wp-admin/edit.php?post_type=tf_tours&open_ai_pro=1',
		legacyUrl.replace('#keep', '&open_ai_pro=1#keep'),
	]) {
		const explicit = visit({ shown, url });
		assert.equal(explicit.opens, 1, 'Explicit submenu access opens exactly once, including on first visit.');
		assert.equal(explicit.scheduled, 1, 'Opening flags must share one opening timer.');
		assert.equal(explicit.url.searchParams.has('open_ai_pro'), false);
		assert.equal(visit({ shown: explicit.shown, url: explicit.url.href }).opens, 0, 'Refreshing explicit access stays closed.');
	}
}

const localOnly = visit({ storage: new Map([['tf_ai_pro_modal_shown', 'true']]) });
assert.equal(localOnly.opens, 0, 'A browser marker suppresses automatic reopening.');
assert.equal(localOnly.shown, true, 'Sync the browser marker to existing user metadata.');
assert.equal(visit({ shown: true, storageBlocked: true }).opens, 0, 'Server metadata works with blocked browser storage.');
assert.equal(visit({ shown: false, storageBlocked: true }).opens, 1, 'First visit works with blocked browser storage.');
const inactive = visit({ license: false, url: legacyUrl.replace('#keep', '&open_ai_pro=1#keep') });
assert.equal(inactive.opens, 0, 'Inactive licenses never automatically open the generator.');
assert.equal(inactive.shown, false, 'Do not mark an unseen generator as shown.');
assert.equal(inactive.url.searchParams.has('openAI'), false);
assert.equal(inactive.url.searchParams.has('open_ai_pro'), false);

console.log('PASS: AI popup first visit, persistence, URL consumption, single timer, manual reopening, license, and storage fallback.');

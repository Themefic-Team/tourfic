/** Exercise the real admin event handlers and both shipped bundles without a browser/network. */
const fs = require('fs');
const path = require('path');
const vm = require('vm');

const root = process.argv[2] || path.resolve(__dirname, '../../../tourfic-vendor');
const actions = [
    ['tf_vendor_bulk', 'click', '.vendor-actions input#submit'],
    ['tf_vendor_activation', 'click', '.vendor-status-switcher'],
    ['tf_vendor_registration', 'click', '#tf-vendor-register .tf-save-user'],
    ['tf_vendor_update', 'click', '#tf-vendor-update .tf-save-user'],
    ['tf_vendor_payouts', 'click', '.tf-payout-pending'],
    ['tf_vendor_payouts_decline', 'click', '.tf-payout-decline'],
    ['tf_vendor_month_reports', 'click', '.tf-vendor-submission'],
    ['tf_vendor_payouts_amount_preview', 'change', '.tf-vendor-unique-id'],
    ['tf_vendor_payouts_create', 'click', '.tf-payout-submit-button'],
    ['tf_vendor_payouts_update', 'click', '.tf-payout-edit'],
    ['tf_vendor_payouts_info_update', 'click', '.tf-payout-update-button'],
];
const failures = [];
let checks = 0;
function expect(value, label) {
    checks++;
    if (!value) failures.push(label);
}

const php = fs.readFileSync(path.join(root, 'tourfic-vendor.php'), 'utf8');
const actionList = php.match(/\$admin_actions = array\(([\s\S]*?)\);/);
const published = actionList ? Array.from(actionList[1].matchAll(/'(tf_vendor_[a-z_]+)'/g), match => match[1]) : [];
expect(published.length === actions.length && actions.every(([action]) => published.includes(action)), 'PHP publishes every admin nonce action');
expect(php.includes("wp_create_nonce( 'tourfic_vendor_admin_' . $action )"), 'PHP nonce names match callback verification');
expect(php.includes("TF_VENDOR_ADMIN_PATH . 'assets/js/tourfic-vendor-admin' . $js_min . '.js'") && php.includes("is_file( $script_path ) ? (string) filemtime( $script_path ) : '1.0.0'"), 'PHP versions the selected admin bundle by its modification time');
expect(/wp_enqueue_script\( 'tf-vendor',[^;]+\$script_version, true \);/.test(php), 'Admin script enqueue uses the asset version');

for (const relative of [
    'sass/admin/js/addon/vendor/tourfic-vendor.js',
    'admin/assets/js/tourfic-vendor-admin.js',
    'admin/assets/js/tourfic-vendor-admin.min.js',
]) {
    const handlers = new Map();
    const requests = [];
    const document = {};
    function jquery(selector) {
        const label = typeof selector === 'string' ? selector : '';
        const node = {
            0: {},
            ready(callback) { callback(); return this; },
            on(event, delegated, callback) {
                handlers.set(`${event}:${typeof delegated === 'string' ? delegated : label}`, callback || delegated);
                return this;
            },
            find() { return this; },
            closest() { return this; },
            val() { return label === '#tf_vendor_bulk' ? 'approved' : '7'; },
            serializeArray() { return [{name: 'vendor_id', value: '7'}]; },
            is() { return true; },
            attr() { return '9'; },
            addClass() { return this; },
            removeClass() { return this; },
            remove() { return this; },
            show() { return this; },
            hide() { return this; },
        };
        return node;
    }
    jquery.ajax = request => requests.push(request);
    jquery.each = (items, callback) => callback.call({}, 0, {});
    class FixtureFormData {
        constructor() { this.fields = {}; }
        append(key, value) { this.fields[key] = value; }
    }
    const nonces = Object.fromEntries(actions.map(([action]) => [action, `fixture:${action}`]));
    const context = {
        document, jQuery: jquery, $: jquery, FormData: FixtureFormData,
        window: {tourficOptions: {ajax_url: 'https://example.invalid/admin-ajax.php'}},
        tf_vendor_params: {ajax_url: 'https://example.invalid/admin-ajax.php', admin_nonces: nonces},
        confirm: () => true, alert: () => {}, console,
    };
    vm.runInNewContext(fs.readFileSync(path.join(root, relative), 'utf8'), context, {filename: relative});
    for (const [action, event, selector] of actions) {
        const handler = handlers.get(`${event}:${selector}`);
        expect(typeof handler === 'function', `${relative}: binds ${action}`);
        if (!handler) continue;
        const before = requests.length;
        handler.call({}, {preventDefault() {}});
        expect(requests.length === before + 1, `${relative}: submits ${action}`);
        const request = requests[requests.length - 1];
        const data = request.data instanceof FixtureFormData ? request.data.fields : request.data;
        expect(data.action === action && data.nonce === nonces[action], `${relative}: ${action} sends its matching nonce`);
    }
}

for (const failure of failures) console.log(`FAIL: ${failure}`);
console.log(JSON.stringify({checks, failures: failures.length}));
process.exit(failures.length ? 1 : 0);

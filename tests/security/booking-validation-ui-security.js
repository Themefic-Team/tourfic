// Rejected booking requests must display their error and must never show confirmation.
const fs = require('fs'), vm = require('vm'), acorn = require('acorn'), assert = require('assert');
let checks = 0;
function requests(source) {
    const found = [];
    function visit(node) {
        if (!node || typeof node !== 'object') return;
        if (node.type === 'CallExpression' && node.callee.type === 'MemberExpression' && node.callee.property.name === 'ajax' && node.arguments[0]?.type === 'ObjectExpression') found.push(node.arguments[0]);
        Object.values(node).forEach(value => Array.isArray(value) ? value.forEach(visit) : visit(value));
    }
    visit(acorn.parse(source, {ecmaVersion: 'latest'}));
    return found.sort((a, b) => a.start - b.start);
}
for (const kind of ['car', 'hotel', 'apartment', 'tour']) {
    const source = fs.readFileSync('sass/app/js/free/' + kind + '.js', 'utf8');
    const objects = requests(source);
    let selected;
    if (kind === 'car' || kind === 'hotel') {
        const action = 'tourfic_' + kind + '_booking';
        const pattern = new RegExp("action: '" + action + "'|formData.append\\('action', '" + action + "'\\)", 'g');
        selected = [...source.matchAll(pattern)].map(match => objects.find(node => node.start > match.index));
    } else if (kind === 'apartment') {
        selected = objects.filter(node => node.properties.some(property => property.key.name === 'error' && source.slice(property.start, property.end).includes('showApartmentBookingErrors')));
    } else {
        const marker = source.indexOf("formData.append('action', 'tourfic_tours_booking')");
        selected = [objects.find(node => node.start > marker)];
    }
    assert.strictEqual(selected.length, {car: 3, hotel: 2, apartment: 2, tour: 1}[kind]); checks++;
    for (const request of selected) {
        const messages = [], shown = [];
        const chain = {removeClass() { return this; }, addClass(value) { shown.push(value); return this; }, find() { return this; }, unblock() {}, hide() { return this; }};
        const sandbox = {$: () => chain, $this: chain, $form: chain, $popup: chain, notyf: {error: message => messages.push(message)},
            hideApartmentPopupLoader() {}, tourficParams: {booking_error: 'Synthetic fallback'}, settings: {fromPopup: false}, keepPopupLoading: false,
            showApartmentBookingErrors: errors => messages.push(...errors), tfShowTourBookingErrors: errors => messages.push(...errors)};
        function callback(name) {
            const property = request.properties.find(value => value.key.name === name);
            return property && vm.runInNewContext('(' + source.slice(property.value.start, property.value.end) + ')', sandbox);
        }
        const error = callback('error'); assert.ok(error, kind + ' must consume HTTP errors'); checks++;
        error({status: 400, responseJSON: {status: 'error', errors: ['Synthetic rejection']}});
        if (callback('complete')) callback('complete')({status: 400});
        assert.deepStrictEqual(messages, ['Synthetic rejection']); checks++;
        assert.deepStrictEqual(shown, []); checks++;
        if (kind === 'hotel' && source.slice(request.start, request.end).includes('tf-withoutpayment-booking-confirm')) {
            messages.length = 0;
            callback('success')({status: 'error', errors: ['Synthetic rejection']});
            assert.deepStrictEqual(shown, []); checks++;
            callback('success')({without_payment: 'true'});
            assert.deepStrictEqual(shown, ['show']); checks++;
        }
    }
}
console.log('PASS: ' + checks + ' booking error and confirmation assertions.');

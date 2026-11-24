// Exercise the real vendored constructors without creating HTML or executing markup.
const fs = require('fs');
const vm = require('vm');
const assert = require('assert');
const acorn = require('acorn');
let checks = 0;
for (const file of ['assets/app/libs/slick/slick.js', 'assets/app/libs/slick/slick.min.js']) {
    let dataReads = 0;
    const element = {};
    const chain = {hasClass: () => true, trigger() { return this; }, data() {
        dataReads++;
        return {dotsClass: 'untrusted-marker', prevArrow: '<button>Inert marker</button>', slidesToShow: 90};
    }};
    const $ = () => chain;
    $.fn = {};
    $.extend = Object.assign;
    $.proxy = (fn, context) => typeof fn === 'function' ? fn.bind(context) : undefined;
    $.type = value => Array.isArray(value) ? 'array' : typeof value;
    vm.runInNewContext(fs.readFileSync(file, 'utf8'), {window: {}, document: {}, jQuery: $});
    $.fn.slick.call({0: element, length: 1}, {accessibility: false, slidesToShow: 3, dotsClass: 'trusted-dots'});
    assert.strictEqual(element.slick.options.slidesToShow, 3); checks++;
    assert.strictEqual(element.slick.options.dotsClass, 'trusted-dots'); checks++;
    assert.ok(!element.slick.options.prevArrow.includes('Inert marker')); checks++;
    assert.strictEqual(dataReads, 0); checks++;
}
const source = fs.readFileSync('sass/app/js/free/apartment.js', 'utf8');
const tree = acorn.parse(source, {ecmaVersion: 'latest'});
let initializer;
function visit(node) {
    if (!node || typeof node !== 'object') return;
    if (node.type === 'AssignmentExpression' && node.left.property?.name === 'tourfic_init_highlight_sliders') {
        initializer = source.slice(node.right.start, node.right.end);
    }
    Object.values(node).forEach(value => Array.isArray(value) ? value.forEach(visit) : visit(value));
}
visit(tree);
assert.ok(initializer); checks++;
const trusted = {nodeType: 1, isConnected: true, classList: {contains: name => name === 'tf-apt-highlights-slider'}};
const forged = {...trusted};
const initialized = [];
const $ = elements => {
    let selected = Array.isArray(elements) ? elements : [elements];
    return {
        filter(callback) { selected = selected.filter(element => callback.call(element)); return this; },
        children() { return {length: 5}; },
        removeAttr(name) { assert.strictEqual(name, 'data-slick'); return this; },
        removeData(name) { assert.strictEqual(name, 'slick'); return this; },
        slick(options) { assert.strictEqual(options.arrows, false); initialized.push(...selected); return this; },
    };
};
vm.runInNewContext('(' + initializer + ')', {$})([trusted, null]);
assert.deepStrictEqual(initialized, [trusted]); checks++;
assert.ok(!initialized.includes(forged)); checks++;
for (const file of ['assets/app/js/tourfic-scripts.js', 'assets/app/js/tourfic-scripts.min.js']) {
    const bundle = fs.readFileSync(file, 'utf8');
    for (const token of ['tourfic_init_highlight_sliders', 'tourfic_highlight_sliders', 'frontend/element_ready/tf-single-highlights.default']) {
        assert.ok(bundle.includes(token), file + ' missing ' + token); checks++;
    }
}
console.log('PASS: ' + checks + ' source, dependency and generated-bundle slider assertions.');

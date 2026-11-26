#!/usr/bin/env node
/**
 * Render the actual Pro booking-details component without WordPress or network access.
 *
 * Usage: node tests/regression/pro-booking-details-render.js [pro-root] [source-file]
 * The optional source file supports checking a saved pre-fix negative control.
 * Dependencies come from the Pro checkout's existing node_modules.
 */
'use strict';

const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { createRequire } = require('node:module');

const proRoot = path.resolve(process.argv[2] || path.join(__dirname, '../../../tourfic-pro'));
const sourceFile = process.argv[3] || path.join(proRoot, 'src/jsx/components/Booking/BookingsDetails.js');
const proRequire = createRequire(path.join(proRoot, 'package.json'));
const { JSDOM, VirtualConsole } = proRequire('jsdom');
const babel = proRequire('@babel/core');
const browserErrors = [];
const virtualConsole = new VirtualConsole();
virtualConsole.on('jsdomError', (error) => browserErrors.push(error.message));

// jsdom does not load subresources; every application request is also replaced below.
const dom = new JSDOM('<!doctype html><html><body><div id="root"></div></body></html>', {
	url: 'https://tourfic-regression.invalid/tf-dashboard/',
	virtualConsole,
});
global.window = dom.window;
global.document = dom.window.document;
Object.defineProperty(global, 'navigator', { configurable: true, value: dom.window.navigator });

const React = proRequire('react');
const ReactDOM = proRequire('react-dom');
const router = proRequire('react-router-dom');
const reactErrors = [];
const originalConsoleError = console.error;
console.error = (...args) => {
	if (!String(args[0]).includes('ReactDOM.render is no longer supported')) {
		reactErrors.push(args.map(String).join(' '));
	}
};

let requestedOrders = [];
let mutationRequests = [];
let resolveOrder;
const axios = {
	get: (url) => {
		requestedOrders.push(url);
		return new Promise((resolve) => { resolveOrder = resolve; });
	},
	post: async (url) => {
		mutationRequests.push(url);
		return { status: 200 };
	},
};
global.fetch = window.fetch = async (url) => {
	mutationRequests.push(url);
	return { ok: true };
};
global.XMLHttpRequest = window.XMLHttpRequest = class {
	constructor() {
		throw new Error('Unexpected network transport in isolated booking-details fixture');
	}
};

// Keep React hooks, routing, rendering and component events real. Only replace
// presentation controls and request boundaries unrelated to the voucher contract.
const EmptyControl = () => null;
const Button = ({ label, onClick }) => React.createElement('button', { onClick }, label);
const Toast = React.forwardRef((props, ref) => {
	React.useImperativeHandle(ref, () => ({ show() {} }));
	return null;
});
const imports = {
	react: React,
	immer: proRequire('immer'),
	'moment/moment': proRequire('moment'),
	'react-router-dom': router,
	'@wordpress/i18n': { __: (text) => text },
	'../../../store/utils/axios': axios,
	'primereact/skeleton': { Skeleton: EmptyControl },
	'primereact/dropdown': { Dropdown: EmptyControl },
	'primereact/button': { Button },
	'primereact/toast': { Toast },
};
const compiled = babel.transformSync(fs.readFileSync(sourceFile, 'utf8'), {
	babelrc: false,
	configFile: false,
	presets: [proRequire('@babel/preset-react')],
	plugins: [proRequire('@babel/plugin-transform-modules-commonjs')],
}).code;
const componentModule = { exports: {} };
new Function('require', 'module', 'exports', compiled)((name) => {
	if (Object.hasOwn(imports, name)) return imports[name];
	if (name.startsWith('../Forms/Elements/')) return EmptyControl;
	throw new Error(`Unexpected booking-details import: ${name}`);
}, componentModule, componentModule.exports);
const BookingsDetails = componentModule.exports.default;
const root = document.getElementById('root');
const uniqueId = 'fixture-voucher-101';
const configuredSettings = {
	'qr-ticket-title': 'Booking reference',
	'qr-ticket-prefix': 'TF-',
	'qr-ticket-content': 'Please bring your booking reference.',
};

async function waitFor(predicate, message) {
	for (let attempt = 0; attempt < 100; attempt++) {
		if (predicate()) return;
		await new Promise((resolve) => setTimeout(resolve, 5));
	}
	throw new Error(message);
}

function assertVoucher(container, settings) {
	const voucher = container.querySelector('.tf-visitor-vouchers');
	assert.ok(voucher, 'Tour voucher renders');
	assert.equal(voucher.querySelector('.title span').textContent,
		`${settings?.['qr-ticket-title'] ?? ''}:${settings?.['qr-ticket-prefix'] ?? ''}${uniqueId}`);
	assert.equal(voucher.querySelector('.tf-voucher-footer-qoute p').textContent,
		settings?.['qr-ticket-content'] ?? '');
	assert.ok(!voucher.textContent.includes('undefined'), 'Voucher does not display undefined');
}

async function renderCase(postType, settings, omitSettings = false) {
	requestedOrders = [];
	mutationRequests = [];
	resolveOrder = undefined;
	browserErrors.length = 0;
	reactErrors.length = 0;
	global.tfDashboardObj = {
		hotel_without_payment_fields: {},
		tour_without_payment_fields: {},
		wp_rest_nonce: 'isolated-fixture',
		booking_nonce: 'isolated-fixture',
		site_url: 'https://tourfic-regression.invalid',
		is_customer: false,
		...(omitSettings ? {} : { tf_settings: settings }),
	};
	assert.ok(!Object.hasOwn(tfDashboardObj, 'tourfic_settings'), 'Only the canonical settings key is supplied');

	try {
		ReactDOM.render(React.createElement(React.StrictMode, null,
			React.createElement(router.MemoryRouter, { initialEntries: ['/booking/101'] },
				React.createElement(router.Routes, null,
					React.createElement(router.Route, {
						path: '/booking/:id',
						element: React.createElement(BookingsDetails, { postType }),
					})))), root);
		await waitFor(() => resolveOrder, 'Initial render never requested booking details');
		assert.ok(root.querySelector('.tf-booking-details-preview'), 'Loading view renders before the response');
		resolveOrder({ data: {
			order_id: 9001,
			ostatus: 'completed',
			checkinout: 'not',
			order_author_role: 'administrator',
			order_detail: { post_name: 'Fixture booking', order_date: '2026-11-25', booked_by: 'Fixture customer' },
			billing_details: JSON.stringify({ billing_first_name: 'Fixture', billing_last_name: 'Customer' }),
			order_details: JSON.stringify({ unique_id: uniqueId }),
			total_price: '$100.00',
			payment_method: 'offline',
		} });
		await waitFor(() => root.querySelector('.tf-booking-details-preview-box'), 'Booking details did not render');
		await new Promise((resolve) => setTimeout(resolve, 0));
		assert.deepEqual(requestedOrders, ['/tf/v1/order/101']);
		assert.deepEqual(mutationRequests, [], 'Freshly mounting booking details only reads the booking');
		assert.ok(root.textContent.includes('Fixture booking'), 'Loaded booking remains visible');
		assert.ok(!root.textContent.includes('undefined'), 'Booking details do not display undefined');
		if (postType === 'tour') {
			assertVoucher(root, settings);
			const preview = [...root.querySelectorAll('button')].find((button) => button.textContent === 'Preview');
			assert.ok(preview, 'Voucher preview button is available');
			preview.dispatchEvent(new window.MouseEvent('click', { bubbles: true }));
			await waitFor(() => root.querySelector('.tf-booking-modal'), 'Voucher preview did not open');
			assertVoucher(root.querySelector('.tf-booking-modal'), settings);
		}
		assert.deepEqual(browserErrors, [], 'No browser runtime errors');
		assert.deepEqual(reactErrors, [], 'No React runtime errors');
	} finally {
		ReactDOM.unmountComponentAtNode(root);
		document.body.classList.remove('tf-booking-modal-open');
	}
}

const cases = [
	...['hotel', 'tour', 'apartment', 'car'].map((postType) => [
		`${postType} first load with canonical settings`, () => renderCase(postType, configuredSettings),
	]),
	['Tour voucher with missing QR fields', () => renderCase('tour', {})],
	['Tour voucher with null QR fields', () => renderCase('tour', {
		'qr-ticket-title': null, 'qr-ticket-prefix': null, 'qr-ticket-content': null,
	})],
	['Tour voucher with missing settings', () => renderCase('tour', undefined, true)],
];

(async () => {
	let failures = 0;
	try {
		for (const [name, run] of cases) {
			try {
				await run();
				process.stdout.write(`ok - ${name}\n`);
			} catch (error) {
				failures++;
				process.stderr.write(`not ok - ${name}: ${error.message}\n`);
			}
		}
	} finally {
		console.error = originalConsoleError;
		dom.window.close();
	}
	process.stdout.write(`${cases.length - failures}/${cases.length} booking-details render scenarios passed\n`);
	process.exitCode = failures ? 1 : 0;
})().catch((error) => {
	console.error = originalConsoleError;
	console.error(error);
	dom.window.close();
	process.exitCode = 1;
});

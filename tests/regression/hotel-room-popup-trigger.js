#!/usr/bin/env node
/**
 * Exercise the actual hotel popup function and delegated click registration.
 * Run: node tests/regression/hotel-room-popup-trigger.js [pro-root]
 * Uses installed jsdom/jQuery, real blockUI, and an in-memory AJAX transport.
 * No browser, WordPress bootstrap, database, booking writes, or network access.
 */
'use strict';

const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { createRequire } = require('node:module');

const root = path.resolve(__dirname, '../..');
const proRoot = path.resolve(process.argv[2] || path.join(root, '../tourfic-pro'));
const dependency = createRequire(path.join(proRoot, 'package.json'));
const { JSDOM, VirtualConsole } = dependency('jsdom');
const acorn = dependency('acorn');
const jquery = fs.readFileSync(dependency.resolve('jquery'), 'utf8');
const blockUI = fs.readFileSync(path.join(root, '../woocommerce/assets/js/jquery-blockui/jquery.blockUI.js'), 'utf8');

function actualPopupCode(file) {
	const source = fs.readFileSync(file, 'utf8');
	const nodes = [];
	function visit(value) {
		if (!value || typeof value !== 'object') return;
		if (typeof value.type === 'string') nodes.push(value);
		for (const child of Object.values(value)) {
			if (Array.isArray(child)) child.forEach(visit);
			else if (child && typeof child === 'object') visit(child);
		}
	}
	visit(acorn.parse(source, { ecmaVersion: 'latest' }));
	const popups = nodes.filter((node) => node.type === 'VariableDeclaration'
		&& node.declarations.some((declaration) => declaration.id.name === 'hotelPopupBooking'));
	assert.equal(popups.length, 1, `${file}: locate the real popup function`);
	const popup = popups[0];
	const handlers = nodes.filter((node) => node.type === 'ExpressionStatement'
		&& node.expression.type === 'CallExpression'
		&& node.expression.callee.type === 'MemberExpression'
		&& node.expression.callee.property.name === 'on'
		&& node.expression.arguments[0]?.value === 'click'
		&& node.expression.arguments[1]?.value === '.tf-hotel-booking-popup-btn');
	assert.equal(handlers.length, 1, `${file}: locate the real delegated click handler`);
	const helpers = ['tfSplitDateRange', 'tfGetHotelSelectedExtras'].map((name) => {
		const matches = nodes.filter((node) => node.type === 'FunctionDeclaration'
			&& node.id.name === name && node.start < popup.start);
		assert.ok(matches.length, `${file}: locate real helper ${name}`);
		return matches.sort((a, b) => b.start - a.start)[0];
	});
	return [...helpers, popup, handlers[0]].map((node) => source.slice(node.start, node.end)).join('\n');
}

const forms = [
	{ id: 'option-0', roomId: '701', uniqueId: 'room-701', optionId: 'room-701_0', quantity: '3' },
	{ id: 'option-1', roomId: '701', uniqueId: 'room-701', optionId: 'room-701_1', quantity: '2' },
	{ id: 'ordinary', roomId: '702', uniqueId: 'room-702', quantity: '1' },
];

function formMarkup(form) {
	return `<form id="${form.id}" class="tf-room reserve">
		<input name="post_id" value="501"><input name="room_id" value="${form.roomId}">
		<input name="unique_id" value="${form.uniqueId}">
		${form.optionId ? `<input name="option_id" value="${form.optionId}">` : ''}
		<input name="hotel_room_depo" value="false">
		<select name="hotel_room_selected">${[1, 2, 3, 4].map((quantity) =>
			`<option value="${quantity}" ${String(quantity) === form.quantity ? 'selected' : ''}>${quantity}</option>`).join('')}</select>
		<div class="room-submit-wrap"><div class="roomselectissue"></div>
			<a id="continue-${form.id}" class="tf_air_service tf-hotel-booking-popup-btn tf_btn" href="#">Continue</a>
		</div>
		<div id="popup-${form.id}" class="tf-withoutpayment-booking tf-hotel-withoutpayment-booking">
			<div class="tf-hotel-booking-content" style="display:none">
				<div class="tf-traveller-info-box"></div><div class="tf-booking-traveller-info"></div>
			</div>
			<div><div class="tf-control-pagination" style="display:none">First step</div></div>
		</div>
	</form>`;
}

function run(file) {
	const errors = [];
	const virtualConsole = new VirtualConsole();
	virtualConsole.on('jsdomError', (error) => errors.push(error.message));
	const dom = new JSDOM(`<!doctype html><html><head><style>
		.tf-withoutpayment-booking { display:none }
		.tf-withoutpayment-booking.show { display:block }
		</style></head><body>
		<input name="tf_room_booking_nonce" value="fixture-room-nonce">
		<input name="adult" value="2"><input name="child" value="0">
		<input name="check_in_date" value="2026/12/01"><input name="check_out_date" value="2026/12/03">
		<input name="place" value="Fixture hotel"><input name="children_ages" value="">
		<input id="hotel_room_number"><input id="hotel_roomid"><input id="hotel_room_uniqueid">
		<div id="tour_room_details_loader" style="display:none"></div>
		${forms.map(formMarkup).join('')}
		</body></html>`, {
		url: 'https://tourfic-popup-regression.invalid/hotel/fixture/#',
		runScripts: 'outside-only',
		virtualConsole,
	});
	const { window } = dom;
	try {
		window.eval(jquery);
		window.eval(blockUI);
		const $ = window.jQuery;
		window.tourficParams = { ajax_url: '/wp-admin/admin-ajax.php', select_room: 'Select a room' };
		const requests = [];
		$.ajax = (options) => {
			requests.push(options);
			options.beforeSend?.();
			const selected = options.data.option_id || options.data.unique_id;
			options.success(JSON.stringify({
				status: 'success', guest_info: `<p>Guests for ${selected}</p>`,
				hotel_booking_summery: `<p>${selected}: ${options.data.room} rooms</p>`,
			}));
			options.complete?.();
		};
		// Only the exact source fragments run; unrelated maps and widgets do not.
		window.eval(`(function ($) { ${actualPopupCode(file)} })(jQuery);`);
		const popupIds = [...window.document.querySelectorAll('.tf-withoutpayment-booking')].map((popup) => popup.id);
		assert.equal(new Set(popupIds).size, forms.length, 'Each form owns a distinct popup ID');

		// Recreate the old bare Continue anchor by removing only its missing hook.
		const bare = $('#continue-option-0').clone().removeClass('tf-hotel-booking-popup-btn')
			.attr('id', 'old-bare-continue').appendTo('#option-0 .room-submit-wrap')[0];
		const bareClick = new window.MouseEvent('click', { bubbles: true, cancelable: true });
		bare.dispatchEvent(bareClick);
		assert.equal(requests.length, 0, 'Old bare anchor does not dispatch a booking popup request');
		assert.equal(bareClick.defaultPrevented, false, 'Old bare anchor has no booking click handler');
		bare.remove();

		for (const [index, form] of forms.entries()) {
			$('.tf-withoutpayment-booking').removeClass('show');
			$('.tf-hotel-booking-content, .tf-control-pagination').hide();
			const trigger = window.document.getElementById(`continue-${form.id}`);
			const before = window.location.href;
			const event = new window.MouseEvent('click', { bubbles: true, cancelable: true });
			assert.equal(trigger.getAttribute('href'), '#', 'Continue has a safe fragment destination');
			assert.equal(trigger.dispatchEvent(event), false, 'Delegated handler cancels anchor navigation');
			assert.equal(event.defaultPrevented, true);
			assert.equal(window.location.href, before);
			assert.equal(requests.length, index + 1, 'One click sends exactly one popup request');
			const request = requests[index];
			assert.equal(request.type, 'post');
			assert.equal(request.url, '/wp-admin/admin-ajax.php');
			assert.equal(request.data.action, 'tourfic_hotel_booking_popup');
			assert.equal(request.data.tf_room_booking_nonce, 'fixture-room-nonce');
			assert.equal(request.data.post_id, '501');
			assert.equal(request.data.room_id, form.roomId);
			assert.equal(request.data.unique_id, form.uniqueId);
			assert.equal(request.data.option_id, form.optionId, 'No stale option leaks into the ordinary form');
			assert.equal(request.data.room, form.quantity, 'Use the clicked form\'s selected quantity');
			const popup = $(`#popup-${form.id}`);
			assert.equal(popup.closest('form')[0], trigger.closest('form'));
			assert.equal(popup.hasClass('show'), true, 'Matching same-form popup is revealed');
			assert.notEqual(popup.css('display'), 'none');
			assert.notEqual(popup.find('.tf-hotel-booking-content').css('display'), 'none');
			assert.notEqual(popup.find('.tf-control-pagination').first().css('display'), 'none');
			const selected = form.optionId || form.uniqueId;
			assert.equal(popup.find('.tf-traveller-info-box').text(), `Guests for ${selected}`);
			assert.equal(popup.find('.tf-booking-traveller-info').text(), `${selected}: ${form.quantity} rooms`);
			assert.equal($('.tf-withoutpayment-booking.show').length, 1, 'Only the matching form is opened');
			assert.equal($('#tour_room_details_loader').css('display'), 'none');
		}
		assert.deepEqual(errors, [], 'No browser runtime errors');
		process.stdout.write(`ok - ${path.relative(root, file)}: three popup triggers and bare-anchor control\n`);
	} finally {
		window.close();
	}
}

run(path.join(root, 'sass/app/js/free/hotel.js'));
run(path.join(root, 'assets/app/js/tourfic-scripts.js'));

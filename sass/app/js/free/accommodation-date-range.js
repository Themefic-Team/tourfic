(function ($, win) {
    const accommodationTypes = ['tf_hotel', 'tf_apartment', 'tf_room'];
    const originalFlatpickr = win.flatpickr;
    const originalJqueryFlatpickr = $.fn.flatpickr;

    if (!originalFlatpickr || !originalJqueryFlatpickr) {
        return;
    }

    function addCalendarDay(date) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate() + 1);
    }

    function startOfDay(date) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate());
    }

    function isAfter(date, comparisonDate) {
        return startOfDay(date).getTime() > startOfDay(comparisonDate).getTime();
    }

    function isSameDay(date, comparisonDate) {
        return startOfDay(date).getTime() === startOfDay(comparisonDate).getTime();
    }

    function getElements(selector) {
        if (typeof selector === 'string') {
            return Array.from(document.querySelectorAll(selector));
        }

        if (selector instanceof Element) {
            return [selector];
        }

        return Array.from(selector || []);
    }

    function isAccommodationRange(elements, options) {
        if (!options || options.mode !== 'range') {
            return false;
        }

        return elements.some(function (element) {
            if (!element.matches('[name="check-in-out-date"]')) {
                return false;
            }

            const form = element.closest('form');
            const typeInput = form ? form.querySelector('[name="type"].tf-post-type') : null;

            return typeInput && accommodationTypes.includes(typeInput.value);
        });
    }

    function runHooks(hooks, context, args) {
        const callbacks = Array.isArray(hooks) ? hooks : [hooks];

        callbacks.forEach(function (callback) {
            if (typeof callback === 'function') {
                callback.apply(context, args);
            }
        });
    }

    function getState(instance) {
        if (!instance.tfAccommodationRangeState) {
            instance.tfAccommodationRangeState = {
                pendingStart: null,
                previousCheckout: null,
                provisionalCheckout: null,
            };
        }

        return instance.tfAccommodationRangeState;
    }

    function getInitialDates(instance) {
        const today = startOfDay(new Date());
        const minDate = instance.config.minDate ? startOfDay(instance.config.minDate) : today;
        const checkIn = isAfter(minDate, today) ? minDate : today;

        return [checkIn, addCalendarDay(checkIn)];
    }

    function getDisplayRoot(instance) {
        return instance.element.closest('form') || instance.element.parentElement || document;
    }

    function updateDateGroup(group, date, instance) {
        if (!group) {
            return;
        }

        const dateElement = group.querySelector('.date, .tf-booking-date');
        const monthInner = group.querySelector('.month > span, .tf-booking-month > span');
        const monthElement = group.querySelector('.month, .tf-booking-month');
        const yearElement = group.querySelector('.year');
        const monthNames = instance.l10n && instance.l10n.months ? instance.l10n.months.shorthand : [];

        if (dateElement) {
            dateElement.textContent = String(date.getDate()).padStart(2, '0');
        }
        if (monthInner) {
            monthInner.textContent = monthNames[date.getMonth()] || instance.formatDate(date, 'M');
        } else if (monthElement) {
            monthElement.textContent = monthNames[date.getMonth()] || instance.formatDate(date, 'M');
        }
        if (yearElement) {
            yearElement.textContent = date.getFullYear();
        }
    }

    function updateDisplayFields(instance, checkIn, checkOut) {
        const root = getDisplayRoot(instance);
        const visibleFormat = instance.config.altInput ? instance.config.altFormat : instance.config.dateFormat;

        root.querySelectorAll('.tf_checkin_dates, .tf-booking-form-checkin').forEach(function (group) {
            updateDateGroup(group, checkIn, instance);
        });
        root.querySelectorAll('.tf_checkout_dates, .tf-booking-form-checkout').forEach(function (group) {
            updateDateGroup(group, checkOut, instance);
        });

        root.querySelectorAll('[name="check_in_date"], [name="tf-check-in"]').forEach(function (input) {
            input.value = instance.formatDate(checkIn, visibleFormat);
        });
        root.querySelectorAll('[name="check_out_date"], [name="tf-check-out"]').forEach(function (input) {
            input.value = instance.formatDate(checkOut, visibleFormat);
        });
    }

    function syncRange(instance, checkIn, checkOut) {
        instance.input.value = instance.formatDate(checkIn, instance.config.dateFormat)
            + ' - '
            + instance.formatDate(checkOut, instance.config.dateFormat);

        if (instance.altInput) {
            instance.altInput.value = instance.formatDate(checkIn, instance.config.altFormat)
                + ' - '
                + instance.formatDate(checkOut, instance.config.altFormat);
        }

        updateDisplayFields(instance, checkIn, checkOut);
    }

    function clearPendingDay(instance) {
        instance.calendarContainer.querySelectorAll('[data-tf-accommodation-checkin]').forEach(function (dayElement) {
            dayElement.classList.remove('flatpickr-disabled');
            dayElement.removeAttribute('aria-disabled');
            dayElement.removeAttribute('data-tf-accommodation-checkin');
        });
    }

    function markPendingDay(instance) {
        const state = getState(instance);

        clearPendingDay(instance);
        if (!state.pendingStart) {
            return;
        }

        instance.calendarContainer.querySelectorAll('.flatpickr-day').forEach(function (dayElement) {
            if (dayElement.dateObj && isSameDay(dayElement.dateObj, state.pendingStart)) {
                dayElement.classList.add('flatpickr-disabled');
                dayElement.setAttribute('aria-disabled', 'true');
                dayElement.setAttribute('data-tf-accommodation-checkin', 'true');
            }
        });
    }

    function initializeRange(selectedDates, instance) {
        const state = getState(instance);
        let dates = selectedDates.slice(0, 2);

        if (!dates.length) {
            dates = getInitialDates(instance);
        } else if (dates.length === 1 || !isAfter(dates[1], dates[0])) {
            dates = [dates[0], addCalendarDay(dates[0])];
        }

        instance.setDate(dates, false);
        state.previousCheckout = dates[1];
        syncRange(instance, dates[0], dates[1]);

        return dates;
    }

    function handleRangeChange(selectedDates, instance) {
        const state = getState(instance);

        if (selectedDates.length === 1) {
            const checkIn = selectedDates[0];
            const checkout = state.previousCheckout && isAfter(state.previousCheckout, checkIn)
                ? state.previousCheckout
                : addCalendarDay(checkIn);

            state.pendingStart = checkIn;
            state.provisionalCheckout = checkout;
            syncRange(instance, checkIn, checkout);
            markPendingDay(instance);

            return [checkIn, checkout];
        }

        if (selectedDates.length >= 2) {
            let checkOut = selectedDates[1];

            if (!isAfter(checkOut, selectedDates[0])) {
                checkOut = addCalendarDay(selectedDates[0]);
                instance.setDate([selectedDates[0], checkOut], false);
            }

            state.previousCheckout = checkOut;
            state.pendingStart = null;
            state.provisionalCheckout = null;
            clearPendingDay(instance);
            syncRange(instance, selectedDates[0], checkOut);

            return [selectedDates[0], checkOut];
        }

        return selectedDates;
    }

    function commitProvisionalRange(selectedDates, instance) {
        const state = getState(instance);

        if (selectedDates.length !== 1 || !state.pendingStart || !state.provisionalCheckout) {
            return;
        }

        const checkIn = state.pendingStart;
        const checkOut = state.provisionalCheckout;

        instance.setDate([checkIn, checkOut], false);
        state.previousCheckout = checkOut;
        state.pendingStart = null;
        state.provisionalCheckout = null;
        clearPendingDay(instance);
        syncRange(instance, checkIn, checkOut);
    }

    function enhanceOptions(elements, options) {
        if (!isAccommodationRange(elements, options)) {
            return options;
        }

        const enhancedOptions = Object.assign({}, options);
        const originalOnReady = options.onReady;
        const originalOnChange = options.onChange;
        const originalOnClose = options.onClose;
        const originalOnDayCreate = options.onDayCreate;

        if (Array.isArray(enhancedOptions.defaultDate)) {
            enhancedOptions.defaultDate = enhancedOptions.defaultDate.filter(function (date) {
                return date instanceof Date || String(date || '').trim() !== '';
            });

            if (!enhancedOptions.defaultDate.length) {
                delete enhancedOptions.defaultDate;
            }
        }

        enhancedOptions.onReady = function (selectedDates, dateStr, instance) {
            const initialDates = initializeRange(selectedDates, instance);

            runHooks(originalOnReady, this, [initialDates, instance.input.value, instance]);
            syncRange(instance, initialDates[0], initialDates[1]);
        };
        enhancedOptions.onChange = function (selectedDates, dateStr, instance) {
            const effectiveDates = handleRangeChange(selectedDates, instance);

            runHooks(originalOnChange, this, [effectiveDates, instance.input.value, instance]);
            if (effectiveDates.length >= 2) {
                syncRange(instance, effectiveDates[0], effectiveDates[1]);
            }
        };
        enhancedOptions.onClose = function (selectedDates, dateStr, instance) {
            runHooks(originalOnClose, this, [selectedDates, dateStr, instance]);
            commitProvisionalRange(selectedDates, instance);
        };
        enhancedOptions.onDayCreate = function (dateObject, dateString, instance, dayElement) {
            runHooks(originalOnDayCreate, this, [dateObject, dateString, instance, dayElement]);

            const state = getState(instance);
            if (state.pendingStart && dayElement.dateObj && isSameDay(dayElement.dateObj, state.pendingStart)) {
                dayElement.classList.add('flatpickr-disabled');
                dayElement.setAttribute('aria-disabled', 'true');
                dayElement.setAttribute('data-tf-accommodation-checkin', 'true');
            }
        };

        return enhancedOptions;
    }

    function accommodationFlatpickr(selector, options) {
        return originalFlatpickr(selector, enhanceOptions(getElements(selector), options));
    }

    Object.keys(originalFlatpickr).forEach(function (property) {
        accommodationFlatpickr[property] = originalFlatpickr[property];
    });

    win.flatpickr = accommodationFlatpickr;
    $.fn.flatpickr = function (options) {
        return originalJqueryFlatpickr.call(this, enhanceOptions(Array.from(this), options));
    };

    win.tfAccommodationDateRange = {
        addCalendarDay: addCalendarDay,
        isAfter: isAfter,
    };
})(jQuery, window);

(function ($) {
    var requestRunning = false;
    var xhr;
    var hasFilter = false;
    var data = URLToArrayNew();
    jQuery(function ($) {
        if ($('.search-result-page.st-style-elementor').length) {
            ajaxFilterHandler();
            $('.show-filter-mobile .button-filter').on('click', function () {
                $('.sidebar-filter').fadeIn();
            });
            $('.sidebar-filter .close-sidebar').on('click', function () {
                $('.sidebar-filter').fadeOut();
            });
        }
    });
    var resizeId;

    function doneResizing() {
        ajaxFilterHandler();
    }

    $('.toolbar .layout span.layout-item').on('click', function () {
        if (!$(this).hasClass('active')) {
            $(this).parent().find('span').removeClass('active');
            $(this).addClass('active');
            data['layout'] = $(this).data('value');
            data['change_layout_to'] = '';
            data['layout_old'] = '';
            ajaxFilterHandler(false);
        }
    });
    $('#st-map-coordinate').on('change', function () {
        let coordinate = $('#st-map-coordinate').val();
        if (coordinate.length > 0) {
            let objCoordinate = coordinate.split('_');
            if (objCoordinate.length === 3) {
                data['location_lat'] = objCoordinate[0];
                data['location_lng'] = objCoordinate[1];
                data['location_distance'] = objCoordinate[2];
                data['move_map'] = true;
            }
        }
        ajaxFilterHandler(true);
    });
    jQuery('.search-move-map .fcheckbox').on('click', function () {
        if (jQuery('#st-move-map').length) {
            if (jQuery('#st-move-map').is(':checked')) {
                jQuery('#st-map-coordinate').val("");
                data['location_lat'] = null;
                data['location_lng'] = null;
                data['location_distance'] = null;
                data['move_map'] = false;
                ajaxFilterHandler(true);
            }
        }
    });
    $('.sort-menu input.service_order').on('change', function () {
        data['st_orderby'] = $(this).data('value');
        $(this).closest('.dropdown-menu').slideUp(50);
        ajaxFilterHandler();
    });
    $('.btn-apply-price-range').on('click', function (e) {
        e.preventDefault();
        data['price_range'] = $(this).closest('.range-slider').find('.price_range').val();
        data['page'] = 1;
        ajaxFilterHandler();
    });
    var filter_checkbox = {};
    $('.filter-item').each(function () {
        if (!Object.keys(filter_checkbox).includes($(this).data('type'))) {
            filter_checkbox[$(this).data('type')] = [];
        }
    });
    $('.filter-item').on('change', function () {
        var t = $(this);
        var filter_type = t.data('type');
        if (t.is(':checked')) {
            filter_checkbox[filter_type].push(t.val());
        } else {
            var index = filter_checkbox[filter_type].indexOf(t.val());
            if (index > -1) {
                filter_checkbox[filter_type].splice(index, 1);
            }
        }
        if (filter_checkbox[filter_type].length) {
            data[filter_type] = filter_checkbox[filter_type].toString();
        } else {
            if (typeof data[filter_type] != 'undefined') {
                delete data[filter_type];
            }
        }
        data['page'] = 1;
        ajaxFilterHandler();
    });
    var arrTax = [];
    $('.filter-tax').each(function () {
        if (!Object.keys(arrTax).includes($(this).data('type'))) {
            arrTax[$(this).data('type')] = [];
        }
        if ($(this).is(':checked')) {
            arrTax[$(this).data('type')].push($(this).val());
        }
    });
    $(document).on('click', '.pagination a.page-numbers:not(.current, .dots)', function (e) {
        e.preventDefault();
        var t = $(this);
        var pagUrl = t.attr('href');
        pageNum = 1;
        if (typeof pagUrl !== typeof undefined && pagUrl !== false) {
            var arr = pagUrl.split('/');
            var pageNum = arr[arr.indexOf('page') + 1];
            if (isNaN(pageNum)) {
                pageNum = 1;
            }
            data['page'] = pageNum;
            ajaxFilterHandler();
            if ($('.modern-search-result-popup').length) {
                $('.col-left-map').animate({scrollTop: 0}, 'slow');
            }
            if ($('#modern-result-string').length) {
                window.scrollTo({top: $('#modern-result-string').offset().top - 20, behavior: 'smooth'});
            }
            return false;
        } else {
            return false;
        }
    });
    $('.filter-tax').on('change', function () {
        var t = $(this);
        var filter_type = t.data('type');
        if (t.is(':checked')) {
            arrTax[filter_type].push(t.val());
        } else {
            var index = arrTax[filter_type].indexOf(t.val());
            if (index > -1) {
                arrTax[filter_type].splice(index, 1);
            }
        }
        if (arrTax[filter_type].length) {
            if (typeof data['taxonomy'] == 'undefined')
                data['taxonomy'] = {};
            data['taxonomy[' + filter_type + ']'] = arrTax[filter_type].toString();
        } else {
            if (typeof data['taxonomy'] == 'undefined')
                data['taxonomy'] = {};
            if (typeof data['taxonomy[' + filter_type + ']'] != 'undefined') {
                delete data['taxonomy[' + filter_type + ']'];
            }
        }
        if (Object.keys(data['taxonomy']).length <= 0) {
            delete data['taxonomy'];
        }
        data['page'] = 1;
        ajaxFilterHandler();
    });

    function duplicateData(parent, parentGet) {
        if (typeof data['price_range'] != 'undefined') {
            $('input[name="price_range"]', parent).each(function () {
                var instance = $(this).data("ionRangeSlider");
                var price_range_arr = data['price_range'].split(';');
                if (price_range_arr.length) {
                    instance.update({from: price_range_arr[0], to: price_range_arr[1]});
                }
            });
        }
        var dataFilterItem = [];
        parent.find('.filter-item').prop('checked', false);
        parentGet.find('.filter-item').each(function () {
            var t = $(this);
            if (t.is(':checked')) {
                if (Object.keys(dataFilterItem).includes(t.data('type'))) {
                    dataFilterItem[t.data('type')].push(t.val());
                } else {
                    dataFilterItem[t.data('type')] = [];
                    dataFilterItem[t.data('type')].push(t.val());
                }
            }
        });
        if (Object.keys(dataFilterItem).length) {
            for (var i = 0; i < Object.keys(dataFilterItem).length; i++) {
                var iD = dataFilterItem[Object.keys(dataFilterItem)[i]];
                if (iD.length) {
                    for (var j = 0; j < iD.length; j++) {
                        $('.filter-item[data-type="' + Object.keys(dataFilterItem)[i] + '"][value="' + iD[j] + '"]', parent).prop('checked', true);
                    }
                }
            }
        }
        var dataFilterTax = [];
        parent.find('.filter-tax').prop('checked', false);
        parentGet.find('.filter-tax').each(function () {
            var t = $(this);
            if (t.is(':checked')) {
                if (Object.keys(dataFilterTax).includes(t.data('type'))) {
                    dataFilterTax[t.data('type')].push(t.val());
                } else {
                    dataFilterTax[t.data('type')] = [];
                    dataFilterTax[t.data('type')].push(t.val());
                }
            }
        });
        if (Object.keys(dataFilterTax).length) {
            for (var i = 0; i < Object.keys(dataFilterTax).length; i++) {
                var iD = dataFilterTax[Object.keys(dataFilterTax)[i]];
                if (iD.length) {
                    for (var j = 0; j < iD.length; j++) {
                        $('.filter-tax[data-type="' + Object.keys(dataFilterTax)[i] + '"][value="' + iD[j] + '"]', parent).prop('checked', true);
                    }
                }
            }
        }
    }

    $('.map-view').on('click', function () {
        var parent = $('.map-view-popup .top-filter');
        var parentGet = $('.sidebar-item');
        duplicateData(parent, parentGet);
        $('.map-view-popup').fadeIn();
        $('html, body').css({'overflow': 'hidden'});
        ajaxFilterHandler();
    });
    $('.close-map-view-popup').on('click', function () {
        var parentGet = $('.map-view-popup .top-filter');
        var parent = $('.sidebar-item');
        duplicateData(parent, parentGet);
        $('html, body').css({'overflow': 'auto'});
        $('.map-view-popup').fadeOut();
    });
    $('.toolbar-action-mobile .btn-date').on('click', function (e) {
        e.preventDefault();
        var me = $(this);
        window.scrollTo({top: 0, behavior: 'auto'});
        $('.popup-date').each(function () {
            var t = $(this);
            var checkinOut = t.find('.check-in-out');
            var options = {singleDatePicker: false, autoApply: true, disabledPast: true, dateFormat: t.data('format'), customClass: 'popup-date-custom', onlyShowCurrentMonth: true, alwaysShowCalendars: true,};
            if (typeof locale_daterangepicker == 'object') {
                options.locale = locale_daterangepicker;
            }
            checkinOut.daterangepicker(options, function (start, end, label) {
                me.text(start.format(t.data('format')) + ' - ' + end.format(t.data('format')));
                data['start'] = start.format(t.data('format'));
                data['end'] = end.format(t.data('format'));
                if ($('#modern-result-string').length) {
                    window.scrollTo({top: $('#modern-result-string').offset().top - 20, behavior: 'smooth'});
                }
                ajaxFilterHandler();
                t.hide();
            });
            checkinOut.trigger('click');
            t.fadeIn();
        });
    });
    $('.popup-close').on('click', function () {
        $(this).closest('.st-popup').hide();
    });
    $('.btn-guest-apply', '.popup-guest').on('click', function (e) {
        e.preventDefault();
        var textGuestAdult = '1 Adult';
        var textGuestChild = '0 Children';
        var me = $('.toolbar-action-mobile .btn-guest');
        $('.popup-guest').each(function () {
            var t = $(this);
            var adult_number = $('input[name="adult_number"]', t).val();
            if (parseInt(adult_number) == 1) {
                textGuestAdult = adult_number + ' ' + st_params.text_adult;
            } else {
                textGuestAdult = adult_number + ' ' + st_params.text_adults;
            }
            data['adult_number'] = adult_number;
            me.text(textGuestAdult + ' - ' + textGuestChild);
            var child_number = $('input[name="child_number"]', t).val();
            if (parseInt(child_number) <= 1) {
                textGuestChild = child_number + ' ' + st_params.text_child;
            } else {
                textGuestChild = child_number + ' ' + st_params.text_childs;
            }
            data['child_number'] = child_number;
            me.text(textGuestAdult + ' - ' + textGuestChild);
            data['room_num_search'] = $('input[name="room_num_search"]', t).val();
            $(this).closest('.st-popup').hide();
            ajaxFilterHandler();
        });
    });
    $('.toolbar-action-mobile .btn-guest').on('click', function (e) {
        e.preventDefault();
        $('.popup-guest').each(function () {
            var t = $(this);
            t.fadeIn();
        });
    });
    $('.toolbar-action-mobile .btn-sort').on('click', function (e) {
        e.preventDefault();
        $('.sort-menu-mobile').fadeIn();
    });
    $('.toolbar-action-mobile .btn-map').on('click', function (e) {
        e.preventDefault();
        $('.page-half-map .col-right').show();
        $('.full-map').show();
        ajaxFilterMapHandler();
    });
    $('.toolbar-action-mobile .btn-filter').on('click', function (e) {
        e.preventDefault();
        $('.st-results .top-filter').show();
        ajaxFilterMapHandler();
    });
    $('.close-filter').on('click', function (e) {
        e.preventDefault();
        $('.st-results .top-filter').hide();
        ajaxFilterMapHandler();
    });
    $('.sidebar-filter .close-filter').on('click', function () {
        $(this).closest('.sidebar-filter').fadeOut(function () {
            $('html, body').css({overflow: 'auto'});
        });
    });
    $('.top-filter .close-filter').on('click', function () {
        $(this).closest('.top-filter').fadeOut(function () {
            $('html, body').css({overflow: 'auto'});
        });
    });
    $('.sort-menu-mobile .close-filter').on('click', function () {
        $(this).closest('.sort-menu-mobile').fadeOut();
    });
    $('.close-map, .close-half-map').on('click', function (e) {
        e.preventDefault();
        $('.page-half-map .col-right').hide();
        $('.full-map').hide();
        ajaxFilterMapHandler();
    });
    $('.show-map-mobile').on('click', function () {
        var t = $(this);
        $('.page-half-map').find('.maparea').show();
        $('body').css({'overflow': 'hidden'});
        ajaxFilterMapHandler();
    });
    $('.close-map-new').on('click', function () {
        var t = $(this);
        t.closest('.maparea').fadeOut();
        $('body').css({'overflow': 'auto'});
    });
    $('#btn-show-map-mobile').on('change', function () {
        var t = $(this);
        var pageHalfMap = $('.page-half-map');
        if (t.is(':checked')) {
            pageHalfMap.find('.col-right').show();
            ajaxFilterMapHandler();
        }
    });
    $('#btn-show-map').on('change', function () {
        var t = $(this);
        var pageHalfMap = $('.page-half-map');
        if (t.is(':checked')) {
            pageHalfMap.find('.modern-search-result').css({"height": "calc(100vh - 80px)", "overflow-y": "scroll"});
            pageHalfMap.removeClass('snormal');
            pageHalfMap.find('.col-right').show();
            pageHalfMap.find('.col-left').attr('class', '').addClass('col-md-6 col-left static');
            if (pageHalfMap.find('.col-left .list-style').length) {
                pageHalfMap.find('.col-left .item-service').attr('class', '').addClass('col-md-12 item-service');
            } else {
                pageHalfMap.find('.col-left .item-service').attr('class', '').addClass('col-md-6 col-12 item-service');
            }
            $('.as').slideUp();
            var topEl = $('.st-hotel-result').offset().top;
            var scroll = $(window).scrollTop();
            if (topEl == scroll) {
                setTimeout(function () {
                    $('.page-half-map').find('.col-left').getNiceScroll().remove();
                    $('.page-half-map').find('.col-left').niceScroll();
                    $('.page-half-map').find('.col-left').getNiceScroll().resize();
                }, 500);
            }
            pageHalfMap.find('.col-left').css({'width': '50%'});
        } else {
            pageHalfMap.find('.modern-search-result').css({"height": "auto", "overflow-y": "hidden"});
            pageHalfMap.addClass('snormal');
            pageHalfMap.find('.col-right').hide();
            pageHalfMap.find('.col-left').attr('class', '').addClass('col-12 col-left');
            if (pageHalfMap.find('.col-left .list-style').length) {
                pageHalfMap.find('.col-left .item-service').attr('class', '').addClass('col-md-6 item-service');
            } else {
                pageHalfMap.find('.col-left .item-service').attr('class', '').addClass('col-md-3 col-xs-12 item-service');
            }
            setTimeout(function () {
                $('.has-matchHeight').matchHeight({remove: true});
                $('.has-matchHeight').matchHeight();
            }, 400);
            $('.as').slideDown();
            pageHalfMap.find('.col-left').css({'width': '100%'});
        }
    });
    $('#btn-show-map').on('change', function () {
        var t = $(this);
        if (t.is(':checked')) {
            data['half_map_show'] = 'yes';
            ajaxFilterMapHandler();
        } else {
            data['half_map_show'] = 'no';
            setTimeout(function () {
                if ($('.has-matchHeight').length) {
                    $('.has-matchHeight').matchHeight({remove: true});
                    $('.has-matchHeight').matchHeight();
                }
            }, 100);
        }
        $('.st-hotel-result').find('.col-left').getNiceScroll().remove();
    });

    function ajaxFilterHandler(loadMap = true) {
        if (requestRunning) {
            xhr.abort();
        }
        hasFilter = true;
        $('html, body').css({'overflow': 'auto'});
        if (window.matchMedia('(max-width: 991px)').matches) {
            $('.sidebar-filter').fadeOut();
            if ($('.st-hotel-result').length) {
                window.scrollTo({top: $('.st-hotel-result').offset().top, behavior: 'smooth'});
            }
        }
        if (window.matchMedia('(max-width: 580px)').matches) {
            if ($('.search-result-page.st-style-elementor').length) {
                if (data['layout'] !== 'grid') {
                    data['layout'] = 'grid';
                    data['layout_old'] = 'list';
                }
            }
        } else {
            if ($('.search-result-page.st-style-elementor').length) {
                if (typeof data['layout_old'] !== 'undefined' && data['layout_old'] != '') {
                    data['layout'] = data['layout_old'];
                    data['layout_old'] = '';
                }
            }
        }
        if (window.matchMedia('(max-width: 1199px)').matches) {
            if ($('.search-result-page.st-style-elementor.layout6').length) {
                if (data['layout'] === 'list') {
                    data['change_layout_to'] = 'list';
                }
            }
        } else {
            if ($('.search-result-page.st-style-elementor.layout6').length) {
                if (data['layout'] === 'list') {
                    data['change_layout_to'] = 'list-2';
                }
            }
        }
        $('.filter-loading').show();
        var layout = $('#modern-search-result').data('layout');
        data['format'] = $('#modern-search-result').data('format');
        if ($('#st-layout-fullwidth').length)
            data['fullwidth'] = 1;
        if ($('.modern-search-result-popup').length) {
            data['is_popup_map'] = '1';
        }
        data['action'] = 'st_filter_hotel_ajax';
        data['is_search_page'] = 1;
        data['_s'] = st_params._s;
        if (typeof data['page'] == 'undefined') {
            data['page'] = 1;
        }
        if ($('.search-result-page.layout5, .search-result-page.layout6').length) {
            let wrapper = $('.search-result-page');
            data['version'] = 'elementorv2';
            data['version_layout'] = wrapper.data('layout');
            data['version_format'] = wrapper.data('format');
        }
        var divResult = $('.modern-search-result');
        var divResultString = $('.modern-result-string');
        var divPagination = $('.moderm-pagination');
        $(document).trigger('st_before_search_ajax', [data]);
        divResult.addClass('loading');
        $('.map-content-loading').each(function () {
            $(this).fadeIn();
        });
        xhr = $.ajax({
            url: st_params.ajax_url, dataType: 'json', type: 'get', data: data, success: function (doc) {
                let content = doc.content;
                if ($('.search-result-page.layout5').length) {
                    content += '<div class="pagination moderm-pagination" id="moderm-pagination">' + doc.pag + '</div>';
                } else {
                    divPagination.each(function () {
                        $(this).html(doc.pag);
                    });
                }
                divResult.each(function () {
                    $(this).html(content);
                });
                divResultString.each(function () {
                    $(this).html(doc.count);
                });
                if ($('.modern-search-result-popup').length) {
                    $('.modern-search-result-popup').html(doc.content_popup);
                    if ($('.col-left-map').length) {
                        $('.col-left-map').each(function () {
                            $(this).getNiceScroll().resize();
                        })
                    }
                }
                $('.map-full-height, .full-map-form').each(function () {
                    var t = $(this);
                    var data_map = doc.data_map;
                    if (loadMap && !t.is(':hidden')) {
                        initHalfMap(t, data_map.data_map, data_map.map_lat_center, data_map.map_lng_center, '', data_map.map_icon, data.version, data.move_map);
                    }
                });
            }, complete: function () {
                divResult.removeClass('loading');
                $('.map-content-loading').each(function () {
                    $(this).fadeOut();
                });
                var time = 0;
                divResult.find('img').one("load", function () {
                    $(this).addClass('loaded');
                    if (divResult.find('img.loaded').length === divResult.find('img').length) {
                        if ($('.has-matchHeight').length) {
                            $('.has-matchHeight').matchHeight({remove: true});
                            $('.has-matchHeight').matchHeight();
                        }
                        setTimeout(function () {
                            if ($('.page-half-map .col-left').length) {
                                $('.page-half-map .col-left').each(function () {
                                    $(this).getNiceScroll().resize();
                                })
                            }
                        }, 205);
                        setTimeout(function () {
                            if ($('.page-half-map .col-left-map').length) {
                                $('.page-half-map .col-left-map').getNiceScroll().resize();
                            }
                        }, 205);
                    }
                });
                if (checkClearFilter()) {
                    $('.btn-clear-filter').fadeIn();
                } else {
                    $('.btn-clear-filter').fadeOut();
                }
                requestRunning = false;
            },
        });
        requestRunning = true;
    }

    var resizeMap = 0;
    jQuery(function ($) {
        if (window.matchMedia('(min-width: 992px)').matches) {
            ajaxFilterMapHandler();
        }
    });

    function ajaxFilterMapHandler() {
        var layout = $('#modern-search-result').data('layout');
        if ($('.search-result-page').length) {
            let wrapper = $('.search-result-page');
            if (wrapper.hasClass('layout5') || wrapper.hasClass('layout6')) {
                data['version'] = 'elementorv2';
                data['version_layout'] = wrapper.data('layout');
                data['version_format'] = wrapper.data('format');
            }
        }
        data['action'] = 'st_filter_hotel_map';
        data['is_search_page'] = 1;
        data['_s'] = st_params._s;
        if (typeof data['page'] == 'undefined') {
            data['page'] = 1;
        }
        $('.map-loading').fadeIn();
        $.ajax({
            url: st_params.ajax_url, dataType: 'json', type: 'get', data: data, success: function (doc) {
                $('.map-full-height, .full-map-form').each(function () {
                    var t = $(this);
                    initHalfMap(t, doc.data_map, doc.map_lat_center, doc.map_lng_center, '', doc.map_icon, data.version, data.move_map);
                });
            }, complete: function () {
                $('.map-loading').fadeOut();
                $('.filter-loading').hide();
                resizeMap = 0;
            },
        });
    }

    jQuery(function ($) {
        if (checkClearFilter()) {
            $('.btn-clear-filter').fadeIn();
        } else {
            $('.btn-clear-filter').fadeOut();
        }
        $(document).on('click', '.btn-clear-filter', function () {
            var arrResetTax = [];
            $('.filter-tax').each(function () {
                if (!Object.keys(arrResetTax).includes($(this).data('type'))) {
                    arrResetTax[$(this).data('type')] = [];
                }
                if ($(this).length) {
                    $(this).prop('checked', false);
                }
            });
            if (Object.keys(arrResetTax).length) {
                for (var i = 0; i < Object.keys(arrResetTax).length; i++) {
                    if (typeof data['taxonomy[' + Object.keys(arrResetTax)[i] + ']'] != 'undefined') {
                        delete data['taxonomy[' + Object.keys(arrResetTax)[i] + ']'];
                    }
                }
            }
            if (typeof data['price_range'] != 'undefined') {
                delete data['price_range'];
                $('input[name="price_range"]').each(function () {
                    var sliderPrice = $(this).data("ionRangeSlider");
                    sliderPrice.reset();
                });
            }
            if (typeof data['star_rate'] != 'undefined') {
                delete data['star_rate'];
            }
            if (typeof data['hotel_rate'] != 'undefined') {
                delete data['hotel_rate'];
            }
            if ($('.filter-item').length) {
                $('.filter-item').prop('checked', false);
            }
            if ($('.filter-tax').length) {
                $('.filter-tax').prop('checked', false);
            }
            if ($('.sort-item').length) {
                data['orderby'] = '';
                $('.sort-item').find('input').prop('checked', false);
            }
            $(document).trigger('st_clear_filter_action');
            $(this).fadeOut();
            ajaxFilterHandler();
        });
    });

    function checkClearFilter() {
        if (((typeof data['price_range'] != 'undefined' && data['price_range'].length) || (typeof data['star_rate'] != 'undefined' && data['star_rate'].length) || (typeof data['hotel_rate'] != 'undefined' && data['hotel_rate'].length) || (typeof data['taxonomy[hotel_facilities]'] != 'undefined' && data['taxonomy[hotel_facilities]'].length) || (typeof data['taxonomy[hotel_theme]'] != 'undefined' && data['taxonomy[hotel_theme]'].length) || (typeof data['orderby'] != 'undefined' && data['orderby'] != 'new' && data['orderby'] != '')) && hasFilter) {
            return true;
        } else {
            return false;
        }
    }

    function decodeQueryParam(p) {
        if (!p) return
        return decodeURIComponent(p.replace(/\+/g, ' '));
    }

    function URLToArrayNew() {
        var res = {};
        $('.toolbar .layout span').each(function () {
            if ($(this).hasClass('active')) {
                res['layout'] = $(this).data('value');
            }
        });
        res['orderby'] = '';
        var sPageURL = window.location.search.substring(1);
        var sURLVariables = sPageURL.split('&');
        if (sURLVariables.length) {
            for (var i = 0; i < sURLVariables.length; i++) {
                var sParameterName = sURLVariables[i].split('=');
                if (sParameterName.length) {
                    if (sParameterName.length) {
                        let val = decodeQueryParam(sParameterName[1]);
                        res[decodeURIComponent(sParameterName[0])] = val == 'undefined' ? '' : val;
                    }
                }
            }
        }
        return res;
    }
})(jQuery);
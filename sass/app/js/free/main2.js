;(function ($) {
    "use strict";
    let Header = {
        $body: $('body'), isValidated: {}, init: function () {
            let base = this;
            base._mobile_menu(base.$body);
            base._login_signup(base.$body);
            base._toglesidebar();
            base._moreSidebar();
            base._mobileLocation();
            base._toolTip();
            base._toggleSection(base.$body);
            base._reviewListSingle(base.$body);
            base._matchHeight(base.$body);
            base._likeReview();
            base._languageCurrency();
            base.initPasswordField();
            base._BlogSolo();
        }, _languageCurrency: function () {
            $('.select2-languages').on('change', function () {
                var target = $('option:selected', this).data('target');
                if (target) {
                    window.location.href = target;
                }
            });
            $('.select2-currencies').on('change', function () {
                var target = $('option:selected', this).data('target');
                if (target) {
                    window.location.href = target;
                }
            });
        }, _likeReview: function () {
            $('.st-like-review').on('click', function (e) {
                e.preventDefault();
                var me = $(this);
                var comment_id = me.data('id');
                $.ajax({
                    url: st_params.ajax_url, type: 'post', dataType: 'json', data: {action: 'like_review', comment_ID: comment_id}, success: function (res) {
                        if (res.status) {
                            $('i', me).toggleClass('fa-thumbs-up fa-thumbs-down');
                            if ($('.booking-item-review-rate').length) {
                                $(me).toggleClass('fa-thumbs-up fa-thumbs-down');
                            }
                            if (typeof res.data.like_count != undefined) {
                                res.data.like_count = parseInt(res.data.like_count);
                                me.parent().find('span').html(res.data.like_count);
                            }
                        }
                    }
                });
            });
        }, _reviewListSingle: function (body) {
            $('.review-list', body).on('click', '.show-more', function (ev) {
                ev.preventDefault();
                var parent = $(this).closest('.comment');
                $(this).css('display', 'none');
                $('.review', parent).slideDown(200);
                $('.show-less', parent).css('display', 'block');
            });
            $('.review-list', body).on('click', '.show-less', function (ev) {
                ev.preventDefault();
                var parent = $(this).closest('.comment');
                $(this).css('display', 'none');
                $('.review', parent).slideUp(200);
                $('.show-more', parent).css('display', 'block');
            });
        }, _toggleSection: function (body) {
            body.on('click', '.toggle-section', function (ev) {
                ev.preventDefault();
                var t = $(this);
                var target = t.data('target');
                if ($('.fas', t).length) {
                    $('.fas', t).toggleClass('fa-angle-up fa-angle-down');
                } else {
                    $('i', t).toggleClass('stt-icon-arrow-up stt-icon-arrow-down');
                }
                $('[data-toggle-section="' + target + '"]').slideToggle(200);
                $('.has-matchHeight', body).matchHeight();
            });
        }, initPasswordField() {
            $('.field-password svg').on('click', function () {
                let t = $(this), parent = t.parents('.field-password');
                if (parent.hasClass('ic-view')) {
                    parent.addClass('viewing').find('input').attr('type', 'text');
                    parent.removeClass('ic-view');
                } else {
                    parent.removeClass('viewing').find('input').attr('type', 'password');
                    parent.addClass('ic-view');
                }
            });
        }, _BlogSolo() {
            $(document).on('change', '.search--blog-solo #cat', function () {
                var $form = $(this).closest('form');
                $form.trigger('submit');
            });
        }, _matchHeight: function (body) {
            if ($('.has-matchHeight', body).length) {
                $('.has-matchHeight', body).matchHeight();
            }
        }, _mobileLocation: function () {
            $('.search-form-mobile .dropdown-menu li').on('click', function () {
                var t = $(this);
                var parent = t.closest('.search-form-mobile');
                $('input[name="location_id"]', parent).val(t.data('value'));
                $('input[name="location_name"]', parent).val(t.find('span').text());
            });
        }, _toolTip: function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        }, _moreSidebar: function () {
            if ($('.btn-more-item').length) {
                $('.btn-more-item').each(function () {
                    var t = $(this);
                    var parent = t.closest('.item-content');
                    if (parent.find('ul li').length > 3) {
                        console.log(t);
                        t.show();
                    }
                    t.on('click', function () {
                        var countLi = parent.find('ul li.hidden').length;
                        var max = 3;
                        if (countLi < 3) {
                            max = countLi;
                        }
                        for (var i = 0; i < max; i++) {
                            parent.find('ul li.hidden').eq(0).removeClass('hidden');
                        }
                        var countLi = parent.find('ul li.hidden').length;
                        if (countLi <= 0) {
                            t.hide();
                        }
                    });
                });
            }
            if ($('.btn-more-item-v2').length) {
                $('.btn-more-item-v2').each(function () {
                    var t = $(this);
                    var parent = t.closest('.item-content');
                    if (parent.find('ul li').length > 3) {
                        console.log(t);
                        t.show();
                    }
                    t.on('click', function () {
                        var countLi = parent.find('ul li.hidden').length;
                        var max = 3;
                        if (countLi < 3) {
                            max = countLi;
                        }
                        for (var i = 0; i < max; i++) {
                            parent.find('ul li.hidden').eq(0).removeClass('hidden');
                        }
                        var countLi = parent.find('ul li.hidden').length;
                        if (countLi <= 0) {
                            t.hide();
                        }
                    });
                });
            }
        }, _toglesidebar: function (body) {
            if ($('.sidebar-item').length) {
                $('.sidebar-item').each(function () {
                    var t = $(this);
                    if (t.hasClass('open')) {
                        t.find('.item-content').slideUp();
                    }
                });
            }
            $('.sidebar-item .item-title').on('click', function () {
                var t = $(this);
                t.parent().toggleClass('open');
                t.parent().find('.item-content').slideToggle();
            });
        }, _login_signup: function (body) {
            $('#st-login-form form, #st-login-form-page form', body).on('submit', function (ev) {
                ev.preventDefault();
                var form = $(this), loader = form.closest('.modal-content').find('.loader-wrapper'), message = $('.message-wrapper', form);
                var data = form.serializeArray();
                data.push({name: 'security', value: st_params._s});
                message.html('');
                loader.show();
                $.post(st_params.ajax_url, data, function (respon) {
                    if (typeof respon == 'object') {
                        message.html(respon.message);
                        setTimeout(function () {
                            message.html('');
                        }, 3000);
                        if (respon.status == 1) {
                            setTimeout(function () {
                                if (!$('#st-login-form-page').length) {
                                    $('.st-sign-up').removeClass('active');
                                    $('.st-sign-in').addClass('active');
                                    $('.register-component').removeClass('active');
                                    $('.login-component').addClass('active');
                                }
                                if (typeof respon.redirect !== 'undefined') {
                                    window.location.href = respon.redirect;
                                }
                            }, 3000);
                        }
                    }
                    loader.hide();
                }, 'json');
            });
            $('#st-register-form form', body).on('submit', function (ev) {
                ev.preventDefault();
                var form = $(this), loader = form.closest('.modal-content').find('.loader-wrapper'), message = $('.message-wrapper', form);
                var data = form.serializeArray();
                data.push({name: 'security', value: st_params._s});
                message.html('');
                loader.show();
                $.post(st_params.ajax_url, data, function (respon) {
                    loader.hide();
                    if (typeof respon == 'object') {
                        message.html(respon.message);
                        if (respon.status == 1) {
                            swal({
                                type: 'success', title: respon.message, text: respon.sub_message, showConfirmButton: true, confirmButtonText: 'close', onClose: function () {
                                    $('#st-login-form', body).modal('show');
                                    $('#st-register-form', body).modal('hide');
                                }, allowOutsideClick: false
                            });
                        } else {
                            message.html(respon.message);
                            setTimeout(function () {
                                message.html('');
                            }, 4000);
                        }
                    }
                }, 'json');
            });
            $('#st-forgot-form form, #st-forgot-form-page form', body).on('submit', function (ev) {
                ev.preventDefault();
                var form = $(this), loader = form.closest('.modal-content').find('.loader-wrapper'), message = $('.message-wrapper', form);
                var data = form.serializeArray();
                data.push({name: 'security', value: st_params._s});
                message.html('');
                loader.show();
                $.post(st_params.ajax_url, data, function (respon) {
                    if (typeof respon == 'object') {
                        message.html(respon.message);
                        setTimeout(function () {
                            message.html('');
                        }, 2000);
                    }
                    loader.hide();
                }, 'json');
            });
            $('.toggle-menu--user').on('click', function (ev) {
                ev.preventDefault();
                $('#st-login-form').modal('toggle');
                $('#st-login-form').modal('show');
            });
            $('.open-loss-password', body).on('click', function (ev) {
                ev.preventDefault();
                $('#st-login-form', body).modal('hide');
                $('#st-register-form', body).modal('hide');
                setTimeout(function () {
                    $('#st-forgot-form', body).modal('show');
                }, 500);
            });
            $('.open-login', body).on('click', function (ev) {
                ev.preventDefault();
                $('#st-register-form', body).modal('hide');
                $('#st-forgot-form', body).modal('hide');
                setTimeout(function () {
                    $('#st-login-form', body).modal('show');
                }, 500);
            });
            $('.form-login--solo .open-signup', body).on('click', function (ev) {
                ev.preventDefault();
                $('#st-forgot-form', body).modal('hide');
                $('#st-login-form', body).modal('hide');
                setTimeout(function () {
                    $('#st-register-form', body).modal('show');
                }, 500);
            });
        }, _mobile_menu: function (body) {
            var body = $('body');
            $('.toggle-menu').on('click', function (ev) {
                ev.preventDefault();
                toggleBody($('#st-main-menu'));
                $('#st-main-menu').toggleClass('open');
            });
            $('.back-menu').on('click', function (ev) {
                ev.preventDefault();
                toggleBody($('#st-main-menu'));
                $('#st-main-menu').toggleClass('open');
            });

            function toggleBody(el) {
                if (el.hasClass('open')) {
                    body.css({'overflow': ''});
                } else {
                    body.css({'overflow': 'hidden'});
                }
            }

            $('#st-main-menu .main-menu .menu-item-has-children .fa').on('click', function () {
                if (window.matchMedia("(max-width: 991px)").matches) {
                    $(this).toggleClass('fa-angle-down fa-angle-up');
                    var parent = $(this).closest('.menu-item-has-children');
                    $('>.menu-dropdown', parent).toggle();
                }
            });
            $('#st-main-menu .main-menu .has-mega-menu .fa').on('click', function () {
                if (window.matchMedia("(max-width: 991px)").matches) {
                    $(this).toggleClass('fa-angle-down fa-angle-up');
                    var parent = $(this).closest('.has-mega-menu');
                    $('>.menu-dropdown', parent).toggle();
                }
            });
            body.on('click', function (ev) {
                if ($(ev.target).is('#st-main-menu')) {
                    toggleBody($(ev.target));
                    $('#st-main-menu').toggleClass('open');
                }
            });
        },
    }
    Header.init();
    let ST_Elementor = {
        $body: $('body'), isValidated: {}, init: function () {
            let base = this;
            base._selectTabServiceList();
            base._addWishlist();
        }, _addWishlist: function () {
            $(document).on('click', '.service-add-wishlist.login', function (event) {
                event.preventDefault();
                var t = $(this);
                t.addClass('loading');
                $.ajax({url: st_params.ajax_url, type: "POST", data: {action: "st_add_wishlist", data_id: t.data('id'), data_type: t.data('type')}, dataType: "json",}).done(function (html) {
                    if (html.status == 'true') {
                        if (html.added == 'true') {
                            t.addClass('added');
                        } else {
                            t.removeClass('added');
                        }
                        t.attr('title', html.title);
                    }
                    t.removeClass('loading');
                })
            });
        }, _selectTabServiceList: function () {
            $('.st-list-service').each(function () {
                var t = $(this);
                var dataTabShowVal = $('.st-list-dropdown .header', t).data('value');
                $('.multi-service-wrapper .tab-content.' + dataTabShowVal, t).show();
            });
            $('.st-list-dropdown').each(function () {
                var t = $(this);
                var parent = t.closest('.st-list-service');
                var currentTabList = t.find('.header').data('value');
                $('.list', t).find('li[data-value="' + currentTabList + '"]').hide();
                $('.header', t).on('click', function () {
                    $('.list', t).toggle();
                });
                $('.list li', t).on('click', function () {
                    var me = $(this);
                    $('.list li', t).removeClass('active');
                    me.addClass('active');
                    var dataS = me.data('value');
                    var dataArg = me.data('arg');
                    var datastyleitem = me.data('styleitem');
                    var dataSName = me.text();
                    $('.header span', t).text(dataSName);
                    $('.header', t).attr('data-value', dataS);
                    me.parent().hide();
                    $.ajax({
                        url: st_params.ajax_url, type: "GET", data: {'action': "st_list_of_service_" + dataS, 'dataArg': dataArg, 'datastyleitem': datastyleitem,}, dataType: "json", beforeSend: function () {
                            parent.find('.map-content-loading').css('z-index', 99);
                            parent.find('.map-content-loading').show();
                        }, error: function (jqXHR, textStatus, errorThrown) {
                        }, success: function (res) {
                            parent.find('.map-content-loading').hide();
                        }, complete: function (xhr, status) {
                            if (xhr.responseJSON) {
                                parent.find('.multi-service-wrapper').html(xhr.responseJSON.html).hide().fadeIn(1500);
                                $('.multi-service-wrapper .tab-content', parent).hide();
                                $('.multi-service-wrapper .tab-content.' + dataS, parent).show();
                                setTimeout(function () {
                                    $('.multi-service-wrapper .tab-content .has-matchHeight', parent).matchHeight({remove: true});
                                    $('.multi-service-wrapper .tab-content .has-matchHeight', parent).matchHeight();
                                }, 1000);
                                $('.list li', t).show();
                                $('.list', t).find('li[data-value="' + dataS + '"]').hide();
                            }
                            $('.st-service-slider').each(function () {
                                $(this).owlCarousel({
                                    loop: false,
                                    items: 4,
                                    margin: 20,
                                    responsiveClass: true,
                                    dots: false,
                                    responsive: {0: {items: 1, nav: false, margin: 15, dots: true,}, 576: {items: 2, nav: false, margin: 15, dots: true,}, 992: {items: 3, nav: true,}, 1200: {items: 4, nav: true,}}
                                });
                            });
                        }
                    });
                })
                $(document).on('mouseup', function (e) {
                    var container = t;
                    if (!container.is(e.target) && container.has(e.target).length === 0) {
                        container.find('.list').hide();
                    }
                });
            });
        }, _resize: function (body) {
            var timeout_fixed_item;
            $(window).on('resize', function () {
                clearTimeout(timeout_fixed_item);
                timeout_fixed_item = setTimeout(function () {
                    $('.st-hotel-content', 'body').each(function () {
                        var t = $(this);
                        $(window).on('scroll', function () {
                            if ($(window).scrollTop() >= 50 && window.matchMedia('(max-width: 991px)').matches) {
                                t.css('display', 'flex');
                            } else {
                                t.css('display', 'none');
                            }
                        });
                    });
                }, 1000);
            }).trigger('resize');
            if (window.matchMedia('(min-width: 992px)').matches) {
                $('.st-gallery', body).each(function () {
                    var parent = $(this);
                    var $fotoramaDiv = $('.fotorama', parent).fotorama({width: parent.data('width'), nav: parent.data('nav'), thumbwidth: '135', thumbheight: '135', allowfullscreen: parent.data('allowfullscreen')});
                    parent.data('fotorama', $fotoramaDiv.data('fotorama'));
                });
            } else {
                $('.st-gallery', body).each(function () {
                    var parent = $(this);
                    if (typeof parent.data('fotorama') !== 'undefined') {
                        parent.data('fotorama').destroy();
                    }
                    var $fotoramaDiv = $('.fotorama', parent).fotorama({width: parent.data('width'), nav: parent.data('nav'), thumbwidth: '80', thumbheight: '80', allowfullscreen: parent.data('allowfullscreen')});
                    parent.data('fotorama', $fotoramaDiv.data('fotorama'));
                });
            }
            if (window.matchMedia('(min-width: 992px)').matches) {
                $('.full-map').show();
            } else {
                $('.full-map').hide();
            }
            if (window.matchMedia('(max-width: 991px)').matches) {
                $('.as').slideDown();
            }
        },
    }
    ST_Elementor.init();
    if ($('.payment-form .payment-item').length) {
        $('.payment-form .payment-item').eq(0).find('.st-icheck-item input[type="radio"]').prop('checked', true);
        $('.payment-form .payment-item').eq(0).find('.dropdown-menu').slideDown();
    }
    $('.payment-form .payment-item').each(function (l, i) {
        var parent = $(this);
        $('.st-icheck-item input[type="radio"]', parent).on('change', function () {
            $('.payment-form .payment-item .dropdown-menu').slideUp();
            if ($(this).is(':checked')) {
                if ($('.dropdown-menu', parent).length) {
                    $('.dropdown-menu', parent).slideDown();
                }
            }
        });
    });
})(jQuery);

function stKeyupsmartSearch(event) {
    var input, filter, ul, li, a, i, txtValue;
    input = event.value.toUpperCase();
    filter = event.value.toUpperCase();
    parent = event.closest(".destination-search");
    ul = parent.getElementsByTagName('ul')[0];
    li = ul.getElementsByTagName('li');
    for (i = 0; i < li.length; i++) {
        txtValue = li[i].textContent || li[i].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}

var markerGolbal;
var mapGobal;

function getMapDistance(map) {
    var bounds = map.getBounds();
    var center = bounds.getCenter();
    var ne = bounds.getNorthEast();
    var r = 3963.0;
    var lat1 = center.lat() / 57.2958;
    var lon1 = center.lng() / 57.2958;
    var lat2 = ne.lat() / 57.2958;
    var lon2 = ne.lng() / 57.2958;
    var dis = r * Math.acos(Math.sin(lat1) * Math.sin(lat2) +
        Math.cos(lat1) * Math.cos(lat2) * Math.cos(lon2 - lon1));
    return dis;
}

function initHalfMap(mapEl, mapData, mapLat, mapLng, mapZoom, mapIcon, mapVersion, isMapMove) {
    var popupPos = mapEl.data('popup-position');
    if (mapData.length <= 0)
        mapData = mapEl.data('data_show');
    if (mapLat.length <= 0)
        mapLat = mapEl.data('lat');
    if (mapLng.length <= 0)
        mapLng = mapEl.data('lng');
    if (mapZoom.length <= 0)
        mapZoom = typeof mapEl.data('zoom') !== 'undefined' ? mapEl.data('zoom') : 13;
    if (mapIcon.length <= 0)
        mapIcon = mapEl.data('icon');
    if (typeof isMapMove === 'undefined')
        isMapMove = false;
    if (!isMapMove) {
        mapGobal = new google.maps.Map(mapEl.get(0), {zoom: mapZoom, center: {lat: parseFloat(mapLat), lng: parseFloat(mapLng)}, disableDefaultUI: true});
    }
    if (!isMapMove) {
        bounds = new google.maps.LatLngBounds();
    }
    if (isMapMove) {
        if (markerGolbal.length) {
            for (var i = 0; i < markerGolbal.length; i++) {
                markerGolbal[i].setMap(null);
            }
        }
    }
    if (typeof mapData != 'undefined' && Object.keys(mapData).length) {
        var marker = [];
        var ib = [];
        var c = {};
        markerGolbal = jQuery.map(mapData, function (location, i) {
            if (mapVersion === 'elementorv2') {
                marker[i] = new MarkerWithLabel({
                    icon: " ",
                    position: new google.maps.LatLng(parseFloat(location.lat), parseFloat(location.lng)),
                    draggable: false,
                    raiseOnDrag: false,
                    map: mapGobal,
                    labelContent: '<div class="inner" data-marker-id="' + location.id + '">' + jQuery(location.content_adv_html).find('.item_price_map span').text() + '</div>',
                    labelAnchor: new google.maps.Point(0, 0),
                    labelClass: "stt-price-label"
                });
            } else {
                marker[i] = new google.maps.Marker({
                    position: {lat: parseFloat(location.lat), lng: parseFloat(location.lng)},
                    options: {icon: mapIcon, animation: isMapMove ? google.maps.Animation.NONE : google.maps.Animation.DROP},
                    map: mapGobal
                });
            }
            if (!isMapMove) {
                var loc = new google.maps.LatLng(parseFloat(location.lat), parseFloat(location.lng));
                bounds.extend(loc);
            }
            var ibOptions = {
                content: '',
                disableAutoPan: true,
                maxWidth: 0,
                pixelOffset: new google.maps.Size(-135, 20),
                zIndex: null,
                boxStyle: {padding: "0px 0px 0px 0px", width: "270px",},
                closeBoxURL: "",
                cancelBubble: true,
                infoBoxClearance: new google.maps.Size(1, 1),
                isHidden: false,
                pane: "floatPane",
                enableEventPropagation: true,
                alignBottom: true
            };
            if (window.matchMedia("(min-width: 768px)").matches) {
                if (popupPos == 'right') {
                    ibOptions.pixelOffset = new google.maps.Size(35, -208);
                    ibOptions.alignBottom = false;
                }
            }
            jQuery(window).on('resize', function () {
                if (window.matchMedia("(min-width: 768px)").matches) {
                    if (popupPos == 'right') {
                        ibOptions.pixelOffset = new google.maps.Size(35, -208);
                        ibOptions.alignBottom = false;
                    }
                }
            });
            google.maps.event.addListener(marker[i], 'click', (function () {
                var source = location.content_html;
                var boxText = document.createElement("div");
                if (window.matchMedia("(min-width: 768px)").matches) {
                    if (popupPos == 'right') {
                        boxText.classList.add("right-box");
                    }
                }
                jQuery(window).on('resize', function () {
                    if (window.matchMedia("(min-width: 768px)").matches) {
                        if (popupPos == 'right') {
                            boxText.classList.add("right-box");
                        }
                    } else {
                        boxText.classList.remove("right-box");
                    }
                });
                boxText.style.cssText = "border-radius: 5px; background: #fff; padding: 0px;";
                boxText.innerHTML = source;
                ibOptions.content = boxText;
                var ks = Object.keys(c);
                if (ks.length) {
                    for (var j = 0; j < ks.length; j++) {
                        c[ks[j]].close();
                    }
                }
                ib[i] = new InfoBox(ibOptions);
                c[i] = ib[i];
                ib[i].open(mapGobal, this);
                mapGobal.panTo(ib[i].getPosition());
                google.maps.event.addListener(ib[i], 'domready', function () {
                    var closeInfoBox = document.getElementById("close-popup-on-map");
                    google.maps.event.addDomListener(closeInfoBox, 'click', function () {
                        ib[i].close();
                    });
                });
            }));
            return marker[i];
        });
        if (!isMapMove) {
            customControlGoogleMap(mapEl.get(0), mapGobal);
        }
    }
    if (!isMapMove) {
        mapGobal.fitBounds(bounds);
        mapGobal.panToBounds(bounds);
    }
    var listener = google.maps.event.addListener(mapGobal, "idle", function () {
        if (mapGobal.getZoom() > 16)
            mapGobal.setZoom(16);
        google.maps.event.removeListener(listener);
    });
    if (mapVersion === 'elementorv2') {
        google.maps.event.addListener(mapGobal, "dragend", function (ev) {
            var moveLat = mapGobal.getCenter().lat();
            var moveLng = mapGobal.getCenter().lng();
            if (jQuery('#st-move-map').length) {
                if (jQuery('#st-move-map').is(':checked')) {
                    let distance = getMapDistance(mapGobal);
                    jQuery('#st-map-coordinate').val(moveLat + '_' + moveLng + '_' + distance).change();
                } else {
                    jQuery('#st-map-coordinate').val("");
                }
            }
        });
        google.maps.event.addListener(mapGobal, "zoom_changed", function (ev) {
            var moveLat = mapGobal.getCenter().lat();
            var moveLng = mapGobal.getCenter().lng();
            if (jQuery('#st-move-map').length) {
                if (jQuery('#st-move-map').is(':checked')) {
                    let distance = getMapDistance(mapGobal);
                    jQuery('#st-map-coordinate').val(moveLat + '_' + moveLng + '_' + distance).change();
                } else {
                    jQuery('#st-map-coordinate').val("");
                }
            }
        });
    }
}

var mapStyles = {
    'silver': [{"elementType": "geometry", "stylers": [{"color": "#f5f5f5"}]}, {"elementType": "labels.icon", "stylers": [{"visibility": "off"}]}, {
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#616161"}]
    }, {"elementType": "labels.text.stroke", "stylers": [{"color": "#f5f5f5"}]}, {"featureType": "administrative.land_parcel", "elementType": "labels.text.fill", "stylers": [{"color": "#bdbdbd"}]}, {
        "featureType": "poi",
        "elementType": "geometry",
        "stylers": [{"color": "#eeeeee"}]
    }, {"featureType": "poi", "elementType": "labels.text.fill", "stylers": [{"color": "#757575"}]}, {"featureType": "poi.park", "elementType": "geometry", "stylers": [{"color": "#e5e5e5"}]}, {
        "featureType": "poi.park",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#9e9e9e"}]
    }, {"featureType": "road", "elementType": "geometry", "stylers": [{"color": "#ffffff"}]}, {
        "featureType": "road.arterial",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#757575"}]
    }, {"featureType": "road.highway", "elementType": "geometry", "stylers": [{"color": "#dadada"}]}, {
        "featureType": "road.highway",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#616161"}]
    }, {"featureType": "road.local", "elementType": "labels.text.fill", "stylers": [{"color": "#9e9e9e"}]}, {
        "featureType": "transit.line",
        "elementType": "geometry",
        "stylers": [{"color": "#e5e5e5"}]
    }, {"featureType": "transit.station", "elementType": "geometry", "stylers": [{"color": "#eeeeee"}]}, {"featureType": "water", "elementType": "geometry", "stylers": [{"color": "#c9c9c9"}]}, {
        "featureType": "water",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#9e9e9e"}]
    }],
    'retro': [{"elementType": "geometry", "stylers": [{"color": "#ebe3cd"}]}, {"elementType": "labels.text.fill", "stylers": [{"color": "#523735"}]}, {
        "elementType": "labels.text.stroke",
        "stylers": [{"color": "#f5f1e6"}]
    }, {"featureType": "administrative", "elementType": "geometry.stroke", "stylers": [{"color": "#c9b2a6"}]}, {
        "featureType": "administrative.land_parcel",
        "elementType": "geometry.stroke",
        "stylers": [{"color": "#dcd2be"}]
    }, {"featureType": "administrative.land_parcel", "elementType": "labels.text.fill", "stylers": [{"color": "#ae9e90"}]}, {
        "featureType": "landscape.natural",
        "elementType": "geometry",
        "stylers": [{"color": "#dfd2ae"}]
    }, {"featureType": "poi", "elementType": "geometry", "stylers": [{"color": "#dfd2ae"}]}, {"featureType": "poi", "elementType": "labels.text.fill", "stylers": [{"color": "#93817c"}]}, {
        "featureType": "poi.park",
        "elementType": "geometry.fill",
        "stylers": [{"color": "#a5b076"}]
    }, {"featureType": "poi.park", "elementType": "labels.text.fill", "stylers": [{"color": "#447530"}]}, {
        "featureType": "road",
        "elementType": "geometry",
        "stylers": [{"color": "#f5f1e6"}]
    }, {"featureType": "road.arterial", "elementType": "geometry", "stylers": [{"color": "#fdfcf8"}]}, {
        "featureType": "road.highway",
        "elementType": "geometry",
        "stylers": [{"color": "#f8c967"}]
    }, {"featureType": "road.highway", "elementType": "geometry.stroke", "stylers": [{"color": "#e9bc62"}]}, {
        "featureType": "road.highway.controlled_access",
        "elementType": "geometry",
        "stylers": [{"color": "#e98d58"}]
    }, {"featureType": "road.highway.controlled_access", "elementType": "geometry.stroke", "stylers": [{"color": "#db8555"}]}, {
        "featureType": "road.local",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#806b63"}]
    }, {"featureType": "transit.line", "elementType": "geometry", "stylers": [{"color": "#dfd2ae"}]}, {
        "featureType": "transit.line",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#8f7d77"}]
    }, {"featureType": "transit.line", "elementType": "labels.text.stroke", "stylers": [{"color": "#ebe3cd"}]}, {
        "featureType": "transit.station",
        "elementType": "geometry",
        "stylers": [{"color": "#dfd2ae"}]
    }, {"featureType": "water", "elementType": "geometry.fill", "stylers": [{"color": "#b9d3c2"}]}, {"featureType": "water", "elementType": "labels.text.fill", "stylers": [{"color": "#92998d"}]}],
    'dark': [{"elementType": "geometry", "stylers": [{"color": "#212121"}]}, {"elementType": "labels.icon", "stylers": [{"visibility": "off"}]}, {
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#757575"}]
    }, {"elementType": "labels.text.stroke", "stylers": [{"color": "#212121"}]}, {"featureType": "administrative", "elementType": "geometry", "stylers": [{"color": "#757575"}]}, {
        "featureType": "administrative.country",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#9e9e9e"}]
    }, {"featureType": "administrative.land_parcel", "stylers": [{"visibility": "off"}]}, {
        "featureType": "administrative.locality",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#bdbdbd"}]
    }, {"featureType": "poi", "elementType": "labels.text.fill", "stylers": [{"color": "#757575"}]}, {"featureType": "poi.park", "elementType": "geometry", "stylers": [{"color": "#181818"}]}, {
        "featureType": "poi.park",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#616161"}]
    }, {"featureType": "poi.park", "elementType": "labels.text.stroke", "stylers": [{"color": "#1b1b1b"}]}, {
        "featureType": "road",
        "elementType": "geometry.fill",
        "stylers": [{"color": "#2c2c2c"}]
    }, {"featureType": "road", "elementType": "labels.text.fill", "stylers": [{"color": "#8a8a8a"}]}, {
        "featureType": "road.arterial",
        "elementType": "geometry",
        "stylers": [{"color": "#373737"}]
    }, {"featureType": "road.highway", "elementType": "geometry", "stylers": [{"color": "#3c3c3c"}]}, {
        "featureType": "road.highway.controlled_access",
        "elementType": "geometry",
        "stylers": [{"color": "#4e4e4e"}]
    }, {"featureType": "road.local", "elementType": "labels.text.fill", "stylers": [{"color": "#616161"}]}, {
        "featureType": "transit",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#757575"}]
    }, {"featureType": "water", "elementType": "geometry", "stylers": [{"color": "#000000"}]}, {"featureType": "water", "elementType": "labels.text.fill", "stylers": [{"color": "#3d3d3d"}]}],
    'night': [{"elementType": "geometry", "stylers": [{"color": "#242f3e"}]}, {"elementType": "labels.text.fill", "stylers": [{"color": "#746855"}]}, {
        "elementType": "labels.text.stroke",
        "stylers": [{"color": "#242f3e"}]
    }, {"featureType": "administrative.locality", "elementType": "labels.text.fill", "stylers": [{"color": "#d59563"}]}, {
        "featureType": "poi",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#d59563"}]
    }, {"featureType": "poi.park", "elementType": "geometry", "stylers": [{"color": "#263c3f"}]}, {"featureType": "poi.park", "elementType": "labels.text.fill", "stylers": [{"color": "#6b9a76"}]}, {
        "featureType": "road",
        "elementType": "geometry",
        "stylers": [{"color": "#38414e"}]
    }, {"featureType": "road", "elementType": "geometry.stroke", "stylers": [{"color": "#212a37"}]}, {
        "featureType": "road",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#9ca5b3"}]
    }, {"featureType": "road.highway", "elementType": "geometry", "stylers": [{"color": "#746855"}]}, {
        "featureType": "road.highway",
        "elementType": "geometry.stroke",
        "stylers": [{"color": "#1f2835"}]
    }, {"featureType": "road.highway", "elementType": "labels.text.fill", "stylers": [{"color": "#f3d19c"}]}, {
        "featureType": "transit",
        "elementType": "geometry",
        "stylers": [{"color": "#2f3948"}]
    }, {"featureType": "transit.station", "elementType": "labels.text.fill", "stylers": [{"color": "#d59563"}]}, {
        "featureType": "water",
        "elementType": "geometry",
        "stylers": [{"color": "#17263c"}]
    }, {"featureType": "water", "elementType": "labels.text.fill", "stylers": [{"color": "#515c6d"}]}, {"featureType": "water", "elementType": "labels.text.stroke", "stylers": [{"color": "#17263c"}]}],
    'aubergine': [{"elementType": "geometry", "stylers": [{"color": "#1d2c4d"}]}, {"elementType": "labels.text.fill", "stylers": [{"color": "#8ec3b9"}]}, {
        "elementType": "labels.text.stroke",
        "stylers": [{"color": "#1a3646"}]
    }, {"featureType": "administrative.country", "elementType": "geometry.stroke", "stylers": [{"color": "#4b6878"}]}, {
        "featureType": "administrative.land_parcel",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#64779e"}]
    }, {"featureType": "administrative.province", "elementType": "geometry.stroke", "stylers": [{"color": "#4b6878"}]}, {
        "featureType": "landscape.man_made",
        "elementType": "geometry.stroke",
        "stylers": [{"color": "#334e87"}]
    }, {"featureType": "landscape.natural", "elementType": "geometry", "stylers": [{"color": "#023e58"}]}, {"featureType": "poi", "elementType": "geometry", "stylers": [{"color": "#283d6a"}]}, {
        "featureType": "poi",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#6f9ba5"}]
    }, {"featureType": "poi", "elementType": "labels.text.stroke", "stylers": [{"color": "#1d2c4d"}]}, {
        "featureType": "poi.park",
        "elementType": "geometry.fill",
        "stylers": [{"color": "#023e58"}]
    }, {"featureType": "poi.park", "elementType": "labels.text.fill", "stylers": [{"color": "#3C7680"}]}, {"featureType": "road", "elementType": "geometry", "stylers": [{"color": "#304a7d"}]}, {
        "featureType": "road",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#98a5be"}]
    }, {"featureType": "road", "elementType": "labels.text.stroke", "stylers": [{"color": "#1d2c4d"}]}, {
        "featureType": "road.highway",
        "elementType": "geometry",
        "stylers": [{"color": "#2c6675"}]
    }, {"featureType": "road.highway", "elementType": "geometry.stroke", "stylers": [{"color": "#255763"}]}, {
        "featureType": "road.highway",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#b0d5ce"}]
    }, {"featureType": "road.highway", "elementType": "labels.text.stroke", "stylers": [{"color": "#023e58"}]}, {
        "featureType": "transit",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#98a5be"}]
    }, {"featureType": "transit", "elementType": "labels.text.stroke", "stylers": [{"color": "#1d2c4d"}]}, {
        "featureType": "transit.line",
        "elementType": "geometry.fill",
        "stylers": [{"color": "#283d6a"}]
    }, {"featureType": "transit.station", "elementType": "geometry", "stylers": [{"color": "#3a4762"}]}, {"featureType": "water", "elementType": "geometry", "stylers": [{"color": "#0e1626"}]}, {
        "featureType": "water",
        "elementType": "labels.text.fill",
        "stylers": [{"color": "#4e6d70"}]
    }]
};

function customControlGoogleMap(mapEl, map) {
    var topRightArea = document.createElement('div');
    topRightArea.className = 'google-control-top-right-area';
    var controlFullScreen = document.createElement('div');
    controlFullScreen.className = 'google-control-fullscreen google-custom-control';
    controlFullScreen.innerHTML = '<img src="' + st_list_map_params.icon_full_screen + '" alt="Full Screen"/>';
    topRightArea.appendChild(controlFullScreen);
    var controlCloseFullScreen = document.createElement('div');
    controlCloseFullScreen.className = 'google-control-closefullscreen google-custom-control hide';
    controlCloseFullScreen.innerHTML = '<img src="' + st_list_map_params.icon_close + '" alt="Full Screen"/>';
    topRightArea.appendChild(controlCloseFullScreen);
    var controlMyLocation = document.createElement('div');
    controlMyLocation.className = 'google-control-mylocation google-custom-control';
    controlMyLocation.innerHTML = '<img src="' + st_list_map_params.icon_my_location + '" alt="Full Screen"/>';
    topRightArea.appendChild(controlMyLocation);
    var controlStyles = document.createElement('div');
    controlStyles.className = 'google-control-styles google-custom-control';
    controlStyles.innerHTML = '<img src="' + st_list_map_params.icon_my_style + '" alt="Full Screen"/><div class="google-control-dropdown"><div class="item">Silver</div><div class="item">Retro</div><div class="item">Dark</div><div class="item">Night</div><div class="item">Aubergine</div></div>';
    topRightArea.appendChild(controlStyles);
    var bottomRightArea = document.createElement('div');
    bottomRightArea.className = 'google-control-bottom-right-area';
    var controlZoomIn = document.createElement('div');
    controlZoomIn.className = 'google-control-zoomin google-custom-control';
    controlZoomIn.innerHTML = '<img src="' + st_list_map_params.icon_zoom_in + '" alt="Full Screen"/>';
    bottomRightArea.appendChild(controlZoomIn);
    var controlZoomOut = document.createElement('div');
    controlZoomOut.className = 'google-control-zoomout google-custom-control';
    controlZoomOut.innerHTML = '<img src="' + st_list_map_params.icon_zoom_out + '" alt="Full Screen"/>';
    bottomRightArea.appendChild(controlZoomOut);
    map.controls[google.maps.ControlPosition.RIGHT_TOP].push(topRightArea);
    map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(bottomRightArea);
    controlFullScreen.addEventListener('click', function () {
        controlFullScreen.classList.add('hide');
        controlCloseFullScreen.classList.remove('hide');
        var element = map.getDiv();
        if (element.requestFullscreen) {
            element.requestFullscreen();
        }
        if (element.webkitRequestFullScreen) {
            element.webkitRequestFullScreen();
        }
        if (element.mozRequestFullScreen) {
            element.mozRequestFullScreen();
        }
    });
    controlCloseFullScreen.addEventListener('click', function () {
        controlFullScreen.classList.remove('hide');
        controlCloseFullScreen.classList.add('hide');
        if (document.exitFullscreen)
            document.exitFullscreen(); else if (document.webkitExitFullscreen)
            document.webkitExitFullscreen(); else if (document.mozCancelFullScreen)
            document.mozCancelFullScreen(); else if (document.msExitFullscreen)
            document.msExitFullscreen();
    });
    controlMyLocation.addEventListener('click', function () {
        if (navigator.geolocation)
            navigator.geolocation.getCurrentPosition(function (pos) {
                var latlng = new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude);
                map.setCenter(latlng);
                new google.maps.Marker({position: latlng, icon: mapEl.data().icon, map: map});
            }, function (error) {
                console.log('Can not get your Location');
            });
    });
    controlZoomIn.addEventListener('click', function () {
        var current = map.getZoom();
        map.setZoom(current + 1);
    });
    controlZoomOut.addEventListener('click', function () {
        var current = map.getZoom();
        map.setZoom(current - 1);
    });
    controlStyles.addEventListener('click', function () {
        controlStyles.querySelector('.google-control-dropdown').classList.toggle('show');
    });
    var dropdownStyles = controlStyles.querySelector('.google-control-dropdown');
    var items = dropdownStyles.querySelectorAll('.item');
    for (var i = 0; i < items.length; i++) {
        items[i].addEventListener('click', function () {
            var style = this.textContent.toLowerCase();
            if (mapStyles[style]) {
                map.setOptions({styles: mapStyles[style]});
            }
        });
    }
}

jQuery(function ($) {
    document.querySelectorAll(".select-number-passenger  .st-number  .plus").forEach((input) => input.addEventListener("click", calculate_add));
    document.querySelectorAll(".select-number-passenger  .st-number  .minus").forEach((input) => input.addEventListener("click", calculate_minus));

    function calculate_add() {
        var num_item = $(this).closest('.select-number-passenger');
        var num = num_item.find('.st-input-number').val();
        var max_val = num_item.find('.st-input-number').data('max');
        var value_num = parseInt(num) + 1;
        num_item.find('.st-input-number').val(value_num);
        num_item.find('strong.num').text(value_num);
    }

    function calculate_minus() {
        var num_item = $(this).closest('.select-number-passenger');
        var num = num_item.find('.st-input-number').val();
        var min_val = num_item.find('.st-input-number').data('min');
        if (parseInt(num) > min_val) {
            var value_num = parseInt(num) - 1;
            num_item.find('.st-input-number').val(value_num);
            num_item.find('strong.num').text(value_num);
        }
    }

    $('.owl-tour-program').each(function () {
        var parent = $(this).parent();
        var owl = $(this);
        owl.owlCarousel({loop: false, items: 3, margin: 20, responsiveClass: true, dots: false, nav: false, responsive: {0: {items: 1, margin: 15,}, 992: {items: 2,}, 1200: {items: 3,}}});
        $('.next', parent).on('click', function (ev) {
            ev.preventDefault();
            owl.trigger('next.owl.carousel');
        });
        $('.prev', parent).on('click', function (ev) {
            ev.preventDefault();
            owl.trigger('prev.owl.carousel');
        });
        owl.on('resized.owl.carousel', function () {
            setTimeout(function () {
                if ($('.ovscroll').length) {
                    $.fn.getNiceScroll && $('.ovscroll').getNiceScroll().resize();
                }
            }, 1000);
        });
    });
    $('.owl-tour-program-7').each(function () {
        var parent = $(this).parent();
        var owl = $(this);
        owl.owlCarousel({loop: false, items: 1, margin: 0, responsiveClass: true, dots: false, nav: true, responsive: {0: {items: 1,}, 992: {items: 1,}, 1200: {items: 1,}}});
        owl.on('resized.owl.carousel', function () {
            setTimeout(function () {
                if ($('.ovscroll').length) {
                    $.fn.getNiceScroll && $('.ovscroll').getNiceScroll().resize();
                }
            }, 1000);
        });
    });
    $('.st-list-tour--slide').each(function () {
        $(this).owlCarousel({
            loop: false,
            items: 3,
            margin: 30,
            responsiveClass: true,
            dots: false,
            nav: false,
            responsive: {0: {items: 1, margin: 15, dots: true,}, 768: {items: 2, margin: 30, dots: true,}, 992: {items: 3, margin: 30, dots: false}, 1200: {items: 3,}}
        });
    });
    ;
})
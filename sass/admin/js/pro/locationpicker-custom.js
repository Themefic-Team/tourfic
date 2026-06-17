(function ($) {
    "use strict";

    function tfProGoogleMapsIsReady() {
        return 'undefined' !== typeof window.google &&
            window.google.maps &&
            window.google.maps.Map &&
            window.google.maps.Marker &&
            window.google.maps.LatLng &&
            window.google.maps.Geocoder &&
            window.google.maps.Circle &&
            window.google.maps.MapTypeId &&
            window.google.maps.MapTypeId.ROADMAP &&
            window.google.maps.event &&
            window.google.maps.places &&
            window.google.maps.places.Autocomplete &&
            $.fn.locationpicker;
    }

    function tfProWhenGoogleMapsReady(callback) {
        var retries = 50;
        var resolved = false;
        var resolve = function () {
            if (resolved) {
                return;
            }

            if (tfProGoogleMapsIsReady()) {
                resolved = true;
                callback();
                return;
            }

            if (0 < retries) {
                retries--;
                setTimeout(resolve, 100);
            }
        };

        window.tfProGoogleMapsQueue = window.tfProGoogleMapsQueue || [];
        window.tfProGoogleMapsQueue.push(resolve);
        resolve();
    }

    if(tf_pro_params.map_option === 'googlemap') {
        tfProWhenGoogleMapsReady(function () {
        $(".gmaps .tf--map-osm-wrap").css("height", "300px");
        var tf_location_lat = $(".tf--latitude").val();
        var tf_location_long = $(".tf--longitude").val();
        if (tf_location_lat && tf_location_long) {
            $('.gmaps .tf--map-osm-wrap').locationpicker({
                location: {
                    latitude: tf_location_lat,
                    longitude: tf_location_long
                },
                radius: 10,
                zoom: 15,
                scrollwheel: true,
                inputBinding: {
                    latitudeInput: $('.gmaps .tf--latitude'),
                    longitudeInput: $('.gmaps .tf--longitude'),
                    radiusInput: $('#us3-radius'),
                    locationNameInput: $('.gmaps .tf_gmap_address')
                },
                onchanged: function (currentLocation, radius, isMarkerDropped) {
                    var locationObj = $(this).locationpicker('map').location;

                    if (isMarkerDropped == true) {
                        locationObj.formattedAddress = `${locationObj.addressComponents.addressLine1}, ${locationObj.addressComponents.postalCode}, ${locationObj.addressComponents.stateOrProvince}, ${locationObj.addressComponents.country}`
                        $('.gmaps .tf_gmap_address').val(locationObj.formattedAddress)
                    }
                },
                enableAutocomplete: true,
                addressFormat: 'route',
                enableReverseGeocode: true,
            });
        } else {
            $('.gmaps .tf--map-osm-wrap').locationpicker({
                location: {
                    latitude: 23.722488805300227,
                    longitude: 89.75740541367188
                },
                radius: 10,
                zoom: 14,
                scrollwheel: true,
                inputBinding: {
                    latitudeInput: $('.gmaps .tf--latitude'),
                    longitudeInput: $('.gmaps .tf--longitude'),
                    radiusInput: $('#us3-radius'),
                    locationNameInput: $('.gmaps .tf_gmap_address')
                },
                onchanged: function (currentLocation, radius, isMarkerDropped) {
                    var locationObj = $(this).locationpicker('map').location;

                    if (isMarkerDropped == true) {
                        locationObj.formattedAddress = `${locationObj.addressComponents.addressLine1}, ${locationObj.addressComponents.postalCode}, ${locationObj.addressComponents.stateOrProvince}, ${locationObj.addressComponents.country}`
                        $('.gmaps .tf_gmap_address').val(locationObj.formattedAddress)
                    }
                },
                enableAutocomplete: true,
                enableReverseGeocode: true,

            });
        }
        });
    }
}(jQuery));

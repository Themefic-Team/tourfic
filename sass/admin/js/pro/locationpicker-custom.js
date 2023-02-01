(function ($) {
    "use strict";
    if(tf_pro_params.map_option === 'googlemap') {
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
                zoom: 14,
                scrollwheel: true,
                inputBinding: {
                    latitudeInput: $('.gmaps .tf--latitude'),
                    longitudeInput: $('.gmaps .tf--longitude'),
                    radiusInput: $('#us3-radius'),
                    locationNameInput: $('.gmaps .tf_gmap_address')
                },
                enableAutocomplete: true,
                addressFormat: 'route',
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
                enableAutocomplete: true,

            });
        }
    }
}(jQuery));
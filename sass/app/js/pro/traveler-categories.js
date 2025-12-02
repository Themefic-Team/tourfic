(function($) {
    $(document).ready(function() {

        wp.hooks.addFilter('tf_search_filter_ajax_data', 'tfProNameSpace', function(formData, context) {
            
            if (context.posttype === 'tf_tours') {
                $.each(tf_pro_params.tour_traveler_category, function(index, category) {
                    formData.append(category.traveler_slug, $('#'+category.traveler_slug).val());
                })
            }

            return formData;
        });

        wp.hooks.addFilter('tf_tour_booking_popup_data', 'tfProNameSpace', function(data, selectedPackage) {
            console.log(tf_pro_params.tour_traveler_category);
            
            $.each(tf_pro_params.tour_traveler_category, function(index, category) {
                if (category.traveler_slug && category.traveler_slug !== '') {
                    var traveler = $('#' + category.traveler_slug).val();
                    if (selectedPackage !== undefined) {
                        var $selectedDiv = $('#package-' + selectedPackage).closest('.tf-single-package');
                        traveler = $selectedDiv.find('input[name="' + category.traveler_slug + '"]').val();
                    }
                    
                    data[category.traveler_slug] = traveler;
                }
            })

            return data;
        });

        wp.hooks.addFilter('tf_guest_count', 'tfProNameSpace', function(guest) {
            
            $.each(tf_pro_params.tour_traveler_category, function(index, category) {
                if (category.traveler_slug && category.traveler_slug !== '') {
                    var value = $('#' + category.traveler_slug).val();
                    if (value) {
                        guest += Number(value);
                    }
                }
            });

            return guest;
        });
    });
})(jQuery);
(function($) {
    $(document).ready(function() {

        wp.hooks.addFilter('tf_search_filter_ajax_data', 'tfProNameSpace', function(formData, context) {

            console.log(tf_pro_params.tour_traveler_category);
            if (context.posttype === 'tf_tours') {
                $.each(tf_pro_params.tour_traveler_category, function(index, category) {
                    formData.append(category.traveler_slug, $('#'+category.traveler_slug).val());
                })
            }

            return formData;
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
/**
 * Ajax install Tourfic
 * 
 * @since 1.0
 */
 (function($) {
	
	$(document).ready(function(){	

        $(document).on('click', '.tf-install', function(e) {
            e.preventDefault();

            var current = $(this);
            var plugin_slug = current.attr("data-plugin-slug");

            current.addClass('updating-message').text(tf_pro_params.installing);

            var data = {
                action: 'tf_ajax_install_tourfic',
                _ajax_nonce: tf_pro_params.tf_pro_nonce,
                slug: plugin_slug,
            };

            jQuery.post( tf_pro_params.ajax_url, data, function(response) {
                current.removeClass('updating-message');
                current.addClass('updated-message').text(tf_pro_params.installed);
                current.attr("href", response.data.activateUrl);
            })
            .fail(function() {
                current.removeClass('updating-message').text(tf_pro_params.install_failed);
            })
            .always(function() {
                current.removeClass('install-now updated-message').addClass('activate-now button-primary').text(tf_pro_params.activating);
                current.unbind(e);
                current[0].click();
            });
        });

        /**
         * License Activate
         * 
         * Ajax
         */
         $(document).on('click', '.tf-license-activate #submit', function(e) {
            e.preventDefault();

            // $('.tf-option-form').submit();

            //after 3 seconds page will be reloaded
            // setTimeout(function() {
            //     location.reload();
            // } , 3000);

            var current = $(this);
            
            var license_key = $("input[name='tf_settings[license-key]']").val();
            var license_email = $("input[name='tf_settings[license-email]']").val();
            
            var data = {
                action: 'tf_act_license',
                license_key: license_key,
                license_email: license_email,
            };
            
            jQuery.post( tf_pro_params.ajax_url, data, function(response) {
                //console.log(response);
                //console.log(response.data.activateUrl);
            })
            .success(function(response) {
                //console.log(response);
                location.reload();
            });
        });

        /**
         * License Deactivate
         * 
         * Ajax
         */
        $(document).on('click', '.el-license-container #submit', function(e) {
            e.preventDefault();

            var current = $(this);

            var data = {
                action: 'tf_deact_license',
            };

            jQuery.post( tf_pro_params.ajax_url, data, function(response) {
                //console.log(response);
                //console.log(response.data.activateUrl);
            })
            .success(function(response) {
                location.reload();
            });
        });

    });

    //export tours ajax
    $(document).on('click', '.tf-export-tours-btn', function(e){
        e.preventDefault();
        $.ajax({
            type: "post",
            url: tf_pro_params.ajax_url,
            data: {
                action: "tf_export_tours",
                nonce: tf_pro_params.nonce,
            },
            dataType: 'json',
            beforeSend: function(){
               $('.tf-export-tours-btn').html('Exporting...');
            },
            success: function(response){
                var date = new Date();
                var generated_date = date.getMonth() + '-' + date.getDate() + '-' + date.getFullYear();

                var link               = document.createElement('a');
                    link.href          = 'data:text/csv;charset=utf-8,' + encodeURI(response);
                    link.download      = 'Tours_' + generated_date + '.csv';
                    link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                //clean up
                document.body.removeChild(link);
                $('.tf-export-tours-btn').html('Export');
            },
            complete: function(){
                $('.tf-export-tours-btn').html('Export');
            }
        });
    });
    //export hotels ajax
    $(document).on('click', '.tf-export-hotels-btn', function(e){
        e.preventDefault();
        $.ajax({
            type: "post",
            url: tf_pro_params.ajax_url,
            data: {
                action: "tf_export_hotels",
                nonce: tf_pro_params.nonce,
            },
            beforeSend: function(){
                $('.tf-export-hotels-btn').html('Exporting...');
            },
            success: function(response){
                var date           = new Date();
                var generated_date = date.getMonth() + '-' + date.getDate() + '-' + date.getFullYear();

                var link               = document.createElement('a');
                    link.href          = 'data:text/csv;charset=utf-8,' + encodeURI(response);
                    link.download      = 'Hotels_' + generated_date + '.csv';
                    link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                //clean up
                document.body.removeChild(link);
                $('.tf-export-hotels-btn').html('Export');
            },
            complete: function(){
                $('.tf-export-hotels-btn').html('Export');
            }
        });
    });

    /**
     * Import Tours ajax
     * 
     * @author Abu Hena
     * @since 2.9.9
     */
    $(document).on('click', '.tf_import_tours_btn', function(e){
        e.preventDefault();
        let formData          = $('#tf-import-tours').serializeArray();
        let tour_csv_file_url = $('#tf-import-tours').find('input[name="tour_csv_file_url"]').val();
        let import_csv_nonce  = $('#tf-import-tours').find('input[name="import_csv_nonce"]').val();
        $('.tf-column-mapping-form').hide();
        
        $.ajax({
            type: 'post',
            url: ajaxurl,
            data:{
                action: 'tf_import_tours',
                form_data: formData,
                tour_csv_file_url: tour_csv_file_url,
                import_csv_nonce: import_csv_nonce,
            },
            beforeSend: function(){
                $('.tf-step-1').addClass('active');
                $('.tf-step-2').addClass('done');
                $('.tf-step-3').addClass('done');
                $('.tf-importing-progressbar-container').show();
            },
            success: function(response){
                //get the percentage value from response
                if( response.success ){
                    console.log(response);
                    let percentage = response.data.imported_percentage;
                    console.log(percentage);
                    $('.tf-importing-progressbar-container').show();
                    $('.tf-importing-progressbar').css('width', percentage + '%');

                    if( percentage == 100 ){
                        $('.tf-importing-progressbar-container').hide();
                        $('.tf_import_tours_btn').html('Import');
                        $('.tf-step-3').addClass('active');
                    }
                }
            },
            complete: function(){
                $('.tf_import_tours_btn').html('Import');
                $('.tf-step-4').addClass('done');
                $('.tf-importing-progressbar-container').hide();
                $('.tf-import-complete-wrap').show();
            },
        });

    });

    /**
     * Import Hotels ajax
     * @author Abu Hena
     */
    $(document).on('click', '.tf_import_hotels_btn', function(e){
        e.preventDefault();
        let formData           = $('#tf-import-hotels').serializeArray();
        let hotel_csv_file_url = $('#tf-import-hotels').find('input[name="hotel_csv_file_url"]').val();
        let import_csv_nonce   = $('#tf-import-hotels').find('input[name="import_csv_nonce"]').val();
        $('.tf-column-mapping-form').hide();
        
        $.ajax({
            type: 'post',
            url: ajaxurl,
            data:{
                action: 'tf_import_hotels',
                form_data: formData,
                hotel_csv_file_url: hotel_csv_file_url,
                import_csv_nonce: import_csv_nonce,
            },
            beforeSend: function(){
                $('.tf-step-1').addClass('done');
                $('.tf-step-2').addClass('done');
                $('.tf-step-3').addClass('done');
                $('.tf-importing-progressbar-container').show();
            },
            success: function(response){
                console.log(response);
                //get the percentage value from response
                if( response.success ){
                    console.log(response.data.post_meta);
                    let percentage = response.data.imported_percentage;
                }
            },
            complete: function(){
                $('.tf_import_hotels_btn').html('Import');
                $('.tf-step-4').addClass('done');
                $('.tf-importing-progressbar-container').hide();
                $('.tf-import-complete-wrap').show();
            },
        });

    });

    let urlParams = new URLSearchParams(window.location.search);
    let mapping   = urlParams.get('step');
    if( mapping == 'tour_mapping' ){
        $('.tf-step-1').addClass('done');
        $('.tf-step-2').addClass('active');
    }
    if( mapping == 'hotel_mapping' ){
        $('.tf-step-1').addClass('done');
        $('.tf-step-2').addClass('active');
    }



})(jQuery);
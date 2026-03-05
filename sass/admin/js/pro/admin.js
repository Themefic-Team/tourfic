(function($) {
    'use strict';
    $(document).ready(function () {
        // Create an instance of Notyf
        const notyf = new Notyf({
            ripple: true,
            duration: 3000,
            dismissable: true,
            position: {
                x: 'right',
                y: 'bottom',
            },
        });	

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
                current[0].trigger("click");
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
            })
            .success(function(response) {
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
            })
            .success(function(response) {
                location.reload();
            });
        });

        //export tours ajax
        $(document).on('submit', '.tf-tours-export-form', function(e){
            e.preventDefault();

            let form = $(this);
            let btn = form.find('.tf-admin-btn');
            let formData = new FormData(form[0]);
                formData.append('action', 'tf_export_tours');

            $.ajax({
                type: "post",
                url: tf_pro_params.ajax_url,
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function(){
                    btn.addClass('tf-btn-loading');
                },
                success: function(response){
                    if(response.success == false){
                        notyf.error(response.data)
                    } else {
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
                    }
                    btn.removeClass('tf-btn-loading');
                },
                complete: function(){
                    btn.removeClass('tf-btn-loading');
                }
            });
        });

        //export hotels ajax
        $(document).on('submit', '.tf-hotels-export-form', function(e){
            e.preventDefault();

            let form = $(this);
            let btn = form.find('.tf-admin-btn');
            let formData = new FormData(form[0]);
                formData.append('action', 'tf_export_hotels');

            $.ajax({
                type: "post",
                url: tf_pro_params.ajax_url,
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function(){
                    btn.addClass('tf-btn-loading');
                },
                success: function(response){
                    if(response.success == false){
                        notyf.error(response.data)
                    } else {
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
                        // notyf.success(response.message);
                    }
                    btn.removeClass('tf-btn-loading');
                },
                complete: function(){
                    btn.removeClass('tf-btn-loading');
                }
            });
        });

        //export apartments ajax
        $(document).on('submit', '.tf-apartments-export-form', function(e){
            e.preventDefault();
            
            let form = $(this);
            let btn = form.find('.tf-admin-btn');
            let formData = new FormData(form[0]);
                formData.append('action', 'tf_export_apartments');

            $.ajax({
                type: "post",
                url: tf_pro_params.ajax_url,
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function(){
                    btn.addClass('tf-btn-loading');
                },
                success: function(response){
                    if(response.success == false){
                        notyf.error(response.data)
                    } else {
                        var date           = new Date();
                        var generated_date = date.getMonth() + '-' + date.getDate() + '-' + date.getFullYear();

                        var link               = document.createElement('a');
                            link.href          = 'data:text/csv;charset=utf-8,' + encodeURI(response);
                            link.download      = 'Apartments_' + generated_date + '.csv';
                            link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        //clean up
                        document.body.removeChild(link);
                    }
                    btn.removeClass('tf-btn-loading');
                },
                complete: function(){
                    btn.removeClass('tf-btn-loading');
                }
            });
        });

        //export cars ajax
        $(document).on('submit', '.tf-cars-export-form', function(e){
            e.preventDefault();
            
            let form = $(this);
            let btn = form.find('.tf-admin-btn');
            let formData = new FormData(form[0]);
                formData.append('action', 'tf_export_cars');

            $.ajax({
                type: "post",
                url: tf_pro_params.ajax_url,
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function(){
                    btn.addClass('tf-btn-loading');
                },
                success: function(response){
                    if(response.success == false){
                        notyf.error(response.data)
                    } else {
                        var date           = new Date();
                        var generated_date = date.getMonth() + '-' + date.getDate() + '-' + date.getFullYear();

                        var link               = document.createElement('a');
                            link.href          = 'data:text/csv;charset=utf-8,' + encodeURI(response);
                            link.download      = 'Cars_' + generated_date + '.csv';
                            link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        //clean up
                        document.body.removeChild(link);
                    }
                    btn.removeClass('tf-btn-loading');
                },
                complete: function(){
                    btn.removeClass('tf-btn-loading');
                }
            });
        });

        /**
         * Import Tours ajax
         * 
         * @since 2.9.9
         */
        $(document).on('click', '.tf_import_tours_btn', function(e){
            e.preventDefault();
            let formData                        = $('#tf-import-tours').serializeArray();
            let tour_csv_file_url               = $('#tf-import-tours').find('input[name="tour_csv_file_url"]').val();
            let import_csv_nonce                = $('#tf-import-tours').find('input[name="import_csv_nonce"]').val();
            let tf_import_tours_update_existing = $('#tf-import-tours').find('input[name="tf_import_tours_update_existing"]').val();
            $('.tf-column-mapping-form').hide();
            $('.tf-import-progress-bar-wrap').addClass('tf-progress-loader').show();
            
            function importNextRow(currentRow = 1) {
                $.ajax({
                    type: 'post',
                    url: ajaxurl,
                    data:{
                        action: 'tf_import_tours',
                        form_data: formData,
                        current_row: currentRow,
                        tour_csv_file_url: tour_csv_file_url,
                        import_csv_nonce: import_csv_nonce,
                        tf_import_tours_update_existing: tf_import_tours_update_existing,
                    },
                    beforeSend: function(){
                        $('.tf-step-1').addClass('active');
                        $('.tf-step-2').addClass('done');
                        $('.tf-step-3').addClass('done');
                        $('.tf-importing-progressbar-container').show();
                        $('.tf-import-log-content').append('<li class="tf-importing-msg">Importing...</li>');
                    },
                    success: function(response){
                        if (response.success) {
                            var currentRow = response.data.current_row;
                            var totalPosts = response.data.total_posts;
                            var log = response.data.log;
    
                            $('.tf-import-progress-bar').text(currentRow + '/' + totalPosts);
    
                            // Calculate progress percentage for the width
                            var progress = (currentRow / totalPosts) * 100;
                            $('.tf-import-progress-bar').css('width', progress + '%');
                            $('.tf-importing-msg').remove();
                            $('.tf-import-log-content').append('<li>' + log + '</li>');
                            $('.tf-import-log-box').scrollTop($('.tf-import-log-box')[0].scrollHeight);

                            if (currentRow < totalPosts) {
                                importNextRow(currentRow + 1);
                            } else if (currentRow === totalPosts) {
                                $('.tf-import-progress-bar-wrap').removeClass('tf-progress-loader');
                                $('.tf-importing-msg').remove();
                                $('.tf-import-log-content').append('<li>Import completed!</li>');
                                $('.tf-step-4').addClass('done');
                                $('.tf-importing-progressbar-container').find('.tf-admin-btn').show();
                            }
                        } else {
                            $('.tf-import-log-content').append('<li>Error: ' + response.data + '</li>');
                        }
                    },
                    error: function(response) {
                        $('.tf-importing-msg').remove();
                        $('.tf-import-log-content').append('<li>Error: ' + response.statusText + '</li>');
                    }
                });
            }

            // Start the import process
            importNextRow(1);
        });

        /**
         * Import Hotels ajax
         */
        $(document).on('click', '.tf_import_hotels_btn', function(e){
            e.preventDefault();
            let formData                         = $('#tf-import-hotels').serializeArray();
            let hotel_csv_file_url               = $('#tf-import-hotels').find('input[name="hotel_csv_file_url"]').val();
            let import_csv_nonce                 = $('#tf-import-hotels').find('input[name="import_csv_nonce"]').val();
            let tf_import_hotels_update_existing = $('#tf-import-hotels').find('input[name="tf_import_hotels_update_existing"]').val();
            $('.tf-column-mapping-form').hide();
            $('.tf-import-progress-bar-wrap').addClass('tf-progress-loader').show();

            function importNextRow(currentRow = 1) {
                $.ajax({
                    type: 'post',
                    url: ajaxurl,
                    data:{
                        action: 'tf_import_hotels',
                        form_data: formData,
                        current_row: currentRow,
                        hotel_csv_file_url: hotel_csv_file_url,
                        import_csv_nonce: import_csv_nonce,
                        tf_import_hotels_update_existing: tf_import_hotels_update_existing,
                    },
                    beforeSend: function(){
                        $('.tf-step-1').addClass('active');
                        $('.tf-step-2').addClass('done');
                        $('.tf-step-3').addClass('done');
                        $('.tf-importing-progressbar-container').show();
                        $('.tf-import-log-content').append('<li class="tf-importing-msg">Importing...</li>');
                    },
                    success: function(response){
                        if (response.success) {
                            var currentRow = response.data.current_row;
                            var totalPosts = response.data.total_posts;
                            var log = response.data.log;
    
                            $('.tf-import-progress-bar').text(currentRow + '/' + totalPosts);
    
                            // Calculate progress percentage for the width
                            var progress = (currentRow / totalPosts) * 100;
                            $('.tf-import-progress-bar').css('width', progress + '%');
                            $('.tf-importing-msg').remove();
                            $('.tf-import-log-content').append('<li>' + log + '</li>');
                            $('.tf-import-log-box').scrollTop($('.tf-import-log-box')[0].scrollHeight);

                            if (currentRow < totalPosts) {
                                importNextRow(currentRow + 1);
                            } else if (currentRow === totalPosts) {
                                $('.tf-import-progress-bar-wrap').removeClass('tf-progress-loader');
                                $('.tf-importing-msg').remove();
                                $('.tf-import-log-content').append('<li>Import completed!</li>');
                                $('.tf-step-4').addClass('done');
                                $('.tf-importing-progressbar-container').find('.tf-admin-btn').show();
                            }
                        } else {
                            $('.tf-import-log-content').append('<li>Error: ' + response.data + '</li>');
                        }
                    },
                    error: function(response) {
                        $('.tf-importing-msg').remove();
                        $('.tf-import-log-content').append('<li>Error: ' + response.statusText + '</li>');
                    }
                });
            }

            // Start the import process
            importNextRow(1);
        });

        /**
         * Import Aprtments ajax
         * @author Jahid
         */
        $(document).on('click', '.tf_import_apartments_btn', function(e){
            e.preventDefault();
            let formData                         = $('#tf-import-apartments').serializeArray();
            let apartment_csv_file_url               = $('#tf-import-apartments').find('input[name="apartment_csv_file_url"]').val();
            let import_csv_nonce                 = $('#tf-import-apartments').find('input[name="import_csv_nonce"]').val();
            let tf_import_apartments_update_existing = $('#tf-import-apartments').find('input[name="tf_import_apartments_update_existing"]').val();
            $('.tf-column-mapping-form').hide();
            $('.tf-import-progress-bar-wrap').addClass('tf-progress-loader').show();
            
            function importNextRow(currentRow = 1) {
                $.ajax({
                    type: 'post',
                    url: ajaxurl,
                    data:{
                        action: 'tf_import_apartments',
                        form_data: formData,
                        current_row: currentRow,
                        apartment_csv_file_url: apartment_csv_file_url,
                        import_csv_nonce: import_csv_nonce,
                        tf_import_apartments_update_existing: tf_import_apartments_update_existing,
                    },
                    beforeSend: function(){
                        $('.tf-step-1').addClass('active');
                        $('.tf-step-2').addClass('done');
                        $('.tf-step-3').addClass('done');
                        $('.tf-importing-progressbar-container').show();
                        $('.tf-import-log-content').append('<li class="tf-importing-msg">Importing...</li>');
                    },
                    success: function(response){
                        if (response.success) {
                            var currentRow = response.data.current_row;
                            var totalPosts = response.data.total_posts;
                            var log = response.data.log;
    
                            $('.tf-import-progress-bar').text(currentRow + '/' + totalPosts);
    
                            // Calculate progress percentage for the width
                            var progress = (currentRow / totalPosts) * 100;
                            $('.tf-import-progress-bar').css('width', progress + '%');
                            $('.tf-importing-msg').remove();
                            $('.tf-import-log-content').append('<li>' + log + '</li>');
                            $('.tf-import-log-box').scrollTop($('.tf-import-log-box')[0].scrollHeight);

                            if (currentRow < totalPosts) {
                                importNextRow(currentRow + 1);
                            } else if (currentRow === totalPosts) {
                                $('.tf-import-progress-bar-wrap').removeClass('tf-progress-loader');
                                $('.tf-importing-msg').remove();
                                $('.tf-import-log-content').append('<li>Import completed!</li>');
                                $('.tf-step-4').addClass('done');
                                $('.tf-importing-progressbar-container').find('.tf-admin-btn').show();
                            }
                        } else {
                            $('.tf-import-log-content').append('<li>Error: ' + response.data + '</li>');
                        }
                    },
                    error: function(response) {
                        $('.tf-importing-msg').remove();
                        $('.tf-import-log-content').append('<li>Error: ' + response.statusText + '</li>');
                    }
                });
            }

            // Start the import process
            importNextRow(1);
        });

        /**
         * Import Cars ajax
         * @author Jahid
         */
        $(document).on('click', '.tf_import_cars_btn', function(e){
            e.preventDefault();
            let formData                         = $('#tf-import-cars').serializeArray();
            let car_csv_file_url               = $('#tf-import-cars').find('input[name="car_csv_file_url"]').val();
            let import_csv_nonce                 = $('#tf-import-cars').find('input[name="import_csv_nonce"]').val();
            let tf_import_cars_update_existing = $('#tf-import-cars').find('input[name="tf_import_cars_update_existing"]').val();
            $('.tf-column-mapping-form').hide();
            $('.tf-import-progress-bar-wrap').addClass('tf-progress-loader').show();
            
            function importNextRow(currentRow = 1) {
                $.ajax({
                    type: 'post',
                    url: ajaxurl,
                    data:{
                        action: 'tf_import_cars',
                        form_data: formData,
                        current_row: currentRow,
                        car_csv_file_url: car_csv_file_url,
                        import_csv_nonce: import_csv_nonce,
                        tf_import_cars_update_existing: tf_import_cars_update_existing,
                    },
                    beforeSend: function(){
                        $('.tf-step-1').addClass('active');
                        $('.tf-step-2').addClass('done');
                        $('.tf-step-3').addClass('done');
                        $('.tf-importing-progressbar-container').show();
                        $('.tf-import-log-content').append('<li class="tf-importing-msg">Importing...</li>');
                    },
                    success: function(response){
                        if (response.success) {
                            var currentRow = response.data.current_row;
                            var totalPosts = response.data.total_posts;
                            var log = response.data.log;
    
                            $('.tf-import-progress-bar').text(currentRow + '/' + totalPosts);
    
                            // Calculate progress percentage for the width
                            var progress = (currentRow / totalPosts) * 100;
                            $('.tf-import-progress-bar').css('width', progress + '%');
                            $('.tf-importing-msg').remove();
                            $('.tf-import-log-content').append('<li>' + log + '</li>');
                            $('.tf-import-log-box').scrollTop($('.tf-import-log-box')[0].scrollHeight);

                            if (currentRow < totalPosts) {
                                importNextRow(currentRow + 1);
                            } else if (currentRow === totalPosts) {
                                $('.tf-import-progress-bar-wrap').removeClass('tf-progress-loader');
                                $('.tf-importing-msg').remove();
                                $('.tf-import-log-content').append('<li>Import completed!</li>');
                                $('.tf-step-4').addClass('done');
                                $('.tf-importing-progressbar-container').find('.tf-admin-btn').show();
                            }
                        } else {
                            $('.tf-import-log-content').append('<li>Error: ' + response.data + '</li>');
                        }
                    },
                    error: function(response) {
                        $('.tf-importing-msg').remove();
                        $('.tf-import-log-content').append('<li>Error: ' + response.statusText + '</li>');
                    }
                });
            }

            // Start the import process
            importNextRow(1);
        });

        /**
         * Reset Google Calendar Access Token
         */
        $(document).on('click', '.tf-reset-calendar-token', function (e) {
            e.preventDefault();
            let btn = $(this);

            $.ajax({
                url: tf_pro_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'tf_google_calendar_reset_token',
                    _nonce: tf_pro_params.tf_pro_nonce,
                },
                beforeSend: function (response) {
                    btn.addClass('tf-btn-loading');
                },
                success: function (response) {
                    location.reload();
                },
            });

        });

        /**
         * Woocommerce Order Sync to Google Calendar
         */
        $(document).on('click', '.tf-google-calendar-sync', function (e) {
            e.preventDefault();
            let btn = $(this);

            $.ajax({
                url: tf_pro_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'tf_google_calendar_sync',
                    type: $(this).attr('data-bookingtype'),
                    _nonce: tf_pro_params.tf_pro_nonce,
                },
                beforeSend: function (response) {
                    btn.addClass('tf-btn-loading');
                },
                success: function (response) {
                    btn.removeClass('tf-btn-loading');
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
        if( mapping == 'apartment_mapping' ){
            $('.tf-step-1').addClass('done');
            $('.tf-step-2').addClass('active');
        }

        if($('.tf-export-select2').length > 0){
            $('.tf-export-select2').select2();
        }
    });

})(jQuery);
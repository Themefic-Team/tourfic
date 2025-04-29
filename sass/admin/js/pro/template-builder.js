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

        // Open popup when "Add New" button is clicked
        $(document).on('click', '.post-type-tf_template_builder .page-title-action', function(e) {
            e.preventDefault();
            tf_open_template_popup();
        });
        
        // Prevent default behavior when clicking title link
        $(document).on('click', '.post-type-tf_template_builder .row-title', function(e) {
            e.preventDefault();
            var post_id = $(this).closest('tr').attr('id').replace('post-', '');
            tf_load_template_data(post_id);
        });
        
        // Open popup when "Edit" action is clicked
        $(document).on('click', '.post-type-tf_template_builder .row-actions .edit a', function(e) {
            e.preventDefault();
            var post_id = $(this).closest('tr').attr('id').replace('post-', '');
            tf_load_template_data(post_id);
        });
        
        // Handle form submission
        $('#tf-template-builder-form').on('submit', function(e) {
            e.preventDefault();
            tf_save_template();
        });
        
        // Edit with Elementor button
        $('#tf-edit-with-elementor').on('click', function() {
            var post_id = $('#tf-post-id').val();
            if (post_id > 0) {
                window.location.href = ajaxurl + '?action=elementor&post=' + post_id;
            }
        });

        $(document).on("click", '.tf-modal-close', function () {
            tf_close_template_popup();
        });
        $(document).on("click", function (event) {
            if (!$(event.target).closest(".tf-modal-content,.page-title-action").length) {
                tf_close_template_popup();
            }
        });
        
        function tf_load_template_data(post_id) {
            $.ajax({
                url: tf_pro_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'tf_get_template_data',
                    post_id: post_id,
                    nonce: tf_pro_params.tf_pro_nonce
                },
                beforeSend: function() {
                    // Show loading indicator
                },
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        $('#tf-post-id').val(data.ID);
                        $('#tf-template-name').val(data.post_title);
                        $('#tf-template-service').val(data.tf_template_service);
                        $('#tf-template-type').val(data.tf_template_type);
                        $('#tf-template-active').prop('checked', data.tf_template_active == '1');
                        $('[name="tf_template_hotel_archive"]').prop('checked', data.tf_template_hotel_archive);
                        
                        tf_open_template_popup();
                    }
                }
            });
        }
        
        function tf_save_template() {
            var form_data = $('#tf-template-builder-form').serialize();
            
            $.ajax({
                url: tf_pro_params.ajax_url,
                type: 'POST',
                data: form_data + '&nonce=' + tf_pro_params.tf_pro_nonce,
                beforeSend: function() {
                    $('#tf-save-template').addClass('tf-btn-loading');
                },
                success: function(response) {
                    tf_close_template_popup();
                    $('#tf-save-template').addClass('tf-btn-loading');
                    if (response.success) {
                        notyf.success(response.data.message);
                        window.location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    // Handle error
                    notyf.error('Error saving template: ' + error);
                    $('#tf-save-template').removeClass('tf-btn-loading');
                }
            });
        }

        
        function tf_open_template_popup() {
            $('#tf-template-builder-popup').addClass('tf-modal-show');
            $('body').addClass('tf-modal-open');
        }
        function tf_close_template_popup() {
            $('#tf-template-builder-popup').removeClass('tf-modal-show');
            $('body').removeClass('tf-modal-open');
            tf_reset_form();
        }
        function tf_reset_form() {
            $('#tf-post-id').val('');
            $('#tf-template-name').val('');
            $('#tf-template-service').val($('#tf-template-service option:first').val());
            $('#tf-template-type').val($('#tf-template-type option:first').val());
            $('#tf-template-active').prop('checked', false);
            $('[name="tf_template_hotel_archive"]').prop('checked', 'blank');
        }
    });
})(jQuery);
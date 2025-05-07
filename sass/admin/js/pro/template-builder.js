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
        
        // Open popup when "Edit" action or row title is clicked
        $(document).on('click', '.post-type-tf_template_builder .row-title, .post-type-tf_template_builder .row-actions .edit a', function(e) {
            e.preventDefault();
            var post_id = $(this).closest('tr').attr('id').replace('post-', '');
            tf_load_template_data(post_id);
        });
        
        // Handle form submission
        $(document).on('submit', '#tf-template-builder-form', function(e) {
            e.preventDefault();
            tf_save_template();
        });
        
        // Edit with Elementor button
        $(document).on('click', '#tf-edit-with-elementor', function(e) {
            e.preventDefault();
            tf_save_template(true);
        });

        $(document).on("click", '.tf-modal-close', function () {
            tf_close_template_popup();
        });
        $(document).on("click", function (event) {
            if (!$(event.target).closest(".tf-modal-content,.page-title-action").length) {
                tf_close_template_popup();
            }
        });

        // Template update based on service & type
        $(document).on('change', '#tf-template-service, #tf-template-type', function(e) {
            e.preventDefault();
            var service = $('#tf-template-service').val();
            var type = $('#tf-template-type').val();
            
            $.ajax({
                url: tf_pro_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'tf_get_template_options',
                    service: service,
                    type: type,
                    nonce: tf_pro_params.tf_pro_nonce
                },
                beforeSend: function() {
                    $('.tf-template-preview-loader').show();
                    $('select[name="tf_taxonomy_type"]').attr('disabled', 'disabled');
                    $('select[name="tf_taxonomy_term"]').attr('disabled', 'disabled');
                },
                success: function(response) {
                    $('.tf-template-preview-loader').hide();
                    $('select[name="tf_taxonomy_type"]').removeAttr('disabled');
                    $('.tf-field-term').hide();
                    $('select[name="tf_taxonomy_term"]').removeAttr('disabled');
                    $('select[name="tf_taxonomy_term"]').html('');
                    if (response.success) {
                        // Update the template options markup
                        $('.tf-field-imageselect').html(response.data.markup);
                        $('.tf-field-taxonomy').html(response.data.taxonomy_markup);
                        
                        // Add subtitle if single template
                        // if (type === 'single') {
                        //     $('.tf-field-imageselect').append('<p class="tf-field-subtitle">You have the option to override this from the settings specific to each individual page.</p>');
                        // } else {
                        //     $('.tf-field-imageselect .tf-field-subtitle').remove();
                        // }
                    }
                },
                error: function(xhr, status, error) {
                    notyf.error('Error loading template options: ' + error);
                    $('.tf-template-preview-loader').hide();
                    $('select[name="tf_taxonomy_type"]').removeAttr('disabled');
                    $('.tf-field-term').hide();
                    $('select[name="tf_taxonomy_term"]').removeAttr('disabled');
                    $('select[name="tf_taxonomy_term"]').html('');
                    // Fallback to blank option
                    $('.tf-field-imageselect').html(`
                        <label class="tf-field-label">${type == 'single' ? 'Single Template' : 'Archive Template'}</label>
                        <div class="tf-fieldset">
                            <ul class="tf-image-radio-group">
                                <li class="">
                                    <label class="tf-image-checkbox">
                                        <input type="radio" name="tf_${type}_template" value="blank" checked>
                                        <div class="tf-template-blank"></div>
                                        <span class="tf-circle-check"></span>
                                    </label>
                                    <span class="tf-image-checkbox-footer">
                                        <span class="tf-template-title">Blank</span>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    `);
                }
            });
        });

        $(document).on('change', 'select[name="tf_taxonomy_type"]', function(e) {
            e.preventDefault();
            var postId = $('#tf-post-id').val();
            var service = $('#tf-template-service').val();
            var type = $('#tf-template-type').val();
            var taxonomy = $(this).val();
            
            $.ajax({
                url: tf_pro_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'tf_update_term_options',
                    postId: postId,
                    service: service,
                    type: type,
                    taxonomy: taxonomy,
                    nonce: tf_pro_params.tf_pro_nonce
                },
                beforeSend: function() {
                    $('select[name="tf_taxonomy_term"]').attr('disabled', 'disabled');
                },
                success: function(response) {
                    $('select[name="tf_taxonomy_term"]').removeAttr('disabled');
                    if (response.success) {
                        if (response.data.term_markup) {
                            $('.tf-field-term').show();
                            $('select[name="tf_taxonomy_term"]').html(response.data.term_markup);
                        } else {
                            $('.tf-field-term').hide();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    notyf.error('Error loading template options: ' + error);
                    $('select[name="tf_taxonomy_term"]').removeAttr('disabled');
                }
            });
        });
        
        function tf_load_template_data(post_id) {
            $.ajax({
                url: tf_pro_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'tf_load_template_markup',
                    post_id: post_id,
                    nonce: tf_pro_params.tf_pro_nonce
                },
                beforeSend: function() {
                    $(".tf-template-builder-loader").show();
                },
                success: function(response) {
                    $(".tf-template-builder-loader").hide();
                    if (response.success) {
                        var data = response.data;
                        $('#tf-post-id').val(data.ID);
                        $('#tf-template-name').val(data.post_title);
                        $('#tf-template-service').val(data.tf_template_service);
                        $('#tf-template-type').val(data.tf_template_type);
                        $('#tf-taxonomy-type').val(data.tf_taxonomy_type);
                        if(data.tf_taxonomy_type != 'all'){
                            $('#tf-taxonomy-type').change();
                        }
                        $('#tf-template-active').prop('checked', data.tf_template_active == '1');
                        $('.tf-template-preview').hide();
                        $('.tf-field-term').show();
                        tf_open_template_popup();
                    }
                },
                error: function(xhr, status, error) {
                    $(".tf-template-builder-loader").hide();
                    // Handle error
                    notyf.error('Error loading template data: ' + error);
                }
            });
        }
        
        function tf_save_template(editWithElementor = false) {
            var form_data = $('#tf-template-builder-form').serialize();
            
            $.ajax({
                url: tf_pro_params.ajax_url,
                type: 'POST',
                data: form_data + '&nonce=' + tf_pro_params.tf_pro_nonce + '&edit_with_elementor=' + editWithElementor,
                beforeSend: function() {
                    if(editWithElementor) {
                        $('#tf-edit-with-elementor').addClass('tf-btn-loading');
                    } else {
                        $('#tf-save-template').addClass('tf-btn-loading');
                    }
                },
                success: function(response) {
                    tf_close_template_popup();
                    if(editWithElementor) {
                        $('#tf-edit-with-elementor').removeClass('tf-btn-loading');
                        if (response.success) {
                            notyf.success(response.data.message);
                            window.location.href = response.data.edit_url; // Redirect to Elementor editor
                        }
                    } else {
                        $('#tf-save-template').removeClass('tf-btn-loading');
                        if (response.success) {
                            notyf.success(response.data.message);
                            window.location.reload();
                        }
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
            $('#tf-taxonomy-type').val($('#tf-taxonomy-type option:first').val());
            $('#tf-taxonomy-term').val($('#tf-taxonomy-term option:first').val());
            $('#tf-template-active').prop('checked', false);
            $('.tf-template-preview').show();
            $('input[name="tf_template_design"][value="blank"]').prop('checked', true);
        }
    });
})(jQuery);
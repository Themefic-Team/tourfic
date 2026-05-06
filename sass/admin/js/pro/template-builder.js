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

        initBuilderDropdown();

        $(document).on("click", '.tf-modal-close', function () {
            tf_close_template_popup();
        });

        $(document).on('click', '#tf-builder-dropdown-trigger', function(e) {
            e.preventDefault();

            if ($(this).is(':disabled')) {
                return;
            }

            const $dropdown = $('#tf-builder-dropdown');
            const isOpen = $dropdown.hasClass('is-open');
            $dropdown.toggleClass('is-open', !isOpen);
            $(this).attr('aria-expanded', isOpen ? 'false' : 'true');

            if (!isOpen) {
                const $menu = $dropdown.find('.tf-builder-dropdown-menu');
              
                $menu.css({ top: 'auto', bottom: 'calc(100% + 8px)' });
                
                // $menu.css({ top: 'calc(100% + 8px)', bottom: 'auto' });
                
            }
        });

        $(document).on('click', '.tf-builder-dropdown-option', function(e) {
            e.preventDefault();

            if ($(this).is(':disabled')) {
                return;
            }

            const selectedValue = $(this).data('value');
            $('#tf-edit-with-builder').val(selectedValue);
            syncBuilderDropdown();
            closeBuilderDropdown();
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#tf-builder-dropdown').length) {
                closeBuilderDropdown();
            }
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
                    $('#tf-save-template').attr('disabled', 'disabled');
                    $('#tf-edit-with-builder').attr('disabled', 'disabled');
                    $('#tf-builder-dropdown-trigger').attr('disabled', 'disabled');
                },
                success: function(response) {
                    $('.tf-template-preview-loader').hide();
                    $('select[name="tf_taxonomy_type"]').removeAttr('disabled');
                    $('.tf-field-term').hide();
                    $('select[name="tf_taxonomy_term"]').removeAttr('disabled');
                    $('select[name="tf_taxonomy_term"]').html('');
                    $('#tf-save-template').removeAttr('disabled');
                    $('#tf-builder-dropdown-trigger').removeAttr('disabled');
                    $('#tf-edit-with-builder').removeAttr('disabled');
                    if (response.success) {
                        // Update the template options markup
                        $('.tf-field-imageselect').html(response.data.markup);
                        $('.tf-field-taxonomy').html(response.data.taxonomy_markup);
                    }
                },
                error: function(xhr, status, error) {
                    notyf.error('Error loading template options: ' + error);
                    $('.tf-template-preview-loader').hide();
                    $('select[name="tf_taxonomy_type"]').removeAttr('disabled');
                    $('.tf-field-term').hide();
                    $('select[name="tf_taxonomy_term"]').removeAttr('disabled');
                    $('select[name="tf_taxonomy_term"]').html('');
                    $('#tf-save-template').removeAttr('disabled');
                    $('#tf-edit-with-builder').removeAttr('disabled');
                    $('#tf-builder-dropdown-trigger').removeAttr('disabled');
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
                    $('.tf-field-term').show();
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
                            $('select[name="tf_taxonomy_term"]').html('');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    notyf.error('Error loading template options: ' + error);
                    $('select[name="tf_taxonomy_term"]').removeAttr('disabled');
                }
            });
        });
        
        function initBuilderDropdown() {
            syncBuilderDropdown();
        }

        function syncBuilderDropdown() {
            const selectedValue = $('#tf-edit-with-builder').val();
            const $selectedOption = $('.tf-builder-dropdown-option[data-value="' + selectedValue + '"]');
            const $label = $('#tf-builder-dropdown-label');
            const $icon = $('#tf-builder-dropdown-icon');

            if (!$selectedOption.length) {
                return;
            }

            $label.text($selectedOption.find('.tf-builder-option-label').text());
            $icon.html($selectedOption.find('.tf-builder-option-icon').html());
            $('.tf-builder-dropdown-option').removeClass('is-selected');
            $selectedOption.addClass('is-selected');
        }

        function setBuilderSelection(builderType) {
            const $select = $('#tf-edit-with-builder');

            if (!builderType) {
                return;
            }

            const $option = $select.find('option[value="' + builderType + '"]:not(:disabled)');

            if ($option.length) {
                $select.val(builderType);
                syncBuilderDropdown();
            }
        }

        function closeBuilderDropdown() {
            $('#tf-builder-dropdown').removeClass('is-open');
            $('#tf-builder-dropdown-trigger').attr('aria-expanded', 'false');
        }

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
                        $('#tf-template-builder-form .tf-fields').html(data.fields_markup);
                        setBuilderSelection(data.builder_type);
                        $('.tf-template-preview').hide();
                        tf_open_template_popup();
                    }
                },
                error: function(xhr, status, error) {
                    $(".tf-template-builder-loader").hide();
                    $('#tf-builder-dropdown-trigger').removeAttr('disabled');
                    syncBuilderDropdown();
                    // Handle error
                    notyf.error('Error loading template data: ' + error);
                }
            });
        }
        
        function tf_save_template() {
            var selectedBuilder = $('#tf-edit-with-builder').val();
            var editWithElementor = selectedBuilder === 'elementor';
            var editWithBricks = selectedBuilder === 'bricks';
            var form_data = $('#tf-template-builder-form').serialize();
            var extra = '&nonce=' + tf_pro_params.tf_pro_nonce + '&edit_with_elementor=' + editWithElementor + '&edit_with_bricks=' + editWithBricks;

            $.ajax({
                url: tf_pro_params.ajax_url,
                type: 'POST',
                data: form_data + extra,
                beforeSend: function() {
                    $('#tf-save-template').addClass('tf-btn-loading').attr('disabled', 'disabled');
                    $('#tf-edit-with-builder').attr('disabled', 'disabled');
                    $('#tf-builder-dropdown-trigger').attr('disabled', 'disabled');
                },
                success: function(response) {
                    tf_close_template_popup();
                    $('#tf-save-template').removeClass('tf-btn-loading').removeAttr('disabled');
                    $('#tf-edit-with-builder').removeAttr('disabled');
                    $('#tf-builder-dropdown-trigger').removeAttr('disabled');
                    syncBuilderDropdown();

                    if (response.success) {
                        notyf.success(response.data.message);

                        if ((editWithElementor || editWithBricks) && response.data && response.data.edit_url) {
                            window.location.href = response.data.edit_url;
                        } else {
                            window.location.reload();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    // Handle error
                    notyf.error('Error saving template: ' + error);
                    $('#tf-save-template').removeClass('tf-btn-loading').removeAttr('disabled');
                    $('#tf-edit-with-builder').removeAttr('disabled');
                    $('#tf-builder-dropdown-trigger').removeAttr('disabled');
                    syncBuilderDropdown();
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
            $('#tf-edit-with-builder').val($('#tf-edit-with-builder').data('default-builder') || '');
            closeBuilderDropdown();
            syncBuilderDropdown();
            $('.tf-field-term').hide();
            $('#tf-template-active').prop('checked', false);
            $('.tf-template-preview').show();
            $('input[name="tf_template_design"][value="blank"]').prop('checked', true);
        }

        $(document).on('change', '.tf-template-toggle', function () {
            let post_id = $(this).data('id');
            let status = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: tf_pro_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'tf_toggle_template_status',
                    post_id: post_id,
                    status: status,
                    nonce: tf_pro_params.tf_pro_nonce
                },
                beforeSend: function() {
                    $('.tf-template-builder-loader').show();
                },
                success: function(response) {
                    $('.tf-template-builder-loader').hide();

                    if (response.success && status === 1) {
                        // RESPONSE WILL INCLUDE OTHER DEACTIVATED IDS (we add this)
                        let deactivated_ids = response.data.deactivated_ids || [];

                        // Turn OFF all returned switchers
                        deactivated_ids.forEach(function (id) {
                            let $switch = $('.tf-template-toggle[data-id="' + id + '"]');
                            $switch.prop('checked', false);
                        });
                    }
                },
            });
        });

    });
})(jQuery);
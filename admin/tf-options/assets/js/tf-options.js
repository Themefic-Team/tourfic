(function ($) {
    'use strict';
    $(document).ready(function () {
      
        /*
        * Each date field initialize flatpickr
         */
        $('.tf-field-date').each(function () {
            let $this = $(this),
                dateField = $this.find('input.flatpickr'),
                format = dateField.data('format'),
                multiple = dateField.data('multiple');

            if (dateField.length === 2) {
                let startDate = $this.find('.tf-date-from input.flatpickr').flatpickr({
                    dateFormat: format,
                    onChange: function (selectedDates, dateStr, instance) {
                        endDate.set('minDate', dateStr);
                        console.log('start', selectedDates, dateStr);
                    }
                });
                let endDate = $this.find('.tf-date-to input.flatpickr').flatpickr({
                    dateFormat: format,
                    onChange: function (selectedDates, dateStr, instance) {
                        startDate.set('maxDate', dateStr);
                    }
                });
            } else {
                dateField.flatpickr({
                    dateFormat: format,
                    mode: multiple ? 'multiple' : 'single',
                });
            }
        });

        /*
        * Each time field initialize flatpickr
         */
        $('.tf-field-time').each(function () {
            let $this = $(this),
                timeField = $this.find('input.flatpickr'),
                format = timeField.data('format');

            timeField.flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: format,
            });
        });

        /*
        * Each select2 field initialize select2
         */
        $('.tf-field-select2').each(function () {
            let $this = $(this),
                selectField = $this.find('select.tf-select2'),
                placeholder = selectField.data('placeholder');

            selectField.select2({
                placeholder: placeholder,
                allowClear: true,
            });
        });

        /*
        * Each color field initialize wpColorPicker
         */
        $('.tf-field-color').each(function () {
            let $this = $(this),
                colorField = $this.find('input.tf-color');

            colorField.wpColorPicker();
        });

        //Custom modal
        $(document).on('click', '.tf-modal-btn', function (e) {
            e.preventDefault();
            let $this = $(this),
                modal = $('#tf-icon-modal');

            if (modal.length > 0 && modal.hasClass('tf-modal-show')) {
                modal.removeClass('tf-modal-show');
                $('body').removeClass('tf-modal-open');
            } else {
                modal.addClass('tf-modal-show');
                $('body').addClass('tf-modal-open');
            }
        });
        $(document).on("click", '.tf-modal-close', function () {
            $('.tf-modal').removeClass('tf-modal-show');
            $('body').removeClass('tf-modal-open');
        });
        $(document).click(function (event) {
            if (!$(event.target).closest(".tf-modal-content,.tf-modal-btn").length) {
                $("body").removeClass("tf-modal-open");
                $(".tf-modal").removeClass("tf-modal-show");
            }
        });

        /*
        * Icon tab
        * */
        $(document).on('click', '.tf-icon-tab', function (e) {
            e.preventDefault();
            let $this = $(this),
                tab = $this.data('tab');

            $('.tf-icon-tab').removeClass('active');
            $this.addClass('active');

            $('#' + tab).addClass('active').siblings().removeClass('active');
        });

        /*
        * Icon select
         */
        $(document).on('click', '.tf-icon-select .tf-admin-btn, .tf-icon-select .tf-icon-preview', function (e) {
            e.preventDefault();
            let btn = $(this);

            //icon select
            $(document).on('click', '.tf-icon-list li', function (e) {
                e.preventDefault();
                let $this = $(this);

                $('.tf-icon-list li').removeClass('active');
                $this.addClass('active');

                //remove disabled class
                $('.tf-icon-insert').removeClass('disabled');
            });

            //insert btn click
            $(document).on('click', '.tf-icon-insert', function (e) {
                e.preventDefault();
                let $this = $(this),
                    preview = btn.closest('.tf-icon-select').find('.tf-icon-preview'),
                    icon = $('.tf-icon-list li.active').data('icon');

                if (icon) {
                    preview.removeClass('tf-hide');
                    btn.closest('.tf-icon-select').find('.tf-icon-preview-wrap i').attr('class', icon);
                    btn.closest('.tf-icon-select').find('.tf-icon-value').val(icon).trigger('change');

                    //Close modal
                    $('.tf-modal').removeClass('tf-modal-show');
                    $('body').removeClass('tf-modal-open');
                }
            })
        });

        $(document).on('change keyup', '.tf-icon-search-input', function () {

            let searchVal = $(this).val(),
                $icons = $('#tf-icon-modal').find('.tf-icon-list li');

            $icons.each(function () {

                var $this = $(this);

                if ($this.data('icon').search(new RegExp(searchVal, 'i')) < 0) {
                    $this.hide();
                } else {
                    $this.show();
                }

            });

        });

        //Icon remove
        $(document).on('click', '.tf-icon-preview .remove-icon', function (e) {
            e.preventDefault();
            let $this = $(this),
                preview = $this.closest('.tf-icon-preview'),
                iconSelect = $this.closest('.tf-icon-select'),
                iconLi = $('#tf-icon-modal').find('.tf-icon-list li');

            preview.addClass('tf-hide');
            iconSelect.find('.tf-icon-preview-wrap i').attr('class', '');
            iconSelect.find('.tf-icon-value').val('').trigger('change');

            //remove active class
            iconLi.removeClass('active');
        })

         // Repeater jquery 
         
         $(document).on('click', '.tf-repeater-icon-add', function(){
            // $(this).closest('.tf-single-repeater').remove();
           var $this = $(this);
           var $this_parent = $this.parent().parent();
           var id = $(this).attr("data-repeater-id");
        //    alert(id);
           var add_value = $this_parent.find('.tf-single-repeater-clone-'+id+' .tf-single-repeater-'+id+'').clone();
           var count = $this_parent.find('.tf-repeater-wrap-'+id+' .tf-single-repeater-'+id+'').length;
           var parent_field = add_value.find(':input[name="tf_parent_field"]').val();
           var current_field = add_value.find(':input[name="tf_current_field"]').val();
          
           if(parent_field == ''){  
                add_value.find(':input').each(function (){ 
                    this.name = this.name.replace( '_____', '' ).replace('['+current_field+'][0]', '['+current_field+']['+ count +']');
                    this.id = this.id.replace( '_____', '' ).replace('['+current_field+'][0]', '['+current_field+']['+ count +']');
                 }); 
             var update_paren  = add_value.find('.tf-repeater input[name="tf_parent_field"]').val();
             var update_paren  = update_paren.replace('['+current_field+'][0]', '['+current_field+']['+ count +']');
             add_value.find('.tf-repeater input[name="tf_parent_field"]').val(update_paren);
 
           }else{
            var update_paren  = add_value.find(':input[name="tf_parent_field"]').val();
            add_value.find(':input').each(function (){ 
                this.name = this.name.replace( '_____', '' ).replace('['+current_field+'][0]', '['+current_field+']['+ count +']');
                this.id = this.id.replace( '_____', '' ).replace('['+current_field+'][0]',  '['+current_field+']['+ count +']');
            });
           }
           
           var append = $this_parent.find('.tf-repeater-wrap-'+id+'');
            add_value.appendTo(append).show(); 
        });
        $(document).on('click', '.tf-repeater-icon-delete', function(){
            if (confirm("Are you sure to delete this item?")) { 
                $(this).closest('.tf-single-repeater').remove();
            }
            return false;
        });
        $(document).on('click', '.tf-repeater-icon-clone', function(){ 
            let clone_value = $(this).closest('.tf-single-repeater').html();
            $(this).closest('.tf-repeater-wrap').append('<div class="tf-single-repeater">' + clone_value + '</div>').show();
        });
        $(document).on('click', '.tf-repeater-title, .tf-repeater-icon-collapse', function(){
            
            $(this).closest('.tf-single-repeater').find('.tf-repeater-content-wrap').slideToggle();
            $(this).closest('.tf-single-repeater').find('.tf-repeater-content-wrap').toggleClass('hide');
            if($(this).closest('.tf-single-repeater').find('.tf-repeater-content-wrap').hasClass('hide') == true){
                $(this).closest('.tf-single-repeater').find('.tf-repeater-icon-collapse').html('<i class="fa-solid fa-angle-up"></i>');
            }else{
                $(this).closest('.tf-single-repeater').find('.tf-repeater-icon-collapse').html('<i class="fa-solid fa-angle-down"></i>');
            }
        });
        $( ".tf-repeater-wrap" ).sortable(); 


         // TAB jquery 
         $(document).on('click', '.tf-tab-item', function(){
            var $this = $(this);
            var tab_id = $this.data('tab-id'); 
            if($this.parent().parent().find('.tf-tab-item-content').hasClass("show") == true){  
                $this.parent().parent().find('.tf-tab-item-content').removeClass('show');
            }
            
            $this.parent().find('.tf-tab-item').removeClass('show');

            $this.addClass('show'); 
            $this.parent().parent().find('.tf-tab-item-content[data-tab-id = '+tab_id+']').addClass('show');
          
        });

    });
})(jQuery);


function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tf-tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tf-tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.target.className += " active";
}

var frame, gframe;
(function ($) {
    // Single Image remove
    $(document).on("click", ".tf-image-close", function (e) {
        e.preventDefault();
        var fieldname = $(this).attr("tf-field-name");
        var tf_preview_class = fieldname.replace(/[.[\]_-]/g, '_');

        $('input[name="' + fieldname + '"]').val('');
        $('.' + tf_preview_class + '').html('');

    });

    // Gallery Image remove
    $(document).on("click", ".tf-gallery-remove", function (e) {
        e.preventDefault();
        var fieldname = $(this).attr("tf-field-name");
        var tf_preview_class = fieldname.replace(/[.[\]_-]/g, '_');

        $('input[name="' + fieldname + '"]').val('');
        $('.tf-fieldset > .' + tf_preview_class + '').html('');
        $('a.' + tf_preview_class + '').css("display", "none");

    });

    $(document).ready(function () {

        // Single Image Upload

        $('body').on('click', '.tf-media-upload', function (e) {
            var fieldname = $(this).attr("tf-field-name");
            var tf_preview_class = fieldname.replace(/[.[\]_-]/g, '_');

            frame = wp.media({
                title: "Select Image",
                button: {
                    text: "Insert Image"
                },
                multiple: false
            });
            frame.on('select', function () {

                var attachment = frame.state().get('selection').first().toJSON();
                $('input[name="' + fieldname + '"]').val(attachment.url);
                $('.' + tf_preview_class + '').html(`<div class="tf-image-close" tf-field-name='${fieldname}'>✖</div><img src='${attachment.sizes.thumbnail.url}' />`);
            });
            frame.open();
            return false;
        });

        // Gallery Image Upload

        $('body').on('click', '.tf-gallery-upload', function (e) {
            var fieldname = $(this).attr("tf-field-name");
            var tf_preview_class = fieldname.replace(/[.[\]_-]/g, '_');
            gframe = wp.media({
                title: "Select Gallery",
                button: {
                    text: "Insert Gallery"
                },
                multiple: 'add'
            });

            gframe.on('open', function () {
                var selection = gframe.state().get('selection');
                var ids_value = jQuery('input[name="' + fieldname + '"]').val();

                if (ids_value.length > 0) {
                    var ids = ids_value.split(',');

                    ids.forEach(function (id) {
                        attachment = wp.media.attachment(id);
                        attachment.fetch();
                        selection.add(attachment ? [attachment] : []);
                    });
                }
            });

            gframe.on('select', function () {
                var image_ids = [];
                var image_urls = [];
                var attachments = gframe.state().get('selection').toJSON();
                $('.tf-fieldset > .' + tf_preview_class + '').html('');
                for (i in attachments) {
                    var attachment = attachments[i];
                    image_ids.push(attachment.id);
                    image_urls.push(attachment.sizes.thumbnail.url);
                    $('.tf-fieldset > .' + tf_preview_class + '').append(`<img src='${attachment.sizes.thumbnail.url}' />`);
                }
                $('input[name="' + fieldname + '"]').val(image_ids.join(","));
                $('a.' + tf_preview_class + '').css("display", "inline-block");
            });

            gframe.open();
            return false;
        });
    


    
        $(".tf-field-map").each(function() {
            var $this         = $(this),
            $map          = $this.find('.tf--map-osm'),
            $search_input = $this.find('.tf--map-search input'),
            $latitude     = $this.find('.tf--latitude'),
            $longitude    = $this.find('.tf--longitude'),
            $zoom         = $this.find('.tf--zoom'),
            map_data      = $map.data( 'map' );
            
          var mapInit = L.map( $map.get(0), map_data);

    
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
          }).addTo(mapInit);
    
          var mapMarker = L.marker(map_data.center,{draggable: true}).addTo(mapInit);
    
          var update_latlng = function( data ) {
            $latitude.val( data.lat );
            $longitude.val( data.lng );
            $zoom.val( mapInit.getZoom() );
          };
    
          mapInit.on( 'click', function ( data ) {
            mapMarker.setLatLng( data.latlng );
            update_latlng( data.latlng );
          });
    
          mapInit.on( 'zoom', function () {
            update_latlng( mapMarker.getLatLng() );
          });
    
          mapMarker.on( 'drag', function () {
            update_latlng( mapMarker.getLatLng() );
          });
    
          if ( ! $search_input.length ) {
            $search_input = $( '[data-depend-id="'+ $this.find('.tf--address-field').data( 'address-field' ) +'"]' );
          }
    
          var cache = {};
    
          $search_input.autocomplete({
            source: function ( request, response ) {
    
              var term = request.term;
    
              if ( term in cache ) {
                response( cache[term] );
                return;
              }
    
              $.get( 'https://nominatim.openstreetmap.org/search', {
                format: 'json',
                q: term,
              }, function( results ) {
    
                var data;
    
                if ( results.length ) {
                  data = results.map( function( item ) {
                    return {
                      value: item.display_name,
                      label: item.display_name,
                      lat: item.lat,
                      lon: item.lon
                    };
                  }, 'json');
                } else {
                  data = [{
                    value: 'no-data',
                    label: 'No Results.'
                  }];
                }
    
                cache[term] = data;
                response(data);
    
              });
    
            },
            select: function ( event, ui ) {
    
              if ( ui.item.value === 'no-data' ) { return false; }
    
              var latLng = L.latLng( ui.item.lat, ui.item.lon );
    
              mapInit.panTo( latLng );
              mapMarker.setLatLng( latLng );
              update_latlng( latLng );
    
            },
            create: function (event, ui) {
              $(this).autocomplete('widget').addClass('tf-map-ui-autocomplate');
            }
          });
    
          var input_update_latlng = function() {
    
            var latLng = L.latLng( $latitude.val(), $longitude.val() );
    
            mapInit.panTo( latLng );
            mapMarker.setLatLng( latLng );
    
          };
    
          $latitude.on('change', input_update_latlng );
          $longitude.on('change', input_update_latlng );
    
        });
    });


})(jQuery);
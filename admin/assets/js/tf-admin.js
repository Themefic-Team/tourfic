
jQuery(function($){
	$(document).ready(function(){

		// Tab controlling
	    $('.tf-tab-nav a').on('click',function(e){
	    	e.preventDefault();
	    	var targetDiv = $(this).attr('href');

    		if(!$(this).parent().hasClass('active')){
                $(this).parent().addClass('active').siblings().removeClass('active');
            }
            $('.tf-tab-container').find(targetDiv).addClass('active').siblings().removeClass('active');

            //location.hash = targetDiv;
            history.pushState({}, '', targetDiv);

    	});

	    // Active tab from location
    	var hash = window.location.hash;
    	$('.tf-tab-nav a[href="'+hash+'"]').click();

        $(window).on('hashchange', function(){
            var a = /^#?chapter(\d+)-section(\d+)\/?$/i.exec(location.hash);
        });

        // Add Room Ajax
        $(document).on('click', '.tf_add-room', function(){
            var $this = $(this);

            var keyLen = jQuery('.tf-add-single-room-wrap').length;

            var data = {
                action: 'tf_add_new_room',
                key: keyLen,
            }

            $.ajax({
              url: ajaxurl,
              type: 'post',
              data: data,
              beforeSend : function ( xhr ) {
                $this.prop('disabled', true);
              },
              success: function( res ) {
                $this.prop('disabled', false);

                // Data push
                $('.tf_room-fields').append(res);
              },
              error: function( result ) {
                $this.prop('disabled', false);
                console.error( result );
              }
            });
        });

        // Add FAQ Ajax
        $(document).on('click', '.tf_add-faq', function(){
            var $this = $(this);

            var keyLen = jQuery('.tf-add-single-faq-wrap').length;

            var data = {
                action: 'tf_add_new_faq',
                key: keyLen,
            }

            $.ajax({
              url: ajaxurl,
              type: 'post',
              data: data,
              beforeSend : function ( xhr ) {
                $this.prop('disabled', true);
              },
              success: function( res ) {
                $this.prop('disabled', false);

                // Data push
                $('.tf_faqs-fields').append(res);
              },
              error: function( result ) {
                $this.prop('disabled', false);
                console.error( result );
              }
            });
        });


        // Add Room Ajax
        $(document).on('click', '.tf_remove_postdiv', function(){
            $(this).closest('.tf_postbox').remove();
            return false;
        });

        // Room Field toggle
        $(document).on('click', '.tf_expend_postdiv', function(){
            $(this).closest('.tf_postbox').toggleClass('active').find('.tf_postbox-inside').slideToggle('fast');
            return false;
        });

        // Room title push on head
        $(document).on('keyup change', '.tf_postbox-title-get', function(){
            var thisVal = ( $(this).val() ) ? $(this).val() : "# Room Title";
            $(this).closest('.tf_postbox').find('.tf_postbox-title').text( thisVal );
        });


        var file_frame, image_data;

        // Add Gallery
        $(document).on('click', '.tf_add-gallery', function(){
            var $this = $(this);



            if ( undefined !== file_frame ) {

                file_frame.open();
                return;

            }

            file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Insert Gallery Images',
                //frame:    'post',
                //state:    'insert',
                multiple: true,
                library: {
                    type: [ 'video', 'image' ]
                },
                button: {text: 'Insert'}
            });

            file_frame.on( 'select', function() {

                var fileHtml = "";
                var fieldIds = [];

                files = file_frame.state().get( 'selection' ).toJSON();

                files.forEach(function(file, i){
                    fileHtml += '<span class="tf_gallery-img" id="'+file.id+'" title="'+file.title+'">';
                    fileHtml += '<img  src="'+file.url+'" />';
                    fileHtml += '</span>';

                    fieldIds.push( file.id );
                });

                $this.closest('.tf_gallery-field-wrap').find('.tf_gallery-images').html( fileHtml );
                $this.closest('.tf_gallery-field-wrap').find('.tf_gallery_ids_push').val( fieldIds );

            });

            // Now display the actual file_frame
            file_frame.open();

        });

	});

});
(function ($) {
    'use strict';
    $(document).ready(function () {
        /*
        * Section tab first one click on load
        * @author: Foysal
        */
        $(window).on('load', function () {
            if ($('.tf-admin-tab .tf-tablinks').length > 0) {
                $('.tf-admin-tab .tf-tablinks').first().trigger('click').addClass('active');
            }
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
    $(document).on("click", ".tf-image-close", function () {
        var fieldname = $(this).attr("tf-field-name");
        var tf_preview_class = fieldname.replace(/[.[\]_-]/g, '_');
    
        $('input[name="'+fieldname+'"]').val('');
        $('.'+tf_preview_class+'').html('');
        
    });

    $(document).ready(function () {

        $('body').on('click', '.tf-media-upload', function(e) {
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
                $('input[name="'+fieldname+'"]').val(attachment.url);
                $('.'+tf_preview_class+'').html(`<div class="tf-image-close" tf-field-name='${fieldname}'>✖</div><img src='${attachment.sizes.thumbnail.url}' />`);
            });


            frame.open();
            return false;
        });

        

        // $("#upload_images").on("click", function () {

        //     if (gframe) {
        //         gframe.open();
        //         return false;
        //     }

        //     gframe = wp.media({
        //         title: "Select Image",
        //         button: {
        //             text: "Insert Image"
        //         },
        //         multiple: true
        //     });

        //     gframe.on('select', function () {
        //         var image_ids = [];
        //         var image_urls = [];
        //         var attachments = gframe.state().get('selection').toJSON();
        //         //console.log(attachments);
        //         $("#images-container").html('');
        //         for (i in attachments) {
        //             var attachment = attachments[i];
        //             image_ids.push(attachment.id);
        //             image_urls.push(attachment.sizes.thumbnail.url);
        //             $("#images-container").append(`<img style="margin-right: 10px;" src='${attachment.sizes.thumbnail.url}' />`);

        //         }
        //         $("#omb_images_id").val(image_ids.join(";"));
        //         $("#omb_images_url").val(image_urls.join(";"));

        //     });


        //     gframe.open();
        //     return false;
        // });
    });
})(jQuery);
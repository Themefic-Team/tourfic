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


        $(".tf-repeater").each(function(){
            let $this = $(this);
            let tf_repeater_add = $this.find('.tf-repeater-icon-add');
            tf_repeater_add.on('click', function(){
                let add_value = $this.find('.tf-single-repeater-clone').html();
                // console.log(add_value)
                // add_value.find(':input').each(function (){

                //     this.name = this.name.replace( '_____', '' );
                // }); 
                $this.find('.tf-repeater-wrap').append(add_value).show(); 
            });
             
        });
        $(document).on('click', '.tf-repeater-icon-delete', function(){
            $(this).closest('.tf-single-repeater').remove();
        });
        $(document).on('click', '.tf-repeater-icon-clone', function(){
            alert(1);
            let clone_value = $(this).closest('.tf-single-repeater').html();
            $(this).closest('.tf-repeater-wrap').append('<div class="tf-single-repeater">'+clone_value+'</div>').show(); 
        });
        $(document).on('click', '.tf-repeater-title, .tf-repeater-icon-collapse', function(){
            $(this).closest('.tf-single-repeater').find('.tf-repeater-content-wrap').toggleClass("hide") 
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

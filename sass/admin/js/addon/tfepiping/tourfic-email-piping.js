;( function( $ ) {
    "use strict",

    $(document).ready(function() {
        $("input[id='tf_settings[tf-email-piping][gmail_auth_origin]']").val(tfep_vars.site_url)
        $("input[id='tf_settings[tf-email-piping][gmail_redirect_url]']").val(tfep_vars.redirect_url)
    })
})( jQuery );
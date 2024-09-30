;( function( $ ) {
    "use strict",

    $(document).ready(function() {
        $("input[id='tf_settings[tf-email-piping][gmail_auth_origin]']").val(tfep_vars.site_url)
        $("input[id='tf_settings[tf-email-piping][gmail_redirect_url]']").val(tfep_vars.redirect_url)

        $("#tfep-test-connection").on("click", function(e) {
            e.preventDefault();
            let type = $("select[id='tf_settings[tf-email-piping][connection_type]']").val();
            let email = $("input[id='tf_settings[tf-email-piping][imap-email-address]']").val();
            let password = $("input[id='tf_settings[tf-email-piping][imap-email-password]']").val();
            let server = $("input[id='tf_settings[tf-email-piping][imap-email-server]']").val();
            let connection_type = $("select[id='tf_settings[tf-email-piping][imp_connection_type]']").val();
            let connection_port = $("input[id='tf_settings[tf-email-piping][imp_connection_port]']").val();

            if (type == "imap") {
                if (email == "" || password == "" || server == "" || connection_type == "" || connection_port == "") {
                    alert("Please fill all the fields");
                    return;
                }

                $.ajax({
                    url: tfep_vars.ajax_url,
                    type: "POST",
                    data: {
                        action: "tfep_test_connection",
                        email: email,
                        password: password,
                        server: server,
                        connection_type: connection_type,
                        connection_port: connection_port
                    },
                    beforeSend: function() {
                        $("#tfep-test-connection").addClass("tf-btn-loading");
                    },
                    success: function(response) {
                        // let data = JSON.parse(response);
                        $("#tfep-test-connection").removeClass("tf-btn-loading");
                        
                        if(response.status == "success") {
                           $(".tfep-connection-result").addClass("connection-success").html(response.message);
                        } else {
                            $(".tfep-connection-result").addClass("connection-failed").html(response.message);
                        }
                    }
                });
            }
        });
    })
})( jQuery );
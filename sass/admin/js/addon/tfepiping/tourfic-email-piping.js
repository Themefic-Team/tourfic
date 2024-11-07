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

            // gmail
            let gmail_address = $("input[id='tf_settings[tf-email-piping][gmail-address]']").val();
            let gmail_client_id = $("input[id='tf_settings[tf-email-piping][gmail-client]']").val();
            let gmail_secret = $("input[id='tf_settings[tf-email-piping][gmail-client-secret]']").val();

            if (type == "imap") {
                if (email == "" || password == "" || server == "" || connection_type == "" || connection_port == "") {
                    alert("Please fill all the fields");
                    return;
                }

                $.ajax({
                    url: tfep_vars.ajax_url,
                    type: "POST",
                    data: {
                        action: "tfep_test_imap_connection",
                        email: email,
                        password: password,
                        server: server,
                        connection_type: connection_type,
                        connection_port: connection_port,
                        _nonce: tfep_vars.tfep_nonce
                    },
                    beforeSend: function() {
                        $("#tfep-test-connection").addClass("tf-btn-loading");
                    },
                    success: function(response) {
                        // let data = JSON.parse(response);
                        $("#tfep-test-connection").removeClass("tf-btn-loading");
                        
                        if(response.status == "success") {
                           $(".tfep-connection-result").addClass("connection-success").html(response.message);
                        } else if( response.status == "error") {
                            $(".tfep-connection-result").addClass("connection-failed").html(response.message);
                        }
                    }
                });
            } else if( type == "gmail") {
                if (gmail_address == "" || gmail_client_id == "" || gmail_secret == "") {
                    alert("Please fill all the fields");
                    return;
                }

                $.ajax({
                    url: tfep_vars.ajax_url,
                    type: "POST",
                    data: {
                        action: "tfep_test_gmail_connection",
                        email: gmail_address,
                        client_id: gmail_client_id,
                        client_secret: gmail_secret,
                        _nonce: tfep_vars.tfep_nonce
                    },
                    beforeSend: function() {
                        $("#tfep-test-connection").addClass("tf-btn-loading");
                    },
                    success: function(response) {
                        $("#tfep-test-connection").removeClass("tf-btn-loading");
                        if(response.status == "success") {
                            $(".tfep-connection-result").addClass("connection-success").html(response.message);
                            window.location.href = response.url;

                        } else if( response.status == "error") {
                            $(".tfep-connection-result").addClass("connection-failed").html(response.message);
                        }
                    }
                });
            }
        });

        $(".tf-enquiry-single-sync").on("click", function(e) {
            let $this = $(this);
            let type = tfep_vars.tfep_connection_type

            if (type == "imap") {

                $.ajax({
                    url: tfep_vars.ajax_url,
                    type: "POST",
                    data: {
                        action: "tfep_single_imap_sync",
                        _nonce: tfep_vars.tfep_nonce,
                    },
                    beforeSend: function() {
                        $this.addClass("tf-btn-loading");
                    },
                    success: function(response) {
                        $this.removeClass("tf-btn-loading");
                        if(response.status == "success") {
                            window.location.reload();
                        } else if( response.status == "error") {
                            alert(response.message);
                        }
                    }
                });
            } else if (type == "gmail") {
                $.ajax({
                    url: tfep_vars.ajax_url,
                    type: "POST",
                    data: {
                        action: "tfep_single_gmail_sync",
                        _nonce: tfep_vars.tfep_nonce,
                    },
                    beforeSend: function() {
                        $this.addClass("tf-btn-loading");
                    },
                    success: function(response) {
                        $this.removeClass("tf-btn-loading");
                        if(response.status == "success") {
                            window.location.reload();
                        } else if( response.status == "error") {
                            alert(response.message);
                        }
                    }
                });
            }
        });
    })
})( jQuery );
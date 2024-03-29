jQuery(function ($) {

    //required taxonomies
	// Create an instance of Notyf
	const notyf = new Notyf({
		ripple: true,
		dismissable: true,
		duration: 3000,
		position: {
			x: 'right',
			y: 'bottom',
		},
	});

    function tf_event_handler(e) {
        tf_admin_params.error = false;
        $.each(tf_admin_params.taxonomies, function (taxonomy, config) {
            if (config.type == 'hierarchical') {
                if ($('#taxonomy-' + taxonomy + ' input:checked').length == 0) {
                    //alert(config.message);
					notyf.error(config.message);
                    tf_admin_params.error = true;
                }
            } else {
                if ($('#tagsdiv-' + taxonomy + ' .tagchecklist').is(':empty')) {
                    //alert(config.message);
                    notyf.error(config.message);
                    tf_admin_params.error = true;
                }
            }
        });
        if (tf_admin_params.error) {
            e.stopImmediatePropagation();
            return false;
        } else {
            return true;
        }
    }

    $('#publish, #save-post').on('click.require-post-category', tf_event_handler);
    $('#post').on('submit.require-post-category', tf_event_handler);
    if ($('#publish')[0] != null && $._data($('#publish')[0], "events") != null) {
        var publish_click_events = $._data($('#publish')[0], "events").click;
        if (publish_click_events) {
            if (publish_click_events.length > 1) {
                publish_click_events.unshift(publish_click_events.pop());
            }
        }
    }
    if ($('#save-post')[0] != null && $._data($('#save-post')[0], "events") != null) {
        var save_click_events = $._data($('#save-post')[0], "events").click;
        if (save_click_events) {
            if (save_click_events.length > 1) {
                save_click_events.unshift(save_click_events.pop());
            }
        }
    }
    if ($('#post')[0] != null && $._data($('#post')[0], "events") != null) {
        var post_submit_events = $._data($('#post')[0], "events").submit;
        if (post_submit_events) {
            if (post_submit_events.length > 1) {
                post_submit_events.unshift(post_submit_events.pop());
            }
        }
    }
});
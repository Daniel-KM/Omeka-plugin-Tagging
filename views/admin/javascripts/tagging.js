jQuery(document).ready(function() {
    // Handle approved / rejected from any status.
    jQuery('.tagging.toggle-status').click(function(event) {
        event.preventDefault();
        var id = jQuery(this).attr('id');
        var current = jQuery('#' + id);
        id = id.substr(id.lastIndexOf('-') + 1);
        var ajaxUrl = jQuery(this).attr('href') + '/tagging/ajax/update';
        jQuery(this).addClass('transmit');
        if (jQuery(this).hasClass('approved')) {
            jQuery.post(ajaxUrl,
                {
                    status: 'rejected',
                    id: id
                },
                function(data) {
                    current.addClass('rejected');
                    current.removeClass('proposed');
                    current.removeClass('allowed');
                    current.removeClass('approved');
                    current.removeClass('transmit');
                    if (current.text() != '') {
                        current.text(Omeka.messages.tagging.rejected);
                    }
                }
            );
        } else {
            jQuery.post(ajaxUrl,
                {
                    status: 'approved',
                    id: id
                },
                function(data) {
                    current.addClass('approved');
                    current.removeClass('proposed');
                    current.removeClass('allowed');
                    current.removeClass('rejected');
                    current.removeClass('transmit');
                    if (current.text() != '') {
                        current.text(Omeka.messages.tagging.approved);
                    }
                }
            );
        }
    });
});

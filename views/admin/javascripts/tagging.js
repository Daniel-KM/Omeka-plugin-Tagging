if (!Omeka) {
    var Omeka = {};
}

Omeka.Taggings = {};

(function ($) {
    Omeka.Taggings.setupUpdate = function (status) {
        jQuery('.tagging-toggle-status').data('status', status);
    };
})(jQuery);

jQuery(document).ready(function() {
    // Handle approved / rejected from any status.
    jQuery('.tagging-toggle-status').click(function(event) {
        event.preventDefault();
        var current = jQuery('#' + jQuery(this).attr('id'));
        var ajaxUrl = jQuery(this).attr('href') + '/tagging/ajax/update';
        jQuery(this).addClass('transmit');
        if (jQuery(this).hasClass('approved')) {
            jQuery.post(ajaxUrl,
                {
                    status: 'rejected',
                    id: jQuery(this).attr('id')
                },
                function(data) {
                    current.addClass('rejected');
                    current.removeClass('approved');
                    current.removeClass('transmit');
                    if (current.text() != '') {
                        current.text(current.data('status').rejected);
                    }
                }
            );
        } else {
            jQuery.post(ajaxUrl,
                {
                    status: 'approved',
                    id: jQuery(this).attr('id')
                },
                function(data) {
                    current.addClass('approved');
                    current.removeClass('rejected');
                    current.removeClass('transmit');
                    if (current.text() != '') {
                        current.text(current.data('status').approved);
                    }
                }
            );
        }
    });
});

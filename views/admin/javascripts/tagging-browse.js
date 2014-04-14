if (!Omeka) {
    var Omeka = {};
}

Omeka.TaggingsBrowse = {};

(function ($) {
    Omeka.TaggingsBrowse.setupBatchEdit = function () {
        var taggingCheckboxes = $("table#taggings tbody input[type=checkbox]");
        var globalCheckbox = $('th.batch-edit-heading').html('<input type="checkbox">').find('input');
        var batchEditSubmit = $('.batch-edit-option input');
        /**
         * Disable the batch submit button first, will be enabled once tagging
         * checkboxes are checked.
         */
        batchEditSubmit.prop('disabled', true);

        /**
         * Check all the taggingCheckboxes if the globalCheckbox is checked.
         */
        globalCheckbox.change(function() {
            taggingCheckboxes.prop('checked', !!this.checked);
            checkBatchEditSubmitButton();
        });

        /**
         * Uncheck the global checkbox if any of the taggingCheckboxes are
         * unchecked.
         */
        taggingCheckboxes.change(function(){
            if (!this.checked) {
                globalCheckbox.prop('checked', false);
            }
            checkBatchEditSubmitButton();
        });

        /**
         * Check whether the batchEditSubmit button should be enabled.
         * If any of the taggingCheckboxes is checked, the batchEditSubmit button
         * is enabled.
         */
        function checkBatchEditSubmitButton() {
            var checked = false;
            taggingCheckboxes.each(function() {
                if (this.checked) {
                    checked = true;
                    return false;
                }
            });

            batchEditSubmit.prop('disabled', !checked);
        }
    };
})(jQuery);

jQuery(document).ready(function() {
    // Approved from any status.
    jQuery('input[name="submit-batch-approve"]').click(function(event) {
        event.preventDefault();
        jQuery('table#taggings thead tr th.batch-edit-heading input').attr('checked', false);
        jQuery('.batch-edit-option input').prop('disabled', true);
        jQuery('table#taggings tbody input[type=checkbox]:checked').each(function(){
            var checkbox = jQuery(this);
            var current = jQuery('#tagging-' + this.value);
            var ajaxUrl = current.attr('href') + '/tagging/ajax/update';
            current.addClass('transmit');
            jQuery.post(ajaxUrl,
                {
                    status: 'approved',
                    id: this.value
                },
                function(data) {
                    checkbox.attr('checked', false);
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
        });
    });

    // Reject from any status.
    jQuery('input[name="submit-batch-reject"]').click(function(event) {
        event.preventDefault();
        jQuery('table#taggings thead tr th.batch-edit-heading input').attr('checked', false);
        jQuery('.batch-edit-option input').prop('disabled', true);
        jQuery('table#taggings tbody input[type=checkbox]:checked').each(function(){
            var checkbox = jQuery(this);
            var current = jQuery('#tagging-' + this.value);
            var ajaxUrl = current.attr('href') + '/tagging/ajax/update';
            current.addClass('transmit');
            jQuery.post(ajaxUrl,
                {
                    status: 'rejected',
                    id: this.value
                },
                function(data) {
                    checkbox.attr('checked', false);
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
        });
    });

    // Delete a tagging..
    jQuery('input[name="submit-batch-delete"]').click(function(event) {
        event.preventDefault();
        if (!confirm(Omeka.messages.tagging.confirmation)) {
            return;
        }
        jQuery('table#taggings thead tr th.batch-edit-heading input').attr('checked', false);
        jQuery('.batch-edit-option input').prop('disabled', true);
        jQuery('table#taggings tbody input[type=checkbox]:checked').each(function(){
            var checkbox = jQuery(this);
            var row = jQuery(this).closest('tr.tagging');
            var current = jQuery('#tagging-' + this.value);
            var ajaxUrl = current.attr('href') + '/tagging/ajax/delete';
            checkbox.addClass('transmit');
            jQuery.post(ajaxUrl,
                {
                    id: this.value
                },
                function(data) {
                    checkbox.attr('checked', false);
                    row.remove();
                }
            );
        });
    });
});

/**
 * Unbind all event handlers before tearing down the page
 */
AJAX.registerTeardown('tbl_tracking.js', function () {
    $('body').off('click', '#versionsForm.ajax button[name="submit_mult"], #versionsForm.ajax input[name="submit_mult"]');
    $('body').off('click', 'a.delete_version_anchor.ajax');
    $('body').off('click', 'a.delete_entry_anchor.ajax');
});

/**
 * Bind event handlers
 */
AJAX.registerOnload('tbl_tracking.js', function () {

    $('#versions tr:first th').append($('<div class="sorticon"></div>'));
    $('#versions').tablesorter({
        sortList: [[1, 0]],
        headers: {
            0: {sorter: false},
            1: {sorter: "integer"},
            5: {sorter: false},
            6: {sorter: false}
        }
    });

    if ($('#ddl_versions tbody tr').length > 0) {
        $('#ddl_versions tr:first th').append($('<div class="sorticon"></div>'));
        $('#ddl_versions').tablesorter({
            sortList: [[0, 0]],
            headers: {
                0: {sorter: "integer"},
                3: {sorter: false},
                4: {sorter: false}
            }
        });
    }

    if ($('#dml_versions tbody tr').length > 0) {
        $('#dml_versions tr:first th').append($('<div class="sorticon"></div>'));
        $('#dml_versions').tablesorter({
            sortList: [[0, 0]],
            headers: {
                0: {sorter: "integer"},
                3: {sorter: false},
                4: {sorter: false}
            }
        });
    }

    /**
     * Handles multi submit for tracking versions
     */
    $('body').on('click', '#versionsForm.ajax button[name="submit_mult"], #versionsForm.ajax input[name="submit_mult"]', function (e) {
        e.preventDefault();
        var $button = $(this);
        var $form = $button.parent('form');
        var submitData = $form.serialize() + '&ajax_request=true&ajax_page_request=true&submit_mult=' + $button.val();

        if ($button.val() == 'delete_version') {
            var question = PMA_messages.strDeleteTrackingVersionMultiple;
            $button.PMA_confirm(question, $form.attr('action'), function (url) {
                PMA_ajaxShowMessage();
                AJAX.source = $form;
                $.post(url, submitData, AJAX.responseHandler);
            });
        } else {
            PMA_ajaxShowMessage();
            AJAX.source = $form;
            $.post($form.attr('action'), submitData, AJAX.responseHandler);
        }
    });

    /**
     * Ajax Event handler for 'Delete version'
     */
    $('body').on('click', 'a.delete_version_anchor.ajax', function (e) {
        e.preventDefault();
        var $anchor = $(this);
        var question = PMA_messages.strDeleteTrackingVersion;
        $anchor.PMA_confirm(question, $anchor.attr('href'), function (url) {
            PMA_ajaxShowMessage();
            AJAX.source = $anchor;
            var params = {
                'ajax_page_request': true,
                'ajax_request': true,
                'token': PMA_commonParams.get('token')
            };
            $.post(url, params, AJAX.responseHandler);
        });
    });

    /**
     * Ajax Event handler for 'Delete tracking report entry'
     */
    $('body').on('click', 'a.delete_entry_anchor.ajax', function (e) {
        e.preventDefault();
        var $anchor = $(this);
        var question = PMA_messages.strDeletingTrackingEntry;
        $anchor.PMA_confirm(question, $anchor.attr('href'), function (url) {
            PMA_ajaxShowMessage();
            AJAX.source = $anchor;
            var params = {
                'ajax_page_request': true,
                'ajax_request': true,
                'token': PMA_commonParams.get('token')
            };
            $.post(url, params, AJAX.responseHandler);
        });
    });
});
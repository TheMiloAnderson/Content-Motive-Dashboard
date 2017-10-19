(function() {
    var btnAll = jQuery('#dealer-list-all');
    var btnNone = jQuery('#dealer-list-none');
    var checkBoxGroup = jQuery('.form-group.field-userswithdealers-dealers label input');
    btnAll.on('click', function() {
        checkBoxGroup.prop('checked', true);
    });
    btnNone.on('click', function() {
        checkBoxGroup.prop('checked', false);
    });
})();
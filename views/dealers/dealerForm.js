jQuery(document).ready(function($) {
    $('.dealers-form .add-row').click(function() {
        var rowHtml = $("#dealerProperties tr:first-of-type");
        $('#dealerProperties').prepend($("#dealerProperties tr:first-of-type"));
    });
});
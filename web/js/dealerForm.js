'use strict';
(function() {
//  window.jQuery = window.$ = require('node_modules/jquery');
//  require('yii2/assets/yii');
//  require('bower/bootstrap/dist/js/bootstrap');
    $(".dynamicform_wrapper").on("beforeInsert", function(e, item) {
        console.log("beforeInsert");
    });

    $(".dynamicform_wrapper").on("afterInsert", function(e, item) {
        var start_date = $(item).find('input').filter(function() {
            return this.id.match(/start_date/);
        });
        start_date.datepicker({dateFormat: 'yy-mm-dd'});
    });

    $(".dynamicform_wrapper").on("beforeDelete", function(e, item) {
        if (! confirm("Are you sure you want to delete this item?")) {
            return false;
        }
        return true;
    });

    $(".dynamicform_wrapper").on("afterDelete", function(e) {
        console.log("Deleted item!");
    });

    $(".dynamicform_wrapper").on("limitReached", function(e, item) {
        alert("Limit reached");
    });
})();
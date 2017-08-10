var dealerSelect = {
    template: '<select></select>'
};

var dash = new Vue({
    el: '#dealer-select',
    components: {
        'dealer-select': dealerSelect
    }
});
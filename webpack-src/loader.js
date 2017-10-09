'use strict';
const dataCache = require('./dataCache');
(function() {
    var Worker = require('./worker.js');
    var w = new Worker;
    var list = [];
    var elements = document.getElementsByClassName('dealerSelect');
    for (var i=0; i < elements.length; i++) {
        let id = elements[i].dataset.id;
        let url = elements[i].getAttribute('href');
        list[i] = {id: id, url: url};
    }
    w.onmessage = function(e) {
        Object.assign(dataCache, e.data);
    };
    w.postMessage(list);
})();
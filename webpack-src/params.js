'use strict';
var params = (function() {
    var chart = {
        width: d3.select('#chart-box #chart').node().getBoundingClientRect().width,
        height: function() {
            if (window.innerHeight > 720) { return 400; } 
                else { return 300; }
        },
        x: 50,
        y: 50,
        duration: 1500,
        dataLines: {
            lineWidth: 1,
            pageViewColor: 'steelblue',
            uniqueVisitorColor: 'midnightblue',
            entrancesColor: 'darkred',
            textDx: 5,
            textDy: '.3em',
            fontSize: '1.1em',
            opacity: 0.7
        },
        margin: {
            top: 20,
            right: 45,
            bottom: 45,
            left: 80
        },
        dateGuideOpacity: 0.3,
        mainChart: () => {
            return d3.select('.mainChart');
        }
    };
    var table = {

    };
    var dash = {
        getCurrentSites: function(d) {
            var sites = d.map(function(d) { return d.url + '__' + d.property_id; });
            var uniqueSites = Array.from(new Set(sites));
            return uniqueSites.map(function(s) { return [s.split('__')[0], s.split('__')[1]];});
        }
    };
    return {
        chart: chart,
        table: table,
        dash: dash
    };
})();
module.exports = params;
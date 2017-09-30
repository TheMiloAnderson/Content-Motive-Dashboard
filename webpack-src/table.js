'use strict';
const params = require('./params');
const d3 = require('node_modules/d3');
var table = (function() {
    var _detailsTable = jQuery('#p0');
    
    function metricReadouts(d) {
        d3.select('#pageviews-readout')
            .transition().duration(params.chart.duration)
            .tween('text', function() {
                var element = d3.select(this);
                var f = d3.format(',.0f');
                var newValue = d[d.length - 1].tot_pageviews - d[0].tot_pageviews + d[0].pageviews;
                var i = d3.interpolateNumber(element.text().replace(/,/g, ''), newValue);
                return function(t) {
                    element.text(f(i(t)));
                };
            });
        d3.select('#visitors-readout')
            .transition().duration(params.chart.duration)
            .tween('text', function() {
                var element = d3.select(this);
                var f = d3.format(',.0f');
                var newValue = d[d.length - 1].tot_visitors - d[0].tot_visitors + d[0].visitors;
                var i = d3.interpolateNumber(element.text().replace(/,/g, ''), newValue);
                return function(t) {
                    element.text(f(i(t)));
                };
            });
        d3.select('#entrances-readout')
            .transition().duration(params.chart.duration)
            .tween('text', function() {
                var element = d3.select(this);
                var f = d3.format(',.0f');
                var newValue = d[d.length - 1].tot_entrances - d[0].tot_entrances + d[0].entrances;
                var i = d3.interpolateNumber(element.text().replace(/,/g, ''), newValue);
                return function(t) {
                    element.text(f(i(t)));
                };
            });  
        d3.select('#duration-readout')
            .transition().duration(params.chart.duration)
            .tween('text', function() {
                var element = d3.select(this);
                var avg_time = 0;
                for (var i=0; i<d.length; i++) {
                    avg_time += Number(d[i].avg_time);
                }
                var avg_seconds = Math.floor(avg_time / d.length);
                var makeTime = function(time) {
                    var min = Math.floor(time / 60);
                    var sec = Math.floor(time - min * 60);
                    return min + ':' + ("0" + sec).slice(-2);
                };
                var breakTime = function(time) {
                    time = time.split(':');
                    var min = +time[0];
                    var sec = +time[1];
                    var totalSec = (min * 60) + sec;
                    return totalSec;
                };
                var elementText = element.text();
                var i = d3.interpolateNumber(breakTime(elementText), avg_seconds);
                return function(t) {
                    element.text(makeTime(i(t)));
                };
            });
        d3.select('#bounce-readout')
            .transition().duration(params.chart.duration)
            .tween('text', function() {
                var element = d3.select(this);
                var bounceRate = 0;
                for (var i=0; i<d.length; i++) {
                    bounceRate += Number(d[i].bounce_rate);
                }
                bounceRate = bounceRate / d.length;
                var f = d3.format('.1f');
                var i = d3.interpolateNumber(element.text().replace(/%/g, ''), bounceRate);
                return function(t) {
                    element.text(f(i(t)) + '%');
                };
            });
    }
    
    function detailTables(d, start = false, end = false) {
        var sites = params.dash.getCurrentSites(d);
        var url = '?r=/dashboard/details&';
        for (var i=0; i < sites.length; i++) {
            url += 'pids[' + i +  ']=' + sites[i][1] + '&';
        }
        if (start) { 
            start = start.getFullYear() + '-' + ('0' + (start.getMonth() + 1)).slice(-2) + '-' + ('0' + start.getDate()).slice(-2);
            url += 'start=' + start + '&';
        }
        if (end) { 
            end = end.getFullYear() + '-' + ('0' + (end.getMonth() + 1)).slice(-2) + '-' + ('0' + end.getDate()).slice(-2);
            url += 'end=' + end + '&';
        }
        jQuery.get(url, (data) => {
            data = '<div id="p0">' + data + '</div>';
            _detailsTable.show();
            _detailsTable.html(data);
        });
    }
    return {
        detailTables: detailTables,
        metricReadouts: metricReadouts
    };
})();
module.exports = table;
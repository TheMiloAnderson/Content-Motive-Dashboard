'use strict';
window.jQuery = window.$ = require('node_modules/jquery');
require('yii2/assets/yii');
require('bower/bootstrap/dist/js/bootstrap');
require('node_modules/jquery-ui/ui/widgets/slider');
require('./loader');
//const Spinner = require('./spin.min');
const chart = require('./chart');
const table = require('./table');
const params = require('./params');
const d3 = require('node_modules/d3');
var dash = (function() {
    var propertyRows = jQuery('.propertyFilter'),
        dealerSelect = jQuery('.dealerSelect'),
        startDateField = jQuery('#startDate'),
        endDateField = jQuery('#endDate'),
        dateSlider = jQuery('#slider-range'),
        dateRangeBtn = jQuery('#dateRangeBtn'),
        dateRangeResetBtn = jQuery('#dateRangeResetBtn'),
        dealerSubhead = jQuery('span#dealerSubhead'),
        websiteSubhead = jQuery('#websiteSubhead'),
        dealersSelectTitle = jQuery('#dealersSelectTitle'),
        websitesSelectTitle = jQuery('#websitesSelectTitle'),
        tableBox = document.getElementById('table-box'),
        chartBox = document.getElementById('chart-box')
    ;

    chart.ajaxUrl = chartBox.dataset.initialUrl;
    var _dealersList = JSON.parse(chartBox.dataset.dealerList);
    chart.changeData(function() {
        var d = chart.getDataset();
        chart.createChart();
        table.metricReadouts(d);
        table.detailTables(d);
        initializeDateSlider();
        updateSiteSelect(d);
        updateSubheads(d);
    });
    dealerSelect.click(function(e) {
        e.preventDefault();
        var dealerId = this.dataset.id;
        closeMenu('#dealers', 'toggle');
        chart.ajaxUrl = jQuery(this).attr('href');
        chart.changeData(function() {
            var d = chart.getDataset();
            chart.updateChart(d);
            table.metricReadouts(d);
            table.detailTables(d);
            resetDateSlider(d);
            updateSiteSelect(d);
            updateSubheads(d);
        }, dealerId);
    });
    propertyRows.click(function() {
        closeMenu('#websites', 'toggle');
        var pid = jQuery(this).attr('data-properties').split(',');
        for (var i=0; i<pid.length; i++) { pid[i] = +pid[i]; }
        var revisedDataset = chart.getDataset().filter(function(d) {
            return pid.includes(d.property_id);
        });
        var d = chart.prepData(revisedDataset);
        chart.setDataSubset(d);
        chart.updateChart(d);
        table.metricReadouts(d);
        table.detailTables(d);
        resetDateSlider(d);
        updateSubheads(d);
    });
    jQuery('body').on('touchstart click', function(e){
        var target = jQuery(e.target);
        if (!target.is('.dealerSelect') && !target.is('.propertyFilter')) {
            closeMenu('#dealers', 'hide');
            closeMenu('#websites', 'hide');
        }
    });
    startDateField.on('change', function() {
        var start = new Date(startDateField.val()).getTime();
        dateSlider.slider('values', 0, start / 1000);
        dateRangeBtn.data('startdate', start);
        chart.setStartDateGuide(start);
    });
    endDateField.on('change', function() {
        var end = new Date(endDateField.val()).getTime();
        dateSlider.slider('values', 1, end / 1000);
        dateRangeBtn.data('enddate', end);
        chart.setEndDateGuide(end);
    });
    dateRangeBtn.click(function() {
        var startDate = $(this).data('startdate');
        var endDate = $(this).data('enddate');
        var revisedData = chart.getDataset().filter(function(d) {
            return d.date_recorded >= startDate && d.date_recorded <= endDate;
        });
        var d = chart.prepData(revisedData);
        chart.updateChart(d);
        chart.setDataSubset(d);
        table.metricReadouts(d);
        table.detailTables(d, startDate, endDate);
        updateSubheads(d);
    });
    dateRangeResetBtn.click(function() {
        var d = chart.prepData(chart.getDataset());
        chart.updateChart(d);
        chart.setDataSubset(d);
        table.metricReadouts(d);
        table.detailTables(d);
        resetDateSlider(d);
        updateSubheads(d);
    });
    
    function closeMenu(id, method) {
        var el = jQuery(id);
        el.collapse(method);
    }
    function updateSubheads(d) {
        var id = d[0].dealer_id;
        var dealer = jQuery.grep(_dealersList, function(obj) { return +obj.id === id; });
        dealerSubhead.text(dealer[0].named);
        if(dealersSelectTitle) { dealersSelectTitle.html(dealer[0].named); }
        
        var sites = params.dash.getCurrentSites(d);
        var sitesText = sites.length === 1 ? sites[0][0] : 'All Websites';
        websiteSubhead.animate({opacity: 0}, 200, function() { 
            websiteSubhead.text(sitesText);
            websiteSubhead.animate({opacity: 1}, 300);
        });
        if (websitesSelectTitle) { websitesSelectTitle.html(sitesText); }
    }
    function updateSiteSelect(d) {
        var siteSelect = d3.select('#websites');
        siteSelect.selectAll('.propertyFilter').style('display', 'none');
        var dByUrl = params.dash.getCurrentSites(d);
        if (dByUrl.length < 2) { 
            siteSelect.style('display', 'none');
            d3.select('#websites-head').style('display', 'none');
        } else {
            siteSelect.style('display', '');
            var pids = [];
            for (var i=0; i<dByUrl.length; i++) {
                    var row = d3.select("li[data-properties='" + dByUrl[i][1] + "']");
                    row.style('display', '');
                    pids.push(dByUrl[i][1]);
                }
            jQuery('#websites-head').css('display', '');
            jQuery('.allWebsites').css('display', '');
            jQuery('.propertyFilter.allWebsites').attr('data-properties', pids.join());
        }
    }
    function initializeDateSlider() {
        var dataset = chart.getDataset();
        var min = new Date(dataset[0].date_recorded).getTime() / 1000;
        var max = new Date(dataset[dataset.length - 1].date_recorded).getTime() / 1000;
        dateSlider.slider({
            range: true,
            min: min,
            max: max,
            step: 86400,
            get values () {
                return [this.min, this.max];
            },
            slide: function(event, ui) {
                var startDate = new Date(ui.values[0] * 1000);
                var endDate = new Date(ui.values[1] * 1000);
                setDateFields(startDate, endDate);
                dateRangeBtn.data('startdate', startDate);
                dateRangeBtn.data('enddate', endDate);
                chart.setStartDateGuide(startDate);
                chart.setEndDateGuide(endDate);
            }
        });
        resetDateSlider(dataset);
    }
    function resetDateSlider(d) {
        var start = new Date(d[0].date_recorded).getTime();
        var end = new Date(d[d.length - 1].date_recorded).getTime();
        dateSlider.slider('option', 'min', start / 1000);
        dateSlider.slider('option', 'max', end / 1000);
        dateSlider.slider('values', 0, start / 1000);
        dateSlider.slider('values', 1, end / 1000);
        dateRangeBtn.data('startdate', start);
        dateRangeBtn.data('enddate', end);
        setDateFields(start, end);
    }
    function setDateFields(start, end) {
        start = new Date(start);
        end = new Date(end);
        startDateField.val(('0' + (start.getMonth() + 1)).slice(-2) + '/' + ('0' + start.getDate()).slice(-2) + '/' + start.getFullYear());
        endDateField.val(('0' + (end.getMonth() + 1)).slice(-2) + '/' + ('0' + end.getDate()).slice(-2) + '/' + end.getFullYear());
    }
//    var spinner = new Spinner(Spinner.spinOpts);
//    spinner.spin(tableBox);
})();
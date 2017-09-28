'use strict';
const chart = require('./chart');
const table = require('./table');
const params = require('./params');
var dash = (function() {
    var element = document.getElementById('chart-box');
    chart.ajaxUrl = element.dataset.initialUrl;
    chart.dealersList = element.dataset.dealerList;
    chart.changeData(function() {
        var d = chart.getDataset();
        chart.createChart();
        table.metricReadouts(d);
        table.detailTables(d);
        //initializeDateSlider();
        chart.setStartDateGuide('start');
    });
    
    var propertyRows = jQuery('.propertyFilter'),
        dealerSelect = jQuery('.dealerSelect'),
        startDateField = jQuery('#startDate'),
        endDateField = jQuery('#endDate'),
        dateRangeBtn = jQuery('#dateRangeBtn'),
        dateRangeResetBtn = jQuery('#dateRangeResetBtn'),
        dateSlider = jQuery('#slider-range')
    ;
    
    propertyRows.click(function() {
        closeMenu('#websites');
        var pid = jQuery(this).attr('data-properties').split(',');
        for (var i=0; i<pid.length; i++) {pid[i] = +pid[i]; }
        var revisedDataset = chart.getDataset().filter(function(d) {
            return pid.includes(d.property_id);
        });
        var d = chart.prepData(revisedDataset);
        chart.setDataSubset(d);
        chart.updateChart(d);
        table.metricReadouts(d);
        table.detailTables(d);
        //resetDateSlider(d);
    });
    dealerSelect.click(function(e) {
        e.preventDefault();
        var dealerId = this.dataset.id;
        closeMenu('#dealers');
        chart.ajaxUrl = jQuery(this).attr('href');
        chart.changeData(function() {
            var d = chart.getDataset();
            chart.updateChart(d);
            table.detailTables(d);
            //resetDateSlider(d);
        }, dealerId);
    });
    function closeMenu(id) {
        var el = jQuery(id);
        el.collapse('toggle');
    }
    
    function initializeDateSlider() {
        var min = new Date(dataset[0].date_recorded).getTime() / 1000;
        var max = new Date(dataset[dataset.length - 1].date_recorded).getTime() / 1000;
        function addDateGuide(id) {
            return g.append('rect')
                .attr('id', id)
                .attr('height', height)
                .attr('width', 0)
                .attr('fill', 'black')
                .attr('opacity', prms.dateGuideOpacity)
            ;
        }
        startDateGuide = addDateGuide('startDateGuide');
        endDateGuide = addDateGuide('endDateGuide');
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
                startDateGuide.attr('width', xScale(startDate));
                endDateGuide.attr('width', width - xScale(endDate)).attr('x', xScale(endDate));
            }
        });
        resetDateSlider(dataset);
    }
    startDateField.on('change', function() {
        var start = new Date(startDateField.val()).getTime();
        dateSlider.slider('values', 0, start / 1000);
        dateRangeBtn.data('startdate', start);
        //chart.startDateGuide.attr('width', xScale(start));
        chart.setStartDateGuide(start);
    });
    endDateField.on('change', function() {
        var end = new Date(endDateField.val()).getTime();
        dateSlider.slider('values', 1, end / 1000);
        dateRangeBtn.data('enddate', end);
        chart.endDateGuide.attr('width', width - xScale(end)).attr('x', xScale(end));
    });
    dateRangeBtn.click(function() {
        var startDate = $(this).data('startdate');
        var endDate = $(this).data('enddate');
        var revisedData = dataSubset.filter(function(d) {
            return d.date_recorded >= startDate && d.date_recorded <= endDate;
        });
        updateChart(prepData(revisedData));
        detailTables(prepData(revisedData), startDate, endDate);
    });
    dateRangeResetBtn.click(function() {
        var d = prepData(dataset);
        updateChart(d);
        detailTables(d);
        resetDateSlider(d);
    });

    return {

    };
})();
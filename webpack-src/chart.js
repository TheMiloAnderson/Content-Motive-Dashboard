'use strict';
window.jQuery = window.$ = require('node_modules/jquery');
const params = require('./params');
const d3 = require('node_modules/d3');
d3.ip = require('node_modules/d3-interpolate-path');
const simplify = require('node_modules/simplify-js');
const dataCache = require('./dataCache');
var chart = (function() {
    var exports = {},
        _mainChart = params.chart.mainChart(),
        _width,
        _height,
        _g,
        _startDateGuide,
        _endDateGuide,
        _xScale,
        _yScale,
        _dataset,
        _dataSubset
    ;
    exports.changeData = function(callback, id) {
        id = id || 0;
        if (dataCache[id]) {
            _dataSubset = _dataset = this.prepData(dataCache[id]);
            callback();
        } else {
            jQuery.ajax({
                url: this.ajaxUrl,
                dataType: 'text',
                method: 'GET',
                success: (data) => {
                    data = JSON.parse(data);
                    _dataset = this.prepData(data);
                    callback();
                    dataCache[id] = _dataSubset = _dataset;
                },
                error:  function(xhr) {
                    console.log(xhr);
                }
            });
        }
    };
    exports.prepData = function(data) {
        var parseTime = d3.timeParse("%Y-%m-%d");
        var tot_pageviews = 0;
        var tot_visitors = 0;
        var tot_entrances = 0;
        for (var i=0; i<data.length; i++) {
            data[i].property_id = +data[i].property_id;
            if (typeof data[i].date_recorded === 'string') { data[i].date_recorded = parseTime(data[i].date_recorded); }
            data[i].pageviews = +data[i].pageviews;
            data[i].visitors = +data[i].visitors;
            data[i].entrances = +data[i].entrances;
            data[i].avg_time = +data[i].avg_time;
            data[i].bounce_rate = +data[i].bounce_rate;
            data[i].dealer_id = +data[i].dealer_id;
            data[i].tot_pageviews = tot_pageviews += data[i].pageviews;
            data[i].tot_visitors = tot_visitors += data[i].visitors;
            data[i].tot_entrances = tot_entrances += data[i].entrances;
        }
        return data;
    };
    exports.createChart = function() {
        // Chart size, margins
        _mainChart
            .attr('width', params.chart.width)
            .attr('height', params.chart.height)
        ;
        _width = +_mainChart.attr('width') - params.chart.margin.left - params.chart.margin.right;
        _height = +_mainChart.attr('height') - params.chart.margin.top - params.chart.margin.bottom;
        _g = _mainChart.append('g')
            .attr('transform', 'translate(' + params.chart.margin.left + ',' + params.chart.margin.top + ')')
            .attr('overflow', 'visible');
        _g.append("g")
            .attr('class', 'xAxis')
            .attr("transform", "translate(0," + _height + ")");
        _g.append("g")
            .attr('class', 'yAxis');
        var g2 = _g.append('g')
            .attr('opacity', params.chart.dataLines.opacity);
        function addPath(lineClass, color) {
            g2.append("path")
                .attr('class', lineClass)
                .attr("fill", color)
                .attr("stroke", 'black')
                .attr("stroke-width", params.chart.dataLines.lineWidth)
            ;
        }
        addPath('pageviews', params.chart.dataLines.pageViewColor);
        addPath('visitors', params.chart.dataLines.uniqueVisitorColor);
        addPath('entrances', params.chart.dataLines.entrancesColor);
        function addDateGuide(id) {
            return _g.append('rect')
                .attr('id', id)
                .attr('height', _height)
                .attr('width', 0)
                .attr('fill', 'black')
                .attr('opacity', params.chart.dateGuideOpacity)
            ;
        }
        _startDateGuide = addDateGuide('startDateGuide');
        _endDateGuide = addDateGuide('endDateGuide');
        this.updateChart(_dataset);
    };
    exports.updateChart = function(d) {
        var svg = _mainChart.transition();
        // Define the D3 scales
        _xScale = d3.scaleTime()
            .range([0, _width])
            .domain(d3.extent(d.map(function(d) { return d.date_recorded; })))
        ;
        var yDomainMin = d3.min(d.map(function(d) { return d.tot_entrances; }));
        var yDomainMax = d3.max(d.map(function(d) { return d.tot_pageviews; }));
        _yScale = d3.scaleLinear()
            .range([_height, 0])
            .domain([yDomainMin, yDomainMax * 1.1])
        ;
        // Axes
        var formatDay = d3.timeFormat("%a %d"),
            formatWeek = d3.timeFormat("%b %d"),
            formatMonth = d3.timeFormat("%b"),
            formatYear = d3.timeFormat("%Y");
        function multiFormat(date) {
          return (d3.timeMonth(date) < date ? (d3.timeWeek(date) < date ? formatDay : formatWeek)
              : d3.timeYear(date) < date ? formatMonth
              : formatYear)(date);
        }
        var bottomAxis = function() {
            return d3.axisBottom(_xScale)
                .ticks(7)
                .tickFormat(multiFormat)
                .tickPadding(10)
                .tickSizeInner((-1 * _height))
                .tickSizeOuter(0)
            ;
        };
        svg.select('.xAxis')
            .duration(params.chart.duration)
            .call(bottomAxis())
        ;
        var leftAxis = function() {
            return d3.axisLeft(_yScale)
                .ticks(8)
                .tickPadding(10)
                .tickSizeInner(-1 * _width)
                .tickSizeOuter(0)
            ;
        };
        svg.select('.yAxis')
            .duration(params.chart.duration)
            .call(leftAxis())
        ;
        _lineTransitions(d,'tot_pageviews', '.pageviews');
        _lineTransitions(d, 'tot_visitors', '.visitors');
        _lineTransitions(d, 'tot_entrances', '.entrances');
        svg.select('#startDateGuide')
            .duration(params.chart.duration)
            .attr('width', _xScale(d[0].date_recorded))
        ;
        svg.select('#endDateGuide')
            .duration(params.chart.duration)
            .attr('width', _width - _xScale(d[d.length - 1].date_recorded))
            .attr('x', _xScale(d[d.length - 1].date_recorded))
        ;
        _formatTicks();
    };
    var _lineTransitions = function(d, dataCol, lineClass) {
        // horrors...
        var lineD = d3.area()
            .x(function(d) { return _xScale(d.date_recorded); })
            .y1(function(d) { return _yScale(d[dataCol]); });
        lineD.y0(_yScale(0));
        lineD = lineD(d);
        var originalPath = lineD.substring(1, lineD.length-1);
        originalPath = originalPath.split('L');
        var pathCoordinates1 = [];
        var pathCoordinates2 = [];
        var endLine = '';
        var secondSegment = false;
        var lineLength = originalPath.length;
        for (var i=0; i<lineLength; i++) {
            var coords = originalPath[i].split(',');
            coords[0] = Number(coords[0]);
            coords[1] = Number(coords[1]);
            var coordsObj = {x: coords[0], y: coords[1]};
            if ((coords[0] < _width) && (!secondSegment)) {
                pathCoordinates1.push(coordsObj);
            } else if (coords[0] === _width) {
                secondSegment = true;
                endLine += originalPath[i] + 'L';
            } else if (secondSegment) {
                pathCoordinates2.push(coordsObj);
            }
        }
        pathCoordinates1 = simplify(pathCoordinates1, .5);
        pathCoordinates2 = simplify(pathCoordinates2, .5);
        var increment = pathCoordinates2[0]['x'] / pathCoordinates1.length;
        for (var i=1; i<=pathCoordinates1.length - 1; i++) {
            var item = {x: pathCoordinates2[0]['x'] - increment * i, y: pathCoordinates2[0]['y']};
            pathCoordinates2.splice(i, 0, item);
        }
        lineD = 'M';
        for (var i=0; i<pathCoordinates1.length; i++) {
            lineD += pathCoordinates1[i]['x'] + ',' + pathCoordinates1[i]['y'] + 'L';
        }
        lineD += endLine;
        for (var i=0; i<pathCoordinates2.length; i++) {
            lineD += pathCoordinates2[i]['x'] + ',' + pathCoordinates2[i]['y'] + 'L';
        }
        lineD = lineD.substring(0, lineD.length-1) + 'Z';
        _mainChart.transition().select(lineClass)
            .duration(params.chart.duration)
            .attrTween('d', function() {
                var previous = d3.select(this).attr('d') || lineD;
                var current = lineD;
                function exclude(a, b) {
                    return a.x === b.x;
                }
                return d3.ip.interpolatePath(previous, current, exclude);
            }); 
    };
    var _formatTicks = function() {
        d3.selectAll('.yAxis .tick line')
            .attr('stroke-dasharray', '4,2')
            .attr('stroke', 'gray')
        ;
        d3.selectAll('.xAxis .tick line')
            .attr('stroke', 'lightgray')
        ;
        d3.selectAll('.yAxis .tick text')
            .style('font-size', '14px');
        d3.selectAll('.xAxis .tick text')
            .style('font-size', '14px');
        d3.timer(function() {
            d3.selectAll('.xAxis .tick > text')
            .style('font-weight', function() {
                var el = d3.select(this);
                if (!isNaN(el.text())) { return 'bold'; } else { return ''; }
            });
        }, 100);
    };
    var resizeId;
    d3.select(window).on('resize', function() {
        clearTimeout(resizeId);
        resizeId = setTimeout(resize, 100);
    });
    function resize() {
        params.chart.width = parseInt(d3.select('#chart-box #chart').style('width'), 10);
        _mainChart.attr('width', params.chart.width).attr('height', params.chart.height);
        _width = +_mainChart.attr('width') - params.chart.margin.left - params.chart.margin.right;
        _height = +_mainChart.attr('height') - params.chart.margin.top - params.chart.margin.bottom;
        this.updateChart(_dataSubset);
    }
    exports.setDataset = function(d) { _dataset = d; };
    exports.getDataset = function() { return _dataset; };
    exports.setDataSubset = function(d) { _dataSubset = d; };
    exports.getDataSubset = function() { return _dataSubset; };
    exports.setStartDateGuide = function(date) {
        _startDateGuide.attr('width', _xScale(date));
    };
    exports.setEndDateGuide = function(date) {
        _endDateGuide.attr('width', _width - _xScale(date)).attr('x', _xScale(date));
    };
    return exports;
})();
module.exports = chart;
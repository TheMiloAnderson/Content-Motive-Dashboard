/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var params = function () {
    var chart = {
        width: d3.select('#chart-box #chart').node().getBoundingClientRect().width,
        height: function height() {
            if (window.innerHeight > 720) {
                return 400;
            } else {
                return 300;
            }
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
        mainChart: function mainChart() {
            return d3.select('.mainChart');
        }
    };
    var table = {};
    var dash = {
        getCurrentSites: function getCurrentSites(d) {
            var sites = d.map(function (d) {
                return d.url + '__' + d.property_id;
            });
            var uniqueSites = Array.from(new Set(sites));
            return uniqueSites.map(function (s) {
                return [s.split('__')[0], s.split('__')[1]];
            });
        }
    };
    return {
        chart: chart,
        table: table,
        dash: dash
    };
}();
module.exports = params;

/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var chart = __webpack_require__(2);
var table = __webpack_require__(3);
var params = __webpack_require__(0);
var dash = function () {
    var element = document.getElementById('chart-box');
    chart.ajaxUrl = element.dataset.initialUrl;
    chart.dealersList = element.dataset.dealerList;
    chart.changeData(function () {
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
        dateSlider = jQuery('#slider-range');

    propertyRows.click(function () {
        closeMenu('#websites');
        var pid = jQuery(this).attr('data-properties').split(',');
        for (var i = 0; i < pid.length; i++) {
            pid[i] = +pid[i];
        }
        var revisedDataset = chart.getDataset().filter(function (d) {
            return pid.includes(d.property_id);
        });
        var d = chart.prepData(revisedDataset);
        chart.setDataSubset(d);
        chart.updateChart(d);
        table.metricReadouts(d);
        table.detailTables(d);
        //resetDateSlider(d);
    });
    dealerSelect.click(function (e) {
        e.preventDefault();
        var dealerId = this.dataset.id;
        closeMenu('#dealers');
        chart.ajaxUrl = jQuery(this).attr('href');
        chart.changeData(function () {
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
            return g.append('rect').attr('id', id).attr('height', height).attr('width', 0).attr('fill', 'black').attr('opacity', prms.dateGuideOpacity);
        }
        startDateGuide = addDateGuide('startDateGuide');
        endDateGuide = addDateGuide('endDateGuide');
        dateSlider.slider({
            range: true,
            min: min,
            max: max,
            step: 86400,
            get values() {
                return [this.min, this.max];
            },
            slide: function slide(event, ui) {
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
    startDateField.on('change', function () {
        var start = new Date(startDateField.val()).getTime();
        dateSlider.slider('values', 0, start / 1000);
        dateRangeBtn.data('startdate', start);
        //chart.startDateGuide.attr('width', xScale(start));
        chart.setStartDateGuide(start);
    });
    endDateField.on('change', function () {
        var end = new Date(endDateField.val()).getTime();
        dateSlider.slider('values', 1, end / 1000);
        dateRangeBtn.data('enddate', end);
        chart.endDateGuide.attr('width', width - xScale(end)).attr('x', xScale(end));
    });
    dateRangeBtn.click(function () {
        var startDate = $(this).data('startdate');
        var endDate = $(this).data('enddate');
        var revisedData = dataSubset.filter(function (d) {
            return d.date_recorded >= startDate && d.date_recorded <= endDate;
        });
        updateChart(prepData(revisedData));
        detailTables(prepData(revisedData), startDate, endDate);
    });
    dateRangeResetBtn.click(function () {
        var d = prepData(dataset);
        updateChart(d);
        detailTables(d);
        resetDateSlider(d);
    });

    return {};
}();

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var params = __webpack_require__(0);
//const d3 = require('../node_modules/d3');
var chart = function () {
    var _mainChart = params.chart.mainChart(),
        _width,
        _height,
        _g,
        _xScale,
        _yScale,
        _dataset,
        _dataSubset,
        _dataCache = {};
    function setDataset(d) {
        _dataset = d;
    }
    function getDataset() {
        return _dataset;
    }
    function setDataSubset(d) {
        _dataSubset = d;
    }
    function getDataSubset() {
        return _dataSubset;
    }
    function changeData(callback, id) {
        id = id || 0;
        if (_dataCache[id]) {
            _dataset = _dataCache[id];
            callback();
            _dataSubset = _dataset;
        } else {
            jQuery.ajax({
                url: this.ajaxUrl,
                dataType: 'text',
                method: 'GET',
                success: function success(data) {
                    data = JSON.parse(data);
                    _dataset = prepData(data);
                    callback();
                    _dataCache[id] = _dataSubset = _dataset;
                },
                error: function error(xhr) {
                    console.log(xhr);
                }
            });
        }
    };
    function prepData(data) {
        var parseTime = d3.timeParse("%Y-%m-%d");
        var tot_pageviews = 0;
        var tot_visitors = 0;
        var tot_entrances = 0;
        for (var i = 0; i < data.length; i++) {
            data[i].property_id = +data[i].property_id;
            if (typeof data[i].date_recorded === 'string') {
                data[i].date_recorded = parseTime(data[i].date_recorded);
            }
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
    var createChart = function createChart() {
        // Chart size, margins
        _mainChart.attr('width', params.chart.width).attr('height', params.chart.height);
        _width = +_mainChart.attr('width') - params.chart.margin.left - params.chart.margin.right;
        _height = +_mainChart.attr('height') - params.chart.margin.top - params.chart.margin.bottom;
        _g = _mainChart.append('g').attr('transform', 'translate(' + params.chart.margin.left + ',' + params.chart.margin.top + ')').attr('overflow', 'visible');
        _g.append("g").attr('class', 'xAxis').attr("transform", "translate(0," + _height + ")");
        _g.append("g").attr('class', 'yAxis');
        var g2 = _g.append('g').attr('opacity', params.chart.dataLines.opacity);
        function addPath(lineClass, color) {
            g2.append("path").attr('class', lineClass).attr("fill", color).attr("stroke", 'black').attr("stroke-width", params.chart.dataLines.lineWidth);
        }
        addPath('pageviews', params.chart.dataLines.pageViewColor);
        addPath('visitors', params.chart.dataLines.uniqueVisitorColor);
        addPath('entrances', params.chart.dataLines.entrancesColor);
        updateChart(_dataset);
    };
    var updateChart = function updateChart(d) {
        var svg = _mainChart.transition();
        // Define the D3 scales
        _xScale = d3.scaleTime().range([0, _width]).domain(d3.extent(d.map(function (d) {
            return d.date_recorded;
        })));
        var yDomainMin = d3.min(d.map(function (d) {
            return d.tot_entrances;
        }));
        var yDomainMax = d3.max(d.map(function (d) {
            return d.tot_pageviews;
        }));
        _yScale = d3.scaleLinear().range([_height, 0]).domain([yDomainMin, yDomainMax * 1.1]);
        // Axes
        var formatDay = d3.timeFormat("%a %d"),
            formatWeek = d3.timeFormat("%b %d"),
            formatMonth = d3.timeFormat("%b"),
            formatYear = d3.timeFormat("%Y");
        function multiFormat(date) {
            return (d3.timeMonth(date) < date ? d3.timeWeek(date) < date ? formatDay : formatWeek : d3.timeYear(date) < date ? formatMonth : formatYear)(date);
        }
        var bottomAxis = function bottomAxis() {
            return d3.axisBottom(_xScale).ticks(7).tickFormat(multiFormat).tickPadding(10).tickSizeInner(-1 * _height).tickSizeOuter(0);
        };
        svg.select('.xAxis').duration(params.chart.duration).call(bottomAxis());
        var leftAxis = function leftAxis() {
            return d3.axisLeft(_yScale).ticks(8).tickPadding(10).tickSizeInner(-1 * _width).tickSizeOuter(0);
        };
        svg.select('.yAxis').duration(params.chart.duration).call(leftAxis());
        _lineTransitions(d, 'tot_pageviews', '.pageviews');
        _lineTransitions(d, 'tot_visitors', '.visitors');
        _lineTransitions(d, 'tot_entrances', '.entrances');
        svg.select('#startDateGuide').duration(params.chart.duration).attr('width', _xScale(d[0].date_recorded));
        svg.select('#endDateGuide').duration(params.chart.duration).attr('width', _width - _xScale(d[d.length - 1].date_recorded)).attr('x', _xScale(d[d.length - 1].date_recorded));
        _formatTicks();
    };
    var _lineTransitions = function _lineTransitions(d, dataCol, lineClass) {
        // horrors...
        var lineD = d3.area().x(function (d) {
            return _xScale(d.date_recorded);
        }).y1(function (d) {
            return _yScale(d[dataCol]);
        });
        lineD.y0(_yScale(0));
        lineD = lineD(d);
        var originalPath = lineD.substring(1, lineD.length - 1);
        originalPath = originalPath.split('L');
        var pathCoordinates1 = [];
        var pathCoordinates2 = [];
        var endLine = '';
        var secondSegment = false;
        var lineLength = originalPath.length;
        for (var i = 0; i < lineLength; i++) {
            var coords = originalPath[i].split(',');
            coords[0] = Number(coords[0]);
            coords[1] = Number(coords[1]);
            var coordsObj = { x: coords[0], y: coords[1] };
            if (coords[0] < _width && !secondSegment) {
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
        for (var i = 1; i <= pathCoordinates1.length - 1; i++) {
            var item = { x: pathCoordinates2[0]['x'] - increment * i, y: pathCoordinates2[0]['y'] };
            pathCoordinates2.splice(i, 0, item);
        }
        lineD = 'M';
        for (var i = 0; i < pathCoordinates1.length; i++) {
            lineD += pathCoordinates1[i]['x'] + ',' + pathCoordinates1[i]['y'] + 'L';
        }
        lineD += endLine;
        for (var i = 0; i < pathCoordinates2.length; i++) {
            lineD += pathCoordinates2[i]['x'] + ',' + pathCoordinates2[i]['y'] + 'L';
        }
        lineD = lineD.substring(0, lineD.length - 1) + 'Z';
        _mainChart.transition().select(lineClass).duration(params.chart.duration).attrTween('d', function () {
            var previous = d3.select(this).attr('d') || lineD;
            var current = lineD;
            function exclude(a, b) {
                return a.x === b.x;
            }
            return d3.interpolatePath(previous, current, exclude);
        });
    };
    var _formatTicks = function _formatTicks() {
        d3.selectAll('.yAxis .tick line').attr('stroke-dasharray', '4,2').attr('stroke', 'gray');
        d3.selectAll('.xAxis .tick line').attr('stroke', 'lightgray');
        d3.selectAll('.yAxis .tick text').style('font-size', '14px');
        d3.selectAll('.xAxis .tick text').style('font-size', '14px');
        d3.timer(function () {
            d3.selectAll('.xAxis .tick > text').style('font-weight', function () {
                var el = d3.select(this);
                if (!isNaN(el.text())) {
                    return 'bold';
                } else {
                    return '';
                }
            });
        }, 100);
    };
    var resizeId;
    d3.select(window).on('resize', function () {
        clearTimeout(resizeId);
        resizeId = setTimeout(resize, 100);
    });
    function resize() {
        params.chart.width = parseInt(d3.select('#chart-box #chart').style('width'), 10);
        //prms.height = parseInt(d3.select('#chart-box #chart').style('height'), 10);
        _mainChart.attr('width', params.chart.width).attr('height', params.chart.height);
        _width = +_mainChart.attr('width') - params.chart.margin.left - params.chart.margin.right;
        _height = +_mainChart.attr('height') - params.chart.margin.top - params.chart.margin.bottom;
        _updateChart(_dataSubset);
    }
    function setStartDateGuide(start) {
        console.log(this);
    }
    return {
        changeData: changeData,
        prepData: prepData,
        createChart: createChart,
        updateChart: updateChart,
        ajaxUrl: '',
        dataCache: [],
        getDataset: getDataset,
        setDataSubset: setDataSubset,
        getDataSubset: getDataSubset,
        dataSubset: null,
        dealersList: [],
        setStartDateGuide: setStartDateGuide
    };
}();
module.exports = chart;

/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var params = __webpack_require__(0);
var table = function () {
    var _detailsTable = jQuery('#p0');

    function metricReadouts(d) {
        d3.select('#pageviews-readout').transition().duration(params.chart.duration).tween('text', function () {
            var element = d3.select(this);
            var f = d3.format(',.0f');
            var newValue = d[d.length - 1].tot_pageviews - d[0].tot_pageviews + d[0].pageviews;
            var i = d3.interpolateNumber(element.text().replace(/,/g, ''), newValue);
            return function (t) {
                element.text(f(i(t)));
            };
        });
        d3.select('#visitors-readout').transition().duration(params.chart.duration).tween('text', function () {
            var element = d3.select(this);
            var f = d3.format(',.0f');
            var newValue = d[d.length - 1].tot_visitors - d[0].tot_visitors + d[0].visitors;
            var i = d3.interpolateNumber(element.text().replace(/,/g, ''), newValue);
            return function (t) {
                element.text(f(i(t)));
            };
        });
        d3.select('#entrances-readout').transition().duration(params.chart.duration).tween('text', function () {
            var element = d3.select(this);
            var f = d3.format(',.0f');
            var newValue = d[d.length - 1].tot_entrances - d[0].tot_entrances + d[0].entrances;
            var i = d3.interpolateNumber(element.text().replace(/,/g, ''), newValue);
            return function (t) {
                element.text(f(i(t)));
            };
        });
        d3.select('#duration-readout').transition().duration(params.chart.duration).tween('text', function () {
            var element = d3.select(this);
            var avg_time = 0;
            for (var i = 0; i < d.length; i++) {
                avg_time += Number(d[i].avg_time);
            }
            var avg_seconds = Math.floor(avg_time / d.length);
            var makeTime = function makeTime(time) {
                var min = Math.floor(time / 60);
                var sec = Math.floor(time - min * 60);
                return min + ':' + ("0" + sec).slice(-2);
            };
            var breakTime = function breakTime(time) {
                time = time.split(':');
                var min = +time[0];
                var sec = +time[1];
                var totalSec = min * 60 + sec;
                return totalSec;
            };
            var elementText = element.text();
            var i = d3.interpolateNumber(breakTime(elementText), avg_seconds);
            return function (t) {
                element.text(makeTime(i(t)));
            };
        });
        d3.select('#bounce-readout').transition().duration(params.chart.duration).tween('text', function () {
            var element = d3.select(this);
            var bounceRate = 0;
            for (var i = 0; i < d.length; i++) {
                bounceRate += Number(d[i].bounce_rate);
            }
            bounceRate = bounceRate / d.length;
            var f = d3.format('.1f');
            var i = d3.interpolateNumber(element.text().replace(/%/g, ''), bounceRate);
            return function (t) {
                element.text(f(i(t)) + '%');
            };
        });
    }

    function detailTables(d) {
        var start = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
        var end = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

        var sites = params.dash.getCurrentSites(d);
        var url = '?r=/dashboard/details&';
        for (var i = 0; i < sites.length; i++) {
            url += 'pids[' + i + ']=' + sites[i][1] + '&';
        }
        if (start) {
            start = start.getFullYear() + '-' + ('0' + (start.getMonth() + 1)).slice(-2) + '-' + ('0' + start.getDate()).slice(-2);
            url += 'start=' + start + '&';
        }
        if (end) {
            end = end.getFullYear() + '-' + ('0' + (end.getMonth() + 1)).slice(-2) + '-' + ('0' + end.getDate()).slice(-2);
            url += 'end=' + end + '&';
        }
        jQuery.get(url, function (data) {
            data = '<div id="p0">' + data + '</div>';
            _detailsTable.show();
            _detailsTable.html(data);
        });
    }
    return {
        detailTables: detailTables,
        metricReadouts: metricReadouts
    };
}();
module.exports = table;

/***/ })
/******/ ]);
//# sourceMappingURL=dashboard.bundle.js.map
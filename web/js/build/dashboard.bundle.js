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
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(1);
__webpack_require__(2);
module.exports = __webpack_require__(3);


/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Chart = function () {
    function Chart(url, dealers) {
        _classCallCheck(this, Chart);

        this._prms = {
            width: d3.select('#chart-box #chart').node().getBoundingClientRect().width, // outer width
            height: function height() {
                // outer height
                var windowHeight = window.innerHeight;
                if (windowHeight > 720) {
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
            dateGuideOpacity: 0.3
        };
        this.dataCache = []; // all data for a user
        this._dataset = []; // all data for a dealer
        this._dataSubset = []; // dealer data filtered by date or website
        this._mainChart = d3.select('.mainChart');
        this._g = this._mainChart.append('g');
        this._startDateGuide;
        this._endDateGuide;
        this._height; // inner height
        this._width; // inner width
        this._xScale;
        this._yScale;
        this.ajaxUrl = url;
        this.dealersList = dealers;
        this.changeData(this.createChart.bind(this), this.dealersList[0].id);
        this.Table;
    }

    _createClass(Chart, [{
        key: 'changeData',
        value: function changeData(callback, id) {
            var _this = this;

            if (this.dataCache[id]) {
                this._dataset = this.dataCache[id];
                callback();
                this._dataSubset = this._dataset;
            } else {
                jQuery.ajax({
                    url: this.ajaxUrl,
                    dataType: 'text',
                    method: 'GET',
                    success: function success(data) {
                        data = JSON.parse(data);
                        _this._dataset = _this.prepData(data);
                        callback();
                        _this.dataCache[id] = _this._dataSubset = _this._dataset;
                    },
                    error: function error(xhr) {
                        console.log(xhr);
                    }
                });
            }
        }
    }, {
        key: 'prepData',
        value: function prepData(data) {
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
        }
    }, {
        key: 'createChart',
        value: function createChart() {
            var _this2 = this;

            this._mainChart.attr('width', this._prms.width).attr('height', this._prms.height);
            this._width = +this._mainChart.attr('width') - this._prms.margin.left - this._prms.margin.right;
            this._height = +this._mainChart.attr('height') - this._prms.margin.top - this._prms.margin.bottom;
            this._g.attr('transform', 'translate(' + this._prms.margin.left + ',' + this._prms.margin.top + ')').attr('overflow', 'visible');
            this._g.append("g").attr('class', 'xAxis').attr("transform", "translate(0," + this._height + ")");
            this._g.append("g").attr('class', 'yAxis');
            var g2 = this._g.append('g').attr('opacity', this._prms.dataLines.opacity);
            var addPath = function addPath(lineClass, color) {
                g2.append("path").attr('class', lineClass).attr("fill", color).attr("stroke", 'black').attr("stroke-width", _this2._prms.dataLines.lineWidth);
            };
            addPath('pageviews', this._prms.dataLines.pageViewColor);
            addPath('visitors', this._prms.dataLines.uniqueVisitorColor);
            addPath('entrances', this._prms.dataLines.entrancesColor);
            this.updateChart(this._dataset);
        }
    }, {
        key: 'updateChart',
        value: function updateChart(d) {
            var _this3 = this;

            var svg = this._mainChart.transition();
            // Define the D3 scales
            this._xScale = d3.scaleTime().range([0, this._width]).domain(d3.extent(d.map(function (d) {
                return d.date_recorded;
            })));
            var yDomainMin = d3.min(d.map(function (d) {
                return d.tot_entrances;
            }));
            var yDomainMax = d3.max(d.map(function (d) {
                return d.tot_pageviews;
            }));
            this._yScale = d3.scaleLinear().range([this._height, 0]).domain([yDomainMin, yDomainMax * 1.1]);
            // Axes
            var formatDay = d3.timeFormat("%a %d"),
                formatWeek = d3.timeFormat("%b %d"),
                formatMonth = d3.timeFormat("%b"),
                formatYear = d3.timeFormat("%Y");
            function multiFormat(date) {
                return (d3.timeMonth(date) < date ? d3.timeWeek(date) < date ? formatDay : formatWeek : d3.timeYear(date) < date ? formatMonth : formatYear)(date);
            }
            var bottomAxis = function bottomAxis() {
                return d3.axisBottom(_this3._xScale).ticks(7).tickFormat(multiFormat).tickPadding(10).tickSizeInner(-1 * _this3._height).tickSizeOuter(0);
            };
            svg.select('.xAxis').duration(this._prms.duration).call(bottomAxis());
            var leftAxis = function leftAxis() {
                return d3.axisLeft(_this3._yScale).ticks(8).tickPadding(10).tickSizeInner(-1 * _this3._width).tickSizeOuter(0);
            };
            svg.select('.yAxis').duration(this._prms.duration).call(leftAxis());
            // Do the transitions
            this.lineTransitions(d, 'tot_pageviews', '.pageviews', '#pageviewsText');
            this.lineTransitions(d, 'tot_visitors', '.visitors', '#visitorsText');
            this.lineTransitions(d, 'tot_entrances', '.entrances', '#entrancesText');
            svg.select('#startDateGuide').duration(this._prms.duration).attr('width', this._xScale(d[0].date_recorded));
            svg.select('#endDateGuide').duration(this._prms.duration).attr('width', this._width - this._xScale(d[d.length - 1].date_recorded)).attr('x', this._xScale(d[d.length - 1].date_recorded));
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
            this.metricReadouts(d);
            this.Table.detailTables(d);
        }
    }, {
        key: 'lineTransitions',
        value: function lineTransitions(d, dataCol, lineClass, txtId) {
            var _this4 = this;

            // horrors...
            var lineD = d3.area().x(function (d) {
                return _this4._xScale(d.date_recorded);
            }).y1(function (d) {
                return _this4._yScale(d[dataCol]);
            });
            lineD.y0(this._yScale(0));
            lineD = lineD(d);
            var originalPath = lineD.substring(1, lineD.length - 1);
            originalPath = originalPath.split('L');
            var pathCoordinates1 = [];
            var pathCoordinates2 = [];
            var endLine = '';
            var secondSegment = false;
            var lineLength = originalPath.length;
            for (var _i = 0; _i < lineLength; _i++) {
                var coords = originalPath[_i].split(',');
                coords[0] = Number(coords[0]);
                coords[1] = Number(coords[1]);
                var coordsObj = { x: coords[0], y: coords[1] };
                if (coords[0] < this._width && !secondSegment) {
                    pathCoordinates1.push(coordsObj);
                } else if (coords[0] === this._width) {
                    secondSegment = true;
                    endLine += originalPath[_i] + 'L';
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
            this._mainChart.transition().select(lineClass).duration(this._prms.duration).attrTween('d', function () {
                var previous = d3.select(this).attr('d') || lineD;
                var current = lineD;
                function exclude(a, b) {
                    return a.x === b.x;
                }
                return d3.interpolatePath(previous, current, exclude);
            });
            this._mainChart.transition().select(txtId).duration(this._prms.duration).attr("transform", "translate(" + (this._width + 3) + "," + this._yScale(d[d.length - 1][dataCol]) + ")");
        }
    }, {
        key: 'metricReadouts',
        value: function metricReadouts(d) {
            d3.select('#pageviews-readout').transition().duration(this._prms.duration).tween('text', function () {
                var element = d3.select(this);
                var f = d3.format(',.0f');
                var newValue = d[d.length - 1].tot_pageviews - d[0].tot_pageviews + d[0].pageviews;
                var i = d3.interpolateNumber(element.text().replace(/,/g, ''), newValue);
                return function (t) {
                    element.text(f(i(t)));
                };
            });
            d3.select('#visitors-readout').transition().duration(this._prms.duration).tween('text', function () {
                var element = d3.select(this);
                var f = d3.format(',.0f');
                var newValue = d[d.length - 1].tot_visitors - d[0].tot_visitors + d[0].visitors;
                var i = d3.interpolateNumber(element.text().replace(/,/g, ''), newValue);
                return function (t) {
                    element.text(f(i(t)));
                };
            });
            d3.select('#entrances-readout').transition().duration(this._prms.duration).tween('text', function () {
                var element = d3.select(this);
                var f = d3.format(',.0f');
                var newValue = d[d.length - 1].tot_entrances - d[0].tot_entrances + d[0].entrances;
                var i = d3.interpolateNumber(element.text().replace(/,/g, ''), newValue);
                return function (t) {
                    element.text(f(i(t)));
                };
            });
            d3.select('#duration-readout').transition().duration(this._prms.duration).tween('text', function () {
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
            d3.select('#bounce-readout').transition().duration(this._prms.duration).tween('text', function () {
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
    }]);

    return Chart;
}();

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Table = function () {
    function Table() {
        _classCallCheck(this, Table);

        this._detailsTable = jQuery('#p0');
    }

    _createClass(Table, [{
        key: 'detailTables',
        value: function detailTables(d) {
            var _this = this;

            var start = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
            var end = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

            var sites = getCurrentSites(d);
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
                _this._detailsTable.show();
                _this._detailsTable.html(data);
            });
        }
    }]);

    return Table;
}();

/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


(function () {
    ChartInst = new Chart(initialDataUrl, dealerList);
    ChartInst.Table = new Table();
})();

/***/ })
/******/ ]);
//# sourceMappingURL=dashboard.bundle.js.map
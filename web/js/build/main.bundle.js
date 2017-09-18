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
module.exports = __webpack_require__(2);


/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var calculateMonthlyPayment = function calculateMonthlyPayment(principal, years, rate) {
	var monthlyRate = 0;
	if (rate) {
		monthlyRate = rate / 100 / 12;
	}
	var monthlyPayment = principal * monthlyRate / (1 - Math.pow(1 / (1 + monthlyRate), years * 12));
	return { principal: principal, years: years, rate: rate, monthlyPayment: monthlyPayment, monthlyRate: monthlyRate };
};

var calculateAmortization = function calculateAmortization(principal, years, rate) {
	var _calculateMonthlyPaym = calculateMonthlyPayment(principal, years, rate),
	    monthlyRate = _calculateMonthlyPaym.monthlyRate,
	    monthlyPayment = _calculateMonthlyPaym.monthlyPayment;

	var balance = principal;
	var amortization = [];
	for (var y = 0; y < years; y++) {
		var interestY = 0;
		var principalY = 0;
		for (var m = 0; m < 12; m++) {
			var interestM = balance * monthlyRate;
			var principalM = monthlyPayment - interestM;
			interestY = interestY + interestM;
			principalY = principalY + principalM;
			balance = balance - principalM;
		}
		amortization.push({ principalY: principalY, interestY: interestY, balance: balance });
	}
	return { monthlyPayment: monthlyPayment, monthlyRate: monthlyRate, amortization: amortization };
};

document.getElementById('calcBtn').addEventListener('click', function () {
	var principal = document.getElementById("principal").value;
	var years = document.getElementById("years").value;
	var rate = document.getElementById("rate").value;

	var _calculateAmortizatio = calculateAmortization(principal, years, rate),
	    monthlyPayment = _calculateAmortizatio.monthlyPayment,
	    monthlyRate = _calculateAmortizatio.monthlyRate,
	    amortization = _calculateAmortizatio.amortization;

	document.getElementById("monthlyPayment").innerHTML = monthlyPayment.toFixed(2);
	document.getElementById("monthlyRate").innerHTML = (monthlyRate * 100).toFixed(2);
	amortization.forEach(function (month) {
		return console.log(month);
	});
});

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var dashboard = function () {
    'use strict';
    //***** Parameters & global variables *****//

    var prms = {
        width: d3.select('#chart-box #chart').node().getBoundingClientRect().width,
        height: 400,
        x: 50,
        y: 50,
        duration: 1500,
        dataLines: {
            lineWidth: 1,
            pageViewColor: 'steelblue',
            uniqueVisitorColor: 'midnightblue',
            entrancesColor: 'darkred',
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
    var ajaxUrl,
        dataCache = [],
        // all data for a user (eventually)
    dataset = [],
        // all data for a dealer
    dataSubset = [],
        // dealer data filtered by date or website
    dealersList = [],
        mainChart,
        g,
        bottomAxis,
        leftAxis,
        startDateGuide,
        endDateGuide,
        height,
        width,
        xScale,
        yScale;

    var dealerSubhead = jQuery('span#dealerSubhead');
    var websiteSubhead = jQuery('#websiteSubhead');

    var dealersSelectTitle = document.getElementById('dealersSelectTitle');
    var websitesSelectTitle = document.getElementById('websitesSelectTitle');

    var dealerSelect = jQuery('.dealerSelect');
    var dateRangeBtn = jQuery('#dateRangeBtn');
    var dateRangeResetBtn = jQuery('#dateRangeResetBtn');
    var dateSlider = jQuery('#slider-range');
    var startDateField = jQuery('#startDate');
    var endDateField = jQuery('#endDate');

    var propertyRows = jQuery('.propertyFilter');
    var detailsTable = jQuery('#p0');

    //***** UI controls *****//
    //
    function initUi() {
        propertyRows.click(function () {
            closeMenu('#websites');
            var pid = jQuery(this).attr('data-properties').split(',');
            for (var i = 0; i < pid.length; i++) {
                pid[i] = +pid[i];
            }
            var revisedDataset = dataset.filter(function (d) {
                return pid.includes(d.property_id);
            });
            dataSubset = prepData(revisedDataset);
            updateChart(dataSubset);
            detailTables(dataSubset);
            resetDateSlider(dataSubset);
        });
        // Click dealer/host names, get new dataset
        dealerSelect.click(function (e) {
            e.preventDefault();
            var dealerId = this.dataset.id;
            closeMenu('#dealers');
            ajaxUrl = jQuery(this).attr('href');
            changeData(function () {
                updateChart(dataset);
                detailTables(dataset);
                resetDateSlider(dataset);
            }, dealerId);
        });
        // Filter dataset by date, update the chart
        startDateField.on('change', function () {
            var start = new Date(startDateField.val()).getTime();
            dateSlider.slider('values', 0, start / 1000);
            dateRangeBtn.data('startdate', start);
            startDateGuide.attr('width', xScale(start));
        });
        endDateField.on('change', function () {
            var end = new Date(endDateField.val()).getTime();
            dateSlider.slider('values', 1, end / 1000);
            dateRangeBtn.data('enddate', end);
            endDateGuide.attr('width', width - xScale(end)).attr('x', xScale(end));
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
    }
    function closeMenu(id) {
        var el = jQuery(id);
        el.collapse('toggle');
    }
    function updateSiteSelect(d) {
        var siteSelect = jQuery('#websites');
        siteSelect.find('.propertyFilter').css('display', 'none');
        var dByUrl = getCurrentSites(d);
        if (dByUrl.length < 2) {
            siteSelect.css('display', 'none');
            jQuery('#websites-head').css('display', 'none');
        } else {
            siteSelect.css('display', '');
            var pids = [];
            for (var i = 0; i < dByUrl.length; i++) {
                var row = jQuery("li[data-properties='" + dByUrl[i][1] + "']");
                row.css('display', '');
                pids.push(dByUrl[i][1]);
            }
            jQuery('#websites-head').css('display', '');
            jQuery('.allWebsites').css('display', '');
            jQuery('.propertyFilter.allWebsites').attr('data-properties', pids.join());
        }
    }
    function detailTables(d) {
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
            detailsTable.show();
            detailsTable.html(data);
        });
    }
    function updateSubheads(d) {
        var id = d[0].dealer_id;
        var dealer = jQuery.grep(dealersList, function (obj) {
            return obj.id === id;
        });
        dealerSubhead.text(dealer[0].named);
        dealersSelectTitle.textContent = dealer[0].named;

        var sites = getCurrentSites(d);
        var sitesText = sites.length === 1 ? sites[0][0] : 'All Websites';
        websiteSubhead.animate({ opacity: 0 }, 200, function () {
            websiteSubhead.text(sitesText);
            websiteSubhead.animate({ opacity: 1 }, 300);
        });
        websitesSelectTitle.textContent = sitesText;
    }
    function getCurrentSites(d) {
        var sites = d.map(function (d) {
            return d.url + '__' + d.property_id;
        });
        var uniqueSites = Array.from(new Set(sites));
        return uniqueSites.map(function (s) {
            return [s.split('__')[0], s.split('__')[1]];
        });
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

    //***** Data processing & display *****//
    function changeData(callback, id) {
        if (dataCache[id]) {
            dataset = dataCache[id];
            callback();
            dataSubset = dataset;
        } else {
            $.ajax({
                url: ajaxUrl,
                dataType: 'text',
                method: 'GET',
                success: function success(data) {
                    data = JSON.parse(data);
                    dataset = prepData(data);
                    callback();
                    dataCache[id] = dataSubset = dataset;
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
    }

    function metricReadouts(d) {
        d3.select('#pageviews-readout').transition().duration(prms.duration).tween('text', function () {
            var element = d3.select(this);
            var f = d3.format(',.0f');
            var newValue = d[d.length - 1].tot_pageviews - d[0].tot_pageviews + d[0].pageviews;
            var i = d3.interpolateNumber(element.text().replace(/,/g, ''), newValue);
            return function (t) {
                element.text(f(i(t)));
            };
        });
        d3.select('#visitors-readout').transition().duration(prms.duration).tween('text', function () {
            var element = d3.select(this);
            var f = d3.format(',.0f');
            var newValue = d[d.length - 1].tot_visitors - d[0].tot_visitors + d[0].visitors;
            var i = d3.interpolateNumber(element.text().replace(/,/g, ''), newValue);
            return function (t) {
                element.text(f(i(t)));
            };
        });
        d3.select('#entrances-readout').transition().duration(prms.duration).tween('text', function () {
            var element = d3.select(this);
            var f = d3.format(',.0f');
            var newValue = d[d.length - 1].tot_entrances - d[0].tot_entrances + d[0].entrances;
            var i = d3.interpolateNumber(element.text().replace(/,/g, ''), newValue);
            return function (t) {
                element.text(f(i(t)));
            };
        });
        d3.select('#duration-readout').transition().duration(prms.duration).tween('text', function () {
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
        d3.select('#bounce-readout').transition().duration(prms.duration).tween('text', function () {
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

    //***** Chart rendering & updating *****//
    function createChart() {
        // Chart size, margins
        mainChart = d3.select('.mainChart').attr('width', prms.width).attr('height', prms.height);
        width = +mainChart.attr('width') - prms.margin.left - prms.margin.right;
        height = +mainChart.attr('height') - prms.margin.top - prms.margin.bottom;
        g = mainChart.append('g').attr('transform', 'translate(' + prms.margin.left + ',' + prms.margin.top + ')').attr('overflow', 'visible');
        g.append("g").attr('class', 'xAxis').attr("transform", "translate(0," + height + ")");
        g.append("g").attr('class', 'yAxis');
        var g2 = g.append('g').attr('opacity', prms.dataLines.opacity);
        function addPath(lineClass, txt, txtId, color) {
            g2.append("path").attr('class', lineClass).attr("fill", color).attr("stroke", 'black').attr("stroke-width", prms.dataLines.lineWidth);
        }
        addPath('pageviews', 'Pageviews', 'pageviewsText', prms.dataLines.pageViewColor);
        addPath('visitors', 'Visitors', 'visitorsText', prms.dataLines.uniqueVisitorColor);
        addPath('entrances', 'Entrances', 'entrancesText', prms.dataLines.entrancesColor);

        initializeDateSlider();
        updateChart(dataset);
        detailTables(dataset);
    };

    function updateChart(d) {
        var svg = mainChart.transition();
        // Define the D3 scales
        xScale = d3.scaleTime().range([0, width]).domain(d3.extent(d.map(function (d) {
            return d.date_recorded;
        })));
        var yDomainMin = d3.min(d.map(function (d) {
            return d.tot_entrances;
        }));
        var yDomainMax = d3.max(d.map(function (d) {
            return d.tot_pageviews;
        }));
        yScale = d3.scaleLinear().range([height, 0]).domain([yDomainMin, yDomainMax * 1.1]);
        // Axes
        var formatDay = d3.timeFormat("%a %d"),
            formatWeek = d3.timeFormat("%b %d"),
            formatMonth = d3.timeFormat("%b"),
            formatYear = d3.timeFormat("%Y");
        function multiFormat(date) {
            return (d3.timeMonth(date) < date ? d3.timeWeek(date) < date ? formatDay : formatWeek : d3.timeYear(date) < date ? formatMonth : formatYear)(date);
        }
        bottomAxis = function bottomAxis() {
            return d3.axisBottom(xScale).ticks(7).tickFormat(multiFormat).tickPadding(10).tickSizeInner(-1 * height).tickSizeOuter(0);
        };
        svg.select('.xAxis').duration(prms.duration).call(bottomAxis());
        leftAxis = function leftAxis() {
            return d3.axisLeft(yScale).ticks(8).tickPadding(10).tickSizeInner(-1 * width).tickSizeOuter(0);
        };
        svg.select('.yAxis').duration(prms.duration).call(leftAxis());
        // Do the transitions
        lineTransitions(d, 'tot_pageviews', '.pageviews', '#pageviewsText');
        lineTransitions(d, 'tot_visitors', '.visitors', '#visitorsText');
        lineTransitions(d, 'tot_entrances', '.entrances', '#entrancesText');

        svg.select('#startDateGuide').duration(prms.duration).attr('width', xScale(d[0].date_recorded));
        svg.select('#endDateGuide').duration(prms.duration).attr('width', width - xScale(d[d.length - 1].date_recorded)).attr('x', xScale(d[d.length - 1].date_recorded));
        metricReadouts(d);
        updateSiteSelect(dataset);
        updateSubheads(d);
        formatTicks();
    }
    function lineTransitions(d, dataCol, lineClass, txtId) {
        var lineD = d3.area().x(function (d) {
            return xScale(d.date_recorded);
        }).y1(function (d) {
            return yScale(d[dataCol]);
        });
        lineD.y0(yScale(0));
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
            if (coords[0] < width && !secondSegment) {
                pathCoordinates1.push(coordsObj);
            } else if (coords[0] === width) {
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
        mainChart.transition().select(lineClass).duration(prms.duration).attrTween('d', function () {
            var previous = d3.select(this).attr('d') || lineD;
            var current = lineD;
            function exclude(a, b) {
                return a.x === b.x;
            }
            return d3.interpolatePath(previous, current, exclude);
        });
        mainChart.transition().select(txtId).duration(prms.duration).attr("transform", "translate(" + (width + 3) + "," + yScale(d[d.length - 1][dataCol]) + ")");
    }
    function formatTicks() {
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
        prms.width = parseInt(d3.select('#chart-box #chart').style('width'), 10);
        mainChart.attr('width', prms.width).attr('height', prms.height);
        width = +mainChart.attr('width') - prms.margin.left - prms.margin.right;
        height = +mainChart.attr('height') - prms.margin.top - prms.margin.bottom;
        updateChart(dataSubset);
    }
    return {
        init: function init(url, dealers) {
            ajaxUrl = url;
            dealersList = dealers;
            changeData(createChart);
            initUi();
        }
    };
}();

/***/ })
/******/ ]);
//# sourceMappingURL=main.bundle.js.map
'use strict';
class Chart {
    constructor(url, dealers) {
        this._prms = {
            width: d3.select('#chart-box #chart').node().getBoundingClientRect().width, // outer width
            height: function() { // outer height
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
        this.Table = new Table();
    }
    changeData(callback, id) {
        if (this.dataCache[id]) {
            this._dataset = this.dataCache[id];
            callback();
            this._dataSubset = this._dataset;
        } else {
            jQuery.ajax({
                url: this.ajaxUrl,
                dataType: 'text',
                method: 'GET',
                success: (data) => {
                    data = JSON.parse(data);
                    this._dataset = this.prepData(data);
                    callback();
                    this.dataCache[id] = this._dataSubset = this._dataset;
                },
                error:  function(xhr) {
                    console.log(xhr);
                }
            });
        }
    }
    prepData(data) {
        let parseTime = d3.timeParse("%Y-%m-%d");
        let tot_pageviews = 0;
        let tot_visitors = 0;
        let tot_entrances = 0;
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
    }
    createChart() {
        this._mainChart
            .attr('width', this._prms.width)
            .attr('height', this._prms.height)
        ;
        this._width = +this._mainChart.attr('width') - this._prms.margin.left - this._prms.margin.right;
        this._height = +this._mainChart.attr('height') - this._prms.margin.top - this._prms.margin.bottom;
        this._g
            .attr('transform', 'translate(' + this._prms.margin.left + ',' + this._prms.margin.top + ')')
            .attr('overflow', 'visible');
        this._g.append("g")
            .attr('class', 'xAxis')
            .attr("transform", "translate(0," + this._height + ")");
        this._g.append("g")
            .attr('class', 'yAxis');
        let g2 = this._g.append('g')
            .attr('opacity', this._prms.dataLines.opacity);
        let addPath = (lineClass, color) => {
            g2.append("path")
                .attr('class', lineClass)
                .attr("fill", color)
                .attr("stroke", 'black')
                .attr("stroke-width", this._prms.dataLines.lineWidth)
            ;
        };
        addPath('pageviews', this._prms.dataLines.pageViewColor);
        addPath('visitors', this._prms.dataLines.uniqueVisitorColor);
        addPath('entrances', this._prms.dataLines.entrancesColor);
        this.updateChart(this._dataset);
    }
    updateChart(d) {
        let svg = this._mainChart.transition();
        // Define the D3 scales
        this._xScale = d3.scaleTime()
            .range([0, this._width])
            .domain(d3.extent(d.map(function(d) { return d.date_recorded; })))
        ;
        let yDomainMin = d3.min(d.map(function(d) { return d.tot_entrances; }));
        let yDomainMax = d3.max(d.map(function(d) { return d.tot_pageviews; }));
        this._yScale = d3.scaleLinear()
            .range([this._height, 0])
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
        let bottomAxis = () => {
            return d3.axisBottom(this._xScale)
                .ticks(7)
                .tickFormat(multiFormat)
                .tickPadding(10)
                .tickSizeInner((-1 * this._height))
                .tickSizeOuter(0)
            ;
        };
        svg.select('.xAxis')
            .duration(this._prms.duration)
            .call(bottomAxis())
        ;
        let leftAxis = () => {
            return d3.axisLeft(this._yScale)
                .ticks(8)
                .tickPadding(10)
                .tickSizeInner(-1 * this._width)
                .tickSizeOuter(0)
            ;
        };
        svg.select('.yAxis')
            .duration(this._prms.duration)
            .call(leftAxis())
        ;
        // Do the transitions
        this.lineTransitions(d,'tot_pageviews', '.pageviews', '#pageviewsText');
        this.lineTransitions(d, 'tot_visitors', '.visitors', '#visitorsText');
        this.lineTransitions(d, 'tot_entrances', '.entrances', '#entrancesText');
        svg.select('#startDateGuide')
            .duration(this._prms.duration)
            .attr('width', this._xScale(d[0].date_recorded))
        ;
        svg.select('#endDateGuide')
            .duration(this._prms.duration)
            .attr('width', this._width - this._xScale(d[d.length - 1].date_recorded))
            .attr('x', this._xScale(d[d.length - 1].date_recorded))
        ;
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
        this.metricReadouts(d);
    }
    lineTransitions(d, dataCol, lineClass, txtId) {
        // horrors...
        let lineD = d3.area()
            .x((d) => { return this._xScale(d.date_recorded); })
            .y1((d) => { return this._yScale(d[dataCol]); });
        lineD.y0(this._yScale(0));
        lineD = lineD(d);
        let originalPath = lineD.substring(1, lineD.length-1);
        originalPath = originalPath.split('L');
        let pathCoordinates1 = [];
        let pathCoordinates2 = [];
        let endLine = '';
        let secondSegment = false;
        let lineLength = originalPath.length;
        for (let i=0; i<lineLength; i++) {
            var coords = originalPath[i].split(',');
            coords[0] = Number(coords[0]);
            coords[1] = Number(coords[1]);
            var coordsObj = {x: coords[0], y: coords[1]};
            if ((coords[0] < this._width) && (!secondSegment)) {
                pathCoordinates1.push(coordsObj);
            } else if (coords[0] === this._width) {
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
        this._mainChart.transition().select(lineClass)
            .duration(this._prms.duration)
            .attrTween('d', function() {
                var previous = d3.select(this).attr('d') || lineD;
                var current = lineD;
                function exclude(a, b) {
                    return a.x === b.x;
                }
                return d3.interpolatePath(previous, current, exclude);
            });
        this._mainChart.transition().select(txtId)
            .duration(this._prms.duration)
            .attr("transform", "translate("+(this._width+3)+","+this._yScale(d[d.length - 1][dataCol])+")")
        ;  
    }
    metricReadouts(d) {
        d3.select('#pageviews-readout')
            .transition().duration(this._prms.duration)
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
            .transition().duration(this._prms.duration)
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
            .transition().duration(this._prms.duration)
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
            .transition().duration(this._prms.duration)
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
            .transition().duration(this._prms.duration)
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
}
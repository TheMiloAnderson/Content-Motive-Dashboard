function mainChart(csv) {
    // Initial rendering of the aggregate chart
    var prms = {
        width: d3.select('#dash-right-col').node().getBoundingClientRect().width,
        height: 400,
        x: 50,
        y: 50,
        duration: 1000,
        dataLines: {
            lineWidth: 4,
            uniqueVisitorColor: 'firebrick',
            pageViewColor: 'steelblue',
            entrancesColor: 'darkgreen',
            textDx: 5,
            textDy: '.3em'
        },
        margin: {
            top: 20,
            right: 120,
            bottom: 30,
            left: 50
        }
    };
    // Chart size, margins
    var mainChart = d3.select('.mainChart').attr('width', prms.width).attr('height', prms.height),
        width = +mainChart.attr('width') - prms.margin.left - prms.margin.right,
        height = +mainChart.attr('height') - prms.margin.top - prms.margin.bottom,
        g = mainChart.append('g').attr('transform', 'translate(' + prms.margin.left + ',' + prms.margin.top + ')')
            .attr('overflow', 'visible')
    ;
    
    // Receive the data & clean it up
    var parseTime = d3.timeParse("%Y-%m-%d");
    var dataset = d3.csvParse(csv, function(data) {
        data.pv = +data.pv;
        data.upv = +data.upv;
        data.total_pageviews = +data.total_pageviews;
        data.total_unique_pageviews = +data.total_unique_pageviews;
        data.total_entrances = +data.total_entrances;
        data.date_recorded = parseTime(data.date_recorded);
        return data;
    });
    
    // Define the scales
    var xScale = d3.scaleTime()
        .range([0, width])
        .domain(d3.extent(dataset.map(function(d) { return d.date_recorded; })))
    ;
    var yDomain = d3.extent(dataset.map(function(d) { return d.total_pageviews; }));
    var yScale = d3.scaleLinear()
        .range([height, 0])
        .domain([yDomain[0], yDomain[1] * 1.1])
    ;
    
    // X Axis
    function bottomAxis() {
        return d3.axisBottom(xScale)
            .tickSizeInner(-1 * height)
        ;
    }
    g.append("g")
        .attr('class', 'xAxis')
        .attr("transform", "translate(0," + height + ")")
        .call(bottomAxis())
    ;
    // Y Axis
    function leftAxis() {
        return d3.axisLeft(yScale)
            .ticks(8)
            .tickSizeInner(-1 * width)
        ;
    }
    g.append("g")
        .attr('class', 'yAxis')
        .call(leftAxis())
    ;
    function formatTicks() {
        d3.selectAll('.yAxis .tick line')
            .attr('stroke-dasharray', '4,2')
            .attr('stroke', 'gray')
        ;
        d3.selectAll('.xAxis .tick line')
            .attr('stroke', 'lightgray')
        ;
    };
    formatTicks();

    // Pageviews path
    var tpvLineD = d3.line()
        .x(function(d) { return xScale(d.date_recorded); })
        .y(function(d) { return yScale(d.total_pageviews); });
    var tpvLine = g.append("path")
        .datum(dataset)
        .attr('class', 'pageviews')
        .attr("fill", "none")
        .attr("stroke", prms.dataLines.pageViewColor)
        .attr("stroke-width", prms.dataLines.lineWidth)
        .attr('d', tpvLineD(dataset))
    ;
    g.append('text')
        .text('Pageviews')
        .attr('id', 'pageviewsText')
        .attr('dx', prms.dataLines.textDx)
        .attr('dy', prms.dataLines.textDy)
        .attr("transform", "translate("+(width+3)+","+yScale(dataset[dataset.length - 1].total_pageviews)+")")
        .attr('fill', prms.dataLines.pageViewColor)
    ;
    
    // Unique Visitors path
    var tupvLineD = d3.line()
        .x(function(d) { return xScale(d.date_recorded); })
        .y(function(d) { return yScale(d.total_unique_pageviews); })
    ;
    var tupvLine = g.append("path")
        .datum(dataset)
        .attr('class', 'visitors')
        .attr("fill", "none")
        .attr("stroke", prms.dataLines.uniqueVisitorColor)
        .attr("stroke-width", prms.dataLines.lineWidth)
        .attr('d', tupvLineD(dataset));
    ; 
    g.append('text')
        .text('Visitors')
        .attr('id', 'visitorsText')
        .attr('dx', prms.dataLines.textDx)
        .attr('dy', prms.dataLines.textDy)
        .attr("transform", "translate("+(width+3)+","+yScale(dataset[dataset.length - 1].total_unique_pageviews)+")")
        .attr('fill', prms.dataLines.uniqueVisitorColor)
    ;
    // Entrances path
    var entLineD = d3.line()
        .x(function(d) { return xScale(d.date_recorded); })
        .y(function(d) { return yScale(d.total_entrances); })
    ;
    var tupvLine = g.append("path")
        .datum(dataset)
        .attr('class', 'entrances')
        .attr("fill", "none")
        .attr("stroke", prms.dataLines.entrancesColor)
        .attr("stroke-width", prms.dataLines.lineWidth)
        .attr('d', entLineD(dataset));
    ; 
    g.append('text')
        .text('Entrances')
        .attr('id', 'entrancesText')
        .attr('dx', prms.dataLines.textDx)
        .attr('dy', prms.dataLines.textDy)
        .attr("transform", "translate("+(width+3)+","+yScale(dataset[dataset.length - 1].total_entrances)+")")
        .attr('fill', prms.dataLines.entrancesColor)
    ;
   
    function metricReadouts(d) {
        // Pageviews
        d3.select('#pageviews-readout')
            .attr('style', 'color:'+prms.dataLines.pageViewColor)
            .transition().duration(prms.duration)
            .tween('text', function() {
                var element = d3.select(this);
                var f = d3.format(',.0f');
                var i = d3.interpolateNumber(element.text().replace(/,/g, ''), d[d.length - 1].total_pageviews);
                return function(t) {
                    element.text(f(i(t)));
                };
            });
        // Visitors
        d3.select('#visitors-readout')
            .attr('style', 'color:'+prms.dataLines.uniqueVisitorColor)
            .transition().duration(prms.duration)
            .tween('text', function() {
                var element = d3.select(this);
                var f = d3.format(',.0f');
                var i = d3.interpolateNumber(element.text().replace(/,/g, ''), d[d.length - 1].total_unique_pageviews);
                return function(t) {
                    element.text(f(i(t)));
                };
            });
        // Entrances
        d3.select('#entrances-readout')
            .attr('style', 'color:'+prms.dataLines.entrancesColor)
            .transition().duration(prms.duration)
            .tween('text', function() {
                var element = d3.select(this);
                var f = d3.format(',.0f');
                var i = d3.interpolateNumber(element.text().replace(/,/g, ''), d[d.length - 1].total_entrances);
                return function(t) {
                    element.text(f(i(t)));
                };
            });  
        // Avg. Duration
        d3.select('#duration-readout')
            .transition().duration(prms.duration)
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
                }
                var breakTime = function(time) {
                    time = time.split(':');
                    var min = +time[0];
                    var sec = +time[1];
                    var totalSec = (min * 60) + sec;
                    return totalSec;
                }
                var elementText = element.text();
                var i = d3.interpolateNumber(breakTime(elementText), avg_seconds);
                return function(t) {
                    element.text(makeTime(i(t)));
                };
            });
        // Bounce Rate
        d3.select('#bounce-readout')
            .transition().duration(prms.duration)
            .tween('text', function() {
                var element = d3.select(this);
                var bounceRate = 0;
                for (var i=0; i<d.length; i++) {
                    bounceRate += Number(d[i].avg_bounce_rate);
                }
                bounceRate = bounceRate / d.length;
                var f = d3.format('.1f');
                var i = d3.interpolateNumber(element.text().replace(/%/g, ''), bounceRate);
                return function(t) {
                    element.text(f(i(t)) + '%');
                };
            });
    }
    metricReadouts(dataset);
    
    // Click element to update chart data
    $('.dealerSelect').click(function(e) {
        e.preventDefault();
        updateChart(this);
    });
    // AJAX call
    function updateChart(url) {
        $.ajax({
            url: url,
            dataType: 'text',
            success: function(csv) {
                updateData(csv);
            }
        });
    };

    function updateData(csv) {
        // Receive revised data & clean it up
        // Fix the line breaks so D3 parses it right
        csv = csv.replace (/\\n/g, "\n");
        var parseTime = d3.timeParse("%Y-%m-%d");
        var dataset = d3.csvParse(csv, function(data) {
            data.pv = +data.pv;
            data.upv = +data.upv;
            data.total_pageviews = +data.total_pageviews;
            data.total_unique_pageviews = +data.total_unique_pageviews;
            data.total_entrances = +data.total_entrances;
            data.date_recorded = parseTime(data.date_recorded);
            return data;
        });
        // Redefine the scales
        xScale.domain(d3.extent(dataset.map(function(d) { return d.date_recorded; })))
        ;
        var yDomain = d3.extent(dataset.map(function(d) { return d.total_pageviews; }))
        ;
        yScale.domain([yDomain[0], yDomain[1] * 1.1])
        ;      
        // Do the transitions
        var svg = d3.select('.mainChart').transition();
        svg.select('.pageviews')
            .duration(prms.duration)
            .attrTween('d', function() {
                var previous = d3.select(this).attr('d');
                var current = tpvLineD(dataset);
                return d3.interpolatePath(previous, current);
            });
        svg.select('.visitors')
            .duration(prms.duration)
            .attrTween('d', function() {
                var previous = d3.select(this).attr('d');
                var current = tupvLineD(dataset);
                return d3.interpolatePath(previous, current);
            });
        svg.select('.entrances')
            .duration(prms.duration)
            .attrTween('d', function() {
                var previous = d3.select(this).attr('d');
                var current = entLineD(dataset);
                return d3.interpolatePath(previous, current);
            });
        svg.select('.xAxis')
            .duration(prms.duration)
            .call(bottomAxis())
        ;
        svg.select('.yAxis')
            .duration(prms.duration)
            .call(leftAxis())
        ;
        svg.select('#visitorsText')
            .duration(prms.duration)
            .attr("transform", "translate("+(width+3)+","+yScale(dataset[dataset.length - 1].total_unique_pageviews)+")")
        ;
        svg.select('#pageviewsText')
            .duration(prms.duration)
            .attr("transform", "translate("+(width+3)+","+yScale(dataset[dataset.length - 1].total_pageviews)+")")
        ;
        svg.select('#entrancesText')
            .duration(prms.duration)
            .attr("transform", "translate("+(width+3)+","+yScale(dataset[dataset.length - 1].total_entrances)+")")
        ;
        // Re-fix the ticks
        formatTicks();
        // Update metric readouts
        metricReadouts(dataset);
    }
};


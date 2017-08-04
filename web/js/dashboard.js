var dashboard = (function() {
    //***** Parameters & global variables *****//
    var prms = {
        width: d3.select('#dash-right-col').node().getBoundingClientRect().width,
        height: 400,
        x: 50,
        y: 50,
        duration: 1000,
        dataLines: {
            lineWidth: 1,
            pageViewColor: 'steelblue',
            uniqueVisitorColor: 'midnightblue',
            entrancesColor: 'darkred',
            textDx: 5,
            textDy: '.3em',
            opacity: 0.7
        },
        margin: {
            top: 20,
            right: 120,
            bottom: 30,
            left: 50
        },
        tableColumns: ['', 'Pageviews', 'Visitors', 'Entrances', 'Avg. Visit Duration']
    };
    
    var ajaxUrl,
        data = [], // all data for a user -- not set up yet, may be unnecessary
        dataset = [], // all data for a dealer
        dataSubset = [], // dealer data filtered by date or website
        dealersList = [],
        mainChart,
        g,
        bottomAxis,
        leftAxis,
        height,
        width,
        xScale,
        yScale;

    var dealerSubhead = jQuery('span#dealerSubhead');
    var websiteSubhead = jQuery('#websiteSubhead');
    var dateRangeSubhead = jQuery('#dateRangeSubhead');
    
    var dateRangeBtn = jQuery('#dateRangeBtn');
    var dateSlider = jQuery('#slider-range');
    
    var propertyRows = jQuery('.single-prop');
    var detailsTable = jQuery('#p0');
    
    //***** UI controls *****//
    //
    function initUi() {
        jQuery('.prop-click').click(function(e) {
            e.preventDefault();
            var pid = jQuery(this).attr('href').split(',');
            for (var i=0; i<pid.length; i++) {pid[i] = +pid[i]; }
            var revisedDataset = dataset.filter(function(d) {
                return pid.includes(d.property_id);
            });
            dataSubset = prepData(revisedDataset);
            updateChart(dataSubset);
            resetDateSlider(dataSubset);
        });
        // Click dealer/host names, get new dataset
        jQuery('.dealerSelect').click(function(e) {
            e.preventDefault();
            ajaxUrl = jQuery(this).attr('href');
            changeData(function() {
                updateChart(dataset);
                resetDateSlider(dataset);
            });
        });
        // Filter dataset by date, update the chart
        dateRangeBtn.click(function() {
            var startDate = $(this).data('startdate');
            var endDate = $(this).data('enddate');
            revisedDataset = dataSubset.filter(function(d) {
                return d.date_recorded >= startDate && d.date_recorded <= endDate;
            });
            updateChart(prepData(revisedDataset));
        });
        jQuery( function() {
            $( "#accordion" ).accordion({
              collapsible: true,
              heightStyle: "content"
            });
            $( "#selectmenu" ).selectmenu();
        } );
    }
    function updateSiteSelect(d) {
        var siteSelect = d3.select('#websites');
        siteSelect.selectAll('.single-prop').style('display', 'none');
        var dByUrl = getCurrentSites(d);
        if (dByUrl.length < 2) { 
            siteSelect.style('display', 'none');
            d3.select('#websites-head').style('display', 'none');
        } else {
            siteSelect.style('display', '');
            var pids = [];
            for (var i=0; i<dByUrl.length; i++) {
                    var row = d3.select("li[id='" + dByUrl[i][1] + "']");
                    row.style('display', '');
                    pids.push(dByUrl[i][1]);
                }
            d3.select('#websites-head').style('display', '');
            var all = d3.select('.all-websites');
            all.style('display', '');
            all.select('.prop-click').attr('href', pids.join());
        }
    }
    
    function detailTables(d) {
        var sites = getCurrentSites(d);
        if (sites.length === 1) {
            jQuery.get('?r=/dashboard/details&pid=' + sites[0][1], function(data) {
                data = '<div id="p0">' + data + '</div>';
                detailsTable.show();
                detailsTable.html(data);
            });
        } else {
            detailsTable.html('<div id="p0">Select a website to view details</div>');
        }
    }
    
    function updateSubheads(d) {
        var id = d[0].dealer_id;
        var dealer = jQuery.grep(dealersList, function(obj) { return obj.id === id; });
        dealerSubhead.text(dealer[0].named);
        
        var sites = getCurrentSites(d);
        sitesText = sites.length === 1 ? sites[0][0] : 'All Websites';
        websiteSubhead.animate({opacity: 0}, 200, function() { 
            websiteSubhead.text(sitesText);
            websiteSubhead.animate({opacity: 1}, 300);
        });
    }
    function getCurrentSites(d) {
        var sites = d.map(function(d) { return d.url + '__' + d.property_id; });
        uniqueSites = Array.from(new Set(sites));
        return uniqueSites.map(function(s) { return [s.split('__')[0], s.split('__')[1]];});
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
                .attr('opacity', .04)
            ;
        }
        var startDateGuide = addDateGuide('startDateGuide');
        var endDateGuide = addDateGuide('endDateGuide');
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
    function resetDateSlider(d) {
        var start = new Date(d[0].date_recorded).getTime();
        var end = new Date(d[d.length - 1].date_recorded).getTime();
        dateSlider.slider('option', 'min', start / 1000);
        dateSlider.slider('option', 'max', end / 1000);
        setDateFields(start, end);
    }
    function setDateFields(start, end) {
        start = new Date(start);
        end = new Date(end);
        jQuery('#startDate').val(('0' + (start.getMonth() + 1)).slice(-2) + '/' + ('0' + start.getDate()).slice(-2) + '/' + start.getFullYear());
        jQuery('#endDate').val(('0' + (end.getMonth() + 1)).slice(-2) + '/' + ('0' + end.getDate()).slice(-2) + '/' + end.getFullYear());
    }
    
    //***** Data processing & display *****//
    function changeData(action) {
        $.ajax({
            url: ajaxUrl,
            dataType: 'text',
            method: 'GET',
            success: function(data) {
                data = JSON.parse(data);
                dataset = dataSubset = prepData(data);
                action();
            },
            error:  function(xhr) {
                console.log(xhr);
            }
        });
    };
    function prepData(data) {
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
    }

    function metricReadouts(d) {
        // Pageviews
        d3.select('#pageviews-readout')
            .attr('style', 'color:'+prms.dataLines.pageViewColor)
            .transition().duration(prms.duration)
            .tween('text', function() {
                var element = d3.select(this);
                var f = d3.format(',.0f');
                var newValue = d[d.length - 1].tot_pageviews - d[0].tot_pageviews + d[0].pageviews;
                var i = d3.interpolateNumber(element.text().replace(/,/g, ''), newValue);
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
                var newValue = d[d.length - 1].tot_visitors - d[0].tot_visitors + d[0].visitors;
                var i = d3.interpolateNumber(element.text().replace(/,/g, ''), newValue);
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
                var newValue = d[d.length - 1].tot_entrances - d[0].tot_entrances + d[0].entrances;
                var i = d3.interpolateNumber(element.text().replace(/,/g, ''), newValue);
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
        // Bounce Rate
        d3.select('#bounce-readout')
            .transition().duration(prms.duration)
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
   
    //***** Chart rendering & updating *****//
     function createChart() {
        // Chart size, margins
        mainChart = d3.select('.mainChart').attr('width', prms.width).attr('height', prms.height);
        width = +mainChart.attr('width') - prms.margin.left - prms.margin.right;
        height = +mainChart.attr('height') - prms.margin.top - prms.margin.bottom;
        g = mainChart.append('g')
            .attr('transform', 'translate(' + prms.margin.left + ',' + prms.margin.top + ')')
            .attr('overflow', 'visible');
        g.append("g")
            .attr('class', 'xAxis')
            .attr("transform", "translate(0," + height + ")");
        g.append("g")
            .attr('class', 'yAxis');
        var g2 = g.append('g')
            .attr('opacity', prms.dataLines.opacity);
        function addPath(lineClass, txt, txtId, color) {
            g2.append("path")
                .attr('class', lineClass)
                .attr("fill", color)
                .attr("stroke", 'black')
                .attr("stroke-width", prms.dataLines.lineWidth)
            ;
            g.append('text')
                .text(txt)
                .attr('id', txtId)
                .attr('dx', prms.dataLines.textDx)
                .attr('dy', prms.dataLines.textDy)
                .attr('fill', color)
                .attr("transform", "translate("+(width)+","+(height)+")")
            ;
        }
        addPath('pageviews', 'Pageviews', 'pageviewsText', prms.dataLines.pageViewColor);
        addPath('visitors', 'Visitors', 'visitorsText', prms.dataLines.uniqueVisitorColor);
        addPath('entrances', 'Entrances', 'entrancesText', prms.dataLines.entrancesColor);

        initializeDateSlider();
        updateChart(dataset);
    };
    
    function updateChart(d) {
        var svg = d3.select('.mainChart').transition();
        // Define the D3 scales
        xScale = d3.scaleTime()
            .range([0, width])
            .domain(d3.extent(d.map(function(d) { return d.date_recorded; })))
        ;
        var yDomainMin = d3.min(d.map(function(d) { return d.tot_entrances; }));
        var yDomainMax = d3.max(d.map(function(d) { return d.tot_pageviews; }));
        yScale = d3.scaleLinear()
            .range([height, 0])
            .domain([yDomainMin, yDomainMax * 1.1])
        ;
        // Axes
        bottomAxis = function() {
            return d3.axisBottom(xScale)
                .tickSizeInner(-1 * height)
            ;
        };
        svg.select('.xAxis')
            .duration(prms.duration)
            .call(bottomAxis())
        ;
        leftAxis = function() {
            return d3.axisLeft(yScale)
                .ticks(8)
                .tickSizeInner(-1 * width)
            ;
        };
        svg.select('.yAxis')
            .duration(prms.duration)
            .call(leftAxis())
        ;
        // Do the transitions
        lineTransitions(d,'tot_pageviews', '.pageviews', '#pageviewsText');
        lineTransitions(d, 'tot_visitors', '.visitors', '#visitorsText');
        lineTransitions(d, 'tot_entrances', '.entrances', '#entrancesText');
        
        svg.select('#startDateGuide')
            .duration(prms.duration)
            .attr('width', xScale(d[0].date_recorded))
        ;
        svg.select('#endDateGuide')
            .duration(prms.duration)
            .attr('width', width - xScale(d[d.length - 1].date_recorded))
            .attr('x', xScale(d[d.length - 1].date_recorded))
        ;
        formatTicks();
        metricReadouts(d);
        updateSiteSelect(dataset);
        updateSubheads(d);
        detailTables(d);
    }
    function lineTransitions(d, dataCol, lineClass, txtId) {
        var lineD = d3.area()
            .x(function(d) { return xScale(d.date_recorded); })
            .y1(function(d) { return yScale(d[dataCol]); });
        lineD.y0(yScale(0));
        mainChart.transition().select(lineClass)
            .duration(prms.duration)
            .attrTween('d', function() {
                var previous = d3.select(this).attr('d') || lineD(d);
                var current = lineD(d);
                function exclude(a, b) {
                    return a.x === b.x;
                }
                return d3.interpolatePath(previous, current, exclude);
            });
        mainChart.transition().select(txtId)
            .duration(prms.duration)
            .attr("transform", "translate("+(width+3)+","+yScale(d[d.length - 1][dataCol])+")")
        ;
    }  
    function formatTicks() {
        d3.selectAll('.yAxis .tick line')
            .attr('stroke-dasharray', '4,2')
            .attr('stroke', 'gray')
        ;
        d3.selectAll('.xAxis .tick line')
            .attr('stroke', 'lightgray')
        ;
    };

    return {
        init: function(url, dealers) {
            ajaxUrl = url;
            dealersList = dealers;
            changeData(createChart);
            initUi();
        }
    };
})();
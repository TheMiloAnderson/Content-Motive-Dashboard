'use strict';
class Table {
    constructor() {
        this._detailsTable = jQuery('#p0');
    }
    detailTables(d, start = false, end = false) {
        var sites = getCurrentSites(d);
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
            this._detailsTable.show();
            this._detailsTable.html(data);
        });
    }
}
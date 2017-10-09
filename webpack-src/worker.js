onmessage = function(e) {
    getData(e.data);
};
function getData(list) {
    for (let i=0; i<list.length; i++) {
        var ajax = new XMLHttpRequest();
        ajax.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                let obj = {};
                let key = (list[i].id).toString();
                let val = JSON.parse(ajax.responseText);
                obj[key] = val;
                postMessage(obj);
            }
        };
        ajax.open('GET', list[i].url, false);
        ajax.send();
    }
}
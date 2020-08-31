// do cool stuff here

var rh_onload = new Array();
var rh_onresize = new Array();

rh_onload.push(del_checkbox_assist_init);
rh_onload.push(remove_unsupported_tags);
rh_onload.push(make_tds_clickable);
rh_onload.push(make_trs_clickable);

window.onload = function() {
        for (var i = 0; i < rh_onload.length; i++) rh_onload[i]();
}

window.onresize = function() {
        for (var i = 0; i < rh_onresize.length; i++) rh_onresize[i]();
}

function check_overlap(e1, e2) {
        try {
            var r1 = e1.getBoundingClientRect();
            var r2 = e2.getBoundingClientRect();
            var overlap = !(r1.right < r2.left || r1.left > r2.right || r1.bottom < r2.top || r1.top > r2.bottom);
        }
        catch (err) {
            overlap = -1;
        }
        return overlap;
}

function del_checkbox_assist_init() {
    var delete_fs = document.getElementsByClassName("rh_delete");
    for (var i = 0; i < delete_fs.length; i++) {
        for (var j = 0; j < delete_fs[i].childNodes.length; j++) {
            if (delete_fs[i].childNodes[j].type == "submit") {
                delete_fs[i].childNodes[j].onclick = del_checkbox_assist_check;
                break;
            }
        }
    }
}

function del_checkbox_assist_check() {
    for (i = 0; i < this.parentNode.childNodes.length; i++) {
        var that = this.parentNode.childNodes[i];
        if (that.type == "checkbox") del_ok = that;
    }
    if (typeof del_ok === undefined) return true; // something went wrong; let PHP handle it
    if (!del_ok.checked) {
        alert(unescape("L%F6schen bitte durch Anklicken der Checkbox best%E4tigen."));
        return false;
    }
    return true;
}

function remove_unsupported_tags() {
    var min_time = document.getElementsByName("min_time")[0];
    if (typeof min_time != "undefined") {
            if (min_time.type != "time") min_time.style['display'] = "none";
    }
}

function make_tds_clickable() {
    var tds = document.getElementsByClassName("rh_html_table_order");
    for (var i = 0; i < tds.length; i++) {
        for (var j = 0; j < tds[i].children.length; j++) {
            if (tds[i].children[j].tagName == "A") {
                tds[i].href = tds[i].children[j].href;
                tds[i].onclick = function() { window.location.href = this.href; };
                tds[i].style['cursor'] = "pointer";
                tds[i].title = (tds[i].children[j].className == "order_desc" ? "aufsteigend sortieren" : "absteigend sortieren");
                break;
            }
        }
    }
}

function make_trs_clickable() {
    var trs = document.getElementsByTagName("tr");
    for (var i = 0; i < trs.length; i++) {
        if (trs[i].className != "rh_even" && trs[i].className != "rh_odd") continue;
        trs[i].href = trs[i].querySelector(".showissue").href;
        trs[i].onclick = function() { window.location.href = this.href; };
        trs[i].style['cursor'] = "pointer";
    }
}

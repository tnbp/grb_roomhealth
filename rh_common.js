// do cool stuff here

var rh_onload = new Array();
var rh_onresize = new Array();

rh_onload.push(del_checkbox_assist_init);

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
    if (typeof that === undefined) return true; // something went wrong; let PHP handle it
    if (!that.checked) {
        alert(unescape("L%F6schen bitte durch Anklicken der Checkbox best%E4tigen."));
        return false;
    }
    return true;
}

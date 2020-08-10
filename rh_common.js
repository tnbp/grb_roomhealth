// do cool stuff here

var rh_onload = new Array();
var rh_onresize = new Array();

window.onload = function() {
        for (i = 0; i < rh_onload.length; i++) rh_onload[i]();
}

window.onresize = function() {
        for (i = 0; i < rh_onresize.length; i++) rh_onresize[i]();
}

function check_overlap(e1, e2) {
        try {
            r1 = e1.getBoundingClientRect();
            r2 = e2.getBoundingClientRect();
            var overlap = !(r1.right < r2.left || r1.left > r2.right || r1.bottom < r2.top || r1.top > r2.bottom);
        }
        catch (err) {
            overlap = -1;
        }
        return overlap;
}

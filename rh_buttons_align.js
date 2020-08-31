rh_onresize.push(rh_buttons_align);
rh_onload.push(rh_buttons_align);

function rh_buttons_align() {   // bad!
    fs = document.getElementsByClassName("align_a");
    div = document.getElementsByClassName("align_b");
    
    for (var i = 0; i < fs.length; i++) {
        r1 = fs[i].getBoundingClientRect();
        r2 = div[i].getBoundingClientRect();
        if ((r2.x - r1.x - r1.width) > 0) {
                div[i].style['position'] = "absolute";
        }
        else {
                div[i].style['position'] = "";
        }
    }
}

rh_onresize.push(rh_buttons_align);
rh_onload.push(rh_buttons_align);

function rh_buttons_align() {   // bad!
    fs = document.getElementById("align_a");
    div = document.getElementById("align_b");
    r1 = fs.getBoundingClientRect();
    r2 = div.getBoundingClientRect();
    
    if ((r2.x - r1.x - r1.width) > 0) {
            div.style['position'] = "absolute";
            //div.style['bottom'] = ".6em";
            //div.style['right'] = ".6em";
    }
    else {
            div.style['position'] = "";
    }
}

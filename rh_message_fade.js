rh_onload.push(init_infobox_fade);

function init_infobox_fade() {
    var allboxes = document.getElementsByClassName("rh_fadebox");
    for (var i = 0; i < allboxes.length; i++) {
        allboxes[i].fade = fade_msg;
        allboxes[i].style['opacity'] = "1";
        setTimeout(allboxes[i].fade.bind(allboxes[i]), 5000); // BLACK MAGIC!
    }
}

function fade_msg() {
    var newOpacity = (parseFloat(this.style['opacity']) - (16/1000));
    if (newOpacity < 0) {
        this.style['margin-bottom'] = "0px";
        this.style['margin-top'] = "0px";
        this.style['padding-top'] = "0px";
        this.style['padding-bottom'] = "0px";
        this.style['max-height'] = "0px";
        return;
    }
    this.style['opacity'] = newOpacity + "";
    setTimeout(this.fade.bind(this), 16);
}

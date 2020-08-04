var filter_form, fs_filterlist;
var filter_legend;
var filterlist_toggle_blocked;
init_filterlist();

filter_legend.onclick = toggle_filterlist;
var filter_msg_p;
//var filter_form_status = false; // true == visible -> defined by PHP script

function toggle_filterlist() {
    if (filterlist_toggle_blocked == true) return;
    if (filter_form_status == true) {   // collapse
        filter_legend.textContent = "Filter [+]";
        filter_form.style['height'] = filter_form.offsetHeight + "px";
        filterlist_toggle_blocked = true;
        setTimeout(filter_form_fadeout, 16);
        /*fs_filterlist.removeChild(filter_form);
        filter_msg_p = document.createElement("p");
        var filter_msg_text = document.createTextNode("Klicken zum Anzeigen");
        filter_msg_p.appendChild(filter_msg_text);
        filter_msg_p.style['font-style'] = "italic";
        filter_msg_p.style['text-align'] = "center";
        fs_filterlist.onclick = expand_filterlist;
        fs_filterlist.appendChild(filter_msg_p);*/
        filter_form_status = false;
    }
    else {  // expand
        filter_legend.textContent = "Filter [-]";
        filterlist_toggle_blocked = true;
        setTimeout(filter_form_fadein, 16);
        /*filter_form.style['overflow'] = "hidden";
        filter_form.style['max-height'] = "1px";
        fs_filterlist.appendChild(filter_form);
        setTimeout(filter_form_fadein, 16);
        fs_filterlist.onclick = false; 
        fs_filterlist.removeChild(filter_msg_p);*/
        filter_form_status = true;
    }
}

function init_filterlist() {
    fs_filterlist = document.getElementById("listissues_filter");
    filter_legend = document.getElementById("listissues_filter_legend");
    for (i = 0; i < fs_filterlist.children.length; i++) {
        if (fs_filterlist.children[i].tagName == "FORM") {
            filter_form = fs_filterlist.children[i];
            break;
        }
    }
    if (filter_form_status == true) {
        filter_legend.textContent = "Filter [-]";
        filter_form.style['overflow'] = "hidden";
    }
    else {
        filter_legend.textContent = "Filter [+]";
        filter_form.style['overflow'] = "hidden";
        filter_form.style['max-height'] = "0px";
    }
    filterlist_toggle_blocked = false;
}

function expand_filterlist() {
    collapse_filterlist(false);
}

function filter_form_fadein() {
    filter_form.style['max-height'] = parseInt(filter_form.style['max-height']) + 10 + "px";
    if (parseInt(filter_form.style['max-height']) >= 1000) {
        filterlist_toggle_blocked = false;
        return;
    }
    setTimeout(filter_form_fadein, 16);    
}

function filter_form_fadeout() {
    var new_height = parseInt(filter_form.style['height']) - 10;
    if (new_height <= 1) {
        filter_form.style['max-height'] = "0px";
        filter_form.style['height'] = "";
        filterlist_toggle_blocked = false;
        return;
    }
    filter_form.style['height'] = new_height + "px";
    setTimeout(filter_form_fadeout, 16);
}

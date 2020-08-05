var cm;
window.onload = commentmods_init;

function commentmods_init() {
    cm = document.getElementsByClassName("rh_commentmod");
    for (i = 0; i < cm.length; i++) {
            for (j = 0; j < cm[i].children.length; j++) {
                if (cm[i].children[j].tagName == "LEGEND") {
                    cm[i].legend = cm[i].children[j];
                    break;
                }
            }
            if (cm[i].id == document.URL.split("#")[1]) continue;
            cm[i].style['overflow'] = "hidden";
            cm[i].style['padding-top'] = "0px";
            cm[i].style['padding-bottom'] = "0px";
            cm[i].style['height'] = "0px";
            cm[i].legend.textContent = "Kommentarmoderation [+]";
            cm[i].legend.onclick = commentmods_expand;
            cm[i].legend.style['cursor'] = "pointer";
    }
}

function commentmods_expand() {
    this.parentElement.style['height'] = "max-content";
    this.parentElement.style['padding-bottom'] = "8px";
    this.parentElement.style['padding-top'] = "4px";
    this.textContent = "Kommentarmoderation [-]";
    this.onclick = commentmods_collapse;
}

function commentmods_collapse() {
    this.parentElement.style['overflow'] = "hidden";
    this.parentElement.style['padding-top'] = "0px";
    this.parentElement.style['padding-bottom'] = "0px";
    this.parentElement.style['height'] = "0px";
    this.textContent = "Kommentarmoderation [+]";
    this.onclick = commentmods_expand;
}

// TODO: animations?

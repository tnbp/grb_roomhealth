var pw1 = document.getElementById("pw1");
var pw2 = document.getElementById("pw2");
var pw_btn = document.getElementById("pw_btn");
var result = document.getElementById("result");
var errormsg = document.getElementById("errormsg");
errormsg.display = display_msg;
errormsg.fade = fade_msg;
var copymsg = document.getElementById("copymsg");
copymsg.display = display_msg;
copymsg.fade = fade_msg;

var bcrypt = dcodeIO.bcrypt;

pw_btn.onclick = calculate_hash;

function calculate_hash() {
    if (pw1.value != pw2.value) {
        errormsg.display("Passwörter stimmen nicht überein!");
        return;
    }
    if (pw1.value.length < 8) {
        errormsg.display("Passwort muss mindestens 8 Zeichen lang sein!");
        return;
    }
    var salt = bcrypt.genSaltSync(10);
    var hash = bcrypt.hashSync(pw1.value, salt);
    result.value = hash;
    result.select();
    if (document.execCommand("copy")) copymsg.display("Hash in die Zwischenablage kopiert!");
}

function display_msg(msg) {
    this.textContent = msg;
    this.style['margin-bottom'] = "1em";
    this.style['margin-top'] = "1em";
    this.style['padding-top'] = ".5em";
    this.style['padding-bottom'] = ".5em";
    this.style['max-height'] = "3em";
    this.style['opacity'] = "1";
    setTimeout(this.fade.bind(this), 5000); // BLACK MAGIC!
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

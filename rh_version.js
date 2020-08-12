rh_onload.push(display_version);

function display_version() {
  commit = "$$COMMIT$$";

  ac = document.getElementById("allcontainer");
  ver_div = document.createElement("div");
  ver_a = document.createElement("a");
  ver_a.href = "https://github.com/tnbp/grb_roomhealth/commit/" + commit;
  ver_text1 = document.createTextNode("letzter Commit: ");
  ver_text2 = document.createTextNode(commit);

  ver_a.appendChild(ver_text2);
  ver_div.appendChild(ver_text1);
  ver_div.appendChild(ver_a);

  ver_div.style['text-align'] = "center";
  ver_div.style['background-color'] = "white";
  ver_div.style['margin-top'] = "1em";

  if (commit != "$$COMMIT$$") ac.appendChild(ver_div);
}

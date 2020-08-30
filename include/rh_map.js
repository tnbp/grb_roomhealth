rh_onload.push(load_map);
rh_onload.push(reset_navigation);

var cur_bldg = 0;   // always start in A-Trakt
var cur_floor = 0;  // always start on ground floor

var req_a = ajax_request();
var req_b = ajax_request();

/*
    building_data is just an array of building objects;
    building objects contain a floors array with floor objects as well as next_left and next_right references to other building objects
    floor objects include an array of rooms on that floor amd next_up and next_down references to other floor objects as well as a svg reference to an SVG file
*/

var building_data = [
    { name: "A-Trakt", id: 0, code: 'A', floors:
        [
            { rooms: [ "A001", "A002", "A003", "A004", "A005", "A006", "A007", "A008", "A009", "A010" ], name: "Erdgeschoss", get next_up() { return building_data[0].floors[1]; }, get next_down() { return false; }, svg: "include/map_a0.svg" },
            { rooms: [ "A101", "A102", "A103", "A104", "A105", "A106", "A107", "A108", "A109", "A110", "G101" ], name: "1. Stock", get next_up() { return building_data[0].floors[2]; }, get next_down() { return building_data[0].floors[0]; }, svg: "include/map_a1.svg" },
            { rooms: [ "A201", "A202", "A203", "A204", "A205", "A206", "A207", "A208", "A209", "A210" ], name: "2. Stock", get next_up() { return false; }, get next_down() { return building_data[0].floors[1]; }, svg: "include/map_a2.svg" }
        ], get next_left() { return building_data[3]; }, get next_right() { return false; }
    },
    { name: "B-Trakt", id: 1, code: 'B', floors:
        [
            { rooms: [ "B001", "B002", "B003", "B004", "B005", "B006", "B007", "B008", "B009", "B010" ], name: "Erdgeschoss", get next_up() { return building_data[1].floors[1]; }, get next_down() { return false; }, svg: "include/map_b0.svg" },
            { rooms: [ "B101", "B102", "B103", "B104", "B105", "B106", "B107", "B108", "B109", "B110" ], name: "1. Stock", get next_up() { return building_data[1].floors[2]; }, get next_down() { return building_data[1].floors[0]; }, svg: "include/map_b1.svg" },
            { rooms: [ "G207", "B201", "B202", "B203", "B204", "B205", "B206", "B207", "B208", "B209", "B210" ], name: "2. Stock", get next_up() { return false; }, get next_down() { return building_data[1].floors[1]; }, svg: "include/map_b2.svg" }
        ], get next_left() { return building_data[4]; }, get next_right() { return building_data[3]; }
    },
    { name: "C-Trakt", id: 2, code: 'C', floors:
        [
            { rooms: [ "C001", "C002", "C003", "C004", "C005", "C006", "C007", "C008", "C009", "C010", "C011" ], name: "Erdgeschoss", get next_up() { return building_data[2].floors[1]; }, get next_down() { return false; }, svg: "include/map_c0.svg" },
            { rooms: [ "C101", "C102", "C103", "C104", "C105", "C106", "C107", "C108", "C109", "C110", "C111" ], name: "1. Stock", get next_up() { return building_data[2].floors[2]; }, get next_down() { return building_data[2].floors[0]; }, svg: "include/map_c1.svg" },
            { rooms: [ "G212", "C201", "C202", "C203", "C204", "C205", "C206", "C207", "C208", "C209", "C210", "C211", "C212" ], name: "2. Stock", get next_up() { return false; }, get next_down() { return building_data[2].floors[1]; }, svg: "include/map_c2.svg" }
        ], get next_left() { return building_data[5]; }, get next_right() { return building_data[4]; }
    },
    { name: "D-Trakt", id: 3, code: 'D', floors:
        [
            { rooms: [ "D001", "D005" ], name: "Erdgeschoss", get next_up() { return building_data[3].floors[1]; }, get next_down() { return false; } , svg: "include/map_d0.svg" },
            { rooms: [ "D102", "D103", "D104", "D105", "D106", "D107", "D108", "D109", "D110", "D112", "D114", "D115", "D116", "D117" ], name: "1. Stock", get next_up() { return false; }, get next_down() { return building_data[3].floors[0]; }, svg: "include/map_d1.svg" }
        ], get next_left() { return building_data[1]; }, get next_right() { return building_data[0]; }
    },
    { name: "E-Trakt", id: 4, code: 'E', floors:
        [
            { rooms: [ "B001", "B002", "B003", "B004", "B005", "B006", "B007", "B008", "B009", "B010" ], name: "Erdgeschoss", get next_up() { return building_data[1].floors[1]; }, get next_down() { return false; }, svg: "include/map_e0.svg" },
            { rooms: [ "B101", "B102", "B103", "B104", "B105", "B106", "B107", "B108", "B109", "B110" ], name: "1. Stock", get next_up() { return false; }, get next_down() { return building_data[1].floors[0]; }, svg: "include/map_e1.svg" }
        ], get next_left() { return building_data[2]; }, get next_right() { return building_data[1]; }
    },
    { name: "F-Trakt", id: 5, code: 'F', floors:
        [
            { rooms: [ "B001", "B002", "B003", "B004", "B005", "B006", "B007", "B008", "B009", "B010" ], name: "Erdgeschoss", get next_up() { return building_data[1].floors[1]; }, get next_down() { return false; }, svg: "include/map_f0.svg" },
            { rooms: [ "B101", "B102", "B103", "B104", "B105", "B106", "B107", "B108", "B109", "B110" ], name: "1. Stock", get next_up() { return false; }, get next_down() { return building_data[1].floors[0]; }, svg: "include/map_f1.svg" }
        ], get next_left() { return false; }, get next_right() { return building_data[2]; }
    }
];

var issue_data = {};

function reset_navigation() {
    cur_bldg = 0;   // always start in A-Trakt
    cur_floor = 0;  // always start on ground floor
    update_navigation();
}

var select_bldg = document.getElementById("rh_map_nav_bldg");
var select_floor = document.getElementById("rh_map_nav_floor");

var rh_map_container = document.getElementById("rh_map_container");
var rh_map_svg;

function load_map() {
    var jsnotice = document.getElementById("jsnotice");
    jsnotice.style['display'] = "none";
    var rh_map = document.getElementById("rh_map_container");
    rh_map.style['display'] = "block";
    var rh_map_nav = document.getElementById("rh_map_nav");
    rh_map.style['display'] = "block";
    
    select_bldg.onchange = function() { cur_bldg = this.value; update_navigation(); };
    select_floor.onchange = function() { cur_floor = this.value; update_navigation(); }
    update_navigation();
}

function update_navigation() {
    select_bldg.querySelectorAll("*").forEach(c => c.remove());
    select_floor.querySelectorAll("*").forEach(c => c.remove());
    
    for (var i = 0; i < building_data.length; i++) {
        var opt = document.createElement("OPTION");
        opt.innerHTML = building_data[i].name;
        opt.value = i;
        if (cur_bldg == i) opt.selected = true;
        select_bldg.append(opt);
    }
    if ((typeof building_data[cur_bldg].floors) == "undefined") {
        // building is invalid, reset!
        return reset_navigation();
    }
    for (var i = 0; i < building_data[cur_bldg].floors.length; i++) {
        var opt = document.createElement("OPTION");
        opt.innerHTML = building_data[cur_bldg].floors[i].name;
        opt.value = i;
        if (cur_floor == i) opt.selected = true;
        select_floor.append(opt);
    }
    document.getElementById("rh_map_nav_l").style['display'] = building_data[cur_bldg].next_left ? "inline" : "none";
    document.getElementById("rh_map_nav_l").onclick = function() { cur_bldg = building_data[cur_bldg].next_left.id; cur_floor = Math.min(cur_floor, (building_data[cur_bldg].floors.length - 1)); update_navigation(); };
    document.getElementById("rh_map_nav_r").style['display'] = building_data[cur_bldg].next_right ? "inline" : "none";
    document.getElementById("rh_map_nav_r").onclick = function() { cur_bldg = building_data[cur_bldg].next_right.id; cur_floor = Math.min(cur_floor, (building_data[cur_bldg].floors.length - 1)); update_navigation(); };
    document.getElementById("rh_map_nav_u").style['display'] = building_data[cur_bldg].floors[cur_floor].next_up ? "inline" : "none";
    document.getElementById("rh_map_nav_u").onclick = function() { cur_floor++; update_navigation(); };
    document.getElementById("rh_map_nav_d").style['display'] = building_data[cur_bldg].floors[cur_floor].next_down ? "inline" : "none";
    document.getElementById("rh_map_nav_d").onclick = function() { cur_floor--; update_navigation(); };
    
    req_a.open("GET", building_data[cur_bldg].floors[cur_floor].svg);
    req_a.onreadystatechange = function(){
        switch(req_a.readyState) {
            case 4:
            if(req_a.status == 200) {
                rh_map_container.innerHTML = req_a.responseText;
                rh_map_svg = rh_map_container.querySelector("svg");
                rh_map_svg.style['height'] = rh_map_svg.style['width'] = "50%";
                fetch_issues();
            }
            else if (req_a.status == 404) {
                alert("SVG-Datei nicht gefunden!");
                rh_map_container.innerHTML = "";
            }
            break;

            default:
            return;
            break;     
        }
    };
    req_a.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    req_a.send(null);
}

function fetch_issues() {
    req_b.open("GET", "json_issues.php?building=" + building_data[cur_bldg].code + "&floor=" + cur_floor);
    req_b.onreadystatechange = function(){
        switch(req_b.readyState) {
            case 4:
            if(req_b.status == 200) {
                issue_data = JSON.parse(req_b.responseText);
                update_map(issue_data);
            }
            break;

            default:
            return;
            break;     
        }
    };
    req_b.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    req_b.send(null);    
}

function update_map(d) {
    for (var i = 0; i < d.length; i++) {
        var cur_issue_room = rh_map_svg.getElementById("r" + d[i].room_name);
        if (cur_issue_room != null) {
            cur_issue_room.style['fill'] = "red";
        }
    }
}

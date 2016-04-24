"use strict";

function buildAcctList(e) {
    
    var i=0;
    var acclist = document.getElementById("acclist");
    acclist.value = "";
    
    for (i = 0; i < e.target.length; i++) {
       if (e.target[i].selected) {
         acclist.value += "," + "#" + e.target[i].value + "#";   
       }
    }
    
    //strip off the leading comma
    acclist.value = acclist.value.substr(1);
}


function buildCatList(e) {
  
    var i=0;
    var catlist = document.getElementById("catlist");
    catlist.value = "";
    
    for (i = 0; i < e.target.length; i++) {
       if (e.target[i].selected) {
         catlist.value += "," + "#" + e.target[i].value + "#";  
       }
    }
    
    //strip off the leading comma
    catlist.value = catlist.value.substr(1);
}


/*Immediate scripts*/

document.getElementById("accountlist").addEventListener("change", buildAcctList);
document.getElementById("categorylist").addEventListener("change", buildCatList);


/*Update the list boxes from previously cached values*/
var i = 0;
var acclist = document.getElementById("acclist");
var accctrl = document.getElementById("accountlist");
var catlist = document.getElementById("catlist");
var catctrl = document.getElementById("categorylist");

if (acclist.value == "##" || acclist.value == "") {
    accctrl[0].selected = true;
} else {
    for (i = 0; i < accctrl.length; i++) {
        if (accctrl[i].value != "" && acclist.value.indexOf("#" + accctrl[i].value + "#") > -1) {
            accctrl[i].selected = true; 
        } else {
            accctrl[i].selected = false;
        }
    }
}

if (catlist.value == "##" || catlist.value == "") {
    catctrl[0].selected = true;
} else {
    for (i = 0; i < catctrl.length; i++) {
        if (catctrl[i].value != "" && catlist.value.indexOf("#" + catctrl[i].value + "#") > -1) {
            catctrl[i].selected = true; 
        } else {
            catctrl[i].selected = false;
        }
    }
}




function editRepeat() {
  
    /*Cycle all repeating items and check only one is selected
    if more than one selected then throw an error and stop*/
    var iFound = 0, txFound = 0, x = 0, elem = null;
    for (x = 1; x <= 1000; x++) {
        elem = document.getElementById("chk_" + x);
        if (elem && elem.checked) {
            iFound++;
            txFound = x;
        }
    }
    
    switch (iFound) {
    case 0:
        window.alert("No repeating transaction selected");
        break;
    case 1:
        break;
    default:
        window.alert("You must only select one repeating transaction");
        break;
    }

    /*redirect browser to edit form*/
    if (iFound === 1) {
        window.location.href = "/tcash/repeating/view?mode=edit&tx=" + txFound;
    }
    
    return false;
}

function applyToDate() {
      
    var go2url = "/tcash/repeating?aptd=" +  
        document.getElementById('applyyear').value + "-" +
        document.getElementById('applymonth').value + "-" +
        document.getElementById('applyday').value;
  
    window.location.href = go2url;
}

document.getElementById('mnuA2d').addEventListener('click', applyToDate);

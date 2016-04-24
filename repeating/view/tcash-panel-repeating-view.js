function validatePayee(objPayeeField, objTxfAccField) {
    "use strict";
    if (objPayeeField.style.display === "block" && objPayeeField.value === "") {
        objPayeeField.setCustomValidity("Please select a payee (" + objPayeeField.value + ")");
    } else {
        objPayeeField.setCustomValidity("");
    }
    
    if (objTxfAccField.style.display === "block" && objTxfAccField.value === "") {
        objTxfAccField.setCustomValidity("Please select an account to transfer into");
    } else {
        objTxfAccField.setCustomValidity("");
    }
}

function changeLabels(txntype) {
    "use strict";
	switch (txntype) {
    case 'Deposit':
        document.getElementById('lbl_payee').innerHTML = "Received From";
        document.getElementById('txfaccount').style.display = "none";
        document.getElementById('payee').style.display = "block";
        break;
    case 'Payment':
        document.getElementById('lbl_payee').innerHTML = "Pay To";
        document.getElementById('txfaccount').style.display = "none";
        document.getElementById('payee').style.display = "block";
        break;
    case 'Transfer':
        document.getElementById('lbl_payee').innerHTML = "Transfer To";
        document.getElementById('txfaccount').style.display = "block";
        document.getElementById('payee').style.display = "none";
        break;
    default:
        document.getElementById('lbl_payee').innerHTML = "From";
        document.getElementById('txfaccount').style.display = "block";
        document.getElementById('payee').style.display = "block";
        break;
	}
    validatePayee(document.getElementById('payee'), document.getElementById('txfaccount'));
}

function changeTxnType(e) {
    "use strict";
    changeLabels(e.target.value);
}

function constructNextDate(e) {
    "use strict";
    var datefield = document.getElementById('nextdate');
    var yearfield = document.getElementById('nextyear');
    var monthfield = document.getElementById('nextmonth');
    var dayfield = document.getElementById('nextday');

	datefield.value = yearfield.value + "-" + monthfield.value + "-" + dayfield.value;
}

function constructEndOnDate(e) {
    "use strict";
    var datefield = document.getElementById('endondate');
    var yearfield = document.getElementById('endonyear');
    var monthfield = document.getElementById('endonmonth');
    var dayfield = document.getElementById('endonday');

	datefield.value = yearfield.value + "-" + monthfield.value + "-" + dayfield.value;
}


function highlightText(e) {
    "use strict";
	e.target.setSelectionRange(0, e.target.value.length);
}


function confirmDelete(e) {
    "use strict";
	if (window.confirm("Are you sure you want to delete this transaction?")) {
		return true;
	} else {
		e.preventDefault();
		return false;
	}
}

function updateEveryLabel(e) {
    "use strict";
    switch (document.getElementById('frequency').value) {
    case "Y":
        document.getElementById('lblEvery').innerHTML = "Years";
        break;
    case "M":
        document.getElementById('lblEvery').innerHTML = "Months";
        break;
    case "W":
        document.getElementById('lblEvery').innerHTML = "Weeks";
        break;
    case "D":
        document.getElementById('lblEvery').innerHTML = "Days";
        break;
    default:
        document.getElementById('lblEvery').innerHTML = "Freq";
        break;
    }
  
}

function amtValidation(e) {
    "use strict";
    validateAmount(e.target);
}
    
function validateAmount(objField) {
    "use strict";
    if (objField.value == 0) {
        objField.setCustomValidity("Amount cannot be zero"); 
    } else {
        objField.setCustomValidity("");
    }
}

function addPayee(e) {
    "use strict";
    document.getElementById('refdata').value='addpayee'; 
    document.rptform.submit(); 
}

function addCategory(e) {
    "use strict";
    document.getElementById('refdata').value='addcategory'; 
    document.rptform.submit(); 
}

function pyeValidation(e) {
    "use strict";
    validatePayee(document.getElementById('payee'), document.getElementById('txfaccount'));
}





//IMMEDIATE SCRIPTS
document.getElementById('txnamount').addEventListener('click', highlightText, false);
document.getElementById('txnamount').addEventListener('input', amtValidation, false);
document.getElementById('txnamount').addEventListener('blur', amtValidation, false);

document.getElementById('every').addEventListener('click', highlightText, false);
document.getElementById('frequency').addEventListener('change', updateEveryLabel);



if (document.getElementById("deletebtn") != null) {
    document.getElementById('deletebtn').addEventListener('click', confirmDelete, false);
}

document.getElementById('nextyear').addEventListener('change', constructNextDate, false);
document.getElementById('nextmonth').addEventListener('change', constructNextDate, false);
document.getElementById('nextday').addEventListener('change', constructNextDate, false);

document.getElementById('endonyear').addEventListener('change', constructEndOnDate, false);
document.getElementById('endonmonth').addEventListener('change', constructEndOnDate, false);
document.getElementById('endonday').addEventListener('change', constructEndOnDate, false);

document.getElementById('txntype').addEventListener('change', changeTxnType, false);
document.getElementById('returnURL').value = window.location.href;

//button click event handlers
document.getElementById('addpayee').addEventListener('click', addPayee);
document.getElementById('addcategory').addEventListener('click', addCategory);


//Payee event handlers
document.getElementById('payee').addEventListener('change', pyeValidation, false);
document.getElementById('txfaccount').addEventListener('change', pyeValidation, false);


validateAmount(document.getElementById('txnamount'));
validatePayee(document.getElementById('payee'), document.getElementById('txfaccount'));

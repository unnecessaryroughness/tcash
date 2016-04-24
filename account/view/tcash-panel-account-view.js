"use strict";

function validatePayee(objPayeeField, objTxfAccField) {
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

function pyeValidation(e) {
    validatePayee(document.getElementById('payee'), document.getElementById('txfaccount'));
}

function mirrorDate(e) {
    document.getElementById("date").value = e.target.value;
}

function updateCal(e) {
    objCalendar.highlightDate(document.getElementById('date').value);
}

function changeLabels(txntype) {
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
    changeLabels(e.target.value);
}



function highlightText(e) {
	e.target.setSelectionRange(0, e.target.value.length);
}

function validateAmount(objField) {
    if (objField.value == 0) {
        objField.setCustomValidity("Amount cannot be zero");
    } else {
        objField.setCustomValidity("");
    }
}

function amtValidation(e) {
    validateAmount(e.target);
}
    
function amtEval(e) {
    e.target.value = eval(e.target.value).toFixed(2);
}


function addPayee(e) {
    document.getElementById('refdata').value = 'addpayee';
    document.entryform.submit();
}


function addCategory(e) {
    document.getElementById('refdata').value = 'addcategory';
    document.entryform.submit();
}


function showRecentTxnDetails(e) {
	var rspData = e.target;

	if (rspData.status == 200) {
		
		var obj = JSON.parse(rspData.responseText);
		var txnDetails = obj[0];

		//replace category and amount field values with recent transaction details
		document.getElementById('category').value = txnDetails.category;

        //only change the amount field if it doesn't already contain a value
        if (document.getElementById('txnamount').value == 0) { 
            //Reverse the sign of the amount if the transaction type is "payment"
            if (document.getElementById('txntype').value == 'Payment') {

                document.getElementById('txnamount').value = eval(-txnDetails.amountcr).toFixed(2);
            } else {
                document.getElementById('txnamount').value = eval(txnDetails.amountcr).toFixed(2);
            }
        }
        
        validateAmount(document.getElementById('txnamount'));
	}
}


function changePayee(e) {

    //get new payee value
	var sNewPayee = e.target.value;

	//construct AJAX call
	var url = "/tcash/account/view/tcash-getRecentTxn.ajax.php";
	var payeeData = new FormData();
    payeeData.append("payee", sNewPayee);

	var request = new XMLHttpRequest();
	request.addEventListener('load', showRecentTxnDetails);

	request.open("POST", url, true);
	request.send(payeeData);
}


function changeReduce(e) {
    
    var sNewReduce = e.target.value;
    var oReduceAmt = document.getElementById('reduceamt');
    
  
    if (sNewReduce != "") {
        oReduceAmt.value = document.getElementById('txnamount').value;
        oReduceAmt.disabled = false;
        oReduceAmt.focus();
    } else {
        oReduceAmt.value = eval(0.00).toFixed(2);
        oReduceAmt.disabled = true;
    }
}


function confirmDelete(e) {

    if (window.confirm("Are you sure you want to delete this transaction?")) {
		return true;
	} else {
		e.preventDefault();
		return false;
	}
}

/* 
    ==================================
    IMMEDIATE SCRIPTS
    ==================================
*/

//Set up event handlers
document.getElementById('txntype').addEventListener('change', changeTxnType, false);
document.getElementById('txnamount').addEventListener('click', highlightText, false);
document.getElementById('txnamount').addEventListener('input', amtValidation, false);
document.getElementById('txnamount').addEventListener('blur', amtEval, false);
document.getElementById('txnamount').addEventListener('blur', amtValidation, false);

document.getElementById('payee').addEventListener('change', changePayee, false);
document.getElementById('payee').addEventListener('change', pyeValidation, false);
document.getElementById('txfaccount').addEventListener('change', changePayee, false);
document.getElementById('txfaccount').addEventListener('change', pyeValidation, false);
document.getElementById('addpayee').addEventListener('click', addPayee);
document.getElementById('addcategory').addEventListener('click', addCategory);

var reduceField = document.getElementById('reduce');
if (typeof (reduceField) !== "undefined" && reduceField != null) {
    document.getElementById('reduce').addEventListener('change', changeReduce);
}


var delbtn = document.getElementById('deletebtn');

if (typeof (delbtn) !== "undefined" && delbtn != null) {
    delbtn.addEventListener('click', confirmDelete, false);
}

//hook events for calendar
var objCalendar = new Calendar();
document.getElementById("txtCalDate").addEventListener('click', mirrorDate, false);
document.getElementById("date").addEventListener('change', updateCal, false);


//Immediate calls to event response functions
changeLabels(document.getElementById('txntype').value);
validateAmount(document.getElementById('txnamount'));
validatePayee(document.getElementById('payee'), document.getElementById('txfaccount'));

//Set default values
document.getElementById('returnURL').value = window.location.href;
objCalendar.highlightDate(document.getElementById('date').value);


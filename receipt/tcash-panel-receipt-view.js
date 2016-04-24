document.getElementById('txnamount').addEventListener('click', highlightText, false);
document.getElementById('txnamount').addEventListener('input', amtValidation, false);
document.getElementById('txnamount').addEventListener('blur', amtValidation, false);
document.getElementById('txnamount').addEventListener('blur', amtEval, false);
document.getElementById('payee').addEventListener('change', changePayee, false);
document.getElementById('returnURL').value = window.location.href;

document.getElementById('reduce').addEventListener('change', changeReduce);

for (i=0; i<document.getElementsByName('accountid').length; i++) {
    document.getElementsByName('accountid')[i].addEventListener('change', changeAccount);
}

function amtEval(e) {
    e.target.value = eval(e.target.value).toFixed(2);
}


function highlightText(e) {
	e.target.setSelectionRange(0, e.target.value.length);
}

function amtValidation(e) {
    validateAmount(e.target);
}
    
function validateAmount(objField) {
    if (objField.value == 0) {
        objField.setCustomValidity("Amount cannot be zero"); 
    } else {
        objField.setCustomValidity("");
    }
}


function changePayee(e) {
	
	//get new payee value
	var sNewPayee = e.target.value;

	//construct AJAX call
	var url = "/tcash/account/view/tcash-getRecentTxn.ajax.php";	
	var payeeData = new FormData();
	payeeData.append("payee", document.getElementById('payee').value);	

	var request = new XMLHttpRequest();
	request.addEventListener('load', showRecentTxnDetails);

	request.open("POST", url, true);
	request.send(payeeData);
}

function showRecentTxnDetails(e) {

	var rspData = e.target;

	if (rspData.status == 200) {
		
		var obj = JSON.parse(rspData.responseText);
		var txnDetails = obj[0];

		//replace category and amount field values with recent transaction details
		document.getElementById('category').value = txnDetails.category;
	}
}


function changeAccount(e) {

    //get new payee value
	var sNewAcct = e.target.value;
    var acctId = "";
    
    for (i=0; i<document.getElementsByName('accountid').length; i++) {
        if (document.getElementsByName('accountid')[i].checked) {
            acctId = document.getElementsByName('accountid')[i].value;
        }
    }
    
	//construct AJAX call
	var url = "/tcash/receipt/tcash-getPlaceholders.ajax.php";	
	var acctData = new FormData();
	acctData.append("account", acctId);	
  
	var request = new XMLHttpRequest();
	request.addEventListener('load', showNewPlaceholders);

	request.open("POST", url, true);
	request.send(acctData);
  }


function showNewPlaceholders(e) {

    var rspData = e.target;
    var objSelect = document.getElementById('reduce');

    for (i=objSelect.options.length-1; i>=0; i--) {
        objSelect.remove(i);
    }
    
    if (rspData.status == 200) {
        var obj = JSON.parse(rspData.responseText);
        
        for (i=0; i<obj.length; i++) {
           objSelect.options[objSelect.options.length] = new Option(obj[i].text, obj[i].value);
        }
    }
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

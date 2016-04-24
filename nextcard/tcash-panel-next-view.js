document.getElementById('arrivedamount').addEventListener('click', highlightText, false);
document.getElementById('arrivedamount').addEventListener('input', amtValidation, false);
document.getElementById('arrivedamount').addEventListener('blur', amtValidation, false);
document.getElementById('returnedamount').addEventListener('click', highlightText, false);
document.getElementById('returnedamount').addEventListener('input', amtValidation, false);
document.getElementById('returnedamount').addEventListener('blur', amtValidation, false);

document.getElementById('rtnaccountid').value = document.getElementsByName('accountid')[0].value;
    
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

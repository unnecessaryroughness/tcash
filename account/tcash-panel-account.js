function toggleCleared(t_txnId, t_isCleared, t_futureDated, t_isPlaceholder, t_isToday) {
	
	//construct AJAX call
	var url = "/tcash/account/tcash-updateIsCleared.ajax.php";	
	var txnData = new FormData();
	txnData.append("txnId", t_txnId);	
	txnData.append("isCleared", (t_isCleared == 1 ? 0 : 1)); 
	txnData.append("isFutureDated", t_futureDated); 
	txnData.append("isPlaceholder", t_isPlaceholder); 
    txnData.append("isToday", t_isToday);

	var request = new XMLHttpRequest();
	request.addEventListener('load', updateIsCleared);

	request.open("POST", url, true);
	request.send(txnData);
}

function updateIsCleared(e) {

	var rspData = e.target;

	if (rspData.status == 200) {
		
		var obj = JSON.parse(rspData.responseText);
		var article = document.getElementById('entry-' + obj.txnId);
		var entrydate = document.getElementById('entry-date-' + obj.txnId);	
        var hlink = document.getElementById('hlink-' + obj.txnId);
		
		//update CSS classes for the changed entry
		article.className = "register" 
			+ (obj.isCleared == 0 ? " unreconciled " : "")
			+ (obj.isFutureDated == 1 ? " futuredated " : "")
            + (obj.isPlaceholder == 1 ? " placeholder " : "")
            + (obj.isToday == 1 ? " today " : "");

        hlink.className = "register" 
			+ (obj.isCleared == 0 ? " unreconciled " : "")
			+ (obj.isFutureDated == 1 ? " futuredated " : "")
            + (obj.isPlaceholder == 1 ? " placeholder " : "");

        
		//update onclick event for the changed entry
		entrydate.onclick = function () { toggleCleared( obj.txnId, obj.isCleared, obj.isFutureDated, 
                                                         obj.isPlaceholder, obj.isToday ) };				
	}
}


function switchDisplayedAccount(e) {
    document.location.href = ".?acc=" + e.target.value;
}


//IMMEDIATE SCRIPTS
document.getElementById('showaccount').addEventListener('change', switchDisplayedAccount);


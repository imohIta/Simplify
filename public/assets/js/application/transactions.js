try{
	var searchTransBtn = document.getElementById('searchTransBtn');
	searchTransBtn.addEventListener('click', function(){
		
		var transId = document.getElementById('transId').value;
		var uri = baseuri + '/transaction/getTransById/' + transId;
		document.getElementById('tDetails').innerHTML = loader;
		xhr.open('GET', uri, true);
		xhr.onload = function(e){
			if(xhr.status == 200){
				document.getElementById('tDetails').innerHTML = this.responseText;
				
			}
		};
	    xhr.send();

	  });

}catch(e){} 

function getGuestCheckInInfo(value){
	if(value == 0){
		var topInfo = document.getElementsByClassName('topInfo');
		for (var i = topInfo.length - 1; i >= 0; i--) 
		{
			 topInfo[i].style.display = 'none';
		}
		document.getElementById('checkOutNotes').style.display = 'none';
		return false;
    }
	var uri = baseuri + '/search/getGuestCheckInInfo/' + value;
	document.getElementById('loader').style.display = "block";
	document.getElementById('loader').innerHTML = loader;
	xhr.open('GET', uri, true);
		xhr.onload = function(e){
			if(xhr.status == 200)
			{
				document.getElementById('loader').style.display = "none";
				var res = JSON.parse(this.responseText); 
				if(res.status == 'success')
				{
					var topInfo = document.getElementsByClassName('topInfo');
					for (var i = topInfo.length - 1; i >= 0; i--) 
					{
						 topInfo[i].style.display = 'block';
					}
					document.getElementById('gName').innerHTML = res.gName;
					document.getElementById('gPhone').innerHTML = res.gPhone;
					document.getElementById('cDate').innerHTML = res.checkInDate;
					document.getElementById('rType').innerHTML = res.roomType;
					document.getElementById('rNo').innerHTML = res.roomNo;
					document.getElementById('guestId').value = res.gId;
					
					//get guest Transactions
					(function(guestId, roomId)
					{
						
                        var uri = baseuri + '/guest/fetchTransactions/' + guestId + '/' + roomId;
						xhr.open('GET', uri, true);
							xhr.onload = function(e){
								if(xhr.status == 200){
									var res = JSON.parse(this.responseText);
									
									var bDiv = document.getElementById('bills');
									var pDiv = document.getElementById('payments');

									bDiv.innerHTML = '';
									pDiv.innerHTML = '';
									
									for (var i = 0, bill; bill = res.bills[i]; i++) 
									{
										 bDiv.innerHTML += '<tr><td >' + bill.date + '</td><td>' + bill.details + ' ( ' + bill.roomNo + ' ) </td><td>' + formatNumber(bill.amt) + '</td></tr>';
						             
									}

									bDiv.innerHTML += '<tr><td colspan="2"><strong>Total</strong></td><td>' + formatNumber(res.totalBill) + '</td></tr>';

									for (var i = 0, payment; payment = res.payments[i]; i++) 
									{

										 var res2 = JSON.parse(payment.details);
										 pDiv.innerHTML += '<tr><td>' + payment.date + '</td><td><small>Payment Type :</small> ' + res2['Pay Type'] + '</td><td>' + formatNumber(payment.amt) + '</td></tr>';
									}

									pDiv.innerHTML += '<tr><td colspan="2"><strong>Total</strong></td><td>' + formatNumber(res.totalPayment) + '</td></tr>';

								
								}
							};
						xhr.send();
					})(res.gId, value);
				}
				
			}
		};
	xhr.send();
	
}

function togglePayOptions(option){
	if(option == 'show'){
		document.getElementById('pOptions').style.display = 'block';
	}else{
		document.getElementById('pOptions').style.display = 'none';
	}
}

function getGuestInfoForBills(value){
	if(value == 0){
		var topInfo = document.getElementsByClassName('topInfo');
		for (var i = topInfo.length - 1; i >= 0; i--) 
		{
			 topInfo[i].style.display = 'none';
		}
		document.getElementById('checkOutNotes').style.display = 'none';
		return false;
    }
	var uri = baseuri + '/search/getGuestCheckInInfo/' + value;
	document.getElementById('loader').style.display = "block";
	document.getElementById('loader').innerHTML = loader;
	xhr.open('GET', uri, true);
		xhr.onload = function(e){
			if(xhr.status == 200)
			{
				document.getElementById('loader').style.display = "none";
				var res = JSON.parse(this.responseText); 
				if(res.status == 'success')
				{
					var topInfo = document.getElementsByClassName('topInfo');
					for (var i = topInfo.length - 1; i >= 0; i--) 
					{
						 topInfo[i].style.display = 'block';
					}
					document.getElementById('gName').innerHTML = res.gName;
					document.getElementById('gPhone').innerHTML = res.gPhone;
					document.getElementById('rNo').innerHTML = res.roomNo;
					document.getElementById('rType').innerHTML = res.roomType;
					document.getElementById('guestId').value = res.gId;
					document.getElementById('roomId').value = value;
				}
				
			}
		};
	xhr.send();
	
}


function getGuestInfoForPayments(value){
	if(value == 0){
		var topInfo = document.getElementsByClassName('topInfo');
		for (var i = topInfo.length - 1; i >= 0; i--) 
		{
			 topInfo[i].style.display = 'none';
		}
		document.getElementById('checkOutNotes').style.display = 'none';
		return false;
    }
	var uri = baseuri + '/search/getGuestCheckInInfo/' + value;
	document.getElementById('loader').style.display = "block";
	document.getElementById('loader').innerHTML = loader;
	xhr.open('GET', uri, true);
		xhr.onload = function(e){
			if(xhr.status == 200)
			{
				document.getElementById('loader').style.display = "none";
				var res = JSON.parse(this.responseText); 
				if(res.status == 'success')
				{
					var topInfo = document.getElementsByClassName('topInfo');
					for (var i = topInfo.length - 1; i >= 0; i--) 
					{
						 topInfo[i].style.display = 'block';
					}
					document.getElementById('gName').innerHTML = res.gName;
					document.getElementById('gPhone').innerHTML = res.gPhone;
					document.getElementById('rNo').innerHTML = res.roomNo;
					document.getElementById('rType').innerHTML = res.roomType;
					document.getElementById('guestId').value = res.gId;
					document.getElementById('roomId').value = value;
					
					//get guest Transactions
					/*(function(guestId)
					{
						
                        var uri = baseuri + '/guest/fetchTransactions/' + guestId;
						xhr.open('GET', uri, true);
							xhr.onload = function(e){
								if(xhr.status == 200){
									var res = JSON.parse(this.responseText);
						            var bal = res.totalBill - res.totalPayment;
						            document.getElementById('outBal').value = bal;
								}
							}
						xhr.send();
					})(res.gId);*/
				}
				
			}
		};
	xhr.send();
	
}


function operateTransReversal (param) {
	// body...
	document.getElementById(param.div+'Id').value = param.id;
	document.getElementById(param.div+'TransId').value = param.transId;
}

function viewResersalInfo (res) {
	// body...

	
	var holder = document.getElementById('reversalInfoContent');
	

	var tr1 = document.createElement('tr');
	holder.appendChild(tr1);

	var td1 = document.createElement('td');
	td1.setAttribute('align', 'right');
	td1.innerHTML = '<p class="text-danger">Reversal Application Date : </p>';

	var td1b = document.createElement('td');
	td1b.innerHTML = '<p class="text-muted" style="margin-left:10px">' + res.reversalAppliDate + '</p>';

	tr1.appendChild(td1);
	tr1.appendChild(td1b);


	var tr2 = document.createElement('tr');
	holder.appendChild(tr2);

	var td2 = document.createElement('td');
	td2.setAttribute('align', 'right');
	td2.innerHTML = '<p class="text-danger">Reversal Application Reason : </p>';

	var td2b = document.createElement('td');
	td2b.innerHTML = '<p class="text-muted" style="margin-left:10px">' + res.reversalAppliReason + '</p>';

	tr2.appendChild(td2);
	tr2.appendChild(td2b);

	var tr3 = document.createElement('tr');
	holder.appendChild(tr3);

	var td3 = document.createElement('td');
	td3.setAttribute('align', 'right');
	td3.innerHTML = '<p class="text-danger">Attended By : </p>';

	var td3b = document.createElement('td');
	td3b.innerHTML = '<p class="text-muted" style="margin-left:10px">' + res.attendedBy + '</p>';

	tr3.appendChild(td3);
	tr3.appendChild(td3b);


	var tr4 = document.createElement('tr');
	holder.appendChild(tr4);

	var td4 = document.createElement('td');
	td4.setAttribute('align', 'right');
	td4.innerHTML = '<p class="text-danger">Attended Date : </p>';

	var td4b = document.createElement('td');
	td4b.innerHTML = '<p class="text-muted" style="margin-left:10px">' + res.attendedDate + '</p>';

	tr4.appendChild(td4);
	tr4.appendChild(td4b);

}

function viewTransDetails (param) {
	// body...

		var uri = baseuri + '/transaction/showTransDetails/' + param.id;
		document.getElementById('transContent').innerHTML = loader;
		xhr.open('GET', uri, true);
		xhr.onload = function(e){
			if(xhr.status == 200){
				document.getElementById('transContent').innerHTML = this.responseText;
			}
		};
	    xhr.send();

}

function viewTransDetails2 (param) {
	// body...

		var uri = baseuri + '/transaction/showTransDetails2/' + param.id;
		document.getElementById('transContent').innerHTML = loader;
		xhr.open('GET', uri, true);
		xhr.onload = function(e){
			if(xhr.status == 200){
				document.getElementById('transContent').innerHTML = this.responseText;
			}
		};
	    xhr.send();

}

function fetchCreditTransaction(){
	var transId = document.getElementById('transId').value;
		var uri = baseuri + '/transaction/getTransById/' + transId;
		document.getElementById('tDetails').innerHTML = loader;
		xhr.open('GET', uri, true);
		xhr.onload = function(e){
			if(xhr.status == 200){
				document.getElementById('tDetails').innerHTML = this.responseText;
				
				// fetch guest type from credits
				(
					function(tId){
						var uri = baseuri + '/credits/fetchByTransId/' + tId;
						document.getElementById('paymentTypeHolder').innerHTML = loader;
						xhr.open('GET', uri, true);
						xhr.onload = function(e){
							if(xhr.status == 200){
								document.getElementById('paymentTypeHolder').innerHTML = this.responseText;
								document.getElementById('submitBtn').style.display = 'block';
							}
						};
					    xhr.send();
				})(transId);
			}
		};
	    xhr.send();
}

/*function getGuestNameByRoomId(roomId){
	var uri = baseuri + '/search/fetchGuestNameByRoomId/' + roomId;
	document.getElementById('inGuestInfo').style.display = 'block';
	document.getElementById('inGuestInfo2').innerHTML = loader;
	xhr.open('GET', uri, true);
	xhr.onload = function(e){
		if(xhr.status == 200){
			document.getElementById('inGuestInfo2').innerHTML = this.responseText;
		}
	}
    xhr.send();
}*/



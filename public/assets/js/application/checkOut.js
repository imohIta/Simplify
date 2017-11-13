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
					document.getElementById('gAddr').innerHTML = res.gAddr;
					document.getElementById('gPhone').innerHTML = res.gPhone;
					document.getElementById('cDate').innerHTML = res.checkInDate;
					document.getElementById('rType').innerHTML = res.roomType;
					document.getElementById('rNo').innerHTML = res.roomNo;
					document.getElementById('guestId').value = res.gId;
					document.getElementById('roomId').value = value;
				
					//get guest Transactions
					(function(guestId, roomId)
					{
					
                        var uri = baseuri + '/guest/fetchTransactions/' + guestId;
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
										 bDiv.innerHTML += '<tr><td>' + bill.date + '</td><td>' + bill.details + ' ( ' + bill.roomNo + ' ) </td><td>' + formatNumber(bill.amt) + '</td></tr>';
						             
									}

									bDiv.innerHTML += '<tr><td colspan="2"><strong>Total</strong></td><td>' + formatNumber(res.totalBill) + '</td></tr>';

									for (var i = 0, payment; payment = res.payments[i]; i++) 
									{

										 var res2 = JSON.parse(payment.details);
										 pDiv.innerHTML += '<tr><td>' + payment.date + '</td><td><small>Payment Type :</small> ' + res2['Pay Type'] + '</td><td>' + formatNumber(payment.amt) + '</td></tr>';
									}

									pDiv.innerHTML += '<tr><td colspan="2"><strong>Total</strong></td><td>' + formatNumber(res.totalPayment) + '</td></tr>';

									if(res.totalBill != res.totalPayment){
										//show checkout Options form
										(function (param) {
											
											var uri = baseuri + '/guest/showCheckOutOptions/' + guestId + '/' + param.totalBill + '/' + param.totalPayment + '/' + param.roomId;
											document.getElementById('checkOutNotes').innerHTML = '';
											xhr.open('GET', uri, true);
												xhr.onload = function(e){
													if(xhr.status == 200){
														document.getElementById('checkOutNotes').style.display = 'block';
														document.getElementById('checkOutNotes').innerHTML = this.responseText;

														//Add Required Attribute to checkout options radio buttons	
														var cOpts = document.getElementsByClassName('cOpts');
														for (var i = cOpts.length - 1; i >= 0; i--) 
														{
															 cOpts[i].setAttribute('required', 'required');
														}						
													}
												};
											xhr.send();

										})({'totalBill' : res.totalBill, 'totalPayment' : res.totalPayment, 'roomId' : roomId});
								    
								    }else{

								    	//hide check out options
								    	document.getElementById('checkOutNotes').innerHTML = '';
								    	document.getElementById('checkOutNotes').style.display = 'none';

								    	//Remove Required Attribute to checkout options radio buttons	
										var cOpts = document.getElementsByClassName('cOpts');
										for (var i = cOpts.length - 1; i >= 0; i--) 
										{
											 cOpts[i].removeAttribute('required');
										}
								    }

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



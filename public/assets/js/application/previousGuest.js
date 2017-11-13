
try{
var searchGuestBtn = document.getElementById('searchGuestBtn');
	searchGuestBtn.addEventListener('click', function(){
		var phone = document.getElementById('phone').value;
		var uri = baseuri + '/previousGuest/fetchStayRecords/' + phone;
		document.getElementById('loader').style.display = "block";
		document.getElementById('loader').innerHTML = loader;
		xhr.open('GET', uri, true);
			xhr.onload = function(e){
				if(xhr.status == 200){
					document.getElementById('loader').style.display = "none";
					//console.log(this.responseText); return false;
					document.getElementById('content').innerHTML = this.responseText;
				}
			};
		xhr.send();
	});

}catch(e){}


try{
var searchGuestBtn2 = document.getElementById('searchGuestBtn2');
	searchGuestBtn2.addEventListener('click', function(){
		var phone = document.getElementById('phone').value;
		var uri = baseuri + '/previousGuest/fetchCreditsAndPayments/' + phone;
		document.getElementById('loader').style.display = "block";
		document.getElementById('loader').innerHTML = loader;
		xhr.open('GET', uri, true);
			xhr.onload = function(e){
				if(xhr.status == 200){
					document.getElementById('loader').style.display = "none";
					//console.log(this.responseText); return false;
					document.getElementById('content').innerHTML = this.responseText;
				}
			};
		xhr.send();
	});

}catch(e){}

try{
var searchGuestBtn3 = document.getElementById('searchGuestBtn3');
	searchGuestBtn3.addEventListener('click', function(){
		var phone = document.getElementById('phone').value;
		var uri = baseuri + '/search/fetchGuestDetailsByPhone/' + phone;
		xhr.open('GET', uri, true);
			xhr.onload = function(e){
				if(xhr.status == 200){
					var res = JSON.parse(this.responseText);
					if(res.err === true){
						document.getElementById('errHolder').style.display = 'block';
						document.getElementById('errMsg').innerHTML = res.errMsg;
						document.getElementById('guestName').value = '';
						document.getElementById('guestId').value = '';
						document.getElementById('outCrdt').value = '';
					}else{
						
						document.getElementById('guestName').value = res.gName;
						document.getElementById('guestId').value = res.gId;
						
						document.getElementById('errMsg').innerHTML = '';
						document.getElementById('errHolder').style.display = 'none';
						

						(
							function(phone){
								var uri = baseuri + '/previousGuest/fetchCreditBal/' + phone;
								xhr.open('GET', uri, true);
									xhr.onload = function(e){
										if(xhr.status == 200){
											document.getElementById('outCrdt').value = this.responseText;
										}
									};
								xhr.send();
							})(phone);

					}
				}
			};
		xhr.send();
	});

}catch(e){}


function populateBills(bills){

	/*for(var i = 0, bill; bill = bills[i]; i++){
		console.log(bill.id);
	};*/
	var cnt = document.getElementById('billContent');
	cnt.innerHTML = '';
	var count = 1;
	bills.map(function(bill){
		cnt.innerHTML += '<tr><td>' + count + '<td>' + bill.date + '</td><td>' + bill.transId + '</td><td>' + bill.details + ' ( ' + bill.roomNo + ' ) </td><td>' + formatNumber(bill.amt) + '</td></tr>';
		count++;
	});
}


function populatePayments(payments){

	var cnt = document.getElementById('payContent');
	cnt.innerHTML = '';
	var count = 1;
	payments.map(function(pay){
		pay2 = JSON.parse(pay.details);
		cnt.innerHTML += '<tr><td>' + count + '<td>' + pay.date + '</td><td>' + pay.transId + '</td><td>Pay Type : ' + pay2['Pay Type'] + '</td><td>' + formatNumber(pay.amt) + '</td></tr>';
		count++;
	});
}

function fetchCreditDetails(params){
	document.getElementById('nameHolder').innerHTML = params.guestName;
	var uri = baseuri + '/previousGuest/fetchCreditsAndPayments/' + params.guestPhone;
	document.getElementById('contentHolder').innerHTML = loader;
	xhr.open('GET', uri, true);
		xhr.onload = function(e){
			if(xhr.status == 200){
				document.getElementById('contentHolder').innerHTML = this.responseText;
			}
		};
	xhr.send();
}

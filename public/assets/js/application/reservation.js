
try{
	//var checkBtn = document.getElementById('checkAvailablity');
	//checkBtn.addEventListener('click', function(){
		//alert('hey');
		//var rooms = document.getElementById('rooms').value;
		//console.log(rooms); return false;

		/*var uri = baseuri + '/previousGuest/fetchStayRecords/' + phone;
		document.getElementById('loader').style.display = "block";
		document.getElementById('loader').innerHTML = loader;
		xhr.open('GET', uri, true);
			xhr.onload = function(e){
				if(xhr.status == 200){
					document.getElementById('loader').style.display = "none";
					//console.log(this.responseText); return false;
					document.getElementById('content').innerHTML = this.responseText;
				}
			}
		xhr.send();*/
	//});

}catch(e){}

function toggleEditOptions(value){
	var options = document.getElementsByClassName('options');
	for (var i = options.length - 1; i >= 0; i--) {
		 options[i].style.display = 'none';
	}
	document.getElementById(value).style.display = "block";
	if(value == 'addPayment'){
		document.getElementById('payRevId').value = document.getElementById('genRevId').value;
	}
}

function fetchReservation(param){
	var uri = baseuri + '/reservation/fetchByRevId/' + param.revId + '/' + param.sr;
		xhr.open('GET', uri, true);
			xhr.onload = function(e){
				if(xhr.status == 200){
					//console.log(this.responseText); return false;
					var res = JSON.parse(this.responseText);

					document.getElementById('genRevId').value = param.revId;

					document.getElementById('revId').value = param.revId;
					document.getElementById('guestName').value = res.guestName;
					document.getElementById('guestPhone').value = res.guestPhone;

					var rooms = JSON.parse(res.rooms);

					var selectedR = '';
					for(var r in rooms){
						selectedR += rooms[r] + ',';
					}


					document.getElementById('sRooms').value = selectedR.substring(0, selectedR.length - 1);
					document.getElementById('beginDate').value = res.beginDate;
					document.getElementById('endDate').value = res.endDate;
					document.getElementById('date').value = res.date;
					
				}
			};
		xhr.send()
}

function fetchReservationPayments(param) {
	// body...
	var uri = baseuri + '/reservation/fetchPaymentsByRevId/' + param.revId;
		xhr.open('GET', uri, true);
			xhr.onload = function(e){
				if(xhr.status == 200){
					//console.log(this.responseText); return false;
					var res = JSON.parse(this.responseText);

					var pDiv = document.getElementById('paymentsHolder');

					pDiv.innerHTML = '';
					
					var count = 1;
					for (var i = 0, pay; pay = res[i]; i++) 
					{
						 var pay2 = JSON.parse(pay.details);
						 //pDiv.innerHTML += '<tr><td>' + count + '</td><td>' + pay.date + '</td><td>' + formatNumber(pay.amt) + ' </td><td> Pay Type : ' + pay2['Pay Type'] + '</td></tr>';
		              	 
		              	 var tr = document.createElement('tr');
		              	 pDiv.appendChild(tr);
		              	 //
		              	 var count_td = document.createElement('td');
		              	 count_td.innerHTML = count;
		              	 tr.appendChild(count_td);
		              	 //
		              	 var pay_td = document.createElement('td');
		              	 pay_td.innerHTML = pay.date;
		              	 tr.appendChild(pay_td);
		              	 //
		              	 var amt_td = document.createElement('td');
		              	 amt_td.innerHTML = formatNumber(pay.amt);
		              	 tr.appendChild(amt_td);
		              	 //
		              	 var pay_type_td = document.createElement('td');
		              	 pay_type_td.innerHTML = 'Pay Type : ' + pay2['Pay Type'];
		              	 tr.appendChild(pay_type_td);

		              	 count++;
					}
					
				}
			};
		xhr.send()
}

function toogleAllowEdit(){
	
	if(document.getElementById('sRooms').getAttribute('readonly')){
		document.getElementById('sRooms').removeAttribute('readonly');
		document.getElementById('editBtn').innerHTML = 'Done';
	}else{
		document.getElementById('sRooms').setAttribute('readonly', 'readonly');
		document.getElementById('editBtn').innerHTML = 'Edit';
	}
}

function populateCancelReservation (param) {
	// body...
	document.getElementById('revId3').value = param.revId;
}

function populateCheckInGuest(param){

	var uri = baseuri + '/reservation/checkInFromReservation/' + param.revId + '/' + param.sr;
		xhr.open('GET', uri, true);
			xhr.onload = function(e){
				if(xhr.status == 200){
					//console.log(this.responseText); return false;
					document.getElementById('checkInHolder').innerHTML = this.responseText;
					
				}
			};
		xhr.send();
}

function fetchRoomPrice(roomId){
	if(roomId == ""){
		document.getElementById('priceHolder').style.display = 'none';
		document.getElementById('sBtnHolder').style.display = 'none';
	}else{
		var uri = baseuri + '/search/fetchRoomPrice/' + roomId;
		xhr.open('GET', uri, true);
			xhr.onload = function(e){
				if(xhr.status == 200){
					document.getElementById('priceHolder').style.display = 'block';
					document.getElementById('roomPrice').value = this.responseText;
					document.getElementById('bill').value = this.responseText;

					document.getElementById('sBtnHolder').style.display = 'block';

					if(roomId < 69){
						document.getElementById('sBtn').innerHTML = 'Check In Guest';
					}else{
						document.getElementById('sBtn').innerHTML = 'Use Reservation';
					}
				}
			};
		xhr.send();
	}
}



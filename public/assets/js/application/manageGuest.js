function getGuestCheckInInfo(value){

	if(value == '0'){
		document.getElementById('topInfo').style.display = 'none';
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
					document.getElementById('topInfo').style.display = 'block';
					document.getElementById('gName').innerHTML = res.gName;
					document.getElementById('oldDiscount').innerHTML = res.discount;
					document.getElementById('od').value = res.discount;
					document.getElementById('rType').innerHTML = res.roomType;
					document.getElementById('rNo').innerHTML = res.roomNo;
					document.getElementById('guestId').value = res.gId;
					document.getElementById('roomId').value = res.roomId;
					
				}
				
			}
		};
	xhr.send();
	
}



function getGuestInfoForTE(value, div){
    
	if(value == '0'){
		document.getElementById('topInfo'+div).style.display = 'none';
		document.getElementById('target').style.display = "none";
	}

	document.getElementById('target').style.display = "block";

	var roomId = '';
	if(div == '2'){
		roomId = document.getElementById('roomNo1').value;
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
					document.getElementById('topInfo'+div).style.display = 'block';
					document.getElementById('gName'+div).innerHTML = res.gName;
					document.getElementById('rType'+div).innerHTML = res.roomType;
					document.getElementById('rNo'+div).innerHTML = res.roomNo;

					document.getElementById('gId'+div).value = res.gId;
					document.getElementById('rId'+div).value = res.roomId;

					if(div == '2'){

						// if the room numbers r the same
						if(value == roomId){
							document.getElementById('billInfo').innerHTML = '<p class="bg-primary padd-sm text-white">You cannot Transfer Expenses to the Same room</p>';
						}else{
							(
								function(params){

									var guestId1 = document.getElementById('gId1').value;
									var roomId1 = document.getElementById('rId1').value;

									var uri = baseuri + '/search/getGuestBillTypes/' + params.guestId + '/' + guestId1 + '/' + params.roomId + '/' + roomId1 ;
									
									xhr.open('GET', uri, true);
									document.getElementById('loader2').style.display = "block";
									document.getElementById('loader2').innerHTML = loader;
									xhr.onload = function(e){
										if(xhr.status == 200)
										{
											document.getElementById('loader2').style.display = "none";
											
											
											if(res.status == 'success')
											{
												document.getElementById('billInfo').innerHTML = this.responseText;
												
											}
											
										}
									};
								xhr.send();
								}
							)({guestId : res.gId, roomId : res.roomId});
						}
				    }

				}
				
			}
		};
	xhr.send();
	
}


function deselectBillTypes(params){
	//alert('check2');
	for (var i = 0; i < params.length; i++) {
        document.getElementById(params[i]).checked = false;
    }
}

function switchExpenseTransferDateOptions(value){
	if(value == 'chooseDate'){
		document.getElementById('tDateOption').style.display = 'block';
	}else{
		document.getElementById('tsd').value = '';
		document.getElementById('tDateOption').style.display = 'none';
	}
}

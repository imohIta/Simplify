function getGuestCheckInInfo(value){
	if(value == 0){
		document.getElementById('guestInfo').style.display = 'none';
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
					document.getElementById('guestInfo').style.display = 'block';
					document.getElementById('gName').innerHTML = res.gName;
					document.getElementById('cDate').innerHTML = res.checkInDate;
					document.getElementById('rType').innerHTML = res.roomType;
					document.getElementById('rNo').innerHTML = res.roomNo;
					document.getElementById('guestId').value = res.gId;
					document.getElementById('oldRoomId').value = value;
					document.getElementById('discount').value = res.discount;

				}
				
			}
		};
	xhr.send();
	
}


function getRoomsByType(value){
	var uri = baseuri + '/search/getFreeRoomsByTypes/' + value;
	xhr.open('GET', uri, true);
		xhr.onload = function(e){
			if(xhr.status == 200){
				document.getElementById('roomsHolder').innerHTML = this.responseText; 
			}
		};
	xhr.send();
}

function fetchRoomPrice(roomId){
	// had to change the content of the function to suit the Page
	document.getElementById('reasonHolder').style.display = 'block';
	
}
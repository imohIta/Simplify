
try{
	var searchGuestBtn = document.getElementById('searchGuestBtn');
	searchGuestBtn.addEventListener('click', function(){
		var phone = document.getElementById('phone').value;
		var uri = baseuri + '/search/fetchGuestDetailsByPhone/' + phone;
		xhr.open('GET', uri, true);
			xhr.onload = function(e){
				if(xhr.status == 200){
					var res = JSON.parse(this.responseText);
					if(res.err === true){
						
						document.getElementById('errHolder').style.display = 'block';
						document.getElementById('errMsg').innerHTML = res.errMsg;
						document.getElementById('name').value = '';
						document.getElementById('addr').value = '';
						document.getElementById('nationality').value = '';
						document.getElementById('occu').value = '';
						document.getElementById('reason').value = '';

						document.getElementById('outBal').value = '';
						document.getElementById('balHolder').style.display = 'none';

						var cntry_chosen = document.getElementById('nationality_chosen');
						var cntry_chosen_span = cntry_chosen.getElementsByTagName("span")[0];
						cntry_chosen_span.innerHTML = 'Select an Option';
					
					}else{
						

						document.getElementById('errHolder').style.display = 'none';
						document.getElementById('name').value = res.gName;
						document.getElementById('addr').value = res.addr;
						document.getElementById('reason').value = res.reason;
						document.getElementById('occu').value = res.occu;
						
						var selected_indx = 0;
						for(var i = 0; i<= document.getElementById('nationality').options.length-1; i++) {
							if(document.getElementById('nationality').options[i].value == res.nationality){
								document.getElementById('nationality').options[i].setAttribute("selected", "selected");
							}
						}
						var cntry_chosen = document.getElementById('nationality_chosen');
						var cntry_chosen_span = cntry_chosen.getElementsByTagName("span")[0];
						cntry_chosen_span.innerHTML = res.nationality;

						var cntry_chosen_ul = cntry_chosen.getElementsByTagName("ul")[0];
						var cntry_chosen_ul_li = cntry_chosen_ul.childNodes;


						var selected_indx2 = 0;
						for(var i = 0; i<= document.getElementById('discount').options.length-1; i++) {
							if(document.getElementById('discount').options[i].value == res.discount){
								document.getElementById('discount').options[i].setAttribute("selected", "selected");
							}
						}
						var dis_chosen = document.getElementById('discount_chosen');
						var dis_chosen_span = dis_chosen.getElementsByTagName("span")[0];
						dis_chosen_span.innerHTML = res.discount + ' %';

						var dis_chosen_ul = dis_chosen.getElementsByTagName("ul")[0];
						var dis_chosen_ul_li = dis_chosen_ul.childNodes;
						
						
						
						(
							function(value){
								var uri = baseuri + '/search/getGuestOutstandingBal/' + value;
								xhr.open('GET', uri, true);
									xhr.onload = function(e){
										if(xhr.status == 200){
											var res = JSON.parse(this.responseText);
											if(res.err === false){
												document.getElementById('balHolder').style.display = 'block';
												document.getElementById('outBal').value = res.amt;
												
											}	
											
										}
									};
								xhr.send();
							}
						)(phone);
					}
				}
			};
		xhr.send();
	});


}catch(e){}





function fetchRoomPrice(roomId){
	var uri = baseuri + '/search/fetchRoomPrice/' + roomId;
	document.getElementById('priceLoader').style.display = 'block';
	document.getElementById('priceLoader').innerHTML = loader;
	xhr.open('GET', uri, true);
		xhr.onload = function(e){
			if(xhr.status == 200){
				document.getElementById('priceLoader').innerHTML = '';
				document.getElementById('priceLoader').style.display = 'none';

				document.getElementById('priceHolder').style.display = 'block';
				document.getElementById('roomPrice').value = this.responseText; 
				document.getElementById('bill').value = this.responseText;

				//get discount
				var discount = document.getElementById('discount').value;
				subtractDiscount(discount);
			}
		};
	xhr.send();
	
}












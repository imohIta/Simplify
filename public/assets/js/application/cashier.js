function fetchReturns(dept){
	var mDate = document.getElementById('date').value;
	document.getElementById('others').style.display = 'block';
	 var uri = baseuri + '/transaction/calculateReturns/' + dept + '/' + mDate;
									
	xhr.open('GET', uri, true);
	xhr.onload = function(e){
		if(xhr.status == 200)
		{
			
			document.getElementById('amtDue').value = this.responseText;
			document.getElementById('amtDue2').value = formatNumber(this.responseText);
			 
		}
	};
	xhr.send();
}

function fetchPosCreditPayments(param){

	 var uri = baseuri + '/cashier/fetchDeptCreditPayments/' + param.transId + '/' + param.date + '/' + param.priv;
	
	document.getElementById('content').innerHTML = loader;							
	xhr.open('GET', uri, true);
	xhr.onload = function(e){
		if(xhr.status == 200)
		{
			
			document.getElementById('content').innerHTML = this.responseText;
				 
		}
	};
	xhr.send();
}

try{
	var searchCreditBtn = document.getElementById('searchCreditBtn');
	searchCreditBtn.addEventListener('click', function(){
		
		var dept = document.getElementById('dept').value;
		if(dept == 0){
			alert('Please Select a Department');
			return false;
		}

		var date = document.getElementById('date').value;
		if(date == ''){
			alert('Please Enter Credit Date');
			return false;
		}

		

		var uri = baseuri + '/cashier/fetchDeptCreditBal/' + date + '/' + dept;
		xhr.open('GET', uri, true);
			xhr.onload = function(e){
				if(xhr.status == 200){
					var res = JSON.parse(this.responseText);
					if(res.status == 'error'){
						document.getElementById('others').style.display = 'none';
						document.getElementById('errHolder').style.display = 'block';
						document.getElementById('errMsg').innerHTML = res.errMsg;
						
						/*var cntry_chosen = document.getElementById('nationality_chosen');
						var cntry_chosen_span = cntry_chosen.getElementsByTagName("span")[0];
						cntry_chosen_span.innerHTML = 'Select an Option';*/
					}else{
						document.getElementById('errHolder').style.display = 'none';
						document.getElementById('others').style.display = 'block';
						document.getElementById('transId').value = res.transId;
						document.getElementById('amtDue').value = res.amt;
						document.getElementById('amtDue2').value = formatNumber2(res.amt);
						
					}
				}
			};
		xhr.send();
	});


}catch(e){}

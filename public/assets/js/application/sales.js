function setItemPrice(itemId){
	var uri = baseuri + '/search/getItemPrice/' + itemId;
									
	xhr.open('GET', uri, true);
	xhr.onload = function(e){
		if(xhr.status == 200)
		{
			var res = JSON.parse(this.responseText);
			document.getElementById('price').value = res.price;
			document.getElementById('qtyInStock').value = res.qtyInStock;

			document.getElementById('qty').removeAttribute('readonly');
			
		}
	};
	xhr.send();
} 

function setMenuPrice(itemId){
	var uri = baseuri + '/search/getMenuPrice/' + itemId;
									
	xhr.open('GET', uri, true);
	xhr.onload = function(e){ 
		if(xhr.status == 200)
		{
			var res = JSON.parse(this.responseText);
			document.getElementById('price').value = res.price;
			document.getElementById('qty').removeAttribute('readonly');
			 
		}
	};
	xhr.send();
}

function calculateAmount (qty, div) {
	// body...
	var oldAmt = document.getElementById('amt').value;
	validateNosOnly(qty, div);
	var price = document.getElementById('price').value;
	if(isNaN(qty)){
		document.getElementById('amt').value = oldAmt;
	}else{
		document.getElementById('amt').value = qty * price;
	}
}




try	{

	var addSalesBtn = document.getElementById('addSalesBtn');
	addSalesBtn.addEventListener('click', function(){

		var itemId = document.getElementById('item').value;
		var price = document.getElementById('price').value;
		var qty = document.getElementById('qty').value;
		var amt = document.getElementById('amt').value;
		var qtyInStock = document.getElementById('qtyInStock').value;

		document.getElementById('item').value = '';
		document.getElementById('price').value = '';
		document.getElementById('qty').value = '';
		document.getElementById('amt').value = '';
		document.getElementById('qtyInStock').value = '';
		document.getElementById('qty').setAttribute('readonly','readonly');

		var form = new FormData();
		form.append('data', JSON.stringify(
			{'itemId' : itemId, 'price' : price, 'qty' : qty, 'amt' : amt, 'qtyInStock' : qtyInStock}
			));

		var uri = baseuri + '/sales/addTemp';
									
		xhr.open('POST', uri, true);
		xhr.onload = function(e){
			if(xhr.status == 200)
			{
				var res = JSON.parse(this.responseText);
				if(res.status == 'error'){
					document.getElementById('errorHolder').innerHTML = res.msg;
				}else{

					var tbl = document.getElementById('tempHolder');
					tbl.style.display = "block";
					tbl.innerHTML = '';

					var r = JSON.parse(res.msg);
					
					
					for (var i = 0, p; p = r[i]; i++) 
					{
						
		              	 var tr = document.createElement('tr');
		              	 tbl.appendChild(tr);
		              	 //
		              	 var item_td = document.createElement('td');
		              	 item_td.style.width = '35%';
		              	 item_td.setAttribute('align', 'center');
		              	 item_td.innerHTML = p.itemName;
		              	 tr.appendChild(item_td);
		              	 //
		              	 var price_td = document.createElement('td');
		              	 price_td.style.width = '20%';
		              	 price_td.setAttribute('align', 'center');
		              	 price_td.innerHTML = p.price;
		              	 tr.appendChild(price_td);
		              	 //
		              	 var qty_td = document.createElement('td');
		              	 qty_td.style.width = '19%';
		              	 qty_td.setAttribute('align', 'center');
		              	 qty_td.innerHTML = p.qty;
		              	 tr.appendChild(qty_td);
		              	 //
		              	 var amt_td = document.createElement('td');
		              	 amt_td.style.width = '18%';
		              	 amt_td.setAttribute('align', 'center');
		              	 amt_td.innerHTML = p.amt;
		              	 tr.appendChild(amt_td);

		              	 var nothing = document.createElement('td');
		              	 nothing.style.width = '8%';
		              	 nothing.setAttribute('align', 'center');
		              	 nothing.innerHTML = '<button type="submit" class="btn btn-danger btn-circle btn-sm saleOptions" data-toggle="modal" data-target="#saleDeleteOptions" onclick="populateDelete(' + p.itemId +')">Delete</button>';
		              	 tr.appendChild(nothing);

					}
				}
				
			}
		};
		xhr.send(form);


	});


 
}catch(e){}

function populateDelete(id){
	document.getElementById('itemId').value = id;
}

function cancelUnpostedSale(transId){
	document.getElementById('transId3').value = transId;
}

function viewUnpostedSale(transId){

    // @todo reverse all previous edit actions here

    resetEditIncompleteSale();

	var gType;

	document.getElementById('trans').innerHTML = '';
	document.getElementById('gType').innerHTML = '';
	document.getElementById('rNo').innerHTML = '';

	var uri = baseuri + '/sales/fetchUnpostedSaleByTransId/' + transId;

	xhr.open('GET', uri, true);
		xhr.onload = function(e){
			if(xhr.status == 200){
				var data = JSON.parse(this.responseText);
				var res = data.res;

				if(data.guestType == 1){
					gType = ' In Guest';
				}else if(data.guestType == 2){
					gType = ' Out Guest';
				}else{
					gType = ' Staff';
				}


				document.getElementById('trans').innerHTML = transId;
				document.getElementById('gType').innerHTML = gType;
				document.getElementById('rNo').innerHTML = data.roomNo;
	

				var tbl = document.getElementById('myTable');
				tbl.innerHTML = '';

				var total = 0;
				var c = 1;

				for (var i = 0, p; p = res[i]; i++) 
				{

		              	 var tr = document.createElement('tr');
                         tr.setAttribute('id', 'salesRole'+ p.autoId);
		              	 tbl.appendChild(tr);
		              	 //
		              	 var count = document.createElement('td');
		              	 count.innerHTML = c;
		              	 tr.appendChild(count);

		              	 var desc = document.createElement('td');
		              	 desc.innerHTML = p.itemName;
		              	 tr.appendChild(desc);

		              	 var qty = document.createElement('td');
		              	 qty.innerHTML = p.qty;
		              	 tr.appendChild(qty);

		              	 var rate = document.createElement('td');
		              	 rate.innerHTML = p.price;
		              	 tr.appendChild(rate);

		              	 var amt = document.createElement('td');
		              	 amt.innerHTML = p.amt;
		              	 tr.appendChild(amt);

                        var btnHolder = document.createElement('td');
                        btnHolder.innerHTML = '<button class="btn btn-small btn-circle btn-danger editBtnSmall" onclick="deleteIncompleteSale(' + p.autoId + ')" style="display:none">Delete</button>';
                        tr.appendChild(btnHolder);

		              	 total = parseInt(total) + parseInt(p.amt);
		              	 c++;

		        }

		        var tr2 = document.createElement('tr');
		        tbl.appendChild(tr2);
		        var td = document.createElement('td');
		        td.setAttribute('colspan', '4');
		        td.innerHTML = '<strong>Total</strong>';
		        tr2.appendChild(td);

		        var td2 = document.createElement('td');
		        td2.innerHTML = formatNumber2(total);
		        tr2.appendChild(td2);

                var td3 = document.createElement('td');
                td3.innerHTML = '<a href="' + baseuri + '/sales/addToIncomplete/' +transId +'"><button class="btn btn-small btn-circle btn-default editBtnSmall" style="display:none">Add</button></a>';
                tr2.appendChild(td3);

		        (
		        	function (param) {
		        		// body...
		        		var uri = baseuri + '/sales/getPostOptions/' + param.guestType + '/' + param.roomId;
		        		document.getElementById('optionsHolder').innerHTML = loader;
						xhr.open('GET', uri, true);
							xhr.onload = function(e){
								if(xhr.status == 200){
									document.getElementById('optionsHolder').innerHTML = this.responseText;
									document.getElementById('transId').value = transId;
									document.getElementById('guestType').value = param.guestType;
									document.getElementById('roomId').value = param.roomId;
								}
							};
						xhr.send();
		        	}
		        )({guestType : data.guestType, roomId : data.roomId, transId : data.transId});


			}
		};
	xhr.send();
}

/*function checkSalesPayType(value){
	
	var payOptions = document.getElementsByClassName('payOption');
	for (var i = payOptions.length - 1; i >= 0; i--) 
	{
		 payOptions[i].style.display = 'none';
	}

	/*var bigOptions = document.getElementsByClassName('bigOption');
	for (var i = bigOptions.length - 1; i >= 0; i--) 
	{
		 bigOptions[i].removeAttribute('required');
	}*/

	
	/*if(value != 0){
		document.getElementById(value).style.display = 'block';
		document.getElementById('submit').style.display = 'block';

		/*if(value == 'cash'){
			
			document.getElementById('guestType').setAttribute('required', 'required');
		}else if(value == 'credit'){
			
			document.getElementById('buyerName').setAttribute('required', 'required');
		}
	}
}*/



function checkGuestType(value){


	var guestOptions = document.getElementsByClassName('guestOption');
	for (var i = guestOptions.length - 1; i >= 0; i--) 
	{
		 guestOptions[i].style.display = 'none';
	}

	/*var smallOptions = document.getElementsByClassName('smallOption');
	for (var i = smallOptions.length - 1; i >= 0; i--) 
	{
		 smallOptions[i].removeAttribute('required');
	}*/

	
	if(value != 0){
		document.getElementById(value).style.display = 'block';
		/*if(value == 'inGuest'){
			document.getElementById('guestRoom').setAttribute('required', 'required');
		}else if(value == 'outGuest'){
			document.getElementById('buyerName').setAttribute('required', 'required');
		}*/
	}

}

function checkCreditType (value) {
	// body...
	var creditOptions = document.getElementsByClassName('creditOption');
	for (var i = creditOptions.length - 1; i >= 0; i--) 
	{
		 creditOptions[i].style.display = 'none';
	}
	if(value != 0){
		document.getElementById(value).style.display = 'block';
	}
}

function setGuestType(guestType) {
	// body...

	if(guestType == 0){
		tooglePrintBtn('none');
		document.getElementById('roomNo').style.display = 'none';
		return false;
	}


	document.getElementById('guestType2').value = guestType;

	if(guestType == 1){
		document.getElementById('roomNo').style.display = 'block';
		tooglePrintBtn('none');
	}else{
		document.getElementById('roomNo').style.display = 'none';
		tooglePrintBtn('block');
	}

	
}

function tooglePrintBtn (display) {
	// body...
	document.getElementById('printBtn').style.display = display;
}

function setPurchaserDetails (value) {
	// body...
	document.getElementById('roomId').value = value;
	tooglePrintBtn('block');
}

function resetEditIncompleteSale(){
    hide('editDone');
    show('editSale');
    var btns = document.getElementsByClassName('editBtnSmall');
    for (var i = btns.length - 1; i >= 0; i--) {
        btns[i].style.display = 'none';
    }
}

function deleteIncompleteSale(id){

    hide('salesRole'+id);
    var uri = baseuri + '/sales/deleteIncompleteSaleById/' + id;

    xhr.open('GET', uri, true);
    xhr.onload = function(e){
        if(xhr.status == 200)
        {
           console.log('deleted');
        }
    };
    xhr.send();
}

try{
	var editSaleBtn = document.getElementById('editSale');
	editSaleBtn.addEventListener('click', function(){
        hide('editSale');
        show('editDone');
		var transId = getContent('transId');
        var btns = document.getElementsByClassName('editBtnSmall');
        for (var i = btns.length - 1; i >= 0; i--) {
            btns[i].style.display = 'block';
        }

	});

    var editDone = document.getElementById('editDone');
    editDone.addEventListener('click',function(){
        hide('editDone');
        show('editSale');
        var btns = document.getElementsByClassName('editBtnSmall');
        for (var i = btns.length - 1; i >= 0; i--) {
            btns[i].style.display = 'none';
        }

    });
} catch (e){}






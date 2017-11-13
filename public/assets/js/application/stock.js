
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

function calculateAmt (price, div) {
	// body...
	var oldAmt = document.getElementById('amt').value;
	validateNosOnly(price, div);
	var qty = document.getElementById('qty').value;
	if(isNaN(price) || isNaN(qty)){
		document.getElementById('amt').value = '';
	}else{
		document.getElementById('amt').value = qty * price;
	}
}


try	{

	var addStkBtn = document.getElementById('addStkBtn');
	addStkBtn.addEventListener('click', function(){

		var itemId = document.getElementById('item').value;
		var price = document.getElementById('price').value;
		var qty = document.getElementById('qty').value;
		var amt = document.getElementById('amt').value;

		document.getElementById('item').value = '';
		document.getElementById('price').value = '';
		document.getElementById('qty').value = '';
		document.getElementById('amt').value = '';

		toggleOthers(0);

		var form = new FormData();
		form.append('data', JSON.stringify(
			{'itemId' : itemId, 'price' : price, 'qty' : qty, 'amt' : amt, 'qtyInStock' : qtyInStock}
			));

		var uri = baseuri + '/stock/addTemp';

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
		              	 nothing.innerHTML = '<button type="submit" class="btn btn-danger btn-circle btn-sm saleOptions" data-toggle="modal" data-target="#stkDeleteOptions" onclick="populateDelete(' + p.itemId +')">Delete</button>';
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

function toggleOthers(value){
	if(value == 0){
		document.getElementById('price').setAttribute('readonly','readonly');
		document.getElementById('qty').setAttribute('readonly','readonly');
	}else{
		document.getElementById('qty').removeAttribute('readonly');
		document.getElementById('price').removeAttribute('readonly');
	}
}

function viewStkDetails(id){

	var uri = baseuri + '/stock/viewStockPurchaseDetails/'+id;
	populate(id,'stkId');
	xhr.open('GET', uri, true);
		xhr.onload = function(e){
			if(xhr.status == 200){
				document.getElementById('content').innerHTML = this.responseText;
			}
		};
	xhr.send();
}

function populate(id, div){

	document.getElementById(div).value = id;
}



/*
		Item Functions
*/

function addRQtyField(){
	var lastAdded = document.getElementById('reductionsNo').value;
	var next = parseInt(lastAdded) + 1;

	document.getElementById('loader').style.display = 'block';
	document.getElementById('loader').innerHTML = loader;

	var uri = baseuri + '/stock/addMenuReductionItem/'+next;
		xhr.open('GET', uri, true);
			xhr.onload = function(e){
				if(xhr.status == 200){
					document.getElementById('loader').style.display = 'none';
					var removeBtns = document.getElementsByClassName('removeBtn');
					for (var i = removeBtns.length - 1; i >= 0; i--)
					{
						 removeBtns[i].style.display = 'none';
					}
					document.getElementById('reductionContent').innerHTML += this.responseText;
					document.getElementById('reductionsNo').value = next;


				}
			};
		xhr.send();

}

function removeRQtyField(id){

	var previous = parseInt(id) - 1;

	var parent = document.getElementById('reductionContent');
	var last = parent.childNodes.length - 1;
	parent.removeChild(parent.childNodes[last]);

	document.getElementById('removeBtn'+previous).style.display = 'block';
	document.getElementById('reductionsNo').value = previous;

}

function fetchItemDetails(itemId){
	var uri = baseuri + '/search/fetchItemDetails/'+itemId;
		xhr.open('GET', uri, true);

		document.getElementById('loader').style.display = 'block';
		document.getElementById('loader').innerHTML = loader;

			xhr.onload = function(e){
				if(xhr.status == 200){
					document.getElementById('loader').style.display = 'none';
					document.getElementById('holder').style.visibility = 'visible';

					var res = JSON.parse(this.responseText);

					document.getElementById('name').value = res.name;
					document.getElementById('id').value = res.id;

					var selected_indx = 0;
					for(var i = 0; i<= document.getElementById('type').options.length-1; i++) {
						if(document.getElementById('type').options[i].value == res.typeId){
							document.getElementById('type').options[i].setAttribute("selected", "selected");
						}
					}
					var type_chosen = document.getElementById('type_chosen');
					var type_chosen_span = type_chosen.getElementsByTagName("span")[0];
					type_chosen_span.innerHTML = res.type;

					var type_chosen_ul = type_chosen.getElementsByTagName("ul")[0];
					var type_chosen_ul_li = type_chosen_ul.childNodes;

					var selected_indx2 = 0;
					for(var i = 0; i<= document.getElementById('unit').options.length-1; i++) {
						if(document.getElementById('unit').options[i].value == res.unitId){
							document.getElementById('unit').options[i].setAttribute("selected", "selected");
						}
					}
					var unit_chosen = document.getElementById('unit_chosen');
					var unit_chosen_span = unit_chosen.getElementsByTagName("span")[0];
					unit_chosen_span.innerHTML = res.unit;

					 var unit_chosen_ul = unit_chosen.getElementsByTagName("ul")[0];
					 var unit_chosen_ul_li = unit_chosen_ul.childNodes;


					document.getElementById('pbPrice').value = res.poolBarPrice;
					document.getElementById('mbPrice').value = res.mainBarPrice;
					document.getElementById('rdPrice').value = res.resDrinksPrice;

				}
			};
	xhr.send();
}



function fetchMenuDetails(menuId){
	var uri = baseuri + '/search/fetchMenuDetails/'+menuId;
		xhr.open('GET', uri, true);

		document.getElementById('loader2').style.display = 'block';
		document.getElementById('loader2').innerHTML = loader;

			xhr.onload = function(e){
				if(xhr.status == 200){
					document.getElementById('loader2').style.display = 'none';
					document.getElementById('holder').style.visibility = 'visible';

					var res = JSON.parse(this.responseText);


					document.getElementById('name').value = res.name;
					document.getElementById('id').value = res.id;

					var selected_indx = 0;
					for(var i = 0; i<= document.getElementById('type').options.length-1; i++) {
						if(document.getElementById('type').options[i].value == res.typeId){
							document.getElementById('type').options[i].setAttribute("selected", "selected");
						}
					}
					var type_chosen = document.getElementById('type_chosen');
					var type_chosen_span = type_chosen.getElementsByTagName("span")[0];
					type_chosen_span.innerHTML = res.type;

					var type_chosen_ul = type_chosen.getElementsByTagName("ul")[0];
					var type_chosen_ul_li = type_chosen_ul.childNodes;

					document.getElementById('price').value = res.price;

					document.getElementById('reductions').innerHTML = '';


					for (var i = 0, r; r = res.reductions[i]; i++)
					{
						document.getElementById('reductions').innerHTML += '<p><input type="text" disabled value="' + r.item +'"  class="form-control22" style="margin-right:12px; width:130px; display:"  /><input type="text" value="'+ r.qty +'" disabled class="form-control22" style="width:50px" /></p>';
					}

					//document.getElementById('reductions').innerHTML += '</table>';
				}
			};
	xhr.send();
}

function switchRed(){
	var val = document.getElementById('switch-button-4').value;
	if(val == '1'){
		// document.getElementById('reductions').style.display = 'none';
		document.getElementById('newReductions').style.display = 'block';
		document.getElementById('reductionsNo').value = '1';
		document.getElementById('switch-button-4').value = '2';
	}else{
		document.getElementById('newReductions').style.display = 'none';
		// document.getElementById('reductions').style.display = 'block';
		document.getElementById('reductionsNo').value = '0';
		document.getElementById('switch-button-4').value = '1';
	}
}

function fetchReqDetails(param){
	document.getElementById('nameHolder').innerHTML = param.itemName;
	document.getElementById('contentHolder').innerHTML = loader;

	var uri = baseuri + '/requisition/fetchRequisitionDetails/'+param.itemId;
		xhr.open('GET', uri, true);
			xhr.onload = function(e){
				if(xhr.status == 200){
					
					document.getElementById('contentHolder').innerHTML = this.responseText;
	
				}
			};
		xhr.send();
}

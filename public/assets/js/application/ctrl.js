//define baseurl
window.baseuri = document.getElementById('uriHolder').innerHTML;
window.loaderSrc = baseuri + '/assets/images/loading/ajax-loader.gif';
window.loader = '<p style="text-align:center; margin:20px auto"><img src="' + loaderSrc + '" alt="Loading..." /></p>';
var xhr = new XMLHttpRequest();

function hide(div){
	document.getElementById(div).style.display = 'none';
}

function show(div){
	document.getElementById(div).style.display = 'block';
}

function getValue(div){
	return document.getElementById(div).value;
}

function setValue(div, value){
	document.getElementById(div).value = value;
}

function getContent(div){
	return document.getElementById(div).innerHTML;
}

function setContent(div, value){
	document.getElementById(div).innerHTML = value;
}

function addContent(div, value){
	document.getElementById(div).innerHTML += value;
}

function validateNosOnly(value, div){
	if(isNaN(value)){
		document.getElementById(div).value = value.substring(0, value.length - 1);
		return false;
	}

}

function printInv(div){
	document.getElementById(div).style.display = "none";
	var classes = document.getElementsByClassName(div);
	for (var i = classes.length - 1; i >= 0; i--) {
		 classes[i].style.display = 'none';
	}
	window.print();
	document.getElementById(div).style.display = "block";
	for (var i = classes.length - 1; i >= 0; i--) {
		 classes[i].style.display = 'block';
	}
}

function checkPayType(value){
	var val = String.toLowerCase(value) + 'Option';
	var payOptions = document.getElementsByClassName('payOptions');
	for (var i = payOptions.length - 1; i >= 0; i--) {
		 payOptions[i].style.display = 'none';
	}
	var classes = document.getElementsByClassName(val);
	for (var i = classes.length - 1; i >= 0; i--) {
		 classes[i].style.display = 'block';
	}
}


function formatNumber(number)
{
	if(number != '' && number !== null){
	    //number = number.toFixed(2) + '';
	    x = number.split('.');
	    x1 = x[0];
	    x2 = x.length > 1 ? '.' + x[1] : '';
	    var rgx = /(\d+)(\d{3})/;
	    while (rgx.test(x1)) {
	        x1 = x1.replace(rgx, '$1' + ',' + '$2');
	    }
	    //return x1 + x2;
	    return x1;
	}else{ return ''; }
}

function formatNumber2(number)
{
	if(number != '' && number !== null){
	    number = number.toFixed(2) + '';
	    x = number.split('.');
	    x1 = x[0];
	    x2 = x.length > 1 ? '.' + x[1] : '';
	    var rgx = /(\d+)(\d{3})/;
	    while (rgx.test(x1)) {
	        x1 = x1.replace(rgx, '$1' + ',' + '$2');
	    }
	    //return x1 + x2;
	    return x1;
	}else{ return ''; }
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

function subtractDiscount(value){
	var price = document.getElementById('roomPrice').value;
	if(value == 0 ){
		document.getElementById('bill').value = price;
	}else{
		var dis = (value/100) * parseInt(price);
		if(price != '' || price !== null){
			document.getElementById('bill').value = price - dis;
		}

	}
}



function toggleInputType(div){
	if(document.getElementById(div).type == 'password'){
		document.getElementById(div).type = 'text';
	}else{
		document.getElementById(div).type = 'password';
	}
}

// repeat action after every 1 hrs
setTimeout(function(){
	var uri = baseuri + '/guest/autoBill';
	xhr.open('GET', uri, true);
		xhr.onload = function(e){
			if(xhr.status == 200){
				console.log('Guest AutoBilled');
			}
		};
	xhr.send();

	//setTimeout(arguments.callee, 1000 * 60 * 60 * 2);

}, 1000 * 60 * 60); 


// repeat action after every 6 hrs
setTimeout(function(){
	var uri = baseuri + '/admin/setShiftTimes';
	xhr.open('GET', uri, true);
		xhr.onload = function(e){
			if(xhr.status == 200){
				console.log('Shift Times Set');
			}
		};
	xhr.send();

}, 1000 * 60 * 60 * 6); 


function checkSalesPayType(value){
	// body...

	if(value == ""){
		document.getElementById('submitBtn').style.display = 'none';
	}else{

		document.getElementById('submitBtn').style.display = 'block';

		var payOptions = document.getElementsByClassName('payOption');
		for (var i = payOptions.length - 1; i >= 0; i--)
		{
			 payOptions[i].style.display = 'none';
		}
		if(document.getElementById(value)){
			document.getElementById(value).style.display = 'block';
		}
	}

}

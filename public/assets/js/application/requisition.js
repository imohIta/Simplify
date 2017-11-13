function reqAction(params){

	document.getElementById(params.div + '_id').value = params.id;
	if(params.div == 'issue'){
		document.getElementById(params.div+'_itemId').value = params.itemId;
		document.getElementById(params.div+'_tbl').value = params.tbl;
		document.getElementById(params.div+'_qty').value = params.qty;
		document.getElementById(params.div+'_staffId').value = params.staffId;
		document.getElementById(params.div+'_dept').value = params.dept;
	}
}
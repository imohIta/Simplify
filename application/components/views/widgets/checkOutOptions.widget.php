<input type="hidden" name="type" value="<?php echo $msg['type']; ?>"  />
<input type="hidden" name="diff" value="<?php echo $msg['diff']; ?>"  />

<?php
if($msg['type'] == '1'){
# if guest bill is more than guest payment
?>

<div class="padd-sm" style="background:#F5F5F5; border: 1px solid #e8e8eb; box-shadow: none;">
	Guest Bill is more than the Guest Payment. Guest has to make a balance Payment of =N= <?php echo number_format($msg['diff']); ?> <br />
    <strong>What do you want to do ?</strong>

    <hr />
 
    <div class="form-group">
    <label class="col-sm-2 ">Balance Collected</label>
    <div class="col-sm-9">
        <div class="switch-button sm showcase-switch-button">
            <input id="switch-button-6" name="switch-radio" value="1" type="radio" class="cOpts" onclick = "togglePayOptions('show')">
            <label for="switch-button-6"></label>
        </div>
    </div> 
    
    <br />
   

    <div id="pOptions" style="display:none">
	    
	    <hr >
	    <table style="width:400px;">
	    <tr><td align="right" style="width:135px">Payment Type</td>
	    	<td style="width:37px">&nbsp;</td>
	    	<td><select class="form-control chosen-select" name="payType" id="payType" style="width:150px; height:30px" onChange="checkPayType(this.value)">
	          <option></option>
	          <option value="Cash">Cash</option>
	          <option value="Cheque">Cheque</option>
	          <option value="POS">POS</option>
	          <option value="BT">Bank Transfer</option>
	        </select>
	    </td></tr>
	    </table>

  
		  
	<span id="payOptionsHolder">

		<table style="width:400px; display:none; margin-top:5px" class="payOptions chequeOption">
	    <tr><td align="right" style="width:135px">Bank</td>
	    	<td style="width:37px">&nbsp;</td>
	    	<td>
	          <select class="form-control chosen-select" name="chequeBank" id="chequeBank" style="width:180px; height:30px">
	          <option></option>
	          <option value="Access Bank Plc">Access Bank Plc</option> 
	          <option value="Citibank Nigeria Limited">Citibank Nigeria Limited</option> 
	          <option value="Diamond Bank Plc">Diamond Bank Plc</option> 
	          <option value="Ecobank Nigeria Plc">Ecobank Nigeria Plc</option> 
	          <option value="Enterprise Bank">Enterprise Bank</option>  
	          <option value="Fidelity Bank Plc">Fidelity Bank Plc</option> 
	          <option value="First Bank of Nigeria Plc">First Bank of Nigeria Plc</option> 
	          <option value="First City Monument Bank Plc">First City Monument Bank Plc</option> 
	          <option value="Guaranty Trust Bank Plc">Guaranty Trust Bank Plc</option> 
	          <option value="Heritage Banking Company Ltd">Heritage Banking Company Ltd</option> 
	          <option value="Key Stone Bank">Key Stone Bank</option> 
	          <option value="MainStreet Bank">MainStreet Bank</option> 
	          <option value="Skye Bank Plc">Skye Bank Plc</option> 
	          <option value="Stanbic IBTC Bank Ltd">Stanbic IBTC Bank Ltd</option> 
	          <option value="Standard Chartered Bank Nigeria Ltd">Standard Chartered Bank Nigeria Ltd</option> 
	          <option value="Sterling Bank Plc">Sterling Bank Plc</option> 
	          <option value="Union Bank of Nigeria Plc">Union Bank of Nigeria Plc</option> 
	          <option value="United Bank For Africa Plc">United Bank For Africa Plc</option> 
	          <option value="Unity Bank Plc">Unity Bank Plc</option> 
	          <option value="Wema Bank Plc">Wema Bank Plc</option> 
	          <option value="Zenith Bank Plc">Zenith Bank Plc</option>
	        </select>
	       </td></tr>
	    </table>

	    <table style="width:400px; display:none; margin-top:5px" class="payOptions chequeOption">
	    <tr><td align="right" style="width:135px">Cheque No.</td>
	    	<td style="width:37px">&nbsp;</td>
	    	<td>
	          <input type="text" name="chequeNo" id="chequeNo" class="form-control form-control-circle"  >
	       </td></tr>
	    </table>

	    <table style="width:400px; display:none; margin-top:5px" class="payOptions posOption">
	    <tr><td align="right" style="width:135px">POS Receipt No.</td>
	    	<td style="width:37px">&nbsp;</td>
	    	<td>
	          <input type="text" name="posNo" id="posNo" class="form-control form-control-circle"  >
	       </td></tr>
	    </table>

	    <table style="width:400px; display:none; margin-top:5px" class="payOptions btOption">
	    <tr><td align="right" style="width:135px">Bank</td>
	    	<td style="width:37px">&nbsp;</td>
	    	<td>
	          <select class="form-control chosen-select" name="btbank" id="btBank" style="width:180px; height:30px">
	          <option></option>
	          <option value="Access Bank Plc">Access Bank Plc</option> 
	          <option value="Citibank Nigeria Limited">Citibank Nigeria Limited</option> 
	          <option value="Diamond Bank Plc">Diamond Bank Plc</option> 
	          <option value="Ecobank Nigeria Plc">Ecobank Nigeria Plc</option> 
	          <option value="Enterprise Bank">Enterprise Bank</option>  
	          <option value="Fidelity Bank Plc">Fidelity Bank Plc</option> 
	          <option value="First Bank of Nigeria Plc">First Bank of Nigeria Plc</option> 
	          <option value="First City Monument Bank Plc">First City Monument Bank Plc</option> 
	          <option value="Guaranty Trust Bank Plc">Guaranty Trust Bank Plc</option> 
	          <option value="Heritage Banking Company Ltd">Heritage Banking Company Ltd</option> 
	          <option value="Key Stone Bank">Key Stone Bank</option> 
	          <option value="MainStreet Bank">MainStreet Bank</option> 
	          <option value="Skye Bank Plc">Skye Bank Plc</option> 
	          <option value="Stanbic IBTC Bank Ltd">Stanbic IBTC Bank Ltd</option> 
	          <option value="Standard Chartered Bank Nigeria Ltd">Standard Chartered Bank Nigeria Ltd</option> 
	          <option value="Sterling Bank Plc">Sterling Bank Plc</option> 
	          <option value="Union Bank of Nigeria Plc">Union Bank of Nigeria Plc</option> 
	          <option value="United Bank For Africa Plc">United Bank For Africa Plc</option> 
	          <option value="Unity Bank Plc">Unity Bank Plc</option> 
	          <option value="Wema Bank Plc">Wema Bank Plc</option> 
	          <option value="Zenith Bank Plc">Zenith Bank Plc</option>
	        </select>
	       </td></tr>
	    </table>

	    <table style="width:400px; display:none; margin-top:5px" class="payOptions btOption">
	    <tr><td align="right" style="width:135px">Transfer Date</td>
	    	<td style="width:37px">&nbsp;</td>
	    	<td>
	          <input type="text" name="btDate" id="btDate" class="form-control form-control-circle" placeholder="YYYY-MM-DD"  >
	       </td></tr>
	    </table>
	   

	</span>

	<hr />

	 </div>

    <br style="clear:both"/>
    
    <label class="col-sm-2 control-label">Credit CheckOut</label>
    <div class="col-sm-9">
        <div class="switch-button sm showcase-switch-button" >
            <input id="switch-button-7" name="switch-radio" value="2" type="radio" class="cOpts" onclick = "togglePayOptions('hide')">
            <label for="switch-button-7"></label>
        </div>
        
    </div>
  </div>

  <br style="clear:both" />

</div>


<?php 

}else{
	# if guest payment is more than the guest bill
?>



<div class="padd-sm" style="background:#F5F5F5; border: 1px solid #e8e8eb; box-shadow: none;">
	Guest Payment is more than the Guest Bill. Guest is due a refund of =N= <?php echo number_format($msg['diff']); ?><br  />
    What do you want to do ?

    <hr />

    <div class="form-group">
    <label class="col-sm-2 ">Refund Made</label>
    <div class="col-sm-9">
        <div class="switch-button sm showcase-switch-button">
            <input id="switch-button-6" name="switch-radio" value="1" type="radio" class="cOpts">
            <label for="switch-button-6"></label>
        </div>
    </div>  

    <br style="clear:both"/>
    
    <label class="col-sm-2 control-label">Send to Guest Balances</label>
    <div class="col-sm-9">
        <div class="switch-button sm showcase-switch-button" >
            <input id="switch-button-7" name="switch-radio" value="2" type="radio" class="cOpts">
            <label for="switch-button-7"></label>
        </div>
        
    </div>
  </div>

  <br style="clear:both" />

</div>

<?php } ?>
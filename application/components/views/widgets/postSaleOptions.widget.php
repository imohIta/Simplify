<?php
global $registry;
$baseUri = $registry->get('config')->get('baseUri');
?>
<form method="post" action="<?php echo $baseUri; ?>/sales/unposted">

<input type="hidden" id="transId" name="transId" />
<input type="hidden" id="guestType" name="guestType" />
<input type="hidden" id="roomId" name="roomId" />

<?php

switch ($msg['guestType']) {
	case 1:
		# inGuest
	?>

    <table style="width:350px">
      <tr>
        <td style="width:150px"><p>Payment Type</p><td>
        <td><select class="form-control" style="width:150px" name="payType" id="payType" onChange="checkSalesPayType(this.value)" required>
              <option value=""></option>
              <option value="cash">Cash</option>
              <option value="credit">Credit</option>
              <option value="postBill">Post Bill</option>
              <option value="pos">POS</option>
             </select>
          </td>
      </tr>
      </table>

      <table style="width:350px; margin-top:10px; display:none" id="pos" class="payOption">
      		<tr>
		        <td style="width:150px"><p>Pos Reciept No.</p><td>
		        <td>
		          	<input type="text" name="posReceiptNo" class="form-control form-control-circle"  style="width:150px" >
		        </td>
		      </tr>
      </table>

	<?php

		break;

	case 2:
		# outGuest
	?>

	<table style="width:350px">
      <tr>
        <td style="width:150px"><p>Payment Type</p><td>
        <td><select class="form-control" style="width:150px" name="payType" id="payType" onChange="checkSalesPayType(this.value)" required>
              <option value=""></option>
              <option value="cash">Cash</option>
              <option value="credit">Credit</option>
              <option value="pos">POS</option>
             </select>
          </td>
      </tr>
      </table>

      <table style="width:350px; margin-top:10px; display:none" id="pos" class="payOption">
      		<tr>
		        <td style="width:150px"><p>Pos Reciept No.</p><td>
		        <td>
		          	<input type="text" name="posReceiptNo" class="form-control form-control-circle"  style="width:150px" >
		        </td>
		      </tr>
      </table>

      <table style="width:350px; margin-top:10px; display:none" id="credit" class="payOption">
      		<tr>
		        <td style="width:150px"><p>Buyer's Name</p><td>
		        <td>
		          	<input type="text" name="debtorName" class="form-control form-control-circle"  style="width:250px" >
		        </td>
		      </tr>
      </table>


    <?php

    	break;
	
	case 3:
		# staff

	?>

	<table style="width:350px">
      <tr>
        <td style="width:150px"><p>Payment Type</p><td>
        <td><select class="form-control" style="width:150px" name="payType" id="payType" onChange="checkSalesPayType(this.value)" required>
              <option value=""></option>
              <option value="cash">Cash</option>
              <option value="credit">Credit</option>
              <option value="pos">POS</option>
             </select>
          </td>
      </tr>
      </table>

      <table style="width:350px; margin-top:10px; display:none" id="pos" class="payOption">
      		<tr>
		        <td style="width:150px"><p>Pos Reciept No.</p><td>
		        <td>
		          	<input type="text" name="posReceiptNo" class="form-control form-control-circle"  style="width:150px" >
		        </td>
		      </tr>
      </table>

      <table style="width:350px; margin-top:10px; display:none" id="credit" class="payOption">
      		<tr>
		        <td style="width:150px"><p>Staff Name</p><td>
		        <td>
		          	<select class="form-control" style="width:150px" name="staffId">
                    <option value="0"></option>
                    <?php
                    foreach (Staff::fetchAll() as $key) {
                      # code...
                      $staff = new Staff($key->id);
                    ?>
                    <option value="<?php echo $staff->id; ?>"><?php echo ucwords($staff->name); ?></option>
                    <?php } ?>
                  </select>
		        </td>
		      </tr>
      </table>

   <?php
		break;
	}
  ?>

  <table id="submitBtn" style="width:350px; margin-top:10px; display:none">
      <tr>
	    <td>
	       <button type="submit" name="submit" class="btn btn-success btn-circle">Post Sale</button>
	    </td>
	  </tr>
    </table>

   </form>
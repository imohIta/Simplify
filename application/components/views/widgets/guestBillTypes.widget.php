<?php 
$billTypes = $msg['billTypes']; 
$beneficiaryRoom = new Room($msg['beneficiaryRoom']);
$payerRoom = new Room($msg['payerRoom']);
?>

<div class="panel panel-default">
    <div class="panel-heading">SELECT GUEST BILL TYPES TO TRANSFER FROM ROOM <?php echo $beneficiaryRoom->no; ?> TO <?php echo $payerRoom->no; ?></div>
    <div class="panel-body table-responsive" >

      <form role="form" method="post" action="<?php echo $registry->get('config')->get('baseUri'); ?>/guest/transferExpenses">

            <input type="hidden" name="payer" value="<?php echo $msg['payer']; ?>" >
            <input type="hidden" name="beneficiary" value="<?php echo $msg['beneficiary']; ?>" >
            <input type="hidden" name="payerRoom" value="<?php echo $msg['payerRoom']; ?>" >
            <input type="hidden" name="beneficiaryRoom" value="<?php echo $msg['beneficiaryRoom']; ?>" >

           <!--  <input type="hidden" name="guestId2" value="<?php echo $msg['guestId']; ?>" >
            <input type="hidden" name="roomId2" value="<?php echo $msg['roomId']; ?>" > -->
              
              <div class="form-group">
                <label class="cr-styled">
                    <input type="checkbox" ng-model="todo.done" id="all" name="billType" value="all" <?php if(in_array('All', $billTypes) !== false || ( in_array('Room Charge', $billTypes) !== false && in_array('Pool Bar', $billTypes) !== false && in_array('Main Bar', $billTypes) !== false && in_array('Resturant', $billTypes) !== false && in_array('Reception', $billTypes) !== false ) ) { ?> checked <?php } ?> onclick="deselectBillTypes(['roomCharge', 'POSUnits']);"  >
                    <i class="fa"></i> 
                </label>
                All Bills ( Cover Both Room Charge & POS Units )
              </div>

              <div class="form-group">
                <label class="cr-styled">
                    <input type="checkbox" ng-model="todo.done" id="roomCharge" name="billType" value="roomCharge" <?php if(in_array('Room Charge', $billTypes) !== false){ ?> checked <?php } ?> onclick="deselectBillTypes(['all', 'POSUnits']);" >
                    <i class="fa"></i> 
                </label>
                Room Charge Only
              </div>

              <div class="form-group">
                <label class="cr-styled">
                    <input type="checkbox" ng-model="todo.done" id="POSUnits" name="billType" value="POSUnits" <?php if( in_array('Pool Bar', $billTypes) !== false && in_array('Main Bar', $billTypes) !== false && in_array('Resturant', $billTypes) !== false && in_array('Reception', $billTypes) !== false ){ ?> checked <?php } ?>onclick="deselectBillTypes(['all', 'roomCharge']);" >
                    <i class="fa"></i> 
                </label>
                POS Units ( Main Bar, Pool Bar & Resturant ) Only
              </div>

             <select class="form-control" style="width:150px" name="startTransferFrom" onchange="switchExpenseTransferDateOptions(this.value);" >
              <option value="0">Start from</option>
              <option value="today">Today</option>
              <option value="checkInDate">Check In Date</option>
              <option value="chooseDate" >Choose Date</option>
            </select>

           <br />

            
            <span id='tDateOption' style='display:none'>
              <input type="text" name="transferStartDate" id="tsd"  class="form-control form-control-circle mDate" placeholder="YYYY-MM-DD" autocomplete="off" style="width:150px">
            </span>
         

            <br />

            <div class="col-sm-9">
              <button type="submit" name="submit" class="btn btn-success">Submit</button>
            </div>

            </form>
        
    </div>
</div>
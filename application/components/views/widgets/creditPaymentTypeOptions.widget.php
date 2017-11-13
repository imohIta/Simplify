<?php
global $registry;

# fetch sales to get room No
$salesInfo = $registry->get('db')->fetchSalesByTransId($msg->transId);
?>
<input type="hidden" name="debtorType" value="<?php echo $msg->debtorType; ?>">
<input type="hidden" name="roomId" value="<?php echo $salesInfo[0]->roomId; ?>">

<div class="form-group">
      <label class="col-sm-3 control-label">Payment Type</label>
      <div class="col-sm-4">
          <!-- onChange="getRoomByType(this.value)" -->
        <select class="form-control chosen-select" name="payType" id="payType" onChange="checkSalesPayType(this.value)" required>
            <option></option>
            <option value="cash" style="padding:4px">Cash</option>
            <option value="pos" style="padding:4px">POS</option>
            <?php
            if($msg->debtorType == 3){
            ?>
            <option value="postBill" style="padding:4px">Post Bill</option>
            <?php } ?>
          </select>
      </div>
</div>

<div class="form-group payOption" id="pos" style="display:none">
    <label class="col-sm-3 control-label">POS Reciept No</label>
    <div class="col-sm-4x">
      <input type="text" name="posNo" class="form-control form-control-circle" style="width:150px; margin-left:4px" >
    </div>
</div>

<!-- <div class="form-group payOption" id="postBill" style="display:none">
    <label class="col-sm-3 control-label">Guest Room No.</label>
    <div class="col-sm-4x">
      <select class="form-control"  name="guestRoom" style="width:150px; margin-left:10px" onchange="getSalesDetails(this.value)">
        <option value="0"></option>
        <?php
          foreach (Room::getOccupied() as $r) {
          $room = new Room($r->roomId);
        ?>
        <option value="<?php echo $room->id; ?>" style="padding:4px"><?php echo $room->no; ?></option>
        <?php } ?>
        
        </select>
    </div>
  </div>


  <div class="form-group payOption" id="inGuestInfo" style="display:none">
    <label  class="col-sm-3 control-label"><p class="text-info">Guest Name</p></label>
    <div class="col-sm-4x">
        <p class="text-warning" id="inGuestInfo2"></p>
    </div>
</div> -->

  
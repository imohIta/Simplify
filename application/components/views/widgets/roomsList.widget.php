<div class="form-group">
    <label class="col-sm-3 control-label">Room No.</label>
    <div class="col-sm-3">
      <select class="form-control chosen-select" name="roomNo" id="room" onChange="<?php echo $msg['jsAction']; ?>(this.value)" required>
          <option></option>
          <?php
          foreach ($msg['data'] as $room) {
           ?>
          <option value="<?php echo $room->id; ?>"><?php echo $room->roomNo; ?></option>
          <?php } ?>
        </select> 
    </div>
</div>
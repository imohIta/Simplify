<?php
$trans = $msg['trans'];
$details = json_decode($trans->details);
?>

<hr >
<input type="hidden" name="found" value="yes" />
<div class="form-group">
<label for="inputPassword3" class="col-sm-3 control-label">&nbsp</label>
 <div class="col-sm-9">
 <p class="text-primary">Transaction Date : <?php echo dateToString($trans->date); ?> </p>
<p class="text-primary">Transaction Type : <?php echo $trans->desc; ?> </p>
<!-- <p class="text-primary">Description : <?php echo $details->desc; ?> </p> -->
</div>
</div>

<hr >

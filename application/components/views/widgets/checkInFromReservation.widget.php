<?php
$data = (object) $msg;
global $registry;
$baseUri = $registry->get('config')->get('baseUri');
?>

			<form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/reservation/checkInFromReservation">

                <input name="guestId" type="hidden" value = "<?php echo $data->guestId; ?>" />
        				<input name="revId" type="hidden" value = "<?php echo $data->revId; ?>" />
        				<input name="src" type="hidden" value = "<?php echo $data->src; ?>" />
        				<input name="roomCount" type="hidden" value = "<?php echo count(json_decode($data->rooms, true)); ?>" />
                           

                               <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Guest Phone</label>
                                <div class="col-sm-9">
                                  <input type="text" name="phone"  class="form-control form-control-circle" id="phone" autocomplete="off" value="<?php echo $data->phone; ?>">
                                </div>
                              </div>

                            

                              <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Check-in Date</label>
                                <div class="col-sm-9">
                                  <input type="text" name="date" class="form-control form-control-circle inputmask" data-inputmask="'alias': 'yyyy-mm-dd'" placeholder="yyyy-mm-dd" readonly value="<?php echo today(); ?>" placeholder="" required>
                                </div>
                              </div>

                              <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Guest Name</label>
                                <div class="col-sm-9">
                                  <input type="text" name="name" id="name" class="form-control form-control-circle" value="<?php echo $data->name; ?>" autocomplete="off" required>
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Address</label>
                                <div class="col-sm-9">
                                  <input type="text" name="addr" id="addr" class="form-control form-control-circle" value="<?php echo $data->addr; ?>" autocomplete="off" required>
                                </div>
                              </div>

                              <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Occupation</label>
                                <div class="col-sm-9">
                                  <input type="text" name="occu" id="occu" class="form-control form-control-circle" id="inputPassword3" value="<?php echo $data->occu; ?>" autocomplete="off" required>
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Reason for Visit</label>
                                <div class="col-sm-9">
                                  <input type="text" name="reason" id="reason" class="form-control form-control-circle"  value="<?php echo $data->reason; ?>" >
                                </div>
                              </div>

                              <div class="form-group">
                                    <label class="col-sm-3 control-label">Nationality</label>
                                    <div class="col-sm-3">
                                      <select class="form-control chosen-select form-control-circle" id="nationality" name="nationality" required >
                                          <option></option>
                                          <option value="Nigerian" <?php if($data->nationality == 'Nigerian'){ ?> selected <?php } ?>>Nigerian</option>
                                          <option value="Non-Nigerian" <?php if($data->nationality == 'Non Nigerian'){ ?> selected <?php } ?>>Non Nigerian</option>
                                        </select>
                                    </div>
                              </div>
                              <div class="form-group">
                                    <label class="col-sm-3 control-label">No of Occupants</label>
                                    <div class="col-sm-3">
                                      <select class="form-control chosen-select form-control-circle" data-placeholder="" name="noOfOccupants" >
                                      	  <option></option>
                                          <option value="1">1</option>
                                          <option value="2">2</option>
                                          <option value="3">3</option>
                                          <option value="4">4</option>
                                          <option value="5">5+</option>
                                        </select>
                                    </div>
                              </div>
                              

                              <hr>

                              <div class="form-group">
                                    <label class="col-sm-3 control-label">Select Room</label>
                                    <div class="col-sm-3">
                                        <!-- onChange="getRoomByType(this.value)" -->
                                      <select class="form-control form-control-circle chosen-select" name="roomId" onChange="fetchRoomPrice(this.value)" required>
                                          <option></option>
                                          <?php
                                          foreach (json_decode($data->rooms) as $roomId => $roomNo ) {
                                           ?>
                                          <option value="<?php echo $roomId; ?>"><?php echo $roomNo; ?></option>
                                          <?php } ?>
                                        </select>
                                    </div>
                              </div>

                             
                              <span id="priceHolder" style="display:none">
                                    <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Room Price</label>
                                    <div class="col-sm-3">
                                      <input type="text" name="roomPrice" id="roomPrice" class="form-control form-control-circle" readonly placeholder="">
                                    </div>
                                  </div>
                              </span>

                             
                              <hr />

                              <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Bill</label>
                                    <div class="col-sm-3">
                                      <input type="text" name="bill" id="bill" class="form-control form-control-circle" readonly >
                                    </div>
                               </div>

                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Total Reservation Deposit</label>
                                    <div class="col-sm-3">
                                      <input type="text" name="revDeposit" value="<?php echo $data->totalDeposit; ?>" class="form-control form-control-circle" readonly >
                                    </div>
                              </div>


                              <div class="form-group">
                                    <label class="col-sm-3 control-label">Discount</label>
                                    <div class="col-sm-3">
                                      <select name="discount" id="discount" class="form-control chosen-select form-control-circle" onchange="subtractDiscount(this.value)">
                                          <option value="<?php echo $data->discount; ?>" selected><?php echo $data->discount; ?> %</option>
                                          <?php 
                                          for ($i=0; $i <= 10 ; $i++) { 
                                          ?>
                                          <option value="<?php echo $i * 5; ?>"><?php echo $i * 5; ?> %</option>
                                          <?php } ?>
                                        </select>
                                    </div>
                              </div>

                             


                              <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Deposit</label>
                                <div class="col-sm-9">
                                  <input type="text" name="deposit1" id="deposit1" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" autocomplete="off" onkeyup="validateNosOnly(this.value, 'deposit1')" required style="width:120px">
                                  <span class="help-block"><small>Deposit Amount will be deducted from the Guest Reservation Payments</small></span>
                                </div>
                              </div>

                              <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Confirm Deposit Amt</label>
                                <div class="col-sm-3">
                                  <input type="text" name="deposit2" id="deposit2" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" autocomplete="off" onkeyup="validateNosOnly(this.value, 'deposit2')" required> 
                                </div>
                              </div>

                              
                             
                              <div class="form-group" id="sBtnHolder" style="display:none">
                                <div class="col-sm-offset-3 col-sm-9">
                                  <button type="submit" name="submit" class="btn btn-success btn-circle" id="sBtn"></button>
                                </div>
                              </div>

                            </form>
<?php
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | reception )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,7), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

/*if(!$session->read('occupiedRoomslist')){
   $session->write('occupiedRoomslist', $registry->get('db')->getOccupiedRooms());
}*/

$occupiedRooms = Room::getOccupied();

	#include header
	$registry->get('includer')->render('header', array('css' => array(
                                'plugins/typeahead/typeahead.css',
                                'plugins/bootstrap-tagsinput/bootstrap-tagsinput.css',
                                'plugins/bootstrap-chosen/chosen.css',
                                'plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.css',
                                'switch-buttons/switch-buttons.css',
                                'font-awesome.min.css'
                                )));

	#include Sidebar
	$registry->get('includer')->render('sidebar', array());


	#include small header
	$registry->get('includer')->renderWidget('smallHeader');
?>

    <div class="warper container-fluid">

            <div class="page-header"><h1>Add Bill <small>&nbsp;</small></h1></div>

            <div class="page-header">
              <h1>
                <small><a href="<?php echo $baseUri; ?>/guest/transactions">Transactions</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/guest/addBill">Add Bill</a></small>
              </h1>
          </div>


            <div class="form-group">

                  <div class="col-sm-3">
                      <select class="form-control chosen-select form-control-circle" id="roomNo" name="roomNo" onchange="getGuestInfoForBills(this.value);" >
                        <option value="0"> Select Guest Room</option>
                        <?php foreach ($occupiedRooms as $r) {
                          $room = new Room($r->roomId);
                        ?>
                        <option value="<?php echo $room->id; ?>" ><?php echo $room->no; ?></option>
                        <?php } ?>
                      </select>
                  </div>
            </div>

            <br style="clear:both" />

            <hr>

             <?php
                 if($registry->get('session')->read('formMsg')){
                  echo $registry->get('session')->read('formMsg');
                  $registry->get('session')->write('formMsg', NULL);
                 }
              ?>

            <div class="row">

              <div id="loader" style="width:120px; margin:40px auto; display:none"></div>


              <div class="col-md-4 topInfo" style="display:none">
                  <div class="panel panel-default">
                        <div class="panel-heading">GUEST INFO</div>

                        <div class="panel-body ">

                            <p><small>Guest Name :</small> <span id="gName"> </span></p>

                            <hr>

                            <p><small>Phone :</small> <span id="gPhone"> </span></p>

                            <hr>

                            <p><small>Room Type :</small> <span id="rType"> </span></p>

                            <hr>

                            <p><small>Room No :</small> <span id="rNo"> </span></p>

                            <br />  <br />

                        </div>
                    </div>


                </div>

                <div class="col-md-8 topInfo" style="display:none">

                    <div class="panel panel-default">
                        <div class="panel-heading">ADD TO GUEST BILL</div>
                        <div class="panel-body">

                        <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/guest/addBill">

                        <input type="hidden" id="guestId" name="guestId" value="" />
                        <input type="hidden" id="roomId" name="roomId" value="" />

                        <div class="form-group">
                          <label for="inputEmail3" class="col-sm-3 control-label">Date</label>
                          <div class="col-sm-9">
                            <input type="text" name="date" placeholder="yyyy-mm-dd" class="form-control form-control-circle inputmask" data-inputmask="'alias': 'yyyy-mm-dd'" readonly value="<?php echo $today; ?>" placeholder="">
                          </div>
                        </div>

                        <!-- <div class="form-group">
                                    <label class="col-sm-3 control-label">Bill Type</label>
                                    <div class="col-sm-3">
                                      <select class="form-control" data-placeholder="" name="billType" style="width:130px">
                                          <option></option>
                                          <option value="1">Room Charge</option>
                                          <option value="2">2</option>
                                          <option value="3">3</option>
                                          <option value="4">4</option>
                                          <option value="5">5+</option>
                                        </select>
                                    </div>
                              </div> -->

                        <div class="form-group">
                          <label for="inputEmail3" class="col-sm-3 control-label">Bill Desc.</label>
                          <div class="col-sm-9">
                            <input type="text" name="desc" class="form-control form-control-circle" value="<?php echo $session->read('desc') ? $session->read('desc') : ''; $session->write('desc', null); ?>" autocomplete="off">
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="inputPassword3" class="col-sm-3 control-label">Amount</label>
                          <div class="col-sm-3">
                            <input type="text" name="amt1" id="amt1" value="<?php echo $session->read('amt1') ? $session->read('amt1') : ''; $session->write('amt1', null); ?>" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" autocomplete="off">
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="inputPassword3" class="col-sm-3 control-label">Confirm Amount</label>
                          <div class="col-sm-3">
                            <input type="text" name="amt2" id="amt2" value="<?php echo $session->read('amt2') ? $session->read('amt2') : ''; $session->write('amt2', null); ?>" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'">
                          </div>
                        </div>

                        <div class="form-group" style="padding-bottom:8px">
                          <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" name="submit" class="btn btn-success">Add Bill</button>
                          </div>
                        </div>

                        </form>

                        </div>
                    </div>



                 </div>


            </div>




        </div>
        <!-- Warper Ends Here (working area) -->


        <?php
        	$registry->get('includer')->render('footer', array('js' => array(
                                  'plugins/nicescroll/jquery.nicescroll.min.js',
                                  'plugins/typehead/typeahead.bundle.js',
                                  'plugins/typehead/typeahead.bundle-conf.js',
                                  'plugins/inputmask/jquery.inputmask.bundle.js',
                                  'plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js',
                                  'plugins/bootstrap-chosen/chosen.jquery.js',
                                  'moment/moment.js',
                                  'plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.js',
                                  'app/custom.js',
                                  'application/ctrl.js',
                                  'application/transactions.js'
                                  )));

        	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js'
        ?>

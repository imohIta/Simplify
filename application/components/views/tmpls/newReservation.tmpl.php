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

$rooms = Room::fetchAll();

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
    	
	
    
<!-- Page Body here...Editable region -->
    
    <div class="warper container-fluid">

      <div class="page-header">
        <h1>New Reservation<small style="color:#FF404B">&nbsp;</small></h1>
      </div>
    	
        
        
         <div class="row">
        
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading">&nbsp;</div>
                    <div class="panel-body">

                     <?php 
                         if($registry->get('session')->read('formMsg')){
                          echo $registry->get('session')->read('formMsg');
                          $registry->get('session')->write('formMsg', NULL);
                         }
                      ?>
                    
                        <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/reservation/">

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Date</label>
                            <div class="col-sm-9">
                              <input type="text" name="date" class="form-control form-control-circle input-sm" readonly value="<?php echo $today; ?>" placeholder="">
                            </div>
                          </div>

                          <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Guest Name</label>
                                <div class="col-sm-9">
                                  <input type="text" name="guestName" id="name" class="form-control form-control-circle input-sm"  autocomplete="off" value="<?php echo $session->read('guestName') ? $session->read('guestName') : ''; $session->write('guestName', null); ?>">
                                </div>
                          </div>
                          <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">Guest Phone</label>
                            <div class="col-sm-9">
                              <input type="text" name="guestPhone" id="phone" class="form-control form-control-circle input-sm" autocomplete="off" value="<?php echo $session->read('guestPhone') ? $session->read('guestPhone') : ''; $session->write('guestPhone', null); ?>">
                            </div>
                          </div>

                          <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Select Rooms</label>
                                <div class="col-sm-9">
                                  <select class="form-control form-control-circle chosen-select" multiple data-placeholder="You can Select More Than one Room" name="rooms[]" id="rooms">
                                      <option></option>
                                      <?php
                                      foreach ($rooms as $room) {
                                      ?>
                                      <option value="<?php echo $room->id; ?>"><?php echo $room->roomNo; ?></option>
                                      <?php } ?>
                                    
                                  </select>
                                </div>
                          </div>

                          <div class="form-group">
                                <label class="col-sm-3 control-label">Begin Date</label>
                                <div class="col-sm-3">
                                  <input type="text" placeholder="yyyy-mm-dd" class="form-control form-control-circle inputmask input-sm" data-inputmask="'alias': 'yyyy-mm-dd'" name="beginDate" value="<?php echo $session->read('beginDate') ? $session->read('beginDate') : ''; $session->write('beginDate', null); ?>">
                                </div>
                          </div>

                          <div class="form-group">
                                <label class="col-sm-3 control-label">End Date</label>
                                <div class="col-sm-3">
                                  <input type="text" placeholder="yyyy-mm-dd" class="form-control form-control-circle inputmask input-sm" data-inputmask="'alias': 'yyyy-mm-dd'" name="endDate" value="<?php echo $session->read('endDate') ? $session->read('endDate') : ''; $session->write('endDate', null); ?>">
                                </div>
                          </div>

                          

                          <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                              <button name="checkAvailablity" class="btn btn-default btn-sm btn-circle" type="submit">Check Availablity</button>

                              <?php if($session->read('showOtherOptions')){ ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <button id="continue" type="button" class="btn btn-default btn-sm btn-circle" onclick ="document.getElementById('others').style.display = 'block'; document.getElementById('continue').style.display = 'none';" >Continue →</button>
                              <?php } $session->write('showOtherOptions', null); ?>

                            </div>
                          </div>
                         
                          <span id="others" style="display:none">

                          <hr>

                              <div class="form-group">
                                <label class="col-sm-3 control-label">Deposit </label>
                                <div class="col-sm-4">
                                  <input type="text" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" name="amt1">
                                </div>
                              </div>

                              <div class="form-group">
                                <label class="col-sm-3 control-label">Confirm Deposit </label>
                                <div class="col-sm-4">
                                  <input type="text" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" name="amt2">
                                </div>
                              </div>

                              <div class="form-group">
                                    <label class="col-sm-3 control-label">Payment Type</label>
                                    <div class="col-sm-3">
                                        <!-- onChange="getRoomByType(this.value)" -->
                                      <select class="form-control" name="payType" id="payType" onChange="checkPayType(this.value)" style="width:200px">
                                          <option></option>
                                          <option value="Cash">Cash</option>
                                          <option value="Cheque">Cheque</option>
                                          <option value="POS">POS</option>
                                          <option value="BT">Bank Transfer</option>
                                        </select>
                                    </div>
                              </div>
                              
                              <span id="payOptionsHolder">

                                    <div class="form-group payOptions chequeOption" style="display:none">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Bank</label>
                                    <div class="col-sm-9">
                                      <!-- <input type="text" name="chequeBank" id="chequeBank" class="form-control form-control-circle"  > -->
                                      <select class="form-control" name="chequeBank" id="chequeBank" style="width:200px">
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
                                    </div>
                                  </div>

                                  <div class="form-group payOptions chequeOption" style="display:none">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Cheque No</label>
                                    <div class="col-sm-3">
                                      <input type="text" name="chequeNo" id="chequeNo" class="form-control form-control-circle"  >
                                    </div>
                                  </div>

                                  <div class="form-group payOptions posOption" style="display:none">
                                    <label for="inputPassword3" class="col-sm-3 control-label">POS Reciept No</label>
                                    <div class="col-sm-3">
                                      <input type="text" name="posNo" id="posNo" class="form-control form-control-circle" >
                                    </div>
                                  </div>

                                  <div class="form-group payOptions btOption" style="display:none">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Bank</label>
                                    <div class="col-sm-9">
                                      <select class="form-control" name="btBank" id="btBank" style="width:200px">
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
                                    </div>
                                  </div>

                                  <div class="form-group payOptions btOption" style="display:none">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Transfer Date</label>
                                    <div class="col-sm-3">
                                      <input type="text" name="btDate" id="btDate" class="form-control form-control-circle inputmask" data-inputmask="'alias': 'yyyy-mm-dd'" placeholder="yyyy-mm-dd"  >
                                    </div>
                                  </div>
                             
                                  <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-9">
                                      <button type="submit" name="submit" class="btn btn-success btn-circle">Reserve</button>
                                    </div>
                                  </div>

                             </span>
                             


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
                              'application/reservation.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
    ?>
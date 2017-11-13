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
          
            <div class="page-header"><h1>Add Payment <small>&nbsp;</small></h1></div>

            <div class="page-header">
              <h1>
                <small><a href="<?php echo $baseUri; ?>/guest/transactions">Transactions</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/guest/addPayment">Add Payment</a></small>
              </h1>
          </div>


            <div class="form-group">
  
                  <div class="col-sm-3">
                      <select class="form-control chosen-select form-control-circle" id="roomNo" name="roomNo" onchange="getGuestInfoForPayments(this.value);" >
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

                            <hr>
                        
                            <br />  <br />  <br />
                        
                        </div>
                    </div>
                   
                   
                </div>
                
                <div class="col-md-8 topInfo" style="display:none">
                  
                    <div class="panel panel-default">
                        <div class="panel-heading">ADD TO GUEST PAYMENTS</div>
                        <div class="panel-body">
                        
                        <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/guest/addPayment">
                        
                        <input type="hidden" id="guestId" name="guestId" value="" />
                        <input type="hidden" id="roomId" name="roomId" value="" />

                        <div class="form-group">
                          <label for="inputEmail3" class="col-sm-3 control-label">Date</label>
                          <div class="col-sm-9">
                            <input type="text" name="date" placeholder="yyyy-mm-dd" class="form-control form-control-circle inputmask" data-inputmask="'alias': 'yyyy-mm-dd'" readonly value="<?php echo $today; ?>" >
                          </div>
                        </div>

                        <!-- <div class="form-group">
                          <label for="inputEmail3" class="col-sm-3 control-label">Out. Balance</label>
                          <div class="col-sm-9">
                            <input type="text" name="outBal" id="outBal" readonly class="form-control form-control-circle" value="" autocomplete="off">
                          </div>
                        </div> -->

                        <div class="form-group">
                                    <label class="col-sm-3 control-label">Payment Type</label>
                                    <div class="col-sm-9">
                                        <!-- onChange="getRoomByType(this.value)" -->
                                      <select class="form-control " name="payType" id="payType" onChange="checkPayType(this.value)" style="width:200px">
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
                                <div class="col-sm-7">
                                  
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
                                <div class="col-sm-7">
                                  
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
                                  <input type="text" name="btDate" id="btDate" placeholder="yyyy-mm-dd" class="form-control form-control-circle inputmask" data-inputmask="'alias': 'yyyy-mm-dd'"  >
                                </div>
                              </div>

                              </span>

                        <div class="form-group">
                          <label for="inputPassword3" class="col-sm-3 control-label">Amount</label>
                          <div class="col-sm-3">
                            <input type="text" name="amt1" id="amt1" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" autocomplete="off">
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="inputPassword3" class="col-sm-3 control-label">Confirm Amount</label>
                          <div class="col-sm-3">
                            <input type="text" name="amt2" id="amt2" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" autocomplete="off"> 
                          </div>
                        </div>

                        <div class="form-group" style="padding-bottom:8px">
                          <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" name="submit" class="btn btn-success">Add Payment</button>
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
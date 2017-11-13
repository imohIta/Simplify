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

if($session->read('invioceData')){
  $session->write('invioceData', null);
}

# if user has not visited edit options page b4 dis one...redirect to dashboard
if(!$session->read('src')){
   $registry->get('uri')->redirect();
}

switch ($session->read('src')) { 
  case 'app':
    $src = 'Application';
    break;
  case 'web':
    $src = 'Online'; 
    break;

}

# fetch reservations for this src
$reservations = $registry->get('db')->fetchReservations($session->read('src'));


#include header
$registry->get('includer')->render('header', array('css' => array(
                                'plugins/typeahead/typeahead.css',
                                'plugins/bootstrap-tagsinput/bootstrap-tagsinput.css',
                                'plugins/bootstrap-chosen/chosen.css',
                                'switch-buttons/switch-buttons.css',
                                'plugins/datatables/jquery.dataTables.css',
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
        <h1>View Reservation <small style="color:#FF404B">( <?php echo $src; ?> )</small></h1>
         <h1>
          <small><a href="<?php echo $baseUri; ?>/reservation/viewOptions">View Reservations</a></small>
          <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
          <small><a href="<?php echo $baseUri; ?>/reservation/view/<?php echo $session->read('src'); ?>"><?php echo $src; ?></a></small>
        </h1>
      </div>
    	
        
        
         <div class="row">
        
            <div class="col-md-11">

              
             <?php 
                 if($registry->get('session')->read('formMsg')){
                  echo $registry->get('session')->read('formMsg');
                  $registry->get('session')->write('formMsg', NULL);
                 }
              ?>

                <div class="panel panel-default">
                    <div class="panel-heading">Reservations List</div>
                    <div class="panel-body">

                    
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="basic-datatable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Rev. Begin Date</th>
                                    <th>Rev. End Date</th>
                                    <th>Guest Name</th>
                                    <th>Rooms</th>
                                    <th>Total Amt Paid</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $count = 1;
                              foreach ($reservations as $row) {
                                
                                # code...
                                $rooms = $registry->get('db')->fetchReservedRoomsByResId($row->reserveId, $session->read('src'));

                                $rm = '';
                                foreach ($rooms as $r) {
                                          $room = new Room($r->roomId);
                                          $rm .= $room->no . ', ';
                                        }
                                $rm = trim($rm, ", ");
                                //$class = ( $count % 2 == 0 ) ? 'even' : 'odd';

                                # give rooms the reservation data has reached a new color
                                $class = ( today() == $row->rStartDate && strtotime(today()) < strtotime($row->rEndDate) ) ? 'even' : 'odd';
                                $class2 = strtotime(today()) >= strtotime($row->rStartDate) ? 'btn-warning' : 'btn-default';

                                //var_dump($registry->get('db')->fetchReservationPayments($row->reserveId));

                                $payments = $registry->get('db')->fetchReservationPayments($row->reserveId);
                                $totalAmtPaid = 0;
                                foreach ($payments as $r) {
                                  # code...
                                  $totalAmtPaid += $r->amt;
                                }

                              ?>
                                 <tr class="<?php echo $class; ?> gradeX">
                                    <td><?php echo dateToString($row->date); ?></td>
                                    <td><?php echo dateToString($row->rStartDate); ?></td>
                                    <td><?php echo dateToString($row->rEndDate); ?></td>
                                    <td><?php echo $row->guestName; ?></td>
                                    <td><?php echo $rm; ?></td>
                                    <td><?php echo number_format($totalAmtPaid); ?></td>
                                    <td>
                                      
                                          <div class="btn-group">
                                            <button class="btn <?php echo $class2; ?>" type="button">Actions</button>
                                            <button type="button" class="btn <?php echo $class2; ?> dropdown-toggle" data-toggle="dropdown">
                                              <span class="caret"></span>
                                              <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul role="menu" class="dropdown-menu">
                                              <li><a href="javascript:void(0)"; onclick="fetchReservation({'revId' :  '<?php echo $row->reserveId; ?>', 'sr' : '<?php echo $session->read("src"); ?>' })"  data-toggle="modal" data-target="#editForm">Edit</a></li>
                                              <li><a href="javascript:void(0)"; onclick="fetchReservationPayments({'revId' : '<?php echo $row->reserveId; ?>', 'sr' : '<?php echo $session->read("src"); ?>' })" data-toggle="modal" data-target="#viewPayments">View Payment Details</a></li>
                                              <li><a href="javascript:void(0)"; onclick="populateCancelReservation({'revId' : '<?php echo $row->reserveId; ?>', 'sr' : '<?php echo $session->read("src"); ?>' })" data-toggle="modal" data-target="#cancel">Cancel</a></li>
                                              
                                              <?php if(strtotime($row->rStartDate) <= strtotime(today()) && strtotime($row->rEndDate) >= strtotime(today())){ ?>
                                                
                                                <li class="divider"></li>
                                                
                                                <li><a href="javascript:void(0)"; onclick="populateCheckInGuest({'revId' : '<?php echo $row->reserveId; ?>', 'sr' : '<?php echo $session->read("src"); ?>' })" data-toggle="modal" data-target="#checkIn">Use Reservation</a></li>
                                              
                                              <?php }  ?>

                                            </ul>
                                          </div> 
                                                                    
                                </tr> 
                                <?php } ?>
                              </tbody>
                            </table>

                    
                    </div>
                </div>
            </div>
            

            <!-- Edit Reservation Form -->
          <div class="modal fade" id="editForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Edit Reservation</h4>
                </div>
                <div class="modal-body">

                  <div class="form-group">
                          <label for="inputEmail3" class="col-sm-3 control-label">Select Action</label>
                    <div class="col-sm-9">
                      <select class="form-control" style="width:250px" onchange="toggleEditOptions(this.value)">
                          <option></option>
                          <option value="editDetails" selected >Edit Reservation Details</option>
                          <option value="addPayment">Add Payment</option>
                      </select>
                    </div>
              </div>

              <input type="hidden" id="genRevId" />

              <br />
              <hr>
              <br />

                    <span id="editDetails" class="options">

                      <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/reservation/view">

                        <input type="hidden" name="revId" id="revId" />
                        <input type="hidden" name="src" value="<?php echo $session->read('src'); ?>" />
                        <input type="hidden" name="date" id="date" />

                          <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Guest Name</label>
                                <div class="col-sm-9">
                                  <input type="text" name="guestName" id="guestName" class="form-control form-control-circle input-sm"  autocomplete="off" >
                                </div>
                          </div>
                          <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">Guest Phone</label>
                            <div class="col-sm-9">
                              <input type="text" name="guestPhone" id="guestPhone" class="form-control form-control-circle input-sm" autocomplete="off" >
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Reserved Rooms</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                          <input type="text" class="form-control form-control-circle" name="rooms" id="sRooms" readonly ="readonly">

                                          <span class="input-group-btn">
                                            <button class="btn btn-warning" id="editBtn" type="button" onclick="toogleAllowEdit();">Edit</button>
                                          </span>
                                        </div>
                                         <span class="help-block"><small>Rooms should be seperated by commas & should be in BLOCK letters.</small></span>
                                    </div>
                                  </div>

                          <div class="form-group">
                                <label class="col-sm-3 control-label">Begin Date</label>
                                <div class="col-sm-3">
                                  <input type="text" placeholder="yyyy-mm-dd" class="form-control form-control-circle inputmask input-sm" data-inputmask="'alias': 'yyyy-mm-dd'" name="beginDate" id="beginDate" value="<?php echo $session->read('beginDate') ? $session->read('beginDate') : ''; $session->write('beginDate', null); ?>">
                                </div>
                          </div>

                          <div class="form-group">
                                <label class="col-sm-3 control-label">End Date</label>
                                <div class="col-sm-3">
                                  <input type="text" placeholder="yyyy-mm-dd" class="form-control form-control-circle inputmask input-sm" data-inputmask="'alias': 'yyyy-mm-dd'" name="endDate" id="endDate" value="<?php echo $session->read('endDate') ? $session->read('endDate') : ''; $session->write('endDate', null); ?>">
                                </div>
                          </div>

                          <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                              <button type="submit" name="editDetails" class="btn btn-success btn-circle">Edit Reservation</button>
                            </div>
                          </div>
                         
                      </form>

                  </span>


                      
                      <!-- Add Payment Option -->
                      

                      <span id="addPayment" class="options" style="display:none">

                        <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/reservation/view">

                        <input type="hidden" name="revId" id="payRevId" />
                        <input type="hidden" name="src" value="<?php echo $session->read('src'); ?>" />

                        <div class="form-group">
                                <label class="col-sm-3 control-label">Amount </label>
                                <div class="col-sm-4">
                                  <input type="text" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" name="amt1">
                                </div>
                              </div>

                              <div class="form-group">
                                <label class="col-sm-3 control-label">Confirm Amount</label>
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
                                      <button type="submit" name="addPayment" class="btn btn-success btn-circle">Add Payment</button>
                                    </div>
                                  </div>

                             </span>
                             
                          </form>

                      </span>
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit form model Ends -->



    <!-- Cancel Form -->
      <div class="modal fade" id="cancel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Cancel Reservation</h4>
                </div>
                <div class="modal-body">

                  <p> Are You Sure you want to Cancel this Reservation </p>

                  <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/reservation/view">

                        <input type="hidden" name="revId" id="revId3" />
                        <input type="hidden" name="src" id="revId" value="<?php echo $session->read('src'); ?>" />
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                              <button type="button"  class="btn btn-danger btn-circle" data-dismiss="modal">Not Really</button>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              <button type="submit" name="cancel" class="btn btn-success btn-circle">Yea, Sure</button>
                            </div>
                          </div>
                     </form>
           </div>
           <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
           </div>
        </div>
      </div>
    </div>
    <!-- cancel Form Ends -->


    <!-- Check In Form -->
      <div class="modal fade" id="checkIn" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Use Reservation</h4>
                </div>
                <div class="modal-body" id="checkInHolder">

                  
               </div>
               <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               </div>
            </div>
      </div>
    </div>
    <!-- checkIn Form Ends -->


    <!-- Payments Normal Size -->
    <div class="modal fade" id="viewPayments" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel">Reservation Payments</h4>
          </div>
          <div class="modal-body">
            
            <div class="panel panel-default">
                        <div class="panel-heading">&nbsp;</div>
                        <div class="panel-body table-responsive">
                        
                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Details</th>
                                  </tr>
                                </thead>
                                <tbody id="paymentsHolder">
                                  
                                </tbody>
                              </table>
                        
                        </div>
                    </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
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
                              'plugins/datatables/jquery.dataTables.js',
                              'plugins/datatables/DT_bootstrap.js',
                              'plugins/datatables/jquery.dataTables-conf.js',
                              'app/custom.js',
                              'application/ctrl.js',
                              'application/reservation.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
    ?>
<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | reception | manager | auditor | accountant | Duty manager )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5,6,7), true);


$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

/*if(!$session->read('occupiedRoomslist')){
   $session->write('occupiedRoomslist', $registry->get('db')->getOccupiedRooms());
}*/
 
$occupiedRooms = Room::getOccupied();

 
	#include header
	$registry->get('includer')->render('header', array('css' => array(
                                  'plugins/bootstrap-chosen/chosen.css',
                                  'switch-buttons/switch-buttons.css',
                                  'font-awesome.min.css',
                                  )));

	#include Sidebar
	$registry->get('includer')->render('sidebar', array());


	#include small header
	$registry->get('includer')->renderWidget('smallHeader');
?>
    
    <div class="warper container-fluid">
          
            <div class="page-header"><h1>View Transactions <small>&nbsp;</small></h1></div>

            <div class="page-header">
              <h1>
                <small><a href="<?php echo $baseUri; ?>/guest/transactions">Transactions</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/guest/viewTransactions">View Transactions</a></small>
              </h1>
          </div>


            <div class="form-group">
  
                  <div class="col-sm-3">
                      <select class="form-control chosen-select form-control-circle" id="roomNo" name="roomNo" onchange="getGuestCheckInInfo(this.value);" >
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
            
            <div class="row">

              <div id="loader" style="width:120px; margin:40px auto; display:none"></div>
              


              <div class="col-md-6 topInfo" style="display:none">
                  <div class="panel panel-default">
                        <div class="panel-heading">GUEST INFO</div>
                        <input type="hidden" id="guestId" name="guestId" value="" />
                        <div class="panel-body ">
                        
                            <p><small>Guest Name :</small> <span id="gName">Imoh Abrahams</span></p>
                            
                            <hr>
                            
                            <p><small>Phone :</small> <span id="gPhone">09088779056</span></p>
                            
                        
                        </div>
                    </div>
                    
                    <div class="panel panel-default">
                        <div class="panel-heading">BILLS</div>
                        <div class="panel-body table-responsive" >
                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th style="width:25%">Date</th>
                                    <th style="width:55%">Description</th>
                                    <th style="width:20%">Amount</th>
                                  </tr>
                                </thead>
                                <tbody id="bills">

                                </tbody>
                              </table>
                            
                        </div>
                    </div>
                   
                </div>
                
                <div class="col-md-6 topInfo" style="display:none">
                  
                    <div class="panel panel-default">
                        <div class="panel-heading">CHECK IN DETAILS</div>
                        <div class="panel-body">
                        
                          <p><small>Check In Date :</small> <span id="cDate">12th July 2014</span></p>
                            
                            <hr>
                            
                            <p><small>Room :</small> <span id="rNo">CB</span> ( <span id="rType">Kelvic Deluxe</span> )</p>
                            
                           
                        
                        </div>
                    </div>
                    
                    <div class="panel panel-default">
                        <div class="panel-heading">PAYMENTS</div>
                        <div class="panel-body table-responsive" >
                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th style="width:25%">Date</th>
                                    <th style="width:55%">Description</th>
                                    <th style="width:20%">Amount</th>
                                  </tr>
                                </thead>
                                <tbody id="payments">

                                </tbody>
                          </table>
                        </div>
                    </div>
                 
                 </div>

            
            </div>
            
          
                              
            
        </div>
        <!-- Warper Ends Here (working area) -->
        
        
        <?php  
        	$registry->get('includer')->render('footer', array('js' => array(
                                  'plugins/nicescroll/jquery.nicescroll.min.js',
                                  'plugins/bootstrap-chosen/chosen.jquery.js',
                                  'app/custom.js',
                                  'application/ctrl.js',
                                  'application/transactions.js'
                                  ))); 

        	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>
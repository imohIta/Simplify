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


$occupiedRooms = Room::getOccupied();

//var_dump($session->read('occupiedRoomslist')); die;

#get room categories list
/*if(!$registry->get('session')->read('roomsCategoriesList')){
    $registry->get('session')->write('roomsCategoriesList', $registry->get('db')->getRoomCategories());
}*/


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
          
            <div class="page-header"><h1>Check Out Guest <small>&nbsp;</small></h1></div>
            <?php 
                   if($registry->get('session')->read('formMsg')){
            ?>
                <br />
                <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <strong>Success!</strong> <?php echo $registry->get('session')->read('formMsg');; ?>. 
              </div>

            <?php 
                $registry->get('session')->write('formMsg', NULL);
                   }
               ?>

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
                        <div class="panel-body ">
                        
                            <p><small>Guest Name :</small> <span id="gName">Imoh Abrahams</span></p>
                            
                            <hr>
                            
                            <p><small>Address :</small> <span id="gAddr">105 Ibb Way, Calabar</span></p>
                            
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
                            
                            <p><small>Room Type :</small> <span id="rType">Kelvic Deluxe</span></p>
                            
                            <hr>
                            
                            <p><small>Room No :</small> <span id="rNo">CB</span></p>
                        
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
            
            <br />
            <form method="post" action="<?php echo $baseUri; ?>/guest/checkOut">
              <input type="hidden" id="guestId" name="guestId" value="" />
              <input type="hidden" id="roomId" name="roomId" value="" />
              <div id="checkOutNotes" style="display:none"></div>

              <br />
                 
              <div style="float:left; display:none" class="topInfo">
                <input type="submit" name="submit" class="btn btn-success" value="Check Out" />
              </div>
           </form>
                              
            
        </div>
        <!-- Warper Ends Here (working area) -->
        
        
        <?php  
        	$registry->get('includer')->render('footer', array('js' => array(
                                  'plugins/nicescroll/jquery.nicescroll.min.js',
                                  'plugins/bootstrap-chosen/chosen.jquery.js',
                                  'app/custom.js',
                                  'application/ctrl.js',
                                  'application/checkOut.js'
                                  ))); 

        	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>
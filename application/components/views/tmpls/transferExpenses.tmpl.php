<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | reception  )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,7), true);


$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

/*if(!$session->read('occupiedRoomslist')){
   $session->write('occupiedRoomslist', $registry->get('db')->getOccupiedRooms());
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

  $occupiedRooms = Room::getOccupied();
  
?>
    
    <div class="warper container-fluid">
          
            <div class="page-header"><h1>Transfer Guest Expenses <small>&nbsp;</small></h1></div>

            <div class="page-header">
              <h1>
                <small><a href="<?php echo $baseUri; ?>/guest/manage">Manage Guest</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/guest/transferExpenses">Transfer Expenses</a></small>
              </h1>
          </div>

          <?php 
                   if($registry->get('session')->read('formMsg')){
                  echo $registry->get('session')->read('formMsg');
                $registry->get('session')->write('formMsg', NULL);
                   }
               ?>


            <div class="form-group">
  
                  <div class="col-sm-3">
                    Transfer Expenses of Room
                      <select class="form-control chosen-select form-control-circle" id="roomNo1" name="roomNo" onchange="getGuestInfoForTE(this.value, '1');" >
                        <option value="0"> Select Room</option>
                        <?php foreach ($occupiedRooms as $r) {
                          $room = new Room($r->roomId);
                        ?>
                        <option value="<?php echo $room->id; ?>" ><?php echo $room->no; ?></option>
                        <?php } ?>
                      </select>
                  </div>


                  <div class="col-sm-3" id="target" style="display:none">
                    To Room
                      <select class="form-control" style="width:150px" id="roomNo2" name="roomNo" onchange="getGuestInfoForTE(this.value, '2');" >
                        <option value="0">Select Room</option>
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
              


              <div class="col-md-6" id="topInfo1" style="display:none">
                  <div class="panel panel-default">
                        <div class="panel-heading">GUEST INFO</div>
                        
                        <div class="panel-body ">
                        
                            <p><small>Guest Name :</small> <span id="gName1"></span></p>
                            
                            <hr>
                            
                            <p><small>Room No :</small> <span id="rNo1"></span> ( <span id="rType1"></span> )</p>
                            <input type="hidden" id="gId1">
                            <input type="hidden" id="rId1">
                        
                        </div>
                    </div>

                    <div id="loader2" style="width:120px; margin:40px auto; display:none"></div>

                    
                
                </div>
                <!--  -->


                <div class="col-md-6" id="topInfo2" style="display:none">
                  
                    <div class="panel panel-default">
                        <div class="panel-heading">TARGET GUEST INFO</div>
                        <div class="panel-body">
                        
                            <p><small>Guest Name :</small> <span id="gName2"></span></p>
                            
                            <hr>
                            
                            <p><small>Room No :</small> <span id="rNo2"></span> ( <span id="rType2"></span> )</p>
                            <input type="hidden" id="gId2">
                            <input type="hidden" id="rId2">
                         
                        </div>
                    </div>
            
                    
                    <span id="billInfo"></span>

                 
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
                                  'application/manageGuest.js'
                                  ))); 

        	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>
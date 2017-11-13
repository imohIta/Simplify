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


# get occupied rooms
$occupiedRooms = Room::getOccupied();

#get room categories list
if(!$registry->get('session')->read('roomsCategoriesList')){
    $registry->get('session')->write('roomsCategoriesList', Room::getCategories());
}


#include header
$registry->get('includer')->render('header', array('css' => array(
                                'plugins/typeahead/typeahead.css',
                                'plugins/bootstrap-tagsinput/bootstrap-tagsinput.css',
                                'plugins/bootstrap-chosen/chosen.css',
                                'plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.css',
                                'switch-buttons/switch-buttons.css',
                                'font-awesome.min.css',
                                )));

	#include Sidebar
	$registry->get('includer')->render('sidebar', array());


	#include small header
	$registry->get('includer')->renderWidget('smallHeader');
?>
    	
	
    
    <!-- Page Body here...Editable region -->
        
        <div class="warper container-fluid">

        	
            <div class="page-header"><h1>Change Guest Room <small style="color:#FF404B">&nbsp;</small></h1></div>
            
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
                        
                          <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/guest/changeRoom">

                              

                              <div class="form-group">
                                    <label class="col-sm-3 control-label">Guest Current Room</label>
                                    <div class="col-sm-4">
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
                                
                            <input type="hidden" name="guestId" id="guestId" class="form-control form-control-circle" >
                            <input type="hidden" name="oldRoomId" id="oldRoomId" class="form-control form-control-circle" >
                            <input type="hidden" name="discount" id="discount">

                            <div id="loader" style"width:100%; text-align:center"></div>

                            <div id="guestInfo" style="display:none">
                            
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Guest Info</label>
                                <div class="col-sm-9">
                                  <div class="bg-primary padd-sm text-white">
                                    <table style="width:90%">
                                      <tr><td style="width:23%">Guest Name :</td>
                                          <td style="width:3%">&nbsp;</td>
                                          <td><span id="gName">Name Here</span></td>
                                      </tr>
                                      <tr><td style="width:20%">Check In Date :</td>
                                          <td style="width:2%">&nbsp;</td>
                                          <td><span id="cDate">Name Here</span></td>
                                      </tr>
                                      <tr><td style="width:20%">Room Type :</td>
                                          <td style="width:2%">&nbsp;</td>
                                          <td><span id="rType">Name Here</span></td>
                                      </tr>
                                      <tr><td style="width:20%">Room No :</td>
                                          <td style="width:2%">&nbsp;</td>
                                          <td><span id="rNo">Name Here</span></td>
                                      </tr>
                                    </table>
                                   
                                    </div>
                                </div>
                              </div>

                              <hr>

                              <div class="form-group">
                                    <label class="col-sm-3 control-label">New Room Type</label>
                                    <div class="col-sm-3">
                                      <!-- onChange="getRoomByType(this.value)" -->
                                      <select class="form-control" name="roomType" id="roomTypes" onChange="getRoomsByType(this.value)" style="width:160px" required>
                                          <option></option>
                                          <?php
                                          foreach ($registry->get('session')->read('roomsCategoriesList') as $roomType) {
                                           ?>
                                          <option value="<?php echo $roomType->id; ?>"><?php echo $roomType->type; ?></option>
                                          <?php } ?>
                                        </select>
                                    </div>
                              </div>

                              <span id="roomsHolder"></span>

                              <span id="reasonHolder" style="display:none">
                                <div class="form-group">
                                  <label for="inputEmail3" class="col-sm-3 control-label">Reason for Room Change</label>
                                  <div class="col-sm-9">
                                    <input type="text" name="reason" id="reason" class="form-control form-control-circle"  autocomplete="off" required>
                                  </div>
                                </div>
                              

                                <div class="form-group">
                                  <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" name="submit" class="btn btn-success">Change Room</button>
                                  </div>
                                </div>

                              </span>

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
                                  'globalize/globalize.min.js',
                                  'plugins/nicescroll/jquery.nicescroll.min.js',
                                  'plugins/typehead/typeahead.bundle.js',
                                  'plugins/typehead/typeahead.bundle-conf.js',
                                  'plugins/inputmask/jquery.inputmask.bundle.js',
                                  'plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js',
                                  'plugins/bootstrap-chosen/chosen.jquery.js',
                                  'plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.js',
                                  'app/custom.js',
                                  'application/ctrl.js',
                                  'application/changeRoom.js'
                                  )));

        	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>
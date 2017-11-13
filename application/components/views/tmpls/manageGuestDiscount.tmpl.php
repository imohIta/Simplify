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





	#include header
	$registry->get('includer')->render('header', array('css' => array(
                                  'plugins/bootstrap-chosen/chosen.css',
                                  'switch-buttons/switch-buttons.css',
                                  'font-awesome.min.css'
                                  )));

	#include Sidebar
	$registry->get('includer')->render('sidebar', array());


	#include small header
	$registry->get('includer')->renderWidget('smallHeader');
  
  $occupiedRooms = Room::getOccupied();
?>
    
    <div class="warper container-fluid">
          
            <div class="page-header"><h1>Manage Guest Discount <small>&nbsp;</small></h1>
              <h1>
                <small><a href="<?php echo $baseUri; ?>/guest/manage">Manage Guest</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/guest/manageDiscount">Discount</a></small>
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
              


              <div class="col-md-6" id="topInfo" style="display:none">
                  <div class="panel panel-default">
                        <div class="panel-heading">GUEST INFO</div>
                        
                        <div class="panel-body ">
                        
                            <p><small>Guest Name :</small> <span id="gName">Imoh Abrahams</span></p>
                            
                            <hr>
                            
                            <p><small>Room No :</small> <span id="rNo"></span> ( <span id="rType"></span> )</p>
                            
                            <hr />

                            <p><small>Old Discount :</small> <span id="oldDiscount"></span> % </p>
                        
                        </div>
                    </div>
                    
                    <div class="panel panel-default">
                        <div class="panel-heading">EDIT GUEST DISCOUNT</div>
                        <div class="panel-body table-responsive" >

                          <form method="post" action="<?php echo $baseUri; ?>/guest/manageDiscount">

                                <input type="hidden" name="guestId" id="guestId" value="" />
                                <input type="hidden" name="oldDiscount" id="od" value="" />
                                <input type="hidden" name="roomId" id="roomId" value="" />
                              
                                <div class="form-group">
                                        <label class="col-sm-3 control-label">New Discount</label>
                                        <div class="col-sm-3">
                                          <select name="newDiscount" id="discount" class="form-control" style="width:120px">
                                              <option value="0" selected>0 %</option>
                                              <?php
                                                  for ($i=1; $i <= 50 ; $i++) {
                                                      ?>
                                                      <option value="<?php echo $i; ?>"><?php echo $i; ?> %</option>
                                                  <?php } ?>
                                            </select>
                                        </div>
                                </div>

                                <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                  <button type="submit" name="submit" class="btn btn-success">Edit Discount</button>
                                </div>
                              </div>


                          </form>
                            
                        </div>
                    </div>


                   
                </div>
                <!--  -->
               

            
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
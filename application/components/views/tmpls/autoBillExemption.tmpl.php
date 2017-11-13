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

          <div class="page-header">
            <h1>Exempt Guest From AutoBill <small style="color:#FF404B">( <?php echo dateToString(today()); ?> )</small></h1>
            <h1>
                <small><a href="<?php echo $baseUri; ?>/guest/manage">Manage Guest</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/guest/exemptFromAutoBill">AutoBill Exemption</a></small>
              </h1>
            
          </div>
        	
            
            
             <div class="row">
            
                <div class="col-md-7">

                  <?php 
                          if($registry->get('session')->read('formMsg')){
                              echo $registry->get('session')->read('formMsg');
                              $registry->get('session')->write('formMsg', NULL);
                             }
                          ?>
                          
                    <div class="panel panel-default">
                        <div class="panel-heading">&nbsp;</div>
                        <div class="panel-body">

                         
                        
                            <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/guest/exemptFromAutoBill">

                            
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Date</label>
                                <div class="col-sm-3">
                                  <input type="text" name="date" class="form-control form-control-circle inputmask" data-inputmask="'alias': 'yyyy-mm-dd'" placeholder="yyyy-mm-dd" readonly value="<?php echo $today; ?>">
                                </div>
                              </div>

                              <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Select Guest</label>
                                    <div class="col-sm-5">
                                        <select class="form-control chosen-select form-control-circle"  name="roomId" required  >
                                          <option value="0"> Select Guest </option>
                                          <?php foreach ($occupiedRooms as $r) {
                                            $room = new Room($r->roomId);
                                            $guestInfo = Guest::getCheckInInfo($room->id);
                                            
                                          ?>
                                          <option value="<?php echo $room->id; ?>" ><?php echo $guestInfo->name . ' ( ' .  $room->no . ' ) ';  ?></option>
                                          <?php } ?>
                                        </select>
                                    </div>
                              </div>

                              <!-- <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Details</label>
                                <div class="col-sm-9">
                                  <input type="text" name="reason" class="form-control form-control-circle" autocomplete="off">
                                </div>
                              </div> -->

                              <br /><hr  /><br />

                              <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                  <button type="submit" name="submit" class="btn btn-danger btn-circle">Submit</button>
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
                                  'application/reception.js'
                                  )));

        	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>
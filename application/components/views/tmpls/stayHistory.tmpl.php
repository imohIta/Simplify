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
                                  'plugins/datatables/jquery.dataTables.css',
                                  'font-awesome.min.css',
                                  )));

	#include Sidebar
	$registry->get('includer')->render('sidebar', array());


	#include small header
	$registry->get('includer')->renderWidget('smallHeader');
?>
    
    <div class="warper container-fluid">
          
            <div class="page-header"><h1>View Guest Stay History <small>&nbsp;</small></h1></div>

            <div class="page-header">
              <h1>
                <small><a href="<?php echo $baseUri; ?>/previousGuest/">Manage Previous Guest</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/previousGuest/stayHistory">Stay History</a></small>
              </h1>
          </div>


            <!-- <div class="form-group"> -->
                  <div class="col-sm-6">
                      <div class="input-group">
                        <input type="text" name="phone" placeholder="Enter Guest Phone No." class="form-control form-control-circle" id="phone" autocomplete="off" >
                        <span class="input-group-btn">
                          <button class="btn btn-info" id="searchGuestBtn" type="button">Search</button>
                        </span>
                      </div>
                  </div>
              <!-- </div> -->

            <br style="clear:both" />
            
            <hr>
            
            <div class="row">

              <div id="loader" style="width:120px; margin:40px auto; display:none"></div>
              <span id="content"></span>

            </div>
            
          
                              
            
        </div>
        <!-- Warper Ends Here (working area) -->
        
        
        <?php  
        	$registry->get('includer')->render('footer', array('js' => array(
                                  'plugins/nicescroll/jquery.nicescroll.min.js',
                                  'plugins/datatables/jquery.dataTables.js',
                                  'plugins/datatables/DT_bootstrap.js',
                                  'plugins/datatables/jquery.dataTables-conf.js',
                                  'app/custom.js',
                                  'application/ctrl.js',
                                  'application/previousGuest.js'
                                  ))); 

        	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>
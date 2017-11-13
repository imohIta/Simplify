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

/***
  This page will share a widget ( showPreviousGuestCredits ) with the Guest Credits & Credit Payments page
  each of these Pages...diffrent presentations of the widget are required...although it is the exact same info
  so...i will set a session here that will be check for in the widget to determine wat presentation is required
*/
$session->write('fullScreen', null);

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
          
            <div class="page-header">
              <h1>Guest Credits & Credit Payments<small>&nbsp;</small></h1>
              <h1>
                <small><a href="<?php echo $baseUri; ?>/previousGuest/">Manage Previous Guest</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/previousGuest/viewCredits">Guest Credits</a></small>
              </h1>
          </div>


            <!-- <div class="form-group"> -->
                  <div class="col-sm-6">
                      <div class="input-group">
                        <input type="text" name="phone" placeholder="Enter Guest Phone No." class="form-control form-control-circle" id="phone" autocomplete="off" >
                        <span class="input-group-btn">
                          <button class="btn btn-info btn-circle" id="searchGuestBtn2" type="button">Search</button>
                        </span>
                      </div>
                  </div>

                  OR

                  <a href="<?php echo $baseUri; ?>/previousGuest/viewAllCredits" style="margin-left:15px"><button class="btn btn-success btn-circle" style="margin-right:100px"  type="button">View All Guest Credits</button></a>


                  
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
<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));


#check if user has access to this page ( super admin | Mgt Staff | reception )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5,6), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');


if($session->read('logPrivilege' . $thisUser->id . $thisUser->get('activeAcct'))){ 
  $session->write('logPrivilege' . $thisUser->id . $thisUser->get('activeAcct'), null);
}



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
    
    <div class="warper container-fluid" id="guestList">

      <div class="page-header">
        <h1>Financial Transactions Options <small style="color:#FF404B">&nbsp;</small></h1>
      </div>
    	
      <hr />


        
         <div class="row">
        
            <div class="col-md-10">

              <ul class="list-unstyled list-inline showcase-btn">
              
                <li><a href="<?php echo $baseUri; ?>/transaction/setLogPrivilege/6" class="btn btn-warning btn-circle btn-lg">Cashier</a></li>

                <li><a href="<?php echo $baseUri; ?>/transaction/setLogPrivilege/7" class="btn btn-info btn-circle btn-lg">Reception</a> </li>
              
                <li><a href="<?php echo $baseUri; ?>/transaction/setLogPrivilege/8" class="btn btn-danger btn-circle btn-lg">Pool Bar</a></li>

              </ul> 

              <ul class="list-unstyled list-inline showcase-btn">
                
                <li><a href="<?php echo $baseUri; ?>/transaction/setLogPrivilege/9" class="btn btn-success btn-circle btn-lg">Main Bar</a></li>

                <li><a href="<?php echo $baseUri; ?>/transaction/setLogPrivilege/10" class="btn btn-orange btn-circle btn-lg">Resturant</a></li>

                <li><a href="<?php echo $baseUri; ?>/transaction/setLogPrivilege/11" class="btn btn-gry btn-circle btn-lg">Resturant Drinks</a></li>

              </ul>

               
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
                              'plugins/bootstrap-chosen/chosen.jquery.js',
                              'moment/moment.js',
                              'app/custom.js',
                              'application/ctrl.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
    ?>
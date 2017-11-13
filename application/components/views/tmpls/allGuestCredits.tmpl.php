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
  $session->write('fullScreen', 'yes');


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
              <h1>All Guest Credits<small>&nbsp;</small></h1>
              <h1>
                <small><a href="<?php echo $baseUri; ?>/previousGuest/">Manage Previous Guest</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/previousGuest/viewCredits">Guest Credits</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/previousGuest/viewAllCredits">All Guest Credits</a></small>
              </h1>
          </div>


            
            
          <div class="row">
        
            <div class="col-md-8">

              <?php 
                 if($registry->get('session')->read('formMsg')){
                  echo $registry->get('session')->read('formMsg');
                  $registry->get('session')->write('formMsg', NULL);
                 }
              ?>

                <div class="panel panel-default">
                    <div class="panel-heading">&nbsp;</div>
                    <div class="panel-body">

                    
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="basic-datatable">
                            <thead>
                                <tr>
                                   <th>SN</th>
                                   <th>Guest Name</th>
                                   <th>Guest Phone</th>
                                    <th>Total Credit</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $count = 1;
                              foreach (Guest::fetchDistinctDebtors() as $row) {

                                $guest = new Guest($row->guestId);
                                
                                $credits = Guest::fetchTotalPreviousGuestCredits($guest->phone);
                                $payments = Guest::fetchTotalPreviousGuestPayments($guest->phone);
                                
                                
                                $bal = $credits - $payments;
                                
                                if($bal > 0){

                              ?>
                                 <tr class="<?php echo $class; ?> gradeX">
                                    <td><?php echo $count; ?>.</td>
                                    <td><?php echo $guest->name; ?></td>
                                    <td><?php echo $guest->phone; ?></td>
                                    <td><?php echo number_format($bal); ?></td>
                                    
                                    <td>
                                      <div class="btn-group">
                                            <button class="btn btn-warning btn-circle" type="button" onclick="fetchCreditDetails({'guestName' : '<?php echo $guest->name; ?>', 'guestPhone' : '<?php echo $guest->phone; ?>'})" data-toggle="modal" data-target="#details">View Details</button>

                                          </div> 
                                    </td>
                                
                                    
                                </tr> 
                                <?php $count++; } } ?>
                              </tbody>
                            </table>

                    
                    </div>
                </div>
            </div>

          

        </div>


        <!-- Issue Req -->
        <div class="modal fade col-md-12" id="details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Credit & Payment Details for <span id="nameHolder"></span></h4>
                </div>
                <div class="modal-body" id="contentHolder">

                 
           </div>
           <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
           </div>
        </div>
      </div>
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
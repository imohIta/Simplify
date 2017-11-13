<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | Cashier )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,4,5,6), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');



$log = Room::getOccupied();

//$log = Payment::fetchDistinctGuestPayments(today());
//var_dump($log); die;


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
        <h1>Guest Chart <small style="color:#FF404B">&nbsp;</small></h1>
      </div>
    	
        
        
         <div class="row">
        
            <div class="col-md-10">

                <div class="panel panel-default">
                    <div class="panel-heading">&nbsp;</div>
                    <div class="panel-body">

                    
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="basic-datatable">
                            <thead>
                                <tr>
                                   <th>SN</th>
                                    <th>Guest Name</th>
                                    <th>Room No.</th>
                                    <th>Room Rate</th>
                                    <th>Discount Given</th>
                                    <th>Actual AMount</th>
                                    <th>Reciept No</th>
                                    <th>Total Today's Payment</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              if(count($log) > 0){
                              $count = 1;
                              $total = 0;
                              foreach ($log as $row) {
                                
                                # code...
                                $guest = new Guest($row->guestId);
                                
                                $room = new Room($row->roomId);
                                $class = ( $count % 2 == 0 ) ? 'even' : 'odd';
                                $pay = $guest->fetchTotalPaymentsForDate(today());
                                $total += $pay;

                              ?>
                                 <tr class="<?php echo $class; ?> gradeX">
                                    <td><?php echo $count; ?>.</td>
                                    <td><?php echo $guest->name; ?></td>
                                    <td><?php echo $room->no; ?></td>
                                    <td><?php echo number_format($room->price); ?></td>
                                    <td><?php echo $row->discount; ?> %</td>
                                    <td><?php echo number_format($room->price - (($row->discount / 100) * $room->price ) ); ?></td>
                                    <td><?php echo ''; ?></td>
                                    <td><?php echo number_format($pay); ?></td>
                                    
                                                                    
                                </tr> 
                                <?php $count++; } ?>
                                <tr>
                                  <td colspan="7">Total</td>
                                  <td><?php echo number_format($total); ?></td>
                                </tr>
                                <?php } ?>
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
                              'plugins/typehead/typeahead.bundle.js',
                              'plugins/typehead/typeahead.bundle-conf.js',
                              'plugins/inputmask/jquery.inputmask.bundle.js',
                              'plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js',
                              'plugins/bootstrap-chosen/chosen.jquery.js',
                              'moment/moment.js',
                              'plugins/datatables/jquery.dataTables.js',
                              'plugins/datatables/DT_bootstrap.js',
                              'plugins/datatables/jquery.dataTables-conf.js',
                              'app/custom.js',
                              'application/ctrl.js',
                              'application/reservation.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
    ?>
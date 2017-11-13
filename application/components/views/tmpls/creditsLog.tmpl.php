<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | Mgt Staff | reception )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,8,9,10,11), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');


#fetch all unposted Sales 
$credits = $registry->get('db')->fetchAllCredits();

//echo 'check'; die;
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
    
    <div class="warper container-fluid" >

      <div class="page-header">
        <h1>Credits Log<small style="color:#FF404B">&nbsp;</small></h1>
      </div>
    	
        
        
         <div class="row">
        
            <div class="col-md-11">

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
                                    <th>Date</th>
                                    <th>Debtor's Name</th>
                                    <th>Debtor Type</th>
                                    <th>Trans ID</th>
                                    <th>Purchase Details</th>
                                    <th>Credit Amount</th>
                                    <th>Total Amt Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $count = 1;
                              foreach ($credits as $row) {
                                
                                $class = ( $count % 2 == 0 ) ? 'even' : 'odd';

                                # fetch all Credit payments
                                $total = $registry->get('db')->fetchTotalCreditPaymentsById($row->transId);

                                if($row->amt > $total){

                                if($row->debtorType == 1 ){
                                   $debtorType = 'Staff';
                                }elseif($row->debtorType == 2){
                                  $debtorType = 'Out Guest';
                                }elseif ($row->debtorType == 3) {
                                  $debtorType = 'In Guest';
                                }

                              ?>
                                 <tr class="<?php echo $class; ?> gradeX">
                                    <td><?php echo $count; ?>.</td>
                                    <td><?php echo dateToString($row->date); ?></td>
                                    <td><?php echo ucwords($row->debtorName); ?></td>
                                    <td><?php echo $debtorType; ?></td>
                                    <td><?php echo $row->transId; ?></td>
                                    <td>
                                      <?php
                                        foreach (json_decode($row->details) as $key) {
                                          # code...
                                          $item = ($key->objectType == 1) ? new Menu($key->objectId) : new Item($key->objectId);
                                          echo '<p> ( ' . $key->qty . ' ) ' . $item->name . ' | ' . number_format($key->price) . '</p>';

                                        }
                                        ?>

                                    </td>
                                    <td><?php echo number_format($row->amt); ?></td>
                                    <td><?php echo number_format($total); ?></td>
                                   
                                </tr> 
                                <?php $count++; } } ?>
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
                              'application/sales.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
    ?>
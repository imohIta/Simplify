<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | cashier | accountant )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,5,6), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');


#fetch all unposted Sales 
$deposits = $registry->get('db')->fetchBankDeposits();

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
        <h1>Bank Deposits Log<small style="color:#FF404B">&nbsp;</small></h1>
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
                                    <th>Payment Date</th>
                                    <th>Trans ID</th>
                                    <th>Bank</th>
                                    <th>Amount</th>
                                    <th>Depositor</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $count = 1;
                              foreach ($deposits as $row) {
                                
                                $class = ( $count % 2 == 0 ) ? 'even' : 'odd';
                                $staff = new User($row->staffId);
                              ?>
                                 <tr class="<?php echo $class; ?> gradeX">
                                    <td><?php echo $count; ?>.</td>
                                    <td><?php echo dateToString($row->payDate); ?></td>
                                    <td><?php echo $row->transId; ?></td>
                                    <td><?php echo $row->bank; ?></td>
                                    <td><?php echo number_format($row->amt); ?></td>
                                   <td><?php echo $thisUser->id == $staff->id ? 'You' : ucwords($staff->name); ?></td>
                                </tr> 
                                <?php $count++; } ?>
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
                              'application/ctrl.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
    ?>
<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | cashier | accountant )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,5,6), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');


#fetch all unposted Sales 
$data = $registry->get('db')->fetchAllDeptCredits();

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
        <h1>Departments Credits & Payments<small style="color:#FF404B"> ( As-At )</small></h1>
        <h1>
                <small><a href="<?php echo $baseUri; ?>/cashier/asAtOptions">As-At Options</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/cashier/asAtPayments">Credits & Payments</a></small>
              </h1>

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
                                    <th>Date</th>
                                    <th>Trans ID</th>
                                    <th>Credit</th>
                                    <th>Total Payment</th>
                                    <th>Department</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $count = 1;
                              foreach ($data as $row) {
                                # fetch all Payments for this credit using the transaction ID;

                                $class = ( $count % 2 == 0 ) ? 'even' : 'odd';
                                $dept = User::getRole($row->privilege);

                                $totalPaid = 0;
                                foreach ($registry->get('db')->fetchDeptCreditPaymentsDetails($row->transId, $row->date, $row->privilege) as $key) {
                                  # code...
                                  $totalPaid += $key->amt;
                                }
                                
                              
                              ?>
                                 <tr class="<?php echo $class; ?> gradeX">
                                    <td><?php echo $count; ?>.</td>
                                    <td><?php echo dateToString($row->date); ?></td>
                                    <td><?php echo $row->transId; ?></td>
                                    <td><?php echo number_format($row->amt); ?></td>
                                    <td><a href="javacript:void(0);" data-toggle="modal" data-target="#viewPayments" onclick="fetchPosCreditPayments({'transId' : '<?php echo $row->transId; ?>', 'date' : '<?php echo $row->date; ?>', 'priv' : '<?php echo $row->privilege; ?>' })"><?php echo number_format($totalPaid); ?></a></td>
                                   <td><?php echo $dept; ?></td>
                                </tr> 
                            <?php $count++; } ?>
                            
                              </tbody>
                            </table>

                    
                    </div>
                </div>
            </div>
          

        </div>




    <!-- Payments Normal Size -->
    <div class="modal fade" id="viewPayments" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel">POS Unit Credit Payments</h4>
          </div>
          <div class="modal-body">
            
            <div class="panel panel-default">
                        <div class="panel-heading">&nbsp;</div>
                        <div class="panel-body table-responsive" id="content">
                        
                          
                        </div>
                    </div>

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
                              'application/cashier.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
    ?>
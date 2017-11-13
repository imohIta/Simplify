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
$unpostedSales = $registry->get('db')->fetchUnpostedSales();


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
        <h1>Unposted Sales<small style="color:#FF404B">&nbsp;</small></h1>
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
                                    <th>Date</th>
                                    <th>TransId</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $count = 1;
                              foreach ($unpostedSales as $row) {
                                
                                $class = ( $count % 2 == 0 ) ? 'even' : 'odd';

                              ?>
                                 <tr class="<?php echo $class; ?> gradeX">
                                    <td><?php echo $count; ?>.</td>
                                    <td><?php echo dateToString($row->date); ?></td>
                                    <td><?php echo $row->transId; ?></td>
                                    <td>
                                      <button class="btn btn-warning btn-circle saleOptions" onclick="viewUnpostedSale('<?php echo $row->transId; ?>')" data-toggle="modal" data-target="#viewOptions">View</button>

                                      <button class="btn btn-danger btn-circle saleOptions" onclick="cancelUnpostedSale('<?php echo $row->transId; ?>')" data-toggle="modal" data-target="#cancelOptions" style="margin-left:10px">Delete</button>
                                    </td>
                                   
                                </tr> 
                                <?php $count++; } ?>
                              </tbody>
                            </table>

                    
                    </div>
                </div>
            </div>




            <div class="modal fade" id="viewOptions" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Incomplete Sale</h4>
              </div>
              <div class="modal-body">
                

                    <div class="panel panel-default">
                        <div class="panel-heading">
                        <p>Trans ID: <span id="trans"></span></p>
                        <p>Guest Type: <span id="gType"></span></p>
                        <p>Room No : <span id="rNo"></span></p>
                        </div>
                        <div class="panel-body table-responsive">
                        
                          <table class="table table-bordered" >
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Description</th>
                                    <th>Qty</th>
                                    <th>Rate</th>
                                    <th>Amount</th>
                                    <th>&nbsp;</th>
                                  </tr>
                                </thead>
                                <tbody id="myTable">
                                  
                                  
                                </tbody>
                              </table>

                              <br /><br />

                              <button class="btn btn-small btn-circle btn-info pull-right" id="editSale">Edit</button>
                            <button class="btn btn-small btn-circle btn-success pull-right" id="editDone"
                                    style="display:none">Done</button>

                              <h4 class="text-success">Submit Order</h4>

                              <hr style="clear:both">

                              <div id="optionsHolder"></div>
                        
                        </div>
                    </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>



        <!-- Cancel -->
        <div class="modal fade" id="cancelOptions" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Delete Unposted Sale</h4>
              </div>
              <div class="modal-body">
              
              <p class="text-muted">Are You Sure you want to Delete this Sale<p>
              <form method="post" action="<?php echo $baseUri; ?>/sales/deleteUnposted">
                <input type="hidden" id="transId3" name="transId" />
                <button type="button" class="btn btn-danger btn-circle" data-dismiss="modal">Not Really</button>
                <button type="submit" name="submit" class="btn btn-success btn-circle" style="margin-left:10px">Yea, Sure</button>
              </form>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
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
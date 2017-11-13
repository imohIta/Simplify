<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | Mgt Staff | reception )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,14), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');


# fetch stock purchases
$log = $registry->get('db')->fetchUnapprovedStockPurchases(10);

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
        <h1>Unapproved Stock Purchases <small style="color:#FF404B">&nbsp;</small></h1>
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
                                    <th>Status</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $count = 1;
                              foreach ($log as $row) {
                               $status = ($row->approved == 0) ? 'Unapproved' : 'Approved';

                              ?>
                                 <tr class="<?php echo $class; ?> gradeX">
                                    <td><?php echo $count; ?>.</td>
                                    <td><?php echo dateToString($row->date); ?></td>
                                    <td><?php echo $status; ?></td>
                                    <td>
                                      <button class="btn btn-circle btn-warning" onclick="viewStkDetails('<?php echo $row->id; ?>')" data-toggle="modal" data-target="#viewOption">View</button>

                                     <!--  <button class="btn btn-circle btn-success" data-toggle="modal" data-target="#approve" onclick="populate('<?php echo $row->id; ?>', 'stkId1')" style="margin-left:10px">Approve</button>

                                      <button class="btn btn-circle btn-danger" data-toggle="modal" data-target="#reject" onclick="populate('<?php echo $row->id; ?>', 'stkId2')" style="margin-left:10px">Reject</button> -->

                                    </td>                                
                                </tr> 
                                <?php $count++; } ?>
                              </tbody>
                            </table>

                    
                    </div>
                </div>
            </div>


            <!-- View Options -->
          <div class="modal fade" id="viewOption" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                      <h4 class="modal-title" id="myModalLabel">Stock Purchases Details</h4>
                    </div>
                    <div class="modal-body">
                    <div  id="content"></div>
                    <div>
                        <p class="text-muted">What Do you want to do ?</p>
                        <hr />
                        <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/stock/approvePurchase">

                        <input type="hidden" name="id" id="stkId" />
                        
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                              <button type="submit" name="reject" class="btn btn-danger btn-circle" onclick="return confirm('Are you sure yo want to reject this stock purchase');" >Reject</button>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              <button type="submit" name="approve" class="btn btn-success btn-circle" onclick="return confirm('Are you sure yo want to approve this stock purchase')">Approve</button>
                            </div>
                          </div>
                     </form>

                    </div>
                      
                   </div>
                 <div class="modal-footer">
                  <button type="button" class="btn btn-default btn-circle" data-dismiss="modal">Close</button>
                 </div>
            </div>
          </div>
        </div>
        <!-- view Form Ends -->


        <!-- Approve Form -->
       <!--  <div class="modal fade" id="approve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Approve Stock Purchase</h4>
                </div>
                <div class="modal-body">

                  <p> Are You Sure you want to Approve this Stock Purchase </p>

                  <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/stock/approvePurchase">

                        <input type="hidden" name="id" id="stkId1" />
                        
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                              <button type="button"  class="btn btn-danger btn-circle" data-dismiss="modal">Not Really</button>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              <button type="submit" name="approve" class="btn btn-success btn-circle">Yea, Sure</button>
                            </div>
                          </div>
                     </form>
           </div>
           <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
           </div>
        </div>
      </div>
    </div> -->
    <!-- approve Form Ends -->


    <!-- reject Form -->
        <!-- <div class="modal fade" id="reject" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Reject Stock Purchase</h4>
                </div>
                <div class="modal-body">

                  <p> Are You Sure you want to Reject this Stock Purchase </p>

                  <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/stock/approvePurchase">

                        <input type="hidden" name="id" id="stkId2" />
                        
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                              <button type="button"  class="btn btn-danger btn-circle" data-dismiss="modal">Not Really</button>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              <button type="submit" name="reject" class="btn btn-success btn-circle">Yea, Sure</button>
                            </div>
                          </div>
                     </form>
           </div>
           <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
           </div>
        </div>
      </div>
    </div> -->
    <!-- approve Form Ends -->


          

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
                              'application/stock.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
    ?>
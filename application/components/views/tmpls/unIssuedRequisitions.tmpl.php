<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | Mgt Staff | reception )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,13), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');


# fetch reservations for this src
$requisitions = $registry->get('db')->fetchUnIssuedRequisitions();

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
    
    <div class="warper container-fluid" id="guestList">

      <div class="page-header">
        <h1>UnIssued Requisitions</h1>
      </div>
    	
        
        
         <div class="row">
        
            <div class="col-md-10">

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
                                   <th>Time</th>
                                    <th>Item Name</th>
                                    <th>Qty</th>
                                    <th>By</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $count = 1;
                              foreach ($requisitions as $row) {
                                
                                # code...
                                $item = new PosItem(new Item($row->itemId));
                                
                                $class = ( $count % 2 == 0 ) ? 'even' : 'odd';
                                $user = new Staff($row->staffId);
                                $dept = User::getRole($row->privilege);

                              ?>
                                 <tr class="<?php echo $class; ?> gradeX">
                                    <td><?php echo $count; ?>.</td>
                                    <td><?php echo dateToString($row->date); ?></td>
                                    <td><?php echo timeToString($row->time); ?></td>
                                    <td><?php echo $item->name; ?></td>
                                    <td><?php echo $row->qty; ?></td>
                                    <td><?php echo $user->name . ' ( ' . $dept . ' )'; ?></td>
                                   
                                    <td>
                                      <div class="btn-group">
                                            <button class="btn btn-warning " type="button">Actions</button>
                                            <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
                                              <span class="caret"></span>
                                              <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul role="menu" class="dropdown-menu">
                                              <li><a href="javascript:void(0)"; onclick="reqAction({'id' :  '<?php echo $row->id; ?>', 'itemId' :  '<?php echo $item->id; ?>', 'qty' : '<?php echo $row->qty; ?>', 'tbl' : '<?php echo User::getTblByPrivilege($row->privilege); ?>', 'staffId' : '<?php echo $user->id; ?>','dept' : '<?php echo $dept; ?>', 'div' : 'issue' })"  data-toggle="modal" data-target="#issue">Issue</a></li>
                                          
                                              <li><a href="javascript:void(0)"; onclick="reqAction({'id' :  '<?php echo $row->id; ?>', 'div' : 'cancel' })" data-toggle="modal" data-target="#cancel">Cancel</a></li>
                                            </ul>
                                          </div> 
                                    </td>
                                
                                    
                                </tr> 
                                <?php $count++; } ?>
                              </tbody>
                            </table>

                    
                    </div>
                </div>
            </div>

          

        </div>


        <!-- Issue Req -->
        <div class="modal fade" id="issue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Issue Requisition</h4>
                </div>
                <div class="modal-body">

                  <p> Are You Sure you want to Issue this Requisition ? </p>

                  <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/requisition/unissued">
                        <input type="hidden" name="id" id="issue_id" />
                        <input type="hidden" name="itemId" id="issue_itemId" />
                        <input type="hidden" name="tbl" id="issue_tbl" value="" />
                        <input type="hidden" name="staffId" id="issue_staffId" value="" />
                        <input type="hidden" name="qty" id="issue_qty" value="" />
                        <input type="hidden" name="role" id="issue_dept" value="" />
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                              <button type="button"  class="btn btn-danger btn-circle" data-dismiss="modal">Not Really</button>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              <button type="submit" name="issue" class="btn btn-success btn-circle">Yea, Sure</button>
                            </div>
                          </div>
                     </form>
           </div>
           <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
           </div>
        </div>
      </div>
    </div>


    <!-- Cancel Req -->
    <div class="modal fade" id="cancel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Cancel Requisition</h4>
                </div>
                <div class="modal-body">

                  <p> Are You Sure you want to Cancel this Requisition ? </p>

                  <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/requisition/cancel">

                        <input type="hidden" name="id" id="cancel_id" />
                       
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                              <button type="button"  class="btn btn-danger btn-circle" data-dismiss="modal">Not Really</button>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              <button type="submit" name="cancel" class="btn btn-success btn-circle">Yea, Sure</button>
                            </div>
                          </div>
                     </form>
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
                              'application/requisition.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
    ?>
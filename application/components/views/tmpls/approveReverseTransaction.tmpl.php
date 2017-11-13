<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | manager | auditor | dutyManager )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');
 

# fetch transaction reversal applications for this src
$reversals = Transaction::fetchReversals(50);


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
    
    <div class="warper container-fluid">

      <div class="page-header">
        <h1>Transaction Reversals <small style="color:#FF404B"></small></h1>
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
                                    <th>Transation Date</th>
                                    <th>Application Date</th>
                                    <th>Reversal Info</th>
                                    <th>Status</th>
                                    <th>Operation</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $count = 1;
                              foreach ($reversals as $row) {
                                
                               
                                $class = ( $count % 2 == 0 ) ? 'even' : 'odd';
                                $transInfo = json_decode($row->transInfo);
                                $transtaff = new User(new Staff($transInfo->staffId));

                                $appliInfo = json_decode($row->reversalInfo);
                                switch ($row->status) {
                                  case 0:
                                    # code...
                                    $status = '<p class="text-warning">Pending</p>';
                                    
                                    break;

                                  case 1:
                                    # code...
                                    $status = '<p class="text-success">Reversed</p>';
                                    
                                    break;

                                  case 2:
                                    # code...
                                    $status = '<p class="text-danger">Rejected</p>';
                                    
                                    break;
                                  
                                }


                              ?>
                                 <tr class="<?php echo $class; ?> gradeX">
                                    <td><?php echo $count; ?></td>
                                    <td><?php echo dateToString($transInfo->transDate); ?></td>
                                    <td>
                                        <?php echo dateToString($appliInfo->reversalAppliDate); ?>
                                    </td>
                                    <td>
                                        <table>
                                            <tr>
                                              <td align="right"><small>Applicant : </small></td>
                                              <td><span style="margin-left:10px"><?php echo $transtaff->name . ' ( ' . User::getRole($transInfo->privilege). ' )'; ?></span></td>
                                            </tr>
                                            <tr>
                                              <td align="right"><small>Reason for Application : </small></td>
                                              <td><span style="margin-left:10px"><?php echo $appliInfo->reversalAppliReason; ?></span></td>
                                            </tr>
                                          
                                        </table>
                                    </td>
                                    <td><?php echo $status; ?></td>
                                    <td>
                                    <?php 
                                        switch ($row->status) {
                                            case 0: # pending
                                            ?>

                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default btn-sm btn-circle" data-toggle="dropdown" type="button">Options &nbsp;
                                                <span class="caret"></span>
                                                <span class="sr-only"></span>
                                                </button>
                                                <ul role="menu" class="dropdown-menu">
                                                <li><a href="javascript:void(0)"; onclick="viewTransDetails({id : '<?php echo $row->id; ?>' })"  data-toggle="modal" data-target="#viewDetails">View Details</a></li>

                                                <li><a href="javascript:void(0)"; onclick="operateTransReversal({id : '<?php echo $row->id; ?>', transId : '<?php echo $row->transId; ?>', div : 'approve'})" data-toggle="modal" data-target="#approveOptions">Approve</a></li>
                                                  
                                                <li><a href="javascript:void(0)"; onclick="operateTransReversal({id : '<?php echo $row->id; ?>', transId : '<?php echo $row->transId; ?>', div : 'decline'})" data-toggle="modal" data-target="#declineOptions">Decline</a></li>
                                                </ul>

                                                </button>
                                            </div>

                                        <?php

                                            break;

                                            case 1: # reversed
                                            ?>
                                            <div class="btn-group">
                                              <button type="button" class="btn btn-default btn-sm btn-circle" data-toggle="dropdown" type="button"> Options &nbsp;
                                                  <span class="caret"></span>
                                                  <span class="sr-only">Options</span>
                                                  </button>
                                                  <ul role="menu" class="dropdown-menu">
                                                  
                                                  <li><a href="javascript:void(0)"; onclick="viewTransDetails({id : '<?php echo $row->id; ?>' })"  data-toggle="modal" data-target="#viewDetails">View Details</a></li>

                                                  <li><a href="javascript:void(0)"; onclick='viewResersalInfo(<?php echo $row->reversalInfo; ?>)'  data-toggle="modal" data-target="#reversalInfo">View Reversal Info</a></li>

                                                  </ul>

                                              </button>
                                            </div>

                                            <?php
                                            break;

                                            case 2: # declined 
                                            //echo $row->reversalInfo;
                                            ?>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default btn-sm btn-circle" data-toggle="dropdown" type="button">Options &nbsp;
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Options</span>
                                                    </button>
                                                    <ul role="menu" class="dropdown-menu">
                                                    
                                                    <li><a href="javascript:void(0)"; onclick="viewTransDetails({id : '<?php echo $row->id; ?>', transId : '<?php echo $row->transId; ?>' })"  data-toggle="modal" data-target="#viewDetails">View Details</a></li>

                                                    <li><a href="javascript:void(0)"; onclick='viewResersalInfo(<?php echo $row->reversalInfo; ?>)'  data-toggle="modal" data-target="#reversalInfo">View Reversal Info</a></li>

                                                    </ul>

                                                </button>
                                            </div>
                                            <?php
                                            break;
                                          }
                                     ?>
                                     </td>
                                                                    
                                </tr> 
                                <?php $count++; } ?>
                              </tbody>
                            </table>

                    
                    </div>
                </div>
            </div>
            

      <!-- viewDetails Form -->
      <div class="modal fade" id="viewDetails" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Transaction Details</h4>
                </div>
                <div id="transContent" class="modal-body">

                  
           </div>
           <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
           </div>
        </div>
      </div>
    </div>
    <!-- view details Ends -->
                      
                     

    <!-- approveOptions Form -->
      <div class="modal fade" id="approveOptions" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Approve Transaction Reversal</h4>
                </div>
                <div class="modal-body">

                  <p> Are You Sure you want to approve this Transaction Reversal ? </p>

                  <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/transaction/reversals">

                        <input type="hidden" name="id" id="approveId" />
                        <input type="hidden" name="transId" id="approveTransId" />
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
    </div>
    <!-- approve options Ends -->

    <!-- declineOptions Form -->
      <div class="modal fade" id="declineOptions" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Decline Transaction Reversal</h4>
                </div>
                <div class="modal-body">

                  <p> Are You Sure you want to Reject this Transaction Reversal ? </p>

                  <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/transaction/reversals">

                        <input type="hidden" name="id" id="declineId" />
                        <input type="hidden" name="transId" id="declineTransId" />
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                              <button type="button"  class="btn btn-danger btn-circle" data-dismiss="modal">Not Really</button>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              <button type="submit" name="decline" class="btn btn-success btn-circle">Yea, Sure</button>
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
    <!-- approve options Ends -->


     <!-- reversal Info -->
      <div class="modal fade" id="reversalInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Reversal Info</h4>
                </div>
                <div class="modal-body">

                <table class="table" id="reversalInfoContent" style="width:70%; align:right">
                  

                </table>
                  
              </div>
           <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
           </div>
        </div>
      </div>
    </div>
    <!-- reversal Info Ends -->


    


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
                              'application/transactions.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
    ?>
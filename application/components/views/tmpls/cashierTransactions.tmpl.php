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

$date = $session->read('transDate') ? $session->read('transDate') : today();

if($session->read('transDate' . $thisUser->get('activeAcct'))){
  $session->write('transDate'. $thisUser->get('activeAcct'), null);
}

$account = $session->read('logPrivilege' . $thisUser->id . $thisUser->get('activeAcct'))
            ? $session->read('logPrivilege' . $thisUser->id . $thisUser->get('activeAcct'))
            : $thisUser->get('activeAcct');

# fetch transactions for this User
$transactions = Transaction::fetchUserTransactions($thisUser->id, $account, $date);

 

$cashcollections = array();
$creditCollections = array();


# split transactions into parts
foreach ($transactions as $row) {
  # code...
  $trans = new Transaction($row->id);
  switch ($trans->type) {
    case 14:
      # guest Payment | Reservation payment
      $cashcollections[] = $row;
      break;

    case 16:
      # bills
      $creditCollections[] = $row;
      break;
    
  }
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
        <h1>Financial Transactions <small style="color:#FF404B">( <?php echo User::getRole($account); ?> )</small></h1>

        <?php 
        if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5,6))){
        ?>
        <h1>
                <small><a href="<?php echo $baseUri; ?>/transaction/mgtOptions">Transaction Options</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/transaction/log">Financial Transactions</a></small>
          </h1>
       <?php } ?>

      </div>
    	
      <hr />


        
         <div class="row">
        
            <div class="col-md-10">

              <form method="post" action="<?php echo $baseUri; ?>/transaction/log"/>
              <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">&nbsp;</label>
                <div class="col-sm-6">
                    <div class="input-group">
                      <input type="text" placeholder="yyyy-mm-dd" class="form-control form-control-circle inputmask" data-inputmask="'alias': 'yyyy-mm-dd'" name="date" value="<?php echo $date; ?>" id="sDate" />
                      <span class="input-group-btn">
                        <button class="btn btn-warning btn-circle" id="searchTransBtn" type="submit" name="search" >Search By Date</button>
                      </span>
                    </div>
                </div>
             </div>
           </form>

             <br />
             <hr class="dotted">

               <ul role="tablist" class="nav nav-tabs" id="myTab">
                      <li class="active"><a data-toggle="tab" role="tab" href="#all">All</a></li>
                      <li><a data-toggle="tab" role="tab" href="#cash">Dept. Cash Returns</a></li>
                      <li><a data-toggle="tab" role="tab" href="#credit">Dept. Credit Payments</a></li>
                    </ul>
                    <div class="tab-content" id="myTabContent">


                       <div id="all" class="tab-pane tabs-up fade in active panel panel-default">
                        
                        <div class="panel-body table-responsive">
                          
                          <span style="float:right; margin-top:25px; margin-right:10px">ALL TRANSACTIONS</span>
                          
                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                          <?php if(count($transactions) > 0){ ?>
                        
                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Time</th>
                                    <th>Trans ID</th>
                                    <th>Type</th>
                                    
                                    <th>Amount</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php 

                                  $counter = 1;
                                  $total = 0;
                                  
                                  foreach ($transactions as $t) {
                                    $trans = new Transaction($t->id);
                                    
                                    $details = json_decode($trans->details, true);
                                    $total += $details['amt'];
                                    
                                    
                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo timeToString($trans->time); ?></td>
                                    <td><?php echo $trans->id; ?></td>
                                    <td><?php echo $trans->desc; ?></td>
                                    
                                    <td><?php echo number_format($details['amt']); ?></td>
                                  </tr>
                                 <?php $counter++; } ?>
                                  
                                </tbody>
                          </table>

                          <hr class="dotted">

                          <h4 class="text-primary">Total Cash At Hand : <?php echo number_format($total); ?></span></h4>

                          <?php }else{ ?>
                          <p class="text-muted">No Transaction found for this Date </p>
                          <?php } ?>

                        </div>

                      </div>

                      <!-- Cash -->
                     
                      <div id="cash" class="tab-pane tabs-up fade in panel panel-default">
                        
                        <div class="panel-body table-responsive">

                          <span style="float:right; margin-top:25px; margin-right:10px">DEPT CASH RETURNS ONLY</span>
                          
                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                           <?php if(count($cashcollections) > 0){ ?>
                        
                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Time</th>
                                    <th>Trans ID</th>
                                    <th>Department</th>
                                    <th>Paid By</th>
                                    <th>Amount</th>
                                  </tr>
                                </thead>
                                <tbody>
                                 <?php 

                                  $counter = 1;
                                  $cashTotal = 0;
                                  foreach ($cashcollections as $t) {
                                    $trans = new Transaction($t->transId);
                                    
                                    $details = json_decode($trans->details, true);
                                  
                                    $cashTotal += $details['amt'];

                                    $srcDetails = $trans->fetchSrcDetails();

                                    $staff = new User(new Staff($srcDetails->staffId));
                                    
                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo timeToString($trans->time); ?></td>
                                    <td><?php echo $trans->id; ?></td>
                                    <td><?php echo User::getRole($srcDetails->privilege); ?></td>
                                    <td><?php echo $staff->name; ?></td>
                                    <td><?php echo number_format($details['amt']); ?></td>
                                  </tr>
                                 <?php $counter++; } ?>
                                 <tr>
                                  <td colspan="5"><h5>Total</h5></td>
                                  <td><h5><?php echo number_format($cashTotal); ?></h5></td>
                                </tr>
                                </tbody>
                              </table>

                          <?php }else{ ?>
                          <p class="text-muted">No Department Cash Collection found for this Date </p>
                          <?php } ?>

                        </div>

                      </div>


                      <!-- Bills -->

                      <div id="credit" class="tab-pane tabs-up fade panel panel-default">
                        
                        <div class="panel-body table-responsive">

                          <span style="float:right; margin-top:25px; margin-right:10px">DEPT CREDIT PAYMENTS ONLY</span>
                          
                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                           <?php if(count($creditCollections) > 0){ ?>
                        
                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Time</th>
                                    <th>Credit Date</th>
                                    <th>Trans ID</th>
                                    <th>Department</th>
                                    <th>Paid By</th>
                                    <th>Amount</th>
                                  </tr>
                                </thead>
                                <tbody>
                                 <?php 

                                  $counter = 1;
                                  $creditTotal = 0;
                                  foreach ($creditCollections as $t) {
                                    $trans = new Transaction($t->transId);
                                    
                                    $details = json_decode($trans->details, true);

                                    $srcDetails = $trans->fetchSrcDetails();
                                   
                                    $creditTotal += $details['amt'];

                                    $staff = new User($srcDetails->staffId);
									
                                    
                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo timeToString($trans->time); ?></td>
                                    <td><?php echo dateToString($srcDetails->date); ?></td>
                                    <td><?php echo $trans->id; ?></td>
                                    <td><?php echo User::getRole($srcDetails->privilege); ?></td>
                                    <td><?php echo $staff->name ?></td>
                                    <td><?php echo number_format($details['amt']); ?></td>
                                  </tr>
                                 <?php $counter++; } ?>
                                 <tr>
                                  <td colspan="6"><h5>Total</h5></td>
                                  <td><h5><?php echo number_format($creditTotal); ?></h5></td>
                                </tr>
                                </tbody>
                              </table>

                          <?php }else{ ?>
                          <p class="text-muted">No Department Credit Payment found for this Date </p>
                          <?php } ?>

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
                              'plugins/bootstrap-chosen/chosen.jquery.js',
                              'moment/moment.js',
                              'app/custom.js',
                              'application/ctrl.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
    ?>
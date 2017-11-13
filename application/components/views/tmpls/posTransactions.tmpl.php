<?php
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | Mgt Staff | POS Units )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5,6,8,9,10,11), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

$date = $session->read('transDate') ? $session->read('transDate') : today();

if($session->read('transDate')){
  $session->write('transDate', null);
}



$account = $session->read('logPrivilege' . $thisUser->id . $thisUser->get('activeAcct'))
            ? $session->read('logPrivilege' . $thisUser->id . $thisUser->get('activeAcct'))
            : $thisUser->get('activeAcct');

# fetch transactions for this User
$transactions = Transaction::fetchUserTransactions($thisUser->id, $account, $date);





$bills = array();

$credits = array();

$cash = array();

$pos = array();



# split transactions into parts
foreach ($transactions as $row) {
  # code...
  $trans = new Transaction($row->id);
  switch ($trans->type) {
    case 3: case 9:
      # cash sales | guest credit payment
      $cash[] = $row;
      break;

    case 1:
      # Bill
      $bills[] = $row;
      break;

    case 4:
      # credit sale
      $credits[] = $row;
      break;

    case 17:
      # credit sale
      $pos[] = $row;
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
        if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
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
                      <li><a data-toggle="tab" role="tab" href="#cash">Cash</a></li>
                      <li><a data-toggle="tab" role="tab" href="#credit">Credit</a></li>
                      <li><a data-toggle="tab" role="tab" href="#bills">Posted</a></li>
                      <li><a data-toggle="tab" role="tab" href="#pos">POS</a></li>
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
                                    <th>Description</th>
                                    <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                    <th>By</th>
                                    <?php } ?>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php

                                  $counter = 1;
                                  $total = 0;
                                  foreach ($transactions as $t) {

                                    $trans = new Transaction($t->id);
                                    # split details
                                    //$details = $trans->extractDetails(true);
                                    $details = json_decode($trans->details, true);

                                    if($trans->desc == 'Cash Sale' || $trans->desc == 'Credit Payment' || $trans->desc == 'POS Sale'){
                                      $total += $details['amt'];
                                    }
                                    $transactor = new Staff($trans->staffId);

                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo timeToString($trans->time); ?></td>
                                    <td><?php echo $trans->id; ?></td>
                                    <td><?php echo $trans->desc; ?></td>
                                    <td><?php echo number_format($details['amt']); ?></td>
                                    <td><?php if(isset($details['desc'])){ echo $details['desc']; } ?></td>
                                    <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                    <td><?php echo $transactor->name; ?></td>
                                    <?php } ?>
                                  </tr>
                                 <?php $counter++; } ?>

                                </tbody>
                          </table>

                          <hr class="dotted">

                          <h4 class="text-primary">Total Cash At Hand <small>( Cash Sales + Cash Credit Payments + POS Sale )</small>:
                          <span style="margin-left:25px" class="text-primary"> <?php echo number_format($total); ?></span></h4>

                          <?php }else{ ?>
                          <p class="text-muted">No Transaction found for this Date </p>
                          <?php } ?>

                        </div>

                      </div>

                      <!-- Cash -->

                      <div id="cash" class="tab-pane tabs-up fade in panel panel-default">

                        <div class="panel-body table-responsive">

                          <span style="float:right; margin-top:25px; margin-right:10px">CASH TRANSACTIONS ONLY</span>

                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                           <?php if(count($cash) > 0){ ?>

                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Time</th>
                                    <th>Trans ID</th>
                                    <th>Amount</th>
                                    <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                    <th>By</th>
                                    <?php } ?>
                                  </tr>
                                </thead>
                                <tbody>
                                 <?php

                                  $counter = 1;
                                  $cashTotal = 0;
                                  foreach ($cash as $t) {
                                    $trans = new Transaction($t->transId);
                                    # split details
                                    //$details = $trans->extractDetails(true);
                                    $details = json_decode($trans->details, true);

                                    $cashTotal += $details['amt'];

                                    $transactor = new Staff($trans->staffId);

                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo timeToString($trans->time); ?></td>
                                    <td><?php echo $trans->id; ?></td>
                                    <td><?php echo number_format($details['amt']); ?></td>
                                    <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                    <td><?php echo $transactor->name; ?></td>
                                    <?php } ?>
                                  </tr>
                                 <?php $counter++; } ?>
                                 <tr>
                                  <td colspan="3"><h5>Total</h5></td>
                                  <td><h5><?php echo number_format($cashTotal); ?></h5></td>
                                  <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                    <td>&nbsp;</td>
                                  <?php } ?>
                                  
                                </tr>
                                </tbody>
                              </table>

                          <?php }else{ ?>
                          <p class="text-muted">No Cash Transaction found for this Date </p>
                          <?php } ?>

                        </div>

                      </div>





                      <!-- Pos -->

                      <div id="pos" class="tab-pane tabs-up fade in panel panel-default">

                        <div class="panel-body table-responsive">

                          <span style="float:right; margin-top:25px; margin-right:10px">POS TRANSACTIONS ONLY</span>

                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                           <?php if(count($pos) > 0){ ?>

                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Time</th>
                                    <th>Trans ID</th>
                                    <th>POS NO</th>
                                    <th>Amount</th>
                                    <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                    <th>By</th>
                                    <?php } ?>
                                  </tr>
                                </thead>
                                <tbody>
                                 <?php

                                  $counter = 1;
                                  $posTotal = 0;
                                  foreach ($pos as $t) {
                                    $trans = new Transaction($t->transId);
                                    # split details
                                    //$details = $trans->extractDetails(true);
                                    $details = json_decode($trans->details, true);

                                    $salesDetails = json_decode($details['saleDetails']);

                                    $posTotal += $details['amt'];

                                    $transactor = new Staff($t->staffId);

                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo timeToString($trans->time); ?></td>
                                    <td><?php echo $trans->id; ?></td>
                                    <td><?php echo $salesDetails->POSReceiptNo; ?></td>
                                    <td><?php echo number_format($details['amt']); ?></td>
                                    <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                    <td><?php echo $transactor->name; ?></td>
                                    <?php } ?>
                                  </tr>
                                 <?php $counter++; } ?>
                                 <tr>
                                  <td colspan="4"><h5>Total</h5></td>
                                  <td><h5><?php echo number_format($posTotal); ?></h5></td>
                                  <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                    <td>&nbsp;</td>
                                    <?php } ?>
                                </tr>
                                </tbody>
                              </table>

                          <?php }else{ ?>
                          <p class="text-muted">No POS Transaction found for this Date </p>
                          <?php } ?>

                        </div>

                      </div>



                      <!-- Bills -->

                      <div id="bills" class="tab-pane tabs-up fade panel panel-default">

                        <div class="panel-body table-responsive">

                          <span style="float:right; margin-top:25px; margin-right:10px">POSTED TRANSACTIONS ONLY</span>

                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                           <?php if(count($bills) > 0){ ?>

                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Time</th>
                                    <th>Trans ID</th>
                                    <th>Guest Room</th>
                                    <th>Amount</th>
                                    <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                    <th>By</th>
                                    <?php } ?>
                                  </tr>
                                </thead>
                                <tbody>
                                 <?php

                                  $counter = 1;
                                  $billsTotal = 0;
                                  foreach ($bills as $t) {
                                    $trans = new Transaction($t->transId);
                                    # split details
                                    //$details = $trans->extractDetails(true);
                                    $details = json_decode($trans->details, true);
                                    $billsTotal += $details['amt'];
									
                                    //$salesDetails = json_decode($details['saleDetails']);

                                    $staff = new Staff($t->staffId);

                                      $src = json_decode($trans->src);

                                      # fetch Room No or this transaction
                                      $data = $registry->get('db')->executeTransQuery($src->tbl, $src->id);
                                      $room = new Room($data->roomId);

                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo timeToString($trans->time); ?></td>
                                    <td><?php echo $trans->id; ?></td>
                                    <td><?php echo $room->no; ?></td>
                                    <td><?php echo number_format($details['amt']); ?></td>
                                    <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                      <td><?php echo $staff->name; ?></td>
                                    <?php } ?>
                                  </tr>
                                 <?php $counter++; } ?>
                                 <tr>
                                  <td colspan="4"><h5>Total</h5></td>
                                  <td><h5><?php echo number_format($billsTotal); ?></h5></td>
                                  <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                    <td>&nbsp;</td>
                                  <?php } ?>
                                </tr>
                                </tbody>
                              </table>

                          <?php }else{ ?>
                          <p class="text-muted">No Bill Transaction found for this Date </p>
                          <?php } ?>

                        </div>

                      </div>



                      <!-- Credits -->

                      <div id="credit" class="tab-pane tabs-up fade panel panel-default">

                        <div class="panel-body table-responsive">

                          <span style="float:right; margin-top:25px; margin-right:10px">CREDIT TRANSACTIONS ONLY</span>

                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                           <?php if(count($credits) > 0){ ?>

                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Time</th>
                                    <th>Trans ID</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                    <th>By</th>
                                    <?php } ?>
                                  </tr>
                                </thead>
                                <tbody>
                                 <?php

                                  $counter = 1;
                                  $creditTotal = 0;
                                  foreach ($credits as $t) {
                                    $trans = new Transaction($t->transId);
                                    # split details
                                    //$details = $trans->extractDetails(true);
                                    $details = json_decode($trans->details);

                                    $creditTotal += $details->amt;

                                    $transactor = new Staff($t->staffId);

                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo timeToString($trans->time); ?></td>
                                    <td><?php echo $trans->id; ?></td>
                                    <td><?php echo $details->desc; ?></td>
                                    <td><?php echo number_format($details->amt); ?></td>
                                    <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                    <td><?php echo $transactor->name; ?></td>
                                    <?php } ?>
                                  </tr>
                                 <?php $counter++; } ?>
                                 <tr>
                                  <td colspan="4"><h5>Total</h5></td>
                                  <td><h5><?php echo number_format($creditTotal); ?></h5></td>
                                  <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                    <td>&nbsp;</td>
                                  <?php } ?>
                                </tr>
                                </tbody>
                              </table>

                          <?php }else{ ?>
                          <p class="text-muted">No Credit Transaction found for this Date </p>
                          <?php } ?>

                        </div>

                      </div>



                    </div>

            </div>



        </div>

    </div>
    <!-- Warper Ends Here (working area) -->

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


    <?php
    	$registry->get('includer')->render('footer', array('js' => array(
                              'plugins/nicescroll/jquery.nicescroll.min.js',
                              'plugins/typehead/typeahead.bundle.js',
                              'plugins/typehead/typeahead.bundle-conf.js',
                              'plugins/inputmask/jquery.inputmask.bundle.js',
                              'plugins/bootstrap-chosen/chosen.jquery.js',
                              'moment/moment.js',
                              'app/custom.js',
                              'application/ctrl.js',
                              'application/transactions.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js'
    ?>

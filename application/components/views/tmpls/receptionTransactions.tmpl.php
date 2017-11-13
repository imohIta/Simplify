<?php
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));


#check if user has access to this page ( super admin | Mgt Staff | reception )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5,6,7), true);

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



$payments = array();
$bills = array();
$refunds = array();
$credits = array();

$cash = array();
$cheques = array();
$pos = array();
$bt = array();
$ce = array();


$sCash = 0;
$sRefunds = 0;
//$sChairmanXpenses = 0;

# split transactions into parts
foreach ($transactions as $row) {
  # code...
  $trans = new Transaction($row->id);
  switch ($trans->type) {
    case 2: case 10:
      # guest Payment | Reservation payment
      $payments[] = $row;
      break;

    case 1:
      # bills
      $bills[] = $row;
      break;

    case 7:
      # guest refunds
      $refunds[] = $row;
      $details = json_decode($row->details, true);
      $sRefunds += $details['amt'];
      break;

    case 8:
      # guest credit
      $credits[] = $row;
      break;

    // case 11:
    //   # chairman expenses
    //   $ce[] = $row;
    //   $details = json_decode($row->details, true);
    //   $sChairmanXpenses += $details['amt'];
    //   break;

  }
}


# Slip Payments into the difrent payment types
foreach ($payments as $row) {
  # code...
  $trans = new Transaction($row->transId);
  $det = $trans->extractDetails();
  $d = json_decode($det->desc, true);


  switch (strtolower($d['Pay Type'])) {
    case 'cash':
      # code...
      $cash[] = $row;
      $details = json_decode($row->details, true);
      $sCash += $details['amt'];
      break;

    case 'cheque':
      # code...
      $cheques[] = $row;
      break;

    case 'pos':
      # code...
      $POS[] = $row;
      break;

    case 'bank transfer':
      # code...
      $bt[] = $row;
      break;

  }
}

//$t = new Transaction(276);
//$src = json_decode($t->src);
//$data = $registry->get('db')->executeTransQuery($src->tbl, $src->id);
//var_dump($data); die;
//$room = new Room($data->roomId);



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
                        <button class="btn btn-warning" id="searchTransBtn" type="submit" name="search" >Search By Date</button>
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
                      <li><a data-toggle="tab" role="tab" href="#bills">Posted</a></li>
                      <li><a data-toggle="tab" role="tab" href="#refunds">Refunds</a></li>
                      <li><a data-toggle="tab" role="tab" href="#credit">Credit</a></li>
                      <li><a data-toggle="tab" role="tab" href="#cheque">Cheques</a></li>
                      <li><a data-toggle="tab" role="tab" href="#pos">POS</a></li>
                      <li><a data-toggle="tab" role="tab" href="#bt">Bank Transfers</a></li>
                      <!-- <li><a data-toggle="tab" role="tab" href="#ce">Chairman Expenses</a></li> -->
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
                                    <th>Description</th>
                                      <th>Room No</th>
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
                                  foreach ($transactions as $t) {
                                    $trans = new Transaction($t->transId);
                                    # split details
                                    //$details = $trans->extractDetails(true);
                                    $details = json_decode($trans->details, true);

                                    if(json_decode($details['desc'])){
                                      $d = json_decode($details['desc'], true);
                                      $det = '<small>Pay Type :</small> ' . $d['Pay Type'];
                                    }else{
                                      $det = $details['desc'];
                                    }

                                    $staff = new Staff($t->staffId);

                                      $src = json_decode($trans->src);
                                      

                                      # fetch Room No or this transaction

										$data = $registry->get('db')->executeTransQuery($src->tbl, $src->id);
										
                                        if(is_null($data) || false === $data || $src->tbl == "guestRefunds"){
                                            $roomNo = '';
                                        }else {
										
                                            $room = new Room($data->roomId);
                                            $roomNo = $room->no;
                                        }
                                      
									
									  
                                      

                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo timeToString($trans->time); ?></td>
                                    <td><?php echo $trans->id; ?></td>
                                    <td><?php echo $trans->desc; ?></td>
                                      <td><?php echo $det; ?></td>
                                      <td><?php echo $roomNo; ?></td>

                                    <td><?php echo number_format((int)$details['amt']); ?></td>
                                    <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                      <td><?php echo $staff->name; ?></td>
                                    <?php } ?>
                                  </tr>
                                 <?php $counter++; } ?>

                                </tbody>
                          </table>

                          <hr class="dotted">

                          <h4 class="text-primary">Total Cash At Hand <small class="text-primary">( Cash Payment minus Refund )</small>
                          <span style="margin-left:25px" class="text-primary"> : <?php echo number_format($sCash -  $sRefunds); ?></span></h4>

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

                                    $staff = new Staff($t->staffId);

                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo timeToString($trans->time); ?></td>
                                    <td><?php echo $trans->id; ?></td>
                                    <td><?php echo number_format($details['amt']); ?></td>
                                    <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                    <td><?php echo $staff->name; ?></td>
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


                      <!-- Bills -->

                      <div id="bills" class="tab-pane tabs-up fade panel panel-default">

                        <div class="panel-body table-responsive">

                          <span style="float:right; margin-top:25px; margin-right:10px">POSTED BILL TRANSACTIONS ONLY</span>

                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                           <?php if(count($bills) > 0){ ?>

                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Time</th>
                                    <th>Trans ID</th>
                                    <th>Description</th>
                                      <th>Room No.</th>
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

                                    if(json_decode($details['desc'])){
                                      $d = json_decode($details['desc'], true);
                                      $det = '<small>Pay Type :</small> ' . $d['Pay Type'];
                                    }else{
                                      $det = $details['desc'];
                                    }
                                    $billsTotal += $details['amt'];

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
                                    <td><?php echo $det; ?></td>
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
                                  <td colspan="5"><h5>Total</h5></td>
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


                      <!-- Refunds -->

                      <div id="refunds" class="tab-pane tabs-up fade in panel panel-default">

                        <div class="panel-body table-responsive">

                          <span style="float:right; margin-top:25px; margin-right:10px">REFUNDS ONLY</span>

                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                           <?php if(count($refunds) > 0){ ?>

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
                                  $refundsTotal = 0;
                                  foreach ($refunds as $t) {
                                    $trans = new Transaction($t->transId);

                                    # split details
                                    $details = json_decode($trans->details, true);

                                    $refundsTotal += $details['amt'];

                                    $staff = new Staff($t->staffId);

                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo timeToString($trans->time); ?></td>
                                    <td><?php echo $trans->id; ?></td>
                                    <td><?php echo $details['desc']; ?></td>
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
                                  <td><h5><?php echo number_format($refundsTotal); ?></h5></td>
                                </tr>
                                </tbody>
                              </table>

                          <?php }else{ ?>
                          <p class="text-muted">No Refund found for this Date </p>
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

                                    $staff = new Staff($t->staffId);

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
                                      <td><?php echo $staff->name; ?></td>
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


                      <!-- Cheques -->

                      <div id="cheque" class="tab-pane tabs-up fade panel panel-default">

                        <div class="panel-body table-responsive">

                          <span style="float:right; margin-top:25px; margin-right:10px">CHEQUES ONLY</span>

                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                           <?php if(count($cheques) > 0){ ?>

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
                                  $chequeTotal = 0;
                                  foreach ($cheques as $t) {
                                    $trans = new Transaction($t->transId);
                                    # split details
                                    //$details = $trans->extractDetails(true);
                                    $details = json_decode($trans->details, true);
                                    $desc = json_decode($details['desc'], true);

                                    $d = '<small>Bank :</small> ' . $desc['Bank'] . '<br />';
                                    $d .= '<small>Cheque No. : </small>' . $desc['Cheque No'];

                                    $chequeTotal += $details['amt'];

                                    $staff = new Staff($t->staffId);


                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo timeToString($trans->time); ?></td>
                                    <td><?php echo $trans->id; ?></td>
                                    <td><?php echo $d; ?></td>
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
                                  <td><h5><?php echo number_format($chequeTotal); ?></h5></td>
                                  <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                    <td>&nbsp;</td>
                                  <?php } ?>
                                </tr>
                                </tbody>
                              </table>

                          <?php }else{ ?>
                          <p class="text-muted">No Cheque Transaction found for this Date </p>
                          <?php } ?>

                        </div>


                      </div>


                      <!-- POS Payments -->

                      <div id="pos" class="tab-pane tabs-up fade panel panel-default">

                        <div class="panel-body table-responsive">

                          <span style="float:right; margin-top:25px; margin-right:10px">POS PAYMENTS ONLY</span>

                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                           <?php if(count($pos) > 0){ ?>

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
                                  $posTotal = 0;
                                  foreach ($pos as $t) {
                                    $trans = new Transaction($t->transId);
                                    # split details
                                    //$details = $trans->extractDetails(true);
                                    $details = json_decode($trans->details, true);
                                    $d = '<small>POS NO. :<small> ' . $details['POS NO'];

                                    $chequeTotal += $details['amt'];

                                    $staff = new Staff($t->staffId);


                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo timeToString($trans->time); ?></td>
                                    <td><?php echo $trans->id; ?></td>
                                    <td><?php echo $d; ?></td>
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
                          <p class="text-muted">No POS Payment found for this Date </p>
                          <?php } ?>

                        </div>
                      </div>


                      <div id="bt" class="tab-pane tabs-up fade panel panel-default">

                        <div class="panel-body table-responsive">

                          <span style="float:right; margin-top:25px; margin-right:10px">BANK TRANSFER ONLY</span>

                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                           <?php if(count($bt) > 0){ ?>

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
                                  $btTotal = 0;
                                  foreach ($bt as $t) {
                                    $trans = new Transaction($t->transId);
                                    # split details
                                    //$details = $trans->extractDetails(true);
                                    $details = json_decode($trans->details, true);
                                    $d = '<small>Bank :<small> ' . $details['Bank'] . '<br />';
                                    $d .= '<small>Transfer Date. : </small>' . $details['Transfer Date'];

                                    $chequeTotal += $details['amt'];

                                    $staff = new Staff($t->staffId);


                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo timeToString($trans->time); ?></td>
                                    <td><?php echo $trans->id; ?></td>
                                    <td><?php echo $d; ?></td>
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
                                  <td><h5><?php echo number_format($btTotal); ?></h5></td>
                                  <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                    <td>&nbsp;</td>
                                  <?php } ?>
                                </tr>
                                </tbody>
                              </table>

                          <?php }else{ ?>
                          <p class="text-muted">No Bank Transfer found for this Date </p>
                          <?php } ?>

                        </div>


                      </div>



                      <!-- Chairman Expenses -->
                      <div id="ce" class="tab-pane tabs-up fade panel panel-default">

                        <div class="panel-body table-responsive">

                          <span style="float:right; margin-top:25px; margin-right:10px">CHAIRMAN EXPENSES ONLY</span>

                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                           <?php if(count($ce) > 0){ ?>

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
                                  $ceTotal = 0;
                                  foreach ($ce as $t) {
                                    $trans = new Transaction($t->transId);
                                    # split details
                                    //$details = $trans->extractDetails(true);
                                    $details = json_decode($trans->details, true);
                                    $ceTotal += $details['amt'];


                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo timeToString($trans->time); ?></td>
                                    <td><?php echo $trans->id; ?></td>
                                    <td><?php echo $details['desc']; ?></td>
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
                                  <td><h5><?php echo number_format($ceTotal); ?></h5></td>
                                  <?php
                                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5))){
                                    ?>
                                    <td>&nbsp;</td>
                                  <?php } ?>
                                </tr>
                                </tbody>
                              </table>

                          <?php }else{ ?>
                          <p class="text-muted">No Chairman Expenses found for this Date </p>
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

<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | Mgt Staff | reception )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,6), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

$date = $session->read('cashBkDate') ? $session->read('cashBkDate') : today();
$direction = $session->read('cashBkDir') ? $session->read('cashBkDir') : 1;


if($session->read('cashBkDir')){ $session->write('cashBkDir', null); }
if($session->read('cashBkDate')){ $session->write('cashBkDate', null); }

if($direction == 1){
  
  # fetch cashier collections
  $collections = $registry->get('db')->fetchCashierCollections($date);
  
  #fetch Impress PayIns
  $impress = new Impress();
  $impressPayIns = $impress->fetchPayIns($date);

  # calculate collections total
  $total = 0;
  foreach ($collections as $t) {
    # code...
    $trans = new Transaction($t->transId);
    # split details
    //$details = $trans->extractDetails(true);
    $details = json_decode($trans->details, true);
    $total += $details['amt'];
  }

  # calculate impress payIn total
  $imTotal = 0;
  foreach ($impressPayIns as $t) {
   $imTotal += $t->amt;
  }

  # fetch As At payment
  $asAt = $registry->get('db')->fetchAllDeptCreditPayments($date);

  # calculate asAt total
  $asAtTotal = 0;
  foreach ($asAt as $a) {
   $asAtTotal += $a->amt;
  }



}else{

  $impress = new Impress();
  $impressExpenses = $impress->fetchExpenses($date);

  # calculate impress Expenses total
  $imTotal = 0;
  foreach ($impressExpenses as $t) {
   $imTotal += $t->amt;
  }

  $bankDeposits = $registry->get('db')->fetchBankDeposits($date);

  #calculate bank deposits total
  $bdTotal = 0;
  foreach ($bankDeposits as $key) {
    # code...
    $bdTotal += $key->amt;
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
        <h1>Cash Book <small style="color:#FF404B">&nbsp;</small></h1>
      </div>
    	
      <hr />


        
         <div class="row">
        
            <div class="col-md-10">

                    <form method="post" action="<?php echo $baseUri; ?>/cashier/cashbook">
                       <div class="col-sm-4">
                            <input type="text" placeholder="yyyy-mm-dd" class="form-control form-control-circle inputmask" data-inputmask="'alias': 'yyyy-mm-dd'" name="date" value="<?php echo $date; ?>" />
                            
                      </div>

                      <div class="col-sm-3">
                        <select class="form-control chosen-select form-control-circle" data-placeholder="" required name="direction">
                            <option></option>
                            <option value="1" <?php if($direction == 1) { ?> selected <?php } ?>>Incoming</option>
                            <option value="2" <?php if($direction == 2) { ?> selected <?php } ?>>Out-Going</option>   
                          </select>
                      </div>

                      <div class="col-sm-3">
                        <button type="submit" name="submit" class="btn btn-warning btn-circle">Search</button>
                      </div>
                  </form>

             <br />
             <hr class="dotted">


             <?php
             # Case Incoming
             if($direction == 1){
             ?>

                <ul role="tablist" class="nav nav-tabs" id="myTab">
                  <li class="active"><a data-toggle="tab" role="tab" href="#cash">Dept Returns</a></li>
                  <li><a data-toggle="tab" role="tab" href="#impress">Impress Pay-Ins</a></li>
                  <li><a data-toggle="tab" role="tab" href="#asAt">As At Payments</a></li>
                </ul>

                <div class="tab-content" id="myTabContent">


                      <!-- cash Option -->
                       <div id="cash" class="tab-pane tabs-up fade in active panel panel-default">
                        
                        <div class="panel-body table-responsive">
                          
                          <span style="float:right; margin-top:25px; margin-right:10px">DEPT RETURNS</span>
                          
                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                          <?php if(count($collections) > 0){ ?>
                        
                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Time</th>
                                    <th>Trans ID</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php 

                                  $counter = 1;
                                  
                                  foreach ($collections as $t) {
                                    $trans = new Transaction($t->transId);
                                    # split details
                                    //$details = $trans->extractDetails(true);
                                    $details = json_decode($trans->details, true);
                                    //var_dump($details);
                                   
                                    //if(json_decode($details['desc'])){
                                    //  $d = json_decode($details['desc'], true);
                                    //  $det = '<small>Pay Type :</small> ' . $d['Pay Type'];
                                    //}else{
                                    //  $det = $details['desc']; 
                                    //}
                                    
                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo timeToString($trans->time); ?></td>
                                    <td><?php echo $trans->id; ?></td>
                                    <td><?php echo $trans->desc; ?></td>
                                    <td><?php echo $details['type']; ?></td>
                                    <td><?php echo number_format($details['amt']); ?></td>
                                  </tr>
                                 <?php } ?>

                                  <tr>
                                  <td colspan="5"><h5>Total</h5></td>
                                  <td><h5><?php echo number_format($total); ?></h5></td>
                                </tr>
                                  
                                </tbody>
                          </table>

                         

                          <?php }else{ ?>
                          <p class="text-muted">No Department Returns found for <?php echo dateToString($date); ?> </p>
                          <?php } ?>

                        </div>

                      </div>
                      <!-- End of cash option -->


                      <!-- impress Option -->

                       <div id="impress" class="tab-pane tabs-up fade panel panel-default">
                        
                        <div class="panel-body table-responsive">

                          <span style="float:right; margin-top:25px; margin-right:10px">IMPRESS PAY-INS ONLY</span>
                          
                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                           <?php if(count($impressPayIns) > 0){ ?>
                        
                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Source</th>
                                    <th>Amount</th>
                                  </tr>
                                </thead>
                                <tbody>
                                 <?php 

                                  $counter = 1;
                                  foreach ($impressPayIns as $t) {
                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo $t->src; ?></td>
                                    <td><?php echo number_format($t->amt); ?></td>
                                  </tr>
                                 <?php $counter++; } ?>
                                 <tr>
                                  <td colspan="2"><h5>Total</h5></td>
                                  <td><h5><?php echo number_format($imTotal); ?></h5></td>
                                </tr>
                                </tbody>
                              </table>

                          <?php }else{ ?>
                          <p class="text-muted">No Impress Pay-In found for <?php echo dateToString($date); ?> </p>
                          <?php } ?>

                        </div>

                      </div>
                      <!-- End of impress option -->

                      <!-- impress Option -->

                       <div id="asAt" class="tab-pane tabs-up fade panel panel-default">
                        
                        <div class="panel-body table-responsive">

                          <span style="float:right; margin-top:25px; margin-right:10px">AS-AT PAYMENTS ONLY</span>
                          
                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                           <?php if(count($asAt) > 0){ ?>
                        
                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Dept</th>
                                    <th>Amount</th>
                                  </tr>
                                </thead>
                                <tbody>
                                 <?php 

                                  $counter = 1;
                                  foreach ($asAt as $t) {
                                    
                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo User::getRole($t->privilege); ?></td>
                                    <td><?php echo number_format($t->amt); ?></td>
                                  </tr>
                                 <?php $counter++; } ?>
                                 <tr>
                                  <td colspan="2"><h5>Total</h5></td>
                                  <td><h5><?php echo number_format($asAtTotal); ?></h5></td>
                                </tr>
                                </tbody>
                              </table>

                          <?php }else{ ?>
                          <p class="text-muted">No As-At Payment found for <?php echo dateToString($date); ?> </p>
                          <?php } ?>

                        </div>

                      </div>
                      
                      <!-- End of As At option -->


                <!-- End of Tab Content -->
                </div>

                <hr />
                <h3>Total Incoming Cash : <?php echo number_format($total + $imTotal + $asAtTotal); ?></h3>



            <?php

             }else{
              # Case OutGiong
            ?>

              <ul role="tablist" class="nav nav-tabs" id="myTab">
                  <li class="active"><a data-toggle="tab" role="tab" href="#impress2">Impress Expenses</a></li>
                  <li><a data-toggle="tab" role="tab" href="#bank">Bank Deposits</a></li>
                </ul>

                <div class="tab-content" id="myTabContent">


                      <!-- cash Option -->
                       <div id="impress2" class="tab-pane tabs-up fade in active panel panel-default">
                        
                        <div class="panel-body table-responsive">
                          
                          <span style="float:right; margin-top:25px; margin-right:10px"> IMPRESS EXPENSES ONLY</span>
                          
                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                          <?php if(count($impressExpenses) > 0){ ?>
                        
                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php 

                                  $counter = 1;
                                  
                                  foreach ($impressExpenses as $t) {
                                    
                                    
                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo dateToString($t->date); ?></td>
                                    <td><?php echo Impress::getCategoryName($t->category); ?></td>
                                    <td><?php echo number_format($t->amt); ?></td>
                                  </tr>
                                 <?php } ?>

                                  <tr>
                                  <td colspan="3"><h5>Total</h5></td>
                                  <td><h5><?php echo number_format($imTotal); ?></h5></td>
                                </tr>
                                  
                                </tbody>
                          </table>

                         

                          <?php }else{ ?>
                          <p class="text-muted">No Impress Exepnses found for <?php echo dateToString($date); ?> </p>
                          <?php } ?>

                        </div>

                      </div>
                      <!-- End of cash option -->


                      <!-- impress Option -->

                       <div id="bank" class="tab-pane tabs-up fade panel panel-default">
                        
                        <div class="panel-body table-responsive">

                          <span style="float:right; margin-top:25px; margin-right:10px">BANK DEPOSITS ONLY</span>
                          
                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                           <?php if(count($bankDeposits) > 0){ ?>
                        
                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Pay Date</th>
                                    <th>Amount</th>
                                  </tr>
                                </thead>
                                <tbody>
                                 <?php 

                                  $counter = 1;
                                  foreach ($bankDeposits as $key) {
                                   
                                    
                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo dateToString($key->payDate); ?></td>
                                    <td><?php echo number_format($key->amt); ?></td>
                                  </tr>
                                 <?php $counter++; } ?>
                                 <tr>
                                  <td colspan="2"><h5>Total</h5></td>
                                  <td><h5><?php echo number_format($bdTotal); ?></h5></td>
                                </tr>
                                </tbody>
                              </table>

                          <?php }else{ ?>
                          <p class="text-muted">No Bank Deposit found for <?php echo dateToString($date); ?> </p>
                          <?php } ?>

                        </div>

                      </div>
                      


                      <!-- End of impress option -->

                    </div>

                    <hr />
                   <h3>Total Outgoing Cash : <?php echo number_format($bdTotal + $imTotal); ?></h3>



            <?php  } ?>
               

               
               
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
<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | Accountant )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,5), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

$date = $session->read('ledgerDate') ? $session->read('ledgerDate') : today();



if($session->read('ledgerDir')){ $session->write('ledgerDir', null); }



  
  # fetch cashier collections
  $collections = $registry->get('db')->fetchCashierCollections($date);
  
  

  # calculate collections total
  $total = 0;
  $reception = 0;
  $poolbar = 0;
  $mainBar = 0;
  $resturant = 0;
  $resturantDrinks = 0;

  foreach ($collections as $t) {
    
    $total += $t->amtPaid;

    switch ($t->privilege) {
      case 7:
        $reception += $t->amtPaid;
        break;
      case 8:
        $poolbar += $t->amtPaid;
        break;
      case 9:
        $mainBar += $t->amtPaid;
        break;
      case 10:
        $resturant += $t->amtPaid;
        break;
      case 11:
        $resturantDrinks += $t->amtPaid;
        break;
      default:
        # code...
        break;
    }
  }

  $pos = 0;
  $bt = 0;
  $cheque = 0;
 
  # fetch all payment transaction

  foreach (Transaction::fetch(2, $date) as $key) {
    # code...
    $transaction = new Transaction($key->transId);
    $details = $transaction->extractDetails();
    
    $desc = json_decode($details->desc, true);
    
    switch ($desc['Pay Type']) {
      case 'POS':
        # code...
        $pos += $details->amt;
        break;
      
      case 'Bank Transfer':
        # code...
        $bt += $details->amt;
        break;
      
      case 'Cheque':
        # code...
        $cheque += $details->amt;
        break;
    }
  }


  # fetch As At payment
  $asAt = $registry->get('db')->fetchAllDeptCreditPayments($date);

  # calculate asAt total
  $asAtTotal = 0;
  foreach ($asAt as $a) {
   $asAtTotal += $a->amt;
  }



  # fetch guest refunds
  $refunds = 0;
  foreach (Transaction::fetch(7, $date) as $key) {
    # code...
    $transaction = new Transaction($key->transId);
    $refunds += $transaction->extractDetails()->amt;
  }


  # fetch chariman expenses
  $cExpenses = 0;
  foreach (Transaction::fetch(11, $date) as $key) {
    # code...
    $transaction = new Transaction($key->transId);
    $cExpenses += $transaction->extractDetails()->amt;
  }

  $bankDeposits = $registry->get('db')->fetchBankDeposits($date);

  #calculate bank deposits total
  $bdTotal = 0;
  foreach ($bankDeposits as $key) {
    # code...
    $bdTotal += $key->amt;
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
        <h1>Income & Expenses Ledger <small style="color:#FF404B">&nbsp;</small></h1>
      </div>
    	
      <hr />


        
         <div class="row">
        
            <div class="col-md-10">

                    <form method="post" action="<?php echo $baseUri; ?>/accountant/ledger">
                       <div class="col-sm-4">
                            <input type="text" placeholder="yyyy-mm-dd" class="form-control form-control-circle inputmask" data-inputmask="'alias': 'yyyy-mm-dd'" name="date" value="<?php echo $date; ?>" />
                            
                      </div>

                     <!--  <div class="col-sm-3">
                        <select class="form-control chosen-select form-control-circle" data-placeholder="" required name="direction">
                            <option></option>
                            <option value="1" <?php if($direction == 1) { ?> selected <?php } ?>>Incoming</option>
                            <option value="2" <?php if($direction == 2) { ?> selected <?php } ?>>Out-Going</option>   
                          </select>
                      </div> -->

                      <div class="col-sm-3">
                        <button type="submit" name="submit" class="btn btn-warning btn-circle">Search</button>
                      </div>
                  </form>

             <br />
             <hr class="dotted">

                <ul role="tablist" class="nav nav-tabs" id="myTab">
                  <li class="active"><a data-toggle="tab" role="tab" href="#incoming">All Incoming Cash</a></li>
                  <li><a data-toggle="tab" role="tab" href="#outgoing">Outgiong</a></li>
                </ul>

                <div class="tab-content" id="myTabContent">


                      <!-- incoming Option -->
                       <div id="incoming" class="tab-pane tabs-up fade in active panel panel-default">
                        
                        <div class="panel-body table-responsive">
                          
                          <span style="float:right; margin-top:25px; margin-right:10px">INCOMING</span>
                          
                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />
                        
                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  
                                  <tr>
                                    <td>1</td>
                                    <td>Total Cash Receipted</td>
                                    <td><?php echo number_format($total); ?></td>
                                  </tr>

                                  <tr>
                                    <td>2</td>
                                    <td>Reception</td>
                                    <td><?php echo number_format($reception); ?></td>
                                  </tr>
                                  
                                  <tr>
                                    <td>3</td>
                                    <td>Pool Bar</td>
                                    <td><?php echo number_format($poolbar); ?></td>
                                  </tr>

                                  <tr>
                                    <td>4</td>
                                    <td>Main Bar</td>
                                    <td><?php echo number_format($mainBar); ?></td>
                                  </tr>

                                  <tr>
                                    <td>5</td>
                                    <td>Resturant</td>
                                    <td><?php echo number_format($resturant); ?></td>
                                  </tr>

                                  <tr>
                                    <td>6</td>
                                    <td>POS</td>
                                    <td><?php echo number_format($pos); ?></td>
                                  </tr>

                                  <tr>
                                    <td>7</td>
                                    <td>Cheque</td>
                                    <td><?php echo number_format($cheque); ?></td>
                                  </tr>

                                  <tr>
                                    <td>8</td>
                                    <td>Bank Transfer</td>
                                    <td><?php echo number_format($bt); ?></td>
                                  </tr>

                                  <tr>
                                    <td>9</td>
                                    <td>As At</td>
                                    <td><?php echo number_format($asAtTotal); ?></td>
                                  </tr>

                                  <tr>
                                    <td colspan="2">Total Income Recipted</td>
                                    <td><?php echo number_format($total + $pos + $cheque + $bt + $asAtTotal ); ?></td>
                                  <tr>

                                  
                                  
                                </tbody>
                          </table>

                          <br /><br />
                         

                        </div>

                      </div>
                      <!-- End of incoming option -->


                      <!-- Outgiong -->
                      
                      <div id="outgoing" class="tab-pane tabs-up fade panel panel-default">
                        
                        <div class="panel-body table-responsive">

                          <span style="float:right; margin-top:25px; margin-right:10px"> OUTGIONG </span>
                          
                          <h3> <?php echo dateToString($date); ?></h3>

                          <hr class="dotted" />

                          
                        
                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  
                                  <tr>
                                    <td>1</td>
                                    <td>Refunds</td>
                                    <td><?php echo number_format($refunds); ?></td>
                                  </tr>

                                  <tr>
                                    <td>2</td>
                                    <td>Chairman Expenses</td>
                                    <td><?php echo number_format($cExpenses); ?></td>
                                  </tr>
                                  
                                  <tr>
                                    <td>3</td>
                                    <td>Bank Deposits</td>
                                    <td><?php echo number_format($bdTotal); ?></td>
                                  </tr>

                                   <tr>
                                    <td colspan="2">Total Income Recipted</td>
                                    <td><?php echo number_format($refunds + $cExpenses + $bdTotal ); ?></td>
                                  <tr>

                                 </tbody>
                              </table>

                         <br /><br />

                        </div>

                      </div>
      


                <!-- End of Tab Content -->
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
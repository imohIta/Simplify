<?php 

#if user not looged in
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}

#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));


$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');


#check if user has access to this page ( super admin | Auditor )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,3), true);

$month = $session->read('ftMonth') ? $session->read('ftMonth') :  date('m');
$year = $session->read('ftYear') ? $session->read('ftYear') : date('Y');

if($session->read('ftMonth')){
  $session->write('ftMonth', null);
  $session->write('ftYear', null); 
} 

//var_dump(Transaction::fetchForFinancialReport($month, $year)); die;

 $depts = array('reception','resturant','pool_bar','main_bar','resturant_drinks');

 $reception = $resturant = $pool_bar = $main_bar = $resturant_drinks = $refunds = 0;

 $log = Transaction::fetchForFinancialReport($month, $year);
 foreach ($log as $row) {
      # code...
      $transaction = new Transaction($row->transId);
      switch ($row->privilege) {
        case 7:
          # code...
          if($row->transType == 7){
            $refunds += $transaction->extractDetails()->amt;
          }else{
            $reception += $transaction->extractDetails()->amt;
          }
          break;

        case 8:
          # code...
          $pool_bar += $transaction->extractDetails()->amt;
          break;

        case 9:
          # code...
          $main_bar += $transaction->extractDetails()->amt;
          break;

        case 10:
          # code...
          $resturant += $transaction->extractDetails()->amt;
          break;

        case 11:
          # code...
          $resturant_drinks += $transaction->extractDetails()->amt;
          break;
        
        default:
          # code...
          break;
      }
 }
 

 # add As-AT Payments
 foreach ($log as $row) {
   # code...
   if($row->transType == 16){
     $src = json_decode($row->src); 
     
     $query = 'select * from `' . $src->tbl . '` where `id`  = ' . $src->id;

     # call query function in database Base class
     $details = $registry->get('db')->query($query);


     switch ($details->privilege) {
        case 7:
          # code...
          
          $reception += $details->amt;
          
          break;

        case 8:
          # code...
          $pool_bar += $details->amt;
          break;

        case 9:
          # code...
          $main_bar += $details->amt;
          break;

        case 10:
          # code...
          $resturant += $details->amt;
          break;

        case 11:
          # code...
          $resturant_drinks += $details->amt;
          break;
        
        default:
          # code...
          break;
      }

   }
 }

 $totalIncome = $reception + $pool_bar + $main_bar + $resturant_drinks;


 /* Impress */

 # initialize all impress categories amt to zero
 foreach (Impress::fetchCategories() as $key) {
   # code...
   $name = strtolower(str_replace(' ', '_', str_replace('/', '_', $key->type))); 
   $$name = 0;
 }

$totalExpenses = 0;
 foreach (Impress::fetchExpensesForDateRange($month, $year) as $row) {
   # code...getCategoryName
    $name = strtolower(str_replace(' ', '_', str_replace('/', '_', Impress::getCategoryName($row->category)))); 
    $$name += $row->amt;
    $totalExpenses += $row->amt;
 }


 
?>

<!-- Bootstrap core CSS -->
  <link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/css/bootstrap/bootstrap.css" /> 

  <!-- Calendar Styling  -->
    <!-- <link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/css/plugins/calendar/calendar.css" /> -->
    
    <!-- Fonts  -->
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,500,600,700,300' rel='stylesheet' type='text/css'>
    
    <!-- Base Styling  -->
    <link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/css/app/app.v1.css" />

    <!-- <meta name="viewport" content="width=device-width, initial-scale=1" /> -->
    
    <title>Kelvic Suite Financial Report</title>
    

    <div style="width:750px; margin:20px auto">

    <div class="warper container-fluid">
          
          
            
                <div class="page-header text-center"><h3 class="no-margn">Kelvic Suites <small>& Towers.</small></h3></div>
                <div class="page-header text-center"><h3 class="no-margn"><small>Plot 107, Area C, New Owerri, Imo State</small></h3></div>
                
                <!-- <hr class="dotted"> -->
                
                <div class="row">
                
                    <div class="col-md-12">
                    <div class="page-header text-center"><h3><?php echo date('F Y', strtotime($year . '-' . $month . '-01')); ?> Financial Report</h3>
                    </div>

                    

                    <div class="print">

                      <hr class="dotted" />

                    <form method="post" action="<?php echo $baseUri; ?>/auditor/financialReport">
                      <table style="width:350px; margin:0px auto">
                                <tr>
                                  <td style="width:150px" align="right"><p class="text-muted">Change Date</p></td>
                                  <td style="width:130px">
                                    <div class="form-group">
                                            <div class="col-sm-11">
                                              <select name="month" data-placeholder="Month" class="chosen-select">
                                                  <option value=""></option>
                                                  <?php
                                                  $months = array('01' => 'Jan', '02' => 'Feb', '03' => 'Mar',
                                                    '04' => 'Apr', '05' => 'May', '06' => 'Jun', '07' => 'Jul',
                                                    '08' => 'Aug', '09' => 'Sep' , '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
                                                    );
                                                  foreach ($months as $key => $value) {
                                                   
                                                    $selected = $month == $key ? 'selected' : '';
                                                  ?>
                                                  <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>

                                                  <?php } ?>
                                                </select>
                                            </div>
                                      </div>
                                  </td>
                                  <td style="width:200px">
                                    <div class="form-group">
                                            <div class="col-sm-12">
                                              <select name="year" data-placeholder="Year" class="chosen-select">
                                                  <option value="<?php echo date('Y'); ?>" selected><?php echo date('Y'); ?></option>
                                                </select>
                                            </div>
                                      </div>
                                  </td>
                                  <td style="width:20px"><button name="submit" type="submit" class="btn btn-warning btn-circle">Sort</button></td>
                                </tr>
                          </table>
                        </form>

                        <hr class="dotted" />

                      </div>

                      
                   
                    
                    <div class="panel panel-default">
                        <div class="panel-heading">Income</div>
                        <div class="panel-body table-responsive">
                        
                            <table class="table">
                                <thead>
                                  <tr>
                                    <td><strong>SN</strong></td>
                                    <td><strong>Income / Dept</strong></td>
                                    <td><strong>Amount ( =N= )</strong></td>
                                   
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                 

                                  $counter = 1;
                                  foreach ($depts as $key) {
                                   $name = ucwords(str_replace('_', ' ', $key));
                                    # code...
                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo $name; ?></td>
                                    <td><?php echo number_format($$key); ?></td>
                                  <tr>
                                  <?php $counter++; } ?>
                                  <tr>
                                    <td colspan="2"><h4>Total</h4></td>
                                    <td><h4><?php echo number_format($totalIncome); ?></h4></td>
                                  </tr>

                                  <tr>
                                    <td colspan="2"><h4>Refunds</h4></td>
                                    <td><h4><?php echo number_format($refunds); ?></h4></td>
                                  </tr>

                                  <tr>
                                    <td colspan="2"><h4>Balance</h4></td>
                                    <td><h4><?php echo number_format($totalIncome - $refunds); ?></h4></td>
                                  </tr>
                                  
                                </tbody>
                              </table>

                        </div>
                      </div>

                      <br />

                      <div class="panel panel-default">
                        <div class="panel-heading">Expenses</div>
                        <div class="panel-body table-responsive">
                        

                              

                              <table class="table">
                                <thead>
                                  <tr>
                                    <td><strong>SN</strong></td>
                                    <td><strong>Expenses</strong></td>
                                    <td><strong>Amount ( =N= )</strong></td>
                                   
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                  $count = 1;
                                  foreach (Impress::fetchCategories() as $key) {
                                     # code...
                                     $name = strtolower(str_replace(' ', '_', str_replace('/', '_', $key->type)));
                                  ?>
                                  <tr>
                                    <td><?php echo $count; ?></td>
                                    <td><?php echo $key->type; ?></td>
                                    <td><?php echo number_format($$name); ?></td>
                                  </tr>

                                  <?php $count++; } ?>

                                  <tr>
                                    <td colspan="2"><h4>Total Expenditure</h4></td>
                                    <td><h4><?php echo number_format($totalExpenses); ?></h4></td>
                                  </tr>

                                </tbody>
                              </table>


                        
                        </div>
                    </div>
                </div>

             

              </div>
            
                
                <div id="print" class="print" class="row">
                    <div class="col-lg-6"><button class="btn btn-warning" type="button" onclick="printInv('print');">Print</button></div>
                    <div class="col-lg-6 text-right"><a href="<?php echo $baseUri; ?>/dashboard/" class="btn btn-success" title="Back to Dashboard">Back</a></div>
                </div>
                    
          </div>

        </div>
        <!-- Warper Ends Here (working area) -->






    <script src="<?php echo $baseUri; ?>/assets/js/jquery/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="<?php echo $baseUri; ?>/assets/js/plugins/underscore/underscore-min.js"></script>
    <!-- Bootstrap -->
    <script src="<?php echo $baseUri; ?>/assets/js/bootstrap/bootstrap.min.js"></script>
    
    <!-- Globalize -->
    <script src="<?php echo $baseUri; ?>/assets/js/globalize/globalize.min.js"></script>
    
    <!-- NanoScroll -->
    <script src="<?php echo $baseUri; ?>/assets/js/plugins/nicescroll/jquery.nicescroll.min.js"></script>
    
  
    
    
    <!-- Custom JQuery -->
  <script src="<?php echo $baseUri; ?>/assets/js/app/custom.js" type="text/javascript"></script>

  <script src="<?php echo $baseUri; ?>/assets/js/application/ctrl.js" type="text/javascript"></script>
    

    
  
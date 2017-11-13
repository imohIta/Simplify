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
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,3,5), true);

$month = $session->read('irMonth') ? $session->read('irMonth') :  date('m');
$year = $session->read('irYear') ? $session->read('irYear') : date('Y');

if($session->read('irMonth')){
  $session->write('irMonth', null);
  $session->write('irYear', null);
}

$totalExpenses = 0;
 foreach (Impress::fetchExpensesForDateRange($month, $year) as $row) {
    $totalExpenses += $row->amt;
 }

 $totalPayIns = 0;
 foreach (Impress::fetchPayInsForDateRange($month, $year) as $row) {
    $totalPayIns += $row->amt;
 }

 $bal = Impress::fetchBalBroughtForward($year .'-'.$month.'-01');

if(empty($bal)){
  $bbf = 0;
}else{

  //$bffIndex = count($bal) - 1;
  if($bal->type == 1){
     $st = Impress::fetchPayInById($bal->typeId);
  }else{
      $st = Impress::fetchExpensesById($bal->typeId);
  }
  $bbf = $st->impressBal;
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
    
    <title>Kelvic Suite Impress Account Report</title>
    

    <div style="width:750px; margin:20px auto">

    <div class="warper container-fluid">
          
          
            
                <div class="page-header text-center"><h3 class="no-margn">Kelvic Suites <small>& Towers.</small></h3></div>
                <div class="page-header text-center"><h3 class="no-margn"><small>Plot 107, Area C, New Owerri, Imo State</small></h3></div>
                
                <!-- <hr class="dotted"> -->
                
                <div class="row">
                
                    <div class="col-md-12">
                    <div class="page-header text-center"><h3><?php echo date('F Y', strtotime($year . '-' . $month . '-01')); ?> Impress Account Report</h3>
                    </div>

                    

                    <div class="print">

                      <hr class="dotted" />

                    <form method="post" action="<?php echo $baseUri; ?>/auditor/impressAcctMgt">
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
                        <div class="panel-heading">&nbsp;</div>
                        <div class="panel-body table-responsive">
                        
                            <table class="table">
                                <tr>
                                  <td><?php echo date('F', strtotime("-1 months", strtotime($year .'-'.$month.'-01'))) . ' ' . $year; ?> Balance B/D</td>
                                  <td><?php echo number_format($bbf); ?></td>
                                </tr>
                                <tr>
                                  <td>Amount Recieved</td>
                                  <td><?php echo number_format($totalPayIns); ?></td>
                                </tr>
                                <tr>
                                  <td>Total Impress</td>
                                  <td><?php echo number_format($totalPayIns + $bbf); ?></td>
                                </tr>

                                <tr>
                                  <td>Total Expenditure</td>
                                  <td><?php echo number_format($totalExpenses); ?></td>
                                </tr>

                                <tr>
                                  <td>Balance</td>
                                  <td><?php echo number_format(($totalPayIns + $bbf) - $totalExpenses); ?></td>
                                </tr>

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
    

    
  
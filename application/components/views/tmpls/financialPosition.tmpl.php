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

$month = $session->read('fpMonth') ? $session->read('fpMonth') :  date('m');
$year = $session->read('fpYear') ? $session->read('fpYear') : date('Y');

if($session->read('fpMonth')){
  $session->write('fpMonth', null);
  $session->write('fpYear', null);
}

$bankLodgment = $pos = $bankTransfer = $chExp = $cheque = 0;
foreach ($registry->get('db')->fetchBankDepositsForDateRange($month, $year) as $key) {
  # code...
  $bankLodgment += $key->amt;
}

foreach (Transaction::fetchForFinancialReport($month, $year) as $row) {
  
  # if trasaction type is Payment
  $trans = new Transaction($row->transId);
  
  if ($trans->type == 2 || $trans->type == 10) { # guest Payment | Reservation payment
      
      
      $det = $trans->extractDetails();
      $d = json_decode($det->desc, true);


      switch (strtolower($d['Pay Type'])) {

        case 'cheque':
          # code...
          $cheque += $det->amt;
          break;

        case 'pos':
          # code...
          $pos += $det->amt;
          break;

        case 'bank transfer':
          # code...
          $bankTransfer = $det->amt;
          break;
        
      }

  }elseif($trans->type == 11){ # Chairman Expenses
      $chExp = $trans->extractDetails()->amt;
  }

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
    
    <title>Kelvic Suite Financial Position</title>
    

    <div style="width:750px; margin:20px auto">

    <div class="warper container-fluid">
          
          
            
                <div class="page-header text-center"><h3 class="no-margn">Kelvic Suites <small>& Towers.</small></h3></div>
                <div class="page-header text-center"><h3 class="no-margn"><small>Plot 107, Area C, New Owerri, Imo State</small></h3></div>
                
                <!-- <hr class="dotted"> -->
                
                <div class="row">
                
                    <div class="col-md-12">
                    <div class="page-header text-center"><h3><?php echo date('F Y', strtotime($year . '-' . $month . '-01')); ?> Financial Position</h3>
                    </div>

                    

                    <div class="print">

                      <hr class="dotted" />

                    <form method="post" action="<?php echo $baseUri; ?>/auditor/financialPosition">
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
                                  <td>Bank Lodgement</td>
                                  <td><?php echo number_format($bankLodgment); ?></td>
                                </tr>
                                <tr>
                                  <td>Pos</td>
                                  <td><?php echo number_format($pos); ?></td>
                                </tr>
                                <tr>
                                  <td>Direct Payment</td>
                                  <td><?php echo number_format($bankTransfer); ?></td>
                                </tr>

                                <tr>
                                  <td>Cheque</td>
                                  <td><?php echo number_format($cheque); ?></td>
                                </tr>

                                <tr>
                                  <td>Chairman Expenses</td>
                                  <td><?php echo number_format($chExp); ?></td>
                                </tr>

                                <tr>
                                  <td>Balance</td>
                                  <td><?php echo number_format(($bankLodgment + $bankTransfer + $pos + $cheque) - $chExp); ?></td>
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
    

    
  
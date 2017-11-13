<?php 
#check if user is logged in
global $today;

#if user not looged in
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}

#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

#if invioce not to be shown
if(!$session->read('showResInvioce')){
	$registry->get('uri')->redirect($baseUri . '/guest/checkInOptions');
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
    
    <title><?php echo $registry->get('config')->get('appTitle'); ?></title>
    

    <div style="width:600px; margin:20px auto">

    <div class="warper container-fluid">
        	
          
            
                <div class="page-header text-right"><h3 class="no-margn">Kelvic Suites <small>& Towers.</small></h3></div>
                
                <hr>
                
                <div class="row">
                

                    <table class="col-md-12" style="width:100%">
                    <tr>
                        <td class="col-md-6" align="left" style="width:50%">
                        <address>
                          <strong>Guest Information.</strong><br>
                          <?php echo ucfirst($session->read('invioceData')['guestName']); ?> <br>
                          
                          <?php echo $session->read('invioceData')['guestPhone']; ?>
                        </address>
                        
                        <address>
                          <strong>Reservation Dates</strong><br>
                          Begin Date : <?php echo dateToString($session->read('invioceData')['beginDate']); ?> <br>
                          End Date : <?php echo dateToString($session->read('invioceData')['endDate']); ?>
                        </address>
                        </td>
                        
                        <td class="col-md-6" style="width:50%" align="right">
                        <address>
                          Plot 107, Area C, New Owerri, <br>
                          Owerri, <br>
                          Imo State<br>
                          (+234 ) 80 - 3746 - 7126 
                        </address>
                        <dl>
                          <dt>Invoice Details</dt>
                          <dd>Invoice No. : <?php echo $session->read('invioceData')['revId']; ?></dd>
                          <dd>Date : <?php echo dateToString(today()); ?></dd>
                          <dd>Time : <?php echo timeToString(time()); ?></dd>
                        </dl>
                        </td>

                    </tr>
                    </table>
                
                </div>
                
                <div class="panel panel-default">
                    <div class="panel-body">
                        <table class="table table-striped">
                           <thead>
                                <tr>
                                    <th>Room Description</th>
                                    <!-- <th class="price">Total Room Charge</th> -->
                                    <th class="total">Amount</th>
                                </tr>
                            </thead>	

                            
                                
                            <tbody>
                                <tr>
                                    
                                    <td>
                                        <?php 
                                            foreach (json_decode($session->read('invioceData')['rooms2']) as $roomId => $roomNo ) {
                                                $room = new Room($roomId);
                                        ?>
                                        <p class="text-muted"><?php echo $room->no; ?> <!-- <span style="margin-left:20px"><?php echo number_format($room->price); ?></span> --></p>
                                    	<!-- <hr class="dotted" /> --> 
                                       <?php } ?>
                                    </td>
                                    <!-- <td class="price"></td> -->
                                    <td class="total"><?php echo number_format($session->read('invioceData')['amt']); ?></td>
                                </tr>
                                <?php

                                $total = $session->read('invioceData')['amt']; ?>
                                

                                <tr>
                                	<td><b>Total Amount</b></td>
                                	<td><b><?php echo number_format($total); ?></b></td>
                                </tr>
                             
                            </tbody>

                        
                        </table>
                    </div>
                </div>
                
                <div id="print" class="row">
                    <div class="col-lg-6"><button class="btn btn-warning" type="button" onclick="printInv('print');">Print Invoice</button></div>
                    <div class="col-lg-6 text-right"><a href="<?php echo $session->read('invioceData')['uri']; ?>" class="btn btn-success" title="Back to Reservations">Back</a></div>
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
    

    
	
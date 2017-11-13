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
if(!$session->read('showInvioce')){
	$registry->get('uri')->redirect($baseUri . '/guest/checkInOptions');
}

//var_dump($session->read('invioceData')); die;

?>

<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/css/bootstrap/bootstrap.css" /> 

	<!-- Calendar Styling  -->
    <!-- <link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/css/plugins/calendar/calendar.css" /> -->
    
    <!-- Fonts  -->
	<!--
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,500,600,700,300' rel='stylesheet' type='text/css'>
	-->
    
    <!-- Base Styling  -->
    <link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/css/app/app.v1.css" />

    <!-- <meta name="viewport" content="width=device-width, initial-scale=1" /> -->
    
    <title><?php echo $registry->get('config')->get('appTitle'); ?></title>
    

    <div style="width:600px; margin:20px auto">

    <div class="warper container-fluid">
        	
          
            
                <div class="page-header text-right"><h3 class="no-margn">Kelvic Suites <small>& Towers.</small></h3></div>
                
                <hr>
                
                <div class="row">
                
                    <!-- <div class="col-md-6">
                    
                        <address>
                          <strong>Guest Information.</strong><br>
                          <?php echo ucfirst($session->read('invioceData')['name']); ?> <br>
                          <?php echo $session->read('invioceData')['addr']; ?><br>
                          <?php echo $session->read('invioceData')['phone']; ?>
                        </address>
                        
                        <address>
                          <strong>Check In Information</strong><br>
                          Date : <?php echo dateToString($session->read('invioceData')['date']); ?> <br>
                          Time : <?php echo timeToString(time()); ?>
                        </address>
                        
                    </div>
                    
                    <div class="col-md-6 text-right">
                    
                        
                    	<address>
                          <strong>Kelvic Suites & Towers.</strong><br>
                          Plot 107, Area C, New Owerri, <br>
                          Imo State<br>
                          (+234 ) 80 - 3746 - 7126 
                        </address>
                        <dl>
                          <dt>Invoice Details</dt>
                          <dd>Invoice No. : <?php echo generateTransId(); ?></dd>
                        </dl>
                        
                        
                    </div> -->

                    <table class="col-md-12" style="width:100%">
                    <tr>
                        <td class="col-md-6" align="left" style="width:50%">
                        <address>
                          <strong>Guest Information.</strong><br>
                          <?php echo ucfirst($session->read('invioceData')['name']); ?> <br>
                          <?php echo $session->read('invioceData')['addr']; ?><br>
                          <?php echo $session->read('invioceData')['phone']; ?>
                        </address>
                        
                        <address>
                          <strong>Check In Information</strong><br>
                          Date : <?php echo dateToString($session->read('invioceData')['date']); ?> <br>
                          Time : <?php echo timeToString(time()); ?>
                        </address>
                        </td>
                        
                        <td class="col-md-6" style="width:50%" align="right">
                        <address>
                          Plot 107, Area C, New Owerri, <br>
                          Owerri, <br >
                          Imo State<br>
                          (+234 ) 80 - 3746 - 7126 
                        </address>
                        <dl>
                          <dt>Invoice Details</dt>
                          <dd>Invoice No. : <?php echo generateTransId(); ?></dd>
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
                                    <th>Description</th>
                                    <th class="price">Room Charge</th>
                                    <th class="total">Amount Paid</th>
                                </tr>
                            </thead>	

                            <?php
                            #if check in is routine 
                            if(strtolower($session->read('invioceType')) == 'routine'){

                            ?>
                                
                            <tbody>
                                <tr>
                                    <?php $room = new Room($session->read('invioceData')['roomNo']); ?>
                                    <td><small>Room Type :</small> <?php echo $room->type; ?><br />
                                    	<small>Room No. :</small> <?php echo $room->no; ?><br />
                                        <?php
                                            # if bill is flat rate
                                            if($session->read('invioceData')['discount'] != 101){

                                        ?>
                                                <small>Room Price :</small> <?php echo number_format($session->read('invioceData')['roomPrice']); ?> <br>
                                                <small>Discount :</small> <?php echo number_format($session->read('invioceData')['discount']); ?> % <br>
                                                <small>Check In Type :</small> <?php echo $session->read('invioceType'); ?>
                                        <?php } ?>

                                    </td>
                                    <td class="price"><?php echo number_format($session->read('invioceData')['bill']); ?></td>
                                    <td class="total"><?php echo number_format( $session->read('invioceData')['deposit1']); ?></td>
                                </tr>
                                <?php

                                $total = $session->read('invioceData')['deposit1']; ?>
                                <?php if(isset($session->read('invioceData')['useOB']) && $session->read('invioceData')['useOB'] == "yes"){ ?>
                                <tr>
                                	<td colspan="2">Outstanding Balance</td>
                                	<td><?php echo number_format($session->read('invioceData')['outBal']); ?></td>
                                </tr>


                                <?php $total += $session->read('invioceData')['outBal']; } ?>

                                <tr>
                                	<td colspan="2"><b>Total Amount</b></td>
                                	<td><b><?php echo number_format($total); ?></b></td>
                                </tr>
                             
                            </tbody>

                            <?php }else{ ?>

                            <tbody>
                                <tr>
                                    <?php $room = new Room($session->read('invioceData')['roomNo']); ?>
                                    <td>Room Type : <?php echo $room->type; ?><br />
                                    	Room No. : <?php echo $room->no; ?>
                                    	Room Price : <?php echo number_format($session->read('invioceData')['roomPrice']); ?> <br>
                                    	Check In Type : <?php echo $session->read('invioceType'); ?>
                                    </td>
                                    <td class="price">0</td>
                                    <td class="total">0</td>
                                </tr>
                                
                             
                            </tbody>


                            <?php } ?>

                        </table>
                    </div>
                </div>
                
                <div id="print" class="row">
                    <div class="col-lg-6"><button class="btn btn-warning" type="button" onclick="printInv('print');">Print Invoice</button></div>
                    <div class="col-lg-6 text-right"><a href="<?php echo $baseUri; ?>/guest/checkInOptions" class="btn btn-success" title="Back to Check In">Back</a></div>
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
    

    
	
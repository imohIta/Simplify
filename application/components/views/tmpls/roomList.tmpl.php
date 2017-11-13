<?php 

#if user not looged in
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}

#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');



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
    
    <title>Kelvic Suite Police Report</title>
    

    <div style="width:750px; margin:20px auto">

    <div class="warper container-fluid">
          
          
            
                <div class="page-header text-center"><h3 class="no-margn">Kelvic Suites <small>& Towers.</small></h3></div>
                <div class="page-header text-center"><h3 class="no-margn"><small>Plot 107, Area C, New Owerri, Imo State</small></h3></div>
                
                <hr class="dotted">
                
                <div class="row">
                
                    <div class="col-md-12">
                    <div class="page-header text-center"><h3>Rooms List <small>( <?php echo dateToString(today()); ?> )</small></h3>
                    </div>
                   
                    
                    <div class="panel panel-default">
                        <div class="panel-heading">&nbsp;</div>
                        <div class="panel-body table-responsive">
                        
                            <table class="table">
                                <thead>
                                  <tr>
                                    <td>SN</td>
                                    <td>Room No</td>
                                    <td>Type</td>
                                    <td>Status</td>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                  $counter = 1;
                                  foreach (Room::fetchAll() as $key) {
                                   $room = new Room($key->id);
                                    # code...
                                  ?>
                                  <tr>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo $room->no; ?></td>
                                    <td><?php echo $room->type; ?></td>
                                    <td><?php echo $room->status; ?></td>
                                  <tr>
                                  <?php $counter++; } ?>
                                  
                                </tbody>
                              </table>
                        
                        </div>
                    </div>
                </div>

             

              </div>
            
                
                <div id="print" class="row">
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
    

    
  
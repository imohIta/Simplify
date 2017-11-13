<?php 

#if user not looged in
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}

#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

# fetch rooms by type
for ($i=1; $i <= 8; $i++) { 
    # code...
    $name = 'roomType' . $i;
    $$name = Room::fetchByType($i);
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
    
    <title>Kelvic Suite Police Report</title>
    

    <div style="width:920px; margin:20px auto">

    <div class="warper container-fluid">
          
          
            
                <div class="page-header text-center"><h3 class="no-margn">Kelvic Suites <small>& Towers.</small></h3></div>
                <div class="page-header text-center"><h3 class="no-margn"><small>Plot 107, Area C, New Owerri, Imo State</small></h3></div>
                
                <hr>
                
                <div class="row">
                
                    <div class="col-md-12">
                    <div class="page-header"><h3>Room Status <small>( <?php echo dateToString(today()); ?> )</small></h3>
                    </div>

                    <p>KEY</p>
                    <table class="table table-bordered" style="width:400px">
                      <tr>
                        <td class="success">Routine</td>
                        <td class="warning">Complimentary</td>
                        <td class="info">Free</td>
                        <td class="danger">Bad</td>
                      </tr>
                    </table>
                   

                    
                    
                    <div class="panel panel-default">
                        <div class="panel-heading">&nbsp;</div>
                        <div class="panel-body table-responsive">
                        
                            <table class="table">
                                <thead>
                                  <tr>
                                    <td>KELVIC DELUX</td>
                                    <td>KELVIC ROYAL</td>
                                    <td>ROYAL DELUX</td>
                                    <td>EXECUTIVE DOUBLE</td>
                                    <td>FLORENCE SUITE</td>
                                    <td>MARGARET SUITE</td>
                                    <td>KELVIC SUITE</td>
                                    <td>CATHERINE SUITE</td>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>

                                <?php
                                    for ($i=1; $i <= 8; $i++) { 
                                       $name = 'roomType' . $i;
                                ?>
                                
                                    <td>
                                    <table class="table table-bordered">
                                       <?php
                                       foreach ($$name as $row) {
                                           # 
                                            $room = new Room($row->id);
                                            

                                            if($room->status == 'Free'){
                                                $class = 'info';
                                            }elseif($room->status ==  'Bad'){
                                                $class = 'danger';
                                            }else{
                                                if($room->checkInType == 'Routine'){
                                                    $class = 'success';
                                                }else{
                                                    $class = 'warning';
                                                }
                                            }
                                       
                                      ?>
                                      <tr class="<?php echo $class; ?>" style="margin:4px">
                                        <td align="center" style="margin:4px">
                                          
                                          <?php if($room->status == 'Occupied'){ 

                                            # build check In Info
                                            $info = Guest::getCheckInInfo($room->id);
                                            $msg = 'Guest Name : ' . $info->name;
                                            $msg .= ' ( Check In Date : ' . dateToString($info->checkInDate) . ' )';
                                            ?>
                                          
                                          <span class="popover-btn" data-container="body" title="Check-In Info" data-toggle="popover" data-placement="top" data-content="<?php echo $msg; ?>" style="cursor:pointer; width:100%; height:100%"><?php echo $room->no; ?></span>
                                          
                                          <?php }else{  echo $room->no;  } ?>
                                        
                                        </td>
                                      </tr>

                                      <?php } ?>
                                
                                  </table>  
                                  </td>                            
                                 
                                
                                <?php } ?>

                                </tr> 
                                  
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
    

    
  
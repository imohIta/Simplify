<?php 
#check if user is logged in
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}

$thisUser = unserialize($registry->get('session')->read('thisUser'));


#include header
$registry->get('includer')->render('header', array('css' => array(
																'font-awesome.min.css',
																)));

#include Sidebar
$registry->get('includer')->render('sidebar', array());


#include small header
$registry->get('includer')->renderWidget('smallHeader');

# Run guest autobill....Use javascript in ctrl.js to do this
Guest::autobill();

$nots = $thisUser->fetchNotifications(5);

# get occupied rooms 
$routine = array();
$complimentary = array();
$bad = Room::getBad();
$appReserved = Room::getAppReserved();
$webReserved =  Room::getWebReserved();
$dueReservations = array(); 

$free = Room::fetchFree();



foreach (Room::getOccupied() as $row) {
    # code...
    $room = new Room($row->roomId);
    if($room->checkInType == 'Routine'){
        $routine[] = $row;
    }elseif($room->checkInType == 'Complimentary'){
        $complimentary[] = $row;
    }
} 


foreach ($appReserved as $row) {
    # code...
    if(strtotime($row->rStartDate) <= strtotime(today()) && strtotime($row->rEndDate) >= strtotime(today())){
        $dueReservations[] = $row;
    }
    
}


    //try to update shiftTimes
    setShiftTimes();


?>
        
    
    
    
    <!-- Page Body here...Editable region -->
        
        <div class="warper container-fluid">
            
            <div class="page-header"><h1>Dashboard <small>( <?php echo $thisUser->role; ?> )</small></h1></div>
            
            <div class="row">
            
                <div class="col-md-6 col-lg-3">
                    <div class="panel panel-default clearfix dashboard-stats rounded" style="padding-left:106px">
                        <span id="dashboard-stats-sparkline3" class="sparkline transit"></span>
                        <i class="fa fa-certificate bg-success transit stats-icon"></i>
                        <h3 class="transit"><?php echo count($routine); ?></h3>
                        <p class="text-muted transit">Routine</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="panel panel-default clearfix dashboard-stats rounded" style="padding-left:106px">
                        <span id="dashboard-stats-sparkline4" class="sparkline transit"></span>
                        <i class="fa fa-desktop bg-warning transit stats-icon"></i>
                        <h3 class="transit"><?php echo count($complimentary); ?></h3>
                        <p class="text-muted transit">Complementary</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="panel panel-default clearfix dashboard-stats rounded" style="padding-left:106px">
                        <span id="dashboard-stats-sparkline2" class="sparkline transit"></span>
                        <i class="fa fa-bookmark-o bg-info transit stats-icon"></i>
                        <h3 class="transit"><?php echo count($free) - 2; ?> </h3>
                        <p class="text-muted transit">Free</p>
                    </div>
                </div>
                
                
                <div class="col-md-6 col-lg-3">
                    <div class="panel panel-default clearfix dashboard-stats rounded" style="padding-left:106px">
                        <span id="dashboard-stats-sparkline1" class="sparkline transit"></span>
                        <i class="fa fa-bell bg-danger transit stats-icon"></i>
                        <h3 class="transit"><?php echo count($appReserved); ?></h3>
                        <p class="text-muted transit">Reserved</p>
                    </div>
                </div>
            
            </div>


            <div class="row">
            
                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-heading">Reservations</div>
                        <div class="panel-body">

                        <h3 class="text-success">Online Reservations</h3>

                         <?php
                            $count = 1;
                            if(is_null($webReserved) || count($webReserved) < 1){
                                echo '<p> No Online Reservation Found';
                            }else{
                                foreach ($webReserved as $row) {
                            ?>

                            <p>You can use the mark tag to <mark>highlight</mark> text..</p>
                                   
                            <?php 
                                   
                            if($count != count($webReserved)){ echo '<hr class="dotted">'; }

                            $count++; } } ?>
                            
                            
                            <hr />
                        
                        <h3 class="text-primary">Due Reservations</h3>

                        <?php if(is_null($dueReservations) || count($dueReservations) < 1){
                            echo '<p>No Due Reservation</p>';
                        }else{ 
                        ?>
                        
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="basic-datatable">
                            <thead>
                                <tr>
                                    
                                    <th>Rev. Begin Date</th>
                                    <th>Rev. End Date</th>
                                    <th>Guest Name</th>
                                    <th>Room No</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $count = 1;
                              foreach ($dueReservations as $row) {
                               $room = new Room($row->roomId)
                              ?>
                                 <tr>
                                    
                                    <td><?php echo dateToString($row->rStartDate); ?></td>
                                    <td><?php echo dateToString($row->rEndDate); ?></td>
                                    <td><?php echo $row->guestName; ?></td>
                                    <td><?php echo $room->no; ?></td>
                                                    
                                </tr> 
                                <?php } ?>
                              </tbody>
                            </table>

                        <?php } ?>

                        
                        </div>
                    </div>
                    
                    
                    
                    
                </div>
                
                <div class="col-md-5">
                    
                    <div class="panel panel-default">
                        <div class="panel-heading">Notifications</div>
                        <div class="panel-body">

                            <?php if(is_null($nots) || count($nots) < 1){ ?>
                            
                                <p>No Notification Found</p>
                            
                            <?php }else{  ?>

                          <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="basic-datatable">
                            <thead>
                                <tr>
                                    
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Details</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $count = 1;
                              foreach ($nots as $row) {
                              ?>
                                 <tr>
                                    
                                    <td><?php echo dateToString($row->date); ?></td>
                                    <td><?php echo timeToString($row->time); ?></td>
                                    <td><?php echo $row->details; ?></td>
                              
                                                    
                                </tr> 
                                <?php } ?>
                              </tbody>
                            </table>
                        
                         
                            <?php 
                                } 
                            ?>
                            
                            
                        </div>
                    </div>
                    
                    
                    
                    

                </div>
            
            </div>
            
            
            
        </div>
        <!-- Warper Ends Here (working area) -->
        
        
        <?php  
            $registry->get('includer')->render('footer', array('js' => array(
                                                            'plugins/nicescroll/jquery.nicescroll.min.js',
                                                            'app/custom.js'
                                                            )));

            //'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>


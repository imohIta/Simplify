<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

#get room categories list
if(!$registry->get('session')->read('roomsCategoriesList')){
    $registry->get('session')->write('roomsCategoriesList', Room::getCategories());
}



	#include header
	$registry->get('includer')->render('header', array('css' => array(
                                  'plugins/typeahead/typeahead.css',
                                  'plugins/bootstrap-tagsinput/bootstrap-tagsinput.css',
                                  'plugins/bootstrap-chosen/chosen.css',
                                  'plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.css',
                                  'switch-buttons/switch-buttons.css',
                                  'font-awesome.min.css',
                                  )));

	#include Sidebar
	$registry->get('includer')->render('sidebar', array());


	#include small header
	$registry->get('includer')->renderWidget('smallHeader');
?>
    
	
    
    <!-- Page Body here...Editable region -->
        
        <div class="warper container-fluid">

          <div class="page-header">
              <h1>
                <small><a href="<?php echo $baseUri; ?>/guest/checkInOptions">Check In Options</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/guest/ComplimentaryCheckIn">Complimentary Check In</a></small>
              </h1>
          </div>
        	
            <div class="page-header"><h1>Check - In Guest <small style="color:#FF404B">( Complimentary )</small></h1></div>
            
             <div class="row">
            
                <div class="col-md-9">
                    <div class="panel panel-default">
                        <div class="panel-heading">&nbsp;</div>
                        <div class="panel-body">

                         <?php 
                             if($registry->get('session')->read('formMsg')){
                              echo $registry->get('session')->read('formMsg');
                              $registry->get('session')->write('formMsg', NULL);
                             }
                          ?>
                        
                            <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/guest/complimentaryCheckIn">

                              <input type="hidden" name="complimentary" value="yes" />

                                <div class="form-group">
                                 <!-- <label for="" class="col-sm-3 control-label"><span style="margin-top:10px">Guest Phones</span></label> -->
                                    <div class="col-sm-9 col-sm-offset-3">
                                        <div class="input-group">
                                          <input type="text" name="phone" placeholder="Guest Phone No." class="form-control form-control-circle" id="phone" autocomplete="off" value="<?php echo $session->read('phone') ? $session->read('phone') : ''; $session->write('phone', null); ?>">
                                          <span class="input-group-btn">
                                            <button class="btn btn-info" id="searchGuestBtn" type="button">Search</button>
                                          </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Error Holder -->                                
                                <div class="alert alert-info alert-dismissible" role="alert" id="errHolder" style="display:none">
                                  
                                  <strong>Info!</strong> <span id="errMsg"></span>. 
                                </div>
                                

                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Check-in Date</label>
                                <div class="col-sm-9">
                                  <input type="text" name="date" class="form-control form-control-circle inputmask" data-inputmask="'alias': 'yyyy-mm-dd'" placeholder="yyyy-mm-dd" readonly value="<?php echo $today; ?>" placeholder="">
                                </div>
                              </div>

                              <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Guest Name</label>
                                <div class="col-sm-9">
                                  <input type="text" name="name" id="name" class="form-control form-control-circle" value="<?php echo $session->read('name') ? $session->read('name') : ''; $session->write('name', null); ?>">
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Address</label>
                                <div class="col-sm-9">
                                  <input type="text" name="addr" id="addr" class="form-control form-control-circle" value="<?php echo $session->read('addr') ? $session->read('addr') : ''; $session->write('addr', null); ?>">
                                </div>
                              </div>

                              <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Occupation</label>
                                <div class="col-sm-9">
                                  <input type="text" name="occu" id="occu" class="form-control form-control-circle" id="inputPassword3" value="<?php echo $session->read('occu') ? $session->read('occu') : ''; $session->write('occu', null); ?>">
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Reason for Visit</label>
                                <div class="col-sm-9">
                                  <input type="text" name="reason" id="reason" class="form-control form-control-circle"  value="<?php echo $session->read('reason') ? $session->read('reason') : ''; $session->write('reason', null); ?>">
                                </div>
                              </div>

                              <div class="form-group">
                                    <label class="col-sm-3 control-label">Nationality</label>
                                    <div class="col-sm-3">
                                      <select class="form-control chosen-select form-control-circle" id="nationality" name="nationality" >
                                          <option></option>
                                          <option value="Nigerian" <?php if($session->read('nationality') && $session->read('nationality') == 'Nigerian'){ ?> selected <?php } $session->write('nationality', null) ?>>Nigerian</option>
                                          <option value="Non-Nigerian" <?php if($session->read('nationality') && $session->read('nationality') == 'Non Nigerian'){ ?> selected <?php } $session->write('nationality', null) ?>>Non Nigerian</option>
                                        </select>
                                    </div>
                              </div>
                              <div class="form-group">
                                    <label class="col-sm-3 control-label">No of Occupants</label>
                                    <div class="col-sm-3">
                                      <select class="form-control chosen-select form-control-circle" data-placeholder="" name="noOfOccupants">
                                        <?php if($session->read('noOfOccupants')){ ?>
                                        <option value="<?php echo $session->read('noOfOccupants'); ?>"> <?php echo $session->read('noOfOccupants'); ?> </option>
                                        <?php } $session->write('noOfOccupants', null); ?>
                                          <option></option>
                                          <option value="1" selected="selected">1</option>
                                          <option value="2">2</option>
                                          <option value="3">3</option>
                                          <option value="4">4</option>
                                          <option value="5">5+</option>
                                        </select>
                                    </div>
                              </div>
                              

                              <hr>

                              <div class="form-group">
                                    <label class="col-sm-3 control-label">Room Type</label>
                                    <div class="col-sm-3">
                                        <!-- onChange="getRoomByType(this.value)" -->
                                      <select class="form-control chosen-select" name="roomType" id="roomTypes" onChange="getRoomsByType(this.value)">
                                          <option></option>
                                          <?php
                                          foreach ($registry->get('session')->read('roomsCategoriesList') as $roomType) {
                                           ?>
                                          <option value="<?php echo $roomType->id; ?>"><?php echo $roomType->type; ?></option>
                                          <?php } ?>
                                        </select>
                                    </div>
                              </div>

                              <span id="roomsHolder"></span>

                              <span id="priceLoader" style="display:none"></span>

                              <span id="priceHolder" style="display:none">
                                    <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Room Price</label>
                                    <div class="col-sm-3">
                                      <input type="text" name="roomPrice" id="roomPrice" class="form-control form-control-circle" readonly placeholder="">
                                    </div>
                                  </div>
                              </span>

                             
                              <hr />

                             <input type="hidden" id="bill" class="form-control form-control-circle" readonly >

                              <span id="balHolder" style="display:none">
                               <input type="hidden" class="form-control form-control-circle" id="outBal" readonly >
                            
                              </span>

                             
                              <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                  <button type="submit" name="submit" class="btn btn-success">Check In</button>
                                </div>
                              </div>
                            </form>
                        
              
                        
                           
                        
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <!-- Warper Ends Here (working area) -->
        
        
        <?php  
        	$registry->get('includer')->render('footer', array('js' => array(
                                  'globalize/globalize.min.js',
                                  'plugins/nicescroll/jquery.nicescroll.min.js',
                                  'plugins/typehead/typeahead.bundle.js',
                                  'plugins/typehead/typeahead.bundle-conf.js',
                                  'plugins/inputmask/jquery.inputmask.bundle.js',
                                  'plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js',
                                  'plugins/bootstrap-chosen/chosen.jquery.js',
                                  'plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.js',
                                  'app/custom.js',
                                  'application/ctrl.js',
                                  'application/checkIn.js'
                                  )));

        	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>
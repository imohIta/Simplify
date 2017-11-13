<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | reception )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,5,6,7), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session'); 

/*if(!$session->read('occupiedRoomslist')){
   $session->write('occupiedRoomslist', $registry->get('db')->getOccupiedRooms());
}*/

$occupiedRooms = Room::getOccupied();

	#include header
	$registry->get('includer')->render('header', array('css' => array(
                                'plugins/typeahead/typeahead.css',
                                'plugins/bootstrap-tagsinput/bootstrap-tagsinput.css',
                                'plugins/bootstrap-chosen/chosen.css',
                                'plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.css',
                                'switch-buttons/switch-buttons.css',
                                'font-awesome.min.css'
                                )));

	#include Sidebar
	$registry->get('includer')->render('sidebar', array());


	#include small header
	$registry->get('includer')->renderWidget('smallHeader');
?>
    
    <div class="warper container-fluid">
          
            <div class="page-header"><h1>Add Chairman Expenses <small>&nbsp;</small></h1>
              <h1>
                <small><a href="<?php echo $baseUri; ?>/reception/chairmanExpensesOptions">Chairman Expenses Options</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/reception/chairmanExpenses">Add Chairman Expenses</a></small>
              </h1>

            </div>


             
            
            <div class="row">

              <div id="loader" style="width:120px; margin:40px auto; display:none"></div>
            
                
                <div class="col-md-8">

                  <?php 
                 if($registry->get('session')->read('formMsg')){
                  echo $registry->get('session')->read('formMsg');
                  $registry->get('session')->write('formMsg', NULL);
                 }
              ?>
                  
                    <div class="panel panel-default">
                        <div class="panel-heading">&nbsp;</div>
                        <div class="panel-body">
                        
                        <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/reception/chairmanExpenses">
                        
                        <input type="hidden" id="guestId" name="guestId" value="" />
                        <input type="hidden" id="roomId" name="roomId" value="" />

                        <div class="form-group">
                          <label for="inputEmail3" class="col-sm-3 control-label">Date</label>
                          <div class="col-sm-9">
                            <input type="text" name="date" placeholder="yyyy-mm-dd" class="form-control form-control-circle inputmask" data-inputmask="'alias': 'yyyy-mm-dd'" readonly value="<?php echo $today; ?>" placeholder="">
                          </div>
                        </div>

                        <!-- <div class="form-group">
                                    <label class="col-sm-3 control-label">Bill Type</label>
                                    <div class="col-sm-3">
                                      <select class="form-control" data-placeholder="" name="billType" style="width:130px">
                                          <option></option>
                                          <option value="1">Room Charge</option>
                                          <option value="2">2</option>
                                          <option value="3">3</option>
                                          <option value="4">4</option>
                                          <option value="5">5+</option> 
                                        </select>
                                    </div>
                              </div> -->

                        <div class="form-group">
                          <label for="inputEmail3" class="col-sm-3 control-label">Expenses Desc.</label>
                          <div class="col-sm-9">
                            <input type="text" name="desc" class="form-control form-control-circle" value="<?php echo $session->read('desc') ? $session->read('desc') : ''; $session->write('desc', null); ?>" autocomplete="off">
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="inputPassword3" class="col-sm-3 control-label">Amount</label>
                          <div class="col-sm-3">
                            <input type="text" name="amt1" id="amt1" value="<?php echo $session->read('amt1') ? $session->read('amt1') : ''; $session->write('amt1', null); ?>" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" autocomplete="off">
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="inputPassword3" class="col-sm-3 control-label">Confirm Amount</label>
                          <div class="col-sm-3">
                            <input type="text" name="amt2" id="amt2" value="<?php echo $session->read('amt2') ? $session->read('amt2') : ''; $session->write('amt2', null); ?>" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'"> 
                          </div>
                        </div>

                        <div class="form-group" style="padding-bottom:8px">
                          <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" name="submit" class="btn btn-success">Submit</button>
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
                                  'plugins/nicescroll/jquery.nicescroll.min.js',
                                  'plugins/typehead/typeahead.bundle.js',
                                  'plugins/typehead/typeahead.bundle-conf.js',
                                  'plugins/inputmask/jquery.inputmask.bundle.js',
                                  'plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js',
                                  'plugins/bootstrap-chosen/chosen.jquery.js',
                                  'moment/moment.js',
                                  'plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.js',
                                  'app/custom.js',
                                  'application/ctrl.js',
                                  'application/transactions.js'
                                  ))); 

        	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>
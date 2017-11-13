<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect(); 
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | Mgt Staff | reception )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5,6,7), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

$month = $session->read('ceMonth') ? $session->read('ceMonth') :  date('m');
$year = $session->read('ceYear') ? $session->read('ceYear') : date('Y');

if($session->read('ceMonth')){
  $session->write('ceMonth', null);
  $session->write('ceYear', null); 
}


# fetch reservations for this src
$log = $registry->get('db')->fetchChairmanExpenses(array('month' => $month, 'year' => $year));

//echo 'check'; die;
#include header
$registry->get('includer')->render('header', array('css' => array(
                                'plugins/typeahead/typeahead.css',
                                'plugins/bootstrap-tagsinput/bootstrap-tagsinput.css',
                                'plugins/bootstrap-chosen/chosen.css',
                                'switch-buttons/switch-buttons.css',
                                'plugins/datatables/jquery.dataTables.css',
                                'font-awesome.min.css'
                                )));

	#include Sidebar
	$registry->get('includer')->render('sidebar', array());


	#include small header
	$registry->get('includer')->renderWidget('smallHeader');
?>
    	
	
    
<!-- Page Body here...Editable region -->
    
    <div class="warper container-fluid" id="guestList">

      <div class="page-header">
        <h1>Chairman Expenses <small style="color:#FF404B">( <?php echo date('F Y', strtotime($year . '-' . $month . '-01')); ?> )</small></h1>
        
        <?php
        if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(7))){
        ?>
        <h1>
                <small><a href="<?php echo $baseUri; ?>/reception/chairmanExpensesOptions">Chairman Expenses Options</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/reception/chairmanExpensesLog">Chairman Expenses Log</a></small>
              </h1>
        <?php } ?>
      </div>
    	
        
        
         <div class="row">
        
            <div class="col-md-8">

              <div>

                  <hr class="dotted" />

                    <form method="post" action="<?php echo $baseUri; ?>/reception/chairmanExpensesLog">
                      <table style="width:400px; margin:0px auto">
                                <tr>
                                  <td style="width:150px" align="right"><p class="text-muted">Change Date</p></td>
                                  <td style="width:125px">
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
                                  <td style="width:125px">
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
                    <div class="panel-body">

                    
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="basic-datatable">
                            <thead>
                                <tr>
                                   <th>SN</th>
                                    <th>Date</th>
                                    <th>Details</th>
                                    <th>Added By</th>
                                    <th>Amount</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $count = 1;
                              $total = 0;
                              foreach ($log as $row) {
                                
                                $class = ( $count % 2 == 0 ) ? 'even' : 'odd';
                                $total += $row->amt;

                                $staff = new User($row->staffId);
                                $addedBy = $staff->id == $thisUser->id ? 'You' : $staff->name;
                              ?>
                                 <tr class="<?php echo $class; ?> gradeX">
                                    <td><?php echo $count; ?>.</td>
                                    <td><?php echo dateToString($row->date); ?></td>
                                    <td><?php echo $row->details; ?></td>
                                     <td><?php echo $addedBy; ?></td>
                                    <td><?php echo number_format($row->amt); ?></td>
                                            
                                </tr> 
                                <?php $count++; } ?>
                                <tr class="odd gradeX">
                                  <td colspan="4"><strong>Total</strong></td>
                                  <td><?php echo number_format($total); ?></td>
                                </tr>
                              </tbody>
                            </table>

                    
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
                              'plugins/datatables/jquery.dataTables.js',
                              'plugins/datatables/DT_bootstrap.js',
                              'plugins/datatables/jquery.dataTables-conf.js',
                              'app/custom.js',
                              'application/ctrl.js',
                              'application/reservation.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
    ?>
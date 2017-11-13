<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}

# This will fetch all Staff Debt that have not been fully paid for a particular month and year


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | accountant )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,5), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');


$year = $session->read('staffDebtYear') ? $session->read('staffDebtYear') : date('Y');
$month = $session->read('staffDebtMonth') ? $session->read('staffDebtMonth') : date('m');
$staff = $session->read('staffDebtStaff') ? $session->read('staffDebtStaff') : null;

#fetch all unposted Sales 
if($session->read('staffDebtMonth')){
  $session->write('staffDebtMonth', null);
  $session->write('staffDebtYear', null);
  $session->write('staffDebtStaff', null);
}

$credits = $registry->get('db')->fetchStaffCredits(array(
      'staffId' => $staff,
      'month' => $month,
      'year' => $year
      ));

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
    
    <div class="warper container-fluid" >

      <div class="page-header">
        <h1>Staff Credits Log<small style="color:#FF404B">&nbsp;</small></h1>
      </div>
    	
        <hr />
        
         <div class="row">
        
            <div class="col-md-11">

              

              <form method="post" action="<?php echo $baseUri; ?>/credits/staffCreditsLog">
              
               <div class="col-sm-4">
                  <select class="form-control chosen-select form-control-circle" data-placeholder="" name="staff">
                      <option value="">All</option>
                    <?php foreach (Staff::fetchAll() as $row) {
                        # code...
                        $s = new Staff($row->id);
                        $selected = ($staff == $s->id) ? 'selected' : '';
                      ?>
                      
                      <option value="<?php echo $s->id; ?>" <?php echo $selected; ?>><?php echo $s->name; ?> ( <?php
                              echo $s->dept; ?> )</option>
                    <?php } ?>
                    </select>
                    <span class="help-block"><small>&nbsp;&nbsp; Staff</small></span>
                </div>

                <div class="col-sm-2">
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
                    <span class="help-block"><small>&nbsp;&nbsp; Month</small></span>
                </div>

                <div class="col-sm-2">
                  <select name="year" data-placeholder="Month" class="chosen-select">
                      <option value=""></option>
                      <?php
                        
                        for ($i = '2015'; $i <= date('Y'); $i++ ) { 
                          # code...
                          $selected = ($year == $i) ? 'selected' : '';
                      ?>
                      <option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>

                      <?php } ?>
                    </select>
                    <span class="help-block"><small>&nbsp;&nbsp; Year</small></span>
                </div>

                <div class="col-sm-2">
                  <button type="submit" name="search" class="btn btn-warning btn-circle" >Search</button>
                  <span class="help-block"><small>&nbsp;</small></span>
                </div>
              </form>
            
                <br style="clear:both" /><br />
                
                <div id="h" >

                  <!-- Write here -->

                <div class="panel panel-default">
                    <div class="panel-heading">&nbsp;</div>
                    <div class="panel-body">
                    
                     <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="basic-datatable">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Date</th>
                                    <th>Staff Name</th>
                                    <th>Purchase Details</th>
                                    <th>Dept | Seller</th>
                                    <th>Amount</th>
                                    <th>Payment Made</th>
                                    <th>Amount Owed</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $totalCredit = 0;
                              if(count($credits) > 0){
                                $count = 1;
                                foreach ($credits as $row) {
                                  
                                  
                                  # fetch payment made for this purchase using the transId
                                  $payments = 0;
                                  foreach ($registry->get('db')->fetchStaffCreditPaymentsByTransId($row->transId) as $p) {
                                    # code...
                                    $payments += $p->amt;
                                  }

                                  # if the purchase cost is greater than the total Payment for the purchase
                                  if($row->amt > $payments){

                                  $totalCredit += $row->amt;
                                  # code...
                                   $class = ( $count % 2 == 0 ) ? 'even' : 'odd';
                                   $debtor = new Staff($row->staffId);
                                   $seller = new Staff($row->seller);

                                 ?>
                                 <tr class="<?php echo $class; ?> gradeX">
                                  <td><?php echo $count; ?></td>
                                  <td><?php echo dateToString($row->date); ?></td>
                                  <td><?php echo $debtor->name; ?></td>
                                  <td>
                                    <table style="width:100%">
                                    <?php 
                                      foreach (json_decode($row->details) as $key) {
                                        # code...
                                        $item = $key->objectType == 1 ? new Menu($key->objectId) : new Item($key->objectId);
                                      ?>
                                      <tr>
                                          <td><?php echo $key->qty; ?></td>
                                          <td><?php echo $item->name; ?></td>
                                          <td><?php echo number_format($key->price); ?></td>
                                      </tr>
                                      <?php } ?>
                                    </table>
                                  </td>
                                  <td><?php echo User::getRole($row->dept) . ' | ' . $seller->name; ?></td>
                                  <td><?php echo number_format($row->amt); ?></td>
                                  <td><?php echo number_format($payments); ?></td>
                                  <td><?php echo number_format($row->amt - $payments); ?></td>
                                  </tr>
                                <?php
                                    $count++;
                                  } }

                                  if($totalCredit != 0){
                                ?>

                                <tr class="even gradeX">
                                  <td colspan="7">Total Amount Owed</td>
                                  <td><?php echo number_format($totalCredit); ?></td>
                                </tr>
                              

                              <?php } } ?>
                              
                            </tbody>
                            </table>

                    
                    </div>
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
                              'application/ctrl.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
    ?>
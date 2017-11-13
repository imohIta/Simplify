<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
} 


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | Mgt Staff  )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,5,6), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

$date = $session->read('impressDate') ? $session->read('impressDate') : today();
$log = Impress::fetchTrend($date);

if($session->read('impressDate')){
  $session->write('impressDate', null);
}

/*if(count($log) > 0){
  if($log[0]->type == 1){
   $st = Impress::fetchPayInById($log[0]->typeId);
   $bbf = ($st->impressBal - $st->amt);
  }else{
    $st = Impress::fetchExpensesById($log[0]->typeId);
    $bbf = ($st->impressBal + $st->amt);
  }
}*/


# fetch balance brought forward
$bal = Impress::fetchBalBroughtForward($date);

if(empty($bal)){
  $bbf = 0;
}else{

  //$bffIndex = count($bal) - 1;
  if($bal->type == 1){
     $st = Impress::fetchPayInById($bal->typeId);
  }else{
      $st = Impress::fetchExpensesById($bal->typeId);
  }
  $bbf = $st->impressBal;
}


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
        <h1>Impress Expenses Log <small style="color:#FF404B">&nbsp;</small></h1>
      </div>
    	
        
        
         <div class="row">
        
            <div class="col-md-10">

              <form method="post" action="<?php echo $baseUri; ?>/impress/"/>
              <div class="form-group">
              <label for="inputEmail3" class="col-sm-2 control-label">&nbsp;</label>
                <div class="col-sm-6">
                    <div class="input-group">
                      <input type="text" placeholder="yyyy-mm-dd" class="form-control form-control-circle inputmask" data-inputmask="'alias': 'yyyy-mm-dd'" name="date" value="<?php echo $date; ?>" />
                      <span class="input-group-btn">
                        <button class="btn btn-warning btn-circle"  type="submit" name="search" >Search By Date</button>
                      </span>
                    </div>
                </div>
             </div>
           </form>

            <br />
             <hr class="dotted">

                <div class="panel panel-default">
                    <div class="panel-heading">
                      <?php 
                      if(isset($bbf)){
                        echo '<h3>Balance Brought Forward : ' . number_format($bbf) . '</h3>';
                      }else{
                        echo '&nbsp;';
                      }

                      ?>

                      
                  </div>
                    <div class="panel-body">

                    
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="basic-datatable">
                            <thead>
                                <tr>
                                   <th>SN</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Details</th>
                                    <th>Amount</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                              <?php
                              $count = 1;
                              foreach ($log as $row) {

                                      $class = ( $count % 2 == 0 ) ? 'even' : 'odd';
                                
                                
                                      if($row->type == 1){

                                        $st = Impress::fetchPayInById($row->typeId);
                                        $type = 'Pay-In';
                                        $details = 'Source : ' . $st->src;

                                      }else{
                                        $st = Impress::fetchExpensesById($row->typeId);
                                        $type = 'Expenses';
                                        $details = $st->details;
                                      }
                                      ?>
                                      <tr class="<?php echo $class; ?> gradeX">
                                        <td><?php echo $count; ?>.</td>
                                        <td><?php echo dateToString($st->date); ?></td>
                                        <td><?php echo $type; ?></td>
                                        <td><?php echo $details ?></td>
                                        <td><?php echo number_format($st->amt); ?></td>
                                        <td><?php echo number_format($st->impressBal); ?></td>
                                       
                                      </tr> 
                              
                              <?php   $count++; }  ?>

                               
                              
                              </tbody>
                            </table>

                   
                    <h3>Total Pay-Ins : <?php echo number_format(Impress::totalPayIns($date)); ?></h3>
                    <h3>Total Exepenses : <?php echo number_format( Impress::totalExpenses($date)); ?></h3>
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
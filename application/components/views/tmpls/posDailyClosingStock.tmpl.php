<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | manager | Auditor | cashier | duty Manager )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,6), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

//$session->write('closingStk', null);
$date = today();

#fetch all unposted Sales 
if($session->read('closingStk')){
  $closingStk = unserialize($session->read('closingStk'));
  $session->write('closingStk', null);
  $date = $closingStk['date'];
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
    
    <div class="warper container-fluid" >

      <div class="page-header">
        <h1>POS Units Daily Closing Stock<small style="color:#FF404B">&nbsp;</small></h1>
      </div>
    	
        <hr />
        
         <div class="row">
        
            <div class="col-md-8">

              <?php 
                 if($registry->get('session')->read('formMsg')){
                  echo $registry->get('session')->read('formMsg');
                  $registry->get('session')->write('formMsg', NULL);
                 }
              ?>

              <form method="post" action="<?php echo $baseUri; ?>/stock/posDailyClosingStock">
              <div class="col-sm-4">
                <input type="text" name="date" class="form-control form-control-circle inputmask" data-inputmask="'alias': 'yyyy-mm-dd'" placeholder="yyyy-mm-dd" value="<?php echo today(); ?>" placeholder="">
                <span class="help-block"><small>&nbsp;&nbsp; Date</small></span>
              </div>

               <div class="col-sm-4">
                  <select class="form-control chosen-select form-control-circle" data-placeholder="" name="dept">
                      <option value="0"></option>
                      <option value="8">Pool Bar</option>
                      <option value="9">Main Bar</option>
                      <option value="10">Resturant</option>
                      <option value="11">Resturant Drinks</option>
                    </select>
                    <span class="help-block"><small>&nbsp;&nbsp; Department</small></span>
                </div>

                <div class="col-sm-3">
                  <button type="submit" name="submit" class="btn btn-warning btn-circle" id="sortStockBtn">Search</button>
                  <span class="help-block"><small>&nbsp;</small></span>
                </div>
              </form>
            
                <br /><br />
                
                <div id="h" >

                  <br />

                  <?php if(isset($closingStk)){ ?>

                  <h3><small>Department: </small><?php echo $closingStk['dept']; ?></h3>
                  <h3><small>Date: </small><?php echo dateToString($closingStk['date']); ?></h3></td>
                  <h3><small>Closed By: </small><?php echo $closingStk['closedBy']; ?></h3>
                  
                  <?php } ?>
                  <br />

                <div class="panel panel-default" id="holder">
                    <div class="panel-heading">&nbsp;</div>
                    <div class="panel-body">
                    
                     <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="basic-datatable">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Item Name</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody id="stkHolder">
                              <?php 
                              if(isset($closingStk)){
                                $count = 1;
                                foreach ($closingStk['stock'] as $row) {
                                  # code...
                                   $class = ( $count % 2 == 0 ) ? 'even' : 'odd';
                              ?>
                               <tr class="<?php echo $class; ?> gradeX">
                                <td><?php echo $count; ?></td>
                                <td><?php echo $row['itemName']; ?></td>
                                <td><?php echo $row['qty']; ?></td>
                                </tr>
                              <?php
                                  $count++;
                                }
                              
                              }
                              ?>
                              
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
                              'application/ctrl.js',
                              'application/stock.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
    ?>
<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | manager | Auditor | cashier | duty Manager )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,8,9,10,11), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');



$closingStk = $registry->get('db')->fetchOpeningStock(array('staffId' => $thisUser->id, 'privilege' => $thisUser->get('activeAcct')));
//echo $closingStk->stock; die;
//var_dump($closingStk); die;


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
        <h1>Opening Stock<small style="color:#FF404B">&nbsp;</small></h1>
      </div>
    	
        <hr />
        
         <div class="row">
        
            <div class="col-md-8">

              
                <div id="h" >

                  <br />

                  <?php
                   if($closingStk){

                    $stockCloser = new Staff($closingStk->staffId);
                  ?>
                 
                  <h3><small>Date: </small><?php echo dateToString($closingStk->date); ?></h3></td>
                  <h3><small>Time: </small><?php echo timeToString($closingStk->time); ?></h3></td>
                  <h3><small>Closed By: </small><?php echo $stockCloser->name; ?></h3>
                  
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
                              if($closingStk){
                                $count = 1;
                                foreach (json_decode($closingStk->stock, true) as $key => $value) {
                                  # code...
                                  $item = new Item($key);
                                  $class = ( $count % 2 == 0 ) ? 'even' : 'odd';
                              ?>
                               <tr class="<?php echo $class; ?> gradeX">
                                <td><?php echo $count; ?></td>
                                <td><?php echo $item->name; ?></td>
                                <td><?php echo $value; ?></td>
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
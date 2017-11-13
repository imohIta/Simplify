<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | Mgt Staff | reception )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5,13), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

$beginDate = $session->read('beginDate') ? $session->read('beginDate') : '';
$endDate = $session->read('endDate') ? $session->read('endDate') : '';
$month = $session->read('month') ? $session->read('month') : date('m');

$session->write('beginDate', null);
$session->write('endDate', null);
$session->write('month', null);

# fetch reservations for this src
$requisitions = $registry->get('db')->fetchIssuedRequisitions($beginDate, $endDate);



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
        <h1>Issued Requisitions</h1>
      </div>
      
        
         <div class="row">
        
            <div class="col-md-8">

              <?php 
                 if($registry->get('session')->read('formMsg')){
                  echo $registry->get('session')->read('formMsg');
                  $registry->get('session')->write('formMsg', NULL);
                 }
              ?>

              <hr class="dotted">

              <form method="post" action="<?php echo $baseUri; ?>/requisition/issued">
              <table style="width:400px;">
                        <tr>
                          <td style="width:120px" align="right"><p class="text-muted">Sort By</p></td>
                          <td style="width:160px">
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
                          <td style="width:240px">
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

               <hr class="dotted">

                <div class="panel panel-default">
                    <div class="panel-heading">&nbsp;</div>
                    <div class="panel-body">

                    
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="basic-datatable">
                            <thead>
                                <tr>
                                   <th>SN</th>
                                   <th>Date</th>
                                   <th>Time</th>
                                    <th>Item Name</th>
                                    <th>Qty</th>
                                    <th>Issued To</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $count = 1;
                              foreach ($requisitions as $row) {
                                
                                # code...
                                $item = new Item($row->itemId);
                                
                                $class = ( $count % 2 == 0 ) ? 'even' : 'odd';
                                $user = new Staff($row->staffId);
                                $dept = User::getRole($row->privilege);

                              ?>
                                 <tr class="<?php echo $class; ?> gradeX">
                                    <td><?php echo $count; ?>.</td>
                                    <td><?php echo dateToString($row->date); ?></td>
                                    <td><?php echo timeToString($row->time); ?></td>
                                    <td><?php echo $item->name; ?></td>
                                    <td><?php echo $row->qty; ?></td>
                                    <td><?php echo $user->name . ' ( ' . $dept . ' )'; ?></td>
                                </tr> 
                                <?php $count++; } ?>
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
                              'application/requisition.js'
                              )));

      //'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
    ?>
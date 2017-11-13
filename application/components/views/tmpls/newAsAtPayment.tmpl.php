<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | reception )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,6), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');
 
$users = User::fetchAll(false);

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
            <h1>Department Credit Payment <small> ( As-At Payment )</small> </h1>
            <h1>
                <small><a href="<?php echo $baseUri; ?>/cashier/asAtOptions">As-At Options</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/cashier/asAtPayment">New Credit Payments</a></small>
              </h1>
          </div>
          
            
            
             <div class="row">
            
                <div class="col-md-7">

                  <?php 
                       if($registry->get('session')->read('formMsg')){
                        echo $registry->get('session')->read('formMsg');
                        $registry->get('session')->write('formMsg', NULL);
                       }
                    ?>
                          
                    <div class="panel panel-default">
                        <div class="panel-heading">&nbsp;</div>
                        <div class="panel-body">

                            <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/cashier/asAtPayment">

                              <div class="form-group">
                                    <label class="col-sm-3 control-label">Department</label>
                                    <div class="col-sm-5">
                                      <select class="form-control chosen-select" id="dept" name="priv" required>
                                          <option value="0"></option>
                                          <option value="7">Reception</option>
                                          <option value="8">Pool Bar</option>
                                          <option value="9">Main Bar</option>
                                          <option value="10">Resturant</option>
                                          <option value="11">Resturant Drinks</option>
                                        </select>
                                    </div>
                              </div>

                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Credit Date</label>
                                <div class="col-sm-6">
                                  <div class="input-group">
                                    <input type="text" id="date" name="creditDate" class="form-control form-control-circle inputmask" data-inputmask="'alias': 'yyyy-mm-dd'" placeholder="yyyy-mm-dd" required>
                                      <span class="input-group-btn">
                                          <button class="btn btn-warning btn-circle" id="searchCreditBtn" type="button">Search</button>
                                      </span>
                                    </div>
                                </div>
                              </div>

                              <!-- Error Holder -->                                
                                <div class="alert alert-info alert-dismissible" role="alert" id="errHolder" style="display:none">
                                  
                                  <strong>Info!</strong> <span id="errMsg"></span>. 
                                </div>
                             
                              

                              <span id="others" style="display:none">

                                    <input type="hidden" name="amtDue" id="amtDue" />
                                    <input type="hidden" name="transId" id="transId" />

                                    <div class="form-group">
                                      <label for="inputPassword3" class="col-sm-3 control-label">Credit</label>
                                      <div class="col-sm-4">
                                        <input type="text" id="amtDue2" class="form-control form-control-circle" readonly >
                                      </div>
                                    </div>

                                    <div class="form-group">
                                      <label for="inputPassword3" class="col-sm-3 control-label">Amount Paid</label>
                                      <div class="col-sm-4">
                                        <input type="text" name="amt1" id="amt1" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" autocomplete="off" >
                                      </div>
                                    </div>

                                    <div class="form-group">
                                      <label for="inputPassword3" class="col-sm-3 control-label">Confirm Amount Paid</label>
                                      <div class="col-sm-4">
                                        <input type="text" name="amt2" id="amt2" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" autocomplete="off"> 
                                      </div>
                                    </div>

                                    <div class="form-group">
                                      <label for="inputPassword3" class="col-sm-3 control-label">Staff Name</label>
                                      <div class="col-sm-7">
                                        <select class="form-control" style="width:230px" name="staffId" required >
                                          <option value="0"></option>
                                          <?php foreach ($users as $u) {
                                            # code...
                                            $user = new Staff($u->id);
                                          ?>
                                          <option value="<?php echo $user->id; ?>"><?php echo $user->name . ' ( ' . $user->role .' ) '; ?></option>
                                        <?php } ?>
                                        </select>
                                      </div>
                                    </div>

                                    <div class="form-group">
                                      <div class="col-sm-offset-3 col-sm-9">
                                        <button type="submit" name="submit" class="btn btn-success btn-circle">Submit</button>
                                      </div>
                                    </div>

                              </span>
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
                                  'application/cashier.js'
                                  )));

          //'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>
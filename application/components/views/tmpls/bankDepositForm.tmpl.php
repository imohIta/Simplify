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
            <h1>Add Bank Payment </h1>
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

                            <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/cashier/newBankDeposit">

                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Payment Date</label>
                                <div class="col-sm-6">
                                  <input type="text" name="date" class="form-control form-control-circle inputmask" data-inputmask="'alias': 'yyyy-mm-dd'" placeholder="yyyy-mm-dd" value="<?php echo today(); ?>" placeholder="" required>
                                </div>
                              </div>
                             
                              <div class="form-group">
                                    <label class="col-sm-3 control-label">Bank</label>
                                    <div class="col-sm-6">
                                      <select class="form-control chosen-select" name="bank"  required>
                                         
                                          <option></option>
                                          <option value="Access Bank Plc">Access Bank Plc</option> 
                                          <option value="Citibank Nigeria Limited">Citibank Nigeria Limited</option> 
                                          <option value="Diamond Bank Plc">Diamond Bank Plc</option> 
                                          <option value="Ecobank Nigeria Plc">Ecobank Nigeria Plc</option> 
                                          <option value="Enterprise Bank">Enterprise Bank</option>  
                                          <option value="Fidelity Bank Plc">Fidelity Bank Plc</option> 
                                          <option value="First Bank of Nigeria Plc">First Bank of Nigeria Plc</option> 
                                          <option value="First City Monument Bank Plc">First City Monument Bank Plc</option> 
                                          <option value="Guaranty Trust Bank Plc">Guaranty Trust Bank Plc</option> 
                                          <option value="Heritage Banking Company Ltd">Heritage Banking Company Ltd</option> 
                                          <option value="Key Stone Bank">Key Stone Bank</option> 
                                          <option value="MainStreet Bank">MainStreet Bank</option> 
                                          <option value="Skye Bank Plc">Skye Bank Plc</option> 
                                          <option value="Stanbic IBTC Bank Ltd">Stanbic IBTC Bank Ltd</option> 
                                          <option value="Standard Chartered Bank Nigeria Ltd">Standard Chartered Bank Nigeria Ltd</option> 
                                          <option value="Sterling Bank Plc">Sterling Bank Plc</option> 
                                          <option value="Union Bank of Nigeria Plc">Union Bank of Nigeria Plc</option> 
                                          <option value="United Bank For Africa Plc">United Bank For Africa Plc</option> 
                                          <option value="Unity Bank Plc">Unity Bank Plc</option> 
                                          <option value="Wema Bank Plc">Wema Bank Plc</option> 
                                          <option value="Zenith Bank Plc">Zenith Bank Plc</option>
                                        </select>
                                    </div>
                              </div>

                              <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Amount</label>
                                <div class="col-sm-5">
                                  <input type="text" name="amt1" id="amt1" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" autocomplete="off" >
                                </div>
                              </div>

                              <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Confirm Amount</label>
                                <div class="col-sm-5">
                                  <input type="text" name="amt2" id="amt2" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" autocomplete="off"> 
                                </div>
                              </div>

                              <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                  <button type="submit" name="submit" class="btn btn-success btn-circle">Submit</button>
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
                                  'application/ctrl.js'
                                  )));

          //'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>
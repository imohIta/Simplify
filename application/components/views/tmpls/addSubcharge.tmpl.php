<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect(); 
}
 

#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | Accountant )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,5), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');



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
          
            <div class="page-header">
                <h1>Add Staff Subcharge <small>&nbsp;</small></h1>
                <h1>
                    <small><a href="<?php echo $baseUri; ?>/credits/subchargesOption">Subcharges Options</a></small>
                    <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                    <small><a href="<?php echo $baseUri; ?>/credits/addSubcharge">Add New Subcharge</a></small>
                </h1>
            </div>
            
            <div class="row">
            
                <div class="col-md-6">

                   <?php 
                       if($registry->get('session')->read('formMsg')){
                        echo $registry->get('session')->read('formMsg');
                        $registry->get('session')->write('formMsg', NULL);
                       }
                    ?>
                          
                    <div class="panel panel-default">
                        <div class="panel-heading">&nbsp;</div>
                        <div class="panel-body">


                        
                            <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri;
                            ?>/credits/addSubcharge">

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Date</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="date" class="form-control form-control-circle inputmask" data-inputmask="'alias': 'yyyy-mm-dd'" placeholder="yyyy-mm-dd" value="<?php echo today(); ?>" placeholder="" required >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Staff Name</label>
                                    <div class="col-sm-5">
                                        <select class="form-control chosen-select form-control-circle"  name="staffId" >
                                            <option></option>
                                            <?php
                                            foreach (Staff::fetchAll() as $row) {
                                                $staff = new Staff($row->id);
                                            ?>

                                            <option value="<?php echo $staff->id; ?>"><?php echo $staff->name;
                                                ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>




                              <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Amount</label>
                                <div class="col-sm-4">
                                  <input type="text" name="amt1" id="amt1" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" autocomplete="off" >
                                </div>
                              </div>

                              <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Confirm Amount</label>
                                <div class="col-sm-4">
                                  <input type="text" name="amt2" id="amt2" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" autocomplete="off"> 
                                </div>
                              </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Reason For Subcharge</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="reason" class="form-control form-control-circle"
                                               autocomplete="off" required>

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
                                  'application/ctrl.js',
                                  )));

          //'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>
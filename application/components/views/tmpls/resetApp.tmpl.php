<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | manager  )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1), true);

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
          
            <div class="page-header"><h1>Reset App<small>&nbsp;</small></h1>
            </div>


             
            
            <div class="row">

             
            
                
                <div class="col-md-9">

                  <?php 
                     if($registry->get('session')->read('formMsg')){
                      echo $registry->get('session')->read('formMsg');
                      $registry->get('session')->write('formMsg', NULL);
                     }
                  ?>
                  
                    <div class="panel panel-default">
                        <div class="panel-heading">&nbsp;</div>
                        <div class="panel-body">

                         <p class="text-danger"><strong>Warning :</strong> Resetting App will Restore the App to Factory Setting... All Data will be Lost ( There is no back Up whatsoever..Atleast for Now)...Please be sure you are not Drunk before you proceeding</p> 

                         <hr class="dotted" />
                        
                        <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/admin/resetApp">

                        
                        <div class="form-group has-feedback">
                            <label class="col-sm-3 control-label">Reset Password</label>
                            <div class="col-sm-9">
                              <input type="password" id="pwd" name="pwd" class="form-control form-control-circle" required>
                              <i class="fa fa-eye-slash panel-icon form-control-feedback" onclick="toggleInputType('pwd')" style="cursor:pointer" title="Toggle View"></i>
                            </div>
                        </div>

                        <div class="form-group has-feedback">
                            <label class="col-sm-3 control-label">Confirm Reset Password</label>
                            <div class="col-sm-9">
                              <input type="password" id="pwd2" name="pwd2" class="form-control form-control-circle" required >
                              <i class="fa fa-eye-slash panel-icon form-control-feedback" onclick="toggleInputType('pwd2')" style="cursor:pointer" title="Toggle View"></i>
                            </div>
                          </div>

                        <div class="form-group" style="padding-bottom:8px">
                          <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" name="submit" class="btn btn-danger btn-circle">Reset App</button>
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
                                  'application/ctrl.js'
                                  ))); 

        	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>
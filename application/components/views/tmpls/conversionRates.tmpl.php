<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
} 


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | purchaser )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,14), true);

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
          
            <div class="page-header"><h1>Items Conversion rates<small>&nbsp;</small></h1></div>
            
            <div class="row">
            
                <div class="col-md-5">
                  <div class="panel panel-default">
                        <div class="panel-heading">&nbsp;</div>
                        <div class="panel-body">
                        
                          <table class="table no-margn">
                              
                              <tbody>
                                <tr>
                                  <td align="right"><p class="text-muted">1 Cartoon of Indomie :</p></td>
                                  <td><p class="text-muted">26 Portions</p></td>
                                </tr>

                                <tr>
                                  <td align="right"><p class="text-muted">1 Kg of Rice :</p></td>
                                  <td><p class="text-muted">7 Portions</p></td>
                                </tr>

                                <tr>
                                  <td align="right"><p class="text-muted">1 Bag of Semovita :</p></td>
                                  <td><p class="text-muted">37 Portions</p></td>
                                </tr>

                                <tr>
                                  <td align="right"><p class="text-muted">1 Bag of Wheat :</p></td>
                                  <td><p class="text-muted">37 Portions</p></td>
                                </tr>

                                <tr>
                                  <td align="right"><p class="text-muted">2 Pieces of Plantain :</p></td>
                                  <td><p class="text-muted">2 Portions</p></td>
                                </tr>

                                <tr>
                                  <td align="right"><p class="text-muted">1 Bag of Poundo Yam :</p></td>
                                  <td><p class="text-muted">7 Portions</p></td>
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
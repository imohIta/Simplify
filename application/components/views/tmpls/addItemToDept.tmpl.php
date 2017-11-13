<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | reception )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,4,5), true);

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
          
            <div class="page-header"><h1>Add New Item to Dept Stock <small>&nbsp;</small></h1>
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
                        
                        <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/stock/addItemToDept">  
                        
                        <div class="form-group">
                              <label class="col-sm-3 control-label">Item</label>
                              <div class="col-sm-4">
                                <select class="form-control chosen-select" data-placeholder="" name="item" required >
                                    <option></option>
                                    <?php
                                    foreach (Item::fetchAll('store') as $key) {
                                      # code...
                                      $item = new Item($key->itemId);
                                    ?>
                                    
                                    <option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
                                    
                                    <?php } ?>

                                  </select>
                              </div>
                        </div>

                        <div class="form-group">
                              <label class="col-sm-3 control-label">Department</label>
                              <div class="col-sm-4">
                                <select class="form-control chosen-select" data-placeholder="" name="dept" required  >
                                    <option></option>
                                    <option value="8">Pool Bar</option>
                                    <option value="9">Main Bar</option>
                                    <option value="10">Resturant</option>
                                    <option value="11">Resturant Drinks</option>
									<option value="12">Kitchen</option>
                                    <option value="15">House Keeping</option>
                                  </select>
                              </div>
                        </div>

                        <div class="form-group">
                          <label for="inputPassword3" class="col-sm-3 control-label">Qty</label>
                          <div class="col-sm-3">
                            <input type="text" name="qty" id="qty" class="form-control form-control-circle" onkeyup="validateNosOnly(this.value, 'qty')" autocomplete="off" required>
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="inputPassword3" class="col-sm-3 control-label">Price</label>
                          <div class="col-sm-3">
                            <input type="text" name="price" id="price" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" > 
                          </div>
                        </div>

                        <div class="form-group" style="padding-bottom:8px">
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
                                  'plugins/nicescroll/jquery.nicescroll.min.js',
                                  'plugins/typehead/typeahead.bundle.js',
                                  'plugins/typehead/typeahead.bundle-conf.js',
                                  'plugins/inputmask/jquery.inputmask.bundle.js',
                                  'plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js',
                                  'plugins/bootstrap-chosen/chosen.jquery.js',
                                  'moment/moment.js',
                                  'plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.js',
                                  'app/custom.js',
                                  'application/ctrl.js',
                                  'application/transactions.js'
                                  ))); 

        	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>
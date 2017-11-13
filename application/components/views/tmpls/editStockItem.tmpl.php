<?php
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | manager  duty manager )
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

            <div class="page-header"><h1>Edit Stock Item <small>&nbsp;</small></h1>
              <h1>
                <small><a href="<?php echo $baseUri; ?>/item/editOptions">Edit Item Options</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/item/editStockItem">Edit Stock Item</a></small>
              </h1>

            </div>




            <div class="row">

                <div class="col-md-8">

                  <?php
                     if($registry->get('session')->read('formMsg')){
                      echo $registry->get('session')->read('formMsg');
                      $registry->get('session')->write('formMsg', NULL);
                     }
                  ?>

                    <div class="panel panel-default">
                        <div class="panel-heading">&nbsp;</div>
                        <div class="panel-body">

                            <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/item/editStockItem">

                            <div class="form-group">
                                        <label class="col-sm-3 control-label">Select Item</label>
                                        <div class="col-sm-4">
                                          <select class="form-control chosen-select" data-placeholder="" onchange="fetchItemDetails(this.value)">
                                              <option></option>
                                              <?php
                                              foreach (Item::fetchAll('store', false) as $key) {
                                                # code...
                                                $item = new Item($key->itemId);
                                              ?>
                                              <option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
                                              <?php } ?>

                                            </select>
                                        </div>
                            </div>

                        <hr class="dotted" />

                        <div id="loader" style="display:none; text-align:center"></div>

                        <span id="holder" style="visibility:hidden">

                        <input type="hidden" id="id" name="id" />

                        <div class="form-group">
                          <label for="inputEmail3" class="col-sm-3 control-label">Item Name</label>
                          <div class="col-sm-6">
                            <input type="text" id="name" name="name" class="form-control form-control-circle" autocomplete="off" required>
                          </div>
                        </div>

                        <div class="form-group">
                                    <label class="col-sm-3 control-label">Item Type</label>
                                    <div class="col-sm-4">
                                      <select class="form-control chosen-select" data-placeholder="" id="type" name="type" required>
                                          <option></option>
                                          <?php
                                          foreach (Item::fetchTypes() as $key) {
                                            # code...
                                          ?>
                                          <option value="<?php echo $key->id; ?>"><?php echo $key->name; ?></option>
                                          <?php } ?>

                                        </select>
                                    </div>
                        </div>

                        <div class="form-group">
                                    <label class="col-sm-3 control-label">Item Unit</label>
                                    <div class="col-sm-4">
                                      <select class="form-control chosen-select" data-placeholder="" id="unit" name="unit" required>
                                          <option></option>
                                          <?php
                                          foreach (Item::fetchUnits() as $key) {
                                            # code...
                                          ?>
                                          <option value="<?php echo $key->id; ?>"><?php echo $key->name; ?></option>
                                          <?php } ?>

                                        </select>
                                    </div>
                        </div>

                        <div class="form-group">
                          <label for="inputPassword3" class="col-sm-3 control-label">Pool Bar Price</label>
                          <div class="col-sm-3">
                            <input type="text" name="pbPrice" id="pbPrice" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" autocomplete="off">
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="inputPassword3" class="col-sm-3 control-label">Main Bar Price</label>
                          <div class="col-sm-3">
                            <input type="text" name="mbPrice" id="mbPrice" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" autocomplete="off">
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="inputPassword3" class="col-sm-3 control-label">Resturant Drinks Price</label>
                          <div class="col-sm-3">
                            <input type="text" name="rdPrice" id="rdPrice" class="form-control inputmask form-control-circle" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 0, 'digitsOptional': false, 'prefix': '=N= ', 'placeholder': '0'" autocomplete="off">
                          </div>
                        </div>



                        <div class="form-group" style="padding-bottom:8px">
                          <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" name="submit" class="btn btn-success btn-circle">Edit Item</button>
<!--                            <button type="submit" name="deleteItem" class="btn btn-danger btn-circle" style="margin-left:10px">Delete Item</button>-->
                          </div>
                        </div>

                        </form>

                    </span>

                      <br /><br />

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
                                  'application/stock.js'
                                  )));

        	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js'
        ?>

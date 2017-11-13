<?php
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));
$session = $registry->get('session');

$user = new User(new Staff($session->read('uId')));
$session->write('uId', null);

#check if user has access to this page ( super admin | manager  )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2), true);

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
                <h1>Change User Department ( Privilege )<small>&nbsp;</small></h1>
                <h1>
                  <small><a href="<?php echo $baseUri; ?>/account/viewAll">View All User</a></small>
                  <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                  <small>Change User Departmant</small>
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

                        <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/account/changeDept">

                        <input type="hidden" name="id" value ="<?php echo $user->id; ?>" />

                        <div class="form-group">
                          <label for="inputEmail3" class="col-sm-3 control-label">Name</label>
                          <div class="col-sm-9">
                            <input type="text" name="name" value="<?php echo $user->name; ?>" class="form-control form-control-circle" disabled>
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="inputEmail3" class="col-sm-3 control-label">Username</label>
                          <div class="col-sm-9">
                            <input type="text" name="username" value="<?php echo $user->username; ?>" class="form-control form-control-circle" disabled>
                          </div>
                        </div>

                      <?php
                      if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1))){
                      ?>

                        <div class="form-group">
                              <label class="col-sm-3 control-label">Privilege</label>
                              <div class="col-sm-4">
                                <select class="form-control chosen-select" data-placeholder="" name="privilege" required>
                                    <option></option>
                                    <?php
                                    foreach (User::fetchAllPrivileges(true) as $key) {
                                      # code...
                                    ?>
                                    <option value="<?php echo $key->id; ?>"><?php echo $key->privilege; ?></option>
                                    <?php } ?>

                                  </select>
                              </div>
                        </div>

                        <?php }else{ ?>

                          <div class="form-group">
                              <label class="col-sm-3 control-label">Privilege</label>
                              <div class="col-sm-4">
                                <select class="form-control chosen-select" data-placeholder="" name="privilege" required>
                                    <option = "<?php echo $user->privilege; ?>"><?php echo $user->role; ?></option>
                                    <?php
                                    foreach (User::fetchAllPrivileges() as $key) {
                                      # code...
                                    ?>
                                    <option value="<?php echo $key->id; ?>"><?php echo $key->privilege; ?></option>
                                    <?php } ?>

                                  </select>
                              </div>
                        </div>

                        <?php } ?>


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
                                  'application/ctrl.js'
                                  )));

        	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js'
        ?>

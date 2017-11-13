<?php
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | reception )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

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

    <div class="warper container-fluid">

      <div class="page-header">
        <h1>User Accounts <small style="color:#FF404B"></small></h1>

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


                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="basic-datatable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Privilege</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php
                              $count = 1;
                              foreach (staff::fetchAll() as $row) {

                                $user = new User(new Staff($row->id));

                                $class = ( $count % 2 == 0 ) ? 'even' : 'odd';
                              ?>
                                 <tr class="<?php echo $class; ?> gradeX">
                                    <td><?php echo $user->name; ?></td>
                                    <td><?php echo $user->role; ?></td>
                                    <td>

                                      <?php
                                      if($user->id != $thisUser->id){
                                        # make sure user does not delete his account
                                      ?>
                                      <a href="<?php echo $baseUri; ?>/account/changeDept/<?php echo $user->staffId; ?>" class="btn btn-circle btn-success" style="margin-right:10px">Change Dept</a>
                                      <button class="btn btn-danger btn-circle btn-sm" onclick="populateDeleteOptions({'userName' : '<?php echo $user->name; ?>', 'userId' : '<?php echo $user->staffId; ?>' })" data-toggle="modal" data-target="#delete">Delete</button>

                                    <?php } ?>


                                </tr>
                                <?php } ?>
                              </tbody>
                            </table>


                    </div>
                </div>
            </div>





    <!-- Delete Form -->
      <div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Delete User Account</h4>
                </div>
                <div class="modal-body">

                  <p> Are You Sure you want to delete <span id="userName"></span> 's Account </p>

                  <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/account/delete">

                        <input type="hidden" name="userId" id="userId" />

                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                              <button type="button"  class="btn btn-danger btn-circle" data-dismiss="modal">Not Really</button>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              <button type="submit" name="submit" class="btn btn-success btn-circle">Yea, Sure</button>
                            </div>
                          </div>
                     </form>
           </div>
           <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
           </div>
        </div>
      </div>
    </div>
    <!-- cancel Form Ends -->




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
                              'application/account.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js'
    ?>

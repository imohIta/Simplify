<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | Mgt Staff | reception )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,4,7), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');


# fetch reservations for this src
$log = Room::getBad();

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
        <h1>Bad Rooms Log <small style="color:#FF404B">&nbsp;</small></h1>

            <h1>
                <small><a href="<?php echo $baseUri; ?>/reception/manageBadRooms">Manage Bad Rooms</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/reception/viewBadRooms">View All</a></small>
              </h1>
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


                    
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="basic-datatable">
                            <thead>
                                <tr>
                                   <th>SN</th>
                                    <th>Room Type</th>
                                    <th>Room No</th>
                                    <th>Date Added</th>
                                    <th>Reason</th>
                                   <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $count = 1;
                              foreach ($log as $row) {
                                
                                # code...
                                $room = new Room($row->roomId);
                                $class = ( $count % 2 == 0 ) ? 'even' : 'odd';

                              ?>
                                 <tr class="<?php echo $class; ?> gradeX">
                                    <td><?php echo $count; ?>.</td>
                                    <td><?php echo $room->type; ?></td>
                                    <td><?php echo $room->no; ?></td>
                                    <td><?php echo dateToString($row->dateAdded); ?></td>
                                    <td><?php echo $row->reason; ?></td>
                                    <td>
                                      
                                       
                                      <button type="button" onclick="showBadRoomRemoveOpts({'roomId' : '<?php echo $room->id; ?>', 'roomNo' : '<?php echo $room->no; ?>' })" data-toggle="modal" data-target="#options" class="btn btn-default btn-circle btn-sm">Remove From List</button</button>
                                     
                                    </td>
                                                                    
                                </tr> 
                                <?php } ?>
                              </tbody>
                            </table>

                    
                    </div>
                </div>
            </div>

          

        </div>

        <!-- Cancel Form -->
      <div class="modal fade" id="options" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Remove Bad Room</h4>
                </div>
                <div class="modal-body">

                  <p> Are You Sure you remove Room <span id="roomNo"></span> from Bad Rooms List </p>

                  <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/reception/removeBadRoom">

                        <input type="hidden" name="roomId" id="roomId" />
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                              <button type="button"  class="btn btn-danger btn-circle" data-dismiss="modal">Not Really</button>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              <button type="submit" name="cancel" class="btn btn-success btn-circle">Yea, Sure</button>
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
    <!-- Delete Form Ends -->
        
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
                              'application/reception.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
    ?>
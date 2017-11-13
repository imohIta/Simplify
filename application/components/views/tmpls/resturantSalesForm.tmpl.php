<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | reception )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,10), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

    $session->read('incompleteSaleTransId_' . $thisUser->id . '_' . $thisUser->get('activeAcct'), null);


# get all items from this user's Stock execept 
# pass true as argument if stock items that are finished are not to be fetched
//if(!$registry->get('session')->read('menuItems')){
 //   $registry->get('session')->write('menuItems', Menu::fetchAll()); 
//}

$menuItems = Menu::fetchAll();

//$session->write('tempSales' . $thisUser->id, null);

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
          
            <div class="page-header"><h1>Make Sales <small>&nbsp;</small></h1></div>

            

             <?php 
                 if($registry->get('session')->read('formMsg')){
                  echo $registry->get('session')->read('formMsg');
                  $registry->get('session')->write('formMsg', NULL);
                 }
              ?>
            
            <div class="row">

              
                <div class="col-md-9">
                 	<div class="panel panel-default">
                        <div class="panel-heading">&nbsp;</div>
                        <div class="panel-body">

                        	
                        	<div id="errorHolder"></div>

                        	<table style="width:100%">
                        	<tr>
                        		<td style="width:35%;" align="center">Menu Name</td>
                        		<td style="width:18%" align="center">Price</td>
                        		<td style="width:19%" align="center">Qty</td>
                        		<td style="width:19%" align="center">Amount</td>
                        		<td style="width:9%" align="center">&nbsp;</td> 
                        	</tr>

                        	<tr><td colspan="5"><hr/></td></tr>
                        	</table>

                        	<table style="width:100%; <?php if(!$session->read('tempSales' . $thisUser->id . $thisUser->get('activeAcct'))){ ?> display:none <?php } ?>" id="tempHolder">
                        	<tr>
                        		<td style="width:35%;" align="center">&nbsp;</td>
                        		<td style="width:19%" align="center">&nbsp;</td>
                        		<td style="width:19%" align="center">&nbsp;</td>
                        		<td style="width:19%" align="center">&nbsp;</td>
                        		<td style="width:9%" align="center">&nbsp;</td> 
                        	</tr>

                          <?php if($session->read('tempSales' . $thisUser->id . $thisUser->get('activeAcct'))){
                            //var_dump($session->read('tempSales' . $thisUser->id));
                             foreach ($session->read('tempSales' . $thisUser->id . $thisUser->get('activeAcct')) as $key => $row) {
                              
                              $itemId = $row['itemId'];
                              
                              ?>
                            <tr>
                            <td style="width:35%;" align="center"><?php echo $row['itemName']; ?></td>
                            <td style="width:19%" align="center"><?php echo $row['price']; ?></td>
                            <td style="width:19%" align="center"><?php echo $row['qty']; ?></td>
                            <td style="width:19%" align="center"><?php echo $row['amt']; ?></td>
                            <td style="width:9%" align="center"><button type="submit" class="btn btn-danger btn-circle btn-sm saleOptions" data-toggle="modal" data-target="#saleDeleteOptions" onclick="populateDelete('<?php echo $itemId; ?>')">Delete</button></td> 
                          </tr>
                          <?php
                             }
                          }
                          ?>
                        	
                        	</table>

                        	<hr class="dotted" />


                        	<table style="width:100%">
                        		<tr>
                        			<!-- <td>
                        			 <div class="form-group">
                                 	 <select class="form-control chosen-select" data-placeholder="Item Type" style="width:220px">
                                      	  <option></option>
                                      	  <?php //foreach ($session->read('itemTypes') as $row) {
                                      	  ?>
                                      	  <option value="<?php //echo $row->id; ?>"><?php //echo $row->name; ?></option>
                                         <?php //} ?>
	                                     </select>
	                                    </div>
	                        		 </td> -->

                        		 <td style="width:35%" align="center">
                        		 	<select class="form-control chosen-select" id="item" data-placeholder="Select Item" style="width:220px" onChange="setMenuPrice(this.value);">
                                      	  <option></option>
                                      	  <?php foreach ($menuItems as $row) {
                                      	    $menu = new Menu($row->id);
                                      	  ?>
                                      	  <option value="<?php echo $menu->id; ?>"><?php echo $menu->name; ?></option>
                                         <?php } ?>

                                     </select>
                        		 </td>

                        		 <td style="width:20%" align="center">
                        		 	<input type="text" class="form-control form-control-circle" readonly  placeholder="" style="width:120px" id="price" autocomplete="off">
  
                        		 </td>
                        		 <td>
                        		 	<input type="text" class="form-control form-control-circle" readonly  placeholder="" style="width:120px" id="qty" onkeyUp="calculateAmount(this.value, 'qty')">
		                             
                        		 </td>
                        		 <td style="width:20%" align="center">
                        		 	
		                                <input type="text" class="form-control form-control-circle" readonly  placeholder="" style="width:120px" id="amt" >
		                             
                        		 </td>

                        		 <td style="width:5%" align="center">
                        		 	<button type="submit" id="addSalesBtn" class="btn btn-default btn-circle" title="Add to this Sale">Add</button>
                        		 </td>

                        		</tr>
                        	</table>

                        	<input type="hidden" id="qtyInStock" />

                        	<hr />

                         <table>
                          <tr><td>
                          <select onchange="setGuestType(this.value)" id="guestType" style="width:220px; padding:4px; margin-left:30px">
                            <option value="">Select Guest Type</option>
                            <option value="1">In Guest</option>
                            <option value="2">Out Guest</option>
                            <option value="3">Staff</option>
                          </select>
                          </td><td>
                          <select onchange="setPurchaserDetails(this.value)" id="roomNo" style="display:none; width:100px; padding:4px; margin-left:12px">
                            <option value="0">Room No</option>
                              <?php
                                foreach (Room::getOccupied() as $r) {
                                $room = new Room($r->roomId);
                              ?>
                              <option value="<?php echo $room->id; ?>"><?php echo $room->no; ?></option>
                              <?php } ?>
                          </select>
                          </td></tr>
                          </table>

                          <hr />

                          <span id="printBtn" style="display:none">
                             <button type="button" class="btn btn-success btn-circle" data-toggle="modal" data-target="#printBill" style="margin-left:30px" >Print Bill</button>
                          </span>
                        
                        </div>
                    </div>
                 </div>
                    
            
            </div>

            <div class="modal fade" id="saleDeleteOptions" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		      <div class="modal-dialog">
		        <div class="modal-content">
		          <div class="modal-header">
		            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		            <h4 class="modal-title" id="myModalLabel">Delete Temporary Sale</h4>
		          </div>
		          <div class="modal-body">
		            <p>Are yo sure yow want to delete this Menu Item from the temporary sales list? .</p>

                    <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/sales/deleteTemp">

                        <input type="hidden" name="itemId" id="itemId" />
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                              <button type="button"  class="btn btn-danger btn-circle" data-dismiss="modal">Not Really</button>
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                              <button type="submit" name="deleteTemp" class="btn btn-success btn-circle">Yea, Sure</button>
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



        <!-- Print Bill -->

        <div class="modal fade" id="printBill" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Print Sales Bill</h4>
              </div>
              <div class="modal-body">
                <p>Are you sure you want to print ths Sales Bill? .</p>

                   <form method="post" action="<?php echo $baseUri; ?>/sales/addNew">

                   <input type="hidden" name="guestType" id="guestType2">
                   <input type="hidden" name="roomId" id="roomId">

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
                                  'application/sales.js'
                                  ))); 

        	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>

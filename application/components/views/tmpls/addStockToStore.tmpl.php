<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | reception )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,14), true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

# get all items from Store
$items = Item::fetchAll('store');
//var_dump($session->read('tempStock' . $thisUser->id)); //die;

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
          
            <div class="page-header"><h1>Add Purchases to Store <small>&nbsp;</small></h1></div>

            

             
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

                        	
                        	<div id="errorHolder"></div>

                        	<table style="width:100%">
                        	<tr>
                        		<td style="width:35%;" align="center">Item Name</td>
                        		<td style="width:18%" align="center">Unit Cost Price</td>
                        		<td style="width:19%" align="center">Qty</td>
                        		<td style="width:19%" align="center">Amount</td>
                        		<td style="width:9%" align="center">&nbsp;</td> 
                        	</tr>

                        	<tr><td colspan="5"><hr/></td></td>
                        	</table>

                        	<table style="width:100%;"  id="tempHolder">
                        	<tr>
                        		<td style="width:35%;" align="center">&nbsp;</td>
                        		<td style="width:19%" align="center">&nbsp;</td>
                        		<td style="width:19%" align="center">&nbsp;</td>
                        		<td style="width:19%" align="center">&nbsp;</td>
                        		<td style="width:9%" align="center">&nbsp;</td> 
                        	</tr>

                          <?php if($session->read('tempStock' . $thisUser->id)){
                            //var_dump($session->read('tempSales' . $thisUser->id));
                             foreach ($session->read('tempStock' . $thisUser->id) as $key => $row) {
                              
                              $itemId = $row['itemId'];
                              
                              ?>
                            <tr>
                            <td style="width:35%;" align="center"><?php echo $row['itemName']; ?></td>
                            <td style="width:19%" align="center"><?php echo $row['price']; ?></td>
                            <td style="width:19%" align="center"><?php echo $row['qty']; ?></td>
                            <td style="width:19%" align="center"><?php echo $row['amt']; ?></td>
                            <td style="width:9%" align="center"><button type="submit" class="btn btn-danger btn-circle btn-sm saleOptions" data-toggle="modal" data-target="#stkDeleteOptions" onclick="populateDelete('<?php echo $itemId; ?>')">Delete</button></td> 
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
                        		 	<select class="form-control chosen-select" id="item" data-placeholder="Select Item" style="width:220px" onChange="toggleOthers(this.value);">
                                      	  <option value="0"></option>
                                      	  <?php foreach ($items as $row) {
                                      	    $posItem = new PosItem(new Item($row->itemId), 'store');
                                      	  ?>
                                      	  <option value="<?php echo $posItem->id; ?>"><?php echo $posItem->name; ?></option>
                                         <?php } ?>
                                     </select>
                        		 </td>

                        		 <td style="width:20%" align="center">
                        		 	<input type="text" class="form-control form-control-circle" readonly  placeholder="" style="width:120px" id="price" autocomplete="off" onkeyup="calculateAmt(this.value, 'price')">
  
                        		 </td>
                        		 <td>
                        		 	<input type="text" class="form-control form-control-circle" readonly  placeholder="" style="width:120px" id="qty" onkeyUp="calculateAmount(this.value, 'qty')">
		                             
                        		 </td>
                        		 <td style="width:20%" align="center">
                        		 	
		                                <input type="text" class="form-control form-control-circle" readonly  placeholder="" style="width:120px" id="amt" >
		                             
                        		 </td>

                        		 <td style="width:5%" align="center">
                        		 	<button type="submit" id="addStkBtn" class="btn btn-default btn-circle" title="Add to this Sale">Add</button>
                        		 </td>

                        		</tr>
                        	</table>

                        	<input type="hidden" id="qtyInStock" />

                        	<hr />

                          
                        	<button type="button" class="btn btn-success btn-circle" data-toggle="modal" data-target="#post" >Post</button>
                        	
                            
                        
                        </div>
                    </div>
                 </div>
                    
            
            </div>

            <div class="modal fade" id="stkDeleteOptions" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		      <div class="modal-dialog">
		        <div class="modal-content">
		          <div class="modal-header">
		            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		            <h4 class="modal-title" id="myModalLabel">Delete Stock Purchase</h4>
		          </div>
		          <div class="modal-body">
		            <p>Are you sure you want to delete this item from this Stock Purchase list? .</p>

                    <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/stock/deleteTemp">

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


        <!-- Post Stock -->

        <div class="modal fade" id="post" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Post Stock Purchase</h4>
              </div>
              <div class="modal-body">
                <p>Are you sure you want to post this Stock Purchase? .</p>

                   <form method="post" action="<?php echo $baseUri; ?>/stock/addStockPurchase">
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
                                  'application/stock.js'
                                  ))); 

        	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>

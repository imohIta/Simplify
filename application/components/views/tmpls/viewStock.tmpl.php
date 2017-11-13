<?php 
#check if user is logged in
global $today;
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}


#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

#check if user has access to this page ( super admin | Mgt Staff | reception )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5,7,8,9,10,11,12,13,15),
    true);

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');


$tbl = $session->read('stockPrivilege'. $thisUser->id . $thisUser->get('activeAcct')) 
      ? User::getTblByPrivilege($session->read('stockPrivilege'. $thisUser->id . $thisUser->get('activeAcct'))) 
      : $thisUser->tbl;

$role = $session->read('stockPrivilege'. $thisUser->id . $thisUser->get('activeAcct')) 
      ? User::getRole($session->read('stockPrivilege'. $thisUser->id . $thisUser->get('activeAcct'))) 
      : $thisUser->role;

$priv = $session->read('stockPrivilege'. $thisUser->id . $thisUser->get('activeAcct')) 
      ? $session->read('stockPrivilege'. $thisUser->id . $thisUser->get('activeAcct')) 
      : $thisUser->privilege;

    //echo $priv; die;


# fetch reservations for this src
$stock = Item::fetchAll($tbl, false);


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
    
    <div class="warper container-fluid" >

      <div class="page-header">
        <h1>Stock Table<small style="color:#FF404B"> ( <?php echo $role; ?> )</small></h1>

        <?php 
        if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,4,5,10,11))){
            $url = (in_array($thisUser->get('activeAcct'), array(10,11)) !== false ) ? 'resturantOptions' :
                'mgtOptions';

        ?>
        <h1>
                <small><a href="<?php echo $baseUri; ?>/stock/<?php echo $url; ?>">Stock Options</a></small>
                <small> &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; </small>
                <small><a href="<?php echo $baseUri; ?>/stock">Stock Table</a></small>
          </h1>
       <?php } ?>

      </div>
    	
        
        
         <div class="row">
        
            <div class="col-md-10">

                <div class="panel panel-default">
                    <div class="panel-heading">&nbsp;</div>
                    <div class="panel-body">

                    <?php 
                    if(strtolower($role) == 'store'){
                    ?>

                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="basic-datatable">
                            <thead>
                                <tr>
                                   <th>SN</th>
                                    <th>Item Name</th>
                                     <th>Opening Stock</th>
                                    <th>Additions</th>
                                    <th>Total Stock</th>
                                    <th>Requisitions</th> 
                                    <th>Balance Stock</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $count = 1;
                              foreach ($stock as $row) {
                                
                                # code...
                                $item = new PosItem(new Item($row->itemId), $tbl);

                                #fetch purchaser additions
                                $additions = PosItem::fetchAdditions(today(), $item->id);

                                #fetch requisitions
                                $req = PosItem::fetchTotalIssuedRequisitions($item->id, today());

                                #calculate the opening stock
                                $openingStock = ($item->qtyInStock + $req) - $additions;

                                $q = ($item->qtyInStock < 5 ) ? '<span class="badge bg-danger">' . number_format($item->qtyInStock) . '</span>'  : number_format($item->qtyInStock);
                                $class = ( $count % 2 == 0 ) ? 'even' : 'odd';

                              ?>
                                 <tr class="<?php echo $class; ?> gradeX">
                                    <td><?php echo $count; ?>.</td>
                                    <td><?php echo $item->name; ?></td>
                                    <td><?php echo $openingStock; ?></td>
                                    <td><?php echo $additions; ?></td>
                                    <td><?php echo number_format($additions + $openingStock); ?></td>
                                    <td><a href="javascript:void(0);" onclick="fetchReqDetails({'itemId' : '<?php echo $item->id; ?>', 'itemName' : '<?php echo $item->name; ?>'})" data-toggle="modal" data-target="#reqs"><?php echo number_format($req); ?></a></td>
                                    <td><?php echo $q; ?></td>
                                    
                                    
                                </tr> 
                                <?php $count++; } ?>
                              </tbody>
                            </table>


                            <!-- Issue Requisition details -->
                          <div class="modal fade col-md-12" id="reqs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <h4 class="modal-title" id="myModalLabel">Issued Requisitions Details for <span id="nameHolder"></span></h4>
                                  </div>
                                  <div class="modal-body" id="contentHolder">

                                   
                                 </div>
                                 <div class="modal-footer">
                                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                 </div>
                              </div>
                            </div>
                          </div>

                    <?php }elseif(strtolower($role) == 'kitchen' || strtolower($role) == 'house keeping'){ ?>

                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="basic-datatable">
                            <thead>
                            <tr>
                                <th>SN</th>
                                <th>Item Name</th>
                                <th>Requisitions</th>
                                <th>Current Stock</th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $count = 1;
                                foreach ($stock as $row) {

                                    # code...
                                    $item = new PosItem(new Item($row->itemId), $tbl);

                                    #fetch requisitions
                                    $req = $item->fetchRequisitions($priv);



                                    $q = ($item->qtyInStock < 5 ) ? '<span class="badge bg-danger">' . number_format($item->qtyInStock) . '</span>'  : number_format($item->qtyInStock);
                                    $class = ( $count % 2 == 0 ) ? 'even' : 'odd';

                                    ?>
                                    <tr class="<?php echo $class; ?> gradeX">
                                        <td><?php echo $count; ?>.</td>
                                        <td><?php echo $item->name; ?></td>
                                        <td><?php echo $req; ?></td>
                                        <td><?php echo $q; ?></td>


                                    </tr>
                                    <?php $count++; } ?>
                            </tbody>
                        </table>

                    <?php }else{ ?>

                    
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="basic-datatable">
                            <thead>
                                <tr>
                                   <th>SN</th>
                                    <th>Item Name</th>
                                    <?php if($thisUser->get('activeAcct') != 13 || $thisUser->get('activeAcct') !=
                                                                                   12){ ?>
                                    <th>Price</th>
                                    <?php } ?>
                                     <th>Opening Stock</th>
                                    <th>Requisitions</th>
                                    <th>Total Stock</th>
                                    <th>Sold</th> 
                                    <th>Balance Stock</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $count = 1;
                              foreach ($stock as $row) {
                                
                                # code...
                                $item = new PosItem(new Item($row->itemId), $tbl);

                                #fetch the total number sold for this item
                                $sold = $item->fetchSold();


                                #fetch requisitions
                                $req = $item->fetchRequisitions($priv);

                                #calculate the opening stock
                                $openingStock = ($item->qtyInStock + $sold ) - $req;

                                $q = ($item->qtyInStock < 5 ) ? '<span class="badge bg-danger">' . $item->qtyInStock . '</span>'  : $item->qtyInStock;
                                $class = ( $count % 2 == 0 ) ? 'even' : 'odd';

                              ?>
                                 <tr class="<?php echo $class; ?> gradeX">
                                    <td><?php echo $count; ?>.</td>
                                    <td><?php echo $item->name; ?></td>
                                    <?php if($thisUser->get('activeAcct') != 13){ ?>
                                    <td><?php echo number_format($item->price) ?></td>
                                    <?php } ?>
                                    <td><?php echo $openingStock; ?></td>
                                    <td><?php echo $req; ?></td>
                                    <td><?php echo $openingStock + $req; ?></td>
                                    <td><?php echo $sold; ?></td>
                                    <td><?php echo $q; ?></td>
                                    
                                    
                                </tr> 
                                <?php $count++; } ?>
                              </tbody>
                            </table>

                        <?php } ?>
                    
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
                              'plugins/datatables/jquery.dataTables.js',
                              'plugins/datatables/DT_bootstrap.js',
                              'plugins/datatables/jquery.dataTables-conf.js',
                              'app/custom.js',
                              'application/ctrl.js',
                              'application/stock.js'
                              )));

    	//'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
    ?>
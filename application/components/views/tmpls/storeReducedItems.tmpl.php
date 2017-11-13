<?php 
#check if user is logged in
global $today;

#if user not looged in
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect(); 
} 

#logged in User
$thisUser = unserialize($registry->get('session')->read('thisUser'));

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

$items = Item::fetchReduced(5);

?>

<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/css/bootstrap/bootstrap.css" /> 

	<!-- Calendar Styling  -->
    <!-- <link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/css/plugins/calendar/calendar.css" /> -->
    
    <!-- Fonts  -->
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,500,600,700,300' rel='stylesheet' type='text/css'>
    
    <!-- Base Styling  -->
    <link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/css/app/app.v1.css" />

    <!-- <meta name="viewport" content="width=device-width, initial-scale=1" /> -->
    
    <title><?php echo $registry->get('config')->get('appTitle'); ?></title>
    

    <div style="width:600px; margin:20px auto">

           <div class="warper container-fluid">
        	
          <div class="col-md-12">
                    <div class="page-header"><h3><?php echo $thisUser->role; ?> Reduced Items</h3></div>
                    
                    <div class="panel panel-default">
                        <div class="panel-heading">&nbsp;</div>
                        <div class="panel-body table-responsive">
                        
                            <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Unit</th>
                                    <th>Qty In Stock</th>
                                   
                                  </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($items as $key) {
                                        # code...
                                        $item = new PosItem(new Item($key->itemId));
                                    ?>
                                    
                                  <tr>
                                    <td><?php echo $count; ?></td>
                                    <td><?php echo $item->name; ?></td>
                                    <td><?php echo $item->unit; ?></td>
                                    <td><?php echo $item->qtyInStock; ?></td>
                                  </tr>

                                  <?php $count++; } ?>
                                  
                                </tbody>
                              </table>
                        
                        </div>
                    </div>
                </div>
            
            </div>
            
            
                
                
                <div id="print" class="row">
                    <div class="col-lg-6"><button class="btn btn-warning" type="button" onclick="printInv('print');">Print</button></div>
                    <div class="col-lg-6 text-right"><a href="<?php echo $baseUri; ?>/dashboard/" class="btn btn-success" title="Back to Dashboard">Back</a></div>
                </div>
                    
          </div>

        </div>
        <!-- Warper Ends Here (working area) -->






    <script src="<?php echo $baseUri; ?>/assets/js/jquery/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="<?php echo $baseUri; ?>/assets/js/plugins/underscore/underscore-min.js"></script>
    <!-- Bootstrap -->
    <script src="<?php echo $baseUri; ?>/assets/js/bootstrap/bootstrap.min.js"></script>
    
    <!-- Globalize -->
    <script src="<?php echo $baseUri; ?>/assets/js/globalize/globalize.min.js"></script>
    
    <!-- NanoScroll -->
    <script src="<?php echo $baseUri; ?>/assets/js/plugins/nicescroll/jquery.nicescroll.min.js"></script>
    
	
    
    
    <!-- Custom JQuery -->
	<script src="<?php echo $baseUri; ?>/assets/js/app/custom.js" type="text/javascript"></script>

	<script src="<?php echo $baseUri; ?>/assets/js/application/ctrl.js" type="text/javascript"></script>
    

    
	
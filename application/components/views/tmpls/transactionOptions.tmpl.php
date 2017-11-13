<?php 
#check if user is logged in
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}

$thisUser = unserialize($registry->get('session')->read('thisUser'));
$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

#check if user has access to this page ( super admin | reception  )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,2,3,5,6,7), true);



#include header
$registry->get('includer')->render('header', array('css' => array(
                                                                'font-awesome.min.css',
                                                                )));

#include Sidebar
$registry->get('includer')->render('sidebar', array());


#include small header
$registry->get('includer')->renderWidget('smallHeader');
?>
 

    <!-- Page Body here...Editable region -->
        
        <div class="warper container-fluid">
            
            <div class="page-header"><h1>Transactions <small></small></h1></div>
            
            <div class="row">
                
                <a href="<?php echo $baseUri; ?>/guest/viewTransactions">
                <div class="col-md-6 col-lg-3">
                    <div class="panel panel-default clearfix dashboard-stats rounded btn-gry">
                        <span id="dashboard-stats-sparkline3" class="sparkline transit"></span>
                        <h3 class="transit2">Transactions</h3>
                        <p class="transit2">( View All )</p>
                        
                    </div>
                </div>
                </a>

                <?php 
                  if($registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,7))){
                ?>

                <a href="<?php echo $baseUri; ?>/guest/addBill">
                <div class="col-md-6 col-lg-3">
                    <div class="panel panel-default clearfix dashboard-stats rounded btn-orange">
                        <span id="dashboard-stats-sparkline3" class="sparkline transit"></span>
                         <h3 class="transit2">Bill</h3>
                         <p class="transit2">( Add New )</p>
                        
                    </div>
                </div>
                </a>
                
                <a href="<?php echo $baseUri; ?>/guest/addPayment">
                <div class="col-md-6 col-lg-3">
                    <div class="panel panel-default clearfix dashboard-stats rounded btn-yellow">
                        <span id="dashboard-stats-sparkline3" class="sparkline transit"></span>
                        <h3 class="transit2">Payment</h3>
                        <p class="transit2">( Add New )</p>
                        
                    </div>
                </div>
                </a>

                <?php } ?>

            
            </div>
            
            
        </div>
        <!-- Warper Ends Here (working area) -->
        
        
        <?php  
            $registry->get('includer')->render('footer', array('js' => array(
                                                            'plugins/nicescroll/jquery.nicescroll.min.js',
                                                            'app/custom.js'
                                                            )));

            //'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>
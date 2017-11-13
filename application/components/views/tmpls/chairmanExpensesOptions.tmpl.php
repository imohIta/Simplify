<?php 
#check if user is logged in
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}

$thisUser = unserialize($registry->get('session')->read('thisUser'));
$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

#check if user has access to this page ( super admin | cashier | Accountant | reception  )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,5,6,7), true);



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
            
            <div class="page-header"><h1>Chairman Expenses Options <small></small></h1></div>
            
            <div class="row">
                
                <a href="<?php echo $baseUri; ?>/reception/chairmanExpenses">
                <div class="col-md-6 col-lg-3">
                    <div class="panel panel-default clearfix dashboard-stats rounded btn-gry">
                        <span id="dashboard-stats-sparkline3" class="sparkline transit"></span>
                        <h4 class="transit2">Add Chairman Expenses</h4>
                        
                    </div>
                </div>
                </a>
 
                <a href="<?php echo $baseUri; ?>/reception/chairmanExpensesLog">
                <div class="col-md-6 col-lg-3">
                    <div class="panel panel-default clearfix dashboard-stats rounded btn-orange">
                        <span id="dashboard-stats-sparkline3" class="sparkline transit"></span>
                         <h4 class="transit2">Chairman Expenses Log</h4>
                        
                    </div>
                </div>
                </a>
                
                

            
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
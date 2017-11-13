<?php 
#check if user is logged in
if(!$registry->get('session')->read('loggedIn')){
    $registry->get('uri')->redirect();
}

$thisUser = unserialize($registry->get('session')->read('thisUser'));
$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

#check if user has access to this page ( super admin | reception  )
$registry->get('authenticator')->checkPrivilege($thisUser->get('activeAcct'), array(1,7), true);

#if invioce session if any
if($session->read('showInvioce')){
    $session->write('invioceData', null);
    $session->write('invioceType', null);
    $session->write('showInvioce', null);
}

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
            
            <div class="page-header"><h1>Manage Guest<small></small></h1></div>
            
            <div class="row">

            

                  <?php 
                     if($registry->get('session')->read('formMsg')){
                      echo $registry->get('session')->read('formMsg');
                      $registry->get('session')->write('formMsg', NULL);
                     }
                  ?>
            

            <a href="<?php echo $baseUri; ?>/guest/manageDiscount">
                <button class="btn btn-circle btn-gry" style="padding:20px; font-size:14px; margin-right:10px">Manage Discount</button>
            </a>

            <a href="<?php echo $baseUri; ?>/guest/transferExpenses">
                <button class="btn btn-circle btn-orange" style="padding:20px; font-size:14px; margin-right:10px">Transfer Expenses</button>
            </a>

            <a href="<?php echo $baseUri; ?>/guest/lateCheckOut">
                <button class="btn btn-circle btn-test" style="padding:20px; font-size:14px; margin-right:10px">Late Check Out</button>
            </a>

            <a href="<?php echo $baseUri; ?>/guest/exemptFromAutoBill">
                <button class="btn btn-circle btn-yellow" style="padding:20px; font-size:14px; margin-right:10px">AutoBill Exemption</button>
            </a>
                
            
            <button class="btn btn-circle btn-default" data-toggle="modal" data-target="#autoBillOptions"  style="padding:20px; font-size:14px; margin-right:10px">Auto Room Charge</button>
             
                
               

            
            </div>
            
            
        </div>
        <!-- Warper Ends Here (working area) -->


        <!-- Delete Form -->
        <div class="modal fade" id="autoBillOptions" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Guest Room Charge Auto-Billing</h4>
                </div>
                <div class="modal-body">

                  <p> Performing this action will auto-bill all the Guests currently checked in for thier Rooms...Do you really want to continue ?</p> 

                  <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/guest/autoBill2">
                        
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
    <!-- cancel Form Ends -->
        
        
        <?php  
            $registry->get('includer')->render('footer', array('js' => array(
                                                            'plugins/nicescroll/jquery.nicescroll.min.js',
                                                            'app/custom.js'
                                                            )));

            //'globalize/globalize.min.js','plugins/sparkline/jquery.sparkline.min.js','plugins/sparkline/jquery.sparkline.demo.js' 
        ?>
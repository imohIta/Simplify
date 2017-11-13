<?php
global $registry;
$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');

#set defaults
$class = "col-md-6";
$break = false;


if($session->read('fullScreen') == 'yes'){
    
    $class = "col-md-12";
    $break = true;
}


if(empty($msg['credits']) && empty($msg['payments'])){
?>

<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <strong>Error!</strong> No Guest with this Phone Number was Found. 
</div>

<?php }else{ ?>

<div class="row" style="width:96%; margin-left:15px">

<?php if(!$break){ ?>
  <div class="page-header" style="margin-left:13px"><h3>Guest Name: <?php echo fetchGuestNameByPhone($msg['credits'][0]->guestPhone); ?> </h3></div>
  <hr class="dotted" />
<?php } ?>


            
              <div class="<?php echo $class; ?>">

                    <div class="panel panel-default">
                        <div class="panel-heading"><h3>Credits</h3></div>
                        <div class="panel-body table-responsive">
                        
                          <table class="table table-bordered">
                              <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Trans Id</th>
                                    <th>Amount</th>
                                    <th>Details</th>
                                </tr>
                              </thead>
                              <tbody>
                               <?php 
                                  $total = 0;
                                  foreach ($msg['credits'] as $row) {  
                                  $total += $row->amt;
                                  ?>
                                    
                                    <tr>
                                        
                                        <td><?php echo dateToString($row->date); ?> </td>
                                        <td><?php echo $row->transId; ?> </td>
                                        <td><?php echo number_format($row->amt); ?></td>
                                        <td><?php echo $row->details; ?></td>
                                    </tr>
                                    
                                  <?php } ?>

                                  <tr>
                                    <td colspan="2"><strong>Total</strong></td>
                                    <td colspan="2"><strong><?php echo number_format($total); ?></strong></td>
                                  </tr>
                                
                              </tbody>
                            </table>
                        
                        </div>
                    </div>
                </div>
                <?php if($break){ ?>
                  <br style="clear:both" />
                <?php } ?>
                
                <div class="<?php echo $class; ?>">
                    
                    <div class="panel panel-default">
                        <div class="panel-heading"><h3>Payments</small></h3></div>
                        <div class="panel-body table-responsive">
                        
                          <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>Date</th>
                                    <th>Trans Id</th>
                                    <th>Amount</th>
                                    <th>Details</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php 
                                  $total = 0;
                                  foreach ($msg['payments'] as $row) {  
                                  $total += $row->amt;
                                  $details = json_decode($row->details, true);
                                  ?>
                                  <td><?php echo dateToString($row->date); ?> </td>
                                        <td><?php echo $row->transId; ?> </td>
                                        <td><?php echo number_format($row->amt); ?></td>
                                        <td>Pay Type : <?php echo $details['Pay Type']; ?></td>
                                    </tr>
                                    
                                  <?php } ?>

                                  <tr>
                                    <td colspan="2"><strong>Total</strong></td>
                                    <td colspan="2"><strong><?php echo number_format($total); ?></strong></td>
                                  </tr>
                                 
                                  
                                </tbody>
                              </table>
                        
                        </div>
                    </div>
                </div>
            
  </div>






<?php } ?>
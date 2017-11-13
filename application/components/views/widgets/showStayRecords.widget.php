<?php
global $registry;
$baseUri = $registry->get('config')->get('baseUri');

if(empty($msg)){
?>

<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <strong>Error!</strong> No Previous Guest with this Phone Number was Found. 
</div>

<?php }else{ ?>


<div class="panel panel-default">
    <div class="panel-heading"><h3>Stay History of <?php echo fetchGuestNameByPhone($msg[0]['guestPhone']); ?></h3></div>
    <div class="panel-body">
    
        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="basic-datatable">
            <thead>
                <tr>
                	<th>Room</th>
                    <th>Check In Date</th>
                    <th>Check Out Date</th>
                    <th>Total Bill</th>
                    <th>Total Payment</th>
                </tr>
            </thead>
            <tbody>

            	<?php foreach ($msg as $row) { 
					$stayInfo = json_decode($row['stayInfo']);
					$bills = json_decode($row['bills']);
					$billsTotal = 0;
					foreach ($bills as $bill) {
						# code...
						$billsTotal += $bill->amt;
					}

					$payments = json_decode($row['payments']);
					$payTotal = 0;
					foreach ($payments as $pay) {
						# code...
						$payTotal += $pay->amt;
					}
            		?>
                
                <tr class="odd gradeX">
                    <td><?php echo $stayInfo->roomNo; ?> ( <?php echo $stayInfo->roomType; ?> )</td>
                    <td><?php echo dateToString($stayInfo->checkInDate); ?> </td>
                    <td><?php echo dateToString($stayInfo->checkOutDate); ?> </td>
                    <td><a href="javascript:void(0)" data-toggle="modal" data-target="#bills" onclick='populateBills(<?php echo json_encode($bills); ?>)'; title="Click to view Bill details" > <?php echo number_format($billsTotal); ?></a></td>
                    <td><a href="javascript:void(0)" data-toggle="modal" data-target="#payments" onclick='populatePayments(<?php echo json_encode($payments); ?>)'; title="Click to view Payment details" > <?php echo number_format($payTotal); ?></a></td>
                </tr>
                
                <?php } ?>
               
            </tbody>
        </table>

    </div>
</div>

<div class="modal fade" id="bills" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel">Bill Details</h4>
          </div>
          
          <div class="modal-body">
            <div class="panel panel-default">
                    
                        <div class="panel-body table-responsive">
                        
                        	<table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Trans Id</th>
                                    <th>Details</th>
                                    <th>Amount</th>
                                  </tr>
                                </thead>
                                <tbody id="billContent">
                                  
                                </tbody>
                              </table>
                        
                        </div>
                    </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
          </div>

        </div>
      </div>
    </div>


    <div class="modal fade" id="payments" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel">Payment Details</h4>
          </div>
          
          <div class="modal-body">
            <div class="panel panel-default">
                    
                        <div class="panel-body table-responsive">
                        
                        	<table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Trans Id</th>
                                    <th>Details</th>
                                    <th>Amount</th>
                                  </tr>
                                </thead>
                                <tbody id="payContent">
                                  
                                </tbody>
                              </table>
                        
                        </div>
                    </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
          </div>

        </div>
      </div>
    </div>
<!-- <button class="btn btn-purple btn-sm" data-toggle="modal" data-target="#modal-normal">Normal Size</button>


    <div class="modal fade" id="modal-normal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel">Normal size modal</h4>
          </div>
          <div class="modal-body">
            <p>This is an example of normal size modal.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Save changes</button>
          </div>
        </div>
      </div>
    </div> 


    <script src="<?php echo $baseUri; ?>/assets/js/plugins/datatables/jquery.dataTables.js"></script>
    <script src="<?php echo $baseUri; ?>/assets/js/plugins/datatables/DT_bootstrap.js"></script>
    <script src="<?php echo $baseUri; ?>/assets/js/plugins/datatables/jquery.dataTables-conf.js"></script>

    -->

   <?php } ?>
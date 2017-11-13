<?php
//var_dump($msg); die;
$transInfo = json_decode($msg->transInfo);
$staff = new User($transInfo->staffId);

# trans details
$details = json_decode($msg->details);

?>

<div class="panel panel-default">
            <div class="panel-heading"><h5>Transaction Type: <?php echo Transaction::fetchDesc($transInfo->transType); ?></h5></div>
            <div class="panel-body table-responsive">

            	<table class="table table-bordered">

          <?php

				switch ($transInfo->transType) {
					case 1: case 7: case 8: default: # guest bill | guest refund | guest credit


			?>

                    <tbody>
                     <tr>
                        <td align="right"><p class="text-danger">Transaction Date</p></td>
                        <td><p class="text-muted" style="margin-left:10px"><?php echo dateToString($transInfo->transDate); ?></p></td>
                      </tr>

                      <tr>
                        <td align="right"><p class="text-danger">Operation By</p></td>
                        <td><p class="text-muted" style="margin-left:10px"><?php echo $staff->name . ' ( ' . User::getRole($transInfo->privilege) . ' )'; ?></p></td>
                      </tr>

                      <tr>
                        <td align="right"><p class="text-danger">Description</p></td>
                        <td><p class="text-muted" style="margin-left:10px"><?php echo $details->desc; ?></p></td>
                      </tr>
                      <tr>
                        <td align="right"><p class="text-danger">Amount</p></td>
                        <td><p class="text-muted" style="margin-left:10px"><?php echo number_format($details->amt); ?></p></td>
                      </tr>

                    </tbody>

               <?php
                  break;

                  case 2 : #guest payment
                  $desc = json_decode($details, true);
                  ?>

                  <tbody>
                     <tr>
                        <td align="right"><p class="text-danger">Transaction Date</p></td>
                        <td><p class="text-muted" style="margin-left:10px"><?php echo dateToString($transInfo->transDate); ?></p></td>
                      </tr>

                      <tr>
                        <td align="right"><p class="text-danger">Operation By</p></td>
                        <td><p class="text-muted" style="margin-left:10px"><?php echo $staff->name . ' ( ' . User::getRole($transInfo->privilege) . ' )'; ?></p></td>
                      </tr>

                      <tr>
                        <td align="right"><p class="text-danger">Description</p></td>
                        <td><p class="text-muted" style="margin-left:10px"><?php echo $details->desc; ?></p></td>
                      </tr>

                      <tr>
                        <td align="right"><p class="text-danger">Amount</p></td>
                        <td><p class="text-muted" style="margin-left:10px"><?php echo number_format($details->amt); ?></p></td>
                      </tr>

                      <tr>
                        <td align="right"><p class="text-danger">Payment Type</p></td>
                        <td><p class="text-muted" style="margin-left:10px"><?php echo $desc['Pay Type']; ?></p></td>
                      </tr>



                    </tbody>

                  <?php
                  	break;

                  	case 3: case 4: # cash sale | # credit sale
                  ?>

                  <tbody>
                  <tr>
                  	<td colspan="2" align="right"><p class="text-danger">Transaction Date</p></td>
                  	<td colspan="2"><p class="text-muted"><?php echo dateToString($transInfo->transDate); ?></p></td>
                  </tr>

                   <tr>
                        <td align="right" colspan="2"><p class="text-danger">Operation By</p></td>
                        <td colspan="2"><p class="text-muted" style="margin-left:10px"><?php echo $staff->name . ' ( ' . User::getRole($transInfo->privilege) . ' )'; ?></p></td>
                   </tr>
                   <tr>
                        <td align="right" colspan="2"><p class="text-danger">Amount</p></td>
                        <td colspan="2"><p class="text-muted" style="margin-left:10px"><?php echo number_format($details->amt); ?></p></td>
                    </tr>

                    <?php

                     //$desc = json_decode($details->desc);

                     $salesDetails = json_decode($details->saleDetails);

                     foreach ($salesDetails->ItemsSold as $row) {
                     	# decode row...

                     	$item = $row->objectType == 1 ? new Menu($row->objectId) : new Item($row->objectId);

                    ?>
                    <tr>
                    	<td align="right"><?php echo $row->qty; ?></td>
                    	<td colspan="2"><?php echo $item->name . ' ( ' . number_format($row->price) . ' )'; ?></td>
                    	<td><?php echo number_format($row->price * $row->qty); ?></td>
                    </tr>

                    </tbody>

                  <?php
              		}
                  	break;

                  	case 8: # guest credit
                 ?>




                 <?php
                 	 break;
                 	?>

            <?php } ?>


                  </table>

            </div>
        </div>
    </div>

</div>



<!-- <div class="panel panel-default">
            <div class="panel-heading">&nbsp;</div>
            <div class="panel-body table-responsive">

            	<table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Table heading</th>
                        <th>Table heading</th>
                        <th>Table heading</th>
                        <th>Table heading</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>1</td>
                        <td>Table cell</td>
                        <td>Table cell</td>
                        <td>Table cell</td>
                        <td>Table cell</td>
                      </tr>

                    </tbody>
                  </table>

            </div>
        </div>
    </div>

</div>
 -->

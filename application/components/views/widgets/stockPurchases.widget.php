  <div class="panel panel-default">
          <div class="panel-heading">&nbsp;</div>
          <div class="panel-body table-responsive">
          
            <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>SN</th>
                      <th>Item Name</th>
                      <th>Price</th>
                      <th>Qty</th>
                      <th>Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $counter = 1;
                    foreach (json_decode($msg->purchase) as $row) {
                      # code..
                      $item = new Item($row->itemId);
                    ?>
                    <tr>
                      <td><?php echo $counter; ?></td>
                      <td><?php echo $item->name; ?></td>
                      <td><?php echo $row->price; ?></td>
                      <td><?php echo $row->qty; ?></td>
                      <td><?php echo number_format($row->amt); ?></td>
                    </tr>
                    <?php $counter++; } ?>
                  </tbody>
                </table>
          
          </div>

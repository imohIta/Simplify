<div class="form-group" id="group<?php echo $msg; ?>">
              
              <div class="col-sm-5">
                <select class="form-control chosen-select" placeholder="Item" name="item<?php echo $msg; ?>" required>
                    <option value="">Item</option>
                    <?php
                    foreach (Item::fetchAll('store') as $key) { 
                      # code...
                      $item = new Item($key->id);
                    ?>
                    <option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
                    <?php } ?>
                   
                  </select>
              </div>

              <div class="col-sm-4">
                <select class="form-control chosen-select" placeholder="Qty to Reduce" name="rQty<?php echo $msg; ?>" required >
                    <option value="">Qty to Reduce</option>
                    <?php
                      for ($i=1; $i <= 5; $i++) { 
                    ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php } ?>
                   
                  </select>
              </div>


              <div class="col-sm-3">
                 <button type="button" class="btn btn-danger btn-sm btn-circle removeBtn" id="removeBtn<?php echo $msg; ?>" onclick="removeRQtyField('<?php echo $msg; ?>')" title="Remove" >X</button>
               </div>
              
  </div>
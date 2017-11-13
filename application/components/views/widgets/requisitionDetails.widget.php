<?php
if(empty($msg['reqs'])){

	echo '<p style="padding:10px">' . $msg['itemName'] . ' was not Issued to any Department Today</p>';
}else{
?>

	<table class="table table-bordered">
      <thead>
        <tr>
            <th>Department</th>
            <th>Qty Issued</th>
            <th>Issued to</th>
        </tr>
      </thead>
      <tbody>
       <?php 
          
          foreach ($msg['reqs'] as $row) {  
            
            $staff = new Staff($row->staffId);
          ?>
            
            <tr>
                
                
                <td><?php echo User::getRole($row->privilege); ?></td>
                <td><?php echo number_format($row->qty); ?></td>
                <td><?php echo $staff->name; ?> </td>
                
            </tr>
            
          <?php } ?>
      </tbody>
    </table>

<?php } ?>
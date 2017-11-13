<?php
if(empty($msg)){
?>

<table class="table table-bordered">
  <tr><td>No Payment Found</td></tr>
</table>

<?php
}else{
?>

<h3>Credit Date : <?php echo $msg[0]->creditDate; ?></h3>
<h3>Department : <?php echo User::getRole($msg[0]->privilege); ?></h3>

<hr />

<table class="table table-bordered">
  <thead>
    <tr>
      <th>#</th>
      <th>Payment Date</th>
      <th>Amount</th>
      <th>Paid By</th>
    </tr>
  </thead>
  <tbody id="paymentsHolder">
    <?php
    $count = 1;
    foreach ($msg as $row) {
      # code...
      $staff = new User($row->staffId);
    ?>
    <tr>
      <td><?php echo $count; ?></td>
      <td><?php echo dateToString($row->date); ?></td>
      <td><?php echo number_format($row->amt); ?></td>
      <td><?php echo ucwords($staff->name); ?></td>
    </tr>
    <?php $count++; } ?>
  </tbody>
</table>

<?php } ?>
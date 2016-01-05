<!-- app/View/Teams/customer_not_teamed.ctp -->

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <h4 class="modal-title" id="myModalLabel">Daftar Pelanggan yang belum memiliki sales</h4>

</div>
<div class="modal-body">
  <div class='row'>
    
  <table class='table table-hover table-bordered'>
    <thead>
      <tr>
        <th>No.</th>
        <th>Kode Pelanggan</th>
        <th>Nama Pelanggan</th>
        <th>Alamat</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $i = 1;
      if(!$customer_not_in_team)
        echo "<tr><td colspan='4'>Semua pelanggan telah memiliki sales</td></tr>";
      foreach ($customer_not_in_team as $customer) {
      ?>
      <tr>
        <td><?php echo $i?></td>
        <td><?php echo $customer['Customer']['kdpelanggan']?></td>
        <td><?php echo $customer['Customer']['namapelanggan']?></td>
        <td><?php echo $customer['Customer']['alamat']?></td>
      </tr>
      <?php
      $i++; }
      ?>
      <tr></tr>
    </tbody>
  </table>
  </div>
</div>
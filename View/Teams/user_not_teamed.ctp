<!-- app/View/Teams/user_not_teamed.ctp -->

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <h4 class="modal-title" id="myModalLabel">Daftar Pengguna yang belum berpasangan</h4>

</div>
<div class="modal-body">
  <div class='row'>
    
  <table class='table table-hover table-bordered'>
    <thead>
      <tr>
        <th>No.</th>
        <th>Username</th>
        <th>Nama Pengguna</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $i = 1;
      if(!$user_not_in_team)
        echo "<tr><td colspan='4'>Semua pengguna telah berpasangan</td></tr>";
      foreach ($user_not_in_team as $user) {
      ?>
      <tr>
        <td><?php echo $i?></td>
        <td><?php echo $user['User']['username']?></td>
        <td><?php echo $user['User']['firstname']. ' ' .$user['User']['lastname']?></td>
      </tr>
      <?php
      $i++; }
      ?>
      <tr></tr>
    </tbody>
  </table>
  </div>
</div>
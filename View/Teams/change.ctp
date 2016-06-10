<!-- app/View/Customers/change.ctp -->

<?php
/*
$total_galon = $total_galon['Good']['stokbarang'];
foreach ($galons as $galon) {
    if($galon['Team']['idtim'] != $idtim)
      $total_galon = $total_galon - $galon['Team']['jmlgalon'];
}
*/
?>

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <h4 class="modal-title" id="myModalLabel">Ubah Galon Tim</h4>

</div>
<div class="modal-body">
  <div class='row'>
    <h4>Galon yang dibawa sekarang: <?php echo $team_galon[0]['Team']['jmlgalon']; ?></h4>
    <form class="form-inline" method="post" action="<?php echo $this->Html->url(array('action'=>'change'));?>">
        <div class="form-group">
          <label for="jmlgalon">Galon untuk tim</label>
          <input type="number" class="form-control" id="jmlgalon" name='jmlgalon' max='1000' min="0">
        </div>
        <div class="form-group hidden">
          <input type="text" class="form-control" name='idtim' value="<?php echo $idtim?>">
        </div>
        <button type="submit" class="btn btn-default" id="search" aria-label="Left Align">
        <span class="glyphicon glyphicon-floppy-saved" aria-hidden="true"></span>
        ubah
      </button>
    </form>
  
  </div>
</div>
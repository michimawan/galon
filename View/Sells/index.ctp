<!-- app/View/Sells/index.ctp -->

<div class='row'>
	<form class="form-inline" method="post" action="<?php echo $this->Html->url(array('action'=>'rekapbulanan'));?>">
  		<div class="form-group">
    		<label for="datepicker">Pilih bulan</label>
    		<input type="text" class="form-control" id="datepicker" name='tanggal' placeholder="Pilih Bulan">
  		</div>
        <div class="form-group hidden">
            <input type="text" class="form-control" id="idpegawai" name='id'>
        </div>
  		<button type="button" class="btn btn-default" id="search" aria-label="Left Align">
			<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
			tampilkan
		</button>
	</form>
</div>
<?php debug($sells);?>
<div class='row'>
	<div class='table-responsive'>
		<table class='table table-hover table-striped'>
			<thead></thead>
			<tbody></tbody>
		</table>
	</div>
</div>
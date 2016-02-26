<!-- app/View/Sells/dashboard.ctp -->

<?php $members = count($teams);?>

<div class='row'>
	<div class="col-md-3">
		<?php echo !$master? "": ($members == 0? "": ($master[0]['Master']['status']? "" : $this->Html->link("<span class='glyphicon glyphicon-plus' aria-hidden='true'></span> | Tambah Data Transaksi", array('action' => 'add', $id), array('escape' => false,'class' => 'btn btn-primary'))));?>
	</div>
	<div class="col-md-3">
		<?php echo !$master? "": ($members == 0? "": ($master[0]['Master']['status']? "" : $this->Form->postLink("<span class='glyphicon glyphicon-lock' aria-hidden='true'></span> | Kunci Data Transaksi", array('action' => 'lock', $id, date('Y-m-d'),$master[0]['Master']['start']),  array('escape' => false,'class' => 'btn btn-danger', 'confirm' => 'Anda yakin semua transaksi sudah selesai?'))));?>
	</div>
	<div class="col-md-3">
		<?php echo  !$master? "": ($members == 0? "": ($master[0]['Master']['status']? "" : $this->Html->link("<span class='glyphicon glyphicon-print' aria-hidden='true'></span> | Cetak Halaman Transaksi", array('action' => 'printblank', $id, date('Y-m-d')), array('escape' => false, 'class' => 'btn btn-success'))));?>
	</div>
	<div class="col-md-3">
        <?php echo !$master? "": ($members == 0? "": (!$master[0]['Master']['status']? "" : 
        $this->Html->link("<span class='glyphicon glyphicon-print' aria-hidden='true'></span> | Cetak Transaksi", 
        array('action' => 'printfull', $master[0]['Master']['id']), 
        array('escape' => false,'class' => 'btn btn-success'))));?>
	</div>
</div>
<div class='row'>
	<div class="col-md-4 col-xs-6">
		<h4>Tanggal: <?php echo date('d-m-Y');?></h4>
		<h4>Nama Sales 1: <?php echo $members==0? "Tidak ada Sales yang masuk": ($members==2?$teams[0]['User']['firstname'].' '.$teams[0]['User']['lastname']:$teams[0]['User']['firstname'].' '.$teams[0]['User']['lastname']);?></h4>
	</div>
	<div class="col-md-4 col-xs-6">

		<h4>&nbsp</h4>
		<h4>Galon Sales: <?php echo !$master? "" : ($team_galon['Team']['jmlgalon']);?></h4>
	</div>
	<div class="col-md-4 col-xs-6">
		<h4>Harga: <?php echo $good_price['Good']['hargajual'];?></h4>
		<h4>Nama Sales 2: <?php echo $members==0? "Tidak ada Sales yang masuk": ($members==2?$teams[1]['User']['firstname'].' '.$teams[1]['User']['lastname']:$teams[0]['User']['firstname'].' '.$teams[0]['User']['lastname']);?></h4>
	</div>
</div>
<div class='row'>
	<div class="col-md-6">
		<?php if ($members == 0): ?>
		<h4>Start: </h4>
		<?php else: ?>
		<?php if ($members != 0 && !$master): ?>
		<form class="form-inline" method="post" action="<?php echo $this->Html->url(array('action'=>'set_start_galon'));?>" accept-charset="utf-8">
			<div class="form-group">
				<input type="hidden" name="data[Master][idtim]" value="<?php echo $id?>"/>
			</div>
  			<div class="form-group">
			    <label for="MasterStart">Start</label>
			    <input type="number" class="form-control" id="MasterStart"  name="data[Master][start]" required min='0' max="<?php echo $team_galon['Team']['jmlgalon']?>">
  			</div>
  			<button type="submit" class="form-group btn btn-success" aria-label="Left Align">
				<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
				set start galon
			</button>
		</form>
		<?php else: ?>
		<h4>Start: <?php echo $master[0]['Master']['start']?></h4>
		<?php endif ?>
		<?php endif ?>
		<h4>Finish: <?php echo !$master? "": $master[0]['Master']['finish'] ?></h4>
	</div>
	<div class="col-md-6">
		<h4>Air galon terjual: <?php echo !$master? "": substr($master[0]['Master']['galonterjual'], 0, 5)  ?></h4>
		<h4>Galon kosong masuk: <?php echo !$master? "": $master[0]['Master']['galonkosong']  ?></h4>
	</div>
</div>
<div class='row'>
	<div class='table-responsive'>
		<table class='table table-striped table-hover'>
			<thead>
				<tr>
					<th>No.</th>
					<th>Tanggal</th>
					<th>Kode Pelanggan</th>
					<th>Nama Pelanggan</th>
					<th>Alamat</th>
					<th>Beli Air</th>
					<th>Pnjm Galon</th>
					<th>Kmbl Galon</th>
					<th>Ttl Gln Pnjm</th>
					<th>Bayar</th>
					<th>Hutang</th>
					<?php echo !$datas? "" : (!$datas[0]['Sell']['status']? "<th>Action</th>" : ""); ?>
				</tr>
			</thead>
			<tbody>
				<?php $i = 1;
				if($datas){
					$jmlbeli = 0;
					$jmlpinjam = 0;
					$jmlkembali = 0;
					$totalgalonpinjam = 0;
					$totalbayar = 0;
					$totalhutang = 0;

				foreach ($datas as $data) {
					?>
					<tr>
						<td><?php echo $i?></td>
						<td><?php echo substr($data['Customer']['transaksiterakhir'],0,10);?></td>
						<td><?php echo $data['Customer']['kdpelanggan'];?></td>
						<td><?php echo $data['Customer']['namapelanggan'];?></td>
						<td><?php echo $data['Customer']['alamat'];?></td>
						<td><?php echo $data['Sell']['jmlbeli'];?></td>
						<td><?php echo $data['Sell']['jmlpinjam'];?></td>
						<td><?php echo $data['Sell']['jmlkembali'];?></td>
						<td><?php echo $data['Customer']['galonterpinjam'] ?></td>
						<td><?php echo $data['Sell']['bayar'];?></td>
						<td><?php echo $data['Sell']['hutang']?></td>
						<td>
						<?php
						echo !$data['Sell']['status']? ($this->Html->link('Edit', array('action' => 'edit', $data['Sell']['id']), array('class'=>'btn btn-primary'))) : "";

						echo !$data['Sell']['status']? ($this->Form->postLink('Hapus', array('action' => 'delete', $data['Sell']['id']), array('class'=>'btn btn-danger', 'confirm' => 'yakin ingin menghapus transaksi penjualan ini?'))) : "";
						?>
						</td>
					</tr>
					<?php
					$i++;
					$jmlbeli = $jmlbeli + $data['Sell']['jmlbeli'];
					$jmlpinjam = $jmlpinjam + $data['Sell']['jmlpinjam'];
					$jmlkembali = $jmlkembali + $data['Sell']['jmlkembali'];
					$totalgalonpinjam = $totalgalonpinjam + $data['Customer']['galonterpinjam'];
					$totalbayar = $totalbayar + $data['Sell']['bayar'];
					$totalhutang = $totalhutang + $data['Sell']['hutang'] ;
				}
				?>
					<tr>
						<td colspan='5'>Total</td>
						<td><?php echo $jmlbeli?></td>
						<td><?php echo $jmlpinjam?></td>
						<td><?php echo $jmlkembali?></td>
						<td><?php echo $totalgalonpinjam?></td>
						<td><?php echo $totalbayar?></td>
						<td><?php echo $totalhutang?></td>
					</tr>
				<?php
				} else {
					foreach ($customers as $customer) {
					?>
					<tr>
						<td><?php echo $i?></td>
						<td><?php echo substr($customer['Customer']['transaksiterakhir'],0,10);?></td>
						<td><?php echo $customer['Customer']['kdpelanggan'];?></td>
						<td><?php echo $customer['Customer']['namapelanggan'];?></td>
						<td><?php echo $customer['Customer']['alamat'];?></td>
						<td>&nbsp</td>
						<td>&nbsp</td>
						<td>&nbsp</td>
						<td><?php echo $customer['Customer']['galonterpinjam'];?></td>
						<td>&nbsp</td>
						<td><?php echo $customer['Customer']['hutang'];?></td>
					</tr>
					<?php
					$i++;}
				}
				?>
			</tbody>
		</table>
	</div>
</div>

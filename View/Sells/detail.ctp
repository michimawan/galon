<!-- app/View/Sells/detail.ctp -->

<?php $user = $this->Auth->user(); ?>
<div class='row'>
	<div class="col-md-3">
        <?php echo $this->Html->link("<span class='glyphicon glyphicon-print' aria-hidden='true'></span> | Cetak Transaksi", array('action' => 'printfull', $master['Master']['id']), array('escape' => false,'class' => 'btn btn-success')); ?>
	</div>
</div>
<div class='row'>
	<div class="col-md-4 col-xs-6">
		<h4>Tanggal: <?php echo $master['Master']['date'];?></h4>
		<h4>Nama Sales 1: <?php echo $team[0]['User']['kehadiran']? $team[0]['User']['firstname'].' '.$team[0]['User']['lastname'] : $team[1]['User']['firstname'].' '.$team[1]['User']['lastname'] ;?></h4>
	</div>
	<div class="col-md-4 col-xs-6">

		<h4>&nbsp</h4>
        <h4>Galon Sales: <?php echo $master['Master']['galon_sales'];?></h4>
	</div>
	<div class="col-md-4 col-xs-6">
		<h4>Harga: <?php echo $master['Master']['harga_galon'];?></h4>
		<h4>Nama Sales 2: <?php echo $team[1]['User']['kehadiran']? $team[1]['User']['firstname'].' '.$team[1]['User']['lastname'] : $team[0]['User']['firstname'].' '.$team[0]['User']['lastname'] ;?></h4>
	</div>
</div>
<div class='row'>
	<div class="col-md-6">
		<h4>Start: <?php echo $master['Master']['start']?></h4>
		<h4>Finish: <?php echo $master['Master']['finish'] ?></h4>
	</div>
	<div class="col-md-6">
		<h4>Air galon terjual: <?php echo substr($master['Master']['galonterjual'], 0, 5)  ?></h4>
		<h4>Galon kosong masuk: <?php echo $master['Master']['galonkosong']  ?></h4>
	</div>
</div>
<div class='row'>
	<div class='table-responsive'>
		<table class='table table-striped table-hover'>
			<thead>
				<tr>
					<th>No.</th>
					<th>Waktu</th>
					<th>Nama Pelanggan</th>
					<th>Alamat</th>
					<th>Kode Pelanggan</th>
					<th>Beli Air</th>
					<th>Bayar</th>
					<th>Pnjm Galon</th>
					<th>Kmbl Galon</th>
					<th>Ttl Gln Pnjm</th>
					<th>Hutang</th>
				</tr>
			</thead>
			<tbody>
				<?php $i = 1;
                $jmlbeli = 0;
                $jmlpinjam = 0;
                $jmlkembali = 0;
                $totalgalonpinjam = 0;
                $totalbayar = 0;
                $totalhutang = 0;
				if($sells){

				foreach ($sells as $data) {
					?>
					<tr>
						<td><?php echo $i?></td>
						<td><?php echo substr($data['Sell']['date'],10);?></td>
						<td><?php echo $data['Customer']['namapelanggan'];?></td>
						<td><?php echo $data['Customer']['alamat'];?></td>
						<td><?php echo $data['Customer']['kdpelanggan'];?></td>
						<td><?php echo $data['Sell']['jmlbeli'];?></td>
						<td><?php echo $data['Sell']['bayar'];?></td>
						<td><?php echo $data['Sell']['jmlpinjam'];?></td>
						<td><?php echo $data['Sell']['jmlkembali'];?></td>
						<td><?php echo $data['Customer']['galonterpinjam'] ?></td>
						<td><?php echo $data['Sell']['hutang']?></td>
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
                }
                ?>
                <?php
                if($customers) {
                    foreach($customers as $customer) {
					?>
					<tr>
						<td><?php echo $i?></td>
						<td>Belum ada transaksi</td>
						<td><?php echo $customer['Customer']['namapelanggan'];?></td>
						<td><?php echo $customer['Customer']['alamat'];?></td>
						<td><?php echo $customer['Customer']['kdpelanggan'];?></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td><?php echo $customer['Customer']['galonterpinjam'] ?></td>
						<td></td>
					</tr>
                    <?php
					$i++;
                    }
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
			</tbody>
		</table>
	</div>
</div>

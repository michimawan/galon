
<?php $members = count($teams); ?>

<div class='row'>
	<div class="col-md-4">
		<h4>Tanggal: <?php echo date('d-m-Y');?></h4>
		<h4>Harga: <?php echo $good_price['Good']['hargajual'];?></h4>
	</div>
	<div class="col-md-4">
		<h4>Nama Sales 1: <?php echo $members==0? "Tidak ada Sales yang masuk": ($members==2?$teams[0]['User']['firstname'].' '.$teams[0]['User']['lastname']:$teams[0]['User']['firstname'].' '.$teams[0]['User']['lastname']);?></h4>
		<h4>Nama Sales 2: <?php echo $members==0? "Tidak ada Sales yang masuk": ($members==2?$teams[1]['User']['firstname'].' '.$teams[1]['User']['lastname']:$teams[0]['User']['firstname'].' '.$teams[0]['User']['lastname']);?></h4>
	</div>
</div>
<div class='row'>
	<div class="col-md-6">
		<h4>Start: <?php echo $master[0]['Master']['start']?></h4>
		<h4>Finish: </h4>
	</div>
	<div class="col-md-6">
		<h4>Air galon terjual: </h4>
		<h4>Galon kosong masuk: </h4>
	</div>
</div>
<div class='row'>
	<div class='table-responsive'>
		<table class='table table-bordered table-striped table-hover '>
			<thead>
				<tr>
					<th>No.</th>
                    <th>Hari</th>
					<th>Kd Plgn</th>
					<th>Nama Plgn</th>
					<th>Alamat</th>
					<th>Beli Air</th>
					<th>Pjm Gln</th>
					<th>Kmb Gln</th>
					<th>Ttl Pjm Gln</th>
					<th class='wider'>Byr</th>
					<th class='wider'>Htg</th>
				</tr>
			</thead>
			<tbody>
                <?php $i = 1; ?>
                <?php foreach($customers as $customer) : ?>
					<tr>
						<td><?php echo $i?></td>
						<td><?php echo $customer['Customer']['harikunjungan'];?></td>
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
                <?php $i++; ?>
                <?php endforeach ?>
				<tr>
					<td colspan=5>Total</td>
					<td>&nbsp</td>
					<td>&nbsp</td>
					<td>&nbsp</td>
					<td>&nbsp</td>
					<td>&nbsp</td>
					<td>&nbsp</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

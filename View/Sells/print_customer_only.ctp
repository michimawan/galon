
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
        <h4>Start: </h4>
        <h4>Finish: </h4>
    </div>
    <div class="col-md-6">
        <h4>Air galon terjual: </h4>
        <h4>Galon kosong masuk: </h4>
    </div>
</div>
<div class='row'>
    <div class='table-responsive'>
        <table class='table table-bordered table-striped'>
            <thead>
                <tr>
                    <th class="col-wide-1">No.</th>
                    <th class="col-wide-1">Transaksi terakhir</th>
                    <th class="col-wide-2">Kd Plgn</th>
                    <th class="col-wide-2">Nama Plgn</th>
                    <th class="col-wide-2">Alamat</th>
                    <th class="col-wide-1">Beli Air</th>
                    <th class="col-wide-1">Pjm Gln</th>
                    <th class="col-wide-1">Kmb Gln</th>
                    <th class="col-wide-1">Ttl Pjm Gln</th>
                    <th class="col-wide-3">Byr</th>
                    <th class="col-wide-3">Htg</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php foreach($customers as $customer) : ?>
                    <tr>
                        <td class="col-wide-1"><?php echo $i?></td>
                        <td class="col-wide-1"><?php echo substr($customer['Customer']['transaksiterakhir'], 0, 10);?></td>
                        <td class="col-wide-2"><?php echo $customer['Customer']['kdpelanggan'];?></td>
                        <td class="col-wide-2"><?php echo $customer['Customer']['namapelanggan'];?></td>
                        <td class="col-wide-2"><?php echo $customer['Customer']['alamat'];?></td>
                        <td class="col-wide-1">&nbsp</td>
                        <td class="col-wide-1">&nbsp</td>
                        <td class="col-wide-1">&nbsp</td>
                        <td class="col-wide-1"><?php echo $customer['Customer']['galonterpinjam'];?></td>
                        <td class="col-wide-3">&nbsp</td>
                        <td class="col-wide-3"><?php echo $customer['Customer']['hutang'];?></td>
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

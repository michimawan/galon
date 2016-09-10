<!-- app/View/Customers/debt.ctp -->


<div class="row">

    <div class="col-xs-12 col-md-10">
    <h1><?php echo $team ?></h1>

    <div class='table-responsive'>
    <table class='table table-condensed table-hover table-stripped'>
        <thead>
            <tr>
                <th>No.</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Hutang</th>
                <th>Pinjam</th>
                <th>Hari</th>
                <th>Transaksi Terakhir</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(!$customers){
            ?>
            <tr>
                <td colspan=8>Belum ada pelanggan yang memiliki piutang</td>
            </tr>
            <?php
            }
            else {
            ?>
            <?php $count=0; ?>
            <?php foreach($customers as $customer):
                $count ++;
            ?>
            <tr>
                <td><?php echo $count; ?></td>
                <td><?php echo $customer['Customer']['kdpelanggan'];?></td>
                <td><?php echo $customer['Customer']['namapelanggan'];?></td>
                <td><?php echo $customer['Customer']['alamat']?></td>
                <td><?php echo $customer['Customer']['hutang']; ?></td>
                <td><?php echo $customer['Customer']['galonterpinjam'];?></td>
                <td><?php echo $customer['Customer']['harikunjungan'];?></td>
                <td><?php echo $customer['Customer']['transaksiterakhir'];?></td>
            </tr>
            <?php endforeach; ?>
            <?php unset($customer); } ?>
        </tbody>
    </table>
    </div>

    </div>
</div>

<!-- app/View/Teams/print_customer_in_team.ctp -->

<div class='row hidden-print'>
    <div class="col-md-3">
        <?php echo $this->Html->link('Kembali', ['action' => 'index'], ['class' => 'btn btn-success']); ?>
    </div>
</div>
<div class='row'>
    <div class="col-xs-6 col-xs-6">
        <h4>Nama Sales 1: <?php echo  $teams[0]['User']['firstname'].' '.$teams[0]['User']['lastname'] ;?></h4>
    </div>
    <div class="col-xs-6 col-xs-6">
        <h4>Nama Sales 2: <?php echo  $teams[1]['User']['firstname'].' '.$teams[1]['User']['lastname'] ;?></h4>
    </div>
</div>
<div class='row'>
    <div class='table-responsive'>
        <table class='table table-striped table-hover'>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Transaksi terakhir</th>
                    <th>Nama Pelanggan</th>
                    <th>Alamat</th>
                    <th>Kode Pelanggan</th>
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
                    ?>
                    <?php
                    if($customers) {
                        foreach($customers as $customer) {
                    ?>
                    <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo substr($customer['Customer']['transaksiterakhir'], 0, 10);?></td>
                        <td><?php echo $customer['Customer']['namapelanggan'];?></td>
                        <td><?php echo $customer['Customer']['alamat'];?></td>
                        <td><?php echo $customer['Customer']['kdpelanggan'];?></td>
                    </tr>
                    <?php
                            $i++;
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="5">Tidak ada data</td>
                    </tr>
                    <?php
                    }
                    ?>
            </tbody>
        </table>
    </div>
</div>

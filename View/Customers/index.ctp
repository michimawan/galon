<!-- app/View/Customers/index.ctp -->

<?php
$params = [
    'action' => 'filter',
    'controllers' => 'customers',
    'filters' => $filters,
    'model' => 'Customer'
];
echo $this->element('filter', $params);
?>
<div class="row">
    <div class="col-xs-12 col-md-12">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bs-example-modal-lg">Tambah Pelanggan</button>
            <?php
            echo $this->Html->link('Lihat Piutang Pelanggan', array('action' => 'debt'), array('escape' => false, 'class' => 'btn btn-danger'));
            ?>
        </div>
    </div>

    <div class="col-xs-12 col-md-12">
    <h1>Daftar Pelanggan</h1>
    <div class='table-responsive'>
    <table class='table table-condensed table-hover table-stripped'>
        <thead>
            <tr>
                <th>No.</th>
                <th><?php echo $this->Paginator->sort('kdpelanggan', 'Kode Pelanggan');?></th>
                <th><?php echo $this->Paginator->sort('namapelanggan', 'Nama Pelanggan', array('direction' => 'asc'));?>  </th>
                <th><?php echo $this->Paginator->sort('alamat', 'Alamat');?></th>
                <th><?php echo $this->Paginator->sort('PairTeamCustomer.idtim', 'Sales');?></th>
                <th><?php echo $this->Paginator->sort('harikunjungan', 'Hari Kunjungan');?></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(!$customers){
            ?>
            <tr>
                <td colspan=7>Belum ada pelanggan yang ditambahkan</td>
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
                <td><?php echo isset($customer['PairTeamCustomer']['idtim']) && ! empty($customer['PairTeamCustomer']['idtim']) ? $list_team[$customer['PairTeamCustomer']['idtim']] : "belum ada sales"; ?></td>
                <td><?php echo $customer['Customer']['harikunjungan'];?></td>
                <td>
                    <div class="btn-group" role="group">
                    <?php
                    echo $this->Html->link(    "Edit",   array('action'=>'edit', $customer['Customer']['id']), array('class' => 'btn btn-info'));
                    echo $this->Form->postLink(    "Hapus", array('action'=>'delete', $customer['Customer']['id']), array('class' => 'btn btn-danger', 'confirm'=>'apakah yakin mau hapus '.$customer['Customer']['namapelanggan']));
                    ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php unset($customer); } ?>
        </tbody>
    </table>
    </div>
    <div class="paging">
        <?php
            echo $this->Paginator->prev() .'  '. $this->Paginator->numbers(array('before'=>false, 'after'=>false,'separator'=> false)) .'  '. $this->Paginator->next();
        ?>
    </div>
    </div>
</div>

<?php echo $this->element('../Customers/add'); ?>

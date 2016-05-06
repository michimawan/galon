<!-- app/View/Goods/index.ctp -->

<div class="row">
    <?php
    $user = $this->Auth->user();
    if($user['role'] != 'pegawai'){ ?>
    <div class="col-xs-12 col-md-12">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_addgoods">Tambah Barang</button>
        </div>
    </div>
    <?php } ?>
    <div class="col-xs-12 col-md-12">
    <h1>Daftar Barang</h1>
    <div class='table-responsive'>
    <table class='table table-condensed table-hover table-stripped'>
        <thead>
            <tr>
                <th>No.</th>
                <th><?php echo $this->Paginator->sort('kdbarang', 'Kode Barang', array('direction' => 'asc'));?>  </th>
                <th><?php echo $this->Paginator->sort('namabarang', 'Nama Barang');?>  </th>
                <th><?php echo $this->Paginator->sort('hargabeli', 'Harga Beli');?></th>
                <th><?php echo $this->Paginator->sort('hargajual', 'Harga Jual');?></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(!$goods){
            ?>
            <tr>
                <td colspan=4>Belum ada data barang yang ditambahkan</td>
            </tr>
            <?php
            }
            else {
            ?>
            <?php $count=0; ?>
            <?php foreach($goods as $good):
                $count ++;
            ?>
            <tr>
                <td><?php echo $count; ?></td>
                <td><?php echo $good['Good']['kdbarang'];?></td>
                <td><?php echo $good['Good']['namabarang'];?></td>
                <td><?php echo $good['Good']['hargabeli']?></td>
                <td><?php echo $good['Good']['hargajual']; ?></td>
                <td>
                    <div class="btn-group" role="group">
                    <?php
                    echo $this->Html->link(    "Edit",   array('action'=>'edit', $good['Good']['id']), array('class' => 'btn btn-info'));
                    echo $this->Form->postLink(    "Hapus", array('action'=>'delete', $good['Good']['id']), array('class' => 'btn btn-danger', 'confirm'=>'apakah yakin mau hapus '.$good['Good']['namabarang']));
                    ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php unset($good); } ?>
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

<?php echo $this->element('../Goods/add'); ?>
<script type="text/javascript">
    /*
    $('.modal-stock').click(function(){
        var datas = $(this).attr('href').split("/");
        var id = datas[datas.length-1];
        $('#GoodId').val(id);
        $('#GoodAddStockForm').attr('action',  $('#GoodAddStockForm').attr('action').substr(0, $('#GoodAddStockForm').attr('action').length-1) + id) ;
    });
    */
</script>

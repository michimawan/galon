<!-- app/View/Goods/index.ctp -->

<div class="row">
    <div class="col-xs-3 col-md-2">
        <div class="btn-group-vertical" role="group">
            <div class='btn-group' role='group'>
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal_addgoods">Tambah Barang</button>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-10">
    <h1>Daftar Barang</h1>
    <h4>Menampilkan <?php echo $this->params['paging']['Good']['count'] < 20? ($this->params['paging']['Good']['count']." dari ".$this->params['paging']['Good']['count']." record") : ("20 dari ".$this->params['paging']['Good']['count']." record") ?></h4>
    <div class='table-responsive'>
    <table class='table table-condensed table-hover table-stripped'>
        <thead>
            <tr>
                <th>No.</th>
                <th><?php echo $this->Paginator->sort('kdbarang', 'Kode Barang', array('direction' => 'asc'));?>  </th>
                <th><?php echo $this->Paginator->sort('namabarang', 'Nama Barang');?>  </th>
                <th><?php echo $this->Paginator->sort('hargabeli', 'Harga Beli');?></th>
                <th><?php echo $this->Paginator->sort('hargajual', 'Harga Jual');?></th>
                <!-- <th><?php echo $this->Paginator->sort('stokbarang', 'Stok Barang');?></th> -->
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
                <!-- <td><?php echo $good['Good']['stokbarang']; ?></td> -->
                <td>
                <?php // echo $this->Html->link(    "Tambah Stok",   array('action'=>'#', $good['Good']['id']), array('class' => 'btn btn-success modal-stock', 'data-toggle' => 'modal', 'data-target' => '#add_stock')); ?> 

                <?php echo $this->Html->link(    "Edit",   array('action'=>'edit', $good['Good']['id']), array('class' => 'btn btn-info')); ?> 

                <?php
                    echo $this->Form->postLink(    "Hapus", array('action'=>'delete', $good['Good']['id']), array('class' => 'btn btn-danger', 'confirm'=>'apakah yakin mau hapus '.$good['Good']['namabarang']));
                ?>
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
<?php // echo $this->element('../Goods/add_stock'); ?>
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
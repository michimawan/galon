<!-- app/View/Goods/add.ctp -->

<div class="modal fade bs-example-modal-lg" id='modal_addgoods' tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Tambah Barang</h4>
            </div>
            <div class="modal-body">
        <?php echo $this->Form->create('Good', array('action' => 'add'));?>
            <?php
            echo $this->Form->input('namabarang', array('label' => 'Nama Barang', 'class' => 'form-control'));
            echo $this->Form->input('hargabeli', array('type' => 'number', 'min' => '1000', 'max'=>'1000000', 'label' => 'Harga Beli', 'class' => 'form-control'));
            echo $this->Form->input('hargajual', array('type' => 'number', 'min' => '1000', 'max'=>'1000000', 'label' => 'Harga Jual','class' => 'form-control'));
            // echo $this->Form->input('stokbarang', array('type' => 'number', 'label' => 'Stok Barang', 'min' => '0', 'max'=>'1000', 'class' => 'form-control'));
            echo $this->Form->submit('Tambah Barang', array('class' => 'form-submit',  'title' => 'klik untuk menambah data barang') ); 
            ?>
        <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
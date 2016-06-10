<div class="modal fade bs-example-modal-sm" id='add_stock' tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Tambah Stok Barang</h4>
          </div>
          <div class="modal-body">
          <?php echo $this->Form->create('Good', array('action' => 'add_stock/'));?>
            <?php
            echo $this->Form->input('id', array('class' => 'form-control hidden'));
            echo $this->Form->input('stokbarang', array('type' => 'number', 'min' => '10', 'max'=>'1000', 'label' => 'Tambahan Stok', 'class' => 'form-control'));
            echo $this->Form->submit('Tambah Stok Barang', array('class' => 'form-submit',  'title' => 'klik untuk menambah stok barang') ); 
            ?>
          <?php echo $this->Form->end(); ?>
          </div>
      </div>
    </div>
</div>
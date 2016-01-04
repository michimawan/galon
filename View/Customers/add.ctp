<!-- app/View/Customers/add.ctp -->

<div class="modal fade bs-example-modal-lg" id='modal_addgoods' tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Tambah Pelanggan</h4>
            </div>
            <div class="modal-body">
        <?php echo $this->Form->create('Customer', array('action' => 'add'));?>
            <?php
            echo $this->Form->input('namapelanggan', array('label' => 'Nama Pelanggan', 'class' => 'form-control'));
            echo $this->Form->input('alamat', array('class' => 'form-control'));
            echo $this->Form->input('nohp', array('label' => 'No. HP', 'type' => 'number', 'class' => 'form-control'));
            echo $this->Form->submit('Tambah Pelanggan', array('class' => 'form-submit',  'title' => 'klik untuk menambah data pelanggan') ); 
            ?>
        <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
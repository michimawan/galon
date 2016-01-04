<!-- app/View/Customers/edit.ctp -->

<div class="row">
    <div class="col-xs-3 col-md-2">
        <div class="btn-group-vertical" role="group">
            <div class='btn-group' role='group'>
            <?php echo $this->Html->link( "Lihat Daftar Pelanggan", array('action'=>'index'), array('class' => 'btn btn-default')); ?>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-10">
    <?php echo $this->Form->create('Customer'); ?>
        <fieldset>
            <h1><?php echo __('Edit Pelanggan'); ?></h1>
            <?php 
            echo $this->Form->hidden('id', array('value' => $this->data['Customer']['id'], 'class' => 'form-control'));
            echo $this->Form->input('kdpelanggan', array('readonly' => 'readonly', 'label' => 'Kode Pelanggan tidak dapat diubah!', 'class' => 'form-control'));
            echo $this->Form->input('namapelanggan', array('readonly' => 'readonly', 'label' => 'Nama Pelanggan tidak dapat diubah!', 'class' => 'form-control'));
            echo $this->Form->input('alamat', array('class' => 'form-control'));
            echo $this->Form->input('nohp', array('label' => 'No. HP', 'type' => 'number', 'class' => 'form-control'));
            
            echo $this->Form->submit('Edit Pelanggan', array('class' => 'form-submit',  'title' => 'klik untuk mengedit pelanggan') ); 
    ?>
        </fieldset>
    <?php echo $this->Form->end(); ?>
    </div>
</div>
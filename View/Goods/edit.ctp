<!-- app/View/Goods/edit.ctp -->

<div class="row">
    <div class="col-xs-3 col-md-2">
        <div class="btn-group-vertical" role="group">
            <div class='btn-group' role='group'>
            <?php echo $this->Html->link( "Lihat Daftar Barang", array('action'=>'index'), array('class' => 'btn btn-default')); ?>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-10">
    <?php echo $this->Form->create('Good'); ?>
            <h1><?php echo __('Edit Data Barang'); ?></h1>
            <?php 
            echo $this->Form->hidden('id', array('value' => $this->data['Good']['id']));
            echo $this->Form->input('kdbarang', array('label' => 'Kode Barang tidak dapat diubah', 'readonly' => true, 'class' => 'form-control'));
            echo $this->Form->input('namabarang', array('label' => 'Nama Barang', 'class' => 'form-control'));
            echo $this->Form->input('hargabeli', array('type' => 'number', 'label' => 'Harga Beli', 'min' => '0', 'max' => '100000', 'class' => 'form-control'));
            echo $this->Form->input('hargajual', array('type' => 'number', 'label' => 'Harga Jual', 'min' => '0', 'max' => '100000', 'class' => 'form-control'));
            echo $this->Form->submit('Edit Data Barang', array('class' => 'form-submit',  'title' => 'klik untuk mengedit data barang') ); 
    ?>
    <?php echo $this->Form->end(); ?>
    </div>
</div>
<!-- app/View/Customers/edit.ctp -->

<?php
$user = $this->Auth->user();
$days = array(
    'senin' => 'Senin',
    'selasa' => 'Selasa',
    'rabu' => 'Rabu',
    'kamis' => 'Kamis',
    'jumat' => 'Jumat',
    'sabtu' => 'Sabtu'
);
?>
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
            echo $this->Form->input('PairTeamCustomer.id', array('class' => 'form-control hidden'));
            echo $this->Form->input('PairTeamCustomer.idcustomer', array('class' => 'form-control hidden', 'label' => false, 'div' => false));
            echo $this->Form->input('PairTeamCustomer.idtim', array(
                'type' => 'select',
                'label' => 'Daftar Tim',
                'options' => $list_team,
                'class' => 'form-control',
                'div' => array('class' => 'form-group')
            ));
            echo $this->Form->input('Customer.harikunjungan', array(
                'type' => 'select',
                'label' => 'Pilih hari',
                'options' => $days,
                'class' => 'form-control',
                'div' => array('class' => 'form-group')
            ));
            if($user['role'] == 'admin')
            echo $this->Form->input('Customer.galonterpinjam', array(
                'label' => 'Galon Dipinjamkan',
                'class' => 'form-control',
                'min' => 0,
                'max' => 1000,
                'div' => array('class' => 'form-group')
            ));
            echo $this->Form->submit('Edit Pelanggan', array('class' => 'form-submit',  'title' => 'klik untuk mengedit pelanggan') );
    ?>
        </fieldset>
    <?php echo $this->Form->end(); ?>
    </div>
</div>

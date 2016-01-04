<!-- app/View/Users/add.ctp -->

<div class="row">
    <div class="col-xs-3 col-md-2">
        <?php 
            if($this->Session->check('Auth.User')){
            echo $this->Html->link( "Lihat Daftar Pegawai",   array('action'=>'index'), array('class'=>'btn btn-default')); 
            }
        ?>
    </div>
    <div class="col-xs-12 col-md-10">
        <?php echo $this->Form->create('User');?>
        <fieldset>
            <h1><?php echo __('Tambah Pegawai'); ?></h1>
            <?php
            echo $this->Form->input('username', array('class' => 'form-control'));
            echo $this->Form->input('firstname', array('label' => 'Nama Depan', 'class' => 'form-control'));
            echo $this->Form->input('lastname', array('label' => 'Nama Belakang', 'class' => 'form-control'));
            echo $this->Form->input('nohp', array('label' => 'No. HP', 'type' => 'number', 'class' => 'form-control'));
            echo $this->Form->input('password', array('class' => 'form-control'));
            echo $this->Form->input('password_confirm', array('label' => 'Confirm Password *', 'maxLength' => 255, 'title' => 'Confirm password', 'type'=>'password', 'class' => 'form-control'));
            echo $this->Form->input('role', array(
                'options' => array( 'admin' => 'Admin', 'pegawai' => 'Pegawai'),
                'label' => 'Jabatan',
                'default' => 'pegawai',
                'class' => 'form-control'
            ));
            
            echo $this->Form->submit('Tambah Pegawai', array('class' => 'form-submit',  'title' => 'klik untuk menambah pegawai') ); 
            ?>
        </fieldset>
    <?php echo $this->Form->end(); ?>
    </div>
</div> 
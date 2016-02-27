<!-- app/View/Users/edit.ctp -->
<div class="row">
    <div class="col-xs-3 col-md-2">
        <div class="btn-group-vertical" role="group">
            <div class='btn-group' role='group'>
            <?php 
            $current_user = $this->Auth->User();
            if($current_user['role'] != 'pegawai')
            echo $this->Html->link( "Daftar Pegawai", array('action'=>'index'), array('escape' => false, 'class' => 'btn btn-default')); 
            else {
                echo $this->Html->link( "Lihat Data", array('action'=>'view', $current_user['id']), array('escape' => false, 'class' => 'btn btn-default')); 
            }
            ?>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-10">
    <?php echo $this->Form->create('User'); ?>
        <fieldset>
            <h1><?php echo __('Edit Pegawai'); ?></h1>
            <?php 
            echo $this->Form->hidden('id', array('value' => $this->data['User']['id'], 'class' => 'form-control'));
            echo $this->Form->input('username', array( 'readonly' => 'readonly', 'label' => 'Username tidak dapat diubah!', 'class' => 'form-control'));
            echo $this->Form->input('firstname', array('label' => 'Nama Depan', 'class' => 'form-control'));
            echo $this->Form->input('lastname', array('label' => 'Nama Belakang', 'class' => 'form-control'));
            echo $this->Form->input('nohp', array('label' => 'No. HP', 'type' => 'number', 'class' => 'form-control'));
            echo $this->Form->input('password_update', array( 'label' => 'New Password (biarkan kosong jika tidak diubah)', 'maxLength' => 255, 'type'=>'password','required' => 0, 'class' => 'form-control'));
            echo $this->Form->input('password_confirm_update', array('label' => 'Confirm New Password *', 'maxLength' => 255, 'title' => 'Confirm New password', 'type'=>'password','required' => 0, 'class' => 'form-control'));
             
            if($current_user['role'] == 'pegawai'){
                echo $this->Form->input('role', array(
                    'options' => array('pegawai' => 'Pegawai'),
                    'label' => 'Jabatan',
                    'class' => 'form-control',
                    'readonly'
                ));    
            } else {
                echo $this->Form->input('role', array(
                    'options' => array( 'admin' => 'Admin', 'pegawai' => 'Pegawai'),
                    'label' => 'Jabatan',
                    'class' => 'form-control'
                ));
            }
            
            echo $this->Form->submit('Edit Pegawai', array('class' => 'form-submit',  'title' => 'klik untuk merubah data pegawai') ); 
    ?>
        </fieldset>
    <?php echo $this->Form->end(); ?>
    </div>
</div>

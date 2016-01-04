<!-- app/View/Users/view.ctp -->

<div class="row">
    <div class="col-xs-3 col-md-2">
        <div class="btn-group-vertical" role="group">
            <div class='btn-group' role='group'>
            <?php 
            $current_user = $this->Auth->User();
            if($current_user['role'] != 'pegawai')
            echo $this->Html->link( "Daftar Pegawai", array('action'=>'index'), array('escape' => false, 'class' => 'btn btn-default')); 
            else {
                echo $this->Html->link( "Edit Profil", array('action'=>'edit', $current_user['id']), array('escape' => false, 'class' => 'btn btn-default')); 
            }
            ?>
            </div>
        </div>
    </div>
    
    <div class="col-xs-12 col-md-10">
        <div>
            <h1>Detail Pegawai</h1>
            <h3>Username: <?php echo $user['User']['username'];?></h3>
            <h3>Nama : <?php echo $user['User']['firstname']. " " .$user['User']['lastname'];?></h3>
            <h3>No. HP: <?php echo $user['User']['nohp'];?></h3>
            <?php echo $this->Html->link( "Lihat Presensi", array('controller'=>'attendances','action'=>'rekapbulanan', $user['User']['id'], date('Y'), date('m')), array('escape' => false, 'class' => 'btn btn-default')); ?>
        </div>
        <div>
            <h1>Teman Tim Pegawai</h1>
            <h3>Username: <?php echo $partner['User']['username'];?></h3>
            <h3>Nama : <?php echo $partner['User']['firstname']. " " .$partner['User']['lastname'];?></h3>
            <h3>No. HP: <?php echo $partner['User']['nohp'];?></h3>
            <?php echo $this->Html->link( "Lihat Presensi", array('controller'=>'attendances','action'=>'rekapbulanan', $partner['User']['id'], date('Y'), date('m')), array('escape' => false, 'class' => 'btn btn-default')); ?>
        </div>
    </div>
</div>
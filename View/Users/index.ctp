<!-- app/View/Users/index.ctp -->
<?php //debug($this->Auth->user()) ?>
<div class="row">
    <div class="col-xs-3 col-md-2">
        <div class="btn-group-vertical" role="group">
            <div class='btn-group' role='group'>
            <?php echo $this->Html->link( "Tambah Pegawai", array('action'=>'add'), array('escape' => false, 'class' => 'btn btn-default')); ?>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-md-10">
    <h1>Daftar Pegawai</h1>
    <h4>Menampilkan <?php echo $this->params['paging']['User']['count'] < 20? ($this->params['paging']['User']['count']." dari ".$this->params['paging']['User']['count']." record") : ("20 dari ".$this->params['paging']['User']['count']." record") ?></h4>
    <div class='table-responsive'>
    <table class='table table-condensed table-hover table-stripped'>
        <thead>
            <tr>
                <th>No.</th>
                <th><?php echo $this->Paginator->sort('username', 'Username', array('direction' => 'asc'));?>  </th>
                <th><?php echo $this->Paginator->sort('firstname', 'Nama');?></th>
                <th><?php echo $this->Paginator->sort('nohp', 'No. Hp');?></th>
                <th><?php echo $this->Paginator->sort('role','Role');?></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if(!$users){
            ?>
            <tr>
                <td colspan=4>Belum ada pegawai yang ditambahkan</td>
            </tr>
            <?php
            }
            else {
            ?>                        
            <?php $count=0; ?>
            <?php foreach($users as $user):
                $count ++;
            ?>
            <tr>
                <td><?php echo $count; ?></td>
                <td>
                    <?php echo $this->Html->link($user['User']['username'], array('action' => 'view', $user['User']['id']));
                    ?>
                </td>
                <td><?php echo $user['User']['firstname']." ".$user['User']['lastname']; ?></td>
                <td><?php echo $user['User']['nohp']; ?></td>
                <td><?php echo $user['User']['role']; ?></td>
                <td>
                <?php echo $this->Html->link(    "Edit",   array('action'=>'edit', $user['User']['id']), array('class' => 'btn btn-info')); ?> 
                <?php
                    if( $user['User']['status'] != 0){ 
                        echo $this->Html->link(    "De-Active", array('action'=>'delete', $user['User']['id']), array('class' => 'btn btn-danger'));
                    }
                    else{
                        echo $this->Html->link(    "Re-Activate", array('action'=>'activate', $user['User']['id']), array('class' => 'btn btn-danger'));
                        }
                ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php unset($user); } ?>
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

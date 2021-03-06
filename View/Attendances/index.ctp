<!-- app/View/Attendances/index.ctp -->

<?php
$params = [
    'action' => 'filter',
    'controllers' => 'attendances',
    'filters' => $filters,
    'model' => 'Attendance'
];
echo $this->element('filter', $params);
?>
<div class="row">
    <div class="col-xs-12 col-md-12">
    <h1>Daftar Pegawai</h1>
    <?php
    if($users)
    echo $this->Html->link("Semua Pegawai Masuk", array('action'=>'present'), array('class' => 'btn btn-primary', 'confirm'=>"apakah yakin semua pegawai masuk?"));
    ?>
    <div class='table-responsive'>
    <table class='table table-condensed table-hover table-stripped'>
        <thead>
            <tr>
                <th>No.</th>
                <th><?php echo $this->Paginator->sort('username', 'Username');?>  </th>
                <th><?php echo $this->Paginator->sort('firstname', 'Nama Petugas');?></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(!$users){
            ?>
            <tr>
                <td colspan=4>Semua pegawai sudah dipresensi</td>
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
                <td><?php echo $this->Html->link( $user['User']['username'], array('controller'=>'attendances','action'=>'rekapbulanan', $user['User']['id'], date('Y'), date('m')), array('escape' => false)); ?></td>
                <td><?php echo $user['User']['firstname']. " ". $user['User']['lastname']?></td>
                <td>
                <?php
                echo $this->Html->link(    "Tidak Masuk", array('action'=>'absent', $user['User']['id']), array('class' => 'btn btn-warning', 'confirm'=>"apakah yakin pegawai ". $user['User']['firstname']. " tidak masuk?"));
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

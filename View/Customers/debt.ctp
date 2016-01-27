<!-- app/View/Customers/debt.ctp -->


<div class="row">
    <div class="col-xs-3 col-md-2">
        <div class="btn-group-vertical" role="group">
            <button type="button" class="btn btn-default" data-toggle="modal" data-target=".bs-example-modal-lg">Tambah Pelanggan</button>
            <?php
            echo $this->Html->link('Lihat Daftar Pelanggan', array('action' => 'index'), array('escape' => false, 'class' => 'btn btn-default'));
            echo $this->Html->link('<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Cetak Piutang', array('action' => 'printdebt', $teams?$teams[0]['Team']['idtim']:""), array('escape' => false, 'class' => 'btn btn-default'));
            ?>
            <?php //echo $this->Html->link( "Tambah Pelanggan", array('action'=>'add'), array('escape' => false, 'class' => 'btn btn-default')); ?>
        </div>
    </div>

    <div class="col-xs-12 col-md-10">
    <h1>Daftar Piutang Pelanggan</h1>
    <?php
    $user = $this->Auth->User();
    ?>
    <?php if ($user['role'] != 'pegawai'): ?>

    <form class="form-inline" action="<?php echo $this->Html->url(array('action' => 'debt'));?>" method='post'>
        <div class="form-group">
            <?php 
            if($this->params->pass != null){
            echo $this->Form->input('idtim', array(
                'type' => 'select',
                'options' => $list_team,
                'class' => 'form-control',
                'label' => false,
                'div' => false,
                'value' => $this->params->pass,
                'empty' => 'Tampilkan Semua'
                )
            );
            } else {
              echo $this->Form->input('idtim', array(
                'type' => 'select',
                'options' => $list_team,
                'class' => 'form-control',
                'label' => false,
                'div' => false,
                'empty' => 'Tampilkan Semua'
                )
            );  
            }
            ?>
        </div>
    </form>
    <?php endif ?>
    <?php if($teams){?>
    <?php foreach ($teams as $team): ?>
    <h4><?php echo $team['User']['firstname'].' '.$team['User']['lastname']?></h4>    
    <?php endforeach ?>
    <?php } else {
        echo "<h4>Semua Sales</h4>";
    }?>
    <h4>Menampilkan <?php echo $this->params['paging']['PairTeamCustomer']['count'] < 20? ($this->params['paging']['PairTeamCustomer']['count']." dari ".$this->params['paging']['PairTeamCustomer']['count']." record") : ("20 dari ".$this->params['paging']['PairTeamCustomer']['count']." record") ?></h4>

    <div class='table-responsive'>
    <table class='table table-condensed table-hover table-stripped'>
        <thead>
            <tr>
                <th>No.</th>
                <th><?php echo $this->Paginator->sort('Customer.kdpelanggan', 'Kode Pelanggan');?></th>
                <th><?php echo $this->Paginator->sort('Customer.namapelanggan', 'Nama Pelanggan', array('direction' => 'asc'));?>  </th>
                <th><?php echo $this->Paginator->sort('Customer.alamat', 'Alamat');?></th>
                <th><?php echo $this->Paginator->sort('Customer.hutang', 'Hutang');?></th>
                <th><?php echo $this->Paginator->sort('Customer.galonterpinjam', 'Pinjam Galon');?></th>
                <th><?php echo $this->Paginator->sort('Customer.harikunjungan', 'Hari Kunjungan');?></th>
                <th><?php echo $this->Paginator->sort('Customer.transaksiterakhir', 'Transaksi Terakhir');?></th>
            </tr>
        </thead>
        <tbody> 
            <?php 
            if(!$customers){
            ?>
            <tr>
                <td colspan=4>Belum ada pelanggan yang memiliki piutang</td>
            </tr>
            <?php
            }
            else {
            ?>                             
            <?php $count=0; ?>
            <?php foreach($customers as $customer):
                $count ++;
            ?>
            <tr>
                <td><?php echo $count; ?></td>
                <td><?php echo $customer['Customer']['kdpelanggan'];?></td>
                <td><?php echo $customer['Customer']['namapelanggan'];?></td>
                <td><?php echo $customer['Customer']['alamat']?></td>
                <td><?php echo $customer['Customer']['hutang']; ?></td>
                <td><?php echo $customer['Customer']['galonterpinjam'];?></td>
                <td><?php echo $customer['Customer']['harikunjungan'];?></td>
                <td><?php echo $customer['Customer']['transaksiterakhir'];?></td>
            </tr>
            <?php endforeach; ?>
            <?php unset($customer); } ?>
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

<?php echo $this->element('../Customers/add'); ?>
<script type="text/javascript">
    $(document).ready(function(){
        $('#idtim').change(function(){
            var idtim = $('#idtim').val();
            var location = '<?php echo $this->Html->url(array('action' => 'debt'))?>/';
            if(!idtim)
                window.location.assign(location);
            else 
                window.location.assign(location + idtim);
        });
    });
</script>
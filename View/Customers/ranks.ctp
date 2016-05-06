<!-- app/View/Customers/ranks.ctp -->

<div class="row">
	<form class="form-inline form-inline-collapse" method="get" action="<?php echo $this->Html->url(array('action'=>'ranks'));?>">
        <div class="form-group">
            <?php
                if($this->params->pass != null){
                echo $this->Form->input('idtim', array(
                    'type' => 'select',
                    'options' => $option,
                    'class' => 'form-control',
                    'label' => false,
                    'div' => false,
                    'value' => $this->params->pass,
                    'empty' => 'Tampilkan Semua',
                    'required'
                    )
                );
                } else {
                  echo $this->Form->input('idtim', array(
                    'type' => 'select',
                    'options' => $option,
                    'class' => 'form-control',
                    'label' => false,
                    'div' => false,
                    'empty' => 'Tampilkan Semua',
                    'required'
                    )
                );
                }
                ?>
            <button type="button" class="btn btn-default" id="search" aria-label="Left Align">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                tampilkan
            </button>
        </div>
	</form>
    <div class="col-xs-12 col-md-12">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bs-example-modal-lg">Tambah Pelanggan</button>
            <?php
            echo $this->Html->link('Lihat Pelanggan', array('action' => 'index'), array('class' => 'btn btn-info'));
            ?>
        </div>
    </div>
    <div class="col-xs-12 col-md-12">
    <h1>Rangking Pelanggan</h1>
    <div class='table-responsive'>
    <table class='table table-condensed table-hover table-stripped'>
        <thead>
            <tr>
                <th>No.</th>
                <th>Kode Pelanggan</th>
                <th>Nama Pelanggan</th>
                <th>Alamat</th>
                <th>Total Beli</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(!$customers){
            ?>
            <tr>
                <td colspan=4>Belum ada pelanggan yang ditambahkan</td>
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
                <td><?php echo $customer[0]['beli'];?></td>
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
<?php echo $this->element('../Customers/add'); ?>
<?php echo $this->element('search_team', array('controller' => 'customers', 'action' => 'ranks')); ?>

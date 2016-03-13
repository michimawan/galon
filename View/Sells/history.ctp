<!-- app/View/Sells/history.ctp -->
<?php $user = $this->Auth->User(); ?>
<div class='row'>
	<form class="form-inline" method="get" action="<?php echo $this->Html->url(array('action'=>'history'));?>">
        <div class="form-group">
            <?php
            if($user['role'] != 'pegawai'){
                if($this->params->pass != null){
                echo $this->Form->input('idtim', array(
                    'type' => 'select',
                    'options' => $list_team,
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
                    'options' => $list_team,
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
                <?php
            } else {
                echo $this->Form->input('idtim', array('value' => $user['Team']['idtim'], 'class' => 'hidden', 'label' => false, 'div' => false));
            }
            ?>
        </div>
	</form>

</div>
<div class='row'>
    <div class="col-xs-12 col-md-12">
        <div class='table-responsive'>
        	<table class='table table-stripped table-hover table-bordered'>
        		<thead>
        			<tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>Sales</th>
                        <th>Start</th>
                        <th>Finish</th>
                        <th>Galon Kosong Masuk</th>
                        <th>Air Galon Terjual</th>
                        <th>Harga</th>
                        <th>Terbayarkan</th>
                        <th>Hutang</th>
                        <th>Aksi</th>
                    </tr>
        		</thead>
        		<tbody>
                    <?php if (!$masters): ?>
                    <tr>
                        <td colspan='11'>Tidak ada transaksi untuk tim ini.</td>
                    </tr>
                    <?php else: ?>

                    <?php
                    $i = 1;
                    $harga = $bayar = $hutang = 0;
                    foreach ($masters as $master) {
                    ?>
                    <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo substr($master['Master']['date'],0,10); ?></td>
                        <td><?php echo $list_team[$master['Master']['idtim']];?></td>
                        <td><?php echo $master['Master']['start'];?></td>
                        <td><?php echo $master['Master']['finish'];?></td>
                        <td><?php echo $master['Master']['galonkosong'];?></td>
                        <td><?php echo $master['Master']['galonterjual'];?></td>
                        <td><?php echo $master['Master']['total_harga'];?></td>
                        <td><?php echo $master['Master']['total_terbayarkan'];?></td>
                        <td><?php echo $master['Master']['total_hutang'];?></td>
                        <td><?php
                        if($master['Master']['status']) {
                            echo $this->Form->postLink( "Un-Lock",
                                array('action'=>'unlock', $master['Master']['id']), array('class' => 'btn btn-danger', 'confirm' => 'Anda yakin ingin meng-unlock transaksi ini?'));
                            echo $this->Html->link( "Detail",
                                array('action'=>'detail', $master['Master']['id']), array('class' => 'btn btn-info'));
                        }
                        else
                            echo $this->Form->postLink( "Lock",
                                array('action'=>'locking', $master['Master']['id']), array('class' => 'btn btn-danger', 'confirm' => 'Anda yakin ingin me-lock transaksi ini?'));
                        ?>
                        </td>
                    </tr>
                    <?php
                    $harga += $master['Master']['total_harga'];
                    $bayar += $master['Master']['total_terbayarkan'];
                    $hutang += $master['Master']['total_hutang'];
                    $i++;
                }
                ?>
                    <tr>
                        <td colspan='7'>Total</td>
                        <td><?php echo $harga?></td>
                        <td><?php echo $bayar?></td>
                        <td><?php echo $hutang?></td>
                    </tr>
                <?php endif ?>
        		</tbody>
        	</table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#search').click(function() {
        var idtim = $('#idtim').val();
        var loc = '<?php echo $this->Html->url(array('action' => 'history', 'controller' => 'sells'));?>' + '/' + idtim;
        window.location = loc;
    });
</script>

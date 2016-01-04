<!-- app/View/Sells/history.ctp -->
<?php debug($master);?>
<?php $user = $this->Auth->User(); ?>
<div class='row'>
	<form class="form-inline" method="post" action="<?php echo $this->Html->url(array('action'=>'history'));?>">
  		<div class="form-group">
    		<label for="datepicker">Pilih tanggal</label>
    		<input type="text" class="form-control" id="datepicker" name='data[tanggal]' placeholder="Pilih Tanggal" required <?php echo $tanggal?"value='$tanggal'":""  ?>>
  		</div>
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
            } else {
                echo $this->Form->input('idtim', array('value' => $user['Team']['idtim'], 'class' => 'hidden', 'label' => false, 'div' => false));
            }
            ?>
        </div>
        <div class="form-group">
          <select class="form-control" id="harikunjungan" name="data[harikunjungan]">
            <option value=''>Pilih Hari Kunjungan</option>
            <option value='senin' <?php if($hari == 'senin') echo 'selected' ?> >Senin</option>
            <option value='selasa' <?php if($hari == 'selasa') echo 'selected' ?> >Selasa</option>
            <option value='rabu' <?php if($hari == 'rabu') echo 'selected' ?> >Rabu</option>
            <option value='kamis' <?php if($hari == 'kamis') echo 'selected' ?> >Kamis</option>
            <option value='jumat' <?php if($hari == 'jumat') echo 'selected' ?> >Jumat</option>
            <option value='sabtu' <?php if($hari == 'sabtu') echo 'selected' ?> >Sabtu</option>
          </select>
        </div>
  		<button type="submit" class="btn btn-default" id="search" aria-label="Left Align">
			<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
			tampilkan
		</button>
	</form>
	
</div>
<?php debug($history_team); echo count($history_team)?>
<?php if ($master): ?>
<div class='row'>
    <div class="col-md-4 col-xs-6">
        <h4>Tanggal: <?php echo $master[0]['Master']['date'];?></h4>
        <h4>Nama Sales 1: <?php echo '';?></h4>
    </div>
    <div class="col-md-4 col-xs-6">

        <h4>&nbsp</h4>
        <h4>Galon Sales: <?php echo !$master? "" : ($team_galon['Team']['jmlgalon']);?></h4>
    </div>
    <div class="col-md-4 col-xs-6">
        <h4>Harga: <?php echo '';?></h4>
        <h4>Nama Sales 2: <?php echo '';?></h4>
    </div>
</div>
<div class='row'>
    <div class="col-md-6">
        <h4>Start: <?php echo $master[0]['Master']['start']?></h4>
        <h4>Finish: <?php echo $master[0]['Master']['finish'] ?></h4>
    </div>
    <div class="col-md-6">
        <h4>Air galon terjual: <?php echo substr($master[0]['Master']['galonterjual'], 0, 5)  ?></h4>
        <h4>Galon kosong masuk: <?php echo $master[0]['Master']['galonkosong']  ?></h4>
    </div>
</div>  
<?php endif ?>

<div class='row'>
    <div class="col-xs-12 col-md-12">
        <div class='table-responsive'>
        	<table class='table table-stripped table-hover table-bordered'>
        		<thead>
        			<tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>Hari Kunjungan</th>
                        <th>Kode Pelanggan</th>
                        <th>Nama Pelanggan</th>
                        <th>Alamat</th>
                        <th>Beli Air</th>
                        <th>Pnjm Galon</th>
                        <th>Kmbl Galon</th>
                        <th>Ttl Gln Pnjm</th>
                        <th>Bayar</th>
                        <th>Hutang</th>
                        <?php echo $user['role']=='pegawai'? "" : (!$datas[0]['Sell']['status']? "<th>Action</th>" : ""); ?>
                    </tr>
        		</thead>
        		<tbody>
                    <?php if (!$datas): ?>
                    <tr>
                        <td colspan='11'>Tidak ada transaksi di tanggal tersebut</td>
                    </tr>
                    <?php else: ?>
                    
                    <?php 
                    $i = 1;
                    $jmlbeli = 0;
                    $jmlpinjam = 0;
                    $jmlkembali = 0;
                    $totalgalonpinjam = 0;
                    $totalbayar = 0;
                    $totalhutang = 0;

                    foreach ($datas as $data) {
                    ?>
                    <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo substr($data['Sell']['date'],0,10);?></td>
                        <td><?php echo $data['Customer']['harikunjungan'];?></td>
                        <td><?php echo $data['Customer']['kdpelanggan'];?></td>
                        <td><?php echo $data['Customer']['namapelanggan'];?></td>
                        <td><?php echo $data['Customer']['alamat'];?></td>
                        <td><?php echo $data['Sell']['jmlbeli'];?></td>
                        <td><?php echo $data['Sell']['jmlpinjam'];?></td>
                        <td><?php echo $data['Sell']['jmlkembali'];?></td>
                        <td><?php echo $data['Customer']['galonterpinjam'] ?></td>
                        <td><?php echo $data['Sell']['bayar'];?></td>
                        <td><?php echo $data['Sell']['hutang']?></td>
                        <?php if ($user['role'] != 'pegawai'): ?>
                        <td>
                        <?php
                        echo !$data['Sell']['status']? ($this->Html->link('Edit', array('action' => 'edit', $data['Sell']['id']), array('class'=>'btn btn-primary'))) : "";

                        echo !$data['Sell']['status']? ($this->Form->postLink('Hapus', array('action' => 'delete', $data['Sell']['id']), array('class'=>'btn btn-danger', 'confirm' => 'yakin ingin menghapus transaksi penjualan ini?'))) : "";
                        ?>
                        </td>
                        <?php endif ?>
                    </tr>
                    <?php
                    $i++;
                    $jmlbeli = $jmlbeli + $data['Sell']['jmlbeli'];
                    $jmlpinjam = $jmlpinjam + $data['Sell']['jmlpinjam'];
                    $jmlkembali = $jmlkembali + $data['Sell']['jmlkembali'];
                    $totalgalonpinjam = $totalgalonpinjam + $data['Customer']['galonterpinjam'];
                    $totalbayar = $totalbayar + $data['Sell']['bayar'];
                    $totalhutang = $totalhutang + $data['Sell']['hutang'] ;
                }
                ?>
                    <tr>
                        <td colspan='6'>Total</td>
                        <td><?php echo $jmlbeli?></td>
                        <td><?php echo $jmlpinjam?></td>
                        <td><?php echo $jmlkembali?></td>
                        <td><?php echo $totalgalonpinjam?></td>
                        <td><?php echo $totalbayar?></td>
                        <td><?php echo $totalhutang?></td>
                    </tr>
                <?php endif ?>
        		</tbody>
        	</table>
        </div>
    </div>
</div>



<?php 
echo $this->Html->script(array('jquery-ui.min.js'));
echo $this->Html->css(array('jquery-ui.min.css'));
?>
<script type="text/javascript">
	$(function() {
        function getMonthFromString(mon){
           var d = Date.parse(mon + "1, 2015");
           if(!isNaN(d)){
              return new Date(d).getMonth() + 1;
           }
           return -1;
        }
    	$( "#datepicker" ).datepicker({
    		changeMonth: true,
	        changeYear: true,
	        showButtonPanel: true,
	        dateFormat: 'yy-mm-dd'
    	});

    	$("#datepicker").focus(function () {
        	$(".ui-datepicker-calendar").show();
        	$("#ui-datepicker-div").position({
            	my: "left top",
            	at: "left bottom",
            	collision: 'none',
            	of: $(this)
	        });    
	    });
  	});
</script>
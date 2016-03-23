<!-- app/View/Sells/index.ctp -->

<?php
foreach($data as $date => $value) {
?>
    <div class='hidden js-line-chart' data-date="<?php echo $date; ?>" data-value="<?php echo $value; ?>" ></div>
<?php
}
?>

<div class='row'>
	<form class="form-inline" method="post" action="<?php echo $this->Html->url(array('action'=>'rekapbulanan'));?>">
  		<div class="form-group">
    		<label for="datepicker_awal">Pilih Tanggal Awal</label>
    		<input type="text" class="form-control" id="datepicker_awal" name='date1' placeholder="Pilih Tanggal">
  		</div>
  		<div class="form-group">
    		<label for="datepicker_akhir">Pilih Tanggal Terakhir</label>
    		<input type="text" class="form-control" id="datepicker_akhir" name='date2' placeholder="Pilih Tanggal">
  		</div>
        <?php 
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
        ?>
  		<button type="button" class="btn btn-default" id="search" aria-label="Left Align">
			<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
			tampilkan
		</button>
	</form>
</div>

<div id="chart" style="width: 750px, height: 550px"></div>

<?php
    echo $this->Html->script('canvasjs.min.js');
    echo $this->element('sells_index');
    echo $this->Html->script(array('jquery-ui.min.js'));
    echo $this->Html->css(array('jquery-ui.min.css'));
?>

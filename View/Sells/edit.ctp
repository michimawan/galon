<!-- app/View/Sells/edit.ctp -->


<div class="row">
    <div class="col-xs-3 col-md-2">
        <div class="btn-group-vertical" role="group">
            <div class='btn-group' role='group'>
            <?php echo $this->Html->link( "Lihat Penjualan", array('action'=>'index'), array('class' => 'btn btn-default')); ?>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-10">
        <?php echo $this->Form->create('Sell', array('action'=>'edit', 'enctype'=>'multipart/form-data'));?>
        <h1><?php echo __('Ubah Penjualan'); ?></h1>
        <?php
        // debug($customer);
        echo $this->Form->input('idtim', array('type' => 'hidden'));
        echo $this->Form->input('idgood', array('type' => 'hidden'));
        echo $this->Form->input('idcustomer', array(
                                    'label'=>'Pilih Pelanggan: ',
                                    'type' => 'select',
                                    //'default' => $sell['Sell']['idcustomer'],
                                    'options' => $customer,
                                    'readonly',
                                    'class' => 'form-control'
                                    ));
        ?>
        <div class='form-input'>
            <label for='hutang_customer'>Hutang Pelanggan</label>
            <input class='form-control' id='hutang_customer' type='text' readonly>
        </div>
        <?php
        echo $this->Form->input('jmlbeli', array('label'=>'Jumlah beli air' ,'type' => 'number', 'max' => '100', 'min' => '0', 'id'=>'SellJmlBeli', 'class' => 'form-control'));
        echo $this->Form->input('jmlpinjam', array('label'=>'Jumlah pinjam galon' ,'type' => 'number', 'max' => '100', 'min' => '0', 'class' => 'form-control'));
        echo $this->Form->input('jmlkembali', array('label'=>'Jumlah kembali galon' ,'type' => 'number', 'max' => '100', 'min' => '0', 'class' => 'form-control'));
        echo $this->Form->input('totalharga', array('label'=>'Total Harga Galon' ,'type' => 'number', 'max' => '1000000', 'min' => '0', 'readonly', 'class' => 'form-control'));
        ?>
        <div class='form-input'>
            <label for='bayar_customer'>Yang harus dibayarkan pelanggan</label>
            <input class='form-control' id='bayar_customer' type='text' readonly>
        </div>
        <?php
        echo $this->Form->input('bayar', array('label'=>'Total Bayar' ,'type' => 'number', 'max' => '1000000', 'min' => '0', 'class' => 'form-control'));
        echo $this->Form->input('hutang', array('label'=>'Hutang' ,'type' => 'number', 'max' => '1000000', 'min' => '0', 'readonly', 'class' => 'form-control'));
        ?>
        <?php
        echo $this->Form->submit('Ubah Transaksi', array('class' => 'form-submit',  'title' => 'klik untuk menambah transaksi', 'type'=>'submit') );
        ?>
    <?php echo $this->Form->end(); ?>
    </div>
</div>

<script type="text/javascript">
    var bill = 0;
    $(document).ready(function() {
        bill = $('#SellHutang').val();
        if($('#SellBayar').val() - $('#SellTotalharga').val() > 0) {
            bill = ($('#SellBayar').val() - $('#SellTotalharga').val() + parseInt(bill));
            $('#hutang_customer').attr('value', bill);
            $('#bayar_customer').attr('value', $());
        }
    });
    $(document).on('keyup', '#SellJmlBeli', function() {
        var amount = $('#SellJmlBeli').val();
        amount = amount * <?php echo $idgood['Good']['hargajual'];?>;
        
        $('#SellTotalharga').attr('value', amount);

        var bayar = $('#SellBayar').val();
        var totalharga = $('#SellTotalharga').val();
        var hutang = totalharga - bayar
        console.log((hutang+bill) + " " + bayar + " " + totalharga);
        if(hutang < 0) hutang = 0;
        $('#SellHutang').attr('value', hutang);
    });
    $(document).on('keyup', '#SellBayar', function() {
        var bayar = $('#SellBayar').val();
        var totalharga = $('#SellTotalharga').val();
        var hutang = totalharga - bayar;
        
        $('#SellHutang').attr('value', hutang);
    });
    function submitform() {
        var totalharga = $('#SellTotalharga').attr('value');
        var hutang = $('#SellHutang').attr('value');
        if(hutang < 0 || totalharga < 0){
            alert('Tidak boleh ada nilai kurang dari 0');
            return false;
        } else 
            return true;
    }

</script>
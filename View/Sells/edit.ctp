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
        <?php echo $this->Form->create('Sell', array('action'=>'edit', 'enctype'=>'multipart/form-data', 'onsubmit' => 'return formcheck()'));?>
        <h1><?php echo __('Ubah Penjualan'); ?></h1>
        <?php
        echo $this->Form->input('id', array('type' => 'hidden'));
        echo $this->Form->input('idtim', array('type' => 'hidden'));
        echo $this->Form->input('idmaster', array('type' => 'hidden'));
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
        echo $this->Form->input('jmlbeli', array('label'=>'Jumlah beli air' ,'type' => 'number', 'max' => '100', 'min' => '0', 'class' => 'form-control'));
        echo $this->Form->input('jmlpinjam', array('label'=>'Jumlah pinjam galon' ,'type' => 'number', 'max' => '100', 'min' => '0', 'class' => 'form-control'));
        echo $this->Form->input('jmlkembali', array('label'=>'Jumlah kembali galon' ,'type' => 'number', 'max' => '100', 'min' => '0', 'class' => 'form-control'));
        echo $this->Form->input('totalhargagalon', array('label'=>'Total Harga Galon' ,'type' => 'number', 'max' => '1000000', 'min' => '0', 'readonly', 'class' => 'form-control'));
        echo $this->Form->input('totalharga', array('label'=>'Total Harga yang harus Dibayarkan' ,'type' => 'number', 'max' => '1000000', 'min' => '0', 'readonly', 'class' => 'form-control'));
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
    var price = 0;
    $(document).ready(function() {
        var bill = $('#SellTotalharga').val() - $('#SellTotalhargagalon').val();
        $('#hutang_customer').attr('value', bill);
        price = $('#SellTotalhargagalon').val() / $('#SellJmlbeli').val();
    });

     $(document).on('keyup', '#SellJmlbeli', function() {
        var tot_galon = $('#SellJmlbeli').val();
        var last_cust_debt = $('#hutang_customer').val() ? $('#hutang_customer').val() : 0;

        var buy_price = tot_galon * price;
        
        var total_price = parseInt(buy_price) + parseInt(last_cust_debt);
        $('#SellTotalhargagalon').attr('value', buy_price);
        $('#SellTotalharga').attr('value', total_price);

        var bayar = $('#SellBayar').val();
        var hutang = (total_price - bayar);
        $('#SellHutang').attr('value', hutang);
    });

    $(document).on('keyup', '#SellBayar', function() {
        var bayar = $('#SellBayar').val();
        var total_price = $('#SellTotalharga').val();
        var hutang = (total_price - bayar);

        $('#SellHutang').attr('value', hutang);
    });

    function formcheck(){
        if(parseInt($('#SellHutang').val()) < 0) {
            alert('Nilai Hutang tidak boleh kurang dari 0');
            return false;
        }

        var jmlbeli = $('#SellJmlbeli').val();
        var jmlpinjam = $('#SellJmlpinjam').val();
        var jmlkembali = $('#SellJmlkembali').val();
        if(jmlbeli == '')
            $('#SellJmlbeli').attr('value', '0');
        if(jmlpinjam== '')
            $('#SellJmlpinjam').attr('value', '0');
        if(jmlkembali == '')
            $('#SellJmlkembali').attr('value', '0');
    }
</script>
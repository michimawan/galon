<!-- app/View/Sells/add.ctp -->
<div id='listofprice' class='hidden'>
    <?php foreach ($prices as $price): ?>
    <span id="price<?php echo $reverse_prices[$price];?>"><?php echo $price;?></span>
    <?php endforeach ?>
</div>
<div class="row">
    <div class="col-xs-3 col-md-2">
        <?php 

            if($this->Session->check('Auth.User')){
            echo $this->Html->link( "Lihat Penjualan",   array('action'=>'index'), array('class'=>'btn btn-default')); 
            }
        ?>
    </div>
    <!-- <span class='hidden' price="<?php ?>"></span> -->
    <div class="col-xs-12 col-md-10">
        <?php echo $this->Form->create('Sell', array('action'=>'add', 'enctype'=>'multipart/form-data', 'onsubmit' => 'return formcheck()'));?>
        <h1><?php echo __('Tambah Penjualan'); ?></h1>
        <?php
        echo $this->Form->input('idtim', array('type' => 'hidden','value'=>$idtim));
        echo $this->Form->input('idgood', array(
                                    'label'=>'Pilih Barang ',
                                    'type' => 'select',
                                    'options' => $goods,
                                    'readonly'=>'readonly',
                                    // 'select' => $goods[0],
                                    'class' => 'form-control'
                                    ));
        
        echo $this->Form->input('idcustomer', array(
                                    'label'=>'Pilih Pelanggan ',
                                    'type' => 'select',
                                    'options' => $customers,
                                    'required',
                                    'class' => 'form-control',
                                    'empty' => 'Pilih Pelanggan'
                                    ));
        ?>
        <div class='form-input'>
            <label for='hutang_customer'>Hutang Pelanggan</label>
            <input class='form-control' id='hutang_customer' type='text' readonly>
        </div>
        <?php
        echo $this->Form->input('jmlbeli', array('label'=>'Jumlah beli air' ,'type' => 'number', 'max' => '100', 'min' => '0', 'id'=>'SellJmlBeli', 'value' => '0', 'class' => 'form-control', 'required'));
        echo $this->Form->input('jmlpinjam', array('label'=>'Jumlah pinjam galon' ,'type' => 'number', 'max' => '100', 'min' => '0', 'value' => '0', 'class' => 'form-control', 'required'));
        echo $this->Form->input('jmlkembali', array('label'=>'Jumlah kembali galon' ,'type' => 'number', 'max' => '100', 'min' => '0', 'value' => '0', 'class' => 'form-control', 'required'));
        echo $this->Form->input('totalhargagalon', array('label'=>'Total Harga Galon' ,'type' => 'number', 'max' => '1000000', 'min' => '0', 'readonly', 'value' => '0', 'class' => 'form-control'));
        echo $this->Form->input('totalharga', array('label'=>'Total Harga yang harus Dibayarkan' ,'type' => 'number', 'max' => '1000000', 'min' => '0', 'readonly', 'value' => '0', 'class' => 'form-control'));
        echo $this->Form->input('bayar', array('label'=>'Pembayaran Pelanggan' ,'type' => 'number', 'max' => '1000000', 'min' => '0', 'value' => '0', 'class' => 'form-control'));
        echo $this->Form->input('hutang', array('label'=>'Hutang' ,'type' => 'number', 'max' => '1000000', 'min' => '0', 'readonly', 'value' => '0', 'class' => 'form-control'));
        ?>
        <?php
        echo $this->Form->submit('Tambah Transaksi', array('class' => 'form-submit',  'title' => 'klik untuk menambah transaksi', 'type'=>'submit') );
        ?>
    <?php echo $this->Form->end(); ?>
    </div>
</div>

<script type="text/javascript">
    $(document).on('keyup', '#SellJmlBeli', function() {
        var tot_galon = $('#SellJmlBeli').val();
        var last_cust_debt = $('#hutang_customer').val() ? $('#hutang_customer').val() : 0;

        var price = $('#price' + $('#SellIdgood').val()).html();
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
        var jmlbeli = $('#SellJmlBeli').val();
        var jmlpinjam = $('#SellJmlpinjam').val();
        var jmlkembali = $('#SellJmlkembali').val();
        if(jmlbeli == '')
            $('#SellJmlBeli').attr('value', '0');
        if(jmlpinjam== '')
            $('#SellJmlpinjam').attr('value', '0');
        if(jmlkembali == '')
            $('#SellJmlkembali').attr('value', '0');
    }

    $('#SellIdcustomer').change(function(){
        var idcustomer = $(this).val();
        if(idcustomer != ''){
            $.ajax({
                url:'<?= $this->Html->url(array('action'=>'get_hutang_customer')); ?>/' +  idcustomer,
                data: idcustomer,
                success: function( data ) {
                data = JSON.parse(data);
                for(x in data){
                    $('#hutang_customer').attr('value', data[x]['hutang']);
                    $('#SellTotalharga').attr('value', data[x]['hutang']);
                }
                reset();
                }
            });
        }
    });

    function reset() {
        $('#SellJmlBeli').attr('value', '0');
        $('#SellJmlkembali').attr('value', '0');
        $('#SellJmlpinjam').attr('value', '0');
        $('#SellTotalhargagalon').attr('value', '0');
        $('#SellBayar').attr('value', '0');
    }
</script>
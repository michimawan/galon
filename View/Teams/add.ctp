<!-- app/View/Teams/add.ctp -->

<?php
/*
$total_galon = $total_galon['Good']['stokbarang'];
foreach ($galons as $galon) {
    $total_galon = $total_galon - $galon['Team']['jmlgalon'];
}
*/
?>
<div class="row">
    <div class="col-xs-3 col-md-2">
        <?php 

            if($this->Session->check('Auth.User')){
            echo $this->Html->link( "Lihat Daftar Tim",   array('action'=>'index'), array('class'=>'btn btn-default')); 
            }
        ?>
    </div>

    <div class="col-xs-12 col-md-10">
        <?php echo $this->Form->create('Team');?>
        <fieldset>
            <h1><?php echo __('Tambah Tim'); ?></h1>
            <!-- <h4>Galon tersisa: <?php echo $total_galon; ?></h4> -->
            <?php
            echo $this->Form->input('pegawai_1', array('label' => 'Nama Pegawai 1', 'type' => 'text','required', 'class' => 'form-control'));

            echo $this->Form->input('idpegawai_1', array('label' => 'Nama Pegawai 1', 'type' => 'text', 'class' => 'hidden', 'required', 'div' => false, 'label' => false));
            ?>
            <?php
            echo $this->Form->input('pegawai_2', array('label' => 'Nama Pegawai 2', 'type' => 'text', 'required', 'class' => 'form-control'));
            echo $this->Form->input('idpegawai_2', array('label' => 'Nama Pegawai 2', 'type' => 'text', 'class' => 'hidden','required', 'div' => false, 'label' => false));
            ?>
            
            <?php
            //echo $this->Form->input('jmlgalon', array('label' => 'Jumlah Galon', 'type' => 'number', 'min' => 1, 'max' => $total_galon, 'required'));       
            echo $this->Form->submit('Tambah Tim', array('class' => 'form-submit','title' => 'klik untuk menambah tim', 'escape' => false) ); 
            ?>
        </fieldset>
    <?php echo $this->Form->end(); ?>
    </div>
</div>
<?php 
echo $this->Html->script(array('jquery-ui.min.js'));
echo $this->Html->css(array('jquery-ui.min.css'));
?>
<script type="text/javascript">
    $(function() {
        function log( message ) {
            $( "<div>" ).text( message ).prependTo( "#log" );
            $( "#log" ).scrollTop( 0 );
        }
        var mappingID_2 = {}; 
        $( "#TeamPegawai2" ).autocomplete({
            source: function( request, response ) {
            $.ajax({
                url:'<?= $this->Html->url(array('action'=>'autocompletes')); ?>/',
                
                data: {term: request.term},
                success: function( data ) {
                    var autos = new Array();
                    result = JSON.parse(data);
                    for (x in result){
                        autos.push(result[x]['User']['firstname'] + " " + result[x]['User']['lastname']);
                        mappingID_2[result[x]['User']['firstname'] + " " + result[x]['User']['lastname']] = result[x]['User']['id'];
                        // autos.push(result[x]['User']['firstname'] + " " + result[x]['User']['lastname'] + " | " + result[x]['User']['username']);
                    }
                    
                    response( autos );
                }
            });
            },
            minLength: 3,
            select: function( event, ui ) {
                $('#TeamIdpegawai2').attr('value',mappingID_2[ui.item.label]);
                //ui.item.label = ui.item.label.substring(0, (ui.item.label.indexOf("|") - 1));
                log( ui.item ?
                "Selected: " + ui.item.label :
                    "Nothing selected, input was " + this.value);
            },
            open: function() {
                $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
            },
            close: function() {
                $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
            }
        });
    var mappingID_1 = {}; 
    $( "#TeamPegawai1" ).autocomplete({
            source: function( request, response ) {
            $.ajax({
                url:'<?= $this->Html->url(array('action'=>'autocompletes')); ?>/',
                
                data: {term: request.term},
                success: function( data ) {
                    var autos = new Array();
                    result = JSON.parse(data);
                    
                    for (x in result){
                        autos.push(result[x]['User']['firstname'] + " " + result[x]['User']['lastname']);
                        mappingID_1[result[x]['User']['firstname'] + " " + result[x]['User']['lastname']] = result[x]['User']['id'];
                        // autos.push(result[x]['User']['firstname'] + " " + result[x]['User']['lastname'] + " | " + result[x]['User']['username']);
                    }

                    response( autos );
                }
            });
            },
            minLength: 3,
            select: function( event, ui ) {
                $('#TeamIdpegawai1').attr('value',mappingID_1[ui.item.label]);
                //ui.item.label = ui.item.label.substring(0, (ui.item.label.indexOf("|") - 1));                
                log( ui.item ?
                "Selected: " + ui.item.label :
                    "Nothing selected, input was " + this.value);               
            },
            open: function() {
                $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
            },
            close: function() {
                $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
            }
        });
    });
</script>
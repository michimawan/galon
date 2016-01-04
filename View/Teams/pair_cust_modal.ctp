
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <h4 class="modal-title" id="myModalLabel">Tambah Pelanggan</h4>
</div>
<div class="modal-body">
  <div class='row'>
    <form class="form-inline" method="post" action="<?php echo $this->Html->url(array('action'=>'pair_cust'));?>">
        <div class="form-group">
          <label for="customer">Nama Pelanggan</label>
          <input type="text" class="form-control" id="customer" name='namacustomer'>
        </div>
        <div class="form-group hidden">
          <input type="text" class="form-control" id="idcustomer" name='idcustomer'>
        </div>
        <div class="form-group hidden">
          <input type="text" class="form-control" name='idtim' value="<?php echo $id?>">
        </div>
        <button type="button" class="btn btn-default" id="search" aria-label="Left Align">
        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
        tambahkan
      </button>
    </form>
  
  </div>
</div>



<script type="text/javascript">
  $(function() {

    function log( message ) {
      $( "<div>" ).text( message ).prependTo( "#log" );
      $( "#log" ).scrollTop( 0 );
    }

    var mappingID = {};
    $( "#customer" ).autocomplete({
      source: function( request, response ) {
      $.ajax({
      url:'<?= $this->Html->url(array('controller' => 'customers', 'action'=>'autocompletes')); ?>/',
                
      data: {term: request.term},
      success: function( data ) {
        var autos = new Array();
        result = JSON.parse(data);
        console.log(result);
        for (x in result){
          autos.push(result[x]['Customer']['namapelanggan']);
          mappingID[result[x]['Customer']['namapelanggan']] = result[x]['Customer']['id'];
        }
        
        response( autos );
      }
      });
      },
      minLength: 3,
      select: function( event, ui ) {
      $('#idcustomer').val(mappingID[ui.item.label]);

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

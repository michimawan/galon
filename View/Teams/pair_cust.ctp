<!-- app/View/Customers/pair_cust.ctp -->

<div class="modal-body">
  <div class='row'>
    <h2>Pegawai: </h2>
    <?php 
    foreach ($team as $data) {
      ?>
      <h4><?php echo $data['User']['firstname']. ' ' .$data['User']['lastname']; ?></h4>
      <?php
    }
    ?>
  </div>
  <div class='row'>
    <h2>Pelanggan: </h2>
    <form class="form-inline" method="post" action="<?php echo $this->Html->url(array('action'=>'pair_cust'));?>">
        <div class="form-group">
          <label for="customer">Nama Pelanggan</label>
          <input type="text" class="form-control" id="customer" name="data[PairTeamCustomer][namacustomer]" required  >
        </div>
        <div class="form-group">
          <select class="form-control" id="harikunjungan" name="data[PairTeamCustomer][harikunjungan]" required>
            <option value=''>Pilih Hari Kunjungan</option>
            <option value='senin'>Senin</option>
            <option value='selasa'>Selasa</option>
            <option value='rabu'>Rabu</option>
            <option value='kamis'>Kamis</option>
            <option value='jumat'>Jumat</option>
            <option value='sabtu'>Sabtu</option>
          </select>
        </div>
        <div class="form-group hidden">
          <input type="text" class="form-control" id="idcustomer" name="data[PairTeamCustomer][idcustomer]" required>
        </div>
        <div class="form-group hidden">
          <input type="text" class="form-control" name="data[PairTeamCustomer][idtim]" value="<?php echo $idtim?>">
        </div>
        <button type="submit" class="btn btn-default" id="search" aria-label="Left Align">
        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
        tambahkan
      </button>
    </form>
    <?php echo $this->Html->link(    "Lihat Daftar Pelanggan",  array('action'=>'customer_not_teamed'), array('class' => 'btn btn-success', 'data-toggle' => 'modal', 'data-action' => $this->Html->url(array('action'=>'customer_not_teamed')),'data-target' => '#modal_cust_not_teamed')); ?> 
    <div class='table-responsive'>
      <table class='table table-striped table-hover'>
        <thead>
          <tr>
            <th>No.</th>
            <th>Nama Pelanggan</th>
            <th>Hari Kunjungan</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            $i = 1;
            foreach ($pair_data as $pair) {
            ?>
            <tr>
              <td><?php echo $i; ?></td>
              <td><?php echo $pair['Customer']['namapelanggan']?></td>
              <td><?php echo $pair['Customer']['harikunjungan']?></td>
              <td>
              <?php
              echo $this->Form->postLink('  Hapus Pelanggan', array('action' => 'delete_pair', $pair['Customer']['id'], $pair['PairTeamCustomer']['idtim']), array('class' => 'btn btn-danger', 'confirm' => 'anda yakin ingin menghapus pelanggan dari tim ini?')); 
              // UBAH HARI KUNJUNGAN ?
              ?>
              </td>
            </tr>
          <?php
            $i++; }
          ?>
        </tbody>
      </table>
    </div>
    
  </div>
  <div class="paging">
        <?php 
            echo $this->Paginator->prev() .'  '. $this->Paginator->numbers(array('before'=>false, 'after'=>false,'separator'=> false)) .'  '. $this->Paginator->next();
        ?>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id='modal_cust_not_teamed' aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id='index'>
        </div>
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

    var mappingID = {};
    $( "#customer" ).autocomplete({
      source: function( request, response ) {
      $.ajax({
      url:'<?= $this->Html->url(array('controller' => 'customers', 'action'=>'autocompletes')); ?>/',
                
      data: {term: request.term},
      success: function( data ) {
        if(data != 'no') {
        var autos = new Array();
        result = JSON.parse(data);
        for (x in result){
          autos.push(result[x]['Customer']['namapelanggan']);
          mappingID[result[x]['Customer']['namapelanggan']] = result[x]['Customer']['id'];
        }
        
        response( autos );
        }
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
  

    $("a[data-target=#modal_cust_not_teamed]").click(function(ev) {
        //ev.preventDefault();
        var target = $(this).attr("href");

        // load the url and show modal on success
        $("#modal_cust_not_teamed #index").load(target, function() { 
             $("#modal_cust_not_teamed").modal("show");
        });
    });
  });

</script>

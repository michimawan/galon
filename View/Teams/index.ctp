<!-- app/View/Teams/index.ctp -->

<?php
/*
$total_galon = $total_galon['Good']['stokbarang'];
foreach ($galons as $galon) {
    $total_galon = $total_galon - $galon['Team']['jmlgalon'];
}
*/
?>
<div class="row">
    <div class="col-xs-12 col-md-12">
        <div class="btn-group" role="group">
            <?php
            $current_user = $this->Auth->User();
            if($current_user['role'] != 'pegawai')
            echo $this->Html->link( "Tambah Tim", array('action'=>'add'), array('escape' => false, 'class' => 'btn btn-primary'));
            ?>
        </div>
    </div>

    <div class="col-xs-12 col-md-12">
    <h1>Daftar Tim</h1>
    <div class='table-responsive'>
    <table class='table table-condensed table-hover table-stripped'>
        <thead>
            <tr>
                <th>No.</th>
                <th>Member</th>
                <th>Nomor Tim</th>
                <th>Jumlah Galon</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(!$teams){
            ?>
            <tr>
                <td colspan=4>Belum ada tim yang ditambahkan</td>
            </tr>
            <?php
            }
            else {
            ?>
            <?php $count=0; ?>
            <?php foreach($teams as $team):
                $count ++;
            ?>
            <tr>
                <td><?php echo $count; ?></td>
                <td><?php echo $team['User']['firstname']. " ".$team['User']['lastname'];?></td>
                <?php
                if($count % 2){
                ?>
                <td rowspan='2'><?php echo $team['Team']['idtim']?></td>
                <td rowspan='2'><?php echo $team['Team']['jmlgalon']?></td>
                <td rowspan='2'>
                    <div class="btn-group" role="group">
                    <?php
                    if($current_user['role'] != 'pegawai'){
                    echo $this->Html->link(    "Cetak Data",   array('action'=>'print_customer_in_team', $team['Team']['idtim']), array('class' => 'btn btn-default'));
                    echo $this->Html->link(    "Ubah Jml Galon",  array('action'=>'change', $team['Team']['idtim']), array('class' => 'btn btn-success', 'data-toggle' => 'modal', 'data-action' => $this->Html->url(array('action'=>'change', $team['Team']['idtim'])),'data-target' => '#modal_addcust'));
                    echo $this->Html->link(    "Tambah Pelanggan",   array('action'=>'pair_cust', $team['Team']['idtim']), array('class' => 'btn btn-primary'));
                    echo $this->Form->postLink(    "Hapus Tim",   array('action'=>'delete', $team['Team']['idtim']), array('class' => 'btn btn-danger', 'confirm' => 'Apakah sudah yakin ingin menghapus tim ini?'));
                    } else if($current_user['role'] == 'pegawai'){
                        if($current_user['Team']['idtim'] == $team['Team']['idtim']){
                        echo $this->Html->link(    "Tambah Pelanggan",   array('action'=>'pair_cust', $team['Team']['idtim']), array('class' => 'btn btn-primary'));
                        }
                    }
                    ?>
                    </div>
                </td>
                <?php } ?>
            </tr>
            <?php endforeach; ?>
            <?php unset($team); } ?>
        </tbody>
    </table>
    </div>
    <div class="paging">
        <?php
            echo $this->Paginator->prev() .'  '. $this->Paginator->numbers(array('before'=>false, 'after'=>false,'separator'=> false)) .'  '. $this->Paginator->next();
        ?>
    </div>

    </div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id='modal_addcust' aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id='index'>
            <!-- <div class="modal-header">
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
            </div> -->
        </div>
    </div>
</div>
<?php
echo $this->Html->script(array('jquery-ui.min.js'));
//echo $this->Html->css(array('jquery-ui.min.css'));
?>
<script type="text/javascript">
    $("a[data-target=#modal_addcust]").click(function(ev) {
        //ev.preventDefault();
        var target = $(this).attr("href");

        // load the url and show modal on success
        $("#modal_addcust #index").load(target, function() {
             $("#modal_addcust").modal("show");
        });
    });

</script>



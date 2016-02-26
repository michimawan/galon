<!-- app/View/Attendances/rekapbulanan.ctp -->

<div class='row'>
	<form class="form-inline" method="post" action="<?php echo $this->Html->url(array('action'=>'rekapbulanan'));?>">
  		<div class="form-group">
    		<label for="datepicker">Pilih bulan</label>
    		<input type="text" class="form-control" id="datepicker" name='tanggal' placeholder="Pilih Bulan" required>
  		</div>
  		<div class="form-group">
    		<label for="pegawai">Nama Pegawai</label>
    		<input type="text" class="form-control" id="pegawai" name='id' required>
  		</div>
        <div class="form-group hidden">
            <input type="text" class="form-control" id="idpegawai" name='id' required>
        </div>
  		<button type="button" class="btn btn-default" id="search" aria-label="Left Align">
			<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
			tampilkan
		</button>
	</form>

</div>
<div class='row'>
	<div class="col-xs-12 col-md-12">
        <div>
            <h1>Presensi Pegawai</h1>
            <h3><?php echo $user['User']['username'];?></h3>
            <h3><?php echo $user['User']['firstname']. " " .$user['User']['lastname'];?></h3>
            <h3><?php echo "Masuk " . $count_present. " hari"; ?></h3>
        </div>
    </div>
    <div class="col-xs-12 col-md-12">
        <div class='table-responsive'>
        	<table class='table table-stripped table-hover table-bordered'>
        		<thead>
        			<tr>
	        			<th rowspan = '2' class='middle'>Bulan</th>
	        			<th colspan = "31" class='center'>Tanggal</th>
        			</tr>
        			<tr>
        				<?php
        				for($i = 1; $i <= 31; $i++){ ?>
							<th><?php echo $i; ?></th>
        				<?php
        				}
        				?>
        			</tr>
        		</thead>
        		<tbody>
                    <?php if ($month_record): ?>
                    <?php
                    $month_names = array(1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember');

                    $row_count = 1;
                    $date_count = 0;
                    ?>
                    <tr>
                        <td><?php echo $month_names[(int) date('m', strtotime($month_record[0]['Attendance']['tanggal']))]; ?></td>
                        <?php
                        for($i = 0; $i < 31; $i++){

                        if($date_count != count($month_record))
                            $date = strtotime($month_record[$date_count]['Attendance']['tanggal']);

                        if(date('d', $date) == ($i + 1) && $month_record[$date_count]['Attendance']['kehadiran']){
                        ?>
                        <td><span class="glyphicon glyphicon-ok text-success" aria-hidden="true"></span></td>
                        <?php $date_count++; } else if(date('d', $date) == ($i + 1) && !$month_record[$date_count]['Attendance']['kehadiran']) {
                        ?>
                        <td><span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span></td>
                        <?php
                        $date_count++;} else {
                        ?>
                        <td><span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span></td>
                        <?php
                        }
                        ?>
                        <?php } ?>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td colspan='32'>Bulan ini belum ada presensi pegawai</td>
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
	        dateFormat: 'MM yy',
	        onClose: function(dateText, inst) {
	            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
	            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
	            $(this).datepicker('setDate', new Date(year, month, 1));
	        }
    	});

    	$("#datepicker").focus(function () {
        	$(".ui-datepicker-calendar").hide();
        	$("#ui-datepicker-div").position({
            	my: "left top",
            	at: "left bottom",
            	collision: 'none',
            	of: $(this)
	        });
	    });

	    function log( message ) {
            $( "<div>" ).text( message ).prependTo( "#log" );
            $( "#log" ).scrollTop( 0 );
        }
        var mappingID = {};
        $( "#pegawai" ).autocomplete({
            source: function( request, response ) {
            $.ajax({
                url:'<?= $this->Html->url(array('controller' => 'users', 'action'=>'autocompletes')); ?>/',

                data: {term: request.term},
                success: function( data ) {
                    if(data != 'no') {
                    var autos = new Array();
                    result = JSON.parse(data);
                    console.log(result);
                    for (x in result){
                        autos.push(result[x]['User']['firstname'] + " " + result[x]['User']['lastname'] + " | " + result[x]['User']['username']);
                        mappingID[result[x]['User']['firstname'] + " " + result[x]['User']['lastname'] + " | " + result[x]['User']['username']] = result[x]['User']['id'];
                    }

                    response( autos );
                    }
                }
            });
            },
            minLength: 3,
            select: function( event, ui ) {
                $('#idpegawai').val(mappingID[ui.item.label]);

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
        var months = {"January" : '01', "February" : '02', "March" : '03', "April" : '04', "May" : '05', "June" : '06',
               "July" : '07', "August" : '08', "September" : '09', "October" : '10', "November" : '11', "December" : '12' };
		$("#search").click(function(){
			var tanggal = $("#datepicker").val();
			var pegawai = $("#idpegawai").val();
            var spl = tanggal.split(" ");
            var month = months[spl[0]];
            var year = spl[1];
            if(pegawai != '' && month != '' && year != ''){
    			var loc = '<?php echo $this->Html->url(array('action' => 'rekapbulanan'))?>/' + pegawai + '/' + year + '/' + month;
                window.location.assign(loc);
            }
		});
  	});
</script>

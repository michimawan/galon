<script>
$(document).ready(function() {
    $('.show').click(function() {
        var startdate;
        var enddate;
        var idtim;
        window.location;
    });

    read();
    function read()
    {
        var dataPoints = [];
        $('.js-line-chart').each(function() {
            var date = $(this).data('date');
            var value = $(this).data('value');

            dataPoints.push({
                x: new Date(date), y: value
            });
        });
        createChart(dataPoints);
    }

    function createChart(dataPoints){
        var chart = new CanvasJS.Chart("chart", {
            title: {text: "Grafik Trend Jumlah Galon Terjual"},
                axisX: {
                    interval: 5,
                        intervalType: "day",
                },
                data: [{
                    type: "line",
                        dataPoints,
                        markerType: "circle",
                }]
        });

        chart.render();
    }

    $( "#datepicker_awal" ).datepicker({
        changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'yy-mm-dd',
            onClose: function(dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1));
            }
    });

    $("#datepicker_awal").focus(function () {
        $(".ui-datepicker-calendar").hide();
        $("#ui-datepicker-div").position({
            my: "left top",
                at: "left bottom",
                collision: 'none',
                of: $(this)
        });
    });

    $( "#datepicker_akhir" ).datepicker({
        changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'yy-mm-dd',
            onClose: function(dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1));
            }
    });

    $("#datepicker_akhir" ).focus(function () {
        $(".ui-datepicker-calendar").hide();
        $("#ui-datepicker-div").position({
            my: "left top",
                at: "left bottom",
                collision: 'none',
                of: $(this)
        });
    });

    $("#search").click(function(){
        var date1 = $('#datepicker_awal').val();
        var date2 = $('#datepicker_akhir').val();
        var loc = '<?php echo $this->Html->url(array('action' => 'index'))?>/index/' + date2 + '/' + date1;
        window.location.assign(loc);
        // var tanggal = $("#datepicker").val();
        // var pegawai = $("#idpegawai").val();
        // var spl = tanggal.split(" ");
        // var month = months[spl[0]];
        // var year = spl[1];
        // if(pegawai != '' && month != '' && year != ''){
        //     var loc = '<?php echo $this->Html->url(array('action' => 'rekapbulanan'))?>/' + pegawai + '/' + year + '/' + month;
        //     window.location.assign(loc);
        // }
    });
});
</script>

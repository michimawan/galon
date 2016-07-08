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
                    valueFormatString: "DD-MMM",
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
    });

    $("#datepicker_awal").focus(function () {
        $(".ui-datepicker-calendar").show();
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
    });

    $("#datepicker_akhir" ).focus(function () {
        $(".ui-datepicker-calendar").show();
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
        var idtim = $('#idtim').val();
        var loc = '<?php echo $this->Html->url(array('action' => 'graph'))?>?';
        if(date1 !== '')
            loc += 'date1=' + date1;
        if(date2 !== '')
            loc += '&date2=' + date2;
        if(idtim !== '')
            loc += '&tim=' + idtim;

        console.log(loc);
        window.location.assign(loc);
    });
});
</script>

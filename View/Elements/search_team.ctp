<script type="text/javascript">
    var action = "<?php echo $action ?>";
    $('#search').click(function() {
        var idtim = $('#idtim').val();
        var loc = '<?php echo $this->Html->url(array(
            'action' => $action,
            'controller' => $controller));?>';

        if(action === 'index')
            loc += '/index/' + idtim;
        else
            loc += '/' + idtim;
        window.location = loc;
    });
</script>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title ?></title>
    <?php
    echo $this->Html->css(array('cake.generic', 'bootstrap.min', 'print'));
    echo $this->Html->script(array('jquery-2.1.3.min'));
    ?>
</head>
<body>
    <div id="wrap">
        <div class="container">
        <?php
        echo $this->fetch('content');
        ?>
        </div>

    </div>
</body>
<script type="text/javascript">
$(document).ready(function () {
    window.print();
});
</script>
</html>

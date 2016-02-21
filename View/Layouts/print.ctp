<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title ?></title>
<?php
echo $this->Html->css(array('bootstrap.min.css', 'print.css'));
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
<?php
echo $this->Html->script(array('jquery-2.1.3.min.js', 'bootstrap.min.js'));
?>
<script type="text/javascript">
$(document).ready(function () {
    window.print();
});
</script>
</html>

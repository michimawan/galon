<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $title ?></title>
	<link rel="stylesheet" type="text/css" media="print" href="print.css">
	<?php
		echo $this->Html->css(array('cake.generic.css','bootstrap.min.css', 'styles.css'));
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
        //window.print();
        // setTimeout("closePrintView()", 1000);
    });
    function closePrintView() {
        document.location.href = "<?php echo $this->Html->url(array('action' => 'index'))?>";
    }
</script>
</html>
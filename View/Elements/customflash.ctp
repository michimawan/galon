<!-- app/View/Elements/customflash.ctp -->

<div class="alert alert-<?php echo $class; ?> alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<?php if($class=='warning' || $class == 'danger') {?>
	<strong>Maaf!</strong>
	<?php 
	} else if($class =='info');
	else if($class == 'success'){
	?>
	<strong>Berhasil!</strong>
	<?php
	}
	echo $message; ?>
</div>
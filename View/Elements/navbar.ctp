<!-- app/View/Elements/navbar.ctp -->

<?php
$main = '';
$users = '';
$teams= '';
$attendances= '';
$customers= '';
$ranks= '';
$goods= '';
$sells= '';
$graphs = '';
$logouturl= $this->Html->url(['controller'=>'users', 'action'=>'logout']);
$usersurl= $this->Html->url(['controller'=>'users', 'action'=>'index']);
$teamsurl= $this->Html->url(['controller'=>'teams', 'action'=>'index']);
$attendancesurl= $this->Html->url(['controller'=>'attendances', 'action'=>'index']);
$customersurl= $this->Html->url(['controller'=>'customers', 'action'=>'index']);
$ranksurl= $this->Html->url(['controller'=>'customers', 'action'=>'ranks']);
$goodsurl= $this->Html->url(['controller'=>'goods', 'action'=>'index']);
$sellsurl= $this->Html->url(['controller'=>'sells', 'action'=>'index']);
$graphurl= $this->Html->url(['controller'=>'sells', 'action'=>'graph']);

if($menu === 'main') {
	$main = 'class="active"';
} else if($menu === 'users') {
	$users= 'class="active"';
} else if ($menu === 'teams') {
	$teams= 'class="active"';
} else if ($menu === 'attendances') {
	$attendances= 'class="active"';
} else if ($menu === 'customers') {
	$customers= 'class="active"';
} else if ($menu === 'ranks') {
	$ranks= 'class="active"';
} else if ($menu === 'goods') {
	$goods= 'class="active"';
} else if($menu === 'graphs'){
	$graphs = 'class=active';
} else if ($menu === 'sells') {
	$sells= 'class="active"';
}
?>

<?php
$user = $this->Auth->user();
?>
<div class="navbar navbar-default navbar-fixed-top">
  	<div class="container">
    	<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<div>
				<?php echo $this->Html->image('adikarya_okane.png', ['class' => 'navbar-brand img-responsive', 'alt' => 'Adikarya Okane', 'title' => 'Adikarya Okane']);?>
			</div>
		</div>

		<div id='navbar' class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<?php if($this->Auth->loggedIn()){ ?>
				<li class="dropdown">
		         	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
		         		User
		         		<span class="caret"></span>
		         	</a>
		          	<ul class="dropdown-menu">
		            	<li <?php echo $users; ?>><a href="<?php echo $usersurl; ?>">User</a></li>
		            	<li <?php echo $attendances; ?>><a href="<?php echo $attendancesurl; ?>">Presensi</a></li>
			            <li <?php echo $teams; ?>><a href="<?php echo $teamsurl; ?>">Team</a></li>
		          	</ul>
        		</li>
        		<li class="dropdown">
		         	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
		         		Pelanggan
		         		<span class="caret"></span>
		         	</a>
		          	<ul class="dropdown-menu">
                    <li <?php echo $customers; ?>><a href="<?php echo $customersurl; ?>">Pelanggan</a></li>
                    <li <?php echo $ranks; ?>><a href="<?php echo $ranksurl; ?>">Rangking Pelanggan</a></li>
		          	</ul>
        		</li>
        		<li <?php echo $goods?> >
        			<a href="<?php echo $goodsurl; ?>">Barang</a>
        		</li>
        		<?php
        		if ($user['role'] == 'pegawai')
        			if (isset($user['Team']['idtim']))
        			$sellsurl = $this->Html->url(
        				['controller'=>'sells', 'action'=>'index', $user['Team']['idtim']]);
        		?>
        		<li class="dropdown">
		         	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
		         		Transaksi
		         		<span class="caret"></span>
		         	</a>
		          	<ul class="dropdown-menu">
		            	<li <?php echo $sells; ?>><a href="<?php echo $sellsurl; ?>">Transaksi</a></li>
                        <li <?php echo $graphs; ?>><a href="<?php echo $graphurl; ?>">Grafik Transaksi</a></li>
		          	</ul>
        		</li>
        		<?php } ?>
			</ul>

			<ul class="nav navbar-nav navbar-right">
				<li id='navbar-login'>
					<?php if($user) { ?>
					<a href="<?php echo $logouturl; ?>" class='logout'><span>Logout, <?php echo $user['firstname'];?></span></a>
					<?php } else {
						if(strpos($this->request->here(), "login") === FALSE)
						echo $this->Html->link('Login',
                            ['controller' => 'users', 'action'=>'login'],
                            ['escape' => false, 'rel' => 'nofollow']
						);
					} ?>
				</li>
			</ul>
		</div>
  	</div>
</div>

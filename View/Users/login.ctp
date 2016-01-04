<!-- app/View/Users/login.ctp -->

<div class="">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <h1><?php echo __('Silahkan login untuk mengakses sistem'); ?></h1>
        <?php echo $this->Form->input('username', array('class' => 'form-control'));
        echo $this->Form->input('password', array('class' => 'form-control'));
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Login')); ?>
</div>

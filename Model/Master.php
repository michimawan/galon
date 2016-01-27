<?php

class Master extends AppModel {
    // public $actsAs = array('Containable');

	public $hasMany = array('Sell' => array(
    	'className' => 'Sell',
       	'foreignKey' => 'idmaster'
    ));
}
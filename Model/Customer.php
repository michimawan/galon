<?php

class Customer extends AppModel {
	public $validate = array(
        'namapelanggan' => array(
            'nonEmpty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Nama Pelanggan harus diisi.',
				'allowEmpty' => false
            )

        ),
        'alamat' => array(
            'nonEmpty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Alamat Pelanggan harus diisi.',
				'allowEmpty' => false
            )

        ),
        'nohp' => array(
            'nonEmpty' => array(
                'rule' => array('notEmpty'),
                'message' => 'No. HP Pelanggan harus diisi.',
				'allowEmpty' => false
            )

        ),
    );

    public $hasOne = array(
        'PairTeamCustomer' => array(
        'className' => 'PairTeamCustomer',
        'foreignKey' => 'idcustomer'
        )
    );

	public $hasMany = array('Sell' => array(
    	'className' => 'Sell',
       	'foreignKey' => 'idcustomer'
    ));

    public function get_names($query){
        $q = "SELECT `id`, `namapelanggan` FROM `customers` AS `Customer`
            WHERE (namapelanggan LIKE '%$query%') AND id NOT IN (SELECT idcustomer FROM pair_team_customers) AND status = 1 LIMIT 5";

        return $this->query($q);
    }

    public function get_customer_in_team($idtim){
        $q = "SELECT * FROM `customers` AS `Customer`
            WHERE `Customer`.`id` IN
                (SELECT `pair_team_customers`.`idcustomer` FROM `pair_team_customers` WHERE `pair_team_customers`.`idtim` = '$idtim')
            AND `Customer`.`status` = 1
            GROUP BY `Customer`.`alamat`";

        return $this->query($q);
    }

    public function get_customer_not_have_team(){
        $q = "SELECT * FROM `customers` AS `Customer` WHERE `Customer`.`id` NOT IN (SELECT `PairTeamCustomer`.`idcustomer` FROM `pair_team_customers` AS `PairTeamCustomer`) AND `Customer`.`status` = 1 ORDER BY `Customer`.`id` ASC";

        return $this->query($q);
    }
}

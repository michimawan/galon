<?php

class Sell extends AppModel {
	public $validate = array();

    public $belongsTo = array(
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey'=>'idcustomer'
        ),
        'Good' => array(
            'className'  => 'Good',
            'foreignKey' => 'idgood'
        ),
        'Team' => array(
            'className' => 'Team',
            'foreignKey'=>'idtim'
        ),
        'Master' => array(
            'className' => 'Master',
            'foreignKey' => 'idmaster',
        ),
    );

    public function cek_lock($idtim){
        $q = "SELECT `Master`.`start`,`Master`.`galonterjual`,`Master`.`finish`, `Master`.`status`, `Master`.`galonkosong` FROM `masters` AS `Master` WHERE `Master`.`idtim` = '$idtim' AND `Master`.`date` = SUBSTRING(NOW(), 1,10)";

        return $this->query($q);
    }

    public function save_start($idtim, $start, $dates){
        $q = "INSERT INTO `galon`.`masters` (`idtim`, `start`, `galonkosong`, `finish`, `date`) 
            VALUES ('$idtim', '$start', '0', '0', '$dates');";

        return $this->query($q);
    }

    public function list_customer_to_team($idtim){
        $q = "SELECT `Customer`.`id`, `Customer`.`namapelanggan` FROM `customers` AS `Customer`
            WHERE `Customer`.`id` IN 
                (SELECT `pair_team_customers`.`idcustomer` FROM `pair_team_customers` 
                    WHERE `pair_team_customers`.`idtim` = '$idtim')
            AND `Customer`.`id` NOT IN (SELECT idcustomer FROM sells WHERE idtim = '$idtim' AND DATE(`date`) = SUBSTRING(NOW(), 1, 10))
            ORDER BY `Customer`.`id`";

        return $this->query($q);
    }

    public function get_master_based_date_idtim($idtim, $date){
        $q = "SELECT * FROM `masters` AS `Master` WHERE `date` LIKE '$date' AND `idtim` LIKE '$idtim'";

        return $this->query($q);
    }
}
<?php

class Team extends AppModel {
    // public $actsAs = array('Containable');
    public $primaryKey = 'idtim';
	public $validate = array(
        'namatim' => array(
            'nonEmpty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Nama Tim harus diisi.',
				'allowEmpty' => false
            )
			
        ),
        'unique' => array(
            'rule'    => array('isUniqueUsername'),
            'message' => 'Nama Tim sudah ada.'
        )
    );

    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey'=>'idpegawai'
        )
    );

    public $hasMany = array(
        'PairTeamCustomer' => array(
            'className' => 'PairTeamCustomer',
            'foreignKey' => 'idtim'
        ),
        'Sell' => array(
            'className' => 'Sell',
            'foreignKey' => 'idtim'
        )
    );

    public function getAllTeams(){
        $q = "SELECT `User`.`id`, firstname, lastname, username, `Team`.`idtim`
                FROM users AS `User`
                LEFT JOIN `teams` AS `Team`
                ON `User`.`id` = `Team`.`idpegawai`
                WHERE `Team`.idtim <> 'NULL'
                ORDER BY `Team`.`idtim` ASC";
                
        return $this->query($q);
    }

    public function getDistinctTeamID(){
        $q = "SELECT DISTINCT(idtim) FROM `teams` AS `Team` ORDER BY `idtim` ASC";

        return $this->query($q);
    }

    public function deleteTeam($idtim){
        $q = "UPDATE `galon`.`teams` SET `status` = '0'  WHERE idtim = ".$idtim;

        return $this->query($q);
    }

    public function id_tim_exist($idtim){
        $q = "SELECT * FROM `teams` As `Team` WHERE `idtim` = ".$idtim;

        return $this->query($q);
    }

    public function get_all_jmlgalon(){
        $q = "SELECT DISTINCT(`Team`.`idtim`), `Team`.`jmlgalon` FROM `teams` AS `Team` WHERE `Team`.`status` = 1";

        return $this->query($q);
    }

    public function get_a_team_jmlgalon($idtim){
        $q = "SELECT DISTINCT(`Team`.`idtim`), `Team`.`jmlgalon` FROM `teams` AS `Team` WHERE `Team`.`status` = 1 AND `Team`.`idtim` LIKE '$idtim'";

        return $this->query($q);
    }

    public function save_galon($idtim, $galon){
        $q = "UPDATE `galon`.`teams` SET `jmlgalon` = '$galon' WHERE `teams`.`idtim` = '$idtim'";

        return $this->query($q);
    }

    public function insert_to_team($idtim, $idpegawai){
        $q = "INSERT INTO `teams`(`idpegawai`, `idtim`) VALUES ('$idpegawai','$idtim')";

        return $this->query($q);
    }
}
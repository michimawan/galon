<?php

class Attendance extends AppModel{
	//define many to one
	public $belongsTo = array(
		'User' => array(
            'className' => 'User',
            'foreignKey' => 'idpegawai'
        )
    );

    public function getMonthRecord($idpegawai, $month, $year){
        $q = "SELECT id, tanggal, kehadiran 
                FROM `attendances` AS `Attendance`
                WHERE EXTRACT( MONTH FROM `tanggal` ) = '$month' 
                    AND EXTRACT( year FROM `tanggal` ) = '$year' 
                    AND idpegawai = '$idpegawai' 
                ORDER BY `tanggal` ASC";

        return $this->query($q);
    }

    public function getYearRecord($idpegawai, $year){
    	
    }

    public function getUserIDThatAttend(){
        $q = "SELECT `idpegawai` FROM `attendances` WHERE tanggal LIKE curdate()";

        return $this->query($q);
    }
}
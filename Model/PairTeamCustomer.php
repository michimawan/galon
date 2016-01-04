<?php

class PairTeamCustomer extends AppModel{
	
	public $belongsTo = array(
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'idcustomer'
        ),
        /*
        'Team' => array(
            'className' => 'Team',
            'foreignKey' => 'idtim'
        )
        */
    );
    
    public $hasMany = array('Team' => array(
            'className' => 'Team',
            'foreignKey' => 'idtim'
        ));
    
    public function delete_by_cust_tim($idcustomer, $idtim){
        $q = "DELETE FROM pair_team_customers WHERE idcustomer = ".$idcustomer." AND idtim =".$idtim;

        return $this->query($q);
    }

    public function delete_related_customer_to_team($idtim){
        $q = "DELETE FROM pair_team_customers WHERE idtim = ".$idtim;

        return $this->query($q);
    }

}
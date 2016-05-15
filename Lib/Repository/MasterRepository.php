<?php
App::uses('Model', 'Model');

class MasterRepository
{
    private $uses = ['Master'];

    public function __construct()
    {
        foreach($this->uses as $use)
            App::import('Model', $use);

        $this->masterModel = new $this->uses[0];
    }

    public function getUnlockedMasterDataFor($idtim = null)
    {
        if(!$idtim)
            return [];

        $this->masterModel->unbindModel(['hasMany' => ['Sell']]);
        return $this->masterModel->find('all', [
            'conditions' => ['Master.idtim' => $idtim, 'Master.status' => 0],
        ]);
    }

    public function getModel()
    {
        return $this->masterModel;
    }
}

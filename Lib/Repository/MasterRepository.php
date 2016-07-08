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

    public function getMasterDataFor($idmaster = null)
    {
        if(!$idmaster)
            return [];

        return $this->masterModel->find('first', [
            'conditions' => ['Master.id' => $idmaster],
            'recursive' => -1,
        ]);
    }

    private function getSelledGalonForDate($idtim, $startDate, $endDate)
    {
        $masters = $this->masterModel->find('all', [
            'conditions' => [
                'and' => [
                    ['Master.date <= ' => $startDate, 'Master.date >= ' => $endDate ],
                    $idtim,
                ]
            ],
            'recursive' => -1
        ]);

        $data = [];
        foreach($masters as $master) {
            if(!isset($data[$master['Master']['date']]))
                $data[$master['Master']['date']] = 0;
            $data[$master['Master']['date']] += $master['Master']['galonterjual'];
        }

        return $data;
    }

    public function getGraphDataFor($idtim, $startDate, $endDate)
    {
        if(!$startDate)
            $startDate = date('Y-m-d');
        if(!$endDate) {
            $endDate = (new Datetime($startDate))->sub(DateInterval::createFromDateString('28 days'));
            $ed = new ReflectionObject($endDate);
            $e = $ed->getProperty('date');
            $endDate = substr($e->getValue($endDate), 0, 10);
        }

        if($idtim)
            $idtim = array('Master.idtim' => $idtim);

        return $this->getSelledGalonForDate($idtim, $startDate, $endDate);
    }

    public function getModel()
    {
        return $this->masterModel;
    }
}

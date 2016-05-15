<?php
App::uses('Model', 'Model');

class TeamRepository
{
    private $uses = ['Team'];

    public function __construct()
    {
        foreach($this->uses as $use)
            App::import('Model', $use);

        $this->teamModel = new $this->uses[0];
    }

    public function getTeamAndAttendanceDataBy($idtim = null, $date = null)
    {
        if(! ($idtim && $date))
            return [];

        $team = $this->teamModel->find('all', [
            'conditions' => array('Team.idtim' => $idtim),
            'recursive' => 0,
        ]);
        foreach($team as &$user) {
            $attendance = $this->teamModel->User->Attendance->find('first', [
                'conditions' => ['idpegawai' => $user['User']['id'], 'tanggal' => substr($date, 0, 10)],
                'recursive' => 0,
            ]);
            $user['User']['kehadiran'] =  $attendance['Attendance']['kehadiran'];
        }
        return $team;
    }

    public function getListAllTeam()
    {
        $this->teamModel->unbindModel(['hasMany' => ['PairTeamCustomer', 'Sell']]);
        $teams = $this->teamModel->find('all', [
            'order' => 'idtim',
            'conditions' => ['Team.status' => 1]
        ]);

        return $this->convertTeamsToListOfTeam($teams);
    }


    private function convertTeamsToListOfTeam($teams = [])
    {
        if(! count($teams))
            return [];

        $list_team = array();
        foreach ($teams as $team) {
            if(!isset($list_team[$team['Team']['idtim']])){
                $list_team[$team['Team']['idtim']] = $team['User']['firstname']." ";
            }
            else
                $list_team[$team['Team']['idtim']] .= $team['User']['firstname'];
        }
        return $list_team;
    }

    public function getModel()
    {
        return $this->teamModel;
    }
}

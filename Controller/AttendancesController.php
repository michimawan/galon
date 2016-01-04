<?php

class AttendancesController extends AppController {
	public $layout = 'layout';

    private function check_user_access($location){
        $user = $this->Auth->User();
        if($user['role'] == 'pegawai')
            if($location == 'index' || $location == 'absent' || $location == 'present')
                $this->redirect(array('action' => 'rekapbulanan', $user['id'], date('Y'), date('m')));
    }

	public function index($search = null) {
        $this->set('title', 'Galon - Presensi Kehadiran Pegawai');
        $this->check_user_access('index');
        //get all user that has been checked
        $user_attend = $this->Attendance->getUserIDThatAttend();
        $data = $this->to1DArray($user_attend, 'attendances', 'idpegawai');
/*
        //convert the result array to normal array
        $users_attend = array();
        foreach($user_attend as $id){
            $users_attend[] = $id['attendances']['idpegawai'];
        }
*/
        //paginate it
        $this->paginate = array(
            'limit' => 20,
            'order' => array('User.username' => 'asc'),
            'conditions' => array('NOT' => array('User.id' => $data), 'AND' => array('User.status' => 1)),
            'recursive' => -1
        );
        $users = $this->paginate('User');
        $this->set(compact('users'));
    }

    public function absent($id = null){
        $this->check_user_access('absent');
        if($id){
            $this->Attendance->User->id = $id;
            if($this->Attendance->User->exists()){

                $data = array('Attendance' => array('idpegawai' => $id, 'tanggal' => date('Y-m-d'), 'kehadiran' => '0'));
                $this->Attendance->create();
                //$this->set(compact('data'));
                if($this->Attendance->save($data)){
                    $this->Session->setFlash('Pegawai sudah ditandai tidak masuk', 'customflash', array('class' => 'success'));
                }
            }
        } 
        
        $this->redirect(array('action' => 'index'));
    }

    public function present(){
        $this->check_user_access('present');
        $user_not_attend = $this->Attendance->User->getAllUserIDThatStillNotAttend();
        $data = $this->to1DArray($user_not_attend, 'users', 'id');
        
        if(!$data){
            $this->redirect(array('action' => 'index'));
        }

        $datas = array();
        foreach ($data as $id_user) {
            $datas[] = array('Attendance' => array('idpegawai' => $id_user, 'tanggal' => date('Y-m-d'), 'kehadiran' => '1'));
        }
        $this->set(compact('datas'));
        $this->Attendance->create();
        if($this->Attendance->saveAll($datas)){
            $this->Session->setFlash('Pegawai sudah dipresensi', 'customflash', array('class' => 'success'));
        }

        $this->redirect(array('action' => 'index'));
    }

    public function rekapbulanan($id = null, $year = null, $month = null){
        $this->set('title', 'Galon - Rekap Kehadiran Bulanan');

        $user = $this->Attendance->User->find('first', array(
            'field' => array('User.id', 'User.username', 'User.firstname', 'User.lastname'),
            'recursive' => -1, 
            'conditions' => array('User.id' => $id, 'User.status' => 1)
            )
        );

        if($user) {
            $month_record = $this->Attendance->getMonthRecord($id, $month, $year);

            $count_present = 0;
            foreach ($month_record as $day) {
                $day['Attendance']['kehadiran'] ? $count_present++ : "";
            }

            $this->set(compact('count_present'));
            $this->set(compact('month_record'));
            $this->set(compact('user'));            
    	} else {
            $this->Session->setFlash('User tidak ada', 'customflash', array('class' => 'warning'));
            $this->redirect(array('controller'=>'users','action' => 'index'));
        }
    }

    private function to1DArray($_3D_array, $field_1, $field_2){
        $users_not_attend = array();
        foreach($_3D_array as $id_user){
             $users_not_attend[] = $id_user[$field_1][$field_2];
        }
        return $users_not_attend;
    }
}
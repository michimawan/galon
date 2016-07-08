<?php

class TeamsController extends AppController {
    public $layout = 'layout';

    private function check_user_access($location){
        $user = $this->Auth->user();
        if($user['role'] == 'pegawai')
            if($location == 'add' || $location == 'delete')
                $this->redirect(array('controller' => 'users', 'action' => 'view', $user['id']));
    }

    public $components = array('Paginator');
    public function index(){
        $this->set('title','Galon - Data Tim Pegawai');

        $this->Paginator->settings = array(
            'limit' => 20,
            'order' => array( 'Team.idtim' => 'asc' ),
            'contain' => array('Team', 'User'=> array('fields' => array('User.firstname'))),
            'conditions' => array('NOT' => array('Team.status' => '0'))
        );
        $teams = $this->Paginator->paginate('Team');

        $galons = $this->Team->get_all_jmlgalon();

        $this->set(compact('teams'));
        $this->set(compact('galons'));
    }

    public function add(){
        $this->set('title','Galon - Tambah Data Tim');
        $this->check_user_access('add');

        if($this->request->is('post')){

            if($this->request->data['Team']['idpegawai_1'] == $this->request->data['Team']['idpegawai_2']){
                $this->Session->setFlash('2 anggota tim tidak boleh sama,','customflash', array('class' => 'warning'));
                $this->redirect(array('action' => 'index'));
            }

            $free_team_id = $this->get_free_team_id();
            $this->Team->insert_to_team($free_team_id, $this->request->data['Team']['idpegawai_1']);
            $this->Team->insert_to_team($free_team_id, $this->request->data['Team']['idpegawai_2']);
            $this->Session->setFlash('Tim baru berhasil dibuat,','customflash', array('class' => 'success'));
            $this->redirect(array('action' => 'index'));
        }
    }

    private function get_free_team_id(){
        $team_ids_now = $this->Team->getDistinctTeamID();
        $counter = 1;
        foreach ($team_ids_now as $single_team_id) {

            if($counter != $single_team_id['Team']['idtim'])
                return $counter;

            $counter++;
        }
        return $counter;
    }

    /*
     * input to this function User.firstname + " " + User.lastname + " | " + User.username
     * return user id for the firstname+lastname
     */
    private function get_user_id($user_names){
        $name_pegawai = $this->remove_behind_pipeline($user_names);
        $id_user = $this->Team->User->getUserIdByName($name_pegawai);
        return $id_user[0]['User']['id'];
    }

    private function remove_behind_pipeline($str){
        if(strlen($str) > 0)
            return substr($str, 0, strrpos($str, " |"));
        else
            return "";
    }

    public function autocompletes($query = null){
        $this->autoRender = false;
        if($this->request->is('ajax')){
            $term = $this->params['url']['term'];

            $names = $this->Team->User->get_names_not_have_team($term);

            if($names){
                echo json_encode($names);
            } else {
                echo "no";
            }
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }

    public function delete($id = null){
        $this->check_user_access('delete');
        if($this->request->is('post')){
            if($id){
                if(!$this->Team->deleteTeam($id)){
                    if(!$this->Team->PairTeamCustomer->delete_related_customer_to_team($id))
                        $this->Session->setFlash('Tim sudah dihapus','customflash', array('class' => 'success'));
                    else
                        $this->Session->setFlash('Tim gagal dihapus','customflash', array('class' => 'warning'));
                }
                else
                    $this->Session->setFlash('Tim gagal dihapus','customflash', array('class' => 'warning'));
            }
        }

        $this->redirect(array('action' => 'index'));
    }

    public function pair_cust($idtim = null){
        $this->set('title', 'Galon - Relasi Pelanggan ke Tim');
        if($this->request->is('post')){
            $idtim = $this->request->data['PairTeamCustomer']['idtim'];
            if($this->Team->id_tim_exist($idtim)){
                $data = $this->request->data;
                $data['Customer']['id'] = $data['PairTeamCustomer']['idcustomer'];
                $data['Customer']['harikunjungan'] = $data['PairTeamCustomer']['harikunjungan'];
                $this->Team->PairTeamCustomer->create();
                if($this->Team->PairTeamCustomer->saveAll($data)){
                    $this->set(compact('data'));
                }
                $this->Session->setFlash('Data Pelanggan pada tim sudah ditambahkan','customflash', array('class' => 'success'));
                $this->redirect(array('action' => 'pair_cust', $idtim));
            } else {
                $this->set('datas', $this->Team->User->find('all'));
            }
        } else {
            if($idtim) {
                $this->Paginator->settings = array(
                    'limit' => 20,
                    'conditions' => array('PairTeamCustomer.idtim' => $idtim),
                    'recursive' => 0
                );
                $pair_data = $this->Paginator->paginate('PairTeamCustomer');

                $team = $this->Team->find('all', array('conditions' => array('Team.idtim' => $idtim), ));
                $this->set(compact('pair_data'));
                $this->set(compact('team'));
                $this->set(compact('idtim'));
            } else {
                $this->redirect(array('action' => 'index'));
            }
        }
    }

    public function customer_not_teamed(){
        $this->layout = 'no_layout';
        $customer_not_in_team = $this->Team->PairTeamCustomer->Customer->get_customer_not_have_team();

        $this->set(compact('customer_not_in_team'));
    }

    public function user_not_teamed(){
        $this->layout = 'no_layout';
        $user_not_in_team = $this->Team->User->get_user_not_have_team();

        $this->set(compact('user_not_in_team'));
    }

    public function delete_pair($idcustomer = null, $idtim = null){
        if($this->request->is('post')){
            if(!$this->Team->PairTeamCustomer->delete_by_cust_tim($idcustomer, $idtim)){

                $this->Session->setFlash('Data Pelanggan pada tim sudah dihapus','customflash', array('class' => 'success'));
                $this->redirect(array('action' => 'pair_cust', $idtim));
            } else {
                $this->Session->setFlash('Data Pelanggan pada tim gagal dihapus','customflash', array('class' => 'danger'));
                $this->redirect(array('action' => 'pair_cust', $idtim));
            }
        } else {
            $this->Session->setFlash('Data Pelanggan pada tim gagal dihapus','customflash', array('class' => 'danger'));
            $this->redirect(array('action' => 'index'));
        }

    }


    public function change($idtim = null){
        $this->layout = 'no_layout';
        if($this->request->is('post')){
            $data = $this->request->data;
            $this->set(compact('data'));
            if(!$this->Team->save_galon($data['idtim'], $data['jmlgalon']))
                $this->Session->setFlash('Jumlah galon pada tim berhasil diubah','customflash', array('class' => 'success'));
            else
                $this->Session->setFlash('Jumlah galon pada tim gagal diubah','customflash', array('class' => 'danger'));
            $this->redirect(array('action' => 'index'));
        } else {
            $team_galon = $this->Team->get_a_team_jmlgalon($idtim);

            $this->set(compact('team_galon'));
            $this->set(compact('idtim'));
        }
    }

}

<?php
class CustomersController extends AppController {
	public $layout = 'layout';
    public $uses = array('PairTeamCustomer', 'Customer');
	public function index() {
        $this->set('title','Galon - Data Pelanggan');
        $this->paginate = array(
            'limit' => 20,
            'order' => array('Customer.kdpelanggan' => 'asc' ),
            'conditions' => array('NOT' => array('Customer.status' => '0'))
        );
        $customers = $this->paginate('Customer');
        $this->set(compact('customers'));
    }

    public function add(){
        $this->set('title','Galon - Tambah Data Pelanggan');
    	if ($this->request->is('post')) {
                 
            $this->Customer->create();
            $this->request->data['Customer']['kdpelanggan'] = $this->generate_kodepelanggan();
            if ($this->Customer->save($this->request->data)) {
                $this->Session->setFlash('Pelanggan baru berhasil dibuat', 'customflash', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Pelanggan baru gagal dibuat, silahkan dicoba lagi', 'customflash', array('class' => 'warning'));
            }   
        }
    }

    public function edit($id = null){
        $this->set('title','Galon - Ubah Data Pelanggan');
    	if (!$id) {
            $this->Session->setFlash('Gagal memilih pelanggan yang akan diedit', 'customflash', array('class' => 'danger'));
            $this->redirect(array('action'=>'index'));
        }
 
        $customer = $this->Customer->findById($id);
        if (!$customer) {
            $this->Session->setFlash('Data pelanggan yang diedit berbeda', 'customflash', array('class' => 'danger'));
            $this->redirect(array('action'=>'index'));
        }
 
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Customer->id = $id;
            if ($this->Customer->save($this->request->data)) {
                $this->Session->setFlash('Data pelanggan berhasil diubah', 'customflash', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Gagal mengedit data pelanggan', 'customflash', array('class' => 'warning'));
            }
        }
 
        if (!$this->request->data) {
            $this->request->data = $customer;
        }
    }

    public function delete($id = null) {
        if($this->request->is('post')){
            if (!$id) {
                $this->Session->setFlash('Tidak ada pelanggan yang dipilih', 'customflash', array('class' => 'warning'));
            }
             
            $this->Customer->id = $id;
            if($this->Customer->exists()){
                if ($this->Customer->saveField('status', 0)) {
                    $this->Session->setFlash('Data pelanggan berhasil dihapus', 'customflash', array('class' => 'success'));
                    $this->redirect(array('action' => 'index'));
                }
            }
            
            $this->Session->setFlash('Data pelanggan gagal dihapus', 'customflash', array('class' => 'warning'));
        }
        $this->redirect(array('action' => 'index'));
    }

    private function generate_kodepelanggan(){
        $kdpelanggan = 'PG';

        $missing_code = str_pad($this->get_missing_number(), 4, '0', STR_PAD_LEFT);
        
        return $kdpelanggan.$missing_code;
    }

    private function get_missing_number(){
        $datas = $this->Customer->find('all', array('fields' => 'SUBSTRING(kdpelanggan, 3) AS kdpelanggan', 'order' => 'kdpelanggan'));
        
        $missing_code = 1;
        if(count($datas) == 0)
            return $missing_code;
        
        for ($i = 0; $i < number_format($datas[count($datas)-1][0]['kdpelanggan']); $i++ ) {
            if(number_format($datas[$i][0]['kdpelanggan']) != $missing_code)
                return $missing_code;

            $missing_code++;
        }
        
        return $missing_code;
    }

    public function autocompletes($query = null){
        $this->autoRender = false;
        if($this->request->is('ajax')){
            $term = $this->params['url']['term'];

            $names = $this->Customer->get_names($term);
            // $this->set(compact('names'));
            if($names){
                echo json_encode($names);
            } else {
                echo "no";
            }
        } else {
            $this->redirect(array('action' => 'index'));
        }
        
        //$this->redirect(array('action' => 'autocompletes'));
    }


    // admin only
    public function debt($idtim = null){
        $this->set('title', 'Galon - Daftar Piutang Pelanggan');
        $teams = array();
        $user = $this->Auth->User(); 
        if($user['role'] == 'pegawai')
            $idtim = $user['Team']['idtim'];
        
        if($idtim){
            $this->paginate = array(
                    'limit' => 20,
                    // 'order' => array('Customer.hutang' => 'DESC' ),
                    'conditions' => array('OR' => array('Customer.hutang > ' => 0, 'Customer.galonterpinjam > ' => 0), 'AND' => array('Customer.status' => 1, 'PairTeamCustomer.idtim' => $idtim)),
                    'fields' => array('PairTeamCustomer.idtim', 'PairTeamCustomer.idcustomer', 'Customer.harikunjungan',
                                'Customer.id', 'Customer.kdpelanggan', 'Customer.namapelanggan', 'Customer.alamat', 'Customer.hutang', 'Customer.galonterpinjam', 'Customer.transaksiterakhir'),
                    'recursive' => 0
                );
                $customers = $this->paginate('PairTeamCustomer');
                $teams = $this->Customer->PairTeamCustomer->Team->find('all', array('order' => 'idtim','conditions' => array('Team.status' => 1, 'Team.idtim' => $idtim), 'recursive' => 0));
        } else {
            $this->paginate = array(
                'limit' => 20,
                // 'order' => array('Customer.hutang' => 'DESC' ),
                'conditions' => array('OR' => array('Customer.hutang > ' => 0, 'Customer.galonterpinjam > ' => 0), 'AND' => array('Customer.status' => 1)),
                'fields' => array('PairTeamCustomer.idtim', 'PairTeamCustomer.idcustomer', 'Customer.harikunjungan',
                            'Customer.id', 'Customer.kdpelanggan', 'Customer.namapelanggan', 'Customer.alamat', 'Customer.hutang', 'Customer.galonterpinjam', 'Customer.transaksiterakhir'),
                'recursive' => 0
            );
            $customers = $this->paginate('PairTeamCustomer');
        }

        $list_teams = $this->Customer->PairTeamCustomer->Team->find('all', array('order' => 'idtim','conditions' => array('Team.status' => 1), 'recursive' => 0));
        $list_team = $this->to_list_team($list_teams);

        $this->set(compact('idtim'));
        $this->set(compact('customers'));
        $this->set(compact('teams'));
        $this->set(compact('list_team'));
        // $this->set(compact('list_teams'));
    }

    private function to_list_team($list_teams){
        $list_team = array();
        foreach ($list_teams as $team) {
            if(!isset($list_team[$team['Team']['idtim']])){
                $list_team[$team['Team']['idtim']] = $team['User']['firstname']." ";
            }
            else 
                $list_team[$team['Team']['idtim']] .= $team['User']['firstname'];
        }
        return $list_team;
    }
}
<?php
App::import('Factory', 'FilterFactory');
App::import('Factory', 'ModelConditionFactory');

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

        $list_team = $this->get_list_team();

        $this->set(compact('customers'));
        $this->set(array('list_team' => $list_team));
        $this->set(array('filters' => $this->getFilters()));
    }

    public function add(){
        $this->set('title','Galon - Tambah Data Pelanggan');
        if ($this->request->is('post')) {

            $this->Customer->create();
            $this->request->data['Customer']['kdpelanggan'] = $this->generate_kodepelanggan();
            $this->request->data['Customer']['galonterpinjam'] = $this->request->data['Customer']['galonterpinjam'] > 0 ?: 1;
            $customer = $this->Customer->save($this->request->data);

            $this->Customer->PairTeamCustomer->create();
            $this->request->data['PairTeamCustomer']['idcustomer'] = $customer['Customer']['id'];
            $pair_team_cust = $this->Customer->PairTeamCustomer->save($this->request->data);
            if ($pair_team_cust['PairTeamCustomer']['id']) {
                $this->Session->setFlash('Pelanggan baru berhasil dibuat', 'customflash', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Pelanggan baru gagal dibuat, silahkan dicoba lagi', 'customflash', array('class' => 'warning'));
            }
        }

        $list_team = $this->get_list_team();

        $this->set(array('list_team' => $list_team));
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
            if ($this->Customer->saveAll($this->request->data)) {
                $this->Session->setFlash('Data pelanggan berhasil diubah', 'customflash', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Gagal mengedit data pelanggan', 'customflash', array('class' => 'warning'));
            }
        }

        $list_team = $this->get_list_team();
        $this->set(array('list_team' => $list_team));

        if (!$this->request->data) {
            $this->request->data = $customer;
        }
    }

    public function ranks($limit = null) {
        $this->set('title', 'Galon - Rangking Pelanggan');

        if(!$limit)
            $limit = 20;

        $this->paginate = array(
            'fields' => array('Sell.idcustomer', 'SUM(Sell.jmlbeli) as beli', 'SUM(Sell.jmlpinjam) as pinjam', 'SUM(Sell.jmlkembali) as kembali', 'Customer.namapelanggan', 'Customer.alamat', 'Customer.kdpelanggan'),
            'group' => array('Sell.idcustomer'),
            'conditions' => array('NOT' => array('Sell.status' => '0')),
            'recursive' => -1,
            'limit' => $limit,
            'order' => 'beli desc',
            'joins' => array(
                array(
                'table' => 'customers',
                'alias' => 'Customer',
                'type' => 'LEFT',
                'conditions' => array('Customer.id = Sell.idcustomer'),
            )),
        );
        $this->loadModel('Sell');
        $customers = $this->paginate('Sell');

        $option = array(
            5 => 5,
            10 => 10,
            25 => 25,
            50 => 50,
            100 => 100,
            500 => 500,
            1000 => 1000,
        );

        $list_team = $this->get_list_team();

        $this->set(compact('list_team'));
        $this->set(compact('customers'));
        $this->set(compact('option'));
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

        $missing_code = str_pad($this->get_missing_number(), 8, '0', STR_PAD_LEFT);

        return $kdpelanggan.$missing_code;
    }

    private function get_missing_number(){
        $datas = $this->Customer->find('all', array('fields' => 'DISTINCT(SUBSTRING(kdpelanggan, 3)) AS kdpelanggan', 'order' => 'kdpelanggan'));

        $missing_code = 1;
        if(count($datas) == 0)
            return $missing_code;

        $maxKdpelanggan = (int) $datas[count($datas)-1][0]['kdpelanggan'];
        for ($i = 0; $i < $maxKdpelanggan; $i++ ) {
            $currentNumber = (int) $datas[$i][0]['kdpelanggan'];
            if($currentNumber != $missing_code)
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

    public function printdebt($idtim = null){
        $this->set('title', 'Galon - Daftar Piutang Pelanggan');

        $tim['Customer.status'] = 1;
        if($idtim)
            $tim['PairTeamCustomer.idtim'] = $idtim;

        $customers = $this->PairTeamCustomer->find('all', array(
            'limit' => 20,
            'conditions' => array(
                'OR' => array(
                    'Customer.hutang > ' => 0,
                    'Customer.galonterpinjam > ' => 0),
                'AND' => $tim
            ),
            'fields' => array('PairTeamCustomer.idtim', 'PairTeamCustomer.idcustomer', 'Customer.harikunjungan',
            'Customer.id', 'Customer.kdpelanggan', 'Customer.namapelanggan', 'Customer.alamat', 'Customer.hutang', 'Customer.galonterpinjam', 'Customer.transaksiterakhir'),
            'recursive' => 0
        ));
        if($idtim) {
            $team = $this->PairTeamCustomer->Team->find('all', array(
                'conditions' => array('Team.idtim' => $idtim)
            ));
            $team = "Daftar Piutang Sales " . $team[0]['User']['firstname'] .
                " dan " . $team[1]['User']['firstname'];
        }
        else
            $team = "Daftar Semua Piutang Pelanggan";

        $this->set(compact('customers'));
        $this->set(compact('team'));

        $this->layout = 'print';
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

        $list_team = $this->get_list_team();

        $this->set(compact('idtim'));
        $this->set(compact('customers'));
        $this->set(compact('teams'));
        $this->set(compact('list_team'));
    }

    private function get_list_team()
    {
        $list_teams = $this->Customer->PairTeamCustomer->Team->find('all', array('order' => 'idtim','conditions' => array('Team.status' => 1), 'recursive' => 0));
        return $this->to_list_team($list_teams);
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

    public function filter()
    {
        $this->set('title','Galon - Data Pelanggan');

        $filter = $this->params['url']['filter'];
        $text = $this->params['url']['text'];

        if($filter == null || $text == null) {
            $this->redirect(['action' => 'index']);
        }

        $filterConditions = $this->getModelCondition($filter, $text);
        $this->paginate = [
            'limit' => 20,
            'order' => ['Customer.id' => 'asc' ],
            'conditions' => ['NOT' => ['Customer.status' => '0'], $filterConditions]
        ];
        $customers = $this->paginate('Customer');
        $this->set(['customers' => $customers, 'filters' => $this->getFilters()]);
        return $this->render('index');
    }

    private function getFilters()
    {
        return (new FilterFactory('Customer'))->produce();
    }

    private function getModelCondition($filter, $text)
    {
        $params = [
            'filter' => $filter,
            'text' => $text
        ];
        return (new ModelConditionFactory('Customer', $params))->produce();
    }
}

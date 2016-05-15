<?php
App::import('Factory', 'FilterFactory');
App::import('Factory', 'ModelConditionFactory');
App::import('Repository', 'TeamRepository');
App::import('Repository', 'CustomerRepository');
App::import('Repository', 'SellRepository');
App::import('Repository', 'MasterRepository');

class SellsController extends AppController {
    public $layout = 'layout';

    private function check_user_access($location){
        $user = $this->Auth->user();
        if($user['role'] == 'pegawai' && isset($user['Team']['idtim']))
            $this->redirect(array('action' => $location, $user['Team']['idtim']));
        else if($user['role'] == 'pegawai')
            $this->redirect(array('controller' => 'users', 'action' => 'index'));
    }

    private function check_admin_access($location){
        $user = $this->Auth->user();
        if($user['role'] == 'admin'){
            if($location == 'dashboard' || $location == 'add' || $location == 'delete'){
                $this->redirect(array('action' => 'index'));
            }
        }
    }

    public function graph(){
        $this->set('title', 'Galon - Daftar Transaksi');

        $enddate = isset($this->params['url']['date1']) ? $this->params['url']['date1'] : null;
        $startdate = isset($this->params['url']['date2']) ? $this->params['url']['date2'] : null;
        $idtim = isset($this->params['url']['tim']) ? $this->params['url']['tim'] : null;

        if(!$startdate)
            $startdate = date('Y-m-d');
        if(!$enddate) {
            $enddate = (new Datetime($startdate))->sub(DateInterval::createFromDateString('28 days'));
            $ed = new ReflectionObject($enddate);
            $e = $ed->getProperty('date');
            $enddate = substr($e->getValue($enddate), 0, 10);
        }

        if($idtim)
            $idtim = array('Master.idtim' => $idtim);

        $masters = $this->Sell->Master->find('all', array(
            'conditions' => array(
                'and' => array(
                    array('Master.date <= ' => $startdate, 'Master.date >= ' => $enddate ),
                    $idtim,
                )
            ),
            'recursive' => -1
        ));

        $data = array();
        foreach($masters as $master) {
            if(!isset($data[$master['Master']['date']]))
                $data[$master['Master']['date']] = 0;
            $data[$master['Master']['date']] += $master['Master']['galonterjual'];
        }

        $list_team = $this->get_list_all_team();

        $this->set(compact('data'));
        $this->set(compact('list_team'));
    }

    // accessed by user and admin
    public function index($idtim = null) {
        $this->set('title', 'Galon - Riwayat Transaksi');

        $user = $this->Auth->user();

        $list_team = $this->get_list_all_team();

        if($user['role'] == 'pegawai')
            $idtim = $user['Team']['idtim'];

        $params = array('limit' => 20, 'recursive' => -1, 'order' => 'Master.date DESC');
        if($idtim > 0)
            $params['conditions'] = array('idtim' => $idtim);
        $this->paginate = $params;

        $this->loadModel('Master');
        $masters = $this->paginate('Master');

        $this->set(compact('masters'));
        $this->set(compact('list_team'));
        $this->set(array('filters' => $this->getFilters()));
    }

    public function detail($idmaster) {
        $this->set('title', 'Galon - Detail Transaksi Tim');
        $master = (new MasterRepository())->getMasterDataFor($idmaster);
        $team = (new TeamRepository())->getTeamAndAttendanceDataBy($master['Master']['idtim'], $master['Master']['date']);
        $sells = (new SellRepository())->getLockedSellTransactionFor($idmaster);
        $customers = (new CustomerRepository())->getCustomerInTeamNotDoingTransaction($master['Master']['idtim'], $sells);

        $this->set([
            'master' => $master,
            'sells' => $sells,
            'customers' => $customers,
            'team' => $team,
        ]);
    }

    // accessed by user
    public function dashboard($idtim = null){
        $this->set('title', 'Galon - Transaksi Tim');
        $this->check_admin_access('dashboard');

        if($idtim){
            $team_galon = $this->Sell->Team->find('first', array('conditions' => array('Team.idtim' => $idtim, 'Team.status' => 1), 'recursive' => -1));
            if($team_galon){

                $teams = $this->Sell->Team->User->get_user_with_idtim_and_attend_today($idtim);
                $good_price = $this->Sell->Good->find('first', array(
                    'conditions' => array('Good.namabarang LIKE' => '%galon%'),
                    'fields' => array('Good.hargajual')
                ));
                $datas = (new SellRepository())->getUnlockedSellTransactionFor($idtim);
                $customers = (new CustomerRepository())->getCustomerInTeamNotDoingTransaction($idtim, $datas);
                $master = (new MasterRepository())->getUnlockedMasterDataFor($idtim);

                $this->set([
                    'idtim' => $idtim,
                    'team_galon' => $team_galon,
                    'teams' => $teams,
                    'good_price' => $good_price,
                    'datas' => $datas,
                    'customers' => $customers,
                    'master' => $master,
                ]);
            } else
                $this->redirect(array('action' => 'index'));
        } else
            $this->redirect(array('action' => 'index'));
    }

    //accessed by user
    public function add($masterId = null){
        $this->check_admin_access('add');
        $this->set('title', 'Galon - Tambah Transaksi');
        $user = $this->Auth->user();

        if ($this->request->is('post')) {
            $saveStatus = (new SellRepository())->save($this->request->data);

            if ($saveStatus) {
                $this->Session->setFlash('Data transaksi berhasil ditambahkan', 'customflash', array('class' => 'success'));
                $this->redirect(array('action' => 'dashboard', $user['Team']['idtim']));
            } else {
                $this->Session->setFlash('Data transaksi gagal ditambahkan, silahkan dicoba lagi', 'customflash', array('class' => 'warning'));
            }

        }

        if($masterId){
            $goods = $this->Sell->Good->find('all', array('fields' => array('Good.id', 'Good.hargajual', 'Good.namabarang'), 'recursive' => -1, 'conditions' => array('Good.status' => 1)));
            $idtim = $user['Team']['idtim'];

            $prices = $this->array_to_list($goods, 'Good', 'id', 'hargajual');
            $reverse_prices = $this->array_to_list($goods, 'Good', 'hargajual', 'id');
            $goods = $this->array_to_list($goods, 'Good', 'id', 'namabarang');

            $this->set(compact('prices'));
            $this->set(compact('reverse_prices'));
            $this->set(compact('goods'));
            $this->set(compact('customers'));
            $this->set(compact('idtim'));
            $this->set(compact('masterId'));
        } else {
            $this->redirect(array('action' => 'dashboard', $user['Team']['idtim']));
        }
    }

    public function get_hutang_customer($kdpelanggan = null, $idtim = null){
        $this->autoRender = false;
        if($this->request->is('ajax')){
            $user_data = $this->Sell->Customer->find('first', array(
                'conditions' => ['Customer.kdpelanggan' => $kdpelanggan, 'PairTeamCustomer.idtim' => $idtim],
                'fields' => ['Customer.id', 'Customer.namapelanggan', 'Customer.hutang'],
                'recursive' => 0
            ));
            if($user_data){
                echo json_encode($user_data);
            } else {
                echo json_encode("no");
            }
        } else
            $this->redirect(array( 'action' => 'index'));
    }

    private function update_customer_hutang($hutang, $customer_id) {
        $this->Sell->Customer->updateAll(array('Customer.hutang' => $hutang), array('Customer.id' => $customer_id));
    }

    private function array_to_list($datas, $main_field, $field1, $field2){
        $list = array();
        foreach ($datas as $data) {
            $list[$data[$main_field][$field1]] = $data[$main_field][$field2];
        }
        return $list;
    }

    public function set_start_galon(){
        if($this->request->is('post')){
            if($this->Sell->save_start($this->request->data['Master']['idtim'], $this->request->data['Master']['start'], date('Y-m-d'))) ;

            $idtim = $this->request->data['Master']['idtim'];
        }

        $this->redirect(array('action' => 'dashboard', $idtim));
    }

    //accessed by user and admin
    public function edit($id = null){
        $this->set('title','Galon - Ubah Data Transaksi');

        if (!$id) {
            $this->Session->setFlash('Gagal memilih transaksi yang akan diedit', 'customflash', array('class' => 'danger'));
            $this->redirect(array('action'=>'index'));
        }

        $sell = $this->Sell->findById($id);
        if (!$sell) {
            $this->Session->setFlash('Data transaksi yang diedit berbeda', 'customflash', array('class' => 'danger'));
            $this->redirect(array('action'=>'index'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            $master_status = $this->Sell->Master->find('first',
                array('fields' => array('Master.id', 'Master.status'),
                'conditions' => array('Master.id' => $this->request->data['Sell']['idmaster']),
                'recursive' => -1));
            $user = $this->Auth->user();
            if($user['role'] == 'pegawai' && $master_status['Master']['status'] == '0'){
                $this->Sell->id = $id;
                if ($this->Sell->save($this->request->data)) {
                    $this->Session->setFlash('Data transaksi berhasil diubah', 'customflash', array('class' => 'success'));
                    $this->redirect(array('action' => 'dashboard', $user['Team']['idtim']));
                } else {
                    $this->Session->setFlash('Gagal mengedit data transaksi', 'customflash', array('class' => 'warning'));
                }
            } else if($user['role'] != 'pegawai' && $master_status['Master']['status'] != '1') {
                $this->Session->setFlash('Admin hanya dapat mengedit data transaksi yang telah di kunci', 'customflash', array('class' => 'warning'));
            } else if ($user['role'] != 'pegawai' && $master_status['Master']['status'] == '1') {


                // do some logic here to change customer data, master data, sell data
            }

        }

        if (!$this->request->data) {
            $this->request->data = $sell;
        }

        $idgood = $this->Sell->Good->find('first', array('conditions' => array('Good.namabarang LIKE' => '%galon%'), 'fields' => array('Good.id', 'Good.hargajual'), 'recursive' => -1));

        $customers = $this->Sell->Customer->find('first', array('conditions' => array('Customer.id' => $sell['Sell']['idcustomer'])));
        $customer[$customers['Customer']['id']] = $customers['Customer']['namapelanggan'];
        unset($customers);
        $this->set(compact('customer'));
        $this->set(compact('idgood'));
    }

    //accessed by admin and user
    public function delete($id){
        if($this->request->is('post')){
            $this->check_admin_access('delete');
            if (!$id) {
                $this->Session->setFlash('Tidak ada data transaksi yang dipilih', 'customflash', array('class' => 'warning'));
            }

            $this->Sell->id = $id;
            if($this->Sell->exists()){
                if ($this->Sell->delete()) {
                    $this->Session->setFlash('Data transaksi berhasil dihapus', 'customflash', array('class' => 'success'));
                    $this->redirect(array('action' => 'index'));
                }
            }
            $this->Session->setFlash('Data transaksi gagal dihapus', 'customflash', array('class' => 'warning'));
        }
        $this->redirect(array('action'=>'index'));
    }

    public function printblank($idtim = null, $date = null){
        $this->set('title', 'Galon - Cetak Blanko Transaksi');
        if($idtim){
            $teams = $this->Sell->Team->User->get_user_with_idtim_and_attend_today($idtim);
            $good_price = $this->Sell->Good->find('first', array(
                'conditions' => array('Good.namabarang LIKE' => '%galon%'),
                'fields' => array('Good.hargajual')
            ));
            $datas = [];
            $customers = (new CustomerRepository())->getCustomerInTeamNotDoingTransaction($idtim, $datas);

            $lock = $this->Sell->cek_lock($idtim);

            $this->set(compact('datas'));
            $this->set(compact('lock'));
            $this->set(compact('idtim'));
            $this->set(compact('teams'));
            $this->set(compact('good_price'));
            $this->set(compact('customers'));

            $this->layout = 'print';
        } else
            $this->redirect(array('action' => 'index'));
    }

    public function printfull($idmaster = null) {
        $this->set('title', 'Galon - Cetak Data');

        $master = (new MasterRepository())->getMasterDataFor($idmaster);
        $team = (new TeamRepository())->getTeamAndAttendanceDataBy($master['Master']['idtim'], $master['Master']['date']);
        $sells = (new SellRepository())->getLockedSellTransactionFor($idmaster);
        $customers = (new CustomerRepository())->getCustomerInTeamNotDoingTransaction($master['Master']['idtim'], $sells);

        $this->set([
            'master' => $master,
            'sells' => $sells,
            'customers' => $customers,
            'team' => $team,
        ]);

        $this->layout = 'print';
    }

    public function unlock($idmaster = null)
    {
        if($this->request->is('post') && $idmaster != null){
            $this->decalculate_sells($idmaster);
            if($this->Sell->updateAll(
                array('Sell.status' => 0),
                array('Sell.idmaster' => $idmaster)
            ))
            $this->Session->setFlash('Data transaksi berhasil unlock', 'customflash', array('class' => 'success'));
            else
                $this->Session->setFlash('Data transaksi gagal di unlock', 'customflash', array('class' => 'danger'));
            $this->redirect(array('action' => 'index'));
        }

        $this->redirect(array('action' => 'index'));
    }

    public function locking($idmaster = null)
    {
        if($this->request->is('post') && $idmaster != null){
            $this->recalculate_sells($idmaster);
            if($this->Sell->updateAll(
                array('Sell.status' => 1),
                array('Sell.idmaster' => $idmaster)
            ))
            $this->Session->setFlash('Data transaksi berhasil di-lock', 'customflash', array('class' => 'success'));
            else
                $this->Session->setFlash('Data transaksi gagal di lock', 'customflash', array('class' => 'danger'));
            $this->redirect(array('action' => 'index'));
        }

        $this->redirect(array('action' => 'index'));
    }

    public function lock($idmaster, $dates){
        $user = $this->Auth->user();

        if($this->request->is('post')){
            if($idmaster && $dates){
                $this->recalculate_sells($idmaster, $dates);
                if($this->Sell->updateAll(
                    array('Sell.status' => "1"),
                    array('Sell.idmaster' => $idmaster)
                ))
                $this->Session->setFlash('Data transaksi berhasil dilock', 'customflash', array('class' => 'success'));
                else
                    $this->Session->setFlash('Data transaksi gagal dilock', 'customflash', array('class' => 'danger'));
            }
        }

        if($user['role'] == 'pegawai')
            $this->redirect(array('action' => 'index'));
        else
            $this->redirect(array('action' => 'index'));

    }

    private function recalculate_sells($idmaster, $dates = null)
    {
        $datas = $this->Sell->find('all', array('conditions' => array('Sell.idmaster' => $idmaster),
            'recursive' => 0, 'order' => 'Sell.idcustomer',
            'fields' => array('DISTINCT Sell.id', 'Sell.idtim', 'Sell.idcustomer', 'Sell.jmlbeli', 'Sell.jmlkembali',
            'Sell.jmlpinjam', 'Sell.bayar','Sell.hutang', 'Sell.totalharga', 'Sell.totalhargagalon', 'Customer.id', 'Customer.hutang', 'Customer.galonterpinjam'))
        );

        if($datas[0]['Sell']['jmlbeli'] == 0) {
            $harga_galon = $this->Sell->Good->find('first', array('conditions' => array('Good.namabarang LIKE' => '%galon%'), 'fields' => array('Good.hargajual')));
            $harga_galon = $harga_galon['Good']['hargajual'];
        }
        else
            $harga_galon = $datas[0]['Sell']['totalhargagalon'] / $datas[0]['Sell']['jmlbeli'];

        $galonkosong = 0;
        $finish = 0;
        $galonterjual = 0;
        $array_id_customer = array();
        $total_harga = $total_terbayarkan = $total_hutang = 0;
        foreach($datas as $data){
            $galonkosong += $data['Sell']['jmlbeli'] + $data['Sell']['jmlkembali'] - $data['Sell']['jmlpinjam'];
            $finish += $data['Sell']['jmlbeli'];
            $galonterjual += $data['Sell']['bayar'];

            $array_id_customer[] = $data['Sell']['idcustomer'];

            $total_harga += $data['Sell']['totalhargagalon'];
            $total_terbayarkan += $data['Sell']['bayar'];
            $total_hutang += $data['Sell']['hutang'];
        }
        $galonterjual = doubleval($galonterjual / $harga_galon);
        $teams = $this->Sell->Team->find('all', array(
            'conditions' => array('Team.idtim' => $datas[0]['Sell']['idtim']),
            'recursive' => -1,
        ));
        $master = $this->Sell->Master->find('first', array(
            'conditions' => array('Master.id' => $idmaster),
            'recursive' => -1,
        ));

        $teams[0]['Team']['jmlgalon'] = $teams[0]['Team']['jmlgalon'] + $galonkosong - $master['Master']['start'] + ($master['Master']['start'] - $finish);
        $teams[1]['Team']['jmlgalon'] = $teams[0]['Team']['jmlgalon'];

        $array_update_customer_hutang_galonterpinjam = array();
        foreach($datas as $data){
            $array_update_customer_hutang_galonterpinjam[]['Customer'] = array(
                'id' => $data['Customer']['id'],
                'galonterpinjam' => $data['Customer']['galonterpinjam'] + $data['Sell']['jmlpinjam'] - $data['Sell']['jmlkembali'],
                'hutang' => $data['Customer']['hutang'] - $data['Sell']['bayar'] + $data['Sell']['totalhargagalon'],
                'transaksiterakhir' => ''
            );
        }

        if($galonkosong < 0){
            $galonkosong = 0;
        }
        $master = array('Master' => array(
            'id' => $idmaster,
            'galon_sales' => $teams[0]['Team']['jmlgalon'],
            'harga_galon' => $harga_galon,
            'galonkosong' => $galonkosong,
            'galonterjual' => $galonterjual,
            'finish' => ($master['Master']['start']-$finish),
            'total_harga' => $total_harga,
            'total_terbayarkan' => $total_terbayarkan,
            'total_hutang' => $total_hutang,
            'status' => 1,
        ));

        if($dates)
            $master['Master']['date'] = $dates;

        $this->Sell->Team->updateAll(
            array('Team.jmlgalon' => $teams[0]['Team']['jmlgalon']),
            array('Team.idtim' => $teams[0]['Team']['idtim'])
        );
        $this->Sell->Master->save($master);
        $this->Sell->Customer->saveAll($array_update_customer_hutang_galonterpinjam);
    }

    private function decalculate_sells($idmaster)
    {
        $datas = $this->Sell->find('all', array('conditions' => array('Sell.idmaster' => $idmaster),
            'recursive' => 0, 'order' => 'Sell.idcustomer',
            'fields' => array('DISTINCT Sell.id', 'Sell.idtim', 'Sell.idcustomer', 'Sell.jmlbeli', 'Sell.jmlkembali',
            'Sell.jmlpinjam', 'Sell.bayar','Sell.hutang', 'Sell.totalharga', 'Sell.totalhargagalon', 'Customer.id', 'Customer.hutang', 'Customer.galonterpinjam'))
        );
        $master = $this->Sell->Master->find('first', array(
            'conditions' => array('Master.id' => $idmaster),
            'recursive' => -1,
        ));

        $teams = $this->Sell->Team->find('all', array(
            'conditions' => array('Team.idtim' => $datas[0]['Sell']['idtim']),
            'recursive' => -1,
        ));
        $teams[0]['Team']['jmlgalon'] = $teams[0]['Team']['jmlgalon'] - $master['Master']['galonkosong'] + $master['Master']['start'] - $master['Master']['finish'];
        $teams[1]['Team']['jmlgalon'] = $teams[0]['Team']['jmlgalon'];

        $array_update_customer_hutang_galonterpinjam = array();
        foreach($datas as $data){
            $array_update_customer_hutang_galonterpinjam[]['Customer'] = array(
                'id' => $data['Customer']['id'],
                'galonterpinjam' => $data['Customer']['galonterpinjam'] - $data['Sell']['jmlpinjam'] + $data['Sell']['jmlkembali'],
                'hutang' => $data['Customer']['hutang'] + $data['Sell']['bayar'] - $data['Sell']['totalhargagalon'],
                'transaksiterakhir' => ''
            );
        }

        $master = array('Master' => array(
            'id' => $idmaster,
            'galon_sales' => 0,
            'harga_galon' => 0,
            'galonkosong' => 0,
            'galonterjual' => 0,
            'finish' => 0,
            'total_harga' => 0,
            'total_terbayarkan' => 0,
            'total_hutang' => 0,
            'status' => 0,
        ));
        $this->Sell->Team->updateAll(
            array('Team.jmlgalon' => $teams[0]['Team']['jmlgalon']),
            array('Team.idtim' => $teams[0]['Team']['idtim'])
        );
        $this->Sell->Master->save($master);
        $this->Sell->Customer->saveAll($array_update_customer_hutang_galonterpinjam);
    }

    private function calculate_today_sells($idtim, $dates, $start) {
        if($idtim && $dates && $start){
            $datas = $this->Sell->find('all', array('conditions' => array('DATE(Sell.date)' => $dates, 'Sell.idtim' => $idtim),
                'recursive' => 0, 'order' => 'Sell.idcustomer',
                'fields' => array('DISTINCT Sell.id', 'Sell.idcustomer', 'Sell.jmlbeli', 'Sell.jmlkembali',
                'Sell.jmlpinjam', 'Sell.bayar','Sell.hutang', 'Sell.totalhargagalon', 'Customer.id', 'Customer.hutang', 'Customer.galonterpinjam'))
            );

            $good_price = $this->Sell->Good->find('first', array('conditions' => array('Good.namabarang LIKE' => '%galon%'), 'fields' => array('Good.hargajual')));

            $galonkosong = 0;
            $finish = 0;
            $galonterjual = 0;
            $array_id_customer = array();

            $total_harga = $total_terbayarkan = $total_hutang = 0;
            foreach($datas as $data){
                $galonkosong += $data['Sell']['jmlbeli'] + $data['Sell']['jmlkembali'] - $data['Sell']['jmlpinjam'];
                $finish += $data['Sell']['jmlbeli'];
                $galonterjual += $data['Sell']['bayar'];

                $array_id_customer[] = $data['Sell']['idcustomer'];

                $total_harga += $data['Sell']['totalhargagalon'];
                $total_terbayarkan += $data['Sell']['bayar'];
                $total_hutang += $data['Sell']['hutang'];
            }
            $galonterjual = doubleval($galonterjual / $good_price['Good']['hargajual']);
            $galontim = $this->Sell->Team->get_a_team_jmlgalon($idtim);
            $galontim = $galontim[0];

            $galontim['Team']['jmlgalon'] = $galontim['Team']['jmlgalon'] + $galonkosong - $start + ($start - $finish);
            if($galonkosong < 0){
                $galonkosong = 0;
            }


            $master = array('Master' => array(
                'idtim' => $idtim,
                'date' => $dates,
                'galon_sales' => $galontim['Team']['jmlgalon'],
                'harga_galon' => $good_price['Good']['hargajual'],
                'galonkosong' => $galonkosong,
                'galonterjual' => $galonterjual,
                'finish' => ($start-$finish),
                'total_harga' => $total_harga,
                'total_terbayarkan' => $total_terbayarkan,
                'total_hutang' => $total_hutang,
                'status' => 1,
            ));

            $idmaster = $this->Sell->Master->find('first', array('conditions' => array('Master.date' => $dates, 'Master.idtim' => $idtim), 'recursive' => -1));

            $master['Master']['id'] = $idmaster['Master']['id'];
            $this->Sell->Team->save($galontim);
            $this->Sell->Master->save($master);
            // $this->Sell->save_finish_master($idtim, $dates, $galonkosong, $galonterjual, ($start-$finish));

            $array_update_customer_hutang_galonterpinjam = array();
            foreach($datas as $data){
                $array_update_customer_hutang_galonterpinjam[]['Customer'] = array(
                    'id' => $data['Customer']['id'],
                    'galonterpinjam' => $data['Customer']['galonterpinjam'] + $data['Sell']['jmlpinjam'] - $data['Sell']['jmlkembali'],
                    'hutang' => $data['Sell']['hutang'],
                    'transaksiterakhir' => ''
                );
            }

            $this->Sell->Customer->saveAll($array_update_customer_hutang_galonterpinjam);
        }
    }

    private function get_list_all_team()
    {
        return (new TeamRepository())->getListAllTeam();
    }

    public function filter()
    {
        $this->set('title', 'Galon - Riwayat Transaksi');

        $user = $this->Auth->user();
        $list_team = $this->get_list_all_team();

        if($user['role'] == 'pegawai')
            $idtim = $user['Team']['idtim'];

        $filter = $this->params['url']['filter'];
        $text = $this->params['url']['text'];

        if($filter == null || $text == null) {
            $this->redirect(['action' => 'index']);
        }

        $filterConditions = $this->getModelCondition($filter, $text);
        $params = array('limit' => 20,
            'recursive' => -1,
            'order' => 'Master.date DESC',
            'conditions' => [$filterConditions]
        );
        if(isset($idtim))
            $params['conditions'] = array('idtim' => $idtim);

        $this->paginate = $params;

        $this->loadModel('Master');
        $masters = $this->paginate('Master');

        $this->set(compact('masters'));
        $this->set(compact('list_team'));
        $this->set(array('filters' => $this->getFilters()));
        return $this->render('index');
    }

    private function getFilters()
    {
        return (new FilterFactory('Sell'))->produce();
    }

    private function getModelCondition($filter, $text)
    {
        $params = [
            'filter' => $filter,
            'text' => $text
        ];
        return (new ModelConditionFactory('Sell', $params))->produce();
    }
}

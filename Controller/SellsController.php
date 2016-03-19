<?php

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

    public function graph($startdate = null, $enddate = null, $idtim = null){
        $this->set('title', 'Galon - Daftar Transaksi');

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
        // $masters = $this->Sell->Master->find('all', array(
        //     'recursive' => -1
        // ));
        // $maxs = $this->Sell->Master->find('all', array(
        //     'recursive' => -1,
        //     'group' => array('Master.date'),
        //     'fields' => array('MAX(Master.galonterjual)', 'Master.date', 'Master.idtim'),
        // ));

        $data = array();
        foreach($masters as $master) {
            if(!isset($data[$master['Master']['date']]))
                $data[$master['Master']['date']] = 0;
            $data[$master['Master']['date']] += $master['Master']['galonterjual'];
        }
        $this->set(compact('data'));
    }

    // accessed by user and admin
    public function index($idtim = null){

        $this->set('title', 'Galon - Riwayat Transaksi');

        $user = $this->Auth->user();

        $list_teams = $this->Sell->Customer->PairTeamCustomer->Team->find('all', array('order' => 'idtim','conditions' => array('Team.status' => 1), 'recursive' => 0));
        $list_team = $this->to_list_team($list_teams);

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
    }

    public function detail($idmaster) {
        $this->set('title', 'Galon - Detail Transaksi Tim');
        $master = $this->get_master_data($idmaster);
        $team = $this->get_team_data($master['Master']['idtim'], $master['Master']['date']);

        $this->set(compact('master'));
        $this->set(compact('team'));
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

    // accessed by user
    public function dashboard($id = null){
        $this->set('title', 'Galon - Transaksi Tim');
        $this->check_admin_access('dashboard');

        if($id){
            if($this->Sell->Team->find('first', array('conditions' => array('Team.idtim' => $id, 'Team.status' => 1), 'recursive' => -1))){

                $teams = $this->Sell->Team->User->get_user_with_idtim_and_attend($id);
                $team_galon = $this->Sell->Team->find('first', array('conditions' => array('Team.idtim' => $id), 'recursive' => -1));
                $good_price = $this->Sell->Good->find('first', array('conditions' => array('Good.namabarang LIKE' => '%galon%'), 'fields' => array('Good.hargajual')));
                $datas = $this->Sell->find('all', array('conditions' => array('Sell.status' => 0, 'Sell.idtim' => $id), 'recursive' => 0, 'order' => 'Sell.idcustomer', 'fields' => array('DISTINCT Sell.id','Sell.idtim', 'Sell.jmlbeli','Sell.jmlpinjam', 'Sell.jmlkembali', 'Sell.bayar', 'Sell.hutang','Sell.status', 'Customer.id', 'Customer.kdpelanggan', 'Customer.namapelanggan', 'Customer.alamat', 'Customer.galonterpinjam', 'Customer.hutang', 'Customer.transaksiterakhir')));
                $customers = array();
                if(!$datas)
                    $customers = $this->Sell->Team->PairTeamCustomer->Customer->get_customer_in_team($id);

                // $master = $this->Sell->cek_lock($id);
                $master = $this->Sell->Master->find('all', array(
                    'conditions' => array('Master.idtim' => $id, 'Master.status' => 0),
                    'recursive' => 0,
                ));

                if(!$master)
                    $customers = $this->Sell->Team->PairTeamCustomer->Customer->get_customer_in_team($id);

                $this->set(compact('master'));
                $this->set(compact('datas'));
                $this->set(compact('id'));
                $this->set(compact('teams'));
                $this->set(compact('good_price'));
                $this->set(compact('team_galon'));
                $this->set(compact('customers'));
            } else
                $this->redirect(array('action' => 'index'));
        } else
            $this->redirect(array('action' => 'index'));
    }

    //accessed by user
    public function add($idtim = null){
        $this->check_admin_access('add');
        $this->set('title', 'Galon - Tambah Transaksi');

        if ($this->request->is('post')) {
            $user = $this->Auth->user();
            $master = $this->Sell->Master->find('all',
                array('conditions' => array('Master.idtim' => $user['Team']['idtim'], 'Master.status' => 0)
            ));

            $idmaster = $master[0]['Master']['id'];
            $this->request->data['Sell']['kodepenjualan'] = $this->generate_kodepenjualan();
            $this->request->data['Sell']['date'] = '';
            $this->request->data['Sell']['idmaster'] = $idmaster;

            $this->Sell->create();
            if ($this->Sell->save($this->request->data)) {
                $this->Session->setFlash('Data transaksi berhasil ditambahkan', 'customflash', array('class' => 'success'));
                $this->redirect(array('action' => 'dashboard', $user['Team']['idtim']));
            } else {
                $this->Session->setFlash('Data transaksi gagal ditambahkan, silahkan dicoba lagi', 'customflash', array('class' => 'warning'));
            }

        } else {

            if($idtim){
                // $idgood = $this->Sell->Good->find('first', array('conditions' => array('Good.kdbarang' => 'CG0004'), 'fields' => array('Good.id', 'Good.hargajual'), 'recursive' => -1));
                $goods = $this->Sell->Good->find('all', array('fields' => array('Good.id', 'Good.hargajual', 'Good.namabarang'), 'recursive' => -1, 'conditions' => array('Good.status' => 1)));

                // $customers = $this->Sell->Team->PairTeamCustomer->find('list', array('fields' => array('PairTeamCustomer.id','PairTeamCustomer.idcustomer'), 'conditions' => array('PairTeamCustomer.idtim' => $idtim)));
                $customers = $this->array_to_list($this->Sell->list_customer_to_team($idtim), 'Customer', 'id', 'namapelanggan');
                $prices = $this->array_to_list($goods, 'Good', 'id', 'hargajual');
                $reverse_prices = $this->array_to_list($goods, 'Good', 'hargajual', 'id');
                $goods = $this->array_to_list($goods, 'Good', 'id', 'namabarang');


                $this->set(compact('prices'));
                $this->set(compact('reverse_prices'));
                $this->set(compact('goods'));
                $this->set(compact('customers'));
                //$this->set(compact('idgood'));
                $this->set(compact('idtim'));
            } else
                $this->redirect(array('action' => 'index'));

        }
    }

    public function get_hutang_customer($idcustomer = null){
        $this->autoRender = false;
        if($this->request->is('get')){
            $user_data = $this->Sell->Customer->find('first', array('conditions' => array('Customer.id' => $idcustomer), 'fields'=>array('Customer.id', 'Customer.namapelanggan', 'Customer.hutang')));
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

    private function generate_kodepenjualan(){
        $kodepenjualan = 'KJ';

        $missing_code = str_pad($this->get_missing_number(), 10, '0', STR_PAD_LEFT);

        return $kodepenjualan.$missing_code;
    }

    private function get_missing_number(){
        $datas = $this->Sell->find('all', array('fields' => 'DISTINCT(SUBSTRING(kodepenjualan, 3)) AS kodepenjualan', 'order' => 'kodepenjualan'));

        $missing_code = 1;
        if(count($datas) == 0)
            return $missing_code;
        for ($i = 0; $i < number_format($datas[count($datas)-1][0]['kodepenjualan']); $i++ ) {
            if(number_format($datas[$i][0]['kodepenjualan']) != $missing_code)
                return $missing_code;

            $missing_code++;
        }

        return $missing_code;
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
            if($this->Sell->Team->find('first', array('conditions' => array('Team.idtim' => $idtim, 'Team.status' => 1), 'recursive' => -1))){

                $teams = $this->Sell->Team->User->get_user_with_idtim_and_attend($idtim);
                $good_price = $this->Sell->Good->find('first', array('conditions' => array('Good.namabarang LIKE' => '%galon%'), 'fields' => array('Good.hargajual')));

                $customers = $this->Sell->Team->PairTeamCustomer->Customer->get_customer_in_team($idtim);

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
        } else
            $this->redirect(array('action' => 'index'));
    }

    public function printfull($idmaster = null) {
        $this->set('title', 'Galon - Cetak Data');
        $master = $this->get_master_data($idmaster);
        $team = $this->get_team_data($master['Master']['idtim'], $master['Master']['date']);

        $this->set(compact('master'));
        $this->set(compact('team'));

        $this->layout = 'print';
    }

    private function get_master_data($idmaster = null) {
        $master = $this->Sell->Master->find('first', array(
            'conditions' => array('Master.id' => $idmaster),
            'recursive' => 2,
        ));
        return $master;
    }

    private function get_team_data($idtim = null, $date) {
        $team = $this->Sell->Team->find('all', array(
            'conditions' => array('Team.idtim' => $idtim),
            'recursive' => 0,
        ));
        foreach($team as &$user) {
            $attendance = $this->Sell->Team->User->Attendance->find('first', array(
                'conditions' => array('idpegawai' => $user['User']['id'], 'tanggal' => substr($date, 0, 10)),
                'recursive' => 0,
            ));
            $user['User']['kehadiran'] =  $attendance['Attendance']['kehadiran'];
        }
        return $team;
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
}

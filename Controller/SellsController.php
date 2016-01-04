<?php 

class SellsController extends AppController {
	public $layout = 'layout';
	//accessed by admin

	private function check_user_access($location){
		$user = $this->Auth->user();
		if($user['role'] == 'pegawai' && $user['Team']['idtim'])
			$this->redirect(array('action' => $location, $user['Team']['idtim']));
		else if($user['role'] == 'pegawai')
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
	}

	private function check_admin_access($location){
		$user = $this->Auth->user();
		if($user['role'] == 'admin'){
			if($location == 'dashboard' || $location == 'add'){
				$this->redirect(array('action' => 'index'));	
			}
		}
	}

	public function index(){
		$this->check_user_access('dashboard');
		
		$this->set('title', 'Galon - Daftar Transaksi');

		$this->paginate = array(
            'limit' => 25,
        );
        $sells = $this->paginate('Sell');
		$this->set(compact('sells'));
	}

	// accessed by user and admin
	public function history(){
		$this->set('title', 'Galon - Riwayat Transaksi');

		$user = $this->Auth->user();
		$idtim = 0;
		$tanggal = 0;
		$hari = 0;
		$master = array();
		$history_team = array();
		$list_team = array();
		$good_price = array();

		if($user['role'] == 'pegawai')
			$idtim = $user['Team']['idtim'];

		if($this->request->is('post')){
			$tanggal = $this->request->data['tanggal'];
			$hari = $this->request->data['harikunjungan'];
			$idtim = $this->request->data['idtim'];
			
			if($tanggal && $idtim && !$hari){
				$master = $this->Sell->get_master_based_date_idtim($idtim, $tanggal);
			}

			$condition_array = array();
			if($tanggal)
				$condition_array["DATE(Sell.date)"] = $tanggal;
			if($idtim)
				$condition_array['Sell.idtim'] = $idtim;
			if($hari)
				$condition_array['Customer.harikunjungan'] = $hari;

			$this->paginate = array(
                    'limit' => 20,
                    // 'conditions' => array('Sell.idtim' => $idtim, 'DATE(Sell.date)' => $tanggal),
                    'conditions' => $condition_array,
                    'fields' => array('DISTINCT Sell.id','Sell.idtim', 'Sell.idgood','Sell.jmlbeli','Sell.jmlpinjam', 'Sell.jmlkembali', 'Sell.bayar', 'Sell.hutang','Sell.status','Sell.date', 'Customer.id', 'Customer.kdpelanggan', 'Customer.namapelanggan', 'Customer.alamat', 'Customer.galonterpinjam', 'Customer.hutang', 'Customer.harikunjungan'),
                    'order' => array('Sell.date' => 'DESC' )
                );
            $datas = $this->paginate('Sell');

            $history_team = $this->Sell->Team->User->find('all', array(
            	// 'conditions' => array('Team.idtim' => $idtim)), 
            	'fields' => array('Team.id', 'Team.idtim', 'User.firstname', 'User.lastname', 'Attendance.idpegawai', 'Attendance.tanggal', 'Attendance.kehadiran'), 
            	'joins' => array(
			        'table' => 'teams',
			        'alias' => 'Team',
			        'type' => 'LEFT',
			        'conditions' => array(
			            'Team.idpegawai = User.id'
			        )
			    ),
			    array(
			        'table' => 'attendances',
			        'alias' => 'Attendance',
			        'type' => 'LEFT',
			        'conditions' => array(
			            'Attendance.idpegawai = User.id',
			            'Attendance.tanggal' => $tanggal
			        )
			    )
            	));
            $good_price = $this->Sell->Good->find('first', array('conditions' => array('Good.id' => $datas[0]['Sell']['idgood']), 'fields' => array('Good.id', 'Good.hargajual')));
		} else {
			$this->paginate = array(
                    'limit' => 20,
                    'order' => array('Sell.date' => 'DESC' ),
                    // 'recursive' => 0
                );
            $datas = $this->paginate('Sell');
		}
		
		if($user['role'] != 'pegawai'){
			$list_teams = $this->Sell->Customer->PairTeamCustomer->Team->find('all', array('order' => 'idtim','conditions' => array('Team.status' => 1), 'recursive' => 0));
	        $list_team = $this->to_list_team($list_teams);
        }

        $this->set(compact('list_team'));
        $this->set(compact('datas'));
        $this->set(compact('tanggal'));
        $this->set(compact('hari'));
        $this->set(compact('master'));
        $this->set(compact('history_team'));
        $this->set(compact('good_price'));
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
				$good_price = $this->Sell->Good->find('first', array('conditions' => array('Good.kdbarang' => 'CG0004'), 'fields' => array('Good.hargajual')));
				// $customers = $this->Sell->Team->PairTeamCustomer->Customer->get_customer_in_team($id);
				// $jml_galon = $this->Sell->Team->find('first', array('conditions' => array('Team.idtim' => $id), 'fields' => array('Team.jmlgalon'), 'recursive' => -1));
				$datas = $this->Sell->find('all', array('conditions' => array('DATE(Sell.date)' => date('Y-m-d'), 'Sell.idtim' => $id), 'recursive' => 0, 'order' => 'Sell.idcustomer', 'fields' => array('DISTINCT Sell.id','Sell.idtim', 'Sell.jmlbeli','Sell.jmlpinjam', 'Sell.jmlkembali', 'Sell.bayar', 'Sell.hutang','Sell.status', 'Customer.id', 'Customer.kdpelanggan', 'Customer.namapelanggan', 'Customer.alamat', 'Customer.galonterpinjam', 'Customer.hutang', 'Customer.transaksiterakhir')));
				$customers = array();
				if(!$datas)
					$customers = $this->Sell->Team->PairTeamCustomer->Customer->get_customer_in_team($id);
				
				$master = $this->Sell->cek_lock($id);


				$this->set(compact('master'));
				$this->set(compact('datas'));
				$this->set(compact('id'));
				$this->set(compact('teams'));
				$this->set(compact('good_price'));
				$this->set(compact('team_galon'));
				// $this->set(compact('jml_galon'));
				$this->set(compact('customers'));
			} else 
				$this->redirect(array('action' => 'index'));
		} else 
			$this->redirect(array('action' => 'index'));
	}

	//accessed by user
	public function add($idtim = null){
		$this->set('title', 'Galon - Tambah Daftar Barang');
		$this->check_admin_access('add');

		if ($this->request->is('post')) {
			
			$this->request->data['Sell']['kodepenjualan'] = $this->generate_kodepenjualan();
			$this->request->data['Sell']['date'] = '';
			
			
            $this->Sell->create();
            if ($this->Sell->save($this->request->data)) {
                $this->Session->setFlash('Data transaksi berhasil ditambahkan', 'customflash', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
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

		}

		$this->redirect(array('action' => 'index'));
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
		$this->check_user_access('edit');

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
            $this->Sell->id = $id;
            debug($this->request->data);
            if ($this->Sell->save($this->request->data)) {
                $this->Session->setFlash('Data transaksi berhasil diubah', 'customflash', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Gagal mengedit data transaksi', 'customflash', array('class' => 'warning'));
            }
        }
 		
        if (!$this->request->data) {
            $this->request->data = $sell;
        }
        
        $idgood = $this->Sell->Good->find('first', array('conditions' => array('Good.kdbarang' => 'CG0004'), 'fields' => array('Good.id', 'Good.hargajual'), 'recursive' => -1));
				
        $customers = $this->Sell->Customer->find('first', array('conditions' => array('Customer.id' => $sell['Sell']['idcustomer'])));
        $customer[$customers['Customer']['id']] = $customers['Customer']['namapelanggan'];
        unset($customers);
		$this->set(compact('customer'));
		$this->set(compact('idgood'));
        $this->set(compact('sell'));
	}
	
	//accessed by admin and user
	public function delete($id){
		if($this->request->is('post')){
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
				$good_price = $this->Sell->Good->find('first', array('conditions' => array('Good.kdbarang' => 'CG0004'), 'fields' => array('Good.hargajual')));
				
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

	
	public function lock($idtim, $dates, $start){
		$user = $this->Auth->user();
		
		if($this->request->is('post')){
			
			if($idtim && $dates){
				if($this->Sell->updateAll(
					array('Sell.status' => "1"),
					array('Sell.idtim' => $idtim, 'DATE(Sell.date)' => $dates)
				)) 
					$this->Session->setFlash('Data transaksi berhasil dilock', 'customflash', array('class' => 'success'));
				else 
					$this->Session->setFlash('Data transaksi gagal dilock', 'customflash', array('class' => 'danger'));

				$datas = $this->Sell->find('all', array('conditions' => array('DATE(Sell.date)' => $dates, 'Sell.idtim' => $idtim), 'recursive' => 0, 'order' => 'Sell.idcustomer', 'fields' => array('DISTINCT Sell.id', 'Sell.idcustomer', 'Sell.jmlbeli', 'Sell.jmlkembali', 'Sell.jmlpinjam', 'Sell.bayar','Sell.hutang','Customer.id', 'Customer.hutang', 'Customer.galonterpinjam')));
				$good_price = $this->Sell->Good->find('first', array('conditions' => array('Good.kdbarang' => 'CG0004'), 'fields' => array('Good.hargajual')));

				$galonkosong = 0;
				$finish = 0;
				$galonterjual = 0;
				$array_id_customer = array();
				foreach($datas as $data){
					$galonkosong += $data['Sell']['jmlbeli'] + $data['Sell']['jmlkembali'] - $data['Sell']['jmlpinjam'];
					$finish += $data['Sell']['jmlbeli'];
					$galonterjual += $data['Sell']['bayar'];

					$array_id_customer[] = $data['Sell']['idcustomer'];
				}
				$galonterjual = doubleval($galonterjual / $good_price['Good']['hargajual']);
				$galontim = $this->Sell->Team->get_a_team_jmlgalon($idtim);
				$galontim = $galontim[0];

				$galontim['Team']['jmlgalon'] = $galontim['Team']['jmlgalon'] + $galonkosong - $start + ($start - $finish);
				if($galonkosong < 0){
					$galonkosong = 0;
				}
				// debug($galontim);
				$this->Sell->Team->save($galontim);
				$this->Sell->save_finish_master($idtim, $dates, $galonkosong, $galonterjual, ($start-$finish));

				$array_update_customer_hutang_galonterpinjam = array();
				foreach($datas as $data){
					$array_update_customer_hutang_galonterpinjam[]['Customer'] = array(
						'id' => $data['Customer']['id'],
						'hutang' => $data['Customer']['hutang'] + $data['Sell']['hutang'],
						'galonterpinjam' => $data['Customer']['galonterpinjam'] + $data['Sell']['jmlpinjam'] - $data['Sell']['jmlkembali'],
						'transaksiterakhir' => ''
					);
				}
				// debug($array_update_customer_hutang_galonterpinjam);
				$this->Sell->Customer->saveAll($array_update_customer_hutang_galonterpinjam);
			}
		}
		
		if($user['role'] == 'pegawai')
			$this->redirect(array('action' => 'dashboard', $user['Team']['idtim']));
		else 
			$this->redirect(array('action' => 'index'));
		
	}
}
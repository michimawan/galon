<?php 

class GoodsController extends AppController {
	public $layout = 'layout';

	public function index(){
		$this->set('title', 'Galon - Daftar Barang');

		//$goods = $this->Good->find('all');
		$this->paginate = array(
            'limit' => 20,
            'order' => array('Good.kdbarang' => 'asc' ),
            'conditions' => array('NOT' => array('Good.status' => '0'))
        );
        $goods = $this->paginate('Good');
		$this->set(compact('goods'));
	}

	private function check_admin_access($location){
		$user = $this->Auth->user();
		if($user['role'] === 'pegawai'){
			if($location == 'add' || $location == 'edit' ||
				$location == 'index' || $location == 'delete'){
					$this->redirect(array('action' => 'index'));	
			}
		}
	}

	public function add(){
		$this->check_admin_access('add');
		$this->set('title', 'Galon - Tambah Daftar Barang');
		if ($this->request->is('post')) {
			
			$data = $this->request->data;
			
			$this->request->data['Good']['kdbarang'] = $this->generate_kodebarang();
            
            $this->Good->create();
            if ($this->Good->save($this->request->data)) {
                $this->Session->setFlash('Data barang berhasil ditambahkan', 'customflash', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Data barang gagal ditambahkan, silahkan dicoba lagi', 'customflash', array('class' => 'warning'));
            }
        }
	}

	private function generate_kodebarang(){
		$kdbarang = 'CG';

		$missing_code = str_pad($this->get_missing_number(), 4, '0', STR_PAD_LEFT);
		
		return $kdbarang.$missing_code;
	}

	private function get_missing_number(){
		$datas = $this->Good->find('all', array('fields' => 'SUBSTRING(kdbarang, 3) AS kdbarang', 'order' => 'kdbarang'));
		
		$missing_code = 1;
		if(count($datas) == 0)
			return $missing_code;
		
		for ($i = 0; $i < number_format($datas[count($datas)-1][0]['kdbarang']); $i++ ) {
			if(number_format($datas[$i][0]['kdbarang']) != $missing_code)
				return $missing_code;

			$missing_code++;
		}
		
		return $missing_code;
	}

	/*
	 * unused function
	 */
	public function add_stock($id = null){
		if($this->request->is('post') || $this->request->is('put')){
			if($this->request->data){
				$current_stock = $this->Good->find('first', array('conditions' => array('Good.id' => $this->request->data['Good']['id']), 'fields' => array('Good.stokbarang', 'Good.id')));
				$this->request->data['Good']['stokbarang'] = $current_stock['Good']['stokbarang'] + $this->request->data['Good']['stokbarang'];

				$this->Good->id = $this->request->data['Good']['id'];
				if($this->Good->save($this->request->data)){
					$this->Session->setFlash('Stok barang berhasil ditambahkan', 'customflash', array('class' => 'success'));	
				} else {
					$this->Session->setFlash('Stok barang tidak berhasil sd', 'customflash', array('class' => 'warning'));		
				}
			} else 
				$this->Session->setFlash('Stok barang tidak berhasil ditambah', 'customflash', array('class' => 'warning'));
		}
		$this->redirect(array('action' => 'index'));
	}

	public function edit($id = null){
		$this->check_admin_access('edit');
		$this->set('title','Galon - Edit Data Barang');
		if($this->request->is('post') || $this->request->is('put'))
		{
			$this->Good->id = $this->request->data['Good']['id'];
			if($this->Good->save($this->request->data)){
				$this->Session->setFlash('Data barang berhasil diubah', 'customflash', array('class' => 'success'));
            } else {
            	$this->Session->setFlash('Data barang gagal diubah', 'customflash', array('class' => 'warning'));
            }
            $this->redirect(array('action'=>'index'));
		} else {
			if($id){
				try{
					$data = $this->Good->read(null, $id);
					$this->request->data = $data;
				} catch (NotFoundException $ex) {
					$this->Session->setFlash('Data barang tidak ditemukan', 'customflash', array('class' => 'warning'));
					$this->redirect(array('action'=>'index'));
				}
			} else {
				$this->Session->setFlash('Data barang tidak ditemukan', 'customflash', array('class' => 'warning'));
				$this->redirect(array('action'=>'index'));
			}

		}
	}
	
	public function delete($id){
		$this->check_admin_access('delete');
		if($this->request->is('post')){
			if (!$id) {
                $this->Session->setFlash('Tidak ada data barang yang dipilih', 'customflash', array('class' => 'warning'));
            }
             
            $this->Good->id = $id;
            if($this->Good->exists()){
                if ($this->Good->saveField('status', 0)) {
                    $this->Session->setFlash('Data barang berhasil dihapus', 'customflash', array('class' => 'success'));
                    $this->redirect(array('action' => 'index'));
                }
            }
            $this->Session->setFlash('Data barang gagal dihapus', 'customflash', array('class' => 'warning'));
        }
		$this->redirect(array('action'=>'index'));
	}
}
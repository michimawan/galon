<?php 
App::uses('File', 'Utility');

class UsersController extends AppController {
    public $layout = "layout";
    
	public $paginate = array(
        'limit' => 20,
        'conditions' => array('status' => '1'),
        'order' => array('User.username' => 'asc' ) 
    );
     
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('login'); 
    }

    private function check_user_access($location){
        $user = $this->Auth->user();
        if($user['role'] == 'pegawai')
            if($location == 'add' || $location == 'index' || $location == 'delete' || $location == 'activate')
                $this->redirect(array('action' => 'view', $user['id']));
    }

    public function login() {
        $this->set('title','Galon - Login Pengguna');
        //if already logged-in, redirect
        if($this->Session->check('Auth.User')){
            $this->redirect(array('action' => 'index'));    
        }
         
        // if we get the post information, try to authenticate
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                $user = $this->Auth->user();
                if($user['role'] == 'pegawai'){
                    $userme = $this->User->find('all', array('conditions' => array('User.id' => $user['id'], 'Team.status' => 1), 'fields' => array('Team.idtim', 'Team.id'), 'recursive' => 0));
                    if($userme)
                        $userme = $userme[0]['Team'];
                    $this->Session->write('Auth.User.Team', $userme);
                }
                $this->Session->setFlash('Selamat datang, '. $this->Auth->user('username'), 'customflash', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Username atau password salah', 'customflash', array('class' => 'danger'));
            }
        } 
    }
 
    public function logout() {
        $this->redirect($this->Auth->logout());
    }
 
    public function index() {
        $this->set('title','Galon - Data Pengguna');
        $this->check_user_access('index');

        $this->paginate = array(
            'limit' => 20,
            'order' => array('User.username' => 'asc' ),
            'recursive' => -1
        );
        $users = $this->paginate('User');
        $this->set(compact('users'));
    } 
 
    public function add() {
        $this->set('title','Galon - Tambah Data Pengguna');
        $this->check_user_access('add');

        if ($this->request->is('post')) {
                 
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash('Pengguna baru berhasil dibuat', 'customflash', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Pengguna baru gagal dibuat, silahkan dicoba lagi', 'customflash', array('class' => 'warning'));
            }   
        }
    }
 
    public function edit($id = null) {
        $this->set('title','Galon - Ubah Data Pengguna');
        $user = $this->Auth->user();
        
        if($id != $user['id'] && $user['role'] == 'pegawai')
            $this->redirect(array('action' => 'index'));

        if (!$id) {
            $this->Session->setFlash('Gagal memilih pengguna yang akan diedit', 'customflash', array('class' => 'danger'));
            $this->redirect(array('action'=>'index'));
        }
 
        $user = $this->User->find('first', array('recursive' => -1, 'conditions' => array('User.id' => $id)));
        if (!$user) {
            $this->Session->setFlash('Data pengguna yang diedit berbeda', 'customflash', array('class' => 'danger'));
            $this->redirect(array('action'=>'index'));
        }
 
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->User->id = $id;
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash('Data pengguna berhasil diubah', 'customflash', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Gagal mengedit data pengguna', 'customflash', array('class' => 'warning'));
            }
        }
 
        if (!$this->request->data) {
            $this->request->data = $user;
        }
    }
 
    public function delete($id = null) {
        $this->check_user_access('delete');

        if (!$id) {
            $this->Session->setFlash('Tidak ada pengguna yang dipilih', 'customflash', array('class' => 'warning'));
            $this->redirect(array('action'=>'index'));
        }
         
        $this->User->id = $id;
        if (!$this->User->exists()) {
            $this->Session->setFlash('Tidak ada pengguna yang dipilih', 'customflash', array('class' => 'warning'));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->User->saveField('status', 0)) {
            $this->Session->setFlash('Pengguna dinon-aktifkan', 'customflash', array('class' => 'success'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Pengguna gagal dinon-aktifkan', 'customflash', array('class' => 'warning'));
        $this->redirect(array('action' => 'index'));
    }
     
    public function activate($id = null) {
        $this->check_user_access('activate');

        if (!$id) {
            $this->Session->setFlash('Tidak ada pengguna yang dipilih', 'customflash', array('class' => 'warning'));
            $this->redirect(array('action'=>'index'));
        }
         
        $this->User->id = $id;
        if (!$this->User->exists()) {
            $this->Session->setFlash('Tidak ada pengguna yang dipilih', 'customflash', array('class' => 'warning'));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->User->saveField('status', 1)) {
            $this->Session->setFlash('Pengguna di-aktifkan', 'customflash', array('class' => 'success'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('Pengguna gagal di-aktifkan', 'customflash', array('class' => 'warning'));
        $this->redirect(array('action' => 'index'));
    }

    public function view($id = null){
        $this->set('title','Galon - Detail Data Pengguna');

        if($id){
            $user = $this->User->find('first', array(
                'fields' => array('User.username', 'User.firstname', 'User.lastname', 'User.nohp', 'Team.idtim'),
                'conditions' => array('User.id' => $id),
                'recursive' => 0
                )
            );

            $partner = $this->User->find('first', array(
                'fields' => array('User.username', 'User.firstname', 'User.lastname', 'User.nohp', 'Team.idtim'),
                'conditions' => array('Team.idtim' => $user['Team']['idtim'], 'AND' => array('NOT' => array('User.id' => $user['User']['id']))),
                'recursive' => 0
                )
            );

            $this->set(compact('user'));
            $this->set(compact('partner'));
        } else {   
            $this->Session->setFlash('Tidak dapat melihat detail user', 'customflash', array('class' => 'warning'));
            $this->redirect(array('action' => 'index'));
        }
    }

    public function autocompletes($query = null){
        $this->autoRender = false;
        if($this->request->is('ajax')){
            $term = $this->params['url']['term'];

            $names = $this->User->get_names($term);
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
    
} 
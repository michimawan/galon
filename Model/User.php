<?php
App::uses('AuthComponent', 'Controller/Component');

class User extends AppModel {
	
	public $hasMany = array(
		'Attendance' => array(
			'className' => 'Attendance',
			'foreignKey' => 'idpegawai'
		)
	);

	public $hasOne = array(
		'Team' => array(
			'className' => 'Team',
			'foreignKey' => 'idpegawai'
		)
	);
	
	public $validate = array(
        'username' => array(
            'nonEmpty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Username harus diisi.',
				'allowEmpty' => false
            ),
			'between' => array( 
				'rule' => array('between', 5, 15), 
				'required' => true, 
				'message' => 'Panjang karakter Username harus antara 5 - 15.'
			),
			'unique' => array(
				'rule'    => array('isUniqueUsername'),
				'message' => 'Username sudah ada.'
			),
			'alphaNumericDashUnderscore' => array(
				'rule'    => array('alphaNumericDashUnderscore'),
				'message' => 'Username hanya dapat berupa huruf, angka, underscores, dan tanpa spasi'
			)
        ),
        'firstname' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Nama depan harus diisi.'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Password harus diisi.'
            ),
			'min_length' => array(
				'rule' => array('minLength', '6'),  
				'message' => 'Panjang karakter Password minimal 6 karakter.'
			)
        ),
		'password_confirm' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Silakan ketik ulang Password.'
            ),
			 'equaltofield' => array(
				'rule' => array('equaltofield','password'),
				'message' => 'Kedua input Password harus sesuai.'
			)
        ),
		
        'role' => array(
            'valid' => array(
                'rule' => array('inList', array('admin', 'pegawai')),
                'message' => 'Pilih Peran Petugas yang tersedia.',
            )
        ),
		
		'password_update' => array(
			'min_length' => array(
				'rule' => array('minLength', '6'),   
				'message' => 'Panjang karakter Password minimal 6 karakter.',
				'allowEmpty' => true,
				'required' => false
			)
        ),
		'password_confirm_update' => array(
			 'equaltofield' => array(
				'rule' => array('equaltofield','password_update'),
				'message' => 'Kedua input Password harus sesuai.',
				'required' => false
			)
        )
		
    );
	
	/**
	 * Before isUniqueUsername
	 * @param array $options
	 * @return boolean
	 */
	function isUniqueUsername($check) {

		$username = $this->find(
			'first',
			array(
				'fields' => array(
					'User.id',
					'User.username'
				),
				'conditions' => array(
					'User.username' => $check['username']
				)
			)
		);

		if(!empty($username)) {
			if($this->data[$this->alias]['id'] == $username['User']['id']){
				return true; 
			} else {
				return false; 
			}
		} else {
			return true; 
		}
    }
	
	public function alphaNumericDashUnderscore($check) {
        // $data array is passed using the form field name as the key
        // have to extract the value to make the function generic
        $value = array_values($check);
        $value = $value[0];

        //return preg_match('/^[a-zA-Z0-9_ \-]*$/', $value);
        return preg_match('/^[a-zA-Z0-9_]+$/', $value);
    }
	
	public function equaltofield($check,$otherfield) 
    { 
        //get name of field 
        $fname = ''; 
        foreach ($check as $key => $value){ 
            $fname = $key; 
            break; 
        } 
        return $this->data[$this->name][$otherfield] === $this->data[$this->name][$fname]; 
    } 

	/**
	 * Before Save
	 * @param array $options
	 * @return boolean
	 */
	 public function beforeSave($options = array()) {
		// hash our password
		if (isset($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		
		// if we get a new password, hash it
		if (isset($this->data[$this->alias]['password_update'])) {
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password_update']);
		}
	
		// fallback to our parent
		return parent::beforeSave($options);
	}

	public function get_names_not_have_team($query){
		$q = "SELECT `id`, `username`, `firstname`, `lastname` FROM `users` AS `User`
			WHERE (firstname LIKE '%$query%' OR username LIKE '%$query%' )  AND `id` NOT IN (SELECT `idpegawai` FROM `teams` WHERE status = 1) LIMIT 5";
		
		return $this->query($q);
	}

	public function get_names($query){
		$q = "SELECT `id`, `username`, `firstname`, `lastname` FROM `users` AS `User`
			WHERE (firstname LIKE '%$query%' OR username LIKE '%$query%' ) AND status = 1 LIMIT 5";
		
		return $this->query($q);
	}

	public function getUserIdByName($query){
		$q = "SELECT id FROM `users` As `User` WHERE CONCAT(firstname, ' ', lastname) LIKE '".$query."' LIMIT 1";
		
		return $this->query($q);
	}

	public function getAllUserIDThatStillNotAttend(){
		$q = "SELECT `id` FROM `users` WHERE id NOT IN (SELECT `idpegawai` FROM `attendances` WHERE tanggal LIKE curdate())";

		return $this->query($q);
	}

	public function get_user_with_idtim_and_attend($idtim){
		$q = "SELECT `User`.`id`, `User`.`username`, `User`.`firstname`, `User`.`lastname` 
			FROM `users` AS `User` 
			WHERE `User`.`id` IN 
				(SELECT `teams`.`idpegawai` FROM `teams` WHERE `teams`.`idtim` = '$idtim' AND `teams`.`status` = 1) 
			AND `User`.`id` IN 
				(SELECT `attendances`.`idpegawai` FROM `attendances` WHERE `attendances`.`kehadiran` = 1 AND `attendances`.`tanggal` LIKE SUBSTRING(NOW(), 1, 10))";


		return $this->query($q);
	}

	public function get_user_not_have_team(){
        $q = "SELECT * FROM `users` AS `User` 
        	WHERE `User`.`id` NOT IN (
        		SELECT `Team`.`idpegawai` FROM `teams` AS `Team` WHERE `Team`.`status` = 1) 
			AND `User`.`status` = 1 ORDER BY `User`.`id` ASC";

        return $this->query($q);
    }
}	

?>

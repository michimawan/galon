<?php

class Good extends AppModel {
	public $validate = array(
        'namabarang' => array(
            'nonEmpty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Nama Barang harus diisi.',
                'allowEmpty' => false
            )
            
        ),
        'hargabeli' => array(
            'nonEmpty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Harga beli harus diisi.',
                'allowEmpty' => false
            )
            
        ),
        'hargajual' => array(
            'nonEmpty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Harga jual harus diisi.',
                'allowEmpty' => false
            )
        ),
        /*
        'stokbarang' => array(
            'nonEmpty' => array(
                'rule' => array('notEmpty'),
                'message' => 'Stok barang harus diisi.',
                'allowEmpty' => false
            )
        )
        */
    );
}
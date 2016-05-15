<?php
App::uses('Model', 'Model');

class SellRepository
{
    private $uses = ['Sell'];

    public function __construct()
    {
        foreach($this->uses as $use)
            App::import('Model', $use);

        $this->sellModel = new $this->uses[0];
    }

    public function save($data = [])
    {
        $data['Sell']['kodepenjualan'] = $this->generate_kodepenjualan();
        $data['Sell']['date'] = '';
        if($data['Sell']['idcustomer'] == '') {
            return false;
        }

        $sellDataSource = $this->sellModel->getDataSource();
        try {
            $sellDataSource->begin();
            $this->sellModel->save($data);

            $sellDataSource->commit();
            return true;
        } catch(Exception $e) {
            $sellDataSource->rollback();
            return false;
        }
    }

    public function getUnlockedSellTransactionFor($idtim = null)
    {
        if(!$idtim)
            return [];

        $this->sellModel->unbindModel(['belongsTo' => ['Good', 'Team', 'Master']]);
        return $this->sellModel->find('all', [
            'conditions' => ['Sell.status' => 0, 'Sell.idtim' => $idtim],
            'recursive' => 0,
            'order' => 'Sell.idcustomer',
            'fields' => [
                'DISTINCT Sell.id','Sell.idtim', 'Sell.jmlbeli', 'Sell.jmlpinjam',
                'Sell.jmlkembali', 'Sell.bayar', 'Sell.hutang','Sell.status',
                'Customer.id', 'Customer.kdpelanggan', 'Customer.namapelanggan',
                'Customer.alamat', 'Customer.galonterpinjam', 'Customer.hutang',
                'Customer.transaksiterakhir']
        ]);
    }

    private function generate_kodepenjualan(){
        $kodepenjualan = 'KJ';

        $missing_code = str_pad($this->get_missing_number(), 10, '0', STR_PAD_LEFT);

        return $kodepenjualan.$missing_code;
    }

    private function get_missing_number(){
        $datas = $this->sellModel->find('all', array('fields' => 'DISTINCT(SUBSTRING(kodepenjualan, 3)) AS kodepenjualan', 'order' => 'kodepenjualan'));

        $missing_code = 1;
        if(count($datas) == 0)
            return $missing_code;

        $maxKdPenjualan = (int) $datas[count($datas)-1][0]['kodepenjualan'];
        for ($i = 0; $i < $maxKdPenjualan; $i++ ) {
            $currentNumber = (int) $datas[$i][0]['kodepenjualan'];
            if($currentNumber != $missing_code)
                return $missing_code;

            $missing_code++;
        }

        return $missing_code;
    }

    public function getModel()
    {
        return $this->sellModel;
    }
}

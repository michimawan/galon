<?php

class FilterFactory
{
    public function  __construct($model)
    {
        $this->model = $model;
    }

    public function produce()
    {
        switch($this->model) {
            case 'User':
                return $this->getUserFilter();
            case 'Attendance':
                return $this->getUserFilter();
            case 'Customer':
                return $this->getCustomerFilter();
            case 'Sell':
                return $this->getSellFilter();
            default:
                return [];
        }
    }

    private function getUserFilter()
    {
        return [
            'all' => 'Show All',
            'username' => 'Username',
            'firstname' => 'Nama Depan',
        ];
    }

    private function getCustomerFilter()
    {
        return [
            'all' => 'Show All',
            'kdpelanggan' => 'Kode Pelanggan',
            'namapelanggan' => 'Nama Pelanggan',
            'alamat' => 'Alamat',
            'harikunjungan' => 'Hari Kunjungan',
        ];
    }

    private function getSellFilter()
    {
        return [
            'all' => 'Show All',
            'date' => 'Tanggal Transaksi',
        ];
    }
}

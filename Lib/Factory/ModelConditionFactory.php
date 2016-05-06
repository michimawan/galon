<?php

class ModelConditionFactory
{
    public function  __construct($model, $params)
    {
        $this->model = $model;
        $this->filter = $params['filter'];
        $this->text = $params['text'];
    }

    public function produce()
    {
        switch($this->model) {
            case 'User':
                return $this->getUserCondition();
            case 'Customer':
                return $this->getCustomerCondition();
            case 'Attendance':
                return $this->getUserCondition();
            case 'Sell':
                return $this->getSellCondition();
            default:
                return [];
        }
    }

    private function getUserCondition()
    {
        switch($this->filter) {
        case 'username':
            return ['User.username LIKE' => '%' . $this->text . '%'];
        case 'firstname':
            return ['User.firstname LIKE' => '%' . $this->text . '%'];
        default:
            return [];
        }
    }

    private function getCustomerCondition()
    {
        switch($this->filter) {
        case 'kdpelanggan':
            return ['Customer.kdpelanggan LIKE' => '%' . $this->text . '%'];
        case 'namapelanggan':
            return ['Customer.namapelanggan LIKE' => '%' . $this->text . '%'];
        case 'alamat':
            return ['Customer.alamat LIKE' => '%' . $this->text . '%'];
        case 'harikunjungan':
            return ['Customer.harikunjungan LIKE' => '%' . $this->text . '%'];
        default:
            return [];
        }
    }

    private function getSellCondition()
    {
        switch($this->filter) {
        case 'date':
            return ['Master.date LIKE' => '%' . $this->text . '%'];
        default:
            return [];
        }
    }
}

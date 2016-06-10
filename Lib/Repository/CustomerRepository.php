<?php
App::uses('Model', 'Model');

class CustomerRepository
{
    private $uses = ['Customer'];

    public function __construct()
    {
        foreach($this->uses as $use)
            App::import('Model', $use);

        $this->customerModel = new $this->uses[0];
    }

    public function getCustomerInTeamNotDoingTransaction($idtim, $datas)
    {
        $customerWithTransaction = $this->getArrayListOfCustomerWithTransaction($datas);
        $allCustomers = $this->customerModel->get_customer_in_team($idtim);
        foreach($allCustomers as &$customer) {
            if(in_array($customer['Customer']['id'], $customerWithTransaction)) {
                unset($customer);
            }
        }

        return $allCustomers;
    }

    private function getArrayListOfCustomerWithTransaction($datas = [])
    {
        $customers = [];
        foreach($datas as $data) {
            $customers[] = $data['Customer']['id'];
        }

        return $customers;
    }

    public function getModel()
    {
        return $this->customerModel;
    }
}

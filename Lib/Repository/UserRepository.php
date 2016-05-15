<?php
App::uses('Model', 'Model');

class UserRepository
{
    private $uses = ['User'];

    public function __construct()
    {
        foreach($this->uses as $use)
            App::import('Model', $use);

        $this->userModel = new $this->uses[0];
    }

    public function getModel()
    {
        return $this->userModel;
    }
}

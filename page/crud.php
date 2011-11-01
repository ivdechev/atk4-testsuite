<?php
class page_crud extends Page {
    function init(){
        parent::init();
        //phpinfo();
        $this->api->db=$this->api->add('DB');
        $model = $this->add('CRUD')->setModel('MyModel');
    }

}
class Model_Client extends Model_Table {
    public $entity_code='client';
    function init(){
        parent::init();

        $this->addField('name');

        $this->addField('email');

    }
}
class Model_MyModel extends Model_Table {
    public $entity_code='user';
    function init(){
        parent::init();

        $this->addField('name')->defaultValue('John');

        $this->addExpression('age')->calculated(function(){
            return 123;
        });

        $this->addReference('client_id')->setModel('Client');
    }
}

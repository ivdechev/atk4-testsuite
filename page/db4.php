<?php

class page_db4 extends Page_Tester {
    public $db;
    function init(){
        $this->db=$this->add('DB');
        $this->add('View_Info')->set('Testing basic rendering functionality');
        parent::init();
    }
    function prepare(){
        return array($this->db->dsql());
    }
    function formatResult(&$row,$key,$result){
        //parent::formatResult($row,$key,$result);
        $x=parent::formatResult($row,$key,$result);
        if($this->input[0]->params)$row[$key.'_para']=print_r($this->input[0]->params,true);
        return array($x,$this->input[0]->params);
    }
    function test_where1($t){
        return $t->where('id',1);
    }
    function test_render2($t){
        return $t->template('hello [table]')->table('user');
    }

}

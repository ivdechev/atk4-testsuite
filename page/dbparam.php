<?php

class page_dbparam extends Page_Tester {
    public $db;
    function init(){
        $this->db=$this->add('DB');
        $this->add('View_Info')->set('Subqueries parametric arguments must not clash with the main query. To avoid
                $dsql->dsql() should be used or unique paramBase()');
        parent::init();
    }
    function runTests(){
        $this->grid->addColumn('text','Test_para');
        return parent::runTests();
    }
    function formatResult(&$row,$key,$result){
        parent::formatResult($row,$key,$result);
        $row[$key.'_para']=print_r($this->input[0]->params,true);
    }
    function prepare(){
        return array($this->db->dsql()->table('foo'));
    }
    function test_param($t){
        return $t->where('id',2);
    }
    function test_param2($t){
        return $t->where('id',2)->where('a',8);
    }
    function test_param_sqlinjection($t){
        return $t->where('id',"'); show tables; --");
    }
    function test_param_subquery($t){
        try{
            return $t->where('id',"1")->field($t->dsql()->where('id',3),'boo');
        }catch(Exception_DB $e){
            return 'Clashed. BAD';
        }
    }
    function test_param_unlinked($t){
        try{
            $t2=$this->db->dsql()->paramBase('b')->where('id',3);
            return $t->where('id',"1")->field($t2,'boo');
        }catch(Exception_DB $e){
            return 'Clashed. BAD';
        }
    }
    function test_param_clash($t){
        try{
            $t2=$this->db->dsql()->where('id',3);
            return $t->where('id',"1")->field($t2,'boo');
        }catch(Exception_DB $e){
            return 'Clashed. Good';
        }
    }
}


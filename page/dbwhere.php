<?php

class page_dbwhere extends Page_Tester {
    public $db;
    function init(){
        $this->db=$this->add('DB');
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
    function test_create($t){
        $this->db->query('drop temporary table if exists foo');
        $this->db->query('create temporary table if not exists foo (id int not null primary key auto_increment, name varchar(255), a int, b
                    int, c int)');
    }
    function test_insert($t){
        $this->db->query('insert into foo (name,a,b,c) values ("John", 1,2,3),("Peter", 2,4,7),("Ian", 2,4,7),'.
                '("Steve", 2,4,7),("Robert", 2,4,7),("Lucas", 2,4,7),("Jane", 2,4,7),("Dot", 2,4,7)');
    }
    function test_where1($t){
        return $t->where('id',2);
    }
    function test_where2a($t){
        return $t->where('a','>',1);
    }
    function test_where2_compat($t){
        return $t->where('a>',1);
    }
    function test_where2b($t){
        return $t->where('a<',1);
    }
    function test_where2c($t){
        return $t->where('a>=',1);
    }
    function test_where2d($t){
        return $t->where('a<=',1);
    }
    function test_where2e($t){
        return $t->where('a!=',1);
    }
    function test_where2f($t){
        return $t->where('a<>',1);
    }
    function test_where2g($t){
        return $t->where('a in',1);
    }
    function test_where2h($t){
        return $t->where('a not in',1);
    }
    function test_where2i($t){
        return $t->where('a like',1);
    }
    function test_where2j($t){
        return $t->where('a not like',1);
    }
    function test_where2k($t){
        return $t->where('anot like',1);
    }
    function test_where3_or($t){
        return $t->where(array(array('a',1),array('b',2)));
    }
    function test_where4_in($t){
        return $t->where('a',array(1,2));
    }
    function test_where5_1($t){
        return $t->where($t->expr('length(name)'),'>',2);
    }
    function test_where5_2($t){
        return $t->where($t->expr('length(name)'),array(2,4));
    }
    function test_where5_3($t){
        return $t->where(
                $t->dsql()
                ->table('foo')
                ->field('name')
                ->where('id',1)
                ,array(2,4)
                );
    }
    function test_where6($t){
        return $t->where('id',$t->dsql()->table('foo')->field('name')->where('id',1));
    }
    function test_where7($t){
        // params should be merged
        return $t
            ->where('id',$t->dsql()->table('foo')->field('name')->where('id',2))
            ->having('id>',1);
    }
    function test_clone_param($t){
        // params should be empty
        $x=clone $t;
        $x->where('foo','bar');
        return $t;
    }
}


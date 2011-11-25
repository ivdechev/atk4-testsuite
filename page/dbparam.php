<?php

class page_dbparam extends Page_Tester {
    public $db;
    public $proper_responses=array(
        "Test_param"=>array (
  0 => 'select  * from `foo`  where (`id` = :a)    ',
  1 => 
  array (
    ':a' => 2,
  ),
),
        "Test_param2"=>array (
  0 => 'select  * from `foo`  where (`id` = :a) and (`a` = :a_2)    ',
  1 => 
  array (
    ':a' => 2,
    ':a_2' => 8,
  ),
),
        "Test_param_sqlinjection"=>array (
  0 => 'select  * from `foo`  where (`id` = :a)    ',
  1 => 
  array (
    ':a' => '\'); show tables; --',
  ),
),
        "Test_param_subquery"=>array (
  0 => 'select  (select  *    where (`id` = :a_2)    ) as `boo` from `foo`  where (`id` = :a)    ',
  1 => 
  array (
    ':a' => '1',
    ':a_2' => 3,
  ),
),
        "Test_param_unlinked"=>array (
  0 => 'select  (select  *    where (`id` = :b)    ) as `boo` from `foo`  where (`id` = :a)    ',
  1 => 
  array (
    ':a' => '1',
    ':b' => 3,
  ),
),
        "Test_param_clash"=>array (
  0 => 'Clashed. Good',
  1 => 
  array (
    ':a' => '1',
  ),
),
        "Test_param_doublewhere"=>array (
  0 => 'select  * from `foo`  where (`id` = :a_2)    ',
  1 => 
  array (
    ':a_2' => 3,
  ),
),
        "Test_param_doublewhere2"=>array (
  0 => 'select  *    where (`id` = :a_2)    Array
(
    [:a_2] => 2
)
',
  1 => 
  array (
    ':a' => 'must not be there',
  ),
),
        "Test_param_doublewhere3"=>array (
  0 => 'select  * from `foo`  where (`id` = :a) and (`id` = :a_2)    Array
(
    [:a] => 3
    [:a_2] => 5
)
',
  1 => 
  array (
    ':a' => 3,
  ),
),
        "Test_param_doublewhere4"=>array (
  0 => 'select  * from `foo`  where (`id` = :a)    ',
  1 => 
  array (
    ':a' => 3,
    ':a_2' => 4,
  ),
),
        "Test_param_doublewhere5"=>array (
  0 => 'update `foo` set id=:a_2 where (`id` = :a)',
  1 => 
  array (
    ':a' => 3,
    ':a_2' => 4,
  ),
)
    );

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
        //parent::formatResult($row,$key,$result);
        $x=parent::formatResult($row,$key,$result);
        $row[$key.'_para']=print_r($this->input[0]->params,true);
        return array($x,$this->input[0]->params);
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
    function test_param_doublewhere($t){
        $t2=$t->dsql()->where('id','must not be there');
        $t->where('id',3);
        return $t;
    }
    function test_param_doublewhere2($t){
        $t->where('id','must not be there');
        $t2=$t->dsql()->where('id',2);
        return $t2.print_r($t2->params,true);
    }
    function test_param_doublewhere3($t){
        $t->where('id',3);
        $t2=clone $t;
        $t2->where('id','5');
        return $t2.print_r($t2->params,true);
    }
    function test_param_doublewhere4($t){
        $t->where('id',3);
        $t->set('id',4);
        return $t;
    }
    function test_param_doublewhere5($t){
        $t->where('id',3);
        $t->set('id',4);
        return $t->update();
    }
}


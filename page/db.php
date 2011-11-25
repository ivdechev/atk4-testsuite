<?php

class page_db extends Page_Tester {
    public $db;
    public $proper_responses=array(
        "Test_create"=>'',
        "Test_raw_insert"=>'',
        "Test_raw_getOne"=>'John',
        "Test_raw_select"=>'John, Peter, Ian, Steve, Robert, Lucas, Jane, Dot',
        "Test_simple"=>'select  `foo` from `bar`      ',
        "Test_simple_tostring"=>'select  `foo` from `bar`      ',
        "Test_simple_dot"=>'select  `x`.`foo`.`bar` from `bar`      ',
        "Test_multifields"=>'select  `a`, `b`, `c` from `bar`      ',
        "Test_multitable"=>'select  `foo`.`a`, `foo`.`b`, `foo`.`c`, `bar`.`x`, `bar`.`y` from `bar`,`baz`      ',
        "Test_selectall"=>'select  * from `bar`      ',
        "Test_select_opton1"=>'select SQL_CALC_FOUND_ROWS * from `foo`      ',
        "Test_select_calc_rows"=>'select SQL_CALC_FOUND_ROWS * from `foo`      limit 0, 5',
        "Test_select_calc_rows2"=>'8',
        "Test_select_calc_rows3"=>'8',
        "Test_row"=>'Array
(
    [id] => 2
    [0] => 2
    [name] => Peter
    [1] => Peter
    [a] => 2
    [2] => 2
    [b] => 4
    [3] => 4
    [c] => 7
    [4] => 7
)
',
        "Test_getAll"=>'Array
(
    [0] => Array
        (
            [id] => 1
            [name] => John
            [a] => 1
            [b] => 2
            [c] => 3
        )

    [1] => Array
        (
            [id] => 2
            [name] => Peter
            [a] => 2
            [b] => 4
            [c] => 7
        )

)
',
        "Test_ts"=>'select  * from `foo`      ',
        "Test_expr"=>'call foobar()',
        "Test_expr2"=>'select  (select 1) as `x1`, (3+3) as `x2`        ',
        "Test_expr3"=>'acceptance',
        "Test_expr4"=>'foo',
        "Test_expr5"=>'foo..bar'
    );
    function init(){
        $this->db=$this->add('DB');
        parent::init();
    }
    function runTests(){
        $this->grid->addColumn('text','Test_para');
        return parent::runTests();
    }
    function prepare(){
        return array($this->db->dsql());
    }
    function formatResult(&$row,$key,$result){
        //parent::formatResult($row,$key,$result);
        $x=parent::formatResult($row,$key,$result);
        $row[$key.'_para']=print_r($this->input[0]->params,true);
        return array($x,$this->input[0]->params);
    }
    function test_create($t){
        $this->db->query('drop temporary table if exists foo');
        $this->db->query('create temporary table if not exists foo (id int not null primary key auto_increment, name varchar(255), a int, b
                    int, c int)');
    }
    function test_raw_insert($t){
        $this->db->query('insert into foo (name,a,b,c) values ("John", 1,2,3),("Peter", 2,4,7),("Ian", 2,4,7),'.
                '("Steve", 2,4,7),("Robert", 2,4,7),("Lucas", 2,4,7),("Jane", 2,4,7),("Dot", 2,4,7)');
    }
    function test_raw_getOne($t){
        return $this->db->getOne('select name from foo');
    }
    function test_raw_select($t){
        $stmt=$this->db->query('select name from foo');
        $data=array();
        foreach($stmt as $row){
            $data[]=$row['name'];
        }
        return implode(', ',$data);
    }

    function test_simple($t){
        return $t->table('bar')->field('foo')->select();
    }
    function test_simple_tostring($t){
        return $t->table('bar')->field('foo');
    }
    function test_simple_dot($t){
        return $t->table('bar')->field('foo.bar','x')->select();
    }
    function test_multifields($t){
        return $t->table('bar')->field(array('a','b','c'))->select();
    }
    function test_multitable($t){
        return $t->table(array('bar','baz'))->field(array('a','b','c'),'foo')->field(array('x','y'),'bar')->select();
    }
    function test_selectall($t){
        return $t->table('bar')->select();
    }
    function test_select_opton1($t){
        return $t->table('foo')->option('SQL_CALC_FOUND_ROWS')->select();
    }
    function test_select_calc_rows($t){
        return $t->table('foo')->limit(5)->calc_found_rows()->select();
    }
    function test_select_calc_rows2($t){
        $data=$t->table('foo')->limit(5)->calc_found_rows()->do_getAll();
        return $t->foundRows();
    }
    function test_select_calc_rows3($t){
        $data=$t->table('foo')->limit(5)->do_getAll();// not using option, backward-compatible mode
        return $t->foundRows();
    }
    function test_row($t){
        return print_r($t->table('foo')->where('id',2)->fetch(),true);
    }
    function test_getAll($t){
        return print_r($t->table('foo')->where('id',array(1,2))->getAll(),true);
    }
    function test_ts($t){
        return $t->table('foo');
    }
    function test_expr($t){
        return $t->expr('call foobar()');
    }
    function test_expr2($t){
        return $t
            ->field($t->expr('select 1'),'x1')
            ->field($t->expr('3+3'),'x2');
    }
    function test_expr3($t){
        return $t->expr('show tables')->getOne();
    }
    function test_expr4($t){
        return $t->expr('select [args]')->args(array('foo'))->getOne();
    }
    function test_expr5($t){
        return implode(',',$t->expr('select concat_ws([args])')->args(array('..','foo','bar'))->getHash());
    }
    function test_update($t){
        return $t->table('foo')->where('id','1')->set('name','Silvia')->update();
    }
    function test_update2($t){
        $t->where('id','1')->set('name','Silvia')->do_update();
        return print_r($t->table('foo')->where('id',array(1,2))->getAll(),true);
    }
}

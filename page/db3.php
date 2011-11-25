<?php

class page_db3 extends Page_Tester {
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
    function test_render1($t){
        return $t->template('hello world');
        //->table('user')->render();
    }
    function test_render2($t){
        return $t->template('hello [table]')->table('user');
    }
    function test_expr($t){
        return $t->useExpr('hello world');
    }
    function test_recursive_render($t){
        return $t->template('hello [table]')->table($t->expr('1+1'));
    }
    function test_render3($t){
        return $t->template('hello [some_unknown_tag]');
    }
    function test_field1($t){
        return $t->template('select [field]')
            ->field('name');
    }
    function test_field2($t){
        return $t->template('select [field]')
            ->field('name,surname');
    }
    function test_field3($t){
        return $t->template('select [field]')
            ->field('name')->field('surname');
    }
    function test_field3a($t){
        return $t->template('select [field]')
            ->field('name','user')->field('postcode','address');
    }
    function test_field4($t){
        return $t->template('select [field]')
            ->field(array('address_name'=>'name','postcode'),'address')
            ->field(array('name','surname'),'user');
    }
    function test_field5($t){
        return $t->template('select [field]')
            ->field($t->expr('len(name)'),'name_length');
    }
    function test_field6($t){
        return $t->template('select [field]')
            ->field($t->expr('len(name)')); // missing alias
    }
    function test_field_subquery1($t){
        return $t->template('select [field]')
            ->field(
                $t->dsql()
                ->table('book')
                ->where('author_id',$t->getField('id'))
                ->field($t->expr('sum(pages)'),'pages'),
                    'total_pages');
    }

}

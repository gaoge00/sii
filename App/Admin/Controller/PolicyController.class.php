<?php
//经营方针
namespace Admin\Controller;
use Think\Controller;

class PolicyController extends CommonController {
	
	public function _initialize() {
		parent::_initialize();
		$this->opname="经营方针";
		$this->dbname = 'policy';
	}
	
	
    public function index(){
    	$list=M($this->dbname)->order('date desc,id desc')->select();
    	$this->assign('list',$list);
    	$this->display();
    }
    

    public function _befor_edit(){
    	
    	//var_dump("aaaaaaaaaaaaa");
    	
    	//$list=orgcateTree($pid=0,$level=0,$type=0);
    	//$this->assign('type',I('get.type'));
    	//$this->assign('list',$list);
    }
    
    
  
}
<?php
//职务管理
namespace Admin\Controller;
use Think\Controller;

class DepController extends CommonController {
	
	public function _initialize() {
		parent::_initialize();
		$this->opname="职务";
		$this->dbname = 'dep';
	}
	
	public function index(){
	
	     $list=D($this->dbname)->select();
	    $this->assign('list',$list);
	    $this->display();
	}
	
}
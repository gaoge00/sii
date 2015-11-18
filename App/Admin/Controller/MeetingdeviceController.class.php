<?php
//职务管理
namespace Admin\Controller;
use Think\Controller;

class MeetingdeviceController extends CommonController {
	
	public function _initialize() {
		parent::_initialize();
		$this->opname="会议设备";
		$this->dbname = 'Meetingdevice';
	}
	
	/*
    public function index(){
    	//echo date('y-m-d h:i:s',time());
    	$list=M($this->dbname)->select();
    	$this->assign('list',$list);
    	$this->display();
    }
    */
	
	
    public function _befor_edit(){
    	
    	//var_dump("aaaaaaaaaaaaa");
    	
    	//$list=orgcateTree($pid=0,$level=0,$type=0);
    	//$this->assign('type',I('get.type'));
    	//$this->assign('list',$list);
    }
    
    
    
    
  
}
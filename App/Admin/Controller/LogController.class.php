<?php
namespace Admin\Controller;
use Think\Controller;

Class LogController extends CommonController{

    public function _initialize() {
        parent::_initialize();
        $this->dbname = "Log";
        $this->opname="日志";
    }
	
   function _filter(&$map) {

        if(IS_POST&&isset($_REQUEST['time1']) && $_REQUEST['time1'] != ''&&isset($_REQUEST['time2']) && $_REQUEST['time2'] != ''){
        	$map['_logic'] = 'and';
        	$map['addtime'] =array(array('egt',I('time1')." 00:00:00"),array('elt',I('time2')." 23:59:59")) ;
		}
		
	}



  
  public function Del() {
  	$ids=$_REQUEST["delids"];
  	
    $list = M('log')->where("id in (" . $ids .")")->delete();
    
    
	$this->mtReturn(200,"清理【".$this->opname."】记录成功",'','',U('index'));
  }
}
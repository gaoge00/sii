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
        if(IS_POST)
        {
           $map['_logic'] = 'and';
           $map['addtime'] =array();
           
           if(isset($_REQUEST['time1']) && $_REQUEST['time1'] != '')
           {
               array_push($map['addtime'],array('egt',I('time1')." 00:00:00"));
           }
               
           if(isset($_REQUEST['time2']) && $_REQUEST['time2'] != '')
           {
               array_push($map['addtime'],array('elt',I('time2')." 23:59:59"));
    	   }

    	   if(count($map['addtime'])==0)
    	   {
    	       unset($map['addtime']);
    	   }
        }
	}



  
  public function Del() {
  	$ids=$_REQUEST["delids"];
  	
    $list = M('log')->where("id in (" . $ids .")")->delete();
    
    
	$this->mtReturn(200,"清理【".$this->opname."】记录成功",'','',U('index'));
  }
}
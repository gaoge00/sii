<?php
//职务管理
namespace Admin\Controller;
use Think\Controller;

class ImportantController extends CommonController {
	
	public function _initialize() {
		parent::_initialize();
		$this->opname="重要度";
		$this->dbname = 'important';
	}
	public function _befor_sort($order,$asc){
	    if(isset($order)&&$order!='')
	    {
	        
	    }
	    
	    
	}
    
  
}
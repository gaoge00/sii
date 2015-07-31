<?php
namespace Admin\Controller;
use Think\Controller;
use Common\ORG\Util\BuildTreeArray;

class TelnotebooksController extends CommonController {
   
    public function _initialize() {
        parent::_initialize();
        $this->dbname = "user";
        $this->opname="用户";
        $this->selname="uv_gettel";
    }
	
	
}

<?php
// 职务管理
namespace Index\Controller;

use Think\Controller;
use Common\ORG\Util\BuildTreeArray;

class OrgController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
        $this->dbname = 'org';
    }

    public function GetAllOrg()
    {
        // import("Home.Library.TreeLib.BuildTreeArray");
        // import('Common/ORG/Util/BuildTreeArray');
        $list = M($this->dbname)->select();
        
        // $bta = new \Common\ORG\Util\BuildTreeArray($list,'orgid','orgpid',0);
        $bta = new BuildTreeArray($list, 'orgid', 'orgpid', 0);
        
        $data = $bta->getTreeArray();
        echo json_encode($data);
        
        // $this->ajaxReturn(json_encode($data),'JSON');
    }

    /*
     * public function index(){
     * //echo date('y-m-d h:i:s',time());
     * $list=M($this->dbname)->select();
     * $this->assign('list',$list);
     * $this->display();
     * }
     */
    public function _befor_edit()
    {
        
        // var_dump("aaaaaaaaaaaaa");
        
        // $list=orgcateTree($pid=0,$level=0,$type=0);
        // $this->assign('type',I('get.type'));
        // $this->assign('list',$list);
    }
}
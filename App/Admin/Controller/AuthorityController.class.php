<?php
//验证

namespace Admin\Controller;
use Think\Controller;
use Common\ORG\Util\BuildTreeArray;


class AuthorityController extends CommonController {
	
	public function _initialize() {
		parent::_initialize();
		$this->opname="权限";
		$this->dbname = 'authority';
	}
	
	
// 	public function GetAllAuthority(){

// 		$list=M($this->dbname)->select();
// 		$bta = new  BuildTreeArray($list,'AuthorityID','AuthorityPid',0);
// 		$data = $bta->getTreeArray();
// 		echo json_encode($data);
//asdads
// 	}
	

	
    public function index(){
   
    	$list=M($this->dbname)->order("sort desc")->select();

    	if (method_exists($this, '_befor_index')) {
    		$this->_befor_index();
    	}
    	else{
    		$this->assign('list',$list);
    	}
    	
    	$this->display();
    }
    
    
    
    public function _after_edit()
    {
        if (IS_POST) {
            try {
                
                $id = $data[$this->dbname . 'ID'];
                $this->mtReturn(200, "编辑成功" . $id, $_REQUEST['navTabId'], true); // 写入日志
            } catch (Exception $e) {
                $infopublishkeys->rollback();
                $this->mtReturn(300, "编辑失败" . $e . $id, $_REQUEST['navTabId'], true); // 写入日志
            }
        }
    
    }
    
//     public function _befor_insert($data){
//     	$pid = I('AuthorityPid');
//     	if ($pid==0){
//     		$data['AuthorityLevel']=0;
//     	}else{
//     		$level=D($this->dbname)->where('AuthorityID='.$pid.'')->field('AuthorityLevel')->limit(1)->select();
//     		$level=$level[0]['AuthorityLevel']+1;
//     		$data['AuthorityLevel']=$level;
//     	}
//     	return $data;
//     }
    
    
    
    
    
  
}
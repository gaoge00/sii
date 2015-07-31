<?php
namespace Admin\Controller;
use Think\Controller;

Class AuthruleController extends CommonController{
	
	 public function _initialize() {
        parent::_initialize();
        $this->dbname = 'authrule';
        $this->opname = "权限管理"; 
        
        
        //123123213123123Test
    }
	
   public function index(){ 
    $list = D($this->dbname)->where('level=0')->order('sort')->select();
    $this->assign('list',$list);
    $this->display(); 
   }
  
   public function _befor_add(){
       
     //var_dump(cateTree($pid=0,$level=1,$this->dbname));
     $list=cateTree($pid=0,$level=0,'uv_authrule');
     $this->assign('list',$list);
  }
  
  public function _befor_insert($data){
	 $pid = I('pid');
	 if ($pid==0){
	 $data['level']=0;
	 }else{
	 $level=D($this->dbname)->where('id='.$pid.'')->field('level')->limit(1)->select();
     $level=$level[0]['level']+1;
     $data['level']=$level;
	 }
	 return $data;
  }
  
  
  public function _befor_edit(){
     $list=cateTree($pid=0,$level=0,'uv_authrule');
     $this->assign('list',$list);
  }
  
    public function _befor_update($data){
	 $pid = I('pid');
	 if ($pid==0){
	 $data['level']=0;
	 }else{
	 $level=D($this->dbname)->where('id='.$pid.'')->field('level')->limit(1)->select();
     $level=$level[0]['level']+1;
     $data['level']=$level;
	 }
	 return $data;
  }
  
  
  
  
}

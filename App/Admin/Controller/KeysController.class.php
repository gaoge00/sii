<?php
//关键字

namespace Admin\Controller;
use Think\Controller;
use Common\ORG\Util\BuildTreeArray;


class KeysController extends CommonController {
	
	public function _initialize() {
		parent::_initialize();
		$this->opname="关键字";
		$this->dbname = 'keys';
	}
	
	
	public function GetAllKeys(){

		$list=M($this->dbname)->select();
		
	
		$bta = new  BuildTreeArray($list,'id','pid',0);
		
		$data = $bta->getTreeArray();
		echo json_encode($data);

	}
	//下面是树形结构现实在表格中用要的方法
	//begin
	
	public function _befor_index(){
	
	    $list=cateTree($id=0,$level=0,$this->dbname);
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
	
	    $list=cateTree($pid=0,$level=0,$this->dbname);
	    
	    
	    $this->assign('type',I('get.type'));
	    $this->assign('list',$list);
	}
	
	public function _befor_add(){
	
	    $list=cateTree($id=0,$level=0,$this->dbname);
	    $this->assign('list',$list);
	}
	
	public function _befor_view(){
	
	    $list=cateTree($pid=0,$level=0,$this->dbname);
	    $this->assign('type',I('get.type'));
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
	    //var_dump($data);
	    return $data;
	}
	
	//无极排序递归删除子级的数据
	public function _after_del($refid,$model){

	    $arrID=D($this->dbname)->where('pid='.$refid.'')->field("id")->select();
	    $intcoun=count($arrID);
	    if($intcoun>0)
	    {
	        foreach ($arrID as $key=>$value)
	        {
	         $model->where('id = ' . $value['id']  )->delete();
	         //var_dump($model->getlastSql());
 	         $this->_after_del($value['id'],$model);
	        }
	    }
	    else 
	   {
	        return;
	   } 
	}
	//end
	
  
}

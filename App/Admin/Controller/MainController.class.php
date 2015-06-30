<?php
namespace Admin\Controller;
use Think\Controller;

class MainController extends Controller {
	
	
    public function Main(){
        $this->assign('name','thinkphp');
        $this->display();
    }
	
	public function Hello(){
        $Data = M('Data1');// 实例化Data数据模型
        $result = $Data->find(3);
        $this->assign('result',$result);
        $this->display();
    }
}
<?php
namespace Admin\Controller;
use Think\Controller;
use Common\ORG\Util\BuildTreeArray;

class IndexController extends Controller {
	
	public function _initialize(){
		
		$this->dbname = 'menu'; 
		
		$config = S('DB_CONFIG_DATA');
		if(!$config){
			$config =   api('Config/lists');
			S('DB_CONFIG_DATA',$config);
		}
		C($config);

	}
	
    public function index(){
    	
    	//得到最新的经营方针
    	$list=M("policy")
    	->field("text")
    	->limit(1)
    	->order("id desc")
    	->select();
        
    	//得到最新信息
    	$infolist=M("infopublish")
    	->field("id,title")
    	->limit(5)
    	//->where("status=1")
    	->order("startdt desc")
    	->select();
    	
    	//得到Menu
    	//select a.id,if(b.coun>0,'true','false') cc from sii_menu a
    	//left join (select pid ,count(1) coun from sii_menu group by pid) b
    	//on a.id=b.pid
    	if(!session('uid')){
    	    $menulist=M("menu")
    	    ->table("__MENU__ a")
    	    ->join("(select pid ,count(1) coun from __MENU__ group by pid) b on(a.id=b.pid)")
    	    ->field("a.*,if(b.coun>0,'true','false') cc")
    	    ->select();
    	}
    	else{
    	    $menulist=M("menu")
    	    ->table("__MENU__ a")
    	    ->join("(select pid ,count(1) coun from __MENU__ group by pid) b on(a.id=b.pid)")
    	    ->field("a.*,if(b.coun>0,'true','false') cc")
    	    ->select();
    	}

    	
    	//$menubta = new  BuildTreeArray($menulist,'id','pid',0);
    	//$menudata = $menubta->getTreeArray();
    	
    	//$menupids=array_column($menulist,'pid');
        
    	//var_dump($menupids);
    	//die();
    	//$this->assign('menupidlist',$menupids);
    	$this->assign('menulist',$menulist);
    	$this->assign('infolist',$infolist);
    	$this->assign('Policy',html_out($list[0]["text"]));
        $this->display();
    }
    
    //得到Menu]
    public function GetMenu(){
         
        //权限
        $menu=M($this->dbname);
    
        if (! in_array(session('uid'), C('ADMINISTRATOR'))) {
            //getChildLst(31) 系统应用的ID  不能修改
            if(session('uid') == ''){
                $list=M($this->dbname)->where("FIND_IN_SET(id,getChildLst(31))")->order("sort asc")->select();
            }
            else{
                $list=M($this->dbname)->where("")->order("sort asc")->select();
            }
            
    
        }
        else{
            //如果管理员权限可以访问所有的
            $list=M($this->dbname)->where("")->order("sort asc")->select();
        }
    
        //echo $menu->getLastSql();
    
    
        $bta = new  BuildTreeArray($list,'id','pid',0);
    
        $data = $bta->getTreeArray();
    
        echo json_encode($data);
    
    }
    
    

    
}

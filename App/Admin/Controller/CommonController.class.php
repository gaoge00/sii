<?php
namespace Admin\Controller;
use Think\Controller;

Class CommonController extends Controller{


	public function _initialize(){
		
		$this->opname = "";	//操作名用于记录日志
		
        $this->_dbname = CONTROLLER_NAME; //添加插入用Model
        
        $this->_selname = CONTROLLER_NAME; //查询用Model

        //var_dump("SessionID".session('uid'));
        //var_dump($_COOKIE['uid']);
        //$_COOKIE['login_action_tabid']
		if(!session('uid')||!isset($_COOKIE['uid'])||$_COOKIE['uid']=''){
			//redirect(U('Public/login'));
		    cookie("login_action_tabid",null);
		    $tabid = CONTROLLER_NAME.'/'.ACTION_NAME;
		    cookie("login_action_tabid",strtolower($tabid));
		    $result = array();
		    $result['statusCode']=301;
		    $result['message']="请先登录";
		    $result['divid']='';
		    $result['closeCurrent']='false';
		    
		    header("Content-Type:text/html; charset=utf-8");
		    exit(json_encode($result));
            //$this->mtReturn(301,"",$_REQUEST['navTabId'],false);
		}
        //
        
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
        	$config =   api('Config/lists');
        	S('DB_CONFIG_DATA',$config);
        }
        C($config);
        
		$name=MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
		//$name=strtolower($name);
		//如果方法名是Ajax开头的就部进行验证 用于获取JSON数据
		if(substr(strtolower(ACTION_NAME), 0,4) !='ajax' ){
		    //var_dump("Ajax的Action进不来:".ACTION_NAME."&&&&&&".substr(ACTION_NAME, 0,4) !='Ajax');
		    if(!authcheck(strtolower($name),session('uid'))){
		        //$this->error(''.session('username').'很抱歉,此项操作您没有权限！');
		        $this->mtReturn(300,''.session('username').'很抱歉,此项操作您没有权限！',$_REQUEST['navTabId'],true);
		    }
		}
 
	}
	
	
	protected function mtReturnUpload($status,$info,$navTabId="",$closeCurrent=true) {
	    $udata['update_time']=time();
	    $Rs=M("user")->save($udata);
	    $dat['username'] = session('uid');
	    $dat['content'] = mb_strcut($info, 0, 100, 'utf-8');
	    $dat['os']=$_SERVER['HTTP_USER_AGENT'];
	    $dat['url'] = U();
	    $dat['addtime'] = date("Y-m-d H:i:s",time());
	    $dat['ip'] = get_client_ip();
	    M("log")->add($dat);
	    //回调
	    $result = array();
	    $result['statusCode'] = $status;
	    $result['message'] = $info;
	    $caltype=$_REQUEST ['caltype'];
	    if(!isset($caltype)||$caltype=="")
	    {
	        $caltype="index";
	    }
	     
	    //$result['tabid'] = strtolower($navTabId).'/index';
	    $result['tabid'] = strtolower($navTabId).'/'.$caltype;
	    $result['forward'] = '';
	    $result['forwardConfirm']='';
	    $result['closeCurrent'] =$closeCurrent;
	
	    if (empty($type)){
	        $type = C('DEFAULT_AJAX_RETURN');
	    }
	    if (strtoupper($type) == 'JSON') {
	        // 返回JSON数据格式到客户端 包含状态信息
	        exit(json_encode($result));
	
	    } elseif (strtoupper($type) == 'XML') {
	        // 返回xml格式数据
	        header("Content-Type:text/xml; charset=utf-8");
	        exit(xml_encode($result));
	    } elseif (strtoupper($type) == 'EVAL') {
	        // 返回可执行的js脚本
	        header("Content-Type:text/html; charset=utf-8");
	        exit($data);
	    } else {
	        // TODO 增加其它格式
	    }
	}
	
	
	
	protected function mtReturn($status,$info,$navTabId="",$closeCurrent=true) {
	    //var_dump("111111111111");
		//写入日志
	    //$udata['id']=session('uid');
        $udata['update_time']=time();
        $Rs=M("user")->save($udata);
        $dat['username'] = session('uid');
        $dat['content'] = $info;
		$dat['os']=$_SERVER['HTTP_USER_AGENT'];
        $dat['url'] = U();
        $dat['addtime'] = date("Y-m-d H:i:s",time());
        $dat['ip'] = get_client_ip();
        M("log")->add($dat);
        
        
	    //回调
	    $result = array();
        $result['statusCode'] = $status; 
        $result['message'] = $info;
        //var_dump($_REQUEST ['caltype']);
        $caltype=$_REQUEST ['caltype'];
        if(!isset($caltype)||$caltype=="")
        {
            $caltype="index";
        }
       
        //$result['tabid'] = strtolower($navTabId).'/index';
        $result['tabid'] = strtolower($navTabId).'/'.$caltype;
        //var_dump(strtolower($navTabId).'/'.$caltype);
        $result['forward'] = '';
		$result['forwardConfirm']='';
        $result['closeCurrent'] =$closeCurrent;
        
        //$this->dwzajaxReturn($result,"JSON");
        if (empty($type)){
        	$type = C('DEFAULT_AJAX_RETURN');
        	//var_dump($type);
        }
        if (strtoupper($type) == 'JSON') {
            // 返回JSON数据格式到客户端 包含状态信息
            header("Content-Type:application/json; charset=utf-8");
            //echo(json_encode($result));
            exit(json_encode($result));
            //dwzajaxReturn($data);
            
        } elseif (strtoupper($type) == 'XML') {
            // 返回xml格式数据
            header("Content-Type:text/xml; charset=utf-8");
            exit(xml_encode($result));
        } elseif (strtoupper($type) == 'EVAL') {
            // 返回可执行的js脚本
            header("Content-Type:text/html; charset=utf-8");
            exit($data);
        } else {
            // TODO 增加其它格式
        }
	}

	
	 /**
     * 列表页面
     */
	protected function _list($model, $map, $asc = false) {
	   
	    
		//排序字段 默认为主键名
		if (isset($_REQUEST ['orderField'])) {
			$order = $_REQUEST ['orderField'];
		}
		else{
		  
		    if( method_exists($this,'_befor_sort')){
		       
		        $this->_befor_sort($order,$asc);
		    }
		}
		//var_dump($map);
		if($order=='') {
			$order = $model->getPk();

		}
			
		//排序方式默认按照倒序排列
		//接受 sort参数 0 表示倒序 非0都 表示正序
		if (isset($_REQUEST ['orderDirection'])) {
			$sort = $_REQUEST ['orderDirection'];
		}
		
		
		if($sort=='') {
			$sort = $asc ? 'asc' : 'desc';

		}

		if (isset($_REQUEST ['pageCurrent'])) {
			$pageCurrent = $_REQUEST ['pageCurrent'];
		}
		if($pageCurrent=='') {
			$pageCurrent =1;

		}
		
	     //$count = count($model->where($map)->select());
		//取得满足条件的记录数
		$count = $model->where($map)->count();

		//$count=10;
		if ($count > 0) {

			//$pageSize=30;	//每页默认30
		    $pageSize = C('PERPAGE');
		    
			//if (isset($_REQUEST ['pageSize'])) {
			//	$pageSize = $_REQUEST ['pageSize'];
			//}
		    $numPerPage=$pageSize;	//每页显示几条数据
		   
		   // $model->where($map)->select();
		    //var_dump($model);
		    //die();
		    //var_dump($map);
		    //var_dump("`" . $order . "` " . $sort);
		    if($count<$numPerPage*($pageCurrent-1))
		    {
		        $pageCurrent=1;
		    }
		    
		    
		    if($pageCurrent==1 and $numPerPage==0){
		        //分页
		        $voList = $model->where($map)->order("`" . $order . "` " . $sort)->select();
		    }
		    else{
		        $voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($numPerPage)->page($pageCurrent.','.$numPerPage.'')->select();
		    }
		    //var_dump("22222");
		    //var_dump($model->getLastSql());
		    //echo $model->getLastSql();
           // var_dump($numPerPage);
            //echo M('')->getLastSql();
			
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			
		   if( method_exists($this, '_after_list')){
				
				$voList=$this->_after_list($voList);
			}
			
			//var_dump($voList);
			$this->assign('list', $voList);

		}
		
		$currentPage=!empty($_REQUEST[C('VAR_PAGE')]) ? $count>1?$_REQUEST[C('VAR_PAGE')]:1 : 1;
		$this->assign('pageSize', $pageSize);//数据总数
		$this->assign('totalCount', $count);//数据总数
		$this->assign('currentPage', $currentPage);//当前的页数，默认为1
		$this->assign('numPerPage', $numPerPage); //每页显示多少条
		cookie('_currentUrl_', __SELF__);
		return;
	}
	
	public function accessory() {
	    //if(IS_POST){on,5,6
	    //var_dump($_REQUEST);
	    $id=$_REQUEST["id"];
	    $navTabId=$_REQUEST["navTabId"];
	    $p_view="";
	    $p_field="";
	    if($navTabId=="Provsionspublish")
	    {
	        $p_view="uv_accessory_provsions";
	        $p_field="provsionspublishid";
	    }
	    else 
	    {
	        $p_view="uv_accessory_infopublish";
	        $p_field="infopublishid";
	    }
	    //$data=I('post.');
	    $model = D("files");
	    $list=$model->table(C('DB_PREFIX').$p_view)
	    ->where($p_field."='".$id."' and filename<>'' and folder<>''")
	    ->field("id,title,filedesc,filetype,username,folder,filename")
	    ->select();
	    //var_dump($model->getLastSql());
	    //}
	    $this->assign('list',$list);
	    $this->display("accessory");
	}

	
	public function index() {

	    
	   if(isset($this->selname)){
	       $this->dbname = $this->selname;
	   }
	   
		$model = D($this->dbname);
		
		//全文检索用
		$map = $this->_search($this->dbname);
		//var_dump($map);
		//查询条件 参考MeetingreservController
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
			//var_dump($map);
		}
		
		
		if (!empty($model)) {
		    $this->_list($model, $map);
		    //echo $model->getLastSql();
		}

		if (method_exists($this, '_befor_index')) {
		    $this->_befor_index();
		}
		
		
		$this->display();
	}

	protected function _search($dbname='') {
		
		
		$dbname = $dbname ? $dbname : $this->dbname;
		$model = D($dbname);
		$map = array();

		foreach ($model->getDbFields() as $key => $val) {
			if (isset($_REQUEST['keys']) && $_REQUEST['keys'] != '') {
				if(in_array($val,C('SEARCHKEY'))){
					$map [$val] = array('like','%'.trim($_REQUEST['keys']).'%');
				}else{
					//$map [$val] = $_REQUEST['keys'];
				}
			}
		}
		//var_dump($map);
		$map['_logic'] = 'or'; 
        if ((IS_POST)&&isset($_REQUEST['keys']) && $_REQUEST['keys'] != '') {
			$where['_complex'] = $map;
			return $where;
		}else{
			return $map;
			}
		
		
	}
	
	//更改状态
	public  function changeStatus(){
		$model = D($this->dbname);
		$id = $_REQUEST [$model->getPk()];
		$model->execute("update " .C('DB_PREFIX') . $this->dbname . " set status=(1-status) where id = '".$id."'");
		//$model->where($this->dbname."ID" . "='".$id."');
		//$data =array("Status"=>(1-Status));
		
		//$model->where($this->dbname.'ID' . ' = ' . $id)->save($data);
				//echo($model->getLastSql());
		$this->mtReturn(200,"更改【".$this->opname."】状态成功",$_REQUEST['navTabId'],false);  
	}
    
	 public function add() {
		if(IS_POST){
		  $model = D($this->dbname);
		  $data=I('post.');
		 
		  
		  
		  if (false === $data = $model->create()) {
			   $this->mtReturn(300,'失败，' . $model->getError(),$_REQUEST['navTabId'],true);  
            }
            
          if (method_exists($this, '_befor_insert')) {
                $data = $this->_befor_insert($data);
            }		

            $saveStatus=$model->add($data);
          if($saveStatus){
			if (method_exists($this, '_after_add')) {
			  $id = $model->getLastInsID();
			  $this->_after_add($id);
		    }
			$id = $model->getLastInsID();
	
			$this->mtReturn(200,"新增【".$this->opname."】成功",$_REQUEST['navTabId'],true); //.$id 
			
			//echo($_REQUEST['navTabId']);
			
			//$this->mtReturn(200,"更改【".$this->opname."】状态成功".$id,$_REQUEST['navTabId'],false);
		  }
		}
		if (method_exists($this, '_befor_add')) {
			$this->_befor_add();
		}
		
		$this->assign('id', 0);
		$this->display("edit");
	}
	
	
	
	public function edit() {
	    
	   
		$model = D($this->dbname);
		if(IS_POST){

			$data=I('post.');
			//die();
			if (false === $data = $model->create()) {
			    //var_dump($model->getError());
				$this->mtReturn(300,'失败，' . $model->getError(),$_REQUEST['navTabId'],true);
			}
			
			//var_dump($data);
			if (method_exists($this, '_befor_update')) {
				$data = $this->_befor_update($data);
			}
			if($model->save($data)||$model->save($data)==0){
				if (method_exists($this, '_after_edit')) {
					$id = $data['id'];
					$this->_after_edit($id);
				}
			}
			$id = $data['id'];
			
			//var_dump($model->getLastSql());
			
			$this->mtReturn(200,"编辑【".$this->opname."】成功",$_REQUEST['navTabId'],true);  //写入日志
			//$this->mtReturn(200,"更改【".$this->opname."】状态成功".$id,$_REQUEST['navTabId'],false);

		}
		
	
		if (method_exists($this, '_befor_edit')) {
			$this->_befor_edit();
		}
		
		$id = $_REQUEST [$model->getPk()];
		//var_dump($id);
		$vo = $model->getById($id);
		$this->assign('id', $id);
		$this->assign('Rs', $vo);

		$this->display();
	}
	
	public function view() {
	    $model = D($this->dbname);
	    if (method_exists($this, '_befor_view')) {
	        $this->_befor_view();
	    }
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$this->assign('id', $id);
		$this->assign('Rs', $vo);
		$this->display("edit");
	}
	
	public function del(){
		$model = D($this->dbname);
		$model->startTrans();
		$id =  $_REQUEST ['id'];
		if($id){
			$model->where('id = ' . $id  )->delete();
		}
		else{
		    $model->where("status=0")->delete();
		}
		//var_dump($data);
		if (method_exists($this, '_after_del')) {
		    $data = $this->_after_del($id,$model);
		}
		
		$model->commit();
		$this->mtReturn(200,"删除【".$this->opname."】成功",$_REQUEST['navTabId'],false);
	}
	
	public function _fenxi($fd,$ft,$type) {
		import("Org.Util.Chart");
        $chart = new \Chart;  
		$model = D($this->dbname);
		$this->fd=$fd;
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$list = $model->where($map)->distinct($this->fd)->field($this->fd)->select();
		//echo  $model->getlastsql();
	    foreach ($list as $key =>$vo){	
			$info=$info.",".$vo[$this->fd];
			$co = $model->where(array($this->fd=>$vo[$this->fd]))->where($map)->count('id');
			$count=$count.",".$co;
		}
    $title = $ft; 
    $data = explode(",", substr ($count, 1)); 
    $size = 140; 
    $width = 750; 
    $height = 300; 
    $legend = explode(",", substr ($info, 1));
    ob_end_clean();
	if ($type == 1) {
		$chart->create3dpie($title,$data,$size,$height,$width,$legend);
     }
	 if ($type == 2) {
		$chart->createcolumnar($title,$data,$size,$height,$width,$legend);
     }
	 if ($type == 3) {
		$chart->createmonthline($title,$data,$size,$height,$width,$legend);
     }
	 if ($type == 4) {
		$chart->createring($title,$data,$size,$height,$width,$legend);
     }
	 if ($type == 5) {
		$chart->createhorizoncolumnar($title,$subtitle,$data,$size,$height,$width,$legend);
     }
   
	}
	
    public function xlsout($filename='数据表',$headArr,$list){
			
		//导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
		import("Org.Util.PHPExcel");
		import("Org.Util.PHPExcel.Writer.Excel5");
		import("Org.Util.PHPExcel.IOFactory.php");
		if (method_exists($this, '_getExcel')) {
		    //var_dump($list);
		    $this->_getExcel($filename,$headArr,$list);
		}
		else 
		{
		    
		  $this->getExcel($filename,$headArr,$list);
		}

	}
	
	
	public function outxls() {
	    if(IS_POST){
	        $this->mtReturn(200,"无数据导入！",$_REQUEST['navTabId'],false);
	        //die();
	        ///$this->mtReturn(200,"用户数据导入成功！",$_REQUEST['navTabId'],false);
	         
	        //$this->xlsout($filename,$headArr,$list);
	    }
	    else
	        $this->display("outxls");
	}
	
	public function deleteaccessory()
	{
	    $strid =  $_REQUEST ['id'];
	    if(isset($strid)&&$strid!=''){
	        $strDelID = $strid;
	    }
	    else
	    {
	        exit;
	    }
	    
	    if(isset($strDelID)&&count($strDelID)>0){
	        $model=M("");
	        $list=$model->table(C('DB_PREFIX')."files")
	        ->where(" id = ".$strDelID." ")->select();
	        if(count($list)>0)
	        {
    	        //删除文件
    	        $file = $list[0]['folder'].$list[0]['filename'];
    	        //var_dump($file);
    	        $result = @unlink ($file);
    	       
    	       
    	        $model->startTrans();
    	        $model
    	        ->table(C('DB_PREFIX')."files")
    	        ->where(" id = ".$strDelID." ")->delete();
    	        $model->commit();
    	        $this->mtReturn(200,"删除【附件】成功",$_REQUEST['navTabId'],false);
	        }
	    }
	}
	
	
}

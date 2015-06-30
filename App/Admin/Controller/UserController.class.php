<?php
namespace Admin\Controller;
use Think\Controller;
use Common\ORG\Util\BuildTreeArray;

class UserController extends CommonController {
   
    public function _initialize() {
        parent::_initialize();
        $this->dbname = "user";
        $this->opname="用户";
    }
	
	
  
   public function _befor_add(){
     $list=orgcateTree($pid=0,$level=0,$type=0);
     $this->assign('list',$list);
  
  }
  
  
  
  public function _befor_insert($data){
	 $password=md5(md5(I('pwd')));
	 $data['password']=$password;
	 unset($data['pwd']);
	 return $data;
  }
  
  
  
  public function _befor_edit(){
 	//得到权限组
  	$UserID=$_REQUEST["id"];
  	$demo=M("user");
 	$list= $demo->table(C('DB_PREFIX')."auth_group a")
                ->join("left join ".C('DB_PREFIX')."auth_group_access b ON (a.id=b.group_id and b.uid='".$UserID."')")
                ->field("a.id as RuleGroupID,a.title as RuleGroupName, case when ifnull(b.uid,'') != '' then 'selected' else '' end as selected")
                ->order("a.Sort asc")
                ->select();
	//echo $demo->getLastSql();	                        
  	$this->assign('ruleslist',$list);
  }
  
  public function _befor_update($data){
      
	 if (strlen(I('pwd'))!==32){
	 $password=md5(md5(I('pwd')));
	 $data['password']=$password;
	 }
	 unset($data['pwd']);
	 return $data;
  }

  
 public function  _after_edit($id){
     $mUser_auth_group_access=M("auth_group_access");
     try {
         
            $rules=$_REQUEST["rules"];
            //var_dump($rules);
            
            if(isset($rules)&&$rules!=null)
            {
            $mUser_auth_group_access->startTrans();
            
            $mUser_auth_group_access->where("uid  = '" . $id . "'")->delete();
            
            $userruledata = array(
                "uid" => "",
                "group_id" => ""
            );
            foreach ($rules as $ruleID)
            {
                $userruledata["uid"]=$id;
                $userruledata["group_id"]=$ruleID;

                //$M_ruledetail->where("RuleID=(select RuleID from ".C('DB_PREFIX')."Rule where RuleGroupID  = '" . $RuleGroupID . "' and MenuID = '".$MenuIDbyRule."')");
                $mUser_auth_group_access->field('uid,group_id')->data($userruledata)->add();
            }

            $mUser_auth_group_access->commit();
            $this->mtReturn(200,"编辑成功".$id,$_REQUEST['navTabId'],true);  //写入日志
            }
            else
            {
                $mUser_auth_group_access->startTrans();
                
                $mUser_auth_group_access->where("uid  = '" . $id . "'")->delete();
                
                $mUser_auth_group_access->commit();
                $this->mtReturn(200,"编辑成功".$id,$_REQUEST['navTabId'],true);  //写入日志
            }
       
      }
      catch(Exception $e){
          $mUserRule->rollback();
          $this->mtReturn(300,"编辑失败".$e.$id,$_REQUEST['navTabId'],true);  //写入日志
      }
      
 }
  public function GetAllOrg(){
  	//import("Home.Library.TreeLib.BuildTreeArray");
  	//import('Common/ORG/Util/BuildTreeArray');
  	$list=M("org")->select();
  
  	//$bta = new  \Common\ORG\Util\BuildTreeArray($list,'orgid','orgpid',0);
  
  	$bta = new  BuildTreeArray($list,'orgid','orgpid',0);
  
  	$data = $bta->getTreeArray();
  	echo json_encode($data);
  
  	//$this->ajaxReturn(json_encode($data),'JSON');
  }
  public function _befor_index(){
  	
  	$demo=M("User");
  	$list=$demo->table("__USER__ a")
  	->join("left join __ORG__ b ON (a.orgid=b.id)")
  	->join("left join __DEP__ c ON (a.depid=c.id)")
  	->join("left join __AUTH_GROUP_ACCESS__ d ON (a.id = d.uid)")
  	->join("left join __AUTH_GROUP__ e ON (d.group_id = e.id)")
  	->field("a.id,a.username, a.password, a.sex,  a.tel,a.ins, a.phone, a.fax, a.email,  a.status, a.logintime, a.loginip, a.logins, c.name as DepName, b.name as OrgName,GROUP_CONCAT(e.title SEPARATOR',') as RuleGroupName")
  	->group("a.id")
  	->order("a.id asc")
  	->select();
  	//echo $demo->getLastSql();
  	$this->assign('list',$list);
  }
  
  public function _befor_del($id){
	  $uid=$id; 
	  M('auth_group_access')->where('uid='.$uid.'')->delete(); 
   }
   
   
   public function telnotebooks(){
        
       $demo=M("User");
       $list=$demo->table("__USER__ a")
       ->join("left join __ORG__ b ON (a.orgid=b.id)")
       ->join("left join __DEP__ c ON (a.depid=c.id)")
       ->field("a.id,a.username, a.password, a.sex,  a.tel,a.ins, a.phone, a.fax, a.email,  a.status, c.name as DepName, b.name as OrgName")
       ->group("a.id")
       ->order("a.id asc")
       ->select();
       //echo $demo->getLastSql();
       $this->assign('list',$list);
       $this->display();
   }
	
}
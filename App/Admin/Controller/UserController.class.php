<?php
namespace Admin\Controller;
use Think\Controller;
use Common\ORG\Util\BuildTreeArray;

class UserController extends CommonController {
   
    public function _initialize() {
        parent::_initialize();
        $this->dbname = "user";
        $this->opname="用户";
        $this->selname = 'uv_user';
    }
	
	
  
   public function _befor_add(){
     $list=orgcateTree($pid=0,$level=0,$type=0);
     $this->assign('list',$list);
     
     $demo=M("user");
     $list= $demo->table(C('DB_PREFIX')."authgroup a")
     ->where("a.status='1'")
     ->field("a.id as RuleGroupID,a.title as RuleGroupName,''  as selected")
     ->order("a.Sort asc")
     ->select();
     
     //var_dump($demo->getLastSql());
     $this->assign('ruleslist',$list);
  }
  
  public function add() {
      if(IS_POST){
          $model = D($this->dbname);
          $data=I('post.');
          if (false === $data = $model->create()) {
              $this->mtReturn(300,'失败，请检查值是否已经存在--' . $model->getError(),$_REQUEST['navTabId'],true);
          }
  
          if (method_exists($this, '_befor_insert')) {
              $data = $this->_befor_insert($data);
          }
  
          $saveStatus=$model->add($data);
          if($saveStatus){
              if (method_exists($this, '_after_add')) {
                  $id = $data["id"];
                  $this->_after_add($id);
              }
              $id = $data["id"];
  
              $this->mtReturn(200,"新增【".$this->opname."】成功".$id,$_REQUEST['navTabId'],true);
          }
           
      }
      if (method_exists($this, '_befor_add')) {
          $this->_befor_add();
      }
  
      $this->assign('id', 0);
      $this->display("edit");
  }
  
  
  public function _after_add($id){
      $this->add_auth_group_access($id);
  }
  
  
  
  public function _befor_insert($data){
	 $password=md5(md5(I('password')));
	 $data['password']=$password;
	 //unset($data['password']);
	 return $data;
  }
  

  
  
  public function _befor_edit(){
 	//得到权限组
  	$UserID=$_REQUEST["id"];
  	$demo=M("user");
 	$list= $demo->table(C('DB_PREFIX')."auth_group a")
                ->join("left join ".C('DB_PREFIX')."authgroupaccess b ON (a.id=b.group_id and b.uid='".$UserID."')")
                ->where("a.status='1'")
                ->field("a.id as RuleGroupID,a.title as RuleGroupName, case when ifnull(b.uid,'') != '' then 'selected' else '' end as selected")
                ->order("a.Sort asc")
                ->select();
	//echo $demo->getLastSql();	                        
  	$this->assign('ruleslist',$list);
  }
  
  public function _befor_update($data){
      
	 if (strlen(I('password'))!==32){
	 $password=md5(md5(I('password')));
	 $data['password']=$password;
	 }
	 unset($data['pwd']);
	 return $data;
  }
  public function  add_auth_group_access($id){
      $mUser_auth_group_access=M("authgroupaccess");
      try {
           
          $rules=$_REQUEST["rules"];
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
  
  
 public function  _after_edit($id){
    $this->add_auth_group_access($id);
      
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
//   public function _befor_index(){
  	
//   	$demo=M("User");
//   	$list=$demo->table("__USER__ a")
//   	->join("left join __ORG__ b ON (a.orgid=b.id)")
//   	->join("left join __DEP__ c ON (a.depid=c.id)")
//   	->join("left join __AUTHGROUPACCESS__ d ON (a.id = d.uid)")
//   	->join("left join __AUTHGROUP__ e ON (d.group_id = e.id)")
//   	->field("a.id,a.username, a.password, a.sex,  a.tel,a.ins, a.phone, a.fax, a.email,  a.status, a.logintime, a.loginip, a.logins, c.name as DepName, b.name as OrgName,GROUP_CONCAT(e.title SEPARATOR',') as RuleGroupName")
//   	->group("a.id")
//   	->order("a.id asc")
//   	->select();
//   	//echo $demo->getLastSql();
//   	$this->assign('list',$list);
//   }
  
  public function _befor_del($id){
	  $uid=$id; 
	  M('authgroupaccess')->where('uid='.$uid.'')->delete(); 
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

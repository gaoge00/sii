<?php
namespace Admin\Controller;
use Think\Controller;
use Common\ORG\Util\BuildTreeArray;
use Think\Exception;

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
              $this->mtReturn(300,'失败，' . $model->getError(),$_REQUEST['navTabId'],true);
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
  
              $this->mtReturn(200,"新增【".$this->opname."】成功",$_REQUEST['navTabId'],true);
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
 	$list= $demo->table(C('DB_PREFIX')."authgroup a")
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
              $this->mtReturn(200,"编辑成功",$_REQUEST['navTabId'],true);  //写入日志
          }
          else
          {
              $mUser_auth_group_access->startTrans();
  
              $mUser_auth_group_access->where("uid  = '" . $id . "'")->delete();
  
              $mUser_auth_group_access->commit();
              $this->mtReturn(200,"编辑成功",$_REQUEST['navTabId'],true);  //写入日志
          }
           
      }
      catch(Exception $e){
          $mUser_auth_group_access->rollback();
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

   
   //数据导入
   //上传方法
   public function upload()
   {
       if(IS_POST){
           
           header("Content-Type:text/html;charset=utf-8");
           //header("multipart/form-data");
           
           $upload = new \Think\Upload();// 实例化上传类
           $upload->maxSize   =     3145728 ;// 设置附件上传大小
           $upload->exts      =     array('xls', 'xlsx');// 设置附件上传类
           $upload->savePath  =      '/'; // 设置附件上传目录
           // 上传文件
           $info   =   $upload->uploadOne($_FILES['filename']);
           $filename = './Uploads'.$info['savepath'].$info['savename'];
           $exts = $info['ext'];
           if(!$info) {// 上传错误提示错误信息
               $this->mtReturnUpload(300,"数据导入失败！原因：".$upload->getError(),$_REQUEST['navTabId'],true);  //写入日志
              
           }else{// 上传成功
               $this->users_import($filename, $exts);
               
           }
        
       }
       $this->display("upload");
   }
   
   
   //导入数据方法
   protected function users_import($filename, $exts='xls')
   {
       //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
       import("Org.Util.PHPExcel");
       //创建PHPExcel对象，注意，不能少了\
       $PHPExcel=new \PHPExcel();
       //如果excel文件后缀名为.xls，导入这个类
       if($exts == 'xls'){
           import("Org.Util.PHPExcel.Reader.Excel5");
           $PHPReader=new \PHPExcel_Reader_Excel5();
       }else if($exts == 'xlsx'){
           import("Org.Util.PHPExcel.Reader.Excel2007");
           $PHPReader=new \PHPExcel_Reader_Excel2007();
       }

       //载入文件
       $PHPExcel=$PHPReader->load($filename);
       //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
       $currentSheet=$PHPExcel->getSheet(0);
       //获取总列数
       $allColumn=$currentSheet->getHighestColumn();
       //获取总行数
       $allRow=$currentSheet->getHighestRow();
       //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
       //去掉第一行 从2开始
       for($currentRow=2;$currentRow<=$allRow;$currentRow++){
           //从哪列开始，A表示第一列
           for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
               //数据坐标
               $address=$currentColumn.$currentRow;
               //读取到的数据，保存到数组$arr中
               $data[$currentRow][$currentColumn]=(string)($currentSheet->getCell($address)->getValue());
           }
       }
       
       
       //var_dump($data);
       $this->save_import($data);
   }
   
   //保存导入数据
   public function save_import($data)
   {
       //print_r($data);exit;
       $model = M('User');
       $model->startTrans();
   try {
       $i=0;
       $row=0;//$j = 1;
       foreach ($data as $k=>$v){
           $row++;
           if(
               (!isset($v['A'])||$v['A']=="")&&
               (!isset($v['B'])||$v['B']=="")&&
               (!isset($v['C'])||$v['C']=="")&&
               (!isset($v['D'])||$v['D']=="")&&
               (!isset($v['E'])||$v['E']=="")&&
               (!isset($v['F'])||$v['F']=="")&&
               (!isset($v['G'])||$v['G']=="")&&
               (!isset($v['H'])||$v['H']=="")
           ){
               continue;
           }
           
               //判断字段是否为空。
               $this->checkFieldIsNull($row,$v['A'],"人员编码");
               
               $id=str_pad($v['A'],5,"0",STR_PAD_LEFT);
               
               $user['id'] =  $id;
               //判断社员编号是否存在
               
               //var_dump($id);
               $result = $model->table(C('DB_PREFIX')."user")->where(array('id' => $id))->find();
                if($result){
                    $model->table(C('DB_PREFIX')."user")->where(array('id' => $id))->delete();
                    $model->table(C('DB_PREFIX')."authgroupaccess")->where(array('uid' => $id))->delete();
                }
                
                
                
               //密码
               $user['password'] = md5(md5("123456"));
               //用户名
               $username=$v['B'];
               //判断字段是否为空。
               $this->checkFieldIsNull($row,$username,"姓名");
               $user['username'] = $username;
               
               //性别
               $sex=trim($v['C']);
                //判断字段是否为空。
               $this->checkFieldIsNull($row,$sex,"性别");
               if($sex!="男"&&$sex!="女")
               {
                   $this->sendError($row,"性别","性别不正确！".$sex);
               }
               $user['sex'] = $sex;
               
               //部门
               $orgName=$v['D'];
               
               if(isset($orgName)&&$orgName!=="")
               {    //$j ++;
                   $result = $model->table(C('DB_PREFIX')."org")->where(array('name' => $orgName))->select();
                   if(count($result)>0)
                   {
                       //var_dump($result);
                       $orgid= $result[0]["id"];
                       $user['orgid'] = $orgid;
                   }
                   else
                 {
                       $this->sendError($row,"部门","部门不存在！");
                   }
               }
               else
             {
                   $user['orgid'] = '';
               }

               //职务
               $depName=$v['E'];
               if(isset($depName)&&$depName!=="")
               {
                   $result = $model->table(C('DB_PREFIX')."dep")->where(array('name' => $depName))->select();
                   if(count($result)>0)
                   {
                       //var_dump($model->getLastSql());
                       $depid= $result[0]["id"];
                       $user['depid'] = $depid;
                   }
                   else
                   {  
                       $this->sendError($row,"职务","职务不存在！");
                   }
               }else{
                   $user['depid'] = '';
               }
               
               //内线
               $ins=$v['F'];
               $user['ins'] = $ins;
               //电话
               $phone=$v['G'];
               $user['tel'] = $phone;
               
               //var_dump($user);
               $resultUser=$model->table(C('DB_PREFIX')."user")->add($user);
               if($resultUser==false)
                   throw new Exception("用户数据导入失败！");
               //权限表
               if(isset($id)&&$id!="")
               {
                   $groupName=$v['H'];
                   $groupNameArr=array();
                   //权限
                   if(isset($groupName)&&$groupName!="")
                   {
                       $groupNameArr=split('\,',$groupName);
                   }
                   for($j=0;$j<count($groupNameArr);$j++)
                   {
                   
                       $authgroupaccess['uid'] = $id;
                       $result = $model->table(C('DB_PREFIX')."authgroup")->where(array('title' => $groupNameArr[$j]))->select();
                       if(count($result)>0)
                       {
                           $authgroupaccess['group_id']=$result[0]["id"];
                       }
                       else
                    {
                           $this->sendError($row,"权限组","权限组不存在！".$model->getLastSql());
                       }
                       //var_dump("11111111");
                       $result = $model->table(C('DB_PREFIX')."authgroupaccess")->where("group_id='".$authgroupaccess['group_id']."' and uid='".$id."'")->find();
                       //var_dump($model->getLastSql());
                       if($result){  
                           continue;
                       }
                       else{
                           $model->table(C('DB_PREFIX')."authgroupaccess")->field('uid,group_id')->data($authgroupaccess)->add();
                       }
                   }
               }
               $i++;  
           }
           //var_dump($j);
           $model->commit();
           if($i==0)
           {
               $this->mtReturnUpload(200,"无数据导入！",$_REQUEST['navTabId'],false);
           }
           else 
          {
                $this->mtReturnUpload(200,"用户数据导入成功！",$_REQUEST['navTabId'],false);
           }
   }
   catch (Exception $ex)
   {
       $model->rollback();
       $this->mtReturnUpload(300, "用户数据导入失败！" . $ex . $id, $_REQUEST['navTabId'], true); // 写入日志
   }

   }
   
   public function checkFieldIsNull($i,$strField="",$Field="")
   {
       if(!isset($strField)||$strField=="")
       {
           $this->mtReturnUpload(300, "第".$i."行数据错误:请检查【".$Field."】字段，不能为空!", $_REQUEST['navTabId'], true); // 写入日志
       }
   }
   
   public function sendError($i,$errField="",$errMsg="")
   {
       $this->mtReturnUpload(300, "第".$i."行数据错误:请检查【".$errField."】字段!".$errMsg, $_REQUEST['navTabId'], true); // 写入日志
   }
   
  
	
}

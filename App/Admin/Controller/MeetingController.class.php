<?php
//职务管理
namespace Admin\Controller;
use Think\Controller;
use Common\ORG\Util\BuildTreeArray;
class MeetingController extends CommonController {
	
	public function _initialize() {
		parent::_initialize();
		$this->opname="会议室";
		$this->dbname = 'Meeting';
		//var_dump("123212312321321321");
	}
	
	/*
    public function index(){
    	//echo date('y-m-d h:i:s',time());
    	$list=M($this->dbname)->select();
    	$this->assign('list',$list);
    	$this->display();
    }
    */
	
	
    public function _befor_edit(){
    	
    	//var_dump("aaaaaaaaaaaaa");
    	
    	//$list=orgcateTree($pid=0,$level=0,$type=0);
    	//$this->assign('type',I('get.type'));
    	//$this->assign('list',$list);
    }
    
    
    public function _after_edit()
    {
        
       $id=$_REQUEST["id"];
       
       //var_dump("111111111");
       $this->addMeet_Devices($id);
       //var_dump("222222222");
    }
    
    public function _after_add($id)
    {
        $this->addMeet_Devices($id);
    }
    
    
    
    //得到所有的Keys关键字信息，ZTree中使用
    public function AjaxGetAllDevices()
    {
        $meetingid = $_REQUEST["id"];
        // 关键词首层
        $demo = M("Meetingdevice");
        $keyslist=$demo->table(C('DB_PREFIX')."meetingdevice a")
        ->join("left join ".C('DB_PREFIX')."meetinganddevice b ON (a.id=b.meetingdeviceid and b.meetingid='".$meetingid."')")
        ->field("a.id,0 pid,a.name, case when ifnull(b.meetingid,'') != '' then 'true' else 'false' end as checked")
        ->where("a.status=1")
        ->order("a.sort asc")
        ->select();
        
        $bta = new BuildTreeArray($keyslist, 'id', 'pid', 0);
        $data = $bta->getTreeArray();
        //var_dump(json_encode($data));
        echo json_encode($data);
        // $this->ajaxReturn(json_encode($data),'JSON');
    }
  
    public function addMeet_Devices($id)
    {
        if (IS_POST) {
            $data = I('post.');
            $meetinganddevice = M("meetinganddevice");
            try {
                $meetingID =$id;
                $strDevicesID = $_REQUEST["devicesid"];
                $meetinganddevice->startTrans();
                // 进行相关的业务逻辑操作
                $meetinganddevice->table(C('DB_PREFIX')."meetinganddevice")->where("meetingid  = '" . $meetingID . "'")->delete();
                //var_dump($meetinganddevice->getLastSql());
                $meetinganddevicedata = array(
                    "meetingid" => "",
                    "meetingdeviceid" => ""
                );
    
    
                if (isset($strDevicesID) && $strDevicesID != "") {
    
                    $DevicesIDArray = explode(",", $strDevicesID);
    
                    foreach ($DevicesIDArray as $DevicesID) {
                        $meetinganddevicedata["meetingid"] = $meetingID;
                        $meetinganddevicedata["meetingdeviceid"] = $DevicesID;
    
                        $meetinganddevice->table(C('DB_PREFIX')."meetinganddevice")->field('meetingid,meetingdeviceid')
                        ->data($meetinganddevicedata)
                        ->add(); // 保存
                        
                        //var_dump($meetinganddevice->getLastSql());
                    }
                }
                $meetinganddevice->commit();
                $this->mtReturn(200, "编辑成功", $_REQUEST['navTabId'], true); // 写入日志
            } catch (Exception $e) {
                $meetinganddevice->rollback();
                $this->mtReturn(300, "编辑失败" . $e . $id, $_REQUEST['navTabId'], true); // 写入日志
            }
        }
    }
    
}
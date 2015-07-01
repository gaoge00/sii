<?php
//职务管理
namespace Admin\Controller;
use Think\Controller;
use Org\Util\ArrayList;

class MeetingreservController extends CommonController {
	
	public function _initialize() {
		parent::_initialize();
		$this->opname="会议室预约管理";
		$this->dbname = 'meetingreserv';// uv_getmeetingreserv
		$this->selname='uv_getmeetingreserv';
	}
	
	function _filter(&$map) {
	    if(IS_POST){
	        if(isset($_REQUEST['time1']) && $_REQUEST['time1'] != ''&&isset($_REQUEST['time2']) && $_REQUEST['time2'] != ''){
	            $map['_logic'] = 'and';
	            $map['startdate'] =array(array('egt',I('time1')." 00:00:00"),array('elt',I('time2')." 23:59:59")) ;
	        }
	        else if(isset($_REQUEST['time1']) && $_REQUEST['time1'] != ''){
	            $map['_logic'] = 'and';
	            $map['startdate'] =array(array('egt',I('time1')." 00:00:00")) ;
	        }
	        else if(isset($_REQUEST['time2']) && $_REQUEST['time2'] != ''){
	            $map['_logic'] = 'and';
	            $map['startdate'] =array(array('elt',I('time2')." 23:59:59")) ;
	        }
	        
	        
	        if(isset($_REQUEST['s_userid']) && $_REQUEST['s_userid'] != ''){
	            $map['_logic'] = 'and';
	            $map['userid'] =array('eq',I('s_userid')) ;
	        }
	        
	        if(isset($_REQUEST['s_meetingid']) && $_REQUEST['s_meetingid'] != ''){
	            $map['_logic'] = 'and';
	            $map['meetingid'] =array('eq',I('s_meetingid')) ;
	        }
	        //var_dump($map);
	    } 
	}
	
    
    //显示日历用
    public function AjaxCale()
    {
    	$Start = $_REQUEST["start"];		//开始时间
    	$End = $_REQUEST["end"];			//结束时间
    	$MeetingID=$_REQUEST["meetingid"];	//会议室ID
    	
    	$strWhere=format("a.startdate between date_format(from_unixtime({0}),'%Y-%m-%d') and date_format(from_unixtime({1}),'%Y-%m-%d') ",$Start,$End);
    	if(isset($MeetingID) && $MeetingID!="0"){
    	    $strWhere =$strWhere . " and a.meetingid = '" . $MeetingID ."'";
    	}
    	
    	$demo=M("meetingreserv");
    	$list=$demo->table(C('DB_PREFIX')."meetingreserv a")
    	->join("left join ".C('DB_PREFIX')."meeting b ON (a.meetingid=b.id)")
    	->join("left join ".C('DB_PREFIX')."user c ON (a.userid=c.id)")
    	->field("a.id,a.meetingid,
    			'' as url ,'' color,
    			concat('[',b.name,']',a.title) as title,
    			concat(a.startdate ,' ' ,a.starttime) as start,
    			concat(a.startdate ,' ' ,a.endtime) as end,
    			a.status,b.color,
    			case when a.hasallday='1' then 'true' else 'false' end  as allDay")
    	->order("a.startdate,a.starttime asc")
    	->where($strWhere)
    	->select();
    	
    	foreach ($list as $key=>$value){
    		if($value["allDay"]=="true"){
    			$list[$key]["allDay"]=true;
    		}
    		if($value["allDay"]=="false"){
    			$list[$key]["allDay"]=false;
    		}
    	}
    	exit(json_encode($list));
    }
    
	
  public function _befor_edit(){

  		$MeetingreservID=$_REQUEST["id"];
  		$this->GetReservByID($MeetingreservID);
  	
  }

  public function _befor_view(){
  		$MeetingreservID=$_REQUEST["id"];
  		$this->GetReservByID($MeetingreservID);
  }
  
  public function GetReservByID($id){
  	
  	 
  	$demo=M("uv_getmeetingreserv");
  	$list = $demo->where("id = '".$id."'")
  	    ->select();
  	/*
  	$list=$demo->table(C('DB_PREFIX')."meetingreserv a")
  	->join("left join ".C('DB_PREFIX')."meeting b ON (a.meetingid=b.id)")
  	->join("left join ".C('DB_PREFIX')."user c ON (a.userid=c.id)")
  	->field("a.id,a.meetingid,b.name,c.username,a.title,
    			date_format(a.startdate,'%Y-%m-%d') as startdate,
    			date_format(a.starttime,'%H:%i') as starttime,
    			date_format(a.endtime,'%H:%i') as endtime,
    			a.status,a.note,a.hasallday,a.timelength,a.orgid,a.userid,
    			b.hastv,b.hasprojection,b.hasvideo,b.hastel")
  	    			->order("a.startdate,a.startdate desc")
  	    			->where("a.id = '".$id."'")
  	    			->select();
  	
    echo $demo->getLastSql();
    */
  	$this->assign('id',$id);
  	$this->assign('beforlist',$list[0]);
  }
  
  
  
  
  //添加更新会议设备
   function _after_add($id){
	   	$this->addDevices($id);
   }

   //编辑更新会议设备
   function _after_edit($id){
   		$this->addDevices($id);
   }
   
   function addDevices($id){
	   	if(IS_POST){
	   		$Devs = $_REQUEST["devs"];	//选择的设备ID
	   		 
	   		 
	   		$M_Lock=M("meetingreservdevice");
	   		 
	   		 
	   		$data=array(
	   				"meetingreservid"=>$id,
	   				"meetingdeviceid"=>""
	  
	   		);
	   		 
	   		$M_Lock->startTrans();
	   		// 进行相关的业务逻辑操作
	   	
	   		$strWhere="meetingreservid = '" . $id . "' ";
	   		$M_Lock->where($strWhere)->delete();	//清空选择
	   		 
	   		foreach ($Devs as $Dev){
	   			$data['meetingdeviceid']=$Dev;
	   			$M_Lock->field('meetingreservid,meetingdeviceid')
	   			->add($data);	//清空选择
	   	
	   			//var_dump($M_Lock->getLastSql());
	   		}
	   		 
	   	
	   	
	   		 
	   		$M_Lock->commit();
	   	}
	}
   
  
    //得到会议室属性
    public function AjaxGetMeetingRoomProperty(){
    
    	$meetingid=$_REQUEST["meetingid"];
    
    	$demo=M("meeting");
    
    	$list=$demo->where("id = '".$meetingid."' and Status=1 ")
    	->field("hastv,hasprojection,hasvideo,hastel,peoples")
    	->select();
    	
    	//header('Content-type: text/json');
    	echo json_encode($list);
    
    }
    
    
    
    
    //得到可用的会议室设备
    public function Ajaxloaddevs(){

    	$id=$_REQUEST["id"];
    	$M_MeetDev=M("");
    	
    	$querySql="
				select a.*,
				CASE WHEN IFNULL(b.meetingdeviceid, '') != '' THEN 'checked'  ELSE '' END AS checked
				from __MEETINGDEVICE__ a
				left join __MEETINGRESERVDEVICE__ b on(a.id=b.meetingdeviceid and b.meetingreservid='".$id."')
				left join __MEETINGRESERV__ c on (b.meetingdeviceid=c.id and c.id='".$id."')
				where a.Status='1'
				order by b.meetingdeviceid desc,a.sort asc;
    			";
    	
    	$devlist = $M_MeetDev->query($querySql);
    	
    	//var_dump($M_MeetDev->getLastSql());
    	$this->assign('Devlist',$devlist);
    	$this->display("loaddevs");
    
    }
    
    //判断会议室是否占用
    public function AjaxExistMeeingRoom()
    {
    	$MeetingreservID = $_REQUEST["id"];
    	$MeetingID = $_REQUEST["meetingid"];
    	$StartDate = $_REQUEST["startdate"];
    	$StartTime = $_REQUEST["starttime"];
    	$EndTime =   $_REQUEST["endtime"];
    	$Devs = $_REQUEST["devs"];	//选择的设备ID

    	/*
    	 * 	P_MeetingID integer,
			P_StartDate date,
    		P_StartTime time,
    		P_EndTime time
    	 */
        if(empty($MeetingreservID)){
            $MeetingreservID=0;
        }
            

    	//$strSql=format("Call UP_GetMeetingDevicesByAdd ('{0}','{1}','{2}','{3}')",$StartDate,$StartTime,$EndTime,'12');
    	$strSql=format("Call UP_ExistMeeingRoom('{0}','{1}','{2}','{3}','{4}')",$MeetingreservID,$MeetingID,$StartDate,$StartTime,$EndTime);
    	//echo $strSql;
    	$Meetslist =M('')->query($strSql);
    	
    	$result = array();
    	
    	if (count($Meetslist) > 0) {
    		
    		$result['error'] = "会议室已被占用";
    		//$result['message'] = "会议室已被占用";
    		exit(json_encode($result));
    	}
		else{
			$pDevs =arr2str($Devs,',') ;
			$strSql=format("Call UP_ExistMeeingDevices('{0}','{1}','{2}','{3}','{4}')",$MeetingreservID,$pDevs,$StartDate,$StartTime,$EndTime);
		
			$DevsList=M('')->query($strSql);
			//var_dump($DevsList) ;
			
			
			if (count($DevsList) > 0) {
			    
			    $arrDeviceNames=array();
			    
				foreach ($DevsList as $key=>$value){
				   array_push($arrDeviceNames,$value["DeviceName"]);
				  // var_dump($value["DeviceName"]);
				}
			   // var_dump($arrDeviceNames );
			    
				$result['error'] = format("设备已被占用[{0}]",arr2str($arrDeviceNames, ','));
    			//$result['message'] = "设备已被占用";
    			exit(json_encode($result));
			}
			
			$result['ok'] = '';
    		//$result['message'] = "";
    		exit(json_encode($result));
		}
    }
    
    public function Del() {
        
        $id=$_REQUEST["id"];
        if(isset($id)&&$id!="")
        {
            $list = M('meetingreserv')->where("id = " . $id ."")->delete();
            $this->mtReturn(200,"清理【".$this->opname."】记录成功",'','',U('index'));
        }
        else 
        {
        $ids=$_REQUEST["delids"];
        $list = M('meetingreserv')->where("id in (" . $ids .")")->delete();
        $this->mtReturn(200,"清理【".$this->opname."】记录成功",'','',U('index'));
        }
    } 
  
}

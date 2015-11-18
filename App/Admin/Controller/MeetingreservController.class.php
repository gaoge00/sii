<?php
//职务管理
namespace Admin\Controller;
use Think\Controller;
use Org\Util\ArrayList;
use Org\Util\Date;
use Think\Exception;

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
	        return $map;
	    } 
	}

    //显示日历用
    public function AjaxCale()
    {
    	$Start = $_REQUEST["start"];		//开始时间
    	$End = $_REQUEST["end"];			//结束时间
    	$MeetingID=$_REQUEST["meetingid"];	//会议室ID
    	//var_dump($demo->);
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
    	//var_dump($demo->getLastSql());
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
     
    public function getOrgNameByUid()
    {
    
        $demo=M("meetingreserv");
        
        //var_dump( $demo);
        
        $list=$demo->table(C('DB_PREFIX')."user a")
        ->join("left join ".C('DB_PREFIX')."org b ON (a.orgid=b.id)")
        ->field("a.id,b.name as orgName")
        ->where("a.id='".session('uid')."'")
        ->select();
        //var_dump( $demo->getLastSql());
       
        
        if(isset($list)&&count($list)>0)
            $result=$list[0]["orgName"];
        else
           $result="";
        
        return $result;
    }
	
    public function _befor_edit(){
    
      //var_dump("111111111111111111");
        $this->assign('caltype',$_REQUEST["caltype"]);
    	$MeetingreservID=$_REQUEST["id"];
    	$this->GetReservByID($MeetingreservID);
    }
    
    public function _befor_add(){
      
      //echo SessionSavePath();
      $this->assign('caltype',$_REQUEST["caltype"]);
      $this->assign('orgName',$this->getOrgNameByUid());
      $MeetingreservID=$_REQUEST["id"];
      $this->GetReservByID($MeetingreservID); 
 
    }
    
    public function _befor_view(){
    	$MeetingreservID=$_REQUEST["id"];
    	$this->GetReservByID($MeetingreservID);
    }
    
    public function _befor_insert($data){
      $MR_hastv = $_REQUEST["MR_hastv"]=="on"?"checked":"unchecked";	//内置电视
      $MR_hasprojection = $_REQUEST["MR_hasprojection"]=="on"?"checked":"unchecked";	//内置投影
      $MR_hasvideo = $_REQUEST["MR_hasvideo"]=="on"?"checked":"unchecked";	//视频会议
      $MR_hastel = $_REQUEST["MR_hastel"]=="on"?"checked":"unchecked";	//电话会议
      $builtindevices=$MR_hastv.','.$MR_hasprojection.','.$MR_hasvideo.','.$MR_hastel;
      
      $data["builtindevices"]=$builtindevices;
      
      //var_dump($builtindevices);
      //die();
      return $data;
    }
    
    public function _befor_update($data){
      $MR_hastv = $_REQUEST["MR_hastv"]=="on"?"checked":"unchecked";	//内置电视
      $MR_hasprojection = $_REQUEST["MR_hasprojection"]=="on"?"checked":"unchecked";	//内置投影
      $MR_hasvideo = $_REQUEST["MR_hasvideo"]=="on"?"checked":"unchecked";	//视频会议
      $MR_hastel = $_REQUEST["MR_hastel"]=="on"?"checked":"unchecked";	//电话会议
      $builtindevices=$MR_hastv.','.$MR_hasprojection.','.$MR_hasvideo.','.$MR_hastel;
      
      $data["builtindevices"]=$builtindevices;
      
      //var_dump($data);
      //die();
      return $data;
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
            $nowDate=date('Y-m-d',time());
            $Date315=date('Y')."-03-15";
            $Date331=(date('Y')+1)."-03-31";

//             if($nowDate>=$Date315&&$nowDate<=$Date331)
//             {
                $maxDate=$Date331;
//             }
//             else
//            {
//                 $maxDate="2050-12-31";
//             }
            //var_dump($maxDate);
            
    $builtindevices=$list[0]["builtindevices"];
    $listbuiltindevices=Array();
    if($builtindevices==''||!isset($builtindevices))
    {
        $listbuiltindevices=array(
            "hastv"=>"unchecked",
            "hasprojection"=>"unchecked",
            "hasvideo"=>"unchecked",
            "hastel"=>"unchecked"  
        );
    }
    else {
        
        $arrbuiltindevices=explode(',', $builtindevices);
        
        $listbuiltindevices=array(
            "hastv"=>$arrbuiltindevices[0],
            "hasprojection"=>$arrbuiltindevices[1],
            "hasvideo"=>$arrbuiltindevices[2],
            "hastel"=>$arrbuiltindevices[3]
        );
    }

    
    //工厂长，部长，副总，总经理，秘书 可以预约 5#和接待室
    
    $MeetingRoomRule=Array(
        "工厂长",
        "部长",
        "副总",
        "总经理",
        "秘书"
        );
    $demo=M("meetingreserv");
    //var_dump( $demo);
    $list=$demo->table(C('DB_PREFIX')."user a")
    ->join("left join ".C('DB_PREFIX')."dep b ON (a.depid=b.id)")
    ->field("a.id,TRIM(BOTH ' ' FROM b.name) as depName")
    ->where("a.id='".session('uid')."'")
    ->select();
//     var_dump($demo->getLastSql());
//     die();
    
    if(isset($list)&&count($list)>0)
        $result=$list[0]["depName"];
    else
       $result="";
//     var_dump($result);
//     die();
    $demo=M('meeting');
    if(in_array($result,$MeetingRoomRule)||authsuperadmin(session('uid')))
    {
        $listz=$demo->where(array('status'=>1))->select();
    }
    else 
   {
        $listz=$demo->where("status='1' and TRIM(BOTH ' ' FROM name) not in ('5#','接待室')")->select();
    }
    
//          var_dump($demo->getLastSql());
//          die();
    //工厂长，部长，副总，总经理，秘书 可以预约 5#和接待室
  	$this->assign('orgName',$this->getOrgNameByUid());
  	$this->assign('id',$id);
  	$this->assign('builtindeviceslist',$listbuiltindevices);
  	$this->assign('beforlist',$list[0]);
  	$this->assign('maxDate',$maxDate);
  	
  	$this->assign('listz',$listz);
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
	   				"meetingreservid" => $id,
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
    	$list=$demo->where("id = if(ifnull('".$meetingid."','')='',id,'".$meetingid."') and Status=1 ")
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
    	
    	//var_dump($devlist);
    	$this->assign('Devlist',$devlist);
    	$this->display("loaddevs");
    
    }
    
    //得到可用的会议室设备
    public function Ajaxloaddevsbymeetid(){
    
        $meetid=$_REQUEST["meetid"];
        $meetreservid=$_REQUEST["meetreservid"];
        
        
        $M_MeetDev=M("");
         
        $querySql="
				select b.*
                 ,CASE WHEN IFNULL(e.meetingdeviceid, '') != '' THEN 'checked'  ELSE '' END AS checked
                from __MEETING__ a
                left join __MEETINGANDDEVICE__ b on (a.id=b.meetingid)
                left join __MEETINGDEVICE__ c on(b.meetingid=c.id)
                left join __MEETINGRESERV__ d on(a.id=d.meetingid and  d.id='".$meetreservid."')
                left join __MEETINGRESERVDEVICE__ e on (d.id=e.MEETINGRESERVID and b.meetingdeviceid=e.meetingdeviceid)

                where a.id='".$meetid."'
				  and c.Status='1'
				order by b.meetingdeviceid desc,c.sort asc;
    			";
        $devlist = $M_MeetDev->query($querySql);
        //var_dump($M_MeetDev->getLastSql());
        //var_dump($devlist);
        //echo json_encode($devlist);
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
        if(isset($ids)&&count($ids)>0){
        $list = M('meetingreserv')->where("id in (" . $ids .")")->delete();
        $this->mtReturn(200,"清理【".$this->opname."】记录成功",'','',U('index'));
        }
        }
    } 
    
    public function outxls() {
        if(IS_POST){
            //         $map = $this->_search();
            //         if (method_exists($this, '_filter')) {
            //             $this->_filter($map);
            //         }
            $StartDate = $_REQUEST["startdt"];		//开始时间
            $EndDate = $_REQUEST["enddt"];			//结束时间
            $Strtype=$_REQUEST["strtype"];	//会议室/设备
            $model = D($this->dbname);
            
            $where=" 1=1 ";
            $field="";
            if($StartDate!=""&&isset($StartDate))
                //var_dump(222);exit;
                $where.=" and startdate>='".$StartDate."'";
            if($EndDate!=""&&isset($EndDate))
                //var_dump(224);exit;
                $where.=" and startdate<='".$EndDate."'";
            
            //var_dump($where);die();
            if($Strtype=="1")
            {   
                $where.=" and ifnull(meetingName,'')<>'' ";
                //var_dump($where);die();
                $field=" distinct meetingName,userName,indevices1,indevices2,indevices3,indevices4,group_concat(ifnull(deviceName,'无')) deviceName,startdate,starttime,endtime,timelength  ";
                $headArr=array('会议室名称','预订人','内置电视','投影','视频会议','电话会议','占用设备','日期','开始时间','结束时间','占用时长');
                $filename="会议室占用情况统计表";
                $list= $model->table(C('DB_PREFIX')."uv_meetingreservdeviceexcel  ")
                ->where($where)
                ->field($field)
                ->group('id')
                ->order("startdate,starttime,endtime ")
                ->select();
                //var_dump($model->getLastSql());
                
            }
            else
           {
                $where.=" and ifnull(deviceName,'')<>'' ";
                //var_dump($where);die();
                $field=" distinct ifnull(deviceName,'无'),userName,indevices1,indevices2,indevices3,indevices4,meetingName,startdate,starttime,endtime,timelength  ";
                $headArr=array('设备名称','预订人','内置电视','投影','视频会议','电话会议','占用会议室','日期','开始时间','结束时间','占用时长');
                $filename="设备占用情况统计表";
                $list= $model->table(C('DB_PREFIX')."uv_meetingreservdeviceexcel  ")
                ->where($where)
                ->field($field)
                ->order("startdate,starttime,endtime ")
                ->select();
                //var_dump($model->getLastSql());
           }
           
           
           
           
           //var_dump($list);

//             //include('PHPExcel.php');
//             import("Org.Util.PHPExcel");
//             import("Org.Util.PHPExcel.Writer.Excel5");
//             import("Org.Util.PHPExcel.IOFactory.php");
//             //这些数据假设是从M('xxx')->select()里面出来的
//             $data = array (
//                 array ('id' => 1, 'name' => '张三' ), array ('id' => 2, 'name' => '李四' ), array ('id' => 3, 'name' => '王五' ) );
//             //PHPExcel支持读模版 所以我还是比较喜欢先做好一个Excel的模版 比较好，不然要写很多代码 模版我放在根目录了
//             //创建一个读Excel模版的对象
//             $objReader = PHPExcel_IOFactory::createReader ( 'Excel5' );
//             $objPHPExcel = $objReader->load ("template.xls" );
//             //获取当前活动的表
//             $objActSheet = $objPHPExcel->getActiveSheet ();
            
//             $objActSheet->setTitle ( '演示工作表' );
//             $objActSheet->setCellValue ( 'A1', '这个是PHPExcel演示标题' );
//             $objActSheet->setCellValue ( 'A2', '日期：' . date ( 'Y年m月d日', time () ));
//             $objActSheet->setCellValue ( 'F2', '导出时间：' . date ( 'H:i:s' ) );
//             //我现在就开始输出列头了
//             $objActSheet->setCellValue ( 'A3', '序号' );
//             $objActSheet->setCellValue ( 'B3', '姓名' );
//             //具体有多少列 看你的数据走 会涉及到计算
//             //现在就开始填充数据了 （从数据库中） $data
//             $baseRow = 4; //数据从N-1行开始往下输出 这里是避免头信息被覆盖
//             foreach ( $data as $r => $dataRow ) {
//                 $row = $baseRow + $r;
//                 //将数据填充到相对应的位置
//                 $objPHPExcel->getActiveSheet ()->setCellValue ( 'A' . $row, $dataRow ['id'] ); //学员编号
//                 $objPHPExcel->getActiveSheet ()->setCellValue ( 'B' . $row, $dataRow ['name'] ); //真实姓名
//             }
//             //导出
//             $filename = time ();
//             header ( 'Content-Type: application/vnd.ms-excel' );
//             header ( 'Content-Disposition: attachment;filename="' . $filename . '.xls"' ); //"'.$filename.'.xls"
//             header ( 'Cache-Control: max-age=0' );
//             $objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' ); //在内存中准备一个excel2003文件
//             $objWriter->save ( 'php://output' );

            $this->xlsout($filename,$headArr,$list);
            }
            else 
            $this->display("outxls");
    }
    
    public	function _getExcel($fileName,$headArr,$data){
        
        
        //var_dump($data);
        //对数据进行检验
        if(empty($data) || !is_array($data)){
            die("data must be a array");
        }
    
        //检查文件名
        if(empty($fileName)){
            exit;
        }
        //标题
        $title=$fileName;
        $date = date("Y_m_d",time());
        $fileName .= "_{$date}.xls";
        import("Org.Util.PHPExcel.php");
        //require_once './ThinkPHP/Library/Org/Util/PHPExcel.php';     //修改为自己的目录
        echo '<p>TEST PHPExcel 1.8.0: read xlsx file</p>';
    
        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        //__PUBLIC__/excelModel/
        $excelType = \PHPExcel_IOFactory::identify("Public/excelModel/1.xls");
        $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        
        $objPHPExcel = $objReader->load("Public/excelModel/1.xls"); //$filename可以是上传的文件，或者是指定的文件
        
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        if($highestRow > 1500){
            echo '数据超过大小限制。请确保数据不超过1500行！';die;
        }
        $highestColumn = $sheet->getHighestColumn(); // 取得总列数
        $k = 0;
        
        
        
        //循环读取excel文件,读取一条,插入一条
//         for($j=2;$j<=$highestRow;$j++)
//         {
//             $d['id'] = $objPHPExcel->getActiveSheet()->getCell("A".$j)->getValue();//ID
//             $d['name'] = $objPHPExcel->getActiveSheet()->getCell("D".$j)->getValue();//姓名
//             $date = explode('/',$objPHPExcel->getActiveSheet()->getCell("F".$j)->getValue());//日期
//         }
        
        
        
        
        $objProps = $objPHPExcel->getProperties();
        
        
        $objActSheet = $objPHPExcel->getActiveSheet();
        
        $objActSheet->setCellValue ( 'A1', $title );
        //设置表头
        $key = ord("A");
        
        
        //var_dump($headArr);
        //var_dump($headArr);
        //die();
        foreach($headArr as $v){
            $colum = chr($key);
            $objActSheet->setCellValue($colum.'2', $v);
            $key += 1;
        }

        $column = 3;
        $row=3;
        
        //var_dump($data);
        //die();
        //var_dump($row);
        //设置字体
        //$objActSheet->getStyle('A3:K'.($row+1))->getFont()->setName('宋体');
        //$objActSheet->getStyle('A3:K'.($row+1))->getFont()->setSize(11);
         
        //设置单元格边框
        //$objActSheet->getStyle('A3:K'.($row))->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        
        //设置为文本格式
        foreach($data as $key => $rows){ //行写入
             if($row%2!=0)
             {
                 
                //$excelStyle=$objActSheet->getStyle('A'.($row).':K'.($row));
                //$excelStyle->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                //$excelStyle->getFill()->getStartColor()->setARGB('FFcccccc');

                    //$objActSheet->getComment('A'.($row))->getFillColor()->setRGB('FFcccccc' );
//                 //$excelStyle->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
//                 //var_dump('A'.($row).':K'.($row));

                 
             }
            $span = ord("A");
            foreach($rows as $keyName=>$value){// 列写入
                $j = chr($span);  
                $objActSheet->setCellValue($j.$column, $value);//setCellValueExplicit
                $span++;
            }
            
            $column++;
            $row++;
        }
        
        
        //设置字体
        $objActSheet->getStyle('A3:K'.($row+1))->getFont()->setName('宋体');
        $objActSheet->getStyle('A3:K'.($row+1))->getFont()->setSize(11);
         
        //设置单元格边框
        $objActSheet->getStyle('A3:K'.($row-1))->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);

        //将B2的样式复制到B3至B7
//         $objActSheet->duplicateConditionalStyle(
//             $objActSheet->getStyle('A3')->getConditionalStyles(),
//             'A5:K'.($row)
//         );
        
        // 格式刷 其他单元区域
        //$objActSheet->duplicateStyle($objActSheet->getStyle('E4'), 'E5:E13' );
        //$objActSheet->duplicateStyle($objActSheet->getStyle('A3:K4'), 'A5:K6');
        $fileName = iconv("utf-8", "gb2312", $fileName);
        //var_dump($row);
        //die();
        //重命名表
        //$objPHPExcel->getActiveSheet()->setTitle('test');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }
    
    //数据导入
    //上传方法
    public function upload()
    {
        if(IS_POST){
            //var_dump($_REQUEST['navTabId']);
            //$this->mtReturn(200,"数据导入成功！",$_REQUEST['navTabId'],true);  //写入日志
            header("Content-Type:text/html;charset=utf-8");
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
                //$this->error($upload->getError());
            }else{// 上传成功
               
                $this->meeting_import($filename, $exts);
                //$this->mtReturn(200,"数据导入成功！",$_REQUEST['navTabId'],true);  //写入日志
            }
 
        }
        $this->display("upload");
    }

    //导入数据方法
    protected function meeting_import($filename, $exts='xls')
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
                
                //if($currentRow==17)
                    //var_dump($currentSheet->getCell($address)->getValue());
                //读取到的数据，保存到数组$arr中
                $data[$currentRow][$currentColumn]=(string)($currentSheet->getCell($address)->getValue());
            }
        }
        
        $this->save_import($data);
    }
     
    //保存导入数据
    public function save_import($data)
    {
        //print_r($data);exit;
        //var_dump($data);
        
        //var_dump($data);
        $model = M('meetingreserv');
        $model->startTrans();
        try {
            
            //判断是否有要导入的数据
            $i=1;
            //判断行号
            $row=0;
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
                    (!isset($v['H'])||$v['H']=="")&&
                    (!isset($v['I'])||$v['I']=="")&&
                    (!isset($v['J'])||$v['J']=="")&&
                    (!isset($v['K'])||$v['K']=="")&&
                    (!isset($v['L'])||$v['L']=="")&&
                    (!isset($v['M'])||$v['M']=="")
                    ){
                    //var_dump("111111111");
                   continue;    
                }
                
                
                
                //var_dump("111111111");
                //检查会议室名称
                $meetingName=$v['A'];
                //判断字段是否为空。
                $this->checkFieldIsNull($row,$meetingName,"会议室名称");
                $result = $model->table(C('DB_PREFIX')."meeting")->where(array('name' => $meetingName))->select();
                if(count($result)>0)
                {
                   //var_dump($model->getLastSql());
                   $meetingid= $result[0]["id"];
                   $meetingreserv["meetingid"]=$meetingid;
                }
                else 
              {
                  $this->sendError($row,"会议室名称","会议室不存在！".$meetingName);
                }
                
                //主题
                $meetingTitle=$v['B'];
                //判断字段是否为空。
                $this->checkFieldIsNull($row,$meetingTitle,"主题");
//                 if($row==16)
//                     var_dump($meetingTitle);
                if(mb_strlen($meetingTitle,"utf8")<=20)
                {
                   
                    $meetingreserv["title"]=$meetingTitle;
                }
                else 
              {
                    //var_dump(sizeof($meetingTitle));
                    $this->sendError($row,"主题","字段长度超过20个字符！");
                }
                
                //预订人
                $userID=$v['C'];
                //判断字段是否为空。
                $this->checkFieldIsNull($row,$userID,"预订人");
                $result = $model->table(C('DB_PREFIX')."user")->where(array('username' => $userID))->select();
                //var_dump($model->getLastSql());
                if(count($result)>0)
                {
                    $userid= $result[0]["id"];
                    $meetingreserv["userid"]=$userid;
                    $orgid= $result[0]["orgid"];
                    $meetingreserv["orgid"]=$orgid;

                }
                else
              {
                    
                    $this->sendError($i,"预订人","预订人不存在！".$model->getLastSql());
                }
                

                //时长
                $timeLength=$v['G'];
                if(!is_numeric($timeLength))
                {
                    $this->sendError($row,"时长","格式不正确！");
                }
                else
              {
                    if($timeLength>24)
                    {
                        $this->sendError($row,"时长","时长不能超过24小时！");
                    }
                    $meetingreserv["timelength"]=$timeLength;
                }
                //日期
                $startDate=$v['D'];
                //判断字段是否为空。
                $this->checkFieldIsNull($row,$startDate,"日期");
                
                //这里可以任意格式，因为strtotime函数很强大
                $is_date=strtotime($startDate)?strtotime($startDate):false;
                
                if($is_date===false){
                    $this->sendError($row,"日期","日期格式不正确！");
                }else{
                    $startDate= date('Y-m-d',$is_date);//只要提交的是合法的日期，这里都统一成2014-11-11格式
                    $meetingreserv["startdate"]=$startDate;
                }
                
                //开始时间，结束时间
                if($timeLength==24)
                {
                    $startTime="00:00:00";
                    $endTime="23:59:00";
                    $meetingreserv["hasallday"]="1";

                }
                else
              {
                  $startTime=$v['E'];
                  $endTime=$v['F'];
                  
                }    
               

                //判断字段是否为空。
                $this->checkFieldIsNull($row,$startTime,"开始时间");
                //判断字段是否为空。
                $this->checkFieldIsNull($row,$endTime,"结束时间");
                
                //这里可以任意格式，因为strtotime函数很强大
                $is_startTime=strtotime($startTime)?strtotime($startTime):false;
                
                if($is_startTime===false){
                    $this->sendError($row,"开始时间","时间格式不正确！".$startTime);
                }else{
                    $startTime= date('H:i:s',$is_startTime);//只要提交的是合法的日期，这里都统一成2014-11-11格式
                    $meetingreserv["starttime"]=$startTime;
                }
                
                //这里可以任意格式，因为strtotime函数很强大
                $is_endTime=strtotime($endTime)?strtotime($endTime):false;
                
                if($is_endTime===false){
                    $this->sendError($row,"开始时间","时间格式不正确！");
                }else{
                    $endTime= date('H:i:s',$is_endTime);//只要提交的是合法的日期，这里都统一成2014-11-11格式
                    $meetingreserv["endtime"]=$endTime;
                }
                

                $strSql=format("Call UP_ExistMeeingRoom('{0}','{1}','{2}','{3}','{4}')",-1,$meetingreserv["meetingid"],$startDate,$startTime,$endTime);
                //echo $strSql;
                $Meetslist =M('')->query($strSql);
                 
                $result = array();
                //var_dump($strSql);
                //var_dump($meetingreserv);
                if (count($Meetslist) > 0) {
                    //var_dump($strSql);
                    $this->sendError($row,"开始时间","会议室时间存在冲突！");
                }
                
                
                //会议内容
                $note=$v['I'];
                $meetingreserv["note"]=$note;
                
                //内置设备
                $builtindevices1=$v['J']=="使用"?"checked":"unchecked";
                $builtindevices2=$v['K']=="使用"?"checked":"unchecked";
                $builtindevices3=$v['L']=="使用"?"checked":"unchecked";
                $builtindevices4=$v['M']=="使用"?"checked":"unchecked";
                $meetingreserv["builtindevices"]=$builtindevices1.','
                                                .$builtindevices2.','
                                                .$builtindevices3.','
                                                .$builtindevices4;
                
                //var_dump($meetingreserv["builtindevices"]);
                
                //与会设备
                if(isset($v['H'])&&$v['H']!="")
                {
                    //var_dump("0.0.0.0.0.0.0");
                    $devices=split('\,',$v['H']);
                    
                    $meetingreservid=0;
                    //判断是否为第一次插入
                    $flag=false;
                    for($j=0;$j<count($devices);$j++)
                    {
                        $result = $model->table(C('DB_PREFIX')."meetingdevice")->where(array('name' => $devices[$j]))->select();
                        //var_dump($result) ;
                        if(count($result)>0)
                        {
                            //$meetingreservdevice['meetingreservid'] = $meetingreserv["meetingid"];
                            $meetingreservdevice['meetingdeviceid'] = $result[0]["id"];
   
                            $strSql=format("Call UP_ExistMeeingDevices('{0}','{1}','{2}','{3}','{4}')",$meetingreserv["meetingid"],$result[0]["id"],$startDate,$startTime,$endTime);
                            
                            $DevsList=M('')->query($strSql);
                            //var_dump($DevsList) ;
            
                            if (count($DevsList) > 0) {
                                //var_dump($meetingreserv) ;
                                //var_dump($DevsList) ;
                                $this->sendError($i,"与会设备",$devices[$j]."存在冲突！");
                            }
                            else    
                         {  
                                 if($flag==false)
                                 {
                                     
                                     
                                     
                                     
                                     
                                     
                                     $model->table(C('DB_PREFIX')."meetingreserv")->add($meetingreserv);
                                     //var_dump($model->getLastSql());
                                     $meetingreservid=$model->getLastInsID();
                                     $meetingreservdevice['meetingreservid']=$meetingreservid;
                                     //var_dump($meetingreservdevice);
                                     $model->table(C('DB_PREFIX')."meetingreservdevice")->field('meetingreservid,meetingdeviceid')->data($meetingreservdevice)->add();
                                     //var_dump($model->getLastSql());
                                     $flag==true;
                                 }
                                 else 
                             {
                                 
                                 //var_dump("2222222");
                                     $meetingreservdevice['meetingreservid']=$meetingreservid;
                                     //var_dump($meetingreservdevice);
                                     $model->table(C('DB_PREFIX')."meetingreservdevice")->field('meetingreservid,meetingdeviceid')->data($meetingreservdevice)->add();
                                     //var_dump($model->getLastSql());
                                 }
                            }

                        }
                        else
                     {
                            $this->sendError($row,"与会设备",$devices[$j]."不存在！");
                        } 
                    }
                
                }
                else
              {

                  $model->table(C('DB_PREFIX')."meetingreserv")->add($meetingreserv);
                    
                }
                //var_dump($i);
                $i++;
            }
            
            $model->commit();
      
            if($i==0)
            {
                $this->mtReturnUpload(200,"无数据导入！",$_REQUEST['navTabId'],false);
            }
            else
           {
                //$this->success('产品导入成功');
                $this->mtReturnUpload(200,"用户数据导入成功！",$_REQUEST['navTabId'],false);
            }
        }
        catch (Exception $ex)
        {
            $model->rollback();
            //$this->error('产品导入失败');
            $this->mtReturnUpload(300, "用户数据导入失败！" . $ex . $id, $_REQUEST['navTabId'], true); // 写入日志
        }
    
    }
    
    public function checkFieldIsNull($i,$strField="",$Field="")
    {
        if(!isset($strField)||$strField=="")
        {
            $this->mtReturnUpload(300, "第".($i+1)."行数据错误:请检查【".$Field."】字段，不能为空!", $_REQUEST['navTabId'], true); // 写入日志
        }
    }
    
    public function sendError($i,$errField="",$errMsg="")
    {
        $this->mtReturnUpload(300, "第".($i+1)."行数据错误:请检查【".$errField."】字段!".$errMsg, $_REQUEST['navTabId'], true); // 写入日志
    }
    
    
    
}

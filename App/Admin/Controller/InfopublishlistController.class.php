<?php
// 职务管理
namespace Admin\Controller;


use Common\ORG\Util\BuildTreeArray;
//use ThinkPHP\Library\Think\Controller;

class InfopublishlistController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
        $this->opname = "信息发布";
        $this->dbname = 'infopublish'; 
        $this->selname = 'uv_getinfopublish';
        // $this->GetKeys();
    }
    
    
    
    
    
    function _filter(&$map) {
    
        
        //用户只能查看自己发布的信息
//         $map['_logic'] = 'and';
//         $map['uid'] =array('eq',session("uid")) ;
        
        if(IS_POST){
            if(isset($_REQUEST['s_startdt']) && $_REQUEST['s_startdt'] != ''&&isset($_REQUEST['s_enddt']) && $_REQUEST['s_enddt'] != ''){
                $map['_logic'] = 'or';
                $map['startdt'] =array(array('egt',I('s_startdt')),array('elt',I('s_enddt'))) ;
                $map['_logic'] = 'or';
                $map['enddt'] =array(array('egt',I('s_startdt')),array('elt',I('s_enddt'))) ;
            }

            if(isset($_REQUEST['s_title']) && $_REQUEST['s_title'] != ''){
                $map['_logic'] = 'and';
                $map['title'] =array('like',"%".I('s_title')."%") ;
            }
            if(
            
                (isset($_REQUEST['s_orgname']) && $_REQUEST['s_orgname'] != '') ||
                (isset($_REQUEST['s_keysname']) && $_REQUEST['s_keysname'] != '')
             )
            {
            
                $map['_logic'] = 'and';
                //$map['_string'] = 'status=1 AND score>10';
            
                $map['_string'] = " 1=1 ";
                if(isset($_REQUEST['s_orgname']) && $_REQUEST['s_orgname'] != '')
                {
                    $map['_string'] .= " and concat(',',orgsname,',') regexp concat(',(',replace('".$_REQUEST['s_orgname']."',',','|'),'),')";
                }
                
            
                if(isset($_REQUEST['s_keysname']) && $_REQUEST['s_keysname'] != '')
                {
                    $map['_string'] .= " and concat(',',keysname,',') regexp concat(',(',replace('".$_REQUEST['s_keysname']."',',','|'),'),')";
                }

            }
            
            if(isset($_REQUEST['s_importantid']) && $_REQUEST['s_importantid'] != ''){
                $map['_logic'] = 'and';
                $map['importantid'] =array('eq',I('s_importantid')) ;
            }
            
            if(isset($_REQUEST['s_statusid']) && $_REQUEST['s_statusid'] != ''&&$_REQUEST['s_statusid'] != '-1'){
                $map['_logic'] = 'and';
                $map['status'] =array('eq',I('s_statusid')) ;
            }
            //return $map;
            //var_dump($map);
        }
    }

    

     
    public function _befor_index(){
         // 重要度important
        $importantlist = M("Important")->where("status=1")
            ->field("id,name")
            ->order("sort asc")
            ->select();
        //var_dump($importantlist);
        $this->assign('importantlist', $importantlist);
        

        $statuslist=array(
            0=>array('id'=>-1,'name'=>'所有'),
            1=>array('id'=>1,'name'=>'有效'),
            2=>array('id'=>0,'name'=>'无效'),
            
        );
       $this->assign('statuslist', $statuslist);
       
      
    }
    
    
    
    public function _befor_view()
    {
            $this->GetMasters();
    }

    //二级联动中的第一个select 之后 加载第二个 select
    public function AjaxGetInfoType()
    {
        $infotypeid = $_REQUEST["infotypeid"];
        $arrWhere = array();
        
        // 信息分类
        if (isset($infotypeid)) {
            $strWhere = "pid=" . $infotypeid . " and status=1";
            // $arrWhere= array(
            // 'InfoTypePid' => $infotypeid,
            // 'Status' => '1'
            // );
        } else {
            $strWhere = " 1<>1 and status=1";
            // $arrWhere= array(
            // '1' => array('NEQ','1')
            // );
        }
        
        $infotypelist = M("Infotype")->where($strWhere)
            ->field("id as value,name as label")
            ->order("sort asc")
            ->select();
        // var_dump($infotypelist);
        // $this->assign('infotypelist',$infotypelist);
        
        echo json_encode($infotypelist);
    }
    
    // 得到Menu
    public function GetMasters()
    {
        
        //根据信息发布的InfopublishID 查找到对应的InfoType中的InfotypePid
        $infopublishid = $_REQUEST["id"];
        if (isset($infopublishid)||$infopublishid!='')
        {
        $arrInfotype= M("Infotype")
        ->where("id = (select infotypeid from ".C('DB_PREFIX')."Infopublish where id='".$infopublishid."') and status=1")
        ->field("pid")
        ->order("sort asc")
        ->select();
        //var_dump($arrInfotype);
        $this->assign('infotypepid', $arrInfotype[0]['pid']);
        }
        // 信息分类首层
        $infotypelist = M("Infotype")->where("level = 0 and status=1")
            ->field("id,name")
            ->order("Sort asc")
            ->select();
        //var_dump($infotypelist);
        $this->assign('infotypelist', $infotypelist);
        // 重要度important
        $importantlist = M("Important")->where("status=1")
            ->field("id,name")
            ->order("sort asc")
            ->select();
        //var_dump($importantlist);
        $this->assign('importantlist', $importantlist);
        
        // //发布部门
        // $orglist=M("Org")->where("Status=1")->field("OrgID,OrgName,OrgLevel")->order("Sort asc")->select();
        // //var_dump($list);
        // $this->assign('orglist',$orglist);
        // 关键词首层
        $keyslist = M("Keys")->where("status=1")
            ->field("id,pid,name")
            ->order("sort asc")
            ->select();
        // var_dump($keyslist);
        $this->assign('keyslist', $keyslist);
        
        
        $fujian_flag = M("infopublish")
        ->table(C('DB_PREFIX')."infopublish a")
        ->join ("left join ".C('DB_PREFIX')."files b on a.attid=b.attid")
        ->field("a.id,sum(if(ifnull(b.id,'')='',0,1)) attcount")
        ->where("a.id='".$infopublishid."'")
        ->select();
        $attcount=0;
        if(count($fujian_flag)>0)
            $attcount=$fujian_flag[0]["attcount"];
        $this->assign('attcount', $attcount);
        
        //var_dump($attcount);
        //die();
        // echo json_encode($list);
        // $this->ajaxReturn(json_encode($data),'JSON');
    }
    //得到所有的Keys关键字信息，ZTree中使用
    public function AjaxGetAllKeys()
    {
        $infopublishid = $_REQUEST["id"];
        //var_dump($infopublishid);
        // 关键词首层
        $demo = M("Keys");
        $keyslist=$demo->table(C('DB_PREFIX')."keys a")
        ->join("left join ".C('DB_PREFIX')."infopublishkeys b ON (a.id=b.keysid and b.infopublishid='".$infopublishid."')")
        ->field("a.id,a.pid,a.name, case when ifnull(b.infopublishid,'') != '' then 'true' else 'false' end as checked")
        ->where("a.status=1")
        ->order("a.sort asc")
        ->select();
        $bta = new BuildTreeArray($keyslist, 'id', 'pid', 0);
        $data = $bta->getTreeArray();
        echo json_encode($data);
        // $this->ajaxReturn(json_encode($data),'JSON');
    }
    
    //得到所有的Keys关键字信息，ZTree中使用
    public function AjaxGetAllOrgs()
    {
        $infopublishid = $_REQUEST["id"];
        // 关键词首层
        $demo = M("Org");
        $keyslist=$demo->table(C('DB_PREFIX')."org a")
        ->join("left join ".C('DB_PREFIX')."infopublishorgs b ON (a.id=b.orgid and b.infopublishid='".$infopublishid."')")
        ->field("a.id,a.pid,a.name, case when ifnull(b.infopublishid,'') != '' then 'true' else 'false' end as checked")
        ->where("a.status=1")
        ->order("a.sort asc")
        ->select();
        //var_dump($demo->getLastSql());
        // $this->assign('keyslist',$keyslist);
        $bta = new BuildTreeArray($keyslist, 'id', 'pid', 0);
        $data = $bta->getTreeArray();
        //var_dump(json_encode($data));
        echo json_encode($data);
        // $this->ajaxReturn(json_encode($data),'JSON');
    }
    
    
    
    //得到所有的Keys关键字信息，ZTree中使用
    public function AjaxGetAllKeys_index()
    {
        $keysid = $_REQUEST["id"];
        //var_dump($infopublishid);
        // 关键词首层
        $demo = M("Keys");
        $keyslist=$demo->table(C('DB_PREFIX')."keys a")
        ->field("a.id,a.pid,a.name, case when FIND_IN_SET(ifnull(a.id,''),'".$keysid."')>0   then 'true' else 'false' end as checked")
        ->where("a.status=1")
        ->order("a.sort asc")
        ->select();
        $bta = new BuildTreeArray($keyslist, 'id', 'pid', 0);
        $data = $bta->getTreeArray();
        echo json_encode($data);
        // $this->ajaxReturn(json_encode($data),'JSON');
    }
    
    //得到所有的Keys关键字信息，ZTree中使用
    public function AjaxGetAllOrgs_index()
    {
        $orgsid = $_REQUEST["id"];
        // 关键词首层
        $demo = M("Org");
        $keyslist=$demo->table(C('DB_PREFIX')."org a")
        //->join("left join ".C('DB_PREFIX')."infopublishorgs b ON (a.id=b.orgid and b.infopublishid='".$infopublishid."')")
        ->field("a.id,a.pid,a.name, case when FIND_IN_SET(ifnull(a.id,''),'".$orgsid."')>0   then  'true' else 'false' end as checked")
        ->where("a.status=1")
        ->order("a.sort asc")
        ->select();
        //var_dump($demo->getLastSql());
        // $this->assign('keyslist',$keyslist);
        $bta = new BuildTreeArray($keyslist, 'id', 'pid', 0);
        $data = $bta->getTreeArray();
        //var_dump(json_encode($data));
        echo json_encode($data);
        // $this->ajaxReturn(json_encode($data),'JSON');
    }
    
   
    
    
}
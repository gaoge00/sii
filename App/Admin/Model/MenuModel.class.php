<?php
namespace Admin\Model;

use Think\Model;

class MenuModel extends Model
{
    public function _initialize() {
        //parent::_initialize();
        $this->dbname = 'Menu';
    }
    
    protected $_validate = array(
        
         array('name', '','Menu已经存在！', 0, 'unique', 3),//1:新增，2:编辑，3:全部
         array('pid','checkLevel','不能选择下级作为上级！',0,'callback',2)
    );
    
//     function checkLevel(){
//         $pid=$_REQUEST['pid'];
//         $id=$_REQUEST['id'];
//         // 新选中的上级 级别
//         $pidlist = M("Menu")->where("id=".$pid."")
//         ->field("level")
//         ->select();
//         // 自身 级别
//         $idlist = M("Menu")->where("id=".$id."")
//         ->field("level")
//         ->select();
//         //                  var_dump($pidlist[0]["level"]);
//         //                  var_dump($idlist[0]["level"]);
//         //顶级可选
//         if($pid==0)
//             return true;
//         if($pidlist[0]["level"]>$idlist[0]["level"])
//         {
//             return false;
//         }
//         else
//         {
//             return true;
//         }
//     }
    
    /*
     * 定义字段的类型，用于某些验证环节。
     * protected $fields = array('id', 'username', 'email', 'age','_pk'=>'id', '_type'=>
     * array('id'=>'bigint','username'=>'varchar','email'=>'varchar','age'=>'int')
     *
     * );
     */
}
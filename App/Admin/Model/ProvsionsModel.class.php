<?php
namespace Admin\Model;

use Think\Model;

class ProvsionsModel extends Model
{
    public function _initialize() {
        //parent::_initialize();
        $this->dbname = 'Provsions';
    }
    
    protected $_validate = array(
        
        array('name', '','基本规定名称已经存在！', 0, 'unique', 3),//1:新增，2:编辑，3:全部
        array('no', '','基本规定编号已经存在！', 0, 'unique', 3),//1:新增，2:编辑，3:全部
        //array('pid','checkLevel','不能选择下级作为上级!',0,'unique',2), // 在新增的时候验证name字段是否唯
        array('pid','checkLevel','不能选择下级作为上级！',0,'callback',2),
        
    );
    
    
    
    function checkName($name){
        $where=array("name"=>$name);
        $_REQUEST['id'] && $where['id']=$_REQUEST['id'];
        $r=$this->where($where)->find();
        if($r){
            return $_REQUEST['id']?true:false;
        }else{
            return true;
        }
    }
    
    
    /*
     * 定义字段的类型，用于某些验证环节。
     * protected $fields = array('id', 'username', 'email', 'age','_pk'=>'id', '_type'=>
     * array('id'=>'bigint','username'=>'varchar','email'=>'varchar','age'=>'int')
     *
     * );
     */
}

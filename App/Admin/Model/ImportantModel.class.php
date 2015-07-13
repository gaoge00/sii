<?php
namespace Admin\Model;

use Think\Model;

class ImportantModel extends Model
{

    protected $_validate = array(
        
        array('name', '','重要度已经存在！', 0, 'unique', 3)//1:新增，2:编辑，3:全部
        //array('name','checkNmae','职务名已经存在!换一个吧亲',0,'unique',3), // 在新增的时候验证name字段是否唯
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
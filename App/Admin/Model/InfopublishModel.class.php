<?php
namespace Admin\Model;

use Think\Model;

class InfopublishModel extends Model
{

    protected $_validate = array(
        array('title','','标题已经存在！',0,'unique',3),
        array('orgid','checkOrgName','所属部门必须入力！',0,'callback',3),
        array('keysid','checkKeysName','关键词必须入力！',0,'callback',3),
        
    );
    
    function checkOrgName(){
        $orgid=$_REQUEST['orgid'];
        if(isset($orgid)&&$orgid!='')
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    function checkKeysName(){
        $keysid=$_REQUEST['keysid'];
        if(isset($keysid)&&$keysid!='')
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /*
     * 定义字段的类型，用于某些验证环节。
     * protected $fields = array('id', 'username', 'email', 'age','_pk'=>'id', '_type'=>
     * array('id'=>'bigint','username'=>'varchar','email'=>'varchar','age'=>'int')
     * );
     */
}
<?php
namespace Admin\Model;
use Think\Model;

class KeysModel extends Model
{

    protected $_validate = array(
        
        array('name', '','关键字名称已经存在!', 0, 'unique', 3),//1:新增，2:编辑，3:全部
        array('pid','checkLevel','不能选择下级作为上级!',0,'unique',2), // 在新增的时候验证name字段是否唯
    );
    
 
    
    
    /*
     * 定义字段的类型，用于某些验证环节。
     * protected $fields = array('id', 'username', 'email', 'age','_pk'=>'id', '_type'=>
     * array('id'=>'bigint','username'=>'varchar','email'=>'varchar','age'=>'int')
     *
     * );
     */
}
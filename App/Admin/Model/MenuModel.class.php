<?php
namespace Admin\Model;

use Think\Model;

class MenuModel extends Model
{

    protected $_validate = array(
        
         array('name', '','Menu已经存在！', 0, 'unique', 3),//1:新增，2:编辑，3:全部
    );
    
    /*
     * 定义字段的类型，用于某些验证环节。
     * protected $fields = array('id', 'username', 'email', 'age','_pk'=>'id', '_type'=>
     * array('id'=>'bigint','username'=>'varchar','email'=>'varchar','age'=>'int')
     *
     * );
     */
}
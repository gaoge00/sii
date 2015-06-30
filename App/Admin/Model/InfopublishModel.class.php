<?php
namespace Admin\Model;

use Think\Model;

class InfopublishModel extends Model
{

    protected $_validate = array(
        array('title','','标题已经存在！',0,'unique',3)
    );
    
    /*
     * 定义字段的类型，用于某些验证环节。
     * protected $fields = array('id', 'username', 'email', 'age','_pk'=>'id', '_type'=>
     * array('id'=>'bigint','username'=>'varchar','email'=>'varchar','age'=>'int')
     * );
     */
}
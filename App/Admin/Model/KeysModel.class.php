<?php
namespace Admin\Model;
use Think\Model;

class KeysModel extends Model
{
    public function _initialize() {
        //parent::_initialize();
        $this->dbname = 'Keys';
    }
    
    protected $_validate = array(
        
        array('name', '','关键字名称已经存在!', 0, 'unique', 3),//1:新增，2:编辑，3:全部
        array('pid','checkLevel','不能选择下级作为上级！',0,'callback',2),
    );
    

 
  
    /*
     * 定义字段的类型，用于某些验证环节。
     * protected $fields = array('id', 'username', 'email', 'age','_pk'=>'id', '_type'=>
     * array('id'=>'bigint','username'=>'varchar','email'=>'varchar','age'=>'int')
     *
     * );
     */
}

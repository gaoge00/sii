<?php
return array(
	/* 数据库设置 */
    'DB_TYPE' => 'mysqli', // 数据库类型
    'DB_HOST' => '192.168.231.21', // 服务器地址192.168.231.21   127.0.0.1
    'DB_NAME' => 'sii_db', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => '123456', // 密码 123456
    'DB_PORT' => '3306', // 端口
    'DB_PREFIX' => 'sii_', // 数据库表前缀
    'DB_FIELDTYPE_CHECK' => false, // 是否进行字段类型检查
    'DB_FIELDS_CACHE' => true, // 启用字段缓存
    'DB_CHARSET' => 'utf8',  // 数据库编码默认采用utf8
    'SESSION_OPTIONS'=>array(
        'type'=> 'db',//session采用数据库保存
        'expire'=>36000,//session过期时间，如果不设就是php.ini中设置的默认值
    ),
    'SESSION_TABLE'=>'sii_session' //必须设置成这样，如果不加前缀就找不到数据表
);

// return array(
//     /* 数据库设置 */
//     'DB_TYPE' => 'mysqli', // 数据库类型
//     'DB_HOST' => '127.0.0.1', // 服务器地址192.168.231.21   127.0.0.1
//     'DB_NAME' => 'sii_db', // 数据库名
//     'DB_USER' => 'root', // 用户名
//     'DB_PWD' => '', // 密码 123456
//     'DB_PORT' => '3306', // 端口
//     'DB_PREFIX' => 'sii_', // 数据库表前缀
//     'DB_FIELDTYPE_CHECK' => false, // 是否进行字段类型检查
//     'DB_FIELDS_CACHE' => true, // 启用字段缓存
//     'DB_CHARSET' => 'utf8',  // 数据库编码默认采用utf8
//     'SESSION_OPTIONS'=>array(
//         'type'=> 'db',//session采用数据库保存
//         'expire'=>36000,//session过期时间，如果不设就是php.ini中设置的默认值
//     ),
//     'SESSION_TABLE'=>'sii_session' //必须设置成这样，如果不加前缀就找不到数据表
// );
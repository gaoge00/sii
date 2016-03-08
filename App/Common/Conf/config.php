<?php
return array(
    'URL_MODEL' => 0, // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    
    'URL_CASE_INSENSITIVE' => true, // 表示URL访问不区分大小写
    'DB_FIELDS_CACHE' => false, // 关闭缓存                        
    // 权限验证设置
      'AUTH_CONFIG'=>array(
      'AUTH_ON' => true,
      'AUTH_TYPE' => 1, // 认证方式，1为时时认证；2为登录认证。
      'AUTH_GROUP' => 'sii_authgroup',
      'AUTH_GROUP_ACCESS' => 'sii_authgroupaccess',
      'AUTHRULE' => 'sii_authrule',
      'AUTH_USER' => 'sii_user'
      ),
     
    'NOT_AUTH_MODULE' => 'Public,Index', // 默认无需认证模块
                                         // 超级管理员id,拥有全部权限,只要用户uid在这个角色组里的,就跳出认证.可以设置多个值,如array('1','2','3')
    'ADMINISTRATOR' => array(
        '00000','99999'
    ),

//     'SESSION_OPTIONS' => array(
//         'expire' => 3600
//     ),
    
    //'SESSION_EXPIRE'=>10,
    
    
    'SESSION_PREFIX' => 'SII', // Session 前缀
                                       
    // 加载扩展配置文件 多个用,隔开
    'LOAD_EXT_CONFIG' => 'web,db',
    
    // 上传设置
    'UPLOAD_MAXSIZE' => 52428800,
    'UPLOAD_EXTS' => array(
        'jpg',
        'gif',
        'png',
        'jpeg',
        'txt',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'ppt',
        'pptx',
        'pdf',
        'rar',
        'zip',
        'wps',
        'wpt',
        'dot',
        'rtf',
        'dps',
        'dpt',
        'pot',
        'pps',
        'et',
        'ett',
        'xlt'
    ), // 设置附件上传类型
    'UPLOAD_SAVEPATH' => './Public/'
);
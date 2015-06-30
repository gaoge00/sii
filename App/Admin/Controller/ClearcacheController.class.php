<?php

namespace Admin\Controller;


class ClearcacheController extends CommonController{


    public function index(){
    	//        $caches = array(
    	//            "allCache" => WEB_CACHE_PATH,
    	//            "allRunCache" => WEB_CACHE_PATH . "Runtime/",
    	//            "allAdminRunCache" => WEB_CACHE_PATH . "Runtime/Admin/",
    	//            "allHomeRunCache" => WEB_CACHE_PATH . "Runtime/Home/",
    	//            "allHomeRunCache" => WEB_CACHE_PATH . "Runtime/Home/",
    	//        );
        //WEB_CACHE_PATH = "";
    
    	$caches = array(

    			"AdminCache" => array("name" => "网站后台缓存文件", "path" => RUNTIME_PATH)
    	);
    	
    	/*
        $caches = array(
            "HomeCache" => array("name" => "网站前台缓存文件", "path" => "Runtime/Cache/Index/"),
            "AdminCache" => array("name" => "网站后台缓存文件", "path" => "Runtime/Cache/Admin/"),
            "HomeData" => array("name" => "网站前台数据库字段缓存文件", "path" => "Runtime/Data/Index/"),
            "AdminData" => array("name" => "网站后台数据库字段缓存文件", "path" => "Runtime/Data/Admin/"),
            "HomeLog" => array("name" => "网站前台日志缓存文件", "path" =>  "Runtime/Logs/Index"),
            "AdminLog" => array("name" => "网站后台日志缓存文件", "path" => "Runtime/Logs/Admin"),
            "HomeTemp" => array("name" => "网站前台临时缓存文件", "path" =>  "Runtime/Temp/Index/"),
            "AdminTemp" => array("name" => "网站后台临时缓存文件", "path" =>  "Runtime/Temp/Admin"),
            "Homeruntime" => array("name" => "网站前台runtime.php缓存文件", "path" =>  "Runtime/~runtime.php"),
            "Adminruntime" => array("name" => "网站后台runtime.php缓存文件", "path" =>  "Runtime/~runtime.php")
        );
        */
    	$this->assign("caches", $caches);
    	$this->display();
    }
    
    public function clear(){
        
        if (IS_POST) {
            $PATHs=$_REQUEST['delids'];
            $arrPath=str2arr($PATHs, ',');
            foreach ($arrPath as $path) {
                //var_dump(delDirAndFile($path));
                delDirAndFile($path);
            }
        
            $this->ajaxReturn(array("status" => 200, "message" => "缓存文件已清除"));
        } 
    }
    



}

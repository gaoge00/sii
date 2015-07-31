<?php
namespace Admin\Controller;
use Think\Controller;

class LoginController extends Controller {

    public function Login(){
		
        $this->assign('name','thinkphp,HelloWorld');
        $this->display();
    }
	
}
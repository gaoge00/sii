<?php
namespace Index\Controller;

use Think\Controller;

class IndexController extends Controller
{

    public function _initialize()
    {
        
        // if(!session('uid')){
        // redirect(U('public/login'));
        // }
        $config = S('DB_CONFIG_DATA');
        if (! $config) {
            $config = api('Config/lists');
            S('DB_CONFIG_DATA', $config);
        }
        C($config);
    }

    public function index()
    {
        $this->display();
    }
}
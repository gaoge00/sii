<?php
namespace Admin\Service;

class AdminService extends CommonService
{

    public function login($admin)
    {   
    	//不足5位补足5位(两种补0的方法)
        $admin['userid']=sprintf("%05d", $admin['userid']);
        
        //$admin['userid']=str_pad($admin['userid'],5,"0",STR_PAD_LEFT);
        if (! $this->existAccount($admin['userid'])) {
            return array(
                'status' => 0,
                'data' => '社员编号不存在！'
            );
        }

        $M_User = M('user');

        $account = $M_User->getByid($admin['userid']);
        
        if ($account['password'] != $this->encrypt($admin['password'])) {
            return array(
                'status' => 0,
                'data' => '密码不正确！'
            );
        }
        //过期时间
        session('uid', $account['id']);
        session('username',$account['username']);
        
        session('depid',$account['depid']);
        session('orgid',$account['orgid']);
        
        session('loginip', get_client_ip());
        session('logintime', date("Y-m-d H:i:s", time()));
        session('logins', $account['logins']);
        
        $data['id'] = session('uid');
        $data['logintime'] = date("Y-m-d H:i:s", time());
        $data['loginip'] = get_client_ip();
        $data['logins'] = array(
            'exp',
            'logins+1'
        );
        // $data['update_time']=time();
        //var_dump($data);
        M("user")->where("id='" . $data['id'] . "'")->save($data);
        
        $dat['username'] = session('uid');
        $dat['content'] = '登录成功！';
        $dat['os'] = $_SERVER['HTTP_USER_AGENT'];
        $dat['url'] = U();
        $dat['addtime'] = date("Y-m-d H:i:s", time());
        $dat['ip'] = get_client_ip();
        M("log")->add($dat);
        
        return array(
            'status' => 1
        );
    }

    public function logout()
    {
        $dat['username'] = session('uid');
        $dat['content'] = '退出成功！';
        $dat['os'] = $_SERVER['HTTP_USER_AGENT'];
        $dat['url'] = U();
        $dat['addtime'] = date("Y-m-d H:i:s", time());
        $dat['ip'] = get_client_ip();
        M("log")->add($dat);
        //session('[destroy]'); // 销毁session
        //session('[regenerate]'); // 重新生成session id
        session_unset();
        session_destroy();
    }

    public function existAccount($userid)
    {
        if (M('user')->where(array(
            "id" => $userid,
            "status" => 1
        ))->count() > 0) {
            return true;
        }
        
       // $dep = M('User');
        
        //echo $dep->getLastSql();
        return false;
    }

    public function encrypt($data)
    {
        // return md5(C('AUTH_MASK') . md5($data));
        return md5(md5($data));
    }
}

<?php

namespace app\admin\controller;

use app\admin\model\Admin;
use think\facade\Request;
use think\facade\Session;

class Login extends Base
{
    public function initialize()
    {

    }

    public function index()
    {
        $param = Request::param();
        if(Request::isAjax()){
            return $this->login($param);
        }
        return $this->fetch();
    }

    public function login($param)
    {
        $user = Admin::where('name','=',$param['username'])->find();
        if(!$user){
            return json(['message'=>'用户名不存在！','code'=>2001]);
        }else{
            if(compare_password($param['password'], $user['pwd'])){
                Session::set('user', $user);
                return json(['message'=>'登录成功！','code'=>0]);
            }else{
                return json(['message'=>'密码错误!','code'=>2002]);
            }
        }
    }

    public function logout()
    {
        Session::delete('user');
        $this->redirect('Index/index');
    }
}

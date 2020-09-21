<?php

namespace app\admin\controller;

use app\admin\model\Admin;
use think\Controller;
use think\facade\Session;

class Base extends Controller
{
    public function initialize()
    {
        $user = Session::get('user');
        $result = Admin::where('name','=',$user['name'])->where('pwd','=',$user['pwd'])->find();
        if(!$result){
            $this->redirect("Login/index");
        }
    }

    public function _empty()
    {
        return $this->fetch('error/404');
    }

    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move( '../public/uploads');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 jpg
            echo $info->getExtension();
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            echo $info->getSaveName();
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            echo $info->getFilename();
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }
}

<?php

namespace app\admin\model;

use think\Model;

class Lottery extends Model
{
    protected $append = ['nickname','realname','mobile'];

    public function getNicknameAttr($value,$data)
    {
        $nickname = Members::where('openid','=',$data['openid'])->value('nickname');
        return $nickname;
    }
    public function getRealnameAttr($value,$data)
    {
        $realname = Members::where('openid','=',$data['openid'])->value('realname');
        return $realname;
    }
    public function getMobileAttr($value,$data)
    {
        $mobile = Members::where('openid','=',$data['openid'])->value('mobile');
        return $mobile;
    }
}
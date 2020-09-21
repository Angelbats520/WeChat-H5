<?php

namespace app\admin\controller;

use app\admin\model\Lottery;
use app\admin\model\Prize;
use think\facade\Request;

class Lists extends Base
{
    public function stock()
    {
        if(Request::isAjax()) {
            $param = Request::param();
            $list = Prize::all();
            $total = Prize::count();
            $count = Prize::count();
            return json(['draw'=>$param['draw'],'recordsTotal'=>$total,'recordsFiltered'=>$count,'data'=>$list]);
        }
        return $this->fetch();
    }

    public function winner()
    {
        if(Request::isAjax()){
            $param = Request::param();
            $search = $param['search']['value'];
            $start = $param['start'];
            $length = $param['length'];
            $where =  "1=:check";
            $bind['check'] = 1;
            if($search!='' && $search!=null){
                $where .= " AND name LIKE :name";
                $bind['name'] = "%".$search."%";
            }
            foreach ($param['order'] as $v) {
                $order[$param['columns'][$v['column']]['data']] = $v['dir'];
            }
            $list = Lottery::where($where,$bind)->order($order)->limit($start,$length)->select();
            $total = Lottery::where($where,$bind)->count();
            $count = Lottery::where($where,$bind)->count();
            return json(['draw'=>$param['draw'],'recordsTotal'=>$total,'recordsFiltered'=>$count,'data'=>$list]);
        }
        return $this->fetch();
    }

    public function edit()
    {
        if(Request::isAjax()){
            $param = Request::param();
            $prize = Prize::get($param['id']);
            $result = $prize->save($param);
            return json($result?['message'=>'修改成功！','code'=>0,'data'=>$param]:['message'=>'服务器繁忙，请重试！','code'=>1011,'data'=>$param]);
        }
    }
}

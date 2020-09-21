<?php

/**
 * 奖品概率
 * @param $proArr
 * @return int|string
 */
// 抽奖概率算法
function get_rand($proArr)
{
    $result = '';
    foreach ($proArr as $key => $val) {
        $arr[$val['id']] = $val['v'];
    }
    // 概率数组的总概率
    $proSum = array_sum($arr);
    // 概率数组循环
    if($proSum==0){
        $result = 1;
    }else{
        foreach ($arr as $k => $v) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $v) {
                $result = $k;
                break;
            } else {
                $proSum -= $v;
            }
        }
    }
    unset ($proArr);
    return $result;
}

/**活动时间
 * @return int
 */
function game_time()
{
    if(strtotime("2018-08-04 00:00:00")>time()){
        //游戏未开始
        return 1;
    }else if(strtotime("2020-12-31 00:00:00")<time()){
        //游戏已结束
        return 2;
    }
    return 0;
}
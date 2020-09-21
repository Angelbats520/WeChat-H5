<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Env;

// +----------------------------------------------------------------------
// | Workerman设置 仅对 php think worker:server 指令有效
// +----------------------------------------------------------------------
return [
    // 扩展自身需要的配置
    'protocol'       => 'websocket', // 协议 支持 tcp udp unix http websocket text
    'host'           => '0.0.0.0', // 监听地址
    'port'           => 2345, // 监听端口
    'socket'         => '', // 完整监听地址
    'context'        => [], // socket 上下文选项
    'worker_class'   => '', // 自定义Workerman服务类名 支持数组定义多个服务

    // 支持workerman的所有配置参数
    'name'           => 'thinkphp',
    'count'          => 4,
    'daemonize'      => false,
    'pidFile'        => Env::get('runtime_path') . 'worker.pid',

    // 支持事件回调
    /**
     * 每个进程启动
     * @param $worker
     */
    'onWorkerStart'  => function ($worker) {

    },
    // onWorkerReload
    'onWorkerReload' => function ($worker) {

    },
    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    'onConnect'      => function ($connection) {

    },
    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    'onMessage'      => function ($connection, $data) {
        $connection->send('receive success');
    },
    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    'onClose'        => function ($connection) {

    },
    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    'onError'        => function ($connection, $code, $msg) {
        echo "error [ $code ] $msg\n";
    },
];

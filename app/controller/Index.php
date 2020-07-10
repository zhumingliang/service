<?php

namespace app\controller;

use app\BaseController;
use app\lib\Redis;
use app\model\AuthT;
use app\model\LogT;
use think\facade\Cache;
use think\Request;

class Index extends BaseController
{
    public function index(Request $request)
    {
        $config = [
            'host' => '121.37.255.12',
            'port' => 6379,
            'password' => 'waHqes-nijpi8-ruwqex'
        ];
        $redis = new \think\cache\driver\Redis($config);
        $redis->set('name', 'zml');
        echo $redis->get('name');


        //echo  array_sum(array_column([['price'=>1],['price'=>3]], 'price'));
    }

    public function hello($name = 'ThinkPHP6')
    {

        //  return 'hello,' . $name;
    }
}

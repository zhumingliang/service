<?php


namespace app\business;


use app\lib\exception\AuthException;
use app\lib\Redis;
use app\model\AuthT;
use think\facade\Cache;

class AuthBusiness
{
    public function checkAuth($appId, $token, $time)
    {
        $app = AuthT::auth($appId);
        if (!$app) {
            new AuthException(['msg' => '不存在该appid，请联系支付平台负责人申请开通']);
        }
        $data = [
            $time,
            $appId,
            $app->key
        ];
        //验证token是否已经使用
       /* $checkToken = Redis::instance()->hGet('token', $token);
        if ($checkToken) {
            new AuthException(['msg' => 'token已使用，不能重复使用']);
        }*/

        // 时间检验
        if ($app->expire + $this->time < time()) {
            new AuthException(['msg' => '请求token时间已过期，请重新生成token']);
        }
        if ($token != md5(implode($app->stitching_symbol, $data))) {
            new AuthException(['msg' => '不合法的请求，请检验token是否合法']);
        }
       // Redis::instance()->hSet('token', $token, time());
        return true;
    }

}
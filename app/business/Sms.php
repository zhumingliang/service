<?php

declare(strict_types=1);
namespace app\business;
use app\lib\Num;
use app\lib\ClassArr;
class Sms {
    public static function sendCode(string $phoneNumber, int $len, string $type = "ali") :bool{

        // 我们需要生成我们短信验证码 4位  6位
        $code = Num::getCode($len);
        $classStats = ClassArr::smsClassStat();
        $classObj = ClassArr::initClass($type, $classStats);
        $sms = $classObj::sendCode($phoneNumber, $code);
        if($sms) {
            // 需要把我们得短信验证码记录到redis 并且需要给出一个失效时间 1分钟
            // 2 redis服务
            cache(config("redis.code_pre").$phoneNumber, $code, config("redis.code_expire"));
        }

        return $sms;
    }


    public static function sendTemplate(string $phoneNumber,string $codeType,array $params,string $type = "ali") :bool{

        $classStats = ClassArr::smsClassStat();
        $classObj = ClassArr::initClass($type, $classStats);
        $sms = $classObj::sendTemplate($phoneNumber, $codeType,$params);
        return $sms;
    }
}
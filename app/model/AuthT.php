<?php


namespace app\model;


use app\lib\enum\CommonEnum;
use think\Model;

class AuthT extends Model
{

    public static function auth($app_id)
    {
        return self::where('app_id', $app_id)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->find();
    }
}
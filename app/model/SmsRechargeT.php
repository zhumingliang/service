<?php


namespace app\model;


use app\lib\enum\CommonEnum;
use think\Model;

class SmsRechargeT extends Model
{
    public static function recharge($id)
    {
        return self::where('id', $id)
            ->find();

    }

    public static function rechargeWithOrderNumber($order_number)
    {
        return self::where('order_number', $order_number)
            ->find();

    }

    public static function rechargeCount($sign)
    {

        $count = self::where('sign', $sign)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('status', 'paid')
            ->sum('count');
        return $count;

    }
}
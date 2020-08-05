<?php


namespace app\model;


use think\Model;

class SmsRechargeT extends Model
{
    public static function recharge($id)
    {
        return self::where('id', $id)
            ->find();

    }
}
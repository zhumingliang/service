<?php


namespace app\model;


use think\Model;

class SmsRecordT extends Model
{
    public static function sendCount($sign)
    {

        $count = self::where('sign', $sign)
          ->count('id');
        return $count;


    }


}
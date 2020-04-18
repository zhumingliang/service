<?php


namespace app\model;


use think\Model;

class LogT extends Model
{
    public static function saveInfo($content)
    {
        return self::create(['content' => $content]);

    }

}
<?php


namespace app\model;


use think\Model;

class AliSmsT extends Model
{
    public function config($sign)
    {
        return $this->where('sign',$sign)->where('state', 1)->find();
    }

}
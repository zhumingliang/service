<?php


namespace app\model;


use think\Model;

class AliSmsT extends Model
{
    public function config()
    {
        return $this->where('state', 1)->find();
    }

}
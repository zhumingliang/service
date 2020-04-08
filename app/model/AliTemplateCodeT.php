<?php


namespace app\model;


use think\Model;

class AliTemplateCodeT extends Model
{
    public  function code($type)
    {
        return $this->where('type', $type)
            ->find();
    }

}
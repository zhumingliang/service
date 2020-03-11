<?php
/**
 * Num 记录和数据相关的类库中的方法
 * Created by singwa
 * User: singwa
 * motto: 现在的努力是为了小时候吹过的牛逼！
 * Time: 00:57
 */
declare(strict_types=1);
namespace app\lib;


class Num {

    /**
     * @param int $len
     * @return int
     */
    public static function getCode(int $len = 4) : int{
        $code = rand(1000, 9999);
        if($len == 6) {
            $code = rand(100000, 999999);
        }

        return $code;
    }
}
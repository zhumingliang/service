<?php


namespace app\business;


use app\lib\ClassArr;

class PayBusiness
{

    public function unifiedOrder($appId, $payType, $data)
    {
        $payType = "weixin";
        $classStats = ClassArr::payClassStat();
        $classObj = ClassArr::initClass($payType, $classStats, [], true);
        try {
            $result = $classObj->unifiedOrder($data);
            print_r($result);
            return $result;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }

    }
}
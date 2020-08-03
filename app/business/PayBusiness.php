<?php


namespace app\business;


use app\lib\ClassArr;

class PayBusiness
{

    public function unifiedOrder($appId, $payType, $data)
    {
        $payType = "Weixin";
        $classStats = ClassArr::payClassStat();
        $classObj = ClassArr::initClass($payType, $classStats, [], true);
        try {
            $result = $classObj->unifiedOrder($data);
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }

    }
}
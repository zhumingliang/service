<?php


namespace app\controller;


use app\business\PayBusiness;
use app\lib\exception\SuccessMessage;
use app\lib\exception\SuccessMessageWithData;
use app\lib\exception\WeChatException;

class Pay extends AuthBase
{
    public function index()
    {


    }

    public function unifiedOrder()
    {
        $params = input("param.");
        try {
            $result = (new PayBusiness())->unifiedOrder($this->appId, $this->payType, $params);
        } catch (\Exception $e) {
            throw new WeChatException(['msg' => $e->getMessage()]);
        }
        if (!$result) {
            throw new WeChatException(['msg' => '下单失败']);
        }
        return json(new SuccessMessageWithData($result));
    }
}
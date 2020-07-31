<?php


namespace app\controller;


use app\BaseController;
use app\business\Sms as SmsBus;
use app\lib\enum\CommonEnum;
use app\lib\exception\SaveException;

use app\lib\exception\SuccessMessageWithData;

class Sms extends BaseController
{
    public function code()
    {
        $phoneNumber = input('param.phone_number', '', 'trim');
        if (SmsBus::sendCode($phoneNumber, 6, "ali")) {
            return json(new SuccessMessageWithData(['msg' => '发送验证码成功']));
        }
        return json(new SaveException(['msg' => '发送验证码失败']));
    }

    public function template()
    {
        $phoneNumber = input('param.phone_number', '', 'trim');
        $sign = input('param.sign', '', 'trim');
        $sign = empty($sign) ? 'ok' : $sign;
        $type = input('param.type', '', 'trim');
        $params = input('param.params', '', 'trim');
        if (SmsBus::sendTemplate($phoneNumber, $type, $params, 'ali', $sign) == CommonEnum::STATE_IS_OK) {
            return json(new SuccessMessageWithData(['msg' => '发送验证码成功']));
        }
        return json(new SaveException(['msg' => '发送验证码失败']));
    }

}
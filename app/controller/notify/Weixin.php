<?php
/**
 * Created by singwa
 * User: singwa
 * motto: 现在的努力是为了小时候吹过的牛逼！
 * Time: 22:37
 */

namespace app\controller\notify;

use app\api\model\PayWxT;
use app\api\service\WalletService;
use app\BaseController;
use app\model\LogT;
use app\model\SmsRechargeT;

class Weixin extends BaseController
{

    public function index()
    {
        LogT::saveInfo('111');
        $app = (new \app\lib\pay\Weixin())->getApp();
        $response = $app->handlePaidNotify(function ($message, $fail) {
            LogT::saveInfo(json_encode($message));

            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = SmsRechargeT::rechargeWithOrderNumber($message['out_trade_no']);

            if (!$order || $order->paid_at) { // 如果订单不存在 或者 订单已经支付过了
                return true;
            }

            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if ($message['result_code'] === 'SUCCESS') {
                    //保存支付记录
                    $data = [
                        'out_trade_no' => $message['out_trade_no'],
                        'openid' => $message['openid'],
                        'total_fee' => $message['total_fee'],
                        'transaction_id' => $message['transaction_id']
                    ];
                    \app\model\PayWxT::create($data);
                    $order->paid_at = time(); // 更新支付时间为当前时间
                    $order->status = 'paid';
                } elseif ($message['result_code'] === 'FAIL') {
                    // 用户支付失败
                    $order->status = 'paid_fail';
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            $order->save(); // 保存订单
            return true; // 返回处理完成
        });

        $response->send(); // return $response;
    }
}

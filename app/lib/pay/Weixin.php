<?php
/**
 * Created by singwa
 * User: singwa
 * motto: 现在的努力是为了小时候吹过的牛逼！
 * Time: 02:43
 */

namespace app\lib\pay;

use app\lib\exception\SaveException;
use app\lib\exception\WeChatException;
use app\lib\pay\weixin\lib\database\WxPayUnifiedOrder;
use app\lib\pay\weixin\lib\WxPayNativePay;
use EasyWeChat\Factory;
use think\Exception;

class Weixin implements PayBase
{

    private $app;

    public function __construct()
    {
        $config = [
            // 必要配置
            'app_id' => 'wxda0c645fd4945a61',
            'mch_id' => '1601561127',
            'key' => '3RcCkiD6QKSa9hAx9O2VMSS5USGITcaw',   // API 密钥
            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path' => 'cert.pem', // XXX: 绝对路径！！！！
            'key_path' => 'key.pem',      // XXX: 绝对路径！！！！
            //'sub_appid' => $sub_app_id,
            //'sub_mch_id' => $sub_mch_id,
            'notify_url' => 'http://service.tonglingok.com/notify/weixin',
            // 你也可以在下单时单独设置来想覆盖它
        ];
        $this->app = Factory::payment($config);


    }

    /**
     * 统一下单API
     */
    public function unifiedOrder($data)
    {
        try {
            //$app = $this->getApp($data['company_id']);
            /*  $result = $this->app->order->unify([
                  'body' => $data['body'],
                  'out_trade_no' => $data['out_trade_no'],
                  'total_fee' => $data['total_fee'],
                  'trade_type' => 'NATIVE',
                  'sign_type' => 'MD5',
                  //'openid' => $data['openid']
              ]);*/
            $result = $this->app->order->unify([
                'body' => "短信充值",
                'out_trade_no' => "Jk22GBsFcl1qc9wQka",
                'total_fee' => 1,
                'trade_type' => 'NATIVE',
                'sign_type' => 'MD5',
                //'openid' => $data['openid']
            ]);
            print_r($result);
            if ($result && isset($result['result_code'])
                && isset($result['return_code'])
                && $result['result_code'] == "SUCCESS"
                && $result['return_code'] == "SUCCESS"
            ) {
                print_r($result);
                $url = $result["code_url"];
                return request()->domain() . (string)url("qcode/index", ["data" => $url]);
            } else {
                throw new SaveException(['msg' => "下单失败，请稍候重试"]);
            }
        } catch (\Exception $e) {
            throw new WeChatException(['msg' => "对接微信支付内部异常"]);
        }
    }
}
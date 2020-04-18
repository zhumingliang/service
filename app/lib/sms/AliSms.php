<?php
/**
 * Created by singwa
 * User: singwa
 * motto: 现在的努力是为了小时候吹过的牛逼！
 * Time: 23:19
 */
declare(strict_types=1);

namespace app\lib\sms;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use app\lib\exception\ParameterException;
use app\model\AliSmsT;
use app\model\AliTemplateCodeT;
use think\facade\Log;
use think\tests\SessionTest;

class AliSms implements SmsBase
{
    private $host = '';
    private $regionId = '';
    private $signName = '';
    private $accessKeyId = '';
    private $accessKeySecret = '';
    private $templateCode = '';

    /**
     * 阿里云发送短信验证码的场景
     * @param string $phone
     * @param int $code
     * @return bool
     * @throws ClientException
     * @throws ServerException
     */
    public static function sendCode(string $phone, int $code): bool
    {
        if (empty($phone) || empty($code)) {
            return false;
        }


        AlibabaCloud::accessKeyClient(config("aliyun.access_key_id"),
            config("aliyun.access_key_secret"))
            ->regionId(config("aliyun.region_id"))
            ->asDefaultClient();

        $templateParam = [
            "code" => $code,
        ];
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host(config("aliyun.host"))
                ->options([
                    'query' => [
                        'RegionId' => config("aliyun.region_id"),
                        'PhoneNumbers' => $phone,
                        'SignName' => config("aliyun.sign_name"),
                        'TemplateCode' => config("aliyun.template_code"),
                        'TemplateParam' => json_encode($templateParam),
                    ],
                ])
                ->request();
            Log::info("alisms-sendCode-{$phone}result" . json_encode($result->toArray()));
        } catch (ClientException $e) {
            Log::error("alisms-sendCode-{$phone}ClientException" . $e->getErrorMessage());
            return false;
            //echo $e->getErrorMessage() . PHP_EOL;
        }
        if (isset($result['Code']) && $result['Code'] == "OK") {
            return true;
        }
        return false;

    }

    public static function sendTemplate(string $phone, string $type, array $params)
    {
        if (empty($phone) || empty($params) || empty($type)) {
            return false;
        }
        $config = (new AliSmsT())->config();
        if (empty($config)) {
            throw new ParameterException(['msg' => '配置参数异常']);
        }
        $host = $config->host;
        $regionId = $config->region_id;
        $signName = $config->sign_name;
        $accessKeyId = $config->access_key_id;
        $accessKeySecret = $config->access_key_secret;

        $template = (new AliTemplateCodeT())->code($type);
        if (empty($template)) {
            throw new ParameterException(['msg' => '配置参数异常,类别不存在']);
        }
        $templateCode = $template->template_code;


        AlibabaCloud::accessKeyClient($accessKeyId,
            $accessKeySecret)
            ->regionId($regionId)
            ->asDefaultClient();

        try {
            if (empty($params)){
                $result = AlibabaCloud::rpc()
                    ->product('Dysmsapi')
                    // ->scheme('https') // https | http
                    ->version('2017-05-25')
                    ->action('SendSms')
                    ->method('POST')
                    ->host($host)
                    ->options([
                        'query' => [
                            'RegionId' => $regionId,
                            'PhoneNumbers' => $phone,
                            'SignName' => $signName,
                            'TemplateCode' => $templateCode
                        ],
                    ])
                    ->request();
            }else{
                $result = AlibabaCloud::rpc()
                    ->product('Dysmsapi')
                    // ->scheme('https') // https | http
                    ->version('2017-05-25')
                    ->action('SendSms')
                    ->method('POST')
                    ->host($host)
                    ->options([
                        'query' => [
                            'RegionId' => $regionId,
                            'PhoneNumbers' => $phone,
                            'SignName' => $signName,
                            'TemplateCode' => $templateCode,
                            'TemplateParam' => json_encode($params),
                        ],
                    ])
                    ->request();
            }
            Log::info("alisms-sendCode-{$phone}result" . json_encode($result->toArray()));
        } catch (ClientException $e) {
            Log::error("alisms-sendCode-{$phone}ClientException" . $e->getErrorMessage());
            return false;
            //echo $e->getErrorMessage() . PHP_EOL;
        }
        if (isset($result['Code']) && $result['Code'] == "OK") {
            return true;
        }
        return false;
    }

}
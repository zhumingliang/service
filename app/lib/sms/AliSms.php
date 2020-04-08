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

    public function __construct(string $type)
    {
        $config = (new AliSmsT())->find();
        if (empty($config)) {
            throw new ParameterException(['msg' => '配置参数异常']);
        }
        $this->host = $config->host;
        $this->regionId = $config->region_id;
        $this->signName = $config->sign_name;
        $this->accessKeyId = $config->access_key_id;
        $this->accessKeySecret = $config->access_key_secret;
        $template = (new AliTemplateCodeT())->code($type);
        if (empty($template)) {
            throw new ParameterException(['msg' => '配置参数异常,类别不存在']);
        }
        $this->templateCode = $template->template_code;

    }

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

    public function sendTemplate(string $phone, string $type, string $params)
    {
        if (empty($phone) || empty($code)) {
            return false;
        }

        AlibabaCloud::accessKeyClient($this->accessKeyId,
            $this->accessKeySecret)
            ->regionId($this->regionId)
            ->asDefaultClient();

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
                        'RegionId' => $this->regionId,
                        'PhoneNumbers' => $phone,
                        'SignName' => $this->signName,
                        'TemplateCode' => $this->templateCode,
                        'TemplateParam' => $params,
                    ],
                ])
                ->request();
            print_r($result);
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
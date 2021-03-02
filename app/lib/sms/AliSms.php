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
use app\lib\enum\CommonEnum;
use app\lib\exception\ParameterException;
use app\model\AliSmsT;
use app\model\AliTemplateCodeT;
use app\model\LogT;
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
        } catch (ClientException $e) {
            Log::error("alisms-sendCode-{$phone}ClientException" . $e->getErrorMessage());
            return false;
        }
        if (isset($result['Code']) && $result['Code'] == "OK") {
            return true;
        }
        return false;

    }

    public static function sendTemplate(string $phone, string $type, array $params, string $sign)
    {
        if (empty($phone) || empty($params) || empty($type)) {
            return false;
        }
        $config = (new AliSmsT())->config($sign);
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
        $content = self::prefixContent($params, $template->template);
        AlibabaCloud::accessKeyClient($accessKeyId,
            $accessKeySecret)
            ->regionId($regionId)
            ->asDefaultClient();

        try {
            $query = [
                'RegionId' => $regionId,
                'PhoneNumbers' => $phone,
                'SignName' => $signName,
                'TemplateCode' => $templateCode
            ];
            if (!empty($params)) {
                $query['TemplateParam'] = json_encode($params);
            }
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host($host)
                ->options([
                    'query' => $query,
                ])
                ->request();
        } catch (ClientException $e) {
            Log::error("alisms-sendCode-{$phone}ClientException" . $e->getErrorMessage());
            throw new ParameterException(['msg' => '发送短信失败']);
        }
        if (isset($result['Code']) && $result['Code'] == "OK") {
            $state = CommonEnum::STATE_IS_OK;
        } else {
            LogT::saveInfo(json_encode($result));
            $state = CommonEnum::STATE_IS_FAIL;
        }
        return [
            'content' => $content,
            'state' => $state
        ];
    }

    private static function prefixContent($params, $template)
    {
        if (count($params)) {
            foreach ($params as $k => $v) {
                $template = str_replace('${' . $k . '}', $v, $template);
            }
        }
        return $template;
    }

}
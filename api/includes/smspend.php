<?php

// This file is auto-generated, don't edit it. Thanks.
namespace AlibabaCloud\SDK\Sample;

use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;
use AlibabaCloud\Tea\Utils\Utils;
use AlibabaCloud\Tea\Console\Console;
use \Exception;
use AlibabaCloud\Tea\Exception\TeaError;

use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;

class Sample {

    // 使用STS鉴权方式初始化账号Client
    public static function createClientWithSTS(){
        $config = new Config([
            // 必填，您的 AccessKey ID
            "accessKeyId" => ACCESS_KEY_ID,
            // 必填，您的 AccessKey Secret
            "accessKeySecret" => ACCESS_KEY_SECRET,
            // 必填，您的 Security Token
            "securityToken" => SECURITY_TOKEN,
            // 必填，表明使用 STS 方式
            "type" => "sts"
        ]);
        // Endpoint 请参考 https://api.aliyun.com/product/Dysmsapi
        $config->endpoint = "dysmsapi.aliyuncs.com";
        return new Dysmsapi($config);
    }

    // 短信发送配置
    public function sendSMS($code, $toPhone) {
        $client = self::createClientWithSTS();
        $sendSmsRequest = new SendSmsRequest([
            "signName" => "阿里云短信测试",// 短信签名
            "templateCode" => "SMS_154950909",// 短信模板
            "phoneNumbers" => $toPhone,// 短信接收者
            "templateParam" => "{\"code\":\"$code\"}"// 短信模板变量
        ]);
        $runtime = new RuntimeOptions([]);
        try {
            $resp = $client->sendSmsWithOptions($sendSmsRequest, $runtime);
            Console::log(Utils::toJSONString($resp));

            // 检查短信发送是否成功
            if ($resp->Message === "OK") {
                return true;
            } else {
                return false;
            }
        } catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
            }
            // 此处仅做打印展示，请谨慎对待异常处理，在工程项目中切勿直接忽略异常。
            // 错误 message
            var_dump($error->message);
            // 诊断地址
            var_dump($error->data["Recommend"]);
            Utils::assertAsString($error->message);
            return false;
        }
    }
}
$path = __DIR__ . \DIRECTORY_SEPARATOR . '..' . \DIRECTORY_SEPARATOR . 'vendor' . \DIRECTORY_SEPARATOR . 'autoload.php';
if (file_exists($path)) {
    require_once $path;
}
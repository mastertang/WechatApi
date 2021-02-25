<?php

namespace WechatApi\src\smsDirver;

use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Sms\V20190711\Models\SendSmsRequest;
use TencentCloud\Sms\V20190711\SmsClient;
use WechatApi\src\Common;

/**
 * Class sms
 * @package WechatApi\src\smsDirver
 */
class sms
{
    /**
     * @var string SecretId
     */
    public $secretId = "";

    /**
     * @var string SecretKey
     */
    public $secretKey = "";

    /**
     * @var string 发送的电话号码
     */
    public $phoneNumber = "";

    /**
     * @var string 模版id
     */
    public $templateId = "";

    /**
     * @var string $smsSdkAppId
     */
    public $smsSdkAppId = "";

    /**
     * @var string 短信签名
     */
    public $sign = "";

    /**
     * @var array 动态变量
     */
    public $templateParam = [];

    /**
     * @var string 发送结果消息
     */
    public $sendResultMessage = "";

    /**
     * 设置SecretId
     *
     * @param $secretId
     * @return $this
     */
    public function setSecretId($secretId)
    {
        $this->secretId = $secretId;
        return $this;
    }

    /**
     * 设置SecretKey
     *
     * @param $SecretKey
     * @return $this
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
        return $this;
    }

    /**
     * 设置phoneNumber
     *
     * @param $phoneNumber
     * @return $this
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * 设置TemplateId
     *
     * @param $templateId
     * @return $this
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
        return $this;
    }

    /**
     * 设置SmsSdkAppId
     *
     * @param $smsSdkAppId
     * @return $this
     */
    public function setSmsSdkAppId($smsSdkAppId)
    {
        $this->smsSdkAppId = $smsSdkAppId;
        return $this;
    }

    /**
     * 设置Sign
     *
     * @param $sign
     * @return $this
     */
    public function setSign($sign)
    {
        $this->sign = $sign;
        return $this;
    }

    /**
     * 设置TemplateParam
     *
     * @param $sign
     * @return $this
     */
    public function setTemplateParam($templateParam)
    {
        if (is_array($templateParam) && !empty($templateParam)) {
            $this->templateParam = $templateParam;
        }
        return $this;
    }

    /**
     * sms发送
     *
     * @return bool
     */
    public function sendSms()
    {
        try {
            $cred = new Credential($this->secretId, $this->secretKey);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("sms.tencentcloudapi.com");
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new SmsClient($cred, "", $clientProfile);
            $req = new SendSmsRequest();
            $params = array(
                "PhoneNumberSet" => [$this->phoneNumber],
                "TemplateParamSet" => $this->templateParam,
                "TemplateID" => $this->templateId,
                "SmsSdkAppid" => $this->smsSdkAppId,
                "Sign" => $this->sign
            );
            $req->fromJsonString(json_encode($params));
            $resp = $client->SendSms($req);
            $result = json_decode($resp->toJsonString(), true);
            if (!is_array($result) || empty($result)) {
                $this->sendResultMessage = $resp->toJsonString();
                return false;
            }
            if (isset($result['Response']['Error'])) {
                $this->sendResultMessage = $result['Response']['Error']['Code']
                    . ":" . $result['Response']['Error']['Message'];
                return false;
            }
            if (isset($result['Response']['SendStatusSet'][0])) {
                $this->sendResultMessage = $result['Response']['SendStatusSet'][0]['Message'];
                if ($result['Response']['SendStatusSet'][0]['Code'] = 'Ok') {
                    return true;
                } else {
                    return false;
                }
            }
            $this->sendResultMessage = $resp->toJsonString();
            return false;
        } catch (TencentCloudSDKException $exception) {
            $this->sendResultMessage = $exception->getMessage();
            return false;
        }
    }

}
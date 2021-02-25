<?php

namespace WechatApi\client;

use WechatApi\src\smsDirver\sms;

/**
 * Class SmsClient
 * @package WechatApi\client
 */
class SmsClient
{
    /**
     * 创建Sms短信基本功能类实例
     *
     * @return sms
     */
    public function smsClient()
    {
        return (new sms());
    }

    /**
     * 发送短信
     *
     * @param $secretId
     * @param $secretKey
     * @param $smsSdkAppId
     * @param $sign
     * @param $templateId
     * @param $phoneNUmber
     * @param array $templateParam
     * @param string $errorMessage
     * @return bool
     */
    public function sendSms(
        $secretId,
        $secretKey,
        $smsSdkAppId,
        $sign,
        $templateId,
        $phoneNUmber,
        $templateParam = [],
        &$errorMessage = "success"
    )
    {
        $sms = new sms();
        $result = $sms
            ->setSecretId($secretId)
            ->setSecretKey($secretKey)
            ->setSmsSdkAppId($smsSdkAppId)
            ->setSign($sign)
            ->setTemplateId($templateId)
            ->setTemplateParam($phoneNUmber)
            ->setPhoneNumber($templateParam)
            ->sendSms();
        if ($result === false) {
            $errorMessage = $sms->sendResultMessage;
            return false;
        }
        return $result;
    }
}
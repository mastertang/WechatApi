<?php

namespace WechatApi;

use WechatApi\client\SmsClient;

/**
 * Class SmsApi
 * @package WechatApi
 */
class SmsApi
{
    /**
     * @var SmsClient null Sms客户端类实例
     */
    public $sms = null;

    /**
     * 选择Sms客户端
     * @return SmsClient null|SmsClient
     */
    public function wechatWeb()
    {
        if ($this->sms instanceof SmsClient) {
            return $this->sms;
        }
        $this->sms = new SmsClient();
        return $this->sms;
    }
}
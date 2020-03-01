<?php

namespace WechatApi\src\wechatPay\dirver;

use WechatApi\src\wechatPay\lib\WxPayConfigInterface;

/**
 *
 * 该类需要业务自己继承， 该类只是作为deamon使用
 * 实际部署时，请务必保管自己的商户密钥，证书等
 *
 */
class WxPayConfig extends WxPayConfigInterface
{
    // appid
    public $appId       = "";
    
    // 商户号
    public $merchantId  = "";
    
    // 回调地址
    public $notifyUrl   = "";
    
    // 签名算法
    public $signType    = "HMAC-SHA256";
    
    // 代理地址
    public $proxyHost   = "0.0.0.0";
    
    // 代理地址端口
    public $proxyPort   = 0;
    
    // 报告等级
    public $reportLevel = 1;
    
    // 支付密钥
    public $payKey      = "";
    
    // appsecret
    public $appSecret   = "";
    
    // 证书路径
    public $sslCertPath = "";
    
    // 证书路径
    public $sslKeyPath  = "";

    //=======【基本信息设置】=====================================

    /**
     * TODO: 修改这里配置为您自己申请的商户信息
     * 微信公众号信息配置
     *
     * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
     *
     * MCHID：商户号（必须配置，开户邮件中可查看）
     *
     */
    public function GetAppId()
    {
        return $this->appId;
    }

    /**
     * 设置appI
     *
     * @param $appId
     * @return $this
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
        return $this;
    }

    /**
     * 获取商户号
     * @return string
     */
    public function GetMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * 设置商户号
     *
     * @param $merchantId
     * @return $this
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    //=======【支付相关配置：支付成功回调地址/签名方式】===================================

    /**
     * TODO:支付回调url
     * 签名和验证签名方式， 支持md5和sha256方式
     **/
    public function GetNotifyUrl()
    {
        return $this->notifyUrl;
    }

    /**
     * 设置支付回调url
     *
     * @param $notifyUrl
     * @return $this
     */
    public function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;
        return $this;
    }

    /**
     * 获取签名方法
     *
     * @return string
     */
    public function GetSignType()
    {
        return $this->signType;
    }

    /**
     * 设置签名方法
     *
     * @param $signType
     * @return $this
     */
    public function setSignType($signType)
    {
        $this->signType = $signType;
        return $this;
    }

    //=======【curl代理设置】===================================

    /**
     * 这里设置代理机器
     * @param $proxyHost
     * @param $proxyPort
     */
    public function GetProxy(&$proxyHost, &$proxyPort)
    {
        $proxyHost = $this->proxyHost;
        $proxyPort = $this->proxyPort;
    }

    /**
     * 设置代理地址
     *
     * @param $proxyHost
     * @param $proxyPort
     * @return $this
     */
    public function setProxy($proxyHost, $proxyPort)
    {
        $this->proxyHost = $proxyHost;
        $this->proxyPort = $proxyPort;
        return $this;
    }


    //=======【上报信息配置】===================================
    /**
     * 接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】
     *
     * @return int
     */
    public function GetReportLevenl()
    {
        return $this->reportLevel;
    }

    /**
     * 设置报告等级
     *
     * @param $level
     * @return $this
     */
    public function setReportLevenl($level)
    {
        $this->reportLevel = $level;
        return $this;
    }


    //=======【商户密钥信息-需要业务方继承】===================================
    /**
     * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）, 请妥善保管， 避免密钥泄露
     * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
     *
     * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置）， 请妥善保管， 避免密钥泄露
     * @return string
     */
    public function GetKey()
    {
        return $this->payKey;
    }

    /**
     * 设置支付密钥
     *
     * @param $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->payKey = $key;
        return $this;
    }

    /**
     * 获取appsecret
     *
     * @return mixed
     */
    public function GetAppSecret()
    {
        return $this->appSecret;
    }

    /**
     * 设置appsecret
     *
     * @param $appSecret
     * @return $this
     */
    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;
        return $this;
    }


    //=======【证书路径设置-需要业务方继承】=====================================
    /**
     * 设置商户证书路径
     *
     * @param $sslCertPath
     * @param $sslKeyPath
     */
    public function GetSSLCertPath(&$sslCertPath, &$sslKeyPath)
    {
        $sslCertPath = $this->sslCertPath;
        $sslKeyPath  = $this->sslKeyPath;
    }

    /**
     * 设置证书路径
     *
     * @param $sslCertPath
     * @param $sslKeyPath
     * @return $this
     */
    public function setSSLCertPath($sslCertPath, $sslKeyPath)
    {
        $this->sslKeyPath  = $sslKeyPath;
        $this->sslCertPath = $sslCertPath;
        return $this;
    }
}

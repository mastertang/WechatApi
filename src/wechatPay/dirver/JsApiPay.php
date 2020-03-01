<?php

namespace WechatApi\src\wechatPay\dirver;

use WechatApi\src\wechatPay\lib\WxPayApi;
use WechatApi\src\wechatPay\lib\WxPayException;
use WechatApi\src\wechatPay\lib\WxPayJsApiPay;
use WechatApi\src\wechatPay\lib\WxPayNotify;
use WechatApi\src\wechatPay\lib\WxPayOrderQuery;
use WechatApi\src\wechatPay\lib\WxPayUnifiedOrder;

/**
 *
 * JSAPI支付实现类
 * 该类实现了从微信公众平台获取code、通过code获取openid和access_token、
 * 生成jsapi支付js接口所需的参数、生成获取共享收货地址所需的参数
 *
 * 该类是微信支付提供的样例程序，商户可根据自己的需求修改，或者使用lib中的api自行开发
 *
 * @author widy
 *
 */
class JsApiPay extends WxPayNotify
{
    public $curl_timeout = 25;

    /**
     * @var WxPayUnifiedOrder null
     */
    public $wxPayUnifiedOrder = null;

    /**
     * @var WxPayConfig null
     */
    public $wxPayConfig = null;

    /**
     * @var null 回调数据
     */
    public $notifyData = null;

    /**
     * @var null accessToken
     */
    public $accessToken = null;

    /**
     * @var null 当前发起支付的页面地址
     */
    public $payUrl = null;

    /**
     * @var null 共享地址
     */
    public $editAddress = null;

    /**
     * 设置accessToken
     *
     * @param $accessToken
     * @return $this
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * 设置现在支付网页地址
     *
     * @param $url
     * @return $this
     */
    public function setNowPayUrl($url)
    {
        $this->payUrl = $url;
        return $this;
    }

    /**
     * 设置WxPayUifiedOrder
     *
     * @param $wxPayUifiedOrder
     * @return $this
     */
    public function setWxPayUifiedOrder($wxPayUifiedOrder)
    {
        $this->wxPayUnifiedOrder = $wxPayUifiedOrder;
        return $this;
    }

    /**
     * 设置WxPayConfig
     *
     * @param $wxPayConfig
     * @return $this
     */
    public function setWxPayConfig($wxPayConfig)
    {
        $this->wxPayConfig = $wxPayConfig;
        return $this;
    }

    /**
     * 获取jsapi支付的参数
     *
     * @param $UnifiedOrderResult
     * @return bool|string|void
     */
    public function GetJsApiParameters($UnifiedOrderResult)
    {
        try {
            if (!array_key_exists("appid", $UnifiedOrderResult)
                || !array_key_exists("prepay_id", $UnifiedOrderResult)
                || $UnifiedOrderResult['prepay_id'] == "") {
                $this->errorMessage = "参数错误!";
                return;
            }

            $jsapi = new WxPayJsApiPay();
            $jsapi->SetAppid($UnifiedOrderResult["appid"]);
            $timeStamp = time();
            $jsapi->SetTimeStamp("$timeStamp");
            $jsapi->SetNonceStr(WxPayApi::getNonceStr());
            $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);

            $config = new WxPayConfig();
            $jsapi->SetPaySign($jsapi->MakeSign($config));
            $parameters = json_encode($jsapi->GetValues());
            return $parameters;
        } catch (\Exception $exception) {
            $this->errorMessage = $exception->getMessage();
            return false;
        }
    }

    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     *
     * @return string 返回已经拼接好的字符串
     */
    public function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
     *
     * @return string
     */
    public function GetEditAddressParameters()
    {
        $data                = array();
        $data["appid"]       = $this->wxPayConfig->GetAppId();
        $data["url"]         = $this->payUrl;
        $time                = time();
        $data["timestamp"]   = "$time";
        $data["noncestr"]    = WxPayApi::getNonceStr();
        $data["accesstoken"] = $this->accessToken;
        ksort($data);
        $params   = $this->ToUrlParams($data);
        $addrSign = sha1($params);

        $afterData  = array(
            "addrSign"  => $addrSign,
            "signType"  => "sha1",
            "scope"     => "jsapi_address",
            "appId"     => $this->wxPayConfig->GetAppId(),
            "timeStamp" => $data["timestamp"],
            "nonceStr"  => $data["noncestr"]
        );
        $parameters = json_encode($afterData);
        return $parameters;
    }

    /**
     * js支付
     *
     * @return bool|string
     */
    public function jsPay()
    {
        try {
            $order           = WxPayApi::unifiedOrder($this->wxPayConfig, $this->wxPayUnifiedOrder);
            $jsApiParameters = $this->GetJsApiParameters($order);
            //获取共享收货地址js函数参数
            $this->editAddress = $this->GetEditAddressParameters();
            return $jsApiParameters;
        } catch (\Exception $exception) {
            $this->errorMessage = $exception->getMessage();
            return false;
        }

    }

    /**
     * 扫描回调
     */
    public function jsNotify()
    {
        $result = $this->Handle($this->wxPayConfig, false);
        if ($result) {
            return $this->notifyData;
        }
        return $result;
    }

    /**
     * 查询订单
     *
     * @param $transactionId
     * @return bool
     */
    public function queryOrder($transactionId)
    {
        try {
            $input = new WxPayOrderQuery();
            $input->SetTransaction_id($transactionId);
            $result = WxPayApi::orderQuery($this->wxPayConfig, $input);
            if (array_key_exists("return_code", $result)
                && array_key_exists("result_code", $result)
                && $result["return_code"] == "SUCCESS"
                && $result["result_code"] == "SUCCESS") {
                return true;
            }
            return false;
        } catch (\Exception $exception) {
            $this->errorMessage = $exception->getMessage();
            return false;
        }
    }

    /**
     * 重写
     *
     * @param \WechatApi\src\wechatPay\lib\WxPayNotifyResults $objData
     * @param \WechatApi\src\wechatPay\lib\WxPayConfigInterface $config
     * @param string $msg
     * @return bool
     */
    public function NotifyProcess($objData, $config, &$msg)
    {
        $data = $this->notifyData = $objData->GetValues();
        //TODO 1、进行参数校验
        if (!array_key_exists("return_code", $data)
            || (array_key_exists("return_code", $data) && $data['return_code'] != "SUCCESS")) {
            //TODO失败,不是支付成功的通知
            $msg = "回调数据异常";
            return false;
        }
        if (!array_key_exists("transaction_id", $data)) {
            $msg = "输入参数不正确";
            return false;
        }
        try {
            $checkResult = $objData->CheckSign($config);
            if ($checkResult == false) {
                //签名错误
                $msg = "签名错误";
                return false;
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return false;
        }

        //查询订单，判断订单真实性
        if (!$this->Queryorder($data["transaction_id"])) {
            $msg = "订单查询失败";
            return false;
        }
        return true;
    }
}

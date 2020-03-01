<?php

namespace WechatApi\src\wechatPay\dirver;

use WechatApi\src\wechatPay\lib\WxPayApi;
use WechatApi\src\wechatPay\lib\WxPayBizPayUrl;
use WechatApi\src\wechatPay\lib\WxPayNotify;
use WechatApi\src\wechatPay\lib\WxPayOrderQuery;
use WechatApi\src\wechatPay\lib\WxPayUnifiedOrder;

/**
 *
 * 刷卡支付实现类
 * @author widyhu
 *
 */
class NativePay extends WxPayNotify
{
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
     * 生成扫描支付URL,模式一
     *
     * @param $productId
     * @return string
     */
    public function GetPrePayUrl($productId)
    {
        $biz = new WxPayBizPayUrl();
        $biz->SetProduct_id($productId);
        try {
            $values = WxPayApi::bizpayurl($this->wxPayConfig, $biz);
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
        $url = "weixin://wxpay/bizpayurl?" . $this->ToUrlParams($values);
        return $url;
    }

    /**
     * 参数数组转换为url参数
     *
     * @param $urlObj
     * @return string
     */
    public function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            $buff .= $k . "=" . $v . "&";
        }
        $buff = trim($buff, "&");
        return $buff;
    }


    /**
     * 生成直接支付url，支付url有效期为2小时,模式二
     *
     * @param $input
     * @return bool|\WechatApi\src\wechatPay\lib\成功时返回，其他抛异常
     */
    public function GetPayUrl($input)
    {
        if ($input->GetTrade_type() == "NATIVE") {
            try {
                $result = WxPayApi::unifiedOrder($this->wxPayConfig, $input);
                return $result;
            } catch (\Exception $e) {
                $this->errorMessage = $e->getMessage();
                return false;
            }
        }
        return false;
    }

    /**
     * 查询订单
     *
     * @param $transactionId
     * @return bool
     * @throws \WechatApi\src\wechatPay\lib\WxPayException
     */
    public function queryOrder($transactionId)
    {
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
    }

    /**
     * 扫码支付
     *
     * @return bool
     */
    public function scan()
    {
        $result = $this->GetPayUrl($this->wxPayUnifiedOrder);
        if (!$result) {
            return false;
        } else {
            $url = $result["code_url"];
            require_once "phpqrcode.php";
            $qrcodeData = (new \QRencode())->encodeRAW($url);
            return $qrcodeData;
        }
    }

    /**
     * 扫描回调
     */
    public function scanNotify()
    {
        $result = $this->Handle($this->wxPayConfig, false);
        if ($result) {
            return $this->notifyData;
        }
        return $result;
    }

    /**
     * 重写
     *
     * @param \WechatApi\src\wechatPay\lib\WxPayNotifyResults $objData
     * @param \WechatApi\src\wechatPay\lib\WxPayConfigInterface $config
     * @param string $msg
     * @return bool|\WechatApi\src\wechatPay\lib\true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     * @throws \WechatApi\src\wechatPay\lib\WxPayException
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
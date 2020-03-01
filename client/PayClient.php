<?php

namespace WechatApi\client;

use WechatApi\src\wechatPay\dirver\JsApiPay;
use WechatApi\src\wechatPay\dirver\MicroPay;
use WechatApi\src\wechatPay\dirver\NativePay;
use WechatApi\src\wechatPay\dirver\OrdersOperation;
use WechatApi\src\wechatPay\dirver\WxPayConfig;
use WechatApi\src\wechatPay\lib\WxPayDownloadBill;
use WechatApi\src\wechatPay\lib\WxPayMicroPay;
use WechatApi\src\wechatPay\lib\WxPayOrderQuery;
use WechatApi\src\wechatPay\lib\WxPayRefund;
use WechatApi\src\wechatPay\lib\WxPayRefundQuery;
use WechatApi\src\wechatPay\lib\WxPayUnifiedOrder;

/**
 * Class PayClient
 * @package WechatApi\client
 */
class PayClient
{
    /**
     * @var WxPayConfig null
     */
    public $wxPayConfig = null;

    /**
     * @var string 错误信息
     */
    public $errorMessage = "";

    /**
     * 设置全局配置
     *
     * @param $appId
     * @param $appSecert
     * @param $merchantId 商户号
     * @param $payKey 支付密钥
     * @param $notifyUrl 回调
     * @param string $proxyHost 代理地址
     * @param string $proxyPort 代理端口
     * @param int $reportLevel 上报等级，1，2，3
     * @param string $sslCertPath 证书路径
     * @param string $sslKeyPath
     * @return $this
     */
    public function setNormalConfig(
        $appId,
        $appSecert,
        $merchantId,
        $payKey,
        $notifyUrl,
        $proxyHost = "",
        $proxyPort = "",
        $reportLevel = "",
        $sslCertPath = "",
        $sslKeyPath = ""
    )
    {
        $wxPayConfig = (new WxPayConfig())
            ->setAppId($appId)
            ->setAppSecret($appSecert)
            ->setMerchantId($merchantId)
            ->setKey($payKey)
            ->setNotifyUrl($notifyUrl);
        if ($proxyHost !== "" && $proxyPort !== "") {
            $wxPayConfig->setProxy($proxyHost, $proxyPort);
        }
        if ($reportLevel !== "") {
            $wxPayConfig->setReportLevenl($reportLevel);
        }
        if ($sslCertPath !== "" && $sslKeyPath !== "") {
            $wxPayConfig->setSSLCertPath($sslCertPath, $sslCertPath);
        }
        $this->wxPayConfig = $wxPayConfig;
        return $this;
    }

    /**
     * 扫描支付
     *
     * @param $body
     * @param $outTradeNo
     * @param $totalFee
     * @param $productId
     * @param string $attach
     * @param string $goodsTag
     * @param string $timeStart
     * @param string $timeExpire
     * @return bool
     */
    public function scanPay(
        $body,
        $outTradeNo,
        $totalFee,
        $productId,
        $attach = "",
        $goodsTag = "",
        $timeStart = "",
        $timeExpire = ""
    )
    {
        if (empty($this->wxPayConfig)) {
            $this->errorMessage = "请先设置支付配置";
            return false;
        }
        $wxPayUnifiedOrder = new WxPayUnifiedOrder();
        $wxPayUnifiedOrder->SetBody($body);
        $wxPayUnifiedOrder->SetOut_trade_no($outTradeNo);
        $wxPayUnifiedOrder->SetTotal_fee($totalFee);
        $wxPayUnifiedOrder->SetNotify_url($this->wxPayConfig->GetNotifyUrl());
        $wxPayUnifiedOrder->SetTrade_type("NATIVE");
        $wxPayUnifiedOrder->SetProduct_id($productId);

        if ($attach !== "") {
            $wxPayUnifiedOrder->SetAttach($attach);
        }
        if ($timeStart !== "") {
            $wxPayUnifiedOrder->SetTime_start($timeStart);
        }
        if ($timeExpire !== "") {
            $wxPayUnifiedOrder->SetTime_expire($timeExpire);
        }
        if ($goodsTag !== "") {
            $wxPayUnifiedOrder->SetGoods_tag($goodsTag);
        }


        $nativePay = (new NativePay())
            ->setWxPayConfig($this->wxPayConfig)
            ->setWxPayUifiedOrder($wxPayUnifiedOrder);
        $result    = $nativePay->scan();
        if ($result === false) {
            $this->errorMessage = $nativePay->errorMessage;
            return false;
        }
        return $result;
    }

    /**
     * 扫描回调
     *
     * @return bool|null
     */
    public function scanNotify()
    {
        if (empty($this->wxPayConfig)) {
            $this->errorMessage = "请先设置支付配置";
            return false;
        }
        $nativePay = new NativePay();
        $result    = $nativePay->scanNotify();
        if ($result === false) {
            $this->errorMessage = $nativePay->errorMessage;
            return false;
        }
        return $nativePay->notifyData;
    }

    /**
     * js支付
     *
     * @param $body
     * @param $outTradeNo
     * @param $totalFee
     * @param $productId
     * @param $openId
     * @param $accessToken
     * @param $nowPayUrl
     * @param string $attach
     * @param string $goodsTag
     * @param string $timeStart
     * @param string $timeExpire
     * @return array|bool
     */
    public function jsPay(
        $body,
        $outTradeNo,
        $totalFee,
        $productId,
        $openId,
        $accessToken,
        $nowPayUrl,
        $attach = "",
        $goodsTag = "",
        $timeStart = "",
        $timeExpire = ""
    )
    {
        if (empty($this->wxPayConfig)) {
            $this->errorMessage = "请先设置支付配置";
            return false;
        }
        $wxPayUnifiedOrder = new WxPayUnifiedOrder();
        $wxPayUnifiedOrder->SetBody($body);
        $wxPayUnifiedOrder->SetOut_trade_no($outTradeNo);
        $wxPayUnifiedOrder->SetTotal_fee($totalFee);
        $wxPayUnifiedOrder->SetNotify_url($this->wxPayConfig->GetNotifyUrl());
        $wxPayUnifiedOrder->SetTrade_type("JSAPI");
        $wxPayUnifiedOrder->SetProduct_id($productId);
        $wxPayUnifiedOrder->SetOpenid($openId);

        if ($attach !== "") {
            $wxPayUnifiedOrder->SetAttach($attach);
        }
        if ($timeStart !== "") {
            $wxPayUnifiedOrder->SetTime_start($timeStart);
        }
        if ($timeExpire !== "") {
            $wxPayUnifiedOrder->SetTime_expire($timeExpire);
        }
        if ($goodsTag !== "") {
            $wxPayUnifiedOrder->SetGoods_tag($goodsTag);
        }

        $jsPay  = (new JsApiPay())
            ->setAccessToken($accessToken)
            ->setNowPayUrl($nowPayUrl)
            ->setWxPayConfig($this->wxPayConfig)
            ->setWxPayUifiedOrder($wxPayUnifiedOrder);
        $result = $jsPay->jsPay();
        if ($result === false) {
            $this->errorMessage = $jsPay->errorMessage;
            return false;
        }
        return [
            "jsApiParameters" => $result,
            "editAddress"     => $jsPay->editAddress
        ];
    }

    /**
     * js支付回调
     *
     * @return bool|null
     */
    public function jsNotify()
    {
        if (empty($this->wxPayConfig)) {
            $this->errorMessage = "请先设置支付配置";
            return false;
        }
        $jsPay  = new JsApiPay();
        $result = $jsPay->jsNotify();
        if ($result === false) {
            $this->errorMessage = $jsPay->errorMessage;
            return false;
        }
        return $jsPay->notifyData;
    }

    /**
     * 刷卡支付
     *
     * @param $authCode
     * @param $body
     * @param $outTradeNo
     * @param $totalFee
     * @param string $queryTimes
     * @return bool|\WechatApi\src\wechatPay\lib\成功时返回，其他抛异常
     * @throws \WechatApi\src\wechatPay\lib\WxPayException
     */
    public function microPay(
        $authCode,
        $body,
        $outTradeNo,
        $totalFee,
        $queryTimes = ""
    )
    {
        if (empty($this->wxPayConfig)) {
            $this->errorMessage = "请先设置支付配置";
            return false;
        }
        $wxPayMicroPay = new WxPayMicroPay();
        $wxPayMicroPay->SetBody($body);
        $wxPayMicroPay->SetOut_trade_no($outTradeNo);
        $wxPayMicroPay->SetTotal_fee($totalFee);
        $wxPayMicroPay->SetAuth_code($authCode);

        $microPay = (new MicroPay())
            ->setWxMicroPay($this->wxPayConfig)
            ->setWxMicroPay($wxPayMicroPay);
        if ($queryTimes !== "") {
            $microPay->setQueryTimes($queryTimes);
        }
        $result = $microPay->pay();
        if ($result === false) {
            $this->errorMessage = $microPay->errorMessage;
            return false;
        }
        return $result;
    }

    /**
     * 微信订单号查询订单
     *
     * @param $transactionId
     * @return array|bool
     */
    public function orderQueryTransactionId($transactionId)
    {
        if (empty($this->wxPayConfig)) {
            $this->errorMessage = "请先设置支付配置";
            return false;
        }
        $wxPayOrderQuery = new WxPayOrderQuery();
        $wxPayOrderQuery->SetTransaction_id($transactionId);

        $ordersOperation = (new OrdersOperation())
            ->setWxPayConfig($this->wxPayConfig)
            ->setWxPayOrderQuery($wxPayOrderQuery);
        $result          = $ordersOperation->orderQuery();
        if ($result === false) {
            $this->errorMessage = $ordersOperation->errorMessage;
            return false;
        }
        return $result;
    }

    /**
     * 商户单号查询订单
     *
     * @param $outTradeNo
     * @return array|bool
     */
    public function orderQueryOutTradeNo($outTradeNo)
    {
        if (empty($this->wxPayConfig)) {
            $this->errorMessage = "请先设置支付配置";
            return false;
        }
        $wxPayOrderQuery = new WxPayOrderQuery();
        $wxPayOrderQuery->SetOut_trade_no($outTradeNo);

        $ordersOperation = (new OrdersOperation())
            ->setWxPayConfig($this->wxPayConfig)
            ->setWxPayOrderQuery($wxPayOrderQuery);
        $result          = $ordersOperation->orderQuery();
        if ($result === false) {
            $this->errorMessage = $ordersOperation->errorMessage;
            return false;
        }
        return $result;
    }

    /**
     * 微信订单号退款
     *
     * @param $transactionId
     * @param $totalFee
     * @param $refundFee
     * @param $outRefundNo
     * @return bool|\WechatApi\src\wechatPay\lib\成功时返回，其他抛异常
     */
    public function refundTransactionTd(
        $transactionId,
        $totalFee,
        $refundFee,
        $outRefundNo
    )
    {
        if (empty($this->wxPayConfig)) {
            $this->errorMessage = "请先设置支付配置";
            return false;
        }
        $wxPayRefund = new WxPayRefund();
        $wxPayRefund->SetTransaction_id($transactionId);
        $wxPayRefund->SetTotal_fee($totalFee);
        $wxPayRefund->SetRefund_fee($refundFee);
        $wxPayRefund->SetOut_refund_no($outRefundNo);
        $wxPayRefund->SetOp_user_id($this->wxPayConfig->GetMerchantId());

        $ordersOperation = (new OrdersOperation())
            ->setWxPayConfig($this->wxPayConfig)
            ->setWxPayRefund($wxPayRefund);
        $result          = $ordersOperation->refund();
        if ($result === false) {
            $this->errorMessage = $ordersOperation->errorMessage;
            return false;
        }
        return $result;
    }

    /**
     * 商户订单号退款
     *
     * @param $outTradeNo
     * @param $totalFee
     * @param $refundFee
     * @param $outRefundNo
     * @return bool|\WechatApi\src\wechatPay\lib\成功时返回，其他抛异常
     */
    public function refundOutTradeNo(
        $outTradeNo,
        $totalFee,
        $refundFee,
        $outRefundNo
    )
    {
        if (empty($this->wxPayConfig)) {
            $this->errorMessage = "请先设置支付配置";
            return false;
        }
        $wxPayRefund = new WxPayRefund();
        $wxPayRefund->SetOut_trade_no($outTradeNo);
        $wxPayRefund->SetTotal_fee($totalFee);
        $wxPayRefund->SetRefund_fee($refundFee);
        $wxPayRefund->SetOut_refund_no($outRefundNo);
        $wxPayRefund->SetOp_user_id($this->wxPayConfig->GetMerchantId());

        $ordersOperation = (new OrdersOperation())
            ->setWxPayConfig($this->wxPayConfig)
            ->setWxPayRefund($wxPayRefund);
        $result          = $ordersOperation->refund();
        if ($result === false) {
            $this->errorMessage = $ordersOperation->errorMessage;
            return false;
        }
        return $result;
    }

    /**
     * 商户订单号退款订单查询
     *
     * @param $outTradeNo
     * @return bool|\WechatApi\src\wechatPay\lib\成功时返回，其他抛异常
     */
    public function refundQueryOutTradeNo($outTradeNo)
    {
        if (empty($this->wxPayConfig)) {
            $this->errorMessage = "请先设置支付配置";
            return false;
        }
        $wxPayRefundQuery = new WxPayRefundQuery();
        $wxPayRefundQuery->SetOut_trade_no($outTradeNo);

        $ordersOperation = (new OrdersOperation())
            ->setWxPayConfig($this->wxPayConfig)
            ->setWxPayRefundQuery($wxPayRefundQuery);
        $result          = $ordersOperation->refundOrderQuery();
        if ($result === false) {
            $this->errorMessage = $ordersOperation->errorMessage;
            return false;
        }
        return $result;
    }

    /**
     * 微信订单号退款订单查询
     *
     * @param $transactionId
     * @return bool|\WechatApi\src\wechatPay\lib\成功时返回，其他抛异常
     */
    public function refundQueryTransactionId($transactionId)
    {
        if (empty($this->wxPayConfig)) {
            $this->errorMessage = "请先设置支付配置";
            return false;
        }
        $wxPayRefundQuery = new WxPayRefundQuery();
        $wxPayRefundQuery->SetTransaction_id($transactionId);

        $ordersOperation = (new OrdersOperation())
            ->setWxPayConfig($this->wxPayConfig)
            ->setWxPayRefundQuery($wxPayRefundQuery);
        $result          = $ordersOperation->refundOrderQuery();
        if ($result === false) {
            $this->errorMessage = $ordersOperation->errorMessage;
            return false;
        }
        return $result;
    }

    /**
     * 商户退款订单号退款订单查询
     *
     * @param $outRefundNo
     * @return bool|\WechatApi\src\wechatPay\lib\成功时返回，其他抛异常
     */
    public function refundQueryOutRefundNo($outRefundNo)
    {
        if (empty($this->wxPayConfig)) {
            $this->errorMessage = "请先设置支付配置";
            return false;
        }
        $wxPayRefundQuery = new WxPayRefundQuery();
        $wxPayRefundQuery->SetOut_refund_no($outRefundNo);

        $ordersOperation = (new OrdersOperation())
            ->setWxPayConfig($this->wxPayConfig)
            ->setWxPayRefundQuery($wxPayRefundQuery);
        $result          = $ordersOperation->refundOrderQuery();
        if ($result === false) {
            $this->errorMessage = $ordersOperation->errorMessage;
            return false;
        }
        return $result;
    }

    /**
     * 退款订单id退款订单查询
     *
     * @param $refundId
     * @return bool|\WechatApi\src\wechatPay\lib\成功时返回，其他抛异常
     */
    public function refundQueryRefundId($refundId)
    {
        if (empty($this->wxPayConfig)) {
            $this->errorMessage = "请先设置支付配置";
            return false;
        }
        $wxPayRefundQuery = new WxPayRefundQuery();
        $wxPayRefundQuery->SetRefund_id($refundId);

        $ordersOperation = (new OrdersOperation())
            ->setWxPayConfig($this->wxPayConfig)
            ->setWxPayRefundQuery($wxPayRefundQuery);
        $result          = $ordersOperation->refundOrderQuery();
        if ($result === false) {
            $this->errorMessage = $ordersOperation->errorMessage;
            return false;
        }
        return $result;
    }

    /**
     * 下载账单
     *
     * @param $billType ALL | SUCCESS | REFUND | REVOKED
     * @param $billDate
     * @return bool|\WechatApi\src\wechatPay\lib\成功时返回，其他抛异常
     */
    public function orderDownload($billType, $billDate)
    {
        if (empty($this->wxPayConfig)) {
            $this->errorMessage = "请先设置支付配置";
            return false;
        }
        $wxPayDownloadBill = new WxPayDownloadBill();
        $wxPayDownloadBill->SetBill_type($billType);
        $wxPayDownloadBill->SetBill_date($billDate);

        $ordersOperation = (new OrdersOperation())
            ->setWxPayConfig($this->wxPayConfig)
            ->setWxPayDownloadBill($wxPayDownloadBill);
        $result          = $ordersOperation->downBill();
        if ($result === false) {
            $this->errorMessage = $ordersOperation->errorMessage;
            return false;
        }
        return $result;
    }
}
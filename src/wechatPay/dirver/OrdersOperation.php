<?php

namespace WechatApi\src\wechatPay\dirver;

use WechatApi\src\wechatPay\lib\WxPayApi;
use WechatApi\src\wechatPay\lib\WxPayDownloadBill;
use WechatApi\src\wechatPay\lib\WxPayOrderQuery;
use WechatApi\src\wechatPay\lib\WxPayRefund;
use WechatApi\src\wechatPay\lib\WxPayRefundQuery;

class OrdersOperation
{
    /**
     * @var WxPayConfig null
     */
    public $wxPayConfig = null;

    /**
     * @var WxPayOrderQuery null
     */
    public $wxPayOrderQuery = null;

    /**
     * @var WxPayRefundQuery null
     */
    public $wxPayRefundQuery = null;

    /**
     * @var WxPayRefund null
     */
    public $wxPayRefund = null;

    /**
     * @var WxPayDownloadBill null
     */
    public $wxPayDownloadBill = null;

    /**
     * @var null 错误信息
     */
    public $errorMessage = null;

    /**
     * 设置wxPayOrderQuery
     *
     * @param $wxPayOrderQuery
     * @return $this
     */
    public function setWxPayOrderQuery($wxPayOrderQuery)
    {
        $this->wxPayOrderQuery = $wxPayOrderQuery;
        return $this;
    }

    /**
     * 设置wxPayRefundQuery
     *
     * @param $wxPayRefundQuery
     * @return $this
     */
    public function setWxPayRefundQuery($wxPayRefundQuery)
    {
        $this->wxPayRefundQuery = $wxPayRefundQuery;
        return $this;
    }

    /**
     * 设置wxPayRefund
     *
     * @param $wxPayRefund
     * @return $this
     */
    public function setWxPayRefund($wxPayRefund)
    {
        $this->wxPayRefund = $wxPayRefund;
        return $this;
    }

    /**
     * 设置wxPayDownloadBill
     *
     * @param $wxPayDownloadBill
     * @return $this
     */
    public function setWxPayDownloadBill($wxPayDownloadBill)
    {
        $this->wxPayDownloadBill = $wxPayDownloadBill;
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
     * 订单查询
     *
     * @return array|bool
     */
    public function orderQuery()
    {
        try {
            return WxPayApi::orderQuery($this->wxPayConfig, $this->wxPayOrderQuery);
        } catch (\Exception $exception) {
            $this->errorMessage = $exception->getMessage();
            return false;
        }
    }

    /**
     * 退款订单查询
     *
     * @return bool|\WechatApi\src\wechatPay\lib\成功时返回，其他抛异常
     */
    public function refundOrderQuery()
    {
        try {
            return WxPayApi::refundQuery($this->wxPayConfig, $this->wxPayRefundQuery);
        } catch (\Exception $exception) {
            $this->errorMessage = $exception->getMessage();
            return false;
        }
    }

    /**
     * 退款
     *
     * @return bool|\WechatApi\src\wechatPay\lib\成功时返回，其他抛异常
     */
    public function refund()
    {
        try {
            return WxPayApi::refund($this->wxPayConfig, $this->wxPayRefund);
        } catch (\Exception $exception) {
            $this->errorMessage = $exception->getMessage();
            return false;
        }
    }

    /**
     * 下载账单
     *
     * @return bool|\WechatApi\src\wechatPay\lib\成功时返回，其他抛异常
     */
    public function downBill()
    {
        try {
            $file =  WxPayApi::downloadBill($this->wxPayConfig, $this->wxPayDownloadBill);
            return htmlspecialchars($file, ENT_QUOTES);
        } catch (\Exception $exception) {
            $this->errorMessage = $exception->getMessage();
            return false;
        }
    }
}

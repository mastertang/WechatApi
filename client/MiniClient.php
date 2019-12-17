<?php

namespace WechatApi\client;

use WechatApi\src\miniDirver\MiniExtend;
use WechatApi\src\miniDirver\MiniNormal;

/**
 * Class MiniClient
 * @package WechatApi\client
 */
class MiniClient
{
    /**
     * 创建小程序基本功能类实例
     *
     * @return MiniNormal
     */
    public function miniNormalClient()
    {
        return (new MiniNormal());
    }

    /**
     * 创建小程序扩展功能类实例
     *
     * @return MiniExtend
     */
    public function miniExtendClient()
    {
        return (new MiniExtend());
    }

    /**
     *
     * 获取小程序openid
     *
     * @param $appId
     * @param $appSecret
     * @param $jsCode
     * @param string $errorMessage
     * @return bool|mixed
     */
    public function miniOpenid(
        $appId,
        $appSecret,
        $jsCode,
        &$errorMessage = "success"
    )
    {
        $miniNormal = new MiniNormal();
        $result     = $miniNormal
            ->appId($appId)
            ->appSecret($appSecret)
            ->jsCode($jsCode)
            ->getMiniOpenid();
        if ($result === false) {
            $errorMessage = $miniNormal->getMiniOpenidResult;
            return false;
        }
        return $result;
    }

    /**
     * 获取小程序的accessToken
     *
     * @param $appId
     * @param $appSecret
     * @param string $errorMessage
     * @return array|bool
     */
    public function miniAccessToken(
        $appId,
        $appSecret,
        &$errorMessage = "success"
    )
    {
        $miniNormal = new MiniNormal();
        $result     = $miniNormal
            ->appId($appId)
            ->appSecret($appSecret)
            ->getMiniToken();
        if ($result === false) {
            $errorMessage = $miniNormal->getMiniTokenResult;
            return false;
        }
        return $result;
    }

    /**
     * 生成小程序二维码
     *
     * @param $appId
     * @param $appSecret
     * @param string $scene
     * @param string $page
     * @param int $width
     * @param string $errorMessage
     * @return bool|mixed
     */
    public function miniCreateQrCode(
        $appId,
        $appSecret,
        &$errorMessage = "success",
        $scene = "",
        $page = "",
        $width = 430
    )
    {
        $miniExtend = new MiniExtend();
        $result     = $miniExtend
            ->appId($appId)
            ->appSecret($appSecret)
            ->qrPage($page)
            ->qrScene($scene)
            ->qrWidth($width)
            ->getMiniQrcode();
        if ($result === false) {
            $errorMessage = $miniExtend->createMiniQrcodeResult;
            return false;
        }
        return $result;
    }

    /**
     * 小程序发送通知消息
     *
     * @param $appId
     * @param $appSecret
     * @param $openid
     * @param $templateId
     * @param $formId
     * @param string $page
     * @param array $data
     * @param string $emphasisKeyWord
     * @param string $errorMessage
     * @return bool|mixed
     */
    public function miniSendNotifyMessage(
        $appId,
        $appSecret,
        $openid,
        $templateId,
        $formId,
        &$errorMessage = "success",
        $page = "",
        $data = [],
        $emphasisKeyWord = ""
    )
    {
        $miniExtend = new MiniExtend();
        $result     = $miniExtend
            ->appId($appId)
            ->appSecret($appSecret)
            ->msgPage($page)
            ->msgOpenid($openid)
            ->msgTemplateId($templateId)
            ->msgFormId($formId)
            ->msgData($data)
            ->msgEmphasisKeyWord($emphasisKeyWord)
            ->miniSendNotifyMessage();
        if ($result === false) {
            $errorMessage = $miniExtend->miniSendMessageResult;
            return false;
        }
        return $result;
    }
}
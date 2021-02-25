<?php

namespace WechatApi\src\miniDirver;

use WechatApi\src\Common;

/**
 * Class sms
 * @package WechatApi\src\webDirver
 */
class MiniExtend
{
    /**
     * @var string 生成二维码接口地址
     */
    protected static $MiniCreateQrcodeUrl = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit';

    /**
     * @var string 发送信息接口地址
     */
    protected static $MiniSendNotifyMessageUrl = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send";

    /**
     * @var string 创建二维码结果
     */
    public $createMiniQrcodeResult = '';

    /**
     * @var string mini发送通知结果
     */
    public $miniSendMessageResult = '';

    /**
     * @var string appid
     */
    protected $appId = '';

    /**
     * @var string appsecret
     */
    protected $appSecret = '';

    /**
     * @var string appsecret
     */
    protected $qrScene = '';

    /**
     * @var string appsecret
     */
    protected $qrPage = '';

    /**
     * @var string 小程序二维码430
     */
    protected $qrWidth = 430;

    /**
     * @var string 小程序通知openid
     */
    protected $msgOpenid = "";

    /**
     * @var string 小程序消息模版id
     */
    protected $msgTemplateId = "";

    /**
     * @var string 小程序消息formid
     */
    protected $msgFormId = "";

    /**
     * @var string 小程序消息跳转页面地址
     */
    protected $msgPage = "";

    /**
     * @var array 小程序消息data
     */
    protected $msgData = [];

    /**
     * @var string 小程序消息关键词
     */
    protected $msgEmphasisKeyWord = "";

    /**
     * 设置小程序通知openid
     *
     * @param $msgOpenid
     * @return $this
     */
    public function msgOpenid($msgOpenid)
    {
        $this->msgOpenid = $msgOpenid;
        return $this;
    }

    /**
     * 设置小程序消息模版id
     *
     * @param $msgTemplateId
     * @return $this
     */
    public function msgTemplateId($msgTemplateId)
    {
        $this->msgTemplateId = $msgTemplateId;
        return $this;
    }

    /**
     * 设置小程序消息formid
     *
     * @param $msgFormId
     * @return $this
     */
    public function msgFormId($msgFormId)
    {
        $this->msgFormId = $msgFormId;
        return $this;
    }

    /**
     * 设置小程序消息跳转页面地址
     *
     * @param $msgPage
     * @return $this
     */
    public function msgPage($msgPage)
    {
        $this->msgPage = $msgPage;
        return $this;
    }

    /**
     * 设置小程序消息data
     *
     * @param $msgData
     * @return $this
     */
    public function msgData($msgData)
    {
        if (is_array($msgData)) {
            $this->msgData = $msgData;
        }
        return $this;
    }

    /**
     * 设置小程序消息关键词
     *
     * @param $msgEmphasisKeyWord
     * @return $this
     */
    public function msgEmphasisKeyWord($msgEmphasisKeyWord)
    {
        $this->msgEmphasisKeyWord = $msgEmphasisKeyWord;
        return $this;
    }


    /**
     * 设置小程序appid
     *
     * @param $appId
     * @return $this
     */
    public function appId($appId)
    {
        $this->appId = $appId;
        return $this;
    }

    /**
     * 设置小程序secret
     *
     * @param $appSecret
     * @return $this
     */
    public function appSecret($appSecret)
    {
        $this->appSecret = $appSecret;
        return $this;
    }

    /**
     * 设置小程序二维码场景值
     *
     * @param $qrScene
     * @return $this
     */
    public function qrScene($qrScene)
    {
        $this->qrScene = $qrScene;
        return $this;
    }

    /**
     * 设置小程序二维码页面地址
     *
     * @param $qrPage
     * @return $this
     */
    public function qrPage($qrPage)
    {
        $this->qrPage = $qrPage;
        return $this;
    }

    /**
     * 设置小程序二维码宽度
     *
     * @param $qrWidth
     * @return $this
     */
    public function qrWidth($qrWidth)
    {
        $this->qrWidth = $qrWidth;
        return $this;
    }

    /**
     * 生成mini的二维码
     *
     * @return bool|mixed
     */
    public function getMiniQrcode()
    {
        $width       = (is_int($this->qrWidth) && $this->qrWidth >= 430) ? $this->qrWidth : 430;
        $miniNormal  = (new MiniNormal())->appId($this->appId)->appSecret($this->appSecret);
        $accessToken = $miniNormal->getMiniToken();
        if ($accessToken === false) {
            $this->createAppQrcodeResult = $miniNormal->getMiniTokenResult;
            return false;
        }
        $dataString = Common::curlRequest(
            self::$MiniCreateQrcodeUrl,
            'POST',
            ['access_token' => $accessToken['accessToken']],
            json_encode([
                            'scene' => $this->qrScene,
                            'page'  => $this->qrPage,
                            'width' => $width
                        ]),
            ['content-type' => 'application/json'],
            $responseString,
            5
        );
        if ($dataString === false) {
            $this->createMiniQrcodeResult = $dataString;
            return false;
        }
        if (json_decode($dataString, true) !== false) {
            $this->createMiniQrcodeResult = $dataString;
            return false;
        }
        return $dataString;
    }

    /**
     * 小程序
     *
     * @return bool|mixed
     */
    public function miniSendNotifyMessage()
    {
        $miniNormal  = (new MiniNormal())->appId($this->appId)->appSecret($this->appSecret);
        $accessToken = $miniNormal->getMiniToken();
        if ($accessToken === false) {
            $this->miniSendMessageResult = $miniNormal->getMiniTokenResult;
            return false;
        }
        $dataString = Common::curlRequest(
            self::$MiniSendNotifyMessageUrl,
            "POST",
            ["access_token" => $accessToken['accessToken']],
            json_encode([
                            "touser"             => $this->msgOpenid,
                            "weapp_template_msg" => [
                                "template_id"      => $this->msgTemplateId,
                                "page"             => $this->msgPage,
                                "form_id"          => $this->msgFormId,
                                "data"             => $this->msgData,
                                "emphasis_keyword" => $this->msgEmphasisKeyWord
                            ],
                            "mp_template_msg"    => null
                        ]),
            ['Content-type:application/json'],
            $responseString,
            5
        );
        if ($dataString === false) {
            $this->miniSendMessageResult = $dataString;
            return false;
        }
        if (json_decode($dataString, true) !== false) {
            $this->miniSendMessageResult = $dataString;
            return false;
        }
        return $dataString;
    }
}
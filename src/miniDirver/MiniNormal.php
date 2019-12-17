<?php

namespace WechatApi\src\miniDirver;

use WechatApi\src\Common;

/**
 * Class MiniNormal
 * @package WechatApi\src\webDirver
 */
class MiniNormal
{
    /**
     * @var string 获取openid接口地址
     */
    protected static $MiniGetOpenidUrl = 'https://api.weixin.qq.com/sns/jscode2session';

    /**
     * @var string 获取token接口地址
     */
    protected static $MiniGetTokenUrl = 'https://api.weixin.qq.com/cgi-bin/token';

    /**
     * @var string 生成二维码接口地址
     */
    protected static $MiniCreateQrcodeUrl = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit';

    /**
     * @var string 发送信息接口地址
     */
    protected static $MiniSendNotifyMessageUrl = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send";

    /**
     * @var string 获取mini的openid的结果
     */
    public $getMiniOpenidResult = '';

    /**
     * @var string 获取mini的token结果
     */
    public $getMiniTokenResult = '';

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
     * @var string jsCode
     */
    protected $jsCode = '';

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
     * 设置jsCode
     *
     * @param $jsCode
     * @return $this
     */
    public function jsCode($jsCode)
    {
        $this->jsCode = $jsCode;
        return $this;
    }

    /**
     * 获取用户openid
     *
     * @return bool|mixed
     */
    public function getMiniOpenid()
    {
        $appConfig  = [
            'appid'      => $this->appId,
            'secret'     => $this->appSecret,
            'js_code'    => $this->jsCode,
            'grant_type' => 'authorization_code'
        ];
        $dataString = Common::curlRequest(
            self::$MiniGetOpenidUrl,
            'GET',
            $appConfig,
            [],
            [],
            $responseString,
            5
        );
        if ($dataString === false) {
            $this->getMiniOpenidResult = $dataString;
            return false;
        }
        $data                      = json_decode($dataString, true);
        $this->getMiniOpenidResult = $data;
        if (!Common::checkKeyExistInArrayAndNotEmpty($data, ['openid'])) {
            return false;
        }
        return $data;
    }

    /**
     * 获取mini的token
     *
     * @return array|bool
     */
    public function getMiniToken()
    {
        $tokenData = $this->getMiniTokenFromLocalFile($accessToken, $expires, $requestAt);
        if ($tokenData === false || $this->isReGetToken($expires, $requestAt)) {
            unset($accessToken, $expires, $requestAt);
            $result = $this->getMiniTokenFromServer($accessToken, $expires, $requestAt);
            if ($result === false) {
                return false;
            }
            $result = $this->saveMiniTokenToLocalFile($accessToken, $expires, $requestAt);
            if ($result === false) {
                $this->getMiniTokenResult = "保存App的token信息到本地文件失败!";
                return false;
            }
            return [
                'accessToken' => $accessToken,
                'expires'     => $expires,
                'requireAt'   => $requestAt
            ];
        } else {
            return [
                'accessToken' => $accessToken,
                'expiresIn'   => $expires,
                'requireAt'   => $requestAt
            ];
        }
    }

    /**
     * 从服务器获取token
     *
     * @param $accessToken
     * @param $expires
     * @param $requestAt
     * @return bool
     */
    protected function getMiniTokenFromServer(
        &$accessToken,
        &$expires,
        &$requestAt
    )
    {
        $dataString = Common::curlRequest(
            self::$MiniGetTokenUrl,
            'GET',
            [
                'grant_type' => 'client_credential',
                'appid'      => $this->appId,
                'secret'     => $this->appSecret
            ],
            [],
            [],
            $responseString,
            5
        );
        if ($dataString === false) {
            $this->getMiniTokenResult = $responseString;
            return false;
        }
        $data = json_decode($dataString, true);
        if (Common::checkKeyExistInArrayAndNotEmpty($data, ['access_token', 'expires_in'])) {
            $this->getMiniTokenResult = $responseString;
            return false;
        }
        $this->getMiniTokenResult = $data;
        $accessToken              = $data['access_token'];
        $expires                  = $data['expires_in'];
        $requestAt                = time();
        return true;
    }

    /**
     * 获取mini的token的文件路径
     *
     * @param $fileName
     * @return string
     */
    protected function getMiniTokenFilePath($fileName)
    {
        $fileName .= ".json";
        $filePath = implode("/", [__DIR__, "../config", $fileName]);
        return $filePath;
    }

    /**
     * 从本地文件获取token信息
     *
     * @param $accessToken
     * @param $expires
     * @param $requestAt
     * @return array|bool
     */
    protected function getMiniTokenFromLocalFile(&$accessToken, &$expires, &$requestAt)
    {
        $filePath = $this->getMiniTokenFilePath($this->appId);
        if (!is_file($filePath)) {
            return [];
        }
        $tokenArray = json_decode(file_get_contents($filePath), true);
        if (!Common::checkKeyExistInArrayAndNotEmpty($tokenArray, ['accessToken', 'expires', 'requestAt'])) {
            return false;
        }
        $accessToken = $tokenArray['accessToken'];
        $expires     = $tokenArray['expires'];
        $requestAt   = $tokenArray['requestAt'];
        return true;
    }

    /**
     * 保存mini的token到本地文件
     *
     * @param $accessToken
     * @param $expires
     * @param $requestAt
     * @return bool|int
     */
    protected function saveMiniTokenToLocalFile($accessToken, $expires, $requestAt)
    {
        $filePath = $this->getMiniTokenFilePath($this->appId);
        return file_put_contents($filePath, json_encode([
                                                            'accessToken' => $accessToken,
                                                            'expires'     => $expires,
                                                            'requestAt'   => $requestAt
                                                        ]));
    }

    /**
     * 是否重新获取token
     *
     * @param $expires
     * @param $requestAt
     * @return bool
     */
    protected function isReGetToken($expires, $requestAt)
    {
        $nowStamp = time();
        if ($nowStamp >= ($requestAt + $expires)) {
            return true;
        }
    }
}
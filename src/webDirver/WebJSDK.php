<?php

namespace WechatApi\src\webDirver;

use WechatApi\src\Common;

/**
 * Class WebJSDK
 * @package WechatApi\src\webDirver
 */
class WebJSDK
{
    /**
     * @var string 网页JSDK获取Token地址
     */
    protected static $WebJsdkGetTokenUrl = 'https://api.weixin.qq.com/cgi-bin/token';

    /**
     * @var string 网页JSDK获取Ticket接口地址
     */
    protected static $WebJsdkGetTicketUrl = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket';

    /**
     * @var string 公众号appid
     */
    protected $appId = "";

    /**
     * @var string 公众号secret
     */
    protected $appSecret = "";

    /**
     * @var string 页面地址
     */
    protected $pageUrl = "";

    /**
     * @var null 获取accesstoken外部方法
     */
    protected $accessTokenExtendHandle = null;

    /**
     * @var null 获取ticket外部方法
     */
    protected $ticketExtendHandle = null;

    /**
     * @var string 错误信息
     */
    public $errorMessage = "";

    /**
     * 设置appid
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
     * 设置appSecret
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
     * 请求页面地址
     *
     * @param $pageUrl
     * @return $this
     */
    public function pageUrl($pageUrl)
    {
        $this->pageUrl = $pageUrl;
        return $this;
    }

    /**
     * 设置获取accesstoken外部方法
     *
     * @param $accessTokenExtendHandle
     * @return $this
     */
    public function accessTokenExtendHandle($accessTokenExtendHandle)
    {
        if (is_callable($accessTokenExtendHandle)) {
            $this->accessTokenExtendHandle = $accessTokenExtendHandle;
        }
        return $this;
    }

    /**
     * 获取ticket外部方法
     *
     * @param $ticketExtendHandle
     * @return $this
     */
    public function ticketExtendHandle($ticketExtendHandle)
    {
        if (is_callable($ticketExtendHandle)) {
            $this->ticketExtendHandle = $ticketExtendHandle;
        }
        return $this;
    }

    /**
     * 获取jsdk签名
     *
     * @return array|bool
     */
    public function jsdkSignature()
    {
        $ticket = $this->getTicket();
        if ($ticket === false) return false;
        $signature = $this->createJsdkSignature($ticket['ticket']);
        return $signature;
    }

    /**
     * 获取ticket
     *
     * @return array|bool|int|mixed|string
     */
    public function getTicket()
    {
        $result = $this->getTicketFromLocalPath(
            $requestAt,
            $expires,
            $ticket
        );
        if ($result && (time() < ($requestAt + $expires))) {
            return [
                "ticket"    => $ticket,
                "expires"   => $expires,
                "requestAt" => $requestAt
            ];
        }
        unset($requestAt, $expires, $ticket);
        $result = $this->getTicketFromServer(
            $ticket,
            $expires,
            $requestAt
        );
        if ($result === false) return false;

        $result = $this->saveTicketToLocalFile($requestAt, $expires, $ticket);
        if ($result === false) {
            $this->errorMessage = "保存Ticket到本地失败!";
            return false;
        }
        return [
            "ticket"    => $ticket,
            "expires"   => $expires,
            "requestAt" => $requestAt
        ];
    }

    /**
     * 获取access_token
     *
     * @return array|bool|int|mixed|string
     */
    public function getAccessToken()
    {
        $result = $this->getAccessTokenFromLocalPath(
            $requestAt,
            $expires,
            $accessToken
        );
        if ($result && (time() < ($requestAt + $expires))) {
            return [
                "accessToken" => $accessToken,
                "expires"     => $expires,
                "requestAt"   => $requestAt
            ];
        }
        unset($requestAt, $expires, $accessToken);
        $result = $this->getAccessTokenFromServer(
            $accessToken,
            $expires,
            $requestAt
        );
        if ($result === false) return false;

        $result = $this->saveAccessTokenToLocalFile($requestAt, $expires, $accessToken);
        if ($result === false) {
            $this->errorMessage = "保存AccessToken到本地失败!";
            return false;
        }
        return [
            "accessToken" => $accessToken,
            "expires"     => $expires,
            "requestAt"   => $requestAt
        ];
    }

    /**
     * 从本地获取accesstoken
     *
     * @param $requestAt
     * @param $expires
     * @param $accessToken
     * @return bool
     */
    protected function getAccessTokenFromLocalPath(
        &$requestAt,
        &$expires,
        &$accessToken
    )
    {
        $filePath        = $this->accessTokenFilePath();
        $accessTokenData = json_decode(file_get_contents($filePath), true);
        if (!is_array($accessTokenData) || !Common::checkKeyExistInArrayAndNotEmpty($accessTokenData, ['requestAt', 'expires', 'accessToken'])) {
            return false;
        }
        $requestAt   = $accessTokenData['requestAt'];
        $expires     = $accessTokenData['expires'];
        $accessToken = $accessTokenData['accessToken'];
        return true;
    }

    /**
     * 保存accesstoken到本地文件
     *
     * @param $requestAt
     * @param $expires
     * @param $accessToken
     * @return bool|int
     */
    protected function saveAccessTokenToLocalFile(
        $requestAt,
        $expires,
        $accessToken
    )
    {
        $filePath = $this->accessTokenFilePath();
        return file_put_contents($filePath, json_encode([
                                                            'requestAt'   => $requestAt,
                                                            'expires'     => $expires,
                                                            'accessToken' => $accessToken
                                                        ]));
    }

    /**
     * 从本地获取ticket
     *
     * @param $requestAt
     * @param $expires
     * @param $ticket
     * @return bool
     */
    protected function getTicketFromLocalPath(
        &$requestAt,
        &$expires,
        &$ticket
    )
    {
        $filePath        = $this->ticketFilePath();
        $accessTokenData = json_decode(file_get_contents($filePath), true);
        if (!is_array($accessTokenData) || !Common::checkKeyExistInArrayAndNotEmpty($accessTokenData, ['requestAt', 'expires', 'ticket'])) {
            return false;
        }
        $requestAt = $accessTokenData['requestAt'];
        $expires   = $accessTokenData['expires'];
        $ticket    = $accessTokenData['ticket'];
        return true;
    }

    /**
     * 保存ticket到本地文件
     *
     * @param $requestAt
     * @param $expires
     * @param $ticket
     * @return bool|int
     */
    protected function saveTicketToLocalFile(
        $requestAt,
        $expires,
        $ticket
    )
    {
        $filePath = $this->ticketFilePath();
        return file_put_contents($filePath, json_encode([
                                                            'requestAt' => $requestAt,
                                                            'expires'   => $expires,
                                                            'ticket'    => $ticket
                                                        ]));
    }

    /**
     * 生成jsdk签名
     *
     * @param $ticket
     * @return array
     */
    protected function createJsdkSignature($ticket)
    {
        $nonceStr = '';
        for ($i = 0, $e = rand(4, 31); $i < $e; $i++) {
            $nonceStr .= chr(rand(65, 126));
        }

        $data = array(
            'jsapi_ticket' => $ticket,
            'nonceStr'     => $nonceStr,
            'timestamp'    => time(),
            'url'          => $this->pageUrl
        );

        $signStr =
            'jsapi_ticket=' . $data['jsapi_ticket']
            . '&noncestr=' . $data['nonceStr']
            . '&timestamp=' . $data['timestamp']
            . '&url=' . $data['url'];

        $signature         = sha1($signStr);
        $data['signature'] = $signature;
        $data['appId']     = $this->appId;
        unset($data['jsapi_ticket']);
        unset($data['url']);
        return $data;
    }

    /**
     * 从微信服务器获取ticket
     *
     * @param $ticket
     * @param $expires
     * @param $requestAt
     * @return bool
     */
    protected function getTicketFromServer(
        &$ticket,
        &$expires,
        &$requestAt
    )
    {
        if (is_callable($this->ticketExtendHandle)) {
            $data = call_user_func_array($this->ticketExtendHandle, []);
            if ($data === false) {
                $this->errorMessage = "通过外部方法获取Ticket失败!";
                return false;
            }
            $ticket    = $data['ticket'];
            $expires   = $data['expires'];
            $requestAt = time();
            return true;
        } else {
            $token = $this->getAccessToken();
            if ($token === false) return false;
            $paramsArray = [
                'type'         => 'jsapi',
                'access_token' => $token['accessToken']
            ];
            $ch          = curl_init(self::$WebJsdkGetTicketUrl . '?' . http_build_query($paramsArray));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resultString = curl_exec($ch);
            curl_close($ch);
            $this->errorMessage = $resultString;
            $jsonResult         = json_decode($resultString, true);
            if ($jsonResult['errcode'] == 0) {
                $ticket    = $jsonResult['ticket'];
                $expires   = $jsonResult['expires_in'];
                $requestAt = time();
                return true;
            } else {
                return false;
            }
        }

    }

    /**
     * 从微信服务器获取access_token
     *
     * @param $accessToken
     * @param $expires
     * @param $requestAt
     * @return bool
     */
    protected function getAccessTokenFromServer(
        &$accessToken,
        &$expires,
        &$requestAt
    )
    {
        if (is_callable($this->accessTokenExtendHandle)) {
            $data = call_user_func_array($this->accessTokenExtendHandle, []);
            if ($data === false) {
                $this->errorMessage = "通过外部方法获取AccessToken失败!";
                return false;
            }
            $accessToken = $data['accessToken'];
            $expires     = $data['expires'];
            $requestAt   = time();
            return true;
        } else {
            $paramsArray = [
                'appID'      => $this->appId,
                'secret'     => $this->appSecret,
                'grant_type' => 'client_credential'
            ];
            $ch          = curl_init(self::$WebJsdkGetTokenUrl . '?' . http_build_query($paramsArray));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resultString = curl_exec($ch);
            curl_close($ch);
            $this->errorMessage = $resultString;
            $jsonResult         = json_decode($resultString, true);
            if (isset($jsonResult['errcode'])) {
                return false;
            } else {
                $accessToken = $jsonResult['access_token'];
                $expires     = $jsonResult['expires_in'];
                $requestAt   = time();
                return true;
            }
        }
    }

    /**
     * 获取Accesstoken本地文件路径
     *
     * @return string
     */
    protected function accessTokenFilePath()
    {
        return __DIR__ . "/../../config/" . $this->appId . ".json";
    }

    /**
     * 获取Ticket本地文件路径
     *
     * @return string
     */
    protected function ticketFilePath()
    {
        return __DIR__ . "/../../config/" . $this->appId . "_ticket.json";
    }
}
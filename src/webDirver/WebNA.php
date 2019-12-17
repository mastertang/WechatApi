<?php

namespace WechatApi\src\webDirver;

use WechatApi\src\Common;

/**
 * Class WebNA
 * @package WechatApi\src\webDirver
 */
class WebNA
{
    /**
     * @var string 网页授权接口地址
     */
    protected static $WebAuthorizeUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize';

    /**
     * @var string 网页授权获取用户信息接口地址
     */
    protected static $WebGetUserInfoUrl = 'https://api.weixin.qq.com/sns/userinfo';

    /**
     * @var string 网页授权获取access_token接口地址
     */
    protected static $WebGetAccessTokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token';

    /**
     * @var string 公众号appid
     */
    protected $appId = "";

    /**
     * @var string 公众号secret
     */
    protected $appSecret = "";

    /**
     * @var string 授权回调地址
     */
    protected $redirectUri = "";

    /**
     * @var string 响应类型
     */
    protected $responseType = "code";

    /**
     * @var string  scope = "snsapi_base" | "snsapi_userinfo"
     */
    protected $scope = "snsapi_base";

    /**
     * @var string 自定义参数
     */
    protected $state = "";

    /**
     * @var array 重定向地址参数
     */
    protected $redirectParams = [];

    /**
     * @var array 获取用户信息参数key
     */
    protected $userinfoParamsKey = [];

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
     * 设置重定向地址
     *
     * @param $redirectUri
     * @return $this
     */
    public function redirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
        return $this;
    }

    /**
     * 设置responseType
     *
     * @param $responseType
     * @return $this
     */
    public function responseType($responseType)
    {
        $this->responseType = $responseType;
        return $this;
    }

    /**
     * 设置静默授权
     *
     * @return $this
     */
    public function snsapiBase()
    {
        $this->scope = "snsapi_base";
        return $this;
    }

    /**
     * 设置用户信息授权
     *
     * @return $this
     */
    public function snsapiUserinfo()
    {
        $this->scope = "snsapi_userinfo";
        return $this;
    }

    /**
     * 设置自定义参数
     *
     * @param $state
     * @return $this
     */
    public function state($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * 设置重定向地址http参数
     *
     * @param $redirectParams
     * @return $this
     */
    public function redirectParams($redirectParams)
    {
        if (is_array($redirectParams)) {
            $this->redirectParams = $redirectParams;
        }
        return $this;
    }

    /**
     * 设置获取用户信息时参数的key
     *
     * @param $userInfoParamsKey
     * @return $this
     */
    public function userInfoParamsKey($userInfoParamsKey)
    {
        if (is_array($userInfoParamsKey)) {
            $this->userinfoParamsKey = $userInfoParamsKey;
        }
        return $this;
    }

    /**
     * 开始授权
     */
    public function startAuthorize()
    {
        $redircetUri         = $this->redirectUri;
        $redirectParamsQuery = http_build_query($this->redirectParams);
        if (!empty($redirectParamsQuery)) {
            $redircetUri .= "?{$redirectParamsQuery}";
        }
        $querys      = [
            'appid'         => $this->appId,
            'redirect_uri'  => $redircetUri,
            'response_type' => $this->responseType,
            'scope'         => $this->scope,
            'state'         => $this->state
        ];
        $queryString = http_build_query($querys);
        header('Location:' . self::$WebAuthorizeUrl . "?{$queryString}");
    }

    /**
     * 授权回调获取用户信息
     *
     * @return bool|mixed
     */
    public function getWechatUserInfo()
    {
        $code = isset($_GET['code']) ? $_GET['code'] : "";
        if (empty($code)) {
            $this->errorMessage = "Code参数空或错误!";
            return false;
        }
        $tokenString        = Common::curlRequest(
            self::$WebGetAccessTokenUrl,
            'GET',
            [
                'code'       => $code,
                'grant_type' => 'authorization_code',
                'appid'      => $this->appId,
                'secret'     => $this->appSecret
            ],
            [],
            [],
            $responseString,
            5
        );
        $this->errorMessage = $responseString;
        if ($tokenString !== false) return false;
        $tokenData = json_decode($tokenString, true);
        if (!Common::checkKeyExistInArrayAndNotEmpty($tokenData, ['scope'])) return false;

        $responseData = [];
        switch ($tokenData['scope']) {
            case 'snsapi_base':
                if (!Common::checkKeyExistInArrayAndNotEmpty($tokenData, ['openid'])) return false;
                $responseData['openid'] = $tokenData['openid'];
                break;
            case 'snsapi_userinfo':
                if (!Common::checkKeyExistInArrayAndNotEmpty($tokenData, ['access_token', 'openid'])) return false;
                $requestString      = Common::curlRequest(
                    self::$WebGetUserInfoUrl,
                    'GET',
                    [
                        'lang'         => 'en',
                        'access_token' => $tokenData['access_token'],
                        'openid'       => $tokenData['openid']
                    ],
                    [],
                    [],
                    $responseString,
                    5
                );
                $this->errorMessage = $responseString;
                if ($requestString === false) return false;
                $requestData = json_decode($requestString, true);
                if (!is_array($requestData)) return false;
                $responseData = array_merge($responseData, $requestData);
                break;
            default:
                $this->errorMessage = "Scope错误!";
                return false;
                break;
        }
        $extendParamsKey   = $this->userinfoParamsKey;
        $extendParamsKey[] = 'state';
        foreach ($extendParamsKey as $key) {
            if (isset($_GET[$key])) {
                $responseData[$key] = $_GET[$key];
            }
        }
        return $responseData;
    }
}
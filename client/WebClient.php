<?php

namespace WechatApi\client;

use WechatApi\src\webDirver\WebGA;
use WechatApi\src\webDirver\WebJSDK;
use WechatApi\src\webDirver\WebNA;

/**
 * Class WebClient
 * @package WechatApi\client
 */
class WebClient
{
    /**
     * 开始网页授权
     *
     * @param $appId
     * @param $appSecret
     * @param $redirectUri
     * @param array $redirectParams
     * @param string $state
     * @param string $scope
     * @param string $responseType
     */
    public function webAuthorize(
        $appId,
        $appSecret,
        $redirectUri,
        $redirectParams = [],
        $state = "",
        $scope = "snsapi_base",
        $responseType = "code"
    )
    {
        $webAuthorize = new WebNA();
        $webAuthorize->appId($appId)
            ->appSecret($appSecret)
            ->redirectUri($redirectUri)
            ->redirectParams($redirectParams);
        if ($scope == "snsapi_userinfo") {
            $webAuthorize->snsapiUserinfo();
        }
        $webAuthorize->responseType($responseType);
        $webAuthorize->state($state);
        $webAuthorize->startAuthorize();
    }

    /**
     * 网页授权获取用户信息
     *
     * @param $appId
     * @param $appSecret
     * @param string $errorMessage
     * @param array $userinfoParamsKey
     * @return bool|mixed
     */
    public function webAuthorizeGetUserinfo(
        $appId,
        $appSecret,
        &$errorMessage = "success",
        $userinfoParamsKey = []
    )
    {
        $webAuthorize = new WebNA();
        $result       = $webAuthorize->appId($appId)
            ->appSecret($appSecret)
            ->userInfoParamsKey($userinfoParamsKey)
            ->getWechatUserInfo();
        if ($result === false) {
            $errorMessage = $webAuthorize->errorMessage;
        }
        return $result;
    }

    /**
     * 开始网页全局授权
     *
     * @param $appId
     * @param $appSecret
     * @param $redirectUri
     * @param array $redirectParams
     * @param string $state
     * @param string $responseType
     */
    public function webGlobalAuthorize(
        $appId,
        $appSecret,
        $redirectUri,
        $redirectParams = [],
        $state = "",
        $responseType = "code"
    )
    {
        $webAuthorize = new WebGA();
        $webAuthorize->appId($appId)
            ->appSecret($appSecret)
            ->redirectUri($redirectUri)
            ->redirectParams($redirectParams);
        $webAuthorize->responseType($responseType);
        $webAuthorize->state($state);
        $webAuthorize->startAuthorize();
    }

    /**
     * 网页授权获取用户信息
     *
     * @param $appId
     * @param $appSecret
     * @param string $errorMessage
     * @param array $userinfoParamsKey
     * @param null $accessTokenExtendHandle
     * @return bool|mixed
     */
    public function webAuthorizeGlobalGetUserinfo(
        $appId,
        $appSecret,
        &$errorMessage = "success",
        $userinfoParamsKey = [],
        $accessTokenExtendHandle = null
    )
    {
        $webAuthorize = new WebGA();
        $result       = $webAuthorize->appId($appId)
            ->appSecret($appSecret)
            ->userInfoParamsKey($userinfoParamsKey)
            ->accessTokenExtendHandle($accessTokenExtendHandle)
            ->getWechatUserInfo();
        if ($result === false) {
            $errorMessage = $webAuthorize->errorMessage;
        }
        return $result;
    }

    /**
     * JSDK签名
     *
     * @param $appId
     * @param $appSecret
     * @param $pageUrl
     * @param string $errorMessage
     * @param null $accessTokenExtendHandle
     * @param null $ticketExtendHandle
     * @return array|bool
     */
    public function jsdkSignature(
        $appId,
        $appSecret,
        $pageUrl,
        &$errorMessage = "success",
        $accessTokenExtendHandle = null,
        $ticketExtendHandle = null
    )
    {
        $webJsdk = new WebJSDK();
        $result  = $webJsdk->appId($appId)
            ->appSecret($appSecret)
            ->pageUrl($pageUrl)
            ->accessTokenExtendHandle($accessTokenExtendHandle)
            ->ticketExtendHandle($ticketExtendHandle)
            ->jsdkSignature();
        if ($result === false) {
            $errorMessage = $webJsdk->errorMessage;
            return false;
        }
        return $result;
    }

    /**
     * 获取Ticket
     *
     * @param $appId
     * @param $appSecret
     * @param string $errorMessage
     * @param null $ticketExtendHandle
     * @return array|bool
     */
    public function getTicket(
        $appId,
        $appSecret,
        &$errorMessage = "success",
        $ticketExtendHandle = null
    ){
        $webJsdk = new WebJSDK();
        $result  = $webJsdk->appId($appId)
            ->appSecret($appSecret)
            ->ticketExtendHandle($ticketExtendHandle)
            ->getTicket();
        if ($result === false) {
            $errorMessage = $webJsdk->errorMessage;
            return false;
        }
        return $result;
    }

    /**
     * 获取Ticket
     *
     * @param $appId
     * @param $appSecret
     * @param string $errorMessage
     * @param null $accessTokenExtendHandle
     * @return array|bool|int|mixed|string
     */
    public function getAccessToken(
        $appId,
        $appSecret,
        &$errorMessage = "success",
        $accessTokenExtendHandle = null
    ){
        $webJsdk = new WebJSDK();
        $result  = $webJsdk->appId($appId)
            ->appSecret($appSecret)
            ->accessTokenExtendHandle($accessTokenExtendHandle)
            ->getAccessToken();
        if ($result === false) {
            $errorMessage = $webJsdk->errorMessage;
            return false;
        }
        return $result;
    }
}
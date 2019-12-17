<?php
include "../WechatApi.php";
include "../client/CloudClient.php";
include "../client/MiniClient.php";
include "../client/WebClient.php";
include "../src/miniDirver/MiniExtend.php";
include "../src/miniDirver/MiniNormal.php";
include "../src/webDirver/WebGA.php";
include "../src/webDirver/WebJSDK.php";
include "../src/webDirver/WebNA.php";

$appId     = "";
$appSecret = "";

// 网页授权
(new \WechatApi\WechatApi())
    ->wechatWeb()
    ->webAuthorize(
        $appId,
        $appSecret,
        "https://www.baidu.com",
        ["a" => 1],
        123456,
        "snsapi_base",
        "code"
    );

// 授权获取用户信息
$result = (new \WechatApi\WechatApi())
    ->wechatWeb()
    ->webAuthorizeGetUserinfo(
        $appId,
        $appSecret,
        $errorMessage,
        ["a" => 1]
    );
var_dump($result === false ? $errorMessage : $result);

// 全局网页授权
(new \WechatApi\WechatApi())
    ->wechatWeb()
    ->webGlobalAuthorize(
        $appId,
        $appSecret,
        "https://www.baidu.com",
        ["a" => 1],
        123456,
        "code"
    );

// 全局授权获取用户信息
$result = (new \WechatApi\WechatApi())
    ->wechatWeb()
    ->webAuthorizeGlobalGetUserinfo(
        $appId,
        $appSecret,
        $errorMessage,
        ["a" => 1]
    );
var_dump($result === false ? $errorMessage : $result);

// 获取JSDK签名
$result = (new \WechatApi\WechatApi())
    ->wechatWeb()
    ->jsdkSignature(
        $appId,
        $appSecret,
        "https://www.baidu.com",
        $errorMessage,
        function () {
            return [
                "accessToken" => "",
                "expires"     => 7200,
                "requestAt"   => 1591438906
            ];
        },
        function () {
            return [
                "accessToken" => "",
                "expires"     => 7200,
                "requestAt"   => 1591438906
            ];
        }
    );
var_dump($result === false ? $errorMessage : $result);

// 获取公众号ticket
$result = (new \WechatApi\WechatApi())
    ->wechatWeb()
    ->getTicket(
        $appId,
        $appSecret,
        $errorMessage,
        function () {
            return [
                "accessToken" => "",
                "expires"     => 7200,
                "requestAt"   => 1591438906
            ];
        }
    );
var_dump($result === false ? $errorMessage : $result);

// 获取公众号accessToken
$result = (new \WechatApi\WechatApi())
    ->wechatWeb()
    ->getAccessToken(
        $appId,
        $appSecret,
        $errorMessage,
        function () {
            return [
                "accessToken" => "",
                "expires"     => 7200,
                "requestAt"   => 1591438906
            ];
        }
    );
var_dump($result === false ? $errorMessage : $result);

// 小程序获取openid
$result = (new \WechatApi\WechatApi())
    ->wechatMini()
    ->miniOpenid(
        $appId,
        $appSecret,
        $jsCode,
        $errorMessage
    );
var_dump($result === false ? $errorMessage : $result);

// 小程序获取accessToken
$result = (new \WechatApi\WechatApi())
    ->wechatMini()
    ->miniAccessToken(
        $appId,
        $appSecret,
        $errorMessage
    );
var_dump($result === false ? $errorMessage : $result);

// 小程序生成二维码
$result = (new \WechatApi\WechatApi())
    ->wechatMini()
    ->miniCreateQrCode(
        $appId,
        $appSecret,
        $errorMessage,
        123456,
        "page/index/index",
        430
    );
var_dump($result === false ? $errorMessage : $result);

// 小程序生成二维码
$result = (new \WechatApi\WechatApi())
    ->wechatMini()
    ->miniSendNotifyMessage(
        $appId,
        $appSecret,
        $openid,
        "通知模版id",
        $formId,
        $errorMessage,
        "page/index/index",
        [
            "动态key" => "对应内容"
        ],
        "突出关键词"
    );
var_dump($result === false ? $errorMessage : $result);
<?php

include "vendor/autoload.php";
include "src/smsDirver/sms.php";
include "client/SmsClient.php";
include "SmsApi.php";

$result = (new \WechatApi\SmsApi())->wechatWeb()->sendSms(
    "dfdfdfd",
    "",
    "",
    "",
    "",
    "",
    [],
    $errorMessage
);

var_dump($errorMessage);

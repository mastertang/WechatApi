<?php

namespace WechatApi\src;

/**
 * Class Common
 * @package WechatApi\src
 */
class Common
{
    /**
     * curl请求
     *
     * @param $host
     * @param $method
     * @param $querys
     * @param $body
     * @param $headers
     * @param int $timeOut
     * @return bool|mixed
     */
    public static function curlRequest($host, $method, $querys, $body, $headers, &$responseString, $timeOut = 30)
    {
        $url  = self::urlAppend($host, $querys);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeOut);
        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (strtoupper($method) == 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }
        $dataString     = curl_exec($curl);
        $errorCode      = curl_errno($curl);
        $responseString = $dataString;
        if (empty($dataString))
            return false;
        else {
            return $errorCode === 0 ? $dataString : false;
        }
    }

    /**
     * 地址添加Get参数
     *
     * @param $url
     * @param $data
     * @return string
     */
    public static function urlAppend($url, $data)
    {
        if (!is_array($data) || empty($data)) {
            return $url;
        }
        $query = urldecode(http_build_query($data));
        $url   .= (strpos($url, '?') === false) ? "?{$query}" : "&{$query}";
        return $url;
    }

    /**
     * 检测key是否存在数组中且不为空
     *
     * @param $array
     * @param $keys
     * @return bool
     */
    public static function checkKeyExistInArrayAndNotEmpty($array, $keys)
    {
        if (!is_array($array)) {
            return false;
        }

        if (is_string($keys)) {
            if (!isset($array[$keys]) || empty($array[$keys])) {
                return false;
            }
        } else if (is_array($keys)) {
            foreach ($keys as $key) {
                if (!isset($array[$key]) || empty($array[$key])) {
                    return false;
                }
            }
        } else {
            return false;
        }
        return true;
    }
}
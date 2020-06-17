<?php

namespace WechatApi\client;

use WechatApi\src\cloudDirver\Cos;

/**
 * Class CloudClient
 * @package WechatApi\client
 */
class CloudClient
{
    /**
     * 上传对象
     *
     * @param $secretId
     * @param $secretKey
     * @param $region
     * @param $bucket
     * @param $key
     * @param string $filePath
     * @param string $fileContent
     * @param string $schema
     * @param string $errorMessage
     * @return bool
     */
    public function putObject(
        $secretId,
        $secretKey,
        $region,
        $bucket,
        $key,
        $filePath = "",
        $fileContent = "",
        $schema = "https",
        &$errorMessage = ""
    )
    {
        $cosClient = (new Cos())
            ->setSecretId($secretId)
            ->setSecretKey($secretKey)
            ->setRegion($region)
            ->setBucket($bucket)
            ->setCosPath($key)
            ->setFilePath($filePath)
            ->setFileContent($fileContent)
            ->setSchema($schema);
        $result    = $cosClient->putObject();
        if ($result === false) {
            $errorMessage = $cosClient->resultMessage;
        } else {
            $errorMessage = "成功!";
        }
        return $result;
    }

    /**
     * 删除object
     *
     * @param $secretId
     * @param $secretKey
     * @param $region
     * @param $bucket
     * @param $key
     * @param string $schema
     * @param string $errorMessage
     * @return bool
     */
    public function deleteObject(
        $secretId,
        $secretKey,
        $region,
        $bucket,
        $key,
        $schema = "https",
        &$errorMessage = ""
    )
    {
        $cosClient = (new Cos())
            ->setSecretId($secretId)
            ->setSecretKey($secretKey)
            ->setRegion($region)
            ->setBucket($bucket)
            ->setCosPath($key)
            ->setSchema($schema);
        $result    = $cosClient->deleteObject();
        if ($result === false) {
            $errorMessage = $cosClient->resultMessage;
        } else {
            $errorMessage = "成功!";
        }
        return $result;
    }
}
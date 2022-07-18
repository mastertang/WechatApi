<?php

namespace WechatApi\src\cloudDirver;

use Qcloud\Cos\Client;
use WechatApi\src\Common;

/**
 * Class Cos
 * @package WechatApi\src\webDirver
 */
class Cos
{
    /**
     * @var string 地区
     */
    protected $region = "";

    /**
     * @var string 请求方式
     */
    protected $schema = "https";

    /**
     * @var string secretId
     */
    protected $secretId = "";

    /**
     * @var string secretKey
     */
    protected $secretKey = "";

    /**
     * @var string 存储桶
     */
    protected $bucket = "";

    /**
     * @var string 存储路径
     */
    protected $key = "";

    /**
     * @var string 文件路径
     */
    protected $filePath = "";

    /**
     * @var string 文件内容
     */
    protected $fileContent = "";

    /**
     * @var string 执行结果
     */
    public $resultMessage = "";

    /**
     * 设置区域
     *
     * @param $region
     * @return $this
     */
    public function setRegion($region)
    {
        if (!empty($region)) {
            $this->region = $region;
        }
        return $this;
    }

    /**
     * 设置schema
     *
     * @param $schema
     * @return $this
     */
    public function setSchema($schema)
    {
        $schema = strtolower($schema);
        if (!empty($region) && in_array($schema, ['http', 'https'])) {
            $this->schema = $schema;
        }
        return $this;
    }

    /**
     * 设置secretId
     *
     * @param $secretId
     * @return $this
     */
    public function setSecretId($secretId)
    {
        if (!empty($secretId)) {
            $this->secretId = $secretId;
        }
        return $this;
    }

    /**
     * 设置secretKey
     *
     * @param $secretKey
     * @return $this
     */
    public function setSecretKey($secretKey)
    {
        if (!empty($secretKey)) {
            $this->secretKey = $secretKey;
        }
        return $this;
    }

    /**
     * 设置bucket
     *
     * @param $bucket
     * @return $this
     */
    public function setBucket($bucket)
    {
        if (!empty($bucket)) {
            $this->bucket = $bucket;
        }
        return $this;
    }

    /**
     * 设置cos路径
     *
     * @param $cosPath
     * @return $this
     */
    public function setCosPath($cosPath)
    {
        if (!empty($cosPath)) {
            $this->key = $cosPath;
        }
        return $this;
    }

    /**
     * 设置文件路径
     *
     * @param $filePath
     * @return $this
     */
    public function setFilePath($filePath)
    {
        if (!empty($filePath) && is_file($filePath)) {
            $this->filePath = $filePath;
        }
        return $this;
    }

    /**
     * 设置文件内容
     *
     * @param $content
     * @return $this
     */
    public function setFileContent($content)
    {
        if (!empty($content)) {
            $this->fileContent = $content;
        }
        return $this;
    }

    /**
     * 创建Cos客户端
     *
     * @return bool|Client
     */
    protected function createClient()
    {
        if (empty($this->secretId)) {
            $this->resultMessage = "SecreteId empty!";
            return false;
        }
        if (empty($this->secretKey)) {
            $this->resultMessage = "SecretKey empty!";
            return false;
        }
        if (empty($this->region)) {
            $this->resultMessage = "Region empty!";
            return false;
        }
        $cosClient = new Client(
            [
                'region'      => $this->region,
                'schema'      => $this->schema, //协议头部，默认为http
                'credentials' => [
                    'secretId'  => $this->secretId,
                    'secretKey' => $this->secretKey
                ]
            ]);
        return $cosClient;
    }

    /**
     * 上传对象
     *
     * @return bool
     */
    public function putObject()
    {
        $cosClient = $this->createClient();
        if (!($cosClient instanceof Client)) {
            return false;
        }
        try {
            if (empty($this->bucket)) {
                $this->resultMessage = "Bucket empty!";
                return false;
            }
            if (empty($this->key)) {
                $this->resultMessage = "Key empty!";
                return false;
            }
            if (empty($this->filePath) && empty($this->fileContent)) {
                $this->resultMessage = "FilePth and FileContent empty";
                return false;
            }
            $objectData = [
                'Bucket' => $this->bucket, //格式：BucketName-APPID
                'Key'    => $this->key
            ];
            if (!empty($this->filePath) && !is_file($this->filePath)) {
                $this->resultMessage = "File not exist!";
                return false;
            }
            if (!empty($this->filePath)) {
                $objectData['Body'] = fopen($this->filePath, 'rb');
            } else {
                $objectData['Body'] = $this->fileContent;
            }
            $result              = $cosClient->putObject($objectData);
            $this->resultMessage = json_encode($result);
            return true;
        } catch (\Exception $exception) {
            // 请求失败
            $this->resultMessage = $exception->getMessage();
            return false;
        }
    }

    /**
     * 删除对象
     *
     * @return bool
     */
    public function deleteObject()
    {
        $cosClient = $this->createClient();
        if (!($cosClient instanceof Client)) {
            return false;
        }
        try {
            if (empty($this->bucket)) {
                $this->resultMessage = "Bucket empty!";
                return false;
            }
            if (empty($this->key)) {
                $this->resultMessage = "Key empty!";
                return false;
            }
            $objectData          = [
                'Bucket' => $this->bucket, //格式：BucketName-APPID
                'Key'    => $this->key
            ];
            $result              = $cosClient->deleteObject($objectData);
            $this->resultMessage = json_encode($result);
            return true;
        } catch (\Exception $exception) {
            // 请求失败
            $this->resultMessage = $exception->getMessage();
            return false;
        }
    }
}
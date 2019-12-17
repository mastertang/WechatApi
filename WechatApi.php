<?php

namespace WechatApi;

use WechatApi\client\MiniClient;
use WechatApi\client\CloudClient;
use WechatApi\client\WebClient;

/**
 * Class WechatApi
 * @package WechatApi
 */
class WechatApi
{
    /**
     * @var WebClient null 微信web客户端类实例
     */
    public $wechatWeb = null;

    /**
     * @var  MiniClient null 微信app客户端实例
     */
    public $wechatMini = null;

    /**
     * @var CloudClient null 微信云客户端实例
     */
    public $wechatCloud = null;

    /**
     * 选择微信网页客户端
     * @return WebClient null|WebClient
     */
    public function wechatWeb()
    {
        if ($this->wechatWeb instanceof WebClient) {
            return $this->wechatWeb;
        }
        $this->wechatWeb = new WebClient();
        return $this->wechatWeb;
    }

    /**
     * 选择微信小程序客户端
     * @return MiniClient null|AppClient
     */
    public function wechatMini()
    {
        if ($this->wechatMini instanceof MiniClient) {
            return $this->wechatMini;
        }
        $this->wechatMini = new MiniClient();
        return $this->wechatMini;
    }

    /**
     * 选择微信云端客户端
     * @return CloudClient null|CloudClient
     */
    public function wechatCloud()
    {
        if ($this->wechatCloud instanceof CloudClient) {
            return $this->wechatCloud;
        }
        $this->wechatCloud = new CloudClient();
        return $this->wechatCloud;
    }

}
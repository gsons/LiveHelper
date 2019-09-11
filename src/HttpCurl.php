<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/15
 * Time: 14:53
 */

namespace Gsons;

use Curl\Curl;

class HttpCurl extends Curl
{
    //默认的useragent
    private $userAgentArr = [
        'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1'
    ];
    //默认的IP
    private $ipArr = [
        '192.168.64.1'
    ];

    public function __construct($config = [], $init = true)
    {
        if ($init) {
            parent::__construct();
        } else {
            $this->curl = curl_init();
            $this->setOpt(CURLINFO_HEADER_OUT, true);
            $this->setOpt(CURLOPT_HEADER, false);
            $this->setOpt(CURLOPT_RETURNTRANSFER, true);
            $this->setOpt(CURLOPT_HEADERFUNCTION, array($this, 'addResponseHeaderLine'));
        }
        $this->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->setOpt(CURLOPT_HTTPPROXYTUNNEL, true);
        $this->setOpt(CURLOPT_SSL_VERIFYHOST, false);

        if (isset($config['user_agent']) && is_array($config['user_agent'])) {
            $this->userAgentArr = array_merge($this->userAgentArr, $config['user_agent']);
        }
        if (isset($config['ip']) && is_array($config['ip'])) {
            $this->ipArr = array_merge($this->ipArr, $config['ip']);
        }
        if ($init) {
            $this->initOpt();
        }
    }

    private function initOpt()
    {
        $index = array_rand($this->userAgentArr);
        if (isset($this->userAgentArr[$index])) {
            $this->setUserAgent($this->userAgentArr[$index]);
        }
    }

    public function __destruct()
    {
        parent::__destruct();
    }
}
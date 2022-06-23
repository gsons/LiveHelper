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
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36'
    ];
    //默认的IP
    private $ipArr = [
        '112.168.64.1',
        '112.168.64.11',
        '112.168.64.21',
        '112.168.64.31',
        '112.168.64.51',
    ];

    const TIME_OUT=15;

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
        $this->setOpt(CURLOPT_TIMEOUT, self::TIME_OUT);
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
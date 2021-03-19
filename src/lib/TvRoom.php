<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2020-08-01
 * Time: 0:51
 */

namespace Gsons\lib;

use Gsons\Console;
use Gsons\HttpCurl;

class TvRoom
{
 const TV_URL="http://192.168.101.13:2020/savediy";
// const TV_URL="http://192.168.101.6:2020/savediy";

    /**
     * @param $arrList
     * @return bool
     */
    public static function sendLiveUrl($arrList)
    {
        if (empty($arrList)) {
            return false;
        }
        $curl = new HttpCurl();
        $urlArrStr = "";
        foreach ($arrList as $title => $list) {
            $urlArrStr .= "{$title}\r\n";
            foreach ($list as $name => $live_url) {
                $urlArrStr .= "{$name},$live_url\r\n";
            }
        }
        $param = "{$urlArrStr} \r\n\\r\\n\r\n: ";
        $curl->setOpt(CURLOPT_URL, self::TV_URL);
        $curl->setOpt(CURLOPT_POSTFIELDS, $param);
        $curl->response = curl_exec($curl->curl);
        $curl->close();
        $data = json_decode($curl->response, true);
        if (isset($data['suc']) && $data['suc'] === '0') {
            return true;
        } else {
            return false;
        }
    }

    public static function getLiveArr($config)
    {
        $listArr = [];
        foreach ($config as $liveCode => $liveName) {
            /**
             * @var $class  \Gsons\Live\HuyaLive;
             */
            $ClassName = "\Gsons\Live\\{$liveCode}Live";
            if (!class_exists($ClassName)) {
                Console::error("ERROR:cant not find class $ClassName");
                continue;
            }

            $class = new $ClassName();
            try {
                $roomIdArr = $class->getTvRoom();
            } catch (\ErrorException $e) {
                Console::error("ERROR:获取{$liveName}直播房间号失败:".$e->getMessage());
                continue;
            }
            $list = [];
            foreach ($roomIdArr as $roomId => $name) {
                try{
                    $liveUrl = $class->getLiveUrl($roomId);
                }catch (\ErrorException $e){
                    Console::error("ERROR:获取{$liveName}房间号{$roomId}的直播源失败:".$e->getMessage());
                    continue;
                }
                Console::log($liveName . '---' . $name . '---' . $liveUrl . PHP_EOL) ;
                $list[$name] = $liveUrl;
            }
            $listArr[$liveName] = $list;
        }
        return $listArr;
    }

}
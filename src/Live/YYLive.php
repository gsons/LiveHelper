<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2019/7/5
 * Time: 2:37
 */

namespace Gsons\Live;

use Gsons\HttpCurl;

class YYLive extends Live implements Api
{
    const SITE_NAME = "YY直播";
    const BASE_ROOM_URL = "https://www.yy.com/%s";
    const BASE_LIVE_URL = "https://interface.yy.com/hls/new/get/19527638/19527638/1200?source=wapyy&callback=jsonp2";
    const DANCE_ROOM_API_URL = "http://data.3g.yy.com/mobyy/nav/dance/idx";

    /**
     * @param $roomId
     * @return mixed
     * @throws \ErrorException
     */
    public static function getLiveUrl($roomId)
    {
        $curl = new HttpCurl();
        $curl->setReferrer('https://wap.yy.com');
        $roomUrl = sprintf(self::BASE_LIVE_URL, $roomId);
        $curl->get($roomUrl);
        $html = $curl->response;
        preg_match("/jsonp2\((.*?)\)/", $html, $match);
        print_r([$html,$match]);
        if (isset($match[1]) && $match[1]) {
            $jsonArr = json_decode($match[1], true);
            if (isset($jsonArr['hls'])) {
                return $jsonArr['hls'];
            } else {
                throw new \ErrorException("maybe not exist the roomId {$roomId}");
            }
        } else {
            throw new \ErrorException("maybe not exist the roomId {$roomId}");
        }
    }


    /**
     * @return array
     * @throws \ErrorException
     */
    public static function getDancingRoomId()
    {
        $curl = new HttpCurl();
        $curl->setReferrer('http://www.yy.com/');
        $curl->get(self::DANCE_ROOM_API_URL);
        $data = json_decode($curl->response, true);
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $arr = [];
        if (isset($data['data'][1]['data']) && !empty($data['data'][1]['data'])) {
            $list = $data['data'][1]['data'];
            foreach ($list as $vo) {
                if (isset($vo['tag']) && $vo['tag'] == '正在热舞') {
                    $arr[] = ['roomId' => $vo['sid'], 'nickName' => $vo['name']];
                }
            }
            $arr = array_column($arr, 'nickName', 'roomId');
        }
        unset($data);
        return $arr;

    }
}
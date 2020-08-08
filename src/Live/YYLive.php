<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2019/7/5
 * Time: 2:37
 */

namespace Gsons\Live;

use Gsons\HttpCurl;

class YYLive extends Live
{
    const SITE_NAME = "YY直播";const SITE_CODE="YY";
    const BASE_ROOM_URL = "https://www.yy.com/%s";
    const BASE_LIVE_URL = "https://interface.yy.com/hls/new/get/%s/%s/2000?source=wapyy&callback=jsonp2";
    const DANCE_ROOM_API_URL = "http://data.3g.yy.com/mobyy/nav/dance/idx";
    const LIVE_ROOM_API_UEL = "https://www.yy.com/more/page.action?biz=other&subBiz=yqk&page=1&moduleId=3134&pageSize=1600";

    /**
     * @param $roomId
     * @return mixed
     * @throws \ErrorException
     */
    public function getLiveUrl($roomId)
    {
        $curl = new HttpCurl();
        $curl->setReferrer('https://wap.yy.com');
        $roomUrl = sprintf(self::BASE_LIVE_URL, $roomId, $roomId);
        $curl->get($roomUrl);
        $html = $curl->response;
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        preg_match("/jsonp2\((.*?)\)/", $html, $match);
        if (isset($match[1]) && $match[1]) {
            $jsonArr = json_decode($match[1], true);
            if (isset($jsonArr['hls'])) {
                return $jsonArr['hls'];
            }
        }
        throw new \ErrorException("failed to get live url {$roomId}");

    }

    /**
     * @return array
     * @throws \ErrorException
     */
    public function getDancingRoom()
    {
        $curl = new HttpCurl();
        $curl->setReferrer('http://www.yy.com/');
        $curl->get(self::DANCE_ROOM_API_URL);
        $data = json_decode($curl->response, true);
        $curl->close();
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

    /**
     * @return array
     * @throws \ErrorException
     */
    public function getTvRoom()
    {
        $curl = new HttpCurl();
        $curl->setReferrer('https://www.yy.com/others/yqk');
        $curl->get(self::LIVE_ROOM_API_UEL);
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $data = json_decode($curl->response, true);
        return isset($data['data']['data']) ? array_column($data['data']['data'], 'desc', 'sid') : [];
    }

    /**
     * @return array|mixed
     * @throws \ErrorException
     */
    public  function getHotDanceRoom()
    {
        $curl = new HttpCurl();
        $curl->setReferrer('http://www.yy.com/');
        $curl->get(self::DANCE_ROOM_API_URL);
        $data = json_decode($curl->response, true);
        $curl->close();
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $arr = [];
        if (isset($data['data'][0]['data']) && !empty($data['data'][0]['data'])) {
            $list = $data['data'][0]['data'];
            foreach ($list as $vo) {
                if(!isset($vo['sid'])||!$vo['sid']) continue;
                $time = time();
                $arr[] = [
                    'site_name' => self::SITE_NAME,
                    'site_code' => self::SITE_CODE,
                    'nick_name' => $vo['name'],
                    'room_id' => $vo['sid'],
                    'room_url' => sprintf(self::BASE_ROOM_URL, $vo['sid']),
                    'record_date' => date('Y-m-d H:i:s', $time),
                    'record_time' => $time,
                    'hot_num' => $vo['users']
                ];
            }
        }
        unset($data);
        return $arr;
    }
}
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
    const BASE_LIVE_URL = "https://wap.yy.com/mobileweb/%s/%s/";
    const DANCE_ROOM_API_URL = "http://data.3g.yy.com/mobyy/nav/dance/idx";

    public static function getLiveUrl($roomId)
    {

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
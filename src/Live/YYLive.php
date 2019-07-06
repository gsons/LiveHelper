<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2019/7/5
 * Time: 2:37
 */

namespace Gsons\Live;

use Curl\Curl;

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
        $curl = new Curl();
        $curl->setUserAgent('user-agent", "Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.22 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1');
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setReferrer('http://www.yy.com/');
        $curl->setHeader('X-Requested-With', 'XMLHttpRequest');
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
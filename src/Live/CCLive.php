<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2019/7/1
 * Time: 0:51
 */


namespace Gsons\Live;
use Curl\Curl;

class CCLive extends Live implements Api
{
    const SITE_NAME = "CC直播";
    const BASE_ROOM_URL = "https://cc.163.com/%s";
    const BASE_LIVE_URL = "http://cgi.v.cc.163.com/video_play_url/%s";
    const DANCE_ROOM_API_URL = "http://cc.163.com/wdf/game_lives/?gametype=65005&tag_id=79&format=json&start=0&size=100";

    /**
     * @param $roomId
     * @return array
     * @throws \ErrorException
     */
    public static function getLiveUrl($roomId)
    {
        $curl = new Curl();
        $curl->setUserAgent('user-agent", "Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.22 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1');
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setReferrer('https://cc.163.com');
        $curl->setHeader('X-Requested-With', 'XMLHttpRequest');
        $roomUrl = sprintf(self::BASE_LIVE_URL, $roomId);
        $curl->get($roomUrl);
        $data = json_decode($curl->response, true);
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        if (isset($data['videourl'])) {
            return $data['videourl'];
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
        $curl = new Curl();
        $curl->setUserAgent('user-agent", "Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.22 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1');
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setReferrer('https://www.huya.com/g/xingxiu');
        $curl->setHeader('X-Requested-With', 'XMLHttpRequest');
        $curl->get(self::DANCE_ROOM_API_URL);
        $data = json_decode($curl->response, true);
        if ($curl->error) {
            throw new \ErrorException($curl->error_message);
        }
        $arr = [];
        if (isset($data['lives']) && !empty($data['lives'])) {
            $list = $data['lives'];
            foreach ($list as $vo) {
                if (isset($vo['left_subscript']['name']) && $vo['left_subscript']['name'] == 'dancing') {
                    $arr[] = ['roomId' => $vo['ccid'], 'nickName' => $vo['nickname']];
                }
            }
            $arr = array_column($arr, 'nickName', 'roomId');
        }
        unset($data);
        return $arr;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: gsonhub
 * Date: 2019/7/1
 * Time: 0:51
 */


namespace Gsons\Live;
use Gsons\HttpCurl;


class CCLive extends Live
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
    public function getLiveUrl($roomId)
    {
        $curl = new HttpCurl();
        $curl->setReferrer('https://cc.163.com');
        $roomUrl = sprintf(self::BASE_LIVE_URL, $roomId);
        $curl->get($roomUrl);
        $data = json_decode($curl->response, true);
        $curl->close();
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
    public function getDancingRoomId()
    {
        $curl = new HttpCurl();
        $curl->setReferrer('https://www.huya.com/g/xingxiu');
        $curl->get(self::DANCE_ROOM_API_URL);
        $data = json_decode($curl->response, true);
        $curl->close();
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